<?php
/**
 * Plugin Installer class.
 * Thanks to Advanced Coupons for WooCommerce for the code.
 *
 * @package WCVendors/Admin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin Installer class.
 *
 * @version 2.4.8
 * @since 2.4.8
 */
class WCV_Plugin_Installer {
    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @version 2.4.8
     * @since 2.4.8
     * @access private
     * @var WCV_Plugin_Installer
     */
    private static $_instance;

    /**
     * Installed plugins.
     *
     * @version 2.4.9
     * @since 2.4.9
     *
     * @var array
     */
    private $installed_plugins;

    /**
     * Activated plugins.
     *
     * @version 2.4.9
     * @since 2.4.9
     *
     * @var array
     */
    private $activated_plugins;

    /**
     * Exclude plugins.
     *
     * @version 2.4.9
     * @since 2.4.9
     *
     * @var array
     */
    private $exclude_plugins = array();

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @version 2.4.8
     * @since 2.4.8
     * @access public
     */
    public function __construct() {
        $this->initialize();
    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @version 2.4.8
     * @since 2.4.8
     * @access public
     *
     * @return WCV_Plugin_Installer
     */
    public static function get_instance() {
        if ( ! self::$_instance instanceof self ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Download and activate a given plugin.
     *
     * @version 2.4.8
     * @since 2.4.8
     * @access public
     *
     * @param string $plugin_slug Plugin slug.
     * @return bool|\WP_Error True if successful, WP_Error otherwise.
     */
    public function download_and_activate_plugin( $plugin_slug ) {

        // Check if the current user has the required permissions.
        if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
            return new \WP_Error( 'permission_denied', __( 'You do not have sufficient permissions to install and activate plugins.', 'wc-vendors' ) );
        }

        // Check if the plugin is valid.
        if ( ! $this->is_plugin_allowed_for_install( $plugin_slug ) ) {
            return new \WP_Error( 'wcv_plugin_not_allowed', __( 'The plugin is not valid.', 'wc-vendors' ) );
        }

        // Get required files since we're calling this outside of context.
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        // Get the plugin info from WordPress.org's plugin repository.
        $api = plugins_api( 'plugin_information', array( 'slug' => $plugin_slug ) );
        if ( is_wp_error( $api ) ) {
            return $api;
        }

        $plugin_basename = $this->get_plugin_basename_by_slug( $plugin_slug );

        // Check if the plugin is already active.
        if ( is_plugin_active( $plugin_basename ) ) {
            return new \WP_Error( 'wcv_plugin_already_active', __( 'The plugin is already installed.', 'wc-vendors' ) );
        }

        // Check if the plugin is already installed but inactive, just activate it and return true.
        if ( wcv_is_plugin_installed( $plugin_basename ) ) {
            return $this->activate_plugin( $plugin_basename, $plugin_slug );
        }

        // Download the plugin.
        $upgrader = new \Plugin_Upgrader(
            new \Plugin_Installer_Skin(
                array(
                    'type'  => 'web',
                    'title' => sprintf( 'Installing Plugin: %s', $api->name ),
                )
            )
        );

        $result = $upgrader->install( $api->download_link );

        // Check if the plugin was installed successfully.
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        // Activate the plugin.
        return $this->activate_plugin( $plugin_basename, $plugin_slug );
    }

    /**
     * Activate a plugin.
     *
     * @version 2.4.8
     * @since 2.4.8
     * @access private
     *
     * @param string $plugin_basename Plugin basename.
     * @param string $plugin_slug     Plugin slug.
     * @return bool|\WP_Error True if successful, WP_Error otherwise.
     */
    private function activate_plugin( $plugin_basename, $plugin_slug = '' ) { // phpcs:ignore
        $result = activate_plugin( $plugin_basename );

        return is_wp_error( $result ) ? $result : true;
    }

    /**
     * Get the list of allowed plugins for install.
     *
     * @version 2.4.8
     * @since 2.4.8
     * @access public
     *
     * @return array List of allowed plugins.
     */
    public function get_allowed_plugins() {

        $allowed_plugins = array(
            'wc-vendors-pro'                        => array(
                'base_name' => 'wc-vendors-pro/wcvendors-pro.php',
                'name'      => __( 'WC Vendors Pro', 'wc-vendors' ),
                'logo'      => WCV_ASSETS_URL . 'images/extensions/icon-cart.png',
                'desc'      => __( 'WC Vendors Pro has all the tools & features to help you build a thriving marketplace that both your customers and Vendors will love. Provide a true frontend multi-vendor experience to rival the big platforms. Grow your marketplace faster with WC Vendors Pro.', 'wc-vendors' ),
            ),
            'wc-vendors-gateway-stripe-connect'     => array(
                'base_name'    => 'wc-vendors-gateway-stripe-connect/wc-vendors-gateway-stripe-connect.php',
                'name'         => __( 'WC Vendors Stripe Connect', 'wc-vendors' ),
                'logo'         => WCV_ASSETS_URL . 'images/extensions/icon-cart.png',
                'desc'         => __( 'Automate your marketplace and save time with WC Vendors Stripe Connect. Use Stripe\'s Connect platform to process credit card payments and pay your vendor commissions automatically.', 'wc-vendors' ),
                'upgrade_link' => 'https://www.wcvendors.com/pricing/?utm_source=plugin&utm_medium=extensionspage&utm_campaign=upgradestripeconnectaddon',
            ),
            'wc-vendors-woocommerce-bookings'       => array(
                'base_name'    => 'wc-vendors-woocommerce-bookings/wcv-woocommerce-bookings.php',
                'name'         => __( 'WC Vendors Bookings Integration', 'wc-vendors' ),
                'logo'         => WCV_ASSETS_URL . 'images/extensions/icon-cart.png',
                'desc'         => __( 'Integration with WooCommerce Bookings plugin to let your Vendors create and sell bookable products such as hotel rooms, gym sessions, consultations, equipment rentals and more.', 'wc-vendors' ),
                'upgrade_link' => 'https://www.wcvendors.com/pricing/?utm_source=plugin&utm_medium=extensionspage&utm_campaign=upgradebookingsaddon',
            ),
            'wc-vendors-tax'                        => array(
                'base_name'    => 'wc-vendors-tax/wcv-tax.php',
                'name'         => __( 'WC Vendors Tax', 'wc-vendors' ),
                'logo'         => WCV_ASSETS_URL . 'images/extensions/icon-cart.png',
                'desc'         => __( 'Provides your marketplace with automatic sales tax calculations using either TaxJar or Avalara tax calculation services.', 'wc-vendors' ),
                'upgrade_link' => 'https://www.wcvendors.com/pricing/?utm_source=plugin&utm_medium=extensionspage&utm_campaign=upgradetaxaddon',
            ),
            'wc-vendors-woocommerce-subscriptions'  => array(
                'base_name'    => 'wc-vendors-woocommerce-subscriptions/wcv-wc-subscriptions.php',
                'name'         => __( 'WC Vendors Subscriptions Integration', 'wc-vendors' ),
                'logo'         => WCV_ASSETS_URL . 'images/extensions/icon-cart.png',
                'desc'         => __( 'Integration with WooCommerce Subscriptions to allow your vendors to create and sell their own subscription products. Turn your marketplace int oa subscription box service, capture recurring membership fees and more.', 'wc-vendors' ),
                'upgrade_link' => 'https://www.wcvendors.com/pricing/?utm_source=plugin&utm_medium=extensionspage&utm_campaign=upgradesubscriptionsaddon',
            ),
            'wc-vendors-membership'                 => array(
                'base_name'    => 'wc-vendors-membership/wc-vendors-membership.php',
                'name'         => __( 'WC Vendors Membership', 'wc-vendors' ),
                'logo'         => WCV_ASSETS_URL . 'images/extensions/icon-cart.png',
                'desc'         => __( 'Allows you to create and sell membership plans to your marketplace for Vendors so you can earn recurring revenue. Set different limits for your vendors on what products they can sell, storage, adjust fees, and more.', 'wc-vendors' ),
                'upgrade_link' => 'https://www.wcvendors.com/pricing/?utm_source=plugin&utm_medium=extensionspage&utm_campaign=upgrademembershipaddon',
            ),
            'wc-vendors-pro-simple-auctions'        => array(
                'base_name'    => 'wc-vendors-pro-simple-auctions/class-wcv-simple-auctions.php',
                'name'         => __( 'WC Vendors Simple Auctions Integration', 'wc-vendors' ),
                'logo'         => WCV_ASSETS_URL . 'images/extensions/icon-cart.png',
                'desc'         => __( 'Integration with Simple Auctions plugin to create an auction marketplace just like eBay, Gumtree, or Facebook Marketplace. Allow your vendors to sell auction products right from their dashboard.', 'wc-vendors' ),
                'upgrade_link' => 'https://www.wcvendors.com/pricing/?utm_source=plugin&utm_medium=extensionspage&utm_campaign=upgradesimpleauctionsaddon',
            ),
            'woocommerce'                           => array(
                'base_name' => 'woocommerce/woocommerce.php',
                'name'      => __( 'WooCommerce', 'wc-vendors' ),
                'logo'      => WCV_ASSETS_URL . 'images/extensions/woocommerce-logo.png',
                'desc'      => __( 'The most customizable eCommerce platform for building your online business.', 'wc-vendors' ),
            ),
            'advanced-coupons-for-woocommerce-free' => array(
                'base_name' => 'advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php',
                'name'      => __( 'Advanced Coupons for WooCommerce', 'wc-vendors' ),
                'logo'      => WCV_ASSETS_URL . 'images/extensions/acf-logo.png',
                'desc'      => __( 'Create new coupon types in WooCommerce with Advanced Coupons. Limit coupons to certain user roles. Control cart conditions when coupons should and shouldn\'t apply. Auto apply coupons + more.', 'wc-vendors' ),
            ),
            'woocommerce-wholesale-prices'          => array(
                'base_name' => 'woocommerce-wholesale-prices/woocommerce-wholesale-prices.bootstrap.php',
                'name'      => __( 'WooCommerce Wholesale Prices', 'wc-vendors' ),
                'logo'      => WCV_ASSETS_URL . 'images/extensions/wwp-logo.png',
                'desc'      => __( 'The #1 WooCommerce wholesale plugin for adding wholesale prices & managing B2B customers. Trusted by over 25k store owners for managing wholesale orders, pricing, visibility, user roles, and more.', 'wc-vendors' ),
            ),
            'woocommerce-store-toolkit'             => array(
                'base_name' => 'woocommerce-store-toolkit/store-toolkit.php',
                'name'      => __( 'Store Toolkit for WooCommerce', 'wc-vendors' ),
                'logo'      => WCV_ASSETS_URL . 'images/extensions/stk.png',
                'desc'      => __( 'A growing set of commonly-used WooCommerce admin tools such as deleting WooCommerce data in bulk, such as products, orders, coupons, and customers. It also adds extra small features, order filtering, and more.', 'wc-vendors' ),
            ),
            'woocommerce-exporter'                  => array(
                'base_name' => 'woocommerce-exporter/exporter.php',
                'name'      => __( 'Store Exporter for WooCommerce', 'wc-vendors' ),
                'logo'      => WCV_ASSETS_URL . 'images/extensions/stk.png',
                'desc'      => __( 'Easily export Orders, Subscriptions, Coupons, Products, Categories, Tags to a variety of formats. The deluxe version also adds scheduled exporting for easy reporting and syncing with other systems.', 'wc-vendors' ),
            ),
            'invoice-gateway-for-woocommerce'       => array(
                'base_name' => 'invoice-gateway-for-woocommerce/invoice-gateway-for-woocommerce.php',
                'name'      => __( 'Invoice Gateway for WooCommerce', 'wc-vendors' ),
                'logo'      => WCV_ASSETS_URL . 'images/extensions/wwp-logo.png',
                'desc'      => __( 'Accept orders via a special invoice payment gateway method which lets your customer enter their order without upfront payment. Then just issue an invoice from your accounting system and paste in the number.', 'wc-vendors' ),
            ),
        );

        // Allow other plugins to be installed but not let them overwrite the ones listed above.
        $extra_allowed_plugins = apply_filters( 'wcv_allowed_install_plugins', array() );
        if ( ! empty( $this->exclude_plugins ) ) {
            $allowed_plugins = array_diff_key( $allowed_plugins, array_flip( $this->exclude_plugins ) );
        }

        return array_merge( $allowed_plugins, $extra_allowed_plugins );
    }

    /**
     * Set exclude plugins.
     *
     * @param array $exclude_plugins List of plugins to exclude.
     *
     * @version 2.4.9
     * @since 2.4.9
     */
    public function set_exclude_plugins( $exclude_plugins ) {
        foreach ( $exclude_plugins as $plugin_slug ) {
            if ( $this->is_plugin_allowed_for_install( $plugin_slug ) ) {
                $this->exclude_plugins[] = $plugin_slug;
            }
        }
    }

    /**
     * Validate if the given plugin is allowed for install.
     *
     * @version 2.4.8
     * @since 2.4.8
     * @access private
     *
     * @param string $plugin_slug Plugin slug.
     * @return bool True if valid, false otherwise.
     */
    private function is_plugin_allowed_for_install( $plugin_slug ) {
        return in_array( $plugin_slug, array_keys( $this->get_allowed_plugins() ), true );
    }

    /**
     * Get the plugin basename by slug.
     *
     * @version 2.4.8
     * @since 2.4.8
     * @access public
     *
     * @param string $plugin_slug Plugin slug.
     * @return string Plugin basename.
     */
    public function get_plugin_basename_by_slug( $plugin_slug ) {
        $allowed_plugins = $this->get_allowed_plugins();

        return $allowed_plugins[ $plugin_slug ]['base_name'] ?? '';
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX Functions
    |--------------------------------------------------------------------------
     */

    /**
     * AJAX install and activate a plugin.
     *
     * @version 2.4.8
     * @since 2.4.8
     * @access public
     */
    public function ajax_install_activate_plugin() {

        // Check nonce.
        check_ajax_referer( 'wcv_install_plugin', 'nonce' );

        // Retrieve the plugin slug from the front-end.
        $plugin_slug = isset( $_POST['plugin_slug'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin_slug'] ) ) : '';

        $result = $this->download_and_activate_plugin( $plugin_slug );

        // Check if the result is a WP_Error.
        if ( is_wp_error( $result ) ) {
            // If it is, return a JSON response indicating failure.
            wp_send_json_error( $result->get_error_message() );
        } else {
            // If not, return a JSON response indicating success.
            wp_send_json_success();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin init.
     *
     * @version 2.4.8
     * @since 2.4.8
     * @access public
     */
    public function initialize() {
        $allowed_plugins = $this->get_allowed_plugins();
        foreach ( $allowed_plugins as $plugin => $plugin_data ) {
			$this->installed_plugins[ $plugin ] = wcv_is_plugin_installed( $plugin_data['base_name'] );
			if ( $this->installed_plugins[ $plugin ] ) {
				$this->activated_plugins[ $plugin ] = is_plugin_active( $plugin_data['base_name'] );
			}
		}
        add_action( 'wp_ajax_wcv_install_activate_plugin', array( $this, 'ajax_install_activate_plugin' ) );
    }

    /**
     * Get plugin data
     *
     * @param string $plugin_slug Plugin slug.
     *
     * @version 2.4.9
     * @since 2.4.9
     */
    public function get_plugin_data( $plugin_slug ) {
        $allowed_plugins = $this->get_allowed_plugins();
        return $allowed_plugins[ $plugin_slug ] ?? '';
    }

    /**
     * Generate boxes
     *
     * @version 2.4.9
     * @since 2.4.9
     */
    public function generate_boxes() {
        $allowed_plugins = $this->get_allowed_plugins();
        foreach ( $allowed_plugins as $plugin_slug => $plugin_data ) {
            $this->generate_box( $plugin_slug, $plugin_data );
        }
    }

    /**
     * Generate the HTML for the plugin installer.
     *
     * @param array $plugin_slug Plugin slug.
     * @param array $plugin_data Plugin data.
     *
     * @version 2.4.9
     * @since 2.4.9
     *
     * @return void
     */
    public function generate_box( $plugin_slug, $plugin_data ) { // phpcs:ignore
        include WCV_ABSPATH_ADMIN . 'views/plugin-box.php';
    }
}
