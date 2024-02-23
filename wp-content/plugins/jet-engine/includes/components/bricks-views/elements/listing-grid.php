<?php
namespace Jet_Engine\Bricks_Views\Elements;

use Jet_Engine\Bricks_Views\Helpers\Options_Converter;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Listing_Grid extends Base {

	// Element properties
	public $category = 'jetengine'; // Use predefined element category 'general'
	public $name = 'jet-engine-listing-grid'; // Make sure to prefix your elements
	public $icon = 'jet-engine-icon-listing-grid'; // Themify icon font class
	public $css_selector = '.jet-listing-grid'; // Default CSS selector
	public $scripts = [ 'jetEngineBricks' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'listing-grid';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Listing Grid', 'jet-engine' );
	}

	public function register_group_query_settings() {
		$this->register_jet_control_group(
			'section_custom_query',
			[
				'title' => esc_html__( 'Custom Query', 'jet-engine' ),
				'tab'   => 'content',
			]
		);
	}

	public function register_group_visibility_settings() {
		$this->register_jet_control_group(
			'section_widget_visibility',
			[
				'title' => esc_html__( 'Element Visibility', 'jet-engine' ),
				'tab'   => 'content',
			]
		);
	}

	// Set builder control groups
	public function set_control_groups() {
		$this->register_jet_control_group(
			'general',
			[
				'title' => esc_html__( 'General', 'jet-engine' ),
				'tab'   => 'content',
			]
		);

		$this->register_group_query_settings();
		$this->register_group_visibility_settings();

		$this->register_jet_control_group(
			'slider',
			[
				'title'    => esc_html__( 'Slider', 'jet-engine' ),
				'tab'      => 'content',
				'required' => [ 'is_masonry', '=', false ],
			]
		);

		$this->register_jet_control_group(
			'section_caption_style',
			[
				'title' => esc_html__( 'Columns', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_loader_style',
			[
				'title'    => esc_html__( 'Loader', 'jet-engine' ),
				'tab'      => 'style',
				'required' => [
					[ 'use_load_more', '=', true ],
				],
			]
		);

		$this->register_jet_control_group(
			'section_slider_style',
			[
				'title'    => esc_html__( 'Slider', 'jet-engine' ),
				'tab'      => 'style',
				'required' => [
					[ 'carousel_enabled', '=', true ],
					[ 'is_masonry', '=', false ],
				],
			]
		);

		$this->register_jet_control_group(
			'section_scrollbar_style',
			[
				'title'    => esc_html__( 'Scrollbar', 'jet-engine' ),
				'tab'      => 'style',
				'required' => [
					[ 'scroll_slider_enabled', '=', true ],
					[ 'is_masonry', '=', false ],
				],
			]
		);
	}

	public function register_controls_query_settings() {
		$this->start_jet_control_group( 'section_custom_query' );

		$this->register_jet_control(
			'custom_query',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Use custom query', 'jet-engine' ),
				'description' => esc_html__( 'Allow to use custom query from Query Builder as items source', 'jet-engine' ),
				'type'        => 'checkbox',
				'default'     => false,
			]
		);

		$this->register_jet_control(
			'custom_query_id',
			[
				'tab'        => 'content',
				'label'      => esc_html__( 'Custom Query', 'jet-engine' ),
				'type'       => 'select',
				'options'    => \Jet_Engine\Query_Builder\Manager::instance()->get_queries_for_options(),
				'searchable' => true,
				'required'   => [ 'custom_query', '=', true ],
			]
		);

		$this->end_jet_control_group();
	}

	public function register_controls_visibility_settings() {
		$this->start_jet_control_group( 'section_widget_visibility' );

		$this->register_jet_control(
			'hide_widget_if',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Hide element if', 'jet-engine' ),
				'type'    => 'select',
				'options' => Options_Converter::changes_empty_key_in_options( jet_engine()->listings->get_widget_hide_options() ),
				'default' => 'always_show'
			]
		);

		$this->end_jet_control_group();
	}

	// Set builder controls
	public function set_controls() {

		$css_scheme = [
			'items'       => '> .jet-listing-grid > .jet-listing-grid__items',
			'item'        => '> .jet-listing-grid > .jet-listing-grid__items > .jet-listing-grid__item',
			'loader'      => '.jet-listing-grid__loader',
			'loader-text' => '.jet-listing-grid__loader-text',
			'slider-list' => '.jet-listing-grid__slider > .jet-listing-grid__items > .slick-list',
			'slider-icon' => '.jet-listing-grid__slider-icon',
			'prev-arrow'  => '.jet-listing-grid__slider-icon.prev-arrow',
			'next-arrow'  => '.jet-listing-grid__slider-icon.next-arrow',
			'dots'        => '.jet-listing-grid__slider .jet-slick-dots',
			'dot'         => '.jet-listing-grid__slider .jet-slick-dots li',
			'dot-active'  => '.jet-listing-grid__slider .jet-slick-dots > li.slick-active',
		];

		$this->start_jet_control_group( 'general' );

		$this->register_jet_control(
			'lisitng_id',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Listing', 'jet-engine' ),
				'type'        => 'select',
				'options'     => jet_engine()->listings->get_listings_for_options(),
				'inline'      => true,
				'clearable'   => false,
				'searchable'  => true,
				'pasteStyles' => false,
			]
		);

		$this->register_jet_control(
			'columns',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Columns', 'jet-engine' ),
				'type'    => 'select',
				'inline'  => true,
				'default' => 3,
				'options' => array(
					1  => 1,
					2  => 2,
					3  => 3,
					4  => 4,
					5  => 5,
					6  => 6,
					7  => 7,
					8  => 8,
					9  => 9,
					10 => 10,
					'auto' => __( 'Auto', 'jet-engine' ),
				),
				'css'     => [
					[
						'property' => '--columns',
						'selector' => $css_scheme['items'],
					],
				],
			],
		);

		$this->register_jet_control(
			'column_min_width',
			array(
				'label'    => __( 'Column Min Width', 'jet-engine' ),
				'type'     => 'number',
				'default'  => 240,
				'min'      => 0,
				'max'      => 1600,
				'step'     => 1,
				'required' => [ 'columns', '=', 'auto' ],
			)
		);

		$this->register_jet_control(
			'is_archive_template',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Use as Archive Template', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'post_status',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Status', 'jet-engine' ),
				'type'     => 'select',
				'multiple' => true,
				'options'  => [
					'publish' => esc_html__( 'Publish', 'jet-engine' ),
					'future'  => esc_html__( 'Future', 'jet-engine' ),
					'draft'   => esc_html__( 'Draft', 'jet-engine' ),
					'pending' => esc_html__( 'Pending Review', 'jet-engine' ),
					'private' => esc_html__( 'Private', 'jet-engine' ),
					'inherit' => esc_html__( 'Inherit', 'jet-engine' ),
				],
				'default'  => 'publish',
				'required' => [ 'is_archive_template', '=', false ],
			]
		);

		$this->register_jet_control(
			'use_random_posts_num',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Use Random posts number', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => false,
				'required' => [ 'is_archive_template', '=', false ],
			]
		);

		$this->register_jet_control(
			'random_posts_num_note',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Note: the `Posts number` control set min random posts number', 'jet-engine' ),
				'type'     => 'info',
				'required' => [
					[ 'is_archive_template', '=', false ],
					[ 'use_random_posts_num', '=', true ],
				],
			]
		);

		$this->register_jet_control(
			'posts_num',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Posts number', 'jet-engine' ),
				'type'     => 'number',
				'min'      => 1,
				'max'      => 1000,
				'default'  => 6,
				'required' => [ 'is_archive_template', '=', false ],
			]
		);

		$this->register_jet_control(
			'max_posts_num',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Max Random Posts number', 'jet-engine' ),
				'type'     => 'number',
				'min'      => 1,
				'max'      => 1000,
				'default'  => 9,
				'required' => [
					[ 'is_archive_template', '=', false ],
					[ 'use_random_posts_num', '=', true ],
				],
			]
		);

		$this->register_jet_control(
			'not_found_message',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Not found message', 'jet-engine' ),
				'type'           => 'text',
				'default'        => esc_html__( 'No data was found', 'jet-engine' ),
				'hasDynamicData' => false,
			]
		);

		$this->register_jet_control(
			'lazy_load',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Lazy load', 'jet-engine' ),
				'description' => esc_html__( 'Lazy load the listing for boosts rendering performance.', 'jet-engine' ),
				'type'        => 'checkbox',
				'default'     => false,
			]
		);

		$this->register_jet_control(
			'lazy_load_offset',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Lazy load offset', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'required' => [ 'lazy_load', '=', true ],
			]
		);

		$this->register_jet_control(
			'is_masonry',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Is masonry grid', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => false,
				'required' => [ 'columns', '!=', 'auto' ],
			]
		);

		$this->register_jet_control(
			'equal_columns_height',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Equal columns height', 'jet-engine' ),
				'description' => esc_html__( 'Fits only top level sections of grid item', 'jet-engine' ),
				'type'        => 'checkbox',
				'default'     => false,
				'required'    => [ 'is_masonry', '=', false ],
			]
		);

		$this->register_jet_control(
			'use_load_more',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Load more', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'load_more_type',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Load more type', 'jet-engine' ),
				'type'     => 'select',
				'options'  => [
					'click'  => esc_html__( 'By Click', 'jet-engine' ),
					'scroll' => esc_html__( 'Infinite Scroll', 'jet-engine' ),
				],
				'default'  => 'click',
				'required' => [ 'use_load_more', '=', true ],
			]
		);

		$this->register_jet_control(
			'load_more_id',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Load more element ID', 'jet-engine' ),
				'description' => esc_html__( 'Please, make sure to add a Button element that will be used as "Load more" button', 'jet-engine' ),
				'type'        => 'text',
				'required'    => [
					[ 'use_load_more', '=', true ],
					[ 'load_more_type', '=', 'click' ],
				],
			]
		);

		$this->register_jet_control(
			'loader_text',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Loader text', 'jet-engine' ),
				'type'     => 'text',
				'required' => [ 'use_load_more', '=', true ],
			]
		);

		$this->register_jet_control(
			'loader_spinner',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Loader spinner', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => false,
				'required' => [ 'use_load_more', '=', true ],
			]
		);

		$this->register_jet_control(
			'use_custom_post_types',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Use Custom Post Types', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'custom_post_types',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Post Types', 'jet-engine' ),
				'type'     => 'select',
				'multiple' => true,
				'options'  => jet_engine()->listings->get_post_types_for_options(),
				'required' => [ 'use_custom_post_types', '=', true ],
			]
		);

		do_action( 'jet-engine/listing/bricks/after-general-settings', $this );

		$this->end_jet_control_group();

		$this->register_controls_query_settings();
		$this->register_controls_visibility_settings();

		$this->start_jet_control_group( 'slider' );

		$this->register_jet_control(
			'carousel_enabled',
			array(
				'tab'      => 'content',
				'label'    => esc_html__( 'Enable Slider', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => false,
				'required' => [ 'scroll_slider_enabled', '=', false ],
			)
		);

		$this->register_jet_control(
			'slides_to_scroll',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Slides to Scroll', 'jet-engine' ),
				'type'     => 'number',
				'min'      => 1,
				'max'      => 6,
				'default'  => 1,
				'required' => [
					[ 'columns', '!=', 1 ],
					[ 'carousel_enabled', '=', true ],
				],
			]
		);

		$this->register_jet_control(
			'arrows',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Show Arrows Navigation', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => true,
				'required' => [ 'carousel_enabled', '=', true ],
			]
		);

		$this->register_jet_control(
			'arrow_icon',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Arrow Icon', 'jet-engine' ),
				'type'     => 'select',
				'options'  => apply_filters( 'jet-engine/listing/grid/arrow-icons/options', [
					'fa fa-angle-left'          => __( 'Angle', 'jet-engine' ),
					'fa fa-chevron-left'        => __( 'Chevron', 'jet-engine' ),
					'fa fa-angle-double-left'   => __( 'Angle Double', 'jet-engine' ),
					'fa fa-arrow-left'          => __( 'Arrow', 'jet-engine' ),
					'fa fa-caret-left'          => __( 'Caret', 'jet-engine' ),
					'fa fa-long-arrow-left'     => __( 'Long Arrow', 'jet-engine' ),
					'fa fa-arrow-circle-left'   => __( 'Arrow Circle', 'jet-engine' ),
					'fa fa-chevron-circle-left' => __( 'Chevron Circle', 'jet-engine' ),
					'fa fa-caret-square-o-left' => __( 'Caret Square', 'jet-engine' ),
				] ),
				'default'  => 'fa fa-angle-left',
				'required' => [
					[ 'carousel_enabled', '=', true ],
					[ 'arrows', '=', true ],
				],
			]
		);

		$this->register_jet_control(
			'dots',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Show Dots Navigation', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => false,
				'required' => [ 'carousel_enabled', '=', true ],
			]
		);

		$this->register_jet_control(
			'autoplay',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Autoplay', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => true,
				'required' => [ 'carousel_enabled', '=', true ],
			]
		);

		$this->register_jet_control(
			'autoplay_speed',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Autoplay Speed', 'jet-engine' ),
				'type'     => 'number',
				'min'      => 0,
				'max'      => 10000,
				'default'  => 5000,
				'required' => [
					[ 'carousel_enabled', '=', true ],
					[ 'autoplay', '=', true ],
				],
			]
		);

		$this->register_jet_control(
			'pause_on_hover',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Pause On Hover', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => true,
				'required' => [
					[ 'carousel_enabled', '=', true ],
					[ 'autoplay', '=', true ],
				],
			]
		);

		$this->register_jet_control(
			'infinite',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Infinite Loop', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => true,
				'required' => [ 'carousel_enabled', '=', true ],
			]
		);

		$this->register_jet_control(
			'center_mode',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Center Mode', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => false,
				'required' => [ 'carousel_enabled', '=', true ],
			]
		);

		$this->register_jet_control(
			'effect',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Effect', 'jet-engine' ),
				'type'     => 'select',
				'options'  => [
					'slide' => esc_html__( 'Slide', 'jet-engine' ),
					'fade'  => esc_html__( 'Fade', 'jet-engine' ),
				],
				'default'  => 'slide',
				'required' => [
					[ 'columns', '=', '1' ],
					[ 'carousel_enabled', '=', true ],
				],
			]
		);

		$this->register_jet_control(
			'speed',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Animation Speed', 'jet-engine' ),
				'type'     => 'number',
				'min'      => 0,
				'max'      => 10000,
				'default'  => 500,
				'required' => [ 'carousel_enabled', '=', true ],
			]
		);

		$this->register_jet_control(
			'scroll_slider_enabled',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Enable Scroll Slider', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => false,
				'required' => [ 'carousel_enabled', '=', false ],
			]
		);

		$this->register_jet_control(
			'scroll_slider_on',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Scroll Slider On', 'jet-engine' ),
				'type'     => 'select',
				'multiple' => true,
				'options'  => [
					'desktop' => esc_html__( 'Desktop', 'jet-engine' ),
					'tablet'  => esc_html__( 'Tablet', 'jet-engine' ),
					'mobile'  => esc_html__( 'Mobile', 'jet-engine' ),
				],
				'default'  => [ 'desktop', 'tablet', 'mobile' ],
				'required' => [ 'scroll_slider_enabled', '=', true ],
			]
		);

		$this->register_jet_control(
			'static_column_width',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Static column width', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'max-width',
						'selector' => $css_scheme['item'],
					],
					[
						'property' => 'flex-basis',
						'selector' => $css_scheme['item'],
					],
				],
				'required' => [ 'scroll_slider_enabled', '=', true ],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_caption_style' );

		$this->register_jet_control(
			'horizontal_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Horizontal Gap', 'jet-engine' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '20px',
				'css'     => [
					[
						'property' => '--column-gap',
						'selector' => $css_scheme['items'],
					],
					[
						'property' => 'column-gap',
						'selector' => $css_scheme['items'],
					],
				],
			]
		);

		$this->register_jet_control(
			'vertical_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Vertical Gap', 'jet-engine' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '20px',
				'css'     => [
					[
						'property' => '--row-gap',
						'selector' => $css_scheme['items'],
					],
					[
						'property' => 'row-gap',
						'selector' => $css_scheme['items'],
					],
				]
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_loader_style' );

		$this->register_jet_control(
			'loader_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Spinner Color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => '--spinner-color',
						'selector' => $css_scheme['loader'],
					]
				],
			]
		);

		$this->register_jet_control(
			'loader_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Spinner Size', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => '--spinner-size',
						'selector' => $css_scheme['loader'],
					]
				],
			]
		);

		$this->register_jet_control(
			'loader_text_typography',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Typography', 'jet-engine' ),
				'type'     => 'typography',
				'css'      => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['loader-text'],
					],
				],
				'required' => [
					[ 'use_load_more', '=', true ],
					[ 'loader_text', '!=', '' ],
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_slider_style' );

		$this->register_jet_control(
			'center_moder_padding',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Center Mode Padding', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property'  => 'padding-left',
						'selector'  => $css_scheme['slider-list'],
						'important' => true,
					],
					[
						'property'  => 'padding-right',
						'selector'  => $css_scheme['slider-list'],
						'important' => true,
					],
				],
				'required' => [ 'center_mode', '=', true ],
			]
		);

		$this->register_jet_control(
			'arrows_box_size',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Slider arrows box size', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'width',
						'selector' => $css_scheme['slider-icon'],
					],
					[
						'property' => 'height',
						'selector' => $css_scheme['slider-icon'],
					],
					[
						'property' => 'line-height',
						'selector' => $css_scheme['slider-icon'],
					],
					[
						'property' => 'margin-top',
						'selector' => $css_scheme['slider-icon'],
						'value'    => 'calc( %s / -2 )',
					],
				],
				'required' => [ 'arrows', '=', true ],
			]
		);

		$this->register_jet_control(
			'arrows_size',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Slider arrows size', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'font-size',
						'selector' => $css_scheme['slider-icon'],
					],
				],
				'required' => [ 'arrows', '=', true ],
			]
		);

		$this->register_jet_control(
			'arrows_border',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Arrows border', 'jet-engine' ),
				'type'     => 'border',
				'css'      => [
					[
						'property' => 'border',
						'selector' => $css_scheme['slider-icon'],
					],
				],
				'required' => [ 'arrows', '=', true ],
			]
		);

		$this->register_jet_control(
			'arrow_color',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Color', 'jet-engine' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'color',
						'selector' => $css_scheme['slider-icon'],
					],
				],
				'required' => [ 'arrows', '=', true ],
			]
		);

		$this->register_jet_control(
			'arrow_bg_color',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Background color', 'jet-engine' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['slider-icon'],
					],
				],
				'required' => [ 'arrows', '=', true ],
			]
		);

		$this->register_jet_control(
			'prev_arrow_position',
			[
				'type'     => 'separator',
				'label'    => esc_html__( 'Prev Arrow Position', 'jet-engine' ),
				'required' => [ 'arrows', '=', true ],
			]
		);

		$this->register_jet_control(
			'prev_vert_position',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Vertical Position by', 'jet-engine' ),
				'type'     => 'select',
				'options'  => [
					'top'    => esc_html__( 'Top', 'jet-engine' ),
					'bottom' => esc_html__( 'Bottom', 'jet-engine' ),
				],
				'default'  => 'top',
				'required' => [ 'arrows', '=', true ],
			]
		);

		$this->register_jet_control(
			'prev_top_position',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Top Indent', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'top',
						'selector' => $css_scheme['prev-arrow'],
					],
					[
						'property' => 'bottom',
						'selector' => $css_scheme['prev-arrow'],
						'value'    => 'auto',
					],
				],
				'required' => [
					[ 'arrows', '=', true ],
					[ 'prev_vert_position', '=', 'top' ],
				],
			]
		);

		$this->register_jet_control(
			'prev_bottom_position',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Bottom Indent', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'bottom',
						'selector' => $css_scheme['prev-arrow'],
					],
					[
						'property' => 'top',
						'selector' => $css_scheme['prev-arrow'],
						'value'    => 'auto',
					],
				],
				'required' => [
					[ 'arrows', '=', true ],
					[ 'prev_vert_position', '=', 'bottom' ],
				],
			]
		);

		$this->register_jet_control(
			'prev_hor_position',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Horizontal Position by', 'jet-engine' ),
				'type'     => 'select',
				'options'  => [
					'left'  => esc_html__( 'Left', 'jet-engine' ),
					'right' => esc_html__( 'Right', 'jet-engine' ),
				],
				'default'  => 'left',
				'required' => [ 'arrows', '=', true ],
			]
		);

		$this->register_jet_control(
			'prev_left_position',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Left Indent', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'left',
						'selector' => $css_scheme['prev-arrow'],
					],
					[
						'property' => 'right',
						'selector' => $css_scheme['prev-arrow'],
						'value'    => 'auto',
					],
				],
				'required' => [
					[ 'arrows', '=', true ],
					[ 'prev_hor_position', '=', 'left' ],
				],
			]
		);

		$this->register_jet_control(
			'prev_right_position',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Right Indent', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'right',
						'selector' => $css_scheme['prev-arrow'],
					],
					[
						'property' => 'left',
						'selector' => $css_scheme['prev-arrow'],
						'value'    => 'auto',
					],
				],
				'required' => [
					[ 'arrows', '=', true ],
					[ 'prev_hor_position', '=', 'right' ],
				],
			]
		);

		$this->register_jet_control(
			'next_arrow_position',
			[
				'type'     => 'separator',
				'label'    => esc_html__( 'Next Arrow Position', 'jet-engine' ),
				'required' => [ 'arrows', '=', true ],
			]
		);

		$this->register_jet_control(
			'next_vert_position',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Vertical Position by', 'jet-engine' ),
				'type'     => 'select',
				'options'  => [
					'top'    => esc_html__( 'Top', 'jet-engine' ),
					'bottom' => esc_html__( 'Bottom', 'jet-engine' ),
				],
				'default'  => 'top',
				'required' => [ 'arrows', '=', true ],
			]
		);

		$this->register_jet_control(
			'next_top_position',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Top Indent', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'top',
						'selector' => $css_scheme['next-arrow'],
					],
					[
						'property' => 'bottom',
						'selector' => $css_scheme['next-arrow'],
						'value'    => 'auto',
					],
				],
				'required' => [
					[ 'arrows', '=', true ],
					[ 'next_vert_position', '=', 'top' ],
				],
			]
		);

		$this->register_jet_control(
			'next_bottom_position',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Bottom Indent', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'bottom',
						'selector' => $css_scheme['next-arrow'],
					],
					[
						'property' => 'top',
						'selector' => $css_scheme['next-arrow'],
						'value'    => 'auto',
					],
				],
				'required' => [
					[ 'arrows', '=', true ],
					[ 'next_vert_position', '=', 'bottom' ],
				],
			]
		);

		$this->register_jet_control(
			'next_hor_position',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Horizontal Position by', 'jet-engine' ),
				'type'     => 'select',
				'options'  => [
					'left'  => esc_html__( 'Left', 'jet-engine' ),
					'right' => esc_html__( 'Right', 'jet-engine' ),
				],
				'default'  => 'right',
				'required' => [ 'arrows', '=', true ],
			]
		);

		$this->register_jet_control(
			'next_left_position',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Left Indent', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'left',
						'selector' => $css_scheme['next-arrow'],
					],
					[
						'property' => 'right',
						'selector' => $css_scheme['next-arrow'],
						'value'    => 'auto',
					],
				],
				'required' => [
					[ 'arrows', '=', true ],
					[ 'next_hor_position', '=', 'left' ],
				],
			]
		);

		$this->register_jet_control(
			'next_right_position',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Right Indent', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'right',
						'selector' => $css_scheme['next-arrow'],
					],
					[
						'property' => 'left',
						'selector' => $css_scheme['next-arrow'],
						'value'    => 'auto',
					],
				],
				'required' => [
					[ 'arrows', '=', true ],
					[ 'next_hor_position', '=', 'right' ],
				],
			]
		);

		$this->register_jet_control(
			'dots_styles',
			[
				'type'     => 'separator',
				'label'    => esc_html__( 'Dots Styles', 'jet-engine' ),
				'required' => [
					[ 'arrows', '=', true ],
					[ 'dots', '=', true ],
				],
			]
		);

		$this->register_jet_control(
			'dots_size',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Dots Size', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'width',
						'selector' => $css_scheme['dot'],
					],
					[
						'property' => 'height',
						'selector' => $css_scheme['dot'],
					],
				],
				'required' => [ 'dots', '=', true ],
			]
		);

		$this->register_jet_control(
			'dots_gap',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Dots Gap', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'gap',
						'selector' => $css_scheme['dots'],
					],
				],
				'required' => [ 'dots', '=', true ],
			]
		);

		$this->register_jet_control(
			'dots_border',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Dots border', 'jet-engine' ),
				'type'     => 'border',
				'css'      => [
					[
						'property' => 'border',
						'selector' => $css_scheme['dot'],
					],
				],
				'required' => [ 'dots', '=', true ],
			]
		);

		$this->register_jet_control(
			'dots_bg_color',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Color', 'jet-engine' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['dot'],
					],
				],
				'required' => [ 'dots', '=', true ],
			]
		);

		$this->register_jet_control(
			'dots_bg_color_active',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Color active', 'jet-engine' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['dot-active'],
					],
				],
				'required' => [ 'dots', '=', true ],
			]
		);


		$this->register_jet_control(
			'dots_border_color_active',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Border color active', 'jet-engine' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'border-color',
						'selector' => $css_scheme['dot-active'],
					],
				],
				'required' => [ 'dots', '=', true ],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_scrollbar_style' );

		$this->register_jet_control(
			'scrollbar_bg',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Scrollbar Color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $this->css_selector( '__scroll-slider::-webkit-scrollbar' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'scrollbar_thumb_bg',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Scrollbar Thumb Color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $this->css_selector( '__scroll-slider::-webkit-scrollbar-thumb' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'scrollbar_height',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Scrollbar Height', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'height',
						'selector' => $this->css_selector( '__scroll-slider::-webkit-scrollbar' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'scrollbar_border_radius',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'border-radius',
						'selector' => $this->css_selector( '__scroll-slider::-webkit-scrollbar' ) . ', ' . $this->css_selector( '__scroll-slider::-webkit-scrollbar-thumb' ),
					],
				],
			]
		);

		$this->end_jet_control_group();

	}

	// Enqueue element styles and scripts
	public function enqueue_scripts() {
		$this->get_jet_render_instance( [ 'inline_columns_css' => true ] )->enqueue_assets( $this->get_jet_settings() );
		wp_enqueue_style( 'jet-engine-frontend' );
	}

	// Render element HTML
	public function render() {

		parent::render();

		$settings = $this->parse_jet_render_attributes( $this->get_jet_settings() );

		$this->set_attribute( '_root', 'class', 'brxe-' . $this->id );
		$this->set_attribute( '_root', 'class', 'brxe-jet-listing-el' );
		$this->set_attribute( '_root', 'class', 'jet-listing-base' );
		$this->set_attribute( '_root', 'data-element-id', $this->id );
		$this->set_attribute( '_root', 'data-listing-type', 'bricks' );

		// STEP: Listing field is empty: Show placeholder text
		if ( empty( $settings['lisitng_id'] ) ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Please select listing to show.', 'jet-engine' )
				]
			);
		}

		$this->enqueue_scripts();

		$render = $this->get_jet_render_instance( [ 'inline_columns_css' => true ] );

		// STEP: Listing renderer class not found: Show placeholder text
		if ( ! $render ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Listing renderer class not found', 'jet-engine' )
				]
			);
		}

		$render->before_listing_grid();

		echo "<div {$this->render_attributes( '_root' )}>";
		jet_engine()->bricks_views->listing->render_assets( $this->get_jet_settings( 'lisitng_id' ) );
		$render->render_content();
		echo "</div>";

		$render->after_listing_grid();

	}

	public function parse_jet_render_attributes( $attrs = [] ) {

		$attrs['arrows']            = $attrs['arrows'] ?? false;
		$attrs['autoplay']          = $attrs['autoplay'] ?? false;
		$attrs['pause_on_hover']    = $attrs['pause_on_hover'] ?? false;
		$attrs['infinite']          = $attrs['infinite'] ?? false;
		$attrs['not_found_message'] = $attrs['not_found_message'] ?? '';

		return $attrs;
	}

	public function css_selector( $mod = null ) {
		return sprintf( '%1$s%2$s', $this->css_selector, $mod );
	}
}