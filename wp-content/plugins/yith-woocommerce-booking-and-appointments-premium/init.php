<?php
/**
 * Plugin Name: YITH Booking and Appointment for WooCommerce Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-booking/
 * Description: <code><strong>YITH Booking and Appointment for WooCommerce</strong></code> allows you to create and manage Booking Products. You can create monthly/daily/hourly/per-minute booking products with Services and People by setting costs and availability. You can also synchronize your booking products with external services such as Booking.com or Airbnb. Moreover, it includes Google Calendar integration, Google Maps, Search Forms, YITH Booking theme, and many other features! <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 3.1.2
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-booking-for-woocommerce
 * Domain Path: /languages/
 * WC requires at least: 4.5.0
 * WC tested up to: 5.9.x
 *
 * @author  YITH
 * @package YITH Booking and Appointment for WooCommerce Premium
 * @version 3.1.2
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
	include_once 'plugin-upgrade/functions-yith-licence.php';
}
register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );


if ( ! defined( 'YITH_WCBK_VERSION' ) ) {
	define( 'YITH_WCBK_VERSION', '3.1.2' );
}

if ( ! defined( 'YITH_WCBK_INIT' ) ) {
	define( 'YITH_WCBK_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCBK' ) ) {
	define( 'YITH_WCBK', true );
}

if ( ! defined( 'YITH_WCBK_PREMIUM' ) ) {
	define( 'YITH_WCBK_PREMIUM', true );
}

if ( ! defined( 'YITH_WCBK_FILE' ) ) {
	define( 'YITH_WCBK_FILE', __FILE__ );
}

if ( ! defined( 'YITH_WCBK_URL' ) ) {
	define( 'YITH_WCBK_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCBK_DIR' ) ) {
	define( 'YITH_WCBK_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCBK_DOMPDF_DIR' ) ) {
	define( 'YITH_WCBK_DOMPDF_DIR', YITH_WCBK_DIR . 'lib/dompdf/' );
}

if ( ! defined( 'YITH_WCBK_TEMPLATE_PATH' ) ) {
	define( 'YITH_WCBK_TEMPLATE_PATH', YITH_WCBK_DIR . 'templates/' );
}

if ( ! defined( 'YITH_WCBK_VIEWS_PATH' ) ) {
	define( 'YITH_WCBK_VIEWS_PATH', YITH_WCBK_DIR . 'views/' );
}

if ( ! defined( 'YITH_WCBK_ASSETS_URL' ) ) {
	define( 'YITH_WCBK_ASSETS_URL', YITH_WCBK_URL . 'assets' );
}

if ( ! defined( 'YITH_WCBK_ASSETS_PATH' ) ) {
	define( 'YITH_WCBK_ASSETS_PATH', YITH_WCBK_DIR . 'assets' );
}

if ( ! defined( 'YITH_WCBK_LANGUAGES_PATH' ) ) {
	define( 'YITH_WCBK_LANGUAGES_PATH', YITH_WCBK_DIR . 'languages/' );
}

if ( ! defined( 'YITH_WCBK_INCLUDES_PATH' ) ) {
	define( 'YITH_WCBK_INCLUDES_PATH', YITH_WCBK_DIR . 'includes' );
}

if ( ! defined( 'YITH_WCBK_SLUG' ) ) {
	define( 'YITH_WCBK_SLUG', 'yith-woocommerce-booking' );
}

if ( ! defined( 'YITH_WCBK_SECRET_KEY' ) ) {
	define( 'YITH_WCBK_SECRET_KEY', 'pJaiF0sH1JraDv721O9m' );
}

if ( ! defined( 'YITH_WCBK_PLUGIN_NAME' ) ) {
	define( 'YITH_WCBK_PLUGIN_NAME', 'YITH Booking and Appointment for WooCommerce' );
}

/**
 * Print admin notice if WooCommerce is not enabled
 */
function yith_wcbk_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p>
			<?php
			// translators: %s is the plugin name.
			echo esc_html( sprintf( __( '%s is enabled but not effective. It requires WooCommerce in order to work.', 'yith-booking-for-woocommerce' ), YITH_WCBK_PLUGIN_NAME ) );
			?>
		</p>
	</div>
	<?php
}

/**
 * Plugin init
 */
