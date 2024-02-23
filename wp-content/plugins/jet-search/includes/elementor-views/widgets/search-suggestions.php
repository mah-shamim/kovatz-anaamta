<?php
/**
 * Class: Jet_Search_Suggestions_Widget
 * Name: Search Suggestions
 * Slug: jet-search-suggestions
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Jet Ajax Search Widget.
 */
class Jet_Search_Search_Suggestions_Widget extends Jet_Search_Widget_Base {

	public $current_query = null;

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'jet-search-suggestions';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Search Suggestions', 'jet-search' );
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/article-category/jet-search-suggestions/';
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'jet-search-icon-suggestions';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'cherry' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		$css_scheme = apply_filters(
			'jet-search/search-suggestions/css-scheme',
			array(
				'form'                   => '.jet-search-suggestions__form',
				'form_focus'             => '.jet-search-suggestions__form--focus',
				'fields_holder'          => '.jet-search-suggestions__fields-holder',
				'field_wrapper'          => '.jet-search-suggestions__field-wrapper',
				'field'                  => '.jet-search-suggestions__field',
				'categories'             => '.jet-search-suggestions__categories',
				'categories_select'      => '.jet-search-suggestions__categories-select',
				'categories_select_icon' => '.jet-search-suggestions__categories-select-icon',
				'submit'                 => '.jet-search-suggestions__submit',
				'submit_icon'            => '.jet-search-suggestions__submit-icon',
				'submit_label'           => '.jet-search-suggestions__submit-label',
				'focus_area'             => '.jet-search-suggestions__focus-area',
				'focus_area_item'        => '.jet-search-suggestions__focus-area-item',
				'focus_area_item_title'  => '.jet-search-suggestions__focus-area-item-title',
				'inline_area'            => '.jet-search-suggestions__inline-area',
				'inline_area_item'       => '.jet-search-suggestions__inline-area-item',
				'inline_area_item_title' => '.jet-search-suggestions__inline-area-item-title',
				'message'                => '.jet-search-suggestions__message',
				'spinner'                => '.jet-search-suggestions__spinner',
			)
		);

		/**
		 * `Search Form` Section
		 */
		$this->start_controls_section(
			'section_search_form_settings',
			array(
				'label' => esc_html__( 'Search Form', 'jet-search' ),
			)
		);

