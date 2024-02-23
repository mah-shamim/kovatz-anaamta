<?php
/**
 * JetWooBuilder Elementor common controls class
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Common_Controls' ) ) {

	/**
	 * Define Jet_Woo_Builder_Parser class
	 */
	class Jet_Woo_Builder_Common_Controls {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.7.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Init common controls.
		 */
		public function __construct() {
		}

		/**
		 * Register WooCommerce style warning message
		 *
		 * @param $obj
		 */
		public function register_wc_style_warning( $obj ) {
			$obj->add_control(
				'wc_style_warning',
				[
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'The style and view of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'jet-woo-builder' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);
		}

		/**
		 * Register carousel controls.
		 *
		 * @param $obj
		 */
		public function register_carousel_controls( $obj ) {

			$obj->add_control(
				'carousel_enabled',
				[
					'label' => __( 'Enable Carousel', 'jet-woo-builder' ),
					'type'  => Controls_Manager::SWITCHER,
				]
			);

			$obj->add_control(
				'carousel_direction',
				[
					'label'     => __( 'Direction', 'jet-woo-builder' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'horizontal',
					'options'   => jet_woo_builder_tools()->get_available_direction_types(),
					'separator' => 'before',
					'condition' => [
						'carousel_enabled!' => '',
					],
				]
			);

			$obj->add_responsive_control(
				'slides_min_height',
				[
					'label'       => __( 'Slides Min Height', 'jet-woo-builder' ),
					'type'        => Controls_Manager::NUMBER,
					'render_type' => 'template',
					'selectors'   => [
						'{{WRAPPER}} ' . '.jet-woo-carousel div[ class *= "__inner-box" ]' => 'min-height: {{VALUE}}px;',
					],
					'condition'   => [
						'carousel_enabled!'  => '',
						'carousel_direction' => 'horizontal',
					],
				]
			);

			$obj->add_responsive_control(
				'carousel_height',
				[
					'label'       => __( 'Carousel Height', 'jet-woo-builder' ),
					'type'        => Controls_Manager::NUMBER,
					'default'     => '1500',
					'render_type' => 'template',
					'selectors'   => [
						'{{WRAPPER}} .jet-woo-carousel.vertical' => 'height: {{VALUE}}px;',
					],
					'condition'   => [
						'carousel_enabled!'  => '',
						'carousel_direction' => 'vertical',
					],
				]
			);

			$obj->add_responsive_control(
				'slides_to_scroll',
				[
					'label'              => __( 'Slides to Scroll', 'jet-woo-builder' ),
					'type'               => Controls_Manager::SELECT,
					'default'            => '1',
					'options'            => jet_woo_builder_tools()->get_select_range( 12 ),
					'frontend_available' => true,
					'condition'          => [
						'carousel_enabled!' => '',
						'columns!'          => '1',
					],
				]
			);

			$obj->add_responsive_control(
				'space_between_slides',
				[
					'type'               => Controls_Manager::NUMBER,
					'label'              => __( 'Space Between', 'jet-woo-builder' ),
					'default'            => 10,
					'frontend_available' => true,
					'render_type'        => 'template',
					'selectors'          => [
						'{{WRAPPER}} .jet-woo-carousel .swiper-slide' => '--space-between: {{VALUE}}px',
					],
					'condition'          => [
						'carousel_enabled!' => '',
					],
				]
			);

			$obj->add_control(
				'slides_overflow_enabled',
				[
					'label'              => __( 'Slide Overflow', 'jet-woo-builder' ),
					'type'               => Controls_Manager::SWITCHER,
					'frontend_available' => true,
					'condition'          => [
						'carousel_enabled!' => '',
					],
				]
			);

			$obj->add_responsive_control(
				'slides_overflow',
				[
					'type'               => Controls_Manager::NUMBER,
					'label'              => __( 'Overflow', 'jet-woo-builder' ),
					'min'                => 0,
					'max'                => 0.7,
					'step'               => 0.1,
					'default'            => 0.5,
					'frontend_available' => true,
					'separator'          => 'after',
					'selectors'          => [
						'{{WRAPPER}} .jet-woo-carousel .swiper-slide' => '--slides-overflow: {{VALUE}}',
					],
					'condition'          => [
						'carousel_enabled!'       => '',
						'slides_overflow_enabled' => 'yes',
					],
				]
			);

			$obj->add_control(
				'arrows',
				[
					'label'     => __( 'Navigation', 'jet-woo-builder' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'condition' => [
						'carousel_enabled!' => '',
					],
				]
			);

			$obj->__add_advanced_icon_control(
				'prev_arrow',
				[
					'label'       => __( 'Prev Arrow', 'jet-woo-builder' ),
					'type'        => Controls_Manager::ICON,
					'label_block' => true,
					'file'        => '',
					'default'     => ! is_rtl() ? 'fa fa-angle-left' : 'fa fa-angle-right',
					'fa5_default' => [
						'value'   => ! is_rtl() ? 'fas fa-angle-left' : 'fas fa-angle-right',
						'library' => 'fa-solid',
					],
					'condition'   => [
						'carousel_enabled!' => '',
						'arrows!'           => '',
					],
				]
			);

			$obj->__add_advanced_icon_control(
				'next_arrow',
				[
					'label'       => __( 'Next Arrow', 'jet-woo-builder' ),
					'type'        => Controls_Manager::ICON,
					'label_block' => true,
					'file'        => '',
					'default'     => ! is_rtl() ? 'fa fa-angle-right' : 'fa fa-angle-left',
					'fa5_default' => [
						'value'   => ! is_rtl() ? 'fas fa-angle-right' : 'fas fa-angle-left',
						'library' => 'fa-solid',
					],
					'separator'   => 'after',
					'condition'   => [
						'carousel_enabled!' => '',
						'arrows!'           => '',
					],
				]
			);

			$obj->add_control(
				'dots',
				[
					'label'        => __( 'Pagination', 'jet-woo-builder' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => [
						'carousel_enabled!' => '',
					],
				]
			);

			$obj->add_control(
				'dynamic_bullets',
				[
					'label'     => __( 'Dynamic Bullets', 'jet-woo-builder' ),
					'type'      => Controls_Manager::SWITCHER,
					'separator' => 'after',
					'condition' => [
						'carousel_enabled!' => '',
						'dots!'             => '',
					],
				]
			);

			$obj->add_control(
				'autoplay',
				[
					'label'     => __( 'Autoplay', 'jet-woo-builder' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'condition' => [
						'carousel_enabled!' => '',
					],
				]
			);

			$obj->add_control(
				'autoplay_speed',
				[
					'label'     => __( 'Delay', 'jet-woo-builder' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 5000,
					'condition' => [
						'carousel_enabled!' => '',
						'autoplay!'         => '',
					],
				]
			);

			$obj->add_control(
				'pause_on_interactions',
				[
					'label'     => __( 'Disable on Interaction', 'jet-woo-builder' ),
					'type'      => Controls_Manager::SWITCHER,
					'separator' => 'after',
					'condition' => [
						'carousel_enabled!' => '',
						'autoplay!'         => '',
					],
				]
			);

			$obj->add_control(
				'infinite',
				[
					'label'     => __( 'Loop', 'jet-woo-builder' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'condition' => [
						'carousel_enabled!' => '',
					],
				]
			);

			$obj->add_control(
				'speed',
				[
					'label'     => __( 'Animation Speed', 'jet-woo-builder' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 500,
					'condition' => [
						'carousel_enabled!' => '',
					],
				]
			);

			$obj->add_control(
				'simulate_touch',
				[
					'label'     => __( 'Simulate Touch', 'jet-woo-builder' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'condition' => [
						'carousel_enabled!' => '',
					],
				]
			);

			$obj->add_control(
				'freemode',
				[
					'label'     => __( 'Free Mode', 'jet-woo-builder' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => [
						'carousel_enabled!' => '',
					],
				]
			);

			$obj->add_control(
				'freemode_velocity',
				[
					'type'      => Controls_Manager::NUMBER,
					'label'     => __( 'Velocity', 'jet-woo-builder' ),
					'min'       => 0,
					'max'       => 1,
					'step'      => 0.01,
					'default'   => 0.02,
					'separator' => 'after',
					'condition' => [
						'carousel_enabled!' => '',
						'freemode!'         => '',
					],
				]
			);

			$obj->add_control(
				'centered',
				[
					'label'     => __( 'Centered Slides', 'jet-woo-builder' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => '',
					'condition' => [
						'carousel_enabled!' => '',
					],
				]
			);

			$obj->add_control(
				'effect',
				[
					'label'     => __( 'Effect', 'jet-woo-builder' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'slide',
					'options'   => [
						'slide' => __( 'Slide', 'jet-woo-builder' ),
						'fade'  => __( 'Fade', 'jet-woo-builder' ),
					],
					'condition' => [
						'carousel_enabled!' => '',
						'columns'           => '1',
					],
				]
			);

		}

		/**
		 * Register carousel navigation style controls.
		 *
		 * @param $obj
		 */
		public function register_carousel_navigation_style_controls( $obj ) {

			$obj->start_controls_tabs( 'tabs_arrows_style' );

			$obj->start_controls_tab(
				'tab_arrow_normal',
				[
					'label' => __( 'Normal', 'jet-woo-builder' ),
				]
			);

			$obj->add_group_control(
				\Jet_Woo_Group_Control_Box_Style::get_type(),
				[
					'name'     => 'arrows_style',
					'selector' => '{{WRAPPER}} .jet-woo-carousel .jet-arrow',
				]
			);

			$obj->end_controls_tab();

			$obj->start_controls_tab(
				'tab_arrows_hover',
				[
					'label' => __( 'Hover', 'jet-woo-builder' ),
				]
			);

			$obj->add_group_control(
				\Jet_Woo_Group_Control_Box_Style::get_type(),
				[
					'name'     => 'arrows_hover_style',
					'selector' => '{{WRAPPER}} .jet-woo-carousel .jet-arrow:hover',
				]
			);

			$obj->end_controls_tab();

			$obj->start_controls_tab(
				'tab_arrows_disabled',
				[
					'label' => __( 'Disabled', 'jet-woo-builder' ),
				]
			);

			$obj->add_group_control(
				\Jet_Woo_Group_Control_Box_Style::get_type(),
				[
					'name'     => 'arrows_disabled_style',
					'selector' => '{{WRAPPER}} .jet-woo-carousel .jet-arrow.swiper-button-disabled',
				]
			);

			$obj->end_controls_tab();

			$obj->end_controls_tabs();

			$obj->add_control(
				'prev_arrow_position',
				[
					'label'     => __( 'Prev Arrow Position', 'jet-woo-builder' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$obj->add_control(
				'prev_vert_position',
				[
					'label'   => __( 'Vertical Position', 'jet-woo-builder' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'top',
					'options' => [
						'top'    => __( 'Top', 'jet-woo-builder' ),
						'bottom' => __( 'Bottom', 'jet-woo-builder' ),
					],
				]
			);

			$obj->add_responsive_control(
				'prev_top_position',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Top Indent', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
					'condition'  => [
						'prev_vert_position' => 'top',
					],
					'selectors'  => [
						'{{WRAPPER}} .jet-woo-carousel .jet-arrow.prev-arrow' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
					],
				]
			);

			$obj->add_responsive_control(
				'prev_bottom_position',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Bottom Indent', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
					'condition'  => [
						'prev_vert_position' => 'bottom',
					],
					'selectors'  => [
						'{{WRAPPER}} .jet-woo-carousel .jet-arrow.prev-arrow' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
					],
				]
			);

			$obj->add_control(
				'prev_hor_position',
				[
					'label'   => __( 'Horizontal Position', 'jet-woo-builder' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'left',
					'options' => [
						'left'  => __( 'Left', 'jet-woo-builder' ),
						'right' => __( 'Right', 'jet-woo-builder' ),
					],
				]
			);

			$obj->add_responsive_control(
				'prev_left_position',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Left Indent', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
					'condition'  => [
						'prev_hor_position' => 'left',
					],
					'selectors'  => [
						'{{WRAPPER}} .jet-woo-carousel .jet-arrow.prev-arrow' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
					],
				]
			);

			$obj->add_responsive_control(
				'prev_right_position',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Right Indent', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
					'condition'  => [
						'prev_hor_position' => 'right',
					],
					'selectors'  => [
						'{{WRAPPER}} .jet-woo-carousel .jet-arrow.prev-arrow' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
					],
				]
			);

			$obj->add_control(
				'enable_specific_prev_arrow_borders',
				[
					'label' => __( 'Specific Prev Arrow Styles', 'jet-woo-builder' ),
					'type'  => Controls_Manager::SWITCHER,
				]
			);

			$obj->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'      => 'prev_arrow_border',
					'label'     => __( 'Border', 'jet-woo-builder' ),
					'selector'  => '{{WRAPPER}} .jet-woo-carousel .jet-arrow.prev-arrow',
					'condition' => [
						'enable_specific_prev_arrow_borders' => 'yes',
					],
				]
			);

			$obj->add_responsive_control(
				'prev_arrow_border_radius',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Border Radius', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} .jet-woo-carousel .jet-arrow.prev-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'  => [
						'enable_specific_prev_arrow_borders' => 'yes',
					],
				]
			);

			$obj->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'      => 'prev_arrow_box_shadow',
					'selector'  => '{{WRAPPER}} .jet-woo-carousel .jet-arrow.prev-arrow',
					'condition' => [
						'enable_specific_prev_arrow_borders' => 'yes',
					],
				]
			);

			$obj->add_control(
				'next_arrow_position',
				[
					'label'     => __( 'Next Arrow Position', 'jet-woo-builder' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$obj->add_control(
				'next_vert_position',
				[
					'label'   => __( 'Vertical Position', 'jet-woo-builder' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'top',
					'options' => [
						'top'    => __( 'Top', 'jet-woo-builder' ),
						'bottom' => __( 'Bottom', 'jet-woo-builder' ),
					],
				]
			);

			$obj->add_responsive_control(
				'next_top_position',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Top Indent', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
					'condition'  => [
						'next_vert_position' => 'top',
					],
					'selectors'  => [
						'{{WRAPPER}} .jet-woo-carousel .jet-arrow.next-arrow' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
					],
				]
			);

			$obj->add_responsive_control(
				'next_bottom_position',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Bottom Indent', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
					'condition'  => [
						'next_vert_position' => 'bottom',
					],
					'selectors'  => [
						'{{WRAPPER}} .jet-woo-carousel .jet-arrow.next-arrow' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
					],
				]
			);

			$obj->add_control(
				'next_hor_position',
				[
					'label'   => __( 'Horizontal Position', 'jet-woo-builder' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'right',
					'options' => [
						'left'  => __( 'Left', 'jet-woo-builder' ),
						'right' => __( 'Right', 'jet-woo-builder' ),
					],
				]
			);

			$obj->add_responsive_control(
				'next_left_position',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Left Indent', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
					'condition'  => [
						'next_hor_position' => 'left',
					],
					'selectors'  => [
						'{{WRAPPER}} .jet-woo-carousel .jet-arrow.next-arrow' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
					],
				]
			);

			$obj->add_responsive_control(
				'next_right_position',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Right Indent', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
					'condition'  => [
						'next_hor_position' => 'right',
					],
					'selectors'  => [
						'{{WRAPPER}} .jet-woo-carousel .jet-arrow.next-arrow' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
					],
				]
			);

			$obj->add_control(
				'enable_specific_next_arrow_borders',
				[
					'label' => __( 'Specific Next Arrow Styles', 'jet-woo-builder' ),
					'type'  => Controls_Manager::SWITCHER,
				]
			);

			$obj->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'      => 'next_arrow_border',
					'label'     => __( 'Border', 'jet-woo-builder' ),
					'selector'  => '{{WRAPPER}} .jet-woo-carousel .jet-arrow.next-arrow',
					'condition' => [
						'enable_specific_next_arrow_borders' => 'yes',
					],
				]
			);

			$obj->add_responsive_control(
				'next_arrow_border_radius',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Border Radius', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} .jet-woo-carousel .jet-arrow.next-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'  => [
						'enable_specific_next_arrow_borders' => 'yes',
					],
				]
			);

			$obj->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'      => 'next_arrow_box_shadow',
					'selector'  => '{{WRAPPER}} .jet-woo-carousel .jet-arrow.next-arrow',
					'condition' => [
						'enable_specific_next_arrow_borders' => 'yes',
					],
				]
			);

		}

		/**
		 * Register carousel pagination style controls.
		 *
		 * @param $obj
		 */
		public function register_carousel_pagination_style_controls( $obj ) {

			$obj->start_controls_tabs( 'tabs_dots_style' );

			$obj->start_controls_tab(
				'tab_dots_normal',
				[
					'label' => __( 'Normal', 'jet-woo-builder' ),
				]
			);

			$obj->add_group_control(
				\Jet_Woo_Group_Control_Box_Style::get_type(),
				[
					'name'     => 'dots_style',
					'selector' => '{{WRAPPER}} .jet-woo-carousel .swiper-pagination .swiper-pagination-bullet',
				]
			);

			$obj->end_controls_tab();

			$obj->start_controls_tab(
				'tab_dots_hover',
				[
					'label' => __( 'Hover', 'jet-woo-builder' ),
				]
			);

			$obj->add_group_control(
				\Jet_Woo_Group_Control_Box_Style::get_type(),
				[
					'name'     => 'dots_style_hover',
					'selector' => '{{WRAPPER}} .jet-woo-carousel .swiper-pagination .swiper-pagination-bullet:hover',
				]
			);

			$obj->end_controls_tab();

			$obj->start_controls_tab(
				'tab_dots_active',
				[
					'label' => __( 'Active', 'jet-woo-builder' ),
				]
			);

			$obj->add_group_control(
				\Jet_Woo_Group_Control_Box_Style::get_type(),
				[
					'name'     => 'dots_style_active',
					'selector' => '{{WRAPPER}} .jet-woo-carousel .swiper-pagination .swiper-pagination-bullet-active',
				]
			);

			$obj->end_controls_tab();

			$obj->end_controls_tabs();

			$obj->add_control(
				'dots_gap',
				[
					'label'     => __( 'Gap', 'jet-woo-builder' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => [
						'size' => 5,
						'unit' => 'px',
					],
					'range'     => [
						'px' => [
							'min' => 0,
							'max' => 50,
						],
					],
					'separator' => 'before',
					'selectors' => [
						'{{WRAPPER}} .jet-woo-carousel .swiper-pagination' => 'gap: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'dynamic_bullets' => '',
					],
				]
			);

			$obj->add_responsive_control(
				'dots_alignment',
				[
					'label'     => __( 'Alignment', 'jet-woo-builder' ),
					'type'      => Controls_Manager::CHOOSE,
					'default'   => 'center',
					'options'   => jet_woo_builder_tools()->get_available_flex_h_align_types(),
					'selectors' => [
						'{{WRAPPER}} .jet-woo-carousel .swiper-pagination' => 'justify-content: {{VALUE}};',
					],
					'condition' => [
						'carousel_direction' => 'horizontal',
						'dynamic_bullets'    => '',
					],
				]
			);

			$obj->add_responsive_control(
				'horizontal_space',
				[
					'type'               => Controls_Manager::DIMENSIONS,
					'label'              => __( 'Bullet Space', 'jet-woo-builder' ),
					'size_units'         => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'allowed_dimensions' => 'horizontal',
					'placeholder'        => [
						'top'    => 'auto',
						'right'  => '',
						'bottom' => 'auto',
						'left'   => '',
					],
					'render_type'        => 'template',
					'selectors'          => [
						'{{WRAPPER}} .jet-woo-carousel .swiper-pagination .swiper-pagination-bullet' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'          => [
						'carousel_direction' => 'horizontal',
						'dynamic_bullets!'   => '',
					],
				]
			);

			$obj->add_responsive_control(
				'vertical_space',
				[
					'type'               => Controls_Manager::DIMENSIONS,
					'label'              => __( 'Bullet Space', 'jet-woo-builder' ),
					'size_units'         => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'allowed_dimensions' => 'vertical',
					'placeholder'        => [
						'top'    => '',
						'right'  => 'auto',
						'bottom' => '',
						'left'   => 'auto',
					],
					'render_type'        => 'template',
					'selectors'          => [
						'{{WRAPPER}} .jet-woo-carousel .swiper-pagination .swiper-pagination-bullet' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'          => [
						'carousel_direction' => 'vertical',
						'dynamic_bullets!'   => '',
					],
				]
			);

			$obj->add_control(
				'dots_vert_position',
				[
					'label'   => __( 'Vertical Position', 'jet-woo-builder' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'bottom',
					'options' => [
						'top'    => __( 'Top', 'jet-woo-builder' ),
						'bottom' => __( 'Bottom', 'jet-woo-builder' ),
					],
				]
			);

			$obj->add_responsive_control(
				'dots_top_position',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Top Indent', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
					'selectors'  => [
						'{{WRAPPER}} .jet-woo-carousel .swiper-pagination' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
					],
					'condition'  => [
						'dots_vert_position' => 'top',
					],
				]
			);

			$obj->add_responsive_control(
				'dots_bottom_position',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Bottom Indent', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
					'selectors'  => [
						'{{WRAPPER}} .jet-woo-carousel .swiper-pagination' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
					],
					'condition'  => [
						'dots_vert_position' => 'bottom',
					],
				]
			);

			$obj->add_control(
				'dots_hor_position',
				[
					'label'   => __( 'Horizontal Position', 'jet-woo-builder' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'right',
					'options' => [
						'left'  => __( 'Left', 'jet-woo-builder' ),
						'right' => __( 'Right', 'jet-woo-builder' ),
					],
				]
			);

			$obj->add_responsive_control(
				'dots_left_position',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Left Indent', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
					'selectors'  => [
						'{{WRAPPER}} .jet-woo-carousel .swiper-pagination' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
					],
					'condition'  => [
						'dots_hor_position' => 'left',
					],
				]
			);

			$obj->add_responsive_control(
				'dots_right_position',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Right Indent', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
					'selectors'  => [
						'{{WRAPPER}} .jet-woo-carousel .swiper-pagination' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
					],
					'condition'  => [
						'dots_hor_position' => 'right',
					],
				]
			);

			$obj->add_responsive_control(
				'dots_padding',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Padding', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} .jet-woo-carousel .swiper-pagination' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		}

		/**
		 * Register button widgets style controls
		 *
		 * @since 1.7.0
		 *
		 * @param $id
		 * @param $css_scheme
		 *
		 * @param $obj
		 */
		public function register_button_style_controls( $obj, $id, $css_scheme ) {

			$obj->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => $id . '_button_typography',
					'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
					'selector' => '{{WRAPPER}} ' . $css_scheme,
				)
			);

			$obj->start_controls_tabs( $id . '_button_style_tabs' );

			$obj->start_controls_tab(
				$id . '_button_normal_styles',
				array(
					'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
				)
			);

			$obj->add_control(
				$id . '_button_normal_color',
				array(
					'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $css_scheme => 'color: {{VALUE}} !important',
					),
				)
			);

			$obj->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => $id . '_button_normal_background',
					'label'    => __( 'Background', 'jet-woo-builder' ),
					'types'    => [ 'classic', 'gradient' ],
					'exclude'  => [ 'image' ],
					'selector' => '{{WRAPPER}} ' . $css_scheme,
				]
			);

			$obj->end_controls_tab();

			$obj->start_controls_tab(
				$id . '_button_hover_styles',
				array(
					'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
				)
			);

			$obj->add_control(
				$id . '_button_hover_color',
				array(
					'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $css_scheme . ':hover, {{WRAPPER}} ' . $css_scheme . ':focus' => 'color: {{VALUE}} !important',
					),
				)
			);

			$obj->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => $id . '_button_hover_background',
					'label'    => __( 'Background', 'jet-woo-builder' ),
					'types'    => [ 'classic', 'gradient' ],
					'exclude'  => [ 'image' ],
					'selector' => '{{WRAPPER}} ' . $css_scheme . ':hover, {{WRAPPER}} ' . $css_scheme . ':focus',
				]
			);

			$obj->add_control(
				$id . '_button_border_color_hover',
				[
					'label'     => __( 'Border Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme . ':hover, {{WRAPPER}} ' . $css_scheme . ':focus' => 'border-color: {{VALUE}}',
					],
					'condition' => [
						$id . '_button_border_border!' => '',
					],
				]
			);

			$obj->end_controls_tab();

			$obj->end_controls_tabs();

			$obj->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'      => $id . '_button_border',
					'label'     => __( 'Border', 'jet-woo-builder' ),
					'selector'  => '{{WRAPPER}} ' . $css_scheme,
					'separator' => 'before',
				]
			);

			$obj->add_responsive_control(
				$id . '_button_radius',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Border Radius', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$obj->add_responsive_control(
				$id . '_button_padding',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Padding', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		}

		/**
		 * Register heading widgets style controls
		 *
		 * @since 1.7.0
		 *
		 * @param null   $obj
		 * @param string $id
		 * @param string $css_scheme
		 */
		public function register_heading_style_controls( $obj = null, $id = '', $css_scheme = '' ) {

			$obj->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => $id . '_heading_typography',
					'label'    => __( 'Typography', 'jet-woo-builder' ),
					'selector' => '{{WRAPPER}} ' . $css_scheme,
				]
			);

			$obj->add_control(
				$id . '_heading_color',
				[
					'label'     => __( 'Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme => 'color: {{VALUE}}',
					],
				]
			);

			$obj->add_responsive_control(
				$id . '_heading_margin',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Margin', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$obj->add_responsive_control(
				$id . '_heading_align',
				[
					'label'     => __( 'Alignment', 'jet-woo-builder' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme => 'text-align: {{VALUE}}',
					],
					'classes'   => 'elementor-control-align',
				]
			);

		}

		/**
		 * Register input widgets style controls
		 *
		 * @since 1.7.0
		 *
		 * @param null   $obj
		 * @param string $id
		 * @param string $css_scheme
		 * @param bool   $margin
		 */
		public function register_input_style_controls( $obj = null, $id = '', $css_scheme = '', $margin = true ) {

			$obj->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => $id . '_input_typography',
					'label'    => __( 'Typography', 'jet-woo-builder' ),
					'selector' => '{{WRAPPER}} ' . $css_scheme,
				]
			);

			$obj->start_controls_tabs( $id . '_fields_styles' );

			$obj->start_controls_tab(
				$id . '_fields_normal_styles', [
					'label' => __( 'Normal', 'jet-woo-builder' ),
				]
			);

			$obj->add_control(
				$id . '_input_color',
				[
					'label'     => __( 'Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme                                                                     => 'color: {{VALUE}}',
						'{{WRAPPER}} .select2-container .select2-selection .select2-selection__rendered'                 => 'color: {{VALUE}}',
						'{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__arrow b' => 'border-color: {{VALUE}} transparent transparent transparent;',
					],
				]
			);

			$obj->add_control(
				$id . '_input_background',
				[
					'label'     => __( 'Background Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme . ':not(.select2)'             => 'background-color: {{VALUE}}',
						'{{WRAPPER}} .select2-container .select2-selection--single' => 'background-color: {{VALUE}}',
					],
				]
			);

			$obj->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => $id . '_box_shadow',
					'selector' => '{{WRAPPER}} ' . $css_scheme . ':not(.select2), {{WRAPPER}} .select2-container .select2-selection--single',
				]
			);

			$obj->end_controls_tab();

			$obj->start_controls_tab(
				$id . '_fields_focus_styles', [
					'label' => __( 'Focus', 'jet-woo-builder' ),
				]
			);

			$obj->add_control(
				$id . '_input_focus_color',
				[
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Color', 'jet-woo-builder' ),
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme . ':focus'                                                                                => 'color: {{VALUE}}',
						'{{WRAPPER}} .select2-container .select2-selection--single[aria-expanded="true"] .select2-selection__rendered'         => 'color: {{VALUE}}',
						'{{WRAPPER}} .select2-container--default .select2-selection--single[aria-expanded="true"] .select2-selection__arrow b' => 'border-color: transparent transparent {{VALUE}} transparent;',
					],
				]
			);

			$obj->add_control(
				$id . '_input_focus_background',
				[
					'label'     => __( 'Background Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme . ':not(.select2):focus'                             => 'background-color: {{VALUE}}',
						'{{WRAPPER}} .select2-container .select2-selection--single[aria-expanded="true"]' => 'background-color: {{VALUE}}',
					],
				]
			);

			$obj->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => $id . '_focus_box_shadow',
					'selector' => '{{WRAPPER}} ' . $css_scheme . ':not(.select2):focus, {{WRAPPER}} .select2-container .select2-selection--single[aria-expanded="true"]',
				]
			);

			$obj->end_controls_tab();

			$obj->end_controls_tabs();

			$obj->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'      => $id . '_input_border',
					'separator' => 'before',
					'selector'  => '{{WRAPPER}} ' . $css_scheme . ':not(.select2), {{WRAPPER}} .select2-container .select2-selection--single',
				]
			);

			$obj->add_responsive_control(
				$id . '_input_border_radius',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Border Radius', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme . ':not(.select2)'             => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .select2-container .select2-selection--single' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			if ( $margin ) {
				$obj->add_responsive_control(
					$id . '_input_margin',
					[
						'type'       => Controls_Manager::DIMENSIONS,
						'label'      => __( 'Margin', 'jet-woo-builder' ),
						'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
						'selectors'  => [
							'{{WRAPPER}} ' . $css_scheme => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
			}

			$obj->add_responsive_control(
				$id . '_input_padding',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Padding', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme . ':not(.select2)'                                                   => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered' => 'line-height: calc( ({{TOP}}{{UNIT}}*2) + 16px ); padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};',
						'{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__arrow'    => 'height: calc( ({{TOP}}{{UNIT}}*2) + 16px );',
						'{{WRAPPER}} .select2-container--default .select2-selection--single'                              => 'height: auto;',
					],
				]
			);

		}

		/**
		 * Register label widgets style controls
		 *
		 * @since 1.7.0
		 *
		 * @param $id
		 * @param $css_scheme
		 *
		 * @param $obj
		 */
		public function register_label_style_controls( $obj, $id, $css_scheme ) {

			$obj->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => $id . '_label_typography',
					'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
					'selector' => '{{WRAPPER}} ' . $css_scheme,
				)
			);

			$obj->add_control(
				$id . '_label_color',
				array(
					'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $css_scheme => 'color: {{VALUE}}',
					),
				)
			);

			$obj->add_control(
				$id . '_label_required_color',
				[
					'label'     => __( 'Asterisk Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme . ' abbr'      => 'color: {{VALUE}}',
						'{{WRAPPER}} ' . $css_scheme . ' .required' => 'color: {{VALUE}}',
					],
				]
			);

			$obj->add_responsive_control(
				$id . '_label_margin',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Margin', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$obj->add_responsive_control(
				$id . '_label_align',
				[
					'label'     => __( 'Alignment', 'jet-woo-builder' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme => 'text-align: {{VALUE}}',
					],
					'classes'   => 'elementor-control-align',
				]
			);

		}

		/**
		 * Register form widgets style controls
		 *
		 * @since 1.7.0
		 *
		 * @param      $obj
		 * @param      $id
		 * @param      $css_scheme
		 * @param bool $with_links
		 */
		public function register_form_style_controls( $obj = null, $id = '', $css_scheme = '', $with_links = false ) {

			$obj->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => $id . '_form_typography',
					'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
					'selector' => '{{WRAPPER}} ' . $css_scheme . ' p',
				)
			);

			$obj->add_control(
				$id . '_form_text_color',
				array(
					'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $css_scheme . ' p' => 'color: {{VALUE}}',
					),
				)
			);

			$obj->add_control(
				$id . '_form_background',
				[
					'label'     => __( 'Background Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme => 'background-color: {{VALUE}}',
					],
				]
			);

			if ( $with_links ) {

				$obj->add_control(
					$id . '_form_links_heading',
					[
						'label' => __( 'Links', 'jet-woo-builder' ),
						'type'  => Controls_Manager::HEADING,
					]
				);

				$obj->start_controls_tabs( $id . '_form_link_styles_tabs' );

				$obj->start_controls_tab(
					$id . '_form_link_styles_normal_tab',
					[
						'label' => __( 'Normal', 'jet-woo-builder' ),
					]
				);

				$obj->add_control(
					$id . '_form_link_normal_color',
					[
						'label'     => __( 'Color', 'jet-woo-builder' ),
						'type'      => Controls_Manager::COLOR,
						'separator' => 'after',
						'selectors' => [
							'{{WRAPPER}} ' . $css_scheme . ' a' => 'color: {{VALUE}}',
						],
					]
				);

				$obj->end_controls_tab();

				$obj->start_controls_tab(
					$id . '_form_link_styles_hover_tab',
					[
						'label' => __( 'Hover', 'jet-woo-builder' ),
					]
				);

				$obj->add_control(
					$id . '_form_link_hover_color',
					[
						'label'     => __( 'Color', 'jet-woo-builder' ),
						'type'      => Controls_Manager::COLOR,
						'separator' => 'after',
						'selectors' => [
							'{{WRAPPER}} ' . $css_scheme . ' a:hover' => 'color: {{VALUE}}',
						],
					]
				);

				$obj->end_controls_tab();

				$obj->end_controls_tabs();

			}

			$obj->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => $id . '_form_border',
					'label'    => esc_html__( 'Border', 'jet-woo-builder' ),
					'selector' => '{{WRAPPER}} ' . $css_scheme,
				)
			);

			$obj->add_responsive_control(
				$id . '_form_border_radius',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Border Radius', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$obj->add_responsive_control(
				$id . '_form_margin',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Margin', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$obj->add_responsive_control(
				$id . '_form_padding',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Padding', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		}

		/**
		 * Register table cell widgets style controls
		 *
		 * @since 1.7.0
		 *
		 * @param $id
		 * @param $css_scheme
		 *
		 * @param $obj
		 */
		public function register_table_cell_style_controls( $obj, $id, $css_scheme ) {

			$obj->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => $id . '_background',
					'label'    => __( 'Background', 'jet-woo-builder' ),
					'types'    => [ 'classic', 'gradient' ],
					'exclude'  => [ 'image' ],
					'selector' => '{{WRAPPER}} ' . $css_scheme,
				]
			);

			$obj->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'     => $id . '_border',
					'label'    => __( 'Border', 'jet-woo-builder' ),
					'selector' => '{{WRAPPER}} ' . $css_scheme,
				]
			);

			$obj->add_responsive_control(
				$id . '_padding',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Padding', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$obj->add_responsive_control(
				$id . '_align',
				[
					'label'     => __( 'Alignment', 'jet-woo-builder' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme => 'text-align: {{VALUE}}',
					],
					'classes'   => 'elementor-control-align',
				]
			);

		}

		/**
		 * Form manager controls.
		 *
		 * Register controls for managing checkout forms fields.
		 *
		 * @since  2.0.0
		 * @access public
		 *
		 * @param object $obj          Widget instance.
		 * @param string $fields_group Specific form prefix.
		 * @param array  $css_scheme   Style selectors list.
		 *
		 * @return void
		 */
		public function register_checkout_forms_manage_fields_controls( $obj, $fields_group, $css_scheme ) {

			$obj->add_control(
				'modify_field',
				[
					'label'              => __( 'Customize Fields', 'jet-woo-builder' ),
					'type'               => Controls_Manager::SWITCHER,
					'frontend_available' => true,
				]
			);

			$repeater = new Repeater();

			$repeater->add_control(
				'field_key',
				[
					'label'   => __( 'Field Type', 'jet-woo-builder' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'first_name',
					'options' => jet_woo_builder_tools()->get_checkout_forms_field_type_options(),
				]
			);

			$repeater->add_control(
				'field_label',
				[
					'label'   => __( 'Label', 'jet-woo-builder' ),
					'type'    => Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$repeater->add_control(
				'field_placeholder',
				[
					'label'   => __( 'Placeholder', 'jet-woo-builder' ),
					'type'    => Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$repeater->add_control(
				'field_default_value',
				[
					'label'   => __( 'Default Value', 'jet-woo-builder' ),
					'type'    => Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$repeater->add_control(
				'field_validation',
				[
					'label'       => __( 'Validation', 'jet-woo-builder' ),
					'type'        => Controls_Manager::SELECT2,
					'multiple'    => true,
					'options'     => [
						'email'    => __( 'Email', 'jet-woo-builder' ),
						'phone'    => __( 'Phone', 'jet-woo-builder' ),
						'postcode' => __( 'Postcode', 'jet-woo-builder' ),
						'state'    => __( 'State', 'jet-woo-builder' ),
						'number'   => __( 'Number', 'jet-woo-builder' ),
					],
					'label_block' => true,
				]
			);

			$repeater->add_control(
				'field_class',
				[
					'label'   => __( 'Class', 'jet-woo-builder' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'form-row-wide',
					'options' => [
						'form-row-first' => __( 'First', 'jet-woo-builder' ),
						'form-row-last'  => __( 'Last', 'jet-woo-builder' ),
						'form-row-wide'  => __( 'Wide', 'jet-woo-builder' ),
					],
				]
			);

			$repeater->add_responsive_control(
				'field_width',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Width', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', '%' ] ),
					'range'      => [
						'px' => [
							'min' => 0,
							'max' => 1000,
						],
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['field'] . '{{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition'  => [
						'field_class!' => 'form-row-wide',
					],
				]
			);

			$repeater->add_control(
				'field_required',
				[
					'label' => __( 'Required', 'jet-woo-builder' ),
					'type'  => Controls_Manager::SWITCHER,
				]
			);

			$obj->add_control(
				'field_list',
				[
					'label'       => __( 'Fields List', 'jet-woo-builder' ),
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => jet_woo_builder_tools()->get_checkout_forms_default_fields_set( $fields_group ),
					'title_field' => '{{{ field_label }}}',
					'condition'   => [
						'modify_field' => 'yes',
					],
				]
			);

			$obj->add_control(
				'fields_mobile_approach',
				[
					'type'         => Controls_Manager::SWITCHER,
					'label'        => __( 'Responsive Mobile Approach', 'jet-woo-builder' ),
					'description'  => __( 'Enable responsive fields width for mobile devices.', 'jet-woo-builder' ),
					'prefix_class' => 'jet-woo-builder-forms-mobile-approach-',
					'separator'    => 'before',
				]
			);

		}

		/**
		 * Register badges style controls.
		 *
		 * @since  2.1.0
		 * @access public
		 *
		 * @param object $obj        Widget instance.
		 * @param array  $css_scheme Styles selector list.
		 *
		 * @return void
		 */
		public function register_badges_style_controls( $obj, $css_scheme ) {
			$obj->add_control(
				'badges_display',
				[
					'label'     => __( 'Display Type', 'jet-woo-builder' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => [
						'inline-flex' => __( 'Inline', 'jet-woo-builder' ),
						'flex'        => __( 'Block', 'jet-woo-builder' ),
					],
					'default'   => 'inline-flex',
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['badge'] => 'display: {{VALUE}};',
					],
				]
			);

			$obj->add_control(
				'badges_custom_size',
				[
					'label' => __( 'Custom Size', 'jet-woo-builder' ),
					'type'  => Controls_Manager::SWITCHER,
				]
			);

			$obj->add_responsive_control(
				'badges_min_width',
				[
					'label'      => __( 'Min Width', 'jet-woo-builder' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range'      => [
						'px' => [
							'min' => 0,
							'max' => 300,
						],
					],
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['badge'] => 'min-width: {{SIZE}}{{UNIT}};',
					],
					'condition'  => [
						'badges_custom_size!' => '',
						'badges_display'      => 'inline-flex',
					],
				]
			);

			$obj->add_responsive_control(
				'badges_min_height',
				[
					'label'      => __( 'Min Height', 'jet-woo-builder' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range'      => [
						'px' => [
							'min' => 0,
							'max' => 300,
						],
					],
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['badge'] => 'min-height: {{SIZE}}{{UNIT}};',
					],
					'condition'  => [
						'badges_custom_size!' => '',
					],
				]
			);

			$obj->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'badge_typography',
					'selector' => '{{WRAPPER}}  ' . $css_scheme['badge'],
				]
			);

			$obj->add_control(
				'badge_on_sale_color',
				[
					'label'     => __( 'Color', 'jet-woo-builder' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['badge'] => 'color: {{VALUE}}',
					],
				]
			);

			$obj->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'badge_on_sale_background',
					'selector' => '{{WRAPPER}} ' . $css_scheme['badge'],
				]
			);

			$obj->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'     => 'badge_on_sale_border',
					'label'    => __( 'Border', 'jet-woo-builder' ),
					'selector' => '{{WRAPPER}} ' . $css_scheme['badge'],
				]
			);

			$obj->add_responsive_control(
				'badge_border_radius',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Border Radius', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['badge'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$obj->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'badge_box_shadow',
					'selector' => '{{WRAPPER}} ' . $css_scheme['badge'],
				]
			);

			$obj->add_responsive_control(
				'badge_margin',
				[
					'type'       => Controls_Manager::DIMENSIONS,
					'label'      => __( 'Margin', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['badge'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$obj->add_responsive_control(
				'badge_padding',
				[
					'label'      => __( 'Padding', 'jet-woo-builder' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['badge'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$obj->add_control(
				'badge_vert_position',
				[
					'type'    => Controls_Manager::SELECT,
					'label'   => __( 'Vertical Position by', 'jet-engine' ),
					'default' => 'top',
					'options' => [
						'top'    => __( 'Top', 'jet-woo-builder' ),
						'bottom' => __( 'Bottom', 'jet-woo-builder' ),
					],
				]
			);

			$obj->add_responsive_control(
				'badge_top_position',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Top Indent', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
					'default'    => [
						'unit' => 'px',
						'size' => 0,
					],
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['badges'] => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
					],
					'condition'  => [
						'badge_vert_position' => 'top',
					],
				]
			);

			$obj->add_responsive_control(
				'badge_bottom_position',
				[
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Bottom Indent', 'jet-woo-builder' ),
					'size_units' => $obj->set_custom_size_unit( [ 'px', 'em', '%' ] ),
					'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
					'default'    => [
						'unit' => 'px',
						'size' => 0,
					],
					'selectors'  => [
						'{{WRAPPER}} ' . $css_scheme['badges'] => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
					],
					'condition'  => [
						'badge_vert_position' => 'bottom',
					],
				]
			);

			$obj->add_responsive_control(
				'badge_alignment',
				[
					'type'      => Controls_Manager::CHOOSE,
					'label'     => __( 'Alignment', 'jet-woo-builder' ),
					'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['badges'] => 'text-align: {{VALUE}};',
					],
					'condition' => [
						'badges_display' => 'inline-flex',
					],
					'classes'   => 'elementor-control-align',
				]
			);

			$obj->add_responsive_control(
				'badge_content_alignment',
				[
					'type'      => Controls_Manager::CHOOSE,
					'label'     => __( 'Alignment', 'jet-woo-builder' ),
					'options'   => jet_woo_builder_tools()->get_available_flex_h_align_types(),
					'selectors' => [
						'{{WRAPPER}} ' . $css_scheme['badge'] => 'justify-content: {{VALUE}};',
					],
					'condition' => [
						'badges_display' => 'flex',
					],
				]
			);

		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.7.0
		 * @access public
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;

		}

	}

}

/**
 * Returns instance of Jet_Woo_Builder_Common_Controls
 *
 * @since 1.7.0
 * @return object
 */
function jet_woo_builder_common_controls() {
	return Jet_Woo_Builder_Common_Controls::get_instance();
}
