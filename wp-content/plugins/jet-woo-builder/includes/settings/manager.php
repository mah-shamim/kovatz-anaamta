<?php
namespace Jet_Woo_Builder;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Controller class
 */
class Settings {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Contain modules subpages
	 *
	 * @var array
	 */
	public $subpage_modules = [];

	// Here initialize our namespace and resource name.
	public function __construct() {

		$this->subpage_modules = apply_filters( 'jet-woo-builder/settings/registered-subpage-modules', [
			'jet-woo-builder-general-settings' => [
				'class' => '\\Jet_Woo_Builder\\Settings\\General',
				'args'  => [],
			],
			'jet-woo-builder-available-addons' => [
				'class' => '\\Jet_Woo_Builder\\Settings\\Available_Addons',
				'args'  => [],
			],
		] );

		add_action( 'init', [ $this, 'register_settings_category' ], 10 );
		add_action( 'init', [ $this, 'init_plugin_subpage_modules' ], 10 );

	}

	/**
	 * Register settings page category
	 */
	public function register_settings_category() {
		\Jet_Dashboard\Dashboard::get_instance()->module_manager->register_module_category( array(
			'name'     => esc_html__( 'JetWooBuilder', 'jet-woo-builder' ),
			'slug'     => 'jet-woo-builder-settings',
			'priority' => 1
		) );
	}

	/**
	 * Initialize plugin subpages modules
	 */
	public function init_plugin_subpage_modules() {

		require jet_woo_builder()->plugin_path( 'includes/settings/subpage-modules/general.php' );
		require jet_woo_builder()->plugin_path( 'includes/settings/subpage-modules/available-addons.php' );

		foreach ( $this->subpage_modules as $subpage => $subpage_data ) {
			\Jet_Dashboard\Dashboard::get_instance()->module_manager->register_subpage_module( $subpage, $subpage_data );
		}

	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;

	}

}

