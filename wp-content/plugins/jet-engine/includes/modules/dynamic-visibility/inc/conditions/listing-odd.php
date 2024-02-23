<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Listing_Odd extends Listing_Even {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'listing-odd';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Is odd item', 'jet-engine' );
	}

	/**
	 * Check current item index
	 * 
	 * @return [type] [description]
	 */
	public function check_index( $args ) {
		$index = $this->get_item_index();
		return ( 0 !== ( $index % 2 ) ) ? true : false;
	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new Listing_Odd() );
} );
