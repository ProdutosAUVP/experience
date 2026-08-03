<?php
/**
 * Seção 4 — Diferencial.
 *
 * @package AUVP_Experience
 * @var array $d Dados da seção.
 */

defined( 'ABSPATH' ) || exit;

$auvp_tags = auvp_repeater( $d, 'tags' );
?>
<section class="auvp-section auvp-edge" id="<?php echo esc_attr( auvp_anchor( $d, 'diferencial' ) ); ?>">
	<div class="auvp-shell auvp-edge__grid">
		<div class="auvp-edge__sticky">
			<p class="auvp-eyebrow" data-reveal><?php echo esc_html( auvp_get( $d, 'eyebrow' ) ); ?></p>
			<h2 class="auvp-edge__title" data-split>
				<?php echo auvp_kses_inline( auvp_get( $d, 'titulo' ) ); ?>
			</h2>
		</div>

		<div class="auvp-edge__body">
			<p class="auvp-lede" data-reveal>
				<?php echo auvp_kses_inline( auvp_get( $d, 'lede' ) ); ?>
			</p>

			<figure class="auvp-edge__figure auvp-media-mask" style="margin:0" data-delay="120">
				<img src="<?php echo esc_url( auvp_image_url( $d, 'imagem', 'img/context-city.svg' ) ); ?>" alt="<?php echo esc_attr( auvp_get( $d, 'alt' ) ); ?>" loading="lazy">
			</figure>

			<p data-reveal>
				<?php echo auvp_kses_inline( auvp_get( $d, 'texto' ) ); ?>
			</p>

			<?php if ( $auvp_tags ) : ?>
				<ul class="auvp-edge__tags" data-reveal data-delay="120">
					<?php foreach ( $auvp_tags as $auvp_tag ) : ?>
						<li><?php echo esc_html( auvp_get( $auvp_tag, 'texto' ) ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
