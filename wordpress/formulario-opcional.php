<?php
/**
 * OPCIONAL — recebimento dos formulários sem plugin.
 *
 * Cole tudo isto no fim do functions.php do seu TEMA FILHO
 * (Aparência > Editor de arquivos de tema > functions.php).
 *
 * Sem este trecho os formulários continuam funcionando: eles caem no
 * modo e-mail, que abre o cliente de e-mail do visitante com os campos
 * já preenchidos. Com ele, as candidaturas passam a ser gravadas no
 * painel do WordPress e você recebe um aviso por e-mail.
 *
 * Depois de colar, as candidaturas aparecem no menu "Candidaturas".
 *
 * @package AUVP_Experience
 */

defined( 'ABSPATH' ) || exit;

/* -----------------------------------------------------------------
   1. Onde as candidaturas ficam guardadas
   ----------------------------------------------------------------- */

add_action(
	'init',
	function () {
		register_post_type(
			'auvp_lead',
			array(
				'labels'       => array(
					'name'          => 'Candidaturas',
					'singular_name' => 'Candidatura',
					'not_found'     => 'Nenhuma candidatura ainda.',
				),
				'public'       => false,
				'show_ui'      => true,
				'menu_icon'    => 'dashicons-airplane',
				'menu_position' => 26,
				'supports'     => array( 'title' ),
				'capabilities' => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap' => true,
			)
		);
	}
);

/* -----------------------------------------------------------------
   2. Colunas da listagem no painel
   ----------------------------------------------------------------- */

add_filter(
	'manage_auvp_lead_posts_columns',
	function ( $columns ) {
		return array(
			'cb'            => isset( $columns['cb'] ) ? $columns['cb'] : '',
			'title'         => 'Nome',
			'auvp_tipo'     => 'Tipo',
			'auvp_email'    => 'E-mail',
			'auvp_whatsapp' => 'WhatsApp',
			'auvp_perfil'   => 'Perfil',
			'auvp_destino'  => 'Destino',
			'date'          => 'Recebida em',
		);
	}
);

add_action(
	'manage_auvp_lead_posts_custom_column',
	function ( $column, $post_id ) {
		if ( 0 !== strpos( $column, 'auvp_' ) ) {
			return;
		}

		$value = get_post_meta( $post_id, '_' . $column, true );

		if ( 'auvp_tipo' === $column ) {
			$value = 'sugestao' === $value ? 'Sugestão de destino' : 'Candidatura';
		}

		echo esc_html( $value ? $value : '—' );
	},
	10,
	2
);

/* Mostra todos os campos ao abrir uma candidatura. */
add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'auvp-lead',
			'Dados enviados',
			function ( $post ) {
				$campos = array( 'tipo', 'nome', 'email', 'whatsapp', 'perfil', 'destino', 'motivo', 'sugestao', 'destinos_marcados', 'origem' );

				echo '<table class="widefat striped"><tbody>';
				foreach ( $campos as $campo ) {
					$valor = get_post_meta( $post->ID, '_auvp_' . $campo, true );
					if ( '' === $valor ) {
						continue;
					}
					printf(
						'<tr><th style="width:200px">%s</th><td>%s</td></tr>',
						esc_html( ucfirst( str_replace( '_', ' ', $campo ) ) ),
						esc_html( $valor )
					);
				}
				echo '</tbody></table>';
			},
			'auvp_lead',
			'normal',
			'high'
		);
	}
);

/* -----------------------------------------------------------------
   3. Rota que recebe o envio
   ----------------------------------------------------------------- */

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'auvp/v1',
			'/lead',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true',
				'callback'            => 'auvp_receber_lead',
			)
		);
	}
);

/**
 * Trata o envio do formulário.
 *
 * @param WP_REST_Request $request Requisição.
 * @return WP_REST_Response
 */
