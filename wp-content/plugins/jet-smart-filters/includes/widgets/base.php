<?php

namespace Elementor;

use Elementor\Group_Control_Border;
use Elementor\Core\Schemes\Typography as Scheme_Typography;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Jet_Smart_Filters_Base_Widget extends Widget_Base {

	public function get_name() {
		// Rewrite this in child widget
		return 'jet-smart-filters-base';
	}

	public function get_categories() {

		return array( jet_smart_filters()->widgets->get_category() );
	}

	/**
	 * Returns filter control settings
	 */
	public function get_filter_control_settings() {

		return array(
			'label'       => __( 'Select filter', 'jet-smart-filters' ),
			'label_block' => true,
			'type'        => 'jet-query',
			'multiple'    => true,
			'default'     => '',
			'query_type'  => 'post',
			'query'       => array(
				'post_type'      => jet_smart_filters()->post_type->slug(),
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => '_filter_type',
						'value'   => $this->get_widget_fiter_type(),
						'compare' => '=',
					),
				)
			),
		);
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'filter_id',
			$this->get_filter_control_settings()
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
					'ajax'   => __( 'AJAX', 'jet-smart-filters' ),
					'reload' => __( 'Page reload', 'jet-smart-filters' ),
					'mixed'  => __( 'Mixed', 'jet-smart-filters' ),
				),
			)
		);

		$this->add_control(
			'apply_on',
			array(
				'label'     => __( 'Apply on', 'jet-smart-filters' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'value',
				'options'   => array(
					'value'  => __( 'Value change', 'jet-smart-filters' ),
					'submit' => __( 'Click on apply button', 'jet-smart-filters' ),
				),
				'condition' => array(
					'apply_type' => array( 'ajax', 'mixed' ),
				),
			)
		);

		$this->add_control(
			'apply_button',
			array(
				'label'        => esc_html__( 'Show apply button', 'jet-smart-filters' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => '',
				'label_on'     => esc_html__( 'Yes', 'jet-smart-filters' ),
				'label_off'    => esc_html__( 'No', 'jet-smart-filters' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'apply_button_text',
			array(
				'label'     => esc_html__( 'Apply button text', 'jet-smart-filters' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Apply filter', 'jet-smart-filters' ),
				'condition' => array(
					'apply_button' => 'yes'
				),
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

		$this->register_filter_settings_controls();

		$this->register_filter_style_controls();

		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/base/css-scheme',
			array(
				'filter'               => '.jet-filter',
				'filters-label'        => '.jet-filter-label',
				'apply-filters'        => '.apply-filters',
				'apply-filters-button' => '.apply-filters__button',
			)
		);

		$this->base_controls_section_filter_label( $css_scheme );

		$this->base_controls_section_filter_apply_button( $css_scheme );

		$this->base_controls_section_filter_group( $css_scheme );
	}

	public function base_controls_section_filter_label( $css_scheme ) {

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

		$this->add_control(
			'label_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filters-label'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
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
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filters-label'] => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	public function base_controls_section_filter_apply_button( $css_scheme ) {

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
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'flex-start'   => array(
						'title' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'  => array(
						'title' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-right',
					),
					'stretch'  => array(
						'title' => esc_html__( 'Stretch', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'align-self: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	public function base_controls_section_filter_group( $css_scheme ) {

		$this->start_controls_section(

			'section_group_filters_style',
			array(
				'label'      => esc_html__( 'Grouped Filters', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(

			'group_filters_vertical_offset',
			array(
				'label'      => esc_html__( 'Vertical Space Between', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] . '+' . $css_scheme['filter'] => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jet-select[data-hierarchical="1"] + .jet-select[data-hierarchical="1"]' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register filter settings controls. Specific for each widget.
	 */
	public function register_filter_settings_controls() {}

	/**
	 * Register filter style controls. Specific for each widget.
	 */
	public function register_filter_style_controls() {}

	/**
	 * Register filter style controls. Specific for each widget.
	 */
	public function register_horizontal_layout_controls( $css_scheme ) {

		$this->add_responsive_control(
			'filters_position',
			array(
				'label'       => esc_html__( 'Filters Position', 'jet-smart-filters' ),
				'type'        => Controls_Manager::CHOOSE,
				'toggle'      => false,
				'label_block' => false,
				'default'     => 'block',
				'options'     => array(
					'block' => array(
						'title' => esc_html__( 'Column', 'jet-smart-filters' ),
						'icon'  => 'eicon-menu-bar',
					),
					'inline-block'    => array(
						'title' => esc_html__( 'Line', 'jet-smart-filters' ),
						'icon'  => 'eicon-ellipsis-h',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['list-item']     => 'display: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['list-children'] => 'display: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'horizontal_layout_description',
			array(
				'raw'             => esc_html__( 'Horizontal Offset control works only with Line Filters Position', 'jet-smart-filters' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'separator'       => 'before',
			)
		);

		$this->add_responsive_control(
			'filters_space_between_horizontal',
			array(
				'label'      => esc_html__( 'Horizontal Offset', 'jet-smart-filters' ),
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
					'size' => 5,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['list-item']     => 'margin-right: calc({{SIZE}}{{UNIT}}/2); margin-left: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} ' . $css_scheme['list-children'] => 'margin-right: calc({{SIZE}}{{UNIT}}/2); margin-left: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} ' . $css_scheme['list-wrapper']  => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2); margin-right: calc(-{{SIZE}}{{UNIT}}/2);',
				),
				'separator' => 'after',
			)
		);

		$this->add_responsive_control(
			'filters_list_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['list-wrapper'] => 'text-align: {{VALUE}};',
				),
			)
		);
	}

	/**
	 * Returns apropriate filters list for widget
	 */
	public function get_widget_filters( $empty_option = false ) {

		$filters_by_type = jet_smart_filters()->data->get_filters_by_type( $this->get_widget_fiter_type() );

		if ( $empty_option ) {
			return array( '0' => esc_html__( 'Select...', 'jet-smart-filters' ) ) + $filters_by_type;
		} else {
			return $filters_by_type;
		}
	}

	/**
	 * Returns widget filter type
	 */
	public function get_widget_fiter_type() {

		return str_replace( 'jet-smart-filters-', '', $this->get_name() );
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
		$indexer_class     = '';
		$show_counter      = false;
		$show_items_rule   = 'show';
		$group             = false;

		if ( empty( $settings['filter_id'] ) ) {
			/* if ( Plugin::instance()->editor->is_edit_mode() ) {
				echo '<div></div>';
			} */

			return;
		}

		$filter_ids = $settings['filter_id'];

		if ( ! is_array( $filter_ids ) ) {
			$filter_ids = array( $filter_ids );
		}

		if ( 1 < count( $filter_ids ) ) {
			$group = true;
		}

		if ( 'submit' === $settings['apply_on'] && in_array( $settings['apply_type'], ['ajax', 'mixed'] ) ) {
			$apply_type = $settings['apply_type'] . '-reload';
		} else {
			$apply_type = $settings['apply_type'];
		}

		$query_id          = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
		$show_label        = ! empty( $settings['show_label'] ) ? filter_var( $settings['show_label'], FILTER_VALIDATE_BOOLEAN ) : false;
		$show_items_label  = ! empty( $settings['show_items_label'] ) ? $settings['show_items_label'] : false;
		$show_decorator    = ! empty( $settings['show_decorator'] ) ? $settings['show_decorator'] : false;
		$apply_indexer     = ! empty( $settings['apply_indexer'] ) ? filter_var( $settings['apply_indexer'], FILTER_VALIDATE_BOOLEAN ) : false;
		$filter_image_size = ! empty( $settings['filter_image_size'] ) ? $settings['filter_image_size'] : 'full';
		$change_items_rule = ! empty( $settings['change_items_rule'] ) ? $settings['change_items_rule'] : 'always';
		// search
		$search_enabled     = ! empty( $settings['search_enabled'] ) ? filter_var( $settings['search_enabled'], FILTER_VALIDATE_BOOLEAN ) : false;
		$search_placeholder = ! empty( $settings['search_placeholder'] ) && $search_enabled ? $settings['search_placeholder'] : false;
		// more/less
		$less_items_count = ! empty( $settings['moreless_enabled'] ) && ! empty( $settings['less_items_count'] ) ? (int)$settings['less_items_count'] : false;
		$more_text        = ! empty( $settings['more_text'] ) ? $settings['more_text'] : false;
		$less_text        = ! empty( $settings['less_text'] ) ? $settings['less_text'] : false;
		// dropdown
		$dropdown_enabled     = ! empty( $settings['dropdown_enabled'] ) ? $settings['dropdown_enabled'] : false;
		$dropdown_placeholder = ! empty( $settings['dropdown_placeholder'] ) ? $settings['dropdown_placeholder'] : false;
		// dropdown n selected
		$dropdown_n_selected_enabled = ! empty( $settings['dropdown_n_selected_enabled'] ) ? filter_var( $settings['dropdown_n_selected_enabled'], FILTER_VALIDATE_BOOLEAN ) : false;
		$dropdown_n_selected_number  = isset( $settings['dropdown_n_selected_number'] ) && $settings['dropdown_n_selected_number'] >= 0 ? $settings['dropdown_n_selected_number'] : 3;
		$dropdown_n_selected_text    = isset( $settings['dropdown_n_selected_text'] ) ? $settings['dropdown_n_selected_text'] : __( 'and {number} others', 'jet-smart-filters' );
		// scroll
		$scroll_height = ! empty( $settings['scroll_enabled'] ) && ! empty( $settings['scroll_height'] ) ? (int)$settings['scroll_height'] : false;

		if ( $apply_indexer ){
			$indexer_class   = 'jet-filter-indexed';
			$show_counter    = 'yes' === $settings['show_counter'] ? $settings['show_counter'] : false;
			$show_items_rule = ! empty( $settings['show_items_rule'] ) ? $settings['show_items_rule'] : 'show';

			if ( $show_counter ) {
				$counter_prefix = ! empty( $settings['counter_prefix'] ) ? $settings['counter_prefix'] : false;
				$counter_suffix = ! empty( $settings['counter_suffix'] ) ? $settings['counter_suffix'] : false;
			}
		}

		if ( $group ) {
			echo '<div class="jet-filters-group">';
		}

		foreach ( $filter_ids as $filter_id ) {

			$filter_id = apply_filters( 'jet-smart-filters/render_filter_template/filter_id', $filter_id );

			jet_smart_filters()->admin_bar_register_item( $filter_id );

			printf(
				'<div class="%1$s jet-filter %2$s" data-indexer-rule="%3$s" data-show-counter="%4$s" data-change-counter="%5$s">',
				apply_filters( 'jet-smart-filters/render_filter_template/base_class', $base_class, $filter_id ),
				$indexer_class,
				$show_items_rule,
				$show_counter,
				$change_items_rule
			);

			$provider             = ! empty( $settings['content_provider'] ) ? $settings['content_provider'] : '';
			$additional_providers = jet_smart_filters()->utils->get_additional_providers( $settings );

			$filter_template_args =  array(
				'filter_id'            => $filter_id,
				'content_provider'     => $provider,
				'additional_providers' => $additional_providers,
				'apply_type'           => $apply_type,
				'query_id'             => $query_id,
				'show_label'           => $show_label,
				'display_options'      => array(
					'show_items_label'  => $show_items_label,
					'show_decorator'    => $show_decorator,
					'filter_image_size' => $filter_image_size,
					'show_counter'      => $show_counter,
				),
			);

			if ( ! empty( $counter_prefix ) ) {
				$filter_template_args['display_options']['counter_prefix'] = $counter_prefix;
			}

			if ( ! empty( $counter_suffix ) ) {
				$filter_template_args['display_options']['counter_suffix'] = $counter_suffix;
			}

			// search
			if ( $search_enabled ) $filter_template_args['search_enabled'] = $search_enabled;
			if ( $search_placeholder ) $filter_template_args['search_placeholder'] = $search_placeholder;
			// more/less
			if ( $less_items_count ) $filter_template_args['less_items_count'] = $less_items_count;
			if ( $more_text ) $filter_template_args['more_text'] = $more_text;
			if ( $less_text ) $filter_template_args['less_text'] = $less_text;
			//dropdown
			if ( $dropdown_enabled ) $filter_template_args['dropdown_enabled'] = $dropdown_enabled;
			if ( $dropdown_placeholder ) $filter_template_args['dropdown_placeholder'] = $dropdown_placeholder;
			//dropdown n selected
			if ( $dropdown_n_selected_enabled ) {
				$filter_template_args['dropdown_n_selected_enabled'] = $dropdown_n_selected_enabled;
				$filter_template_args['dropdown_n_selected_number'] = $dropdown_n_selected_number;
				$filter_template_args['dropdown_n_selected_text'] = $dropdown_n_selected_text;
			}
			// scroll
			if ( $scroll_height ) $filter_template_args['scroll_height'] = $scroll_height;
			//indexer
			if ( $apply_indexer ) $filter_template_args['apply_indexer'] = $apply_indexer;

			include jet_smart_filters()->get_template( 'common/filter-label.php' );

			jet_smart_filters()->filter_types->render_filter_template( $this->get_widget_fiter_type(), $filter_template_args );

			echo '</div>';

		}

		if ( $group ) {
			echo '</div>';
		}

		include jet_smart_filters()->get_template( 'common/apply-filters.php' );
	}
}
