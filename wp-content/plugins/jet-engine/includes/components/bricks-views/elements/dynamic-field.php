<?php

namespace Jet_Engine\Bricks_Views\Elements;

use Bricks\Element;
use Jet_Engine\Bricks_Views\Helpers\Options_Converter;
use Jet_Engine\Bricks_Views\Helpers\Controls_Hook_Bridge;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Dynamic_Field extends Base {
	// Element properties
	public $category = 'jetengine'; // Use predefined element category 'general'
	public $name = 'jet-engine-listing-dynamic-field'; // Make sure to prefix your elements
	public $icon = 'jet-engine-icon-dynamic-field'; // Themify icon font class
	public $css_selector = ''; // Default CSS selector
	public $scripts = [ 'jetEngineBricks' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'dynamic-field';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Dynamic Field', 'jet-engine' );
	}

	// Set builder control groups
	public function set_control_groups() {

		$this->register_general_group();
		$this->register_field_group();
		$this->register_icon_group();
		$this->register_misc_group();

	}

	// Set builder controls
	public function set_controls() {

		$this->register_general_controls();
		$this->register_field_controls();
		$this->register_icon_controls();
		$this->register_misc_controls();

	}

	public function register_general_group() {
		$this->register_jet_control_group(
			'section_general',
			[
				'title' => esc_html__( 'General', 'jet-engine' ),
				'tab'   => 'content',
			]
		);
	}

	public function register_general_controls() {

		$this->start_jet_control_group( 'section_general' );

		$this->register_jet_control(
			'dynamic_field_source',
			[
				'tab'        => 'content',
				'label'      => esc_html__( 'Source', 'jet-engine' ),
				'type'       => 'select',
				'options'    => jet_engine()->listings->data->get_field_sources(),
				'searchable' => true,
				'default'    => 'object',
			]
		);

		$this->register_jet_control(
			'dynamic_field_relation_type',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Get', 'jet-engine' ),
				'type'     => 'select',
				'options'  => [
					'grandparents'  => esc_html__( 'Grandparent Posts', 'jet-engine' ),
					'grandchildren' => esc_html__( 'Grandchildren Posts', 'jet-engine' ),
				],
				'default'  => 'grandparents',
				'required' => [ 'dynamic_field_source', '=', 'relations_hierarchy' ],
			]
		);

		$this->register_jet_control(
			'dynamic_field_relation_post_type',
			[
				'tab'        => 'content',
				'label'      => esc_html__( 'From post type', 'jet-engine' ),
				'type'       => 'select',
				'options'    => jet_engine()->listings->get_post_types_for_options(),
				'searchable' => true,
				'required'   => [ 'dynamic_field_source', '=', 'relations_hierarchy' ],
			]
		);

		$object_fields = jet_engine()->listings->data->get_object_fields();

		if ( ! empty( $object_fields ) ) {

			$this->register_jet_control(
				'dynamic_field_post_object',
				[
					'tab'        => 'content',
					'label'      => esc_html__( 'Object field', 'jet-engine' ),
					'type'       => 'select',
					'options'    => Options_Converter::convert_select_groups_to_options( $object_fields ),
					'searchable' => true,
					'default'    => 'post_title',
					'required'   => [ 'dynamic_field_source', '=', 'object' ],
				]
			);
		}

		$this->register_jet_control(
			'dynamic_field_wp_excerpt',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Automatically generated excerpt', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => false,
				'required' => [
					[ 'dynamic_field_source', '=', 'object' ],
					[ 'dynamic_field_post_object', '=', 'post_excerpt' ],
				],
			]
		);

		$this->register_jet_control(
			'dynamic_excerpt_more',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'More string', 'jet-engine' ),
				'type'           => 'text',
				'hasDynamicData' => false,
				'default'        => '...',
				'required'       => [
					[ 'dynamic_field_source', '=', 'object' ],
					[ 'dynamic_field_post_object', '=', 'post_excerpt' ],
					[ 'dynamic_field_wp_excerpt', '=', true ],
				],
			]
		);

		$this->register_jet_control(
			'dynamic_excerpt_length',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Custom length', 'jet-engine' ),
				'type'     => 'number',
				'min'      => 0,
				'max'      => 300,
				'default'  => 0,
				'required' => [
					[ 'dynamic_field_source', '=', 'object' ],
					[ 'dynamic_field_post_object', '=', 'post_excerpt' ],
					[ 'dynamic_field_wp_excerpt', '=', true ],
				],
			]
		);

		$meta_fields = $this->get_meta_fields_for_post_type();

		if ( ! empty( $meta_fields ) ) {

			$this->register_jet_control(
				'dynamic_field_post_meta',
				[
					'tab'      => 'content',
					'label'    => esc_html__( 'Meta field', 'jet-engine' ),
					'type'     => 'select',
					'options'  => Options_Converter::convert_select_groups_to_options( $meta_fields ),
					'required' => [ 'dynamic_field_source', '=', 'meta' ],
				]
			);
		}

		if ( jet_engine()->options_pages ) {

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'plain' );

			if ( ! empty( $options_pages_select ) ) {
				$this->register_jet_control(
					'dynamic_field_option',
					[
						'tab'      => 'content',
						'label'    => esc_html__( 'Option', 'jet-engine' ),
						'type'     => 'select',
						'options'  => Options_Converter::convert_select_groups_to_options( $options_pages_select ),
						'required' => [ 'dynamic_field_source', '=', 'options_page' ],
					]
				);
			}

		}

		$this->register_jet_control(
			'dynamic_field_var_name',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Variable name', 'jet-engine' ),
				'type'     => 'text',
				'required' => [ 'dynamic_field_source', '=', 'query_var' ],
			]
		);

		$hooks = new Controls_Hook_Bridge( $this );
		$hooks->do_action( 'jet-engine/listings/dynamic-field/source-controls' );

		$this->register_jet_control(
			'dynamic_field_post_meta_custom',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Custom Object field / Meta field / Repeater key', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Note: this field will override Object field / Meta field value', 'jet-engine' ),
				'required'    => [ 'dynamic_field_source', '!=', [ 'query_var', 'options_page', 'relations_hierarchy' ] ],
			]
		);

		$this->register_jet_control(
			'selected_field_icon',
			[
				'tab'   => 'content',
				'label' => esc_html__( 'Field icon', 'jet-engine' ),
				'type'  => 'icon',
				'css'   => [
					[
						'selector' => $this->css_selector( '__icon svg' ), // Use to target SVG file
					],
				],
			]
		);

		$this->register_jet_control(
			'field_tag',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'HTML tag', 'jet-engine' ),
				'type'    => 'select',
				'options' => [
					'div'  => 'DIV',
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'p'    => 'P',
					'span' => 'SPAN',
				],
				'default' => 'div',
			]
		);

		$this->register_jet_control(
			'hide_if_empty',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Hide if value is empty', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => false,

			]
		);

		$this->register_jet_control(
			'field_fallback',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Fallback', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Show this if field value is empty', 'jet-engine' ),
				'required'    => [ 'hide_if_empty', '=', false ],
			]
		);

		$this->register_jet_control(
			'dynamic_field_filter',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Filter field output', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'filter_callback',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Callback', 'jet-engine' ),
				'type'     => 'select',
				'options'  => jet_engine()->listings->get_allowed_callbacks(),
				'required' => [ 'dynamic_field_filter', '=', true ],
			]
		);

		foreach ( jet_engine()->listings->get_callbacks_args() as $control_name => $control_args ) {
			$control_args = Options_Converter::convert( $control_args );

			if ( $control_name === 'img_slider_cols' ) {
				$control_args = array_merge(
					$control_args,
					[ 'css' => [] ],
				);
			}

			$this->register_jet_control( $control_name, $control_args );
		}

		$this->register_jet_control(
			'dynamic_field_custom',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Customize field output', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'dynamic_field_format',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Field format', 'jet-engine' ),
				'type'        => 'textarea',
				'default'     => '%s',
				'description' => esc_html__( '%s will be replaced with field value. If you need use plain % sign, replace it with %% (for example for JetEngine macros wrappers)', 'jet-engine' ),
				'required'    => [ 'dynamic_field_custom', '=', true ],
			]
		);

		$this->register_jet_control(
			'object_context',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Context', 'jet-engine' ),
				'type'    => 'select',
				'options' => jet_engine()->listings->allowed_context_list(),
				'default' => 'default_object',
			]
		);

		$this->end_jet_control_group();

	}

	/**
	 * Register non-DOM optimized control group
	 * @return [type] [description]
	 */
	public function register_field_group() {

		if ( $this->prevent_wrap() ) {
			return;
		}

		$this->register_jet_control_group(
			'section_field_style',
			[
				'title' => esc_html__( 'Field', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

	}

	/**
	 * Register non-DOM optimized control
	 * @return [type] [description]
	 */
	public function register_field_controls() {

		if ( $this->prevent_wrap() ) {
			return;
		}

		$this->start_jet_control_group( 'section_field_style' );

		$this->register_jet_control(
			'field_width',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Field content width', 'jet-engine' ),
				'type'    => 'select',
				'options' => [
					'auto' => esc_html__( 'Auto', 'jet-engine' ),
					'100%' => esc_html__( 'Fullwidth', 'jet-engine' ),
				],
				'default' => 'auto',
				'css'     => [
					[
						'property' => 'width',
						'selector' => $this->css_selector( '__inline-wrap' ) . ', ' . $this->css_selector( '__content' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'field_display',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Display', 'jet-engine' ),
				'type'    => 'select',
				'options' => [
					'inline'    => esc_html__( 'Inline', 'jet-engine' ),
					'multiline' => esc_html__( 'Multiline', 'jet-engine' ),
				],
				'default' => 'inline',
			]
		);

		$this->end_jet_control_group();

	}

	public function register_icon_group() {

		$this->register_jet_control_group(
			'section_icon_style',
			[
				'title'    => esc_html__( 'Icon', 'jet-engine' ),
				'tab'      => 'style',
				'required' => [ 'selected_field_icon', '!=', '' ],
			]
		);

	}

	public function register_icon_controls() {

		$this->start_jet_control_group( 'section_icon_style' );

		if ( ! $this->prevent_wrap() ) {
			$this->register_jet_control(
				'field_icon_direction',
				[
					'tab'       => 'style',
					'label'     => esc_html__( 'Direction', 'jet-engine' ),
					'type'      => 'direction',
					'direction' => 'row',
					'css'       => [
						[
							'property' => 'flex-direction',
							'selector' => '.display-multiline, ' . $this->css_selector( '__inline-wrap' ),
						],
					],
				]
			);
		}

		$this->register_jet_control(
			'field_icon_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Icon color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $this->css_selector( '__icon' ),
					],
					[
						'property' => 'fill',
						'selector' => $this->css_selector( '__icon :is(svg)' ) . ', ' . $this->css_selector( '__icon :is(path)' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'field_icon_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Icon size', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'font-size',
						'selector' => $this->css_selector( '__icon' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'field_icon_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Icon gap', 'jet-engine' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '12px',
				'css'     => [
					[
						'property' => 'gap',
						'selector' => ! $this->prevent_wrap() ? '.display-multiline, ' . $this->css_selector( '__inline-wrap' ) : '',
					],
				],
			]
		);

		$this->end_jet_control_group();

	}

	public function register_misc_group() {

		$this->register_jet_control_group(
			'section_misc_style',
			[
				'title'    => esc_html__( 'Misc', 'jet-engine' ),
				'tab'      => 'style',
				'required' => [ 'filter_callback', '=', ['jet_engine_img_gallery_slider', 'jet_engine_img_gallery_grid'] ],
			]
		);

	}

	public function register_misc_controls() {

		$this->start_jet_control_group( 'section_misc_style' );

		do_action( 'jet-engine/bricks-views/dynamic-field/misc-style-controls', $this );

		$this->end_jet_control_group();

	}

	// Enqueue element styles and scripts
	public function enqueue_scripts() {

		wp_enqueue_style( 'jet-engine-frontend' );

		do_action( 'jet-engine/bricks-views/dynamic-field/assets', $this );

	}

	/**
	 * Fixed an issue #(2307) with Bricks Theme styles and headings in a dynamic field.
	 */
	public function set_controls_before() {
		parent::set_controls_before();

		if ( empty( $this->css_selector ) ) {
			$this->controls['_typography']['css'][0]['selector'] = '&, .jet-listing-dynamic-field__content';
		}
	}

	// Render element HTML
	public function render() {

		parent::render();

		$this->enqueue_scripts();

		$render = $this->get_jet_render_instance();

		// STEP: Dynamic field renderer class not found: Show placeholder text
		if ( ! $render ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Dynamic field renderer class not found', 'jet-engine' )
				]
			);
		}

		echo "<div {$this->render_attributes( '_root' )}>";
		$render->render_content();
		echo "</div>";
	}

	public function parse_jet_render_attributes( $attrs = [] ) {

		$attrs['selected_field_icon']    = isset( $attrs['selected_field_icon'] ) ? Element::render_icon( $attrs['selected_field_icon'] ) : null;
		$attrs['related_list_is_linked'] = $attrs['related_list_is_linked'] ?? false;
		$attrs['prevent_wrap']           = true;

		return $attrs;
	}

	public function css_selector( $mod = null ) {
		return sprintf( '%1$s%2$s', '.jet-listing-dynamic-field', $mod );
	}

	/**
	 * Get meta fields for post type
	 *
	 * @return array
	 */
	public function get_meta_fields_for_post_type() {

		if ( jet_engine()->meta_boxes ) {
			return jet_engine()->meta_boxes->get_fields_for_select( 'plain' );
		} else {
			return array();
		}

	}
}