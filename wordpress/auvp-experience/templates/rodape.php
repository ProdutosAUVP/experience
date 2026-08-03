<?php
/**
 * Rodapé.
 *
 * @package AUVP_Experience
 * @var array $d Dados da seção.
 */

defined( 'ABSPATH' ) || exit;

$auvp_colunas = array();
for ( $auvp_c = 1; $auvp_c <= 3; $auvp_c++ ) {
	$auvp_links = auvp_repeater( $d, 'col' . $auvp_c . '_links' );
	if ( $auvp_links ) {
		$auvp_colunas[] = array(
			'titulo' => auvp_get( $d, 'col' . $auvp_c . '_titulo' ),
			'links'  => $auvp_links,
		);
	}
}
?>
<footer class="auvp-footer">
	<div class="auvp-shell">
		<?php if ( $auvp_colunas ) : ?>
			<div class="auvp-footer__grid">
				<?php foreach ( $auvp_colunas as $auvp_col ) : ?>
					<div class="auvp-footer__col">
						<h4><?php echo esc_html( $auvp_col['titulo'] ); ?></h4>
						<ul>
							<?php foreach ( $auvp_col['links'] as $auvp_link ) : ?>
								<li><a href="<?php echo esc_url( auvp_get( $auvp_link, 'link', '#' ) ); ?>"><?php echo esc_html( auvp_get( $auvp_link, 'label' ) ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<p class="auvp-footer__word" aria-hidden="true"><?php echo esc_html( auvp_get( $d, 'palavra' ) ); ?></p>

		<div class="auvp-footer__bar">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( auvp_get( $d, 'assinatura' ) ); ?></span>
			<span><?php echo esc_html( auvp_get( $d, 'tagline' ) ); ?></span>
		</div>
	</div>
</footer>
