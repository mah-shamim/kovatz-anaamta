<?php
/**
 * Class: Jet_Woo_Taxonomy_Tiles
 * Name: Taxonomy Tiles
 * Slug: jet-woo-taxonomy-tiles
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

class Jet_Woo_Taxonomy_Tiles extends Jet_Woo_Builder_Base {

	public $__current_tax_count = 0;

	public function get_name() {
		return 'jet-woo-taxonomy-tiles';
	}

	public function get_title() {
		return __( 'Taxonomy Tiles', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-taxonomy-tiles';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-taxonomy-tiles-widget-overview/';
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/taxonomy-tiles/css-scheme',
			[
				'wrap'       => '.jet-woo-taxonomy-tiles',
				'box'        => '.jet-woo-taxonomy-item__box',
				'box-inner'  => '.jet-woo-taxonomy-item__box-inner',
				'title'      => '.jet-woo-taxonomy-item__box-title',
				'count'      => '.jet-woo-taxonomy-item__box-count',
				'desc'       => '.jet-woo-taxonomy-item__box-description',
				'terms_link' => '.jet-woo-taxonomy-item__box-link',
			]
		);

		$layout_data       = $this->taxonomy_tiles_layout_data();
		$available_layouts = [];

		foreach ( $layout_data as $key => $data ) {
			$available_layouts[ $key ] = [
				'title' => $data['label'],
				'icon'  => $data['icon'],
			];
		}

		$this->start_controls_section(
			'section_general_style',
			[
				'label' => __( 'Taxonomy Tiles', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'layout',
			[
				'label'       => __( 'Layout', 'jet-woo-builder' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'default'     => '2-1-2',
				'options'     => $available_layouts,
				'render_type' => 'template',
				'classes'     => 'jet-woo-builder-layout-control',
			]
		);

		$this->add_responsive_control(
			'taxonomy_min_height',
			[
				'label'      => __( 'Min Height', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1200,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 300,
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['wrap'] => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'main_img_width',
			[
				'label'      => __( 'Featured Box Width', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => 50,
				],
				'selectors'  => apply_filters( 'jet-woo-builder/taxonomy-tiles/main-image-selectors', [
					'{{WRAPPER}} .jet-woo-taxonomy-tiles--layout-2-1-2'   => 'grid-template-columns: 1fr {{SIZE}}{{UNIT}} 1fr; -ms-grid-columns: 1fr {{SIZE}}{{UNIT}} 1fr;',
					'{{WRAPPER}} .jet-woo-taxonomy-tiles--layout-1-1-2-h' => 'grid-template-columns: {{SIZE}}{{UNIT}} 1fr 1fr; -ms-grid-columns: {{SIZE}}{{UNIT}} 1fr 1fr;',
					'{{WRAPPER}} .jet-woo-taxonomy-tiles--layout-1-1-2-v' => 'grid-template-columns: {{SIZE}}{{UNIT}} 1fr 1fr; -ms-grid-columns: {{SIZE}}{{UNIT}} 1fr 1fr;',
					'{{WRAPPER}} .jet-woo-taxonomy-tiles--layout-1-2'     => 'grid-template-columns: {{SIZE}}{{UNIT}} 1fr; -ms-grid-columns: {{SIZE}}{{UNIT}} 1fr',
					'{{WRAPPER}} .jet-woo-taxonomy-tiles--layout-1-2-2'   => 'grid-template-columns: {{SIZE}}{{UNIT}} 1fr 1fr; -ms-grid-columns: {{SIZE}}{{UNIT}} 1fr 1fr;',
				] ),
				'condition'  => [
					'layout' => apply_filters(
						'jet-woo-builder/taxonomy-tiles/main-image-conditions',
						[ '2-1-2', '1-1-2-h', '1-1-2-v', '1-2', '1-2-2' ]
					),
				],
			]
		);

		$this->add_control(
			'rows_num',
			[
				'label'     => __( 'Rows Number', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 1,
				'options'   => jet_woo_builder_tools()->get_select_range( 3 ),
				'condition' => [
					'layout' => [ '2-x', '3-x', '4-x' ],
				],
			]
		);

		$this->add_control(
			'open_new_tab',
			[
				'label' => __( 'Open in New Window', 'jet-woo-builder' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'taxonomy',
			[
				'label'     => __( 'Taxonomy Type', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'product_cat',
				'options'   => apply_filters( 'jet-woo-builder/jet-woo-taxonomy-tiles/taxonomy_options', [
					'product_tag' => __( 'Tags', 'jet-woo-builder' ),
					'product_cat' => __( 'Categories', 'jet-woo-builder' ),
				] ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_taxonomy_by_id',
			[
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Include by IDs', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'taxonomy_id',
			[
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'label'       => __( 'Set comma separated IDs list (10, 22, 19 etc.).', 'jet-woo-builder' ),
				'condition'   => [
					'show_taxonomy_by_id' => 'yes',
				],
			]
		);

		$this->add_control(
			'exclude_taxonomy_by_id',
			[
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Exclude by IDs', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'exclude_taxonomy_id',
			[
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'label'       => __( 'Set comma separated IDs list (10, 22, 19 etc.).', 'jet-woo-builder' ),
				'condition'   => [
					'exclude_taxonomy_by_id' => 'yes',
				],
			]
		);

		$this->add_control(
			'sort_by',
			[
				'type'    => 'select',
				'label'   => __( 'Order by', 'jet-woo-builder' ),
				'default' => 'name',
				'options' => [
					'name'  => __( 'Name', 'jet-woo-builder' ),
					'id'    => __( 'IDs', 'jet-woo-builder' ),
					'count' => __( 'Count', 'jet-woo-builder' ),
					'rand'  => __( 'Random', 'jet-woo-builder' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'type'      => 'select',
				'label'     => __( 'Order', 'jet-woo-builder' ),
				'default'   => 'asc',
				'options'   => jet_woo_builder_tools()->order_arr(),
				'condition' => [
					'sort_by!' => 'rand',
				],
			]
		);

		$this->add_control(
			'hide_empty',
			[
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Hide Empty', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'hide_default_cat',
			[
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Hide Uncategorized', 'jet-woo-builder' ),
				'condition' => [
					'taxonomy' => 'product_cat',
				],
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label'     => __( 'Title HTML Tag', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h5',
				'options'   => jet_woo_builder_tools()->get_available_title_html_tags(),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_length',
			[
				'label'       => __( 'Title Length', 'jet-woo-builder' ),
				'description' => __( 'Set -1 to show full title and 0 to hide it.', 'jet-woo-builder' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => -1,
				'min'         => -1,
				'max'         => 15,
				'step'        => 1,
			]
		);

		$this->add_control(
			'desc_length',
			[
				'label'       => __( 'Description Length', 'jet-woo-builder' ),
				'description' => __( 'Set -1 to show full description and 0 to hide it.', 'jet-woo-builder' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 50,
				'min'         => -1,
				'max'         => 200,
				'step'        => 1,
			]
		);

		$this->add_control(
			'show_taxonomy_count',
			[
				'type'    => Controls_Manager::SWITCHER,
				'label'   => __( 'Show Counts', 'jet-woo-builder' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'count_displaying',
			[
				'label'     => __( 'Position', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'in-content',
				'options'   => [
					'in-content'     => __( 'In content', 'jet-woo-builder' ),
					'out-of-content' => __( 'Out of content', 'jet-woo-builder' ),
				],
				'condition' => [
					'show_taxonomy_count' => 'yes',
				],
			]
		);

		$this->add_control(
			'count_before_text',
			[
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Before Count', 'jet-woo-builder' ),
				'default'   => '(',
				'condition' => [
					'show_taxonomy_count' => 'yes',
				],
			]
		);

		$this->add_control(
			'count_after_text',
			[
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'After Count', 'jet-woo-builder' ),
				'default'   => ')',
				'condition' => [
					'show_taxonomy_count' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_box_style',
			[
				'label' => __( 'Tile', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'boxes_gap',
			[
				'label'      => __( 'Gap', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 1,
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['wrap']        => 'grid-column-gap: {{SIZE}}{{UNIT}}; grid-row-gap: {{SIZE}}{{UNIT}};',
					'(mobile){{WRAPPER}} ' . $css_scheme['box'] => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'boxes_border',
				'label'    => __( 'Border', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['box'],
			]
		);

		$this->add_responsive_control(
			'boxes_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['box'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'boxes_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['box'],
			]
		);

		$this->add_responsive_control(
			'boxes_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['box'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'boxes_text_alignment_h',
			[
				'label'     => __( 'Horizontal Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => [
					'left'   => __( 'Left', 'jet-woo-builder' ),
					'center' => __( 'Center', 'jet-woo-builder' ),
					'right'  => __( 'Right', 'jet-woo-builder' ),
				],
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['box'] => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'boxes_text_alignment_v',
			[
				'label'     => __( 'Vertical Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'flex-start',
				'options'   => [
					'flex-start' => __( 'Top', 'jet-woo-builder' ),
					'center'     => __( 'Center', 'jet-woo-builder' ),
					'flex-end'   => __( 'Bottom', 'jet-woo-builder' ),
				],
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['box'] => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'boxes_overlay_styles',
			[
				'label'     => __( 'Overlay', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_overlay_style' );

		$this->start_controls_tab(
			'tab_overlay_normal',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'boxes_overlay_background_normal',
				'fields_options' => [
					'image' => [
						'dynamic' => [
							'active' => false,
						],
					],
				],
				'selector'       => '{{WRAPPER}} ' . $css_scheme['box'] . ':before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_overlay_hover',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'boxes_overlay_background_hover',
				'fields_options' => [
					'image' => [
						'dynamic' => [
							'active' => false,
						],
					],
				],
				'selector'       => '{{WRAPPER}} ' . $css_scheme['box'] . ':hover:before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'boxes_hover_shadow',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['box'] . ':hover',
				'condition' => [
					'boxes_box_shadow_box_shadow_type!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_content_style' );

		$this->start_controls_tab(
			'tab_content_normal_style',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'content_background',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['box-inner'] => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_content_hover_style',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'box_hover_content_bg',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['box'] . ':hover .jet-woo-taxonomy-item__box-inner' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'content_hover_border_color',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['box'] . ':hover .jet-woo-taxonomy-item__box-inner' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'content_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'content_border',
				'label'     => __( 'Border', 'jet-woo-builder' ),
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['box-inner'],
			]
		);

		$this->add_responsive_control(
			'content_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['box-inner'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['box-inner'],
			]
		);

		$this->add_responsive_control(
			'content_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['box-inner'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'boxes_title_styles',
			[
				'label'     => __( 'Title', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'boxes_title_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['title'],
			]
		);

		$this->start_controls_tabs( 'tabs_content_title_style' );

		$this->start_controls_tab(
			'tab_content_title_normal_style',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'boxes_title_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['title'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_content_title_hover_style',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'boxes_hover_title_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['box'] . ':hover .jet-woo-taxonomy-item__box-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'boxes_hover_title_decoration',
			[
				'label'     => __( 'Text Decoration', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => jet_woo_builder_tools()->get_available_text_decoration_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['box'] . ':hover .jet-woo-taxonomy-item__box-title' => 'text-decoration: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'boxes_title_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['title'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'boxes_count_styles',
			[
				'label'     => __( 'Count', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'boxes_count_display',
			[
				'label'     => __( 'Display Type', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => jet_woo_builder_tools()->get_available_display_types(),
				'default'   => 'inline-block',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['count'] => 'display: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['title'] => 'display: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'boxes_count_position',
			[
				'label'     => __( 'Position', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'top-left'     => __( 'Top Left', 'jet-woo-builder' ),
					'top-right'    => __( 'Top Right', 'jet-woo-builder' ),
					'bottom-left'  => __( 'Bottom Left', 'jet-woo-builder' ),
					'bottom-right' => __( 'Bottom Right', 'jet-woo-builder' ),
				],
				'default'   => 'top-right',
				'condition' => [
					'count_displaying' => 'out-of-content',
				],
			]
		);

		$this->add_responsive_control(
			'boxes_count_min_width',
			[
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['count'] => 'min-width: {{SIZE}}{{UNIT}}; text-align: center;',
				],
				'condition'  => [
					'count_displaying' => 'out-of-content',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'boxes_count_typography',
				'selector' => '{{WRAPPER}}  ' . $css_scheme['count'],
			]
		);

		$this->start_controls_tabs( 'tabs_content_count_style' );

		$this->start_controls_tab(
			'tab_content_count_normal_style',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'boxes_count_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['count'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'boxes_count_bg',
			[
				'label'     => __( 'Background', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['count'] => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_content_count_hover_style',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'boxes_hover_count_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['box'] . ':hover .jet-woo-taxonomy-item__box-count' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'box_hover_count_bg',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['box'] . ':hover .jet-woo-taxonomy-item__box-count' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'boxes_count_hover_border_color',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['box'] . ':hover .jet-woo-taxonomy-item__box-count' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'boxes_count_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'box_hover_count_decoration',
			[
				'label'     => __( 'Text Decoration', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => jet_woo_builder_tools()->get_available_text_decoration_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['box'] . ':hover .jet-woo-taxonomy-item__box-count' => 'text-decoration: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'boxes_count_border',
				'label'     => __( 'Border', 'jet-woo-builder' ),
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['count'],
			]
		);

		$this->add_responsive_control(
			'boxes_count_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['count'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'boxes_count_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['count'],
			]
		);

		$this->add_responsive_control(
			'boxes_count_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['count'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'boxes_count_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['count'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'boxes_text_style',
			[
				'label'     => __( 'Description', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'boxes_text_typography',
				'selector' => '{{WRAPPER}}  ' . $css_scheme['desc'],
			]
		);

		$this->start_controls_tabs( 'tabs_content_description_style' );

		$this->start_controls_tab(
			'tab_content_description_normal_style',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'boxes_text_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['desc'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_content_description_hover_style',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'boxes_hover_desc_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['box'] . ':hover .jet-woo-taxonomy-item__box-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'boxes_hover_desc_decoration',
			[
				'label'     => __( 'Text Decoration', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => jet_woo_builder_tools()->get_available_text_decoration_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['box'] . ':hover .jet-woo-taxonomy-item__box-description' => 'text-decoration: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'boxes_text_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['desc'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$query = $this->taxonomy_query();

		if ( empty( $query ) || is_wp_error( $query ) ) {
			echo sprintf( '<h3 class="jet-woo-taxonomy__not-found">%s</h3>', __( 'Taxonomy not found.', 'jet-woo-builder' ) );

			return null;
		}

		$this->__open_wrap();

		echo '<div class="' . $this->get_taxonomy_wrap_classes() . '" dir="ltr">';

		foreach ( $query as $taxonomy ) {
			setup_postdata( $taxonomy );

			include $this->get_template( 'global/taxonomy-tiles.php' );
		}

		wp_reset_postdata();

		echo '</div>';

		$this->__close_wrap();

	}

	/**
	 * Tax wrap classes.
	 *
	 * Add classes for taxonomy wrapper.
	 *
	 * @since  1.13.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_taxonomy_wrap_classes() {

		$settings = $this->get_settings_for_display();

		$classes = [
			'jet-woo-taxonomy-tiles',
			'jet-woo-taxonomy-tiles--layout-' . $settings['layout'],
			'jet-woo-taxonomy-tiles-count--' . $settings['count_displaying'],
		];

		if ( 'out-of-content' === $settings['count_displaying'] ) {
			$classes[] = 'jet-woo-taxonomy-tiles-count--' . $settings['boxes_count_position'];
		}

		if ( $this->is_multirow_layout( $settings['layout'] ) ) {
			$rows      = isset( $settings['rows_num'] ) ? absint( $settings['rows_num'] ) : 1;
			$classes[] = 'rows-' . $rows;
		}

		return implode( ' ', $classes );

	}

	/**
	 * Tax count.
	 *
	 * Return taxonomy count to display for current layout.
	 *
	 * @since  1.13.0
	 * @access public
	 *
	 * @param array $settings Settings list.
	 *
	 * @return float|int
	 */
	public function get_tax_count( $settings ) {

		if ( 0 === $this->__current_tax_count ) {
			$layout         = $settings['layout'];
			$layouts_data   = $this->taxonomy_tiles_layout_data();
			$current_layout = isset( $layouts_data[ $layout ] ) ? $layouts_data[ $layout ] : false;

			if ( ! $current_layout ) {
				return $this->__current_tax_count;
			}

			$this->__current_tax_count = $current_layout['num'];

			if ( $this->is_multirow_layout( $layout ) ) {
				$rows                      = isset( $settings['rows_num'] ) ? absint( $settings['rows_num'] ) : 1;
				$this->__current_tax_count *= $rows;
			}
		}

		return $this->__current_tax_count;

	}

	/**
	 * Tax query.
	 *
	 * Query taxonomy by attributes.
	 *
	 * @since  1.13.0
	 * @since  2.1.6 Fixed exclude functionality.
	 * @access public
	 *
	 * @return object
	 */
	public function taxonomy_query() {

		$settings = $this->get_settings_for_display();
		$num      = $this->get_tax_count( $settings );

		$defaults = apply_filters(
			'jet-woo-builder/jet-woo-taxonomy-tiles/query-args',
			[
				'post_status'  => 'publish',
				'hierarchical' => 1,
			]
		);

		$args = [
			'number'     => $num,
			'orderby'    => $settings['sort_by'],
			'hide_empty' => $settings['hide_empty'],
			'order'      => $settings['order'],
		];

		if ( filter_var( $settings['show_taxonomy_by_id'], FILTER_VALIDATE_BOOLEAN ) ) {
			$args['include'] = $settings['taxonomy_id'];
		}

		$exclude_tax = [];

		if ( filter_var( $settings['exclude_taxonomy_by_id'], FILTER_VALIDATE_BOOLEAN ) ) {
			$exclude_tax = explode( ',', $settings['exclude_taxonomy_id'] );
		}

		if ( 'product_cat' === $settings['taxonomy'] && $settings['hide_default_cat'] ) {
			$exclude_tax[] = get_option( 'default_product_cat', 0 );
		}

		if ( ! empty( $exclude_tax ) ) {
			$args['exclude'] = implode( ',', $exclude_tax );
		}

		$args       = wp_parse_args( $args, $defaults );
		$taxonomies = get_terms( $settings['taxonomy'], $args );

		if ( 'rand' === $args['orderby'] ) {
			$terms_count = count( $taxonomies );

			shuffle( $taxonomies );

			return array_slice( $taxonomies, 0, $terms_count );
		}

		return $taxonomies;

	}

	/**
	 * Taxonomy background.
	 *
	 * Get style attribute with taxonomy background.
	 *
	 * @since  1.13.0
	 * @access public
	 *
	 * @param object $taxonomy Tax instance.
	 *
	 * @return void|null
	 */
	public function get_taxonomy_background( $taxonomy ) {

		$key          = apply_filters( 'jet-woo-builder/jet-woo-taxonomy-tiles/tax_thumbnail', 'thumbnail_id', $taxonomy );
		$thumbnail_id = get_term_meta( $taxonomy->term_id, $key, true );

		if ( $thumbnail_id ) {
			$thumb = wp_get_attachment_url( $thumbnail_id );
		} else {
			$thumb = sprintf( 'http://via.placeholder.com/900x600?text=%s', str_replace( ' ', '+', $taxonomy->name ) );
		}

		printf( 'style="background-image:url(\'%s\')"', $thumb );

	}

	/**
	 * Multirow layout.
	 *
	 * Check if current layout is multirow layout.
	 *
	 * @since  1.13.0
	 * @access public
	 *
	 * @param string $layout Layout type.
	 *
	 * @return boolean
	 */
	public function is_multirow_layout( $layout ) {

		$multirow_layouts = apply_filters(
			'jet-woo-builder/taxonomy-tiles/multirow-layouts',
			[ '2-x', '3-x', '4-x' ]
		);

		return in_array( $layout, $multirow_layouts );

	}

	/**
	 * Tiles layout data.
	 *
	 * Returns information about available taxonomy tiles layouts.
	 *
	 * @since  1.13.0
	 * @access public
	 *
	 * @return array
	 */
	public function taxonomy_tiles_layout_data() {
		return apply_filters( 'jet-woo-builder/taxonomy-tiles/available-layouts', [
			'2-1-2'   => [
				'label'    => __( 'Layout 1 (5 taxonomy)', 'jet-woo-builder' ),
				'icon'     => 'jet-woo-builder-icon jet-woo-taxonomy-tiles-layout-1',
				'num'      => 5,
				'has_rows' => false,
			],
			'1-1-2-h' => [
				'label'    => __( 'Layout 2 (4 taxonomy)', 'jet-woo-builder' ),
				'icon'     => 'jet-woo-builder-icon jet-woo-taxonomy-tiles-layout-2',
				'num'      => 4,
				'has_rows' => false,
			],
			'1-1-2-v' => [
				'label'    => __( 'Layout 3 (4 taxonomy)', 'jet-woo-builder' ),
				'icon'     => 'jet-woo-builder-icon jet-woo-taxonomy-tiles-layout-3',
				'num'      => 4,
				'has_rows' => false,
			],
			'1-2'     => [
				'label'    => __( 'Layout 4 (3 taxonomy)', 'jet-woo-builder' ),
				'icon'     => 'jet-woo-builder-icon jet-woo-taxonomy-tiles-layout-4',
				'num'      => 3,
				'has_rows' => false,
			],
			'2-3-v'   => [
				'label'    => __( 'Layout 5 (5 taxonomy)', 'jet-woo-builder' ),
				'icon'     => 'jet-woo-builder-icon jet-woo-taxonomy-tiles-layout-5',
				'num'      => 5,
				'has_rows' => false,
			],
			'1-2-2'   => [
				'label'    => __( 'Layout 6 (5 taxonomy)', 'jet-woo-builder' ),
				'icon'     => 'jet-woo-builder-icon jet-woo-taxonomy-tiles-layout-6',
				'num'      => 5,
				'has_rows' => false,
			],
			'2-x'     => [
				'label'    => __( 'Layout 7 (2, 4, 6 taxonomy)', 'jet-woo-builder' ),
				'icon'     => 'jet-woo-builder-icon jet-woo-taxonomy-tiles-layout-7',
				'num'      => 2,
				'has_rows' => false,
			],
			'3-x'     => [
				'label'    => __( 'Layout 8 (3, 6, 9 taxonomy)', 'jet-woo-builder' ),
				'icon'     => 'jet-woo-builder-icon jet-woo-taxonomy-tiles-layout-8',
				'num'      => 3,
				'has_rows' => false,
			],
			'4-x'     => [
				'label'    => __( 'Layout 9 (4, 8, 12 taxonomy)', 'jet-woo-builder' ),
				'icon'     => 'jet-woo-builder-icon jet-woo-taxonomy-tiles-layout-9',
				'num'      => 4,
				'has_rows' => false,
			],
		] );
	}

}
