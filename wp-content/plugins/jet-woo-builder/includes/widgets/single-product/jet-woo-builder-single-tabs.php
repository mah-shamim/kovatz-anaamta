<?php
/**
 * Class: Jet_Woo_Builder_Single_Tabs
 * Name: Single Tabs
 * Slug: jet-single-tabs
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

class Jet_Woo_Builder_Single_Tabs extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-single-tabs';
	}

	public function get_title() {
		return __( 'Single Tabs', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-single-tabs';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-and-set-a-single-product-page-template/';
	}

	public function get_categories() {
		return array( 'jet-woo-builder' );
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'single' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-single-tabs/tabs/css-scheme',
			array(
				'control_wrapper'  => '.jet-woo-builder > .jet-single-tabs__wrap ul.wc-tabs',
				'content_wrapper'  => '.jet-woo-builder > .jet-single-tabs__wrap .wc-tab',
				'tabs_list_item'   => '.elementor-jet-single-tabs.jet-woo-builder > .jet-single-tabs__wrap .woocommerce-tabs .tabs > li',
				'tabs_item'        => '.jet-woo-builder > .jet-single-tabs__wrap .tabs > li > a',
				'tabs_item_active' => '.elementor-jet-single-tabs.jet-woo-builder > .jet-single-tabs__wrap .woocommerce-tabs .tabs > li.active',
			)
		);

		$this->start_controls_section(
			'section_single_tabs_style',
			[
				'label' => __( 'Product Tabs', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'single_tabs_position',
			[
				'label'        => __( 'Position', 'jet-woo-builder' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'top',
				'prefix_class' => 'elementor-tabs-view-',
				'options'      => [
					'left'  => __( 'Left', 'jet-woo-builder' ),
					'top'   => __( 'Top', 'jet-woo-builder' ),
					'right' => __( 'Right', 'jet-woo-builder' ),
				],
			]
		);

		$this->add_responsive_control(
			'single_tabs_items_display',
			[
				'label'     => __( 'Display Type', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'row',
				'options'   => jet_woo_builder_tools()->get_available_flex_directions_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['control_wrapper'] => 'flex-direction: {{VALUE}}',
				],
				'condition' => [
					'single_tabs_position' => 'top',
				],
			]
		);

		$this->add_responsive_control(
			'single_tabs_control_wrapper_width',
			[
				'label'      => __( 'Tabs Control Width', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'      => [
					'%'  => [
						'min' => 10,
						'max' => 50,
					],
					'px' => [
						'min' => 100,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['control_wrapper'] => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} ' . $css_scheme['content_wrapper'] => 'width: calc(100% - {{SIZE}}{{UNIT}})',
				],
				'condition'  => [
					'single_tabs_position' => [ 'left', 'right' ],
				],
			]
		);

		$this->add_responsive_control(
			'single_tabs_controls_alignment',
			[
				'label'        => __( 'Alignment', 'jet-woo-builder' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'flex-start' => [
						'title' => __( 'Start', 'jet-woo-builder' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					],
					'center'     => [
						'title' => __( 'Center', 'jet-woo-builder' ),
						'icon'  => 'eicon-text-align-center',
					],
					'stretch'    => [
						'title' => __( 'Stretch', 'jet-woo-builder' ),
						'icon'  => 'eicon-text-align-justify',
					],
					'flex-end'   => [
						'title' => __( 'End', 'jet-woo-builder' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					],
				],
				'separator'    => 'after',
				'selectors'    => [
					'{{WRAPPER}} ' . $css_scheme['control_wrapper'] => 'justify-content: {{VALUE}}; align-items: {{VALUE}};',
				],
				'prefix_class' => 'elementor-tabs-controls-',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'single_tabs_control_background',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ],
				'selector' => '{{WRAPPER}} ' . $css_scheme['control_wrapper'],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'single_tabs_control_border',
				'label'    => __( 'Border', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['control_wrapper'],
			]
		);

		$this->add_responsive_control(
			'single_tabs_control_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['control_wrapper'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'single_tabs_control_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['control_wrapper'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_single_tabs_item_style',
			[
				'label' => __( 'Tabs', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'single_tabs_item_width',
			[
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'      => [
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 100,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['tabs_list_item'] => 'max-width: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'single_tabs_position'      => 'top',
					'single_tabs_items_display' => 'column',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'single_tabs_item_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['tabs_item'],
			]
		);

		$this->start_controls_tabs( 'single_tabs_item_styles' );

		$this->start_controls_tab(
			'single_tabs_item_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'single_tabs_item_color_normal',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['tabs_item'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'single_tabs_item_background_normal',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['tabs_list_item'] => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'single_tabs_item_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'single_tabs_item_color_hover',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['tabs_list_item'] . ':hover a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'single_tabs_item_background_hover',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['tabs_list_item'] . ':hover' => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$this->add_control(
			'single_tabs_item_border_color_hover',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['tabs_list_item'] . ':hover' => 'border-color: {{VALUE}} !important',
				],
				'condition' => [
					'single_tabs_item_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'single_tabs_item_box_shadow_hover',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['tabs_list_item'] . ':hover',
				'condition' => [
					'single_tabs_item_box_shadow_normal_box_shadow_type!' => '',
				],
			]
		);

		$this->add_control(
			'single_tabs_item_decoration_hover',
			array(
				'label'     => esc_html__( 'Text Decoration', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => jet_woo_builder_tools()->get_available_text_decoration_types(),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['tabs_list_item'] . ':hover a' => 'text-decoration: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'single_tabs_item_active',
			array(
				'label' => esc_html__( 'Active', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'single_tabs_item_color_active',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['tabs_item_active'] . ' a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'single_tabs_item_background_active',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['tabs_item_active'] => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$this->add_control(
			'single_tabs_item_border_color_active',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['tabs_item_active'] => 'border-color: {{VALUE}} !important',
				],
				'condition' => [
					'single_tabs_item_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'single_tabs_item_box_shadow_active',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['tabs_item_active'],
				'condition' => [
					'single_tabs_item_box_shadow_normal_box_shadow_type!' => '',
				],
			]
		);

		$this->add_control(
			'single_tabs_item_decoration_active',
			array(
				'label'     => esc_html__( 'Text Decoration', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => jet_woo_builder_tools()->get_available_text_decoration_types(),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['tabs_item_active'] . ' a' => 'text-decoration: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'single_tabs_item_border',
				'label'          => __( 'Border', 'jet-woo-builder' ),
				'separator'      => 'before',
				'selector'       => '{{WRAPPER}} ' . $css_scheme['tabs_list_item'],
				'fields_options' => [
					'border' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-style: {{VALUE}} !important;',
						],
					],
					'width'  => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
						],
					],
					'color'  => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-color: {{VALUE}} !important;',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'single_tabs_item_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['tabs_list_item'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'single_tabs_item_box_shadow_normal',
				'selector' => '{{WRAPPER}} ' . $css_scheme['tabs_list_item'],
			)
		);

		$this->add_responsive_control(
			'single_tabs_item_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['tabs_list_item'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'single_tabs_item_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['tabs_item'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'single_tabs_item_alignment',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['tabs_list_item'] => 'text-align: {{VALUE}};',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_single_tabs_content_style',
			[
				'label' => __( 'Panel', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'panel_typography',
				'selector' => '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel',
			]
		);

		$this->add_control(
			'panel_text_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} .woocommerce-Tabs-panel' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'heading_panel_heading_style',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Heading', 'jet-woo-builder' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'panel_heading_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-Tabs-panel h2' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'panel_heading_typography',
				'selector' => '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel h2',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'single_tabs_content_background',
				'types'     => [ 'classic', 'gradient' ],
				'exclude'   => [ 'image' ],
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['content_wrapper'],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'single_tabs_content_border',
				'label'    => __( 'Border', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['content_wrapper'],
			]
		);

		$this->add_responsive_control(
			'single_tabs_content_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['content_wrapper'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'single_tabs_content_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['content_wrapper'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		global $product;

		if ( ! is_a( $product, 'WC_Product' ) ) {
			return;
		}

		if ( $this->__set_editor_product() ) {
			$this->__open_wrap();

			// Add filters before displaying our Widget.
			add_filter( 'comments_template', [ $this, 'comments_template_loader' ] );

			include $this->get_template( 'single-product/tabs.php' );

			// Remove filters after displaying our Widget.
			remove_filter( 'comments_template', [ $this, 'comments_template_loader' ] );

			$this->__close_wrap();

			if ( jet_woo_builder()->elementor_views->in_elementor() ) {
				$this->__reset_editor_product();
			}
		}

	}

	/**
	 * Template loader.
	 *
	 * Load comments template.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @return string
	 */
	public function comments_template_loader( $template ) {

		if ( ! jet_woo_builder()->elementor_views->in_elementor() && ! wp_doing_ajax() ) {
			return $template;
		}

		if ( 'product' !== get_post_type() ) {
			return $template;
		}

		$check_dirs = [
			trailingslashit( get_stylesheet_directory() ) . WC()->template_path(),
			trailingslashit( get_template_directory() ) . WC()->template_path(),
			trailingslashit( get_stylesheet_directory() ),
			trailingslashit( get_template_directory() ),
			trailingslashit( WC()->plugin_path() ) . 'templates/',
		];

		if ( WC_TEMPLATE_DEBUG_MODE ) {
			$check_dirs = [ array_pop( $check_dirs ) ];
		}

		foreach ( $check_dirs as $dir ) {
			if ( file_exists( trailingslashit( $dir ) . 'single-product-reviews.php' ) ) {
				return trailingslashit( $dir ) . 'single-product-reviews.php';
			}
		}

		return $template;

	}

}
