<?php
namespace Jet_Engine\Modules\Data_Stores\Block_Types;

/**
 * Data Store Button View
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Button extends \Jet_Engine_Blocks_Views_Type_Base {

	/**
	 * Returns block name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'data-store-button';
	}

	/**
	 * Return attributes array
	 *
	 * @return array
	 */
	public function get_attributes() {
		return apply_filters( 'jet-engine/blocks-views/data-store-button/attributes', array(
			'store' => array(
				'type'    => 'string',
				'default' => '',
			),
			'label' => array(
				'type'    => 'string',
				'default' => __( 'Add to store', 'jet-engine' ),
			),
			'icon' => array(
				'type' => 'number',
			),
			'icon_url' => array(
				'type'    => 'string',
				'default' => '',
			),
//			'synch_grid' => array(
//				'type'    => 'boolean',
//				'default' => false,
//			),
//			'synch_grid_id' =>array(
//				'type'    => 'string',
//				'default' => '',
//			),
			'action_after_added' => array(
				'type'    => 'string',
				'default' => 'remove_from_store',
			),
			'added_to_store_label' => array(
				'type'    => 'string',
				'default' => '',
			),
			'added_to_store_icon' => array(
				'type' => 'number',
			),
			'added_to_store_icon_url' => array(
				'type'    => 'string',
				'default' => '',
			),
			'added_to_store_url' => array(
				'type'    => 'string',
				'default' => '',
			),
			'open_in_new' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'rel_attr' => array(
				'type'    => 'string',
				'default' => '',
			),
//			'object_context' => array(
//				'type'    => 'string',
//				'default' => 'default_object',
//			),
		) );
	}

	/**
	 * Add style block options
	 *
	 * @return void
	 */
	public function add_style_manager_options() {

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_style',
				'title' => __( 'Button', 'jet-engine' ),
				'initial_open' => true,
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'      => 'button_align',
				'label'   => __( 'Alignment', 'jet-engine' ),
				'type'    => 'choose',
				'options' => array(
					'flex-start' => array(
						'shortcut' => __( 'Start', 'jet-engine' ),
						'icon'     => ! is_rtl() ? 'dashicons-editor-alignleft' : 'dashicons-editor-alignright',
					),
					'center' => array(
						'shortcut' => __( 'Center', 'jet-engine' ),
						'icon'     => 'dashicons-editor-aligncenter',
					),
					'flex-end' => array(
						'shortcut' => __( 'End', 'jet-engine' ),
						'icon'     => ! is_rtl() ? 'dashicons-editor-alignright' : 'dashicons-editor-alignleft',
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link-wrapper' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'icon_size',
				'label'     => __( 'Icon Size', 'jet-engine' ),
				'type'      => 'range',
				'separator' => 'before',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 1,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link__icon' => 'font-size: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'icon_indent',
				'label'     => __( 'Icon Spacing', 'jet-engine' ),
				'type'      => 'range',
				'separator' => 'before',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 1,
							'max'  => 50,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link' => 'gap: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'typography',
				'label' => __( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->start_tabs(
			'style_controls',
			array(
				'id' => 'tabs_button_style',
			)
		);

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'tab_button_normal',
				'title' => __( 'Normal', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'button_text_color',
				'label' => __( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link' => 'fill: {{VALUE}}; color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'button_icon_color',
				'label' => __( 'Icon Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link__icon' => 'fill: {{VALUE}}; color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'button_background_color',
				'label' => __( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'tab_button_hover',
				'title' => __( 'Hover', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'button_text_color_hover',
				'label' => __( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jet-data-store-link:hover svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'button_icon_color_hover',
				'label' => __( 'Icon Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link:hover .jet-data-store-link__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jet-data-store-link:hover .jet-data-store-link__icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'button_background_color_hover',
				'label' => __( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'button_border_color_hover',
				'label' => __( 'Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'tab_button_in_store',
				'title' => __( 'In Store', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'button_text_color_in_store',
				'label' => __( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link.in-store:not(:hover)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jet-data-store-link.in-store:not(:hover) svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'button_icon_color_in_store',
				'label' => __( 'Icon Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link.in-store:not(:hover) .jet-data-store-link__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jet-data-store-link.in-store:not(:hover) .jet-data-store-link__icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'button_background_color_in_store',
				'label' => __( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link.in-store:not(:hover)' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'button_border_color_in_store',
				'label' => __( 'Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link.in-store:not(:hover)' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->end_tabs();

		$this->controls_manager->add_control(
			array(
				'id'             => 'button_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'separator'      => 'before',
				'disable_radius' => true,
				'css_selector'   => array(
					'{{WRAPPER}} .jet-data-store-link' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'button_border_radius',
				'label'     => __( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'separator' => 'before',
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'button_padding',
				'label' => __( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'           => 'button_margin',
				'label'        => __( 'Margin', 'jet-engine' ),
				'type'         => 'dimensions',
				'units'        => array( 'px' ),
				'input_props'  => array( 'min' => '-900' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-data-store-link-wrapper' => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->end_section();

	}

}
