<?php
/**
 * Object factory class
 */
namespace Jet_Engine\Timber_Views;

use Timber\Timber;
use Timber\Post;
use Timber\User;

class Object_Factory {

	public function get_post( \WP_Post $post, bool $set_object = true ) {
		if ( $this->is_2X_branch() ) {
			return Post::build( $post );
		} else {
			return new Post( $post->ID );
		}

		if ( $set_object ) {
			$this->set_current( $post );
		}

	}

	public function get_user( \WP_User $user, bool $set_object = true ) {
		if ( $this->is_2X_branch() ) {
			return User::build( $user );
		} else {
			return new User( $user->ID );
		}

		if ( $set_object ) {
			$this->set_current( $user );
		}

	}

	public function set_current( $object ) {
		jet_engine()->listings->data->set_current_object( $object );
	}

	public function is_2X_branch() {
		return version_compare( Timber::$version, '2.0.0', '>=' );
	}

}
