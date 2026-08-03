<?php
/**
 * Armazenamento das candidaturas e sugestões.
 *
 * @package AUVP_Experience
 */

defined( 'ABSPATH' ) || exit;

/**
 * Leads.
 */
class AUVP_Leads {

	const POST_TYPE = 'auvp_lead';

	/**
	 * Ganchos.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column' ), 10, 2 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_box' ) );
		add_action( 'admin_post_auvp_export', array( __CLASS__, 'export_csv' ) );
		add_action( 'admin_notices', array( __CLASS__, 'export_button' ) );
	}

	/**
	 * Registra o tipo de post.
	 */
	public static function register_post_type() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'          => array(
					'name'          => __( 'Candidaturas', 'auvp-experience' ),
					'singular_name' => __( 'Candidatura', 'auvp-experience' ),
					'menu_name'     => __( 'AUVP Experience', 'auvp-experience' ),
					'all_items'     => __( 'Candidaturas', 'auvp-experience' ),
					'search_items'  => __( 'Buscar candidaturas', 'auvp-experience' ),
					'not_found'     => __( 'Nenhuma candidatura ainda.', 'auvp-experience' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_icon'       => 'dashicons-airplane',
				'menu_position'   => 26,
				'supports'        => array( 'title' ),
				'capability_type' => 'post',
				'map_meta_cap'    => true,
				'capabilities'    => array(
					'create_posts' => 'do_not_allow',
				),
				'has_archive'     => false,
				'rewrite'         => false,
				'show_in_rest'    => false,
			)
		);
	}

	/**
	 * Campos guardados por tipo de formulário.
	 *
	 * @return array
	 */
	public static function fields() {
		return array( 'nome', 'email', 'whatsapp', 'perfil', 'destino', 'motivo', 'sugestao', 'destinos_marcados' );
	}

	/**
	 * Grava um lead.
	 *
	 * @param string $tipo Tipo (candidatura|sugestao).
	 * @param array  $data Dados já sanitizados.
	 * @return int|WP_Error
	 */
	public static function store( $tipo, $data ) {
		$titulo = '';

		if ( ! empty( $data['nome'] ) ) {
			$titulo = $data['nome'];
		} elseif ( ! empty( $data['email'] ) ) {
			$titulo = $data['email'];
		} else {
			$titulo = __( 'Sem identificação', 'auvp-experience' );
		}

		$post_id = wp_insert_post(
			array(
				'post_type'   => self::POST_TYPE,
				'post_status' => 'publish',
				'post_title'  => wp_strip_all_tags( $titulo ),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		update_post_meta( $post_id, '_auvp_tipo', $tipo );

		foreach ( self::fields() as $field ) {
			if ( isset( $data[ $field ] ) && '' !== $data[ $field ] ) {
				update_post_meta( $post_id, '_auvp_' . $field, $data[ $field ] );
			}
		}

		update_post_meta( $post_id, '_auvp_origem', isset( $data['_origem'] ) ? $data['_origem'] : '' );

		/**
		 * Disparado após gravar um lead. Ponto de integração com CRM.
		 *
		 * @param int    $post_id ID do registro.
		 * @param string $tipo    Tipo do formulário.
		 * @param array  $data    Dados sanitizados.
		 */
		do_action( 'auvp_lead_stored', $post_id, $tipo, $data );

		return $post_id;
	}

	/**
	 * Colunas da listagem.
	 *
	 * @param array $columns Colunas originais.
	 * @return array
	 */
	public static function columns( $columns ) {
		return array(
			'cb'            => isset( $columns['cb'] ) ? $columns['cb'] : '',
			'title'         => __( 'Nome', 'auvp-experience' ),
			'auvp_tipo'     => __( 'Tipo', 'auvp-experience' ),
			'auvp_email'    => __( 'E-mail', 'auvp-experience' ),
			'auvp_whatsapp' => __( 'WhatsApp', 'auvp-experience' ),
			'auvp_perfil'   => __( 'Perfil', 'auvp-experience' ),
			'auvp_destino'  => __( 'Destino', 'auvp-experience' ),
			'date'          => __( 'Recebida em', 'auvp-experience' ),
		);
	}

	/**
	 * Conteúdo das colunas.
	 *
	 * @param string $column  Coluna.
	 * @param int    $post_id Post.
	 */
	public static function column( $column, $post_id ) {
		if ( 0 !== strpos( $column, 'auvp_' ) ) {
			return;
		}

		$key   = substr( $column, 5 );
		$value = get_post_meta( $post_id, '_auvp_' . $key, true );

		if ( 'tipo' === $key ) {
			$value = 'sugestao' === $value ? __( 'Sugestão de destino', 'auvp-experience' ) : __( 'Candidatura', 'auvp-experience' );
		}

		if ( 'email' === $key && $value ) {
			printf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $value ) );
			return;
		}

		echo esc_html( $value ? $value : '—' );
	}

	/**
	 * Caixa com todos os dados do lead.
	 */
	public static function meta_box() {
		add_meta_box(
			'auvp-lead',
			__( 'Dados enviados', 'auvp-experience' ),
			array( __CLASS__, 'render_meta_box' ),
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Renderiza a caixa.
	 *
	 * @param WP_Post $post Post.
	 */
	public static function render_meta_box( $post ) {
		$rows = array( 'tipo' => __( 'Tipo', 'auvp-experience' ) );

		foreach ( self::fields() as $field ) {
			$rows[ $field ] = ucfirst( str_replace( '_', ' ', $field ) );
		}
		$rows['origem'] = __( 'Origem', 'auvp-experience' );

		echo '<table class="widefat striped"><tbody>';
		foreach ( $rows as $key => $label ) {
			$value = get_post_meta( $post->ID, '_auvp_' . $key, true );
			if ( '' === $value ) {
				continue;
			}
			printf(
				'<tr><th style="width:200px">%s</th><td>%s</td></tr>',
				esc_html( $label ),
				esc_html( $value )
			);
		}
		echo '</tbody></table>';
	}

	/**
	 * Botão de exportação na listagem.
	 */
	public static function export_button() {
		$screen = get_current_screen();

		if ( ! $screen || 'edit-' . self::POST_TYPE !== $screen->id || ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$url = wp_nonce_url( admin_url( 'admin-post.php?action=auvp_export' ), 'auvp_export' );

		printf(
			'<div class="notice notice-info"><p><a class="button button-primary" href="%s">%s</a></p></div>',
			esc_url( $url ),
			esc_html__( 'Exportar tudo em CSV', 'auvp-experience' )
		);
	}

	/**
	 * Neutraliza fórmulas no CSV.
	 *
	 * Um campo começando com =, +, - ou @ é interpretado como fórmula pelo
	 * Excel e pelo Sheets; o prefixo com aspa simples evita a execução.
	 *
	 * @param string $value Valor.
	 * @return string
	 */
	protected static function csv_safe( $value ) {
		if ( '' !== $value && in_array( $value[0], array( '=', '+', '-', '@', "\t", "\r" ), true ) ) {
			return "'" . $value;
		}

		return $value;
	}

	/**
	 * Exporta os leads em CSV.
	 */
	public static function export_csv() {
		if ( ! current_user_can( 'edit_posts' ) || ! check_admin_referer( 'auvp_export' ) ) {
			wp_die( esc_html__( 'Sem permissão.', 'auvp-experience' ) );
		}

		$posts = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			)
		);

		$columns = array_merge( array( 'data', 'tipo' ), self::fields(), array( 'origem' ) );

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=auvp-experience-' . gmdate( 'Y-m-d' ) . '.csv' );

		$out = fopen( 'php://output', 'w' );
		fwrite( $out, "\xEF\xBB\xBF" ); // BOM, para o Excel abrir os acentos.
		fputcsv( $out, $columns );

		foreach ( $posts as $post ) {
			$row = array( get_the_date( 'Y-m-d H:i', $post ) );
			foreach ( array_slice( $columns, 1 ) as $column ) {
				$row[] = self::csv_safe( (string) get_post_meta( $post->ID, '_auvp_' . $column, true ) );
			}
			fputcsv( $out, $row );
		}

		fclose( $out );
		exit;
	}
}
