<?php
/**
 * Shortcodes — alternativa aos widgets do Elementor.
 *
 * @package AUVP_Experience
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shortcodes.
 */
class AUVP_Shortcodes {

	/**
	 * Ganchos.
	 */
	public static function init() {
		add_shortcode( 'auvp_experience', array( __CLASS__, 'page' ) );
		add_shortcode( 'auvp_secao', array( __CLASS__, 'section' ) );
		add_shortcode( 'auvp_navegacao', array( __CLASS__, 'navegacao' ) );
		add_shortcode( 'auvp_rodape', array( __CLASS__, 'rodape' ) );
	}

	/**
	 * [auvp_experience] — página inteira, na ordem do briefing.
	 *
	 * @return string
	 */
	public static function page() {
		return AUVP_Render::page();
	}

	/**
	 * [auvp_secao slug="hero" titulo="..."] — uma seção.
	 *
	 * Qualquer campo de texto da seção pode ser sobrescrito por atributo.
	 *
	 * @param array $atts Atributos.
	 * @return string
	 */
	public static function section( $atts ) {
		$atts = shortcode_atts( array_merge( array( 'slug' => '' ), self::text_atts() ), $atts, 'auvp_secao' );
		$slug = sanitize_key( $atts['slug'] );

		if ( ! $slug || ! AUVP_Content::section( $slug ) ) {
			return '';
		}

		unset( $atts['slug'] );

		return AUVP_Render::section( $slug, array_filter( $atts, 'strlen' ) );
	}

	/**
	 * [auvp_navegacao] — menu fixo, menu em tela cheia e overlays.
	 *
	 * @return string
	 */
	public static function navegacao() {
		return AUVP_Render::section( 'navegacao' );
	}

	/**
	 * [auvp_rodape] — rodapé.
	 *
	 * @return string
	 */
	public static function rodape() {
		return AUVP_Render::section( 'rodape' );
	}

	/**
	 * Lista de atributos de texto aceitos por [auvp_secao].
	 *
	 * @return array
	 */
	protected static function text_atts() {
		$keys = array();

		foreach ( AUVP_Content::sections() as $section ) {
			foreach ( $section['fields'] as $key => $field ) {
				if ( in_array( $field['type'], array( 'text', 'textarea', 'url', 'select' ), true ) ) {
					$keys[ $key ] = '';
				}
			}
		}

		return $keys;
	}
}
