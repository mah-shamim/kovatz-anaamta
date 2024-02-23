<?php
namespace Elementor;

use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Listing_Dynamic_Terms_Widget extends Widget_Base {

	private $source     = false;
	private $show_field = true;

	public function get_name() {
		return 'jet-listing-dynamic-terms';
	}

	public function get_title() {
		return __( 'Dynamic Terms', 'jet-engine' );
	}

	public function get_icon() {
		return 'jet-engine-icon-dynamic-terms';
	}

	public function get_categories() {
		return array( 'jet-listing-elements' );
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetengine-dynamic-terms-widget-overview/?utm_source=jetengine&utm_medium=dynamic-terms&utm_campaign=need-help';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-engine' ),
			)
		);

		$this->add_control(
			'from_tax',
			array(
				'label'   => __( 'From taxonomy', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'groups'  => $this->get_taxonomies_for_options(),
			)
		);

		$this->add_control(
			'show_all_terms',
			array(
				'label'        => esc_html__( 'Show all terms', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'terms_num',
			array(
				'label'   => esc_html__( 'Terms number to show', 'jet-engine' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
				'min'     => 1,
				'max'     => 20,
				'step'    => 1,
				'condition' => array(
					'show_all_terms!' => 'yes',
				),
			)
		);

		$this->add_control(
			'terms_delimiter',
			array(
				'label'       => __( 'Delimiter', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => ',',
			)
		);

		$this->add_control(
			'terms_linked',
			array(
				'label'        => esc_html__( 'Linked terms', 'jet-engine' ),
				'description'  => __( 'Terms labels are linked to term archive page', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
			$this->add_control(
				'selected_terms_icon',
				array(
					'label'            => __( 'Terms Icon', 'jet-engine' ),
					'type'             => Controls_Manager::ICONS,
					'label_block'      => true,
					'fa4compatibility' => 'field_icon',

				)
			);
		} else {
			$this->add_control(
				'terms_icon',
				array(
					'label'       => __( 'Terms Icon', 'jet-engine' ),
					'type'        => Controls_Manager::ICON,
					'label_block' => true,
					'file'        => '',
					'default'     => '',
				)
			);
		}

		$this->add_control(
			'terms_prefix',
			array(
				'label'       => __( 'Text before terms list', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
			)
		);

		$this->add_control(
			'terms_suffix',
			array(
				'label'       => __( 'Text after terms list', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => esc_html__( 'Order By', 'jet-engine' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'name',
				'separator' => 'before',
				'options'   => array(
					'name'        => esc_html__( 'Name', 'jet-engine' ),
					'slug'        => esc_html__( 'Slug', 'jet-engine' ),
					'term_group'  => esc_html__( 'Term group', 'jet-engine' ),
					'term_id'     => esc_html__( 'Term ID', 'jet-engine' ),
					'description' => esc_html__( 'Description', 'jet-engine' ),
					'parent'      => esc_html__( 'Parent', 'jet-engine' ),
					'term_order'  => esc_html__( 'Term Order', 'jet-engine' ),
					'count'       => esc_html__( 'By the number of objects associated with the term', 'jet-engine' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => array(
					'ASC'  => esc_html__( 'ASC', 'jet-engine' ),
					'DESC' => esc_html__( 'DESC', 'jet-engine' ),
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
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default_object',
				'options'   => jet_engine()->listings->allowed_context_list(),
				'separator' => 'before',
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
			'terms_alignment',
			array(
				'label'   => __( 'Alignment', 'jet-engine' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
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
			'section_link_style',
			array(
				'label'      => __( 'Labels', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'selector' => $this->css_selector( '__link' ),
			)
		);

		$this->start_controls_tabs( 'tabs_form_submit_style' );

		$this->start_controls_tab(
			'dynamic_link_normal',
			array(
				'label' => __( 'Normal', 'jet-engine' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'link_bg',
				'selector' => $this->css_selector( '__link' ),
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'  => __( 'Text Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__link' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'dynamic_link_hover',
			array(
				'label' => __( 'Hover', 'jet-engine' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'link_bg_hover',
				'selector' => $this->css_selector( '__link:hover' ),
			)
		);

		$this->add_control(
			'link_color_hover',
			array(
				'label'  => __( 'Text Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__link:hover' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'link_hover_border_color',
			array(
				'label' => __( 'Border Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array(
					'link_border_border!' => '',
				),
				'selectors' => array(
					$this->css_selector( '__link:hover' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'link_padding',
			array(
				'label'      => __( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'selectors'  => array(
					$this->css_selector( '__link' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'link_margin',
			array(
				'label'      => __( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'selectors'  => array(
					$this->css_selector( '__link' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'link_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'selector'       => $this->css_selector( '__link' ),
			)
		);

		$this->add_responsive_control(
			'link_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					$this->css_selector( '__link' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'link_box_shadow',
				'selector' => $this->css_selector( '__link' ),
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
			'delimiter_size',
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
					$this->css_selector( '__delimiter' ) => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'delimiter_l_gap',
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
					$this->css_selector( '__delimiter' ) => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'delimiter_r_gap',
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
					$this->css_selector( '__delimiter' ) => 'margin-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_prefix_style',
			array(
				'label'      => __( 'Text before', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'prefix_typography',
				'selector' => $this->css_selector( '__prefix' ),
			)
		);

		$this->add_control(
			'prefix_color',
			array(
				'label' => __( 'Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__prefix' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'prefix_gap',
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
					'body:not(.rtl) ' . $this->css_selector( '__prefix' ) => 'margin-right: {{SIZE}}{{UNIT}};',
					'body.rtl ' . $this->css_selector( '__prefix' ) => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_suffix_style',
			array(
				'label'      => __( 'Text after', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'suffix_typography',
				'selector' => $this->css_selector( '__suffix' ),
			)
		);

		$this->add_control(
			'suffix_color',
			array(
				'label' => __( 'Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__suffix' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'suffix_gap',
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
					'body:not(.rtl) ' . $this->css_selector( '__suffix' ) => 'margin-left: {{SIZE}}{{UNIT}};',
					'body.rtl ' . $this->css_selector( '__suffix' ) => 'margin-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Returns all taxonomies list for options
	 *
	 * @return [type] [description]
	 */
	public function get_taxonomies_for_options() {

		$result     = array();
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

		foreach ( $taxonomies as $taxonomy ) {

			if ( empty( $taxonomy->object_type ) || ! is_array( $taxonomy->object_type ) ) {
				continue;
			}

			foreach ( $taxonomy->object_type as $object ) {
				if ( empty( $result[ $object ] ) ) {
					$post_type = get_post_type_object( $object );

					if ( ! $post_type ) {
						continue;
					}

					$result[ $object ] = array(
						'label'   => $post_type->labels->name,
						'options' => array(),
					);
				}

				$result[ $object ]['options'][ $taxonomy->name ] = $taxonomy->labels->name;

			};
		}

		return $result;

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
		jet_engine()->listings->render_item( 'dynamic-terms', $this->get_settings() );
	}

}
