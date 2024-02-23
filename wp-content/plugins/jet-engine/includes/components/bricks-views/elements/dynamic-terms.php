<?php

namespace Jet_Engine\Bricks_Views\Elements;

use Jet_Engine\Bricks_Views\Helpers\Options_Converter;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Dynamic_Terms extends Base {

	// Element properties
	public $category = 'jetengine'; // Use predefined element category 'general'
	public $name = 'jet-engine-listing-dynamic-terms'; // Make sure to prefix your elements
	public $icon = 'jet-engine-icon-dynamic-terms'; // Themify icon font class
	public $css_selector = ''; // Default CSS selector
	public $scripts = [ 'jetEngineBricks' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'dynamic-terms';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Dynamic Terms', 'jet-engine' );
	}

	// Set builder control groups
	public function set_control_groups() {
		$this->control_group_general();
		$this->control_group_general_style();
		$this->control_group_icon_style();
		$this->control_group_link_style();
		$this->control_group_text_style();
	}

	// Set builder controls
	public function set_controls() {
		$this->controls_general();
		$this->controls_general_style();
		$this->controls_icon_style();
		$this->controls_link_style();
		$this->controls_text_style();
	}

	// Start general
	public function control_group_general() {
		$this->register_jet_control_group(
			'section_general',
			[
				'title' => esc_html__( 'General', 'jet-engine' ),
				'tab'   => 'content',
			]
		);
	}

	public function controls_general() {
		$this->start_jet_control_group( 'section_general' );

		$tax_for_options = $this->get_taxonomies_for_options();

		$this->register_jet_control(
			'from_tax',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'From taxonomy', 'jet-engine' ),
				'type'    => 'select',
				'options' => Options_Converter::convert_select_groups_to_options( $tax_for_options ),
				'default' => '',
			]
		);

		$this->register_jet_control(
			'show_all_terms',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Show all terms', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => true,
			]
		);

		$this->register_jet_control(
			'terms_num',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Terms number to show', 'jet-engine' ),
				'type'     => 'number',
				'min'      => 1,
				'max'      => 20,
				'default'  => 1,
				'required' => [
					[ 'show_all_terms', '=', false ],
				],
			]
		);

		$this->register_jet_control(
			'terms_delimiter',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Delimiter', 'jet-engine' ),
				'type'           => 'text',
				'hasDynamicData' => false,
				'default'        => ',',
			]
		);

		$this->register_jet_control(
			'terms_linked',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Linked terms', 'jet-engine' ),
				'description' => esc_html__( 'Terms labels are linked to term archive page', 'jet-engine' ),
				'type'        => 'checkbox',
				'default'     => true,
			]
		);

		$this->register_jet_control(
			'selected_terms_icon',
			[
				'tab'   => 'content',
				'label' => esc_html__( 'Terms icon', 'jet-engine' ),
				'type'  => 'icon',
			]
		);

		$this->register_jet_control(
			'terms_prefix',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Text before terms list', 'jet-engine' ),
				'type'           => 'text',
				'hasDynamicData' => false,
				'default'        => '',
			]
		);

		$this->register_jet_control(
			'terms_suffix',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Text after terms list', 'jet-engine' ),
				'type'           => 'text',
				'hasDynamicData' => false,
				'default'        => '',
			]
		);

		$this->register_jet_control(
			'orderby',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Order by', 'jet-engine' ),
				'type'    => 'select',
				'options' => [
					'name'        => esc_html__( 'Name', 'jet-engine' ),
					'slug'        => esc_html__( 'Slug', 'jet-engine' ),
					'term_group'  => esc_html__( 'Term group', 'jet-engine' ),
					'term_id'     => esc_html__( 'Term ID', 'jet-engine' ),
					'description' => esc_html__( 'Description', 'jet-engine' ),
					'parent'      => esc_html__( 'Parent', 'jet-engine' ),
					'term_order'  => esc_html__( 'Term Order', 'jet-engine' ),
					'count'       => esc_html__( 'By the number of objects associated with the term', 'jet-engine' ),
				],
				'default' => 'name',
			]
		);

		$this->register_jet_control(
			'order',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Order', 'jet-engine' ),
				'type'    => 'select',
				'options' => [
					'ASC'  => esc_html__( 'ASC', 'jet-engine' ),
					'DESC' => esc_html__( 'DESC', 'jet-engine' ),
				],
				'default' => 'ASC',
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
	// End general

	// Start general style
	public function control_group_general_style() {
		$this->register_jet_control_group(
			'section_general_style',
			[
				'title' => esc_html__( 'General', 'jet-engine' ),
				'tab'   => 'style',
			]
		);
	}

	public function controls_general_style() {
		$this->start_jet_control_group( 'section_general_style' );

		$this->register_jet_control(
			'terms_direction',
			[
				'tab'       => 'style',
				'label'     => esc_html__( 'Direction', 'jet-engine' ),
				'type'      => 'direction',
				'direction' => 'row',
				'css'       => [ [ 'property' => '--je-terms-flex-direction' ] ],
			]
		);

		$this->register_jet_control(
			'terms_align_main_axis',
			[
				'tab'       => 'style',
				'label'     => esc_html__( 'Align main axis', 'jet-engine' ),
				'type'      => 'justify-content',
				'direction' => 'row',
				'exclude'   => 'space',
				'css'       => [ [ 'property' => '--je-terms-justify-content' ] ],
			]
		);

		$this->register_jet_control(
			'terms_align_cross_axis',
			[
				'tab'       => 'style',
				'label'     => esc_html__( 'Align cross axis', 'jet-engine' ),
				'type'      => 'align-items',
				'direction' => 'row',
				'exclude'   => 'stretch',
				'css'       => [ [ 'property' => '--je-terms-align-items' ] ],
			]
		);

		$this->register_jet_control(
			'terms_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Gap', 'jet-engine' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '12px',
				'css'     => [ [ 'property' => '--je-terms-gap' ] ],
			]
		);

		$this->end_jet_control_group();
	}
	// End general style

	// Start icon style
	public function control_group_icon_style() {
		$this->register_jet_control_group(
			'section_icon_style',
			[
				'title'    => esc_html__( 'Icon', 'jet-engine' ),
				'tab'      => 'style',
				'required' => [ 'selected_terms_icon', '!=', '' ],
			]
		);
	}

	public function controls_icon_style() {
		$this->start_jet_control_group( 'section_icon_style' );

		$this->register_jet_control(
			'icon_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Icon color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [ [ 'property' => '--je-terms-icon-color' ] ],
			]
		);

		$this->register_jet_control(
			'icon_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Icon size', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [ [ 'property' => '--je-terms-icon-fz' ] ],
			]
		);

		$this->end_jet_control_group();
	}
	// End icon style

	// Start link style
	public function control_group_link_style() {
		$this->register_jet_control_group(
			'section_link_style',
			[
				'title' => esc_html__( 'Term', 'jet-engine' ),
				'tab'   => 'style',
			]
		);
	}

	public function controls_link_style() {
		$css_selector = $this->css_selector( '__link' );

		$this->start_jet_control_group( 'section_link_style' );

		$this->register_jet_control(
			'link_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_selector,
					],
				],
			]
		);

		$this->register_jet_control(
			'link_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_selector,
					],
				],
			]
		);

		$this->register_jet_control(
			'link_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_selector,
					],
				],
			]
		);

		$this->register_jet_control(
			'link_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_selector,
					],
				],
			]
		);

		$this->register_jet_control(
			'link_box_shadow',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box shadow', 'jet-smart-filters' ),
				'type'  => 'box-shadow',
				'css'   => [
					[
						'property' => 'box-shadow',
						'selector' => $css_selector,
					],
				],
			]
		);

		$this->end_jet_control_group();
	}
	// End link style

	// Start text style
	public function control_group_text_style() {
		$this->register_jet_control_group(
			'section_text_style',
			[
				'title' => esc_html__( 'Text', 'jet-engine' ),
				'tab'   => 'style',
			]
		);
	}

	public function controls_text_style() {
		$css_selectors = [
			'prefix' => $this->css_selector( '__prefix' ),
			'suffix' => $this->css_selector( '__suffix' ),
		];

		$this->start_jet_control_group( 'section_text_style' );

		$this->register_jet_control(
			'before_text_heading',
			array(
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Before text', 'jet-engine' ),
			)
		);

		$this->register_jet_control(
			'before_text_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_selectors['prefix'],
					],
				],
			]
		);

		$this->register_jet_control(
			'after_text_heading',
			array(
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'After text', 'jet-engine' ),
			)
		);

		$this->register_jet_control(
			'after_text_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_selectors['suffix'],
					],
				],
			]
		);
	}
	// End text style

	// Render element HTML
	public function render() {

		parent::render();

		$this->enqueue_scripts();

		$render = $this->get_jet_render_instance();

		// STEP: Dynamic terms renderer class not found: Show placeholder text
		if ( ! $render ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Dynamic terms renderer class not found', 'jet-engine' )
				]
			);
		}

		echo "<div {$this->render_attributes( '_root' )}>";
		$render->render_content();
		echo "</div>";
	}

	public function parse_jet_render_attributes( $attrs = [] ) {
		$attrs['show_all_terms'] = $attrs['show_all_terms'] ?? false;
		$attrs['terms_linked']   = $attrs['terms_linked'] ?? false;

		return $attrs;
	}

	public function css_selector( $mod = null ) {
		return sprintf( '%1$s%2$s', '.jet-listing-dynamic-terms', $mod );
	}

	/**
	 * Returns all taxonomies list for options
	 *
	 * @return [type] [description]
	 */
	public function get_taxonomies_for_options() {
		$result     = array();
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

		foreach ( $taxonomies as $taxonomy ) {

			if ( empty( $taxonomy->object_type ) || ! is_array( $taxonomy->object_type ) ) {
				continue;
			}

			foreach ( $taxonomy->object_type as $index => $object ) {

				if ( empty( $result[ $object ] ) ) {
					$post_type = get_post_type_object( $object );

					if ( ! $post_type ) {
						continue;
					}

					$result[ $object ] = array(
						'label'   => $post_type->labels->name,
						'options' => array(),
					);
				}

				$result[ $object ]['options'][ $taxonomy->name ] = $taxonomy->labels->name;
			};
		}

		// Convert to an indexed array
		$indexed_result = array();

		foreach ( $result as $value ) {
			$indexed_result[] = $value;
		}

		return $indexed_result;
	}
}