<?php
namespace Jet_Smart_Filters\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class PostsList extends Base {

	public function get_name() {

		return 'posts-list';
	}

	public function get_args() {

		return array(
			'post_type' => array(
				'default'  => 'post',
				'required' => false,
			),
			'post_status' => array(
				'default'  => 'publish',
				'required' => false,
			),
			'posts_per_page' => array(
				'default'  => -1,
				'required' => false,
			)
		);
	}

	public function callback( $request ) {

		$args = $request->get_params();

		// Args
		$post_type      = $args['post_type'];
		$post_status    = $args['post_status'];
		$posts_per_page = $args['posts_per_page'];

		$args = array(
			'post_type'      => $post_type,
			'post_status'    => $post_status,
			'posts_per_page' => $posts_per_page,
		);

		$posts = get_posts( $args );

		if ( ! empty( $posts ) ) {
			$posts = wp_list_pluck( $posts, 'post_title', 'ID' );
		}

		return rest_ensure_response( $posts );
	}
}
