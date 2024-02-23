<?php

namespace Elementor;

use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Engine_Booking_Form_Widget extends Widget_Base {

	private $source = false;

	public function get_name() {
		return 'jet-engine-booking-form';
	}

	public function get_title() {
		return __( 'Form', 'jet-engine' );
	}

	public function get_icon() {
		return 'jet-engine-icon-forms';
	}

	public function get_categories() {
		return array( 'jet-listing-elements' );
	}

	public function get_script_depends() {
		return array( 'jet-engine-frontend-forms' );
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/how-to-create-a-booking-form-layout/?utm_source=jetengine&utm_medium=booking-form&utm_campaign=need-help';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-engine' ),
			)
		);

		$this->add_control(
			'_form_id',
			array(
				'label'      => __( 'Select form', 'jet-engine' ),
				'type'       => 'jet-query',
				'query_type' => 'post',
				'query'      => array(
					'post_type' => jet_engine()->forms->slug(),
				),
				'edit_button' => array(
					'active' => true,
					'label'  => __( 'Edit Form', 'jet-engine' ),
				),
			)
		);

		$this->add_control(
			'fields_layout',
			array(
				'label'   => __( 'Fields layout', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'column',
				'options' => array(
					'column' => __( 'Column', 'jet-engine' ),
					'row'    => __( 'Row', 'jet-engine' ),
				),
			)
		);

		$this->add_control(
			'fields_label_tag',
			array(
				'label'   => __( 'Fields label HTML tag', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'div',
				'options' => array(
					'div'   => __( 'DIV', 'jet-engine' ),
					'label' => __( 'LABEL', 'jet-engine' ),
				)
			)
		);

		$this->add_control(
			'submit_type',
			array(
				'label'   => __( 'Submit type', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'reload',
				'options' => array(
					'reload' => __( 'Reload', 'jet-engine' ),
					'ajax'   => __( 'AJAX', 'jet-engine' ),
				),
			)
		);

		$this->add_control(
			'cache_form',
			array(
				'label'        => esc_html__( 'Cache form output', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => '',
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_rows_style',
			array(
				'label'      => __( 'Rows', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'rows_divider',
			array(
				'label'        => esc_html__( 'Divider between rows', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => '',
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_responsive_control(
			'rows_divider_height',
			array(
				'label'      => __( 'Height', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors'  => array(
					$this->css_selector( '__divider' ) => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'rows_divider' => 'yes',
				),
			)
		);

		$this->add_control(
			'rows_divider_color',
			array(
				'label'     => __( 'Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__divider' ) => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'rows_divider' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'rows_gap',
			array(
				'label'      => __( 'Rows Gap', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					$this->combine_selector( '-row:not(:last-child)' ) => 'padding-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'cols_gap',
			array(
				'label'      => __( 'Columns Gap', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					$this->css_selector( '-row' ) => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 )',
					$this->css_selector( '-col' ) => 'padding-left: calc( {{SIZE}}{{UNIT}}/2 ); padding-right: calc( {{SIZE}}{{UNIT}}/2 )',
				),
			)
		);

		$this->add_control(
			'label_styles',
			array(
				'label'     => esc_html__( 'Labels', 'jet-engine' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'labels_typography',
				'selector' => $this->combine_selector( '__label' ),
			)
		);

		$this->add_control(
			'labels_color',
			array(
				'label'     => __( 'Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__label' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'labels_gap',
			array(
				'label'      => __( 'Gap', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					$this->combine_selector( '__label' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'labels_width',
			array(
				'label'      => __( 'Width', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 30,
				),
				'selectors'  => array(
					$this->combine_selector( '.layout-row .%s-col__start' ) => 'max-width: {{SIZE}}%; -ms-flex: 0 0 {{SIZE}}%; flex: 0 0 {{SIZE}}%;',
				),
				'condition'  => array(
					'fields_layout' => 'row',
				),
			)
		);

		$this->add_control(
			'desc_styles',
			array(
				'label'     => esc_html__( 'Descriptions', 'jet-engine' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'selector' => $this->combine_selector( '__desc' ),
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__desc' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'desc_gap',
			array(
				'label'      => __( 'Gap', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					$this->combine_selector( '__desc' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'labels_h_alignment',
			array(
				'label'                => __( 'Horizontal Alignment', 'jet-engine' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'default'              => 'left',
				'separator'            => 'before',
				'options'              => array(
					'left'   => array(
						'title' => __( 'Start', 'jet-engine' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'jet-engine' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'End', 'jet-engine' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'selectors_dictionary' => array(
					'left'  => 'flex-start',
					'right' => 'flex-end'
				),
				'selectors'            => array(
					$this->multiple_selectors(
						$this->combine_selector( '__label' ),
						$this->combine_selector( '__desc' )
					) => 'display:flex; justify-content:{{VALUE}};'
				),
			)
		);

		$this->add_responsive_control(
			'labels_v_alignment',
			array(
				'label'       => __( 'Vertical Alignment', 'jet-engine' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default'     => 'flex-start',
				'options'     => array(
					'flex-start' => array(
						'title' => __( 'Top', 'jet-engine' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => __( 'Middle', 'jet-engine' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'jet-engine' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors'   => array(
					$this->combine_selector( '-col', 'labels_v_alignment' ) => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_fields_style',
			array(
				'label'      => __( 'Fields', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'fields_typography',
				'selector' => $this->combine_selector( '__field:not(.checkradio-field):not(.range-field)' ),
			)
		);

		$this->add_control(
			'fields_color',
			array(
				'label'     => __( 'Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__field:not(.checkradio-field):not(.range-field)' ) => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'fields_placeholder_color',
			array(
				'label'     => __( 'Placeholder Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( ' ::-webkit-input-placeholder' ) => 'color: {{VALUE}};',
					$this->css_selector( ' ::-ms-input-placeholder' )     => 'color: {{VALUE}};',
					$this->css_selector( ' ::-moz-placeholder' )          => 'color: {{VALUE}};',
					$this->css_selector( ' :-moz-placeholder' )           => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'fields_background_color',
			array(
				'label'     => __( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector(
						'__field:not(.checkradio-field):not(.range-field)',
						'fields_background_color'
					) => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'fields_padding',
			array(
				'label'      => __( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					$this->combine_selector( '__field:not(.checkradio-field):not(.range-field)' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'fields_margin',
			array(
				'label'      => __( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					$this->combine_selector( '__field:not(.checkradio-field):not(.range-field)' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'fields_border',
				'label'       => __( 'Border', 'jet-engine' ),
				'placeholder' => '1px',
				'selector'    => $this->combine_selector( '__field:not(.checkradio-field):not(.range-field)' ),
			)
		);

		$this->add_responsive_control(
			'fields_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$this->combine_selector( '__field:not(.checkradio-field):not(.range-field)' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'fields_box_shadow',
				'selector' => $this->combine_selector( '__field:not(.checkradio-field):not(.range-field)' ),
			)
		);

		$this->add_responsive_control(
			'fields_width',
			array(
				'label'      => __( 'Fields width', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					$this->combine_selector( '__field:not(.checkradio-field):not(.range-field)' ) => 'max-width: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};flex: 0 1 {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'fields_textarea_height',
			array(
				'label'      => __( 'Textarea Height', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 500,
					),
				),
				'selectors'  => array(
					$this->combine_selector( '__field.textarea-field' ) => 'height:{{SIZE}}px;min-height:{{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'reset_appearance',
			array(
				'label'                => esc_html__( 'Reset Select Field Appearance', 'jet-engine' ),
				'description'          => esc_html__( 'Check this option to reset select field appearance CSS value. This will make select fields appearance the same for all browsers', 'jet-engine' ),
				'type'                 => Controls_Manager::SWITCHER,
				'default'              => '',
				'separator'            => 'before',
				'selectors_dictionary' => array(
					'yes' => '-webkit-appearance: none;',
				),
				'selectors'            => array(
					$this->combine_selector( '__field.select-field' ) => '{{VALUE}}'
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_checkradio_fields_style',
			array(
				'label'      => __( 'Checkbox & Radio Fields', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'checkradio_fields_layout',
			array(
				'label'       => esc_html__( 'Layout', 'jet-engine' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => array(
					'0 1 auto' => array(
						'title' => esc_html__( 'Horizontal', 'jet-engine' ),
						'icon'  => 'eicon-ellipsis-h',
					),
					'0 1 100%' => array(
						'title' => esc_html__( 'Vertical', 'jet-engine' ),
						'icon'  => 'eicon-menu-bar',
					),
				),
				'selectors'   => array(
					$this->combine_selector( ' .checkradio-wrap' ) => 'flex: {{VALUE}};',
					$this->combine_selector( '__fields-group' )    => 'display: flex; flex-wrap: wrap;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'checkradio_fields_typography',
				'selector' => $this->combine_selector( ' .checkradio-wrap' ),
			)
		);

		$this->add_control(
			'checkradio_fields_color',
			array(
				'label'     => __( 'Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( ' .checkradio-wrap' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'checkradio_fields_width',
			array(
				'label'      => __( 'Width', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 600,
					),
				),
				'selectors'  => array(
					$this->combine_selector( '__fields-group.checkradio-wrap' ) => 'max-width: {{SIZE}}{{UNIT}};flex: 0 1 {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'checkradio_fields_col_width',
			array(
				'label'      => __( 'Column Width', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 600,
					),
				),
				'selectors'  => array(
					$this->combine_selector( '__field-wrap.checkradio-wrap' ) => 'flex: 0 1 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'checkradio_fields_gap',
			array(
				'label'      => __( 'Gap between control and label', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'body:not(.rtl) ' . $this->css_selector( '__field.checkradio-field' ) => 'margin-right: {{SIZE}}px;',
					'body.rtl ' . $this->css_selector( '__field.checkradio-field' )       => 'margin-left: {{SIZE}}px;',

				),
			)
		);

		$this->add_control(
			'checkradio_fields_background_color',
			array(
				'label'     => __( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( ' .checkradio-wrap label' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'checkradio_fields_padding',
			array(
				'label'      => __( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					$this->combine_selector( ' .checkradio-wrap label' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				),
			)
		);

		$this->add_responsive_control(
			'checkradio_fields_margin',
			array(
				'label'      => __( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					$this->combine_selector( ' .checkradio-wrap label' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'checkradio_fields_border',
				'label'       => __( 'Border', 'jet-engine' ),
				'placeholder' => '1px',
				'selector'    => $this->combine_selector( ' .checkradio-wrap label' ),
			)
		);

		$this->add_responsive_control(
			'checkradio_fields_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$this->combine_selector( ' .checkradio-wrap label' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_calc_fields_style',
			array(
				'label'      => __( 'Calculated Fields', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'calc_fields_typography',
				'selector' => $this->combine_selector( '__calculated-field' ),
			)
		);

		$this->add_control(
			'calc_fields_color',
			array(
				'label'     => __( 'Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__calculated-field' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'calc_fields_prefix_color',
			array(
				'label'     => __( 'Prefix Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__calculated-field-prefix' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'calc_fields_prefix_size',
			array(
				'label'      => __( 'Prefix size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors'  => array(
					$this->combine_selector( '__calculated-field-prefix' ) => 'font-size: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'calc_fields_suffix_color',
			array(
				'label'     => __( 'Suffix Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__calculated-field-suffix' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'calc_fields_suffix_size',
			array(
				'label'      => __( 'Suffix size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors'  => array(
					$this->combine_selector( '__calculated-field-suffix' ) => 'font-size: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'calc_fields_background_color',
			array(
				'label'     => __( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__calculated-field' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'calc_fields_padding',
			array(
				'label'      => __( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					$this->combine_selector( '__calculated-field' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'calc_fields_margin',
			array(
				'label'      => __( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					$this->combine_selector( '__calculated-field' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'calc_fields_border',
				'label'       => __( 'Border', 'jet-engine' ),
				'placeholder' => '1px',
				'selector'    => $this->combine_selector( '__calculated-field' ),
			)
		);

		$this->add_responsive_control(
			'calc_fields_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$this->combine_selector( '__calculated-field' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_range_fields_style',
			array(
				'label' => __( 'Range Fields', 'jet-engine' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'range_max_width',
			array(
				'label'      => esc_html__( 'Max Width', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					$this->combine_selector( '__field-wrap.range-wrap' ) => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'range_slider_heading',
			array(
				'label'     => esc_html__( 'Slider', 'jet-engine' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'track_height',
			array(
				'label'      => esc_html__( 'Track Height', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'selectors'  => array(
					$this->css_selector( ' .range-field::-webkit-slider-runnable-track' ) => 'height: {{SIZE}}{{UNIT}};',
					$this->css_selector( ' .range-field::-moz-range-track' )              => 'height: {{SIZE}}{{UNIT}};',
					$this->css_selector( ' .range-field::-ms-track' )                     => 'height: {{SIZE}}{{UNIT}};',
					$this->css_selector( ' .range-field::-webkit-slider-thumb' )          => 'margin-top: calc( (18px - {{SIZE}}{{UNIT}})/-2 )',
				),
			)
		);

		$this->add_control(
			'thumb_size',
			array(
				'label'      => esc_html__( 'Thumb Size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'selectors'  => array(
					$this->css_selector( '__field.range-field' )                       => 'min-height: {{SIZE}}{{UNIT}};',
					$this->css_selector( '__field.range-field::-webkit-slider-thumb' ) => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; margin-top: calc( ({{SIZE}}{{UNIT}} - 4px)/-2 )',
					$this->css_selector( '__field.range-field::-moz-range-thumb' )     => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					$this->css_selector( '__field.range-field::-ms-thumb' )            => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'thumb_indent',
			array(
				'type'      => Controls_Manager::HIDDEN,
				'default'   => 'style',
				'selectors' => array(
					$this->css_selector( '__field.range-field::-webkit-slider-thumb' ) => 'margin-top: calc( ({{thumb_size.SIZE}}{{thumb_size.UNIT}} - {{track_height.SIZE}}{{track_height.UNIT}})/-2 )',
				),
			)
		);

		$this->add_control(
			'track_border_radius',
			array(
				'label'      => esc_html__( 'Track Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$this->css_selector( '__field.range-field::-webkit-slider-runnable-track' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					$this->css_selector( '__field.range-field::-moz-range-track' )              => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					$this->css_selector( '__field.range-field::-ms-track' )                     => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'thumb_border_radius',
			array(
				'label'      => esc_html__( 'Thumb Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$this->css_selector( '__field.range-field::-webkit-slider-thumb' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					$this->css_selector( '__field.range-field::-moz-range-thumb' )     => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					$this->css_selector( '__field.range-field::-ms-thumb' )            => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'track_bg_color',
			array(
				'label'     => esc_html__( 'Track Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__field.range-field::-webkit-slider-runnable-track' ) => 'background-color: {{VALUE}};',
					$this->css_selector( '__field.range-field::-moz-range-track' )              => 'background-color: {{VALUE}};',
					$this->css_selector( '__field.range-field::-ms-track' )                     => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'thumb_bg_color',
			array(
				'label'     => esc_html__( 'Thumb Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__field.range-field::-webkit-slider-thumb' ) => 'background-color: {{VALUE}};',
					$this->css_selector( '__field.range-field::-moz-range-thumb' )     => 'background-color: {{VALUE}};',
					$this->css_selector( '__field.range-field::-ms-thumb' )            => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'range_value_heading',
			array(
				'label'     => esc_html__( 'Value', 'jet-engine' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'range_value_typography',
				'selector' => $this->combine_selector( '__field-value.range-value' ),
			)
		);

		$this->add_control(
			'range_value_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__field-value.range-value' ) => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'range_prefix_value_size',
			array(
				'label'      => __( 'Prefix size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors'  => array(
					$this->combine_selector( '__field-value.range-value .%s__field-value-prefix' ) => 'font-size: {{SIZE}}px;',
				),
				'separator'  => 'before',
			)
		);

		$this->add_control(
			'range_prefix_value_color',
			array(
				'label'     => __( 'Prefix Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__field-value.range-value .%s__field-value-prefix' ) => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'range_suffix_value_size',
			array(
				'label'      => __( 'Suffix size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors'  => array(
					$this->combine_selector( '__field-value.range-value .%s__field-value-suffix' ) => 'font-size: {{SIZE}}px;',
				),
				'separator'  => 'before',
			)
		);

		$this->add_control(
			'range_suffix_value_color',
			array(
				'label'     => __( 'Suffix Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__field-value.range-value .%s__field-value-suffix' ) => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_headings_style',
			array(
				'label'      => __( 'Heading', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'field_heading_styles_heading',
			array(
				'label'     => esc_html__( 'Label', 'jet-engine' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'field_heading_typography',
				'selector' => $this->combine_selector( '__heading' ),
			)
		);

		$this->add_control(
			'fields_heading_color',
			array(
				'label'     => __( 'Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__heading' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'fields_heading_gap',
			array(
				'label'      => __( 'Gap', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					$this->combine_selector( '__heading' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'field_heading_styles_desc',
			array(
				'label'     => esc_html__( 'Description', 'jet-engine' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'field_desc_typography',
				'selector' => $this->combine_selector( '__heading-desc' ),
			)
		);

		$this->add_control(
			'fields_desc_color',
			array(
				'label'     => __( 'Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__heading-desc' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'fields_heading_desc_gap',
			array(
				'label'      => __( 'Gap', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					$this->combine_selector( '__heading-desc' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_repeater_style',
			array(
				'label'      => __( 'Repeater', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'field_repeater_row_desc',
			array(
				'label'     => esc_html__( 'Repeater row', 'jet-engine' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'booking_form_repeater_row_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$this->combine_selector( '-repeater__row' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'field_repeater_new_desc',
			array(
				'label'     => esc_html__( 'New item button', 'jet-engine' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'tabs_booking_form_repeater_style' );

		$this->start_controls_tab(
			'booking_form_repeater_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-engine' ),
			)
		);

		$this->add_control(
			'booking_form_repeater_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '-repeater__new' ) => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'booking_form_repeater_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '-repeater__new' ) => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'booking_form_repeater_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-engine' ),
			)
		);

		$this->add_control(
			'booking_form_repeater_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '-repeater__new:hover' ) => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'booking_form_repeater_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '-repeater__new:hover' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_form_repeater_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'booking_form_repeater_border_border!' => '',
				),
				'selectors' => array(
					$this->combine_selector( '-repeater__new:hover' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'booking_form_repeater_typography',
				'selector'       => $this->combine_selector( '-repeater__new' ),
				'fields_options' => array(
					'typography' => array(
						'separator' => 'before',
					),
				),
			)
		);

		$this->add_responsive_control(
			'booking_form_repeater_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$this->combine_selector( '-repeater__new' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'booking_form_repeater_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$this->combine_selector( '-repeater__new' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'booking_form_repeater_border',
				'label'       => esc_html__( 'Border', 'jet-engine' ),
				'placeholder' => '1px',
				'selector'    => $this->combine_selector( '-repeater__new' ),
			)
		);

		$this->add_responsive_control(
			'booking_form_repeater_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$this->combine_selector( '-repeater__new' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'booking_form_repeater_box_shadow',
				'selector' => $this->combine_selector( '-repeater__new' ),
			)
		);

		$this->add_responsive_control(
			'booking_form_repeater_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-engine' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-engine' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'jet-engine' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'End', 'jet-engine' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'selectors' => array(
					$this->combine_selector( '-repeater__actions' ) => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'field_repeater_del_desc',
			array(
				'label'     => esc_html__( 'Remove item button', 'jet-engine' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'tabs_booking_form_repeater_del_style' );

		$this->start_controls_tab(
			'booking_form_repeater_del_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-engine' ),
			)
		);

		$this->add_control(
			'booking_form_repeater_del_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '-repeater__remove' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_form_repeater_del_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '-repeater__remove' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'booking_form_repeater_del_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-engine' ),
			)
		);

		$this->add_control(
			'booking_form_repeater_del_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '-repeater__remove:hover' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_form_repeater_del_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '-repeater__remove:hover' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_form_repeater_del_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'booking_form_repeater_del_border_border!' => '',
				),
				'selectors' => array(
					$this->combine_selector( '-repeater__remove:hover' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'booking_form_repeater_del_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'separator'  => 'before',
				'selectors'  => array(
					$this->combine_selector( '-repeater__remove' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'booking_form_repeater_del_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$this->combine_selector( '-repeater__remove' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'booking_form_repeater_del_border',
				'label'       => esc_html__( 'Border', 'jet-engine' ),
				'placeholder' => '1px',
				'selector'    => $this->combine_selector( '-repeater__remove' ),
			)
		);

		$this->add_responsive_control(
			'booking_form_repeater_del_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$this->combine_selector( '-repeater__remove' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'booking_form_repeater_del_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 90,
					),
				),
				'selectors'  => array(
					$this->combine_selector( '-repeater__remove' ) => 'font-size: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'booking_form_repeater_del_box_shadow',
				'selector' => $this->combine_selector( '-repeater__remove' ),
			)
		);

		$this->add_responsive_control(
			'booking_form_repeater_del_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-engine' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'jet-engine' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => esc_html__( 'Middle', 'jet-engine' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Bottom', 'jet-engine' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors' => array(
					$this->combine_selector( '-repeater__row-remove' ) => 'align-self: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_group_break_style',
			array(
				'label'      => __( 'Groups Break', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'group_break_height',
			array(
				'label'      => __( 'Height', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors'  => array(
					$this->combine_selector( '__group-break' ) => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'group_break_color',
			array(
				'label'     => __( 'Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__group-break' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'group_break_gap_before',
			array(
				'label'      => __( 'Gap Before', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					$this->combine_selector( '__group-break' ) => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'group_break_gap_after',
			array(
				'label'      => __( 'Gap After', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					$this->combine_selector( '__group-break' ) => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_required_style',
			array(
				'label'      => __( 'Required mark', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'required_mark',
			array(
				'label'       => __( 'Required mark', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '*',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'required_mark_color',
			array(
				'label'     => __( 'Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__required' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'required_size',
			array(
				'label'      => __( 'Size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors'  => array(
					$this->combine_selector( '__required' ) => 'font-size: {{SIZE}}px;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'form_submit_style',
			array(
				'label'      => esc_html__( 'Submit', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->start_controls_tabs( 'tabs_booking_form_submit_style' );

		$this->start_controls_tab(
			'booking_form_submit_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-engine' ),
			)
		);

		$this->add_control(
			'booking_form_submit_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__submit' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_form_submit_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__submit' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'booking_form_submit_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-engine' ),
			)
		);

		$this->add_control(
			'booking_form_submit_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__submit:hover' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_form_submit_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__submit:hover' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_form_submit_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'booking_form_submit_border_border!' => '',
				),
				'selectors' => array(
					$this->combine_selector( '__submit:hover' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'booking_form_submit_typography',
				'selector'       => $this->combine_selector( '__submit' ),
				'fields_options' => array(
					'typography' => array(
						'separator' => 'before',
					),
				),
			)
		);

		$this->add_responsive_control(
			'booking_form_submit_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$this->combine_selector( '__submit' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'booking_form_submit_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$this->combine_selector( '__submit' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'booking_form_submit_border',
				'label'       => esc_html__( 'Border', 'jet-engine' ),
				'placeholder' => '1px',
				'selector'    => $this->combine_selector( '__submit' ),
			)
		);

		$this->add_responsive_control(
			'booking_form_submit_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$this->combine_selector( '__submit' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'booking_form_submit_box_shadow',
				'selector' => $this->combine_selector( '__submit' ),
			)
		);

		$this->add_responsive_control(
			'booking_form_submit_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-engine' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-engine' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'jet-engine' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'End', 'jet-engine' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
					'stretch'    => array(
						'title' => esc_html__( 'Fullwidth', 'jet-engine' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'selectors' => array(
					$this->combine_selector( '__submit-wrap' ) => 'justify-content: {{VALUE}}; align-items: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'form_next_page_style',
			array(
				'label'      => esc_html__( 'Next Page Button', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->start_controls_tabs( 'tabs_booking_form_next_page_style' );

		$this->start_controls_tab(
			'booking_form_next_page_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-engine' ),
			)
		);

		$this->add_control(
			'booking_form_next_page_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__next-page' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_form_next_page_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__next-page' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'booking_form_next_page_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-engine' ),
			)
		);

		$this->add_control(
			'booking_form_next_page_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__next-page:hover' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_form_next_page_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__next-page:hover' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_form_next_page_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'booking_form_next_page_border_border!' => '',
				),
				'selectors' => array(
					$this->combine_selector( '__next-page:hover' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'booking_form_next_page_typography',
				'selector'       => $this->combine_selector( '__next-page' ),
				'fields_options' => array(
					'typography' => array(
						'separator' => 'before',
					),
				),
			)
		);

		$this->add_responsive_control(
			'booking_form_next_page_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$this->combine_selector( '__next-page' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'booking_form_next_page_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$this->combine_selector( '__next-page' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'booking_form_next_page_border',
				'label'       => esc_html__( 'Border', 'jet-engine' ),
				'placeholder' => '1px',
				'selector'    => $this->combine_selector( '__next-page' ),
			)
		);

		$this->add_responsive_control(
			'booking_form_next_page_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$this->combine_selector( '__next-page' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'booking_form_next_page_box_shadow',
				'selector' => $this->combine_selector( '__next-page' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'form_prev_page_style',
			array(
				'label'      => esc_html__( 'Prev Page Button', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->start_controls_tabs( 'tabs_booking_form_prev_page_style' );

		$this->start_controls_tab(
			'booking_form_prev_page_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-engine' ),
			)
		);

		$this->add_control(
			'booking_form_prev_page_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__prev-page' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_form_prev_page_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__prev-page' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'booking_form_prev_page_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-engine' ),
			)
		);

		$this->add_control(
			'booking_form_prev_page_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__prev-page:hover' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_form_prev_page_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '__prev-page:hover' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_form_prev_page_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'booking_form_prev_page_border_border!' => '',
				),
				'selectors' => array(
					$this->combine_selector( '__prev-page:hover' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'booking_form_prev_page_typography',
				'selector'       => $this->combine_selector( '__prev-page' ),
				'fields_options' => array(
					'typography' => array(
						'separator' => 'before',
					),
				),
			)
		);

		$this->add_responsive_control(
			'booking_form_prev_page_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$this->combine_selector( '__prev-page' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'booking_form_prev_page_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$this->combine_selector( '__prev-page' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'booking_form_prev_page_border',
				'label'       => esc_html__( 'Border', 'jet-engine' ),
				'placeholder' => '1px',
				'selector'    => $this->combine_selector( '__prev-page' ),
			)
		);

		$this->add_responsive_control(
			'booking_form_prev_page_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$this->combine_selector( '__prev-page' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'booking_form_prev_page_box_shadow',
				'selector' => $this->combine_selector( '__prev-page' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'form_messages_style',
			array(
				'label'      => esc_html__( 'Messages', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'booking_messages_typography',
				'selector' => $this->combine_selector( '-message' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'booking_messages_border',
				'label'       => esc_html__( 'Border', 'jet-engine' ),
				'placeholder' => '1px',
				'selector'    => $this->combine_selector( '-message' ),
			)
		);

		$this->add_responsive_control(
			'booking_messages_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$this->combine_selector( '-message' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_booking_messages_style' );

		$this->start_controls_tab(
			'booking_messages_success',
			array(
				'label' => esc_html__( 'Success', 'jet-engine' ),
			)
		);

		$this->add_control(
			'booking_messages_success_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '-message--success' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_messages_success_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '-message--success' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_messages_success_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'booking_messages_border_border!' => '',
				),
				'selectors' => array(
					$this->combine_selector( '-message--success' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'booking_messages_box_shadow_success',
				'selector' => $this->combine_selector( '-message--success' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'booking_messages_error',
			array(
				'label' => esc_html__( 'Error', 'jet-engine' ),
			)
		);

		$this->add_control(
			'booking_messages_error_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '-message--error' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_messages_error_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->combine_selector( '-message--error' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'booking_messages_error_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'booking_messages_border_border!' => '',
				),
				'selectors' => array(
					$this->combine_selector( '-message--error' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'booking_messages_box_shadow_error',
				'selector' => $this->combine_selector( '-message--error' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'booking_messages_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'separator'  => 'before',
				'selectors'  => array(
					$this->combine_selector( '-message' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'booking_messages_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$this->combine_selector( '-message' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'booking_messages_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-engine' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'jet-engine' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-engine' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'jet-engine' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					$this->combine_selector( '-message' ) => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'field_messages',
			array(
				'label'     => esc_html__( 'Field Messages', 'jet-engine' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'field_messages_font_size',
			array(
				'label'      => __( 'Font Size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 9,
						'max' => 50,
					),
				),
				'selectors'  => array(
					$this->multiple_selectors(
						$this->combine_selector( '__field-error' ),
						$this->combine_selector( ' .jet-engine-file-upload__errors', 'field_messages_font_size' )
					) => 'font-size: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'field_messages_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$this->multiple_selectors(
						$this->combine_selector( '__field-error' ),
						$this->combine_selector( ' .jet-engine-file-upload__errors', 'field_messages_color' )
					) => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'field_messages_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$this->multiple_selectors(
						$this->combine_selector( '__field-error' ),
						$this->combine_selector( ' .jet-engine-file-upload__errors', 'field_messages_margin' )
					) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'field_messages_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-engine' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'jet-engine' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-engine' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'jet-engine' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					$this->multiple_selectors(
						$this->combine_selector( '__field-error' ),
						$this->combine_selector( ' .jet-engine-file-upload__errors', 'field_messages_alignment' )
					) => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Returns CSS selector for nested element
	 *
	 * @param  [type] $el [description]
	 *
	 * @return [type]     [description]
	 */
	public function css_selector( $el = null ) {
		$namespace = 'jet-form';
		$base      = "{{WRAPPER}} .{$namespace}";

		if ( ! is_array( $el ) ) {
			return ( $base . sprintf( $el, $namespace ) );
		} else {

			$res = array();
			foreach ( $el as $selector ) {
				$res[] = ( $base . sprintf( $selector, $namespace ) );
			}

			return implode( ', ', $res );
		}
	}

	protected function combine_selector( $engine = '', $control_id = '' ) {
		$selectors = $this->css_selector( $engine );

		$additional = apply_filters( 'jet-engine/booking-form/combine-selector', false, $engine, $control_id );

		if ( $additional ) {
			$selectors .= ', ' . $additional;
		}

		return $selectors;
	}

	protected function multiple_selectors( ...$selectors ) {
		return implode( ', ', $selectors );
	}

	protected function render() {

		$instance = jet_engine()->listings->get_render_instance( 'booking-form', $this->get_settings() );

		$is_edit_mode = \Elementor\Plugin::instance()->editor->is_edit_mode();

		if ( $is_edit_mode ) {
			$instance->set_edit_mode( true );
		}

		$instance->render_content();
	}

}
