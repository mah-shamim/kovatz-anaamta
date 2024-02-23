<?php
namespace Jet_Engine\Modules\Dynamic_Visibility;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as DynamicTags;
use Elementor\Repeater;

class Settings {

	public function __construct() {

		$callback = array( $this, 'add_visibility_settings' );

		add_action( 'elementor/element/column/section_advanced/after_section_end', $callback, 10, 2 );
		add_action( 'elementor/element/section/section_advanced/after_section_end', $callback, 10, 2 );
		add_action( 'elementor/element/common/_section_style/after_section_end', $callback, 10, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', $callback, 10, 2 );

		add_action( 'elementor/preview/enqueue_styles', array( $this, 'preview_styles' ) );

		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );

	}

	/**
	 * Add preview styles for elements with dynamic visibility is enabled
	 * @return void
	 */
	public function preview_styles() {
		wp_add_inline_style( 'editor-preview', '.jedv-enabled--yes:not(.elementor-element-editable){opacity: .6;}' );
	}

	/**
	 * Add visibility settings
	 */
	public function add_visibility_settings( $element, $section_id ) {

		$type = $element->get_type();

		$element->start_controls_section(
			'jedv_section',
			array(
				'tab' => Controls_Manager::TAB_ADVANCED,
				'label' => __( 'Dynamic Visibility', 'jet-engine' ),
			)
		);

		$element->add_control(
			'jedv_enabled',
			array(
				'type'           => Controls_Manager::SWITCHER,
				'label'          => __( 'Enable', 'jet-engine' ),
				'render_type'    => 'template',
				'prefix_class'   => 'jedv-enabled--',
				'style_transfer' => false,
			)
		);

		$element->add_control(
			'jedv_type',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Visibility condition type', 'jet-engine' ),
				'label_block' => true,
				'default'     => 'show',
				'options'     => array(
					'show' => __( 'Show element if condition met', 'jet-engine' ),
					'hide' => __( 'Hide element if condition met', 'jet-engine' ),
				),
				'condition'  => array(
					'jedv_enabled' => 'yes',
				),
				'style_transfer' => false,
			)
		);

		$repeater = new Repeater();

		foreach ( Module::instance()->get_condition_controls() as $name => $control_data ) {
			$repeater->add_control( $name, $control_data );
		}

		$element->add_control(
			'jedv_conditions',
			array(
				'label'   => __( 'Conditions', 'jet-engine' ),
				'type'    => 'jet-repeater',
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'jedv_condition' => '',
					)
				),
				'title_field' => '<# var jedv_labels=' . json_encode( Module::instance()->conditions->get_conditions_for_options() ) . ';#> {{{ jedv_labels[jedv_condition] }}}',
				'condition'   => array(
					'jedv_enabled' => 'yes',
				),
				'style_transfer' => false,
			)
		);

		$element->add_control(
			'jedv_relation',
			array(
				'label'   => __( 'Relation', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'AND',
				'options' => array(
					'AND' => __( 'AND', 'jet-engine' ),
					'OR'  => __( 'OR', 'jet-engine' ),
				),
				'condition' => array(
					'jedv_enabled' => 'yes',
				),
				'style_transfer' => false,
			)
		);

		if ( 'column' === $type ) {
			$element->add_control(
				'jedv_resize_columns',
				array(
					'label'     => __( 'Resize other columns', 'jet-engine' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'jedv_enabled' => 'yes',
					),
					'style_transfer' => false,
				)
			);
		}

		$element->end_controls_section();

	}

	public function enqueue_editor_scripts() {
		wp_enqueue_script(
			'jet-engine-dynamic-visibility-editor',
			jet_engine()->modules->modules_url( 'dynamic-visibility/inc/assets/js/elementor-editor.js' ),
			array( 'jquery', 'elementor-editor' ),
			jet_engine()->get_version(),
			true
		);
	}

}
