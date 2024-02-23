<?php
/**
 * Main class file
 *
 * @package Temporary Login Without Password
 */

/**
 * Class Wp_Temporary_Login_Without_Password
 *
 * @package Temporary Login Without Password
 */
class Wp_Temporary_Login_Without_Password {

	/**
	 * Loader.
	 *
	 * @var string $loader Loader.
	 */
	protected $loader;

	/**
	 * Plugin Name.
	 *
	 * @var string $plugin_name Plugin Name.
	 */
	protected $plugin_name;

	/**
	 * Plugin Version
	 *
	 * @var string $version Plugin Version.
	 */
	protected $version;

	/**
	 * Wp_Temporary_Login_Without_Password constructor.
	 */
	public function __construct() {
		global $tlwp_feedback, $tlwp_tracker;

		$this->plugin_name = 'temporary-login-without-password';
		$this->version     = WTLWP_PLUGIN_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		$tlwp_tracker = 'IG_Tracker_V_' . str_replace( '.', '_', WTLWP_TRACKER_VERSION );
		if ( is_admin() ) {
			$ig_feedback_class = 'IG_Feedback_V_' . str_replace( '.', '_', WTLWP_FEEDBACK_VERSION );
			$tlwp_feedback     = new $ig_feedback_class( 'Temporary Login Without Password', 'temporary-login-without-password', 'tlwp', 'tlwp.', false );
			$tlwp_feedback->render_deactivate_feedback();
		}
	}

	/**
	 * Load dependencies.
	 *
	 * @since 1.0.0
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-temporary-login-without-password-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-temporary-login-without-password-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-temporary-login-without-password-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-temporary-login-without-password-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-temporary-login-without-password-common.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-temporary-login-without-password-layout.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wtlwp-system-info.php';


		// Feedback
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/feedback/class-ig-tracker.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/feedback/class-ig-feedback.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/feedback.php';

		$this->loader = new Wp_Temporary_Login_Without_Password_Loader();
	}

	/**
	 * Set Localization.
	 *
	 * @since   1.0.0
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_Temporary_Login_Without_Password_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Define Admin Hooks.
	 *
	 * @since   1.0.0
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wp_Temporary_Login_Without_Password_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
		$this->loader->add_action( 'network_admin_menu', $plugin_admin, 'admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'create_user' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'update_tlwp_settings' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'manage_temporary_login' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'tlwp_display_admin_notices' );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'tlwp_show_temporary_access_notice_in_admin_bar', 999 );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'tlwp_test_mode_notice_admin_bar_css', 999 );

		$this->loader->add_filter( 'wpmu_welcome_notification', $plugin_admin, 'disable_welcome_notification', 10, 5 );
		$this->loader->add_filter( 'plugin_action_links', $plugin_admin, 'disable_plugin_deactivation', 10, 4 );
		$this->loader->add_filter( 'plugin_action_links_' . WTLWP_PLUGIN_BASE_NAME, $plugin_admin, 'plugin_add_settings_link', 10, 4 );

		$this->loader->add_action( 'admin_print_scripts', $plugin_admin, 'remove_admin_notices' );

		$this->loader->add_action( 'in_plugin_update_message-temporary-login-without-password/temporary-login-without-password.php', $plugin_admin, 'in_plugin_update_message', 10, 2 );

		$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'update_admin_footer_text' );

		$this->loader->add_action( 'wp_loaded', $plugin_admin, 'wtlwp_may_be_show_sa_in_app_offer' );

	}

	/**
	 * Defind Admin hooks.
	 *
	 * @since 1.0.0
	 */
	private function define_public_hooks() {

		$plugin_public = new Wp_Temporary_Login_Without_Password_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_public, 'init_wtlwp' );
		$this->loader->add_filter( 'wp_authenticate_user', $plugin_public, 'disable_temporary_user_login', 10, 2 );
		$this->loader->add_filter( 'allow_password_reset', $plugin_public, 'disable_password_reset', 10, 2 );
	}

	/**
	 * Start Loading.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Get Plugin Name.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Get Loader Class.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Get Plugin Version
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

}
