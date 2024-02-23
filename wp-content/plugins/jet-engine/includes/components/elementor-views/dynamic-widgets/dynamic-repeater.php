<?php
namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Listing_Dynamic_Repeater_Widget extends Widget_Base {

	public function get_name() {
		return 'jet-listing-dynamic-repeater';
	}

	public function get_title() {
		return __( 'Dynamic Repeater', 'jet-engine' );
	}

	public function get_icon() {
		return 'jet-engine-icon-dynamic-repeater';
	}

	public function get_categories() {
		return array( 'jet-listing-elements' );
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetengine-dynamic-repeater-widget-overview/?utm_source=jetengine&utm_medium=dynamic-repeater&utm_campaign=need-help';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-engine' ),
			)
		);

		$this->add_control(
			'repeater_notice',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( '<b>Note</b> this widget could process only repeater meta fields created with JetEngine or ACF plugins', 'jet-engine' ),
			)
		);

		$repeater_fields = $this->get_repeater_fields();

		if ( jet_engine()->options_pages ) {
			$repeater_fields['options_page'] = __( 'Options', 'jet-engine' );
		}

		$this->add_control(
			'dynamic_field_source',
			array(
				'label'   => __( 'Source', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'groups'  => $repeater_fields,
			)
		);

		if ( jet_engine()->options_pages ) {

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'repeater' );

			if ( ! empty( $options_pages_select ) ) {
				$this->add_control(
					'dynamic_field_option',
					array(
						'label'     => __( 'Option', 'jet-engine' ),
						'type'      => Controls_Manager::SELECT,
						'default'   => '',
						'groups'    => $options_pages_select,
						'condition' => array(
							'dynamic_field_source' => 'options_page',
						),
					)
				);
			}

		}

		/**
		 * Add 3rd-party controls for sources
		 */
		do_action( 'jet-engine/listings/dynamic-repeater/source-controls', $this );

		$this->add_control(
			'dynamic_field_format',
			array(
				'label'       => __( 'Item format', 'jet-engine' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => '<span>%name%</span>',
				'description' => __( 'You can render repeater fields values with macros %repeater field name%', 'jet-engine' )
			)
		);

		$this->add_control(
			'item_tag',
			array(
				'label'   => __( 'Item HTML tag', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'div',
				'options' => array(
					'div'  => 'DIV',
					'tr'   => 'tr',
					'li'   => 'li',
				),
			)
		);

		$this->add_control(
			'items_delimiter',
			array(
				'label'   => __( 'Items delimiter', 'jet-engine' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'dynamic_field_before',
			array(
				'label'       => __( 'Before items markup', 'jet-engine' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => '',
				'description' => __( 'HTML to output before repeater items', 'jet-engine' )
			)
		);

		$this->add_control(
			'dynamic_field_after',
			array(
				'label'       => __( 'After items markup', 'jet-engine' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => '',
				'description' => __( 'HTML to output after repeater items', 'jet-engine' )
			)
		);

		$this->add_control(
			'dynamic_field_counter',
			array(
				'label'     => __( 'Add counter to repeater items', 'jet-engine' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
			)
		);

		$this->add_control(
			'dynamic_field_leading_zero',
			array(
				'label'     => __( 'Add leding zero before counter items', 'jet-engine' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'condition' => array(
					'dynamic_field_counter' => 'yes',
				),
			)
		);

		$this->add_control(
			'dynamic_field_counter_after',
			array(
				'label'       => __( 'Text after counter number', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'condition'   => array(
					'dynamic_field_counter' => 'yes',
				),
			)
		);

		$this->add_control(
			'dynamic_field_counter_position',
			array(
				'label'     => __( 'Position', 'jet-engine' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'at-left',
				'options'   => array(
					'above'    => __( 'Above items', 'jet-engine' ),
					'at-left'  => __( 'At the left of the items', 'jet-engine' ),
					'at-right' => __( 'At the right of the items', 'jet-engine' ),
				),
				'condition'   => array(
					'dynamic_field_counter' => 'yes',
				),
			)
		);

		$this->add_control(
			'hide_if_empty',
			array(
				'label'        => esc_html__( 'Hide if value is empty', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'object_context',
			array(
				'label'     => __( 'Context', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'default_object',
				'options'   => jet_engine()->listings->allowed_context_list(),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_general_style',
			array(
				'label'      => __( 'General', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'items_direction',
			array(
				'label'   => __( 'Direction', 'jet-engine' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'flex-start',
				'options' => array(
					'row'    => array(
						'title' => __( 'Horizontal', 'jet-engine' ),
						'icon' => 'eicon-ellipsis-h',
					),
					'column' => array(
						'title' => __( 'Vertical', 'jet-engine' ),
						'icon' => 'eicon-editor-list-ul',
					),
				),
				'label_block' => false,
				'selectors'  => array(
					$this->css_selector( '__items' ) => 'flex-direction: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'items_alignment',
			array(
				'label'   => __( 'Alignment', 'jet-engine' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'flex-start',
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-engine' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-engine' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-engine' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'selectors'  => array(
					$this->css_selector( '__items' )    => 'justify-content: {{VALUE}};',
					$this->css_selector( '__item > *' ) => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'general_typography',
				'selector' => $this->css_selector( '__item > *' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_items_style',
			array(
				'label'      => __( 'Items', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'fixed_size',
			array(
				'label'        => esc_html__( 'Fixed item size', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_responsive_control(
			'item_width',
			array(
				'label'      => esc_html__( 'Item Width', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 15,
						'max' => 150,
					),
				),
				'condition' => array(
					'fixed_size' => 'yes',
				),
				'selectors'  => array(
					$this->css_selector( '__item > *' ) => 'display: flex; width: {{SIZE}}{{UNIT}}; justify-content: center;',
				),
			)
		);

		$this->add_responsive_control(
			'item_height',
			array(
				'label'      => esc_html__( 'Item Height', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 15,
						'max' => 150,
					),
				),
				'condition' => array(
					'fixed_size' => 'yes',
				),
				'selectors'  => array(
					$this->css_selector( '__item > *' ) => 'height: {{SIZE}}{{UNIT}}; display: flex; align-items: center;',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_item_style' );

		$this->start_controls_tab(
			'tabs_item_style_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-engine' ),
			)
		);

		$this->add_control(
			'item_color',
			array(
				'label' => __( 'Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__item > *' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'item_background_color',
			array(
				'label' => __( 'Background color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__item > *' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_item_style_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-engine' ),
			)
		);

		$this->add_control(
			'item_color_hover',
			array(
				'label' => __( 'Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__item > *:hover' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'item_background_color_hover',
			array(
				'label' => __( 'Background color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__item > *:hover' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'item_border_color_hover',
			array(
				'label' => __( 'Border Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array(
					'item_border_border!' => '',
				),
				'selectors' => array(
					$this->css_selector( '__item > *:hover' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'item_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'selector'       => $this->css_selector( '__item > *' ),
			)
		);

		$this->add_responsive_control(
			'item_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					$this->css_selector( '__item > *' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_box_shadow',
				'selector' => $this->css_selector( '__item > *' ),
			)
		);

		$this->add_control(
			'item_padding',
			array(
				'label'      => __( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					$this->css_selector( '__item > *' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'fixed_size!' => 'yes',
				),
			)
		);

		$this->add_control(
			'item_margin',
			array(
				'label'      => __( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					$this->css_selector( '__item > *' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_delimiter_style',
			array(
				'label'      => __( 'Delimiter', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'delimiter_typography',
				'selector' => $this->css_selector( '__delimiter' ),
			)
		);

		$this->add_control(
			'delimiter_color',
			array(
				'label' => __( 'Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__delimiter' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'delimiter_margin',
			array(
				'label'      => __( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					$this->css_selector( '__delimiter' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_counter_style',
			array(
				'label'      => __( 'Counters', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'counter_fixed_size',
			array(
				'label'        => esc_html__( 'Fixed counter box size', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_responsive_control(
			'counter_item_size',
			array(
				'label'      => esc_html__( 'Item Width', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 15,
						'max' => 150,
					),
				),
				'condition' => array(
					'counter_fixed_size' => 'yes',
				),
				'selectors'  => array(
					$this->css_selector( '__counter' ) => 'width: {{SIZE}}{{UNIT}}; flex: 0 0 {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'counter_item_color',
			array(
				'label' => __( 'Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__counter' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'counter_item_typography',
				'selector' => $this->css_selector( '__counter' ),
			)
		);

		$this->add_control(
			'counter_item_background_color',
			array(
				'label' => __( 'Background color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__counter' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'counter_item_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'selector'       => $this->css_selector( '__counter' ),
			)
		);

		$this->add_responsive_control(
			'counter_item_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					$this->css_selector( '__counter' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'counter_item_box_shadow',
				'selector' => $this->css_selector( '__counter' ),
			)
		);

		$this->add_control(
			'counter_item_padding',
			array(
				'label'      => __( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					$this->css_selector( '__counter' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'fixed_size!' => 'yes',
				),
			)
		);

		$this->add_control(
			'counter_item_margin',
			array(
				'label'      => __( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					$this->css_selector( '__counter' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'counter_item_depth',
			array(
				'label'      => esc_html__( 'Counter depth', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors'  => array(
					$this->css_selector( '__counter' ) => 'z-index: {{SIZE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_self_align',
			array(
				'label' => __( 'Alignment', 'jet-engine' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Left/Top', 'elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center/Middle', 'elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => __( 'Right/Bottom', 'elementor' ),
						'icon' => 'eicon-h-align-right',
					),
					'stretch' => array(
						'title' => __( 'Stretch', 'elementor' ),
						'icon' => 'eicon-h-align-stretch',
					),
				),
				'default' => '',
				'selectors' => array(
					$this->css_selector( '__counter' ) => 'align-self: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Returns CSS selector for nested element
	 *
	 * @param  [type] $el [description]
	 * @return [type]     [description]
	 */
	public function css_selector( $el = null ) {
		return sprintf( '{{WRAPPER}} .%1$s%2$s', $this->get_name(), $el );
	}

	/**
	 * Get meta fields for post type
	 *
	 * @return array
	 */
	public function get_repeater_fields() {

		if ( jet_engine()->meta_boxes ) {
			$result = jet_engine()->meta_boxes->get_fields_for_select( 'repeater' );
		} else {
			$result = array();
		}

		return apply_filters( 'jet-engine/listings/dynamic-repeater/fields', $result );

	}

	protected function render() {
		jet_engine()->listings->render_item( 'dynamic-repeater', $this->get_settings() );
	}

}
