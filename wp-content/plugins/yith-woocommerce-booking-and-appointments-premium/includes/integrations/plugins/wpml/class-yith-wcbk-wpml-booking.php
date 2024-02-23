<?php
/**
 * Class YITH_WCBK_Wpml_Booking
 *
 * @author  YITH
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Wpml_Booking
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   2.1.28
 */
class YITH_WCBK_Wpml_Booking {
	/**
	 * Single intance of the class.
	 *
	 * @var YITH_WCBK_Wpml_Booking
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
	 * @return YITH_WCBK_Wpml_Booking
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

		add_filter( 'yith_wcbk_booking_get_title', array( $this, 'translate_booking_title' ), 10, 2 );
		add_filter( 'yith_wcbk_booking_details_product_title', array( $this, 'translate_booking_product_title' ), 10, 2 );
	}

	/**
	 * Translate Booking Title
	 *
	 * @param string            $title   The title.
	 * @param YITH_WCBK_Booking $booking The booking.
	 */
	public function translate_booking_title( $title, $booking ) {

		$product_id = $this->wpml_integration->get_current_language_id( $booking->get_product_id() );
		if ( absint( $product_id ) !== absint( $booking->get_product_id() ) ) {
			$product = wc_get_product( $product_id );
			if ( $product ) {
				$title = sprintf( '#%s %s', $booking->get_id(), $product->get_title() );
			}
		}

		return $title;
	}

	/**
	 * Translate Booking Product Title
	 *
	 * @param string            $title   The title.
	 * @param YITH_WCBK_Booking $booking The booking.
	 */
	public function translate_booking_product_title( $title, $booking ) {
		$product_id = $this->wpml_integration->get_current_language_id( $booking->get_product_id() );
		if ( absint( $product_id ) !== absint( $booking->get_product_id() ) ) {
			$product = wc_get_product( $product_id );

			if ( $product ) {
				$title = $product->get_title();
			}
		}

		return $title;
	}
}
