<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Blocks_Views_Type_Dynamic_Image' ) ) {

	/**
	 * Define Jet_Engine_Blocks_Views_Type_Dynamic_Image class
	 */
	class Jet_Engine_Blocks_Views_Type_Dynamic_Image extends Jet_Engine_Blocks_Views_Type_Base {

		/**
		 * Returns block name
		 *
		 * @return [type] [description]
		 */
		public function get_name() {
			return 'dynamic-image';
		}

		/**
		 * Return attributes array
		 *
		 * @return array
		 */
		public function get_attributes() {
			return apply_filters( 'jet-engine/blocks-views/block-types/attributes/dynamic-image', array(
				'dynamic_image_source' => array(
					'type' => 'string',
					'default' => 'post_thumbnail',
				),
				'dynamic_image_source_custom' => array(
					'type' => 'string',
					'default' => '',
				),
				'dynamic_field_option' => array(
					'type' => 'string',
					'default' => '',
				),
				'dynamic_image_size' => array(
					'type' => 'string',
					'default' => 'full',
				),
				'dynamic_avatar_size' => array(
					'type' => 'number',
					'default' => 50,
				),
				'custom_image_alt' => array(
					'type' => 'string',
					'default' => '',
				),
				'lazy_load_image' => array(
					'type' => 'boolean',
					'default' => wp_lazy_loading_enabled( 'img', 'wp_get_attachment_image' ),
				),
				'linked_image' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'image_link_source' => array(
					'type' => 'string',
					'default' => '_permalink',
				),
				'image_link_source_custom' => array(
					'type' => 'string',
					'default' => '',
				),
				'image_link_option' => array(
					'type' => 'string',
					'default' => '',
				),
				'open_in_new' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'rel_attr' => array(
					'type' => 'string',
					'default' => '',
				),
				'hide_if_empty' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'fallback_image' => array(
					'type' => 'number',
				),
				'fallback_image_url' => array(
					'type' => 'string',
					'default' => '',
				),
				'image_url_prefix' => array(
					'type' => 'string',
					'default' => '',
				),
				'link_url_prefix' => array(
					'type' => 'string',
					'default' => '',
				),
				'object_context' => array(
					'type' => 'string',
					'default' => 'default_object',
				),
			) );
		}

		/**
		 * Add style block options
		 *
		 * @return boolean
		 */
		public function add_style_manager_options() {

			$this->controls_manager->start_section(
				'style_controls',
				array(
					'id'           => 'section_field_style',
					'initial_open' => true,
					'title'        => esc_html__( 'Field Style', 'jet-engine' )
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'    => 'image_alignment',
					'label' => __( 'Alignment', 'jet-engine' ),
					'type'  => 'choose',
					'options' => array(
						'flex-start'   => array(
							'label' => esc_html__( 'Start', 'jet-engine' ),
							'icon'  => 'dashicons-editor-alignleft',
						),
						'center' => array(
							'label' => esc_html__( 'Center', 'jet-engine' ),
							'icon'  => 'dashicons-editor-aligncenter',
						),
						'flex-end'  => array(
							'label' => esc_html__( 'End', 'jet-engine' ),
							'icon'  => 'dashicons-editor-alignright',
						),
					),
					'css_selector' => array(
						$this->css_selector() => 'justify-content: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'    => 'image_width',
					'label' => esc_html__( 'Width', 'jet-engine' ),
					'type'  => 'range',
					'units' => array(
						array(
							'value'     => 'px',
							'intervals' => array(
								'step' => 1,
								'min'  => 1,
								'max'  => 1000,
							),
						),
						array(
							'value'     => '%',
							'intervals' => array(
								'step' => 1,
								'min'  => 1,
								'max'  => 100,
							),
						),
						array(
							'value'     => 'vw',
							'intervals' => array(
								'step' => 1,
								'min'  => 1,
								'max'  => 100,
							),
						),
					),
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' a' )   => 'width: {{VALUE}}{{UNIT}};',
						$this->css_selector( ' img' ) => 'width: {{VALUE}}{{UNIT}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'    => 'image_max_width',
					'label' => esc_html__( 'Max Width', 'jet-engine' ),
					'type'  => 'range',
					'units' => array(
						array(
							'value'     => 'px',
							'intervals' => array(
								'step' => 1,
								'min'  => 1,
								'max'  => 1000,
							),
						),
						array(
							'value'     => '%',
							'intervals' => array(
								'step' => 1,
								'min'  => 1,
								'max'  => 100,
							),
						),
						array(
							'value'     => 'vw',
							'intervals' => array(
								'step' => 1,
								'min'  => 1,
								'max'  => 100,
							),
						),
					),
					'css_selector' => array(
						$this->css_selector( ' a' )   => 'max-width: {{VALUE}}{{UNIT}};',
						$this->css_selector( ' img' ) => 'max-width: {{VALUE}}{{UNIT}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'    => 'image_height',
					'label' => esc_html__( 'Height', 'jet-engine' ),
					'type'  => 'range',
					'units' => array(
						array(
							'value'     => 'px',
							'intervals' => array(
								'step' => 1,
								'min'  => 1,
								'max'  => 1000,
							),
						),
						array(
							'value'     => 'vh',
							'intervals' => array(
								'step' => 1,
								'min'  => 1,
								'max'  => 100,
							),
						),
					),
					'css_selector' => array(
						$this->css_selector( ' img' ) => 'height: {{VALUE}}{{UNIT}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'      => 'image_object_fit',
					'label'   => esc_html__( 'Object Fit', 'jet-engine' ),
					'type'    => 'select',
					'options' => array(
						array(
							'label' => esc_html__( 'Default', 'jet-engine' ),
							'value' => '',
						),
						array(
							'label' => esc_html__( 'Fill', 'jet-engine' ),
							'value' => 'fill',
						),
						array(
							'label' => esc_html__( 'Cover', 'jet-engine' ),
							'value' => 'cover',
						),
						array(
							'label' => esc_html__( 'Contain', 'jet-engine' ),
							'value' => 'contain',
						),
					),
					'css_selector' => array(
						$this->css_selector( ' img' ) => 'object-fit: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'             => 'image_border',
					'label'          => __( 'Border', 'jet-engine' ),
					'type'           => 'border',
					'separator'      => 'before',
					'disable_radius' => true,
					'css_selector'   => array(
						$this->css_selector( ' img' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'         => 'image_border_radius',
					'label'      => __( 'Border Radius', 'jet-engine' ),
					'type'         => 'dimensions',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' img' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$this->controls_manager->end_section();
		}

	}

}
