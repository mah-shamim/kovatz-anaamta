<?php
namespace Jet_Engine\Modules\Profile_Builder\Dynamic_Visibility;

use Jet_Engine\Modules\Profile_Builder\Module;

class Post_By_Queried_User extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'is-post-by-queried-user';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Is post by queried user', 'jet-engine' );
	}

	/**
	 * Returns group for current operator
	 *
	 * @return string
	 */
	public function get_group() {
		return 'posts';
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @param  array $args
	 * @return bool
	 */
	public function check( $args = array() ) {

		$type    = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$by_user = false;

		$post = get_post( get_the_ID() );
		$queried_user = Module::instance()->query->get_queried_user();

		if ( $post && $queried_user ) {
			$author_id = absint( $post->post_author );
			$by_user   = $author_id === absint( $queried_user->ID );
		}

		if ( 'hide' === $type ) {
			return ! $by_user;
		} else {
			return $by_user;
		}

	}

	/**
	 * Check if is condition available for meta fields control
	 *
	 * @return boolean
	 */
	public function is_for_fields() {
		return false;
	}

	/**
	 * Check if is condition available for meta value control
	 *
	 * @return boolean
	 */
	public function need_value_detect() {
		return false;
	}

}
