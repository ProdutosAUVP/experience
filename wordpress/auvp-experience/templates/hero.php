<?php
/**
 * Seção 1 — Hero.
 *
 * @package AUVP_Experience
 * @var array $d Dados da seção.
 */

defined( 'ABSPATH' ) || exit;

$auvp_img   = auvp_image_url( $d, 'imagem', 'img/hero-city.svg' );
$auvp_video = auvp_get( $d, 'video' );
$auvp_selos = auvp_repeater( $d, 'selos' );
?>
<section class="auvp-hero" id="<?php echo esc_attr( auvp_anchor( $d, 'hero' ) ); ?>">
	<div class="auvp-hero__media" data-parallax="0.08">
		<?php if ( $auvp_video ) : ?>
			<video autoplay muted loop playsinline poster="<?php echo esc_url( $auvp_img ); ?>">
				<source src="<?php echo esc_url( $auvp_video ); ?>" type="video/mp4">
			</video>
		<?php else : ?>
			<img src="<?php echo esc_url( $auvp_img ); ?>" alt="<?php echo esc_attr( auvp_get( $d, 'alt' ) ); ?>" fetchpriority="high">
		<?php endif; ?>
	</div>
	<div class="auvp-hero__scrim" aria-hidden="true"></div>

	<div class="auvp-shell auvp-hero__content">
		<p class="auvp-eyebrow auvp-hero__eyebrow"><?php echo esc_html( auvp_get( $d, 'eyebrow' ) ); ?></p>

		<h1 class="auvp-hero__title" data-split><?php echo auvp_kses_inline( auvp_get( $d, 'titulo' ) ); ?></h1>

		<div class="auvp-hero__row">
			<p class="auvp-hero__lede" data-reveal data-delay="260">
				<?php echo auvp_kses_inline( auvp_get( $d, 'lede' ) ); ?>
			</p>
			<div data-reveal data-delay="380">
				<a class="auvp-btn" href="<?php echo esc_url( auvp_get( $d, 'cta_link', '#aplicar' ) ); ?>" data-magnetic="0.25">
					<?php echo esc_html( auvp_get( $d, 'cta_label' ) ); ?>
					<span class="auvp-btn__arrow" aria-hidden="true">&rarr;</span>
				</a>
			</div>
		</div>
	</div>

	<div class="auvp-shell auvp-hero__foot">
		<span class="auvp-scroll-cue"><span class="auvp-scroll-cue__line" aria-hidden="true"></span> <?php echo esc_html( auvp_get( $d, 'rodape' ) ); ?></span>
		<?php if ( $auvp_selos ) : ?>
			<span class="auvp-hero__meta">
				<?php foreach ( $auvp_selos as $auvp_selo ) : ?>
					<span><?php echo esc_html( auvp_get( $auvp_selo, 'texto' ) ); ?></span>
				<?php endforeach; ?>
			</span>
		<?php endif; ?>
	</div>
</section>
