<?php
/**
 * Get searching posts endpoint
 */

class Jet_Search_Rest_Search_Route extends Jet_Search_Rest_Base_Route {

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
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'search-posts';
	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELETE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'GET';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params = $request->get_params();

		if ( empty( $params['data'] ) ) {
			return false;
		}

		$data                                      = $params['data'];
		$lang									   = isset( $params['lang'] ) ? $params['lang'] : '';
		$this->search_query['s']                   = urldecode( $data['value'] );
		$this->search_query['nopaging']            = false;
		$this->search_query['ignore_sticky_posts'] = false;
		$this->search_query['posts_per_page']      = ( int ) $data['limit_query_in_result_area'];
		$this->search_query['post_status']         = 'publish';

		$this->set_query_settings( $data );

		// Polylang, WPML Compatibility
		if ( '' != $lang ) {
			$this->search_query['lang'] = $lang;
		}

		add_filter( 'wp_query_search_exclusion_prefix', '__return_empty_string' );

		$search = new WP_Query( apply_filters( 'jet-search/ajax-search/query-args', $this->search_query, $this ) );

		if ( function_exists( 'relevanssi_do_query' ) ) {
			relevanssi_do_query( $search );
		}

		$response = array(
			'error'      => false,
			'post_count' => 0,
			'message'    => '',
			'posts'      => null,
		);

		remove_filter( 'wp_query_search_exclusion_prefix', '__return_empty_string' );

		if ( is_wp_error( $search ) ) {
			$response['error']   = true;
			$response['message'] = esc_html( $data['server_error'] );

			return wp_send_json_success( $response );
		}

		if ( empty( $search->post_count ) ) {
			$response['message'] = esc_html( $data['negative_search'] );

			return wp_send_json_success( $response );
		}

		$data['limit_query'] = jet_search_ajax_handlers()->extract_limit_query( $data );

		$data['post_count'] = $search->post_count;
		$data['columns']    = ceil( $data['post_count'] / $data['limit_query'] );

		if ( '' != $data['highlight_searched_text'] ) {
			$response['search_value']     = $this->search_query['s'];
			$response['search_highlight'] = true;
		} else {
			$response['search_highlight'] = false;
		}

		$response['posts']              = array();
		$response['columns']            = $data['columns'];
		$response['limit_query']        = $data['limit_query'];
		$response['post_count']         = $data['post_count'];
		$response['results_navigation'] = jet_search_ajax_handlers()->get_results_navigation( $data );

		if ( $response['post_count'] > $response['limit_query'] ) {
			$this->has_navigation = true;
		}

		$link_target_attr = ( isset( $data['show_result_new_tab'] ) && 'yes' === $data['show_result_new_tab'] ) ? '_blank' : '';

		foreach ( $search->posts as $key => $post ) {

			$response['posts'][ $key ] = array(
				'title'            => $post->post_title,
				'before_title'     => Jet_Search_Template_Functions::get_meta_fields( $data, $post, 'title_related', 'jet-search-title-fields', array( 'before' ) ),
				'after_title'      => Jet_Search_Template_Functions::get_meta_fields( $data, $post, 'title_related', 'jet-search-title-fields', array( 'after' ) ),
				'content'          => Jet_Search_Template_Functions::get_post_content( $data, $post ),
				'before_content'   => Jet_Search_Template_Functions::get_meta_fields( $data, $post, 'content_related', 'jet-search-content-fields', array( 'before' ) ),
				'after_content'    => Jet_Search_Template_Functions::get_meta_fields( $data, $post, 'content_related', 'jet-search-content-fields', array( 'after' ) ),
				'thumbnail'        => Jet_Search_Template_Functions::get_post_thumbnail( $data, $post ),
				'link'             => esc_url( get_permalink( $post->ID ) ),
				'link_target_attr' => $link_target_attr,
				'price'            => Jet_Search_Template_Functions::get_product_price( $data, $post ),
				'rating'           => Jet_Search_Template_Functions::get_product_rating( $data, $post ),
			);

			$custom_post_data = apply_filters( 'jet-search/ajax-search/custom-post-data', array(), $data, $post );

			if ( ! empty( $custom_post_data ) ) {
				$response['posts'][ $key ] = array_merge( $response['posts'][ $key ], $custom_post_data );
			}

			if ( ! $this->has_navigation && $key === $data['limit_query'] - 1 ) {
				break;
			}
		}

		return wp_send_json_success( $response );

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
	 * Check user access to current end-popint
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		return true;
	}

	/**
	 * Returns arguments config
	 *
	 * @return array
	 */
	public function get_args() {
		return array(
			'data' => array(
				'default'  => array(),
				'required' => true,
			),
		);
	}
}