<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Render_Listing_Grid' ) ) {

	class Jet_Engine_Render_Listing_Grid extends Jet_Engine_Render_Base {

		public $is_first         = false;
		public $data             = false;
		public $query_vars       = array();
		public $posts_query      = false;
		public $listing_id       = null;
		public $listing_query_id = null;

		public static $did_listings = array();

		public function get_name() {
			return 'jet-listing-grid';
		}

		public function default_settings() {
			return apply_filters( 'jet-engine/listing/render/default-settings', array(
				'lisitng_id'               => '',
				'columns'                  => 3,
				'columns_tablet'           => 3,
				'columns_mobile'           => 3,
				'column_min_width'         => 240,
				'column_min_width_tablet'  => 240,
				'column_min_width_mobile'  => 240,
				'inline_columns_css'       => false,
				'is_archive_template'      => '',
				'post_status'              => array( 'publish' ),
				'use_random_posts_num'     => '',
				'posts_num'                => 6,
				'max_posts_num'            => 9,
				'not_found_message'        => __( 'No data was found', 'jet-engine' ),
				'is_masonry'               => '',
				'equal_columns_height'     => '',
				'use_load_more'            => '',
				'load_more_id'             => '',
				'load_more_type'           => 'click',
				'load_more_offset'         => null,
				'loader_text'              => '',
				'loader_spinner'           => '',
				'use_custom_post_types'    => '',
				'custom_post_types'        => array(),
				'hide_widget_if'           => '',
				'carousel_enabled'         => '',
				'slides_to_scroll'         => '1',
				'arrows'                   => 'true',
				'arrow_icon'               => 'fa fa-angle-left',
				'dots'                     => '',
				'autoplay'                 => 'true',
				'pause_on_hover'           => 'true',
				'autoplay_speed'           => 5000,
				'infinite'                 => 'true',
				'center_mode'              => '',
				'effect'                   => 'slide',
				'speed'                    => 500,
				'inject_alternative_items' => '',
				'injection_items'          => array(),
				'scroll_slider_enabled'    => '',
				'scroll_slider_on'         => array( 'desktop', 'tablet', 'mobile' ),
				'custom_query'             => false,
				'custom_query_id'          => null,
				'_element_id'              => '',
			) );
		}

		public function render() {

			$this->setup_listing_props();

			if ( ! $this->listing_id || ! get_post( $this->listing_id ) ) {
				$this->print_no_listing_notice();
				return;
			}

			$this->render_posts();
			jet_engine()->frontend->frontend_scripts();

		}

		public function setup_listing_props() {
			$settings               = $this->get_settings();
			$listing_id             = absint( $settings['lisitng_id'] );
			$this->listing_id       = $listing_id;
			$this->listing_query_id = \Jet_Engine\Query_Builder\Manager::instance()->listings->get_query_id(
				$listing_id,
				$settings
			);
		}

		/**
		 * Build query arguments array based on settings
		 *
		 * @param  array $settings
		 * @return array
		 */
		public function build_posts_query_args_array( $settings = array() ) {

			$post_type   = jet_engine()->listings->data->get_listing_post_type();
			$per_page    = $this->get_posts_num( $settings );
			$post_status = ! empty( $settings['post_status'] ) ? $settings['post_status'] : 'publish';

			$args = array(
				'post_status'         => $post_status,
				'post_type'           => $post_type,
				'posts_per_page'      => $per_page,
				'paged'               => ! empty( $settings['current_page'] ) ? absint( $settings['current_page'] ) : 1,
				'ignore_sticky_posts' => true,
			);

			if ( jet_engine()->listings->legacy->is_disabled() ) {
				return apply_filters( 'jet-engine/listing/grid/posts-query-args', $args, $this, $settings );
			}

			$use_custom_post_types = ! empty( $settings['use_custom_post_types'] ) ? $settings['use_custom_post_types'] : false;
			$use_custom_post_types = filter_var( $use_custom_post_types, FILTER_VALIDATE_BOOLEAN );

			if ( $use_custom_post_types && ! empty( $settings['custom_post_types'] ) ) {
				$args['post_type'] = $settings['custom_post_types'];
			}

			if ( ! empty( $settings['posts_query'] ) ) {

				foreach ( $settings['posts_query'] as $query_item ) {

					if ( empty( $query_item['type'] ) ) {
						continue;
					}

					$meta_index = 0;
					$tax_index  = 0;

					switch ( $query_item['type'] ) {

						case 'posts_params':
							$args = $this->add_posts_params_to_args( $args, $query_item );
							break;

						case 'order_offset':
							$args = $this->add_order_offset_to_args( $args, $query_item );
							break;

						case 'tax_query':
							$args = $this->add_tax_query_to_args( $args, $query_item );
							break;

						case 'meta_query':
							$args = $this->add_meta_query_to_args( $args, $query_item );
							break;

						case 'date_query':
							$args = $this->add_date_query_to_args( $args, $query_item );
							break;

					}

				}
			}

			// Custom query arguments passed in JSON format
			if ( ! empty( $settings['custom_posts_query'] ) ) {
				$custom_args = json_decode( $settings['custom_posts_query'], true );
				$args        = wp_parse_args( $custom_args, $args );
			}

			if ( ! empty( $args['tax_query'] ) && ( 1 < count( $args['tax_query'] ) ) ) {
				$relation = ! empty( $settings['tax_query_relation'] ) ? $settings['tax_query_relation'] : 'AND';
				$args['tax_query']['relation'] = $relation;
			}

			if ( ! empty( $args['meta_query'] ) && ( 1 < count( $args['meta_query'] ) ) ) {
				$relation = ! empty( $settings['meta_query_relation'] ) ? $settings['meta_query_relation'] : 'AND';
				$args['meta_query']['relation'] = $relation;
			}

			array_walk( $args, array( $this, 'apply_macros_in_query' ) );

			return apply_filters( 'jet-engine/listing/grid/posts-query-args', $args, $this, $settings );

		}

		/**
		 * Apply macros in query callback
		 *
		 * @param  mixed &$item
		 * @return void
		 */
		public function apply_macros_in_query( &$item ) {
			if ( ! is_array( $item ) ) {
				$item = jet_engine()->listings->macros->do_macros( $item );
			}
		}

		/**
		 * Build terms query arguments array based on settings
		 *
		 * @param  array $settings
		 * @return array
		 */
		public function build_terms_query_args_array( $settings = array() ) {

			$tax    = jet_engine()->listings->data->get_listing_tax();
			$number = $this->get_posts_num( $settings );

			$args = array(
				'taxonomy' => $tax,
				'number'   => $number,
			);

			if ( jet_engine()->listings->legacy->is_disabled() ) {
				return apply_filters( 'jet-engine/listing/grid/posts-query-args', $args, $this, $settings );
			}

			$keys = array(
				'terms_orderby',
				'terms_order',
				'terms_offset',
				'terms_child_of',
				'terms_parent',
			);

			foreach ( $keys as $key ) {
				$setting = $settings[ $key ] ?? '';

				if ( Jet_Engine_Tools::is_empty( $setting ) ) {
					continue;
				}

				$args[ str_replace( 'terms_', '', $key ) ] = esc_attr( $setting );

			}

			if ( ! empty( $settings['terms_object_ids'] ) ) {

				$ids = jet_engine()->listings->macros->do_macros( $settings['terms_object_ids'], $tax );
				$ids = $this->explode_string( $ids );

				if ( 1 === count( $ids ) ) {
					$args['object_ids'] = $ids[0];
				} else {
					$args['object_ids'] = $ids;
				}

			}

			if ( ! empty( $settings['terms_hide_empty'] ) && 'true' === $settings['terms_hide_empty'] ) {
				$args['hide_empty'] = true;
			} else {
				$args['hide_empty'] = false;
			}

			if ( ! empty( $settings['terms_meta_query'] ) ) {
				foreach ( $settings['terms_meta_query'] as $query_item ) {
					$args = $this->add_meta_query_to_args( $args, $query_item );
				}
			}

			if ( ! empty( $args['meta_query'] ) && ( 1 < count( $args['meta_query'] ) ) ) {
				$rel = ! empty( $settings['term_meta_query_relation'] ) ? $settings['term_meta_query_relation'] : 'AND';
				$args['meta_query']['relation'] = $rel;
			}

			array_walk( $args, array( $this, 'apply_macros_in_query' ) );

			foreach ( array( 'terms_include', 'terms_exclude' ) as $key ) {
				$setting = $settings[ $key ] ?? '';

				$ids = jet_engine()->listings->macros->do_macros( $setting, $tax );
				$ids = $this->explode_string( $ids );
				$arg = str_replace( 'terms_', '', $key );

				if ( 1 === count( $ids ) ) {
					$args[ $arg ] = $ids[0];
				} else {
					$args[ $arg ] = $ids;
				}
			}

			return apply_filters( 'jet-engine/listing/grid/terms-query-args', $args, $this, $settings );

		}

		/**
		 * Builder users query arguments array by widget settings
		 *
		 * @param  array $settings
		 * @return array
		 */
		public function build_users_query_args_array( $settings ) {

			$number = $this->get_posts_num( $settings );

			$args = array(
				'_query_type' => 'users',
				'number'      => $number,
			);

			if ( jet_engine()->listings->legacy->is_disabled() ) {
				return apply_filters( 'jet-engine/listing/grid/posts-query-args', $args, $this, $settings );
			}

			if ( ! empty( $settings['users_meta_query'] ) ) {
				foreach ( $settings['users_meta_query'] as $query_item ) {
					$args = $this->add_meta_query_to_args( $args, $query_item );
				}
			}

			if ( ! empty( $args['meta_query'] ) && ( 1 < count( $args['meta_query'] ) ) ) {
				$rel = ! empty( $settings['users_meta_query_relation'] ) ? $settings['users_meta_query_relation'] : 'AND';
				$args['meta_query']['relation'] = $rel;
			}

			foreach ( array( 'users_role__in', 'users_role__not_in' ) as $key ) {
				$roles = ! empty( $settings[ $key ] ) ? $settings[ $key ] : array();
				$arg   = str_replace( 'users_', '', $key );

				if ( ! empty( $roles ) ) {
					$args[ $arg ] = $roles;
				}
			}

			foreach ( array( 'users_include', 'users_exclude' ) as $key ) {

				$ids = ! empty( $settings[ $key ] ) ? $settings[ $key ] : '';
				$ids = jet_engine()->listings->macros->do_macros( $ids );
				$ids = $this->explode_string( $ids );
				$arg = str_replace( 'users_', '', $key );

				if ( 1 === count( $ids ) ) {
					$args[ $arg ] = $ids[0];
				} else {
					$args[ $arg ] = $ids;
				}
			}

			if ( ! empty( $settings['users_search_query'] ) ) {

				$args['search'] = sprintf( '*%s*', $settings['users_search_query'] );

				if ( ! empty( $settings['users_search_columns'] ) ) {
					$args['search_columns'] = $settings['users_search_columns'];
				}

			}

			array_walk( $args, array( $this, 'apply_macros_in_query' ) );

			return apply_filters( 'jet-engine/listing/grid/users-query-args', $args, $this );

		}

		/**
		 * Add post parameters to arguments
		 *
		 * @param  array $args
		 * @param  array $settings
		 * @return array
		 */
		public function add_posts_params_to_args( $args, $settings ) {

			$post_args = array(
				'posts_in'     => isset( $settings['posts_in'] ) ? $settings['posts_in'] : '',
				'posts_not_in' => isset( $settings['posts_not_in'] ) ? $settings['posts_not_in'] : '',
				'posts_parent' => isset( $settings['posts_parent'] ) ? $settings['posts_parent'] : '',
				'search_query' => isset( $settings['search_query'] ) ? $settings['search_query'] : '',
			);

			array_walk( $post_args, array( $this, 'apply_macros_in_query' ) );

			if ( isset( $post_args['posts_in'] ) && '' !== $post_args['posts_in'] ) {
				$args['post__in'] = $this->explode_string( $post_args['posts_in'], true );
			}

			if ( ! empty( $post_args['posts_not_in'] ) ) {
				$args['post__not_in'] = $this->explode_string( $post_args['posts_not_in'] );
			}

			if ( ! empty( $post_args['posts_parent'] ) ) {
				$parent = $this->explode_string( $post_args['posts_parent'] );

				if ( 1 === count( $parent ) ) {
					$args['post_parent'] = $parent[0];
				} else {
					$args['post_parent__in'] = $parent;
				}

			}

			if ( ! empty( $post_args['search_query'] ) ) {
				$args['s'] = $post_args['search_query'];
			}

			if ( ! empty( $settings['posts_status'] ) ) {
				$args['post_status'] = esc_attr( $settings['posts_status'] );
			}

			if ( ! empty( $settings['posts_author'] ) && 'any' !== $settings['posts_author'] ) {
				if ( 'current' === $settings['posts_author'] && is_user_logged_in() ) {
					$args['author'] = get_current_user_id();
				} elseif ( 'id' === $settings['posts_author'] && ! empty( $settings['posts_author_id'] ) ) {
					$args['author'] = $settings['posts_author_id'];
				} elseif( 'queried' === $settings['posts_author'] ) {

					$u_id = false;

					if ( is_author() ) {
						$u_id = get_queried_object_id();
					} elseif ( jet_engine()->modules->is_module_active( 'profile-builder' ) ) {
						$u_id = \Jet_Engine\Modules\Profile_Builder\Module::instance()->query->get_queried_user_id();
					}

					if ( ! $u_id ) {
						$u_id = get_current_user_id();
					}

					$args['author'] = $u_id;
				}
			}

			return $args;

		}

		/**
		 * Process multiple orderby parameters
		 *
		 * @param  array $args
		 * @param  array $settings
		 * @return array
		 */
		public function process_multiple_orderby( $args, $settings ) {

			if ( ! is_array( $args['orderby'] ) ) {

				$initial_orderby = $args['orderby'];
				$initial_order = ! empty( $args['order'] ) ? $args['order'] : 'DESC';

				if ( ! empty( $args['order'] ) ) {
					unset( $args['order'] );
				}

				if ( in_array( $initial_orderby, array( 'meta_value', 'meta_value_num' ) ) ) {
					$initial_orderby = $args['meta_key'];
				}

				$args['orderby'] = array(
					$initial_orderby => $initial_order,
				);

			}

			$order_by = ! empty( $settings['order_by'] ) ? esc_attr( $settings['order_by'] ) : 'date';
			$order    = ! empty( $settings['order'] ) ? esc_attr( $settings['order'] ) : 'DESC';

			if ( 'meta_value' === $order_by ) {
				$order_by  = ! empty( $settings['meta_key'] ) ? esc_attr( $settings['meta_key'] ) : $order_by;
			} elseif ( 'meta_clause' === $order_by ) {
				$order_by = ! empty( $settings['meta_clause_key'] ) ? esc_attr( $settings['meta_clause_key'] ) : '';
			} elseif ( 'rand' === $order_by ) {
				$order_by = sprintf( 'RAND(%s)', rand() );
			}

			if ( $order_by ) {
				$args['orderby'][ $order_by ] = $order;
			}

			return $args;

		}

		/**
		 * Add order and offset parameters to arguments
		 *
		 * @param  array $args
		 * @param  array $settings
		 * @return array
		 */
		public function add_order_offset_to_args( $args, $settings ) {

			if ( ! empty( $settings['offset'] ) ) {
				$args['offset'] = absint( $settings['offset'] );
			}

			if ( ! empty( $args['orderby'] ) ) {
				return $this->process_multiple_orderby( $args, $settings );
			}

			if ( ! empty( $settings['order'] ) ) {
				$args['order'] = esc_attr( $settings['order'] );
			}

			$order_by = ! empty( $settings['order_by'] ) ? esc_attr( $settings['order_by'] ) : 'date';

			if ( 'meta_value' === $order_by ) {

				$meta_key  = ! empty( $settings['meta_key'] ) ? esc_attr( $settings['meta_key'] ) : 'CHAR';
				$meta_type = ! empty( $settings['meta_type'] ) ? esc_attr( $settings['meta_type'] ) : 'CHAR';

				if ( 'CHAR' === $meta_type ) {
					$args['orderby']  = $order_by;
					$args['meta_key'] = $meta_key;
				} else {
					$args['orderby']   = 'meta_value_num';
					$args['meta_key']  = $meta_key;
					$args['meta_type'] = $meta_type;
				}

			} elseif ( 'meta_clause' === $order_by ) {

				$clause = ! empty( $settings['meta_clause_key'] ) ? esc_attr( $settings['meta_clause_key'] ) : '';

				if ( $clause ) {
					$args['orderby'] = $clause;
				}

			} elseif ( 'rand' === $order_by ) {
				$args['orderby'] = sprintf( 'RAND(%s)', rand() );
			} else {
				$args['orderby'] = $order_by;
			}

			return $args;

		}

		/**
		 * Add tax query parameters to arguments
		 *
		 * @param  array $args
		 * @param  array $settings
		 * @return array
		 */
		public function add_tax_query_to_args( $args, $settings ) {

			$taxonomy = '';

			if ( ! empty( $settings['tax_query_taxonomy_meta'] ) ) {
				$taxonomy = get_post_meta( get_the_ID(), esc_attr( $settings['tax_query_taxonomy_meta'] ), true );
			} else {
				$taxonomy = ! empty( $settings['tax_query_taxonomy'] ) ? esc_attr( $settings['tax_query_taxonomy'] ) : '';
			}

			$settings = apply_filters( 'jet-engine/listing/grid/tax-query-item-settings', $settings, $args, $this );

			if ( ! $taxonomy ) {
				return $args;
			}

			if ( empty( $args['tax_query'] ) ) {
				$args['tax_query'] = array();
			}

			$compare = ! empty( $settings['tax_query_compare'] ) ? esc_attr( $settings['tax_query_compare'] ) : 'IN';
			$field   = ! empty( $settings['tax_query_field'] ) ? esc_attr( $settings['tax_query_field'] ) : 'IN';

			$terms = '';

			if ( ! empty( $settings['tax_query_terms_meta'] ) ) {
				$terms = get_post_meta( get_the_ID(), esc_attr( $settings['tax_query_terms_meta'] ), true );
			} else {

				$terms = ! empty( $settings['tax_query_terms'] ) ? esc_attr( $settings['tax_query_terms'] ) : '';
				$terms = jet_engine()->listings->macros->do_macros( $terms, $taxonomy );
				$terms = $this->explode_string( $terms );

			}

			if ( ! empty( $terms ) && ! in_array( $compare, array( 'NOT EXISTS', 'EXISTS' ) ) ) {
				$args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'field'    => $field,
					'terms'    => $terms,
					'operator' => $compare,
				);
			} elseif ( in_array( $compare, array( 'NOT EXISTS', 'EXISTS' ) ) ) {
				$args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'operator' => $compare,
				);
			}

			return $args;

		}

		/**
		 * Add meta query parameters to arguments
		 *
		 * @param  array $args
		 * @param  array $settings
		 * @return array
		 */
		public function add_meta_query_to_args( $args, $settings ) {

			$key = ! empty( $settings['meta_query_key'] ) ? esc_attr( $settings['meta_query_key'] ) : '';

			if ( ! $key ) {
				return $args;
			}

			$type    = ! empty( $settings['meta_query_type'] ) ? esc_attr( $settings['meta_query_type'] ) : 'CHAR';
			$compare = ! empty( $settings['meta_query_compare'] ) ? $settings['meta_query_compare'] : '=';
			$value   = isset( $settings['meta_query_val'] ) ? $settings['meta_query_val'] : '';

			if ( ! empty( $settings['meta_query_request_val'] ) ) {

				$query_var = $settings['meta_query_request_val'];

				if ( isset( $_GET[ $query_var ] ) ) {
					$request_val = $_GET[ $query_var ];
				} else {
					$request_val = get_query_var( $query_var );
				}

				if ( $request_val ) {
					$value = $request_val;
				}

			}

			$value = jet_engine()->listings->macros->do_macros( $value, $key );

			if ( in_array( $compare, array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ) ) ) {
				$value = $this->explode_string( $value );
			}

			if ( in_array( $type, array( 'DATE', 'DATETIME' ) ) ) {

				if ( is_array( $value ) ) {
					$value = array_map( 'strtotime', $value );
				} else {
					$value = strtotime( $value );
				}

				$type = 'NUMERIC';

			}

			$row = array(
				'key'     => $key,
				'value'   => $value,
				'compare' => $compare,
				'type'    => $type,
			);

			if ( in_array( $compare, array( 'EXISTS', 'NOT EXISTS' ) ) ) {
				unset( $row['value'] );
			}

			if ( ! empty( $settings['meta_query_clause'] ) ) {
				$clause = esc_attr( $settings['meta_query_clause'] );
				$args['meta_query'][ $clause ] = $row;
			} else {
				$args['meta_query'][] = $row;
			}

			return $args;

		}

		/**
		 * Add date query parameters to args.
		 *
		 * @param  array $args
		 * @param  array $settings
		 * @return array
		 */
		public function add_date_query_to_args( $args, $settings ) {

			$column    = isset( $settings['date_query_column'] ) ? $settings['date_query_column'] : 'post_date';
			$after     = isset( $settings['date_query_after'] ) ? $settings['date_query_after'] : '';
			$before    = isset( $settings['date_query_before'] ) ? $settings['date_query_before'] : '';
			$after     = jet_engine()->listings->macros->do_macros( $after );
			$before    = jet_engine()->listings->macros->do_macros( $before );

			$args['date_query'][] = array(
				'column'    => $column,
				'after'     => $after,
				'before'    => $before,
			);

			return $args;

		}

		/**
		 * Explode string to array
		 *
		 * @param  string $string
		 * @return mixed
		 */
		public function explode_string( $string = '', $unfiltered = false ) {

			if ( is_array( $string ) ) {
				return $string;
			}

			$array = explode( ',', $string );

			if ( empty( $array ) ) {
				return array();
			}

			if ( $unfiltered ) {
				return array_map( 'trim', $array );
			} else {
				return array_filter( array_map( 'trim', $array ) );
			}

		}

		public function get_default_query( $wp_query ) {

			// Ensure jet-engine/listing/grid/posts-query-args hook correctly fires even for archive (For filters compat)
			$default_query = array(
				'post_status'    => 'publish',
				'found_posts'    => $wp_query->found_posts,
				'max_num_pages'  => $wp_query->max_num_pages,
				'post_type'      => $wp_query->get( 'post_type' ),
				'tax_query'      => $wp_query->get( 'tax_query' ),
				'orderby'        => $wp_query->get( 'orderby' ),
				'order'          => $wp_query->get( 'order' ),
				'paged'          => $wp_query->get( 'paged' ),
				'posts_per_page' => $wp_query->get( 'posts_per_page' ),
			);

			if ( is_object( $wp_query->tax_query ) ) {
				$default_query['tax_query'] = $wp_query->tax_query->queries;
			}

			$author = $wp_query->get( 'author' );

			if ( $author ) {
				$default_query['author'] = $author;
			}

			if ( $wp_query->get( 'taxonomy' ) ) {
				$default_query['taxonomy'] = $wp_query->get( 'taxonomy' );
				$default_query['term']     = $wp_query->get( 'term' );
			}

			if ( $wp_query->get( 's' ) ) {
				$default_query['s'] = $wp_query->get( 's' );
			}

			return $default_query;

		}

		/**
		 * Get posts
		 *
		 * @param  array $settings
		 * @return array
		 */
		public function get_posts( $settings ) {

			if ( isset( $settings['is_archive_template'] ) && filter_var( $settings['is_archive_template'], FILTER_VALIDATE_BOOLEAN ) ) {

				global $wp_query;

				$default_query = $this->get_default_query( $wp_query );
				$default_query = apply_filters( 'jet-engine/listing/grid/posts-query-args', $default_query, $this, $settings );

				$this->query_vars['page']    = $wp_query->get( 'paged' ) ? $wp_query->get( 'paged' ) : 1;
				$this->query_vars['pages']   = $wp_query->max_num_pages;
				$this->query_vars['request'] = $default_query;

				$this->posts_query = $wp_query;

				return $wp_query->posts;

			} else {

				$args  = $this->build_posts_query_args_array( $settings );
				$query = new \WP_Query( $args );

				$this->posts_query = $query;

				$this->query_vars['page']    = $query->get( 'paged' ) ? $query->get( 'paged' ) : 1;
				$this->query_vars['pages']   = $query->max_num_pages;
				$this->query_vars['request'] = $args;

				return $query->posts;
			}

		}

		/**
		 * Get terms list
		 *
		 * @param  array $settings
		 * @return array
		 */
		public function get_terms( $settings ) {

			$args = $this->build_terms_query_args_array( $settings );

			$this->query_vars['request'] = $args;

			if ( ! empty( $settings['use_load_more'] ) ) {
				$taxonomy                  = jet_engine()->listings->data->get_listing_tax();
				$total                     = wp_count_terms( $taxonomy, $args );
				$per_page                  = $this->get_posts_num( $settings );
				$pages                     = ceil( $total / $per_page );
				$page                      = 1;
				$this->query_vars['page']  = $page;
				$this->query_vars['pages'] = $pages;
			} else {
				$this->query_vars['page']  = 1;
				$this->query_vars['pages'] = 1;
			}

			$terms = get_terms( $args );

			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				$terms = array();
			}

			return $terms;

		}

		/**
		 * Check widget visibility settings and hide if false
		 *
		 * @param  array  $query    Query array.
		 * @param  array  $settings Settings array.
		 * @return boolean
		 */
		public function is_widget_visible( $query, $settings ) {

			if ( ! empty( $settings['hide_widget_if'] ) ) {

				switch ( $settings['hide_widget_if'] ) {

					case 'empty_query':

						return empty( $query ) ? false : true;

						break;

					default:

						if ( is_callable( $settings['hide_widget_if'] ) ) {
							return call_user_func( $settings['hide_widget_if'], $query, $settings );
						} else {
							return apply_filters( 'jet-engine/listing/grid/widget-visibility', true, $query, $settings );
						}

						break;
				}

			}

			return true;

		}

		public function maybe_prevent_recursion( $settings ) {

			if ( ! empty( $_REQUEST['post'] ) && $_REQUEST['post'] == $settings['lisitng_id'] ) {
				return true;
			}

			if ( ! empty( $_REQUEST['post_id'] ) && $_REQUEST['post_id'] == $settings['lisitng_id'] ) {
				return true;
			}

			if ( ! empty( $_REQUEST['editor_post_id'] ) && $_REQUEST['editor_post_id'] == $settings['lisitng_id'] ) {
				return true;
			}

			if ( in_array( $settings['lisitng_id'], self::$did_listings ) ) {
				return true;
			}

			return false;
		}

		public function get_query( $settings ) {

			$listing_source = apply_filters(
				'jet-engine/listing/grid/source',
				jet_engine()->listings->data->get_listing_source(),
				$settings,
				$this
			);

			switch ( $listing_source ) {

				case 'posts':
					$query = $this->get_posts( $settings );
					break;

				case 'terms':
					$query = $this->get_terms( $settings );
					break;

				case 'users':
					$query = $this->get_users( $settings );
					break;

				case 'repeater':
					$query = $this->get_repeater_items( $settings );
					break;

				default:
					$query = apply_filters(
						'jet-engine/listing/grid/query/' . $listing_source,
						array(),
						$settings,
						$this
					);

					break;
			}

			return $query;
		}

		/**
		 * Print no listing notice
		 * This way will not work in blocks editor, so for block editor render plain notice without link,
		 * in the other cases - with a linke to create new listing
		 *
		 * @return void
		 */
		public function print_no_listing_notice() {

			$notice = __( 'Please select listing to show.', 'jet-engine' );
			printf( '<div class="jet-listing-notice">%1$s</div>', $notice );

		}

		/**
		 * Render grid posts
		 *
		 * @return void
		 */
		public function render_posts() {

			$settings   = $this->get_settings();
			$listing_id = absint( $settings['lisitng_id'] );

			if ( ! $listing_id ) {
				$this->print_no_listing_notice();
				return;
			}

			$view_type = jet_engine()->listings->data->get_listing_type( $listing_id );

			jet_engine()->admin_bar->register_item( 'edit_post_' . $listing_id, array(
				'title'     => get_the_title( $listing_id ),
				'sub_title' => jet_engine()->admin_bar->get_post_type_label( jet_engine()->post_type->slug() ),
				'href'      => jet_engine()->post_type->admin_screen->get_edit_url( $view_type, $listing_id ),
			) );

			if ( $this->maybe_prevent_recursion( $settings ) ) {
				printf( '<div class="jet-listing-notice">%s</div>', __( 'Please select another listing to show to avoid recursion.', 'jet-engine' ) );
				return;
			}

			if ( $this->is_lazy_load_enabled( $settings ) ) {
				$this->print_lazy_load_wrapper( $settings );
				return;
			}

			$current_listing = jet_engine()->listings->data->get_listing();

			jet_engine()->listings->data->set_listing_by_id( $listing_id );

			$query = $this->get_query( $settings );

			if ( ! $this->is_widget_visible( $query, $settings ) ) {
				jet_engine()->listings->data->set_listing( $current_listing );
				return;
			}

			$did_listings = self::$did_listings;
			self::$did_listings[] = $listing_id;

			$current_object = jet_engine()->listings->data->get_current_object();

			$this->posts_template( $query, $settings );

			//jet_engine()->listings->data->reset_listing();

			// Need when several listings into a listing item
			jet_engine()->listings->data->set_current_object( $current_object );
			jet_engine()->listings->data->set_listing( $current_listing );

			self::$did_listings = $did_listings;
		}

		/**
		 * Is the Lazy Load enabled.
		 *
		 * @param  array $settings
		 * @return bool
		 */
		public function is_lazy_load_enabled( $settings ) {

			$result = ! empty( $settings['lazy_load'] ) ? filter_var( $settings['lazy_load'], FILTER_VALIDATE_BOOLEAN ) : false;

			return apply_filters( 'jet-engine/listing/grid/is_lazy_load', $result, $settings );
		}

		/**
		 * Print Lazy Load wrapper.
		 *
		 * @param  array $settings Settings array
		 * @return void
		 */
		public function print_lazy_load_wrapper( $settings ) {

			$base_class = $this->get_name();

			$this->enqueue_assets( $settings );

			if ( ! empty( $settings['lazy_load_offset'] ) && is_array( $settings['lazy_load_offset'] ) ) {
				$size = ! empty( $settings['lazy_load_offset']['size'] ) ? $settings['lazy_load_offset']['size'] : '0';
				$unit = ! empty( $settings['lazy_load_offset']['unit'] ) ? $settings['lazy_load_offset']['unit'] : 'px';

				$offset = $size . $unit;
			} elseif ( ! empty( $settings['lazy_load_offset'] ) ) {
				$offset = absint( $settings['lazy_load_offset'] ) . 'px';
			} else {
				$offset = '0px';
			}

			$post_id = get_the_ID();

			if ( jet_engine()->has_elementor() ) {
				if ( isset( Elementor\Plugin::$instance->documents ) && Elementor\Plugin::$instance->documents->get_current() ) {
					$post_id = Elementor\Plugin::$instance->documents->get_current()->get_main_id();
				}
			}

			$post_id = apply_filters( 'jet-engine/listing/grid/lazy-load/post-id', $post_id );

			$options = array(
				'offset'  => $offset,
				'post_id' => $post_id,
			);

			$queried_id = $this->get_queried_id();

			if ( $queried_id ) {
				$options['queried_id'] = $queried_id;
			}

			/*
			This code not needed anymore with new ajax listing url.
			if ( ( is_home() || is_archive() || is_search() ) && ! empty( $settings['is_archive_template'] ) ) {
				global $wp_query;
				$default_query = $this->get_default_query( $wp_query );
				$default_query = apply_filters( 'jet-engine/listing/grid/posts-query-args', $default_query, $this, $settings );
				$options['query'] = $default_query;
			}
			*/

			$options = apply_filters( 'jet-engine/listing/grid/lazy-load/options', $options, $settings );

			printf(
				'<div class="%1$s %1$s--lazy-load jet-listing jet-listing-grid-loading" data-lazy-load="%2$s">%3$s</div>',
				$base_class, htmlspecialchars( json_encode( $options ) ), $this->get_loader_html()
			);

		}

		/**
		 * Ensure current object is properly set in the edit context of blocks editor
		 *
		 * @return [type] [description]
		 */
		public function ensure_current_object_for_block_editor() {

			if ( empty( $_GET['context'] ) || 'edit' !== $_GET['context'] ) {
				return;
			}

			if ( empty( $_GET['post_id'] ) ) {
				return;
			}

			jet_engine()->listings->data->set_current_object( get_post( absint( $_GET['post_id'] ) ) );

		}

		/**
		 * Returns repeater items
		 *
		 * @param  [type] $settings [description]
		 * @return [type]           [description]
		 */
		public function get_repeater_items( $settings ) {

			$this->ensure_current_object_for_block_editor();

			$query           = array();
			$listing         = jet_engine()->listings->data->get_listing();
			$repeater_source = $listing->get_settings( 'repeater_source' );
			$repeater_field  = $listing->get_settings( 'repeater_field' );
			$repeater_option = $listing->get_settings( 'repeater_option' );
			$current_object  = jet_engine()->listings->data->get_current_object();
			$meta_value      = false;

			if ( 'jet_engine_options' !== $repeater_source && ( ! $current_object || ! jet_engine()->listings->data->get_current_object_id() ) ) {
				return $query;
			}

			switch ( $repeater_source ) {

				case 'jet_engine_options':

					if ( ! $repeater_option ) {
						return $query;
					} else {
						$meta_value = jet_engine()->listings->data->get_option( $repeater_option );
					}
					break;

				default:
					$meta_value = get_post_meta( jet_engine()->listings->data->get_current_object_id(), $repeater_field, true );
					break;

			}

			if ( empty( $meta_value ) ) {
				return $query;
			}

			if ( 'acf' === $repeater_source ) {
				$count = $meta_value;
			} else {
				$count = is_array( $meta_value ) ? count( $meta_value ) : 0;
			}

			$query = array_fill( 0, $count, $current_object );

			$this->query_vars['page']    = 1;
			$this->query_vars['pages']   = 1;
			$this->query_vars['request'] = false;

			return $query;

		}

		/**
		 * Query users
		 *
		 * @param  array $settings
		 * @return array
		 */
		public function get_users( $settings ) {

			$args = $this->build_users_query_args_array( $settings );

			$args['count_total'] = ! empty( $settings['use_load_more'] ) ? true : false;

			$args = apply_filters( 'jet-engine/listing/grid/posts-query-args', $args, $this, $settings );

			$user_query = new \WP_User_Query( $args );

			if ( $args['count_total'] ) {

				$total    = $user_query->get_total();
				$per_page = $this->get_posts_num( $settings );
				$offset   = ! empty( $settings['users_offset'] ) ? absint( $settings['users_offset'] ) : 0;
				$pages    = ceil( $total / $per_page );
				$page     = floor( $offset / $per_page ) + 1;

				$this->query_vars['page']    = $page;
				$this->query_vars['pages']   = $pages;
				$this->query_vars['request'] = $args;

			} else {
				$this->query_vars['page']    = 1;
				$this->query_vars['pages']   = 1;
				$this->query_vars['request'] = $args;
			}

			$users = (array) $user_query->get_results();

			return apply_filters( 'jet-engine/listing/grid/users-query-results', $users, $user_query, $this );

		}

		/**
		 * Returns navigation data settings string
		 *
		 * @param  array $settings
		 * @return string
		 */
		public function get_nav_settings( $settings ) {

			$columns = $this->get_columns_settings( $settings );

			$result = array(
				'enabled'         => false,
				'type'            => null,
				'more_el'         => null,
				'query'           => array(),
				'widget_settings' => array(
					'lisitng_id'               => ! empty( $settings['lisitng_id'] ) ? absint( $settings['lisitng_id'] ) : '',
					'posts_num'                => $this->get_posts_num( $settings ),
					'columns'                  => $columns['desktop'],
					'columns_tablet'           => $columns['tablet'],
					'columns_mobile'           => $columns['mobile'],
					'is_archive_template'      => ! empty( $settings['is_archive_template'] ) ? $settings['is_archive_template'] : '',
					'post_status'              => ! empty( $settings['post_status'] ) ? $settings['post_status'] : array( 'publish' ),
					'use_random_posts_num'     => ! empty( $settings['use_random_posts_num'] ) ? $settings['use_random_posts_num'] : '',
					'max_posts_num'            => ! empty( $settings['max_posts_num'] ) ? $settings['max_posts_num'] : 9,
					'not_found_message'        => ! empty( $settings['not_found_message'] ) ? $settings['not_found_message'] : __( 'No data was found', 'jet-engine' ),
					'is_masonry'               => $this->is_masonry_enabled( $settings ),
					'equal_columns_height'     => ! empty( $settings['equal_columns_height'] ) ? $settings['equal_columns_height'] : '',
					'use_load_more'            => ! empty( $settings['use_load_more'] ) ? $settings['use_load_more'] : '',
					'load_more_id'             => ! empty( $settings['load_more_id'] ) ? $settings['load_more_id'] : '',
					'load_more_type'           => ! empty( $settings['load_more_type'] ) ? $settings['load_more_type'] : 'click',
					'load_more_offset'         => ! empty( $settings['load_more_offset'] ) ? $settings['load_more_offset'] : null,
					'use_custom_post_types'    => ! empty( $settings['use_custom_post_types'] ) ? $settings['use_custom_post_types'] : '',
					'custom_post_types'        => ! empty( $settings['custom_post_types'] ) ? $settings['custom_post_types'] : array(),
					'hide_widget_if'           => ! empty( $settings['hide_widget_if'] ) ? $settings['hide_widget_if'] : '',
					'carousel_enabled'         => ! empty( $settings['carousel_enabled'] ) ? $settings['carousel_enabled'] : '',
					'slides_to_scroll'         => ! empty( $settings['slides_to_scroll'] ) ? $settings['slides_to_scroll'] : '1',
					'arrows'                   => ! empty( $settings['arrows'] ) ? $settings['arrows'] : 'true',
					'arrow_icon'               => ! empty( $settings['arrow_icon'] ) ? $settings['arrow_icon'] : 'fa fa-angle-left',
					'dots'                     => ! empty( $settings['dots'] ) ? $settings['dots'] : '',
					'autoplay'                 => ! empty( $settings['autoplay'] ) ? $settings['autoplay'] : 'true',
					'pause_on_hover'           => ! empty( $settings['pause_on_hover'] ) ? $settings['pause_on_hover'] : 'true',
					'autoplay_speed'           => ! empty( $settings['autoplay_speed'] ) ? $settings['autoplay_speed'] : 5000,
					'infinite'                 => ! empty( $settings['infinite'] ) ? $settings['infinite'] : 'true',
					'center_mode'              => ! empty( $settings['center_mode'] ) ? $settings['center_mode'] : '',
					'effect'                   => ! empty( $settings['effect'] ) ? $settings['effect'] : 'slide',
					'speed'                    => ! empty( $settings['speed'] ) ? $settings['speed'] : 500,
					'inject_alternative_items' => ! empty( $settings['inject_alternative_items'] ) ? $settings['inject_alternative_items'] : '',
					'injection_items'          => ! empty( $settings['injection_items'] ) ? $settings['injection_items'] : array(),
					'scroll_slider_enabled'    => ! empty( $settings['scroll_slider_enabled'] ) ? $settings['scroll_slider_enabled'] : '',
					'scroll_slider_on'         => ! empty( $settings['scroll_slider_on'] ) ? $settings['scroll_slider_on'] : array(),
					'custom_query'             => ! empty( $settings['custom_query'] ) ? $settings['custom_query'] : false,
					'custom_query_id'          => ! empty( $settings['custom_query_id'] ) ? $settings['custom_query_id'] : '',
					'_element_id'              => ! empty( $settings['_element_id'] ) ? $settings['_element_id'] : '',
				),
			);

			$has_load_more  = ! empty( $settings['use_load_more'] );
			$add_query_data = apply_filters( 'jet-engine/listing/grid/add-query-data', $has_load_more, $this );

			if ( $add_query_data ) {
				$result['query']           = $this->query_vars['request'];
				$result['widget_settings'] = apply_filters(
					'jet-engine/listing/grid/nav-widget-settings',
					$result['widget_settings'],
					$settings,
					$this
				);
			}

			if ( $has_load_more ) {
				$result['enabled'] = true;
				$result['type']    = ! empty( $settings['load_more_type'] ) ? $settings['load_more_type'] : 'click';
				$result['more_el'] = ! empty( $settings['load_more_id'] ) ? '#' . trim( $settings['load_more_id'], '#' ) : null;
			}

			return htmlspecialchars( json_encode( $result ) );

		}

		/**
		 * Render posts template.
		 * Moved to separate function to be rewritten by other layouts
		 *
		 * @param  array  $query    Query array.
		 * @param  array  $settings Settings array.
		 * @return void
		 */
		public function posts_template( $query, $settings ) {

			$base_class  = $this->get_name();
			$columns     = $this->get_columns_settings( $settings );
			$desktop_col = esc_attr( $columns['desktop'] );
			$tablet_col  = esc_attr( $columns['tablet'] );
			$mobile_col  = esc_attr( $columns['mobile'] );
			$base_col    = 'grid-col-';

			$container_attrs   = array();
			$container_classes = array(
				$base_class . '__items',
				$base_col . 'desk-' . $desktop_col,
				$base_col . 'tablet-' . $tablet_col,
				$base_col . 'mobile-' . $mobile_col,
				$base_class . '--' . absint( $settings['lisitng_id'] ),
			);

			if ( ! empty( $settings['inline_columns_css'] ) ) {

				$inline_css = '';

				$settings['column_min_width_tablet'] = ! empty( $settings['column_min_width_tablet'] ) ? $settings['column_min_width_tablet'] : $settings['column_min_width'];
				$settings['column_min_width_mobile'] = ! empty( $settings['column_min_width_mobile'] ) ? $settings['column_min_width_mobile'] : $settings['column_min_width_tablet'];

				if ( 'auto' === $desktop_col ) {
					$container_classes[] = 'inline-desk-css';
					$inline_css .= '--jet-column-min-width: ' . absint( $settings['column_min_width'] ) . 'px;';
				}

				if ( 'auto' === $tablet_col ) {
					$container_classes[] = 'inline-tablet-css';
					$inline_css .= '--jet-column-tablet-min-width: ' . absint( $settings['column_min_width_tablet'] ) . 'px;';
				}
				
				if ( 'auto' === $mobile_col ) {
					$container_classes[] = 'inline-mobile-css';
					$inline_css .= '--jet-column-mobile-min-width: ' . absint( $settings['column_min_width_mobile'] ) . 'px;';
				}

				if ( $inline_css ) {
					$container_attrs[] = 'style="' . $inline_css . '"';
				}

			}

			$this->enqueue_assets( $settings );

			$carousel_enabled = $this->is_carousel_enabled( $settings );

			if ( $this->is_masonry_enabled( $settings ) ) {
				$container_classes[] = $base_class . '__masonry';
				$container_attrs[]   = $this->get_masonry_options( $settings );
			}

			$queried_id = $this->get_queried_id();

			if ( $queried_id ) {
				$container_attrs[] = sprintf( 'data-queried-id="%s"', $queried_id );
			}

			printf( '<div class="%1$s jet-listing">', $base_class );

			$container_attrs = apply_filters(
				'jet-engine/listing/container-atts',
				$container_attrs,
				$settings,
				$this
			);

			if ( ! empty( $query ) ) {

				do_action( 'jet-engine/listing/grid/before', $this );

				if ( $carousel_enabled ) {

					$settings['items_count'] = count( $query );
					$slider_options          = $this->get_slider_options( $settings );
					$is_rtl                  = isset( $slider_options['rtl'] ) ? $slider_options['rtl'] : is_rtl();
					$dir                     = $is_rtl ? 'rtl' : 'ltr';

					printf(
						'<div class="%1$s__slider" data-slider_options="%2$s" dir="%3$s">',
						$base_class,
						htmlspecialchars( json_encode( $slider_options ) ),
						$dir
					);

				}

				$scroll_slider_enabled = $this->is_scroll_slider_enabled( $settings );

				if ( $scroll_slider_enabled ) {

					$scroll_slider_classes[] = sprintf( '%s__scroll-slider', $base_class );

					foreach ( $settings['scroll_slider_on'] as $device ) {
						$scroll_slider_classes[] = sprintf( '%1$s__scroll-slider-%2$s', $base_class, esc_attr( $device ) );
						$container_classes[] = sprintf( '%1$s__scroll-slider-wrap-%2$s', $base_class, esc_attr( $device ) );
					}

					printf( '<div class="%s">', implode( ' ', $scroll_slider_classes ) );
				}

				$equal_cols_class     = '';
				$equal_columns_height = ! empty( $settings['equal_columns_height'] ) ? $settings['equal_columns_height'] : false;
				$equal_columns_height = filter_var( $equal_columns_height, FILTER_VALIDATE_BOOLEAN );

				if ( $equal_columns_height ) {
					$equal_cols_class    = 'jet-equal-columns';
					$container_classes[] = 'jet-equal-columns__wrapper';
				}

				do_action( 'jet-engine/listing/grid-items/before', $settings, $this );

				printf(
					'<div class="%1$s" %2$s data-nav="%3$s" data-page="%4$d" data-pages="%5$d" data-listing-source="%6$s" data-listing-id="%7$s" data-query-id="%8$s">',
					esc_attr( implode( ' ', $container_classes ) ),
					implode( ' ', $container_attrs ),
					$this->get_nav_settings( $settings ),
					esc_attr( $this->query_vars['page'] ),
					esc_attr( $this->query_vars['pages'] ),
					jet_engine()->listings->data->get_listing_source(),
					$this->listing_id,
					$this->listing_query_id
				);

				do_action( 'jet-engine/listing/posts-loop/before', $settings, $this );

				$this->posts_loop( $query, $settings, $base_class, $equal_cols_class );

				do_action( 'jet-engine/listing/posts-loop/after', $settings, $this );

				echo '</div>';

				$this->maybe_print_load_more_loader( $settings );

				do_action( 'jet-engine/listing/grid-items/after', $settings, $this );

				if ( $carousel_enabled || $scroll_slider_enabled ) {
					echo '</div>';
				}

				do_action( 'jet-engine/listing/grid/after', $this );

			} else {

				do_action( 'jet-engine/listing/grid/not-found/before', $this );

				printf(
					'<div class="jet-listing-not-found %3$s" data-nav="%2$s" %4$s>%1$s</div>',
					wp_kses_post( do_shortcode( wp_unslash( $settings['not_found_message'] ) ) ),
					$this->get_nav_settings( $settings ),
					$base_class . '__items',
					implode( ' ', $container_attrs )
				);

				do_action( 'jet-engine/listing/grid/not-found/after', $this );
			}

			echo '</div>';

		}

		/**
		 * Output posts loop
		 *
		 * @param array  $query
		 * @param array  $settings
		 * @param string $base_class
		 * @param string $equal_cols_class
		 * @param bool $start_from
		 */
		public function posts_loop( $query = array(), $settings = array(), $base_class = '', $equal_cols_class = '', $start_from = false ) {

			$query = apply_filters( 'jet-engine/listing/query/items', $query, $settings, $this );

			if ( ! empty( $start_from ) ) {
				$i = absint( $start_from );
			} else {
				$i = 1;
			}

			$i = apply_filters( 'jet-engine/listing/posts-loop/start-from', $i, $settings, $this );

			global $wp_query, $post;
			$default_object = $wp_query->queried_object;

			$initial_index = jet_engine()->listings->data->get_index();
			jet_engine()->listings->data->reset_index();

			$temp_query = false;

			// Added for correctly setup and reset global $post in nested listings.
			if ( $this->posts_query ) {

				$is_singular = is_singular();

				$temp_query = $wp_query;
				$wp_query   = $this->posts_query;

				// For compatibility with ACF Dynamic Tags(Elementor Pro)
				$wp_query->is_singular = $is_singular;

				$temp_query->post = $post;
			}

			$col_width     = ! empty( $settings['static_column_width'] ) ? $settings['static_column_width'] : false;
			$scroll_slider = ! empty( $settings['scroll_slider_enabled'] ) ? $settings['scroll_slider_enabled'] : false;
			$scroll_slider = filter_var( $scroll_slider, FILTER_VALIDATE_BOOLEAN );
			$custom_css    = '';

			if ( $scroll_slider && $col_width && ! is_array( $col_width ) ) {
				$custom_css = 'style="flex: 0 0 ' . $col_width . 'px; max-width: ' . $col_width . 'px;"';
			}

			//timer_start();

			foreach ( $query as $post_obj ) {

				if ( empty( $post_obj ) ) {
					continue;
				}

				$wp_query->queried_object = $post_obj;

				ob_start();

				$content = apply_filters(
					'jet-engine/listing/pre-get-item-content',
					false,
					$post_obj,
					$i,
					$this,
					$query
				);

				$static_inject = ob_get_clean();

				if ( ! $content ) {
					jet_engine()->frontend->set_listing( absint( $settings['lisitng_id'] ) );
					$content = jet_engine()->frontend->get_listing_item( $post_obj );
				}

				$class   = get_class( $post_obj );
				$post_id = jet_engine()->listings->data->get_current_object_id();

				$classes = array(
					$base_class . '__item',
					'jet-listing-dynamic-post-' . $post_id,
					$equal_cols_class,
				);

				if ( $static_inject ) {

					$static_classes = apply_filters(
						'jet-engine/listing/item-classes',
						$classes, $post_obj, $i, $this, true
					);

					$static_post_id = apply_filters(
						'jet-engine/listing/item-post-id',
						$post_id, $post_obj, $i, $this, true
					);

					printf(
						'<div class="%1$s" data-post-id="%3$s">%2$s</div>',
						implode( ' ', array_filter( $static_classes ) ),
						$static_inject,
						$static_post_id
					);

					$i++;

				}

				$classes = apply_filters( 'jet-engine/listing/item-classes', $classes, $post_obj, $i, $this, false );

				do_action( 'jet-engine/listing/before-grid-item', $post_obj, $this );

				printf(
					'<div class="%1$s" data-post-id="%3$s" %4$s>%2$s</div>',
					implode( ' ', array_filter( $classes ) ),
					$content,
					$post_id,
					$custom_css
				);

				do_action( 'jet-engine/listing/after-grid-item', $post_obj, $this, $i );

				$i++;

				jet_engine()->listings->data->increase_index();

			}

			if ( $this->posts_query && $temp_query ) {
				$wp_query = $temp_query;
			}

			$wp_query->queried_object = $default_object;

			jet_engine()->frontend->reset_listing();
			jet_engine()->listings->data->set_index( $initial_index );

		}

		/**
		 * Enqueue depends assets.
		 *
		 * @param  array $settings Settings array
		 * @return void
		 */
		public function enqueue_assets( $settings ) {

			$carousel_enabled = $this->is_carousel_enabled( $settings );

			if ( $this->is_masonry_enabled( $settings ) ) {
				jet_engine()->frontend->enqueue_masonry_assets();
			}

			if ( $carousel_enabled ) {
				wp_enqueue_script( 'jquery-slick' );
			}

			do_action( 'jet-engine/listing/grid/assets', $settings, $this );

		}

		public function is_masonry_enabled( $settings ) {

			$columns = $this->get_columns_settings( $settings );

			if ( 'auto' === $columns['desktop']
				|| 'auto' === $columns['tablet']
				|| 'auto' === $columns['mobile'] ) {
				return false;
			}

			$masonry_enabled  = ! empty( $settings['is_masonry'] ) ? $settings['is_masonry'] : false;
			return filter_var( $masonry_enabled, FILTER_VALIDATE_BOOLEAN );
		}

		/**
		 * Is carousel enabled.
		 *
		 * @param  array $settings
		 * @return bool
		 */
		public function is_carousel_enabled( $settings ) {

			$carousel_enabled = ! empty( $settings['carousel_enabled'] ) ? $settings['carousel_enabled'] : false;

			if ( $this->is_masonry_enabled( $settings ) ) {

				// Force carousel disabling if masonry layout is active to avoid scripts duplicating
				$carousel_enabled = false;
			}

			return filter_var( $carousel_enabled, FILTER_VALIDATE_BOOLEAN );
		}

		/**
		 * Is scroll slider enabled.
		 *
		 * @param  array $settings
		 * @return bool
		 */
		public function is_scroll_slider_enabled( $settings ) {

			$carousel_enabled = $this->is_carousel_enabled( $settings );
			$masonry_enabled  = $this->is_masonry_enabled( $settings );

			if ( $masonry_enabled || $carousel_enabled ) {
				return false;
			}

			$scroll_slider_enabled = ! empty( $settings['scroll_slider_enabled'] ) && filter_var( $settings['scroll_slider_enabled'], FILTER_VALIDATE_BOOLEAN );

			return $scroll_slider_enabled && ! empty( $settings['scroll_slider_on'] );
		}

		/**
		 * Returns formatted data-attribute with masonry options
		 *
		 * @param  array $settings
		 * @return string
		 */
		public function get_masonry_options( $settings = array() ) {

			$options = apply_filters( 'jet-engine/listing/grid/masonry-options', array(
				'columns' => $this->get_columns_settings( $settings ),
			), $settings, $this );

			return sprintf( 'data-masonry-grid-options="%s"', htmlspecialchars( json_encode( $options ) ) );

		}

		/**
		 * Return arrow icon HTML markup
		 *
		 * @param  string $dir      [description]
		 * @param  array  $settings [description]
		 * @return [type]           [description]
		 */
		public function get_arrow_icon( $dir = 'prev', $settings = array(), $additional_classes = '' ) {

			$icon = '';

			switch ( $settings['arrow_icon'] ) {
				case 'fa fa-angle-left':
					$icon = "<svg width='180' height='180' viewBox='0 0 180 180' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M119 47.3166C119 48.185 118.668 48.9532 118.003 49.6212L78.8385 89L118.003 128.379C118.668 129.047 119 129.815 119 130.683C119 131.552 118.668 132.32 118.003 132.988L113.021 137.998C112.356 138.666 111.592 139 110.729 139C109.865 139 109.101 138.666 108.436 137.998L61.9966 91.3046C61.3322 90.6366 61 89.8684 61 89C61 88.1316 61.3322 87.3634 61.9966 86.6954L108.436 40.002C109.101 39.334 109.865 39 110.729 39C111.592 39 112.356 39.334 113.021 40.002L118.003 45.012C118.668 45.68 119 46.4482 119 47.3166Z' fill='black'/></svg>";
					break;

				case 'fa fa-chevron-left':
					$icon = "<svg width='180' height='180' viewBox='0 0 180 180' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M140.093 36.6365L86.7876 90L140.093 143.364C141.364 144.636 142 146.144 142 147.886C142 149.628 141.364 151.135 140.093 152.408L123.429 169.091C122.157 170.364 120.651 171 118.911 171C117.171 171 115.665 170.364 114.394 169.091L39.9073 94.5223C38.6358 93.2494 38 91.7419 38 90C38 88.2581 38.6358 86.7506 39.9073 85.4777L114.394 10.9094C115.665 9.63648 117.171 9 118.911 9C120.651 9 122.157 9.63648 123.429 10.9094L140.093 27.5918C141.364 28.8648 142 30.3722 142 32.1141C142 33.8561 141.364 35.3635 140.093 36.6365Z' fill='black'/></svg>";
					break;

				case 'fa fa-angle-double-left':
					$icon = "<svg width='180' height='180' viewBox='0 0 180 180' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M99.8385 131.683C99.8385 132.552 99.5072 133.32 98.8447 133.988L93.8758 138.998C93.2133 139.666 92.4513 140 91.5901 140C90.7288 140 89.9669 139.666 89.3043 138.998L42.9938 92.3046C42.3313 91.6366 42 90.8684 42 90C42 89.1316 42.3313 88.3634 42.9938 87.6954L89.3043 41.002C89.9669 40.334 90.7288 40 91.5901 40C92.4513 40 93.2133 40.334 93.8758 41.002L98.8447 46.012C99.5072 46.68 99.8385 47.4482 99.8385 48.3166C99.8385 49.185 99.5072 49.9532 98.8447 50.6212L59.7888 90L98.8447 129.379C99.5072 130.047 99.8385 130.815 99.8385 131.683ZM138 131.683C138 132.552 137.669 133.32 137.006 133.988L132.037 138.998C131.375 139.666 130.613 140 129.752 140C128.89 140 128.128 139.666 127.466 138.998L81.1553 92.3046C80.4928 91.6366 80.1615 90.8684 80.1615 90C80.1615 89.1316 80.4928 88.3634 81.1553 87.6954L127.466 41.002C128.128 40.334 128.89 40 129.752 40C130.613 40 131.375 40.334 132.037 41.002L137.006 46.012C137.669 46.68 138 47.4482 138 48.3166C138 49.185 137.669 49.9532 137.006 50.6212L97.9503 90L137.006 129.379C137.669 130.047 138 130.815 138 131.683Z' fill='black'/></svg>";
					break;

				case 'fa fa-arrow-left':
					$icon = "<svg width='180' height='180' viewBox='0 0 180 180' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M164 83.5918V96.4082C164 99.9461 162.911 102.967 160.732 105.47C158.554 107.973 155.722 109.225 152.236 109.225H81.4538L110.913 138.662C113.46 141.065 114.734 144.069 114.734 147.674C114.734 151.279 113.46 154.282 110.913 156.685L103.372 164.295C100.892 166.765 97.8759 168 94.3234 168C90.8379 168 87.788 166.765 85.1739 164.295L19.7201 99.0116C17.24 96.5417 16 93.5379 16 90C16 86.5289 17.24 83.4917 19.7201 80.8883L85.1739 15.8049C87.721 13.2683 90.7708 12 94.3234 12C97.8089 12 100.825 13.2683 103.372 15.8049L110.913 23.2144C113.46 25.751 114.734 28.7882 114.734 32.3261C114.734 35.8639 113.46 38.9012 110.913 41.4377L81.4538 70.7754H152.236C155.722 70.7754 158.554 72.027 160.732 74.5302C162.911 77.0334 164 80.0539 164 83.5918Z' fill='black'/></svg>";
					break;

				case 'fa fa-caret-left':
					$icon = "<svg width='180' height='180' viewBox='0 0 180 180' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M120 44.5V135.5C120 137.26 119.34 138.784 118.021 140.07C116.701 141.357 115.139 142 113.333 142C111.528 142 109.965 141.357 108.646 140.07L61.9792 94.5703C60.6597 93.2839 60 91.7604 60 90C60 88.2396 60.6597 86.7161 61.9792 85.4297L108.646 39.9297C109.965 38.6432 111.528 38 113.333 38C115.139 38 116.701 38.6432 118.021 39.9297C119.34 41.2161 120 42.7396 120 44.5Z' fill='black'/></svg>";
					break;

				case 'fa fa-long-arrow-left':
					$icon = "<svg width='180' height='180' viewBox='0 0 180 180' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M175 80.4926V99.4937C175 100.417 174.705 101.176 174.115 101.77C173.524 102.364 172.77 102.661 171.852 102.661H49.0741V124.828C49.0741 126.214 48.451 127.171 47.2049 127.698C45.9587 128.226 44.811 128.061 43.7616 127.204L5.9838 92.5662C5.32793 91.9064 5 91.1477 5 90.29C5 89.3664 5.32793 88.5747 5.9838 87.9149L43.7616 52.8817C44.811 51.958 45.9587 51.7601 47.2049 52.2879C48.451 52.8817 49.0741 53.8383 49.0741 55.1579V77.3258H171.852C172.77 77.3258 173.524 77.6227 174.115 78.2164C174.705 78.8102 175 79.5689 175 80.4926Z' fill='black'/></svg>";
					break;

				case 'fa fa-arrow-circle-left':
					$icon = "<svg width='180' height='180' viewBox='0 0 180 180' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M143.333 96.6667V83.3333C143.333 81.5278 142.674 79.9653 141.354 78.6458C140.035 77.3264 138.472 76.6667 136.667 76.6667H84.375L104.062 56.9792C105.382 55.6597 106.042 54.0972 106.042 52.2917C106.042 50.4861 105.382 48.9236 104.062 47.6042L94.5833 38.125C93.3333 36.875 91.7708 36.25 89.8958 36.25C88.0208 36.25 86.4583 36.875 85.2083 38.125L47.5 75.8333L38.0208 85.3125C36.7708 86.5625 36.1458 88.125 36.1458 90C36.1458 91.875 36.7708 93.4375 38.0208 94.6875L47.5 104.167L85.2083 141.875C86.4583 143.125 88.0208 143.75 89.8958 143.75C91.7708 143.75 93.3333 143.125 94.5833 141.875L104.062 132.396C105.312 131.146 105.937 129.583 105.937 127.708C105.937 125.833 105.312 124.271 104.062 123.021L84.375 103.333H136.667C138.472 103.333 140.035 102.674 141.354 101.354C142.674 100.035 143.333 98.4722 143.333 96.6667ZM170 90C170 104.514 166.424 117.899 159.271 130.156C152.118 142.413 142.413 152.118 130.156 159.271C117.899 166.424 104.514 170 90 170C75.4861 170 62.1007 166.424 49.8437 159.271C37.5868 152.118 27.8819 142.413 20.7292 130.156C13.5764 117.899 10 104.514 10 90C10 75.4861 13.5764 62.1007 20.7292 49.8438C27.8819 37.5868 37.5868 27.8819 49.8437 20.7292C62.1007 13.5764 75.4861 10 90 10C104.514 10 117.899 13.5764 130.156 20.7292C142.413 27.8819 152.118 37.5868 159.271 49.8438C166.424 62.1007 170 75.4861 170 90Z' fill='black'/></svg>";
					break;

				case 'fa fa-chevron-circle-left':
					$icon = "<svg width='180' height='180' viewBox='0 0 180 180' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M104.687 141.979L115.312 131.354C116.632 130.035 117.292 128.472 117.292 126.667C117.292 124.861 116.632 123.299 115.312 121.979L83.3333 90L115.312 58.0208C116.632 56.7014 117.292 55.1389 117.292 53.3333C117.292 51.5278 116.632 49.9653 115.312 48.6458L104.687 38.0208C103.368 36.7014 101.806 36.0417 100 36.0417C98.1944 36.0417 96.6319 36.7014 95.3125 38.0208L48.0208 85.3125C46.7014 86.6319 46.0417 88.1944 46.0417 90C46.0417 91.8056 46.7014 93.3681 48.0208 94.6875L95.3125 141.979C96.6319 143.299 98.1944 143.958 100 143.958C101.806 143.958 103.368 143.299 104.687 141.979ZM170 90C170 104.514 166.424 117.899 159.271 130.156C152.118 142.413 142.413 152.118 130.156 159.271C117.899 166.424 104.514 170 90 170C75.4861 170 62.1007 166.424 49.8437 159.271C37.5868 152.118 27.8819 142.413 20.7292 130.156C13.5764 117.899 10 104.514 10 90C10 75.4861 13.5764 62.1007 20.7292 49.8438C27.8819 37.5868 37.5868 27.8819 49.8437 20.7292C62.1007 13.5764 75.4861 10 90 10C104.514 10 117.899 13.5764 130.156 20.7292C142.413 27.8819 152.118 37.5868 159.271 49.8438C166.424 62.1007 170 75.4861 170 90Z' fill='black'/></svg>";
					break;

				case 'fa fa-caret-square-o-left':
					$icon = "<svg width='180' height='180' viewBox='0 0 180 180' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M116.667 56.6667V123.333C116.667 125.139 116.007 126.701 114.687 128.021C113.368 129.34 111.806 130 110 130C108.611 130 107.326 129.583 106.146 128.75L59.4792 95.4167C57.6042 94.0972 56.6667 92.2917 56.6667 90C56.6667 87.7083 57.6042 85.9028 59.4792 84.5833L106.146 51.25C107.326 50.4167 108.611 50 110 50C111.806 50 113.368 50.6597 114.687 51.9792C116.007 53.2986 116.667 54.8611 116.667 56.6667ZM143.333 140V40C143.333 39.0972 143.003 38.316 142.344 37.6563C141.684 36.9965 140.903 36.6667 140 36.6667H40C39.0972 36.6667 38.316 36.9965 37.6562 37.6563C36.9965 38.316 36.6667 39.0972 36.6667 40V140C36.6667 140.903 36.9965 141.684 37.6562 142.344C38.316 143.003 39.0972 143.333 40 143.333H140C140.903 143.333 141.684 143.003 142.344 142.344C143.003 141.684 143.333 140.903 143.333 140ZM170 40V140C170 148.264 167.066 155.33 161.198 161.198C155.33 167.066 148.264 170 140 170H40C31.7361 170 24.6701 167.066 18.8021 161.198C12.934 155.33 10 148.264 10 140V40C10 31.7361 12.934 24.6701 18.8021 18.8021C24.6701 12.934 31.7361 10 40 10H140C148.264 10 155.33 12.934 161.198 18.8021C167.066 24.6701 170 31.7361 170 40Z' fill='black'/></svg>";
					break;

				default:
					$icon = apply_filters( 'jet-engine/listing/grid/arrow-icon/' . $settings['arrow_icon'] , null, $this );
			}

			return sprintf(
				'<div class=\'%1$s__slider-icon %3$s-arrow %4$s\' role=\'button\' aria-label=\'%5$s\'>%2$s</div>',
				$this->get_name(),
				$icon,
				$dir,
				$additional_classes,
				'prev' === $dir ? __( 'Previous', 'jet-engine' ) : __( 'Next', 'jet-engine' )
			);

		}

		/**
		 * Returns formatted slider options
		 *
		 * @param  array $settings
		 * @return array
		 */
		public function get_slider_options( $settings = array() ) {

			$fade   = false;
			$effect = isset( $settings['effect'] ) ? $settings['effect'] : 'slide';

			if ( 1 === absint( $settings['columns'] ) && 'fade' === $effect ) {
				$fade = true;
			}

			$options = array(
				'autoplaySpeed'  => absint( $settings['autoplay_speed'] ),
				'autoplay'       => filter_var( $settings['autoplay'], FILTER_VALIDATE_BOOLEAN ),
				'pauseOnHover'   => filter_var( $settings['pause_on_hover'], FILTER_VALIDATE_BOOLEAN ),
				'infinite'       => filter_var( $settings['infinite'], FILTER_VALIDATE_BOOLEAN ),
				'centerMode'     => filter_var( $settings['center_mode'], FILTER_VALIDATE_BOOLEAN ),
				'speed'          => absint( $settings['speed'] ),
				'arrows'         => filter_var( $settings['arrows'], FILTER_VALIDATE_BOOLEAN ),
				'dots'           => filter_var( $settings['dots'], FILTER_VALIDATE_BOOLEAN ),
				'slidesToScroll' => absint( $settings['slides_to_scroll'] ),
				'prevArrow'      => $this->get_arrow_icon( 'prev', $settings ),
				'nextArrow'      => $this->get_arrow_icon( 'next', $settings ),
				'rtl'            => is_rtl(),
				'itemsCount'     => absint( $settings['items_count'] ),
				'fade'           => $fade,
			);

			$columns = $this->get_columns_settings( $settings );

			if ( 'auto' === $columns['desktop']
				|| 'auto' === $columns['tablet']
				|| 'auto' === $columns['mobile'] ) {
				$options['slidesToShow'] = 1;
				$options['variableWidth'] = true;
			} else {
				$options['slidesToShow'] = $columns;
			}

			$options = apply_filters( 'jet-engine/listing/grid/slider-options', $options, $settings );

			return $options;

		}

		/**
		 * Get posts number
		 *
		 * @param  array $settings
		 * @return int
		 */
		public function get_posts_num( $settings = array() ) {
			$posts_num = ! empty( $settings['posts_num'] ) ? absint( $settings['posts_num'] ) : 6;
			$is_random = isset( $settings['use_random_posts_num'] ) && filter_var( $settings['use_random_posts_num'], FILTER_VALIDATE_BOOLEAN );

			if ( $is_random ) {
				$max_posts_num = ! empty( $settings['max_posts_num'] ) ? absint( $settings['max_posts_num'] ) : 9;
				$posts_num     = rand( $posts_num, $max_posts_num );
			}

			return $posts_num;
		}

		/**
		 * Get columns settings.
		 *
		 * @param  array $settings
		 * @return array
		 */
		public function get_columns_settings( $settings = array() ) {

			$desktop_col = ! empty( $settings['columns'] ) ? absint( $settings['columns'] ) : 3;
			$tablet_col  = ! empty( $settings['columns_tablet'] ) ? absint( $settings['columns_tablet'] ) : $desktop_col;
			$mobile_col  = ! empty( $settings['columns_mobile'] ) ? absint( $settings['columns_mobile'] ) : $tablet_col;

			return apply_filters( 'jet-engine/listing/grid/columns', array(
				'desktop' => 0 === $desktop_col ? 'auto' : $desktop_col,
				'tablet'  => 0 === $tablet_col ? 'auto' : $tablet_col,
				'mobile'  => 0 === $mobile_col ? 'auto' : $mobile_col,
			), $settings );
		}

		/**
		 * Maybe print loader html.
		 *
		 * @param array $settings
		 */
		public function maybe_print_load_more_loader( $settings = array() ) {

			if ( empty( $settings['use_load_more'] ) ) {
				return;
			}

			$loader_text    = ! empty( $settings['loader_text'] ) ? wp_kses_post( $settings['loader_text'] ) : false;
			$loader_spinner = ! empty( $settings['loader_spinner'] ) ? filter_var( $settings['loader_spinner'], FILTER_VALIDATE_BOOLEAN ) : false;

			if ( ! $loader_text && ! $loader_spinner ) {
				return;
			}

			echo $this->get_loader_html( $loader_spinner, $loader_text );
		}

		/**
		 * Get loader html.
		 *
		 * @param bool   $show_spinner
		 * @param string $text
		 *
		 * @return string
		 */
		public function get_loader_html( $show_spinner = true, $text = '' ) {

			if ( empty( $show_spinner ) && empty( $text ) ) {
				return '';
			}

			$format = apply_filters(
				'jet-engine/listing/grid/loader/format',
				'<div class="jet-listing-grid__loader">%1$s%2$s</div>'
			);

			$loader_spinner_html = '';
			$loader_text_html    = '';

			if ( $show_spinner ) {
				$loader_spinner_html = apply_filters(
					'jet-engine/listing/grid/loader/spinner/html',
					'<div class="jet-listing-grid__loader-spinner"></div>'
				);
			}

			if ( $text ) {
				$loader_text_html = sprintf( '<div class="jet-listing-grid__loader-text">%s</div>', $text );
			}

			return sprintf( $format, $loader_spinner_html, $loader_text_html );
		}

		public function before_listing_grid() {
			do_action( 'jet-engine/listing/grid/before-render', $this );
		}

		public function after_listing_grid() {
			do_action( 'jet-engine/listing/grid/after-render', $this );
		}

		public function get_queried_id() {
			$queried_id     = false;
			$current_obj    = jet_engine()->listings->data->get_current_object();
			$current_obj_id = jet_engine()->listings->data->get_current_object_id();

			if ( $current_obj && $current_obj_id ) {
				$queried_id = sprintf( '%s|%s', $current_obj_id, get_class( $current_obj ) );
				$queried_id = apply_filters( 'jet-engine/listing/grid/queried-id', $queried_id, $current_obj_id, $current_obj );
			}

			return $queried_id;
		}

	}

}

