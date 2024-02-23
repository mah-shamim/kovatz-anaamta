<?php
/**
 * Class ro implement single object of the repeater query item
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

#[AllowDynamicProperties]
class Jet_Engine_Queried_Repeater_Item {

	/**
	 * Index of specific item in current query
	 * 
	 * @var integer
	 */
	private $_item_ID = 0;

	public function __construct( $item, $item_id, $object_id = 0, $query_id = 0 ) {

		foreach ( get_object_vars( $item ) as $prop => $value) {
			$this->$prop = $value;
		}

		$this->_item_ID = $query_id;

		if ( $object_id ) {
			$this->_item_ID .= '-' . $object_id;
		}

		$this->_item_ID .= '-' . $item_id;

	}

	public function get_id() {
		return $this->_item_ID;
	}

}
