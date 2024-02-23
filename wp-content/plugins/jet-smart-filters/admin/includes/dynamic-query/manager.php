<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

if ( ! class_exists( 'Jet_Smart_Filters_Admin_Dynamic_Query_Manager' ) ) {
	/**
	 * Jet Smart Filters Admin Dynamic Query Manager class
	 */
	class Jet_Smart_Filters_Admin_Dynamic_Query_Manager {

		public $list = [];
		
		public function __construct() {
			// Init
			require jet_smart_filters()->plugin_path( 'admin/includes/dynamic-query/base.php' );

			add_action( 'admin_enqueue_scripts', function() {
				do_action( 'jet-smart-filters/admin/register-dynamic-query', $this );
			} );
		}
		
		public function register_item( $dynamic_query_instance ) {

			$this->list[ $dynamic_query_instance->get_name() ] = $dynamic_query_instance;
		}

		public function register_items( $items_list ) {

			foreach ( $items_list as $itemKey => $itemLabel ) {
				$new_item = new class( $itemKey, $itemLabel ) extends Jet_Smart_Filters_Admin_Dynamic_Query_Base {
					public function __construct( $itemKey, $itemLabel ) {
						$this->itemKey = $itemKey;
						$this->itemLabel = $itemLabel;
					}

					public function get_name() {
						return $this->itemKey;
					}

					public function get_label() {
						return $this->itemLabel;
					}
				};

				$this->register_item( $new_item );
			}
		}
	}
}
