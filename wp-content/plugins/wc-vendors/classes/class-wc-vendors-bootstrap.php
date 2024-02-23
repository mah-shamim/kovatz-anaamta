<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WC Vendors Class
 *
 * Main Product Vendor class
 *
 * @since   2.4.8 - Refactored from class-wc-vendors.php
 */
class WC_Vendors_Bootstrap {

    /**
     * Plugin version
     *
     * @var string $version Plugin version number
     */
    public $version = WCV_VERSION;

    /**
     * Settings options
     *
     * @var array
     */
    public static $pv_options;

    /**
     * Plugin ID.
     *
     * @var string
     */
    public static $id = 'wc_prd_vendor';

    /**
     * Plugin title
     *
     * @var string
     */
    public $title;

    /**
     * Constructor.
     */
    public function __construct() {

        // Load text domain.
        add_action( 'plugins_loaded', array( $this, 'load_il8n' ) );

        $this->title = __( 'WC Vendors Marketplace', 'wc-vendors' );

        // Install & upgrade.
        add_action( 'admin_init', array( $this, 'check_install' ) );
        add_action( 'init', array( $this, 'maybe_flush_permalinks' ), 99 );
        add_action( 'admin_init', array( $this, 'wcv_required_ignore_notices' ) );

        add_action( 'wcvendors_flush_rewrite_rules', array( $this, 'flush_rewrite_rules' ) );

        add_action( 'plugins_loaded', array( $this, 'include_gateways' ) );
        add_action( 'plugins_loaded', array( $this, 'include_core' ) );
        add_action( 'init', array( $this, 'include_init' ) );
        add_action( 'current_screen', array( $this, 'include_assets' ) );

        // Legacy settings.
        add_action( 'admin_init', array( 'WCVendors_Install', 'check_pro_version' ) );
        add_action( 'plugins_loaded', array( $this, 'load_legacy_settings' ) );

        // Show update notices.
        $file   = basename( __FILE__ );
        $folder = basename( __DIR__ );
        $hook   = "in_plugin_update_message-{$folder}/{$file}";
        add_action( $hook, array( $this, 'show_upgrade_notification' ), 10, 2 );

        // Add become a vendor rewrite endpoint.
        add_action( 'init', array( $this, 'add_rewrite_endpoint' ) );
        add_action( 'after_switch_theme', array( $this, 'flush_rewrite_rules' ) );

        // Add shop vendor order type.
        add_filter( 'wc_order_types', array( $this, 'add_custom_order_types' ), 99, 2 );

        // Adjust the data store for the shop_order_vendor order type.
        add_filter( 'woocommerce_data_stores', array( $this, 'add_custom_data_store' ) );

        // Test payment gateway.
        add_filter( 'woocommerce_payment_gateways', array( $this, 'add_wcvendors_test_gateway' ) );

        add_action( 'wcvendors_sync_vendor_status', 'wcvendors_add_vendor_status_meta_key' );

        add_action( 'upgrader_process_complete', 'wcvendors_add_vendor_status_meta_key' );
        add_action( 'upgrader_overwrote_package', 'wcvendors_add_vendor_status_meta_key' );
    }

    /**
     * Add custom order types to WooCommerce.
     *
     * @param array  $types The registered order types.
     * @param string $context The context for which the order types are being requested.
     * @return array
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public function add_custom_order_types( $types, $context = '' ) {
        switch ( $context ) {
            case 'order-count':
            case 'view-orders':
            case 'cot-migration':
            case 'reports':
            case 'sales-reports':
                return $types;
            case 'admin-menu':
                // Add the shop_order_vendor on admin dashboard. Only add the shop_order_vendor type if it's not already in the list.
                if ( ! is_admin() && ! in_array( 'shop_order_vendor', $types, true ) ) {
                    $types[] = 'shop_order_vendor';
                }
                return $types;
            default:
                if ( ! in_array( 'shop_order_vendor', $types, true ) ) {
                    $types[] = 'shop_order_vendor';
                }
                return $types;
        }
    }

    /**
     * Add custom data stores.
     *
     * @param array $data_stores The list of data stores.
     * @return array
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public function add_custom_data_store( $data_stores ) {
        $data_stores['shop_order_vendor'] = wcv_cot_enabled()
            ? 'Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore'
            : 'WC_Order_Data_Store_CPT';

        return $data_stores;
    }

    /**
     * Display message saying invalid WooCommerce version
     */
    public function invalid_wc_version() {
        ?>
        <div class="error"><p>
            <?php
            echo wp_kses_post(
                __(
                    '<b>WC Vendors Marketplace is inactive</b>. WC Vendors Marketplace requires a minimum of WooCommerce 3.0.0 to operate.',
                    'wc-vendors'
                )
            );
            ?>
        </p></div>
        <?php
    }

