<?php
/**
 * Class: Jet_Woo_Builder_Archive_Product_Rating
 * Name: Archive Rating
 * Slug: jet-woo-builder-archive-product-rating
 */

namespace Elementor;

use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Archive_Product_Rating extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-builder-archive-product-rating';
	}

	public function get_title() {
		return __( 'Archive Rating', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-rating';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/woocommerce-jetwoobuilder-settings-how-to-create-and-set-a-custom-categories-archive-template/?utm_source=need-help&utm_medium=jet-woo-categories&utm_campaign=jetwoobuilder';
	}

	public function get_jet_style_depends() {
		return [ 'jet-woo-builder', 'jet-woo-builder-frontend-font' ];
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'archive' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-single-rating/css-scheme',
			[
				'rating' => '.jet-woo-product-rating',
				'stars'  => '.product-star-rating',
			]
		);

		$this->start_controls_section(
			'section_archive_rating_styles',
			[
				'label' => __( 'Rating', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'show_empty_rating',
			[
				'label'     => __( 'Show Empty Rating', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'after',
			]
		);

		$this->add_control(
			'archive_rating_icon',
			[
				'label'   => __( 'Stars Type', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'jetwoo-front-icon-rating-1',
				'options' => jet_woo_builder_tools()->get_available_rating_icons_list(),
			]
		);

		$this->add_responsive_control(
			'archive_stars_font_size',
			[
				'label'      => __( 'Size', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['stars'] . ' .product-rating__icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'archive_stars_space_between',
			[
				'label'      => __( 'Space Between', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 2,
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['stars'] . ' .product-rating__icon + .product-rating__icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_archive_stars_styles' );

		$this->start_controls_tab(
			'tab_archive_stars_all',
			array(
				'label' => esc_html__( 'All', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'stars_archive_color_all',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#a1a2a4',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['stars'] . ' .product-rating__icon' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_archive_stars_rated',
			array(
				'label' => esc_html__( 'Rated', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'archive_stars_color_rated',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fdbc32',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['stars'] . ' .product-rating__icon.active' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_archive_stars_empty',
			[
				'label'     => __( 'Empty', 'jet-woo-builder' ),
				'condition' => [
					'show_empty_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'archive_stars_color_empty',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .empty ' . $css_scheme['stars'] . ' .product-rating__icon' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'archive_stars_alignment',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['rating'] => 'text-align: {{VALUE}};',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * CSS selector.
	 *
	 * Returns CSS selector for nested element.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @param null $el Selector.
	 *
	 * @return string
	 */
	public function css_selector( $el = null ) {
		return sprintf( '{{WRAPPER}} .%1$s%2$s', $this->get_name(), $el );
	}

	public static function render_callback( $settings = [] ) {

		global $product;

		if ( ! is_a( $product, 'WC_Product' ) ) {
			return;
		}

		$empty_rating = filter_var( $settings['empty_rating'], FILTER_VALIDATE_BOOLEAN );
		$rating = jet_woo_builder_template_functions()->get_product_custom_rating( $settings['icon'], $empty_rating );

		if ( ! $rating ) {
			return;
		}

		$classes = [ 'jet-woo-product-rating' ];

		if ( $empty_rating && empty( $product->get_average_rating() ) ) {
			$classes[] = 'empty';
		}

		printf( '<div class="jet-woo-builder-archive-product-rating"><div class="%2$s">%1$s</div></div>', $rating, implode( ' ', $classes ) );

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$macros_settings = [
			'icon'         => $settings['archive_rating_icon'] ?? 'jetwoo-front-icon-rating-1',
			'empty_rating' => $settings['show_empty_rating'] ?? false,
		];

		if ( jet_woo_builder_tools()->is_builder_content_save() ) {
			echo jet_woo_builder()->parser->get_macros_string( $this->get_name(), $macros_settings );
		} else {
			echo self::render_callback( $macros_settings );
		}

	}

}
