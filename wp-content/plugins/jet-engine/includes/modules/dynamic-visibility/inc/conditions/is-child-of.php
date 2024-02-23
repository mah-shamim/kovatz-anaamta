<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Is_Child_Of extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'is_child_post_of';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Is Child Post Of', 'jet-engine' );
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

		$type      = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$post_ids  = $this->explode_string( $args['value'] );
		$parent_id = wp_get_post_parent_id( get_the_ID() );

		if ( empty( $post_ids ) ) {

			if ( 'hide' === $type ) {
				return empty( $parent_id );
			} else {
				return ! empty( $parent_id );
			}
		}

		if ( 'hide' === $type ) {
			return ! in_array( $parent_id, $post_ids );
		} else {
			return in_array( $parent_id, $post_ids );
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

	public function get_custom_controls() {
		return array(
			$this->get_id() . '_notice' => array(
				'type' => 'raw_html',
				'raw'  => __( 'To check if a post has/hasn\'t  any parent post, leave empty the Value field.', 'jet-engine' ),
			),
		);
	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new Is_Child_Of() );
} );
