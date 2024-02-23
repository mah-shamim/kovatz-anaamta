<?php
/**
 * Modules manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Modules' ) ) {

	/**
	 * Define Jet_Engine_Modules class
	 */
	class Jet_Engine_Modules {

		public  $option_name    = 'jet_engine_modules';
		private $modules        = array();
		private $active_modules = array();

		public $installer;
		public $updater;

		/**
		 * Constructor for the class
		 */
		function __construct() {

			include $this->modules_path( 'modules-installer.php' );
			include $this->modules_path( 'modules-updater.php' );

			$this->installer = new Jet_Engine_Modules_Installer();
			$this->updater   = new Jet_Engine_Modules_Updater();

			$this->preload_modules();
			$this->init_active_modules();

			add_action( 'wp_ajax_jet_engine_save_modules', array( $this, 'save_modules' ) );

		}

		/**
		 * Save active modules
		 *
		 * @return [type] [description]
		 */
		public function save_modules() {

			$nonce_action = jet_engine()->dashboard->get_nonce_action();

			if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], $nonce_action ) ) {
				wp_send_json_error( array(
					'message' => __( 'Nonce validation failed', 'jet-engine' ),
				) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array(
					'message' => 'You don\'t have permissions to do this',
				) );
			}

			$reload         = false;
			$current        = get_option( $this->option_name, array() );
			$new            = isset( $_REQUEST['modules'] ) ? $_REQUEST['modules'] : array();
			$activated      = array_diff( $new, $current );
			$deactivated    = array_diff( $current, $new );
			$reload_modules = array(
				'booking-forms',
				'profile-builder',
				'maps-listings',
				'data-stores',
				'custom-content-types',
				'rest-api-listings',
			);

			foreach ( $reload_modules as $module ) {
				if ( in_array( $module, $activated ) || in_array( $module, $deactivated ) ) {
					$reload = true;
				}
			}

			update_option( $this->option_name, $new );

			wp_send_json_success( array( 'reload' => $reload ) );

		}

		/**
		 * Deactivate separate module
		 *
		 * @param  [type] $module [description]
		 * @return [type]         [description]
		 */
		public function deactivate_module( $module ) {

			$active_modules = get_option( $this->option_name, array() );

			if ( ! $active_modules || ! is_array( $active_modules ) ) {
				$active_modules = array();
				update_option( $this->option_name, $active_modules );
				return;
			}

			$index = array_search( $module, $active_modules );

			if ( false !== $index ) {
				unset( $active_modules[ $index ] );
			}

			update_option( $this->option_name, $active_modules );

		}

		/**
		 * Activate separate module
		 *
		 * @param  [type] $module [description]
		 * @return [type]         [description]
		 */
		public function activate_module( $module ) {

			$active_modules = get_option( $this->option_name, array() );

			if ( ! $active_modules || ! is_array( $active_modules ) ) {
				$active_modules = array();
			}

			if ( ! in_array( $module, $active_modules ) ) {
				$active_modules[] = $module;
			}

			update_option( $this->option_name, $active_modules );

		}

		/**
		 * Returns path to file inside modules dir
		 *
		 * @param  [type] $path [description]
		 * @return [type]       [description]
		 */
		public function modules_path( $path ) {
			return jet_engine()->plugin_path( 'includes/modules/' . $path );
		}

		/**
		 * Returns url to file inside modules dir
		 *
		 * @param  [type] $path [description]
		 * @return [type]       [description]
		 */
		public function modules_url( $path ) {
			return jet_engine()->plugin_url( 'includes/modules/' . $path );
		}

		/**
		 * Preload modules
		 *
		 * @return void
		 */
		public function preload_modules() {

			$path        = jet_engine()->plugin_path( 'includes/modules/' );
			$all_modules = apply_filters( 'jet-engine/available-modules', array(

				// Internal modules
				'Jet_Engine_Module_Gallery_Grid'           => $path . 'gallery/grid.php',
				'Jet_Engine_Module_Gallery_Slider'         => $path . 'gallery/slider.php',
				'Jet_Engine_Module_QR_Code'                => $path . 'qr-code/qr-code.php',
				'Jet_Engine_Module_Calendar'               => $path . 'calendar/calendar.php',
				'Jet_Engine_Module_Booking_Forms'          => $path . 'forms/forms.php',
				'Jet_Engine_Module_Listing_Injections'     => $path . 'listing-injections/listing-injections.php',
				'Jet_Engine_Module_Profile_Builder'        => $path . 'profile-builder/profile-builder.php',
				'Jet_Engine_Module_Maps_Listings'          => $path . 'maps-listings/maps-listings.php',
				'Jet_Engine_Module_Dynamic_Visibility'     => $path . 'dynamic-visibility/dynamic-visibility.php',
				'Jet_Engine_Module_Data_Stores'            => $path . 'data-stores/data-stores.php',
				'Jet_Engine_Module_Custom_Content_Types'   => $path . 'custom-content-types/custom-content-types.php',
				'Jet_Engine_Module_Rest_Api_Listings'      => $path . 'rest-api-listings/rest-api-listings.php',
				'Jet_Engine_Module_Fullwidth_Block_Editor' => $path . 'fullwidth-block-editor/fullwidth-block-editor.php',

				// External modules
				'Jet_Engine_Module_Dynamic_Tables'               => $path . 'external-dynamic-tables/dynamic-tables.php',
				'Jet_Engine_Module_Dynamic_Charts'               => $path . 'external-dynamic-charts/dynamic-charts.php',
				'Jet_Engine_Module_Attachment_Link_Callback'     => $path . 'external-attachment-link-callback/attachment-link-callback.php',
				'Jet_Engine_Module_Custom_Visibility_Conditions' => $path . 'external-custom-visibility-conditions/custom-visibility-conditions.php',
				'Jet_Engine_Module_Trim_Callback'                => $path . 'external-trim-callback/trim-callback.php',
				'Jet_Engine_Module_Post_Expiration_Period'       => $path . 'external-post-expiration-period/post-expiration-period.php',

			) );

			require_once jet_engine()->plugin_path( 'includes/base/base-module.php' );
			require_once jet_engine()->plugin_path( 'includes/base/base-external-module.php' );

			foreach ( $all_modules as $module => $file ) {
				require $file;
				$instance = new $module;
				$this->modules[ $instance->module_id() ] = $instance;
			}

		}

		/**
		 * Initialize active modulles
		 *
		 * @return void
		 */
		public function init_active_modules() {

			// Initialize built-in modules
			require_once jet_engine()->plugin_path( 'includes/modules/performance/performance.php' );

			foreach( array( new Jet_Engine_Module_Performance() ) as $module ) {
				$module->module_init();
			}

			$modules = $this->get_active_modules();

			if ( empty( $modules ) ) {
				return;
			}

			/**
			 * Check if is new modules format or old
			 */
			if ( ! isset( $modules['gallery-grid'] ) ) {

				$fixed = array();

				foreach ( $modules as $module ) {
					$fixed[ $module ] = 'true';
				}

				$modules = $fixed;

			}

			foreach ( $modules as $module => $is_active ) {
				if ( 'true' === $is_active ) {
					$module_instance = isset( $this->modules[ $module ] ) ? $this->modules[ $module ] : false;
					if ( $module_instance ) {
						call_user_func( array( $module_instance, 'module_init' ) );
						$this->active_modules[] = $module;
					}
				}
			}

		}

		/**
		 * Get all modules list in format required for JS
		 *
		 * @return [type] [description]
		 */
		public function get_all_modules_for_js( $extra_data = false, $type = false ) {

			$result = array();

			foreach ( $this->modules as $module ) {

				if ( $type && 'internal' === $type && $module->external_slug() ) {
					continue;
				}

				if ( $type && 'external' === $type && ! $module->external_slug() ) {
					continue;
				}

				$module_data = array(
					'value'       => $module->module_id(),
					'label'       => $module->module_name(),
					'embed'       => $module->get_video_embed(),
					'isElementor' => $module->support_elementor(),
					'isBlocks'    => $module->support_blocks(),
				);

				if ( $extra_data ) {
					$module_data['details'] = $module->get_module_details();
					$module_data['links']   = $this->get_module_links( $module );
				}

				if ( $type && 'external' === $type ) {
					$module_data['is_related_plugin_active'] = $module->is_related_plugin_active();
					$module_data['plugin_data']              = $module->get_related_plugin_data();
				}

				$result[] = $module_data;

			}

			return $result;



		}

		public function get_module_links( $module ) {

			$links  = $module->get_module_links();
			$result = array();

			if ( empty( $links ) ) {
				return $result;
			}

			foreach ( $links as $link ) {

				if ( empty( $link['is_video'] ) ) {
					$link['url'] = add_query_arg( array(
						'utm_campaign' => 'need-help',
						'utm_source'   => 'jetengine-modules',
						'utm_medium'   => $module->module_id(),
					), $link['url'] );
				}

				$result[] = $link;

			}

			return $result;

		}

		/**
		 * Get all modules list
		 *
		 * @return [type] [description]
		 */
		public function get_all_modules() {
			$result = array();
			foreach ( $this->modules as $module ) {
				$result[ $module->module_id() ] = $module->module_name();
			}
			return $result;
		}

		/**
		 * Get active modules list
		 *
		 * @return [type] [description]
		 */
		public function get_active_modules() {

			$active_modules = get_option( $this->option_name, array() );

			// backward compatibility
			if ( ! empty( $active_modules ) ) {
				if ( in_array( 'true', $active_modules ) || in_array( 'false', $active_modules ) ) {
					$new_format = array();
					foreach ( $active_modules as $module => $is_active ) {
						if ( 'true' === $is_active ) {
							$new_format[] = $module;
						}
					}
					$active_modules = $new_format;
				}

			}

			return array_values( $active_modules );
		}

		/**
		 * Check if pased module is currently active
		 *
		 * @param  [type]  $module_id [description]
		 * @return boolean            [description]
		 */
		public function is_module_active( $module_id = null ) {
			return in_array( $module_id, $this->active_modules );
		}

		/**
		 * Get module instance by module ID
		 *
		 * @param  [type] $module_id [description]
		 * @return [type]            [description]
		 */
		public function get_module( $module_id = null ) {
			return isset( $this->modules[ $module_id ] ) ? $this->modules[ $module_id ] : false;
		}

	}

}
