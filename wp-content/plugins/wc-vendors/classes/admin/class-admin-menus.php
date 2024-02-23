<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The admin class handles all admin custom page functions for admin view
 *
 * @author      Jamie Madden, WC Vendors
 * @category    Admin
 * @package     WCVendors/Admin
 * @version     2.0.0
 */
class WCVendors_Admin_Menus {

    /**
     * The commissions table name
     *
     * @var string
     * @version 2.4.7
     * @since   1.0.0
     */
    public $commissions_table;

    /**
     * Constructor
     */
    public function __construct() {

		// Add menus.
		add_action( 'current_screen', array( $this, 'add_wcv_logo' ), 0 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'commissions_menu' ), 20 );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 40 );
		add_action( 'admin_menu', array( $this, 'extensions_menu' ), 60 );
		add_action( 'admin_menu', array( $this, 'all_vendors_menu' ), 30 );
        add_action( 'admin_menu', array( $this, 'license_page' ), 50 );
        // Add help page and about page menu items.
        add_action( 'admin_menu', array( $this, 'help_menu' ), 70 );
        add_action( 'admin_menu', array( $this, 'about_menu' ), 80 );
		if ( ! is_wcv_pro_active() ) {
			add_action( 'admin_menu', array( $this, 'go_pro_menu' ), 90 );
			add_action( 'admin_menu', array( $this, 'pricing_link' ), 100 );
		}

        add_action( 'admin_head', array( $this, 'commission_table_header_styles' ) );
        add_action( 'admin_head', array( $this, 'admin_menu_styles' ) );
        add_action( 'admin_footer', array( $this, 'commission_table_script' ) );
        add_action( 'admin_footer', array( $this, 'admin_menu_scripts' ) );

		add_filter( 'set_screen_option_wcvendor_commissions_perpage', array( __CLASS__, 'set_commissions_screen' ), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

    /**
     * WC Vendors menu
     */
    public function admin_menu() {

        global $menu;

		if ( current_user_can( 'manage_woocommerce' ) ) {
			$menu[] = array( '', 'read', 'separator-woocommerce', '', 'wp-menu-separator wcvendors' ); //phpcs:ignore
		}

        add_menu_page(
            __( 'WC Vendors', 'wc-vendors' ),
            __( 'WC Vendors', 'wc-vendors' ),
            'manage_woocommerce',
            'wc-vendors',
            array( $this, 'extensions_page' ),
            'dashicons-cart',
            50
        );
    }

    /**
     * Addons menu item.
     */
    public function extensions_menu() {

        add_submenu_page(
            'wc-vendors',
            __( 'WC Vendors Extensions', 'wc-vendors' ),
            __( 'Extensions', 'wc-vendors' ),
            'manage_woocommerce',
            'wcv-extensions',
            array( $this, 'extensions_page' )
        );
        remove_submenu_page( 'wc-vendors', 'wc-vendors' );
    }

    /**
     *    Addons Page
     */
    public function extensions_page() {
        WCVendors_Admin_Extensions::output();
    }

    /**
     * Go Pro Menu.
     *
     * @since 2.2.2
     */
    public function go_pro_menu() {

        add_submenu_page(
            'wc-vendors',
            __( 'Upgrade To WC Vendors Pro Today', 'wc-vendors' ),
            __( 'Upgrade to Pro', 'wc-vendors' ),
            'manage_woocommerce',
            'wcv-go-pro',
            array( $this, 'go_pro_page' )
        );
        remove_submenu_page( 'wc-vendors', 'wc-vendors' );
    }

    /**
     * Go Pro Page output
     *
     * @since 2.2.2
     */
    public function go_pro_page() {
        WCVendors_Admin_GoPro::output();
    }

    /**
     * Add the commissions sub menu
     *
     * @since  1.0.0
     * @access public
     */
    public function commissions_menu() {

        $commissions_page = add_submenu_page(
            'wc-vendors',
            __( 'Commissions', 'wc-vendors' ),
            __( 'Commissions', 'wc-vendors' ),
            'manage_woocommerce',
            'wcv-commissions',
            array(
                $this,
                'commissions_page',
            )
        );

        add_action( "load-$commissions_page", array( $this, 'commission_screen_options' ) );
    }


    /**
     * Settings menu item
     */
    public function settings_menu() {

        $settings_page = add_submenu_page(
            'wc-vendors',
            __( 'WC Vendors Settings', 'wc-vendors' ),
            __( 'Settings', 'wc-vendors' ),
            'manage_woocommerce',
            'wcv-settings',
            array( $this, 'settings_page' )
        );

        add_action( 'load-' . $settings_page, array( $this, 'settings_page_init' ) );
    }


    /**
     *  Loads required objects into memory for use within settings
     */
    public function settings_page_init() {

        global $current_tab, $current_section;

        // phpcs:disable
        // Include settings pages.
        WCVendors_Admin_Settings::get_settings_pages();

		// Get current tab/section.

		$current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( $_GET['tab'] );// phpcs:ignore
		$current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( $_REQUEST['section'] ); // phpcs:ignore

        // Save settings if data has been posted.
        if ( ! empty( $_POST ) ) { //phpcs:ignore
            WCVendors_Admin_Settings::save();
        }

        // Add any posted messages.
        if ( ! empty( $_GET['wcv_error'] ) ) {
            WCVendors_Admin_Settings::add_error( stripslashes( $_GET['wcv_error'] ) );
        }

        if ( ! empty( $_GET['wcv_message'] ) ) {
            WCVendors_Admin_Settings::add_message( stripslashes( $_GET['wcv_message'] ) );
        }
        // phpcs:enable
    }

    /**
     * Settings Page
     */
    public function settings_page() {

        WCVendors_Admin_Settings::output();
    }

    /**
     * Commission page output
     *
     * @since 2.0.0
     */
    public function commissions_page() {
        include WCV_ABSPATH_ADMIN . 'views/html-admin-commission-page.php';
    }


    /**
     * Screen options
     */
    public function commission_screen_options() {

        $option = 'per_page';
        $args   = array(
            'label'   => 'Commissions',
            'default' => 10,
            'option'  => 'wcvendor_commissions_perpage',
        );

        add_screen_option( $option, $args );

        $this->commissions_table = new WCVendors_Commissions_Page();
    }

    /**
     * Set commission screen options
     *
     * @param string $status The status of the screen option. Required.
     * @param string $option The option name. Required.
     * @param mixed  $value The value of the option. Required.
     * @return mixed
     * @version 2.4.7
     * @since   2.4.7
     */
    public static function set_commissions_screen( $status, $option, $value ) {

        return $value;
    }

    /**
     * Add menu styles for the upgrade link
     *
     * @return void
     * @version 2.4.7
     * @since   2.4.7
     */
    public function admin_menu_styles() {
        ?>
        <style type="text/css">
            .toplevel_page_wc-vendors li a[href="admin.php?page=wcv-go-pro"]
            {
            background: #6bb738;
            color: #fff;
            font-weight: 700;
            border-left-color: #fff;
            }
        </style>
        <?php
    }

	/**
	 * Add script to modify the menu style after the page has loaded
	 *
	 * @return void
	 * @version 2.4.7
	 * @since   2.4.7
	 */
	public function admin_menu_scripts() {
		?>
		<script>
			jQuery(document).ready(function ($) {
				$('.toplevel_page_wc-vendors li a[href$="upgradetopro"]')
					.css('background', '#6bb738')
					.css('border-left-color', '#fff')
					.css('color', '#fff')
					.css('font-weight', '700');
			});
		</script>
		<?php
    }

    /**
     * Load styles for the commissions table page
     */
    public function commission_table_header_styles() {

        $page = ( isset( $_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false; // phpcs:ignore

        wp_enqueue_style( 'wcv-admin-styles', WCV_ASSETS_URL . 'css/wcv-admin.css', array(), WCV_VERSION );

        // Only load the styles on the license table page.
        if ( 'wcv-commissions' !== $page ) {
            return;
        }

        echo '<style type="text/css">';
        echo '.wp-list-table .column-qty { width: 8%; }';
        echo '.wp-list-table .column-order_id { width: 8%; }';
        echo '.wp-list-table .column-vendor_id { width: 12%; }';
        echo '.wp-list-table .column-total_due { width: 10%;}';
        echo '.wp-list-table .column-total_shipping { width: 8%;}';
        echo '.wp-list-table .column-tax { width: 5%;}';
        echo '.wp-list-table .column-totals { width: 6%;}';
        echo '.wp-list-table .column-status { width: 7%;}';
        echo '.wp-list-table .column-shipped { width: 7%;}';
        echo '.wp-list-table .column-time { width: 10%;}';
        echo '</style>';
    } //table_header_styles

    /**
     * Print script required by commission.
     *
     * @return  void
     * @version 2.1.20
     * @since   2.1.20
     */
    public function commission_table_script() {

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }
        wp_enqueue_script( 'jquery-ui-datepicker' );
        ?>
        <script>
            jQuery(document).ready(
            function() {
                jQuery('#from_date, #to_date').datepicker({
                dateFormat: 'yy-mm-dd'
                });

                jQuery("#vendor_id").select2();

                jQuery('#reset').click( function(e){
                e.preventDefault();
                jQuery('#from_date, #to_date').val('');
                jQuery('#com_status_dropdown, #vendor_id').val('').select2();

                jQuery('#posts-filter').submit();
                });
            }
            );
        </script>
        <?php
    }

	/**
	 * Add wcv Logo to admin menu
	 *
	 * @return void
	 */
	public function add_wcv_logo() {
		$parent         = get_admin_page_parent();
		$hook           = get_current_screen()->id;
		$exclude_screen = apply_filters( 'wcvendors_exclude_logo_screen', array( 'wc-vendors_page_wcv-go-pro' ) );
		if ( 'wc-vendors' === $parent && ! in_array( $hook, $exclude_screen, true ) ) {
			add_filter( "$hook", array( $this, 'wcv_logo' ), 0 );
		}
	}

	/**
	 * Add wcv Logo to admin menu
	 *
	 * @return void
	 */
	public function wcv_logo() {
		echo '<div class="wcv-logo-wrap">';
		echo '<a href="https://www.wcvendors.com/?utm_source=plugin&utm_medium=pageheader&utm_campaign=headerlogo" target="_blank"><img class="wcv-logo" src="' . esc_url( WCV_ASSETS_URL ) . 'images/wcvendors_logo.png" alt="WC Vendors" /></a>';
		if ( ! is_wcv_pro_active() ) {
			echo '<a href="https://www.wcvendors.com/pricing/?utm_source=plugin&utm_medium=pageheader&utm_campaign=upgradetopro" target="_blank" class="wcv-upgrade-pro">' . esc_html__( 'Upgrade to Pro', 'wc-vendors' ) . '</a>';
		}
		echo '</div>';
	}

	/**
	 * All Vendors page submenu
	 *
	 * @return void
     */
	public function all_vendors_menu() {
		add_submenu_page(
			'wc-vendors',
			__( 'All Vendors', 'wc-vendors' ),
			__( 'All Vendors', 'wc-vendors' ),
			'manage_woocommerce',
			'wcv-all-vendors',
			array( $this, 'all_vendors_page' )
		);
	}

	/**
	 * All Vendors page output
	 *
	 * @return void
	 */
	public function all_vendors_page() {
		echo '<div class="wrap">';
		echo '<h1 class="wcv-page-title">' . esc_html__( 'Vendor Management', 'wc-vendors' ) . '</h1>';
		echo '<p class="wcv-page-description">' . esc_html__( 'Manage your vendor store details, set commission rates, and control vendor status.', 'wc-vendors' ) . '</p>';
		echo '<div id="avp" class="avp_ui"></div>';
		echo '</div>';
	}

	/**
	 * Pricing link navigate
	 *
	 * @version 2.4.8
	 * @since   2.4.8
	 */
	public function pricing_link() {
		global $submenu;

		if ( ! isset( $submenu['wc-vendors'] ) ) {
			return;
		}

		foreach ( $submenu['wc-vendors'] as $key => $menu_item ) {
			if ( 'wcv-go-pro' === $menu_item[2] ) {
				$submenu['wc-vendors'][ $key ][2] = 'https://www.wcvendors.com/pricing/?utm_source=plugin&utm_medium=menulink&utm_campaign=upgradetopro'; // phpcs:ignore
			}
		}
	}

	/**
	 * Enqueue scripts
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'admin-script', WCV_ASSETS_URL . 'js/admin/wcv-admin.js', array( 'jquery' ), WCV_VERSION, true );
		wp_localize_script(
			'admin-script',
			'wcv_admin_script_params',
			array(
				'installing_text'        => __( 'Installing...', 'wc-vendors' ),
				'install_text'           => __( 'Install', 'wc-vendors' ),
				'installed_text'         => __( 'Installed', 'wc-vendors' ),
				'install_nonce'          => wp_create_nonce( 'wcv_install_plugin' ),
				'installed_message'      => __( 'The plugin has been installed and activated.', 'wc-vendors' ),
				'try_again_text'         => __( 'Try again', 'wc-vendors' ),
                'switch_cc_blocks_nonce' => wp_create_nonce( 'switch_cc_blocks' ),
			)
		);
	}

    /**
     * License page
     *
     * @since 2.4.9
     * @version 2.4.9
     */
    public function license_page() {
        add_submenu_page(
            'wc-vendors',
            __( 'WC Vendors License', 'wc-vendors' ),
            __( 'License', 'wc-vendors' ),
            'manage_woocommerce',
            'wc-vendors-license',
            array( $this, 'license_page_output' )
        );
    }

    /**
     * License page output
     *
     * @since 2.4.9
     * @version 2.4.9
     */
    public function license_page_output() {
        $license_page = new WCV_License_Page();
        $license_page->output();
    }

    /**
     * Help menu item.
     *
     * @since 2.4.9
     * @version 2.4.9
     */
    public function help_menu() {

        add_submenu_page(
            'wc-vendors',
            __( 'Getting Help', 'wc-vendors' ),
            __( 'Help', 'wc-vendors' ),
            'manage_woocommerce',
            'wcv-help',
            array( $this, 'help_page' )
        );
    }

    /**
     * Help Page
     *
     * @since 2.4.9
     * @version 2.4.9
     */
    public function help_page() {
        include WCV_ABSPATH_ADMIN . 'views/html-admin-help-page.php';
    }

    /**
     * About menu item.
     *
     * @since 2.4.9
     * @version 2.4.9
     */
    public function about_menu() {

        add_submenu_page(
            'wc-vendors',
            __( 'About WC Vendors', 'wc-vendors' ),
            __( 'About', 'wc-vendors' ),
            'manage_woocommerce',
            'wcv-about',
            array( $this, 'about_page' )
        );
    }

    /**
     * About Page
     *
     * @since 2.4.9
     * @version 2.4.9
     */
    public function about_page() {
        $plugin_installer = ( new WCV_Plugin_Installer() )->get_instance();
        $plugin_installer->set_exclude_plugins(
            array(
				'woocommerce',
                'wc-vendors-pro-simple-auctions',
                'wc-vendors-membership',
                'wc-vendors-woocommerce-subscriptions',
                'wc-vendors-tax',
                'wc-vendors-woocommerce-bookings',
                'wc-vendors-gateway-stripe-connect',
                'wc-vendors-pro',
            )
        );
        include WCV_ABSPATH_ADMIN . 'views/html-admin-about-page.php';
    }
}
new WCVendors_Admin_Menus();
