<?php
/**
 * Funções auxiliares compartilhadas pelos templates.
 *
 * @package AUVP_Experience
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lê uma chave de um array de dados com valor padrão.
 *
 * @param array  $data    Dados da seção.
 * @param string $key     Chave.
 * @param mixed  $default Valor padrão.
 * @return mixed
 */
function auvp_get( $data, $key, $default = '' ) {
	return ( is_array( $data ) && isset( $data[ $key ] ) && '' !== $data[ $key ] ) ? $data[ $key ] : $default;
}

/**
 * Escapa texto permitindo apenas a formatação inline usada nos títulos
 * (<em>, <strong>, <br>, <span>).
 *
 * @param string $text Texto.
 * @return string
 */
function auvp_kses_inline( $text ) {
	return wp_kses(
		(string) $text,
		array(
			'em'     => array( 'class' => array() ),
			'i'      => array( 'class' => array() ),
			'strong' => array( 'class' => array() ),
			'b'      => array( 'class' => array() ),
			'br'     => array(),
			'span'   => array( 'class' => array() ),
			'a'      => array(
				'href'   => array(),
				'target' => array(),
				'rel'    => array(),
				'class'  => array(),
			),
		)
	);
}

/**
 * URL de um arquivo dentro de assets/ do plugin.
 *
 * @param string $path Caminho relativo (ex.: 'img/hero-city.svg').
 * @return string
 */
function auvp_asset( $path ) {
	return AUVP_EXP_URL . 'assets/' . ltrim( $path, '/' );
}

/**
 * Resolve o campo de imagem de um widget: usa o anexo escolhido no
 * Elementor e cai para a arte que acompanha o plugin.
 *
 * @param array  $data     Dados da seção.
 * @param string $key      Chave do campo (array do controle MEDIA).
 * @param string $fallback Caminho relativo em assets/.
 * @return string
 */
function auvp_image_url( $data, $key, $fallback ) {
	$value = isset( $data[ $key ] ) ? $data[ $key ] : null;

	if ( is_array( $value ) && ! empty( $value['url'] ) ) {
		return $value['url'];
	}
	if ( is_string( $value ) && '' !== $value ) {
		return $value;
	}

	return auvp_asset( $fallback );
}

/**
 * Normaliza um campo de repetidor vindo do Elementor ou dos padrões.
 *
 * @param array  $data    Dados da seção.
 * @param string $key     Chave do repetidor.
 * @param array  $default Lista padrão.
 * @return array
 */
function auvp_repeater( $data, $key, $default = array() ) {
	$value = isset( $data[ $key ] ) ? $data[ $key ] : null;

	if ( is_array( $value ) && ! empty( $value ) ) {
		return $value;
	}

	return $default;
}

/**
 * Monta o atributo de âncora de uma seção.
 *
 * @param array  $data    Dados da seção.
 * @param string $default Âncora padrão.
 * @return string
 */
function auvp_anchor( $data, $default ) {
	$id = auvp_get( $data, 'ancora', $default );

	return sanitize_title( $id );
}
