<?php
/**
 * Seção 6 — Experiência.
 *
 * @package AUVP_Experience
 * @var array $d Dados da seção.
 */

defined( 'ABSPATH' ) || exit;

$auvp_itens = array_values( auvp_repeater( $d, 'itens' ) );
?>
<section class="auvp-section" id="<?php echo esc_attr( auvp_anchor( $d, 'experiencia' ) ); ?>">
	<div class="auvp-shell auvp-care__grid">
		<div>
			<p class="auvp-eyebrow" data-reveal><?php echo esc_html( auvp_get( $d, 'eyebrow' ) ); ?></p>
			<h2 class="auvp-display" data-split style="margin:1.25rem 0 2.5rem;max-width:16ch">
				<?php echo auvp_kses_inline( auvp_get( $d, 'titulo' ) ); ?>
			</h2>

			<div class="auvp-care__list">
				<?php foreach ( $auvp_itens as $auvp_i => $auvp_item ) : ?>
					<button class="auvp-care__item" type="button" aria-expanded="<?php echo $auvp_i ? 'false' : 'true'; ?>">
						<span class="auvp-num"><?php echo esc_html( auvp_get( $auvp_item, 'num', str_pad( $auvp_i + 1, 2, '0', STR_PAD_LEFT ) ) ); ?></span>
						<span>
							<h3 class="auvp-serif"><?php echo esc_html( auvp_get( $auvp_item, 'titulo' ) ); ?></h3>
							<span class="auvp-care__text"><p><?php echo esc_html( auvp_get( $auvp_item, 'texto' ) ); ?></p></span>
						</span>
					</button>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="auvp-care__stage" aria-hidden="true">
			<svg class="auvp-care__rings" viewBox="0 0 400 500" preserveAspectRatio="xMidYMid slice" fill="none" stroke="#c9a15b">
				<circle cx="200" cy="250" r="90" stroke-width="0.6" opacity="0.35"/>
				<circle cx="200" cy="250" r="140" stroke-width="0.6" opacity="0.25"/>
				<circle cx="200" cy="250" r="196" stroke-width="0.6" opacity="0.16"/>
				<circle cx="200" cy="250" r="258" stroke-width="0.6" opacity="0.1"/>
				<line x1="0" y1="250" x2="400" y2="250" stroke-width="0.5" opacity="0.12"/>
				<line x1="200" y1="0" x2="200" y2="500" stroke-width="0.5" opacity="0.12"/>
			</svg>
			<?php foreach ( $auvp_itens as $auvp_i => $auvp_item ) : ?>
				<div class="auvp-care__plate<?php echo $auvp_i ? '' : ' auvp-is-on'; ?>">
					<figure><?php echo esc_html( auvp_get( $auvp_item, 'num', str_pad( $auvp_i + 1, 2, '0', STR_PAD_LEFT ) ) ); ?></figure>
					<p><?php echo esc_html( auvp_get( $auvp_item, 'legenda' ) ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
