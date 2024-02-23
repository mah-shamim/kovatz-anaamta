<?php
/**
 * WC Vendors License Page
 *
 * @package  WC Vendors
 * @version 2.4.9
 * @since   2.4.9
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WC_Vendors_License_Page Class.
 */
class WCV_License_Page {

        /**
         * All our plugins.
         *
         * @var array
         */
        private $plugins = array();

        /**
         * Constructor.
         */
        public function __construct() {
            $this->plugins = array(
                'wcvendors_pro'             => array(
                    'title'                => __( 'WC Vendors', 'wc-vendors' ),
                    'tab_content_callback' => array( $this, 'wcvp_tab_content' ),
                ),
                'wcvendors_stripe_connect'  => array(
                    'title'                => __( 'Stripe Connect', 'wc-vendors' ),
                    'tab_content_callback' => array( $this, 'wcv_sc_tab_content' ),
                ),
                'wcvendors_membership'      => array(
                    'title'                => __( 'Membership', 'wc-vendors' ),
                    'tab_content_callback' => array( $this, 'wcvm_tab_content' ),
                ),
                'wcvendorss_tax'            => array(
                    'title'                => __( 'Tax', 'wc-vendors' ),
                    'tab_content_callback' => array( $this, 'wcvt_tab_content' ),
                ),
                'wcvendors_subscriptions'   => array(
                    'title'                => __( 'Subscriptions', 'wc-vendors' ),
                    'tab_content_callback' => array( $this, 'wcv_wcs_tab_content' ),
                ),
                'wcvendors_simple_auctions' => array(
                    'title'                => __( 'Simple Auctions', 'wc-vendors' ),
                    'tab_content_callback' => array( $this, 'wcv_sa_tab_content' ),
                ),
                'wcvendors_bookings'        => array(
                    'title'                => __( 'Bookings', 'wc-vendors' ),
                    'tab_content_callback' => array( $this, 'wcv_wcb_tab_content' ),
                ),
            );

            add_action( 'wcvendors_pro_before_license_page_content', array( $this, 'wcv_current_plan_status' ) );
        }

        /**
         * Get plugins.
         *
         * @version 2.4.9
         * @since  2.4.9
         *
         * @return array
         */
        public function get_plugins() {
            return apply_filters( 'wcvendors_license_page_plugins', $this->plugins );
        }

        /**
         * Ouput tabs titles.
         *
         * @version 2.4.9
         * @since   2.4.9
         */
        public function output_tabs_title() {
            $plugins = $this->get_plugins();
            $active  = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'wcvendors_pro'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            ?>
            <h2 class="nav-tab-wrapper">
                <?php
                foreach ( $plugins as $plugin => $data ) {
                    $tab_title = apply_filters(
                        "{$plugin}_license_page_tab_title_html",
                        sprintf(
                            '<a href="%s" class="nav-tab %s">%s</a>',
                            esc_url( add_query_arg( 'tab', $plugin, admin_url( 'admin.php?page=wc-vendors-license' ) ) ),
                            $active === $plugin ? 'nav-tab-active' : '',
                            esc_html( $data['title'] )
                        )
                    );
                    echo wp_kses_post( $tab_title );
                }
                ?>
            </h2>
            <?php
        }

        /**
         * Output tab content
         *
         * @version 2.4.9
         * @since   2.4.9
         */
        public function output_tabs_content() {

            $plugins = $this->get_plugins();
            $active  = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'wcvendors_pro'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            echo '<div class="wcv-tab-content wcv_product-border">';
            if ( isset( $plugins[ $active ]['tab_content_callback'] ) ) {
                do_action( "{$active}_before_license_page_content" );
                if ( is_callable( $plugins[ $active ]['tab_content_callback'] ) ) {
                    $content = call_user_func( $plugins[ $active ]['tab_content_callback'] );
                    $content = apply_filters( "{$active}_license_page_content", $content );
                    echo wp_kses_post( $content );
                }
                do_action( "{$active}_after_license_page_content" );
            }
            echo '</div>';
        }

        /**
         * Ouput the license page content.
         *
         * @version 2.4.9
         * @since   2.4.9
         */
        public function output() {
            echo '<div class="wrap wcv_addons_wrap">';
            echo '<h2 class="wcv-page-title">' . esc_html__( 'Licenses', 'wc-vendors' ) . '</h2>';
            echo '<p class="wcv-page-description">' . esc_html__( 'Enter your license keys below to enjoy full access, plugin updates, and support.', 'wc-vendors' ) . '</p>';
            $this->output_tabs_title();
            $this->output_tabs_content();
            echo '</div>';
        }

        /**
         * WC Vendors tab content.
         *
         * @version 2.4.9
         * @since   2.4.9
         */
        public function wcvp_tab_content() {
            ob_start();
            require_once WCV_ABSPATH_ADMIN . 'views/upsell/html-wcvp-tab-content.php';
            return ob_get_clean();
        }

        /**
         * WC Vendor Stripe Connect tab content.
         *
         * @version 2.4.9
         * @since   2.4.9
         */
        public function wcv_sc_tab_content() {
            ob_start();
            require_once WCV_ABSPATH_ADMIN . 'views/upsell/html-wcvsc-tab-content.php';
            return ob_get_clean();
        }

        /**
         * WC Vendor Tax tab content.
         *
         * @version 2.4.9
         * @since   2.4.9
         */
        public function wcvt_tab_content() {
            ob_start();
            require_once WCV_ABSPATH_ADMIN . 'views/upsell/html-wcvt-tab-content.php';
            return ob_get_clean();
        }

        /**
         * WC Vendor Membership tab content.
         *
         * @version 2.4.9
         * @since   2.4.9
         */
        public function wcvm_tab_content() {
            ob_start();
            require_once WCV_ABSPATH_ADMIN . 'views/upsell/html-wcvm-tab-content.php';
            return ob_get_clean();
        }

        /**
         * WC Vendor WooCommerce Subscriptions tab content.
         *
         * @version 2.4.9
         * @since   2.4.9
         */
        public function wcv_wcs_tab_content() {
            ob_start();
            require_once WCV_ABSPATH_ADMIN . 'views/upsell/html-wcv-wcs-tab-content.php';
            return ob_get_clean();
        }

        /**
         * WC Vendor WooCommerce Bookings tab content.
         *
         * @version 2.4.9
         * @since   2.4.9
         */
        public function wcv_wcb_tab_content() {
            ob_start();
            require_once WCV_ABSPATH_ADMIN . 'views/upsell/html-wcv-wcb-tab-content.php';
            return ob_get_clean();
        }

        /**
         * WC Vendor Simple Auctions tab content.
         *
         * @version 2.4.9
         * @since   2.4.9
         */
        public function wcv_sa_tab_content() {
            ob_start();
            require_once WCV_ABSPATH_ADMIN . 'views/upsell/html-wcv-sa-tab-content.php';
            return ob_get_clean();
        }

        /**
         * WCV current plan status.
         *
         * @version 2.4.9
         * @since   2.4.9
         */
        public function wcv_current_plan_status() {
            $current_plan = __( 'Free Version', 'wc-vendors' );
            if ( is_wcv_pro_active() ) {
                $current_plan = __( 'Pro Version', 'wc-vendors' );
            }

            $current_plan_html = sprintf(
                '<div class="wcvp-current-plan" style="font-size: 14px;"><b>%s</b> <span style="color: #6bb738;">%s</span></div>',
                esc_html__( 'License status: ', 'wc-vendors' ),
                esc_html( $current_plan )
            );
            echo wp_kses_post( apply_filters( 'wcvendors_pro_current_plan_status_html', $current_plan_html ) );
        }
}
