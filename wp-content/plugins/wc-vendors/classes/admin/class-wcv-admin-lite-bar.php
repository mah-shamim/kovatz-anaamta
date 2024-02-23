<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class WCV_Admin_Lite_Bar
 *
 * Display the lite bar on the admin pages to promote the Pro version
 *
 * @version 2.4.8
 * @since   2.4.8
 */
class WCV_Admin_Lite_Bar {

    /**
     * Is pro version active?
     *
     * @var bool
     */
    private $is_pro_activated = false;

    /**
     * Allowed pages
     *
     * @var array
     */
    private $allowed_pages = array(
        'wcv-settings',
        'wcv-commissions',
        'wcv-all-vendors',
    );


    /**
     * WCV_Admin_Lite_Bar constructor.
     */
    public function __construct() {
        $this->is_pro_activated = is_wcv_pro_active();
        $this->allowed_pages    = apply_filters( 'wcv_admin_lite_bar_allowed_pages', $this->allowed_pages );
        add_action( 'in_admin_header', array( $this, 'display_lite_bar' ), 100 );
    }


    /**
     * Display the lite bar
     *
     * @since 2.4.8
     * @version 2.4.8
     */
    public function display_lite_bar() {
        $page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        if ( ! $this->is_pro_activated && in_array( $page, $this->allowed_pages, true ) ) {
            $this->display_bar();
        }
    }

    /**
     * Display bar
     *
     * @since 2.4.8
     * @version 2.4.8
     */
    private function display_bar() {

        $message = sprintf(
            '%s <a href="%s" target="_blank">%s</a>.',
            __( 'You\'re using WC Vendors Marketplace by WC Vendors free version. To unlock more features consider', 'wcvendors' ),
            esc_url( 'https://www.wcvendors.com/pricing/?utm_source=plugin&utm_medium=upsell&utm_campaign=litebar' ),
            __( 'upgrading to Pro', 'wcvendors' )
        );
        include WCV_ABSPATH_ADMIN . 'views/html-admin-lite-bar.php';
    }
}
