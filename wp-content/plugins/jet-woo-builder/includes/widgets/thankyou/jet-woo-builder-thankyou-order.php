<?php
/**
 * Class: Jet_Woo_Builder_ThankYou_Order
 * Name: Thank You Order
 * Slug: jet-thankyou-order
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_ThankYou_Order extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-thankyou-order';
	}

	public function get_title() {
		return __( 'Thank You Order', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-thank-you-order';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-a-thank-you-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'thankyou' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-thankyou-order/css-scheme',
			[
				'message'     => '.woocommerce-notice',
				'overview'    => 'ul.order_details li',
				'details'     => 'ul.order_details li strong',
				'fail-button' => '.button.pay',
			]
		);

		$this->start_controls_section(
			'thankyou_order_general_section',
			[
				'label' => __( 'General', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		$this->add_control(
			'thankyou_order_custom_labels',
			[
				'label' => __( 'Modify', 'jet-woo-builder' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'thankyou_message_text',
			[
				'label'       => __( 'Message', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Thank you. Your order has been received.', 'jet-woo-builder' ),
				'placeholder' => __( 'Your order has been received.', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'thankyou_order_custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'thankyou_order_headings_heading',
			[
				'label'     => __( 'Headings', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'thankyou_order_custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'thankyou_order_table_order_heading',
			[
				'label'       => __( 'Order', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Order number:', 'jet-woo-builder' ),
				'placeholder' => __( 'Order number:', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'thankyou_order_custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'thankyou_order_table_date_heading',
			[
				'label'       => __( 'Date', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Date:', 'jet-woo-builder' ),
				'placeholder' => __( 'Date:', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'thankyou_order_custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'thankyou_order_table_email_heading',
			[
				'label'       => __( 'Email', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Email:', 'jet-woo-builder' ),
				'placeholder' => __( 'Email:', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'thankyou_order_custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'thankyou_order_table_total_heading',
			[
				'label'       => __( 'Total', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Total:', 'jet-woo-builder' ),
				'placeholder' => __( 'Total:', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'thankyou_order_custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'thankyou_order_table_payment_method_heading',
			[
				'label'       => __( 'Payment', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Payment method:', 'jet-woo-builder' ),
				'placeholder' => __( 'Payment method:', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'thankyou_order_custom_labels' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'thankyou_message_styles',
			array(
				'label' => esc_html__( 'Message', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_heading_style_controls( $this, 'thankyou_message', $css_scheme['message'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'thankyou_overview_styles',
			array(
				'label' => esc_html__( 'Overview', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'thankyou_overview_display_type',
			[
				'label'     => __( 'Display Type', 'jet-smart-filters' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'row',
				'options'   => [
					'row' => [
						'title' => __( 'Row', 'jet-smart-filters' ),
						'icon'  => 'eicon-ellipsis-h',
					],
					'column'        => [
						'title' => __( 'Column', 'jet-smart-filters' ),
						'icon'  => 'eicon-menu-bar',
					],
				],
				'selectors' => [
					'{{WRAPPER}} ul.order_details' => 'flex-direction: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'thankyou_overview_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['overview'],
			)
		);

		$this->add_control(
			'thankyou_overview_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['overview'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'thankyou_overview_border',
				'label'       => esc_html__( 'Border', 'jet-woo-builder' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['overview'],
			)
		);

		$this->add_responsive_control(
			'thankyou_overview_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['overview'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thankyou_overview_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['overview'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thankyou_overview_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['overview'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thankyou_overview_align',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['overview'] => 'text-align: {{VALUE}}',
				),
				'classes'   => 'elementor-control-align',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'thankyou_details_styles',
			array(
				'label' => esc_html__( 'Details', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_heading_style_controls( $this, 'thankyou_details', $css_scheme['details'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'thankyou_order_failed_buttons',
			[
				'label' => __( 'Buttons', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'thankyou_order_failed_buttons_info',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'These buttons only become visible when your order is considered failed.', 'jet-woo-builder' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'thankyou_order_failed', $css_scheme['fail-button'] );

		$this->end_controls_section();

	}

	protected function render() {

		// Add & Remove actions & filters before displaying our Widget.
		add_filter( 'woocommerce_thankyou_order_received_text', [ $this, 'modify_thankyou_order_received_message' ] );
		// Remove order details in order.
		remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table' );

		$this->__open_wrap();

		include $this->get_template( 'thankyou/order.php' );

		$this->__close_wrap();

		// Add & Remove actions & filters after displaying our Widget.
		remove_filter( 'woocommerce_thankyou_order_received_text', [ $this, 'modify_thankyou_order_received_message' ] );
		add_action( 'woocommerce_thankyou', 'woocommerce_order_details_table' );

	}

	/**
	 * Change default WooCommerce thank you message text.
	 *
	 * @since 1.12.0
	 *
	 * @param $message
	 *
	 * @return mixed
	 */
	public function modify_thankyou_order_received_message( $message ) {

		$settings = $this->get_settings_for_display();

		if ( isset( $settings['thankyou_order_custom_labels'] ) && 'yes' === $settings['thankyou_order_custom_labels'] ) {
			if ( isset( $settings['thankyou_message_text'] ) && ! empty( $settings['thankyou_message_text'] ) ) {
				$message = $settings['thankyou_message_text'];
			}
		}

		return $message;

	}

}
