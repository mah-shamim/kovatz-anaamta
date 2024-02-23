<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Post_ID_Not extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'post-id-not';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Post ID is not', 'jet-engine' );
	}

	/**
	 * Returns group for current operator
	 *
	 * @return [type] [description]
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

		$type     = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$post_ids = $this->explode_string( $args['value'] );

		if ( 'hide' === $type ) {
			return in_array( get_the_ID(), $post_ids );
		} else {
			return ! in_array( get_the_ID(), $post_ids );
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
		return true;
	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new Post_ID_Not() );
} );
