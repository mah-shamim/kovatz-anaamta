<?php
/**
 * Class: Jet_Woo_Woocommerce_Actions
 * Name: Woocommerce Actions
 * Slug: jet-woo-woocommerce-actions
 */

namespace Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Woocommerce_Actions extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-woocommerce-actions';
	}

	public function get_title() {
		return __( 'Woocommerce Actions', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-woocommerce-actions';
	}

	public function get_jet_help_url() {
		return '#';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'general_section',
			[
				'label' => __( 'Woocommerce Actions', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'hook_name',
			[
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Hook', 'jet-woo-builder' ),
				'description' => __( 'The names of the hooks are based on the location of the elements in the default WooCommerce templates.', 'jet-woo-builder' ),
				'groups'      => [
					[
						'label'   => __( 'Single Product', 'jet-woo-builder' ),
						'options' => [
							'woocommerce_before_single_product_summary' => __( 'Before Summary', 'jet-woo-builder' ),
							'woocommerce_single_product_summary'        => __( 'Summary', 'jet-woo-builder' ),
							'woocommerce_after_single_product_summary'  => __( 'After Summary', 'jet-woo-builder' ),
						],
					],
					[
						'label'   => __( 'Shop Loop', 'jet-woo-builder' ),
						'options' => [
							'woocommerce_before_shop_loop_item'       => __( 'Before Item', 'jet-woo-builder' ),
							'woocommerce_before_shop_loop_item_title' => __( 'Before Item Title', 'jet-woo-builder' ),
							'woocommerce_shop_loop_item_title'        => __( 'Item Title', 'jet-woo-builder' ),
							'woocommerce_after_shop_loop_item_title'  => __( 'After Item Title', 'jet-woo-builder' ),
							'woocommerce_after_shop_loop_item'        => __( 'After Item', 'jet-woo-builder' ),
						],
					],
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

		$settings = $this->get_settings_for_display();

		if ( empty( $settings['hook_name'] ) ) {
			return;
		}

		jet_woo_builder()->woocommerce->remove_action_hooked_callbacks( $settings['hook_name'] );

		do_action( $settings['hook_name'] );

	}

}
