<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Is_Not_Child_Of extends Is_Child_Of {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'is_not_child_post_of';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Is Not Child Post Of', 'jet-engine' );
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
	$manager->register_condition( new Is_Not_Child_Of() );
} );
