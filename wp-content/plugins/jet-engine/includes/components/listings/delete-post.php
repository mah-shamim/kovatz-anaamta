<?php
/**
 * Delete post class class
 */

class Jet_Engine_Delete_Post {

	public $query_var = 'jet_engine_delete_post';

	public function __construct() {
		if ( ! empty( $_GET[ $this->query_var ] ) ) {
			add_action( 'wp_loaded', array( $this, 'delete_post' ), -1 );
		}
	}

	/**
	 * Return delete post URL by arguments list
	 */
	public function get_delete_url( $args = array() ) {

		$post_id  = ! empty( $args['post_id'] ) ? $args['post_id'] : get_the_ID();
		$type     = ! empty( $args['type'] ) ? $args['type'] : 'trash';
		$redirect = ! empty( $args['redirect'] ) ? esc_url( $args['redirect'] ) : home_url( '/' );

		return add_query_arg( 
			apply_filters( 'jet-engine/listings/delete-post/query-args', array(
				$this->query_var => $post_id,
				'type' => $type,
				'redirect' => urlencode( $redirect ),
				'nonce' => wp_create_nonce( $this->query_var ),
			), $args ),
			esc_url( home_url( '/' ) )
		);

	}

	public function delete_post() {

		$nonce    = ! empty( $_GET['nonce'] ) ? $_GET['nonce'] : false;
		$type     = ! empty( $_GET['type'] ) ? $_GET['type'] : 'trash';
		$redirect = ! empty( $_GET['redirect'] ) ? esc_url( $_GET['redirect'] ) : home_url( '/' );
		$post_id  = ! empty( $_GET[ $this->query_var ] ) ? $_GET[ $this->query_var ] : false;

		if ( ! $post_id ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			wp_die(
				__( 'Only logged-in user can delete posts', 'jet-engine' ),
				__( 'Error!', 'jet-engine' )
			);
		}

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->query_var ) ) {
			wp_die(
				__( 'The link is expired, please return to the previous page and try again.', 'jet-engine' ),
				__( 'Error!', 'jet-engine' )
			);
		}

		do_action( 'jet-engine/listings/delete-post/before', $this );

		if ( ! current_user_can( 'delete_post', $post_id ) ) {

			$post = get_post( $post_id );
			$post_author = absint( $post->post_author );
			$current_user_id = get_current_user_id();

			if ( $current_user_id !== $post_author ) {
				wp_die(
					__( 'You don`t have access to this post.', 'jet-engine' ),
					__( 'Error!', 'jet-engine' )
				);
			}
		}

		$force_delete = ( 'permanently' === $type ) ? true : false;

		if ( $force_delete ) {
			wp_delete_post( $post_id, $force_delete );
		} else {
			wp_trash_post( $post_id );
		}

		if ( $redirect ) {
			// Fixed '&' encoding
			$redirect = str_replace( '&#038;', '&', $redirect );
			wp_redirect( $redirect );
			die();
		}

	}

}
