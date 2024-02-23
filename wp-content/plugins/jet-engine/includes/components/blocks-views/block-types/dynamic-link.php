<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Blocks_Views_Type_Dynamic_Link' ) ) {

	/**
	 * Define Jet_Engine_Blocks_Views_Type_Dynamic_Link class
	 */
	class Jet_Engine_Blocks_Views_Type_Dynamic_Link extends Jet_Engine_Blocks_Views_Type_Base {

		/**
		 * Returns block name
		 *
		 * @return [type] [description]
		 */
		public function get_name() {
			return 'dynamic-link';
		}

		/**
		 * Return attributes array
		 *
		 * @return array
		 */
		public function get_attributes() {
			return apply_filters( 'jet-engine/blocks-views/block-types/attributes/dynamic-link', array(
				'dynamic_link_source' => array(
					'type'    => 'string',
					'default' => '_permalink',
				),
				'dynamic_link_option' => array(
					'type'    => 'string',
					'default' => '',
				),
				'dynamic_link_profile_page' => array(
					'type'    => 'string',
					'default' => '',
				),
				'dynamic_link_source_custom' => array(
					'type'    => 'string',
					'default' => '',
				),
				'delete_link_dialog' => array(
					'type'    => 'string',
					'default' => __( 'Are you sure you want to delete this post?', 'jet-engine' ),
				),
				'delete_link_redirect' => array(
					'type'    => 'string',
					'default' => '',
				),
				'delete_link_type' => array(
					'type'    => 'string',
					'default' => 'trash',
				),
				'selected_link_icon' => array(
					'type'    => 'number',
				),
				'selected_link_icon_url' => array(
					'type'    => 'string',
					'default' => '',
				),
				'link_label' => array(
					'type'    => 'string',
					'default' => '%title%',
				),
				'link_wrapper_tag' => array(
					'type'    => 'string',
					'default' => 'div',
				),
				'add_query_args' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'query_args' => array(
					'type' => 'string',
				),
				'url_prefix' => array(
					'type' => 'string',
				),
				'url_anchor' => array(
					'type' => 'string',
				),
				'open_in_new' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'rel_attr' => array(
					'type'    => 'string',
					'default' => '',
				),
				'aria_label_attr' => array(
					'type'    => 'string',
					'default' => '',
				),
				'hide_if_empty' => array(
					'type'    => 'boolean',
					'default' => false,
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
					'id'           => 'link_typography',
					'label'        => __( 'Typography', 'jet-engine' ),
					'type'         => 'typography',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-link__link' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'link_bg',
					'label'        => esc_html__( 'Background Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-link__link' ) => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'link_color',
					'label'        => __( 'Text Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-link__link' ) => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'link_icon_color',
					'label'        => __( 'Icon Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-link__icon' ) => 'color: {{VALUE}}',
						$this->css_selector( ' .jet-listing-dynamic-link__icon svg path' ) => 'fill: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'link_border_color',
					'label'        => __( 'Border Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-link__link' ) => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'link_bg_hover',
					'label'        => esc_html__( 'Background Color on Hover', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-link__link:hover' ) => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'link_color_hover',
					'label'        => esc_html__( 'Text Color on Hover', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-link__link:hover' ) => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'link_icon_color_hover',
					'label'        => __( 'Icon Color on Hover', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-link__link:hover .jet-listing-dynamic-link__icon' ) => 'color: {{VALUE}}',
						$this->css_selector( ' .jet-listing-dynamic-link__link:hover .jet-listing-dynamic-link__icon svg path' ) => 'fill: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'link_hover_border_color',
					'label'        => __( 'Border Color on Hover', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-link__link:hover' ) => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'      => 'link_alignment',
					'label'   => __( 'Alignment', 'jet-engine' ),
					'default'     => 'flex-start',
					'type'        => 'choose',
					'separator'    => 'before',
					'options'     => array(
						'flex-start'    => array(
							'shortcut' => esc_html__( 'Left', 'jet-engine' ),
							'icon'  => ! is_rtl() ? 'dashicons-editor-alignleft' : 'dashicons-editor-alignright',
						),
						'center' => array(
							'shortcut' => esc_html__( 'Center', 'jet-engine' ),
							'icon'  => 'dashicons-editor-aligncenter',
						),
						'flex-end' => array(
							'shortcut' => esc_html__( 'Right', 'jet-engine' ),
							'icon'  => ! is_rtl() ? 'dashicons-editor-alignright' : 'dashicons-editor-alignleft',
						),
						'stretch' => array(
							'shortcut' => esc_html__( 'Justify', 'jet-engine' ),
							'icon'  => 'dashicons-editor-justify',
						),
					),
					'css_selector'  => array(
						$this->css_selector( ' .jet-listing-dynamic-link__link' ) => 'align-self: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'link_padding',
					'label'        => __( 'Padding', 'jet-engine' ),
					'type'         => 'dimensions',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-link__link' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'link_margin',
					'label'      => __( 'Margin', 'jet-engine' ),
					'type'         => 'dimensions',
					'separator'    => 'before',
					'css_selector'  => array(
						$this->css_selector( ' .jet-listing-dynamic-link__link' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'             => 'link_border',
					'label'          => __( 'Border', 'jet-engine' ),
					'type'           => 'border',
					'separator'      => 'before',
					'disable_radius' => true,
					'disable_color'  => true,
					'css_selector'   => array(
						$this->css_selector( ' .jet-listing-dynamic-link__link' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'link_border_radius',
					'label'        => __( 'Border Radius', 'jet-engine' ),
					'type'         => 'dimensions',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-link__link' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$low_order = $this->prevent_wrap() ? -1 : 1;

			$this->controls_manager->add_control(
				array(
					'id'        => 'link_icon_position',
					'label'     => __( 'Icon Position', 'jet-engine' ),
					'type'      => 'choose',
					'separator' => 'before',
					'default'   => $low_order,
					'options'   => array(
						$low_order => array(
							'label' => esc_html__( 'Before Label', 'jet-engine' ),
							'icon'  => 'dashicons-editor-outdent',
						),
						'2' => array(
							'label' => esc_html__( 'After Label', 'jet-engine' ),
							'icon'  => 'dashicons-editor-indent',
						),
					),
					'css_selector'  => array(
						$this->css_selector( ' .jet-listing-dynamic-link__icon' ) => 'order: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'        => 'link_icon_orientation',
					'label'     => __( 'Icon Orientation', 'jet-engine' ),
					'type'      => 'choose',
					'default'   => 'row',
					'separator' => 'before',
					'options'   => array(
						'row' => array(
							'label' => esc_html__( 'Horizontal', 'jet-engine' ),
							'icon'  => 'dashicons-arrow-right-alt',
						),
						'column' => array(
							'label' => esc_html__( 'Vertical', 'jet-engine' ),
							'icon'  => 'dashicons-arrow-down-alt',
						),
					),
					'css_selector'  => array(
						$this->css_selector( ' .jet-listing-dynamic-link__link' ) => 'flex-direction: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'link_icon_size',
					'label'        => __( 'Icon Size', 'jet-engine' ),
					'type'         => 'range',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-link__icon svg' ) => 'width: {{VALUE}}px !important; height: auto !important;',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'link_icon_gap',
					'label'        => __( 'Icon Gap', 'jet-engine' ),
					'type'         => 'range',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( ' .jet-listing-dynamic-link__link' ) => 'gap: {{VALUE}}px;',
					),
				)
			);

			$this->controls_manager->end_section();

			do_action( 'jet-engine/blocks-views/dynamic-link/style-controls', $this->controls_manager, $this );

		}

		public function render_callback( $attributes = array() ) {
			$this->_root['class'][] = 'jet-listing-dynamic-link-block';
			return parent::render_callback( $attributes, false );
		}

	}

}
