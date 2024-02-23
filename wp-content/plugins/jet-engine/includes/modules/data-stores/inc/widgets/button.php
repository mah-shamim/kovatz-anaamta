<?php
namespace Jet_Engine\Modules\Data_Stores\Widgets;

use Jet_Engine\Modules\Data_Stores\Module as Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Button extends \Elementor\Widget_Base {

	public function get_name() {
		return 'jet-engine-data-store-button';
	}

	public function get_title() {
		return __( 'Data Store Button', 'jet-engine' );
	}

	public function get_icon() {
		return 'jet-engine-icon-data-store-button';
	}

	public function get_categories() {
		return array( 'jet-listing-elements' );
	}

	public function get_help_url() {
		return false;
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'jet-engine' ),
			)
		);

		$this->add_control(
			'store',
			array(
				'label'   => __( 'Select store', 'jet-engine' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'groups'  => Module::instance()->elementor_integration->get_store_options(),
			)
		);

		$this->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'jet-engine' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Add to store', 'jet-engine' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'       => __( 'Icon', 'jet-engine' ),
				'label_block' => false,
				'type'        => \Elementor\Controls_Manager::ICONS,
				'skin'        => 'inline',
			)
		);

		$this->add_control(
			'synch_grid',
			array(
				'label'       => __( 'Reload listing grid on success', 'jet-engine' ),
				'description' => __( 'You can use this option to reload listing grid with current Store posts on success', 'jet-engine' ),
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'default'     => '',
			)
		);

		$this->add_control(
			'synch_grid_id',
			array(
				'label'       => __( 'Listing grid ID', 'jet-engine' ),
				'label_block' => true,
				'description' => __( 'Here you need to set listing ID to reload. The same ID must be set in the Advanced settings of selected listing', 'jet-engine' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'condition'   => array(
					'synch_grid' => 'yes',
				),
			)
		);

		if ( function_exists( 'jet_popup' ) ) {
			$this->add_control(
				'trigger_popup',
				array(
					'label'       => __( 'Open popup on success', 'jet-engine' ),
					'description' => __( 'Open selected popup from JetPopup after post successfully added to store. Popup should be selected in the <b>Advanced Tab > JetPopup</b> section, <b>Trigger Type</b> must be set to <b>None</b>', 'jet-engine' ),
					'type'        => \Elementor\Controls_Manager::SWITCHER,
					'default'     => '',
				)
			);
		}

		$this->add_control(
			'action_after_added',
			array(
				'label'       => __( 'Action after an item added to store', 'jet-engine' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'remove_from_store',
				'separator'   => 'before',
				'options'     => array(
					'remove_from_store' => __( 'Remove from store button', 'jet-engine' ),
					'switch_status'     => __( 'Switch button status', 'jet-engine' ),
					'hide'              => __( 'Hide button', 'jet-engine' ),
				),
			)
		);

		$this->add_control(
			'added_to_store_label',
			array(
				'label'       => __( 'Label after added to store', 'jet-engine' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'action_after_added' => array( 'switch_status', 'remove_from_store' ),
				),
			)
		);

		$this->add_control(
			'added_to_store_icon',
			array(
				'label'       => __( 'Icon after added to store', 'jet-engine' ),
				'label_block' => false,
				'type'        => \Elementor\Controls_Manager::ICONS,
				'skin'        => 'inline',
				'condition'   => array(
					'action_after_added' => array( 'switch_status', 'remove_from_store' ),
				),
			)
		);

		$this->add_control(
			'added_to_store_url',
			array(
				'label'       => __( 'URL after added to store ', 'jet-engine' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'dynamic'     => array(
					'active'     => true,
					'categories' => array(
						\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
						\Jet_Engine_Dynamic_Tags_Module::URL_CATEGORY,
					),
				),
				'condition' => array(
					'action_after_added' => array( 'switch_status' ),
				),
			)
		);

		$this->add_control(
			'open_in_new',
			array(
				'label'        => __( 'Open in new window', 'jet-engine' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'action_after_added' => array( 'switch_status' ),
				),
			)
		);

		$this->add_control(
			'rel_attr',
			array(
				'label'   => __( 'Add "rel" attr', 'jet-engine' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => \Jet_Engine_Tools::get_rel_attr_options(),
				'condition' => array(
					'action_after_added' => array( 'switch_status' ),
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
			'section_style',
			array(
				'label' => __( 'Button', 'jet-engine' ),
				'tab'  => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'button_align',
			array(
				'label' => __( 'Alignment', 'jet-engine' ),
				'type'  => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Start', 'elementor' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => __( 'Center', 'elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => __( 'End', 'elementor' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .jet-data-store-link-wrapper' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label' => __( 'Icon Size', 'jet-engine' ),
				'type'  => \Elementor\Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-data-store-link__icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_indent',
			array(
				'label' => __( 'Icon Spacing', 'jet-engine' ),
				'type'  => \Elementor\Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-data-store-link' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .jet-data-store-link',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'text_shadow',
				'selector' => '{{WRAPPER}} .jet-data-store-link',
			)
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			array(
				'label' => __( 'Normal', 'jet-engine' ),
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => __( 'Text Color', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .jet-data-store-link' => 'fill: {{VALUE}}; color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_icon_color',
			array(
				'label'     => __( 'Icon Color', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .jet-data-store-link__icon' => 'fill: {{VALUE}}; color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name' => 'button_background',
				'label'    => __( 'Background', 'jet-engine' ),
				'types'    => array( 'classic', 'gradient' ),
				'exclude'  => array( 'image' ),
				'selector' => '{{WRAPPER}} .jet-data-store-link',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			array(
				'label' => __( 'Hover', 'jet-engine' ),
			)
		);

		$this->add_control(
			'button_text_color_hover',
			array(
				'label' => __( 'Text Color', 'jet-engine' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-data-store-link:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jet-data-store-link:hover svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_icon_color_hover',
			array(
				'label' => __( 'Icon Color', 'jet-engine' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-data-store-link:hover .jet-data-store-link__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jet-data-store-link:hover .jet-data-store-link__icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'     => 'button_background_hover',
				'label'    => __( 'Background', 'jet-engine' ),
				'types'    => array( 'classic', 'gradient' ),
				'exclude'  => array( 'image' ),
				'selector' => '{{WRAPPER}} .jet-data-store-link:hover',
			)
		);

		$this->add_control(
			'button_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'condition' => array(
					'button_border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-data-store-link:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_in_store',
			array(
				'label' => __( 'In Store', 'jet-engine' ),
				'condition' => array(
					'action_after_added' => array( 'remove_from_store', 'switch_status' ),
				),
			)
		);

		$this->add_control(
			'button_text_color_in_store',
			array(
				'label' => __( 'Text Color', 'jet-engine' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-data-store-link.in-store:not(:hover)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jet-data-store-link.in-store:not(:hover) svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'action_after_added' => array( 'remove_from_store', 'switch_status' ),
				),
			)
		);

		$this->add_control(
			'button_icon_color_in_store',
			array(
				'label' => __( 'Icon Color', 'jet-engine' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-data-store-link.in-store:not(:hover) .jet-data-store-link__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jet-data-store-link.in-store:not(:hover) .jet-data-store-link__icon svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'action_after_added' => array( 'remove_from_store', 'switch_status' ),
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'      => 'button_background_in_store',
				'label'     => __( 'Background', 'jet-engine' ),
				'types'     => array( 'classic', 'gradient' ),
				'exclude'   => array( 'image' ),
				'selector'  => '{{WRAPPER}} .jet-data-store-link.in-store:not(:hover)',
				'condition' => array(
					'action_after_added' => array( 'remove_from_store', 'switch_status' ),
				),
			)
		);

		$this->add_control(
			'button_border_color_in_store',
			array(
				'label'     => __( 'Border Color', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-data-store-link.in-store:not(:hover)' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'button_border_border!' => '',
					'action_after_added' => array( 'remove_from_store', 'switch_status' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'      => 'button_border',
				'selector'  => '{{WRAPPER}} .jet-data-store-link',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-data-store-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .jet-data-store-link',
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'jet-engine' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', 'em', '%' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-data-store-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$instance = jet_engine()->listings->get_render_instance( 'data-store-button', $this->get_settings_for_display() );
		$instance->render_content();
	}

}
