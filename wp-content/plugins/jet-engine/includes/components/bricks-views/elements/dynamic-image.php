<?php

namespace Jet_Engine\Bricks_Views\Elements;

use Jet_Engine\Bricks_Views\Helpers\Controls_Hook_Bridge;
use Jet_Engine\Bricks_Views\Helpers\Options_Converter;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Dynamic_Image extends Base {
	// Element properties
	public $category = 'jetengine'; // Use predefined element category 'general'
	public $name = 'jet-engine-listing-dynamic-image'; // Make sure to prefix your elements
	public $icon = 'jet-engine-icon-dynamic-image'; // Themify icon font class
	public $css_selector = '.jet-listing-dynamic-image > *'; // Default CSS selector
	public $scripts = [ 'jetEngineBricks' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'dynamic-image';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Dynamic Image', 'jet-engine' );
	}

	// Set builder control groups
	public function set_control_groups() {
		$this->register_jet_control_group(
			'content',
			[
				'title' => esc_html__( 'General', 'jet-engine' ),
				'tab'   => 'content',
			]
		);

		$this->register_jet_control_group(
			'section_image_style',
			[
				'title' => esc_html__( 'Image', 'jet-engine' ),
				'tab'   => 'style',
			]
		);
	}


	// Set builder controls
	public function set_controls() {

		$this->start_jet_control_group( 'content' );

		$dynamic_image_source = $this->get_dynamic_sources( 'media' );

		if ( ! empty( $dynamic_image_source ) ) {

			$this->register_jet_control(
				'dynamic_image_source',
				[
					'tab'        => 'content',
					'label'      => esc_html__( 'Source', 'jet-engine' ),
					'type'       => 'select',
					'options'    => Options_Converter::convert_select_groups_to_options( $dynamic_image_source ),
					'searchable' => true,
					'default'    => 'post_thumbnail',
				]
			);

		}

		if ( jet_engine()->options_pages ) {

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'media' );

			if ( ! empty( $options_pages_select ) ) {
				$this->register_jet_control(
					'dynamic_field_option',
					[
						'tab'      => 'content',
						'label'    => esc_html__( 'Option', 'jet-engine' ),
						'type'     => 'select',
						'options'  => Options_Converter::convert_select_groups_to_options( $options_pages_select ),
						'required' => [ 'dynamic_image_source', '=', 'options_page' ],
					]
				);
			}

		}

		$this->register_jet_control(
			'dynamic_image_source_custom',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Custom meta field/repeater key', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Note: this field will override Source value', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'image_url_prefix',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Image URL prefix', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Add prefix to the image URL. For example for the cases when source contains only part of the URL', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'dynamic_image_size',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Dynamic image size', 'jet-engine' ),
				'type'     => 'select',
				'options'  => jet_engine_get_image_sizes(),
				'required' => [ 'dynamic_image_source', '!=', 'user_avatar' ],
			]
		);

		$this->register_jet_control(
			'dynamic_avatar_size',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Image size', 'jet-engine' ),
				'type'        => 'number',
				'units'       => true,
				'default'     => '100px',
				'description' => esc_html__( 'Note: this option will work only if image stored as attachment ID', 'jet-engine' ),
				'required'    => [ 'dynamic_image_source', '=', 'user_avatar' ],
			]
		);

		$this->register_jet_control(
			'linked_image',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Linked image', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => true,
			]
		);

		$image_link_source = $this->get_dynamic_sources( 'plain' );

		if ( ! empty( $image_link_source ) ) {

			$this->register_jet_control(
				'image_link_source',
				[
					'tab'      => 'content',
					'label'    => esc_html__( 'Link source', 'jet-engine' ),
					'type'     => 'select',
					'options'  => Options_Converter::convert_select_groups_to_options( $image_link_source ),
					'default'  => '_permalink',
					'required' => [ 'linked_image', '=', true ],
				]
			);

		}

		if ( jet_engine()->options_pages ) {

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'plain' );

			if ( ! empty( $options_pages_select ) ) {
				$this->register_jet_control(
					'image_link_option',
					[
						'tab'      => 'content',
						'label'    => esc_html__( 'Option', 'jet-engine' ),
						'type'     => 'select',
						'options'  => Options_Converter::convert_select_groups_to_options( $options_pages_select ),
						'required' => [
							[ 'linked_image', '=', true ],
							[ 'image_link_source', '=', 'options_page' ],
						],
					]
				);
			}

		}

		$hooks = new Controls_Hook_Bridge( $this );
		$hooks->do_action( 'jet-engine/listings/dynamic-image/link-source-controls' );

		$this->register_jet_control(
			'image_link_source_custom',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Custom meta field/repeater key', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Note: this field will override Meta Field value', 'jet-engine' ),
				'required'    => [ 'linked_image', '=', true ],
			]
		);

		$this->register_jet_control(
			'link_url_prefix',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Link URL prefix', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Add prefix to the URL, for example tel:, mailto: etc.', 'jet-engine' ),
				'required'    => [ 'linked_image', '=', true ],
			]
		);

		$this->register_jet_control(
			'open_in_new',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Open in new window', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => false,
				'required' => [ 'linked_image', '=', true ],
			]
		);

		$this->register_jet_control(
			'rel_attr',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Add "rel" attr', 'jet-engine' ),
				'type'     => 'select',
				'options'  => \Jet_Engine_Tools::get_rel_attr_options(),
				'required' => [ 'linked_image', '=', true ],
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
			'fallback_image',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Fallback image', 'jet-engine' ),
				'type'        => 'image',
				'description' => esc_html__( 'This image will be shown if selected source field is empty', 'jet-engine' ),
				'required'    => [ 'hide_if_empty', '=', false ],

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

		$this->start_jet_control_group( 'section_image_style' );

		$this->register_jet_control(
			'image_object_fit',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Object fit', 'jet-engine' ),
				'type'     => 'select',
				'options'  => [
					'fill'    => esc_html__( 'Fill', 'jet-engine' ),
					'cover'   => esc_html__( 'Cover', 'jet-engine' ),
					'contain' => esc_html__( 'Contain', 'jet-engine' ),
				],
				'css'      => [
					[
						'property' => 'object-fit',
						'selector' => $this->css_selector('__img'),
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

		// STEP: Dynamic image renderer class not found: Show placeholder text
		if ( ! $render ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Dynamic image renderer class not found', 'jet-engine' )
				]
			);
		}

		echo "<div {$this->render_attributes( '_root' )}>";
		$render->render_content();
		echo "</div>";
	}

	public function parse_jet_render_attributes( $attrs = [] ) {

		$attrs['dynamic_avatar_size'] = [
			'size' => intval( $attrs['dynamic_avatar_size'] ),
		];

		$attrs['linked_image'] = $attrs['linked_image'] ?? false;

		return $attrs;

	}

	// Get meta fields for post type
	public function get_dynamic_sources( $for = 'media' ) {

		if ( 'media' === $for ) {
			$default = array(
				'label'   => esc_html__( 'General', 'jet-engine' ),
				'options' => array(
					'post_thumbnail' => esc_html__( 'Post thumbnail', 'jet-engine' ),
					'user_avatar'    => esc_html__( 'User avatar', 'jet-engine' ),
				),
			);
		} else {
			$default = array(
				'label'   => esc_html__( 'General', 'jet-engine' ),
				'options' => array(
					'_permalink' => esc_html__( 'Permalink', 'jet-engine' ),
				),
			);

			if ( jet_engine()->modules->is_module_active( 'profile-builder' ) ) {
				$default['options']['profile_page'] = esc_html__( 'Profile Page', 'jet-engine' );
			}

		}

		$result      = array();
		$meta_fields = array();

		if ( jet_engine()->meta_boxes ) {
			$meta_fields = jet_engine()->meta_boxes->get_fields_for_select( $for );
		}

		if ( jet_engine()->options_pages ) {
			$default['options']['options_page'] = esc_html__( 'Options', 'jet-engine' );
		}

		$result = apply_filters(
			'jet-engine/listings/dynamic-image/fields',
			array_merge( array( $default ), $meta_fields ),
			$for
		);

		return $result;

	}

	public function css_selector( $mod = null ) {
		return sprintf( '%1$s%2$s', '.jet-listing-dynamic-image', $mod );
	}
}