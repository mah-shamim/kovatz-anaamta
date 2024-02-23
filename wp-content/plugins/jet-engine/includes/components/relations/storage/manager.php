<?php
namespace Jet_Engine\Relations\Storage;

/**
 * Database manager class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Manager class
 */
class Manager {

	private $default_db      = null;
	private $default_meta_db = null;
	private $db_instances    = array();

	/**
	 * Get default DB
	 *
	 * @return [type] [description]
	 */
	public function get_default_db() {

		if ( ! $this->default_db ) {
			$this->default_db = $this->get_db_instance( 'default', $this->get_db_schema() );
		}

		return $this->default_db;

	}

	/**
	 * Get default DB
	 *
	 * @return [type] [description]
	 */
	public function get_default_meta_db() {

		if ( ! $this->default_meta_db ) {
			$this->default_meta_db = $this->get_db_instance( 'default_meta', $this->get_meta_db_schema() );
		}

		return $this->default_meta_db;

	}

	/**
	 * Returns new DB instance
	 * @param  string $table  [description]
	 * @param  array  $schema [description]
	 * @return [type]         [description]
	 */
	public function get_db_instance( $table = 'default', $schema = array() ) {

		if ( ! class_exists( '\Jet_Engine\Relations\Storage\DB' ) ) {
			require_once jet_engine()->relations->component_path( 'storage/db.php' );
		}

		$this->db_instances[ $table ] = new DB( $table, $schema );
		return $this->db_instances[ $table ];
	}

	/**
	 * Returns schema of relations table
	 *
	 * @return [type] [description]
	 */
	public function get_db_schema() {
		return array(
			'rel_id'           => 'TEXT',
			'parent_rel'       => 'INT',
			'parent_object_id' => 'BIGINT',
			'child_object_id'  => 'BIGINT',
		);
	}

	/**
	 * Returns schema of relations meta table
	 *
	 * @return [type] [description]
	 */
	public function get_meta_db_schema() {
		return array(
			'rel_id'           => 'TEXT',
			'parent_object_id' => 'BIGINT',
			'child_object_id'  => 'BIGINT',
			'meta_key'         => 'TEXT',
			'meta_value'       => 'TEXT',
		);
	}

}
