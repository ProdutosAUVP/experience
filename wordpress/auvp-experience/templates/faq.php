<?php
/**
 * Seção 7 — FAQ.
 *
 * @package AUVP_Experience
 * @var array $d Dados da seção.
 */

defined( 'ABSPATH' ) || exit;

$auvp_perguntas = auvp_repeater( $d, 'perguntas' );
?>
<section class="auvp-section" id="<?php echo esc_attr( auvp_anchor( $d, 'faq' ) ); ?>">
	<div class="auvp-shell auvp-faq__grid">
		<div class="auvp-faq__aside">
			<p class="auvp-eyebrow" data-reveal><?php echo esc_html( auvp_get( $d, 'eyebrow' ) ); ?></p>
			<h2 class="auvp-title" data-split><?php echo auvp_kses_inline( auvp_get( $d, 'titulo' ) ); ?></h2>
			<p class="auvp-muted auvp-measure-sm" data-reveal data-delay="150">
				<?php echo esc_html( auvp_get( $d, 'aside' ) ); ?>
			</p>
		</div>

		<div class="auvp-acc" data-acc="single" data-reveal>
			<?php foreach ( $auvp_perguntas as $auvp_q ) : ?>
				<div class="auvp-acc__item">
					<button class="auvp-acc__trigger" type="button" aria-expanded="false">
						<h3><?php echo esc_html( auvp_get( $auvp_q, 'pergunta' ) ); ?></h3>
						<span class="auvp-acc__sign" aria-hidden="true"></span>
					</button>
					<div class="auvp-acc__panel"><div><p><?php echo esc_html( auvp_get( $auvp_q, 'resposta' ) ); ?></p></div></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
