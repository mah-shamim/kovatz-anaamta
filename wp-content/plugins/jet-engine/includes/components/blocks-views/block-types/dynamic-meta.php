<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Blocks_Views_Type_Dynamic_Meta' ) ) {

	/**
	 * Define Jet_Engine_Blocks_Views_Type_Dynamic_Meta class
	 */
	class Jet_Engine_Blocks_Views_Type_Dynamic_Meta extends Jet_Engine_Blocks_Views_Type_Base {

		/**
		 * Returns block name
		 *
		 * @return [type] [description]
		 */
		public function get_name() {
			return 'dynamic-meta';
		}

		/**
		 * Returns CSS selector for nested element
		 *
		 * @param  string|array $el
		 * @return string
		 */
		public function css_selector( $el = null ) {
			if ( ! is_array( $el ) ) {
				return sprintf( '{{WRAPPER}} .jet-listing-dynamic-meta%s', $el );
			} else {

				$res = array();

				foreach ( $el as $selector ) {
					$res[] = sprintf( '{{WRAPPER}} .jet-listing-dynamic-meta%s', $selector );
				}

				return implode( ', ', $res );
			}
		}

		public function prepare_attributes( $attributes = array() ) {

			$attributes['meta_items'] = array();

			if ( ! isset( $attributes['date_enabled'] ) || true === $attributes['date_enabled'] ) {

				$attributes['meta_items'][] = array(
					'type'          => 'date',
					'selected_icon' => $this->get_attr( 'date_selected_icon', $attributes ),
					'prefix'        => $this->get_attr( 'date_prefix', $attributes ),
					'suffix'        => $this->get_attr( 'date_suffix', $attributes ),
				);

			}

			if ( ! isset( $attributes['author_enabled'] ) || true === $attributes['author_enabled'] ) {

				$attributes['meta_items'][] = array(
					'type'          => 'author',
					'selected_icon' => $this->get_attr( 'author_selected_icon', $attributes ),
					'prefix'        => $this->get_attr( 'author_prefix', $attributes ),
					'suffix'        => $this->get_attr( 'author_suffix', $attributes ),
				);

			}

			if ( ! isset( $attributes['comments_enabled'] ) || true === $attributes['comments_enabled'] ) {

				$attributes['meta_items'][] = array(
					'type'          => 'comments',
					'selected_icon' => $this->get_attr( 'comments_selected_icon', $attributes ),
					'prefix'        => $this->get_attr( 'comments_prefix', $attributes ),
					'suffix'        => $this->get_attr( 'comments_suffix', $attributes ),
				);

			}

			$unset = array(
				'date_enabled',
				'date_selected_icon',
				'date_prefix',
				'date_suffix',
				'author_enabled',
				'author_selected_icon',
				'author_prefix',
				'author_suffix',
				'comments_enabled',
				'comments_selected_icon',
				'comments_prefix',
				'comments_suffix',
			);


			foreach ( $unset as $key ) {
				if ( isset( $attributes[ $key ] ) ) {
					unset( $attributes[ $key ] );
				}
			}

			return $attributes;
		}

		/**
		 * Return attributes array
		 *
		 * @return array
		 */
		public function get_attributes() {
			return array(
				'date_enabled' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'date_selected_icon' => array(
					'type' => 'number',
				),
				'date_selected_icon_url' => array(
					'type' => 'string',
					'default' => '',
				),
				'date_prefix' => array(
					'type' => 'string',
					'default' => '',
				),
				'date_suffix' => array(
					'type' => 'string',
					'default' => '',
				),
				'date_format' => array(
					'type' => 'string',
					'default' => 'F-j-Y',
				),
				'date_link' => array(
					'type' => 'string',
					'default' => 'archive',
				),
				'author_enabled' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'author_selected_icon' => array(
					'type' => 'number',
				),
				'author_selected_icon_url' => array(
					'type' => 'string',
					'default' => '',
				),
				'author_prefix' => array(
					'type' => 'string',
					'default' => '',
				),
				'author_suffix' => array(
					'type' => 'string',
					'default' => '',
				),
				'author_link' => array(
					'type' => 'string',
					'default' => 'archive',
				),
				'comments_enabled' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'comments_selected_icon' => array(
					'type' => 'number',
				),
				'comments_selected_icon_url' => array(
					'type' => 'string',
					'default' => '',
				),
				'comments_prefix' => array(
					'type' => 'string',
					'default' => '',
				),
				'comments_suffix' => array(
					'type' => 'string',
					'default' => '',
				),
				'comments_link' => array(
					'type' => 'string',
					'default' => 'single',
				),
				'zero_comments_format' => array(
					'type' => 'string',
					'default' => '0',
				),
				'one_comment_format' => array(
					'type' => 'string',
					'default' => '1',
				),
				'more_comments_format' => array(
					'type' => 'string',
					'default' => '%',
				),
				'layout' => array(
					'type' => 'string',
					'default' => 'inline',
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
					'id'           => 'section_general_style',
					'initial_open' => true,
					'title'        => esc_html__( 'General', 'jet-engine' )
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'         => 'meta_alignment',
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

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_typography',
					'label'        => __( 'Typography', 'jet-engine' ),
					'type'         => 'typography',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( array( '__item', '__item-val') ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
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
					'id'    => 'dynamic_item_normal',
					'title' => esc_html__( 'Normal', 'jet-engine' ),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_bg_color',
					'label'        => esc_html__( 'Background Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'css_selector' => array(
						$this->css_selector( '__item' ) => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_color',
					'label'        => esc_html__( 'Text Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( array( '__item', '__item-val') ) => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				array(
					'id'    => 'dynamic_item_hover',
					'title' => esc_html__( 'Hover', 'jet-engine' ),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_bg_color_hover',
					'label'        => esc_html__( 'Background Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'css_selector' => array(
						$this->css_selector( '__item:hover' ) => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_color_hover',
					'label'        => esc_html__( 'Text Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( array( '__item:hover', '__item-val:hover') ) => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_hover_border_color',
					'label'        => esc_html__( 'Border Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item-val:hover' ) => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'item_padding',
					'label'        => esc_html__( 'Padding', 'jet-engine' ),
					'type'         => 'dimensions',
					'units'        => array( 'px', '%', 'em' ),
					'css_selector' => array(
						$this->css_selector( '__item' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'item_margin',
					'label'        => esc_html__( 'Margin', 'jet-engine' ),
					'type'         => 'dimensions',
					'units'        => array( 'px', '%', 'em' ),
					'css_selector' => array(
						$this->css_selector( '__item' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'separator' => 'before',
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'             => 'item_border',
					'label'          => esc_html__( 'Border', 'jet-engine' ),
					'type'           => 'border',
					'separator'      => 'before',
					'disable_radius' => true,
					'css_selector'   => array(
						$this->css_selector( '__item' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
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
						$this->css_selector( '__item' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			// Not supported
			//
			// $this->add_group_control(
			// 	Group_Control_Box_Shadow::get_type(),
			// 	array(
			// 		'name'     => 'item_box_shadow',
			// 		'selector' => $this->css_selector( '__item' ),
			// 	)
			// );

			$this->controls_manager->add_control(
				array(
					'id'    => 'icon_color',
					'label' => __( 'Icon Color', 'jet-engine' ),
					'type'  => 'color-picker',
					'css_selector' => array(
						$this->css_selector( '__icon svg path' ) => 'fill: {{VALUE}}',
					),
					'separator' => 'before',
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'icon_size',
					'label'        => __( 'Icon Size', 'jet-engine' ),
					'type'         => 'range',
					'separator'    => 'before',
					'attributes'   => array(
						'default' => array(
							'value' => 16,
						),
					),
					'css_selector' => array(
						$this->css_selector( '__icon svg' ) => 'width: {{VALUE}}px !important; height: auto !important;',
						$this->css_selector( '__icon img' ) => 'width: {{VALUE}}px !important; height: auto !important;',
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
						'body:not(.rtl) ' . $this->css_selector('__icon svg' ) => 'margin-right: {{VALUE}}px;',
						'body:not(.rtl) ' . $this->css_selector( '__icon img' ) => 'margin-right: {{VALUE}}px;',
						'body.rtl ' . $this->css_selector('__icon svg' ) => 'margin-left: {{VALUE}}px;',
						'body.rtl ' . $this->css_selector( '__icon img' ) => 'margin-left: {{VALUE}}px;',
					),
				)
			);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				array(
					'id'           => 'section_items_value_style',
					'initial_open' => false,
					'title'        => esc_html__( 'Items Value', 'jet-engine' )
				)
			);

			$this->controls_manager->start_tabs(
				'style_controls',
				array(
					'id'        => 'tabs_item_val_style',
					'separator' => 'after',
				)
			);

			$this->controls_manager->start_tab(
				'style_controls',
				array(
					'id'    => 'dynamic_item_val_normal',
					'title' => esc_html__( 'Normal', 'jet-engine' ),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_val_bg_color',
					'label'        => esc_html__( 'Background Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'css_selector' => array(
						$this->css_selector( '__item-val' ) => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_val_color',
					'label'        => esc_html__( 'Text Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item-val' ) => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				array(
					'id'    => 'dynamic_item_val_hover',
					'title' => esc_html__( 'Hover', 'jet-engine' ),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_val_bg_color_hover',
					'label'        => esc_html__( 'Background Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'css_selector' => array(
						$this->css_selector( '__item-val:hover' ) => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_val_color_hover',
					'label'        => esc_html__( 'Text Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item-val:hover' ) => 'color: {{VALUE}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'item_val_hover_border_color',
					'label'        => esc_html__( 'Border Color', 'jet-engine' ),
					'type'         => 'color-picker',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item-val:hover' ) => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'item_val_padding',
					'label'        => __( 'Padding', 'jet-engine' ),
					'type'         => 'dimensions',
					'units'        => array( 'px', '%', 'em' ),
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item-val' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'item_val_margin',
					'label'        => __( 'Margin', 'jet-engine' ),
					'type'         => 'dimensions',
					'units'        => array( 'px', '%', 'em' ),
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item-val' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'             => 'item_val_border',
					'label'          => esc_html__( 'Border', 'jet-engine' ),
					'type'           => 'border',
					'separator'      => 'before',
					'disable_radius' => true,
					'css_selector'   => array(
						$this->css_selector( '__item-val' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'           => 'item_val_border_radius',
					'label'        => __( 'Border Radius', 'jet-engine' ),
					'type'         => 'dimensions',
					'units'        => array( 'px', '%' ),
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__item-val' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			// Not supported
			//
			// $this->add_group_control(
			// 	Group_Control_Box_Shadow::get_type(),
			// 	array(
			// 		'name'     => 'item_box_shadow',
			// 		'selector' => $this->css_selector( '__item' ),
			// 	)
			// );

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				array(
					'id'           => 'section_prefix_style',
					'initial_open' => false,
					'title'        => esc_html__( 'Prefix', 'jet-engine' )
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'    => 'prefix_l_gap',
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
					'css_selector' => array(
						$this->css_selector( '__prefix' ) => 'margin-left: {{VALUE}}{{UNIT}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'    => 'prefix_r_gap',
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
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__prefix' ) => 'margin-right: {{VALUE}}{{UNIT}};',
					),
				)
			);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				array(
					'id'           => 'section_suffix_style',
					'initial_open' => false,
					'title'        => esc_html__( 'Suffix', 'jet-engine' )
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'    => 'suffix_l_gap',
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
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__suffix' ) => 'margin-left: {{VALUE}}{{UNIT}};',
					),
				)
			);

			$this->controls_manager->add_responsive_control(
				array(
					'id'    => 'suffix_r_gap',
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
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector( '__suffix' ) => 'margin-right: {{VALUE}}{{UNIT}};',
					),
				)
			);

			$this->controls_manager->end_section();
		}
	}
}