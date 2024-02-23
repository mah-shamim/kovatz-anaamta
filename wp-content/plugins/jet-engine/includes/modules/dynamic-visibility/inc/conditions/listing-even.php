<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Listing_Even extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'listing-even';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Is even item', 'jet-engine' );
	}

	/**
	 * Returns group for current operator
	 *
	 * @return [type] [description]
	 */
	public function get_group() {
		return 'listing';
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$type = ! empty( $args['type'] ) ? $args['type'] : 'show';

		if ( 'hide' === $type ) {
			return ! $this->check_index( $args );
		} else {
			return $this->check_index( $args );
		}

	}

	public function get_item_index() {
		$index = jet_engine()->listings->data->get_index();
		$index++;
		return $index;
	}

	/**
	 * Check current item index
	 * 
	 * @return [type] [description]
	 */
	public function check_index( $args ) {
		$index = $this->get_item_index();
		return ( 0 === ( $index % 2 ) ) ? true : false;
	}

	/**
	 * Check if is condition available for meta fields control
	 *
	 * @return boolean [description]
	 */
	public function is_for_fields() {
		return false;
	}

	/**
	 * Check if is condition available for meta value control
	 *
	 * @return boolean [description]
	 */
	public function need_value_detect() {
		return false;
	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new Listing_Even() );
} );
