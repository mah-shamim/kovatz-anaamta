<?php
namespace Elementor;

use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Engine_Check_Mark_Widget extends Widget_Base {

	private $source = false;

	public function get_name() {
		return 'jet-engine-forms-check-mark';
	}

	public function get_title() {
		return __( 'Check Mark', 'jet-engine' );
	}

	public function get_icon() {
		return 'jet-engine-icon-check-mark';
	}

	public function get_categories() {
		return array( 'jet-listing-elements' );
	}

	public function get_script_depends() {
		return array();
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

		$this->start_controls_tabs( 'tabs_check_mark_icons' );

		$this->start_controls_tab(
			'tabs_check_mark_icon_default',
			array(
				'label' => esc_html__( 'Default', 'jet-engine' ),
			)
		);

		$this->add_control(
			'check_mark_icon_default',
			array(
				'label'       => __( 'Default Icon', 'jet-engine' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => true,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_check_mark_icon_checked',
			array(
				'label' => esc_html__( 'Checked', 'jet-engine' ),
			)
		);

		$this->add_control(
			'check_mark_icon_checked',
			array(
				'label'       => __( 'Checked Icon', 'jet-engine' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => true,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'check_mark_style',
			array(
				'label'      => esc_html__( 'Style', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'check_mark_box_size',
			array(
				'label'      => __( 'Box Size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 600,
					),
				),
				'selectors'  => array(
					$this->css_selector( array( '--default', '--checked' ) ) => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'check_mark_icon_size',
			array(
				'label'      => __( 'Icon Size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 9,
						'max' => 100,
					),
				),
				'selectors'  => array(
					$this->css_selector( array( '--default', '--checked' ) ) => 'font-size: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'check_mark_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$this->css_selector( array( '--default', '--checked' ) ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_check_marks_styles' );

		$this->start_controls_tab(
			'check_mark_styles_default',
			array(
				'label' => esc_html__( 'Default', 'jet-engine' ),
			)
		);

		$this->add_control(
			'check_mark_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '--default' ) => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'check_mark_icon_color',
			array(
				'label' => esc_html__( 'Icon Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '--default' ) => 'color: {{VALUE}};',
					$this->css_selector( '--default :is(svg, path)' ) => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'check_mark_border',
				'label'       => esc_html__( 'Border', 'jet-engine' ),
				'placeholder' => '1px',
				'selector'    => $this->css_selector( '--default' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'check_mark_styles_checked',
			array(
				'label' => esc_html__( 'Checked', 'jet-engine' ),
			)
		);

		$this->add_control(
			'check_mark_bg_color_checked',
			array(
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '--checked' ) => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'check_mark_icon_color_checked',
			array(
				'label' => esc_html__( 'Icon Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '--checked' ) => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'check_mark_border_checked',
				'label'       => esc_html__( 'Border', 'jet-engine' ),
				'placeholder' => '1px',
				'selector'    => $this->css_selector( '--checked' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'check_mark_box_shadow',
				'separator' => 'before',
				'selector'  => $this->css_selector( array( '--default', '--checked' ) ),
				'fields_options' => array(
					'box_shadow_type' => array(
						'separator' => 'before',
					),
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Returns CSS selector for nested element
	 *
	 * @param  string|array $el
	 * @return string
	 */
	public function css_selector( $el = null ) {
		if ( ! is_array( $el ) ) {
			return sprintf( '{{WRAPPER}} .jet-form__check-mark%s', $el );
		} else {

			$res = array();
			foreach ( $el as $selector ) {
				$res[] = sprintf( '{{WRAPPER}} .jet-form__check-mark%s', $selector );
			}

			return implode( ', ', $res );
		}
	}

	protected function render() {
		$instance = jet_engine()->listings->get_render_instance( 'check-mark', $this->get_settings_for_display() );
		$instance->render_content();
	}

}
