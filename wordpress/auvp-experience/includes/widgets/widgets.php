<?php
/**
 * Um widget por seção. Toda a lógica está em AUVP_Widget; aqui só existe
 * a amarração entre a classe e o slug do registro de conteúdo.
 *
 * @package AUVP_Experience
 */

defined( 'ABSPATH' ) || exit;

/** Hero. */
class AUVP_Widget_Hero extends AUVP_Widget {
	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function auvp_slug() {
		return 'hero';
	}
}

/** Posicionamento. */
class AUVP_Widget_Posicionamento extends AUVP_Widget {
	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function auvp_slug() {
		return 'posicionamento';
	}
}

/** Imersões. */
class AUVP_Widget_Destinos extends AUVP_Widget {
	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function auvp_slug() {
		return 'destinos';
	}
}

/** Diferencial. */
class AUVP_Widget_Diferencial extends AUVP_Widget {
	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function auvp_slug() {
		return 'diferencial';
	}
}

/** Faixa deslizante. */
class AUVP_Widget_Marquee extends AUVP_Widget {
	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function auvp_slug() {
		return 'marquee';
	}
}

/** Networking. */
class AUVP_Widget_Networking extends AUVP_Widget {
	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function auvp_slug() {
		return 'networking';
	}
}

/** Experiência. */
class AUVP_Widget_Experiencia extends AUVP_Widget {
	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function auvp_slug() {
		return 'experiencia';
	}
}

/** FAQ. */
class AUVP_Widget_Faq extends AUVP_Widget {
	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function auvp_slug() {
		return 'faq';
	}
}

/** Candidatura. */
class AUVP_Widget_Candidatura extends AUVP_Widget {
	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function auvp_slug() {
		return 'candidatura';
	}
}

/** Navegação e overlays. */
class AUVP_Widget_Navegacao extends AUVP_Widget {
	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function auvp_slug() {
		return 'navegacao';
	}
}

/** Rodapé. */
class AUVP_Widget_Rodape extends AUVP_Widget {
	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function auvp_slug() {
		return 'rodape';
	}
}

/**
 * Lista de classes registradas no Elementor.
 *
 * @return array
 */
function auvp_widget_classes() {
	return apply_filters(
		'auvp_widget_classes',
		array(
			'AUVP_Widget_Navegacao',
			'AUVP_Widget_Hero',
			'AUVP_Widget_Posicionamento',
			'AUVP_Widget_Destinos',
			'AUVP_Widget_Diferencial',
			'AUVP_Widget_Marquee',
			'AUVP_Widget_Networking',
			'AUVP_Widget_Experiencia',
			'AUVP_Widget_Faq',
			'AUVP_Widget_Candidatura',
			'AUVP_Widget_Rodape',
		)
	);
}
