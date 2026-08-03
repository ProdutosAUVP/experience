<?php
/**
 * Seção 3 — Nossas imersões + painel de sugestão de destinos.
 *
 * @package AUVP_Experience
 * @var array $d Dados da seção.
 */

defined( 'ABSPATH' ) || exit;

$auvp_cards = auvp_repeater( $d, 'cards' );
$auvp_chips = auvp_repeater( $d, 'chips' );
$auvp_uid   = 'auvp-vote-' . wp_rand( 1000, 9999 );
?>
<section class="auvp-section" id="<?php echo esc_attr( auvp_anchor( $d, 'imersoes' ) ); ?>">
	<div class="auvp-shell">
		<div class="auvp-dest__head">
			<div>
				<p class="auvp-eyebrow" data-reveal><?php echo esc_html( auvp_get( $d, 'eyebrow' ) ); ?></p>
				<h2 class="auvp-display" data-split><?php echo auvp_kses_inline( auvp_get( $d, 'titulo' ) ); ?></h2>
			</div>
			<p class="auvp-muted auvp-measure-sm" data-reveal data-delay="150">
				<?php echo esc_html( auvp_get( $d, 'aside' ) ); ?>
			</p>
		</div>

		<div class="auvp-dest__grid">
			<?php
			foreach ( array_values( $auvp_cards ) as $auvp_i => $auvp_c ) :
				$auvp_sugerir = 'sugerir' === auvp_get( $auvp_c, 'acao', 'link' );
				$auvp_delay   = $auvp_i ? ' data-delay="' . esc_attr( $auvp_i * 120 ) . '"' : '';
				$auvp_src     = AUVP_Render::media_url( isset( $auvp_c['imagem'] ) ? $auvp_c['imagem'] : '', 'img/dest-next.svg' );
				?>
				<?php if ( $auvp_sugerir ) : ?>
					<button class="auvp-card auvp-card--next" type="button" data-vote-open aria-controls="<?php echo esc_attr( $auvp_uid ); ?>" aria-expanded="false" data-reveal<?php echo $auvp_delay; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-cursor="<?php esc_attr_e( 'Sugerir', 'auvp-experience' ); ?>">
				<?php else : ?>
					<a class="auvp-card" href="<?php echo esc_url( auvp_get( $auvp_c, 'link', '#' ) ); ?>" data-reveal<?php echo $auvp_delay; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-cursor="<?php esc_attr_e( 'Ver', 'auvp-experience' ); ?>">
				<?php endif; ?>

					<div class="auvp-card__media"><img src="<?php echo esc_url( $auvp_src ); ?>" alt="<?php echo esc_attr( auvp_get( $auvp_c, 'alt' ) ); ?>" loading="lazy"></div>
					<span class="auvp-card__index"><?php echo esc_html( auvp_get( $auvp_c, 'indice' ) ); ?></span>
					<h3 class="auvp-card__title"><?php echo esc_html( auvp_get( $auvp_c, 'titulo' ) ); ?></h3>
					<p class="auvp-card__sub"><?php echo esc_html( auvp_get( $auvp_c, 'sub' ) ); ?></p>
					<p class="auvp-card__desc"><?php echo esc_html( auvp_get( $auvp_c, 'texto' ) ); ?></p>
					<span class="auvp-card__cta"><?php echo esc_html( auvp_get( $auvp_c, 'cta_label' ) ); ?> <i aria-hidden="true">&rarr;</i></span>

				<?php if ( $auvp_sugerir ) : ?>
					</button>
				<?php else : ?>
					</a>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if ( $auvp_chips ) : ?>
		<!-- inert enquanto fechado: sem ele, os chips e campos continuam no fluxo de tabulação -->
		<div class="auvp-vote" id="<?php echo esc_attr( $auvp_uid ); ?>" aria-hidden="true" inert>
			<div class="auvp-vote__inner">
				<div class="auvp-shell">
					<div class="auvp-vote__box">
						<div>
							<p class="auvp-eyebrow"><?php echo esc_html( auvp_get( $d, 'vote_eyebrow' ) ); ?></p>
							<h3 class="auvp-title" style="margin-top:1rem"><?php echo esc_html( auvp_get( $d, 'vote_titulo' ) ); ?></h3>
							<p class="auvp-muted" style="margin-top:1rem;max-width:38ch">
								<?php echo esc_html( auvp_get( $d, 'vote_texto' ) ); ?>
							</p>
						</div>

						<form class="auvp-vote__form" data-form data-tipo="sugestao" data-subject="<?php esc_attr_e( 'Sugestão de destino — AUVP Experience', 'auvp-experience' ); ?>" data-success="<?php echo esc_attr( $auvp_uid ); ?>-ok">
							<div class="auvp-chips" role="group" aria-label="<?php esc_attr_e( 'Destinos sugeridos', 'auvp-experience' ); ?>">
								<?php foreach ( $auvp_chips as $auvp_chip ) : ?>
									<button class="auvp-chip" type="button" aria-pressed="false"><?php echo esc_html( auvp_get( $auvp_chip, 'texto' ) ); ?></button>
								<?php endforeach; ?>
							</div>

							<input type="hidden" name="destinos_marcados" data-vote-selected value="">
							<?php AUVP_Forms::hidden_fields(); ?>

							<div class="auvp-field">
								<label for="<?php echo esc_attr( $auvp_uid ); ?>-sug"><?php esc_html_e( 'Sua sugestão', 'auvp-experience' ); ?></label>
								<input id="<?php echo esc_attr( $auvp_uid ); ?>-sug" name="sugestao" type="text" placeholder="<?php esc_attr_e( 'Destino e o que você gostaria de ver lá', 'auvp-experience' ); ?>" required>
								<span class="auvp-field__error"></span>
							</div>

							<div class="auvp-field">
								<label for="<?php echo esc_attr( $auvp_uid ); ?>-email"><?php esc_html_e( 'E-mail', 'auvp-experience' ); ?></label>
								<input id="<?php echo esc_attr( $auvp_uid ); ?>-email" name="email" type="email" placeholder="voce@empresa.com" required>
								<span class="auvp-field__error"></span>
							</div>

							<div class="auvp-vote__actions">
								<button class="auvp-btn" type="submit" data-magnetic="0.2"><?php esc_html_e( 'Enviar sugestão', 'auvp-experience' ); ?> <span class="auvp-btn__arrow" aria-hidden="true">&rarr;</span></button>
								<span class="auvp-form__status" role="status"></span>
							</div>
						</form>

						<div class="auvp-form__success" id="<?php echo esc_attr( $auvp_uid ); ?>-ok">
							<h3 class="auvp-serif"><?php echo esc_html( auvp_get( $d, 'vote_sucesso_titulo' ) ); ?></h3>
							<p><?php echo esc_html( auvp_get( $d, 'vote_sucesso_texto' ) ); ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</section>
