<?php

namespace Jet_Engine\Bricks_Views\Elements;

use Bricks\Element;
use Jet_Engine\Bricks_Views\Helpers\Options_Converter;
use Jet_Engine\Bricks_Views\Helpers\Controls_Hook_Bridge;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Dynamic_Link extends Base {

	// Element properties
	public $category = 'jetengine'; // Use predefined element category 'general'
	public $name = 'jet-engine-listing-dynamic-link'; // Make sure to prefix your elements
	public $icon = 'jet-engine-icon-dynamic-link'; // Themify icon font class
	public $css_selector = '.jet-listing-dynamic-link__link'; // Default CSS selector
	public $scripts = [ 'jetEngineBricks' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'dynamic-link';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Dynamic Link', 'jet-engine' );
	}

	// Set builder control groups
	public function set_control_groups() {

		$this->register_general_group();
		$this->register_icon_group();

	}

	// Set builder controls
	public function set_controls() {

		$this->register_general_controls();
		$this->register_icon_controls();

	}

	public function register_general_group() {

		$this->register_jet_control_group(
			'content',
			[
				'title' => esc_html__( 'General', 'jet-engine' ),
				'tab'   => 'content',
			]
		);

	}

	public function register_general_controls() {

		$this->start_jet_control_group( 'content' );

		$meta_fields = $this->get_meta_fields_for_post_type();

		if ( ! empty( $meta_fields ) ) {

			$this->register_jet_control(
				'dynamic_link_source',
				[
					'tab'        => 'content',
					'label'      => esc_html__( 'Source', 'jet-engine' ),
					'type'       => 'select',
					'options'    => Options_Converter::convert_select_groups_to_options( $meta_fields ),
					'searchable' => true,
					'default'    => '_permalink',
				]
			);

		}

		if ( jet_engine()->options_pages ) {

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'plain' );

			if ( ! empty( $options_pages_select ) ) {

				$this->register_jet_control(
					'dynamic_link_option',
					[
						'tab'      => 'content',
						'label'    => esc_html__( 'Option', 'jet-engine' ),
						'type'     => 'select',
						'options'  => Options_Converter::convert_select_groups_to_options( $options_pages_select ),
						'required' => [ 'dynamic_link_source', '=', 'options_page' ],
					]
				);
			}

		}

		$hooks = new Controls_Hook_Bridge( $this, [ 'dynamic_link_trigger_popup' ] );
		$hooks->do_action( 'jet-engine/listings/dynamic-link/source-controls' );

		$this->register_jet_control(
			'dynamic_link_source_custom',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Custom meta field/repeater key', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Note: this field will override Meta Field value', 'jet-engine' ),
				'required'    => [ 'dynamic_link_source', '!=', 'delete_post_link' ],
			]
		);

		$this->register_jet_control(
			'delete_link_dialog',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Confirm deletion message', 'jet-engine' ),
				'type'        => 'textarea',
				'default'     => esc_html__( 'Are you sure you want to delete this post?', 'jet-engine' ),
				'description' => esc_html__( 'Only users with appropriate permissions can delete posts', 'jet-engine' ),
				'required'    => [ 'dynamic_link_source', '=', 'delete_post_link' ],
			]
		);

		$this->register_jet_control(
			'delete_link_redirect',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Redirect after delete', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'If empty will redirect to home page. Use the %current_page_url% macro to redirect to the current page.', 'jet-engine' ),
				'required'    => [ 'dynamic_link_source', '=', 'delete_post_link' ],
			]
		);

		$this->register_jet_control(
			'delete_link_type',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Delete post type', 'jet-engine' ),
				'type'     => 'select',
				'options'  => [
					'trash'       => esc_html__( 'Move to trash', 'jet-engine' ),
					'permanently' => esc_html__( 'Delete permanently', 'jet-engine' ),
				],
				'default'  => 'trash',
				'required' => [ 'dynamic_link_source', '=', 'delete_post_link' ],
			]
		);

		$this->register_jet_control(
			'link_label',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Label', 'jet-engine' ),
				'type'        => 'text',
				'default'     => 'Read More',
				'description' => sprintf(
					__( 'You can use %s as value for this field', 'jet-engine' ),
					'<a href="' . admin_url( 'admin.php?page=jet-engine#macros_generator' ) . '" target="_blank">JetEngine macros</a>'
				),
			]
		);

		$this->register_jet_control(
			'add_query_args',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Add query arguments', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'query_args',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Query arguments', 'jet-engine' ),
				'type'        => 'textarea',
				'default'     => '_post_id=%current_id%',
				'description' => esc_html__( 'One argument per line. Separate key and value with "="', 'jet-engine' ),
				'required'    => [ 'add_query_args', '=', true ],
			]
		);

		$this->register_jet_control(
			'url_prefix',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'URL prefix', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Add prefix to the URL, for example tel:, mailto: etc.', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'url_anchor',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'URL anchor', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Add anchor to the URL. Without #.', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'selected_link_icon',
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

		if ( ! $this->prevent_wrap() ) {
			$this->register_jet_control(
				'link_wrapper_tag',
				[
					'tab'     => 'content',
					'label'   => esc_html__( 'HTML wrapper', 'jet-engine' ),
					'type'    => 'select',
					'options' => [
						'div'  => 'DIV',
						'h1'   => 'H1',
						'h2'   => 'H2',
						'h3'   => 'H3',
						'h4'   => 'H4',
						'h5'   => 'H5',
						'h6'   => 'H6',
						'span' => 'SPAN',
					],
					'default' => 'div',
				]
			);
		}

		$this->register_jet_control(
			'open_in_new',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Open in new window', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'rel_attr',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Add "rel" attr', 'jet-engine' ),
				'type'    => 'select',
				'options' => \Jet_Engine_Tools::get_rel_attr_options(),
			]
		);

		$this->register_jet_control(
			'aria_label_attr',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Aria label attr', 'jet-engine' ),
				'type'    => 'text',
				'default' => '',
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

	public function register_icon_group() {

		$this->register_jet_control_group(
			'section_icon_style',
			[
				'title'    => esc_html__( 'Icon', 'jet-engine' ),
				'tab'      => 'style',
				'required' => [ 'selected_link_icon', '!=', '' ],

			]
		);

	}

	public function register_icon_controls() {

		$this->start_jet_control_group( 'section_icon_style' );

		$this->register_jet_control(
			'link_icon_direction',
			[
				'tab'       => 'style',
				'label'     => esc_html__( 'Direction', 'jet-engine' ),
				'type'      => 'direction',
				'direction' => 'row',
				'css'       => [
					[
						'property' => 'flex-direction',
					],
				],
			]
		);

		$this->register_jet_control(
			'link_icon_color',
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
			'link_icon_size',
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
			'link_icon_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Icon gap', 'jet-engine' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '12px',
				'css'     => [
					[
						'property' => 'gap',
					],
				],
			]
		);

		$this->end_jet_control_group();

	}

	// Enqueue element styles and scripts
	public function enqueue_scripts() {
		wp_enqueue_style( 'jet-engine-frontend' );
	}

	// Render element HTML
	public function render() {

		parent::render();

		$this->enqueue_scripts();

		$render = $this->get_jet_render_instance();

		// STEP: Dynamic link renderer class not found: Show placeholder text
		if ( ! $render ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Dynamic link renderer class not found', 'jet-engine' )
				]
			);
		}

		echo "<div {$this->render_attributes( '_root' )}>";
		$render->render_content();
		echo "</div>";
	}

	public function parse_jet_render_attributes( $attrs = [] ) {

		$attrs['selected_link_icon'] = isset( $attrs['selected_link_icon'] ) ? Element::render_icon( $attrs['selected_link_icon'] ) : '';

		return $attrs;

	}

	// Get meta fields for post type
	public function get_meta_fields_for_post_type() {

		$default = array(
			'label'   => __( 'General', 'jet-engine' ),
			'options' => apply_filters( 'jet-engine/elementor-view/dynamic-link/generel-options', array(
				'_permalink'       => __( 'Permalink', 'jet-engine' ),
				'delete_post_link' => __( 'Delete current post link', 'jet-engine' ),
			) ),
		);

		$result      = array();
		$meta_fields = array();

		if ( jet_engine()->options_pages ) {
			$default['options']['options_page'] = __( 'Options', 'jet-engine' );
		}

		if ( jet_engine()->modules->is_module_active( 'profile-builder' ) ) {
			$default['options']['profile_page'] = __( 'Profile Page', 'jet-engine' );
		}

		if ( jet_engine()->meta_boxes ) {
			$meta_fields = jet_engine()->meta_boxes->get_fields_for_select( 'plain' );
		}

		return apply_filters(
			'jet-engine/listings/dynamic-link/fields',
			array_merge( array( $default ), $meta_fields )
		);

	}

	public function css_selector( $mod = null ) {
		return sprintf( '%1$s%2$s', '.jet-listing-dynamic-link', $mod );
	}
}