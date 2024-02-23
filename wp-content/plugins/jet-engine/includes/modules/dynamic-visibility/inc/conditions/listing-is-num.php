<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Listing_Is_Number extends Listing_Even {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'listing-is-num';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Is N item', 'jet-engine' );
	}

	/**
	 * Returns condition specific repeater controls
	 */
	public function get_custom_controls() {
		return array(
			'item_number' => array(
				'label'       => __( 'Number', 'jet-engine' ),
				'description' => __( 'Item number to compare', 'jet-engine' ),
				'type'        => 'text',
				'default'     => '',
			),
			'each_item_number' => array(
				'label'       => __( 'Each N Number', 'jet-engine' ),
				'description' => __( 'Check this if you need to condition met each N number. Leave empty to met only exact number', 'jet-engine' ),
				'type'        => 'switcher',
				'default'     => '',
			),
		);
	}

	/**
	 * Check current item index
	 * 
	 * @return [type] [description]
	 */
	public function check_index( $args ) {
		
		$args = ! empty( $args['condition_settings'] ) ? $args['condition_settings'] : array();
		$item_number = isset( $args['item_number'] ) ? absint( $args['item_number'] ) : false;
		$each = isset( $args['each_item_number'] ) ? filter_var( $args['each_item_number'], FILTER_VALIDATE_BOOLEAN ) : false;
		$index = $this->get_item_index();

		if ( ! $item_number ) {
			return false;
		}

		if ( ! $each ) {
			return $item_number == $index;
		} else {
			return ( 0 === ( $index % $item_number ) ) ? true : false;
		}

	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new Listing_Is_Number() );
} );
