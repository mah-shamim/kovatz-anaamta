<?php
/**
 * Class: Jet_Woo_Builder_Products_Loop
 * Name: Products Loop
 * Slug: jet-woo-builder-products-loop
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Products_Loop extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-builder-products-loop';
	}

	public function get_title() {
		return esc_html__( 'Products Loop', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-shop-loop';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-and-set-a-shop-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'shop' );
	}

	protected function register_controls() {

		$templates    = jet_woo_builder_post_type()->get_templates_query_args( 'archive' );
		$admin_url    = admin_url( 'admin.php' );
		$notification = sprintf(
			'<p>%s <a href="%s" target="_blank">%s</a>.</p>',
			__( 'Before using the custom template functionality, make sure to Enable Custom Archive Product option', 'jet-woo-builder' ),
			esc_url( $admin_url . '?page=wc-settings&tab=jet-woo-builder-settings' ),
			__( 'here', 'jet-woo-builder' )
		);

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-woo-builder-products-loop/css-scheme',
			array(
				'switcher'              => '.jet-woo-builder-products-loop .jet-woo-switcher-controls-wrapper',
				'buttons'               => '.jet-woo-builder-products-loop .jet-woo-switcher-btn',
				'active_button'         => '.jet-woo-builder-products-loop .jet-woo-switcher-btn.active',
				'main_button'           => '.jet-woo-builder-products-loop .jet-woo-switcher-btn-main',
				'secondary_button'      => '.jet-woo-builder-products-loop .jet-woo-switcher-btn-secondary',
				'switcher_icon'         => '.jet-woo-builder-products-loop .jet-woo-switcher-btn .jet-woo-switcher-btn__icon',
				'switcher_icon_hover'   => '.jet-woo-builder-products-loop .jet-woo-switcher-btn:hover .jet-woo-switcher-btn__icon',
				'switcher_icon_active'  => '.jet-woo-builder-products-loop .jet-woo-switcher-btn.active .jet-woo-switcher-btn__icon',
				'switcher_label'        => '.jet-woo-builder-products-loop .jet-woo-switcher-btn .jet-woo-switcher-btn__label',
				'switcher_label_hover'  => '.jet-woo-builder-products-loop .jet-woo-switcher-btn:hover .jet-woo-switcher-btn__label',
				'switcher_label_active' => '.jet-woo-builder-products-loop .jet-woo-switcher-btn.active .jet-woo-switcher-btn__label',
			)
		);

		$this->start_controls_section(
			'template_section',
			[
				'label' => __( 'Products Loop', 'jet-woo-builder' ),
			]
		);

		if ( ! filter_var( jet_woo_builder_shop_settings()->get( 'custom_archive_page' ), FILTER_VALIDATE_BOOLEAN ) ) {
			$this->add_control(
				'custom_templates_notification',
				[
					'raw'             => $notification,
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-descriptor elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->end_controls_section();
		} else {
			$this->add_control(
				'archive_item_layout',
				[
					'label'       => __( 'Template', 'jet-woo-builder' ),
					'label_block' => true,
					'type'        => 'jet-query',
					'query_type'  => 'post',
					'query'       => $templates,
					'edit_button' => [
						'active' => true,
						'label'  => __( 'Edit Template', 'jet-woo-builder' ),
					],
					'separator'   => 'after',
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'layout_switcher_section',
				[
					'label' => __( 'Layout Switcher', 'jet-woo-builder' ),
				]
			);

			$this->add_control(
				'switcher_enable',
				[
					'label'              => __( 'Enable', 'jet-woo-builder' ),
					'type'               => Controls_Manager::SWITCHER,
					'frontend_available' => true,
				]
			);

			$this->start_controls_tabs(
				'layouts_tabs',
				[
					'condition' => [
						'switcher_enable' => 'yes',
					],
				]
			);

			$this->start_controls_tab(
				'main_tab',
				[
					'label' => __( 'Main', 'jet-woo-builder' ),
				]
			);

			$this->add_control(
				'main_layout',
				[
					'label'              => __( 'Template', 'jet-woo-builder' ),
					'label_block'        => true,
					'type'               => 'jet-query',
					'query_type'         => 'post',
					'query'              => $templates,
					'edit_button'        => [
						'active' => true,
						'label'  => __( 'Edit Template', 'jet-woo-builder' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'main_layout_switcher_label',
				[
					'label'   => __( 'Label', 'jet-woo-builder' ),
					'type'    => Controls_Manager::TEXT,
					'default' => __( 'Main', 'jet-woo-builder' ),
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$this->__add_advanced_icon_control(
				'main_layout_switcher_icon',
				[
					'label'       => __( 'Icon', 'jet-woo-builder' ),
					'type'        => Controls_Manager::ICON,
					'label_block' => true,
					'file'        => '',
					'default'     => 'fa fa-th',
					'fa5_default' => [
						'value'   => 'fas fa-th',
						'library' => 'fa-solid',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'secondary_tab',
				[
					'label' => __( 'Secondary', 'jet-woo-builder' ),
				]
			);

			$this->add_control(
				'secondary_layout',
				[
					'label'              => __( 'Template', 'jet-woo-builder' ),
					'label_block'        => true,
					'type'               => 'jet-query',
					'query_type'         => 'post',
					'query'              => $templates,
					'edit_button'        => [
						'active' => true,
						'label'  => __( 'Edit Template', 'jet-woo-builder' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'secondary_layout_switcher_label',
				[
					'label'   => __( 'Label', 'jet-woo-builder' ),
					'type'    => Controls_Manager::TEXT,
					'default' => __( 'Secondary', 'jet-woo-builder' ),
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$this->__add_advanced_icon_control(
				'secondary_layout_switcher_icon',
				[
					'label'       => __( 'Icon', 'jet-woo-builder' ),
					'type'        => Controls_Manager::ICON,
					'label_block' => true,
					'file'        => '',
					'default'     => 'fa fa-th-list',
					'fa5_default' => [
						'value'   => 'fas fa-th-list',
						'library' => 'fa-solid',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->end_controls_section();

			$this->start_controls_section(
				'section_switcher_wrapper_styles_section',
				[
					'label'     => __( 'Switcher Wrapper', 'jet-woo-builder' ),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'switcher_enable' => 'yes',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'switcher_background',
					'types'    => [ 'classic', 'gradient' ],
					'exclude'  => [ 'image' ],
					'selector' => '{{WRAPPER}} ' . $css_scheme['switcher'],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'     => 'switcher_border',
					'label'    => __( 'Border', 'jet-woo-builder' ),
					'selector' => '{{WRAPPER}} ' . $css_scheme['switcher'],
				]
			);

			$this->add_responsive_control(
				'switcher_border_radius',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Border Radius', 'jet-woo-builder' ),
					'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['switcher'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'switcher_margin',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Margin', 'jet-woo-builder' ),
					'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['switcher'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'switcher_padding',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Padding', 'jet-woo-builder' ),
					'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['switcher'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'switcher_buttons_alignment',
				[
					'label'     => __( 'Alignment', 'jet-woo-builder' ),
					'type'      => Controls_Manager::CHOOSE,
					'default'   => 'flex-end',
					'options'   => jet_woo_builder_tools()->get_available_flex_h_align_types( true ),
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['switcher'] => 'justify-content: {{VALUE}};',
					],
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_switcher_buttons_style_section',
				[
					'label'     => __( 'Switcher Buttons', 'jet-woo-builder' ),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'switcher_enable' => 'yes',
					],
				]
			);

			$this->add_control(
				'switcher_buttons_custom_size',
				[
					'label' => __( 'Custom Size', 'jet-woo-builder' ),
					'type'  => Controls_Manager::SWITCHER,
				]
			);

			$this->add_responsive_control(
				'switcher_buttons_custom_width',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Width', 'jet-woo-builder' ),
					'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => [
						'px' => [
							'min' => 40,
							'max' => 1000,
						],
						'em' => [
							'min' => 0.1,
							'max' => 20,
						],
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['buttons'] => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition'  => [
						'switcher_buttons_custom_size' => 'yes',
					],
				]
			);

			$this->add_responsive_control(
				'switcher_buttons_custom_height',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Height', 'jet-woo-builder' ),
					'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => [
						'px' => [
							'min' => 10,
							'max' => 1000,
						],
						'em' => [
							'min' => 0.1,
							'max' => 20,
						],
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['buttons'] => 'height: {{SIZE}}{{UNIT}};',
					],
					'condition'  => [
						'switcher_buttons_custom_size' => 'yes',
					],
				]
			);

			$this->add_responsive_control(
				'switcher_buttons_distance',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Gap', 'jet-woo-builder' ),
					'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => [
						'px' => [
							'min' => 0,
							'max' => 200,
						],
						'em' => [
							'min' => 0.1,
							'max' => 20,
						],
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['switcher'] => 'gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'switcher_buttons_label_typography',
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} ' . $css_scheme['switcher_label'],
				]
			);

			$this->start_controls_tabs( 'tabs_switcher_buttons_styles' );

			$this->start_controls_tab(
				'tab_switcher_buttons_normal',
				array(
					'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
				)
			);

			$this->add_control(
				'switcher_buttons_normal_label_color',
				[
					'label'     => __( 'Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['switcher_label'] => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'switcher_buttons_normal_background',
					'types'    => [ 'classic', 'gradient' ],
					'exclude'  => [ 'image' ],
					'selector' => '{{WRAPPER}} ' . $css_scheme['buttons'],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_switcher_buttons_hover',
				array(
					'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
				)
			);

			$this->add_control(
				'switcher_buttons_hover_label_color',
				[
					'label'     => __( 'Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['switcher_label_hover'] => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'switcher_buttons_hover_background',
					'types'    => [ 'classic', 'gradient' ],
					'exclude'  => [ 'image' ],
					'selector' => '{{WRAPPER}} ' . $css_scheme['buttons'] . ':hover',
				]
			);

			$this->add_control(
				'switcher_buttons_hover_border_color',
				[
					'label'     => __( 'Border Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['buttons'] . ':hover' => 'border-color: {{VALUE}}',
					],
					'condition' => [
						'switcher_buttons_normal_border_border!' => '',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_switcher_buttons_active',
				array(
					'label' => esc_html__( 'Active', 'jet-woo-builder' ),
				)
			);

			$this->add_control(
				'switcher_buttons_active_label_color',
				[
					'label'     => __( 'Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['switcher_label_active'] => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'switcher_buttons_active_background',
					'selector' => '{{WRAPPER}} ' . $css_scheme['active_button'],
					'types'    => [ 'classic', 'gradient' ],
					'exclude'  => [ 'image' ],
				]
			);

			$this->add_control(
				'switcher_buttons_active_border_color',
				[
					'label'     => __( 'Border Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['active_button'] => 'border-color: {{VALUE}}',
					],
					'condition' => [
						'switcher_buttons_normal_border_border!' => '',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'      => 'switcher_buttons_normal_border',
					'label'     => __( 'Border', 'jet-woo-builder' ),
					'selector'  => '{{WRAPPER}} ' . $css_scheme['buttons'],
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'switcher_buttons_normal_border_radius',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Border Radius', 'jet-woo-builder' ),
					'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['buttons'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'switcher_buttons_normal_box_shadow',
					'selector' => '{{WRAPPER}} ' . $css_scheme['buttons'],
				]
			);

			$this->add_responsive_control(
				'switcher_buttons_padding',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Padding', 'jet-woo-builder' ),
					'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['buttons'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'switcher_buttons_content_alignment',
				[
					'label'     => __( 'Alignment', 'jet-woo-builder' ),
					'type'      => Controls_Manager::CHOOSE,
					'default'   => 'center',
					'options'   => jet_woo_builder_tools()->get_available_flex_h_align_types(),
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['buttons'] => 'justify-content: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'switcher_buttons_icon_heading',
				[
					'label'     => __( 'Icon', 'jet-woo-builder' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'switcher_buttons_icon_font_size',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Size', 'jet-woo-builder' ),
					'size_units' => $this->set_custom_size_unit( [ 'px', 'em', 'rem' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['switcher_icon'] => 'font-size: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_responsive_control(
				'switcher_buttons_icon_box_width',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Box Width', 'jet-woo-builder' ),
					'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['switcher_icon'] => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'switcher_buttons_icon_box_height',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Box Height', 'jet-woo-builder' ),
					'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['switcher_icon'] => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'switcher_buttons_icon_spacing',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Indent', 'jet-woo-builder' ),
					'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['buttons'] => 'gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->start_controls_tabs( 'tabs_switcher_buttons_icon_box_styles' );

			$this->start_controls_tab(
				'tab_switcher_buttons_icon_normal',
				array(
					'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
				)
			);

			$this->add_control(
				'switcher_buttons_normal_icon_color',
				[
					'label'     => __( 'Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['switcher_icon']          => 'color: {{VALUE}}',
						'{{WRAPPER}} ' . $css_scheme['switcher_icon'] . ' svg' => 'fill: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'switcher_buttons_normal_icon_bg_color',
				array(
					'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $css_scheme['switcher_icon'] => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_switcher_buttons_icon_hover',
				array(
					'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
				)
			);

			$this->add_control(
				'switcher_buttons_hover_icon_color',
				[
					'label'     => __( 'Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['switcher_icon_hover']          => 'color: {{VALUE}}',
						'{{WRAPPER}} ' . $css_scheme['switcher_icon_hover'] . ' svg' => 'fill: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'switcher_buttons_hover_icon_bg_color',
				array(
					'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $css_scheme['switcher_icon_hover'] => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'switcher_buttons_hover_icon_border_color',
				[
					'label'     => __( 'Border Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['switcher_icon_hover'] => 'border-color: {{VALUE}}',
					],
					'condition' => [
						'switcher_buttons_normal_icon_border_border!' => '',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_switcher_buttons_icon_active',
				array(
					'label' => esc_html__( 'Active', 'jet-woo-builder' ),
				)
			);

			$this->add_control(
				'switcher_buttons_active_icon_color',
				[
					'label'     => __( 'Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['switcher_icon_active']          => 'color: {{VALUE}}',
						'{{WRAPPER}} ' . $css_scheme['switcher_icon_active'] . ' svg' => 'fill: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'switcher_buttons_active_icon_bg_color',
				[
					'label'     => __( 'Background Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['switcher_icon_active'] => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'switcher_buttons_active_icon_border_color',
				[
					'label'     => __( 'Border Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['switcher_icon_active'] => 'border-color: {{VALUE}}',
					],
					'condition' => [
						'switcher_buttons_normal_icon_border_border!' => '',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'      => 'switcher_buttons_normal_icon_border',
					'label'     => __( 'Border', 'jet-woo-builder' ),
					'separator' => 'before',
					'selector'  => '{{WRAPPER}} ' . $css_scheme['switcher_icon'],
				]
			);

			$this->add_responsive_control(
				'switcher_buttons_normal_icon_border_radius',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Border Radius', 'jet-woo-builder' ),
					'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['switcher_icon'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_section();

		}

	}

	public static function products_loop() {
		if ( jet_woo_builder()->elementor_views->in_elementor() || is_product_taxonomy() || is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) ) {
			if ( woocommerce_product_loop() ) {
				woocommerce_product_loop_start();

				if ( wc_get_loop_prop( 'total' ) ) {
					while ( have_posts() ) {
						the_post();
						do_action( 'woocommerce_shop_loop' );
						wc_get_template_part( 'content', 'product' );
					}
				}

				woocommerce_product_loop_end();
				wp_reset_postdata();
			} else {
				do_action( 'woocommerce_no_products_found' );
			}
		}
	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$switcher_enable = isset( $settings['switcher_enable'] ) && filter_var( $settings['switcher_enable'], FILTER_VALIDATE_BOOLEAN );

		// Add filters before displaying our Widget.
		// Define default archive item template.
		add_filter( 'jet-woo-builder/custom-archive-template', [ $this, 'get_default_custom_template' ] );
		// Define if switcher is active.
		add_filter( 'jet-woo-builder/jet-products-loop/switcher-option-enable', [ $this, 'get_switcher_option_status' ] );

		$this->__open_wrap();

		if ( $switcher_enable && 'products' === woocommerce_get_loop_display_mode() ) {
			include $this->get_template( 'shop/products-loop.php' );
		} else {
			unset( $_COOKIE['jet_woo_builder_layout'] );

			echo '<div class="jet-woo-products-wrapper">';
			self::products_loop();
			echo '</div>';
		}

		$this->__close_wrap();

		// Remove filters after displaying our Widget.
		remove_filter( 'jet-woo-builder/custom-archive-template', [ $this, 'get_default_custom_template' ] );
		remove_filter( 'jet-woo-builder/jet-products-loop/switcher-option-enable', [ $this, 'get_switcher_option_status' ] );

	}

	/**
	 * Default custom template.
	 *
	 * Define default archive item template if admin settings is empty or override if set.
	 *
	 * @since  1.7.0
	 * @access public
	 *
	 * @param string $custom_template Template ID.
	 *
	 * @return mixed
	 */
	public function get_default_custom_template( $custom_template ) {

		if ( 'yes' !== jet_woo_builder_shop_settings()->get( 'custom_archive_page' ) ) {
			return $custom_template;
		}

		if ( 'archive' !== jet_woo_builder()->woocommerce->get_current_loop() ) {
			return $custom_template;
		}

		$settings        = $this->get_settings_for_display();
		$switcher_enable = isset( $settings['switcher_enable'] ) && filter_var( $settings['switcher_enable'], FILTER_VALIDATE_BOOLEAN );
		$main_layout     = $settings['main_layout'] ?? null;
		$archive_item    = $settings['archive_item_layout'] ?? null;

		if ( $switcher_enable && ! empty( $main_layout ) ) {
			$custom_template = $main_layout;
		} elseif ( ! empty( $archive_item ) ) {
			$custom_template = $archive_item;
		} elseif ( 'default' !== jet_woo_builder_shop_settings()->get( 'archive_template' ) ) {
			$custom_template = jet_woo_builder_shop_settings()->get( 'archive_template' );
		} else {
			$custom_template = null;
		}

		return $custom_template;

	}

	/**
	 * Switcher status.
	 *
	 * Define if switcher is active.
	 *
	 * @since  1.7.0
	 * @access public
	 *
	 * @param bool $switcher_enable Switcher availability status.
	 *
	 * @return bool
	 */
	public function get_switcher_option_status( $switcher_enable ) {

		$settings = $this->get_settings_for_display();

		if ( isset( $settings['switcher_enable'] ) && ! empty( $settings['switcher_enable'] ) ) {
			$switcher_enable = filter_var( $settings['switcher_enable'], FILTER_VALIDATE_BOOLEAN );
		}

		return $switcher_enable;

	}

}
