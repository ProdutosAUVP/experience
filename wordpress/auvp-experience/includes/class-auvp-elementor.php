<?php
/**
 * Integração com o Elementor: categoria e widgets.
 *
 * Os controles de cada widget são gerados a partir do registro em
 * AUVP_Content, então adicionar um campo lá já o faz aparecer no editor.
 *
 * @package AUVP_Experience
 */

defined( 'ABSPATH' ) || exit;

/**
 * Elementor.
 */
class AUVP_Elementor {

	/**
	 * Ganchos.
	 */
	public static function init() {
		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'category' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'widgets' ) );
	}

	/**
	 * Cria a categoria "AUVP Experience" no painel de widgets.
	 *
	 * @param \Elementor\Elements_Manager $manager Gerenciador.
	 */
	public static function category( $manager ) {
		$manager->add_category(
			'auvp-experience',
			array(
				'title' => __( 'AUVP Experience', 'auvp-experience' ),
				'icon'  => 'eicon-globe',
			)
		);
	}

	/**
	 * Registra um widget por seção.
	 *
	 * @param \Elementor\Widgets_Manager $manager Gerenciador.
	 */
	public static function widgets( $manager ) {
		require_once AUVP_EXP_PATH . 'includes/widgets/class-auvp-widget.php';
		require_once AUVP_EXP_PATH . 'includes/widgets/widgets.php';

		foreach ( auvp_widget_classes() as $class ) {
			if ( class_exists( $class ) ) {
				$manager->register( new $class() );
			}
		}
	}
}
