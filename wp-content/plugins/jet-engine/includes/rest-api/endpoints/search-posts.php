<?php
/**
 * Add/Update post type endpoint
 */

class Jet_Engine_Rest_Search_Posts extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'search-posts';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params       = $request->get_params();
		$query        = $params['query'];
		$ids          = $params['ids'];
		$post_type    = $params['post_type'];
		$search_terms = $params['search_terms'];
		$tax          = $params['tax'];

		if ( ! empty( $ids ) ) {
			$ids = explode( ',', $ids );
		}

		if ( $search_terms ) {
			return $this->search_terms( $query, $ids, $tax );
		}

		if ( ! empty( $post_type ) ) {
			$post_type = explode( ',', $post_type );
		}

		add_filter( 'posts_where', array( $this, 'force_search_by_title' ), 10, 2 );

		$args = array(
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'suppress_filters'    => false,
			'numberposts'         => 10,
		);

		if ( $query ) {
			$args['s']       = $query;
			$args['s_title'] = $query;
		}

		if ( ! empty( $ids ) ) {
			$args['post__in'] = $ids;
		}

		if ( ! empty( $post_type ) ) {
			$args['post_type'] = $post_type;
		}

		$posts = get_posts( $args );

		remove_filter( 'posts_where', array( $this, 'force_search_by_title' ), 10, 2 );

		$result = array();

		foreach ( $posts as $post ) {
			$result[] = array(
				'value' => (string) $post->ID,
				'label' => $post->post_title,
			);
		}

		return rest_ensure_response( $result );

	}

	/**
	 * Perform search by taxonomy terms
	 *
	 * @param  [type] $query [description]
	 * @param  [type] $ids   [description]
	 * @param  [type] $tax   [description]
	 * @return [type]        [description]
	 */
	public function search_terms( $query = '', $ids = array(), $tax = '' ) {

		$result = array();

		if ( ! $tax ) {
			return rest_ensure_response( $result );
		}

		$args = array(
			'taxonomy'   => $tax,
			'hide_empty' => false,
		);

		if ( ! empty( $ids ) ) {
			$args['include'] = $ids;
		} else {
			$args['name__like'] = $query;
		}

		$terms = get_terms( $args );

		foreach ( $terms as $term ) {
			$result[] = array(
				'value' => (string) $term->term_id,
				'label' => $term->name,
			);
		}

		return rest_ensure_response( $result );
	}

	/**
	 * Force query to look in post title while searching
	 *
	 * @return [type] [description]
	 */
	public function force_search_by_title( $where, $query ) {

		$args = $query->query;

		if ( ! isset( $args['s_title'] ) ) {
			return $where;
		} else {
			global $wpdb;

			$searh = esc_sql( $wpdb->esc_like( $args['s_title'] ) );
			$where .= " AND {$wpdb->posts}.post_title LIKE '%$searh%'";

		}

		return $where;
	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELTE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'GET';
	}

	/**
	 * Check user access to current end-popint
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Returns arguments config
	 *
	 * @return array
	 */
	public function get_args() {
		return array(
			'query' => array(
				'default'  => '',
				'required' => false,
			),
			'ids' => array(
				'default'  => '',
				'required' => false,
			),
			'post_type' => array(
				'default'  => 'any',
				'required' => false,
			),
			'tax' => array(
				'default'  => '',
				'required' => false,
			),
			'search_terms' => array(
				'default'  => false,
				'required' => false,
			),
		);
	}

}
