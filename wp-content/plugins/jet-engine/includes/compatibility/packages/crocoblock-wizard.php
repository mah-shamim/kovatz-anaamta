<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CB_Wizard_Package' ) ) {

	/**
	 * Define Jet_Engine_CB_Wizard_Package class
	 */
	class Jet_Engine_CB_Wizard_Package {

		/**
		 * Constructor for the class
		 */
		public function __construct() {
			add_filter( 'crocoblock-wizard/export/tables-to-export', array( $this, 'export_tables' ) );
			add_filter( 'crocoblock-wizard/export/options-to-export', array( $this, 'export_options' ) );
		}

		/**
		 * Add JetEngine custom tables to export tables
		 */
		public function export_tables( $tables_to_export = array() ) {

			if ( ! is_array( $tables_to_export ) ) {
				$tables_to_export = array();
			}

			$all_tables = jet_engine()->db->tables( null, 'all' );

			foreach ( $all_tables as $table ) {
				$tables_to_export[] = $table['export_name'];
			}

			return $tables_to_export;

		}

		/**
		 * Add JetEngine options to export options list
		 */
		public function export_options( $options_to_export = array() ) {

			if ( ! is_array( $options_to_export ) ) {
				$options_to_export = array();
			}

			$options_to_export[] = jet_engine()->meta_boxes->data->option_name;
			$options_to_export[] = jet_engine()->relations->data->option_name;
			$options_to_export[] = jet_engine()->modules->option_name;

			if ( jet_engine()->modules->is_module_active( 'profile-builder' ) ) {
				$options_to_export[] = 'profile-builder';
			}

			return $options_to_export;

		}

	}

}

if ( is_admin() ) {
	new Jet_Engine_CB_Wizard_Package();
}
