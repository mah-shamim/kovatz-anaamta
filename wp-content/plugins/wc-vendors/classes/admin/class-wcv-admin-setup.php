<?php
use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
/**
 * Admin setup
 *
 * @author      Jamie Madden, WC Vendors
 * @category    Admin
 * @package     WCVendors/Admin
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin Setup class.
 *
 * @since  2.4.8 - Added HPOS Compatibility. Applied PHPCS Rules
 */
class WCV_Admin_Setup {

    /**
     * Construct an instance of this class.
     *
     * @return void.
     */
    public function __construct() {
        // Add wcvendors tools to the WooCommerce Debug tools screen.
        add_filter( 'woocommerce_debug_tools', array( $this, 'wcvendors_tools' ) );

        add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
        add_action( 'admin_init', array( $this, 'export_commissions' ) );
        add_action( 'admin_init', array( $this, 'export_sum_commissions' ) );
        add_action( 'admin_init', array( $this, 'export_paypal_masspay' ) );
        add_action( 'admin_init', array( $this, 'mark_all_paid' ) );
        add_filter( 'woocommerce_screen_ids', array( $this, 'wcv_screen_ids' ) );
        add_action( 'wcvendors_update_options_capabilities', array( $this, 'update_vendor_role' ) );

        add_filter( 'woocommerce_inventory_settings', array( $this, 'add_vendor_stock_notification' ) );
    }

    /**
     * Add tools to the woocommerce status tools page
     *
     * @param array $tools The current list of tools.
     *
     * @since  1.9.2
     * @access public
     */
    public function wcvendors_tools( $tools ) {

        $tools['reset_wcvendor_roles'] = array(
            'name'     => __( 'Reset WC Vendors roles ', 'wc-vendors' ),
            'button'   => __( 'Reset WC Vendor Roles', 'wc-vendors' ),
            'desc'     => __( 'This will reset the wcvendors roles ( vendor & pending_vendor ), back to the default capabilities.', 'wc-vendors' ),
            'callback' => array( 'WCV_Admin_Setup', 'reset_vendor_roles' ),
        );

        $tools['reset_wcvendors'] = array(
            'name'     => __( 'Reset WC Vendors ', 'wc-vendors' ),
            'button'   => __( 'Reset WC Vendors Settings', 'wc-vendors' ),
            'desc'     => __( 'This will reset wcvendors back to defaults. This DELETES ALL YOUR Settings.', 'wc-vendors' ),
            'callback' => array( 'WCV_Admin_Setup', 'reset_wcvendors' ),
        );

        $tools['remove_suborders'] = array(
            'name'     => __( 'Remove orphaned sub orders', 'wc-vendors' ),
            'button'   => __( 'Remove orphaned sub orders', 'wc-vendors' ),
            'desc'     => __( 'This will remove all orphaned sub orders ', 'wc-vendors' ),
            'callback' => array( 'WCV_Admin_Setup', 'remove_orphaned_orders' ),
        );

        $tools['schedule_sync_order_meta_data'] = array(
            'name'     => __( 'Add custom meta data to old orders', 'wc-vendors' ),
            'button'   => __( 'Sync order meta data', 'wc-vendors' ),
            'desc'     => __( 'This will add vendor IDs and sub order IDs to all orders to make it easier to query orders based on vendor ID or vendor order ID.', 'wc-vendors' ),
            'callback' => array( $this, 'schedule_order_data_sync' ),
        );

        $tools['schedule_tracking_details_migration'] = array(
            'name'     => __( 'Migrate order tracking details to new storage', 'wc-vendors' ),
            'button'   => __( 'Migrate order tracking details', 'wc-vendors' ),
            'desc'     => __( 'Migrates the order tracking details from the post meta to the new data storage for orders.', 'wc-vendors' ),
            'callback' => array( $this, 'schedule_tracking_details_migration' ),
        );

        return $tools;
    }

