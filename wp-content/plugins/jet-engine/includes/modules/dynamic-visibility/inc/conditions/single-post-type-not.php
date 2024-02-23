<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Single_Post_Type_Not extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'single-post-type-not';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Single Post Type is not', 'jet-engine' );
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

		if ( 'hide' === $type ) {
			return is_singular( $post_types );
		} else {
			return ! is_singular( $post_types );
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
	$manager->register_condition( new Single_Post_Type_Not() );
} );
