<?php

namespace Elementor;

use Elementor\Group_Control_Border;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Jet_Smart_Filters_Pagination_Widget extends Widget_Base {

	public function get_name() {

		return 'jet-smart-filters-pagination';
	}

	public function get_title() {

		return __( 'Pagination', 'jet-smart-filters' );
	}

	public function get_icon() {

		return 'jet-smart-filters-icon-pagination';
	}

	public function get_help_url() {

		return jet_smart_filters()->widgets->prepare_help_url(
			'https://crocoblock.com/knowledge-base/articles/jetsmartfilters-how-to-use-ajax-pagination/',
			$this->get_name()
		);
	}

	public function get_categories() {

		return array( jet_smart_filters()->widgets->get_category() );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/pagination/css-scheme',
			array(
				'container'               => '.jet-smart-filters-pagination',
				'pagination'              => '.jet-filters-pagination',
				'pagination-item'         => '.jet-filters-pagination__item',
				'pagination-link'         => '.jet-filters-pagination__link',
				'pagination-link-current' => '.jet-filters-pagination__current .jet-filters-pagination__link',
				'pagination-dots'         => '.jet-filters-pagination__dots',
				'pagination-load-more'    => '.jet-filters-pagination__load-more .jet-filters-pagination__link',
			)
		);

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'content_provider',
			array(
				'label'   => __( 'Pagination for:', 'jet-smart-filters' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => jet_smart_filters()->data->content_providers(),
			)
		);

		$this->add_control(
			'epro_posts_notice',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Please set <b>jet-smart-filters</b> into Query ID option of Posts widget you want to filter', 'jet-smart-filters' ),
				'condition' => array(
					'content_provider' => array( 'epro-posts', 'epro-portfolio' ),
				),
			)
		);

		$this->add_control(
			'apply_type',
			array(
				'label'   => __( 'Apply type', 'jet-smart-filters' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ajax',
				'options' => array(
					'ajax'   => __( 'AJAX', 'jet-smart-filters' ),
					'reload' => __( 'Page reload', 'jet-smart-filters' ),
					'mixed'  => __( 'Mixed', 'jet-smart-filters' ),
				),
			)
		);

		$this->add_control(
			'query_id',
			array(
				'label'       => esc_html__( 'Query ID', 'jet-smart-filters' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Set unique query ID if you use multiple widgets of same provider on the page. Same ID you need to set for filtered widget.', 'jet-smart-filters' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_controls',
			array(
				'label' => __( 'Controls', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'enable_items',
			array(
				'label'        => esc_html__( 'Enable Items', 'jet-smart-filters' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => '',
				'label_on'     => esc_html__( 'Yes', 'jet-smart-filters' ),
				'label_off'    => esc_html__( 'No', 'jet-smart-filters' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'pages_center_offset',
			array(
				'label'       => esc_html__( 'Items center offset', 'jet-smart-filters' ),
				'description' => esc_html__( 'Set number of items to either side of current page, not including current page.Set 0 to show all items.', 'jet-smart-filters' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 50,
				'step'        => 1,
				'condition'   => array(
					'enable_items' => 'yes',
				),
			)
		);

		$this->add_control(
			'pages_end_offset',
			array(
				'label'       => esc_html__( 'Items edge offset', 'jet-smart-filters' ),
				'description' => esc_html__( 'Set number of items on either the start and the end list edges.', 'jet-smart-filters' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 50,
				'step'        => 1,
				'condition'   => array(
					'enable_items' => 'yes',
				),
			)
		);

		$this->add_control(
			'enable_prev_next',
			array(
				'label'        => esc_html__( 'Enable Prev/Next', 'jet-smart-filters' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => '',
				'label_on'     => esc_html__( 'Yes', 'jet-smart-filters' ),
				'label_off'    => esc_html__( 'No', 'jet-smart-filters' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before'
			)
		);

		$this->add_control(
			'prev_text',
			array(
				'label'     => esc_html__( 'Prev Text', 'jet-smart-filters' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Prev', 'jet-smart-filters' ),
				'condition' => array(
					'enable_prev_next' => 'yes',
				),
			)
		);

		$this->add_control(
			'next_text',
			array(
				'label'     => esc_html__( 'Next Text', 'jet-smart-filters' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Next', 'jet-smart-filters' ),
				'condition' => array(
					'enable_prev_next' => 'yes',
				),
			)
		);

		$this->add_control(
			'enable_load_more',
			array(
				'label'        => esc_html__( 'Enable Load More', 'jet-smart-filters' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => '',
				'label_on'     => esc_html__( 'Yes', 'jet-smart-filters' ),
				'label_off'    => esc_html__( 'No', 'jet-smart-filters' ),
				'return_value' => 'yes',
				'separator'    => 'before'
			)
		);

		$this->add_control(
			'load_more_text',
			array(
				'label'     => esc_html__( 'Load More Text', 'jet-smart-filters' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Load More', 'jet-smart-filters' ),
				'condition' => array(
					'enable_load_more' => 'yes',
				),
			)
		);

		$this->add_control(
			'autoscroll',
			array(
				'label'        => esc_html__( 'Enable autoscroll', 'jet-smart-filters' ),
				'description'  => esc_html__( 'Autoscroll to top of the provider when reloading content via AJAX.', 'jet-smart-filters' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-smart-filters' ),
				'label_off'    => esc_html__( 'No', 'jet-smart-filters' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
				'condition'    => array(
					'apply_type' => array( 'ajax', 'mixed' ),
				),
				'render_type'  => 'none'
			)
		);

		$this->add_control(
			'provider_top_offset',
			array(
				'label'       => esc_html__( 'Provider top offset', 'jet-smart-filters' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 999,
				'step'        => 1,
				'condition'   => array(
					'autoscroll' => 'yes',
				),
				'render_type'  => 'none'
			)
		);

		$this->end_controls_section();

		$this->controls_section_pagination( $css_scheme );
	}

	protected function controls_section_pagination( $css_scheme ) {

		$this->start_controls_section(
			'pagination_style',
			array(
				'label'      => esc_html__( 'Pagination', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'pagination_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'pagination_border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['pagination'],
			)
		);

		$this->add_control(
			'pagination_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['pagination'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pagination_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['pagination'],
			)
		);

		$this->add_responsive_control(
			'pagination_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} '. $css_scheme['pagination'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['container'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pagination_items_style',
			array(
				'label'      => esc_html__( 'Items', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pagination_items_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['pagination-link'] . ', {{WRAPPER}} ' . $css_scheme['pagination-dots'],
			)
		);

		$this->start_controls_tabs( 'tabs_pagination_items_style' );

		$this->start_controls_tab(
			'pagination_items_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'pagination_items_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] => 'background-color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_items_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_items_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'pagination_items_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] . ':hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_items_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] . ':hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_items_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'pagination_items_border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] . ':hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_items_active',
			array(
				'label' => esc_html__( 'Current', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'pagination_items_bg_color_active',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link-current'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_items_color_active',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link-current'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_items_active_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'pagination_items_border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link-current'] => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_items_dots',
			array(
				'label' => esc_html__( 'Dots', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'pagination_items_bg_color_dots',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_items_color_dots',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_items_dots_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'pagination_items_border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-item'] . ' ' . $css_scheme['pagination-dots'] => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'pagination_items_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'top'      => 10,
					'right'    => 10,
					'bottom'   => 10,
					'left'     => 10,
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_items_horizontal_gap',
			array(
				'label'       => esc_html__( 'Horizontal Gap Between Items', 'jet-smart-filters' ),
				'label_block' => true,
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'default'     => array(
					'unit' => 'px',
					'size' => 6,
				),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-item'] => 'margin-right: calc({{SIZE}}{{UNIT}}/2); margin-left: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} ' . $css_scheme['pagination']      => 'margin-right: calc(-{{SIZE}}{{UNIT}}/2); margin-left: calc(-{{SIZE}}{{UNIT}}/2);',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_items_vertical_gap',
			array(
				'label'       => esc_html__( 'Vertical Gap Between Items', 'jet-smart-filters' ),
				'label_block' => true,
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'default'     => array(
					'unit' => 'px',
					'size' => 6,
				),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-item'] => 'margin-top: calc({{SIZE}}{{UNIT}}/2); margin-bottom: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} ' . $css_scheme['pagination']      => 'margin-top: calc(-{{SIZE}}{{UNIT}}/2); margin-bottom: calc(-{{SIZE}}{{UNIT}}/2)',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'pagination_items_border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'placeholder' => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['pagination-link'] . ', {{WRAPPER}} ' . $css_scheme['pagination-dots'],
			)
		);

		$this->add_responsive_control(
			'pagination_items_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pagination_items_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'default'   => 'center',
				'options'   => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination'] => 'justify-content: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pagination_load_more_style',
			array(
				'label'      => esc_html__( 'Load More', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pagination_load_more_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['pagination-load-more'],
			)
		);

		$this->start_controls_tabs( 'tabs_pagination_load_more_style' );

		$this->start_controls_tab(
			'pagination_load_more_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'pagination_load_more_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-load-more'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_load_more_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-load-more'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_load_more_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'pagination_load_more_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-load-more'] . ':hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_load_more_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-load-more'] . ':hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_load_more_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'pagination_load_more_border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-load-more'] . ':hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'pagination_load_more_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-load-more'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_load_more_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-filters-pagination__load-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'pagination_load_more_border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'placeholder' => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['pagination-load-more'] . ', {{WRAPPER}} ' . $css_scheme['pagination-dots'],
			)
		);

		$this->add_responsive_control(
			'pagination_load_more_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-load-more'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pagination_load_more_position',
			array(
				'label'   => esc_html__( 'Position', 'jet-smart-filters' ),
				'type'    => Controls_Manager::CHOOSE,
				'toggle'  => false,
				'default' => 'right',
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'eicon-order-start',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'selectors_dictionary' => array(
					'left'  => 'order: -1;',
					'right' => 'order: initial;',
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-filters-pagination__load-more' => '{{VALUE}}',
				)
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Returns CSS selector for nested element
	 */
	public function css_selector( $el = null ) {

		return sprintf( '{{WRAPPER}} .%1$s%2$s', $this->get_name(), $el );
	}

	protected function render() {

		jet_smart_filters()->set_filters_used();

		$base_class        = $this->get_name();
		$settings          = $this->get_settings();
		$content_provider  = $settings['content_provider'];
		$apply_type        = $settings['apply_type'];
		$query_id          = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
		$items_enabled     = isset( $settings['enable_items'] ) ? $settings['enable_items'] : '';
		$nav_enabled       = isset( $settings['enable_prev_next'] ) ? $settings['enable_prev_next'] : '';
		$load_more_enabled = isset( $settings['enable_load_more'] ) ? $settings['enable_load_more'] : '';
		$controls          = array();

		if ( 'yes' === $items_enabled ) {
			$controls['items_enabled']  = true;
			$controls['pages_mid_size'] = ! empty( $settings['pages_center_offset'] ) ? absint( $settings['pages_center_offset'] ) : 0;
			$controls['pages_end_size'] = ! empty( $settings['pages_end_offset'] ) ? absint( $settings['pages_end_offset'] ) : 0;
		} else {
			$controls['items_enabled'] = false;
		}

		if ( 'yes' === $nav_enabled ) {
			$controls['nav_enabled'] = true;
			$controls['prev']        = $settings['prev_text'];
			$controls['next']        = $settings['next_text'];
		} else {
			$controls['nav_enabled'] = false;
		}

		if ( 'yes' === $load_more_enabled ) {
			$controls['load_more_enabled'] = true;
			$controls['load_more_text']    = $settings['load_more_text'];
		} else {
			$controls['load_more_enabled'] = false;
		}
		
		if ( $settings['autoscroll'] === 'yes' ) {
			$controls['provider_top_offset'] = ! empty( $settings['provider_top_offset'] ) ? absint( $settings['provider_top_offset'] ) : 0;
		}

		printf(
			'<div
				class="%1$s jet-filter"
				data-apply-provider="%2$s"
				data-content-provider="%2$s"
				data-query-id="%3$s"
				data-controls="%4$s"
				data-apply-type="%5$s"
			>',
			$base_class,
			$content_provider,
			$query_id,
			htmlspecialchars( json_encode( $controls ) ),
			$apply_type
		);

		if ( Plugin::instance()->editor->is_edit_mode() ) {
			$pagination_filter_type = jet_smart_filters()->filter_types->get_filter_types( 'pagination' );
			$pagination_filter_type->render_pagination_sample( $controls );
		}

		echo '</div>';
	}
}
