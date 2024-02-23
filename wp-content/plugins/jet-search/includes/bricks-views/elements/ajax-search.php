<?php

namespace Jet_Search\Bricks_Views\Elements;

use Bricks\Element;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Search_Bricks_Ajax_Search extends Base {

	// Element properties
	public $category     = 'jetsearch'; // Use predefined element category 'general'
	public $name         = 'jet-search-ajax-search'; // Make sure to prefix your elements
	public $icon         = 'ti-search'; // Themify icon font class
	public $css_selector = '.jet-ajax-search'; // Default CSS selector
	public $scripts      = [ 'jetSearchBricks' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'ajax-search';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Ajax Search', 'jet-search' );
	}

	// Set builder control groups
	public function set_control_groups() {
		$this->register_jet_control_group(
			'section_search_form_settings',
			[
				'title' => esc_html__( 'Search Form', 'jet-search' ),
				'tab'   => 'content',
			]
		);

		$this->register_jet_control_group(
			'section_search_settings',
			[
				'title' => esc_html__( 'Search Query', 'jet-search' ),
				'tab'   => 'content',
			]
		);

		$this->register_jet_control_group(
			'section_results_area_settings',
			[
				'title' => esc_html__( 'Results Area', 'jet-search' ),
				'tab'   => 'content',
			]
		);

		$this->register_jet_control_group(
			'section_results_custom_fields',
			[
				'title' => esc_html__( 'Custom Fields', 'jet-search' ),
				'tab'   => 'content',
			]
		);

		$this->register_jet_control_group(
			'section_notifications_settings',
			[
				'title' => esc_html__( 'Notifications', 'jet-search' ),
				'tab'   => 'content',
			]
		);

		$this->register_jet_control_group(
			'section_search_form_style',
			[
				'title' => esc_html__( 'Search Form', 'jet-search' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_search_fields_holder_style',
			[
				'title'    => esc_html__( 'Input Field and Categories List Wrapper', 'jet-search' ),
				'tab'      => 'style',
				'required' => [ 'show_search_category_list', '!=', false ],
			]
		);

		$this->register_jet_control_group(
			'section_search_input_style',
			[
				'title' => esc_html__( 'Input Field', 'jet-search' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_search_submit_style',
			[
				'title' => esc_html__( 'Submit Button', 'jet-search' ),
				'tab'   => 'style',
				'required' => [ 'show_search_submit', '!=', false ],
			]
		);

		$this->register_jet_control_group(
			'section_search_category_style',
			[
				'title'    => esc_html__( 'Categories List', 'jet-search' ),
				'tab'      => 'style',
				'required' => [ 'show_search_category_list', '!=', false ],
			]
		);

		$this->register_jet_control_group(
			'section_results_area_style',
			[
				'title' => esc_html__( 'Results Area', 'jet-search' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_results_items_style',
			[
				'title' => esc_html__( 'Results Items', 'jet-search' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_title_custom_fields_styles',
			[
				'title'    => esc_html__( 'Before/After Title Custom Fields', 'jet-search' ),
				'tab'      => 'style',
				'required' => [ 'show_title_related_meta', '!=', false ],
			]
		);

		$this->register_jet_control_group(
			'section_content_custom_fields_styles',
			[
				'title'    => esc_html__( 'Before/After Content Custom Fields', 'jet-search' ),
				'tab'      => 'style',
				'required' => [ 'show_content_related_meta', '!=', false ],
			]
		);

		$this->register_jet_control_group(
			'section_results_counter_style',
			[
				'title'    => esc_html__( 'Results Counter', 'jet-search' ),
				'tab'      => 'style',
				'required' => [ 'show_results_counter', '!=', false ],
			]
		);

		$this->register_jet_control_group(
			'section_full_results_style',
			[
				'title'    => esc_html__( 'All Results Button', 'jet-search' ),
				'tab'      => 'style',
				'required' => [ 'show_full_results', '!=', false ],
			]
		);

		$this->register_jet_control_group(
			'section_bullet_pagination_style',
			[
				'title'    => esc_html__( 'Bullet Pagination', 'jet-search' ),
				'tab'      => 'style',
				'required' => [ 'bullet_pagination', '!=', '' ],
			]
		);

		$this->register_jet_control_group(
			'section_number_pagination_style',
			[
				'title'    => esc_html__( 'Number Pagination', 'jet-search' ),
				'tab'      => 'style',
				'required' => [ 'number_pagination', '!=', '' ],
			]
		);

		$this->register_jet_control_group(
			'section_navigation_arrows_style',
			[
				'title'    => esc_html__( 'Navigation Arrows', 'jet-search' ),
				'tab'      => 'style',
				'required' => [ 'navigation_arrows', '!=', '' ],
			]
		);

		$this->register_jet_control_group(
			'section_notifications_style',
			[
				'title' => esc_html__( 'Notifications', 'jet-search' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_spinner_style',
			[
				'title' => esc_html__( 'Spinner', 'jet-search' ),
				'tab'   => 'style',
			]
		);
	}

	// Set builder controls
	public function set_controls() {

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
		$this->start_jet_control_group( 'section_search_form_settings' );

		$this->register_jet_control(
			'selected_search_field_icon',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Input Icon', 'jet-search' ),
				'type'    => 'icon',
			],
		);

		$this->register_jet_control(
			'search_clear_btn_icon',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Clear Text Button Icon', 'jet-search' ),
				'description' => esc_html__( 'Firefox and IE Explorer browsers are not supported', 'jet-search' ),
				'type'        => 'svg',
			],
		);

		$this->register_jet_control(
			'search_placeholder_text',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Placeholder Text', 'jet-search' ),
				'type'           => 'text',
				'default'        => esc_html__( 'Search ...', 'jet-search' ),
				'hasDynamicData' => false,
			]
		);

		$this->register_jet_control(
			'symbols_for_start_searching',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Minimal Quantity of Symbols for Search', 'jet-search' ),
				'type'    => 'number',
				'inline'  => true,
				'min'     => 1,
				'max'     => 10,
				'default' => 2,
			],
		);

		$this->register_jet_control(
			'search_by_empty_value',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Allow Search by Empty String', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'show_search_submit',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Show Submit Button', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => true,
			]
		);

		$this->register_jet_control(
			'search_submit_label',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Submit Button Label', 'jet-search' ),
				'type'           => 'text',
				'default'        => '',
				'hasDynamicData' => false,
				'required'       => [ 'show_search_submit', '=', true ],
			]
		);

		$this->register_jet_control(
			'selected_search_submit_icon',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Submit Button Icon', 'jet-search' ),
				'type'     => 'icon',
				'default'  => [
					'library' => 'themify',
					'icon'    => 'ti-search',
				],
				'css'      => [
					[
						'selector' => $this->css_selector( '__submit-icon svg' ),
					],
				],
				'required' => [ 'show_search_submit', '=', true ],
			],
		);

		$this->register_jet_control(
			'show_search_category_list',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Show Categories List', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'search_taxonomy',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Taxonomy', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => \Jet_Search_Tools::get_taxonomies(),
				'default'  => 'category',
				'required' => [ 'show_search_category_list', '=', true ],
			]
		);

		$this->register_jet_control(
			'search_category_select_placeholder',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Select Placeholder', 'jet-search' ),
				'type'           => 'text',
				'default'        => esc_html__( 'All Categories', 'jet-search' ),
				'hasDynamicData' => false,
				'required'       => [ 'show_search_category_list', '=', true ],
			]
		);

		$this->register_jet_control(
			'search_form_responsive_on_mobile',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Responsive Form on Mobile', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Search Settings` Section
		 */
		$this->start_jet_control_group( 'section_search_settings' );

		$this->register_jet_control(
			'current_query',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Search by the current query', 'jet-search' ),
				'description' => esc_html__( 'Use for Archive Templates', 'jet-search' ),
				'type'        => 'checkbox',
				'default'     => false,
			]
		);

		$this->register_jet_control(
			'search_source',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Source', 'jet-search' ),
				'description' => esc_html__( 'You can select particular search areas. If nothing is selected in the option, the search will be made over the entire site', 'jet-search' ),
				'type'        => 'select',
				'multiple'    => true,
				'options'     => \Jet_Search_Tools::get_post_types(),
				'default'     => array(),
				'required'    => [ 'current_query', '=', false ],
			]
		);

		$this->register_jet_control(
			'tab_search_query_include',
			[
				'type'     => 'separator',
				'label'    => esc_html__( 'Include', 'jet-search' ),
				'required' => [ 'current_query', '=', false ],
			]
		);

		$this->register_jet_control(
			'include_terms_ids',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Terms', 'jet-search' ),
				'type'           => 'select',
				'multiple'       => true,
				'searchable'     => true,
				'optionsAjax'    => [
					'action'     => 'jet_search_get_query_control_options',
					'query_type' => 'terms',
				],
				'required'       => [ 'current_query', '=', false ],
			]
		);

		$this->register_jet_control(
			'tab_search_query_exclude',
			[
				'type'     => 'separator',
				'label'    => esc_html__( 'Exclude', 'jet-search' ),
				'required' => [ 'current_query', '=', false ],
			]
		);

		$this->register_jet_control(
			'exclude_terms_ids',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Terms', 'jet-search' ),
				'type'           => 'select',
				'multiple'       => true,
				'searchable'     => true,
				'optionsAjax'    => [
					'action'     => 'jet_search_get_query_control_options',
					'query_type' => 'terms',
				],
				'required'       => [ 'current_query', '=', false ],
			]
		);

		$this->register_jet_control(
			'exclude_posts_ids',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Posts', 'jet-search' ),
				'type'           => 'select',
				'multiple'       => true,
				'searchable'     => true,
				'optionsAjax'    => [
					'action'   => 'bricks_get_posts',
					'postType' => 'post',
				],
				'required'       => [ 'current_query', '=', false ],
			]
		);

		$this->register_jet_control(
			'tab_search_query_exclude_end',
			[
				'type'     => 'separator',
				'required' => [ 'current_query', '=', false ],
			]
		);

		$this->register_jet_control(
			'custom_fields_source',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Search in custom fields', 'jet-search' ),
				'description'    => esc_html__( 'Set comma separated custom fields keys list (_sku, _price, etc.)', 'jet-search' ),
				'type'           => 'text',
				'default'        => '',
				'hasDynamicData' => false,
			]
		);

		$this->register_jet_control(
			'sentence',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Sentence Search', 'jet-search' ),
				'type'     => 'checkbox',
				'default'  => false,
			]
		);

		$this->register_jet_control(
			'search_in_taxonomy',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Search in taxonomy terms', 'jet-search' ),
				'description' => esc_html__( 'Include in the search results the posts containing the terms of the selected taxonomies with the search phrase in the term name', 'jet-search' ),
				'type'        => 'checkbox',
				'default'     => false,
			]
		);

		$this->register_jet_control(
			'search_in_taxonomy_source',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Taxonomies', 'jet-search' ),
				'type'     => 'select',
				'multiple' => true,
				'options'  => \Jet_Search_Tools::get_taxonomies( true ),
				'default'  => array(),
				'required' => [ 'search_in_taxonomy', '=', true ],
			]
		);

		$this->register_jet_control(
			'results_order_by',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Results Order By', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => [
					'relevance'     => esc_html__( 'Relevance', 'jet-search' ),
					'ID'            => esc_html__( 'ID', 'jet-search' ),
					'author'        => esc_html__( 'Author', 'jet-search' ),
					'title'         => esc_html__( 'Title', 'jet-search' ),
					'date'          => esc_html__( 'Date', 'jet-search' ),
					'modified'      => esc_html__( 'Last modified', 'jet-search' ),
					'rand'          => esc_html__( 'Rand', 'jet-search' ),
					'comment_count' => esc_html__( 'Number of Comments (descending)', 'jet-search' ),
					'menu_order'    => esc_html__( 'Menu order', 'jet-search' ),
				],
				'default'  => 'relevance',
			]
		);

		$this->register_jet_control(
			'results_order',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Results Order', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => [
					'asc'  => esc_html__( 'ASC', 'jet-search' ),
					'desc' => esc_html__( 'DESC', 'jet-search' ),
				],
				'default'  => 'asc',
			]
		);

		$this->register_jet_control(
			'limit_query',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Posts Per Page', 'jet-search' ),
				'description' => esc_html__( 'A number of results displayed on one search page.', 'jet-search' ),
				'type'        => 'number',
				'inline'      => true,
				'default'     => 5,
				'min'         => 0,
				'max'         => 50,
			],
		);

		$this->register_jet_control(
			'limit_query_in_result_area',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Posts Number', 'jet-search' ),
				'description' => esc_html__( 'A number of results displayed in one search query.', 'jet-search' ),
				'type'        => 'number',
				'inline'      => true,
				'default'     => 25,
				'min'         => 0,
				'max'         => 150,
			],
		);

		do_action( 'jet-search/ajax-search-bricks/add-custom-controls', $this );

		$this->end_jet_control_group();

		/**
		 * `Results Area` Section
		 */
		$this->start_jet_control_group( 'section_results_area_settings' );

		$this->register_jet_control(
			'results_area_width_by',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Results Area Width', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => [
					'form'          => esc_html__( 'by Search Form', 'jet-search' ),
					'fields_holder' => esc_html__( 'by Input Box and Categories List', 'jet-search' ),
					'custom'        => esc_html__( 'Custom', 'jet-search' ),
				],
				'default'  => 'form',
			]
		);

		$this->register_jet_control(
			'results_area_custom_width',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Custom Width', 'jet-search' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'width',
						'selector' => $css_scheme['results_area']
					],
				],
				'required' => [ 'results_area_width_by', '=', 'custom' ],
			]
		);

		$this->register_jet_control(
			'results_area_custom_position',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Custom Position', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => [
					'left'   => esc_html__( 'Left', 'jet-search' ),
					'center' => esc_html__( 'Center', 'jet-search' ),
					'right'  => esc_html__( 'Right', 'jet-search' ),
				],
				'default'  => 'left',
				'css'      => [
					[
						'selector' => $css_scheme['results_area'],
						'property' => 'left',
						'value'    => '0;right:auto;',
						'required' => 'left',
					],
					[
						'selector' => $css_scheme['results_area'],
						'property' => 'left',
						'value'    => '50%;right: auto;-webkit-transform: translateX(-50%);transform: translateX(-50%);',
						'required' => 'center',
					],
					[
						'selector' => $css_scheme['results_area'],
						'property' => 'left',
						'value'    => 'auto;right: 0;',
						'required' => 'right',
					],
				],
				'required' => [ 'results_area_width_by', '=', 'custom' ],
			]
		);

		$this->register_jet_control(
			'highlight_searched_text',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Highlight Searched Text', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'thumbnail_visible',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Show Post Thumbnail', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => true,
			]
		);

		$this->register_jet_control(
			'thumbnail_size',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Thumbnail Size', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => \Jet_Search_Tools::get_image_sizes(),
				'default'  => 'left',
				'required' => [ 'thumbnail_visible', '=', true ],
			]
		);

		$this->register_jet_control(
			'thumbnail_placeholder',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Thumbnail Placeholder', 'bricks' ),
				'type'           => 'image',
				'default'        => array(
					'url' => \Jet_Search_Tools::get_placeholder_image_src(),
				),
				'hasDynamicData' => false,
				'required'       => [ 'thumbnail_visible', '=', true ],
			]
		);

		$this->register_jet_control(
			'post_content_source',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Post Content Source', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => [
					'content'      => esc_html__( 'Post Content', 'jet-search' ),
					'excerpt'      => esc_html__( 'Post Excerpt', 'jet-search' ),
					'custom-field' => esc_html__( 'Custom Field', 'jet-search' ),
				],
				'default'  => 'content',
			]
		);

		$this->register_jet_control(
			'post_content_custom_field_key',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Custom Field Key', 'jet-search' ),
				'type'           => 'text',
				'default'        => '',
				'hasDynamicData' => false,
				'required'       => [ 'post_content_source', '=', 'custom-field' ],
			]
		);

		$this->register_jet_control(
			'post_content_length',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Post Content Length', 'jet-search' ),
				'description' => esc_html__( 'Set 0 to hide content.', 'jet-search' ),
				'type'        => 'number',
				'inline'      => true,
				'min'         => 0,
				'max'         => 150,
				'default'     => 30,
			],
		);

		$this->register_jet_control(
			'show_product_price',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Show Product Price', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'show_product_rating',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Show Product Rating', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'show_results_counter',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Show Results Counter', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => true,
			]
		);

		$this->register_jet_control(
			'results_counter_text',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Results Counter Text', 'jet-search' ),
				'type'           => 'text',
				'default'        => esc_html__( 'Results', 'jet-search' ),
				'hasDynamicData' => false,
				'required'       => [ 'show_results_counter', '=', true ],
			]
		);

		$this->register_jet_control(
			'show_full_results',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Show All Results Button', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => true,
			]
		);

		$this->register_jet_control(
			'full_results_btn_text',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'All Results Button Text', 'jet-search' ),
				'type'           => 'text',
				'default'        => esc_html__( 'See all results', 'jet-search' ),
				'hasDynamicData' => false,
				'required'       => [ 'show_full_results', '=', true ],
			]
		);

		$this->register_jet_control(
			'show_result_new_tab',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Open Results In New Tab', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'results_navigation_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Results Navigation', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'bullet_pagination',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Bullet Pagination', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => [
					''          => esc_html__( 'Hide', 'jet-search' ),
					'in_header' => esc_html__( 'Show in header', 'jet-search' ),
					'in_footer' => esc_html__( 'Show in footer', 'jet-search' ),
					'both'      => esc_html__( 'Show in header and footer', 'jet-search' ),
				],
				'default'  => '',
			]
		);

		$this->register_jet_control(
			'number_pagination',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Number Pagination', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => [
					''          => esc_html__( 'Hide', 'jet-search' ),
					'in_header' => esc_html__( 'Show in header', 'jet-search' ),
					'in_footer' => esc_html__( 'Show in footer', 'jet-search' ),
					'both'      => esc_html__( 'Show in header and footer', 'jet-search' ),
				],
				'default'  => '',
			]
		);

		$this->register_jet_control(
			'navigation_arrows',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Navigation Arrows', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => [
					''          => esc_html__( 'Hide', 'jet-search' ),
					'in_header' => esc_html__( 'Show in header', 'jet-search' ),
					'in_footer' => esc_html__( 'Show in footer', 'jet-search' ),
					'both'      => esc_html__( 'Show in header and footer', 'jet-search' ),
				],
				'default'  => 'in_header',
			]
		);

		$this->register_jet_control(
			'navigation_arrows_type',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Navigation Arrows Type', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => \Jet_Search_Tools::get_available_prev_arrows_list(),
				'default'  => 'angle',
				'required' => [ 'navigation_arrows', '=', true ],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Custom Fields` Sections
		 */
		$this->start_jet_control_group( 'section_results_custom_fields' );

		$this->add_meta_controls( 'title_related', esc_html__( 'Before/After Title', 'jet-search' ) );

		$this->add_meta_controls( 'content_related', esc_html__( 'Before/After Content', 'jet-search' ) );

		$this->end_jet_control_group();

		/**
		 * `Notifications` Section
		 */
		$this->start_jet_control_group( 'section_notifications_settings' );

		$this->register_jet_control(
			'negative_search',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Negative search results', 'jet-search' ),
				'type'           => 'textarea',
				'rows'           => 5,
				'inlineEditing'  => true,
				'default'        => esc_html__( 'Sorry, but nothing matched your search terms.', 'jet-search' ),
				'hasDynamicData' => false,
			]
		);

		$this->register_jet_control(
			'server_error',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Technical error', 'jet-search' ),
				'type'           => 'textarea',
				'rows'           => 5,
				'inlineEditing'  => true,
				'default'        => esc_html__( 'Sorry, but we cannot handle your search query now. Please, try again later!', 'jet-search' ),
				'hasDynamicData' => false,
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Search Form` Style Section
		 */
		$this->start_jet_control_group( 'section_search_form_style' );

		$this->register_jet_control(
			'tab_search_form_normal',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Normal', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_form_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['form'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_form_box_shadow',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'   => 'box-shadow',
				'css'    => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['form'],
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'tab_search_form_focus',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Focus', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_form_bg_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['form_focus'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_form_border_color_focus',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Border Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'border-color',
						'selector' => $css_scheme['form_focus'],
					]
				],
				'required' => [ 'search_form_border', '!=', '' ],
			]
		);

		$this->register_jet_control(
			'search_form_box_shadow_focus',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'   => 'box-shadow',
				'css'    => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['form_focus'],
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'tab_search_form_focus_end',
			[
				'type' => 'separator',
			]
		);

		$this->register_jet_control(
			'search_form_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['form'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_form_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-search' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['form'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Input Field and Categories List Wrapper` Style Section
		 */

		$this->start_jet_control_group( 'section_search_fields_holder_style' );

		$this->register_jet_control(
			'tab_search_fields_holder_normal',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Normal', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_fields_holder_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['fields_holder'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_fields_holder_box_shadow',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'   => 'box-shadow',
				'css'    => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['fields_holder'],
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'tab_search_fields_holder_focus',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Focus', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_fields_holder_bg_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['form_focus'] . ' ' . $css_scheme['fields_holder'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_fields_holder_border_color_focus',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Border Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'border-color',
						'selector' => $css_scheme['form_focus'] . ' ' . $css_scheme['fields_holder'],
					]
				],
				'required' => [ 'search_fields_holder_border', '!=', '' ],
			]
		);

		$this->register_jet_control(
			'search_fields_holder_box_shadow_focus',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'   => 'box-shadow',
				'css'    => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['form_focus'] . ' ' . $css_scheme['fields_holder'],
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'tab_search_fields_holder_focus_end',
			[
				'type'  => 'separator',
			]
		);

		$this->register_jet_control(
			'search_fields_holder_padding',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Padding', 'jet-search' ),
				'type'   => 'dimensions',
				'css'    => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['fields_holder'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_fields_holder_border',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Border', 'jet-search' ),
				'type'     => 'border',
				'css'      => [
					[
						'property' => 'border',
						'selector' => $css_scheme['fields_holder'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Input Field` Style Section
		 */

		$this->start_jet_control_group( 'section_search_input_style' );

		$this->register_jet_control(
			'search_input_typography',
			[
				'tab'         => 'style',
				'label'       => esc_html__( 'Typography', 'jet-search' ),
				'description' => esc_html__( 'To properly display the Text decoration overline, you need to increase the value of Line height.', 'jet-search' ),
				'type'        => 'typography',
				'css'         => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['field'],
					],
				],
				'exclude'     => [ 'text-align', 'color' ]
			]
		);

		$this->register_jet_control(
			'search_placeholder_typography',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Placeholder Typography', 'jet-search' ),
				'type'    => 'typography',
				'css'     => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['field'] . '::placeholder',
					],
				],
				'exclude' => [ 'text-align' ]
			]
		);

		$this->register_jet_control(
			'search_input_icon_style',
			[
				'type'     => 'separator',
				'label'    => esc_html__( 'Icon', 'jet-search' ),
				'required' => [ 'selected_search_field_icon', '!=', '' ],
			]
		);

		$this->register_jet_control(
			'search_input_icon_font_size',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Font Size', 'jet-search' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'font-size',
						'selector' => $css_scheme['field_icon']
					],
				],
				'required' => [ 'selected_search_field_icon', '!=', '' ],
			]
		);

		$this->register_jet_control(
			'search_input_icon_gap',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Gap', 'jet-search' ),
				'type'     => 'number',
				'units'    => true,
				'required' => [ 'selected_search_field_icon', '!=', '' ],
			]
		);

		$this->register_jet_control(
			'tab_search_input_normal',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Normal', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_input_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Text Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['field'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_input_icon_color',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Icon Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'color',
						'selector' => $css_scheme['field_icon'],
					]
				],
				'required' => [ 'selected_search_field_icon', '!=', '' ],
			]
		);

		$this->register_jet_control(
			'search_input_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['field'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_input_box_shadow',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'   => 'box-shadow',
				'css'    => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['field'],
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'tab_search_input_focus',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Focus', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_input_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Text Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['form_focus'] . ' ' . $css_scheme['field'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_input_icon_color_focus',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Icon Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'color',
						'selector' => $css_scheme['form_focus'] . ' ' . $css_scheme['field_icon'],
					]
				],
				'required' => [ 'selected_search_field_icon', '!=', '' ],
			]
		);

		$this->register_jet_control(
			'search_input_bg_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['form_focus'] . ' ' . $css_scheme['field'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_input_border_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => $css_scheme['form_focus'] . ' ' . $css_scheme['field'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_input_box_shadow_focus',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'   => 'box-shadow',
				'css'    => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['form_focus'] . ' ' . $css_scheme['field'],
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'tab_search_input_focus_end',
			[
				'type'  => 'separator',
			]
		);

		$this->register_jet_control(
			'search_input_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['field'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_input_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['field_wrapper'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_input_border',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Border', 'jet-search' ),
				'type'    => 'border',
				'css'     => [
					[
						'selector' => $css_scheme['field'],
					]
				],
				'default' => [
					'style' => 'solid',
					'color' => [
						'hex' => '#E1E5EB',
					],
					'width' => [
						'top'    => 1,
						'right'  => 1,
						'bottom' => 1,
						'left'   => 1,
					]
				]
			]
		);

		$this->register_jet_control(
			'search_clear_btn_icon_style',
			[
				'type'     => 'separator',
				'label'    => esc_html__( 'Clear Text Button Icon', 'jet-search' ),
				'required' => [ 'search_clear_btn_icon', '!=', '' ],
			]
		);

		$this->register_jet_control(
			'search_clear_btn_icon_size',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Icon Size', 'jet-search' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'width',
						'selector' => $css_scheme['field'] . '::-webkit-search-cancel-button',
					],
					[
						'property' => 'height',
						'selector' => $css_scheme['field'] . '::-webkit-search-cancel-button',
					],
				],
				'default'  => 16,
				'required' => [ 'search_clear_btn_icon', '!=', '' ],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Submit Button` Style Section
		 */

		$this->start_jet_control_group( 'section_search_submit_style' );

		$this->register_jet_control(
			'search_submit_typography',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Typography', 'jet-search' ),
				'type'    => 'typography',
				'css'     => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['submit'],
					],
				],
				'exclude' => [ 'text-align', 'color' ]
			]
		);

		$this->register_jet_control(
			'search_submit_icon_font_size',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Icon Font Size', 'jet-search' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'font-size',
						'selector' => $css_scheme['submit_icon']
					],
				],
				'required' => [ 'selected_search_submit_icon', '!=', '' ],
			]
		);

		$this->register_jet_control(
			'search_submit_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['submit'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_submit_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['submit'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_submit_box_shadow',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'   => 'box-shadow',
				'css'    => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['submit'],
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'search_submit_vertical_align',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Vertical Align', 'jet-search' ),
				'tooltip' => [
					'content'  => 'align-self',
					'position' => 'top-left',
				],
				'type'    => 'align-items',
				'css'     => [
					[
						'property' => 'align-self',
						'selector' => $css_scheme['submit'],
					],
				]
			]
		);

		$this->register_jet_control(
			'search_submit_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['submit'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_submit_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['submit'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_submit_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-search' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['submit'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Categories List` Style Section
		 */

		$this->start_jet_control_group( 'section_search_category_style' );

		$this->register_jet_control(
			'search_category_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Width', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'width',
						'selector' => $css_scheme['categories']
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_typography',
			[
				'tab'         => 'style',
				'label'       => esc_html__( 'Typography', 'jet-search' ),
				'description' => esc_html__( 'To properly display the Text decoration overline, you need to increase the value of Line height.', 'jet-search' ),
				'type'        => 'typography',
				'css'         => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['categories_select'],
					],
					[
						'property' => 'typography',
						'selector' => $css_scheme['categories'] . ' .chosen-single',
					],
				],
				'exclude'     => [ 'text-align', 'color' ],
			]
		);

		$this->register_jet_control(
			'search_category_icon_font_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Arrow Font Size', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'width',
						'selector' => $css_scheme['categories_select_icon'] . ' svg'
					],
					[
						'property' => 'height',
						'selector' => $css_scheme['categories_select_icon'] . ' svg'
					],
				],
			]
		);

		$this->register_jet_control(
			'tab_search_category_normal',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Normal', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_category_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Text Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['categories_select'],
					],
					[
						'property' => 'color',
						'selector' => $css_scheme['categories'] . ' .chosen-single',
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_icon_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Arrow Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'fill',
						'selector' => $css_scheme['categories_select_icon'] . ' svg > *',
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories_select'],
					],
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories'] . ' .chosen-single',
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_box_shadow',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'   => 'box-shadow',
				'css'    => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['categories_select'],
					],
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['categories'] . ' .chosen-single',
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'tab_search_category_focus',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Focus', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_category_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Text Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['categories_select'] . ':focus',
					],
					[
						'property' => 'color',
						'selector' => $css_scheme['categories'] . ' .chosen-with-drop .chosen-single',
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_icon_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Arrow Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'fill',
						'selector' => $css_scheme['categories_select'] . ':focus ~ ' . $css_scheme['categories_select_icon'] . ' svg > *',
					],
					[
						'property' => 'fill',
						'selector' => $css_scheme['categories'] . ' .chosen-with-drop ~ ' . $css_scheme['categories_select_icon'] . ' svg > *',
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_bg_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories_select'] . ':focus',
					],
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories'] . ' .chosen-with-drop .chosen-single',
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_border_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => $css_scheme['categories_select'] . ':focus',
					],
					[
						'property' => 'border-color',
						'selector' => $css_scheme['categories'] . ' .chosen-with-drop .chosen-single',
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_box_shadow_focus',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'   => 'box-shadow',
				'css'    => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['categories_select'] . ':focus',
					],
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['categories'] . ' .chosen-with-drop .chosen-single',
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'tab_search_category_focus_end',
			[
				'type'  => 'separator',
			]
		);

		$this->register_jet_control(
			'search_category_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['categories_select'],
					],
					[
						'property' => 'padding',
						'selector' => $css_scheme['categories'] . ' .chosen-single',
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['categories'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_border',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Border', 'jet-search' ),
				'type'    => 'border',
				'css'     => [
					[
						'selector' => $css_scheme['categories_select'],
					],
					[
						'selector' => $css_scheme['categories'] . ' .chosen-single',
					]
				],
				'default' => [
					'style' => 'solid',
					'color' => [
						'hex' => '#E1E5EB',
					],
					'width' => [
						'top'    => 1,
						'right'  => 1,
						'bottom' => 1,
						'left'   => 1,
					]
				]
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Dropdown Style', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_max_height',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Max Height', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'max-height',
						'selector' => $css_scheme['categories'] . ' .chosen-results'
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Gap', 'jet-search' ),
				'type'    => 'number',
				'units'   => true,
				'css'     => [
					[
						'property' => 'margin-top',
						'selector' => $css_scheme['categories'] . ' .chosen-drop'
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories'] . ' .chosen-drop'
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_box_shadow',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'    => 'box-shadow',
				'css'     => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['categories'] . ' .chosen-drop',
					],
				],
				'default' => [
					'values' => [
						'offsetX' => "0",
						'offsetY' => "0",
						'blur'    => "10",
						'spread'  => "0"
					],
					'color'  => [
						'rgb' => "rgba(0, 0, 0, 0.5)"
					]
				],
				'inline'  => true,
				'small'   => true,
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-search' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['categories'] . ' .chosen-drop',
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_scrollbar_thumb_bg',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Scrollbar Thumb Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories'] . ' .chosen-drop ::-webkit-scrollbar-thumb'
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Dropdown Items Style', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_typography',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Typography', 'jet-search' ),
				'type'    => 'typography',
				'css'     => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['categories'] . ' .chosen-results li',
					],
				],
				'exclude' => [ 'text-align', 'color' ]
			]
		);

		$this->register_jet_control(
			'tab_search_category_dropdown_items_normal',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Normal', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['categories'] . ' .chosen-results li'
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories'] . ' .chosen-results li'
					],
				],
			]
		);

		$this->register_jet_control(
			'tab_search_category_dropdown_items_hov',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Hover', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_color_hov',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['categories'] . ' .chosen-results li.highlighted'
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_bg_color_hov',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories'] . ' .chosen-results li.highlighted'
					],
				],
			]
		);

		$this->register_jet_control(
			'tab_search_category_dropdown_items_hover_end',
			[
				'type'  => 'separator',
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['categories'] . ' .chosen-results li',
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_border_radius',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Border Radius', 'jet-search' ),
				'type'    => 'border',
				'css'     => [
					[
						'selector' => $css_scheme['categories'] . ' .chosen-results li',
					],
				],
				'exclude' => [ 'style', 'color', 'width'],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'margin-top',
						'selector' => $css_scheme['categories'] . ' .chosen-results li:not(:first-child)'
					],
				],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Results Area` Style Section
		 */

		$this->start_jet_control_group( 'section_results_area_style' );

		$this->register_jet_control(
			'results_area_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Results Area', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'results_area_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'margin-top',
						'selector' => $css_scheme['results_area'],
					],
				],
			]
		);

		$this->register_jet_control(
			'results_area_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['results_area'],
					],
				],
			]
		);

		$this->register_jet_control(
			'results_area_box_shadow',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'    => 'box-shadow',
				'css'     => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['results_area'],
					],
				],
				'default' => [
					'values' => [
						'blur' => 10
					],
					'color'  => [
						'rgb' => 'rgba(0, 0, 0, 0.5)'
					]
				],
				'inline'  => true,
				'small'   => true,
			]
		);

		$this->register_jet_control(
			'results_area_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-search' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['results_area'],
					],
				],
			]
		);

		$this->register_jet_control(
			'results_header_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Results Header', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'results_header_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['results_header'],
					],
				],
			]
		);

		$this->register_jet_control(
			'results_header_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-search' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['results_header'],
					],
				],
			]
		);

		$this->register_jet_control(
			'results_list_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Results List', 'jet-search' ),
			]
		);


		$this->register_jet_control(
			'enable_scroll',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Enable Scrolling', 'jet-search' ),
				'type'    => 'checkbox',
				'css'     => [
					[
						'property' => 'overflow-y',
						'selector' => $css_scheme['results_slide'],
						'value'    => 'auto;'
					],
				],
				'default'  => false,
				'rerender' => true,
			]
		);

		$this->register_jet_control(
			'results_list_height',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Max Height (px)', 'jet-search' ),
				'type'     => 'number',
				'inline'   => true,
				'min'      => 0,
				'max'      => 500,
				'default'  => 500,
				'required' => [ 'enable_scroll', '=', true ],
			],
		);

		$this->register_jet_control(
			'results_list_scrollbar_bg',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Scrollbar Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['results_slide'] . '::-webkit-scrollbar',
					],
				],
				'required' => [ 'enable_scroll', '=', true ],
			]
		);

		$this->register_jet_control(
			'results_list_scrollbar_thumb_bg',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Scrollbar Thumb Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['results_slide'] . '::-webkit-scrollbar-thumb',
					],
				],
				'required' => [ 'enable_scroll', '=', true ],
			]
		);

		$this->register_jet_control(
			'results_footer_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Results Footer', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'results_footer_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['results_footer'],
					],
				],
			]
		);

		$this->register_jet_control(
			'results_footer_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-search' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['results_footer'],
					],
				],
			]
		);

		$this->register_jet_control(
			'results_highlight',
			[
				'type'     => 'separator',
				'label'    => esc_html__( 'Results Highlight', 'jet-search' ),
				'required' => [ 'highlight_searched_text', '!=', false ],
			]
		);

		$this->register_jet_control(
			'results_highlight_color',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'color',
						'selector' => $css_scheme['results_item'] . ' mark',
					],
				],
				'required' => [ 'highlight_searched_text', '!=', false ],
			]
		);

		$this->register_jet_control(
			'results_highlight_bg',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'BackgroundColor', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'background',
						'selector' => $css_scheme['results_item'] . ' mark',
					],
				],
				'required' => [ 'highlight_searched_text', '!=', false ],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Results Items` Style Section
		 */

		$this->start_jet_control_group( 'section_results_items_style' );

		$this->register_jet_control(
			'tab_results_item_normal',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Normal', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'results_item_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['results_item_link'],
					],
				],
			]
		);

		$this->register_jet_control(
			'results_item_rating_color',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Product Rating Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'color',
						'selector' => $css_scheme['results_rating_star'] . ':before',
					],
				],
				'required' => [ 'show_product_rating', '!=', false ],
			]
		);

		$this->register_jet_control(
			'results_item_rating_unmarked_color',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Product Rating Unmarked Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'color',
						'selector' => $css_scheme['results_rating_star'],
					],
				],
				'required' => [ 'show_product_rating', '!=', false ],
			]
		);

		$this->register_jet_control(
			'tab_results_item_hover',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Hover', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'results_item_title_color_hov',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Title Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['results_item_link'] . ':hover ' . $css_scheme['results_item_title'],
					],
				],
			]
		);

		$this->register_jet_control(
			'results_item_title_decoration_hov',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Title Text Decoration', 'jet-search' ),
				'type'     => 'typography',
				'css'      => [
					[
						'property' => 'font',
						'selector' => $css_scheme['results_item_link'] . ':hover ' . $css_scheme['results_item_title'],
					],
				],
				'exclude'  => [
					'color',
					'font-family',
					'font-weight',
					'font-style',
					'font-size',
					'text-align',
					'text-transform',
					'text-shadow',
					'line-height',
					'letter-spacing',
				],
			]
		);

		$this->register_jet_control(
			'results_item_content_color_hov',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Content Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['results_item_link'] . ':hover ' . $css_scheme['results_item_content'],
					],
				],
			]
		);

		$this->register_jet_control(
			'results_item_price_color_hov',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Product Price Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'color',
						'selector' => $css_scheme['results_item_link'] . ':hover ' . $css_scheme['results_item_price'] . ' .price',
					],
				],
				'required' => [ 'show_product_price', '!=', false ],
			]
		);

		$this->register_jet_control(
			'results_item_old_price_color_hov',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Product Old Price Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'color',
						'selector' => $css_scheme['results_item_link'] . ':hover ' . $css_scheme['results_item_price'] . ' .price del',
					],
				],
				'required' => [ 'show_product_price', '!=', false ],
			]
		);

		$this->register_jet_control(
			'tab_results_item_hover_end',
			[
				'type'  => 'separator',
			]
		);

		$this->register_jet_control(
			'results_item_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['results_item_link'],
					]
				],
			]
		);

		$this->register_jet_control(
			'results_item_align',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Alignment', 'jet-search' ),
				'tooltip' => [
					'content'  => 'align-self',
					'position' => 'top-left',
				],
				'type'    => 'text-align',
				'css'     => [
					[
						'property' => 'text-align',
						'selector' => $css_scheme['results_item_link'],
					],
				],
			]
		);

		$this->register_jet_control(
			'results_item_divider',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Divider', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'results_item_divider_style',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Style', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => [
					'solid'  => esc_html__( 'Solid', 'jet-search' ),
					'double' => esc_html__( 'Double', 'jet-search' ),
					'dotted' => esc_html__( 'Dotted', 'jet-search' ),
					'dashed' => esc_html__( 'Dashed', 'jet-search' ),
				],
				'default'  => 'solid',
				'css'      => [
					[
						'property' => 'border-top-style',
						'selector' => $css_scheme['results_item'] . ':not(:first-child)',
					],
				],
				'required' => [ 'results_item_divider', '!=', false ],
			]
		);

		$this->register_jet_control(
			'results_item_divider_weight',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Weight', 'jet-search' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'border-top-width',
						'selector' => $css_scheme['results_item'] . ':not(:first-child)'
					],
				],
				'default'  => 1,
				'required' => [ 'results_item_divider', '!=', false ],
			]
		);

		$this->register_jet_control(
			'results_item_divider_color',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'border-color',
						'selector' => $css_scheme['results_item'] . ':not(:first-child)',
					],
				],
				'required' => [ 'results_item_divider', '!=', false ],
			]
		);

		$this->register_jet_control(
			'results_item_thumb_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Thumbnail', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'results_item_thumb_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Width', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'width',
						'selector' => $css_scheme['results_item_thumb']
					],
				],
			]
		);

		$this->register_jet_control(
			'results_item_thumb_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
			]
		);

		$this->register_jet_control(
			'results_item_thumb_border_radius',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Border Radius', 'jet-search' ),
				'type'    => 'border',
				'css'     => [
					[
						'selector' => $css_scheme['results_item_thumb_img'],
					]
				],
				'exclude' => [ 'style', 'color', 'width'],
			]
		);

		$this->register_jet_control(
			'results_item_title_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Title', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'results_item_title_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-search' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['results_item_title'],
					],
				],
			]
		);

		$this->register_jet_control(
			'results_item_title_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['results_item_title'],
					]
				],
			]
		);

		$this->register_jet_control(
			'results_item_content_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Content', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'results_item_content_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-search' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['results_item_content'],
					],
				],
			]
		);

		$this->register_jet_control(
			'results_item_content_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['results_item_content'],
					]
				],
			]
		);

		$this->register_jet_control(
			'results_item_rating_heading',
			[
				'type'     => 'separator',
				'label'    => esc_html__( 'Product Rating', 'jet-search' ),
				'required' => [ 'show_product_rating', '!=', false ],
			]
		);

		$this->register_jet_control(
			'results_item_rating_font_size',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Font Size', 'jet-search' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'font-size',
						'selector' => $css_scheme['results_rating_star']
					],
				],
				'required' => [ 'show_product_rating', '!=', false ],
			]
		);

		$this->register_jet_control(
			'results_item_rating_margin',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Margin', 'jet-search' ),
				'type'    => 'dimensions',
				'css'     => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['results_item_rating'],
					]
				],
				'required' => [ 'show_product_rating', '!=', false ],
			]
		);

		$this->register_jet_control(
			'results_item_price_heading',
			[
				'type'     => 'separator',
				'label'    => esc_html__( 'Product Price', 'jet-search' ),
				'required' => [ 'show_product_price', '!=', false ],
			]
		);

		$this->register_jet_control(
			'results_item_price_typography',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Typography', 'jet-search' ),
				'type'     => 'typography',
				'css'      => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['results_item_price'] . ' .price',
					],
				],
				'required' => [ 'show_product_price', '!=', false ],
			]
		);

		$this->register_jet_control(
			'results_item_previous_price_typography',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Old Price Typography', 'jet-search' ),
				'type'     => 'typography',
				'css'      => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['results_item_price'] . ' .price del',
					],
				],
				'required' => [ 'show_product_price', '!=', false ],
			]
		);

		$this->register_jet_control(
			'results_item_price_margin',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Margin', 'jet-search' ),
				'type'     => 'dimensions',
				'css'      => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['results_item_price'],
					]
				],
				'required' => [ 'show_product_price', '!=', false ],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Before/After Title Custom Fields` Style Section
		 */

		$this->start_jet_control_group( 'section_title_custom_fields_styles' );

		$this->add_meta_style_controls(
			'title_related',
			esc_html__( 'Before/After Title', 'jet-search' ),
			'jet-search-title-fields'
		);

		$this->end_jet_control_group();

		/**
		 * `Before/After Content Custom Fields` Style Section
		 */

		$this->start_jet_control_group( 'section_content_custom_fields_styles' );

		$this->add_meta_style_controls(
			'content_related',
			esc_html__( 'Before/After Content', 'jet-search' ),
			'jet-search-content-fields'
		);

		$this->end_jet_control_group();

		/**
		 * `Results Counter` Style Section
		 */

		$this->start_jet_control_group( 'section_results_counter_style' );

		$this->register_jet_control(
			'results_counter_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-search' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['results_counter'],
					],
				],
				'exclude' => [ 'text-align', 'color' ]
			]
		);

		$this->register_jet_control(
			'results_counter_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['results_counter'],
					],
				],
			]
		);

		$this->register_jet_control(
			'results_counter_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['results_counter'],
					],
				],
			]
		);

		$this->register_jet_control(
			'results_counter_box_shadow',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'  => 'box-shadow',
				'css'   => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['results_counter'],
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'results_counter_padding',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Padding', 'jet-search' ),
				'type'   => 'dimensions',
				'css'    => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['results_counter'],
					]
				],
			]
		);

		$this->register_jet_control(
			'results_counter_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-search' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['results_counter'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `All Results Button` Style Section
		 */

		$this->start_jet_control_group( 'section_full_results_style' );

		$this->register_jet_control(
			'full_results_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-search' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['full_results'],
					],
				],
				'exclude' => [ 'text-align', 'color' ]
			]
		);

		$this->register_jet_control(
			'full_results_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['full_results'],
					],
				],
			]
		);

		$this->register_jet_control(
			'full_results_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['full_results'],
					],
				],
			]
		);

		$this->register_jet_control(
			'full_results_box_shadow',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'   => 'box-shadow',
				'css'    => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['full_results'],
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'full_results_padding',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Padding', 'jet-search' ),
				'type'   => 'dimensions',
				'css'    => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['full_results'],
					]
				],
			]
		);

		$this->register_jet_control(
			'full_results_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-search' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['full_results'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Bullet Pagination` Style Section
		 */

		$this->start_jet_control_group( 'section_bullet_pagination_style' );

		$this->register_jet_control(
			'bullet_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Size', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'width',
						'selector' => $css_scheme['bullet_btn'],
					],
					[
						'property' => 'height',
						'selector' => $css_scheme['bullet_btn'],
					],
				],
			]
		);

		$this->register_jet_control(
			'tab_bullet_pagination_normal',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Normal', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'bullet_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['bullet_btn'],
					],
				],
			]
		);

		$this->register_jet_control(
			'tab_bullet_pagination_active',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Active', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'bullet_bg_color_active',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['bullet_btn'] . $css_scheme['active_nav_btn'],
					],
				],
			]
		);

		$this->register_jet_control(
			'bullet_border_color_active',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Border Color', 'jet-search' ),
				'type'    => 'color',
				'css'     => [
					[
						'property' => 'border-color',
						'selector' => $css_scheme['bullet_btn'] . $css_scheme['active_nav_btn'],
					],
				],
			]
		);

		$this->register_jet_control(
			'tab_bullet_pagination_active_end',
			[
				'type' => 'separator',
			]
		);

		$this->register_jet_control(
			'bullet_border',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Border', 'jet-search' ),
				'type'    => 'border',
				'css'     => [
					[
						'property' => 'border',
						'selector' => $css_scheme['bullet_btn']
					],
				],
				'default' => [
					'style' => 'solid'
				]
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Number Pagination` Style Section
		 */

		$this->start_jet_control_group( 'section_number_pagination_style' );

		$this->register_jet_control(
			'number_typography',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Typography', 'jet-search' ),
				'type'    => 'typography',
				'css'     => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['number_btn'],
					],
				],
				'exclude' => [ 'text-align', 'color' ]
			]
		);

		$this->register_jet_control(
			'tab_number_pagination_normal',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Normal', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'number_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['number_btn'],
					]
				],
			]
		);

		$this->register_jet_control(
			'number_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['number_btn'],
					]
				],
			]
		);

		$this->register_jet_control(
			'tab_number_pagination_active',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Active', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'number_color_active',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['number_btn'] . $css_scheme['active_nav_btn'],
					]
				],
			]
		);

		$this->register_jet_control(
			'number_bg_color_active',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['number_btn'] . $css_scheme['active_nav_btn'],
					]
				],
			]
		);

		$this->register_jet_control(
			'number_border_color_active',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => $css_scheme['number_btn'] . $css_scheme['active_nav_btn'],
					]
				],
			]
		);

		$this->register_jet_control(
			'tab_number_pagination_active_end',
			[
				'type' => 'separator',
			]
		);

		$this->register_jet_control(
			'number_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['number_btn'],
					]
				],
			]
		);

		$this->register_jet_control(
			'number_border',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Border', 'jet-search' ),
				'type'    => 'border',
				'css'     => [
					[
						'selector' => $css_scheme['number_btn']
					],
				],
				'default' => [
					'style' => 'solid'
				]
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Navigation Arrows` Style Section
		 */

		$this->start_jet_control_group( 'section_navigation_arrows_style' );

		$this->register_jet_control(
			'arrow_font_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Font Size', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'width',
						'selector' => $css_scheme['arrow_btn'] . ' svg',
					],
					[
						'property' => 'height',
						'selector' => $css_scheme['arrow_btn'] . ' svg',
					],
				],
			]
		);

		$this->register_jet_control(
			'tab_navigation_arrows_normal',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Normal', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'arrow_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'fill',
						'selector' => $css_scheme['arrow_btn'] . ' svg > *',
					],
				],
			]
		);

		$this->register_jet_control(
			'arrow_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['arrow_btn'],
					],
				],
			]
		);

		$this->register_jet_control(
			'tab_navigation_arrows_hover',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Hover', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'arrow_color_hov',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'fill',
						'selector' => $css_scheme['arrow_btn'] . ':hover svg > *',
					],
				],
			]
		);

		$this->register_jet_control(
			'tab_navigation_arrows_hover_end',
			[
				'type'  => 'separator',
			]
		);

		$this->register_jet_control(
			'arrow_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['arrow_btn'],
					]
				],
			]
		);

		$this->register_jet_control(
			'arrow_border',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Border', 'jet-search' ),
				'type'    => 'border',
				'css'     => [
					[
						'selector' => $css_scheme['arrow_btn']
					],
				],
				'default' => [
					'style' => 'solid',
					'color' => [
						'hex' => '#E1E5EB',
					],
					'width' => [
						'top'    => 1,
						'right'  => 1,
						'bottom' => 1,
						'left'   => 1,
					]
				]
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Notifications` Style Section
		 */

		$this->start_jet_control_group( 'section_notifications_style' );

		$this->register_jet_control(
			'notifications_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-search' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['message'],
					],
				],
				'exclude' => [ 'text-align', 'color' ],
			]
		);

		$this->register_jet_control(
			'notifications_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['message'],
					],
				],
			]
		);

		$this->register_jet_control(
			'notifications_align',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Alignment', 'jet-search' ),
				'tooltip' => [
					'content'  => 'align-self',
					'position' => 'top-left',
				],
				'type'    => 'text-align',
				'css'     => [
					[
						'property' => 'text-align',
						'selector' => $css_scheme['message'],
					],
				]
			]
		);

		$this->register_jet_control(
			'notifications_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['message'],
					]
				],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Spinner` Style Section
		 */

		$this->start_jet_control_group( 'section_spinner_style' );

		$this->register_jet_control(
			'spinner_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['spinner'],
					],
				],
			]
		);

		$this->end_jet_control_group();

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

		$this->register_jet_control(
			'show_' . $position_slug . '_meta',
			[
				'tab'     => 'content',
				'label'   => sprintf( esc_html__( 'Show Meta %s', 'jet-search' ), $position_name ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'meta_' . $position_slug . '_position',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Meta Fields Position', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => [
					'before' => esc_html__( 'Before', 'jet-search' ),
					'after'  => esc_html__( 'After', 'jet-search' ),
				],
				'default'  => 'before',
				'required' => [ 'show_' . $position_slug . '_meta', '=', true ],
			]
		);

		$this->register_jet_control(
			$position_slug . '_meta',
			[
				'tab'           => 'content',
				'label'         => esc_html__( 'Meta Fields List', 'jet-search' ),
				'type'          => 'repeater',
				'titleProperty' => 'title', // Default 'title'
				'default'       => [
					[
						'meta_label'  => 'Label',
						'meta_format' => '%s',
						'date_format' => 'F j, Y',
					],
				],
				'placeholder'   => esc_html__( 'Meta Field', 'jet-search' ),
				'fields'        => [
					'meta_key'      => [
						'label'          => esc_html__( 'Key', 'jet-search' ),
						'description'    => esc_html__( 'Meta key from post-meta table in database', 'jet-search' ),
						'type'           => 'text',
						'hasDynamicData' => false,
						'default'        => '',
					],
					'meta_label'    => [
						'label'          => esc_html__( 'Label', 'jet-search' ),
						'type'           => 'text',
						'default'        => '',
						'hasDynamicData' => false,
					],
					'meta_format'   => [
						'label'          => esc_html__( 'Value Format', 'jet-search' ),
						'description'    => esc_html__( 'Value format string, accepts HTML markup. %s - is meta value', 'jet-search' ),
						'type'           => 'text',
						'hasDynamicData' => false,
					],
					'meta_callback' => [
						'label'    => esc_html__( 'Prepare meta value with callback', 'jet-search' ),
						'type'     => 'select',
						'multiple' => false,
						'options'  => \Jet_Search_Tools::allowed_meta_callbacks(),
						'default'  => '',
					],
					'date_format'   => [
						'label'          => esc_html__( 'Format', 'jet-search' ),
						'description'    => sprintf( '<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">%s</a>', esc_html__( 'Documentation on date and time formatting', 'jet-search' ) ),
						'type'           => 'text',
						'default'        => 'F j, Y',
						'hasDynamicData' => false,
						'required'       => [ 'meta_callback', '=', [ 'date', 'date_i18n' ] ],
					]
				],
				'required'      => [ 'show_' . $position_slug . '_meta', '=', true ],
			]
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

		$this->register_jet_control(
			$position_slug . '_meta_label_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Meta Label', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			$position_slug . '_meta_label_hov_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color on Hover', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => '.jet-ajax-search__item-link:hover .' . $base . '__item-label',
					]
				],
			]
		);

		$this->register_jet_control(
			$position_slug . '_meta_label_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-search' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => '.' . $base . '__item-label',
					],
				],
			]
		);

		$this->register_jet_control(
			$position_slug . '_meta_value_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Meta Value', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			$position_slug . '_meta_hov_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color on Hover', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => '.jet-ajax-search__item-link:hover .' . $base . '__item-value',
					]
				],
			]
		);

		$this->register_jet_control(
			$position_slug . '_meta_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-search' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => '.' . $base . '__item-value',
					],
				],
			]
		);

		$this->register_jet_control(
			$position_slug . '_meta_label_display',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Display Meta Label and Value', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => [
					''       => esc_html__( 'As Blocks', 'jet-search' ),
					'inline' => esc_html__( 'Inline', 'jet-search' ),
				],
				'default'  => '',
				'css'      => [
					[
						'property' => 'display',
						'selector' => '.' . $base . '__item-label',
					],
					[
						'property' => 'display',
						'selector' => '.' . $base . '__item-value',
					],
				],
			]
		);

		$this->register_jet_control(
			$position_slug . '_meta_label_gap',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Gap Between Label and Value', 'jet-search' ),
				'type'     => 'number',
				'units'    => true,
				'default'  => 5,
				'inline'   => false,
				'required' => [ $position_slug . '_meta_label_display', '=', 'inline' ],
			]
		);

		$this->register_jet_control(
			$position_slug . '_meta_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => '.' . $base,
					]
				],
			]
		);

		$this->register_jet_control(
			$position_slug . '_meta_align',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Alignment', 'jet-search' ),
				'tooltip' => [
					'content'  => 'align-self',
					'position' => 'top-left',
				],
				'type'    => 'text-align',
				'css'     => [
					[
						'property' => 'text-align',
						'selector' => '.' . $base,
					],
				]
			]
		);
	}

	// Render element HTML
	public function render() {

		parent::render();

		$settings   = $this->parse_jet_render_attributes( $this->get_jet_settings() );
		$element_id = $this->id;
		$inline_css = $this->generate_inline_css( $settings, $element_id );

		$this->set_attribute( '_root', 'class', 'brxe-' . $this->id );
		$this->set_attribute( '_root', 'class', 'brxe-jet-search-el' );
		$this->set_attribute( '_root', 'data-element-id', $this->id );

		echo "<div {$this->render_attributes( '_root' )}>";

		$render = new \Jet_Search_Render( $settings, $element_id );
		$render->render();

		echo "<style>" . $inline_css . "</style>";
		echo "</div>";
	}

	public function parse_jet_render_attributes( $attrs = [] ) {

		$highlight_searched_text = ! empty( $attrs['highlight_searched_text'] ) ? $attrs['highlight_searched_text'] : '';

		$attrs['selected_search_submit_icon'] = isset( $attrs['selected_search_submit_icon'] ) ? Element::render_icon( $attrs['selected_search_submit_icon'] ) : null;
		$attrs['selected_search_field_icon']  = isset( $attrs['selected_search_field_icon'] ) ? Element::render_icon( $attrs['selected_search_field_icon'] ) : null;
		$attrs['highlight_searched_text']     = true === $highlight_searched_text ? 'yes' : '';

		return $attrs;
	}

	public function generate_inline_css( $settings, $element_id ) {
		$inline_css = '';

		$search_clear_btn_icon_url        = ! empty( $settings['search_clear_btn_icon']['url'] ) ? $settings['search_clear_btn_icon']['url'] : '';
		$search_input_icon_gap            = ! empty( $settings['search_input_icon_gap'] ) ? $settings['search_input_icon_gap'] : '';
		$search_category_padding          = ! empty( $settings['search_category_padding'] ) ? $settings['search_category_padding'] : '';
		$search_category_dropdown_padding = ! empty( $settings['search_category_dropdown_padding'] ) ? $settings['search_category_dropdown_padding'] : '';
		$results_item_thumb_gap           = ! empty( $settings['results_item_thumb_gap'] ) ? $settings['results_item_thumb_gap'] : '';
		$title_related_meta_label_gap     = ! empty( $settings['title_related_meta_label_gap'] ) ? $settings['title_related_meta_label_gap'] : '';
		$content_related_meta_label_gap   = ! empty( $settings['content_related_meta_label_gap'] ) ? $settings['content_related_meta_label_gap'] : '';
		$enable_scroll                    = ! empty( $settings['enable_scroll'] ) ? $settings['enable_scroll'] : '';
		$results_list_height              = ! empty( $settings['results_list_height'] ) ? $settings['results_list_height'] : '';
		$results_area_width_by            = ! empty( $settings['results_area_width_by'] ) ? $settings['results_area_width_by'] : '';

		//fix results_area_width_by reset from custom values
		if ( 'form' === $results_area_width_by || 'fields_holder' === $results_area_width_by ) {
			$inline_css .= "#brxe-" . $element_id . " .jet-ajax-search .jet-ajax-search__results-area {
				left: 0;
				right: auto;
				width: 100%;
				transform: translateX(0);
			}";
		}

		//fix results_list_height when enable_scroll disabled
		if ( '' != $enable_scroll || true === $enable_scroll ) {
			if ( '' != $results_list_height ) {
				$inline_css .= "#brxe-" . $element_id . " .jet-ajax-search__results-slide {
					max-height: " . $results_list_height . "px;
				}";
			}
		}

		//search_clear_btn_icon_styles
		if ( '' != $search_clear_btn_icon_url ) {
			$inline_css .= "#brxe-" . $element_id . " .jet-ajax-search__field::-webkit-search-cancel-button {
				-webkit-appearance: none;
				background-size: contain !important;
				background: url('" . $search_clear_btn_icon_url . "') no-repeat 50% 50%;
				opacity: 1;
			}";
		}

		//search_input_icon_gap
		if ( '' != $search_input_icon_gap ) {
			$inline_css .= "
				body:not(.rtl) #brxe-" . $element_id . " .jet-ajax-search__field-icon {
					left: " . $search_input_icon_gap . "px
				}

				body.rtl #brxe-" . $element_id . " .jet-ajax-search__field-icon {
					right: " . $search_input_icon_gap . "px
				}
			";
		}

		//search_category_padding
		if ( '' != $search_category_padding ) {
			$inline_css .= "
				body:not(.rtl) #brxe-" . $element_id . " .jet-ajax-search__field-icon {
					right: " . $search_category_padding['right'] . "px
				}

				body.rtl #brxe-" . $element_id . " .jet-ajax-search__field-icon {
					left: " . $search_category_padding['left'] . "px
				}
			";
		}

		//search_category_dropdown_padding
		if ( '' != $search_category_dropdown_padding ) {
			$search_category_dropdown_padding_top    = ! empty( $search_category_dropdown_padding['top'] ) ? $search_category_dropdown_padding['top'] : 0;
			$search_category_dropdown_padding_right  = ! empty( $search_category_dropdown_padding['right'] ) ? $search_category_dropdown_padding['right'] : 0;
			$search_category_dropdown_padding_bottom = ! empty( $search_category_dropdown_padding['bottom'] ) ? $search_category_dropdown_padding['bottom'] : 0;
			$search_category_dropdown_padding_left   = ! empty( $search_category_dropdown_padding['left'] ) ? $search_category_dropdown_padding['left'] : 0;

			$inline_css .= "
				#brxe-" . $element_id . " .jet-ajax-search__categories .chosen-drop {
					padding: " . $search_category_dropdown_padding_top . "px 0 " . $search_category_dropdown_padding_bottom . "px 0;
				}

				#brxe-" . $element_id . " .jet-ajax-search__categories .chosen-results {
					padding: 0 " . $search_category_dropdown_padding_right . "px 0 " . $search_category_dropdown_padding_left . "px
				}
			";
		}

		return $inline_css;
	}


	public function css_selector( $mod = null ) {
		return sprintf( '%1$s%2$s', $this->css_selector, $mod );
	}
}