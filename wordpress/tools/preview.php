<?php
/**
 * Renderiza as seções do plugin FORA do WordPress, para conferência visual.
 *
 *   php wordpress/tools/preview.php > /tmp/preview.html
 *
 * Implementa apenas os stubs das funções do WordPress que os templates
 * usam. Serve para validar markup e escape sem precisar de uma instalação;
 * não substitui um teste no WordPress de verdade.
 *
 * @package AUVP_Experience
 */

define( 'ABSPATH', __DIR__ );
define( 'MINUTE_IN_SECONDS', 60 );

/* ---------- stubs do WordPress ---------- */

function esc_html( $t ) {
	return htmlspecialchars( (string) $t, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $t ) {
	return htmlspecialchars( (string) $t, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $u ) {
	return htmlspecialchars( (string) $u, ENT_QUOTES, 'UTF-8' ); }
function esc_html__( $t, $d = '' ) {
	return esc_html( $t ); }
function esc_attr__( $t, $d = '' ) {
	return esc_attr( $t ); }
function esc_html_e( $t, $d = '' ) {
	echo esc_html( $t ); }
function esc_attr_e( $t, $d = '' ) {
	echo esc_attr( $t ); }
function __( $t, $d = '' ) {
	return $t; }
function _e( $t, $d = '' ) {
	echo $t; }
function wp_rand( $a = 0, $b = 0 ) {
	return random_int( $a, $b ); }
function wp_strip_all_tags( $t ) {
	return trim( strip_tags( (string) $t ) ); }
function sanitize_title( $t ) {
	return preg_replace( '/[^a-z0-9_-]+/', '-', strtolower( (string) $t ) ); }
function apply_filters( $tag, $value ) {
	return $value; }
function do_action() {}
function absint( $n ) {
	return abs( (int) $n ); }
function plugin_dir_url() {
	return 'ASSETS/'; }
function plugin_dir_path() {
	return dirname( __DIR__ ) . '/auvp-experience/'; }

function wp_kses( $text, $allowed ) {
	$tags = '<' . implode( '><', array_keys( $allowed ) ) . '>';
	return strip_tags( (string) $text, $tags );
}

/* ---------- carga do plugin ---------- */

define( 'AUVP_EXP_VERSION', 'preview' );
define( 'AUVP_EXP_PATH', dirname( __DIR__ ) . '/auvp-experience/' );
define( 'AUVP_EXP_URL', 'ASSETS/' );

require_once AUVP_EXP_PATH . 'includes/helpers.php';
require_once AUVP_EXP_PATH . 'includes/class-auvp-content.php';
require_once AUVP_EXP_PATH . 'includes/class-auvp-render.php';

/** Stub de assets: o preview não enfileira nada. */
class AUVP_Assets {
	/** Não faz nada no preview. */
	public static function mark_used() {}
}

/** Stub de formulários: reproduz os campos ocultos. */
class AUVP_Forms {
	/** Campos ocultos. */
	public static function hidden_fields() {
		echo '<input type="text" name="_gotcha" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0">';
		printf( '<input type="hidden" name="_ts" value="%d">', time() );
	}
}

/* ---------- saída ---------- */

$auvp_secoes = array_merge( array( 'navegacao' ), AUVP_Content::page_order(), array( 'rodape' ) );
$auvp_html   = '';

foreach ( $auvp_secoes as $auvp_slug ) {
	$auvp_html .= AUVP_Render::section( $auvp_slug );
}

// Aponta os assets para o build do plugin.
$auvp_html = str_replace( 'ASSETS/assets/', '../auvp-experience/assets/', $auvp_html );

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Preview dos templates — AUVP Experience</title>
<link rel="stylesheet" href="../auvp-experience/assets/css/auvp-experience.css">
<style>
	/* Simula um tema WordPress hostil: classes genéricas e estilos que
	   invadiriam o site se ele não estivesse isolado. */
	body { margin: 0; background: #fff; color: #111; font-family: Georgia, serif; }
	.btn, .card, .title, .nav, .menu, .section, .field, .form, .chip {
		background: #ff0 !important; color: #f0f !important; border: 3px dashed red !important;
		font-family: "Comic Sans MS", cursive !important; text-transform: lowercase !important;
	}
	h1, h2, h3 { color: #f0f; font-family: "Comic Sans MS", cursive; }
	a { color: #00f; text-decoration: underline; }
	.tema-antes, .tema-depois { padding: 24px; font-family: system-ui, sans-serif; }
</style>
</head>
<body>

<div class="tema-antes">
	<h2 class="title">Cabeçalho do tema (fora do plugin)</h2>
	<p>Este bloco usa as classes genéricas do "tema". Ele deve continuar feio —
		e o site abaixo deve continuar intacto.</p>
	<a class="btn" href="#">Botão do tema</a>
</div>

<?php echo $auvp_html; // phpcs:ignore ?>

<div class="tema-depois">
	<h2 class="title">Rodapé do tema (fora do plugin)</h2>
	<p>Se as regras do site tivessem vazado, este texto estaria escuro e em serifada.</p>
</div>

<script src="../auvp-experience/assets/js/app.js"></script>
</body>
</html>