    /**
     * Reset the vendor roles
     *
     * @since  1.9.2
     * @access public
     */
    public static function reset_vendor_roles() {

        $can_add         = wc_string_to_bool( get_option( 'wcvendors_capability_products_enabled', 'no' ) );
        $can_edit        = wc_string_to_bool( get_option( 'wcvendors_capability_products_edit', 'no' ) );
        $can_submit_live = wc_string_to_bool( get_option( 'wcvendors_capability_products_live', 'no' ) );

        $args = array(
            'assign_product_terms'      => $can_add,
            'edit_products'             => $can_add || $can_edit,
            'edit_product'              => $can_add || $can_edit,
            'edit_published_products'   => $can_edit,
            'delete_published_products' => $can_edit,
            'delete_products'           => $can_edit,
            'manage_product'            => $can_add,
            'publish_products'          => $can_submit_live,
            'delete_posts'              => true,
            'read'                      => true,
            'read_products'             => $can_edit || $can_add,
            'upload_files'              => true,
            'import'                    => true,
            'view_woocommerce_reports'  => false,
        );

        remove_role( 'vendor' );
        add_role(
            'vendor',
            sprintf(
                // translators: %s - Name used to refer to a vendor.
                __( '%s', 'wc-vendors' ), // phpcs:ignore
                wcv_get_vendor_name()
            ),
            $args
        );

        remove_role( 'pending_vendor' );
        add_role(
            'pending_vendor',
            sprintf(
            // translators: name used to refer to a vendor.
                __( 'Pending %s', 'wc-vendors' ),
                wcv_get_vendor_name()
            ),
            array(
				'read'         => true,
				'edit_posts'   => false,
				'delete_posts' => false,
            )
        );

        // Reset the capabilities.
        WCVendors_Install::create_capabilities();

        ?>
        <div class="updated inline"><p>
            <?php esc_html_e( 'WC Vendor roles successfully reset.', 'wc-vendors' ); ?>
        </p></div>
        <?php
    }

    /**
     * Reset wcvendors
     *
     * @since  1.9.2
     * @access public
     */
    public static function reset_wcvendors() {

        delete_option( WC_Vendors::$id . '_options' );
        ?>
        <div class="updated inline"><p>
            <?php esc_html_e( 'WC Vendors was successfully reset. All settings have been reset.', 'wc-vendors' ); ?>
        </p></div>
        <?php
    }

    /**
     *  Clean up orphaned Vendor sub orders that do not have parent posts
     *
     * @since 2.1.13
     */
    public static function remove_orphaned_orders() {

        $args = array(
            'status' => 'any',
            'type'   => WC_Order_Vendor::ORDER_TYPE,
            'fields' => array( 'id', 'parent' ),
            'limit'  => -1,
        );

        $vendor_sub_orders = wc_get_orders( $args );

        if ( empty( $vendor_sub_orders ) ) {
            return;
        }

        foreach ( $vendor_sub_orders as $vendor_sub_order ) {
            $parent_order = wc_get_order( $vendor_sub_order->get_parent_id() );

            if ( ! $parent_order ) {
                $vendor_sub_order->delete( true );
            }

            if ( ! wcv_cot_enabled() ) {
                global $wpdb;
                $order_table_name = OrdersTableDataStore::get_orders_table_name();
                $wpdb->delete( $order_table_name, array( 'id' => $vendor_sub_order->get_id() ) );
            }

            if ( wcv_cot_enabled() ) {
                wp_delete_post( $vendor_sub_order->get_id(), true );
            }
        }

        ?>
        <div class="updated inline">
            <p>
                <?php esc_html_e( 'Orphaned sub orders have been removed.', 'wc-vendors' ); ?>
            </p>
        </div>
    <?php
    }

