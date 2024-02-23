<?php
/**
 * Class: Jet_Woo_Builder_Cart_Return_To_Shop
 * Name: Cart Return To Shop
 * Slug: jet-cart-return-to-shop
 */

namespace Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Cart_Return_To_Shop extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-cart-return-to-shop';
	}

	public function get_title() {
		return __( 'Cart Return To Shop', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-cart-return-to-shop';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-a-cart-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'cart' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-cart-return-to-shop/css-scheme',
			[
				'button' => '.return-to-shop .button',
			]
		);

		$this->start_controls_section(
			'cart_return_to_shop_button_section',
			[
				'label' => __( 'Button', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		$this->add_control(
			'cart_return_to_shop_enable_custom_controls',
			[
				'label' => __( 'Modify', 'jet-woo-builder' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'cart_return_to_shop_button_text',
			[
				'label'     => __( 'Label', 'jet-woo-builder' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'cart_return_to_shop_enable_custom_controls' => 'yes',
				],
			]
		);

		$this->add_control(
			'cart_return_to_shop_button_link',
			[
				'label'       => __( 'Link', 'jet-woo-builder' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'jet-woo-builder' ),
				'condition'   => [
					'cart_return_to_shop_enable_custom_controls' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'return_to_shop_button_styles',
			array(
				'label' => esc_html__( 'Styles', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'return_to_shop', $css_scheme['button'] );

		$this->add_responsive_control(
			'return_to_shop_button_align',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => array(
					'{{WRAPPER}} .return-to-shop' => 'text-align: {{VALUE}}',
				),
				'separator' => 'before',
				'classes'   => 'elementor-control-align',
			)
		);

		$this->end_controls_section();

	}

	protected function render() {

		// Add filters before displaying our Widget.
		// Change button label.
		add_filter( 'woocommerce_return_to_shop_text', [ $this, 'modify_return_to_shop_button_label' ] );
		// Change button link.
		add_filter( 'woocommerce_return_to_shop_redirect', [ $this, 'modify_return_to_shop_button_link' ] );

		$this->__open_wrap();

		include $this->get_template( 'cart/return-to-shop.php' );

		$this->__close_wrap();

		// Remove filters after displaying our Widget.
		remove_filter( 'woocommerce_return_to_shop_text', [ $this, 'modify_return_to_shop_button_label' ] );
		remove_filter( 'woocommerce_return_to_shop_redirect', [ $this, 'modify_return_to_shop_button_link' ] );

	}

	/**
	 * Change default empty cart return to shot button label.
	 *
	 * @since 1.12.0
	 *
	 * @param $label
	 *
	 * @return mixed
	 */
	public function modify_return_to_shop_button_label( $label ) {

		$settings = $this->get_settings_for_display();

		if ( isset( $settings['cart_return_to_shop_enable_custom_controls'] ) && 'yes' === $settings['cart_return_to_shop_enable_custom_controls'] ) {
			if ( isset( $settings['cart_return_to_shop_button_text'] ) && ! empty( $settings['cart_return_to_shop_button_text'] ) ) {
				$label = $settings['cart_return_to_shop_button_text'];
			}
		}

		return $label;

	}

	/**
	 * Change default empty cart return to shot button link.
	 *
	 * @since 1.12.0
	 *
	 * @param $link
	 *
	 * @return mixed
	 */
	public function modify_return_to_shop_button_link( $link ) {

		$settings = $this->get_settings_for_display();

		if ( isset( $settings['cart_return_to_shop_enable_custom_controls'] ) && 'yes' === $settings['cart_return_to_shop_enable_custom_controls'] ) {
			if ( isset( $settings['cart_return_to_shop_button_link'] ) && ! empty( $settings['cart_return_to_shop_button_link']['url'] ) ) {
				$this->add_link_attributes( 'button', $settings['cart_return_to_shop_button_link'] );

				$link = $settings['cart_return_to_shop_button_link']['url'];
			}
		}

		return $link;

	}

}
