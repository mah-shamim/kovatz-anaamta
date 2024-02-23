<?php
/**
 * Class: Jet_Woo_Builder_Products_Pagination
 * Name: Products Pagination
 * Slug: jet-woo-builder-products-pagination
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

class Jet_Woo_Builder_Products_Pagination extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-builder-products-pagination';
	}

	public function get_title() {
		return esc_html__( 'Products Pagination', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-shop-pagination';
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
				'label' => __( 'Products Pagination', 'jet-woo-builder' ),
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
			'prev_next',
			[
				'label'   => esc_html__( 'Enable Prev & Next Pages Links.', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'prev_text',
			[
				'label'     => __( 'Previous Label', 'jet-woo-builder' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Previous', 'jet-woo-builder' ),
				'condition' => [
					'prev_next' => 'yes',
				],
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
				'condition'   => [
					'prev_next' => 'yes',
				],
			]
		);

		$this->add_control(
			'next_text',
			[
				'label'     => __( 'Next Label', 'jet-woo-builder' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Next', 'jet-woo-builder' ),
				'condition' => [
					'prev_next' => 'yes',
				],
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
				'condition'   => [
					'prev_next' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'items_style',
			[
				'label' => __( 'Products Pagination', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'items_typography',
				'scheme'   => Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .jet-woo-builder-shop-pagination .page-numbers',
				'exclude'  => array(
					'text_decoration',
				),
			)
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
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'items_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers' => 'background-color: {{VALUE}}',
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
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'items_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'items_hover_border_color',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'items_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'items_active',
			array(
				'label' => esc_html__( 'Current', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'items_color_active',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination span.page-numbers.current' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'items_bg_color_active',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-woo-builder-shop-pagination span.page-numbers.current' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'items_active_border_color',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination span.page-numbers.current' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'items_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'items_min_width',
			[
				'label'      => __( 'Item Min Width', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
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
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination .page-numbers' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'items_margin',
			[
				'label'       => __( 'Gap', 'jet-woo-builder' ),
				'label_block' => true,
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px' ],
				'default'     => [
					'unit' => 'px',
					'size' => 5,
				],
				'range'       => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination .page-numbers' => 'margin-left: calc( {{SIZE}}px / 2 ); margin-right: calc( {{SIZE}}px / 2 );',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'items_border',
				'label'       => esc_html__( 'Border', 'jet-woo-builder' ),
				'placeholder' => '1px',
				'selector'    => '{{WRAPPER}} .jet-woo-builder-shop-pagination .page-numbers:not(.dots)',
			)
		);

		$this->add_responsive_control(
			'items_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination .page-numbers' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .jet-woo-builder-shop-pagination .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'items_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
				'options'   => jet_woo_builder_tools()->get_available_flex_h_align_types( true ),
				'selectors' => array(
					'{{WRAPPER}} .jet-woo-builder-shop-pagination' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'prev_next_style',
			[
				'label' => __( 'Navigation', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'prev_next_min_width',
			[
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
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
					'{{WRAPPER}} .jet-woo-builder-shop-pagination .page-numbers.prev' => 'min-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jet-woo-builder-shop-pagination .page-numbers.next' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_prev_next_style' );

		$this->start_controls_tab(
			'prev_next_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'prev_next_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers.prev' => 'color: {{VALUE}}',
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers.next' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'prev_next_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers.prev' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers.next' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'prev_next_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'prev_next_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers.next:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers.prev:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'prev_next_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers.prev:hover' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers.next:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'prev_next_hover_border_color',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers.prev:hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers.next:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'prev_next_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'prev_next_border',
				'separator'  => 'before',
				'selector'    => '{{WRAPPER}} .jet-woo-builder-shop-pagination .page-numbers.prev,' . '{{WRAPPER}} .jet-woo-builder-shop-pagination .page-numbers.next',
			]
		);

		$this->add_responsive_control(
			'prev_next_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination .page-numbers.prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .jet-woo-builder-shop-pagination .page-numbers.next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'prev_next_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination .page-numbers.prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .jet-woo-builder-shop-pagination .page-numbers.next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'icons_style',
			[
				'label' => __( 'Navigation Icons', 'jet-woo-builder' ),
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
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers .jet-woo-builder-shop-pagination__arrow' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icons_box_size',
			[
				'label'      => __( 'Box Size', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 18,
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers .jet-woo-builder-shop-pagination__arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers .jet-woo-builder-shop-pagination__arrow.jet-arrow-prev' => ! is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers .jet-woo-builder-shop-pagination__arrow.jet-arrow-next' => ! is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
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
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination .jet-woo-builder-shop-pagination__arrow' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icons_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-woo-builder-shop-pagination .jet-woo-builder-shop-pagination__arrow' => 'background-color: {{VALUE}}',
				),
			)
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
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers:hover .jet-woo-builder-shop-pagination__arrow' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icons_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers:hover .jet-woo-builder-shop-pagination__arrow' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'icons_hover_border_color',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination a.page-numbers:hover .jet-woo-builder-shop-pagination__arrow' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'icons_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'icons_border',
				'label'     => __( 'Border', 'jet-woo-builder' ),
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .jet-woo-builder-shop-pagination .jet-woo-builder-shop-pagination__arrow',
			]
		);

		$this->add_responsive_control(
			'icons_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} .jet-woo-builder-shop-pagination .jet-woo-builder-shop-pagination__arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		if ( ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) {
			return false;
		}

		$settings = $this->get_settings_for_display();

		$prev_next = isset( $settings['prev_next'] ) ? filter_var( $settings['prev_next'], FILTER_VALIDATE_BOOLEAN ) : false;
		$prev_text = isset( $settings['prev_text'] ) ? $settings['prev_text'] : '';
		$next_text = isset( $settings['next_text'] ) ? $settings['next_text'] : '';
		$prev_icon = $this->__render_icon( 'prev_icon', '%s', '', false );
		$next_icon = $this->__render_icon( 'next_icon', '%s', '', false );
		$total     = wc_get_loop_prop( 'total_pages' );
		$current   = wc_get_loop_prop( 'current_page' );
		$base      = esc_url_raw( add_query_arg( 'product-page', '%#%', false ) );
		$format    = esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );

		if ( ! wc_get_loop_prop( 'is_shortcode' ) ) {
			$format = '';
			$base   = esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
		}

		if ( $total <= 1 ) {
			return false;
		}

		$this->__open_wrap();

		if ( ! empty( $prev_icon ) ) {
			$prev_text = $this->get_pagination_arrow( 'prev', $prev_icon ) . esc_html__( $prev_text, 'jet-woo-builder' );
		}
		if ( ! empty( $next_icon ) ) {
			$next_text = esc_html__( $next_text, 'jet-woo-builder' ) . $this->get_pagination_arrow( 'next', $next_icon );
		}

		echo '<nav class="jet-woo-builder-shop-pagination">';
		echo paginate_links(
			array(
				'base'      => $base,
				'format'    => $format,
				'prev_next' => $prev_next,
				'prev_text' => $prev_text,
				'next_text' => $next_text,
				'current'   => max( 1, $current ),
				'total'     => $total,
				'type'      => 'plain',
				'end_size'  => 3,
				'mid_size'  => 3,
			) );
		echo '</nav>';

		$this->__close_wrap();

	}

	/**
	 * Pagination arrow.
	 *
	 * Return html for arrows in pagination.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @param string $type Navigation label.
	 * @param string $icon Navigation icon.
	 *
	 * @return string
	 */
	public function get_pagination_arrow( $type = 'next', $icon = '' ) {

		$format = apply_filters( 'jet-woo-builder/shop-pagination/arrows-format', '<span class="jet-arrow-%s jet-woo-builder-shop-pagination__arrow jet-woo-builder-icon">%s</span>' );

		return sprintf( $format, $type, $icon );

	}

}
