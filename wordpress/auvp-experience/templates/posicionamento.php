<?php
/**
 * Seção 2 — Prova / posicionamento.
 *
 * @package AUVP_Experience
 * @var array $d Dados da seção.
 */

defined( 'ABSPATH' ) || exit;

$auvp_pilares = auvp_repeater( $d, 'pilares' );
?>
<section class="auvp-section" id="<?php echo esc_attr( auvp_anchor( $d, 'posicionamento' ) ); ?>">
	<div class="auvp-shell">
		<div class="auvp-proof__head">
			<div>
				<p class="auvp-eyebrow" data-reveal><?php echo esc_html( auvp_get( $d, 'eyebrow' ) ); ?></p>
				<h2 class="auvp-proof__statement" data-split>
					<?php echo auvp_kses_inline( auvp_get( $d, 'titulo' ) ); ?>
				</h2>
			</div>
			<p class="auvp-proof__aside" data-reveal data-delay="200">
				<?php echo auvp_kses_inline( auvp_get( $d, 'aside' ) ); ?>
			</p>
		</div>
	</div>

	<?php if ( $auvp_pilares ) : ?>
		<div class="auvp-shell">
			<ul class="auvp-pillars">
				<?php foreach ( array_values( $auvp_pilares ) as $auvp_i => $auvp_p ) : ?>
					<li class="auvp-pillar" data-reveal<?php echo $auvp_i ? ' data-delay="' . esc_attr( $auvp_i * 100 ) . '"' : ''; ?>>
						<span class="auvp-num"><?php echo esc_html( auvp_get( $auvp_p, 'num', str_pad( $auvp_i + 1, 2, '0', STR_PAD_LEFT ) ) ); ?></span>
						<h3><?php echo esc_html( auvp_get( $auvp_p, 'titulo' ) ); ?></h3>
						<p><?php echo esc_html( auvp_get( $auvp_p, 'texto' ) ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
</section>
