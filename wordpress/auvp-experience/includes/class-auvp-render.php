<?php
/**
 * Renderização das seções.
 *
 * @package AUVP_Experience
 */

defined( 'ABSPATH' ) || exit;

/**
 * Renderizador.
 */
class AUVP_Render {

	/**
	 * Renderiza uma seção e devolve o HTML.
	 *
	 * Todo o markup sai embrulhado em `.auvp-x`, que é o escopo do CSS —
	 * sem ele nenhuma regra do site se aplica.
	 *
	 * @param string $slug Slug da seção.
	 * @param array  $data Dados (sobrepõem os padrões).
	 * @return string
	 */
	public static function section( $slug, $data = array() ) {
		$definition = AUVP_Content::section( $slug );

		if ( ! $definition ) {
			return '';
		}

		$template = AUVP_EXP_PATH . 'templates/' . $slug . '.php';

		/**
		 * Permite trocar o arquivo de template de uma seção.
		 *
		 * @param string $template Caminho do arquivo.
		 * @param string $slug     Slug da seção.
		 */
		$template = apply_filters( 'auvp_template', $template, $slug );

		if ( ! file_exists( $template ) ) {
			return '';
		}

		$d = self::prepare( $slug, $data );

		AUVP_Assets::mark_used();

		ob_start();
		echo '<div class="auvp-x">';
		include $template;
		echo '</div>';

		return ob_get_clean();
	}

	/**
	 * Mescla os dados recebidos com os padrões, descartando valores vazios.
	 *
	 * O Elementor entrega strings vazias para controles não preenchidos;
	 * nesses casos o padrão da seção continua valendo.
	 *
	 * @param string $slug Slug da seção.
	 * @param array  $data Dados recebidos.
	 * @return array
	 */
	public static function prepare( $slug, $data ) {
		$defaults = AUVP_Content::defaults( $slug );

		if ( ! is_array( $data ) ) {
			return $defaults;
		}

		$out = $defaults;

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				if ( ! empty( $value ) ) {
					$out[ $key ] = $value;
				}
				continue;
			}

			if ( '' !== $value && null !== $value ) {
				$out[ $key ] = $value;
			}
		}

		/**
		 * Permite alterar os dados de uma seção antes da renderização.
		 *
		 * @param array  $out  Dados finais.
		 * @param string $slug Slug da seção.
		 */
		return apply_filters( 'auvp_section_data', $out, $slug );
	}

	/**
	 * Renderiza a página completa, na ordem do briefing.
	 *
	 * @return string
	 */
	public static function page() {
		$html = '';

		foreach ( AUVP_Content::page_order() as $slug ) {
			$html .= self::section( $slug );
		}

		return $html;
	}

	/**
	 * Resolve a URL de uma imagem de repetidor, aceitando tanto o array do
	 * controle MEDIA quanto um caminho relativo dentro de assets/.
	 *
	 * @param mixed  $value    Valor do campo.
	 * @param string $fallback Caminho relativo padrão.
	 * @return string
	 */
	public static function media_url( $value, $fallback = '' ) {
		if ( is_array( $value ) && ! empty( $value['url'] ) ) {
			return $value['url'];
		}

		if ( is_string( $value ) && '' !== $value ) {
			// Caminho relativo do próprio plugin.
			if ( 0 === strpos( $value, 'img/' ) ) {
				return auvp_asset( $value );
			}
			return $value;
		}

		return $fallback ? auvp_asset( $fallback ) : '';
	}
}
