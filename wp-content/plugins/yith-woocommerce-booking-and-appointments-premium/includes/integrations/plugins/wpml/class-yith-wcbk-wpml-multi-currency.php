<?php
/**
 * Class YITH_WCBK_Wpml_Multi_Currency
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Wpml_Multi_Currency
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   2.0.3
 */
class YITH_WCBK_Wpml_Multi_Currency {
	/**
	 * Single instance of the class.
	 *
	 * @var YITH_WCBK_Wpml_Multi_Currency
	 */
	private static $instance;

	/**
	 * WPML Integration instance.
	 *
	 * @var YITH_WCBK_Wpml_Integration
	 */
	public $wpml_integration;

	/**
	 * Singleton implementation
	 *
	 * @param YITH_WCBK_Wpml_Integration $wpml_integration WPML Integration instance.
	 *
	 * @return YITH_WCBK_Wpml_Multi_Currency
	 */
	public static function get_instance( $wpml_integration ) {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new static( $wpml_integration );
	}

	/**
	 * Constructor
	 *
	 * @param YITH_WCBK_Wpml_Integration $wpml_integration WPML Integration instance.
	 */
	private function __construct( $wpml_integration ) {
		$this->wpml_integration = $wpml_integration;

		add_filter( 'yith_wcbk_booking_product_get_price', array( $this, 'multi_currency_price' ) );
		add_filter( 'yith_wcbk_get_price_to_display', array( $this, 'multi_currency_price' ) ); // For totals.
		add_filter( 'yith_wcbk_booking_service_get_pricing_html_price', array( $this, 'multi_currency_price' ) ); // For service prices in tooltip.
		add_action( 'yith_wcbk_booking_form_start', array( $this, 'add_currency_hidden_input_in_booking_form' ), 20 );

		add_action( 'wp_ajax_yith_wcbk_get_booking_data', array( $this, 'set_currency_and_filters' ), 9 );
		add_action( 'wp_ajax_nopriv_yith_wcbk_get_booking_data', array( $this, 'set_currency_and_filters' ), 9 );

		add_filter( 'wcml_price_custom_fields_filtered', array( $this, 'remove_price_key_for_booking_products' ), 10, 2 );
	}

	/**
	 * Return true if the current version of WPML Multi Currency has the right classes and methods.
	 *
	 * @return bool
	 */
	public function check_wpml_classes() {
		global $woocommerce_wpml;

		return $woocommerce_wpml && isset( $woocommerce_wpml->multi_currency ) && isset( $woocommerce_wpml->multi_currency->prices ) && is_callable( array( $woocommerce_wpml->multi_currency, 'set_client_currency' ) ) && is_callable( array( $woocommerce_wpml->multi_currency, 'get_client_currency' ) );
	}

	/**
	 * Set currency and filters.
	 */
	public function set_currency_and_filters() {
		global $woocommerce_wpml;
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! $this->check_wpml_classes() || empty( $_REQUEST['yith_wcbk_wpml_currency'] ) ) {
			return;
		}

		$currency = wc_clean( wp_unslash( $_REQUEST['yith_wcbk_wpml_currency'] ) );
		$woocommerce_wpml->multi_currency->set_client_currency( $currency );

		add_filter( 'woocommerce_currency', array( $this, 'currency_filter' ) );
		add_filter( 'yith_wcbk_get_calculated_price_html_price', array( $this, 'multi_currency_price' ), 10, 3 );

		// phpcs:enable
	}

	/**
	 * Remove the _price key from multi currency filtered keys to prevent double price filtering
	 * the $object_id parameter will be added in WooCommerce Multi Currency > 4.4.2.1
	 *
	 * @param array $price_keys Price keys.
	 * @param int   $object_id  Object ID.
	 *
	 * @return array
	 */
	public function remove_price_key_for_booking_products( $price_keys, $object_id = 0 ) {
		$key = array_search( '_price', $price_keys, true );
		if ( $object_id && is_numeric( $object_id ) && yith_wcbk_is_booking_product( $object_id ) && false !== $key ) {
			unset( $price_keys[ $key ] );
		}

		return $price_keys;
	}

	/**
	 * Filter currency
	 *
	 * @param string $currency The currency.
	 *
	 * @return string
	 */
	public function currency_filter( $currency ) {
		global $woocommerce_wpml;

		return $woocommerce_wpml->multi_currency->get_client_currency();
	}

	/**
	 * Change price based on currency.
	 *
	 * @param string $price Price.
	 *
	 * @return float
	 */
	public function multi_currency_price( $price ) {
		return apply_filters( 'wcml_raw_price_amount', $price );
	}

	/**
	 * Add hidden input field in booking form.
	 */
	public function add_currency_hidden_input_in_booking_form() {
		global $woocommerce_wpml;
		if ( ! $this->check_wpml_classes() ) {
			return;
		}

		$client_currency = $woocommerce_wpml->multi_currency->get_client_currency();
		echo '<input type="hidden" class="yith-wcbk-booking-form-additional-data" name="yith_wcbk_wpml_currency" value="' . esc_attr( $client_currency ) . '" />';
	}
}