    /**
     * Check whether install has ran before or not
     *
     * Run install if it hasn't.
     *
     * @return bool
     */
    public function check_install() {

        if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
            add_action( 'admin_notices', array( $this, 'invalid_wc_version' ) );
            deactivate_plugins( plugin_basename( __FILE__ ) );

            return false;
        }

        return true;
    }

    /**
     * Set static $pv_options to hold options class
     */
    public function load_legacy_settings() {
        if ( empty( self::$pv_options ) ) {
            include_once WCV_PLUGIN_DIR . 'classes/includes/class-sf-settings.php';
            self::$pv_options = new SF_Settings_API();
        }
    }

    /**
     * Load internationalization
     *
     * @return void
     * @version 1.0.0
     * @since   1.0.0
     */
    public function load_il8n() {
        $locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
        $locale = apply_filters( 'plugin_locale', $locale, 'wc-vendors' );
        load_textdomain( 'wc-vendors', WP_LANG_DIR . '/wc-vendors/wc-vendors-' . $locale . '.mo' );
        load_plugin_textdomain( 'wc-vendors', false, plugin_basename( __DIR__ ) . '/languages/' );
    }

    /**
     * Include core files
     */
    public function include_core() {

        include_once WCV_PLUGIN_DIR . 'classes/class-queries.php';
        include_once WCV_PLUGIN_DIR . 'classes/class-vendors.php';
        include_once WCV_PLUGIN_DIR . 'classes/class-cron.php';
        include_once WCV_PLUGIN_DIR . 'classes/class-commission.php';
        include_once WCV_PLUGIN_DIR . 'classes/class-shipping.php';
        include_once WCV_PLUGIN_DIR . 'classes/class-vendor-order.php';
        include_once WCV_PLUGIN_DIR . 'classes/class-vendor-post-types.php';
        include_once WCV_PLUGIN_DIR . 'classes/includes/wcv-template-functions.php';
        include_once WCV_PLUGIN_DIR . 'classes/includes/wcv-vendor-functions.php';
        include_once WCV_PLUGIN_DIR . 'classes/includes/wcv-update-functions.php';
        include_once WCV_PLUGIN_DIR . 'classes/includes/wcv-helper-functions.php';
        include_once WCV_PLUGIN_DIR . 'classes/admin/emails/class-emails.php';
        include_once WCV_PLUGIN_DIR . 'classes/class-wcv-shipping-providers.php';
        include_once WCV_PLUGIN_DIR . 'classes/includes/class-wcv-order-data-synchronizer.php';
        include_once WCV_PLUGIN_DIR . 'classes/class-vendor-settings.php';
        include_once WCV_PLUGIN_DIR . 'classes/includes/class-all-vendors-page.php';
        include_once WCV_PLUGIN_DIR . 'classes/includes/class-wcv-cli.php';

        if ( is_admin() ) {

            include_once WCV_PLUGIN_DIR . 'classes/class-install.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-vendor-applicants.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-admin-reports.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-wcv-commissions-page.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-wcv-admin-setup.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-wcv-admin-notices.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-wcv-admin-settings.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-admin-menus.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-wcv-admin-extensions.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-wcv-admin-go-pro.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-wcv-admin-help.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-vendor-admin-dashboard.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-setup-wizard.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-wcv-vendor-order-page.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-admin-media.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-wcv-admin-import-export.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-wcv-admin-orders.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-wcv-admin-lite-bar.php';
			include_once WCV_PLUGIN_DIR . 'classes/admin/class-wcv-plugin-installer.php';
            include_once WCV_PLUGIN_DIR . 'classes/admin/class-wcv-license-page.php';

            new WCV_Admin_Lite_Bar();
            new WCV_Vendor_Applicants();
            new WCV_Admin_Setup();
            new WCV_Vendor_Admin_Dashboard();
            new WCV_Admin_Reports();
            new WCV_Admin_Import_Export();
            new WCVendors_Admin_Orders();
            new WCV_Plugin_Installer();

        } else {

            include_once WCV_PLUGIN_DIR . 'classes/includes/class-wcv-shortcodes.php';
            include_once WCV_PLUGIN_DIR . 'classes/front/class-vendor-cart.php';
            include_once WCV_PLUGIN_DIR . 'classes/front/dashboard/class-vendor-dashboard.php';
            include_once WCV_PLUGIN_DIR . 'classes/front/class-vendor-shop.php';
            include_once WCV_PLUGIN_DIR . 'classes/front/signup/class-vendor-signup.php';
            include_once WCV_PLUGIN_DIR . 'classes/front/orders/class-orders.php';
            include_once WCV_PLUGIN_DIR . 'classes/front/account/class-wc-account-links.php';

            new WCV_Orders();
            new WCV_Vendor_Dashboard();
            new WCV_Vendor_Signup();
            new WCV_Vendor_Shop();
            new WCV_Vendor_Cart();
            new WCV_Shortcodes();
            new WCV_Account_Links();
        }

        // Include.
        if ( ! function_exists( 'woocommerce_wp_text_input' ) && ! is_admin() ) {
            include_once WC()->plugin_path() . '/includes/admin/wc-meta-box-functions.php';
        }

        new WCV_Shipping();
        new WCV_Cron();
        new WCV_Commission();
        new WCV_Vendors();
        new WCV_Emails();

        // Initialize the synchronizer.
        $synchronizer = new WCV_Order_Data_Synchronizer();
        $synchronizer->init_hooks();
    }

    /**
     * These need to be initialized later in loading to fix interaction with other plugins that call current_user_can at the right time.
     *
     * @since  1.9.4
     * @access public
     */
    public function include_init() {

        require_once WCV_PLUGIN_DIR . 'classes/admin/class-vendor-reports.php';
        require_once WCV_PLUGIN_DIR . 'classes/admin/class-product-meta.php';
        require_once WCV_PLUGIN_DIR . 'classes/admin/class-admin-users.php';

        new WCV_Vendor_Reports();
        new WCV_Product_Meta();
        new WCV_Admin_Users();
    }

    /**
     *  Load plugin assets
     *
     * @version 2.1.10
     */
    public function include_assets() {

        $screen = get_current_screen();

        switch ( $screen->id ) {
        case 'edit-product':
            wp_enqueue_script(
                'wcv_quick-edit',
                WCV_ASSETS_URL . 'js/wcv-admin-quick-edit.js',
                array( 'jquery' ),
                WCV_VERSION,
                true
            );
            wp_localize_script(
                'wcv_quick-edit',
                'wcv_quick_edit_params',
                array(
                    'allow_featured' => apply_filters(
                        'wcvendors_capability_allow_product_featured',
                        get_option( 'wcvendors_capability_product_featured', 'no' )
                    ),
                )
            );
            break;
        case 'wc-vendors_page_wcv-commissions':
            wp_register_script(
                'wcv_admin_commissions',
                WCV_ASSETS_URL . 'js/admin/wcv-admin-commissions.js',
                array( 'jquery' ),
                WCV_VERSION,
                true
            );
            $param_args = apply_filters_deprecated(
                'wcv_admin_commissions_params',
                array(
                    array(
                        'confirm_prompt'                 => __( 'Are you sure you want mark all commissions paid?', 'wc-vendors' ),
                        'confirm_delete_commission'      => __( 'Are you sure delete this commission?', 'wc-vendors' ),
                        'confirm_bulk_delete_commission' => __( 'Are you sure delete these commissions?', 'wc-vendors' ),
                    ),
                ),
                '2.3.0',
                'wcvendors_admin_commissions_params'
            );
            $param_args = apply_filters( 'wcvendors_admin_commissions_params', $param_args );
            wp_localize_script( 'wcv_admin_commissions', 'wcv_admin_commissions_params', $param_args );
            wp_enqueue_script( 'wcv_admin_commissions' );
            break;
        default:
            // code...
            break;
        }
    }

    /**
     * Include payment gateways
     */
    public function include_gateways() {
        require_once WCV_PLUGIN_DIR . 'classes/gateways/PayPal_AdvPayments/paypal_ap.php';
        require_once WCV_PLUGIN_DIR . 'classes/gateways/PayPal_Masspay/class-paypal-masspay.php';
        require_once WCV_PLUGIN_DIR . 'classes/gateways/WCV_Gateway_Test/class-wcv-gateway-test.php';
    }

    /**
     *  If the settings are updated and the vendor page link has changed update permalinks
     *
     * @access public
     */
    public function maybe_flush_permalinks() {
        if ( wc_string_to_bool( get_option( 'wcvendors_queue_flush_rewrite_rules', 'no' ) ) ) {
            $this->flush_rewrite_rules();
            update_option( 'wcvendors_queue_flush_rewrite_rules', 'no' );
        }
    }

    /**
     * Flush rewrite rules.
     *
     * @return void
     */
    public function flush_rewrite_rules() {
        flush_rewrite_rules();
    }

    /**
     * Add rewrite endpoint
     *
     * @return void
     */
    public function add_rewrite_endpoint() {
        add_rewrite_endpoint( 'become-a-vendor', EP_PAGES );
        $this->flush_rewrite_rules();
    }

    /**
     * Add user meta to remember ignore notices
     *
     * If user clicks to ignore the notice, add that to their user meta.
     *
     * @access public
     */
    public function wcv_required_ignore_notices() {
        global $current_user;
        $current_user_id = $current_user->ID;

        // phpcs:disable
        if ( isset( $_GET['wcv_shop_ignore_notice'] ) && '0' == $_GET['wcv_shop_ignore_notice'] ) {
            add_user_meta( $current_user_id, 'wcv_shop_ignore_notice', 'true', true );
        }
        if ( isset( $_GET['wcv_pl_ignore_notice'] ) && '0' == $_GET['wcv_pl_ignore_notice'] ) {
            add_user_meta( $current_user_id, 'wcv_pl_ignore_notice', 'true', true );
        }
        // phpcs:enable
    }

    /**
     * Upgrade notice displayed on the plugin screen
     *
     * @param array  $args     The options of the upgrade.
     * @param object $response The update response object.
     * @return void
     */
    public function show_upgrade_notification( $args, $response ) {

        $new_version = $response->new_version;

        $upgrade_notice  = __( 'WC Vendors 2.0 is a major update.', 'wc-vendors' );
        $upgrade_notice .= __( 'This is not compatible with any of our existing extensions.', 'wc-vendors' );
        $upgrade_notice .= __( 'You should test this update on a staging server before updating.', 'wc-vendors' );
        $upgrade_notice .= sprintf(
            // translators: %s - the url to the docs.
            __(
                'Backup your site and update your theme and extensions, and <a href="%s">review update details here</a> before upgrading.',
                'wc-vendors'
            ),
            'https://docs.wcvendors.com/knowledge-base/upgrading-to-wc-vendors-2-0/'
        );

        if ( version_compare( WCV_VERSION, '2.0.0', '<' ) && version_compare( $new_version, '2.0.0', '>=' ) ) {
            echo '<h3>Important Upgrade Notice:</h3>';
            echo '<p style="background-color: #d54e21; padding: 10px; color: #f9f9f9; margin-top: 10px">';
            echo wp_kses_post( $upgrade_notice );
            if ( ! class_exists( 'WCVendors_Pro' ) ) {
                echo '</p>';
            }

            if ( class_exists( 'WCVendors_Pro' ) ) {

                if ( version_compare( WCV_PRO_VERSION, '1.5.0', '<' ) ) {
                    echo '<h3>WC Vendors Pro Notice</h3>';
                    echo '<p style="background-color: #d54e21; padding: 10px; color: #f9f9f9; margin-top: 10px">';

                    $pro_required_notice = __(
                        'WC Vendors Pro 1.5.0 is required to run WC Vendors 2.0.0.',
                        'wc-vendors'
                    );
                    $pro_upgrade         = sprintf(
                        // translators: %1$s - the notice stating WCV Pro is required, %2$s - the current version.
                        __(
                            '%1$s Your current version %2$s will be deactivated. Please upgrade to the latest version.',
                            'wc-vendors'
                        ),
                        $pro_required_notice,
                        WCV_PRO_VERSION
                    );

                    echo wp_kses_post( $pro_upgrade );
                }
            }
        }
    }

    /**
     * Add WC Vendors Test Gateway.
     *
     * @version 1.4.8
     * @since   1.4.8 - Refactored from class-wcv-gateway-test.php
     *
     * @param array $methods List of available payment methods.
     * @return array
     */
    public function add_wcvendors_test_gateway( $methods ) {
        $methods[] = 'WC_Gateway_WCV_Gateway_Test';
        return $methods;
    }
}
