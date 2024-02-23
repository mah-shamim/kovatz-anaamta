<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Blocks_Views_Type_Dynamic_Field' ) ) {

	/**
	 * Define Jet_Engine_Blocks_Views_Type_Dynamic_Field class
	 */
	class Jet_Engine_Blocks_Views_Type_Dynamic_Field extends Jet_Engine_Blocks_Views_Type_Base {

		/**
		 * Returns block name
		 *
		 * @return [type] [description]
		 */
		public function get_name() {
			return 'dynamic-field';
		}

		/**
		 * Return attributes array
		 *
		 * @return array
		 */
		public function get_attributes() {
			return apply_filters( 'jet-engine/blocks-views/block-types/attributes/dynamic-field', array_merge( array(
				'dynamic_field_source' => array(
					'type' => 'string',
					'default' => 'object',
				),
				'dynamic_field_post_object' => array(
					'type' => 'string',
					'default' => 'post_title',
				),
				'dynamic_field_relation_type' => array(
					'type' => 'string',
					'default' => 'grandparents',
				),
				'dynamic_field_relation_post_type' => array(
					'type' => 'string',
					'default' => '',
				),
				'dynamic_field_post_meta' => array(
					'type' => 'string',
					'default' => '',
				),
				'dynamic_field_option' => array(
					'type' => 'string',
					'default' => '',
				),
				'dynamic_field_var_name' => array(
					'type' => 'string',
					'default' => '',
				),
				'dynamic_field_post_meta_custom' => array(
					'type' => 'string',
					'default' => '',
				),
				'dynamic_field_wp_excerpt' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'dynamic_excerpt_more' => array(
					'type' => 'string',
					'default' => '...',
				),
				'dynamic_excerpt_length' => array(
					'type' => 'string',
					'default' => '',
				),
				'selected_field_icon' => array(
					'type' => 'number',
				),
				'selected_field_icon_url' => array(
					'type' => 'string',
					'default' => '',
				),
				'field_tag' => array(
					'type' => 'string',
					'default' => 'div',
				),
				'hide_if_empty' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'field_fallback' => array(
					'type' => 'string',
					'default' => '',
				),
				'dynamic_field_filter' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'filter_callback' => array(
					'type' => 'string',
					'default' => '',
				),
				'dynamic_field_custom' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'dynamic_field_format' => array(
					'type' => 'string',
					'default' => '%s',
				),
				'field_display' => array(
					'type'    => 'string',
					'default' => 'inline',
				),
				'object_context' => array(
					'type'    => 'string',
					'default' => 'default_object',
				),
			), jet_engine()->blocks_views->block_types->get_allowed_callbacks_atts() ) );
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
					'id'           => 'field_color',
					'label'        => __( 'Text Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-field__content' ) => 'color: {{VALUE}}',
						$this->css_selector( ' .jet-listing-dynamic-field__content a' ) => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'field_typography',
					'label'        => __( 'Typography', 'jet-engine' ),
					'type'         => 'typography',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-field__content' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
						$this->css_selector( ' .jet-listing-dynamic-field__content a' ) => 'text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}};',
					),
				)
			);

			$this->dom_optimized_styles();
			$this->dom_default_styles();
			// Not supported
			//$this->controls_manager->add_control(
			//	Group_Control_Box_Shadow::get_type(),
			//	array(
			//		'name'     => 'field_box_shadow',
			//		'css_selector' => $this->css_selector( '.display-multiline' ) . ', ' . $this->css_selector( '.display-inline .jet-listing-dynamic-field__inline-wrap' ),
			//	)
			//);

			$this->controls_manager->add_control(
				array(
					'id'           => 'icon_color',
					'label'        => __( 'Icon Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-field__icon' ) => 'color: {{VALUE}}',
						$this->css_selector( ' .jet-listing-dynamic-field__icon svg path' ) => 'fill: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'icon_size',
					'label'        => __( 'Icon Size', 'jet-engine' ),
					'type'         => 'range',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-field__icon svg' ) => 'width: {{VALUE}}px !important; height: auto !important;',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'icon_gap',
					'label'        => __( 'Icon Horizontal Gap', 'jet-engine' ),
					'type'         => 'range',
					'separator'    => 'before',
					'css_selector' => array(
						'body:not(.rtl) ' . $this->css_selector( ' .jet-listing-dynamic-field__icon' ) => 'margin-right: {{VALUE}}px;',
						'body.rtl ' . $this->css_selector( ' .jet-listing-dynamic-field__icon' ) => 'margin-left: {{VALUE}}px;',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'icon_gap',
					'label'        => __( 'Icon Vertical Gap', 'jet-engine' ),
					'type'         => 'range',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-field__icon' ) => 'margin-top: {{VALUE}}px;',
					),
				)
			);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				array(
					'id'    => 'section_misc_style',
					'title' => esc_html__( 'Misc', 'jet-engine' ),
				)
			);

			do_action( 'jet-engine/blocks-views/dynamic-field/misc-style-controls', $this->controls_manager, $this );

			$this->controls_manager->end_section();
		}

		/**
		 * Register DOM optimized styles
		 * @return [type] [description]
		 */
		public function dom_optimized_styles() {

			if ( ! $this->prevent_wrap() ) {
				return;
			}

			$this->controls_manager->add_control(
				array(
					'id'          => 'content_alignment',
					'label'       => esc_html__( 'Field Content Alignment', 'jet-engine' ),
					'type'        => 'choose',
					'default'     => 'left',
					'options'     => array(
						'left'    => array(
							'label'    => esc_html__( 'Left', 'jet-engine' ),
							'shortcut' => __( 'Left', 'jet-engine' ),
							'icon'     => 'dashicons-editor-alignleft',
						),
						'center' => array(
							'label'    => esc_html__( 'Center', 'jet-engine' ),
							'shortcut' => __( 'Center', 'jet-engine' ),
							'icon'     => 'dashicons-editor-aligncenter',
						),
						'right' => array(
							'label'    => esc_html__( 'Right', 'jet-engine' ),
							'shortcut' => esc_html__( 'Right', 'jet-engine' ),
							'icon'     => 'dashicons-editor-alignright',
						),
					),
					'css_selector'  => array(
						$this->css_selector( '.jet-listing-dynamic-field__content' ) => 'text-align: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'field_bg',
					'label'        => esc_html__( 'Background Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '.jet-listing-dynamic-field__content' ) => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'field_padding',
					'label'        => __( 'Padding', 'jet-engine' ),
					'type'         => 'dimensions',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '.jet-listing-dynamic-field__content' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'field_margin',
					'label'        => __( 'Margin', 'jet-engine' ),
					'separator'    => 'before',
					'type'         => 'dimensions',
					'css_selector' => array(
						$this->css_selector( '.jet-listing-dynamic-field__content' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'             => 'field_border',
					'label'          => __( 'Border', 'jet-engine' ),
					'type'           => 'border',
					'separator'      => 'before',
					'disable_radius' => true,
					'css_selector'   => array(
						$this->css_selector( '.jet-listing-dynamic-field__content' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'field_border_radius',
					'label'        => __( 'Border Radius', 'jet-engine' ),
					'type'         => 'dimensions',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '.jet-listing-dynamic-field__content' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

		}

		/**
		 * Register non-DOM optimized styles
		 * @return [type] [description]
		 */
		public function dom_default_styles() {
			
			if ( $this->prevent_wrap() ) {
				return;
			}

			$this->controls_manager->add_control(
				array(
					'id'           => 'field_width',
					'label'   => __( 'Field content width', 'jet-engine' ),
					'type'    => 'choose',
					'separator'    => 'before',
					'attributes' => array(
						'default' => array(
							'value' => 'auto',
						),
					),
					'options' => array(
						'auto' => array(
							'icon'     => 'dashicons-editor-alignleft',
							'shortcut' => __( 'Auto', 'jet-engine' ),
							'label'    => __( 'Auto', 'jet-engine' ),
						),
						'100%' => array(
							'icon'     => 'dashicons-editor-justify',
							'shortcut' => __( 'Fullwidth', 'jet-engine' ),
							'label'    => __( 'Fullwidth', 'jet-engine' ),
						),
					),
					'css_selector'  => array(
						$this->css_selector( ' .jet-listing-dynamic-field__inline-wrap' ) => 'width: {{VALUE}};',
						$this->css_selector( ' .jet-listing-dynamic-field__content' ) => 'width: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'      => 'field_alignment',
					'label'   => esc_html__( 'Field Elements Position', 'jet-engine' ),
					'type'    => 'choose',
					'default' => 'flex-start',
					'separator'    => 'before',
					'options' => array(
						'flex-start'    => array(
							'label' => esc_html__( 'Start', 'jet-engine' ),
							'icon'  => ! is_rtl() ? 'dashicons-editor-alignleft' : 'dashicons-editor-alignright',
						),
						'center' => array(
							'label' => esc_html__( 'Center', 'jet-engine' ),
							'icon'  => 'dashicons-editor-aligncenter',
						),
						'flex-end' => array(
							'label' => esc_html__( 'End', 'jet-engine' ),
							'icon'  => ! is_rtl() ? 'dashicons-editor-alignright' : 'dashicons-editor-alignleft',
						),
					),
					'css_selector'  => array(
						$this->css_selector() => 'justify-content: {{VALUE}};',
					),
					'condition' => array(
						'field_display' => 'inline',
						'field_width'   => 'auto',
					),
				)
			);


			$this->controls_manager->add_control(
				array(
					'id'          => 'content_alignment',
					'label'       => esc_html__( 'Field Content Alignment', 'jet-engine' ),
					'default'     => 'left',
					'type'        => 'choose',
					'separator'    => 'before',
					'options'     => array(
						'left'    => array(
							'label' => esc_html__( 'Left', 'jet-engine' ),
							'icon'  => 'dashicons-editor-alignleft',
						),
						'center' => array(
							'label' => esc_html__( 'Center', 'jet-engine' ),
							'icon'  => 'dashicons-editor-aligncenter',
						),
						'right' => array(
							'label' => esc_html__( 'Right', 'jet-engine' ),
							'icon'  => 'dashicons-editor-alignright',
						),
					),
					'css_selector'  => array(
						$this->css_selector( ' .jet-listing-dynamic-field__content' ) => 'text-align: {{VALUE}};',
					),
					//'condition' => array(
					//	'field_display' => 'multiline',
					//),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'field_bg',
					'label'        => esc_html__( 'Background Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '.display-multiline' ) . ', ' . $this->css_selector( '.display-inline .jet-listing-dynamic-field__inline-wrap' ) => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'field_padding',
					'label'        => __( 'Padding', 'jet-engine' ),
					'type'         => 'dimensions',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '.display-multiline' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
						$this->css_selector( '.display-inline .jet-listing-dynamic-field__inline-wrap' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'field_margin',
					'label'        => __( 'Margin', 'jet-engine' ),
					'separator'    => 'before',
					'type'         => 'dimensions',
					'css_selector' => array(
						$this->css_selector( '.display-multiline' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
						$this->css_selector( '.display-inline .jet-listing-dynamic-field__inline-wrap' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'             => 'field_border',
					'label'          => __( 'Border', 'jet-engine' ),
					'type'           => 'border',
					'separator'      => 'before',
					'disable_radius' => true,
					'css_selector'   => array(
						$this->css_selector( '.display-multiline' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
						$this->css_selector( '.display-inline .jet-listing-dynamic-field__inline-wrap' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'field_border_radius',
					'label'        => __( 'Border Radius', 'jet-engine' ),
					'type'         => 'dimensions',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '.display-multiline' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
						$this->css_selector( '.display-inline .jet-listing-dynamic-field__inline-wrap' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

		}

		public function render_callback( $attributes = array() ) {
			$this->_root['class'][] = 'jet-listing-dynamic-field-block';
			return parent::render_callback( $attributes, false );
		}

	}

}
