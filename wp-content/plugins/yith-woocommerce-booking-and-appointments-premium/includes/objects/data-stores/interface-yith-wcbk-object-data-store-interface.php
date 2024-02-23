<?php
/**
 * YITH_WCBK_Object_Data_Store_Interface
 *
 * Data store interface
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @package YITH\Booking
 */

defined( 'ABSPATH' ) || exit();

/**
 * Interface YITH_WCBK_Object_Data_Store_Interface
 *
 * @since 3.0.0
 */
interface YITH_WCBK_Object_Data_Store_Interface {
	/**
	 * Method to create a new record of a YITH_TRS_Data based object.
	 *
	 * @param YITH_WCBK_Data $data Data object.
	 */
	public function create( &$data );

	/**
	 * Method to read a record. Creates a new YITH_WCBK_Data based object.
	 *
	 * @param YITH_WCBK_Data $data Data object.
	 */
	public function read( &$data );

	/**
	 * Updates a record in the database.
	 *
	 * @param YITH_WCBK_Data $data Data object.
	 */
	public function update( &$data );

	/**
	 * Deletes a record from the database.
	 *
	 * @param YITH_WCBK_Data $data Data object.
	 * @param array          $args Arguments.
	 *
	 * @return bool result
	 */
	public function delete( &$data, $args );

	/**
	 * Retrieve the internal meta keys.
	 */
	public function get_internal_meta_keys();

	/**
	 * Returns an array of meta for an object.
	 *
	 * @param YITH_WCBK_Data $object The object.
	 *
	 * @return array
	 */
	public function read_meta( &$object );

	/**
	 * Deletes meta based on meta ID.
	 *
	 * @param YITH_WCBK_Data $object YITH_WCBK_Data object.
	 * @param stdClass       $meta   (containing at least ->id).
	 */
	public function delete_meta( &$object, $meta );

	/**
	 * Add new piece of meta.
	 *
	 * @param YITH_WCBK_Data $object YITH_WCBK_Data object.
	 * @param stdClass       $meta   (containing ->key and ->value).
	 *
	 * @return int meta ID
	 */
	public function add_meta( &$object, $meta );

	/**
	 * Update meta.
	 *
	 * @param YITH_WCBK_Data $object YITH_WCBK_Data object.
	 * @param stdClass       $meta   (containing ->id, ->key and ->value).
	 */
	public function update_meta( &$object, $meta );
}