		$this->add_control(
			'search_placeholder_text',
			array(
				'label'   => esc_html__( 'Placeholder Text', 'jet-search' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Search ...', 'jet-search' ),
			)
		);

		$this->add_control(
			'show_search_suggestions_list_on_focus_preloader',
			array(
				'label'        => esc_html__( 'Show preloader', 'jet-search' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-search' ),
				'label_off'    => esc_html__( 'No', 'jet-search' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'Add box with loading animation while suggestions data is fetching from the server', 'jet-search' ),
			)
		);

		$this->add_control(
			'highlight_searched_text',
			array(
				'label'   => esc_html__( 'Highlight Searched Text', 'jet-search' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'search_suggestions_quantity_limit',
			array(
				'label'     => esc_html__( 'Number of Suggestions', 'jet-search' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 10,
				'max'       => 50,
				'min'       => 1,
			)
		);

		$this->add_control(
			'show_search_submit',
			array(
				'label'     => esc_html__( 'Show Submit Button', 'jet-search' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'search_submit_label',
			array(
				'label'     => esc_html__( 'Submit Button Label', 'jet-search' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					'show_search_submit' => 'yes',
				),
			)
		);

		$this->add_control(
			'selected_search_submit_icon',
			array(
				'label'            => esc_html__( 'Submit Button Icon', 'jet-search' ),
				'label_block'      => false,
				'type'             => Controls_Manager::ICONS,
				'skin'             => 'inline',
				'fa4compatibility' => 'search_submit_icon',
				'default'          => array(
					'value'   => 'fas fa-search',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'show_search_submit' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_search_category_list',
			array(
				'label'     => esc_html__( 'Show Categories List', 'jet-search' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'search_taxonomy',
			array(
				'label'     => esc_html__( 'Taxonomy', 'jet-search' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'category',
				'options'   => \Jet_Search_Tools::get_taxonomies(),
				'condition' => array(
					'show_search_category_list' => 'yes',
				),
			)
		);

		$this->add_control(
			'search_category_select_placeholder',
			array(
				'label'     => esc_html__( 'Select Placeholder', 'jet-search' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'All Categories', 'jet-search' ),
				'condition' => array(
					'show_search_category_list' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_search_suggestions_list_inline',
			array(
				'label'     => esc_html__( 'Show Suggestions Below Search Form', 'jet-search' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'search_suggestions_list_inline',
			array(
				'label'       => esc_html__( 'Suggestions List', 'jet-search' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'popular',
				'options'     => array(
					'popular' => esc_html__( 'Most popular', 'jet-search' ),
					'latest'  => esc_html__( 'Latest', 'jet-search' ),
					'manual'  => esc_html__( 'Manual', 'jet-search' ),
				),
				'condition' => array(
					'show_search_suggestions_list_inline' => 'yes',
				),
			)
		);

		$this->add_control(
			'search_suggestions_list_inline_quantity',
			array(
				'label'     => esc_html__( 'Number of Suggestions', 'jet-search' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 10,
				'max'       => 50,
				'min'       => 1,
				'condition' => array(
					'search_suggestions_list_inline!' => 'manual',
					'show_search_suggestions_list_inline' => 'yes',
				),
			)
		);

		$this->add_control(
			'search_suggestions_list_inline_manual',
			array(
				'label'       => esc_html__( 'List of Manual Suggestions', 'jet-search' ),
				'description' => esc_html__( 'Write multiple suggestions by semicolon separated with "," sign.', 'jet-search' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( '', 'jet-search' ),
				'condition'   => array(
					'search_suggestions_list_inline' => 'manual',
					'show_search_suggestions_list_inline' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_search_suggestions_list_on_focus',
			array(
				'label'     => esc_html__( 'Show Suggestions on Input Focus', 'jet-search' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'search_suggestions_list_on_focus',
			array(
				'label'       => esc_html__( 'Suggestions List', 'jet-search' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'popular',
				'options'     => array(
					'popular' => esc_html__( 'Most popular', 'jet-search' ),
					'latest'  => esc_html__( 'Latest', 'jet-search' ),
					'manual'  => esc_html__( 'Manual', 'jet-search' ),
				),
				'separator' => 'before',
				'condition' => array(
					'show_search_suggestions_list_on_focus' => 'yes',
				),
			)
		);

		$this->add_control(
			'search_suggestions_list_on_focus_quantity',
			array(
				'label'     => esc_html__( 'Number of Suggestions', 'jet-search' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 10,
				'max'       => 50,
				'min'       => 1,
				'condition' => array(
					'search_suggestions_list_on_focus!' => 'manual',
					'show_search_suggestions_list_on_focus' => 'yes',
				),
			)
		);

		$this->add_control(
			'search_suggestions_list_on_focus_manual',
			array(
				'label'       => esc_html__( 'List of Manual Suggestions ', 'jet-search' ),
				'description' => esc_html__( 'Write multiple suggestions by semicolon separated with "," sign.', 'jet-search' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( '', 'jet-search' ),
				'condition'   => array(
					'search_suggestions_list_on_focus' => 'manual',
					'show_search_suggestions_list_on_focus' => 'yes',
				),
			)
		);

		// $this->add_control(
		// 	'search_results_url',
		// 	array(
		// 		'label'       => esc_html__( 'Search results URL', 'jet-search' ),
		// 		'label_block' => true,
		// 		'type'        => Controls_Manager::TEXTAREA,
		// 		'default'     => esc_html__( '', 'jet-search' ),
		// 		'separator' => 'before',
		// 	)
		// );

		$this->add_control(
			'manage_saved_suggestions',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => sprintf(
						esc_html__( 'Manage Saved Suggestions %1$s', 'jet-search' ),
						'<a target="_blank" href="' . jet_search_settings()->get_settings_page_link() . '">' . esc_html__( 'here', 'jet-search' ) . '</a>'
					),
					'separator' => 'before',
				)
			);

		$this->end_controls_section();


		/**
		 * `WooCommerce` Section
		 */

		if ( function_exists( 'WC' ) ) {

			$this->start_controls_section(
				'section_woocommerce',
				array(
					'label' => esc_html__( 'WooCommerce', 'jet-search' ),
				)
			);

			$this->add_control(
				'is_product_search',
				array(
					'label'        => esc_html__( 'Is Product Search', 'jet-search' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'jet-search' ),
					'label_off'    => esc_html__( 'No', 'jet-search' ),
					'return_value' => 'true',
					'default'      => '',
					'separator'    => 'before',
				)
			);

			$this->end_controls_section();
		}

		/**
		 * `Search Form` Style Section
		 */
		$this->start_controls_section(
			'section_search_form_style',
			array(
				'label' => esc_html__( 'Search Form', 'jet-search' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_search_form' );

		$this->start_controls_tab(
			'tab_search_form_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-search' ),
			)
		);

		$this->add_control(
			'search_form_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['form'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'search_form_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['form'],
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_search_form_focus',
			array(
				'label' => esc_html__( 'Focus', 'jet-search' ),
			)
		);

		$this->add_control(
			'search_form_bg_color_focus',
			array(
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['form_focus'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_form_border_color_focus',
			array(
				'label' => esc_html__( 'Border Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['form_focus'] => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'search_form_border_border!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'search_form_box_shadow_focus',
				'selector' => '{{WRAPPER}} ' . $css_scheme['form_focus'],
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'search_form_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['form'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'search_form_border',
				'selector' => '{{WRAPPER}} ' . $css_scheme['form'],
			)
		);

		$this->add_control(
			'search_form_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['form'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * `Input Field` Style Section
		 */
		$this->start_controls_section(
			'section_search_input_style',
			array(
				'label' => esc_html__( 'Input Field', 'jet-search' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'search_input_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['field'],
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'search_placeholder_typography',
				'label'    => esc_html__( 'Placeholder Typography', 'jet-search' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['field'] . '::placeholder',
			)
		);

		$this->start_controls_tabs( 'tabs_search_input' );

		$this->start_controls_tab(
			'tab_search_input_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-search' ),
			)
		);

		$this->add_control(
			'search_input_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field'] => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_input_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_input_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field'] => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'search_input_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['field'],
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_search_input_focus',
			array(
				'label' => esc_html__( 'Focus', 'jet-search' ),
			)
		);

		$this->add_control(
			'search_input_color_focus',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['form_focus'] . ' ' . $css_scheme['field'] => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_input_bg_color_focus',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['form_focus'] . ' ' . $css_scheme['field'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_input_border_color_focus',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['form_focus'] . ' ' . $css_scheme['field'] => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'search_input_box_shadow_focus',
				'selector' => '{{WRAPPER}} ' . $css_scheme['form_focus'] . ' ' . $css_scheme['field'],
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'search_input_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['field'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'search_input_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['field'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'search_input_border_width',
			array(
				'label'      => esc_html__( 'Border Width', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['field'] => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'search_input_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['field'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * `Submit Button` Style Section
		 */
		$this->start_controls_section(
			'section_search_submit_style',
			array(
				'label'     => esc_html__( 'Submit Button', 'jet-search' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_search_submit' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'search_submit_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['submit_label'],
			)
		);

		$this->add_responsive_control(
			'search_submit_icon_font_size',
			array(
				'label'      => esc_html__( 'Icon Font Size', 'jet-search' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['submit_icon'] => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'selected_search_submit_icon!' => '',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_search_submit' );

		$this->start_controls_tab(
			'tab_search_submit_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-search' ),
			)
		);

		$this->add_control(
			'search_submit_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['submit'] => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_submit_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['submit'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'search_submit_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['submit'],
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_search_submit_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-search' ),
			)
		);

		$this->add_control(
			'search_submit_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['submit'] . ':hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_submit_border_color_hover',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['submit'] . ':hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'search_submit_border_border!' => '',
				),
			)
		);

		$this->add_control(
			'search_submit_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['submit'] . ':hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'search_submit_box_shadow_hover',
				'selector' => '{{WRAPPER}} ' . $css_scheme['submit'] . ':hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'search_submit_vertical_align',
			array(
				'label'     => esc_html__( 'Vertical Align', 'jet-search' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''           => esc_html__( 'Default', 'jet-search' ),
					'flex-start' => esc_html__( 'Start', 'jet-search' ),
					'center'     => esc_html__( 'Center', 'jet-search' ),
					'flex-end'   => esc_html__( 'End', 'jet-search' ),
					'stretch'    => esc_html__( 'Stretch', 'jet-search' ),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['submit'] => 'align-self: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'search_submit_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['submit'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'search_submit_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['submit'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'search_submit_border',
				'selector' => '{{WRAPPER}} ' . $css_scheme['submit'],
			)
		);

		$this->add_responsive_control(
			'search_submit_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['submit'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * `Categories List` Style Section
		 */
		$this->start_controls_section(
			'section_search_category_style',
			array(
				'label'     => esc_html__( 'Categories List', 'jet-search' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_search_category_list' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'search_category_width',
			array(
				'label'      => esc_html__( 'Width', 'jet-search' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 500,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories'] => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'search_category_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['categories_select'] . ', {{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-single',
			)
		);

		$this->add_control(
			'search_category_icon_font_size',
			array(
				'label'      => esc_html__( 'Arrow Font Size', 'jet-search' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories_select_icon'] . ' svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_search_category' );

		$this->start_controls_tab(
			'tab_search_category_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-search' ),
			)
		);

		$this->add_control(
			'search_category_color',
			array(
				'label' => esc_html__( 'Text Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories_select'] => 'color: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-single' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_category_icon_color',
			array(
				'label' => esc_html__( 'Arrow Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories_select_icon'] . ' svg > *' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_category_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories_select'] => 'background-color: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-single' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_category_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories_select'] => 'border-color: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-single' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'search_category_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['categories_select'] . ', {{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-single',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_search_category_focus',
			array(
				'label' => esc_html__( 'Focus', 'jet-search' ),
			)
		);

		$this->add_control(
			'search_category_color_focus',
			array(
				'label' => esc_html__( 'Text Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories_select'] . ':focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-with-drop .chosen-single' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_category_icon_color_focus',
			array(
				'label' => esc_html__( 'Arrow Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories_select'] . ':focus ~ ' . $css_scheme['categories_select_icon'] . ' svg > *' => 'fill: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-with-drop ~ ' . $css_scheme['categories_select_icon'] . ' svg > *' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_category_bg_color_focus',
			array(
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories_select'] . ':focus' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-with-drop .chosen-single' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_category_border_color_focus',
			array(
				'label' => esc_html__( 'Border Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories_select'] . ':focus' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-with-drop .chosen-single' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'search_category_box_shadow_focus',
				'selector' => '{{WRAPPER}} ' . $css_scheme['categories_select'] . ':focus , {{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-with-drop .chosen-single',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'search_category_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['categories_select'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-single' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'body:not(.rtl) {{WRAPPER}} ' . $css_scheme['categories_select_icon'] => 'right: {{RIGHT}}{{UNIT}};',
					'body.rtl {{WRAPPER}} ' . $css_scheme['categories_select_icon'] => 'left: {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'search_category_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['categories'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'search_category_border_width',
			array(
				'label'      => esc_html__( 'Border Width', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['categories_select'] => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-single' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'search_category_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['categories_select'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-single' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'search_category_dropdown_heading',
			array(
				'label'     => esc_html__( 'Dropdown Style', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'search_category_dropdown_max_height',
			array(
				'label'      => esc_html__( 'Max Height', 'jet-search' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 500,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-results' => 'max-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'search_category_dropdown_gap',
			array(
				'label'      => esc_html__( 'Gap', 'jet-search' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-drop' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'search_category_dropdown_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-drop' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'search_category_dropdown_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-drop',
			)
		);

		$this->update_control( 'search_category_dropdown_box_shadow_box_shadow_type',
			array(
				'default' => 'yes',
			)
		);

		$this->add_responsive_control(
			'search_category_dropdown_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-drop' => 'padding: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0;',
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-results' => 'padding: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'search_category_dropdown_border',
				'selector' => '{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-drop',
			)
		);

		$this->add_control(
			'search_category_dropdown_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-drop' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'search_category_dropdown_scrollbar_thumb_bg',
			array(
				'label'       => esc_html__( 'Scrollbar Thumb Color', 'jet-search' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-drop ::-webkit-scrollbar-thumb' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_category_dropdown_items_heading',
			array(
				'label'     => esc_html__( 'Dropdown Items Style', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'search_category_dropdown_items_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-results li',
			)
		);

		$this->start_controls_tabs( 'tabs_search_category_dropdown_items' );

		$this->start_controls_tab(
			'tab_search_category_dropdown_items_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-search' ),
			)
		);

		$this->add_control(
			'search_category_dropdown_items_color',
			array(
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-results li' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_category_dropdown_items_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-results li' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_search_category_dropdown_items_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-search' ),
			)
		);

		$this->add_control(
			'search_category_dropdown_items_color_hover',
			array(
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-results li.highlighted' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'search_category_dropdown_items_bg_color_hover',
			array(
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-results li.highlighted' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'search_category_dropdown_items_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-results li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'search_category_dropdown_items_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-results li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'search_category_dropdown_items_gap',
			array(
				'label' => esc_html__( 'Gap', 'jet-search' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['categories'] . ' .chosen-results li:not(:first-child)' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * `Inline Area` Style Section
		 */
		$this->start_controls_section(
			'section_inline_area_style',
			array(
				'label' => esc_html__( 'Inline Area', 'jet-search' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'inline_area_heading',
			array(
				'label' => esc_html__( 'Inline Area', 'jet-search' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'inline_area_gap',
			array(
				'label'      => esc_html__( 'Gap', 'jet-search' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inline_area'] => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'inline_area_item_heading',
			array(
				'label'     => esc_html__( 'Inline Area Item', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'inline_area_item_title_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['inline_area_item_title'],
			)
		);

		$this->add_responsive_control(
			'inline_area_item_column_gap',
			array(
				'label'      => esc_html__( 'Column Gap', 'jet-search' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inline_area'] => 'column-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'inline_area_item_rows_gap',
			array(
				'label'      => esc_html__( 'Rows Gap', 'jet-search' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inline_area'] => 'row-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_inline_area_item' );

		$this->start_controls_tab(
			'tab_inline_area_item_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-search' ),
			)
		);

		$this->add_control(
			'inline_area_item_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inline_area_item_title'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'inline_area_item_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inline_area_item_title'] => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'inline_area_item_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['inline_area_item_title'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'inline_area_item_bg_color!' => '',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_inline_area_item_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-search' ),
			)
		);

		$this->add_control(
			'inline_area_item_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inline_area_item_title'] . ':hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'inline_area_item_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inline_area_item_title'] . ':hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'inline_area_item_border_radius_hover',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['inline_area_item_title'] . ':hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'inline_area_item_bg_color!' => '',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'inline_area_item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['inline_area_item_title'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		/**
		 * `Focus Area` Style Section
		 */
		$this->start_controls_section(
			'section_focus_area_style',
			array(
				'label' => esc_html__( 'Focus Area', 'jet-search' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'focus_area_heading',
			array(
				'label' => esc_html__( 'Focus Area', 'jet-search' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'focus_area_gap',
			array(
				'label'      => esc_html__( 'Gap', 'jet-search' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['focus_area'] => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'focus_area_item_heading',
			array(
				'label'     => esc_html__( 'Focus Area Item', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'focus_area_item_title_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['focus_area_item_title'],
			)
		);

		$this->start_controls_tabs( 'tabs_focus_area_item' );

		$this->start_controls_tab(
			'tab_focus_area_item_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-search' ),
			)
		);

		$this->add_control(
			'focus_area_item_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['focus_area_item'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'focus_area_item_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['focus_area_item_title'] => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_focus_area_item_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-search' ),
			)
		);

		$this->add_control(
			'focus_area_item_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['focus_area_item'] . ':hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'focus_area_item_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['focus_area_item_title'] . ':hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'focus_area_item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['focus_area_item_title'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'focus_area_item_highlight',
			array(
				'label'     => esc_html__( 'Results Highlight', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'highlight_searched_text!' => '',
				),
			)
		);

		$this->add_control(
			'focus_area_item_highlight_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['focus_area_item_title'] . ' mark' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'highlight_searched_text!' => '',
				),
			)
		);

		$this->add_control(
			'focus_area_item_highlight_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['focus_area_item_title'] . ' mark' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'highlight_searched_text!' => '',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * `Spinner` Style Section
		 */
		$this->start_controls_section(
			'section_spinner_style',
			array(
				'label' => esc_html__( 'Spinner', 'jet-search' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_search_suggestions_list_on_focus_preloader!' => '',
				),
			)
		);

		$this->add_control(
			'spinner_color',
			array(
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['spinner'] => 'color: {{VALUE}};',
				),
				'condition' => array(
					'show_search_suggestions_list_on_focus_preloader!' => '',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		$this->__context = 'render';

		$this->__open_wrap();

		$render = new \Jet_Search_Suggestions_Render( $this->get_settings_for_display(), $this->get_id() );
		$render->render();

		$this->__close_wrap();
	}

}
