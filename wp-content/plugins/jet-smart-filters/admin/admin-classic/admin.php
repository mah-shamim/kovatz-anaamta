<?php
/**
 * Jet Smart Filters Сlassic Admin class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Сlassic_Admin' ) ) {
	/**
	 * Define Jet_Smart_Filters_Сlassic_Admin class
	 */
	class Jet_Smart_Filters_Сlassic_Admin {
		/**
		 * Constructor for the class
		 */
		public function __construct() {
			// Init components
			require jet_smart_filters()->plugin_path( 'admin/includes/data.php' );
			add_action( 'init', function() {
				$this->data = new Jet_Smart_Filters_Admin_Data();
			}, 999 );

			//Init Setting Pages
			add_action( 'admin_menu', array( $this, 'register_settings_page' ), 99 );

			require jet_smart_filters()->plugin_path( 'admin/setting-pages/setting-pages.php' );
			new Jet_Smart_Filters_Admin_Setting_Pages();

			// Indexer
			$this->is_indexer_enabled = filter_var( jet_smart_filters()->settings->get( 'use_indexed_filters' ), FILTER_VALIDATE_BOOLEAN );

			if ( $this->is_indexer_enabled ) {
				add_action( 'restrict_manage_posts', array( $this, 'add_index_filters_button' ) );
			}

			add_filter( 'jet-smart-filters/post-type/args', array( $this, 'set_post_type_args' ) );
			add_action( 'admin_init', array( $this, 'init_meta' ), 99999 );

			add_filter( 'jet-smart-filters/admin/filter-types', array( $this, 'add_placeholder_types' ) );
			add_filter( 'jet-smart-filters/post-type/options-data-sources', array( $this, 'add_placeholder_sources' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_assets' ) );

			add_action( 'wp_ajax_jet_smart_filters_admin', array( $this, 'filters_admin_action' ) );
			add_action( 'wp_ajax_nopriv_jet_smart_filters_admin', array( $this, 'filters_admin_action' ) );

			add_action( 'add_meta_boxes_' . jet_smart_filters()->post_type->slug(), array( $this, 'disable_metaboxes' ), 9999 );
			add_filter( 'post_row_actions', array( $this, 'remove_view_action' ), 10, 2 );

			// Admin mode switcher button
			add_action( 'restrict_manage_posts', array( $this, 'add_admin_mode_switcher_button' ) );
		}

		public function set_post_type_args( $args ) {

			$args = array(
				'labels' => array(
					'name'               => esc_html__( 'Smart Filters', 'jet-smart-filters' ),
					'singular_name'      => esc_html__( 'Filter', 'jet-smart-filters' ),
					'add_new'            => esc_html__( 'Add New', 'jet-smart-filters' ),
					'add_new_item'       => esc_html__( 'Add New Filter', 'jet-smart-filters' ),
					'edit_item'          => esc_html__( 'Edit Filter', 'jet-smart-filters' ),
					'new_item'           => esc_html__( 'Add New Item', 'jet-smart-filters' ),
					'view_item'          => esc_html__( 'View Filter', 'jet-smart-filters' ),
					'search_items'       => esc_html__( 'Search Filter', 'jet-smart-filters' ),
					'not_found'          => esc_html__( 'No Filters Found', 'jet-smart-filters' ),
					'not_found_in_trash' => esc_html__( 'No Filters Found In Trash', 'jet-smart-filters' ),
					'menu_name'          => esc_html__( 'Smart Filters', 'jet-smart-filters' ),
				),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 101,
				'menu_icon'           => 'data:image/svg+xml;base64,' . base64_encode('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M20 1H4C2.34315 1 1 2.34315 1 4V20C1 21.6569 2.34315 23 4 23H20C21.6569 23 23 21.6569 23 20V4C23 2.34315 21.6569 1 20 1ZM4 0C1.79086 0 0 1.79086 0 4V20C0 22.2091 1.79086 24 4 24H20C22.2091 24 24 22.2091 24 20V4C24 1.79086 22.2091 0 20 0H4Z" fill="black"/><path fill-rule="evenodd" clip-rule="evenodd" d="M21.6293 6.00066C21.9402 5.98148 22.1176 6.38578 21.911 6.64277L20.0722 8.93035C19.8569 9.19824 19.4556 9.02698 19.4598 8.669L19.4708 7.74084C19.4722 7.61923 19.4216 7.50398 19.3343 7.42975L18.6676 6.86321C18.4105 6.6447 18.5378 6.19134 18.8619 6.17135L21.6293 6.00066ZM6.99835 12.008C6.99835 14.1993 5.20706 15.9751 2.99967 15.9751C2.44655 15.9751 2 15.5293 2 14.9827C2 14.4361 2.44655 13.9928 2.99967 13.9928C4.10336 13.9928 4.99901 13.1036 4.99901 12.008V9.03323C4.99901 8.48413 5.44556 8.04082 5.99868 8.04082C6.55179 8.04082 6.99835 8.48413 6.99835 9.03323V12.008ZM17.7765 12.008C17.7765 13.1036 18.6721 13.9928 19.7758 13.9928C20.329 13.9928 20.7755 14.4336 20.7755 14.9827C20.7755 15.5318 20.329 15.9751 19.7758 15.9751C17.5684 15.9751 15.7772 14.1993 15.7772 12.008V9.03323C15.7772 8.48413 16.2237 8.04082 16.7768 8.04082C17.33 8.04082 17.7765 8.48665 17.7765 9.03323V9.92237H18.5707C19.1238 9.92237 19.5729 10.3682 19.5729 10.9173C19.5729 11.4664 19.1238 11.9122 18.5707 11.9122H17.7765V12.008ZM15.2038 10.6176C15.2063 10.6151 15.2088 10.6151 15.2088 10.6151C14.8942 9.79393 14.3056 9.07355 13.4835 8.60001C11.5755 7.50181 9.13979 8.15166 8.04117 10.0508C6.94001 11.9475 7.59462 14.3731 9.50008 15.4688C10.9032 16.2749 12.593 16.1338 13.8261 15.2472L13.8184 15.2371C14.1026 15.0633 14.2904 14.751 14.2904 14.3958C14.2904 13.8492 13.8438 13.4059 13.2932 13.4059C13.0268 13.4059 12.7833 13.5092 12.6057 13.6805C12.0069 14.081 11.2102 14.1439 10.5378 13.7762L14.5644 11.9198C14.7978 11.8493 15.0059 11.6931 15.1353 11.4664C15.2926 11.1969 15.3078 10.8871 15.2038 10.6176ZM12.4864 10.3153C12.6057 10.3833 12.7122 10.4614 12.8112 10.5471L9.49754 12.0709C9.48993 11.7208 9.5762 11.3657 9.76395 11.0407C10.3145 10.0937 11.5324 9.76874 12.4864 10.3153Z" fill="#24292D"/></svg>'),
				'show_in_nav_menus'   => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'query_var'           => false,
				'can_export'          => true,
				'rewrite'             => false,
				'capability_type'     => 'post',
				'supports'            => array( 'title' ),
			);

			return $args;
		}

		/**
		 * Initialize filters meta
		 */
		public function init_meta() {

			new Cherry_X_Post_Meta( array(
				'id'            => 'filter-labels',
				'title'         => __( 'Filter Labels', 'jet-smart-filters' ),
				'page'          => array( jet_smart_filters()->post_type->slug() ),
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'builder_cb'    => array( $this, 'get_builder' ),
				'fields'        => apply_filters(
					'jet-smart-filters/post-type/meta-fields-labels',
					$this->data->settings_data['labels']
				)
			) );

			new Cherry_X_Post_Meta( array(
				'id'            => 'filter-settings',
				'title'         => __( 'Filter Settings', 'jet-smart-filters' ),
				'page'          => array( jet_smart_filters()->post_type->slug() ),
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'builder_cb'    => array( $this, 'get_builder' ),
				'fields'        => apply_filters(
					'jet-smart-filters/post-type/meta-fields-settings',
					$this->data->settings_data['settings']
				),
			) );

			new Cherry_X_Post_Meta( array(
				'id'            => 'query-settings',
				'title'         => 'Query Settings',
				'page'          => array( jet_smart_filters()->post_type->slug() ),
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'builder_cb'    => array( $this, 'get_builder' ),
				'fields'        => apply_filters(
					'jet-smart-filters/post-type/meta-fields-query',
					$this->data->settings_data['query']
				)
			) );

			$filter_date_formats = jet_smart_filters()->utils->get_file_html( 'admin/templates/info-blocks/date-formats.php' );

			new Cherry_X_Post_Meta( array(
				'id'            => 'filter-date-formats',
				'title'         => __( 'Date Formats', 'jet-smart-filters' ),
				'page'          => array( jet_smart_filters()->post_type->slug() ),
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'builder_cb'    => array( $this, 'get_builder' ),
				'fields'        => array(
					'license' => array(
						'type'   => 'html',
						'class'  => 'cx-component',
						'html'   => $filter_date_formats,
					),
				),
			) );

			$filter_notes = jet_smart_filters()->utils->get_file_html( 'admin/admin-classic/templates/notes.php' );

			new Cherry_X_Post_Meta( array(
				'id'            => 'filter-notes',
				'title'         => __( 'Notes', 'jet-smart-filters' ),
				'page'          => array( jet_smart_filters()->post_type->slug() ),
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'builder_cb'    => array( $this, 'get_builder' ),
				'fields'        => array(
					'license' => array(
						'type'   => 'html',
						'class'  => 'cx-component',
						'html'   => $filter_notes,
					),
				),
			) );
		}

		/**
		 * Add placeholders for options lists
		 */
		public function add_placeholder_types( $options ) {

			return array( 0 => __( 'Select filter type...', 'jet-smart-filters' ) ) + $options;
		}

		public function add_placeholder_sources( $options ) {

			return array_merge( array( '' => __( 'Select data source...', 'jet-smart-filters' ) ), $options );
		}

		/**
		 * Admin enqueue assets
		 */
		public function admin_enqueue_assets() {

			$screen = get_current_screen();

			if ( jet_smart_filters()->post_type->slug() !== $screen->id && 'edit-' . jet_smart_filters()->post_type->slug() !== $screen->id ) {
				return;
			}

			wp_enqueue_script(
				'jet-smart-filters',
				jet_smart_filters()->plugin_url( 'admin/admin-classic/assets/js/jsf-admin-classic.js' ),
				array( 'jquery' ),
				jet_smart_filters()->get_version(),
				true
			);

			wp_enqueue_style(
				'jet-smart-filters-admin',
				jet_smart_filters()->plugin_url( 'admin/admin-classic/assets/css/admin-classic.css' ),
				array(),
				jet_smart_filters()->get_version()
			);

			// localized data
			$post_id              = isset( $_GET['post'] ) ? $_GET['post'] : false;
			$data_exclude_include = array();
			$data_color_image     = array();

			if ( ! $post_id && isset( $_REQUEST['post_ID'] ) ) {
				$post_id = $_REQUEST['post_ID'];
			}

			if ( !empty( $post_id ) ){
				$data_exclude_include = get_post_meta( $_REQUEST['post'], '_data_exclude_include', true );
				$data_color_image = get_post_meta( $_REQUEST['post'], '_source_color_image_input', true );
			}

			wp_localize_script( 'jet-smart-filters', 'JetSmartFiltersAdminData', array(
				'urls' => array(
					'admin'     => get_admin_url(),
					'ajaxurl'   => admin_url( 'admin-ajax.php' ),
					'endpoints' => jet_smart_filters()->rest_api->get_endpoints_urls(),
				),
				'nonce'              => wp_create_nonce( 'wp_rest' ),
				'dataExcludeInclude' => $data_exclude_include,
				'dataColorImage'     => $data_color_image,
			) );
		}

		/**
		 * Admin action in AJAX request
		 */
		public function filters_admin_action() {

			$tax        = ! empty( $_REQUEST['taxonomy'] ) ? $_REQUEST['taxonomy'] : false;
			$post_type  = ! empty( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : false;
			$hide_empty = isset( $_REQUEST['hide_empty'] ) ? filter_var( $_REQUEST['hide_empty'], FILTER_VALIDATE_BOOLEAN ) : true;
			$posts_list = '';
			$terms_list = '';

			if ( $tax ) {
				$args = array(
					'taxonomy'   => $tax,
					'hide_empty' => $hide_empty
				);

				$terms = get_terms( $args );
				$terms = wp_list_pluck( $terms, 'name', 'term_id' );

				foreach ( $terms as $terms_id => $term_name ) {
					$terms_list .= '<option value="' . $terms_id . '">' . $term_name . '</option>';
				}
			}

			if ( $post_type ) {
				$args = array(
					'post_type'      => $post_type,
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
				);

				$posts = get_posts( $args );

				if ( ! empty( $posts ) ) {
					$posts = wp_list_pluck( $posts, 'post_title', 'ID' );
				}

				foreach ( $posts as $post_id => $post_title ) {
					$posts_list .= '<option value="' . $post_id . '">' . $post_title . '</option>';
				}
			}

			wp_send_json( array(
				'terms' => $terms_list,
				'posts' => $posts_list,
			) );
		}

		/**
		 * Register add/edit page
		 */
		public function register_settings_page() {

			add_submenu_page(
				'edit.php?post_type=jet-smart-filters',
				esc_html__( 'Settings', 'jet-dashboard' ),
				esc_html__( 'Settings', 'jet-dashboard' ),
				'manage_options',
				add_query_arg(
					array(
						'page' => 'jet-dashboard-settings-page',
						'subpage' => 'jet-smart-filters-general-settings'
					),
					admin_url( 'admin.php' )
				)
			);
		}

		/**
		 * Add admin mode switcher button in manage post panel
		 */
		public function add_admin_mode_switcher_button( $post_type ) {

			if ( 'jet-smart-filters' !== $post_type ) {
				return;
			}

			printf( '<button type="button" id="jet-smart-filters-admin-mode-switcher" data-loading-text="%2$s">%1$s</button>',
				esc_html__( 'Switch to New View', 'jet-smart-filters' ),
				esc_html__( 'Switching...', 'jet-smart-filters' )
			);
		}

		/**
		 * Add index filter button in manage post panel
		 */
		public function add_index_filters_button( $post_type ) {

			if ( 'jet-smart-filters' !== $post_type ) {
				return;
			}

			printf( '<button type="button" id="jet-smart-filters-indexer-button" data-default-text="%1$s" data-loading-text="%2$s">%1$s</button>',
				esc_html__( 'Index Filters', 'jet-smart-filters' ),
				esc_html__( 'Indexing...', 'jet-smart-filters' )
			);
		}

		/**
		 * Return UI builder instance
		 */
		public function get_builder() {

			$data = jet_smart_filters()->framework->get_included_module_data( 'cherry-x-interface-builder.php' );

			return new CX_Interface_Builder(
				array(
					'path' => $data['path'],
					'url'  => $data['url'],
				)
			);
		}

		/**
		 * Disable metaboxes from Jet Templates
		 */
		public function disable_metaboxes() {

			global $wp_meta_boxes;
			unset( $wp_meta_boxes[jet_smart_filters()->post_type->slug()]['side']['core']['pageparentdiv'] );
		}

		/**
		 * Actions posts
		 */
		public function remove_view_action( $actions, $post ) {

			if ( jet_smart_filters()->post_type->slug() === $post->post_type ) {
				unset( $actions['view'] );
			}

			return $actions;
		}
	}
}