function auvp_receber_lead( $request ) {
	$body = $request->get_json_params();
	if ( ! is_array( $body ) ) {
		$body = (array) $request->get_body_params();
	}

	$responder = function ( $ok, $mensagem = '', $status = 200 ) {
		return new WP_REST_Response(
			array(
				'ok'      => $ok,
				'message' => $mensagem,
			),
			$status
		);
	};

	// Honeypot: campo invisível preenchido significa robô.
	if ( ! empty( $body['_gotcha'] ) ) {
		return $responder( true );
	}

	// Tempo mínimo de preenchimento.
	$ts = isset( $body['_ts'] ) ? absint( $body['_ts'] ) : 0;
	if ( $ts && ( time() - $ts ) < 3 ) {
		return $responder( false, 'Envio rápido demais. Tente novamente.', 429 );
	}

	// Limite de 5 envios por IP a cada 10 minutos.
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	if ( $ip ) {
		$chave = 'auvp_rl_' . md5( $ip );
		$conta = (int) get_transient( $chave );
		if ( $conta >= 5 ) {
			return $responder( false, 'Muitos envios seguidos. Aguarde alguns minutos.', 429 );
		}
		set_transient( $chave, $conta + 1, 10 * MINUTE_IN_SECONDS );
	}

	$tipo   = ( isset( $body['_tipo'] ) && 'sugestao' === $body['_tipo'] ) ? 'sugestao' : 'candidatura';
	$campos = array( 'nome', 'email', 'whatsapp', 'perfil', 'destino', 'motivo', 'sugestao', 'destinos_marcados' );
	$dados  = array();

	foreach ( $campos as $campo ) {
		if ( ! isset( $body[ $campo ] ) ) {
			continue;
		}
		$valor = wp_unslash( $body[ $campo ] );

		if ( 'email' === $campo ) {
			$dados[ $campo ] = sanitize_email( $valor );
		} elseif ( in_array( $campo, array( 'motivo', 'sugestao' ), true ) ) {
			$dados[ $campo ] = sanitize_textarea_field( $valor );
		} else {
			$dados[ $campo ] = sanitize_text_field( $valor );
		}
	}

	$obrigatorios = 'sugestao' === $tipo
		? array( 'sugestao', 'email' )
		: array( 'nome', 'email', 'whatsapp', 'perfil', 'destino', 'motivo' );

	foreach ( $obrigatorios as $campo ) {
		if ( empty( $dados[ $campo ] ) ) {
			return $responder( false, 'Preencha todos os campos obrigatórios.', 400 );
		}
	}

	if ( ! is_email( $dados['email'] ) ) {
		return $responder( false, 'E-mail inválido.', 400 );
	}

	$titulo  = ! empty( $dados['nome'] ) ? $dados['nome'] : $dados['email'];
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'auvp_lead',
			'post_status' => 'publish',
			'post_title'  => wp_strip_all_tags( $titulo ),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return $responder( false, 'Não foi possível registrar agora.', 500 );
	}

	update_post_meta( $post_id, '_auvp_tipo', $tipo );
	update_post_meta( $post_id, '_auvp_origem', isset( $body['_origem'] ) ? esc_url_raw( wp_unslash( $body['_origem'] ) ) : '' );

	foreach ( $dados as $campo => $valor ) {
		if ( '' !== $valor ) {
			update_post_meta( $post_id, '_auvp_' . $campo, $valor );
		}
	}

	// Aviso por e-mail. Troque o destinatário se precisar.
	$para    = apply_filters( 'auvp_lead_email_to', get_option( 'admin_email' ) );
	$assunto = 'sugestao' === $tipo
		? '[AUVP Experience] Nova sugestão de destino'
		: '[AUVP Experience] Nova candidatura';

	$linhas = array();
	foreach ( $dados as $campo => $valor ) {
		if ( '' !== $valor ) {
			$linhas[] = ucfirst( str_replace( '_', ' ', $campo ) ) . ': ' . $valor;
		}
	}
	$linhas[] = '';
	$linhas[] = 'Ver no painel: ' . admin_url( 'post.php?post=' . (int) $post_id . '&action=edit' );

	wp_mail( $para, $assunto, implode( "\n", $linhas ) );

	/* Ponto de integração com CRM:
	   add_action( 'auvp_lead_stored', function ( $post_id, $tipo, $dados ) { ... }, 10, 3 ); */
	do_action( 'auvp_lead_stored', $post_id, $tipo, $dados );

	return $responder( true );
}

/* -----------------------------------------------------------------
   4. Avisa o JavaScript da página onde fica a rota
   ----------------------------------------------------------------- */

add_action(
	'wp_head',
	function () {
		printf(
			'<script>window.AUVP_WP=%s;</script>' . "\n",
			wp_json_encode(
				array(
					'endpoint' => esc_url_raw( rest_url( 'auvp/v1/lead' ) ),
					'nonce'    => wp_create_nonce( 'wp_rest' ),
					'email'    => sanitize_email( get_option( 'admin_email' ) ),
				)
			)
		);
	},
	1
);
