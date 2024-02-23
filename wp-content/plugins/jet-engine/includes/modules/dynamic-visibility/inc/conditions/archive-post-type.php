<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Archive_Post_Type extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'archive-post-type';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Post Type Archive is', 'jet-engine' );
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

		$type       = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$post_types = $this->explode_string( $args['value'] );

		if ( in_array( 'post', $post_types ) && 'post' === get_post_type() ) {
			$result = is_archive() || is_home();
		} else {
			$result = is_post_type_archive( $post_types ) || ( is_tax() && in_array( get_post_type(), $post_types ) );
		}

		if ( 'hide' === $type ) {
			return ! $result;
		} else {
			return $result;
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
	$manager->register_condition( new Archive_Post_Type() );
} );
