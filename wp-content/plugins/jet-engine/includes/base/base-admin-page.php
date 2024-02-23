<?php
/**
 * Base class for CPT page
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CPT_Page_Base' ) ) {

	/**
	 * Define Jet_Engine_CPT_Page_Base class
	 */
	abstract class Jet_Engine_CPT_Page_Base {

		/**
		 * Manager instance
		 *
		 * @var Jet_Engine_CPT
		 */
		public $manager = null;

		/**
		 * Check if is default page for current parent manager
		 *
		 * @var boolean
		 */
		public $is_default = false;

		/**
		 * Class constructor
		 */
		public function __construct( $manager ) {

			$this->manager = $manager;

			if ( $this->is_page_now() ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'page_specific_assets' ) );
			}

		}

		/**
		 * Register page specific assets
		 * ]
		 * @return [type] [description]
		 */
		public function page_specific_assets() {}

		/**
		 * Check if this page is currently requested
		 *
		 * @return boolean [description]
		 */
		public function is_page_now() {

			if ( ! $this->manager->is_cpt_page() ) {
				return false;
			}

			$key = $this->manager->action_key;

			if ( $this->is_default && ! isset( $_GET[ $key ] ) ) {
				return true;
			}

			if ( ! isset( $_GET[ $key ] ) || $this->get_slug() !== $_GET[ $key ] ) {
				return false;
			}

			return true;

		}

		/**
		 * Returns current page URL
		 *
		 * @return string
		 */
		public function get_current_page_link() {

			return add_query_arg(
				array(
					'page'                     => $this->manager->page_slug(),
					$this->manager->action_key => $this->get_slug(),
				),
				esc_url( admin_url( 'admin.php' ) )
			);

		}

		/**
		 * Register interface builder controls
		 *
		 * @return void
		 */
		public function register_controls() {}

		/**
		 * Page slug
		 *
		 * @return string
		 */
		abstract public function get_slug();

		/**
		 * Page name
		 *
		 * @return string
		 */
		abstract public function get_name();

		/**
		 * Renderer callback
		 *
		 * @return void
		 */
		abstract public function render_page();

	}

}
