<?php
/**
 * Abstract class for WordPress global instances like Post Types, Taxonomies etc.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! trait_exists( 'Jet_Engine_Notices_Trait' ) ) {
	require_once jet_engine()->plugin_path( 'includes/traits/notices.php' );
}

if ( ! class_exists( 'Jet_Engine_Base_WP_Intance' ) ) {

	/**
	 * Define Jet_Engine_Base_WP_Intance class
	 */
	abstract class Jet_Engine_Base_WP_Intance {

		use Jet_Engine_Notices_Trait;

		/**
		 * Instances init priority
		 *
		 * @var integer
		 */
		public $init_priority = 10;

		/**
		 * Base slug for instance-related pages
		 * @var string
		 */
		public $page = null;

		/**
		 * Instance pages
		 *
		 * @var array
		 */
		public $_pages = array();

		/**
		 * Instance action request key
		 *
		 * @var string
		 */
		public $action_key = null;

		/**
		 * Data manger instance
		 */
		public $data = null;

		/**
		 * Set object type
		 * @var string
		 */
		public $object_type = null;

		/**
		 * Items list
		 *
		 * @var null
		 */
		public $items = null;

		/**
		 * OPtions page builder instance
		 *
		 * @var [type]
		 */
		public $builder;

		/**
		 * Constructor for the class
		 */
		function __construct() {

			add_action( 'init', array( $this, 'register_instances' ), $this->init_priority );

			$this->init_data();

			add_action( 'jet-engine/rest-api/init-endpoints', array( $this, 'init_rest' ) );

			$this->init_admin_pages();

		}

		/**
		 * Initialize instance-related admin pages
		 *
		 * @return [type] [description]
		 */
		public function init_admin_pages() {

			if ( is_admin() ) {
				add_action( 'admin_menu', array( $this, 'add_menu_page' ), 20 );
			}

			if ( ! $this->is_cpt_page() ) {
				return;
			}

			add_action( 'admin_init', array( $this, 'register_pages' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ), 0 );
			add_action( 'admin_init', array( $this, 'handle_actions' ) );

		}

		/**
		 * Return instance items
		 *
		 * @return [type] [description]
		 */
		public function get_items() {

			if ( ! $this->items ) {
				$this->items = $this->data->get_item_for_register();
			}

			return $this->items;
		}

		/**
		 * Run actions handlers
		 *
		 * @return void
		 */
		public function handle_actions() {

			if ( ! isset( $_GET['action'] ) ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$core_actions = array(
				'create_item' => array( $this->data, 'create_item' ),
				'edit_item'   => array( $this->data, 'edit_item' ),
				'delete_item' => array( $this->data, 'delete_item' ),
			);

			$action = $_GET['action'];

			if ( ! isset( $core_actions[ $action ] ) ) {
				return;
			}

			call_user_func( $core_actions[ $action ] );

		}

		/**
		 * Register CPT menu page
		 */
		public function add_menu_page() {

			add_submenu_page(
				jet_engine()->admin_page,
				$this->get_page_title(),
				$this->get_page_title(),
				'manage_options',
				$this->page_slug(),
				array( $this, 'render_page' )
			);

		}

		/**
		 * Check if CPT-related page currently displayed
		 *
		 * @return boolean
		 */
		public function is_cpt_page() {
			return ( isset( $_GET['page'] ) && $this->page_slug() === $_GET['page'] );
		}

		/**
		 * Enqueue admin pages assets
		 *
		 * @return void
		 */
		public function enqueue_assets() {

			wp_enqueue_style(
				'jet-engine-admin-pages',
				jet_engine()->plugin_url( 'assets/css/admin/pages.css' ),
				array(),
				jet_engine()->get_version()
			);

			jet_engine()->accessibility->contrast_ui( 'jet-engine-admin-pages' );

		}

		/**
		 * Register CPT related pages
		 *
		 * @return void
		 */
		public function register_pages() {

			if ( $this->data ) {
				$this->data->ensure_db_table();
			}

			if ( ! class_exists( 'Jet_Engine_CPT_Page_Base' ) ) {
				require_once jet_engine()->plugin_path( 'includes/base/base-admin-page.php' );
			}

			$default = $this->get_instance_pages();

			foreach ( $default as $class => $file ) {
				if ( is_object( $file ) ) {
					$this->register_page( $file );
				} else {
					require $file;
					$this->register_page( $class );
				}
			}

			/**
			 * You could register custom pages on this hook
			 */
			do_action( 'jet-engine/pages/' . $this->instance_slug() . '/register', $this );

		}

		/**
		 * Register new dashboard page
		 *
		 * @return [type] [description]
		 */
		public function register_page( $class ) {

			if ( is_object( $class ) ) {
				$page = $class;
			} else {
				$page = new $class( $this );
			}

			$this->_pages[ $page->get_slug() ] = $page;

		}

		/**
		 * Return page slug
		 *
		 * @return string
		 */
		public function page_slug() {
			return $this->page;
		}

		/**
		 * Render CPT page
		 *
		 * @return void
		 */
		public function render_page() {

			$page = $this->get_current_page();

			if ( ! $page ) {
				return;
			}
			?>
			<div class="wrap">
				<div class="cpt-header">
					<h1 class="wp-heading-inline"><?php echo $page->get_name(); ?></h1>
					<?php do_action( 'jet-engine/' . $this->instance_slug() . '/page/after-title', $page ); ?>
					<hr class="wp-header-end">
				</div>
				<?php $this->print_notices(); ?>
				<div class="cpt-content">
					<?php $page->render_page(); ?>
				</div>
			</div>
			<?php


		}

		/**
		 * Get requested page link
		 *
		 * @param  [type] $page [description]
		 * @return [type]       [description]
		 */
		public function get_page_link( $page = null ) {

			if ( ! $page ) {
				return add_query_arg(
					array(
						'page' => $this->page_slug(),
					),
					esc_url( admin_url( 'admin.php' ) )
				);
			} else {
				return add_query_arg(
					array(
						'page'            => $this->page_slug(),
						$this->action_key => $page,
					),
					esc_url( admin_url( 'admin.php' ) )
				);
			}
		}

		/**
		 * Returns url to current instance edit item page (if it exists in this instance
		 * )
		 * @param  string|number $id Item ID
		 * @return string
		 */
		public function get_edit_item_link( $id = null ) {
			return add_query_arg(
				array(
					'id' => $id,
				),
				$this->get_page_link( 'edit' )
			);
		}

		/**
		 * Returns current page object
		 *
		 * @return object
		 */
		public function get_current_page() {

			$action = isset( $_GET[ $this->action_key ] ) ? $_GET[ $this->action_key ] : 'list';
			$page   = isset( $this->_pages[ $action ] ) ? $this->_pages[ $action ] : false;

			return $page;

		}

		/**
		 * Initiizlize instance specific API endpoints
		 *
		 * @param  Jet_Engine_REST_API $api_manager API manager instance.
		 * @return void
		 */
		public function init_rest( $api_manager ) {
			/**
			 * Rewrti this funciton in extended class to init Rest API endpoints for this instance
			 */
		}

		/**
		 * Return admin pages for current instance
		 *
		 * @return array
		 */
		public function get_instance_pages() {
			return array();
		}

		/**
		 * Init data instance
		 *
		 * @return [type] [description]
		 */
		abstract public function init_data();

		/**
		 * Register current object instances
		 *
		 * @return void
		 */
		abstract public function register_instances();

		/**
		 * Returns current menu page title (for JetEngine submenu)
		 * @return [type] [description]
		 */
		abstract public function get_page_title();

		/**
		 * Returns current instance slug
		 *
		 * @return [type] [description]
		 */
		abstract public function instance_slug();

	}

}
