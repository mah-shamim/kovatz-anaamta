<?php
/**
 * Class: Jet_Woo_Builder_MyAccount_Account_Details
 * Name: Account Details
 * Slug: jet-myaccount-account-details
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_MyAccount_Account_Details extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-myaccount-account-details';
	}

	public function get_title() {
		return __( 'Account Details', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-my-account-address';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-my-account-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'myaccount' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-woo-builder-myaccount-account-details/css-scheme',
			[
				'label'    => '.woocommerce-EditAccountForm label',
				'text'     => '.woocommerce-EditAccountForm em',
				'legend'   => '.woocommerce-EditAccountForm legend',
				'fieldset' => '.woocommerce-EditAccountForm fieldset',
				'button'   => '.woocommerce-EditAccountForm .woocommerce-Button',
				'field'    => '.woocommerce-EditAccountForm .form-row',
				'input'    => '.woocommerce-EditAccountForm input',
			]
		);

		$this->start_controls_section(
			'myaccount_details_general_styles',
			[
				'label' => __( 'Description', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'myaccount_details_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['text'],
			)
		);

		$this->add_control(
			'myaccount_details_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['text'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'myaccount_details_label_styles',
			array(
				'label' => esc_html__( 'Label', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_label_style_controls( $this, 'myaccount_details', $css_scheme['label'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'myaccount_details_input_styles',
			[
				'label' => __( 'Fields', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'myaccount_details_form_col_gap',
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
					'{{WRAPPER}} ' . $css_scheme['field'] => 'padding-left: calc( {{SIZE}}{{UNIT}}/2 ); padding-right: calc( {{SIZE}}{{UNIT}}/2 ); margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				],
			]
		);

		$this->add_responsive_control(
			'myaccount_details_form_row_gap',
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
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['field'] => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		jet_woo_builder_common_controls()->register_input_style_controls( $this, 'myaccount_details', $css_scheme['input'], false );

		$this->end_controls_section();

		$this->start_controls_section(
			'myaccount_details_button_styles',
			array(
				'label' => esc_html__( 'Button', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'myaccount_details', $css_scheme['button'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'myaccount_details_fieldset_styles',
			array(
				'label' => esc_html__( 'Fieldset', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'myaccount_details_legend_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['legend'],
			)
		);

		$this->add_control(
			'myaccount_details_legend_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['legend'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'myaccount_details_fieldset_border',
				'selector' => '{{WRAPPER}} ' . $css_scheme['fieldset'],
			)
		);

		$this->add_control(
			'myaccount_details_fieldset_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['fieldset'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'myaccount_details_fieldset_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['fieldset'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'myaccount_details_fieldset_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['fieldset'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$this->__open_wrap();

		include $this->get_template( 'myaccount/account-details.php' );

		$this->__close_wrap();

	}

}
