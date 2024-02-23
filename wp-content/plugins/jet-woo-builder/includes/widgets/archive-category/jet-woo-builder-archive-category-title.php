<?php
/**
 * Class: Jet_Woo_Builder_Archive_Category_Title
 * Name: Archive Category Title
 * Slug: jet-woo-builder-archive-category-title
 */

namespace Elementor;

use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Archive_Category_Title extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-builder-archive-category-title';
	}

	public function get_title() {
		return __( 'Archive Category Title', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-category-title';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/woocommerce-jetwoobuilder-settings-how-to-create-and-set-a-custom-categories-archive-template/?utm_source=need-help&utm_medium=jet-woo-categories&utm_campaign=jetwoobuilder';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'category' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			[
				'label' => __( 'Title', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'is_linked',
			[
				'label' => __( 'Enable Permalink', 'jet-woo-builder' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'open_new_tab',
			[
				'label'     => __( 'Open in New Window', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'is_linked' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label'   => __( 'HTML Tag', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h5',
				'options' => jet_woo_builder_tools()->get_available_title_html_tags(),
			]
		);

		$this->add_control(
			'title_trim_type',
			[
				'label'   => __( 'Trim Type', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'word',
				'options' => jet_woo_builder_tools()->get_available_title_trim_types(),
			]
		);

		$this->add_control(
			'title_length',
			[
				'label'       => __( 'Length', 'jet-woo-builder' ),
				'description' => __( 'Set -1 to show full title and 0 to hide it.', 'jet-woo-builder' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => -1,
				'default'     => 10,
			]
		);

		$this->add_control(
			'title_line_wrap',
			[
				'label'        => __( 'Enable Line Wrap', 'jet-woo-builder' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'jet-woo-builder-title-line-wrap-',
			]
		);

		$this->add_control(
			'title_tooltip',
			[
				'label' => __( 'Enable Tooltip', 'jet-woo-builder' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_archive_category_title_style',
			[
				'label' => __( 'Title', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'archive_category_title_typography',
				'selector' => $this->css_selector(),
			]
		);

		$this->start_controls_tabs( 'tabs_archive_category_title_style' );

		$this->start_controls_tab(
			'tab_archive_category_title_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'archive_category_title_color_normal',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector() => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'archive_category_title_bg_normal',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector() => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_archive_category_title_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'archive_category_title_color_hover',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ':hover' ) => ' color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'archive_category_title_bg_hover',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ':hover' ) => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'archive_category_title_border_hover',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ':hover' ) => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'archive_category_title_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'archive_category_title_border',
				'label'     => __( 'Border', 'jet-woo-builder' ),
				'separator' => 'before',
				'selector'  => $this->css_selector(),
			]
		);

		$this->add_control(
			'archive_category_title_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					$this->css_selector() => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'archive_category_title_shadow',
				'selector' => $this->css_selector(),
			]
		);

		$this->add_responsive_control(
			'archive_category_title_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					$this->css_selector() => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'archive_category_title_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					$this->css_selector() => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'archive_category_title_alignment',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => jet_woo_builder_tools()->get_available_h_align_types( true ),
				'selectors' => [
					$this->css_selector() => 'text-align: {{VALUE}};',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * CSS selector.
	 *
	 * Returns CSS selector for nested element.
	 *
	 * @since  1.3.0
	 * @access public
	 *
	 * @param null $el Selector.
	 *
	 * @return string
	 */
	public function css_selector( $el = null ) {
		return sprintf( '{{WRAPPER}} .%1$s%2$s', $this->get_name(), $el );
	}

	public static function render_callback( $settings = array(), $args = [] ) {

		$category      = ! empty( $args ) ? $args['category'] : get_queried_object();
		$open_wrap     = '<' . $settings['html_tag'] . '>';
		$close_wrap    = '</' . $settings['html_tag'] . '>';
		$target_attr   = $settings['open_new_tab'] ? 'target="_blank"' : '';
		$title_tooltip = '';

		if ( $settings['enable_permalink'] ) {
			$open_wrap  = $open_wrap . '<a href="' . jet_woo_builder_tools()->get_term_permalink( $category->term_id ) . '" ' . $target_attr . '>';
			$close_wrap = '</a>' . $close_wrap;
		}

		$title = jet_woo_builder_tools()->trim_text(
			$category->name,
			$settings['title_length'],
			$settings['trim_type'],
			'...'
		);

		if ( $settings['title_tooltip'] ) {
			$title_tooltip = 'title="' . $category->name . '"';
		}

		printf( '%s <div class="jet-woo-builder-archive-category-title" %s> %s </div> %s', $open_wrap, $title_tooltip, $title, $close_wrap );

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$macros_settings = [
			'enable_permalink' => isset( $settings['is_linked'] ) ? filter_var( $settings['is_linked'], FILTER_VALIDATE_BOOLEAN ) : false,
			'open_new_tab'     => isset( $settings['open_new_tab'] ) ? filter_var( $settings['open_new_tab'], FILTER_VALIDATE_BOOLEAN ) : false,
			'html_tag'         => isset( $settings['title_html_tag'] ) ? jet_woo_builder_tools()->sanitize_html_tag( $settings['title_html_tag'] ) : 'h5',
			'trim_type'        => isset( $settings['title_trim_type'] ) ? $settings['title_trim_type'] : 'word',
			'title_length'     => isset( $settings['title_length'] ) ? $settings['title_length'] : 10,
			'title_tooltip'    => isset( $settings['title_tooltip'] ) ? filter_var( $settings['title_tooltip'], FILTER_VALIDATE_BOOLEAN ) : false,
		];

		if ( jet_woo_builder_tools()->is_builder_content_save() ) {
			echo jet_woo_builder()->parser->get_macros_string( $this->get_name(), $macros_settings );
		} else {
			echo self::render_callback( $macros_settings, jet_woo_builder()->woocommerce->get_current_args() );
		}

	}

}
