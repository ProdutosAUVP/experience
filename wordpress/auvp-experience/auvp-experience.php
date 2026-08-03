<?php
/**
 * Plugin Name:       AUVP Experience
 * Plugin URI:        https://github.com/ProdutosAUVP/experience
 * Description:       Seções, widgets Elementor e formulários do site AUVP Experience — imersões estratégicas globais.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            AUVP Experience Co.
 * Text Domain:       auvp-experience
 * Domain Path:       /languages
 *
 * @package AUVP_Experience
 */

defined( 'ABSPATH' ) || exit;

define( 'AUVP_EXP_VERSION', '1.0.0' );
define( 'AUVP_EXP_FILE', __FILE__ );
define( 'AUVP_EXP_PATH', plugin_dir_path( __FILE__ ) );
define( 'AUVP_EXP_URL', plugin_dir_url( __FILE__ ) );

require_once AUVP_EXP_PATH . 'includes/helpers.php';
require_once AUVP_EXP_PATH . 'includes/class-auvp-content.php';
require_once AUVP_EXP_PATH . 'includes/class-auvp-render.php';
require_once AUVP_EXP_PATH . 'includes/class-auvp-assets.php';
require_once AUVP_EXP_PATH . 'includes/class-auvp-shortcodes.php';
require_once AUVP_EXP_PATH . 'includes/class-auvp-leads.php';
require_once AUVP_EXP_PATH . 'includes/class-auvp-forms.php';
require_once AUVP_EXP_PATH . 'includes/class-auvp-elementor.php';

/**
 * Inicializa o plugin.
 */
function auvp_exp_bootstrap() {
	load_plugin_textdomain( 'auvp-experience', false, dirname( plugin_basename( AUVP_EXP_FILE ) ) . '/languages' );

	AUVP_Assets::init();
	AUVP_Shortcodes::init();
	AUVP_Leads::init();
	AUVP_Forms::init();
	AUVP_Elementor::init();
}
add_action( 'plugins_loaded', 'auvp_exp_bootstrap' );

/**
 * Na ativação, registra o CPT e limpa as regras de rewrite.
 */
function auvp_exp_activate() {
	require_once AUVP_EXP_PATH . 'includes/class-auvp-leads.php';
	AUVP_Leads::register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'auvp_exp_activate' );

/**
 * Na desativação, apenas limpa as regras. Os leads permanecem no banco.
 */
function auvp_exp_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'auvp_exp_deactivate' );
