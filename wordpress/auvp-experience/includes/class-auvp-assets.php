<?php
/**
 * Registro e carregamento de CSS/JS.
 *
 * @package AUVP_Experience
 */

defined( 'ABSPATH' ) || exit;

/**
 * Assets.
 */
class AUVP_Assets {

	/**
	 * Marca se alguma seção foi renderizada nesta requisição.
	 *
	 * @var bool
	 */
	protected static $used = false;

	/**
	 * Ganchos.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register' ), 5 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'maybe_enqueue' ), 20 );
		add_action( 'wp_head', array( __CLASS__, 'preload_fonts' ), 2 );

		// No editor e no preview do Elementor os assets sempre entram.
		add_action( 'elementor/preview/enqueue_styles', array( __CLASS__, 'enqueue' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( __CLASS__, 'enqueue' ) );
	}

	/**
	 * Registra os handles.
	 */
	public static function register() {
		wp_register_style(
			'auvp-experience',
			auvp_asset( 'css/auvp-experience.css' ),
			array(),
			AUVP_EXP_VERSION
		);

		wp_register_script(
			'auvp-experience',
			auvp_asset( 'js/app.js' ),
			array(),
			AUVP_EXP_VERSION,
			true
		);
	}

	/**
	 * Sinaliza que uma seção foi renderizada.
	 *
	 * Serve de rede de segurança: se a detecção automática falhar (seção
	 * inserida por template do tema, por exemplo), os assets ainda entram.
	 */
	public static function mark_used() {
		self::$used = true;

		if ( ! did_action( 'wp_enqueue_scripts' ) || wp_style_is( 'auvp-experience', 'enqueued' ) ) {
			return;
		}

		self::enqueue();
	}

	/**
	 * Decide se a página precisa dos assets.
	 */
	public static function maybe_enqueue() {
		if ( is_admin() ) {
			return;
		}

		$post   = get_post();
		$needed = false;

		if ( $post instanceof WP_Post ) {
			foreach ( array( 'auvp_experience', 'auvp_secao', 'auvp_navegacao', 'auvp_rodape' ) as $tag ) {
				if ( has_shortcode( (string) $post->post_content, $tag ) ) {
					$needed = true;
					break;
				}
			}

			if ( ! $needed ) {
				// Páginas montadas no Elementor guardam os widgets em JSON.
				$elementor = get_post_meta( $post->ID, '_elementor_data', true );
				if ( is_string( $elementor ) && false !== strpos( $elementor, 'auvp-' ) ) {
					$needed = true;
				}
			}
		}

		/**
		 * Força (ou impede) o carregamento dos assets.
		 *
		 * @param bool         $needed Resultado da detecção automática.
		 * @param WP_Post|null $post   Post atual.
		 */
		if ( apply_filters( 'auvp_load_assets', $needed, $post ) ) {
			self::enqueue();
		}
	}

	/**
	 * Coloca CSS e JS na fila e injeta a configuração do front-end.
	 */
	public static function enqueue() {
		if ( ! wp_style_is( 'auvp-experience', 'registered' ) ) {
			self::register();
		}

		wp_enqueue_style( 'auvp-experience' );
		wp_enqueue_script( 'auvp-experience' );

		$config = array(
			'endpoint' => esc_url_raw( rest_url( AUVP_Forms::REST_NAMESPACE . '/lead' ) ),
			'nonce'    => wp_create_nonce( 'wp_rest' ),
			'email'    => sanitize_email( AUVP_Forms::contact_email() ),
		);

		wp_add_inline_script(
			'auvp-experience',
			'window.AUVP_WP = ' . wp_json_encode( $config ) . ';',
			'before'
		);
	}

	/**
	 * Pré-carrega as duas fontes usadas acima da dobra.
	 */
	public static function preload_fonts() {
		if ( ! self::$used && ! wp_style_is( 'auvp-experience', 'enqueued' ) ) {
			return;
		}

		foreach ( array( 'cormorant-garamond-latin.woff2', 'inter-latin.woff2' ) as $font ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( auvp_asset( 'fonts/' . $font ) )
			);
		}
	}
}
