<?php
/**
 * Navegação fixa, menu em tela cheia, índice lateral e overlays.
 *
 * Este bloco é renderizado no rodapé do documento (wp_footer) para que os
 * elementos com position:fixed não fiquem presos a nenhum container do
 * Elementor com transform — o que quebraria o posicionamento.
 *
 * @package AUVP_Experience
 * @var array $d Dados da seção.
 */

defined( 'ABSPATH' ) || exit;

$auvp_links   = auvp_repeater( $d, 'links' );
$auvp_menu_id = 'auvp-menu';
?>
<div class="auvp-grain" aria-hidden="true"></div>
<div class="auvp-cursor" aria-hidden="true"></div>
<div class="auvp-cursor-ring" aria-hidden="true"><span class="auvp-cursor-ring__label"></span></div>
<div class="auvp-progress" aria-hidden="true"><div class="auvp-progress__bar"></div></div>

<?php if ( 'nao' !== auvp_get( $d, 'preloader', 'sim' ) ) : ?>
	<div class="auvp-preloader" role="status" aria-live="polite">
		<div class="auvp-preloader__inner">
			<div class="auvp-preloader__mark"><?php echo esc_html( auvp_get( $d, 'preloader_texto' ) ); ?></div>
			<div class="auvp-preloader__track"><div class="auvp-preloader__fill"></div></div>
			<div class="auvp-preloader__count">000</div>
		</div>
	</div>
<?php endif; ?>

<header class="auvp-nav">
	<div class="auvp-shell auvp-nav__inner">
		<a class="auvp-brand" href="<?php echo esc_url( auvp_get( $d, 'marca_link', '#hero' ) ); ?>" aria-label="<?php esc_attr_e( 'AUVP Experience — início', 'auvp-experience' ); ?>">
			<strong><?php echo esc_html( auvp_get( $d, 'marca_forte' ) ); ?></strong><span><?php echo esc_html( auvp_get( $d, 'marca_leve' ) ); ?></span>
		</a>

		<nav class="auvp-nav__links" aria-label="<?php esc_attr_e( 'Navegação principal', 'auvp-experience' ); ?>">
			<?php foreach ( $auvp_links as $auvp_link ) : ?>
				<a class="auvp-nav__link" href="<?php echo esc_url( auvp_get( $auvp_link, 'link', '#' ) ); ?>"><?php echo esc_html( auvp_get( $auvp_link, 'label' ) ); ?></a>
			<?php endforeach; ?>
			<a class="auvp-btn auvp-btn--sm auvp-nav__cta" href="<?php echo esc_url( auvp_get( $d, 'cta_link', '#aplicar' ) ); ?>" data-magnetic="0.2"><?php echo esc_html( auvp_get( $d, 'cta_label' ) ); ?></a>
		</nav>

		<button class="auvp-burger" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $auvp_menu_id ); ?>">
			<span class="auvp-burger__text"><?php esc_html_e( 'Menu', 'auvp-experience' ); ?></span>
			<span class="auvp-burger__bars" aria-hidden="true"><i></i><i></i></span>
		</button>
	</div>
</header>

<div class="auvp-menu" id="<?php echo esc_attr( $auvp_menu_id ); ?>">
	<div></div>
	<nav class="auvp-shell auvp-menu__body" aria-label="<?php esc_attr_e( 'Navegação em tela cheia', 'auvp-experience' ); ?>">
		<?php foreach ( array_values( $auvp_links ) as $auvp_i => $auvp_link ) : ?>
			<a class="auvp-menu__link" href="<?php echo esc_url( auvp_get( $auvp_link, 'link', '#' ) ); ?>" style="--i:<?php echo esc_attr( $auvp_i ); ?>"><i><?php echo esc_html( str_pad( $auvp_i + 1, 2, '0', STR_PAD_LEFT ) ); ?></i><?php echo esc_html( auvp_get( $auvp_link, 'label' ) ); ?></a>
		<?php endforeach; ?>
		<a class="auvp-menu__link auvp-gold" href="<?php echo esc_url( auvp_get( $d, 'cta_link', '#aplicar' ) ); ?>" style="--i:<?php echo esc_attr( count( $auvp_links ) ); ?>"><i><?php echo esc_html( str_pad( count( $auvp_links ) + 1, 2, '0', STR_PAD_LEFT ) ); ?></i><?php echo esc_html( auvp_get( $d, 'cta_label' ) ); ?></a>
	</nav>
	<div class="auvp-shell auvp-menu__foot">
		<span>AUVP Experience Co.</span>
		<span><?php esc_html_e( 'Imersões estratégicas globais', 'auvp-experience' ); ?></span>
	</div>
</div>

<?php if ( $auvp_links ) : ?>
	<nav class="auvp-dotnav" aria-label="<?php esc_attr_e( 'Índice das seções', 'auvp-experience' ); ?>">
		<?php foreach ( $auvp_links as $auvp_link ) : ?>
			<?php $auvp_href = auvp_get( $auvp_link, 'link', '#' ); ?>
			<?php if ( 0 === strpos( $auvp_href, '#' ) ) : ?>
				<a class="auvp-dotnav__item" href="<?php echo esc_url( $auvp_href ); ?>"><span class="auvp-dotnav__label"><?php echo esc_html( auvp_get( $auvp_link, 'label' ) ); ?></span></a>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if ( 0 === strpos( auvp_get( $d, 'cta_link', '#aplicar' ), '#' ) ) : ?>
			<a class="auvp-dotnav__item" href="<?php echo esc_url( auvp_get( $d, 'cta_link', '#aplicar' ) ); ?>"><span class="auvp-dotnav__label"><?php echo esc_html( auvp_get( $d, 'cta_label' ) ); ?></span></a>
		<?php endif; ?>
	</nav>
<?php endif; ?>
