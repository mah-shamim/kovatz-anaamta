<?php
/**
 *  WC Vendor Admin Dashboard - Vendor WP-Admin Dashboard Pages
 *
 * @package WCVendors
 */

/**
 * WC Vendors Admin Dashboard Class
 *
 * @version 1.4.8
 * @since   1.4.8 - Added HPOS compatibility.
 */
class WCV_Vendor_Admin_Dashboard {

    /**
     * Dashboard error message.
     *
     * @var string
     */
    public $dashboard_error_msg;

    /**
     * The constructor of the class.
     *
     * @version 1.4.8
     * @since   1.4.8 - Added HPOS compatibility.
     */
    public function __construct() {

        // Add Shop Settings page.
        add_action( 'admin_menu', array( $this, 'vendor_dashboard_pages' ) );
        // Hook into init for form processing.
        add_action( 'admin_init', array( $this, 'save_shop_settings' ) );
        add_action( 'admin_head', array( $this, 'admin_enqueue_order_style' ) );
    }

    /**
     * Add the vendor dashboard pages menu exclude admin.
     */
    public function vendor_dashboard_pages() {

        if ( current_user_can( 'manage_options' ) ) {
            return;
        }

        add_menu_page(
            __( 'Shop Settings', 'wc-vendors' ),
            __( 'Shop Settings', 'wc-vendors' ),
            'manage_product', // phpcs:ignore
            'wcv-vendor-shopsettings',
            array(
                $this,
                'settings_page',
            )
        );
        $hook = add_menu_page(
            __( 'Orders', 'wc-vendors' ),
            __( 'Orders', 'wc-vendors' ),
            'manage_product', // phpcs:ignore
            'wcv-vendor-orders',
            array(
                'WCV_Vendor_Admin_Dashboard',
                'orders_page',
            )
        );
        add_action( "load-$hook", array( 'WCV_Vendor_Admin_Dashboard', 'add_options' ) );
    }

