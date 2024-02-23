<?php
namespace Elementor;

use Elementor\Group_Control_Border;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Listing_Dynamic_Meta_Widget extends Widget_Base {

	private $source = false;

	public function get_name() {
		return 'jet-listing-dynamic-meta';
	}

	public function get_title() {
		return __( 'Dynamic Meta', 'jet-engine' );
	}

	public function get_icon() {
		return 'jet-engine-icon-dynamic-meta';
	}

	public function get_categories() {
		return array( 'jet-listing-elements' );
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetengine-dynamic-meta-widget-overview/?utm_source=jetengine&utm_medium=dynamic-meta&utm_campaign=need-help';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-engine' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'type',
			array(
				'label'   => esc_html__( 'Type', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'     => __( 'Date', 'jet-engine' ),
					'author'   => __( 'Author', 'jet-engine' ),
					'comments' => __( 'Comments', 'jet-engine' ),
				),
			)
		);



		if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
			$repeater->add_control(
				'selected_icon',
				array(
					'label'            => __( 'Icon', 'jet-engine' ),
					'type'             => Controls_Manager::ICONS,
					'label_block'      => true,
					'fa4compatibility' => 'icon',

				)
			);
		} else {
			$repeater->add_control(
				'icon',
				array(
					'label'       => __( 'Icon', 'jet-engine' ),
					'type'        => Controls_Manager::ICON,
					'label_block' => true,
					'file'        => '',
					'default'     => '',
				)
			);
		}

		$repeater->add_control(
			'prefix',
			array(
				'label'   => esc_html__( 'Prefix', 'jet-engine' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$repeater->add_control(
			'suffix',
			array(
				'label'   => esc_html__( 'Suffix', 'jet-engine' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'meta_items',
			array(
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'type' => 'date',
						'icon' => '',
					),
					array(
						'type' => 'author',
						'icon' => '',
					),
					array(
						'type' => 'comments',
						'icon' => '',
					),
				),
				'title_field' => '{{{ type }}}',
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'Layout', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => array(
					'inline' => __( 'Inline', 'jet-engine' ),
					'list'   => __( 'List', 'jet-engine' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_date',
			array(
				'label' => __( 'Date Settings', 'jet-engine' ),
			)
		);

		$this->add_control(
			'date_format',
			array(
				'label'       => esc_html__( 'Format', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'F j, Y',
				'description' => sprintf( '<a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank">%s</a>', __( 'Documentation on date and time formatting', 'jet-engine' ) ),
			)
		);

		$this->add_control(
			'date_link',
			array(
				'label'   => esc_html__( 'Link', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'archive',
				'options' => array(
					'archive' => __( 'Archive', 'jet-engine' ),
					'single'  => __( 'Post', 'jet-engine' ),
					'no-link' => __( 'None', 'jet-engine' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_author',
			array(
				'label' => __( 'Author Settings', 'jet-engine' ),
			)
		);

		$this->add_control(
			'author_link',
			array(
				'label'   => esc_html__( 'Link', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'archive',
				'options' => array(
					'archive' => __( 'Author Archives', 'jet-engine' ),
					'single'  => __( 'Post', 'jet-engine' ),
					'no-link' => __( 'None', 'jet-engine' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_comments',
			array(
				'label' => __( 'Comments Settings', 'jet-engine' ),
			)
		);

		$this->add_control(
			'comments_link',
			array(
				'label'   => esc_html__( 'Link', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'single',
				'options' => array(
					'single'  => __( 'Post', 'jet-engine' ),
					'no-link' => __( 'None', 'jet-engine' ),
				),
			)
		);

		$this->add_control(
			'zero_comments_format',
			array(
				'label'       => esc_html__( 'Zero Comments Format', 'jet-engine' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => '0',
			)
		);

		$this->add_control(
			'one_comment_format',
			array(
				'label'       => esc_html__( 'One Comments Format', 'jet-engine' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => '1',
			)
		);

		$this->add_control(
			'more_comments_format',
			array(
				'label'       => esc_html__( 'More Comments Format', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '%',
				'description' => __( 'Use % for comments number', 'jet-engine' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label'      => __( 'General', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'meta_alignment',
			array(
				'label'   => __( 'Alignment', 'jet-engine' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'flex-start',
				'options' => array(
					'left'    => array(
						'title' => __( 'Left', 'jet-engine' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'jet-engine' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'jet-engine' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors'  => array(
					$this->css_selector() => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_item_style',
			array(
				'label'      => __( 'Items', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_typography',
				'selector' => $this->css_selector( '__item' ) . ', ' . $this->css_selector( '__item-val' ),
			)
		);

		$this->start_controls_tabs( 'tabs_form_submit_style' );

		$this->start_controls_tab(
			'dynamic_item_normal',
			array(
				'label' => __( 'Normal', 'jet-engine' ),
			)
		);

		$this->add_control(
			'item_bg_color',
			array(
				'label'  => __( 'Background Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__item' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'item_color',
			array(
				'label'  => __( 'Text Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__item' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'dynamic_item_hover',
			array(
				'label' => __( 'Hover', 'jet-engine' ),
			)
		);

		$this->add_control(
			'item_bg_color_hover',
			array(
				'label'  => __( 'Background Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__item:hover' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'item_color_hover',
			array(
				'label'  => __( 'Text Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__item:hover' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'item_hover_border_color',
			array(
				'label' => __( 'Border Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array(
					'item_border_border!' => '',
				),
				'selectors' => array(
					$this->css_selector( '__item:hover' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => __( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'selectors'  => array(
					$this->css_selector( '__item' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_margin',
			array(
				'label'      => __( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'selectors'  => array(
					$this->css_selector( '__item' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'item_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'selector'       => $this->css_selector( '__item' ),
			)
		);

		$this->add_responsive_control(
			'item_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					$this->css_selector( '__item' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_box_shadow',
				'selector' => $this->css_selector( '__item' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			array(
				'label'      => __( 'Icon', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label' => __( 'Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__icon' ) => 'color: {{VALUE}}',
					$this->css_selector( '__icon :is(svg, path)' ) => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors'  => array(
					$this->css_selector( '__icon' ) => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_gap',
			array(
				'label'      => __( 'Gap', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'body:not(.rtl) ' . $this->css_selector( '__icon' ) => 'margin-right: {{SIZE}}{{UNIT}};',
					'body.rtl ' . $this->css_selector( '__icon' ) => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_item_val_style',
			array(
				'label'      => __( 'Items Value', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->start_controls_tabs( 'tabs_item_val_style' );

		$this->start_controls_tab(
			'dynamic_item_val_normal',
			array(
				'label' => __( 'Normal', 'jet-engine' ),
			)
		);

		$this->add_control(
			'item_val_bg_color',
			array(
				'label'  => __( 'Background Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__item-val' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'item_val_color',
			array(
				'label'  => __( 'Text Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__item-val' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'dynamic_item_val_hover',
			array(
				'label' => __( 'Hover', 'jet-engine' ),
			)
		);

		$this->add_control(
			'item_val_bg_color_hover',
			array(
				'label'  => __( 'Background Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__item-val:hover' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'item_val_color_hover',
			array(
				'label'  => __( 'Text Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__item-val:hover' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'item_val_hover_border_color',
			array(
				'label' => __( 'Border Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array(
					'item_border_border!' => '',
				),
				'selectors' => array(
					$this->css_selector( '__item-val:hover' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'item_val_padding',
			array(
				'label'      => __( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'selectors'  => array(
					$this->css_selector( '__item-val' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_val_margin',
			array(
				'label'      => __( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'selectors'  => array(
					$this->css_selector( '__item-val' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'item_val_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'selector'       => $this->css_selector( '__item-val' ),
			)
		);

		$this->add_responsive_control(
			'item_val_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					$this->css_selector( '__item-val' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_val_box_shadow',
				'selector' => $this->css_selector( '__item-val' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_prefix_style',
			array(
				'label'      => __( 'Prefix', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'prefix_l_gap',
			array(
				'label'      => __( 'Left Gap', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					$this->css_selector( '__prefix' ) => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'prefix_r_gap',
			array(
				'label'      => __( 'Right Gap', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					$this->css_selector( '__prefix' ) => 'margin-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_suffix_style',
			array(
				'label'      => __( 'Suffix', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'suffix_l_gap',
			array(
				'label'      => __( 'Left Gap', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					$this->css_selector( '__suffix' ) => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'suffix_r_gap',
			array(
				'label'      => __( 'Right Gap', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					$this->css_selector( '__suffix' ) => 'margin-right: {{SIZE}}{{UNIT}};',
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

	protected function render() {
		jet_engine()->listings->render_item( 'dynamic-meta', $this->get_settings() );
	}

}
