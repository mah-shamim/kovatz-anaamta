<?php
/**
 * Class: Jet_Woo_Builder_MyAccount_Content
 * Name: Account Content
 * Slug: jet-myaccount-content
 */

namespace Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_MyAccount_Content extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-myaccount-content';
	}

	public function get_title() {
		return __( 'Account Content', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-my-account-content';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-my-account-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'myaccount' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-woo-builder-myaccount-content-endpoints/css-scheme',
			[
				'forms_headings'              => '.elementor-jet-myaccount-content  h3',
				'forms_labels'                => '.elementor-jet-myaccount-content form:not(.woocommerce-EditAccountForm) .form-row label',
				'forms_fields'                => '.elementor-jet-myaccount-content form:not(.woocommerce-EditAccountForm) .form-row',
				'forms_inputs'                => '.elementor-jet-myaccount-content form:not(.woocommerce-EditAccountForm) .form-row .woocommerce-input-wrapper > *:not(.woocommerce-password-strength):not(.woocommerce-password-hint):not(.show-password-input)',
				'forms_buttons'               => '.elementor-jet-myaccount-content form:not(.woocommerce-EditAccountForm) .button',
				'order_status'                => '.elementor-jet-myaccount-content mark',
				'downloads_title'             => '.elementor-jet-myaccount-content .woocommerce-order-downloads .woocommerce-order-downloads__title',
				'downloads_table_heading'     => '.elementor-jet-myaccount-content > :not(.jet-woo-account-downloads-content) .woocommerce-table.woocommerce-table--order-downloads thead th',
				'downloads_table_cell'        => '.elementor-jet-myaccount-content > :not(.jet-woo-account-downloads-content) .woocommerce-table.woocommerce-table--order-downloads tbody tr td',
				'downloads_button'            => '.elementor-jet-myaccount-content > :not(.jet-woo-account-downloads-content) .woocommerce-table.woocommerce-table--order-downloads .download-file a.woocommerce-MyAccount-downloads-file',
				'order_details_title'         => '.elementor-jet-myaccount-content .woocommerce-order-details .woocommerce-order-details__title',
				'order_details_table_heading' => '.elementor-jet-myaccount-content .woocommerce-order-details .woocommerce-table.order_details tr th',
				'order_details_table_cell'    => '.elementor-jet-myaccount-content .woocommerce-order-details .woocommerce-table.shop_table.order_details tr td',
				'order_details_button'        => '.elementor-jet-myaccount-content .woocommerce-order-details .order-again .button',
				'address_heading'             => '.elementor-jet-myaccount-content .woocommerce-customer-details .woocommerce-column__title',
				'address_content'             => '.elementor-jet-myaccount-content .woocommerce-customer-details address',
			]
		);

		$this->start_controls_section(
			'my_account_content',
			[
				'label' => __( 'General', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'my_account_content_info',
			[
				'raw'             => __( 'Use this widget for display My Account Page Endpoints.', 'jet-woo-builder' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->end_controls_section();

		// Address form style controls section.
		$this->start_controls_section(
			'my_account_addresses_forms_styles_section',
			[
				'label' => __( 'Addresses Forms', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'my_account_addresses_forms_headings_heading',
			[
				'label' => __( 'Headings', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		jet_woo_builder_common_controls()->register_heading_style_controls( $this, 'my_account_addresses_forms', $css_scheme['forms_headings'] );

		$this->add_control(
			'my_account_addresses_forms_labels_heading',
			[
				'label'     => __( 'Labels', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		jet_woo_builder_common_controls()->register_label_style_controls( $this, 'my_account_addresses_labels', $css_scheme['forms_labels'] );

		$this->add_control(
			'my_account_addresses_forms_fields_heading',
			[
				'label'     => __( 'Fields', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'my_account_addresses_forms_col_gap',
			[
				'label'      => __( 'Columns Gap', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [ 'px' => 0 ],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['forms_fields'] => 'padding-left: calc( {{SIZE}}{{UNIT}}/2 ); padding-right: calc( {{SIZE}}{{UNIT}}/2 ); margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				],
			]
		);

		$this->add_responsive_control(
			'my_account_addresses_forms_form_row_gap',
			[
				'label'      => __( 'Rows Gap', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'default'    => [ 'px' => 0 ],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['forms_fields'] => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		jet_woo_builder_common_controls()->register_input_style_controls( $this, 'my_account_addresses_inputs', $css_scheme['forms_inputs'], false );

		$this->add_control(
			'my_account_addresses_forms_buttons_heading',
			[
				'label'     => __( 'Buttons', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'my_account_addresses_buttons', $css_scheme['forms_buttons'] );

		$this->end_controls_section();

		// View order endpoint status style controls section.
		$this->start_controls_section(
			'my_account_order_details_status_styles_section',
			[
				'label' => __( 'Order Status', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'my_account_order_details_status_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['order_status'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'my_account_order_details_status_background_color',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['order_status'] => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		// View order endpoint downloads style controls section.
		$this->start_controls_section(
			'my_account_order_details_downloads_styles_section',
			[
				'label' => __( 'Order Downloads', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'my_account_order_details_downloads_title_heading',
			[
				'label' => __( 'Heading', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		jet_woo_builder_common_controls()->register_heading_style_controls( $this, 'my_account_order_details_downloads_header', $css_scheme['downloads_title'] );

		$this->add_control(
			'my_account_order_details_downloads_table_heading_heading',
			[
				'label'     => __( 'Table Heading', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'my_account_order_details_downloads_heading_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['downloads_table_heading'],
			]
		);

		$this->add_control(
			'my_account_order_details_downloads_heading_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['downloads_table_heading'] => 'color: {{VALUE}}',
				],
			]
		);

		jet_woo_builder_common_controls()->register_table_cell_style_controls( $this, 'my_account_order_details_downloads_heading', $css_scheme['downloads_table_heading'] );

		$this->add_control(
			'my_account_order_details_downloads_table_cell_heading',
			[
				'label'     => __( 'Table Cell', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'my_account_order_details_downloads_cell_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['downloads_table_cell'],
			]
		);

		$this->add_control(
			'my_account_order_details_downloads_cell_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['downloads_table_cell'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'my_account_order_details_downloads_cell_link_heading',
			[
				'label' => __( 'Links', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->start_controls_tabs( 'my_account_order_details_downloads_cell_link_styles_tabs' );

		$this->start_controls_tab(
			'my_account_order_details_downloads_cell_link_styles_normal_tab',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'my_account_order_details_downloads_cell_order_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['downloads_table_cell'] . ' a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'my_account_order_details_downloads_cell_link_styles_hover_tab',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'my_account_order_details_downloads_cell_order_hover_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['downloads_table_cell'] . ' a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		jet_woo_builder_common_controls()->register_table_cell_style_controls( $this, 'my_account_order_details_downloads_cell', $css_scheme['downloads_table_cell'] );

		$this->add_control(
			'my_account_order_details_downloads_button_heading',
			[
				'label'     => __( 'Button', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'my_account_order_details_downloads', $css_scheme['downloads_button'] );

		$this->end_controls_section();

		// View order endpoint order table style controls section.
		$this->start_controls_section(
			'my_account_order_details_order_styles_section',
			[
				'label' => __( 'Order Table', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'my_account_order_details_order_title_heading',
			[
				'label' => __( 'Heading', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		jet_woo_builder_common_controls()->register_heading_style_controls( $this, 'my_account_order_details_order', $css_scheme['order_details_title'] );

		$this->add_control(
			'my_account_order_details_table_heading_heading',
			[
				'label'     => __( 'Table Headings', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'my_account_order_details_table_heading_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['order_details_table_heading'],
			]
		);

		$this->add_control(
			'my_account_order_details_table_heading_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['order_details_table_heading'] => 'color: {{VALUE}}',
				],
			]
		);

		jet_woo_builder_common_controls()->register_table_cell_style_controls( $this, 'my_account_order_details_heading', $css_scheme['order_details_table_heading'] );

		$this->add_control(
			'my_account_order_details_table_cells_heading',
			[
				'label'     => __( 'Table Cells', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'my_account_order_details_cell_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['order_details_table_cell'],
			]
		);

		$this->add_control(
			'my_account_order_details_cell_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['order_details_table_cell']                        => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['order_details_table_cell'] . ' .product-quantity' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'my_account_order_details_cell_link_heading',
			[
				'label' => __( 'Links', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->start_controls_tabs( 'my_account_order_details_cell_link_styles_tabs' );

		$this->start_controls_tab(
			'my_account_order_details_cell_link_styles_normal_tab',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'my_account_order_details_cell_link_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['order_details_table_cell'] . ' a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'my_account_order_details_cell_link_styles_hover_tab',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'my_account_order_details_cell_link_hover_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['order_details_table_cell'] . ' a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		jet_woo_builder_common_controls()->register_table_cell_style_controls( $this, 'my_account_order_details_cell', $css_scheme['order_details_table_cell'] );

		$this->end_controls_section();

		// View order endpoint order action button style controls section.
		$this->start_controls_section(
			'my_account_order_details_action_button_styles',
			[
				'label' => __( 'Order Action Button', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'my_account_order_details_action', $css_scheme['order_details_button'] );

		$this->add_responsive_control(
			'my_account_order_details_action_button_align',
			[
				'label'     => esc_html__( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => [
					'{{WRAPPER}} .elementor-jet-myaccount-content .woocommerce-order-details .order-again' => 'text-align: {{VALUE}}',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->end_controls_section();

		// View order endpoint addresses style controls section.
		$this->start_controls_section(
			'my_account_order_details_address_styles_section',
			[
				'label' => __( 'Order Address', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'my_account_order_details_address_title_heading',
			[
				'label' => __( 'Heading', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		jet_woo_builder_common_controls()->register_heading_style_controls( $this, 'my_account_order_details_address', $css_scheme['address_heading'] );

		$this->add_control(
			'my_account_order_details_address_content_heading',
			[
				'label'     => __( 'Content', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'my_account_order_details_address_content_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['address_content'],
			]
		);

		$this->add_control(
			'my_account_order_details_address_content_color',
			[
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['address_content'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'my_account_order_details_address_content_background_color',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['address_content'] => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'my_account_order_details_address_content_border',
				'label'     => esc_html__( 'Border', 'jet-woo-builder' ),
				'selector'  => '{{WRAPPER}} ' . $css_scheme['address_content'],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'my_account_order_details_address_content_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['address_content'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'after',
			]
		);

		$this->add_responsive_control(
			'my_account_order_details_address_content_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['address_content'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'my_account_order_details_address_content_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['address_content'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'my_account_order_details_address_content_align',
			[
				'label'     => esc_html__( 'Text Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['address_content'] => 'text-align: {{VALUE}}',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$this->__open_wrap();

		do_action( 'woocommerce_account_content' );

		$this->__close_wrap();

	}

}
