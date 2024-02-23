<?php
namespace Jet_Engine\Modules\Custom_Content_Types;

use Jet_Engine\Modules\Custom_Content_Types\Forms\Preset;

/**
 * @property Preset form_preset
 *
 * Class Module
 * @package Jet_Engine\Modules\Custom_Content_Types
 */
class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	public $slug = 'custom-content-types';
	public $manager = null;
	public $rest_controller = null;
	public $listings = null;
	public $export = null;
	public $form_preset = null;

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), -1 );
	}

	/**
	 * Init module components
	 *
	 * @return [type] [description]
	 */
	public function init() {

		require_once $this->module_path( 'db.php' );
		require_once $this->module_path( 'manager.php' );
		require_once $this->module_path( 'listings/manager.php' );
		require_once $this->module_path( 'rest-api/public-controller.php' );

		$this->manager         = new Manager();
		$this->listings        = new Listings\Manager();
		$this->rest_controller = new Rest\Public_Controller();

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			require_once $this->module_path( 'elementor/manager.php' );
			new Elementor\Manager();
		}

		add_action(
			'jet-engine/rest-api/init-endpoints',
			array( $this->query_dialog(), 'register_api_endpoint' )
		);

		if ( jet_engine()->modules->is_module_active( 'booking-forms' ) ) {
			require_once $this->module_path( 'forms/notification.php' );

			new Forms\Notification();
		}
		require_once $this->module_path( 'forms/preset.php' );
		require_once $this->module_path( 'forms/create-form.php' );
		require_once $this->module_path( 'forms/create-jfb-form.php' );
		require_once $this->module_path( 'forms/preset-jfb.php' );
		require_once $this->module_path( 'forms/fields-jfb.php' );

		$this->form_preset = new Forms\Preset();
		new Forms\Create_Form();
		new Forms\Create_Jfb_Form();
		new Forms\Preset_Jfb();
		new Forms\Fields_Jfb();

		if ( jet_engine()->modules->is_module_active( 'data-stores' ) ) {
			require_once $this->module_path( 'data-stores/manager.php' );
			new Data_Stores\Manager();
		}

		if ( is_admin() ) {
			require_once $this->module_path( 'export.php' );
			$this->export = new Export();

			require_once $this->module_path( 'dashboard/skins-export-import.php' );
			new Dashboard\Skins_Export_Import();

			require_once $this->module_path( 'delete-users.php' );
			new Delete_Users();
		}

		require_once $this->module_path( 'query-builder/manager.php' );
		Query_Builder\Manager::instance();

		require_once $this->module_path( 'forms/action-manager.php' );

		/** Integration with JetFormBuilder */
		new Forms\Action_Manager();
	}

	public function query_dialog() {

		if ( ! class_exists( '\Jet_Engine\Modules\Custom_Content_Types\Query_Dialog' ) ) {
			require_once $this->module_path( 'query-dialog.php' );
		}

		return Query_Dialog::instance();

	}

	/**
	 * Return path inside module
	 *
	 * @param  string $relative_path [description]
	 * @return [type]                [description]
	 */
	public function module_path( $relative_path = '' ) {
		return jet_engine()->modules->modules_path( 'custom-content-types/inc/' . $relative_path );
	}

	/**
	 * Return url inside module
	 *
	 * @param  string $relative_path [description]
	 * @return [type]                [description]
	 */
	public function module_url( $relative_path = '' ) {
		return jet_engine()->plugin_url( 'includes/modules/custom-content-types/inc/' . $relative_path );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}
