<?php
/**
 * Seção 8 — Candidatura.
 *
 * @package AUVP_Experience
 * @var array $d Dados da seção.
 */

defined( 'ABSPATH' ) || exit;

$auvp_pontos   = auvp_repeater( $d, 'pontos' );
$auvp_perfis   = auvp_repeater( $d, 'perfis' );
$auvp_destinos = auvp_repeater( $d, 'destinos' );
$auvp_uid      = 'auvp-ap-' . wp_rand( 1000, 9999 );
?>
<section class="auvp-section auvp-apply" id="<?php echo esc_attr( auvp_anchor( $d, 'aplicar' ) ); ?>">
	<div class="auvp-shell auvp-apply__grid">
		<div>
			<p class="auvp-eyebrow" data-reveal><?php echo esc_html( auvp_get( $d, 'eyebrow' ) ); ?></p>
			<h2 class="auvp-apply__title" data-split><?php echo auvp_kses_inline( auvp_get( $d, 'titulo' ) ); ?></h2>

			<?php if ( $auvp_pontos ) : ?>
				<ul class="auvp-apply__points">
					<?php foreach ( array_values( $auvp_pontos ) as $auvp_i => $auvp_ponto ) : ?>
						<li data-reveal<?php echo $auvp_i ? ' data-delay="' . esc_attr( $auvp_i * 90 ) . '"' : ''; ?>><?php echo esc_html( auvp_get( $auvp_ponto, 'texto' ) ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div>
			<form class="auvp-form" data-form data-tipo="candidatura" data-subject="<?php esc_attr_e( 'Candidatura — AUVP Experience', 'auvp-experience' ); ?>" data-success="<?php echo esc_attr( $auvp_uid ); ?>-ok" data-reveal>
				<?php AUVP_Forms::hidden_fields(); ?>

				<div class="auvp-form__row">
					<div class="auvp-field">
						<label for="<?php echo esc_attr( $auvp_uid ); ?>-nome"><?php esc_html_e( 'Nome completo', 'auvp-experience' ); ?></label>
						<input id="<?php echo esc_attr( $auvp_uid ); ?>-nome" name="nome" type="text" autocomplete="name" required>
						<span class="auvp-field__error"></span>
					</div>
					<div class="auvp-field">
						<label for="<?php echo esc_attr( $auvp_uid ); ?>-email"><?php esc_html_e( 'E-mail', 'auvp-experience' ); ?></label>
						<input id="<?php echo esc_attr( $auvp_uid ); ?>-email" name="email" type="email" autocomplete="email" required>
						<span class="auvp-field__error"></span>
					</div>
				</div>

				<div class="auvp-form__row">
					<div class="auvp-field">
						<label for="<?php echo esc_attr( $auvp_uid ); ?>-tel"><?php esc_html_e( 'WhatsApp', 'auvp-experience' ); ?></label>
						<input id="<?php echo esc_attr( $auvp_uid ); ?>-tel" name="whatsapp" type="tel" autocomplete="tel" placeholder="(00) 00000-0000" required>
						<span class="auvp-field__error"></span>
					</div>
					<div class="auvp-field">
						<label for="<?php echo esc_attr( $auvp_uid ); ?>-perfil"><?php esc_html_e( 'Seu perfil', 'auvp-experience' ); ?></label>
						<select id="<?php echo esc_attr( $auvp_uid ); ?>-perfil" name="perfil" required>
							<option value=""><?php esc_html_e( 'Selecione', 'auvp-experience' ); ?></option>
							<?php foreach ( $auvp_perfis as $auvp_op ) : ?>
								<option><?php echo esc_html( auvp_get( $auvp_op, 'texto' ) ); ?></option>
							<?php endforeach; ?>
						</select>
						<span class="auvp-field__error"></span>
					</div>
				</div>

				<div class="auvp-field">
					<label for="<?php echo esc_attr( $auvp_uid ); ?>-destino"><?php esc_html_e( 'Destino de interesse', 'auvp-experience' ); ?></label>
					<select id="<?php echo esc_attr( $auvp_uid ); ?>-destino" name="destino" required>
						<option value=""><?php esc_html_e( 'Selecione', 'auvp-experience' ); ?></option>
						<?php foreach ( $auvp_destinos as $auvp_op ) : ?>
							<option><?php echo esc_html( auvp_get( $auvp_op, 'texto' ) ); ?></option>
						<?php endforeach; ?>
					</select>
					<span class="auvp-field__error"></span>
				</div>

				<div class="auvp-field">
					<label for="<?php echo esc_attr( $auvp_uid ); ?>-motivo"><?php esc_html_e( 'Em uma frase, o que você quer extrair da imersão?', 'auvp-experience' ); ?></label>
					<textarea id="<?php echo esc_attr( $auvp_uid ); ?>-motivo" name="motivo" rows="3" required></textarea>
					<span class="auvp-field__error"></span>
				</div>

				<div style="display:grid;gap:1rem;justify-items:start">
					<button class="auvp-btn auvp-btn--solid" type="submit" data-magnetic="0.2">
						<?php echo esc_html( auvp_get( $d, 'botao' ) ); ?> <span class="auvp-btn__arrow" aria-hidden="true">&rarr;</span>
					</button>
					<span class="auvp-form__status" role="status"></span>
					<p class="auvp-form__note"><?php echo esc_html( auvp_get( $d, 'nota' ) ); ?></p>
				</div>
			</form>

			<div class="auvp-form__success" id="<?php echo esc_attr( $auvp_uid ); ?>-ok">
				<h3 class="auvp-serif"><?php echo esc_html( auvp_get( $d, 'sucesso_titulo' ) ); ?></h3>
				<p><?php echo esc_html( auvp_get( $d, 'sucesso_texto' ) ); ?></p>
			</div>
		</div>
	</div>
</section>
