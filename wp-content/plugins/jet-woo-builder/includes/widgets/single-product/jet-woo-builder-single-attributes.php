<?php
/**
 * Class: Jet_Woo_Builder_Single_Attributes
 * Name: Single Attributes
 * Slug: jet-single-attributes
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Single_Attributes extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-single-attributes';
	}

	public function get_title() {
		return __( 'Single Attributes', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-single-attributes';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-and-set-a-single-product-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'single' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-single-attributes/css-scheme',
			array(
				'title'                => '.jet-woo-builder .jet-single-attrs__title',
				'attributes_table'     => '.jet-woo-builder .shop_attributes',
				'attributes_table_row' => '.jet-woo-builder .shop_attributes tr',
				'attributes_title'     => '.jet-woo-builder .shop_attributes tr > th',
				'attributes_value'     => '.jet-woo-builder .shop_attributes tr > td',
			)
		);

		$this->start_controls_section(
			'section_attrs_content',
			[
				'label' => __( 'Heading', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'enable_attributes_heading',
			[
				'label' => __( 'Enable', 'jet-woo-builder' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'block_title',
			[
				'label'     => __( 'Heading', 'jet-woo-builder' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'enable_attributes_heading' => 'yes',
				],
			]
		);

		$this->add_control(
			'block_title_tag',
			[
				'label'     => __( 'HTML Tag', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => jet_woo_builder_tools()->get_available_title_html_tags(),
				'default'   => 'h2',
				'condition' => [
					'enable_attributes_heading' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_attrs_title_style',
			[
				'label'     => __( 'Heading', 'jet-woo-builder' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_attributes_heading' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'attrs_title_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['title'],
			]
		);

		$this->add_control(
			'attrs_title_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'attrs_title_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['title'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'attrs_title_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['title'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'attrs_title_alignment',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types( true ),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['title'] => 'text-align: {{VALUE}};',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_attrs_table_style',
			[
				'label' => __( 'Attributes', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'section_attrs_table_width',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'      => [
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 100,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['attributes_table'] => 'max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'section_attrs_table_row_color',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['attributes_table_row'] . ' th' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['attributes_table_row'] . ' td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_attrs_table_striped_row',
			[
				'label' => __( 'Striped', 'jet-woo-builder' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'section_attrs_table_even_row_color',
			[
				'label'     => __( 'Even Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['attributes_table_row'] . ':nth-child(even) th' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['attributes_table_row'] . ':nth-child(even) td' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'section_attrs_table_striped_row' => 'yes',
				],
			]
		);

		$this->add_control(
			'heading_attrs_table_title',
			[
				'label'     => __( 'Table Heading', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'attrs_table_title_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['attributes_title'],
			]
		);

		$this->add_control(
			'attrs_table_title_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['attributes_title'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'attrs_table_title_background',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['attributes_title'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'attrs_table_title_border',
				'label'          => __( 'Border', 'jet-woo-builder' ),
				'selector'       => '{{WRAPPER}} ' . $css_scheme['attributes_title'],
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'attrs_table_title_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['attributes_title'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'attrs_table_title_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['attributes_title'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'attrs_table_title_width',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'      => [
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 100,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['attributes_title'] => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'attrs_table_title_alignment',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['attributes_title'] => 'text-align: {{VALUE}};',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->add_responsive_control(
			'attrs_table_title_vertical_alignment',
			[
				'label'     => esc_html__( 'Vertical Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'middle',
				'options'   => jet_woo_builder_tools()->get_available_v_align_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['attributes_title'] => 'vertical-align: {{VALUE}};',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->add_control(
			'heading_attrs_table_value',
			[
				'label'     => __( 'Table Cell', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'attrs_table_value_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['attributes_value'],
			]
		);

		$this->add_control(
			'attrs_table_value_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['attributes_value'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'attrs_table_value_background',
			array(
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['attributes_value'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'attrs_table_value_border',
				'label'          => __( 'Border', 'jet-woo-builder' ),
				'selector'       => '{{WRAPPER}} ' . $css_scheme['attributes_value'],
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'attrs_table_value_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['attributes_value'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'attrs_table_value_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['attributes_value'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'attrs_table_value_width',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'      => [
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 100,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['attributes_value'] => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'attrs_table_value_alignment',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['attributes_value'] => 'text-align: {{VALUE}};',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->add_responsive_control(
			'attrs_table_value_vertical_alignment',
			[
				'label'     => esc_html__( 'Vertical Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'middle',
				'options'   => jet_woo_builder_tools()->get_available_v_align_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['attributes_value'] => 'vertical-align: {{VALUE}};',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		if ( $this->__set_editor_product() ) {
			$this->__open_wrap();

			include $this->get_template( 'single-product/attributes.php' );

			$this->__close_wrap();

			if ( jet_woo_builder()->elementor_views->in_elementor() ) {
				$this->__reset_editor_product();
			}
		}
	}

}
