<?php
/**
 * Jet_Search_Ajax_Handlers class
 *
 * @package   jet-search
 * @author    Zemez
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Search_Ajax_Handlers' ) ) {

	/**
	 * Define Jet_Search_Ajax_Handlers class
	 */
	class Jet_Search_Ajax_Handlers {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   Jet_Search_Ajax_Handlers
		 */
		private static $instance = null;

		/**
		 * Ajax action.
		 *
		 * @var string
		 */
		private $action = 'jet_ajax_search';

		/**
		 * Has navigation.
		 *
		 * @var bool
		 */
		public $has_navigation = false;

		/**
		 * Search query.
		 *
		 * @var array
		 */
		public $search_query = array();

		/**
		 * Table alias.
		 *
		 * @var string
		 */
		private $postmeta_table_alias = 'jetsearch';

		/**
		 * Constructor for the class
		 */
		public function init() {

			// Set search query settings on the search result page
			add_action( 'pre_get_posts', array( $this, 'set_search_query' ) );

			// Search in custom fields
			add_filter( 'posts_clauses', array( $this, 'cf_search_clauses' ), 99, 2 );

			// Search in taxonomy terms
			add_filter( 'posts_clauses', array( $this, 'tax_terms_search_clauses' ), 99, 2 );

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				add_action( 'wp_ajax_jet_search_get_query_control_options', array( $this, 'get_query_control_options' ) );
				add_action( 'wp_ajax_jet_advanced_list_block_get_svg',      array( $this, 'get_icon_svg' ) );
				add_action( 'wp_ajax_suggestions_get_user_id',              array( $this, 'suggestions_get_user_id' ) );
				add_action( 'wp_ajax_nopriv_suggestions_get_user_id',       array( $this, 'suggestions_get_user_id' ) );
				add_action( 'wp_ajax_suggestions_save_settings',            array( $this, 'suggestions_save_settings' ) );
				add_action( 'wp_ajax_suggestions_get_settings',             array( $this, 'suggestions_get_settings' ) );
			}

			// Set Jet Smart Filters extra props
			add_filter( 'jet-smart-filters/filters/localized-data', array( $this, 'set_jet_smart_filters_extra_props' ) );

			// Set JetEngine extra props
			add_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'set_jet_engine_extra_props' ), -10, 3 );

			// Set JetWooBuilder extra props
			add_filter( 'jet-woo-builder/shortcodes/jet-woo-products/query-args',      array( $this, 'set_jet_woo_extra_props' ), 10, 2 );
			add_filter( 'jet-woo-builder/shortcodes/jet-woo-products-list/query-args', array( $this, 'set_jet_woo_extra_props' ), 10, 2 );

		}

		/**
		 * Get ajax action.
		 *
		 * @since  1.1.2
		 * @return string
		 */
		public function get_ajax_action() {
			return $this->action;
		}

		public function suggestions_save_settings() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-search' ) ) );
			}

			$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

			if ( ! $nonce || ! wp_verify_nonce( $nonce, 'jet-search-settings' ) ) {
				wp_send_json_error( array(
					'success' => false,
					array( 'message' => __( 'Nonce validation failed', 'jet-search' )
				) ) );
			}

			$settings = ! empty( $_REQUEST['settings'] ) ? $_REQUEST['settings'] : null;

			if ( ! empty( $settings ) ) {

				$settings_list = array( 'records_limit', 'use_session' );

				foreach ( $settings_list as $setting ) {
					if ( isset( $settings[$setting] ) ) {
						if ( false === get_option( 'jet_search_suggestions_' . $setting ) ) {
							add_option( 'jet_search_suggestions_' . $setting , $settings[$setting] );
						} else {
							update_option( 'jet_search_suggestions_' . $setting, $settings[$setting] );
						}
					}
				}

				wp_send_json_success( array(
					'message' => __( 'Settings saved', 'jet-search' )
				) );
			} else {
				wp_send_json_error( array(
					array( 'message' => __( 'Error', 'jet-search' )
				) ) );
			}
		}

		public function suggestions_get_settings() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-search' ) ) );
			}

			$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

			if ( ! $nonce || ! wp_verify_nonce( $nonce, 'jet-search-settings' ) ) {
				wp_send_json_error( array(
					'success' => false,
					array( 'message' => __( 'Nonce validation failed', 'jet-search' )
				) ) );
			}

			$settings_list = array( 'records_limit', 'use_session' );
			$settings      = array();

			foreach ( $settings_list as $setting ) {
				switch ( $setting ) {
					case 'records_limit':
						if ( false === get_option( 'jet_search_suggestions_' . $setting ) ) {
							add_option( 'jet_search_suggestions_' . $setting , 5 );
							$settings[$setting] = 5;
						} else {
							$settings[$setting] = get_option( 'jet_search_suggestions_' . $setting );
						}

						break;
					case 'use_session':
						if ( false === get_option( 'jet_search_suggestions_' . $setting ) ) {
							add_option( 'jet_search_suggestions_' . $setting , "false" );
							$settings[$setting] = "false";
						} else {
							$settings[$setting] = get_option( 'jet_search_suggestions_' . $setting );
						}

						break;
				}
			}

			return wp_send_json_success( array(
				'settings' => $settings
			) );
		}

		/**
		 * Returns a SVG code of selected icon
		 *
		 * @return [type] [description]
		 */
		public function get_icon_svg() {

			if ( ! current_user_can( 'upload_files' ) ) {
				wp_send_json_error( 'You are not allowed to do this' );
			}

			$media_id = ! empty( $_GET['media_id'] ) ? absint( $_GET['media_id'] ) : false;

			if ( ! $media_id ) {
				wp_send_json_error( 'Media ID not found in the request' );
			}

			$mime = get_post_mime_type( $media_id );

			if ( ! $mime || 'image/svg+xml' !== $mime ) {
				wp_send_json_error( 'This media type is not supported, please use SVG image' );
			}

			$file = get_attached_file( $media_id );

			ob_start();
			include $file;
			$content = apply_filters( 'jet-search/get-svg/content', ob_get_clean(), $media_id );

			wp_send_json_success( $content );

		}

		/**
		 * Set search query settings on the search result page.
		 *
		 * @param object $query
		 */
		public function set_search_query( $query ) {

			if ( ! is_admin() && is_search() && $query->is_search() ) {

				$form_settings = $this->get_form_settings();

				if ( ! empty( $form_settings ) && $query->is_main_query() ) {
					$this->search_query['s'] = $_GET['s'];

					if ( ! empty( $_REQUEST['jet_search_suggestions_settings'] ) ) {
						$this->set_suggestions_query_settings( $form_settings );
					} else {
						$this->set_query_settings( $form_settings );
					}

					// If the query is created by Query Builder, these query vars are primary.
					if ( isset( $query->query_vars['_query_type'] ) ) {
						$query->query_vars = array_merge( $this->search_query, $query->query_vars );
					} else {
						$query->query_vars = array_merge( $query->query_vars, $this->search_query );
					}
				}
			}
		}

		/**
		* Set Jet Smart Filters extra props.
		*/
		public function set_jet_smart_filters_extra_props( $data ) {

			if ( ! is_search() ) {
				return $data;
			}

			$settings = $this->get_form_settings();

			if ( ! empty( $settings ) ) {
				$data['extra_props']['jet_ajax_search_settings'] = json_encode( $settings );

				// For compatibility with Products Loop
				if ( ! empty( $data['queries']['woocommerce-archive'] ) && ! empty( $data['queries']['woocommerce-archive']['default'] ) ) {
					$data['queries']['woocommerce-archive']['default'][ $this->action ] = true;
				}
			}

			return $data;
		}

		/**
		 * Set JetEngine extra props.
		 */
		public function set_jet_engine_extra_props( $args, $render, $settings ) {

			$is_archive_template = isset( $settings['is_archive_template'] ) && 'yes' === $settings['is_archive_template'];

			if ( ! is_search() || ! $is_archive_template ) {
				return $args;
			}

			$settings = $this->get_form_settings();

			if ( ! empty( $settings ) ) {
				$args[ $this->action ] = true;
				$args['jet_ajax_search_settings'] = $settings;
			}

			return $args;
		}

		/**
		 * Set JetWooBuilder extra props
		 */
		public function set_jet_woo_extra_props( $args, $shortcode ) {

			$use_current_query = $shortcode->get_attr( 'use_current_query' );
			$use_current_query = filter_var( $use_current_query, FILTER_VALIDATE_BOOLEAN );

			if ( ! is_search() || ! $use_current_query ) {
				return $args;
			}

			$args[ $this->action ] = true;

			return $args;
		}

		/**
		 * Get form settings on the search result page.
		 *
		 * @return array
		 */
		public function get_form_settings() {

			$form_settings = array();

			// Ajax search form settings

			if ( ! empty( $_REQUEST['jet_ajax_search_settings'] ) ) {
				$form_settings = $_REQUEST['jet_ajax_search_settings'];
				$form_settings = stripcslashes( $form_settings );
				$form_settings = json_decode( $form_settings );
				$form_settings = get_object_vars( $form_settings );
			} elseif ( ! empty( $_REQUEST['query']['jet_ajax_search_settings'] ) ) {
				$form_settings = $_REQUEST['query']['jet_ajax_search_settings'];
			}

			//Suggestions form settings

			if ( ! empty( $_REQUEST['jet_search_suggestions_settings'] ) ) {
				$form_settings = $_REQUEST['jet_search_suggestions_settings'];
				$form_settings = stripcslashes( $form_settings );
				$form_settings = json_decode( $form_settings );
				$form_settings = get_object_vars( $form_settings );
			} elseif ( ! empty( $_REQUEST['query']['jet_search_suggestions_settings'] ) ) {
				$form_settings = $_REQUEST['query']['jet_search_suggestions_settings'];
			}

			return $form_settings;
		}

		/**
		 * Set search query settings.
		 *
		 * @param array $args
		 */
		protected function set_query_settings( $args = array() ) {
			if ( $args ) {
				$this->search_query[ $this->action ] = true;
				$this->search_query['cache_results'] = true;
				$this->search_query['post_type']     = $args['search_source'];
				$this->search_query['order']         = isset( $args['results_order'] ) ? $args['results_order'] : '';
				$this->search_query['orderby']       = isset( $args['results_order_by'] ) ? $args['results_order_by'] : '';
				$this->search_query['tax_query']     = array( 'relation' => 'AND' );
				$this->search_query['sentence']      = isset( $args['sentence'] ) ? filter_var( $args['sentence'], FILTER_VALIDATE_BOOLEAN ) : false;
				$this->search_query['post_status']   = 'publish';

				// Include specific terms
				if ( ! empty( $args['category__in'] ) ) {
					$tax = ! empty( $args['search_taxonomy'] ) ? $args['search_taxonomy'] : 'category';

					array_push(
						$this->search_query['tax_query'],
						array(
							'taxonomy' => $tax,
							'field'    => 'id',
							'operator' => 'IN',
							'terms'    => $args['category__in'],
						)
					);
				} else if ( ! empty( $args['include_terms_ids'] ) ) {

					$include_tax_query = array( 'relation' => 'OR' );
					$terms_data        = $this->prepare_terms_data( $args['include_terms_ids'] );

					foreach ( $terms_data as $taxonomy => $terms_ids ) {
						$include_tax_query[] = array(
							'taxonomy' => $taxonomy,
							'field'    => 'id',
							'operator' => 'IN',
							'terms'    => $terms_ids,
						);
					}

					array_push(
						$this->search_query['tax_query'],
						$include_tax_query
					);
				}

				// Exclude specific terms
				if ( ! empty( $args['exclude_terms_ids'] ) ) {

					$exclude_tax_query = array( 'relation' => 'OR' );
					$terms_data        = $this->prepare_terms_data( $args['exclude_terms_ids'] );

					foreach ( $terms_data as $taxonomy => $terms_ids ) {
						$exclude_tax_query[] = array(
							'taxonomy' => $taxonomy,
							'field'    => 'id',
							'operator' => 'NOT IN',
							'terms'    => $terms_ids,
						);
					}

					array_push(
						$this->search_query['tax_query'],
						$exclude_tax_query
					);
				}

				// Exclude specific posts
				if ( ! empty( $args['exclude_posts_ids'] ) ) {
					$this->search_query['post__not_in'] = $args['exclude_posts_ids'];
				}

				// Current Query
				if ( ! empty( $args['current_query'] ) ) {
					$this->search_query = array_merge( $this->search_query, (array) $args['current_query'] );
				}

				do_action( 'jet-search/ajax-search/search-query', $this, $args );
			}
		}

		/**
		 * Set suggestions search query settings.
		 *
		 * @param array $args
		 */
		protected function set_suggestions_query_settings( $args = array() ) {
			if ( $args ) {
				$this->search_query['cache_results'] = true;
				$this->search_query['tax_query']     = array( 'relation' => 'AND' );
				$this->search_query['post_status']   = 'publish';

				// Include specific terms
				if ( ! empty( $args['category__in'] ) ) {
					$tax = ! empty( $args['search_taxonomy'] ) ? $args['search_taxonomy'] : 'category';

					array_push(
						$this->search_query['tax_query'],
						array(
							'taxonomy' => $tax,
							'field'    => 'id',
							'operator' => 'IN',
							'terms'    => $args['category__in'],
						)
					);
				}

				// Current Query
				if ( ! empty( $args['current_query'] ) ) {
					$this->search_query = array_merge( $this->search_query, (array) $args['current_query'] );
				}

				do_action( 'jet-search/search-suggestions/search-query', $this, $args );
			}
		}

		/**
		 * Get Query control options list.
		 *
		 * @since  2.0.0
		 * @return void
		 */
		function get_query_control_options() {

			$data = $_REQUEST;

			if ( ! isset( $data['query_type'] ) ) {
				wp_send_json_error();
				return;
			}

			$results = array();

			switch ( $data['query_type'] ) {
				case 'terms':

					$terms_args = array(
						'hide_empty' => false,
					);

					if ( ! empty( $data['q'] ) ) {
						$terms_args['search'] = $data['q'];
					}

					if ( ! empty( $data['post_type'] ) ) {
						$terms_args['taxonomy'] = get_object_taxonomies( $data['post_type'], 'names' );
					} else {
						$terms_args['taxonomy'] = get_taxonomies( array( 'show_in_nav_menus' => true ), 'names' );
					}

					if ( ! empty( $data['ids'] ) ) {
						$terms_args['include'] = $data['ids'];
					}

					$terms = get_terms( $terms_args );

					global $wp_taxonomies;

					foreach ( $terms as $term ) {

						if ( "1" === $data['bricks-is-builder'] ) {
							$results[ (int)$term->term_id] = sprintf( '%1$s: %2$s', $wp_taxonomies[ $term->taxonomy ]->label, $term->name );
						} else {
							$results[] = array(
								'id'   => $term->term_id,
								'text' => sprintf( '%1$s: %2$s', $wp_taxonomies[ $term->taxonomy ]->label, $term->name ),
							);
						}
					}

					break;

				case 'posts':

					$query_args = array(
						'post_type'           => 'any',
						'posts_per_page'      => - 1,
						'suppress_filters'    => false,
						'ignore_sticky_posts' => true,
					);

					if ( ! empty( $data['q'] ) ) {
						$query_args['s_title'] = $data['q'];
						$query_args['orderby'] = 'relevance';
					}

					if ( ! empty( $data['post_type'] ) ) {
						$query_args['post_type'] = $data['post_type'];
					}

					if ( ! empty( $data['ids'] ) ) {
						$query_args['post__in'] = $data['ids'];
					}

					add_filter( 'posts_where', array( $this, 'force_search_by_title' ), 10, 2 );

					$posts = get_posts( $query_args );

					remove_filter( 'posts_where', array( $this, 'force_search_by_title' ), 10 );

					foreach ( $posts as $post ) {
						$results[] = array(
							'id'   => $post->ID,
							'text' => sprintf( '%1$s: %2$s', ucfirst( $post->post_type ), $post->post_title ),
						);
					}

					break;
			}

			if ( "1" === $data['bricks-is-builder'] ) {
				$data = $results;

			} else {
				$data = array(
					'results' => $results,
				);
			}

			wp_send_json_success( $data );
		}

		/**
		 * Force query to look in post title while searching.
		 *
		 * @since  2.0.0
		 * @param  string $where
		 * @param  object $query
		 * @return string
		 */
		public function force_search_by_title( $where, $query ) {

			$args = $query->query;

			if ( ! isset( $args['s_title'] ) ) {
				return $where;
			}

			global $wpdb;

			$search = esc_sql( $wpdb->esc_like( $args['s_title'] ) );
			$where .= " AND {$wpdb->posts}.post_title LIKE '%$search%'";

			return $where;
		}

		/**
		 * Prepare terms data for tax query
		 *
		 * @since  2.0.0
		 * @param  array $terms_ids
		 * @return array
		 */
		public function prepare_terms_data( $terms_ids = array() ) {

			$result = array();

			foreach ( $terms_ids as $term_id ) {
				$term     = get_term( $term_id );
				$taxonomy = $term->taxonomy;

				$result[ $taxonomy ][] = $term_id;
			}

			return $result;
		}

		/**
		 * Get custom fields keys for search
		 *
		 * @since  2.0.0
		 * @return array|bool
		 */
		public function get_cf_search_keys() {

			if ( isset( $_GET['action'] ) && $this->action === $_GET['action'] && ! empty( $_GET['data']['custom_fields_source'] ) ) {
				$cf_source = $_GET['data']['custom_fields_source'];

			} else {
				$settings  = $this->get_form_settings();
				$cf_source = ! empty( $settings['custom_fields_source'] ) ? $settings['custom_fields_source'] : false;
			}

			if ( empty( $cf_source ) ) {
				return false;
			}

			return explode( ',', str_replace( ' ', '', $cf_source ) );
		}

		/**
		 * Modify the WHERE and JOIN clauses of the query for search ib custom fields.
		 *
		 * @param  array  $args
		 * @param  object $query
		 * @return array
		 */
		public function cf_search_clauses( $args, $query ) {

			if ( ! $query->get( $this->action ) ) {
				return $args;
			}

			$cf_keys = $this->get_cf_search_keys();

			if ( ! $cf_keys ) {
				return $args;
			}

			global $wpdb;

			// Modify the JOIN clause.
			$args['join'] .= " LEFT JOIN {$wpdb->postmeta} {$this->postmeta_table_alias} ON {$wpdb->posts}.ID = {$this->postmeta_table_alias}.post_id ";

			// Modify the WHERE clause.
			$cf_where = '';
			$or_op    = '';

			foreach ( $cf_keys as $cf_key ) {
				$cf_where .= "{$or_op}({$this->postmeta_table_alias}.meta_key = '{$cf_key}' AND {$this->postmeta_table_alias}.meta_value LIKE $1)";
				$or_op = ' OR ';
			}

			$args['where'] = preg_replace(
				"/\(\s*{$wpdb->posts}.post_content\s+LIKE\s*(\'[^\']+\')\s*\)/",
				"({$wpdb->posts}.post_content LIKE $1) OR {$cf_where}", $args['where'] );

			return $args;
		}

		/**
		 * Modify the WHERE and JOIN clauses of the query for search in taxonomy terms.
		 *
		 * @param  array  $args
		 * @param  object $query
		 * @return array
		 */
		public function tax_terms_search_clauses( $args, $query ) {

			if ( ! $query->get( $this->action ) ) {
				return $args;
			}

			if ( isset( $_GET['action'] ) && $this->action === $_GET['action']
				&& ! empty( $_GET['data']['search_in_taxonomy'] )
				&& ! empty( $_GET['data']['search_in_taxonomy_source'] )
			) {
				$taxonomies = $_GET['data']['search_in_taxonomy_source'];
			} else {
				$settings   = $this->get_form_settings();
				$taxonomies = ! empty( $settings['search_in_taxonomy'] ) && ! empty( $settings['search_in_taxonomy_source'] ) ? $settings['search_in_taxonomy_source'] : false;
			}

			if ( ! $taxonomies ) {
				return $args;
			}

			if ( ! class_exists( 'Jet_Search_Tax_Query' ) ) {
				require jet_search()->plugin_path( 'includes/jet-search-tax-query.php' );
			}

			$tax_query_source = array( 'relation' => 'OR' );

			foreach ( $taxonomies as $key => $value ) {
				$tax_query_source[] = array(
					'taxonomy' => $value,
					'field'    => 'name', // keep this
					'terms'    => $query->get( 's' ),
				);
			}

			$search_taxonomy = ! empty( $_GET['data']['search_taxonomy'] ) ? $_GET['data']['search_taxonomy'] : '';
			$category__in    = ! empty( $_GET['data']['category__in'] ) ? $_GET['data']['category__in'] : '';

			if ( !empty( $search_taxonomy ) && ! empty( $category__in ) ) {
				$tax_query_source[] = array(
					'taxonomy' => $search_taxonomy,
					'field'    => 'id',
					'operator' => 'IN',
					'terms'    => $category__in,
					'req'      => true
				);
			}

			$include_terms_ids = ! empty( $_GET['data']['include_terms_ids'] ) ? $_GET['data']['include_terms_ids'] : '';
			$exclude_terms_ids = ! empty( $_GET['data']['exclude_terms_ids'] ) ? $_GET['data']['exclude_terms_ids'] : '';
			$exclude_posts_ids = ! empty( $_GET['data']['exclude_posts_ids'] ) ? $_GET['data']['exclude_posts_ids'] : '';

			// Exclude specific terms
			if ( ! empty( $exclude_terms_ids ) ) {
				$tax_query_source[] = array(
					'terms'     => $exclude_terms_ids,
					'_tax_type' => 'exclude_terms',
					'_ids'      => $exclude_terms_ids
				);
			}

			// Include specific terms
			if ( ! empty( $include_terms_ids ) ) {
				$tax_query_source[] = array(
					'terms'     => $include_terms_ids,
					'_tax_type' => 'include_terms',
					'_ids'      => $include_terms_ids
				);
			}

			// Exclude specific posts
			if ( ! empty( $exclude_posts_ids ) ) {
				$tax_query_source[] = array(
					'terms'     => $exclude_posts_ids,
					'_tax_type' => 'exclude_posts',
					'_ids'      => $exclude_posts_ids
				);
			}

			$tax_query = new Jet_Search_Tax_Query( $tax_query_source );

			global $wpdb;

			$tax_sql = $tax_query->get_sql( $wpdb->posts, 'ID' );

			$args['join']  .= $tax_sql['join'] . ' ';
			$args['where'] .= $tax_sql['where'] . ' ';

			return $args;
		}

		/**
		 * Extract limit query from data array.
		 *
		 * @since  2.0.0
		 * @param  array $data
		 * @return int
		 */
		public function extract_limit_query( $data ) {
			$limit_query = ! empty( $data['limit_query'] ) ? $data['limit_query'] : 5;

			if ( empty( $data['deviceMode'] ) ) {
				return $limit_query;
			}

			$limit_query_tablet = ! empty( $data['limit_query_tablet'] ) ? $data['limit_query_tablet'] : $limit_query;
			$limit_query_mobile = ! empty( $data['limit_query_mobile'] ) ? $data['limit_query_mobile'] : $limit_query_tablet;

			switch ( $data['deviceMode'] ) {
				case 'tablet':
					$limit_query = $limit_query_tablet;
					break;

				case 'mobile':
					$limit_query = $limit_query_mobile;
					break;
			}

			return $limit_query;
		}

		/**
		 * Return result area navigation.
		 *
		 * @param array $settings
		 *
		 * @return array
		 */
		public function get_results_navigation( $settings = array() ) {
			$navigation_container_html = apply_filters(
				'jet-search/ajax-search/navigation-container-html',
				'<div class="jet-ajax-search__navigation-container">%s</div>'
			);

			$navigation_types = apply_filters(
				'jet-search/ajax-search/navigation-types',
				array( 'bullet_pagination', 'number_pagination', 'navigation_arrows' )
			);

			$header_navigation = '';
			$footer_navigation = '';
			if ( $settings['limit_query'] < $settings['post_count'] ) {

				foreach ( $navigation_types as $type ) {
					if ( ! isset( $settings[ $type ] ) ) {
						continue;
					}

					if ( ! $settings[ $type ] ) {
						continue;
					}

					$buttons = $this->get_navigation_buttons_html( $settings, $type );

					if ( empty( $buttons ) ) {
						continue;
					}

					$this->has_navigation = true;

					switch ( $settings[ $type ] ) {
						case 'in_header':
							$header_navigation .= sprintf( $navigation_container_html, $buttons );
							break;

						case 'in_footer':
							$footer_navigation .= sprintf( $navigation_container_html, $buttons );
							break;

						case 'both':
							$header_navigation .= sprintf( $navigation_container_html, $buttons );
							$footer_navigation .= sprintf( $navigation_container_html, $buttons );
							break;
					}
				}
			}

			return array(
				'in_header' => $header_navigation,
				'in_footer' => $footer_navigation,
			);
		}

		/**
		 * Get results navigation buttons html.
		 *
		 * @param array  $settings
		 * @param string $type
		 *
		 * @return string
		 */
		public function get_navigation_buttons_html( $settings = array(), $type = 'bullet_pagination' ) {
			$output_html = '';
			$bullet_html = apply_filters( 'jet-search/ajax-search/navigate-button-html', '<div role=button class="jet-ajax-search__navigate-button %1$s" data-number="%2$s"></div>' );

			switch ( $type ) {
				case 'bullet_pagination':
					$button_class = 'jet-ajax-search__bullet-button';

				case 'number_pagination':
					$button_class = isset( $button_class ) ? $button_class : 'jet-ajax-search__number-button';

					for ( $i = 0; $i < $settings['columns']; $i++ ) {
						$active_button_class = ( $i === 0 ) ? ' jet-ajax-search__active-button' : '' ;
						$output_html .= sprintf( $bullet_html, $button_class . $active_button_class, $i + 1 );
					}
					break;

				case 'navigation_arrows':
					$prev_button = apply_filters( 'jet-search/ajax-search/prev-button-html', '<div role=button class="jet-ajax-search__prev-button jet-ajax-search__arrow-button jet-ajax-search__navigate-button jet-ajax-search__navigate-button-disable" data-direction="-1">%s</div>' );
					$next_button = apply_filters( 'jet-search/ajax-search/next-button-html', '<div role=button class="jet-ajax-search__next-button jet-ajax-search__arrow-button jet-ajax-search__navigate-button" data-direction="1">%s</div>' );
					$arrow       = Jet_Search_Tools::get_svg_arrows( $settings['navigation_arrows_type'] );
					$output_html = sprintf( $prev_button . $next_button, $arrow['left'], $arrow['right'] );
					break;
			}

			return $output_html;
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return Jet_Search_Ajax_Handlers
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}
}

/**
 * Returns instance of Jet_Search_Ajax_Handlers
 *
 * @return Jet_Search_Ajax_Handlers
 */
function jet_search_ajax_handlers() {
	return Jet_Search_Ajax_Handlers::get_instance();
}
