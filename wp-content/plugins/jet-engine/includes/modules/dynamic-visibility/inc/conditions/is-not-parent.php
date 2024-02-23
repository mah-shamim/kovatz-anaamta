<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Is_Not_Parent extends Is_Parent {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'is_not_parent_post';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Is Not Parent Post', 'jet-engine' );
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
	$manager->register_condition( new Is_Not_Parent() );
} );