    /**
     * Schedule a task to sync the custom order meta data
     *
     * @return void
     * @version 2.4.8
     * @since   2.4.8 - Added
     */
    public function schedule_order_data_sync() {
        WC()->queue()->schedule_single(
            current_time( 'mysql' ),
            'wcvendors_scheduled_sync_order_meta_data'
        );

        ?>
        <div class="updated inline">
            <p>
                <?php esc_html_e( 'Scheduled: The order data will be synced in the background.', 'wc-vendors' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Schedule a task to migrate order tracking details.
     *
     * @return void
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public function schedule_tracking_details_migration() {
        WC()->queue()->schedule_single(
            current_time( 'mysql' ),
            'wcvendors_scheduled_migrate_tracking_details'
        );

        ?>
        <div class="updated inline">
            <p>
                <?php esc_html_e( 'Scheduled: The order tracking details will be synced in the background.', 'wc-vendors' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Export commissions via csv
     *
     * @return void
     */
    public function export_commissions() {

        // prepare the items to export.
        if ( isset( $_GET['action'], $_GET['nonce'] ) && wp_verify_nonce( wp_unslash( $_GET['nonce'] ), 'export_commissions' ) && 'export_commissions' === wp_unslash( $_GET['action'] ) ) {

            include_once 'class-wcv-commissions-csv-exporter.php';

            $exporter = new WCV_Commissions_CSV_Export();

            $date = gmdate( 'Y-M-d' );

            if ( ! empty( $_GET['com_status'] ) ) { // WPCS: input var ok.
                $exporter->set_filename( 'wcv_commissions_' . wp_unslash( $_GET['com_status'] ) . '-' . $date . '.csv' ); // WPCS: input var ok, sanitization ok.
            } else {
                $exporter->set_filename( 'wcv_commissions-' . $date . '.csv' ); // WPCS: input var ok, sanitization ok.
            }

            $exporter->export();
        }
    }

    /**
     * Export sum commissions via csv
     *
     * @return void
     */
    public function export_sum_commissions() {

        // prepare the items to export.
        if ( isset( $_GET['action'], $_GET['nonce'] ) && wp_verify_nonce( wp_unslash( $_GET['nonce'] ), 'export_commission_totals' ) && 'export_commission_totals' === wp_unslash( $_GET['action'] ) ) {

            include_once 'class-wcv-commissions-sum-csv-exporter.php';

            $exporter = new WCV_Commissions_Sum_CSV_Export();

            $date = gmdate( 'Y-M-d' );

            if ( ! empty( $_GET['com_status'] ) ) { // WPCS: input var ok.
                $exporter->set_filename( 'wcv_commissions_sum_' . wp_unslash( $_GET['com_status'] ) . '-' . $date . '.csv' ); // WPCS: input var ok, sanitization ok.
            } else {
                $exporter->set_filename( 'wcv_commissions_sum-' . $date . '.csv' ); // WPCS: input var ok, sanitization ok.
            }

            $exporter->export();
        }
    }

    /**
     * Generate the PayPal Masspay web csv
     *
     * @since 2.4.3 - Added suport for PayPal Masspay Web
     *
     * @return void
     */
    public function export_paypal_masspay() {

        // prepare the items to export.
        if ( isset( $_GET['action'], $_GET['nonce'] ) && wp_verify_nonce( wp_unslash( $_GET['nonce'] ), 'export_paypal_masspay' ) && 'export_paypal_masspay' === wp_unslash( $_GET['action'] ) ) {

            include_once 'class-wcv-commissions-paypal-csv-exporter.php';

            $exporter = new WCV_Commissions_PayPal_Masspay_CSV_Export();

            $date = gmdate( 'Y-M-d' );

            if ( ! empty( $_GET['com_status'] ) ) { // WPCS: input var ok.
                $exporter->set_filename( 'wcv_commissions_sum_' . wp_unslash( $_GET['com_status'] ) . '-' . $date . '.csv' ); // WPCS: input var ok, sanitization ok.
            } else {
                $exporter->set_filename( 'wcv_commissions_sum-' . $date . '.csv' ); // WPCS: input var ok, sanitization ok.
            }

            $exporter->export();
        }
    }

    /**
     * Mark all commissions that are due as paid this is triggered by the Mark All Paid button on the commissions screen
     *
     * @since 2.1.10
     * @version 2.1.10
     */
    public function mark_all_paid() {

        // set all.
        if ( isset( $_GET['action'], $_GET['nonce'] ) && wp_verify_nonce( wp_unslash( $_GET['nonce'] ), 'mark_all_paid' ) && 'mark_all_paid' === wp_unslash( $_GET['action'] ) ) {

        global $wpdb;
        $result = $wpdb->query( "UPDATE `{$wpdb->prefix}pv_commission` SET `status` = 'paid' WHERE `status` = 'due'" );

        if ( $result ) {
            add_action( 'admin_notices', array( $this, 'mark_all_paid__success' ) );
        }
        }
    }


    /**
     * Display a message when all commissions have been marked as paid
     *
     * @return void
     */
    public function mark_all_paid__success() {
        ?>
        <div class="notice notice-success is-dismissible"><p>
            <?php esc_html_e( 'All commissions marked as paid.', 'wc-vendors' ); ?>
        </p></div>
        <?php
    }

    /**
     * Add wc vendors screens to woocommerce screen ids to utilize js and css assets from woocommerce.
     *
     * @param array $screen_ids - The screen ids.
     *
     * @since 2.0.0
     */
    public function wcv_screen_ids( $screen_ids ) {

        $wcv_screen_ids = wcv_get_screen_ids();
        $screen_ids     = array_merge( $wcv_screen_ids, $screen_ids );

        return $screen_ids;
    }

    /**
     * Change the admin footer text on WooCommerce admin pages.
     *
     * @since  2.0.0
     *
     * @param  string $footer_text The footer text.
     *
     * @return string
     */
    public function admin_footer_text( $footer_text ) {

        if ( ! current_user_can( 'manage_woocommerce' ) || ! function_exists( 'wcv_get_screen_ids' ) ) {
        return $footer_text;
        }
        $current_screen = get_current_screen();
        $wcv_pages      = wcv_get_screen_ids();

        // Set only WC pages.
        // Check to make sure we're on a WooCommerce admin page.
        if ( isset( $current_screen->id ) && apply_filters( 'wcvendors_display_admin_footer_text', in_array( $current_screen->id, $wcv_pages, true ) ) ) {
            // Change the footer text.
            $footer_text = sprintf(
            /* translators: 1: WooCommerce 2:: five stars */
                __( 'If you like %1$s please leave us a %2$s rating. A huge thanks in advance!', 'wc-vendors' ),
                sprintf( '<strong>%s</strong>', esc_html__( 'WC Vendors', 'wc-vendors' ) ),
                '<a href="https://wordpress.org/support/plugin/wc-vendors/reviews?rate=5#new-post" target="_blank" class="wcv-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'wc-vendors' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
            );
        }

        return $footer_text;
    }

    /**
     * Update the vendor role based on the capabilities saved.
     */
    public function update_vendor_role() {

        $can_add         = wc_string_to_bool( get_option( 'wcvendors_capability_products_enabled', 'no' ) );
        $can_edit        = wc_string_to_bool( get_option( 'wcvendors_capability_products_edit', 'no' ) );
        $can_submit_live = wc_string_to_bool( get_option( 'wcvendors_capability_products_live', 'no' ) );

        $args = array(
            'assign_product_terms'      => $can_add,
            'edit_products'             => $can_add || $can_edit,
            'edit_product'              => $can_add || $can_edit,
            'edit_published_products'   => $can_edit,
            'delete_published_products' => $can_edit,
            'delete_products'           => $can_edit,
            'delete_posts'              => true,
            'manage_product'            => $can_add,
            'publish_products'          => $can_submit_live,
            'read'                      => true,
            'read_products'             => $can_edit || $can_add,
            'upload_files'              => true,
            'import'                    => true,
            'view_woocommerce_reports'  => false,
        );

        remove_role( 'vendor' );
        add_role(
            'vendor',
            sprintf(
            // translators: %s: Name used to refer to vendor.
                    __( '%s', 'wc-vendors' ), // phpcs:ignore
                wcv_get_vendor_name()
            ),
            $args
        );
    }

    /**
     * Add options to disable vendor low / no stock notifications
     *
     * @param array $options The options to disable stock notifications.
     *
     * @since 2.1.10
     * @version 2.1.10
     */
    public function add_vendor_stock_notification( $options ) {
        $new_options = array();

        foreach ( $options as $option ) {
            if ( 'woocommerce_stock_email_recipient' === $option['id'] ) {
            // Low stock.
            $new_options[] = array(
                'title'         => sprintf(
                // translators: %s: Name used to refer to vendor.
                    __( '%s Notifications', 'wc-vendors' ),
                    wcv_get_vendor_name()
                ),
                'desc'          => sprintf(
                // translators: %s: Name used to refer to vendor.
                    __( 'Enable %s low stock notifications', 'wc-vendors' ),
                    wcv_get_vendor_name( true, false )
                ),
                'id'            => 'wcvendors_notify_low_stock',
                'default'       => 'yes',
                'type'          => 'checkbox',
                'checkboxgroup' => 'start',
                'class'         => 'manage_stock_field',
			);
            // No Stock.
            $new_options[] = array(
                'desc'          => sprintf(
                // translators: %s: Name used to refer to vendor.
                    __( 'Enable %s out of stock notifications', 'wc-vendors' ),
                    wcv_get_vendor_name( true, false )
                ),
                'id'            => 'wcvendors_notify_no_stock',
                'default'       => 'yes',
                'type'          => 'checkbox',
                'checkboxgroup' => 'middle',
                'class'         => 'manage_stock_field',
			);
            // Back order.
            $new_options[] = array(
                'desc'          => sprintf(
				// translators: %s: Name used to refer to vendor.
                    __( 'Enable %s backorder stock notifications', 'wc-vendors' ),
                    wcv_get_vendor_name( true, false )
                ),
                'id'            => 'wcvendors_notify_backorder_stock',
                'default'       => 'yes',
                'type'          => 'checkbox',
                'checkboxgroup' => 'end',
                'class'         => 'manage_stock_field',
            );

            }
            $new_options[] = $option;
        }
        return $new_options;
    }
}
