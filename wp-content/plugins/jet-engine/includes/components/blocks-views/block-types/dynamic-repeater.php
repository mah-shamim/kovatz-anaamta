<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Blocks_Views_Type_Dynamic_Repeater' ) ) {

	/**
	 * Define Jet_Engine_Blocks_Views_Type_Dynamic_Repeater class
	 */
	class Jet_Engine_Blocks_Views_Type_Dynamic_Repeater extends Jet_Engine_Blocks_Views_Type_Base {

		/**
		 * Returns block name
		 *
		 * @return [type] [description]
		 */
		public function get_name() {
			return 'dynamic-repeater';
		}

		/**
		 * Returns CSS selector for nested element
		 *
		 * @param  string|array $el
		 * @return string
		 */
		public function css_selector( $el = null ) {
			if ( ! is_array( $el ) ) {
				return sprintf( '{{WRAPPER}} .jet-listing-dynamic-repeater%s', $el );
			} else {

				$res = array();

				foreach ( $el as $selector ) {
					$res[] = sprintf( '{{WRAPPER}} .jet-listing-dynamic-repeater%s', $selector );
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
			return apply_filters( 'jet-engine/blocks-views/block-types/attributes/dynamic-repeater', array(
				'dynamic_field_source' => array(
					'type' => 'string',
					'default' => '',
				),
				'dynamic_field_option' => array(
					'type' => 'string',
					'default' => '',
				),
				'dynamic_field_format' => array(
					'type' => 'string',
					'default' => '<span>%name%</span>',
				),
				'item_tag' => array(
					'type' => 'string',
					'default' => 'div',
				),
				'items_delimiter' => array(
					'type' => 'string',
					'default' => '',
				),
				'dynamic_field_before' => array(
					'type' => 'string',
					'default' => '',
				),
				'dynamic_field_after' => array(
					'type' => 'string',
					'default' => '',
				),
				'dynamic_field_counter' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'dynamic_field_leading_zero' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'dynamic_field_counter_after' => array(
					'type' => 'string',
					'default' => '',
				),
				'dynamic_field_counter_position' => array(
					'type' => 'string',
					'default' => 'at-left',
				),
				'hide_if_empty' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'object_context' => array(
					'type'    => 'string',
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
					'id'           => 'section_general_style',
					'initial_open' => true,
					'title'        => esc_html__( 'General', 'jet-engine' )
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'         => 'items_direction',
					'label'      => __( 'Direction', 'jet-engine' ),
					'type'       => 'choose',
					'attributes' => array(
						'default' => array(
							'value' => 'flex-start',
						),
					),
					'options' => array(
						'row'    => array(
							'label' => esc_html__( 'Horizontal', 'jet-engine' ),
							'icon'  => 'dashicons-arrow-right-alt',
						),
						'column' => array(
							'label' => esc_html__( 'Vertical', 'jet-engine' ),
							'icon'  => 'dashicons-arrow-down-alt',
						),
					),
					'css_selector' => array(
						$this->css_selector( '__items' ) => 'flex-direction: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'         => 'items_alignment',
					'label'      => __( 'Alignment', 'jet-engine' ),
					'type'       => 'choose',
					'attributes' => array(
						'default' => array(
							'value' => 'flex-start',
						),
					),
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
						$this->css_selector( '__items' )    => 'justify-content: {{VALUE}};',
						$this->css_selector( '__item > *' ) => 'justify-content: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'general_typography',
					'label'        => __( 'Typography', 'jet-engine' ),
					'type'         => 'typography',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item > *' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
					),
				)
			);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				array(
					'id'           => 'section_items_style',
					'initial_open' => false,
					'title'        => esc_html__( 'Items', 'jet-engine' )
				)
			);

			// Not supported
			//
			// $this->controls_manager->add_control(
			// 	array(
			// 		'id'        => 'fixed_size',
			// 		'label'     => __( 'Fixed Item Size', 'jet-engine' ),
			// 		'type'      => 'choose',
			// 		'default'   => '',
			// 		'separator' => 'before',
			// 		'options'   => array(
			// 			'yes' => array(
			// 				'shortcut' => __( 'Yes', 'jet-engine' ),
			// 				'icon'     => 'dashicons-yes',
			// 			),
			// 			'' => array(
			// 				'shortcut' => __( 'No', 'jet-engine' ),
			// 				'icon'     => 'dashicons-no',
			// 			),
			// 		),
			// 	)
			// );

			// $this->controls_manager->add_responsive_control(
			// 	array(
			// 		'id'    => 'item_width',
			// 		'label' => __( 'Fixed Item Width', 'jet-engine' ),
			// 		'type'  => 'range',
			// 		'units' => array(
			// 			array(
			// 				'value'     => 'px',
			// 				'intervals' => array(
			// 					'step' => 1,
			// 					'min'  => 15,
			// 					'max'  => 150,
			// 				),
			// 			),
			// 		),
			// 		'css_selector' => array(
			// 			$this->css_selector( '__item > *' ) => 'display: flex; width: {{VALUE}}{{UNIT}}; justify-content: center;',
			// 		),
			// 		'condition' => array(
			// 			'fixed_size' => 'yes'
			// 		)
			// 	)
			// );

			// $this->controls_manager->add_responsive_control(
			// 	array(
			// 		'id'    => 'item_height',
			// 		'label' => __( 'Fixed Item Height', 'jet-engine' ),
			// 		'type'  => 'range',
			// 		'units' => array(
			// 			array(
			// 				'value'     => 'px',
			// 				'intervals' => array(
			// 					'step' => 1,
			// 					'min'  => 15,
			// 					'max'  => 150,
			// 				),
			// 			),
			// 		),
			// 		'css_selector' => array(
			// 			$this->css_selector( '__item > *' ) => 'height: {{VALUE}}{{UNIT}}; display: flex; align-items: center;',
			// 		),
			// 		'condition' => array(
			// 			'fixed_size' => 'yes'
			// 		)
			// 	)
			// );

			$this->controls_manager->start_tabs(
				'style_controls',
				array(
					'id'        => 'tabs_item_style',
					'separator' => 'after',
				)
			);

			$this->controls_manager->start_tab(
				'style_controls',
				array(
					'id'    => 'tabs_item_style_normal',
					'title' => esc_html__( 'Normal', 'jet-engine' ),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_color',
					'label'        => esc_html__( 'Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item > *' ) => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_background_color',
					'label'        => esc_html__( 'Background Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'css_selector' => array(
						$this->css_selector( '__item > *' ) => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				array(
					'id'    => 'tabs_item_style_hover',
					'title' => esc_html__( 'Hover', 'jet-engine' ),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_color_hover',
					'label'        => esc_html__( 'Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item > *:hover' ) => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_background_color_hover',
					'label'        => esc_html__( 'Background Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'css_selector' => array(
						$this->css_selector( '__item > *:hover' ) => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_border_color_hover',
					'label'        => esc_html__( 'Border Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item > *:hover' ) => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control(
				array(
					'id'             => 'item_border',
					'label'          => esc_html__( 'Border', 'jet-engine' ),
					'type'           => 'border',
					'separator'      => 'before',
					'disable_radius' => true,
					'css_selector'   => array(
						$this->css_selector( '__item > *' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'item_border_radius',
					'label'        => esc_html__( 'Border Radius', 'jet-engine' ),
					'type'         => 'dimensions',
					'units'        => array( 'px', '%' ),
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item > *' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			// Not supported
			// $this->add_group_control(
			// 	Group_Control_Box_Shadow::get_type(),
			// 	array(
			// 		'name'     => 'item_box_shadow',
			// 		'selector' => $this->css_selector( '__counter' ),
			// 	)
			// );

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_padding',
					'label'        => esc_html__( 'Padding', 'jet-engine' ),
					'type'         => 'dimensions',
					'units'        => array( 'px', '%' ),
					'css_selector' => array(
						$this->css_selector( '__item > *' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_margin',
					'label'        => esc_html__( 'Margin', 'jet-engine' ),
					'type'         => 'dimensions',
					'units'        => array( 'px', '%' ),
					'css_selector' => array(
						$this->css_selector( '__item > *' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'separator' => 'before',
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
					'id'           => 'delimiter_typography',
					'label'        => __( 'Typography', 'jet-engine' ),
					'type'         => 'typography',
					'css_selector' => array(
						$this->css_selector( '__delimiter' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'delimiter_color',
					'label'        => esc_html__( 'Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__delimiter' ) => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'delimiter_margin',
					'label'        => esc_html__( 'Margin', 'jet-engine' ),
					'type'         => 'dimensions',
					'units'        => array( 'px', '%' ),
					'css_selector' => array(
						$this->css_selector( '__delimiter' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'separator' => 'before',
				)
			);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				array(
					'id'           => 'section_counter_style',
					'initial_open' => false,
					'title'        => esc_html__( 'Counters', 'jet-engine' )
				)
			);

			// Not supported
			//
			// $this->controls_manager->add_control(
			// 	array(
			// 		'id'        => 'counter_fixed_size',
			// 		'label'     => __( 'Fixed counter box size', 'jet-engine' ),
			// 		'type'      => 'choose',
			// 		'default'   => '',
			// 		'separator' => 'before',
			// 		'options'   => array(
			// 			'yes' => array(
			// 				'shortcut' => __( 'Yes', 'jet-engine' ),
			// 				'icon'     => 'dashicons-yes',
			// 			),
			// 			'' => array(
			// 				'shortcut' => __( 'No', 'jet-engine' ),
			// 				'icon'     => 'dashicons-no',
			// 			),
			// 		),
			// 	)
			// );

			// $this->controls_manager->add_responsive_control(
			// 	array(
			// 		'id'    => 'counter_item_size',
			// 		'label' => __( 'Item Width', 'jet-engine' ),
			// 		'type'  => 'range',
			// 		'units' => array(
			// 			array(
			// 				'value'     => 'px',
			// 				'intervals' => array(
			// 					'step' => 1,
			// 					'min'  => 15,
			// 					'max'  => 150,
			// 				),
			// 			),
			// 		),
			// 		'css_selector' => array(
			// 			$this->css_selector( '__item > *' ) => 'display: flex; width: {{VALUE}}{{UNIT}}; justify-content: center;',
			// 		),
			// 		'condition' => array(
			// 			'counter_fixed_size' => 'yes'
			// 		)
			// 	)
			// );

			$this->controls_manager->add_control(
				array(
					'id'           => 'counter_item_color',
					'label'        => esc_html__( 'Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item') . ' .jet-listing-dynamic-repeater__counter' => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'counter_item_typography',
					'label'        => __( 'Typography', 'jet-engine' ),
					'type'         => 'typography',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item') . ' .jet-listing-dynamic-repeater__counter' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'counter_item_background_color',
					'label'        => esc_html__( 'Background color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item') . ' .jet-listing-dynamic-repeater__counter' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'             => 'counter_item_border',
					'label'          => esc_html__( 'Border', 'jet-engine' ),
					'type'           => 'border',
					'separator'      => 'before',
					'disable_radius' => true,
					'css_selector'   => array(
						$this->css_selector( '__item') . ' .jet-listing-dynamic-repeater__counter' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'counter_item_border_radius',
					'label'        => esc_html__( 'Border Radius', 'jet-engine' ),
					'type'         => 'dimensions',
					'units'        => array( 'px', '%' ),
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item') . ' .jet-listing-dynamic-repeater__counter' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			// Not supported
			// $this->add_group_control(
			// 	Group_Control_Box_Shadow::get_type(),
			// 	array(
			// 		'name'     => 'counter_item_box_shadow',
			// 		'selector' => $this->css_selector( '__counter' ),
			// 	)
			// );

			//Not supported
			//
			// $this->controls_manager->add_control(
			// 	array(
			// 		'id'           => 'counter_item_padding',
			// 		'label'        => __( 'Padding', 'jet-engine' ),
			// 		'type'         => 'dimensions',
			// 		'units'        => array( 'px', '%' ),
			// 		'separator'    => 'before',
			// 		'css_selector' => array(
			// 			$this->css_selector( '__counter' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
			// 		),
			// 		'condition' => array(
			// 			'fixed_size!' => 'yes',
			// 		),
			// 	)
			// );

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'counter_item_margin',
					'label'        => __( 'Margin', 'jet-engine' ),
					'type'         => 'dimensions',
					'units'        => array( 'px', '%' ),
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item') . ' .jet-listing-dynamic-repeater__counter' => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'    => 'counter_item_depth',
					'label' => esc_html__( 'Counter depth', 'jet-engine' ),
					'type'  => 'range',
					'units' => array(
						array(
							'value'     => 'px',
							'intervals' => array(
								'step' => 1,
								'min'  => 0,
								'max'  => 10,
							),
						),
					),
					'css_selector' => array(
						$this->css_selector( '__item') . ' .jet-listing-dynamic-repeater__counter' => 'z-index: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'         => 'icon_self_align',
					'label'      => __( 'Alignment', 'jet-engine' ),
					'type'       => 'choose',
					'attributes' => array(
						'default' => array(
							'value' => '',
						),
					),
					'options' => array(
						'flex-start'    => array(
							'label' => esc_html__( 'Left/Top', 'jet-engine' ),
							'icon'  => 'dashicons-editor-alignleft',
						),
						'center' => array(
							'label' => esc_html__( 'Center/Middle', 'jet-engine' ),
							'icon'  => 'dashicons-editor-aligncenter',
						),
						'flex-end' => array(
							'label' => esc_html__( 'Right/Bottom', 'jet-engine' ),
							'icon'  => 'dashicons-editor-alignright',
						),
						'stretch' => array(
							'label' => esc_html__( 'Stretch', 'jet-engine' ),
							'icon'  => 'dashicons-editor-justify',
						),
					),
					'css_selector' => array(
						$this->css_selector( '__item') . ' .jet-listing-dynamic-repeater__counter' => 'align-self: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->end_section();
		}
	}
}