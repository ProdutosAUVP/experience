<?php
/**
 * Seção 5 — Networking.
 *
 * @package AUVP_Experience
 * @var array $d Dados da seção.
 */

defined( 'ABSPATH' ) || exit;

$auvp_perfis = auvp_repeater( $d, 'perfis' );
?>
<section class="auvp-section auvp-net" id="<?php echo esc_attr( auvp_anchor( $d, 'networking' ) ); ?>">
	<div class="auvp-shell">
		<p class="auvp-eyebrow" data-reveal><?php echo esc_html( auvp_get( $d, 'eyebrow' ) ); ?></p>
		<h2 class="auvp-net__statement" data-split><?php echo auvp_kses_inline( auvp_get( $d, 'titulo' ) ); ?></h2>
		<p class="auvp-net__complement" data-reveal data-delay="180">
			<?php echo auvp_kses_inline( auvp_get( $d, 'complemento' ) ); ?>
		</p>
	</div>

	<?php if ( $auvp_perfis ) : ?>
		<div class="auvp-shell">
			<div class="auvp-hscroll" data-cursor="<?php esc_attr_e( 'Arraste', 'auvp-experience' ); ?>">
				<div class="auvp-hscroll__track">
					<?php foreach ( array_values( $auvp_perfis ) as $auvp_i => $auvp_p ) : ?>
						<article class="auvp-profile">
							<span class="auvp-profile__idx"><?php echo esc_html( auvp_get( $auvp_p, 'num', str_pad( $auvp_i + 1, 2, '0', STR_PAD_LEFT ) ) ); ?></span>
							<h3 class="auvp-serif"><?php echo esc_html( auvp_get( $auvp_p, 'titulo' ) ); ?></h3>
							<p><?php echo esc_html( auvp_get( $auvp_p, 'texto' ) ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
			<p class="auvp-hscroll__hint"><span aria-hidden="true"></span> <?php echo esc_html( auvp_get( $d, 'dica' ) ); ?></p>
		</div>
	<?php endif; ?>
</section>
