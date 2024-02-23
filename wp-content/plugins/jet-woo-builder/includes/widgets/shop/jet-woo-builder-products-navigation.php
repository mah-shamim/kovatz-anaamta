<?php
/**
 * Class: Jet_Woo_Builder_Products_Navigation
 * Name: Products Navigation
 * Slug: jet-woo-builder-products-navigation
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

class Jet_Woo_Builder_Products_Navigation extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-builder-products-navigation';
	}

	public function get_title() {
		return __( 'Products Navigation', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-shop-navigation';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-and-set-a-shop-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'shop' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			[
				'label' => __( 'Products Navigation', 'jet-woo-builder' ),
			]
		);
		$this->add_control(
			'info_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Works only with main Query object.', 'jet-woo-builder' ),
				'content_classes' => 'elementor-descriptor elementor-panel-alert elementor-panel-alert-info',
			)
		);
		$this->add_control(
			'prev_text',
			[
				'label'   => __( 'Previous Label', 'jet-woo-builder' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Previous', 'jet-woo-builder' ),
			]
		);
		$this->__add_advanced_icon_control(
			'prev_icon',
			[
				'label'       => __( 'Previous Icon', 'jet-woo-builder' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => true,
				'file'        => '',
				'default'     => 'fa fa-angle-left',
				'fa5_default' => [
					'value'   => 'fas fa-angle-left',
					'library' => 'fa-solid',
				],
			]
		);
		$this->add_control(
			'next_text',
			[
				'label'   => __( 'Next Label', 'jet-woo-builder' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Next', 'jet-woo-builder' ),
			]
		);

		$this->__add_advanced_icon_control(
			'next_icon',
			[
				'label'       => __( 'Next Icon', 'jet-woo-builder' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => true,
				'file'        => '',
				'default'     => 'fa fa-angle-right',
				'fa5_default' => [
					'value'   => 'fas fa-angle-right',
					'library' => 'fa-solid',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'items_style',
			[
				'label' => __( 'Products Navigation', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'items_min_width',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Link Min Width', 'jet-woo-builder' ),
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 20,
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'items_typography',
				'selector' => '{{WRAPPER}} .jet-woo-builder-shop-navigation > a',
			]
		);

		$this->start_controls_tabs( 'tabs_items_style' );

		$this->start_controls_tab(
			'items_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'items_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a' => 'color: {{VALUE}}',

				],
			]
		);

		$this->add_control(
			'items_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'items_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'items_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'items_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'items_hover_border_color',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'items_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'items_border',
				'separator'  => 'before',
				'selector'    => '{{WRAPPER}} .jet-woo-builder-shop-navigation > a',
			]
		);

		$this->add_responsive_control(
			'items_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'items_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'items_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'items_alignment',
			array(
				'label'        => esc_html__( 'Alignment', 'jet-woo-builder' ),
				'type'         => Controls_Manager::CHOOSE,
				'default'      => 'flex-start',
				'options'      => jet_woo_builder_tools()->get_available_flex_h_align_types( true ),
				'prefix_class' => 'jet-woo-builder-shop-navigation-',
				'selectors'    => array(
					'{{WRAPPER}} .jet-woo-builder-shop-navigation' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'icons_style',
			[
				'label' => __( 'Icon', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'items_icon_size',
			[
				'label'      => __( 'Size', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a .jet-woo-builder-shop-navigation__arrow' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icons_box_size',
			[
				'label'      => __( 'Box Size', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 150,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a .jet-woo-builder-shop-navigation__arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'items_icon_gap',
			[
				'label'      => __( 'Gap', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a .jet-woo-builder-shop-navigation__arrow.jet-arrow-prev' => ! is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a .jet-woo-builder-shop-navigation__arrow.jet-arrow-next' => ! is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_icons_style' );

		$this->start_controls_tab(
			'icons_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'icons_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-navigation .jet-woo-builder-shop-navigation__arrow' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icons_bg_color',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-navigation .jet-woo-builder-shop-navigation__arrow' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icons_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'icons_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a:hover .jet-woo-builder-shop-navigation__arrow' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icons_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a:hover .jet-woo-builder-shop-navigation__arrow' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'icons_hover_border_color',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-navigation > a:hover .jet-woo-builder-shop-navigation__arrow' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'items_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'icons_border',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .jet-woo-builder-shop-navigation .jet-woo-builder-shop-navigation__arrow',
			]
		);

		$this->add_responsive_control(
			'icons_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} .jet-woo-builder-shop-navigation .jet-woo-builder-shop-navigation__arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$prev_text = $settings['prev_text'] ?? '';
		$next_text = $settings['next_text'] ?? '';
		$prev_icon = $this->__render_icon( 'prev_icon', '%s', '', false );
		$next_icon = $this->__render_icon( 'next_icon', '%s', '', false );

		if ( ! empty( $prev_icon ) ) {
			$prev_text = $this->get_navigation_arrow( 'prev', $prev_icon ) . esc_html__( $prev_text, 'jet-woo-builder' );
		}
		if ( ! empty( $next_icon ) ) {
			$next_text = esc_html__( $next_text, 'jet-woo-builder' ) . $this->get_navigation_arrow( 'next', $next_icon );
		}

		$this->__open_wrap();

		echo '<div class="jet-woo-builder-shop-navigation">';

		posts_nav_link( ' ', $prev_text, $next_text );

		echo '</div>';

		$this->__close_wrap();

	}

	/**
	 * Navigation arrow.
	 *
	 * Return html for arrows in navigation.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @param string $type Navigation label.
	 * @param string $icon Navigation icon.
	 *
	 * @return string
	 */
	public function get_navigation_arrow( $type = 'next', $icon = '' ) {

		$format = apply_filters( 'jet-woo-builder/shop-navigation/arrows-format', '<span class="jet-arrow-%s jet-woo-builder-shop-navigation__arrow jet-woo-builder-icon">%s</span>' );

		return sprintf( $format, $type, $icon );

	}

}
