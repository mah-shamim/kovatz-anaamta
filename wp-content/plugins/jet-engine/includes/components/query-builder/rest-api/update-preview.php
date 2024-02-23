<?php
namespace Jet_Engine\Query_Builder\Rest;

use Jet_Engine\Query_Builder\Manager;

class Update_Preview extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'update-query-preview';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		Manager::instance()->include_factory();

		$params = $request->get_params();
		$type = ! empty( $params['query_type'] ) ? $params['query_type'] : false;

		if ( ! $type ) {
			return rest_ensure_response( array(
				'success' => false,
				'data'    => null,
			) );
		}

		$preview = ! empty( $params['preview'] ) ? $params['preview'] : array();

		$this->setup_preview( $preview );

		$factory = new \Jet_Engine\Query_Builder\Query_Factory( array(
			'id'     => $params['query_id'],
			'lables' => array( 'name' => 'Preview' ),
			'args'   => array(
				'query_type'         => $type,
				$type                => $params['query'],
				'__dynamic_' . $type => $params['dynamic_query'],
			),
		) );

		$query = $factory->get_query();

		if ( ! $query ) {
			return rest_ensure_response( array(
				'success' => true,
				'count'   => 0,
				'data'    => __( 'Can`t find the query object', 'jet-engine' ),
			) );
		}

		$count = $query->get_items_total_count();
		$items = $query->get_items();
		$more  = '';
		$count = $query->get_items_total_count();

		if ( 10 < $count ) {
			$items = array_slice( $items, 0, 10 );
			$more  = "\r\n...";
		}

		return rest_ensure_response( array(
			'success' => true,
			'count'   => $count,
			'data'    => $this->stringify_data( $query, $items, $more ),
		) );

	}

	public function setup_preview( $preview = array() ) {

		if ( ! empty( $preview['page'] ) ) {

			global $wp_query, $post;

			$pid = absint( $preview['page'] );
			$post = get_post( $pid );

			if ( $post && 'page' === $post->post_type ) {
				$wp_query = new \WP_Query( array( 'page_id' => $pid ) );
			} elseif ( $post ) {
				$wp_query = new \WP_Query( array( 'p' => $pid ) );
			}

		}

		if ( ! empty( $preview['page_url'] ) ) {
			$_SERVER['REQUEST_URI'] = preg_replace(
				'/wp-json\/.*/',
				ltrim( $preview['page_url'], '/' ),
				$_SERVER['REQUEST_URI']
			);

			global $wp;

			$wp->parse_request();
			$wp->query_posts();

		}

		if ( ! empty( $preview['query_string'] ) ) {

			parse_str( $preview['query_string'], $query_array );

			if ( ! empty( $query_array ) ) {
				foreach ( $query_array as $key => $value ) {
					$_GET[ $key ]     = $value;
					$_REQUEST[ $key ] = $value;
				}
			}

		}

	}

	public function stringify_data( $query = null, $items = array(), $more = '' ) {
		ob_start();
		$query->before_preview_body();
		print_r( $items );
		return ob_get_clean() . $more;
	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELTE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'POST';
	}

	/**
	 * Check user access to current end-popint
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}

}
