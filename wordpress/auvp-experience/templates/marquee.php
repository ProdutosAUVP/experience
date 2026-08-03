<?php
/**
 * Faixa deslizante de assinatura.
 *
 * @package AUVP_Experience
 * @var array $d Dados da seção.
 */

defined( 'ABSPATH' ) || exit;

$auvp_itens = auvp_repeater( $d, 'itens' );

if ( ! $auvp_itens ) {
	return;
}

$auvp_texto_puro = array();
foreach ( $auvp_itens as $auvp_item ) {
	$auvp_texto_puro[] = wp_strip_all_tags( auvp_get( $auvp_item, 'texto' ) );
}
?>
<div class="auvp-marquee" data-speed="<?php echo esc_attr( auvp_get( $d, 'velocidade', '0.5' ) ); ?>" aria-hidden="true">
	<div class="auvp-marquee__track">
		<?php foreach ( $auvp_itens as $auvp_item ) : ?>
			<span class="auvp-marquee__item"><?php echo auvp_kses_inline( auvp_get( $auvp_item, 'texto' ) ); ?></span>
		<?php endforeach; ?>
	</div>
</div>
<p class="auvp-sr-only"><?php echo esc_html( implode( '. ', $auvp_texto_puro ) ); ?>.</p>
