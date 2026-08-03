<?php
/**
 * Widget base: monta os controles a partir do registro de seções.
 *
 * @package AUVP_Experience
 */

defined( 'ABSPATH' ) || exit;

/**
 * Base dos widgets do Elementor.
 */
abstract class AUVP_Widget extends \Elementor\Widget_Base {

	/**
	 * Slug da seção no registro.
	 *
	 * @return string
	 */
	abstract public function auvp_slug();

	/**
	 * Definição da seção.
	 *
	 * @return array
	 */
	protected function definition() {
		$definition = AUVP_Content::section( $this->auvp_slug() );

		return $definition ? $definition : array(
			'title'  => $this->auvp_slug(),
			'icon'   => 'eicon-text',
			'fields' => array(),
		);
	}

	/**
	 * Nome interno do widget.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'auvp-' . str_replace( '_', '-', $this->auvp_slug() );
	}

	/**
	 * Título no painel.
	 *
	 * @return string
	 */
	public function get_title() {
		$definition = $this->definition();

		return $definition['title'];
	}

	/**
	 * Ícone no painel.
	 *
	 * @return string
	 */
	public function get_icon() {
		$definition = $this->definition();

		return isset( $definition['icon'] ) ? $definition['icon'] : 'eicon-text';
	}

	/**
	 * Categoria.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'auvp-experience' );
	}

	/**
	 * Palavras-chave de busca.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'auvp', 'experience', 'imersão', $this->auvp_slug() );
	}

	/**
	 * O widget depende do CSS/JS do plugin.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'auvp-experience' );
	}

	/**
	 * Script do plugin.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'auvp-experience' );
	}

	/**
	 * Monta os controles.
	 */
	protected function register_controls() {
		$definition = $this->definition();

		$this->start_controls_section(
			'auvp_conteudo',
			array(
				'label' => __( 'Conteúdo', 'auvp-experience' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		foreach ( $definition['fields'] as $key => $field ) {
			if ( 'repeater' === $field['type'] ) {
				$this->add_repeater( $key, $field );
				continue;
			}

			$this->add_control( $key, $this->control_args( $field ) );
		}

		$this->end_controls_section();
	}

	/**
	 * Converte a definição de um campo em argumentos de controle.
	 *
	 * @param array $field Definição.
	 * @return array
	 */
	protected function control_args( $field ) {
		$args = array(
			'label'       => $field['label'],
			'label_block' => true,
		);

		if ( isset( $field['desc'] ) ) {
			$args['description'] = $field['desc'];
		}
		if ( isset( $field['default'] ) ) {
			$args['default'] = $field['default'];
		}

		switch ( $field['type'] ) {
			case 'textarea':
				$args['type'] = \Elementor\Controls_Manager::TEXTAREA;
				$args['rows'] = 4;
				break;

			case 'media':
				$args['type'] = \Elementor\Controls_Manager::MEDIA;
				unset( $args['label_block'] );
				if ( isset( $field['fallback'] ) ) {
					$args['description'] = sprintf(
						/* translators: %s: nome do arquivo padrão. */
						__( 'Em branco usa a arte que acompanha o plugin (%s).', 'auvp-experience' ),
						basename( $field['fallback'] )
					);
				}
				break;

			case 'select':
				$args['type']    = \Elementor\Controls_Manager::SELECT;
				$args['options'] = $field['options'];
				break;

			case 'url':
				$args['type']        = \Elementor\Controls_Manager::TEXT;
				$args['placeholder'] = '#aplicar';
				break;

			default:
				$args['type'] = \Elementor\Controls_Manager::TEXT;
		}

		return $args;
	}

	/**
	 * Adiciona um repetidor.
	 *
	 * @param string $key   Chave.
	 * @param array  $field Definição.
	 */
	protected function add_repeater( $key, $field ) {
		$repeater = new \Elementor\Repeater();

		foreach ( $field['fields'] as $sub_key => $sub_field ) {
			$repeater->add_control( $sub_key, $this->control_args( $sub_field ) );
		}

		$args = array(
			'type'    => \Elementor\Controls_Manager::REPEATER,
			'label'   => $field['label'],
			'fields'  => $repeater->get_controls(),
			'default' => isset( $field['default'] ) ? $field['default'] : array(),
		);

		if ( isset( $field['titulo'] ) ) {
			$args['title_field'] = '{{{ ' . $field['titulo'] . ' }}}';
		}

		$this->add_control( $key, $args );
	}

	/**
	 * Renderiza a seção.
	 */
	protected function render() {
		// O HTML já sai escapado dos templates.
		echo AUVP_Render::section( $this->auvp_slug(), $this->get_settings_for_display() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
