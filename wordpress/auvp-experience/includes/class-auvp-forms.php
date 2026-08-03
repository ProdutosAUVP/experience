<?php
/**
 * Recebimento dos formulários.
 *
 * @package AUVP_Experience
 */

defined( 'ABSPATH' ) || exit;

/**
 * Formulários.
 */
class AUVP_Forms {

	const REST_NAMESPACE = 'auvp/v1';

	/**
	 * Ganchos.
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_route' ) );
	}

	/**
	 * Endereço que recebe as notificações.
	 *
	 * @return string
	 */
	public static function contact_email() {
		/**
		 * Altera o destinatário das notificações.
		 *
		 * @param string $email Endereço.
		 */
		return apply_filters( 'auvp_lead_email_to', get_option( 'admin_email' ) );
	}

	/**
	 * Campos ocultos comuns aos formulários: honeypot e carimbo de tempo.
	 *
	 * Não usamos nonce aqui de propósito — páginas de campanha costumam
	 * ficar em cache de página inteira, e um nonce expirado derrubaria
	 * envios legítimos. A proteção é honeypot + tempo mínimo + limite por IP,
	 * e o nonce do REST é enviado pelo JS quando está disponível.
	 */
	public static function hidden_fields() {
		echo '<input type="text" name="_gotcha" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0">';
		printf( '<input type="hidden" name="_ts" value="%d">', absint( time() ) );
	}

	/**
	 * Registra a rota REST.
	 */
	public static function register_route() {
		register_rest_route(
			self::REST_NAMESPACE,
			'/lead',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'handle' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Trata o envio.
	 *
	 * @param WP_REST_Request $request Requisição.
	 * @return WP_REST_Response
	 */
	public static function handle( $request ) {
		$body = $request->get_json_params();

		if ( empty( $body ) ) {
			$body = $request->get_body_params();
		}
		if ( ! is_array( $body ) ) {
			$body = array();
		}

		// 1. Honeypot: preenchido significa robô.
		if ( ! empty( $body['_gotcha'] ) ) {
			return self::response( true );
		}

		// 2. Tempo mínimo de preenchimento.
		$ts = isset( $body['_ts'] ) ? absint( $body['_ts'] ) : 0;
		if ( $ts && ( time() - $ts ) < 3 ) {
			return self::response( false, __( 'Envio rápido demais. Tente novamente.', 'auvp-experience' ), 429 );
		}

		// 3. Limite por IP.
		if ( ! self::within_rate_limit() ) {
			return self::response( false, __( 'Muitos envios seguidos. Aguarde alguns minutos.', 'auvp-experience' ), 429 );
		}

		$tipo = isset( $body['_tipo'] ) && 'sugestao' === $body['_tipo'] ? 'sugestao' : 'candidatura';
		$data = self::sanitize( $body );

		// 4. Campos obrigatórios por tipo.
		$required = 'sugestao' === $tipo ? array( 'sugestao', 'email' ) : array( 'nome', 'email', 'whatsapp', 'perfil', 'destino', 'motivo' );

		foreach ( $required as $field ) {
			if ( empty( $data[ $field ] ) ) {
				return self::response( false, __( 'Preencha todos os campos obrigatórios.', 'auvp-experience' ), 400 );
			}
		}

		if ( ! is_email( $data['email'] ) ) {
			return self::response( false, __( 'E-mail inválido.', 'auvp-experience' ), 400 );
		}

		$stored = AUVP_Leads::store( $tipo, $data );

		if ( is_wp_error( $stored ) ) {
			return self::response( false, __( 'Não foi possível registrar agora.', 'auvp-experience' ), 500 );
		}

		self::notify( $tipo, $data, $stored );

		return self::response( true );
	}

	/**
	 * Sanitiza os campos aceitos.
	 *
	 * @param array $body Dados crus.
	 * @return array
	 */
	protected static function sanitize( $body ) {
		$data = array();

		foreach ( AUVP_Leads::fields() as $field ) {
			if ( ! isset( $body[ $field ] ) ) {
				continue;
			}

			$value = wp_unslash( $body[ $field ] );

			if ( 'email' === $field ) {
				$data[ $field ] = sanitize_email( $value );
			} elseif ( 'motivo' === $field || 'sugestao' === $field ) {
				$data[ $field ] = sanitize_textarea_field( $value );
			} else {
				$data[ $field ] = sanitize_text_field( $value );
			}
		}

		$data['_origem'] = isset( $body['_origem'] ) ? esc_url_raw( wp_unslash( $body['_origem'] ) ) : '';

		return $data;
	}

	/**
	 * Limite simples por IP: 5 envios a cada 10 minutos.
	 *
	 * @return bool
	 */
	protected static function within_rate_limit() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

		if ( ! $ip ) {
			return true;
		}

		$key   = 'auvp_rl_' . md5( $ip );
		$count = (int) get_transient( $key );

		if ( $count >= 5 ) {
			return false;
		}

		set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );

		return true;
	}

	/**
	 * Envia a notificação por e-mail.
	 *
	 * @param string $tipo    Tipo.
	 * @param array  $data    Dados.
	 * @param int    $post_id Registro criado.
	 */
	protected static function notify( $tipo, $data, $post_id ) {
		$assunto = 'sugestao' === $tipo
			? __( '[AUVP Experience] Nova sugestão de destino', 'auvp-experience' )
			: __( '[AUVP Experience] Nova candidatura', 'auvp-experience' );

		$linhas = array();

		foreach ( $data as $key => $value ) {
			if ( '' === $value || 0 === strpos( $key, '_' ) ) {
				continue;
			}
			$linhas[] = ucfirst( str_replace( '_', ' ', $key ) ) . ': ' . $value;
		}

		$linhas[] = '';
		// get_edit_post_link() depende das permissões do usuário atual e a
		// requisição é anônima — montamos a URL na mão.
		$linhas[] = __( 'Ver no painel:', 'auvp-experience' ) . ' ' . admin_url( 'post.php?post=' . (int) $post_id . '&action=edit' );

		$mensagem = implode( "\n", $linhas );

		/**
		 * Altera o corpo da notificação.
		 *
		 * @param string $mensagem Corpo.
		 * @param string $tipo     Tipo.
		 * @param array  $data     Dados.
		 */
		$mensagem = apply_filters( 'auvp_lead_email_body', $mensagem, $tipo, $data );

		wp_mail( self::contact_email(), $assunto, $mensagem );
	}

	/**
	 * Resposta padronizada.
	 *
	 * @param bool   $ok      Sucesso.
	 * @param string $message Mensagem.
	 * @param int    $status  Código HTTP.
	 * @return WP_REST_Response
	 */
	protected static function response( $ok, $message = '', $status = 200 ) {
		return new WP_REST_Response(
			array(
				'ok'      => (bool) $ok,
				'message' => $message,
			),
			$status
		);
	}
}
