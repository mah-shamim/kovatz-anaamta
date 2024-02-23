<?php
/**
 * Class: Jet_Search_Ajax_Search_Widget
 * Name: Ajax Search
 * Slug: jet-ajax-search
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
class Jet_Search_Ajax_Search_Widget extends Jet_Search_Widget_Base {

	public $current_query = null;

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'jet-ajax-search';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Ajax Search', 'jet-search' );
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/article-category/jet-search/';
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-search';
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
			'jet-search/ajax-search/css-scheme',
			array(
				'form'                           => '.jet-ajax-search__form',
				'form_focus'                     => '.jet-ajax-search__form--focus',
				'fields_holder'                  => '.jet-ajax-search__fields-holder',
				'field_wrapper'                  => '.jet-ajax-search__field-wrapper',
				'field'                          => '.jet-ajax-search__field',
				'field_icon'                     => '.jet-ajax-search__field-icon',
				'categories'                     => '.jet-ajax-search__categories',
				'categories_select'              => '.jet-ajax-search__categories-select',
				'categories_select_icon'         => '.jet-ajax-search__categories-select-icon',
				'submit'                         => '.jet-ajax-search__submit',
				'submit_icon'                    => '.jet-ajax-search__submit-icon',
				'submit_label'                   => '.jet-ajax-search__submit-label',
				'results_area'                   => '.jet-ajax-search__results-area',
				'results_header'                 => '.jet-ajax-search__results-header',
				'results_list'                   => '.jet-ajax-search__results-list',
				'results_slide'                  => '.jet-ajax-search__results-slide',
				'results_footer'                 => '.jet-ajax-search__results-footer',
				'results_item'                   => '.jet-ajax-search__results-item',
				'results_item_link'              => '.jet-ajax-search__item-link',
				'results_item_thumb'             => '.jet-ajax-search__item-thumbnail',
				'results_item_thumb_img'         => '.jet-ajax-search__item-thumbnail-img',
				'results_item_thumb_placeholder' => '.jet-ajax-search__item-thumbnail-placeholder',
				'results_item_title'             => '.jet-ajax-search__item-title',
				'results_item_content'           => '.jet-ajax-search__item-content',
				'results_item_price'             => '.jet-ajax-search__item-price',
				'results_item_rating'            => '.jet-ajax-search__item-rating',
				'results_rating_star'            => '.jet-ajax-search__rating-star',
				'results_counter'                => '.jet-ajax-search__results-count',
				'full_results'                   => '.jet-ajax-search__full-results',
				'bullet_btn'                     => '.jet-ajax-search__bullet-button',
				'number_btn'                     => '.jet-ajax-search__number-button',
				'active_nav_btn'                 => '.jet-ajax-search__active-button',
				'arrow_btn'                      => '.jet-ajax-search__arrow-button',
				'message'                        => '.jet-ajax-search__message',
				'spinner'                        => '.jet-ajax-search__spinner',
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
			'selected_search_field_icon',
			array(
				'label'            => esc_html__( 'Input Icon', 'jet-search' ),
				'label_block'      => false,
				'type'             => Controls_Manager::ICONS,
				'skin'             => 'inline',
				'fa4compatibility' => 'search_field_icon',
			)
		);

		$this->add_control(
			'search_clear_btn_icon',
			array(
				'label'       => esc_html__( 'Clear Text Button Icon', 'jet-search' ),
				'description' => esc_html__( 'Firefox and IE Explorer browsers are not supported', 'jet-search' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'svg' ),
				'default'     => array(
					'url' => '',
				),
			)
		);

		$this->add_control(
			'search_clear_btn_icon_styles',
			array(
				'type'      => Controls_Manager::HIDDEN,
				'default'   => ' ',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field'] . '::-webkit-search-cancel-button' => '{{VALUE}}-webkit-appearance: none;
					background-size: contain !important;
					background: url({{search_clear_btn_icon.URL}}) no-repeat 50% 50%;
					opacity: 1;',
				),
				'condition' => array(
					'search_clear_btn_icon[url]!' => '',
				),
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
			'symbols_for_start_searching',
			array(
				'label'   => esc_html__( 'Minimal Quantity of Symbols for Search', 'jet-search' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 2,
				'max'     => 10,
				'min'     => 1,
			)
		);

		$this->add_control(
			'search_by_empty_value',
			array(
				'label'     => esc_html__( 'Allow Search by Empty String', 'jet-search' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
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
			'search_form_responsive_on_mobile',
			array(
				'label'     => esc_html__( 'Responsive Form on Mobile', 'jet-search' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'separator' => 'before',
			)
		);


		$this->end_controls_section();

		/**
		 * `Search Settings` Section
		 */
		$this->start_controls_section(
			'section_search_settings',
			array(
				'label' => esc_html__( 'Search Query', 'jet-search' ),
			)
		);

		$this->add_control(
			'current_query',
			array(
				'label'       => esc_html__( 'Search by the current query', 'jet-search' ),
				'description' => esc_html__( 'Use for Archive Templates', 'jet-search' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => '',
			)
		);

		$this->add_control(
			'search_source',
			array(
				'label'       => esc_html__( 'Source', 'jet-search' ),
				'description' => esc_html__( 'You can select particular search areas. If nothing is selected in the option, the search will be made over the entire site.', 'jet-search' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'default'     => array(),
				'options'     => \Jet_Search_Tools::get_post_types(),
				'condition'   => array(
					'current_query' => '',
				),
			)
		);

		$this->start_controls_tabs(
			'tabs_search_query',
			array(
				'condition' => array(
					'current_query' => '',
				),
			)
		);

		$this->start_controls_tab(
			'tab_search_query_include',
			array(
				'label' => esc_html__( 'Include', 'jet-search' ),
			)
		);

		$this->add_control(
			'include_terms_ids',
			array(
				'label'       => esc_html__( 'Terms', 'jet-search' ),
				'label_block' => true,
				'type'        => 'jet-search-query',
				'multiple'    => true,
				'action'      => 'jet_search_get_query_control_options',
				'query_type'  => 'terms',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_search_query_exclude',
			array(
				'label' => esc_html__( 'Exclude', 'jet-search' ),
			)
		);

		$this->add_control(
			'exclude_terms_ids',
			array(
				'label'       => esc_html__( 'Terms', 'jet-search' ),
				'label_block' => true,
				'type'        => 'jet-search-query',
				'multiple'    => true,
				'action'      => 'jet_search_get_query_control_options',
				'query_type'  => 'terms',
			)
		);

		$this->add_control(
			'exclude_posts_ids',
			array(
				'label'       => esc_html__( 'Posts', 'jet-search' ),
				'label_block' => true,
				'type'        => 'jet-search-query',
				'multiple'    => true,
				'action'      => 'jet_search_get_query_control_options',
				'query_type'  => 'posts',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'custom_fields_source',
			array(
				'label'       => esc_html__( 'Search in custom fields', 'jet-search' ),
				'label_block' => true,
				'description' => esc_html__( 'Set comma separated custom fields keys list (_sku, _price, etc.)', 'jet-search' ),
				'type'        => Controls_Manager::TEXT,
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'sentence',
			array(
				'label'     => esc_html__( 'Sentence Search', 'jet-search' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'search_in_taxonomy',
			array(
				'label'     => esc_html__( 'Search in taxonomy terms', 'jet-search' ),
				'description' => esc_html__( 'Include in the search results the posts containing the terms of the selected taxonomies with the search phrase in the term name', 'jet-search' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'search_in_taxonomy_source',
			array(
				'label'       => esc_html__( 'Taxonomies', 'jet-search' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'default'     => array(),
				'options'     => \Jet_Search_Tools::get_taxonomies( true ),
				'condition'   => array(
					'search_in_taxonomy!' => '',
				),
			)
		);

		$this->add_control(
			'results_order_by',
			array(
				'label'       => esc_html__( 'Results Order By', 'jet-search' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'relevance',
				'options' => array(
					'relevance'     => esc_html__( 'Relevance', 'jet-search' ),
					'ID'            => esc_html__( 'ID', 'jet-search' ),
					'author'        => esc_html__( 'Author', 'jet-search' ),
					'title'         => esc_html__( 'Title', 'jet-search' ),
					'date'          => esc_html__( 'Date', 'jet-search' ),
					'modified'      => esc_html__( 'Last modified', 'jet-search' ),
					'rand'          => esc_html__( 'Rand', 'jet-search' ),
					'comment_count' => esc_html__( 'Number of Comments (descending)', 'jet-search' ),
					'menu_order'    => esc_html__( 'Menu order', 'jet-search' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'results_order',
			array(
				'label'   => esc_html__( 'Results Order', 'jet-search' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'asc',
				'options' => array(
					'asc'  => esc_html__( 'ASC', 'jet-search' ),
					'desc' => esc_html__( 'DESC', 'jet-search' ),
				),
			)
		);

		do_action( 'jet-search/ajax-search/add-custom-controls', $this );

		$this->add_responsive_control(
			'limit_query',
			array(
				'label'       => esc_html__( 'Posts Per Page', 'jet-search' ),
				'description' => esc_html__( 'A number of results displayed on one search page.', 'jet-search' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 5,
				'max'         => 50,
				'min'         => 0,
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'limit_query_in_result_area',
			array(
				'label'   => esc_html__( 'Posts Number', 'jet-search' ),
				'description' => esc_html__( 'A number of results displayed in one search query.', 'jet-search' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 25,
				'max'     => 150,
				'min'     => 0,
			)
		);

		$this->end_controls_section();

		/**
		 * `Results Area` Section
		 */
		$this->start_controls_section(
			'section_results_area_settings',
			array(
				'label' => esc_html__( 'Results Area', 'jet-search' ),
			)
		);

		$this->add_control(
			'results_area_width_by',
			array(
				'label'   => esc_html__( 'Results Area Width', 'jet-search' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'form',
				'options' => array(
					'form'          => esc_html__( 'by Search Form', 'jet-search' ),
					'fields_holder' => esc_html__( 'by Input Box and Categories List', 'jet-search' ),
					'custom'        => esc_html__( 'Custom', 'jet-search' ),
				),
			)
		);

		$this->add_control(
			'highlight_searched_text',
			array(
				'label'   => esc_html__( 'Highlight Searched Text', 'jet-search' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'results_area_custom_width',
			array(
				'label'      => esc_html__( 'Custom Width', 'jet-search' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 2000,
					),
				),
				'default' => array(
					'unit' => 'px',
					'size' => '',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['results_area'] => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'results_area_width_by' => 'custom',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'results_area_custom_position',
			array(
				'label'   => esc_html__( 'Custom Position', 'jet-search' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'jet-search' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-search' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-search' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'selectors_dictionary' => array(
					'left'   => 'left: 0; right: auto;',
					'center' => 'left: 50%; right: auto; -webkit-transform: translateX(-50%); transform: translateX(-50%);',
					'right'  => 'left: auto; right: 0;',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_area'] => '{{VALUE}}',
				),
				'condition' => array(
					'results_area_width_by' => 'custom',
				),
			)
		);

		$this->add_control(
			'thumbnail_visible',
			array(
				'label'   => esc_html__( 'Show Post Thumbnail', 'jet-search' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'thumbnail_size',
			array(
				'label'     => esc_html__( 'Thumbnail Size', 'jet-search' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => \Jet_Search_Tools::get_image_sizes(),
				'default'   => 'thumbnail',
				'condition' => array(
					'thumbnail_visible' => 'yes',
				),
			)
		);

		$this->add_control(
			'thumbnail_placeholder',
			array(
				'label'     => esc_html__( 'Thumbnail Placeholder', 'jet-search' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'thumbnail_visible' => 'yes',
				),
			)
		);

		$this->add_control(
			'post_content_source',
			array(
				'label'   => esc_html__( 'Post Content Source', 'jet-search' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'content',
				'options' => array(
					'content'      => esc_html__( 'Post Content', 'jet-search' ),
					'excerpt'      => esc_html__( 'Post Excerpt', 'jet-search' ),
					'custom-field' => esc_html__( 'Custom Field', 'jet-search' ),
				),
			)
		);

		$this->add_control(
			'post_content_custom_field_key',
			array(
				'label'     => esc_html__( 'Custom Field Key', 'jet-search' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'post_content_source' => 'custom-field',
				),
			)
		);

		$this->add_control(
			'post_content_length',
			array(
				'label'       => esc_html__( 'Post Content Length', 'jet-search' ),
				'description' => esc_html__( 'Set 0 to hide content.', 'jet-search' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 30,
				'max'         => 150,
				'min'         => 0,
			)
		);

		$this->add_control(
			'show_product_price',
			array(
				'label'   => esc_html__( 'Show Product Price', 'jet-search' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'show_product_rating',
			array(
				'label'   => esc_html__( 'Show Product Rating', 'jet-search' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'show_results_counter',
			array(
				'label'     => esc_html__( 'Show Results Counter', 'jet-search' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'results_counter_text',
			array(
				'label'     => esc_html__( 'Results Counter Text', 'jet-search' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Results', 'jet-search' ),
				'condition' => array(
					'show_results_counter' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_full_results',
			array(
				'label'   => esc_html__( 'Show All Results Button', 'jet-search' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'full_results_btn_text',
			array(
				'label'     => esc_html__( 'All Results Button Text', 'jet-search' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'See all results', 'jet-search' ),
				'condition' => array(
					'show_full_results' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_result_new_tab',
			array(
				'label'   => esc_html__( 'Open Results In New Tab', 'jet-search' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'results_navigation_heading',
			array(
				'label'     => esc_html__( 'Results Navigation', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'bullet_pagination',
			array(
				'label'   => esc_html__( 'Bullet Pagination', 'jet-search' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''          => esc_html__( 'Hide', 'jet-search' ),
					'in_header' => esc_html__( 'Show in header', 'jet-search' ),
					'in_footer' => esc_html__( 'Show in footer', 'jet-search' ),
					'both'      => esc_html__( 'Show in header and footer', 'jet-search' ),
				),
			)
		);

		$this->add_control(
			'number_pagination',
			array(
				'label'   => esc_html__( 'Number Pagination', 'jet-search' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''          => esc_html__( 'Hide', 'jet-search' ),
					'in_header' => esc_html__( 'Show in header', 'jet-search' ),
					'in_footer' => esc_html__( 'Show in footer', 'jet-search' ),
					'both'      => esc_html__( 'Show in header and footer', 'jet-search' ),
				),
			)
		);

		$this->add_control(
			'navigation_arrows',
			array(
				'label'   => esc_html__( 'Navigation Arrows', 'jet-search' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'in_header',
				'options' => array(
					''          => esc_html__( 'Hide', 'jet-search' ),
					'in_header' => esc_html__( 'Show in header', 'jet-search' ),
					'in_footer' => esc_html__( 'Show in footer', 'jet-search' ),
					'both'      => esc_html__( 'Show in header and footer', 'jet-search' ),
				),
			)
		);

		$this->add_control(
			'navigation_arrows_type',
			array(
				'label'       => esc_html__( 'Navigation Arrows Type', 'jet-search' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'angle',
				'options'     => \Jet_Search_Tools::get_available_prev_arrows_list(),
				'condition'   => array(
					'navigation_arrows!' => '',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * `Custom Fields` Section
		 */
		$this->start_controls_section(
			'section_results_custom_fields',
			array(
				'label' => esc_html__( 'Custom Fields', 'jet-search' ),
			)
		);

		$this->add_meta_controls( 'title_related', esc_html__( 'Before/After Title', 'jet-search' ) );

		$this->add_meta_controls( 'content_related', esc_html__( 'Before/After Content', 'jet-search' ) );

		$this->end_controls_section();

		/**
		 * `Notifications` Section
		 */
		$this->start_controls_section(
			'section_notifications_settings',
			array(
				'label' => esc_html__( 'Notifications', 'jet-search' ),
			)
		);

		$this->add_control(
			'negative_search',
			array(
				'label'       => esc_html__( 'Negative search results', 'jet-search' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Sorry, but nothing matched your search terms.', 'jet-search' ),
			)
		);

		$this->add_control(
			'server_error',
			array(
				'label'       => esc_html__( 'Technical error', 'jet-search' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Sorry, but we cannot handle your search query now. Please, try again later!', 'jet-search' ),
			)
		);

		$this->end_controls_section();

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
		 * `Input Field and Categories List Wrapper` Style Section
		 */
		$this->start_controls_section(
			'section_search_fields_holder_style',
			array(
				'label'     => esc_html__( 'Input Field and Categories List Wrapper', 'jet-search' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_search_category_list' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_search_fields_holder' );

		$this->start_controls_tab(
			'tab_search_fields_holder_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-search' ),
			)
		);

		$this->add_control(
			'search_fields_holder_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['fields_holder'] => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'show_search_category_list' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'search_fields_holder_box_shadow',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['fields_holder'],
				'condition' => array(
					'show_search_category_list' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_search_fields_holder_focus',
			array(
				'label' => esc_html__( 'Focus', 'jet-search' ),
			)
		);

		$this->add_control(
			'search_fields_holder_bg_color_focus',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['form_focus'] . ' ' . $css_scheme['fields_holder'] => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'show_search_category_list' => 'yes',
				),
			)
		);

		$this->add_control(
			'search_fields_holder_border_color_focus',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['form_focus'] . ' ' . $css_scheme['fields_holder'] => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'show_search_category_list' => 'yes',
					'search_fields_holder_border_border!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'search_fields_holder_box_shadow_focus',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['form_focus'] . ' ' . $css_scheme['fields_holder'],
				'condition' => array(
					'show_search_category_list' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'search_fields_holder_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['fields_holder'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
				'condition' => array(
					'show_search_category_list' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'search_fields_holder_border',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['fields_holder'],
				'condition' => array(
					'show_search_category_list' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'search_fields_holder_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['fields_holder'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'show_search_category_list' => 'yes',
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

		$this->add_control(
			'search_input_icon_style',
			array(
				'label'       => esc_html__( 'Icon', 'jet-search' ),
				'type'        => Controls_Manager::POPOVER_TOGGLE,
				'render_type' => 'ui',
				'condition'   => array(
					'selected_search_field_icon!' => '',
				),
			)
		);

		$this->start_popover();

		$this->add_responsive_control(
			'search_input_icon_font_size',
			array(
				'label'      => esc_html__( 'Font Size', 'jet-search' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field_icon'] => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'search_input_icon_style' => 'yes',
				),
			)
		);

		$this->add_control(
			'search_input_icon_gap',
			array(
				'label' => esc_html__( 'Gap', 'jet-search' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'selectors' => array(
					'body:not(.rtl) {{WRAPPER}} ' . $css_scheme['field_icon'] => 'left: {{SIZE}}{{UNIT}};',
					'body.rtl {{WRAPPER}} ' . $css_scheme['field_icon'] => 'right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'search_input_icon_style' => 'yes',
				),
			)
		);

		$this->end_popover();

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
			'search_input_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field_icon'] => 'color: {{VALUE}};',
				),
				'condition' => array(
					'selected_search_field_icon!' => '',
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
			'search_input_icon_color_focus',
			array(
				'label'     => esc_html__( 'Icon Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['form_focus'] . ' ' . $css_scheme['field_icon'] => 'color: {{VALUE}};',
				),
				'condition' => array(
					'selected_search_field_icon!' => '',
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

		$this->add_control(
			'search_clear_btn_icon_style',
			array(
				'label'     => esc_html__( 'Clear Text Button Icon', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'search_clear_btn_icon[url]!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'search_clear_btn_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'jet-search' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default' => array(
					'unit' => 'px',
					'size' => 16,
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field'] . '::-webkit-search-cancel-button' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};'
				),
				'condition' => array(
					'search_clear_btn_icon[url]!' => '',
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
		 * `Results Area` Style Section
		 */
		$this->start_controls_section(
			'section_results_area_style',
			array(
				'label' => esc_html__( 'Results Area', 'jet-search' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'results_area_heading',
			array(
				'label' => esc_html__( 'Results Area', 'jet-search' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'results_area_gap',
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
					'{{WRAPPER}} ' . $css_scheme['results_area'] => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'results_area_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_area'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'results_area_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['results_area'],
			)
		);

		$this->update_control( 'results_area_box_shadow_box_shadow_type',
			array(
				'default' => 'yes',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'results_area_border',
				'selector' => '{{WRAPPER}} ' . $css_scheme['results_area'],
			)
		);

		$this->add_control(
			'results_area_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['results_area'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'results_header_heading',
			array(
				'label'     => esc_html__( 'Results Header', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'results_header_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' .  $css_scheme['results_header'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'results_header_border',
				'selector' => '{{WRAPPER}} ' . $css_scheme['results_header'],
			)
		);

		$this->add_control(
			'results_list_heading',
			array(
				'label'     => esc_html__( 'Results List', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'enable_scroll',
			array(
				'label'     => esc_html__( 'Enable Scrolling ', 'jet-search' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_slide'] => 'overflow-y: auto;',
				),
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'results_list_height',
			array(
				'label'       => esc_html__( 'Max Height (px)', 'jet-search' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 500,
				'max'         => 500,
				'min'         => 0,
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_slide'] => 'max-height: {{VALUE}}px;',
				),
				'condition' => array(
					'enable_scroll' => 'yes',
				),
			)
		);

		$this->add_control(
			'results_list_scrollbar_bg',
			array(
				'label'     => esc_html__( 'Scrollbar Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_slide'] . '::-webkit-scrollbar' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'enable_scroll' => 'yes',
				),
			)
		);

		$this->add_control(
			'results_list_scrollbar_thumb_bg',
			array(
				'label'     => esc_html__( 'Scrollbar Thumb Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_slide'] . '::-webkit-scrollbar-thumb' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'enable_scroll' => 'yes',
				),
			)
		);

		$this->add_control(
			'results_footer_heading',
			array(
				'label'     => esc_html__( 'Results Footer', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'results_footer_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' .  $css_scheme['results_footer'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'results_footer_border',
				'selector' => '{{WRAPPER}} ' . $css_scheme['results_footer'],
			)
		);

		$this->add_control(
			'results_highlight',
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
			'results_highlight_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item'] . ' mark' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'highlight_searched_text!' => '',
				),
			)
		);

		$this->add_control(
			'results_highlight_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item'] . ' mark' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'highlight_searched_text!' => '',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * `Results Items` Style Section
		 */
		$this->start_controls_section(
			'section_results_items_style',
			array(
				'label' => esc_html__( 'Results Items', 'jet-search' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_results_item' );

		$this->start_controls_tab(
			'tab_results_item_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-search' ),
			)
		);

		$this->add_control(
			'results_item_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_link'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'results_item_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_title'] => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'results_item_content_color',
			array(
				'label'     => esc_html__( 'Content Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_content'] => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'results_item_rating_color',
			array(
				'label'     => esc_html__( 'Product Rating Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_rating_star'] . ':before' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'show_product_rating' => 'yes',
				),
			)
		);

		$this->add_control(
			'results_item_rating_unmarked_color',
			array(
				'label'     => esc_html__( 'Product Rating Unmarked Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_rating_star'] => 'color: {{VALUE}};',
				),
				'condition' => array(
					'show_product_rating' => 'yes',
				),
			)
		);

		$this->add_control(
			'results_item_price_color',
			array(
				'label'     => esc_html__( 'Product Price Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_price'] . ' .price' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'show_product_price' => 'yes',
				),
			)
		);

		$this->add_control(
			'results_item_old_price_color',
			array(
				'label'     => esc_html__( 'Product Old Price Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_price'] . ' .price del' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'show_product_price' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_results_item_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-search' ),
			)
		);

		$this->add_control(
			'results_item_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_link'] . ':hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'results_item_title_color_hover',
			array(
				'label'     => esc_html__( 'Title Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_link'] . ':hover ' . $css_scheme['results_item_title'] => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'results_item_title_decoration_hover',
			array(
				'label'   => esc_html__( 'Title Text Decoration', 'jet-search' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''             => esc_html__( 'Default', 'jet-search' ),
					'underline'    => esc_html_x( 'Underline', 'Typography Control', 'jet-search' ),
					'overline'     => esc_html_x( 'Overline', 'Typography Control', 'jet-search' ),
					'line-through' => esc_html_x( 'Line Through', 'Typography Control', 'jet-search' ),
					'none'         => esc_html_x( 'None', 'Typography Control', 'jet-search' ),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_link'] . ':hover ' . $css_scheme['results_item_title'] => 'text-decoration: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'results_item_content_color_hover',
			array(
				'label'     => esc_html__( 'Content Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_link'] . ':hover ' . $css_scheme['results_item_content'] => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'results_item_price_color_hover',
			array(
				'label'     => esc_html__( 'Product Price Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_link'] . ':hover ' . $css_scheme['results_item_price'] . ' .price' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'show_product_price' => 'yes',
				),
			)
		);

		$this->add_control(
			'results_item_old_price_color_hover',
			array(
				'label'     => esc_html__( 'Product Old Price Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_link'] . ':hover ' . $css_scheme['results_item_price'] . ' .price del' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'show_product_price' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'results_item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_link'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'results_item_align',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-search' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'jet-search' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-search' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-search' ),
						'icon'  => 'fa fa-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'jet-search' ),
						'icon'  => 'fa fa-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_link'] => 'text-align: {{VALUE}};',
				),
				'classes' => 'jet-search-text-align-control',
			)
		);

		$this->add_control(
			'results_item_divider',
			array(
				'label'       => esc_html__( 'Divider', 'jet-search' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => '',
				'render_type' => 'ui',
			)
		);

		$this->add_control(
			'results_item_divider_style',
			array(
				'label'   => esc_html__( 'Style', 'jet-search' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => array(
					'solid'  => esc_html__( 'Solid', 'jet-search' ),
					'double' => esc_html__( 'Double', 'jet-search' ),
					'dotted' => esc_html__( 'Dotted', 'jet-search' ),
					'dashed' => esc_html__( 'Dashed', 'jet-search' ),
				),
				'condition' => array(
					'results_item_divider' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item'] . ':not(:first-child)' => 'border-top-style: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'results_item_divider_weight',
			array(
				'label'   => esc_html__( 'Weight', 'jet-search' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 1,
				),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'condition' => array(
					'results_item_divider' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item'] . ':not(:first-child)' => 'border-top-width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'results_item_divider_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'results_item_divider' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item'] . ':not(:first-child)' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'results_item_thumb_heading',
			array(
				'label'     => esc_html__( 'Thumbnail', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'results_item_thumb_width',
			array(
				'label'      => esc_html__( 'Width', 'jet-search' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 600,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_thumb'] => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'results_item_thumb_gap',
			array(
				'label' => esc_html__( 'Gap', 'jet-search' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'body:not(.rtl) {{WRAPPER}} ' . $css_scheme['results_item_thumb'] => 'margin-right: {{SIZE}}{{UNIT}};',
					'body.rtl {{WRAPPER}} ' . $css_scheme['results_item_thumb'] => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'results_item_thumb_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_thumb_img'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'results_item_title_heading',
			array(
				'label'     => esc_html__( 'Title', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'results_item_title_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['results_item_title'],
			)
		);

		$this->add_responsive_control(
			'results_item_title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_title'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'results_item_content_heading',
			array(
				'label'     => esc_html__( 'Content', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'results_item_content_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['results_item_content'],
			)
		);

		$this->add_responsive_control(
			'results_item_content_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_content'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'results_item_rating_heading',
			array(
				'label'     => esc_html__( 'Product Rating', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'show_product_rating' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'results_item_rating_font_size',
			array(
				'label' => esc_html__( 'Font Size', 'jet-search' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_rating_star'] => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'show_product_rating' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'results_item_rating_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_rating'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'show_product_rating' => 'yes',
				),
			)
		);

		$this->add_control(
			'results_item_price_heading',
			array(
				'label'     => esc_html__( 'Product Price', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'show_product_price' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'results_item_price_typography',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['results_item_price'] . ' .price',
				'condition' => array(
					'show_product_price' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'     => esc_html__( 'Old Price Typography', 'jet-search' ),
				'name'      => 'results_item_previous_price_typography',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['results_item_price'] . ' .price del',
				'condition' => array(
					'show_product_price' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'results_item_price_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['results_item_price'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'show_product_price' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_custom_fields_styles',
			array(
				'label'      => esc_html__( 'Custom Fields', 'jet-search' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'show_title_related_meta',
							'operator' => '===',
							'value'    => 'yes',
						),
						array(
							'name'     => 'show_content_related_meta',
							'operator' => '===',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_meta_style_controls(
			'title_related',
			esc_html__( 'Before/After Title', 'jet-search' ),
			'jet-search-title-fields'
		);

		$this->add_meta_style_controls(
			'content_related',
			esc_html__( 'Before/After Content', 'jet-search' ),
			'jet-search-content-fields'
		);

		$this->end_controls_section();

		/**
		 * `Results Counter` Style Section
		 */
		$this->start_controls_section(
			'section_results_counter_style',
			array(
				'label'     => esc_html__( 'Results Counter', 'jet-search' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_results_counter' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'results_counter_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['results_counter'],
			)
		);

		$this->start_controls_tabs( 'tabs_results_counter' );

		$this->start_controls_tab(
			'tab_results_counter_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-search' ),
			)
		);

		$this->add_control(
			'results_counter_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_counter'] => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'results_counter_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_counter'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'results_counter_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['results_counter'],
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_results_counter_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-search' ),
			)
		);

		$this->add_control(
			'results_counter_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_counter'] . ':hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'results_counter_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_counter'] . ':hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'results_counter_border_color_hover',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_counter'] . ':hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'results_counter_border_border!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'results_counter_box_shadow_hover',
				'selector' => '{{WRAPPER}} ' . $css_scheme['results_counter'] . ':hover',
			)
		);

		$this->add_control(
			'results_counter_decoration_hover',
			array(
				'label'   => esc_html__( 'Text Decoration', 'jet-search' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''             => esc_html__( 'Default', 'jet-search' ),
					'underline'    => esc_html_x( 'Underline', 'Typography Control', 'jet-search' ),
					'overline'     => esc_html_x( 'Overline', 'Typography Control', 'jet-search' ),
					'line-through' => esc_html_x( 'Line Through', 'Typography Control', 'jet-search' ),
					'none'         => esc_html_x( 'None', 'Typography Control', 'jet-search' ),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['results_counter'] . ':hover ' => 'text-decoration: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'results_counter_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['results_counter'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'results_counter_border',
				'selector' => '{{WRAPPER}} ' . $css_scheme['results_counter'],
			)
		);

		$this->add_control(
			'results_counter_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['results_counter'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * `All Results Button` Style Section
		 */
		$this->start_controls_section(
			'section_full_results_style',
			array(
				'label'     => esc_html__( 'All Results Button', 'jet-search' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_full_results' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'full_results_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['full_results'],
			)
		);

		$this->start_controls_tabs( 'tabs_full_results' );

		$this->start_controls_tab(
			'tab_full_results_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-search' ),
			)
		);

		$this->add_control(
			'full_results_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['full_results'] => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'full_results_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['full_results'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'full_results_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['full_results'],
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_full_results_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-search' ),
			)
		);

		$this->add_control(
			'full_results_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['full_results'] . ':hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'full_results_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['full_results'] . ':hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'full_results_border_color_hover',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['full_results'] . ':hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'full_results_border_border!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'full_results_box_shadow_hover',
				'selector' => '{{WRAPPER}} ' . $css_scheme['full_results'] . ':hover',
			)
		);

		$this->add_control(
			'full_results_decoration_hover',
			array(
				'label'   => esc_html__( 'Text Decoration', 'jet-search' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''             => esc_html__( 'Default', 'jet-search' ),
					'underline'    => esc_html_x( 'Underline', 'Typography Control', 'jet-search' ),
					'overline'     => esc_html_x( 'Overline', 'Typography Control', 'jet-search' ),
					'line-through' => esc_html_x( 'Line Through', 'Typography Control', 'jet-search' ),
					'none'         => esc_html_x( 'None', 'Typography Control', 'jet-search' ),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['full_results'] . ':hover ' => 'text-decoration: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'full_results_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['full_results'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'full_results_border',
				'selector' => '{{WRAPPER}} ' . $css_scheme['full_results'],
			)
		);

		$this->add_control(
			'full_results_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['full_results'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * `Bullet Pagination` Style Section
		 */
		$this->start_controls_section(
			'section_bullet_pagination_style',
			array(
				'label'     => esc_html__( 'Bullet Pagination', 'jet-search' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'bullet_pagination!' => '',
				),
			)
		);

		$this->add_control(
			'bullet_size',
			array(
				'label' => esc_html__( 'Size', 'jet-search' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['bullet_btn'] => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_bullet_pagination' );

		$this->start_controls_tab(
			'tab_bullet_pagination_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-search' ),
			)
		);

		$this->add_control(
			'bullet_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['bullet_btn'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bullet_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['bullet_btn'] => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_bullet_pagination_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-search' ),
			)
		);

		$this->add_control(
			'bullet_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['bullet_btn'] . ':hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bullet_border_color_hover',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['bullet_btn'] . ':hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_bullet_pagination_active',
			array(
				'label' => esc_html__( 'Active', 'jet-search' ),
			)
		);

		$this->add_control(
			'bullet_bg_color_active',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['bullet_btn'] . $css_scheme['active_nav_btn'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bullet_border_color_active',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['bullet_btn'] . $css_scheme['active_nav_btn'] => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'bullet_border_width',
			array(
				'label' => esc_html__( 'Border Width', 'jet-search' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['bullet_btn'] => 'border-width: {{SIZE}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'bullet_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'jet-search' ),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['bullet_btn'] => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * `Number Pagination` Style Section
		 */
		$this->start_controls_section(
			'section_number_pagination_style',
			array(
				'label'     => esc_html__( 'Number Pagination', 'jet-search' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'number_pagination!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'number_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['number_btn'],
			)
		);

		$this->start_controls_tabs( 'tabs_number_pagination' );

		$this->start_controls_tab(
			'tab_number_pagination_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-search' ),
			)
		);

		$this->add_control(
			'number_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['number_btn'] => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'number_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['number_btn'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'number_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['number_btn'] => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_number_pagination_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-search' ),
			)
		);

		$this->add_control(
			'number_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['number_btn'] . ':hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'number_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['number_btn'] . ':hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'number_border_color_hover',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['number_btn'] . ':hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_number_pagination_active',
			array(
				'label' => esc_html__( 'Active', 'jet-search' ),
			)
		);

		$this->add_control(
			'number_color_active',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['number_btn'] . $css_scheme['active_nav_btn'] => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'number_bg_color_active',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['number_btn'] . $css_scheme['active_nav_btn'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'number_border_color_active',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['number_btn'] . $css_scheme['active_nav_btn'] => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'number_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['number_btn'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'number_border_width',
			array(
				'label' => esc_html__( 'Border Width', 'jet-search' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['number_btn'] => 'border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'number_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'jet-search' ),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['number_btn'] => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * `Navigation Arrows` Style Section
		 */
		$this->start_controls_section(
			'section_navigation_arrows_style',
			array(
				'label'     => esc_html__( 'Navigation Arrows', 'jet-search' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'navigation_arrows!' => '',
				),
			)
		);

		$this->add_control(
			'arrow_font_size',
			array(
				'label'      => esc_html__( 'Font Size', 'jet-search' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['arrow_btn'] . ' svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_navigation_arrows' );

		$this->start_controls_tab(
			'tab_navigation_arrows_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-search' ),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['arrow_btn'] . ' svg > *' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['arrow_btn'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['arrow_btn'] => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_navigation_arrows_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-search' ),
			)
		);

		$this->add_control(
			'arrow_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['arrow_btn'] . ':hover svg > *' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['arrow_btn'] . ':hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_border_color_hover',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['arrow_btn'] . ':hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'arrow_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['arrow_btn'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'arrow_border_width',
			array(
				'label' => esc_html__( 'Border Width', 'jet-search' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['arrow_btn'] => 'border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'arrow_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'jet-search' ),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['arrow_btn'] => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * `Notifications` Style Section
		 */
		$this->start_controls_section(
			'section_notifications_style',
			array(
				'label' => esc_html__( 'Notifications', 'jet-search' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'notifications_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['message'],
			)
		);

		$this->add_control(
			'notifications_color',
			array(
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['message'] => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'notifications_align',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-search' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'jet-search' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-search' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-search' ),
						'icon'  => 'fa fa-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'jet-search' ),
						'icon'  => 'fa fa-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['message'] => 'text-align: {{VALUE}};',
				),
				'classes' => 'jet-search-text-align-control',
			)
		);

		$this->add_control(
			'notifications_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['message'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Add meta controls for selected position
	 *
	 * @since 2.0.0
	 * @param string $position_slug
	 * @param string $position_name
	 *
	 * @return void
	 */
	public function add_meta_controls( $position_slug, $position_name ) {

		$this->add_control(
			'show_' . $position_slug . '_meta',
			array(
				'label'     => sprintf( esc_html__( 'Show Meta %s', 'jet-search' ), $position_name ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'meta_' . $position_slug . '_position',
			array(
				'label'   => esc_html__( 'Meta Fields Position', 'jet-search' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'before',
				'options' => array(
					'before' => esc_html__( 'Before', 'jet-search' ),
					'after'  => esc_html__( 'After', 'jet-search' ),
				),
				'condition'   => array(
					'show_' . $position_slug . '_meta' => 'yes',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'meta_key',
			array(
				'label'       => esc_html__( 'Key', 'jet-search' ),
				'description' => esc_html__( 'Meta key from post-meta table in database', 'jet-search' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
			)
		);

		$repeater->add_control(
			'meta_label',
			array(
				'label'   => esc_html__( 'Label', 'jet-search' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$repeater->add_control(
			'meta_format',
			array(
				'label'       => esc_html__( 'Value Format', 'jet-search' ),
				'description' => esc_html__( 'Value format string, accepts HTML markup. %s - is meta value', 'jet-search' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '%s',
			)
		);

		$repeater->add_control(
			'meta_callback',
			array(
				'label'   => esc_html__( 'Prepare meta value with callback', 'jet-search' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => \Jet_Search_Tools::allowed_meta_callbacks(),
			)
		);

		$repeater->add_control(
			'date_format',
			array(
				'label'       => esc_html__( 'Format', 'jet-search' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'F j, Y',
				'description' => sprintf( '<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">%s</a>', esc_html__( 'Documentation on date and time formatting', 'jet-search' ) ),
				'condition'   => array(
					'meta_callback' => array( 'date', 'date_i18n' ),
				),
			)
		);

		$this->add_control(
			$position_slug . '_meta',
			array(
				'label'       => esc_html__( 'Meta Fields List', 'jet-search' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'meta_label' => esc_html__( 'Label', 'jet-search' ),
					)
				),
				'title_field' => '{{{ meta_key }}}',
				'condition'   => array(
					'show_' . $position_slug . '_meta' => 'yes',
				),
			)
		);

	}

	/**
	 * Add meta style controls for selected position
	 *
	 * @since 2.0.0
	 * @param string $position_slug
	 * @param string $position_name
	 * @param string $base
	 *
	 * @return void
	 */
	public function add_meta_style_controls( $position_slug, $position_name, $base ) {

		$this->add_control(
			$position_slug . '_meta_styles',
			array(
				'label'     => sprintf( esc_html__( 'Meta Styles %s', 'jet-search' ), $position_name ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			$position_slug . '_meta_label_heading',
			array(
				'label' => esc_html__( 'Meta Label', 'jet-search' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			$position_slug . '_meta_label_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .' . $base . '__item-label' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			$position_slug . '_meta_label_hover_color',
			array(
				'label'     => esc_html__( 'Color on Hover', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-ajax-search__item-link:hover .' . $base . '__item-label' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $position_slug . '_meta_label_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .' . $base . '__item-label',
			)
		);

		$this->add_control(
			$position_slug . '_meta_value_heading',
			array(
				'label'     => esc_html__( 'Meta Value', 'jet-search' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			$position_slug . '_meta_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .' . $base . '__item-value' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			$position_slug . '_meta_hover_color',
			array(
				'label'     => esc_html__( 'Color on Hover', 'jet-search' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-ajax-search__item-link:hover .' . $base . '__item-value' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $position_slug . '_meta_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .' . $base . '__item-value',
			)
		);

		$this->add_control(
			$position_slug . '_meta_label_display',
			array(
				'label'   => esc_html__( 'Display Meta Label and Value', 'jet-search' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''       => esc_html__( 'As Blocks', 'jet-search' ),
					'inline' => esc_html__( 'Inline', 'jet-search' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .' . $base . '__item-label' => 'display: {{VALUE}}',
					'{{WRAPPER}} .' . $base . '__item-value' => 'display: {{VALUE}}',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			$position_slug . '_meta_label_gap',
			array(
				'label'      => esc_html__( 'Horizontal Gap Between Label and Value', 'jet-search' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 5,
				),
				'selectors'  => array(
					'body:not(.rtl) {{WRAPPER}} .' . $base . '__item-label' => 'margin-right: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}} .' . $base . '__item-label'       => 'margin-left: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					$position_slug . '_meta_label_display' => 'inline',
				),
			)
		);

		$this->add_responsive_control(
			$position_slug . '_meta_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-search' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .' . $base => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			$position_slug . '_meta_align',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-search' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'jet-search' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-search' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-search' ),
						'icon'  => 'fa fa-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'jet-search' ),
						'icon'  => 'fa fa-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .' . $base => 'text-align: {{VALUE}};',
				),
				'classes' => 'jet-search-text-align-control',
			)
		);

	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		$this->__context = 'render';

		$this->__open_wrap();

		$render = new \Jet_Search_Render( $this->get_settings_for_display(), $this->get_id() );
		$render->render();

		$this->__close_wrap();
	}

}
