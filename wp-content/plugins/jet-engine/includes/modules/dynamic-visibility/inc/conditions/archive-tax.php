<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Archive_Tax extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'archive-tax';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Taxonomy Archive is', 'jet-engine' );
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

		$type = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$tax  = $this->explode_string( $args['value'] );

		if ( in_array( 'category', $tax ) && 'post' === get_post_type() ) {
			$result = is_category();
		} elseif ( in_array( 'post_tag', $tax ) && 'post' === get_post_type() ) {
			$result = is_tag();
		} else {
			$result = is_tax( $tax );
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
	$manager->register_condition( new Archive_Tax() );
} );
