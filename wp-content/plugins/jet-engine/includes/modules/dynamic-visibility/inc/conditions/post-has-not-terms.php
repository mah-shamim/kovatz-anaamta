<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Post_Has_Not_Terms extends Post_Has_Terms {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'post-has-not-terms';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Post Hasn\'t Terms', 'jet-engine' );
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @param  array $args
	 * @return bool
	 */
	public function check( $args = array() ) {
		return ! parent::check( $args );
	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new Post_Has_Not_Terms() );
} );
