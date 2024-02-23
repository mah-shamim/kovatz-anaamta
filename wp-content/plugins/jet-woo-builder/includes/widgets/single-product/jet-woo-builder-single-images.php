<?php
/**
 * Class: Jet_Woo_Builder_Single_Images
 * Name: Single Images
 * Slug: jet-single-images
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Single_Images extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-single-images';
	}

	public function get_title() {
		return __( 'Single Images', 'jet-woo-builder' );
	}

	public function get_script_depends() {
		return [ 'flexslider', 'zoom', 'wc-single-product' ];
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-images';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-and-set-a-single-product-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'single' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-single-images/css-scheme',
			[
				'images'             => '.jet-single-images__wrap div.images',
				'main_image'         => '.jet-single-images__wrap .woocommerce-product-gallery > .flex-viewport',
				'single_main_image'  => '.jet-single-images__wrap .woocommerce-product-gallery__trigger + .woocommerce-product-gallery__wrapper',
				'thumbnails_wrapper' => '.jet-single-images__wrap .flex-control-thumbs',
				'thumbnails'         => '.jet-single-images__wrap .flex-control-thumbs li',
				'thumbnails_img'     => '.jet-single-images__wrap .flex-control-thumbs li > img',
			]
		);

		$this->start_controls_section(
			'section_single_main_image_style',
			[
				'label' => __( 'Featured Image', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		$this->add_responsive_control(
			'main_image_width',
			[
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Width', 'jet-woo-builder' ),
				'size_units'  => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'       => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'     => [
					'unit' => '%',
					'size' => 100,
				],
				'render_type' => 'template',
				'selectors'   => [
					'{{WRAPPER}} ' . $css_scheme['images'] => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'product_images_alignment',
			[
				'label'                => __( 'Alignment', 'jet-woo-builder' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'jet-woo-builder' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'jet-woo-builder' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'jet-woo-builder' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors_dictionary' => [
					'left'   => 'margin-right: auto;',
					'center' => 'margin: 0 auto;',
					'right'  => 'margin-left: auto;',
				],
				'separator'            => 'after',
				'selectors'            => [
					'{{WRAPPER}} ' . $css_scheme['images'] => '{{VALUE}}',
				],
				'classes'              => 'elementor-control-align',
			]
		);

		$this->add_control(
			'main_image_background_color',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['main_image']        => 'background-color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['single_main_image'] => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'main_image_border',
				'selector' => '{{WRAPPER}} ' . $css_scheme['main_image'] . ',{{WRAPPER}} ' . $css_scheme['single_main_image'],
			]
		);

		$this->add_responsive_control(
			'main_image_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['main_image']        => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['single_main_image'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'main_image_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['main_image'] . ',{{WRAPPER}} ' . $css_scheme['single_main_image'],
			]
		);

		$this->add_responsive_control(
			'main_image_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['main_image']        => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['single_main_image'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_single_image_thumbnails_style',
			[
				'label' => __( 'Thumbnails', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'control_nav_direction',
			[
				'label'   => __( 'Direction:', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => jet_woo_builder_tools()->get_available_direction_types(),
			]
		);

		$this->add_responsive_control(
			'thumbnails_vertical_width',
			[
				'label'      => __( 'Vertical Thumbnails Width', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'%'  => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default'    => [
					'size' => 150,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .jet-single-images-nav-vertical' . $css_scheme['thumbnails_wrapper'] => 'flex: 0 0 {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'control_nav_direction' => 'vertical',
				],
			]
		);

		$this->add_control(
			'control_nav_v_position',
			[
				'label'     => __( 'Position', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => [
					'left'  => [
						'title' => __( 'Start', 'jet-woo-builder' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					],
					'right' => [
						'title' => __( 'End', 'jet-woo-builder' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					],
				],
				'condition' => [
					'control_nav_direction' => 'vertical',
				],
			]
		);

		$this->add_control(
			'image_thumbnails_alignment',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
				'options'   => jet_woo_builder_tools()->get_available_flex_h_align_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['thumbnails_wrapper'] => 'justify-content: {{VALUE}}',
				],
				'condition' => [
					'control_nav_direction' => 'horizontal',
				],
			]
		);

		$this->add_responsive_control(
			'image_thumbnails_width',
			[
				'label'          => __( 'Width (%)', 'jet-woo-builder' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ '%' ],
				'range'          => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'        => [
					'unit' => '%',
					'size' => 25,
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'selectors'      => [
					'{{WRAPPER}} ' . $css_scheme['thumbnails'] => 'width: {{SIZE}}{{UNIT}}; flex: 0 0 {{SIZE}}{{UNIT}}',
				],
				'condition'      => [
					'control_nav_direction' => 'horizontal',
				],
			]
		);

		$this->add_control(
			'image_thumbnails_background_color',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['thumbnails'] => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'image_thumbnails_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['thumbnails']                                          => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .jet-single-images-nav-horizontal' . $css_scheme['thumbnails_wrapper'] => 'margin-left: -{{LEFT}}{{UNIT}}; margin-right: -{{RIGHT}}{{UNIT}};',
					'{{WRAPPER}} .jet-single-images-nav-vertical' . $css_scheme['thumbnails_wrapper']   => 'margin-top: -{{TOP}}{{UNIT}}; margin-bottom: -{{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_image_thumbnails_style',
			[
				'label'     => __( 'Image', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_thumbnails_border',
				'selector' => '{{WRAPPER}} ' . $css_scheme['thumbnails_img'],
			]
		);

		$this->add_responsive_control(
			'image_thumbnails_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['thumbnails_img'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_thumbnails_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['thumbnails_img'],
			]
		);

		$this->add_responsive_control(
			'image_thumbnails_images_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['thumbnails_img'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'image_thumbnails_border_border!' => '',
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

			include $this->get_template( 'single-product/images.php' );

			$this->__close_wrap();

			if ( jet_woo_builder()->elementor_views->in_elementor() ) {
				$this->__reset_editor_product();
			}
		}

		// On render widget from Editor - trigger the init manually.
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			?>
			<script>
				jQuery( '.woocommerce-product-gallery' ).each( function () {
					jQuery( this ).wc_product_gallery();
				} );
			</script>
			<?php
		}

	}

}
