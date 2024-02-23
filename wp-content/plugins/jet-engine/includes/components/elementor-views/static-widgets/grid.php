<?php
namespace Elementor;

use Elementor\Group_Control_Border;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Ensure required files are included
jet_engine()->elementor_views->include_base_widget();

if ( ! class_exists( 'Elementor\Jet_Listing_Grid_Widget' ) ) {

	class Jet_Listing_Grid_Widget extends \Jet_Listing_Dynamic_Widget {

		public $is_first     = false;
		public $data         = false;
		public $query_vars   = array();

		public function get_name() {
			return 'jet-listing-grid';
		}

		public function get_title() {
			return __( 'Listing Grid', 'jet-engine' );
		}

		public function get_icon() {
			return 'jet-engine-icon-listing-grid';
		}

		public function get_categories() {
			return array( 'jet-listing-elements' );
		}

		public function get_help_url() {
			return 'https://crocoblock.com/knowledge-base/articles/jetengine-listing-functionality-how-to-create-a-new-listing-to-apply-for-the-certain-post-type/?utm_source=jetengine&utm_medium=listing-grid&utm_campaign=need-help';
		}

		public function register_general_settings() {

			$this->start_controls_section(
				'section_general',
				array(
					'label' => __( 'General', 'jet-engine' ),
				)
			);

			$this->add_control(
				'lisitng_id',
				array(
					'label'         => __( 'Listing', 'jet-engine' ),
					'type'          => 'jet-query',
					'query_type'    => 'post',
					'create_button' => array(
						'active'  => true,
						'handler' => 'JetListings',
					),
					'query'         => array(
						'post_type' => jet_engine()->post_type->slug(),
					),
					'prevent_looping' => true,
				)
			);

			$this->add_responsive_control(
				'columns',
				array(
					'label'   => __( 'Columns Number', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
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
					'frontend_available' => true,
					'selectors' => array(
						'{{WRAPPER}} > .elementor-widget-container > .jet-listing-grid > .jet-listing-grid__items' => '--columns: {{VALUE}}',
					),
				)
			);

			$this->add_responsive_control(
				'column_min_width',
				array(
					'label'   => __( 'Column Min Width', 'jet-engine' ),
					'type'        => Controls_Manager::NUMBER,
					'default'     => 240,
					'min'         => 0,
					'max'         => 1600,
					'step'        => 1,
					'condition'   => array(
						'columns' => 'auto',
					),
					'frontend_available' => true,
					'selectors' => array(
						'{{WRAPPER}} > .elementor-widget-container > .jet-listing-grid > .jet-listing-grid__items' => 'display: grid; grid-template-columns: repeat( auto-fill, minmax( {{VALUE}}px, 1fr ) );',
						'{{WRAPPER}} > .elementor-widget-container > .jet-listing-grid > .jet-listing-grid__slider > .jet-listing-grid__items.slick-slider .slick-slide' => 'width: {{VALUE}}px;',
					),
				)
			);

			$this->add_control(
				'is_archive_template',
				array(
					'label'        => __( 'Use as Archive Template', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => '',
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'post_status',
				array(
					'label'       => esc_html__( 'Status', 'jet-engine' ),
					'type'        => Controls_Manager::SELECT2,
					'multiple'    => true,
					'label_block' => true,
					'options' => array(
						'publish' => esc_html__( 'Publish', 'jet-engine' ),
						'future'  => esc_html__( 'Future', 'jet-engine' ),
						'draft'   => esc_html__( 'Draft', 'jet-engine' ),
						'pending' => esc_html__( 'Pending Review', 'jet-engine' ),
						'private' => esc_html__( 'Private', 'jet-engine' ),
						'inherit' => esc_html__( 'Inherit', 'jet-engine' ),
					),
					'default'   => array( 'publish' ),
					'condition' => array(
						'is_archive_template!' => 'yes',
					),
				)
			);

			$this->add_control(
				'use_random_posts_num',
				array(
					'label'        => __( 'Use Random posts number', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array(
						'is_archive_template!' => 'yes',
					),
				)
			);

			$this->add_control(
				'random_posts_num_note',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'Note: the `Posts number` control set min random posts number', 'jet-engine' ),
					'content_classes' => 'elementor-descriptor',
					'condition'       => array(
						'is_archive_template!' => 'yes',
						'use_random_posts_num' => 'yes',
					),
				)
			);

			$this->add_control(
				'posts_num',
				array(
					'label'       => __( 'Posts number', 'jet-engine' ),
					'type'        => Controls_Manager::NUMBER,
					'default'     => 6,
					'min'         => -1,
					'max'         => 1000,
					'step'        => 1,
					'condition'   => array(
						'is_archive_template!' => 'yes',
					),
				)
			);

			$this->add_control(
				'max_posts_num',
				array(
					'label'       => __( 'Max Random Posts number', 'jet-engine' ),
					'type'        => Controls_Manager::NUMBER,
					'default'     => 9,
					'min'         => 1,
					'max'         => 1000,
					'step'        => 1,
					'condition'   => array(
						'is_archive_template!' => 'yes',
						'use_random_posts_num' => 'yes',
					),
				)
			);

			$this->add_control(
				'not_found_message',
				array(
					'label'       => __( 'Not found message', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => __( 'No data was found', 'jet-engine' ),
					'label_block' => true,
					'dynamic'     => array(
						'active' => true,
					),
				)
			);

			$this->add_control(
				'lazy_load',
				array(
					'label'        => __( 'Lazy load', 'jet-engine' ),
					'description'  => __( 'Lazy load the listing for boosts rendering performance.', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'lazy_load_offset',
				array(
					'label'      => __( 'Lazy load offset', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%' ),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 1000,
						),
					),
					'default' => array(
						'unit' => 'px',
						'size' => 0,
					),
					'condition' => array(
						'lazy_load' => 'yes',
					),
				)
			);

			$this->add_control(
				'is_masonry',
				array(
					'label'        => __( 'Is masonry grid', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array(
						'columns!' => 'auto',
					),
				)
			);

			$this->add_control(
				'equal_columns_height',
				array(
					'label'        => __( 'Equal columns height', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => __( 'Fits only top level sections of grid item', 'jet-engine' ),
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array(
						'is_masonry!' => 'yes',
					),
				)
			);

			$this->add_control(
				'use_load_more',
				array(
					'label'        => __( 'Load more', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'load_more_type',
				array(
					'label'   => __( 'Load more type', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'click',
					'options' => array(
						'click'  => __( 'By Click', 'jet-engine' ),
						'scroll' => __( 'Infinite Scroll', 'jet-engine' ),
					),
					'condition' => array(
						'use_load_more'  => 'yes',
					),
				)
			);

			$this->add_control(
				'load_more_id',
				array(
					'label'       => __( 'Load more element ID', 'jet-engine' ),
					'description' => __( 'Please, make sure to add a Button widget that will be used as "Load more" button', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
					'condition'   => array(
						'use_load_more'  => 'yes',
						'load_more_type' => 'click',
					),
					'dynamic' => array(
						'active' => true,
					),
				)
			);

			$this->add_control(
				'load_more_offset',
				array(
					'label'      => __( 'Load more offset', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%' ),
					'range' => array(
						'px' => array(
							'min' => -1000,
							'max' => 1000,
						),
						'%' => array(
							'min' => -100,
							'max' => 100,
						),
					),
					'default' => array(
						'unit' => 'px',
						'size' => 0,
					),
					'condition' => array(
						'use_load_more'  => 'yes',
						'load_more_type' => 'scroll',
					),
				)
			);

			$this->add_control(
				'loader_text',
				array(
					'label'     => __( 'Loader text', 'jet-engine' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => '',
					'condition' => array(
						'use_load_more' => 'yes',
					),
				)
			);

			$this->add_control(
				'loader_spinner',
				array(
					'label'     => __( 'Loader spinner', 'jet-engine' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_on'  => __( 'Show', 'jet-engine' ),
					'label_off' => __( 'Hide', 'jet-engine' ),
					'default'   => '',
					'separator' => 'after',
					'condition' => array(
						'use_load_more' => 'yes',
					),
				)
			);

			if ( ! jet_engine()->listings->legacy->is_disabled() ) {
				$this->add_control(
					'use_custom_post_types',
					array(
						'label'        => __( 'Use Custom Post Types', 'jet-engine' ),
						'type'         => Controls_Manager::SWITCHER,
						'label_on'     => __( 'Yes', 'jet-engine' ),
						'label_off'    => __( 'No', 'jet-engine' ),
						'return_value' => 'yes',
						'default'      => '',
					)
				);

				$this->add_control(
					'custom_post_types',
					array(
						'label'       => esc_html__( 'Post Types', 'jet-engine' ),
						'type'        => Controls_Manager::SELECT2,
						'label_block' => true,
						'multiple'    => true,
						'options'     => jet_engine()->listings->get_post_types_for_options(),
						'condition'   => array(
							'use_custom_post_types' => 'yes',
						),
					)
				);
				
			}

			do_action( 'jet-engine/listing/after-general-settings', $this );

			$this->add_control(
				'legacy_notice',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => jet_engine()->listings->legacy->get_notice(),
				)
			);

			$this->end_controls_section();

		}

		public function register_query_settings() {

			$this->start_controls_section(
				'section_custom_query',
				array(
					'label' => __( 'Custom Query', 'jet-engine' ),
				)
			);

			$this->add_control(
				'custom_query',
				array(
					'label'        => __( 'Use Custom Query', 'jet-engine' ),
					'description'  => __( 'Allow to use custom query from Query Builder as items source', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'custom_query_id',
				array(
					'label'   => __( 'Custom Query', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '',
					'options' => \Jet_Engine\Query_Builder\Manager::instance()->get_queries_for_options(),
					'condition' => array(
						'custom_query' => 'yes',
					),
				)
			);

			$this->end_controls_section();

			if ( jet_engine()->listings->legacy->is_disabled() ) {
				return;
			}

			$this->start_controls_section(
				'section_posts_query',
				array(
					'label' => __( 'Posts Query', 'jet-engine' ),
				)
			);

			$this->add_control(
				'posts_query_notice',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => __( 'Set advanced query parameters', 'jet-engine' ),
				)
			);

			$this->add_control(
				'posts_query_ignored_notice',
				array(
					'type'      => Controls_Manager::RAW_HTML,
					'raw'       => __( 'You select <b>Use as Archive Template</b> option, so other query parameters will be ignored', 'jet-engine' ),
					'condition' => array(
						'is_archive_template' => 'yes',
					),
				)
			);

			$posts_query_repeater = new Repeater();

			$posts_query_types = array(
				''             => __( 'Select...', 'jet-engine' ),
				'posts_params' => __( 'Posts & Author Parameters', 'jet-engine' ),
				'order_offset' => __( 'Order & Offset', 'jet-engine' ),
				'tax_query'    => __( 'Tax Query', 'jet-engine' ),
				'meta_query'   => __( 'Meta Query', 'jet-engine' ),
				'date_query'   => __( 'Date Query', 'jet-engine' ),
			);

			$posts_query_repeater->add_control(
				'type',
				array(
					'label'   => __( 'Type', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '',
					'options' => $posts_query_types,
				)
			);

			$posts_query_repeater->add_control(
				'date_query_column',
				array(
					'label'   => __( 'Column', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '',
					'options' => array(
						'post_date'         => __( 'Post date', 'jet-engine' ),
						'post_date_gmt'     => __( 'Post date GMT', 'jet-engine' ),
						'post_modified'     => __( 'Post modified', 'jet-engine' ),
						'post_modified_gmt' => __( 'Post modified GMT', 'jet-engine' ),
					),
					'condition'   => array(
						'type' => 'date_query'
					),
				)
			);

			$posts_query_repeater->add_control(
				'date_query_after',
				array(
					'label'       => __( 'After', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'description' => __( 'Date to retrieve posts after. Accepts strtotime()-compatible string', 'jet-engine' ),
					'label_block' => true,
					'condition'   => array(
						'type' => 'date_query'
					),
					'dynamic' => array(
						'active' => true,
						'categories' => array(
							\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
							\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
						),

					),
				)
			);

			$posts_query_repeater->add_control(
				'date_query_before',
				array(
					'label'       => __( 'Before', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'description' => __( 'Date to retrieve posts before. Accepts strtotime()-compatible string', 'jet-engine' ),
					'label_block' => true,
					'condition'   => array(
						'type' => 'date_query'
					),
					'dynamic' => array(
						'active' => true,
						'categories' => array(
							\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
							\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
						),
					),
				)
			);

			$posts_query_repeater->add_control(
				'posts_in',
				array(
					'label'       => __( 'Include posts by IDs', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'default'     => '',
					'description' => __( 'Eg. 12, 24, 33', 'jet-engine' ),
					'dynamic'     => array(
						'active' => true,
						'categories' => array(
							\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
							\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
						),
					),
					'condition'   => array(
						'type' => 'posts_params'
					),
				)
			);

			$posts_query_repeater->add_control(
				'posts_not_in',
				array(
					'label'       => __( 'Exclude posts by IDs', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'default'     => '',
					'description' => __( 'Eg. 12, 24, 33. If this is used in the same query as Include posts by IDs, it will be ignored', 'jet-engine' ),
					'dynamic' => array(
						'active' => true,
						'categories' => array(
							\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
							\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
						),
					),
					'condition'   => array(
						'type' => 'posts_params'
					),
				)
			);

			$posts_query_repeater->add_control(
				'posts_parent',
				array(
					'label'       => __( 'Get child of', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'description' => __( 'Eg. 12, 24, 33', 'jet-engine' ),
					'condition'   => array(
						'type' => 'posts_params'
					),
					'dynamic' => array(
						'active' => true,
						'categories' => array(
							\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
							\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
						),
					),
				)
			);

			$posts_query_repeater->add_control(
				'posts_status',
				array(
					'label'   => __( 'Get posts with status', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'publish',
					'options' => array(
						'publish'    => __( 'Publish', 'jet-engine' ),
						'pending'    => __( 'Pending', 'jet-engine' ),
						'draft'      => __( 'Draft', 'jet-engine' ),
						'auto-draft' => __( 'Auto draft', 'jet-engine' ),
						'future'     => __( 'Future', 'jet-engine' ),
						'private'    => __( 'Private', 'jet-engine' ),
						'trash'      => __( 'Trash', 'jet-engine' ),
						'any'        => __( 'Any', 'jet-engine' ),
					),
					'condition'   => array(
						'type' => 'posts_params'
					),
				)
			);

			$posts_query_repeater->add_control(
				'posts_author',
				array(
					'label'   => __( 'Posts by author', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'any',
					'options' => array(
						'any'     => __( 'Any author', 'jet-engine' ),
						'current' => __( 'Current User', 'jet-engine' ),
						'id'      => __( 'Specific Author ID', 'jet-engine' ),
						'queried' => __( 'Queried User', 'jet-engine' ),
					),
					'condition'   => array(
						'type' => 'posts_params'
					),
				)
			);

			$posts_query_repeater->add_control(
				'posts_author_id',
				array(
					'label'       => __( 'Author ID', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'dynamic'     => array(
						'active' => true,
						'categories' => array(
							\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
							\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
						),
					),
					'condition'   => array(
						'type'         => 'posts_params',
						'posts_author' => 'id',
					),
				)
			);

			$posts_query_repeater->add_control(
				'search_query',
				array(
					'label'       => __( 'Search Query', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'dynamic'     => array(
						'active' => true,
						'categories' => array(
							\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
							\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
						),
					),
					'condition'   => array(
						'type' => 'posts_params',
					),
				)
			);

			$posts_query_repeater->add_control(
				'offset',
				array(
					'label'     => __( 'Posts offset', 'jet-engine' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => '0',
					'min'       => 0,
					'max'       => 100,
					'step'      => 1,
					'condition' => array(
						'type' => 'order_offset'
					),
				)
			);

			$posts_query_repeater->add_control(
				'order',
				array(
					'label'   => __( 'Order', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'DESC',
					'options' => array(
						'ASC'  => __( 'ASC', 'jet-engine' ),
						'DESC' => __( 'DESC', 'jet-engine' ),
					),
					'condition'   => array(
						'type' => 'order_offset'
					),
				)
			);

			$posts_query_repeater->add_control(
				'order_by',
				array(
					'label'   => __( 'Order by', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'date',
					'options' => array(
						'none'          => __( 'None', 'jet-engine' ),
						'ID'            => __( 'ID', 'jet-engine' ),
						'author'        => __( 'Author', 'jet-engine' ),
						'title'         => __( 'Title', 'jet-engine' ),
						'name'          => __( 'Name', 'jet-engine' ),
						'type'          => __( 'Type', 'jet-engine' ),
						'date'          => __( 'Date', 'jet-engine' ),
						'modified'      => __( 'Modified', 'jet-engine' ),
						'parent'        => __( 'Parent', 'jet-engine' ),
						'rand'          => __( 'Random', 'jet-engine' ),
						'comment_count' => __( 'Comment count', 'jet-engine' ),
						'relevance'     => __( 'Relevance', 'jet-engine' ),
						'menu_order'    => __( 'Menu order', 'jet-engine' ),
						'meta_value'    => __( 'Meta value', 'jet-engine' ),
						'meta_clause'   => __( 'Meta clause', 'jet-engine' ),
						'post__in'      => __( 'Preserve post ID order given in the "Include posts by IDs" option', 'jet-engine' ),
					),
					'condition'   => array(
						'type' => 'order_offset'
					),
				)
			);

			$posts_query_repeater->add_control(
				'meta_key',
				array(
					'label'       => __( 'Meta key to order', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
					'description' => __( 'Set meta field name to order by', 'jet-engine' ),
					'condition'   => array(
						'type'     => 'order_offset',
						'order_by' => 'meta_value',
					),
				)
			);

			$posts_query_repeater->add_control(
				'meta_clause_key',
				array(
					'label'       => __( 'Meta clause to order', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
					'description' => __( 'Meta clause name to order by. Clause with this name should be created in Meta Query parameters', 'jet-engine' ),
					'condition'   => array(
						'type'     => 'order_offset',
						'order_by' => 'meta_clause',
					),
				)
			);

			$posts_query_repeater->add_control(
				'meta_type',
				array(
					'label'   => __( 'Meta type', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'CHAR',
					'options' => array(
						'NUMERIC'  => __( 'NUMERIC', 'jet-engine' ),
						'CHAR'     => __( 'CHAR', 'jet-engine' ),
						'DATE'     => __( 'DATE', 'jet-engine' ),
						'DATETIME' => __( 'DATETIME', 'jet-engine' ),
						'DECIMAL'  => __( 'DECIMAL', 'jet-engine' ),
					),
					'condition'   => array(
						'type'     => 'order_offset',
						'order_by' => 'meta_value',
					),
				)
			);

			$posts_query_repeater->add_control(
				'tax_query_taxonomy',
				array(
					'label'   => __( 'Taxonomy', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'options' => jet_engine()->listings->get_taxonomies_for_options(),
					'default' => '',
					'condition' => array(
						'type' => 'tax_query'
					),
				)
			);

			$posts_query_repeater->add_control(
				'tax_query_taxonomy_meta',
				array(
					'label'       => __( 'Taxonomy from meta field', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
					'description' => __( 'Get taxonomy name from current page meta field', 'jet-engine' ),
					'condition'   => array(
						'type' => 'tax_query'
					),
				)
			);


			$posts_query_repeater->add_control(
				'tax_query_compare',
				array(
					'label'   => __( 'Operator', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'options' => array(
						'IN'         => __( 'IN', 'jet-engine' ),
						'NOT IN'     => __( 'NOT IN', 'jet-engine' ),
						'AND'        => __( 'AND', 'jet-engine' ),
						'EXISTS'     => __( 'EXISTS', 'jet-engine' ),
						'NOT EXISTS' => __( 'NOT EXISTS', 'jet-engine' ),
					),
					'default' => 'IN',
					'condition' => array(
						'type' => 'tax_query'
					),
				)
			);

			$posts_query_repeater->add_control(
				'tax_query_field',
				array(
					'label'   => __( 'Field', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'options' => array(
						'term_id' => __( 'Term ID', 'jet-engine' ),
						'slug'    => __( 'Slug', 'jet-engine' ),
						'name'    => __( 'Name', 'jet-engine' ),
					),
					'default' => 'term_id',
					'condition' => array(
						'type' => 'tax_query'
					),
				)
			);

			$posts_query_repeater->add_control(
				'tax_query_terms',
				array(
					'label'       => __( 'Terms', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
					'condition'   => array(
						'type' => 'tax_query'
					),
				)
			);

			$posts_query_repeater->add_control(
				'tax_query_terms_meta',
				array(
					'label'       => __( 'Terms from meta field', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
					'description' => __( 'Get terms IDs from current page meta field', 'jet-engine' ),
					'condition'   => array(
						'type' => 'tax_query'
					),
				)
			);

			do_action( 'jet-engine/listing/after-tax-fields', $posts_query_repeater, $this );

			$posts_query_repeater->add_control(
				'meta_query_key',
				array(
					'label'   => __( 'Key (name/ID)', 'jet-engine' ),
					'type'    => Controls_Manager::TEXT,
					'default' => '',
					'condition' => array(
						'type' => 'meta_query'
					),
				)
			);

			$posts_query_repeater->add_control(
				'meta_query_compare',
				array(
					'label'   => __( 'Operator', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '=',
					'options' => array(
						'='           => __( 'Equal', 'jet-engine' ),
						'!='          => __( 'Not equal', 'jet-engine' ),
						'>'           => __( 'Greater than', 'jet-engine' ),
						'>='          => __( 'Greater or equal', 'jet-engine' ),
						'<'           => __( 'Less than', 'jet-engine' ),
						'<='          => __( 'Equal or less', 'jet-engine' ),
						'LIKE'        => __( 'Like', 'jet-engine' ),
						'NOT LIKE'    => __( 'Not like', 'jet-engine' ),
						'IN'          => __( 'In', 'jet-engine' ),
						'NOT IN'      => __( 'Not in', 'jet-engine' ),
						'BETWEEN'     => __( 'Between', 'jet-engine' ),
						'NOT BETWEEN' => __( 'Not between', 'jet-engine' ),
						'EXISTS'      => __( 'Exists', 'jet-engine' ),
						'NOT EXISTS'  => __( 'Not Exists', 'jet-engine' ),
						'REGEXP'      => __( 'Regexp', 'jet-engine' ),
						'NOT REGEXP'  => __( 'Not Regexp', 'jet-engine' ),
					),
					'condition'   => array(
						'type' => 'meta_query',
					),
				)
			);

			$posts_query_repeater->add_control(
				'meta_query_val',
				array(
					'label'       => __( 'Value', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
					'description' => __( 'For <b>In</b>, <b>Not in</b>, <b>Between</b> and <b>Not between</b> compare separate multiple values with comma', 'jet-engine' ),
					'condition'   => array(
						'type' => 'meta_query',
						'meta_query_compare!' => array( 'EXISTS', 'NOT EXISTS' ),
					),
					'dynamic'     => array(
						'active' => true,
						'categories' => array(
							\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
							\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
						),
					),
				)
			);

			$posts_query_repeater->add_control(
				'meta_query_request_val',
				array(
					'label'       => __( 'Or get value from query variable', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
					'description' => __( 'Set query variable name (from URL or WordPress query var) to get value from', 'jet-engine' ),
					'condition'   => array(
						'type' => 'meta_query',
						'meta_query_compare!' => array( 'EXISTS', 'NOT EXISTS' ),
					),
				)
			);

			$posts_query_repeater->add_control(
				'meta_query_type',
				array(
					'label'   => __( 'Type', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'CHAR',
					'options' => $this->meta_types(),
					'condition'   => array(
						'type' => 'meta_query',
					),
				)
			);

			$posts_query_repeater->add_control(
				'meta_query_clause',
				array(
					'label'       => __( 'Meta Query Clause', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
					'description' => __( 'Set unique name for current query clause to use it to order posts by this clause', 'jet-engine' ),
					'condition'   => array(
						'type' => 'meta_query',
					),
				)
			);

			do_action( 'jet-engine/listing/after-posts-query-fields', $posts_query_repeater, $this );

			$this->add_control(
				'posts_query',
				array(
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $posts_query_repeater->get_controls(),
					'default'     => array(),
					'title_field' => '<# var posts_query_types=' . json_encode( $posts_query_types ) . ';#> {{{ posts_query_types[type] }}}',
				)
			);

			$this->add_control(
				'meta_query_relation',
				array(
					'label'   => __( 'Meta query relation', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'AND',
					'options' => array(
						'AND' => __( 'AND', 'jet-engine' ),
						'OR'  => __( 'OR', 'jet-engine' ),
					),
				)
			);

			$this->add_control(
				'tax_query_relation',
				array(
					'label'   => __( 'Tax query relation', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'AND',
					'options' => array(
						'AND' => __( 'AND', 'jet-engine' ),
						'OR'  => __( 'OR', 'jet-engine' ),
					),
				)
			);

			$this->end_controls_section();

		}

		/**
		 * Terms query settings
		 * @return [type] [description]
		 */
		public function register_terms_query_settings() {

			if ( jet_engine()->listings->legacy->is_disabled() ) {
				return;
			}

			$this->start_controls_section(
				'section_terms_query',
				array(
					'label' => __( 'Terms Query', 'jet-engine' ),
				)
			);

			$this->add_control(
				'terms_query_notice',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => __( 'Set advanced query parameters', 'jet-engine' ),

				)
			);

			$this->add_control(
				'terms_query_ignored_notice',
				array(
					'type'      => Controls_Manager::RAW_HTML,
					'raw'       => __( 'You select <b>Use as Archive Template</b> option, so other query parameters will be ignored', 'jet-engine' ),
					'condition' => array(
						'is_archive_template' => 'yes',
					),
				)
			);

			$this->add_control(
				'terms_object_ids',
				array(
					'label'       => __( 'Get terms of posts', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
				)
			);

			$this->add_control(
				'terms_orderby',
				array(
					'label'   => __( 'Order By', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'name',
					'options' => array(
						'name'        => __( 'Name', 'jet-engine' ),
						'slug'        => __( 'Slug', 'jet-engine' ),
						'term_group'  => __( 'Term Group', 'jet-engine' ),
						'term_id'     => __( 'Term ID', 'jet-engine' ),
						'description' => __( 'Description', 'jet-engine' ),
						'parent'      => __( 'Parent', 'jet-engine' ),
						'count'       => __( 'Count', 'jet-engine' ),
						'include'     => __( 'Include', 'jet-engine' ),
						'none'        => __( 'None', 'jet-engine' ),
					),
				)
			);

			$this->add_control(
				'terms_order',
				array(
					'label'   => __( 'Order', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'DESC',
					'options' => array(
						'ASC'  => __( 'ASC', 'jet-engine' ),
						'DESC' => __( 'DESC', 'jet-engine' ),
					),
				)
			);

			$this->add_control(
				'terms_hide_empty',
				array(
					'label'        => __( 'Hide empty', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => '',
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'true',
					'default'      => 'true',
				)
			);

			$this->add_control(
				'terms_include',
				array(
					'label'       => __( 'Include terms', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
					'description' => __( 'Comma/space-separated string of term ids to include', 'jet-engine' ),
				)
			);

			$this->add_control(
				'terms_exclude',
				array(
					'label'       => __( 'Exclude terms', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
					'description' => __( 'Comma/space-separated string of term ids to exclude. Ignore if <b>Include terms</b> not empty', 'jet-engine' ),
				)
			);

			$this->add_control(
				'terms_offset',
				array(
					'label'     => __( 'Offset', 'jet-engine' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => '0',
					'min'       => 0,
					'max'       => 100,
					'step'      => 1,
				)
			);

			$this->add_control(
				'terms_parent',
				array(
					'label'       => __( 'Parent', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'description' => __( 'Term ID to retrieve only direct descendants. Set 0 to show only the top-level terms', 'jet-engine' ),
				)
			);

			$this->add_control(
				'terms_child_of',
				array(
					'label'       => __( 'Child of', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'description' => __( 'Term ID to retrieve child terms of', 'jet-engine' ),
				)
			);

			$this->add_control(
				'terms_meta_query_heading',
				array(
					'label'     => __( 'Meta Query', 'jet-engine' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$terms_meta_query = new Repeater();

			$terms_meta_query->add_control(
				'meta_query_key',
				array(
					'label'   => __( 'Key (name/ID)', 'jet-engine' ),
					'type'    => Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$terms_meta_query->add_control(
				'meta_query_compare',
				array(
					'label'   => __( 'Operator', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '=',
					'options' => array(
						'='           => __( 'Equal', 'jet-engine' ),
						'!='          => __( 'Not equal', 'jet-engine' ),
						'>'           => __( 'Greater than', 'jet-engine' ),
						'>='          => __( 'Greater or equal', 'jet-engine' ),
						'<'           => __( 'Less than', 'jet-engine' ),
						'<='          => __( 'Equal or less', 'jet-engine' ),
						'LIKE'        => __( 'Like', 'jet-engine' ),
						'NOT LIKE'    => __( 'Not like', 'jet-engine' ),
						'IN'          => __( 'In', 'jet-engine' ),
						'NOT IN'      => __( 'Not in', 'jet-engine' ),
						'BETWEEN'     => __( 'Between', 'jet-engine' ),
						'NOT BETWEEN' => __( 'Not between', 'jet-engine' ),
						'EXISTS'      => __( 'Exists', 'jet-engine' ),
						'NOT EXISTS'  => __( 'Not Exists', 'jet-engine' ),
					),
				)
			);

			$terms_meta_query->add_control(
				'meta_query_val',
				array(
					'label'       => __( 'Value', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
					'description' => __( 'For <b>In</b>, <b>Not in</b>, <b>Between</b> and <b>Not between</b> compare separate multiple values with comma', 'jet-engine' ),
				)
			);

			$terms_meta_query->add_control(
				'meta_query_type',
				array(
					'label'   => __( 'Type', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'CHAR',
					'options' => $this->meta_types(),
				)
			);

			$this->add_control(
				'terms_meta_query',
				array(
					'type'    => Controls_Manager::REPEATER,
					'fields'  => $terms_meta_query->get_controls(),
					'default' => array(),
					'title_field' => '{{{ meta_query_key }}}',
				)
			);

			$this->add_control(
				'term_meta_query_relation',
				array(
					'label'   => __( 'Meta query relation', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'AND',
					'options' => array(
						'AND' => __( 'AND', 'jet-engine' ),
						'OR'  => __( 'OR', 'jet-engine' ),
					),
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_users_query',
				array(
					'label' => __( 'Users Query', 'jet-engine' ),
				)
			);

			$this->add_control(
				'users_role__in',
				array(
					'label'       => esc_html__( 'Roles', 'jet-engine' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => $this->get_user_roles(),
					'default'     => array(),
				)
			);

			$this->add_control(
				'users_role__not_in',
				array(
					'label'       => esc_html__( 'Exclude roles', 'jet-engine' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => $this->get_user_roles(),
					'default'     => array(),
				)
			);

			$this->add_control(
				'users_include',
				array(
					'label'       => __( 'Include users by ID', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'description' => __( 'Comma-separated ID\'s list', 'jet-engine' ),
					'label_block' => true,
					'dynamic'     => array(
						'active' => true,
						'categories' => array(
							\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
							\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
						),
					),
				)
			);

			$this->add_control(
				'users_exclude',
				array(
					'label'       => __( 'Exclude users by ID', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'description' => __( 'Comma-separated ID\'s list', 'jet-engine' ),
					'label_block' => true,
					'dynamic'     => array(
						'active' => true,
						'categories' => array(
							\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
							\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
						),
					),
				)
			);

			$this->add_control(
				'users_search_query',
				array(
					'label'       => __( 'Search Query', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'default'     => '',
					'dynamic'     => array(
						'active' => true,
						'categories' => array(
							\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
							\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
						),
					),
				)
			);

			$this->add_control(
				'users_search_columns',
				array(
					'label'       => __( 'Search Columns', 'jet-engine' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'description' => __( 'Select users DB columns to search by', 'jet-engine' ),
					'multiple'    => true,
					'options'     => array(
						'ID'            => __( 'User id', 'jet-engine' ),
						'user_login'    => __( 'Login', 'jet-engine' ),
						'user_nicename' => __( 'Nicename', 'jet-engine' ),
						'user_email'    => __( 'Email', 'jet-engine' ),
						'user_url'      => __( 'User url', 'jet-engine' ),
					),
					'default'     => '',
					'condition'   => array(
						'users_search_query!' => '',
					),
				)
			);

			$this->add_control(
				'users_meta_query_heading',
				array(
					'label'     => __( 'Meta Query', 'jet-engine' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$users_meta_query = new Repeater();

			$users_meta_query->add_control(
				'meta_query_key',
				array(
					'label'   => __( 'Key (name/ID)', 'jet-engine' ),
					'type'    => Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$users_meta_query->add_control(
				'meta_query_compare',
				array(
					'label'   => __( 'Operator', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '=',
					'options' => array(
						'='           => __( 'Equal', 'jet-engine' ),
						'!='          => __( 'Not equal', 'jet-engine' ),
						'>'           => __( 'Greater than', 'jet-engine' ),
						'>='          => __( 'Greater or equal', 'jet-engine' ),
						'<'           => __( 'Less than', 'jet-engine' ),
						'<='          => __( 'Equal or less', 'jet-engine' ),
						'LIKE'        => __( 'Like', 'jet-engine' ),
						'NOT LIKE'    => __( 'Not like', 'jet-engine' ),
						'IN'          => __( 'In', 'jet-engine' ),
						'NOT IN'      => __( 'Not in', 'jet-engine' ),
						'BETWEEN'     => __( 'Between', 'jet-engine' ),
						'NOT BETWEEN' => __( 'Not between', 'jet-engine' ),
						'EXISTS'      => __( 'Exists', 'jet-engine' ),
						'NOT EXISTS'  => __( 'Not Exists', 'jet-engine' ),
					),
				)
			);

			$users_meta_query->add_control(
				'meta_query_val',
				array(
					'label'       => __( 'Value', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
					'description' => __( 'For <b>In</b>, <b>Not in</b>, <b>Between</b> and <b>Not between</b> compare separate multiple values with comma', 'jet-engine' ),
					'condition'   => array(
						'meta_query_compare!' => array( 'EXISTS', 'NOT EXISTS' ),
					),
				)
			);

			$users_meta_query->add_control(
				'meta_query_type',
				array(
					'label'     => __( 'Type', 'jet-engine' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'CHAR',
					'options'   => $this->meta_types(),
					'condition' => array(
						'meta_query_compare!' => array( 'EXISTS', 'NOT EXISTS' ),
					),
				)
			);

			$this->add_control(
				'users_meta_query',
				array(
					'type'    => Controls_Manager::REPEATER,
					'fields'  => $users_meta_query->get_controls(),
					'default' => array(),
					'title_field' => '{{{ meta_query_key }}}',
				)
			);

			$this->add_control(
				'users_meta_query_relation',
				array(
					'label'   => __( 'Meta query relation', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'AND',
					'options' => array(
						'AND' => __( 'AND', 'jet-engine' ),
						'OR'  => __( 'OR', 'jet-engine' ),
					),
				)
			);

			$this->end_controls_section();

		}

		public function register_repeater_query_settings() {

			$this->start_controls_section(
				'section_repeater_query',
				array(
					'label' => __( 'Repeater Items Query', 'jet-engine' ),
				)
			);

			$this->add_control(
				'repeater_items_query',
				array(
					'label'   => __( 'Repeater Items Query', 'jet-engine' ),
					'type'    => Controls_Manager::TEXTAREA,
					'default' => '',
					'description' => __( 'Set one query condition per line. Condition format - <b>field_name=value</b>, where "field_name" is repeater field name to query items by, "=" is a conditional operator and "value" - is value to compare. Available operators - =, !=, >, >=, <, <=. Reserved words for values: null, false, true', 'jet-engine' ),
				)
			);

			$this->end_controls_section();

		}

		public function register_visibility_settings() {

			$this->start_controls_section(
				'section_widget_visibility',
				array(
					'label' => __( 'Widget Visibility', 'jet-engine' ),
				)
			);

			$this->add_control(
				'hide_widget_if',
				array(
					'label'   => __( 'Hide widget if', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '',
					'options' => jet_engine()->listings->get_widget_hide_options(),
				)
			);

			$this->end_controls_section();

		}

		/**
		 * Register style settings
		 * @return [type] [description]
		 */
		public function register_style_settings() {

			$this->start_controls_section(
				'section_caption_style',
				array(
					'label'      => __( 'Columns', 'jet-engine' ),
					'tab'        => Controls_Manager::TAB_STYLE,
					'show_label' => false,
				)
			);

			$this->add_responsive_control(
				'horizontal_gap',
				array(
					'label' => __( 'Horizontal Gap', 'jet-engine' ),
					'type'  => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array( 'px', 'em', 'rem' ),
					'selectors' => array(
						':is( {{WRAPPER}} > .elementor-widget-container > .jet-listing-grid > .jet-listing-grid__items, {{WRAPPER}} > .elementor-widget-container > .jet-listing-grid > .jet-listing-grid__slider > .jet-listing-grid__items > .slick-list > .slick-track, {{WRAPPER}} > .elementor-widget-container > .jet-listing-grid > .jet-listing-grid__scroll-slider > .jet-listing-grid__items ) > .jet-listing-grid__item' => 'padding-left: calc({{SIZE}}{{UNIT}} / 2); padding-right: calc({{SIZE}}{{UNIT}} / 2);',
						':is( {{WRAPPER}} > .elementor-widget-container > .jet-listing-grid, {{WRAPPER}} > .elementor-widget-container > .jet-listing-grid > .jet-listing-grid__slider, {{WRAPPER}} > .elementor-widget-container > .jet-listing-grid > .jet-listing-grid__scroll-slider ) > .jet-listing-grid__items' => 'margin-left: calc(-{{SIZE}}{{UNIT}} / 2); margin-right: calc(-{{SIZE}}{{UNIT}} / 2); width: calc(100% + {{SIZE}}{{UNIT}});',
					),
				)
			);

			$this->add_responsive_control(
				'vertical_gap',
				array(
					'label' => __( 'Vertical Gap', 'jet-engine' ),
					'type'  => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array( 'px', 'em', 'rem' ),
					'selectors' => array(
						':is( {{WRAPPER}} > .elementor-widget-container > .jet-listing-grid > .jet-listing-grid__items, {{WRAPPER}} > .elementor-widget-container > .jet-listing-grid > .jet-listing-grid__slider > .jet-listing-grid__items > .slick-list > .slick-track, {{WRAPPER}} > .elementor-widget-container > .jet-listing-grid > .jet-listing-grid__scroll-slider > .jet-listing-grid__items ) > .jet-listing-grid__item' => 'padding-top: calc({{SIZE}}{{UNIT}} / 2); padding-bottom: calc({{SIZE}}{{UNIT}} / 2);',
					),
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_loader_style',
				array(
					'label'      => esc_html__( 'Loader', 'jet-engine' ),
					'tab'        => Controls_Manager::TAB_STYLE,
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'lazy_load',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'use_load_more',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_control(
				'loader_color',
				array(
					'label'     => esc_html__( 'Spinner Color', 'jet-engine' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-listing-grid__loader' => '--spinner-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'loader_size',
				array(
					'label'      => esc_html__( 'Spinner Size', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range'      => array(
						'px' => array(
							'min' => 10,
							'max' => 100,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .jet-listing-grid__loader' => '--spinner-size: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'loader_text_color',
				array(
					'label'     => esc_html__( 'Text Color', 'jet-engine' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-listing-grid__loader-text' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'use_load_more' => 'yes',
						'loader_text!'  => '',
					),
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'loader_text_typography',
					'selector'  => '{{WRAPPER}} .jet-listing-grid__loader-text',
					'condition' => array(
						'use_load_more' => 'yes',
						'loader_text!'  => '',
					),
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_not_found_style',
				array(
					'label' => __( 'Not Found Message', 'jet-engine' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'not_found_typography',
					'selector' => '{{WRAPPER}} .jet-listing-not-found',
				)
			);

			$this->add_control(
				'not_found_color',
				array(
					'label' => esc_html__( 'Color', 'jet-engine' ),
					'type'  => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-listing-not-found' => 'color: {{VALUE}};',
					),
				)
			);
			
			$this->end_controls_section();

		}

		/**
		 * Register style settings
		 * @return [type] [description]
		 */
		public function register_carousel_settings() {

			$this->start_controls_section(
				'section_carousel',
				array(
					'label' => __( 'Slider', 'jet-engine' ),
				)
			);

			$this->add_control(
				'masonry_notice',
				array(
					'type'      => Controls_Manager::RAW_HTML,
					'raw'       => __( 'Slider settings are disabled for masonry layout', 'jet-engine' ),
					'condition' => array(
						'is_masonry' => 'yes',
					),
				)
			);

			$this->add_control(
				'carousel_enabled',
				array(
					'label'        => __( 'Enable Slider', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array(
						'is_masonry!' => 'yes',
						'scroll_slider_enabled!' => 'yes',
					),
				)
			);

			$this->add_control(
				'slides_to_scroll',
				array(
					'label'     => __( 'Slides to Scroll', 'jet-engine' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '1',
					'options'   => array(
						1 => 1,
						2 => 2,
						3 => 3,
						4 => 4,
					),
					'condition' => array(
						'columns!' => '1',
						'is_masonry!' => 'yes',
						'carousel_enabled' => 'yes',
					),
				)
			);

			$this->add_control(
				'arrows',
				array(
					'label'        => __( 'Show Arrows Navigation', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'true',
					'default'      => 'true',
					'condition'    => array(
						'is_masonry!' => 'yes',
						'carousel_enabled' => 'yes',
					),
				)
			);

			$this->add_control(
				'arrow_icon',
				array(
					'label'   => __( 'Arrow Icon', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'fa fa-angle-left',
					'options' => apply_filters( 'jet-engine/listing/grid/arrow-icons/options', array(
						'fa fa-angle-left'          => __( 'Angle', 'jet-engine' ),
						'fa fa-chevron-left'        => __( 'Chevron', 'jet-engine' ),
						'fa fa-angle-double-left'   => __( 'Angle Double', 'jet-engine' ),
						'fa fa-arrow-left'          => __( 'Arrow', 'jet-engine' ),
						'fa fa-caret-left'          => __( 'Caret', 'jet-engine' ),
						'fa fa-long-arrow-left'     => __( 'Long Arrow', 'jet-engine' ),
						'fa fa-arrow-circle-left'   => __( 'Arrow Circle', 'jet-engine' ),
						'fa fa-chevron-circle-left' => __( 'Chevron Circle', 'jet-engine' ),
						'fa fa-caret-square-o-left' => __( 'Caret Square', 'jet-engine' ),
					) ),
					'condition' => array(
						'arrows' => 'true',
						'is_masonry!' => 'yes',
						'carousel_enabled' => 'yes',
					),
				)
			);

			$this->add_control(
				'dots',
				array(
					'label'        => __( 'Show Dots Navigation', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'true',
					'default'      => '',
					'condition'    => array(
						'is_masonry!' => 'yes',
						'carousel_enabled' => 'yes',
					),
				)
			);

			$this->add_control(
				'autoplay',
				array(
					'label'        => __( 'Autoplay', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'true',
					'default'      => 'true',
					'condition'    => array(
						'is_masonry!' => 'yes',
						'carousel_enabled' => 'yes',
					),
				)
			);

			$this->add_control(
				'autoplay_speed',
				array(
					'label'     => __( 'Autoplay Speed', 'jet-engine' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 5000,
					'condition' => array(
						'autoplay' => 'true',
						'is_masonry!' => 'yes',
						'carousel_enabled' => 'yes',
					),
				)
			);

			$this->add_control(
				'pause_on_hover',
				array(
					'label'        => __( 'Pause On Hover', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'true',
					'default'      => 'true',
					'condition'    => array(
						'autoplay' => 'true',
						'is_masonry!' => 'yes',
						'carousel_enabled' => 'yes',
					),
				)
			);

			$this->add_control(
				'infinite',
				array(
					'label'        => __( 'Infinite Loop', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'true',
					'default'      => 'true',
					'condition'    => array(
						'is_masonry!' => 'yes',
						'carousel_enabled' => 'yes',
					),
				)
			);

			$this->add_control(
				'center_mode',
				array(
					'label'        => __( 'Center Mode', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'true',
					'default'      => '',
					'condition'    => array(
						'is_masonry!' => 'yes',
						'carousel_enabled' => 'yes',
					),
				)
			);

			$this->add_control(
				'effect',
				array(
					'label'   => __( 'Effect', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'slide',
					'options' => array(
						'slide' => __( 'Slide', 'jet-engine' ),
						'fade'  => __( 'Fade', 'jet-engine' ),
					),
					'condition' => array(
						'columns' => '1',
						'is_masonry!' => 'yes',
						'carousel_enabled' => 'yes',
					),
				)
			);

			$this->add_control(
				'speed',
				array(
					'label'     => __( 'Animation Speed', 'jet-engine' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 500,
					'condition' => array(
						'is_masonry!' => 'yes',
						'carousel_enabled' => 'yes',
					),
				)
			);

			$this->add_control(
				'scroll_slider_enabled',
				array(
					'label'        => __( 'Enable Scroll Slider', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array(
						'is_masonry!' => 'yes',
						'carousel_enabled!' => 'yes',
					),
				)
			);

			if ( Plugin::$instance->breakpoints && method_exists( Plugin::$instance->breakpoints, 'get_active_devices_list' ) ) {
				$active_devices     = array_reverse( Plugin::$instance->breakpoints->get_active_devices_list() );
				$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();

				$devices_list = array();

				foreach ( $active_devices as $breakpoint_key ) {
					$devices_list[ $breakpoint_key ] = 'desktop' === $breakpoint_key ? __( 'Desktop', 'jet-engine' ) : $active_breakpoints[ $breakpoint_key ]->get_label();
				}

				unset( $devices_list['widescreen'] );
			} else {
				$devices_list = array(
					'desktop' => __( 'Desktop', 'jet-engine' ),
					'tablet'  => __( 'Tablet', 'jet-engine' ),
					'mobile'  => __( 'Mobile', 'jet-engine' ),
				);
			}

			$this->add_control(
				'scroll_slider_on',
				array(
					'label'       => __( 'Scroll Slider On', 'jet-engine' ),
					'label_block' => true,
					'type'        => Controls_Manager::SELECT2,
					'multiple'    => true,
					'default'     => array( 'desktop', 'tablet', 'mobile' ),
					'options'     => $devices_list,
					'condition'   => array(
						'scroll_slider_enabled' => 'yes',
						'is_masonry!' => 'yes',
						'carousel_enabled!' => 'yes',
					),
				)
			);

			foreach ( $devices_list as $device_key => $device_label ) {

				$suffix = 'desktop' !== $device_key ? '_' . $device_key : '';

				$media_selector  = '(' . $device_key . '+)';

				if ( 'desktop' !== $device_key ) {
					$media_selector .= '(' . $device_key . ')';
				}

				$this->add_control(
					'static_column_width' . $suffix,
					array(
						'label'      => sprintf( esc_html__( 'Static column width on %s', 'jet-engine' ), $device_label ),
						'type'       => Controls_Manager::SLIDER,
						'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'vw' ) ),
						'range' => array(
							'px' => array(
								'min' => 0,
								'max' => 600,
							),
						),
						'selectors' => array(
							$media_selector . '{{WRAPPER}} .jet-listing-grid__scroll-slider-' . $device_key . ' > .jet-listing-grid__items > .jet-listing-grid__item' => 'flex: 0 0 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
						),
						'conditions' => array(
							'terms' => array(
								array(
									'name'  => 'scroll_slider_enabled',
									'value' => 'yes',
								),
								array(
									'name'     => 'is_masonry',
									'operator' => '!==',
									'value'    => 'yes',
								),
								array(
									'name'     => 'carousel_enabled',
									'operator' => '!==',
									'value'    => 'yes',
								),
								array(
									'name'     => 'scroll_slider_on',
									'operator' => 'contains',
									'value'    => $device_key,
								),
							),
						),
					)
				);
			}

			$this->end_controls_section();

		}

		/**
		 * Register carousel styles settings
		 *
		 * @return [type] [description]
		 */
		public function register_carousel_style_settings() {

			$this->start_controls_section(
				'section_slider_style',
				array(
					'label'     => __( 'Slider', 'jet-engine' ),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => array(
						'carousel_enabled' => 'yes',
						'is_masonry!' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'center_mode_padding',
				array(
					'label'      => __( 'Center Mode Padding', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 200,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-listing-grid__slider > .jet-listing-grid__items > .slick-list' => 'padding: 0 {{SIZE}}{{UNIT}} !important;',
					),
					'condition' => array(
						'center_mode' => 'true',
					),
				)
			);

			$this->add_responsive_control(
				'arrows_box_size',
				array(
					'label'      => __( 'Slider arrows box size', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range'      => array(
						'px' => array(
							'min' => 16,
							'max' => 120,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-listing-grid__slider-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; margin-top: calc( -{{SIZE}}{{UNIT}}/2 );',
					),
				)
			);

			$this->add_responsive_control(
				'arrows_size',
				array(
					'label'      => __( 'Slider arrows size', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range'      => array(
						'px' => array(
							'min' => 10,
							'max' => 50,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-listing-grid__slider-icon' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .jet-listing-grid__slider-icon svg' => 'height: {{SIZE}}{{UNIT}};',
					),
				)
			);
			
			$this->add_control(
				'arrow_z_index',
				array(
					'label'     => esc_html__( 'Slider arrows Z-Index', 'jet-engine' ),
					'type'      => Controls_Manager::NUMBER,
					'selectors' => array(
						'{{WRAPPER}} .jet-listing-grid__slider-icon' => 'z-index: {{VALUE}};',
					),
				)
			);

			$this->start_controls_tabs( 'tabs_arrow_style' );

			$this->start_controls_tab(
				'tab_arrow_normal',
				array(
					'label' => __( 'Normal', 'jet-engine' ),
				)
			);

			$this->add_control(
				'arrow_color',
				array(
					'label'     => __( 'Color', 'jet-engine' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-listing-grid__slider-icon' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'arrow_bg_color',
				array(
					'label'     => __( 'Background', 'jet-engine' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-listing-grid__slider-icon' => 'background: {{VALUE}}',
					),
				)
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_arrow_hover',
				array(
					'label' => __( 'Hover', 'jet-engine' ),
				)
			);

			$this->add_control(
				'arrow_color_hover',
				array(
					'label'     => __( 'Color', 'jet-engine' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-listing-grid__slider-icon:hover' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'arrow_bg_color_hover',
				array(
					'label'     => __( 'Background', 'jet-engine' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-listing-grid__slider-icon:hover' => 'background: {{VALUE}}',
					),
				)
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'prev_arrow_position',
				array(
					'label'     => __( 'Prev Arrow Position', 'jet-engine' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'prev_vert_position',
				array(
					'label'   => __( 'Vertical Position by', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'top',
					'options' => array(
						'top'    => __( 'Top', 'jet-engine' ),
						'bottom' => __( 'Bottom', 'jet-engine' ),
					),
				)
			);

			$this->add_responsive_control(
				'prev_top_position',
				array(
					'label'      => __( 'Top Indent', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%', 'em' ),
					'range'      => array(
						'px' => array(
							'min' => -400,
							'max' => 400,
						),
						'%' => array(
							'min' => -100,
							'max' => 100,
						),
						'em' => array(
							'min' => -50,
							'max' => 50,
						),
					),
					'condition' => array(
						'prev_vert_position' => 'top',
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-listing-grid__slider-icon.prev-arrow' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
					),
				)
			);

			$this->add_responsive_control(
				'prev_bottom_position',
				array(
					'label'      => __( 'Bottom Indent', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%', 'em' ),
					'range'      => array(
						'px' => array(
							'min' => -400,
							'max' => 400,
						),
						'%' => array(
							'min' => -100,
							'max' => 100,
						),
						'em' => array(
							'min' => -50,
							'max' => 50,
						),
					),
					'condition' => array(
						'prev_vert_position' => 'bottom',
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-listing-grid__slider-icon.prev-arrow' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
					),
				)
			);

			$this->add_control(
				'prev_hor_position',
				array(
					'label'   => __( 'Horizontal Position by', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'left',
					'options' => array(
						'left'  => __( 'Left', 'jet-engine' ),
						'right' => __( 'Right', 'jet-engine' ),
					),
				)
			);

			$this->add_responsive_control(
				'prev_left_position',
				array(
					'label'      => __( 'Left Indent', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%', 'em' ),
					'range'      => array(
						'px' => array(
							'min' => -400,
							'max' => 400,
						),
						'%' => array(
							'min' => -100,
							'max' => 100,
						),
						'em' => array(
							'min' => -50,
							'max' => 50,
						),
					),
					'condition' => array(
						'prev_hor_position' => 'left',
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-listing-grid__slider-icon.prev-arrow' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
					),
				)
			);

			$this->add_responsive_control(
				'prev_right_position',
				array(
					'label'      => __( 'Right Indent', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%', 'em' ),
					'range'      => array(
						'px' => array(
							'min' => -400,
							'max' => 400,
						),
						'%' => array(
							'min' => -100,
							'max' => 100,
						),
						'em' => array(
							'min' => -50,
							'max' => 50,
						),
					),
					'condition' => array(
						'prev_hor_position' => 'right',
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-listing-grid__slider-icon.prev-arrow' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
					),
				)
			);

			$this->add_control(
				'next_arrow_position',
				array(
					'label'     => __( 'Next Arrow Position', 'jet-engine' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'next_vert_position',
				array(
					'label'   => __( 'Vertical Position by', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'top',
					'options' => array(
						'top'    => __( 'Top', 'jet-engine' ),
						'bottom' => __( 'Bottom', 'jet-engine' ),
					),
				)
			);

			$this->add_responsive_control(
				'next_top_position',
				array(
					'label'      => __( 'Top Indent', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%', 'em' ),
					'range'      => array(
						'px' => array(
							'min' => -400,
							'max' => 400,
						),
						'%' => array(
							'min' => -100,
							'max' => 100,
						),
						'em' => array(
							'min' => -50,
							'max' => 50,
						),
					),
					'condition' => array(
						'next_vert_position' => 'top',
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-listing-grid__slider-icon.next-arrow' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
					),
				)
			);

			$this->add_responsive_control(
				'next_bottom_position',
				array(
					'label'      => __( 'Bottom Indent', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%', 'em' ),
					'range'      => array(
						'px' => array(
							'min' => -400,
							'max' => 400,
						),
						'%' => array(
							'min' => -100,
							'max' => 100,
						),
						'em' => array(
							'min' => -50,
							'max' => 50,
						),
					),
					'condition' => array(
						'next_vert_position' => 'bottom',
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-listing-grid__slider-icon.next-arrow' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
					),
				)
			);

			$this->add_control(
				'next_hor_position',
				array(
					'label'   => __( 'Horizontal Position by', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'right',
					'options' => array(
						'left'  => __( 'Left', 'jet-engine' ),
						'right' => __( 'Right', 'jet-engine' ),
					),
				)
			);

			$this->add_responsive_control(
				'next_left_position',
				array(
					'label'      => __( 'Left Indent', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%', 'em' ),
					'range'      => array(
						'px' => array(
							'min' => -400,
							'max' => 400,
						),
						'%' => array(
							'min' => -100,
							'max' => 100,
						),
						'em' => array(
							'min' => -50,
							'max' => 50,
						),
					),
					'condition' => array(
						'next_hor_position' => 'left',
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-listing-grid__slider-icon.next-arrow' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
					),
				)
			);

			$this->add_responsive_control(
				'next_right_position',
				array(
					'label'      => __( 'Right Indent', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%', 'em' ),
					'range'      => array(
						'px' => array(
							'min' => -400,
							'max' => 400,
						),
						'%' => array(
							'min' => -100,
							'max' => 100,
						),
						'em' => array(
							'min' => -50,
							'max' => 50,
						),
					),
					'condition' => array(
						'next_hor_position' => 'right',
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-listing-grid__slider-icon.next-arrow' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
					),
				)
			);

			$this->add_control(
				'dots_styles',
				array(
					'label'     => __( 'Dots Styles', 'jet-engine' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_responsive_control(
				'dots_size',
				array(
					'label'      => __( 'Dots Size', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range'      => array(
						'px' => array(
							'min' => 6,
							'max' => 100,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-listing-grid__slider .jet-slick-dots li' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'dots_gap',
				array(
					'label'      => __( 'Dots Gap', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-listing-grid__slider .jet-slick-dots li' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 ); margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
					),
				)
			);

			$this->start_controls_tabs( 'tabs_dots_style' );

			$this->start_controls_tab(
				'tab_dots_normal',
				array(
					'label' => __( 'Normal', 'jet-engine' ),
				)
			);

			$this->add_control(
				'dots_bg_color',
				array(
					'label'     => __( 'Color', 'jet-engine' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-listing-grid__slider .jet-slick-dots li' => 'background: {{VALUE}}',
					),
				)
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_dots_hover',
				array(
					'label' => __( 'Hover', 'jet-engine' ),
				)
			);

			$this->add_control(
				'dots_bg_color_hover',
				array(
					'label'     => __( 'Color', 'jet-engine' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-listing-grid__slider .jet-slick-dots li:hover' => 'background: {{VALUE}}',
					),
				)
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_dots_active',
				array(
					'label' => __( 'Active', 'jet-engine' ),
				)
			);

			$this->add_control(
				'dots_bg_color_active',
				array(
					'label'     => __( 'Color', 'jet-engine' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-listing-grid__slider .jet-slick-dots li.slick-active' => 'background: {{VALUE}}',
					),
				)
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->end_controls_section();

			$this->start_controls_section(
				'section_scrollbar_style',
				array(
					'label'     => __( 'Scrollbar', 'jet-engine' ),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => array(
						'scroll_slider_enabled' => 'yes',
						'is_masonry!' => 'yes',
						'carousel_enabled!' => 'yes',
					),
				)
			);

			$this->add_control(
				'non_webkit_notice',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => esc_html__( 'Currently works only in -webkit- browsers', 'jet-engine' ),
					'content_classes' => 'elementor-descriptor',
				)
			);

			$this->add_control(
				'scrollbar_bg',
				array(
					'label'     => esc_html__( 'Scrollbar Color', 'jet-engine' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-listing-grid__scroll-slider::-webkit-scrollbar' => 'background-color: {{VALUE}};',
						'{{WRAPPER}} .jet-listing-grid__scroll-slider::-webkit-scrollbar-button' => 'width: 0; height: 0;',
						'{{WRAPPER}} .jet-listing-grid__scroll-slider::-webkit-scrollbar-track' => 'border: none; background: transparent;',
						'{{WRAPPER}} .jet-listing-grid__scroll-slider::--webkit-scrollbar-corner' => 'background: transparent;',
					),
				)
			);

			$this->add_control(
				'scrollbar_thumb_bg',
				array(
					'label'     => esc_html__( 'Scrollbar Thumb Color', 'jet-engine' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-listing-grid__scroll-slider::-webkit-scrollbar-thumb' => 'background-color: {{VALUE}}; border: none;',
					),
				)
			);

			$this->add_control(
				'scrollbar_height',
				array(
					'label' => esc_html__( 'Scrollbar Height', 'jet-engine' ),
					'type'  => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 1,
							'max' => 20,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .jet-listing-grid__scroll-slider::-webkit-scrollbar' => 'height: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'scrollbar_border_radius',
				array(
					'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-listing-grid__scroll-slider::-webkit-scrollbar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .jet-listing-grid__scroll-slider::-webkit-scrollbar-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->end_controls_section();

		}

		protected function register_controls() {

			$this->register_general_settings();

			$this->register_query_settings();
			$this->register_terms_query_settings();
			//$this->register_repeater_query_settings();

			if ( ! jet_engine()->listings->legacy->is_disabled() ) {
				do_action( 'jet-engine/listing/custom-query-settings', $this );
			}

			$this->register_visibility_settings();
			$this->register_carousel_settings();
			$this->register_style_settings();
			$this->register_carousel_style_settings();

		}

		/**
		 * Return meta types list for options
		 * @return [type] [description]
		 */
		public function meta_types() {

			return array(
				'NUMERIC'  => __( 'NUMERIC', 'jet-engine' ),
				'BINARY'   => __( 'BINARY', 'jet-engine' ),
				'CHAR'     => __( 'CHAR', 'jet-engine' ),
				'DATE'     => __( 'DATE', 'jet-engine' ),
				'DATETIME' => __( 'DATETIME', 'jet-engine' ),
				'DECIMAL'  => __( 'DECIMAL', 'jet-engine' ),
				'SIGNED'   => __( 'SIGNED', 'jet-engine' ),
				'UNSIGNED' => __( 'UNSIGNED', 'jet-engine' ),
			);

		}

		/**
		 * Returns all registered user roles
		 *
		 * @return [type] [description]
		 */
		public function get_user_roles() {
			return \Jet_Engine_Tools::get_user_roles();
		}

		/**
		 * Returns widget settings or custom settings
		 *
		 * @return array
		 */
		public function get_widget_settings() {

			$custom_settings = apply_filters( 'jet-engine/listing/grid/custom-settings', false, $this );

			if ( ! empty( $custom_settings ) ) {
				return array_merge( array( '_id' => $this->get_id() ), $custom_settings );
			} else {
				return array_merge( array( '_id' => $this->get_id() ), $this->get_settings_for_display() );
			}

		}

		/**
		 * Render grid posts
		 *
		 * @return void
		 */
		public function render_posts() {
			jet_engine()->listings->render_listing( $this->get_widget_settings() );
		}

		protected function render() {
			$this->render_posts();
		}

	}

}
