<?php
namespace Elementor;

use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Listing_Dynamic_Image_Widget extends \Jet_Listing_Dynamic_Widget {

	public function get_name() {
		return 'jet-listing-dynamic-image';
	}

	public function get_title() {
		return __( 'Dynamic Image', 'jet-engine' );
	}

	public function get_icon() {
		return 'jet-engine-icon-dynamic-image';
	}

	public function get_categories() {
		return array( 'jet-listing-elements' );
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetengine-dynamic-image-widget-overview/?utm_source=jetengine&utm_medium=dynamic-image&utm_campaign=need-help';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-engine' ),
			)
		);

		$this->add_control(
			'dynamic_image_source',
			array(
				'label'   => __( 'Source', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post_thumbnail',
				'groups'  => $this->get_dynamic_sources( 'media' ),
			)
		);

		if ( jet_engine()->options_pages ) {

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'media' );

			if ( ! empty( $options_pages_select ) ) {
				$this->add_control(
					'dynamic_field_option',
					array(
						'label'     => __( 'Option', 'jet-engine' ),
						'type'      => Controls_Manager::SELECT,
						'default'   => '',
						'groups'    => $options_pages_select,
						'condition' => array(
							'dynamic_image_source' => 'options_page',
						),
					)
				);
			}

		}

		/**
		 * Add 3rd-party controls for sources
		 */
		do_action( 'jet-engine/listings/dynamic-image/source-controls', $this );

		$this->add_control(
			'dynamic_image_source_custom',
			array(
				'label'       => __( 'Custom meta field/repeater key', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => __( 'Note: this field will override Source value', 'jet-engine' ),
			)
		);

		$this->add_control(
			'image_url_prefix',
			array(
				'label'       => __( 'Image URL Prefix', 'jet-engine' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __( 'Add prefix to the image URL. For example for the cases when source contains only part of the URL', 'jet-engine' ),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'dynamic_image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `dynamic_image_size` and `dynamic_image_custom_dimension`.
				'default' => 'full',
				'fields_options' => array(
					'size' => array(
						'description' => __( 'Note: this option will work only if image stored as attachment ID', 'jet-engine' ),
					),
				),
				'condition' => array(
					'dynamic_image_source!' => 'user_avatar',
				),
			)
		);

		$this->add_control(
			'dynamic_avatar_size',
			array(
				'label'      => __( 'Image Size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 50,
				),
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 500,
					),
				),
				'condition'   => array(
					'dynamic_image_source' => 'user_avatar',
				),
				'description' => __( 'Note: this option will work only if image stored as attachment ID', 'jet-engine' ),
			)
		);

		$this->add_control(
			'custom_image_alt',
			array(
				'label'       => __( 'Custom Image Alt', 'jet-engine' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'dynamic'     => array(
					'active' => 'yes',
				),
			)
		);

		$this->add_control(
			'lazy_load_image',
			array(
				'label'   => __( 'Lazy Load', 'jet-engine' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => wp_lazy_loading_enabled( 'img', 'wp_get_attachment_image' ) ? 'yes' : '',
			)
		);

		$this->add_control(
			'linked_image',
			array(
				'label'        => __( 'Linked image', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'jet-engine' ),
				'label_off'    => __( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'image_link_source',
			array(
				'label'     => __( 'Link Source', 'jet-engine' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '_permalink',
				'groups'    => $this->get_dynamic_sources( 'plain' ),
				'condition' => array(
					'linked_image' => 'yes',
				),
			)
		);

		$this->add_control(
			'lightbox',
			array(
				'label'     => esc_html__( 'Lightbox', 'jet-engine' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'linked_image'      => 'yes',
					'image_link_source' => '_file',
				),
			)
		);

		if ( jet_engine()->options_pages ) {

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'plain' );

			if ( ! empty( $options_pages_select ) ) {
				$this->add_control(
					'image_link_option',
					array(
						'label'     => __( 'Option', 'jet-engine' ),
						'type'      => Controls_Manager::SELECT,
						'default'   => '',
						'groups'    => $options_pages_select,
						'condition' => array(
							'linked_image'      => 'yes',
							'image_link_source' => 'options_page',
						),
					)
				);
			}

		}

		/**
		 * Add 3rd-party controls for sources
		 */
		do_action( 'jet-engine/listings/dynamic-image/link-source-controls', $this );

		$this->add_control(
			'image_link_source_custom',
			array(
				'label'       => __( 'Custom meta field/repeater key', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => __( 'Note: this field will override Meta Field value', 'jet-engine' ),
				'condition'   => array(
					'linked_image' => 'yes',
				),
			)
		);

		$this->add_control(
			'link_url_prefix',
			array(
				'label'       => __( 'Link URL Prefix', 'jet-engine' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __( 'Add prefix to the URL, for example tel:, mailto: etc.', 'jet-engine' ),
				'condition'   => array(
					'linked_image' => 'yes',
				),
			)
		);

		$this->add_control(
			'open_in_new',
			array(
				'label'        => esc_html__( 'Open in new window', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'linked_image' => 'yes',
				),
			)
		);

		$this->add_control(
			'rel_attr',
			array(
				'label'   => __( 'Add "rel" attr', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => \Jet_Engine_Tools::get_rel_attr_options(),
				'condition'   => array(
					'linked_image' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'image_alignment',
			array(
				'label'   => __( 'Alignment', 'jet-engine' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'flex-start',
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-engine' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-engine' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-engine' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'selectors'  => array(
					$this->css_selector() => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hide_if_empty',
			array(
				'label'        => esc_html__( 'Hide if value is empty', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'fallback_image',
			array(
				'label'       => __( 'Fallback Image', 'jet-engine' ),
				'description' => __( 'This image will be shown if selected source field is empty', 'jet-engine' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'hide_if_empty' => '',
				),
			)
		);

		$this->add_control(
			'object_context',
			array(
				'label'     => __( 'Context', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'default_object',
				'options'   => jet_engine()->listings->allowed_context_list(),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			array(
				'label'      => __( 'Image', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label' => esc_html__( 'Width', 'jet-engine' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => '%',
				),
				'tablet_default' => array(
					'unit' => '%',
				),
				'mobile_default' => array(
					'unit' => '%',
				),
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( '%', 'px', 'vw' ) ),
				'range' => array(
					'%' => array(
						'min' => 1,
						'max' => 100,
					),
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
					'vw' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					$this->css_selector( ' a' )   => 'width: {{SIZE}}{{UNIT}};',
					$this->css_selector( ' img' ) => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_max_width',
			array(
				'label' => esc_html__( 'Max Width', 'jet-engine' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => '%',
				),
				'tablet_default' => array(
					'unit' => '%',
				),
				'mobile_default' => array(
					'unit' => '%',
				),
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( '%', 'px', 'vw' ) ),
				'range' => array(
					'%' => array(
						'min' => 1,
						'max' => 100,
					),
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
					'vw' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					$this->css_selector( ' a' )   => 'max-width: {{SIZE}}{{UNIT}};',
					$this->css_selector( ' img' ) => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label' => esc_html__( 'Height', 'jet-engine' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', 'vh' ) ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 500,
					),
					'vh' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					$this->css_selector( ' img' ) => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_object_fit',
			array(
				'label' => esc_html__( 'Object Fit', 'jet-engine' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					''        => esc_html__( 'Default', 'jet-engine' ),
					'fill'    => esc_html__( 'Fill', 'jet-engine' ),
					'cover'   => esc_html__( 'Cover', 'jet-engine' ),
					'contain' => esc_html__( 'Contain', 'jet-engine' ),
				),
				'default' => '',
				'condition' => array(
					'image_height[size]!' => '',
				),
				'selectors' => array(
					$this->css_selector( ' img' ) => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'image_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'selector'       => $this->css_selector( ' img' ),
			)
		);

		$this->add_responsive_control(
			'image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					$this->css_selector( ' img' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Returns CSS selector for nested element
	 *
	 * @param  [type] $el [description]
	 * @return [type]     [description]
	 */
	public function css_selector( $el = null ) {
		return sprintf( '{{WRAPPER}} .%1$s%2$s', $this->get_name(), $el );
	}

	/**
	 * Get meta fields for post type
	 *
	 * @return array
	 */
	public function get_dynamic_sources( $for = 'media' ) {

		if ( 'media' === $for ) {
			$default = array(
				'label'   => __( 'General', 'jet-engine' ),
				'options' => array(
					'post_thumbnail' => __( 'Post thumbnail', 'jet-engine' ),
					'user_avatar'    => __( 'User avatar', 'jet-engine' ),
				),
			);
		} else {
			$default = array(
				'label'   => __( 'General', 'jet-engine' ),
				'options' => array(
					'_permalink' => __( 'Permalink', 'jet-engine' ),
					'_file'      => __( 'Media File', 'jet-engine' ),
				),
			);

			if ( jet_engine()->modules->is_module_active( 'profile-builder' ) ) {
				$default['options']['profile_page'] = __( 'Profile Page', 'jet-engine' );
			}

		}

		$result      = array();
		$meta_fields = array();

		if ( jet_engine()->meta_boxes ) {
			$meta_fields = jet_engine()->meta_boxes->get_fields_for_select( $for );
		}

		if ( jet_engine()->options_pages ) {
			$default['options']['options_page'] = __( 'Options', 'jet-engine' );
		}

		$result = apply_filters(
			'jet-engine/listings/dynamic-image/fields',
			array_merge( array( $default ), $meta_fields ),
			$for
		);

		return $result;

	}

	protected function render() {
		jet_engine()->listings->render_item( 'dynamic-image', $this->get_settings_for_display() );
	}

}
