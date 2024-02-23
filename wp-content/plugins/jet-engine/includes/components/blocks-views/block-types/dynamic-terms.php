<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Blocks_Views_Type_Dynamic_Terms' ) ) {

	/**
	 * Define Jet_Engine_Blocks_Views_Type_Dynamic_Terms class
	 */
	class Jet_Engine_Blocks_Views_Type_Dynamic_Terms extends Jet_Engine_Blocks_Views_Type_Base {

		/**
		 * Returns block name
		 *
		 * @return [type] [description]
		 */
		public function get_name() {
			return 'dynamic-terms';
		}

		/**
		 * Returns CSS selector for nested element
		 *
		 * @param  string|array $el
		 * @return string
		 */
		public function css_selector( $el = null ) {
			if ( ! is_array( $el ) ) {
				return sprintf( '{{WRAPPER}} .jet-listing-dynamic-terms%s', $el );
			} else {

				$res = array();

				foreach ( $el as $selector ) {
					$res[] = sprintf( '{{WRAPPER}} .jet-listing-dynamic-terms%s', $selector );
				}

				return implode( ', ', $res );
			}
		}

		/**
		 * Return attributes array
		 *
		 * @return array
		 */
		public function get_attributes() {
			return array(
				'from_tax' => array(
					'type' => 'string',
					'default' => '',
				),
				'show_all_terms' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'terms_num' => array(
					'type' => 'number',
					'default' => 1,
				),
				'terms_delimiter' => array(
					'type' => 'string',
					'default' => ',',
				),
				'terms_linked' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'selected_terms_icon' => array(
					'type' => 'number',
				),
				'selected_terms_icon_url' => array(
					'type' => 'string',
					'default' => '',
				),
				'terms_prefix' => array(
					'type' => 'string',
					'default' => '',
				),
				'terms_suffix' => array(
					'type' => 'string',
					'default' => '',
				),
				'orderby' => array(
					'type' => 'string',
					'default' => 'name',
				),
				'order' => array(
					'type' => 'string',
					'default' => 'ASC',
				),
				'hide_if_empty' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'object_context' => array(
					'type'    => 'string',
					'default' => 'default_object',
				),
			);
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
					'id'           => 'section_style',
					'initial_open' => true,
					'title'        => esc_html__( 'General', 'jet-engine' )
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'         => 'terms_alignment',
					'label'      => __( 'Alignment', 'jet-engine' ),
					'type'       => 'choose',
					'attributes' => array(
						'default' => array(
							'value' => 'left',
						),
					),
					'options' => array(
						'left'   => array(
							'label' => esc_html__( 'Left', 'jet-engine' ),
							'icon'  => 'dashicons-editor-alignleft',
						),
						'center' => array(
							'label' => esc_html__( 'Center', 'jet-engine' ),
							'icon'  => 'dashicons-editor-aligncenter',
						),
						'right'  => array(
							'label' => esc_html__( 'Right', 'jet-engine' ),
							'icon'  => 'dashicons-editor-alignright',
						),
					),
					'css_selector' => array(
						$this->css_selector() => 'text-align: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				array(
					'id'           => 'section_link_style',
					'initial_open' => false,
					'title'        => esc_html__( 'Labels', 'jet-engine' )
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'link_typography',
					'label'        => __( 'Typography', 'jet-engine' ),
					'type'         => 'typography',
					'css_selector' => array(
						$this->css_selector( '__link' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
					),
				)
			);

			$this->controls_manager->start_tabs(
				'style_controls',
				array(
					'id'        => 'tabs_form_submit_style',
					'separator' => 'after',
				)
			);

			$this->controls_manager->start_tab(
				'style_controls',
				array(
					'id'    => 'dynamic_link_normal',
					'title' => esc_html__( 'Normal', 'jet-engine' ),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'link_bg',
					'label'        => esc_html__( 'Background Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'css_selector' => array(
						$this->css_selector( '__link' ) => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'link_color',
					'label'        => esc_html__( 'Text Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__link' ) => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				array(
					'id'    => 'dynamic_link_hover',
					'title' => esc_html__( 'Hover', 'jet-engine' ),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'link_bg_hover',
					'label'        => esc_html__( 'Background Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'css_selector' => array(
						$this->css_selector( '__link:hover' ) => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'link_color_hover',
					'label'        => esc_html__( 'Text Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__link:hover' ) => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'link_hover_border_color',
					'label'        => esc_html__( 'Border Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__link:hover' ) => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'link_padding',
					'label'        => __( 'Padding', 'jet-engine' ),
					'type'         => 'dimensions',
					'units'        => array( 'px', '%', 'em' ),
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__link' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'link_margin',
					'label'        => __( 'Margin', 'jet-engine' ),
					'type'         => 'dimensions',
					'units'        => array( 'px', '%', 'em' ),
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__link' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'             => 'link_border',
					'label'          => esc_html__( 'Border', 'jet-engine' ),
					'type'           => 'border',
					'separator'      => 'before',
					'disable_radius' => true,
					'css_selector'   => array(
						$this->css_selector( '__link' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'link_border_radius',
					'label'        => __( 'Border Radius', 'jet-engine' ),
					'type'         => 'dimensions',
					'units'        => array( 'px', '%' ),
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__link' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			// Not supported
			// $this->add_group_control(
			// 	Group_Control_Box_Shadow::get_type(),
			// 	array(
			// 		'name'     => 'link_box_shadow',
			// 		'selector' => $this->css_selector( '__counter' ),
			// 	)
			// );

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				array(
					'id'           => 'section_icon_style',
					'initial_open' => false,
					'title'        => esc_html__( 'Icon', 'jet-engine' )
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'icon_color',
					'label'        => __( 'Icon Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__icon' ) => 'color: {{VALUE}}',
						$this->css_selector( '__icon svg path' ) => 'fill: {{VALUE}}',
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
						$this->css_selector( '__icon svg' ) => 'width: {{VALUE}}px !important; height: auto !important;',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'icon_gap',
					'label'        => __( 'Icon Gap', 'jet-engine' ),
					'type'         => 'range',
					'separator'    => 'before',
					'css_selector' => array(
						'body:not(.rtl) ' . $this->css_selector( '__icon' ) => 'margin-right: {{VALUE}}px;',
						'body.rtl ' . $this->css_selector( '__icon' ) => 'margin-left: {{VALUE}}px;',
					),
				)
			);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				array(
					'id'           => 'section_delimiter_style',
					'initial_open' => false,
					'title'        => esc_html__( 'Delimiter', 'jet-engine' )
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'delimiter_color',
					'label'        => esc_html__( 'Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'css_selector' => array(
						$this->css_selector( '__delimiter' ) => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'    => 'delimiter_size',
					'label' => esc_html__( 'Size', 'jet-engine' ),
					'type'  => 'range',
					'units' => array(
						array(
							'value'     => 'px',
							'intervals' => array(
								'step' => 1,
								'min'  => 10,
								'max'  => 100,
							),
						),
					),
					'separator' => 'before',
					'css_selector' => array(
						$this->css_selector( '__delimiter' ) => 'font-size: {{VALUE}}{{UNIT}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'    => 'delimiter_l_gap',
					'label' => esc_html__( 'Left Gap', 'jet-engine' ),
					'type'  => 'range',
					'units' => array(
						array(
							'value'     => 'px',
							'intervals' => array(
								'step' => 1,
								'min'  => 0,
								'max'  => 100,
							),
						),
					),
					'separator' => 'before',
					'css_selector' => array(
						$this->css_selector( '__delimiter' ) => 'margin-left: {{VALUE}}{{UNIT}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'    => 'delimiter_r_gap',
					'label' => esc_html__( 'Right Gap', 'jet-engine' ),
					'type'  => 'range',
					'units' => array(
						array(
							'value'     => 'px',
							'intervals' => array(
								'step' => 1,
								'min'  => 0,
								'max'  => 100,
							),
						),
					),
					'separator' => 'before',
					'css_selector' => array(
						$this->css_selector( '__delimiter' ) => 'margin-right: {{VALUE}}{{UNIT}};',
					),
				)
			);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				array(
					'id'           => 'section_prefix_style',
					'initial_open' => false,
					'title'        => esc_html__( 'Text before', 'jet-engine' )
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'        => 'prefix_typography',
					'label'     => __( 'Typography', 'jet-engine' ),
					'type'      => 'typography',
					'css_selector' => array(
						$this->css_selector( '__prefix' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'    => 'prefix_color',
					'label' => esc_html__( 'Color', 'jet-engine' ),
					'type'  => 'color-picker',
					'css_selector' => array(
						$this->css_selector( '__prefix' ) => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'    => 'prefix_gap',
					'label' => esc_html__( 'Gap', 'jet-engine' ),
					'type'  => 'range',
					'units' => array(
						array(
							'value'     => 'px',
							'intervals' => array(
								'step' => 1,
								'min'  => 0,
								'max'  => 100,
							),
						),
					),
					'separator' => 'before',
					'css_selector' => array(
						'body:not(.rtl) ' . $this->css_selector( '__prefix' )  => 'margin-right: {{VALUE}}{{UNIT}};',
						'body.rtl ' . $this->css_selector( '__prefix' ) => 'margin-left: {{VALUE}}{{UNIT}};',
					),
				)
			);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				array(
					'id'           => 'section_suffix_style',
					'initial_open' => false,
					'title'        => esc_html__( 'Text after', 'jet-engine' )
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'        => 'suffix_typography',
					'label'     => __( 'Typography', 'jet-engine' ),
					'type'      => 'typography',
					'css_selector' => array(
						$this->css_selector( '__suffix' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'    => 'suffix_color',
					'label' => esc_html__( 'Color', 'jet-engine' ),
					'type'  => 'color-picker',
					'css_selector' => array(
						$this->css_selector( '__suffix' ) => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'    => 'suffix_gap',
					'label' => esc_html__( 'Gap', 'jet-engine' ),
					'type'  => 'range',
					'units' => array(
						array(
							'value'     => 'px',
							'intervals' => array(
								'step' => 1,
								'min'  => 0,
								'max'  => 100,
							),
						),
					),
					'separator' => 'before',
					'css_selector' => array(
						'body:not(.rtl) ' . $this->css_selector( '__suffix' )  => 'margin-left: {{VALUE}}{{UNIT}};',
						'body.rtl ' . $this->css_selector( '__suffix' ) => 'margin-right: {{VALUE}}{{UNIT}};',
					),
				)
			);

			$this->controls_manager->end_section();
		}
	}

}