function yith_wcbk_init() {
	load_plugin_textdomain( 'yith-booking-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once 'includes/functions.yith-wcbk.php';
	require_once 'includes/traits/trait-yith-wcbk-singleton-trait.php';

	require_once 'includes/class-yith-wcbk-maps.php';
	require_once 'includes/class-yith-wcbk-shortcodes.php';
	require_once 'includes/class-yith-wcbk-notifier.php';
	require_once 'includes/abstract-yith-wcbk-db.php';
	require_once 'includes/class-yith-wcbk-language.php';
	require_once 'includes/class-yith-wcbk-notes.php';
	require_once 'includes/class-yith-wcbk-person-type-helper.php';
	require_once 'includes/class-yith-wcbk-extra-cost-helper.php';
	require_once 'includes/class-yith-wcbk-printer.php';
	require_once 'includes/class-yith-wcbk-cart.php';
	require_once 'includes/class-yith-wcbk-orders.php';
	require_once 'includes/class-yith-wcbk-frontend.php';
	require_once 'includes/class-yith-wcbk-frontend-action-handler.php';
	require_once 'includes/class-yith-wcbk-admin.php';
	require_once 'includes/class-yith-wcbk-admin-notices.php';
	require_once 'includes/class-yith-wcbk.php';
	require_once 'includes/class-yith-wcbk-product-post-type-admin.php';
	require_once 'includes/class-yith-wcbk-date-helper.php';
	require_once 'includes/class-yith-wcbk-ajax.php';
	require_once 'includes/class-yith-wcbk-service-tax-admin.php';
	require_once 'includes/class-yith-wcbk-search-form-frontend.php';
	require_once 'includes/class-yith-wcbk-search-form-helper.php';
	require_once 'includes/class-yith-wcbk-service-helper.php';
	require_once 'includes/class-yith-wcbk-settings.php';
	require_once 'includes/class-yith-wcbk-cache.php';
	require_once 'includes/class-yith-wcbk-google-calendar.php';
	require_once 'includes/class-yith-wcbk-google-calendar-sync.php';
	require_once 'includes/class-yith-wcbk-logger.php';
	require_once 'includes/class-yith-wcbk-cron.php';
	require_once 'includes/class-yith-wcbk-theme.php';
	require_once 'includes/class-yith-wcbk-endpoints.php';
	require_once 'includes/class-yith-wcbk-post-types.php';

	require_once 'includes/background-process/class-yith-wcbk-background-processes.php';

	// Objects.
	require_once 'includes/objects/class-yith-wcbk-data.php';
	require_once 'includes/objects/abstract-yith-wcbk-simple-object.php';
	require_once 'includes/objects/class-wc-product-booking.php';
	require_once 'includes/objects/class-yith-wcbk-product-extra-cost.php';
	require_once 'includes/objects/class-yith-wcbk-product-extra-cost-custom.php';
	require_once 'includes/objects/class-yith-wcbk-availability-rule-legacy.php';
	require_once 'includes/objects/class-yith-wcbk-availability-rule.php';
	require_once 'includes/objects/class-yith-wcbk-availability.php';
	require_once 'includes/objects/class-yith-wcbk-price-rule.php';
	require_once 'includes/objects/class-yith-wcbk-search-form.php';
	require_once 'includes/objects/class-yith-wcbk-service.php';
	require_once 'includes/objects/class-yith-wcbk-booking-data-query.php';

	require_once 'includes/objects/abstract-yith-wcbk-booking.php';
	require_once 'includes/objects/class-yith-wcbk-booking.php';
	require_once 'includes/objects/class-yith-wcbk-booking-external.php';

	// Data stores.
	require_once 'includes/data-stores/class-yith-wcbk-product-booking-data-store-cpt.php';
	require_once 'includes/objects/data-stores/interface-yith-wcbk-object-data-store-interface.php';
	require_once 'includes/objects/data-stores/class-yith-wcbk-data-store-wp.php';
	require_once 'includes/objects/data-stores/class-yith-wcbk-booking-data-store.php';

	// Assets.
	require_once 'includes/assets/class-yith-wcbk-admin-assets.php';
	require_once 'includes/assets/class-yith-wcbk-frontend-assets.php';
	require_once 'includes/assets/class-yith-wcbk-common-assets.php';

	// Integrations.
	require_once 'includes/integrations/class-yith-wcbk-integrations.php';

	// Tools.
	require_once 'includes/class-yith-wcbk-tools.php';

	// Utils.
	require_once 'includes/utils/class-yith-wcbk-exporter.php';
	require_once 'includes/utils/class-yith-wcbk-wp-compatibility.php';
	require_once 'includes/utils/class-yith-wcbk-ics-parser.php';

	// Booking classes.
	require_once 'includes/objects/class-yith-wcbk-booking-external-sources.php';
	require_once 'includes/class-yith-wcbk-booking-externals.php';
	require_once 'includes/class-yith-wcbk-booking-helper.php';

	require_once 'includes/admin/class-yith-wcbk-booking-calendar.php';

	// Widgets.
	require_once 'includes/widgets/class-yith-wcbk-search-form-widget.php';
	require_once 'includes/widgets/class-yith-wcbk-product-form-widget.php';

	// Builders.
	require_once 'includes/builders/class-yith-wcbk-builders.php';

	// Install.
	require_once 'includes/class-yith-wcbk-install.php';

	require_once 'includes/legacy/class-yith-wcbk-legacy-elements.php';

	// Let's start the game!
	yith_wcbk();
}

add_action( 'yith_wcbk_init', 'yith_wcbk_init' );

/**
 * Install
 */
function yith_wcbk_install() {
	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_wcbk_install_woocommerce_admin_notice' );
	} else {
		do_action( 'yith_wcbk_init' );
	}
}

add_action( 'plugins_loaded', 'yith_wcbk_install', 11 );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( plugin_dir_path( __FILE__ ) );
