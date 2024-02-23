<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Post_Has_Terms extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'post-has-terms';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Post Has Terms', 'jet-engine' );
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
		$terms   = $this->explode_string( $args['value'] );
		$tax     = ! empty( $args['condition_settings']['terms_taxonomy'] ) ? $args['condition_settings']['terms_taxonomy'] : '';
		$post_id = jet_engine()->listings->data->get_current_object_id();

		if ( 'hide' === $type ) {
			return ! has_term( $terms, $tax, $post_id );
		} else {
			return has_term( $terms, $tax, $post_id );
		}

	}

	/**
	 * Returns condition specific repeater controls
	 */
	public function get_custom_controls() {
		return array(
			'terms_taxonomy' => array(
				'label'   => __( 'Taxonomy', 'jet-engine' ),
				'type'    => 'select',
				'options' => jet_engine()->listings->get_taxonomies_for_options(),
				'default' => '',
			),
		);
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
	$manager->register_condition( new Post_Has_Terms() );
} );
