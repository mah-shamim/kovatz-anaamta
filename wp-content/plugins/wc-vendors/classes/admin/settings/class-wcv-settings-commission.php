<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The commission admin settings
 *
 * @author      Jamie Madden, WC Vendors
 * @category    Settings
 * @package     WCVendors/Admin/Settings
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WCVendors_Settings_Commission', false ) ) :

	/**
	 * WC_Admin_Settings_General.
	 */
	class WCVendors_Settings_Commission extends WCVendors_Settings_Page {

		/**
		 * Constructor.
		 */
		public function __construct() {

			$this->id    = 'commission';
			$this->label = __( 'Commission', 'wc-vendors' );

			parent::__construct();
		}

		/**
		 * Get sections.
		 *
		 * @return array
		 */
		public function get_sections() {

			$sections = array(
				'' => __( 'General', 'wc-vendors' ),
				'paypal' => __( 'PayPal Payouts Web', 'wc-vendors' ),
			);

			return apply_filters( 'wcvendors_get_sections_' . $this->id, $sections );
		}

		/**
		 * Get settings array.
		 *
		 * @return array
		 */
		public function get_settings( $current_section = '' ) {

			$settings = array();

			if ( 'paypal' === $current_section ){ 
				$settings = apply_filters(
					'wcvendors_settings_comission_paypal', array(
						// General Options
						array(
							'type' => 'title',
							'desc' => sprintf( __( 'These are the settings for the PayPal Payouts Web. You can manually pay up to 5000 %s by uploading a formatted file to your PayPal business account.', 'wc-vendors' ), wcv_get_vendor_name( false, false ) ),
							'id'   => 'paypal_web_options',
						),
						array(
							/* translators: %s: vendor name */
							'title'   => __( 'Payout Currency', 'wc-vendors' ),
							'desc'    => sprintf( __( 'Choose which currency to pay your %s in. ', 'wcvendors-pro' ), lcfirst( wcv_get_vendor_name( false ) ) ),
							'id'      => 'wcvendors_paypal_web_currency',
							'type'    => 'select',
							'class'   => 'wc-enhanced-select-nostd',
							'css'     => 'min-width:300px;',
							'options' => wcv_paypal_currencies(),
							'default' => get_woocommerce_currency(),
						),
						array(
							'title'    => __( 'Payout Note', 'wc-vendors' ),
							'desc'     => sprintf( __( 'Note to %s in payout.', 'wc-vendors' ), wcv_get_vendor_name() ),
							'id'       => 'wcvendors_paypal_payout_note',
							'css'      => 'width: 500px;min-height:100px',
							'default'  => '',
							'type'     => 'textarea',
							'default'  => sprintf( __( 'Commission payout for %s', 'wc-vendors' ), get_bloginfo( 'name' ) ),
						),
						array(
							'type' => 'sectionend',
							'id'   => 'paypal_web_options',
						),
					)
				);
			} else {
				$settings = apply_filters(
					'wcvendors_settings_comission', array(

						// General Options
						array(
							'type' => 'title',
							'desc' => __( 'These are the commission settings for your marketplace', 'wc-vendors' ),
							'id'   => 'commission_options',
						),
						array(
							'title'   => sprintf( __( '%s Commission %%', 'wc-vendors' ), wcv_get_vendor_name() ),
							'desc'    => sprintf( __( 'The global commission rate for your %s', 'wc-vendors' ), wcv_get_vendor_name( false, false ) ),
							'id'      => 'wcvendors_vendor_commission_rate',
							'css'     => 'width:55px;',
							'default' => '50',
							'type'    => 'number',
						),
						array(
							'title'   => __( 'Show reversed orders', 'wc-vendors' ),
							'desc'    => __( 'Show reversed / refunded orders on the order dashboard', 'wc-vendors' ),
							'id'      => 'wcvendors_dashboard_orders_show_reversed_orders',
							'type'    => 'checkbox',
							'default' => 'no',
						),
						array(
							'type' => 'sectionend',
							'id'   => 'commission_options',
						),

					)
				);
			}

			return apply_filters( 'wcvendors_get_settings_' . $this->id, $settings, $current_section );
		}

	}

endif;

return new WCVendors_Settings_Commission();