    /**
     * Settings page
     *
     * @return void
     */
    public function settings_page() {

        $user_id          = get_current_user_id();
        $paypal_address   = true;
        $shop_description = true;
        $description      = get_user_meta( $user_id, 'pv_shop_description', true );
        $seller_info      = get_user_meta( $user_id, 'pv_seller_info', true );
        $has_html         = get_user_meta( $user_id, 'pv_shop_html_enabled', true );
        $shop_page        = WCV_Vendors::get_vendor_shop_page( wp_get_current_user()->user_login );
        $global_html      = wc_string_to_bool( get_option( 'wcvendors_display_shop_description_html', 'no' ) );
        include 'views/html-vendor-settings-page.php';
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * @return void
     */
    public function admin_enqueue_order_style() {

        $screen = get_current_screen();

        if ( ! $screen ) {
            return;
        }

        $screen_id = $screen->id;

        if ( 'wcv-vendor-orders' === $screen_id ) {

            add_thickbox();
            wp_enqueue_style(
                'admin_order_styles',
                WCV_ASSETS_URL . 'css/admin-orders.css',
                array(),
                WCV_VERSION
            );
        }
    }

    /**
     *    Save shop settings
     */
    public function save_shop_settings() {

        $user_id   = get_current_user_id();
        $error     = false;
        $error_msg = '';

        if ( isset( $_POST['wc-vendors-nonce'] ) ) {

            if ( ! wp_verify_nonce( $_POST['wc-vendors-nonce'], 'save-shop-settings-admin' ) ) {
                return false;
            }

            if ( isset( $_POST['pv_paypal'] ) && '' !== $_POST['pv_paypal'] ) {
                if ( ! is_email( $_POST['pv_paypal'] ) ) {
                    $error_msg .= __( 'Your PayPal address is not a valid email address.', 'wc-vendors' );
                    $error      = true;
                } else {
                    update_user_meta( $user_id, 'pv_paypal', $_POST['pv_paypal'] );
                }
            } else {
                update_user_meta( $user_id, 'pv_paypal', $_POST['pv_paypal'] );
            }

            if ( ! empty( $_POST['pv_shop_name'] ) ) {
                $users = get_users(
                    array(
                        'meta_key'   => 'pv_shop_slug',
                        'meta_value' => sanitize_title( $_POST['pv_shop_name'] ),
                    )
                );
                if ( ! empty( $users ) && $users[0]->ID !== $user_id ) {
                    $error_msg .= __( 'That shop name is already taken. Your shop name must be unique.', 'wc-vendors' );
                    $error      = true;
                } else {
                    update_user_meta( $user_id, 'pv_shop_name', $_POST['pv_shop_name'] );
                    update_user_meta( $user_id, 'pv_shop_slug', sanitize_title( $_POST['pv_shop_name'] ) );
                }
            }

            if ( isset( $_POST['pv_shop_description'] ) ) {
                update_user_meta( $user_id, 'pv_shop_description', $_POST['pv_shop_description'] );
            }

            if ( isset( $_POST['pv_seller_info'] ) ) {
                update_user_meta( $user_id, 'pv_seller_info', $_POST['pv_seller_info'] );
            }

            // PayPal Masspay wallet.
            if ( isset( $_POST['wcv_paypal_masspay_wallet'] ) ) {
                update_user_meta( $user_id, 'wcv_paypal_masspay_wallet', sanitize_text_field( $_POST['wcv_paypal_masspay_wallet'] ) );
            } else {
                delete_user_meta( $user_id, 'wcv_paypal_masspay_wallet' );
            }

            // PayPal Masspay venmo.
            if ( isset( $_POST['wcv_paypal_masspay_venmo_id'] ) ) {
                update_user_meta( $user_id, 'wcv_paypal_masspay_venmo_id', sanitize_text_field( $_POST['wcv_paypal_masspay_venmo_id'] ) );
            } else {
                delete_user_meta( $user_id, 'wcv_paypal_masspay_venmo_id' );
            }

            // Bank details.
            if ( isset( $_POST['wcv_bank_account_name'] ) ) {
                update_user_meta( $user_id, 'wcv_bank_account_name', $_POST['wcv_bank_account_name'] );
            }
            if ( isset( $_POST['wcv_bank_account_number'] ) ) {
                update_user_meta( $user_id, 'wcv_bank_account_number', $_POST['wcv_bank_account_number'] );
            }
            if ( isset( $_POST['wcv_bank_name'] ) ) {
                update_user_meta( $user_id, 'wcv_bank_name', $_POST['wcv_bank_name'] );
            }
            if ( isset( $_POST['wcv_bank_routing_number'] ) ) {
                update_user_meta( $user_id, 'wcv_bank_routing_number', $_POST['wcv_bank_routing_number'] );
            }
            if ( isset( $_POST['wcv_bank_iban'] ) ) {
                update_user_meta( $user_id, 'wcv_bank_iban', $_POST['wcv_bank_iban'] );
            }
            if ( isset( $_POST['wcv_bank_bic_swift'] ) ) {
                update_user_meta( $user_id, 'wcv_bank_bic_swift', $_POST['wcv_bank_bic_swift'] );
            }

            do_action( 'wcvendors_shop_settings_admin_saved', $user_id );

            if ( ! $error ) {
                add_action( 'admin_notices', array( $this, 'add_admin_notice_success' ) );
            } else {
                $this->dashboard_error_msg = $error_msg;
                add_action( 'admin_notices', array( $this, 'add_admin_notice_error' ) );
            }
        }
    }

    /**
     * Output a successful message after saving the shop settings
     *
     * @since  1.9.9
     * @access public
     */
    public function add_admin_notice_success() {

        echo '<div class="updated"><p>';
        echo esc_attr( __( 'Settings saved.', 'wc-vendors' ) );
        echo '</p></div>';
    }

    /**
     * Output an error message
     *
     * @since  1.9.9
     * @access public
     */
    public function add_admin_notice_error() {

        echo '<div class="error"><p>';
        echo esc_attr( $this->dashboard_error_msg );
        echo '</p></div>';
    }

    /**
     * Set screen options for the Orders Page
     *
     * @param string $status Status.
     * @param string $option Option.
     * @param mixed  $value Value.
     *
     * @return int|void
     */
    public static function set_table_option( $status, $option, $value ) {

        if ( 'orders_per_page' === $option ) {
            return $value;
        }
    }

    /**
     * Add screen options for the Orders Page
     */
    public static function add_options() {

        global $WCV_Vendor_Order_Page;

        $args = array(
            'label'   => 'Rows',
            'default' => 10,
            'option'  => 'orders_per_page',
        );
        add_screen_option( 'per_page', $args );

        $WCV_Vendor_Order_Page = new WCV_Vendor_Order_Page();
    }

    /**
     * HTML setup for the Orders Page
     */
    public static function orders_page() {

        global $woocommerce, $WCV_Vendor_Order_Page;

        $WCV_Vendor_Order_Page->prepare_items();

        ?>
        <div class="wrap">

            <div id="icon-woocommerce" class="icon32 icon32-woocommerce-reports"><br/></div>
            <h2><?php esc_attr_e( 'Orders', 'wc-vendors' ); ?></h2>

            <form id="posts-filter" method="get">

                <input type="hidden" name="page" value="wcv-vendor-orders"/>
                <?php $WCV_Vendor_Order_Page->display(); ?>

            </form>
            <div id="ajax-response"></div>
            <br class="clear"/>
        </div>

        <?php
    }
}
