<?php

namespace Elementor;

use Elementor\Group_Control_Border;
use Elementor\Core\Schemes\Typography as Scheme_Typography;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Jet_Smart_Filters_Search_Widget extends Jet_Smart_Filters_Base_Widget {

	public function get_name() {

		return 'jet-smart-filters-search';
	}

	public function get_title() {

		return __( 'Search Filter', 'jet-smart-filters' );
	}

	public function get_icon() {

		return 'jet-smart-filters-icon-search-filter';
	}

	public function get_help_url() {

		return jet_smart_filters()->widgets->prepare_help_url(
			'https://crocoblock.com/knowledge-base/articles/jetsmartfilters-how-to-create-a-search-filter/',
			$this->get_name()
		);
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/search/css-scheme',
			array(
				'filters-label'             => '.jet-filter-label',
				'filter-wrapper'            => '.jet-search-filter',
				'input'                     => '.jet-search-filter__input',
				'input-wrapper'             => '.jet-search-filter__input-wrapper',
				'input-clear'               => '.jet-search-filter__input-clear',
				'input-loading'             => '.jet-search-filter__input-loading',
				'apply-filters-button'      => '.jet-search-filter__submit',
				'apply-filters-button-icon' => '.jet-search-filter__submit > i',
			)
		);

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'filter_id',
			array_merge(
				$this->get_filter_control_settings(),
				array(
					'multiple' => false,
				)
			)
		);

		$this->add_control(
			'content_provider',
			array(
				'label'   => __( 'This filter for', 'jet-smart-filters' ),
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
					'ajax'          => __( 'AJAX', 'jet-smart-filters' ),
					'ajax-ontyping' => __( 'AJAX on typing', 'jet-smart-filters' ),
					'reload'        => __( 'Page reload', 'jet-smart-filters' ),
					'mixed'         => __( 'Mixed', 'jet-smart-filters' ),
				),
			)
		);

		$this->add_control(
			'typing_min_letters_count',
			array(
				'label'         => esc_html__( 'Min number of letters', 'jet-smart-filters' ),
				'description'   => esc_html__( 'The minimum number of letters to start the search.', 'jet-smart-filters' ),
				'type'          => Controls_Manager::NUMBER,
				'default'       => 3,
				'min'           => 1,
				'max'           => 10,
				'step'          => 1,
				'condition'     => array(
					'apply_type' => 'ajax-ontyping'
				)
			)
		);

		$this->add_control(
			'apply_button_text',
			array(
				'label'   => esc_html__( 'Search button text', 'jet-smart-filters' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Apply filter', 'jet-smart-filters' ),
				'condition' => array(
					'apply_type'        => array( 'ajax', 'reload', 'mixed' ),
					'hide_apply_button' => ''
				)
			)
		);

		$this->add_control(
			'apply_button_icon',
			array(
				'label'       => esc_html__( 'Search button icon', 'jet-smart-filters' ),
				'label_block' => true,
				'type'        => Controls_Manager::ICON,
				'default'     => '',
				'condition'   => array(
					'apply_type'        => array( 'ajax', 'reload', 'mixed' ),
					'hide_apply_button' => ''
				)
			)
		);

		$this->add_control(
			'hide_apply_button',
			array(
				'label'        => esc_html__( 'Hide apply button', 'jet-smart-filters' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-smart-filters' ),
				'label_off'    => esc_html__( 'No', 'jet-smart-filters' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'apply_type'   => array( 'ajax', 'reload', 'mixed' ),
				)
			)
		);

		$this->add_control(
			'show_label',
			array(
				'label'        => esc_html__( 'Show filter label', 'jet-smart-filters' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => '',
				'label_on'     => esc_html__( 'Yes', 'jet-smart-filters' ),
				'label_off'    => esc_html__( 'No', 'jet-smart-filters' ),
				'return_value' => 'yes',
				'default'      => '',
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

		// Include Additional Providers Settings
		include jet_smart_filters()->plugin_path( 'includes/widgets/common-controls/additional-providers.php' );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_search_content_style',
			array(
				'label'      => __( 'Content', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'content_position',
			array(
				'label'   => esc_html__( 'Position', 'jet-smart-filters' ),
				'type'    => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => array(
					'line' => array(
						'title' => esc_html__( 'Line', 'jet-smart-filters' ),
						'icon'  => 'eicon-ellipsis-h',
					),
					'column' => array(
						'title' => esc_html__( 'Columns', 'jet-smart-filters' ),
						'icon'  => 'eicon-menu-bar',
					),
				),
				'selectors_dictionary' => array(
					'line'      => 'display:flex; flex-direction:row;',
					'column'    => 'display:flex; flex-direction:column;',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter-wrapper'] => '{{VALUE}}',
				),
				'prefix_class' => 'jet-smart-filter-content-position-',
			)
		);

		$this->add_responsive_control(
			'content_search_input_width',
			array(
				'label'      => esc_html__( 'Input Width', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 100,
					'unit' => '%',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['input-wrapper'] => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_line_horizontal_alignment',
			array(
				'label'   => esc_html__( 'Horizontal Alignment', 'jet-smart-filters' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'eicon-h-align-right',
					),
					'space-between' => array(
						'title' => esc_html__( 'Justify', 'jet-smart-filters' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter-wrapper'] => 'justify-content: {{VALUE}};',
				),
				'condition' => array(
					'content_position' => 'line'
				)
			)
		);

		$this->add_responsive_control(
			'content_line_vertical_alignment',
			array(
				'label'     => esc_html__( 'Vertical Alignment', 'jet-smart-filters' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'jet-smart-filters' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => esc_html__( 'Middle', 'jet-smart-filters' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Bottom', 'jet-smart-filters' ),
						'icon'  => 'eicon-v-align-bottom',
					),
					'stretch'   => array(
						'title' => esc_html__( 'Stretch', 'jet-smart-filters' ),
						'icon'  => 'eicon-v-align-stretch',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter-wrapper'] => 'align-items: {{VALUE}};',
				),
				'condition' => array(
					'content_position' => 'line'
				)
			)
		);

		$this->add_responsive_control(
			'content_column_horizontal_alignment',
			array(
				'label'   => esc_html__( 'Horizontal Alignment', 'jet-smart-filters' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'eicon-h-align-right',
					),
					'stretch' => array(
						'title' => esc_html__( 'Stretch', 'jet-smart-filters' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter-wrapper'] => 'align-items: {{VALUE}};',
				),
				'condition' => array(
					'content_position' => 'column'
				)
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_search_input_style',
			array(
				'label'      => __( 'Input', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'search_input_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['input']
			)
		);

		$this->add_control(
			'search_input_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['input']                             => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['input'] . '::placeholder'           => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['input'] . ':-ms-input-placeholder'  => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['input'] . '::-ms-input-placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['input-clear']                       => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['input-loading']                     => 'color: {{VALUE}}'
				),
			)
		);

		$this->add_control(
			'search_input_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['input'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'search_input_border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['input'],
				'separator'   => 'before'
			)
		);

		$this->add_control(
			'search_input_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['input'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'search_input_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['input'],
			)
		);

		$this->add_responsive_control(
			'search_input_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['input'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before'
			)
		);

		$this->add_responsive_control(
			'search_input_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['input'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; width: calc(100% - {{RIGHT}}{{UNIT}} - {{LEFT}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filter_apply_button_style',
			array(
				'label'      => esc_html__( 'Button', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filter_apply_button_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} ' . $css_scheme['apply-filters-button'],
			)
		);

		$this->start_controls_tabs( 'filter_apply_button_style_tabs' );

		$this->start_controls_tab(
			'filter_apply_button_normal_styles',
			array(
				'label' => esc_html__( 'Normal', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'filter_apply_button_normal_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'filter_apply_button_normal_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'filter_apply_button_hover_styles',
			array(
				'label' => esc_html__( 'Hover', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'filter_apply_button_hover_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] . ':hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'filter_apply_button_hover_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] . ':hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'filter_apply_button_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] . ':hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'filter_apply_button_border_border!' => '',
				)
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'filter_apply_button_border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['apply-filters-button'],
				'separator'   => 'before'
			)
		);

		$this->add_control(
			'filter_apply_button_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'filter_apply_button_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['apply-filters-button'],
			)
		);

		$this->add_responsive_control(
			'filter_apply_button_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before'
			)
		);

		$this->add_responsive_control(
			'filter_apply_button_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_apply_button_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter-wrapper'] => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'content_position' => ''
				)
			)
		);

		$this->add_control(
			'filter_apply_button_icon_heading',
			array(
				'label'     => esc_html__( 'Icon', 'jet-smart-filters' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'apply_button_icon!' => ''
				)
			)
		);

		$this->add_control(
			'filter_apply_button_icon_position',
			array(
				'label'       => esc_html__( 'Position', 'jet-smart-filters' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => array(
					'left'  => array(
						'title' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'eicon-arrow-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'eicon-arrow-right',
					),
				),
				'toggle'      => true,
				'default'     => 'left',
				'condition'   => array(
					'apply_button_icon!' => ''
				)
			)
		);

		$this->add_responsive_control(
			'filter_apply_button_icon_size',
			array(
				'label'      => esc_html__( 'Size', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'size' => 15,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button-icon'] => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'apply_button_icon!' => ''
				)
			)
		);

		$this->add_responsive_control(
			'filter_apply_button_icon_offset',
			array(
				'label'      => esc_html__( 'Icon Offset', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .button-icon-position-right ' . $css_scheme['apply-filters-button-icon'] => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .button-icon-position-left ' . $css_scheme['apply-filters-button-icon']  => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'apply_button_icon!' => ''
				)
			)
		);

		$this->add_control(
			'filter_apply_button_icon_normal_color',
			array(
				'label'     => esc_html__( 'Default Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button-icon'] => 'color: {{VALUE}}',
				),
				'condition'  => array(
					'apply_button_icon!' => ''
				)
			)
		);

		$this->add_control(
			'filter_apply_button_icon_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] . ':hover > i' => 'color: {{VALUE}}',
				),
				'condition'  => array(
					'apply_button_icon!' => ''
				)
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_label_style',
			array(
				'label'      => esc_html__( 'Label', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['filters-label'],
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filters-label'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'label_border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['filters-label'],
			)
		);

		$this->add_responsive_control(
			'label_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filters-label'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before'
			)
		);

		$this->add_responsive_control(
			'label_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filters-label'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'label_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filters-label'] => 'text-align: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {

		jet_smart_filters()->set_filters_used();

		$base_class = $this->get_name();
		$settings   = $this->get_settings();

		if ( empty( $settings['filter_id'] ) ) {
			/* if ( Plugin::instance()->editor->is_edit_mode() ) {
				echo '<div></div>';
			} */

			return;
		}

		printf( '<div class="%1$s jet-filter">', $base_class );

		if ( in_array( $settings['apply_type'], ['ajax', 'mixed'] ) ) {
			$apply_type = $settings['apply_type'] . '-reload';
		} else {
			$apply_type = $settings['apply_type'];
		}

		$filter_id            = apply_filters( 'jet-smart-filters/render_filter_template/filter_id', $settings['filter_id'] );
		$provider             = ! empty( $settings['content_provider'] ) ? $settings['content_provider'] : '';
		$query_id             = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
		$show_label           = ! empty( $settings['show_label'] ) ? filter_var( $settings['show_label'], FILTER_VALIDATE_BOOLEAN ) : false;
		$additional_providers = jet_smart_filters()->utils->get_additional_providers( $settings );
		$format               = '<i class="%s"></i>';
		$icon                 = $settings['apply_button_icon'] ? sprintf( $format, $settings['apply_button_icon'] ) : '';
		$hide_apply_button    = ! empty( $settings['hide_apply_button'] ) ? filter_var( $settings['hide_apply_button'], FILTER_VALIDATE_BOOLEAN ) : false;

		jet_smart_filters()->admin_bar_register_item( $filter_id );

		include jet_smart_filters()->get_template( 'common/filter-label.php' );

		jet_smart_filters()->filter_types->render_filter_template( $this->get_widget_fiter_type(), array(
			'filter_id'            => $filter_id,
			'content_provider'     => $provider,
			'additional_providers' => $additional_providers,
			'apply_type'           => $apply_type,
			'min_letters_count'    => $settings['typing_min_letters_count'],
			'button_text'          => $settings['apply_button_text'],
			'button_icon'          => $icon,
			'hide_apply_button'    => $hide_apply_button,
			'query_id'             => $query_id,
			'button_icon_position' => $settings['filter_apply_button_icon_position'],
		) );

		echo '</div>';
	}
}
