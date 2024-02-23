<?php
/**
 * Abstract Class YITH_WCBK_Booking_Abstract
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @package YITH\Booking
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'YITH_WCBK_Booking_Abstract' ) ) {
	/**
	 * Class YITH_WCBK_Booking_Abstract
	 *
	 * @since  2.0.0
	 */
	abstract class YITH_WCBK_Booking_Abstract extends YITH_WCBK_Data {

		/**
		 * Set function.
		 *
		 * @param string $prop  The property.
		 * @param mixed  $value The value.
		 *
		 * @deprecated 3.0.0 | use specific setter methods or update_meta_data() and save() instead.
		 */
		public function set( $prop, $value ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Booking::set', '3.0.0', 'specific setter methods or update_meta_data() and save()' );
			if ( $this->is_internal_prop( $prop ) ) {
				$this->set_prop( $prop, $value );
			} else {
				$this->update_meta_data( '_' . $prop, $value );
			}

			$this->save();
		}

		/**
		 * Get the name of the Booking: Booking #123
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_name( $context = 'view' ) {
			// translators: %s is the ID of the Booking. Example: Booking #123.
			$value = sprintf( _x( 'Booking #%s', 'Booking name', 'yith-booking-for-woocommerce' ), $this->get_id() );

			if ( 'view' === $context ) {
				$value = apply_filters( $this->get_hook_prefix() . 'name', $value, $this );
				$value = apply_filters( $this->get_hook(), $value, 'name', $this );
			}

			return $value;
		}

		/**
		 * Get the title of the booking: #123 Product Title
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		abstract public function get_title( $context = 'view' );

		/**
		 * Get the duration of booking including duration unit
		 */
		abstract public function get_duration_html();

		/**
		 * Check if the booking is valid
		 *
		 * @return bool
		 */
		abstract public function is_valid();

		/**
		 * Check if the booking is external
		 *
		 * @return bool
		 */
		public function is_external() {
			return false;
		}

		/**
		 * Get the edit link
		 *
		 * @return string
		 */
		abstract public function get_edit_link();

		/**
		 * Return the "from" timestamp
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		abstract public function get_from( $context = 'view' );

		/**
		 * Return the "to" timestamp
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		abstract public function get_to( $context = 'view' );

		/**
		 * Return the status
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		abstract public function get_status( $context = 'view' );

		/**
		 * Return string for status
		 *
		 * @return string
		 */
		abstract public function get_status_text();

		/**
		 * Return the product ID
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		abstract public function get_product_id( $context = 'view' );

		/**
		 * Return the product
		 *
		 * @return WC_Product_Booking|false
		 */
		abstract public function get_product();

		/**
		 * Return string for dates
		 *
		 * @param string $date_type The type of date : from | to.
		 *
		 * @return string
		 * @deprecated 3.0.0 | use YITH_WCBK_Booking_Abstract::get_formatted_from or YITH_WCBK_Booking_Abstract::get_formatted_to instead.
		 */
		public function get_formatted_date( $date_type ) {
			$format = wc_date_format();
			$getter = 'get_' . $date_type;

			$format .= $this->has_time() ? ( ' ' . wc_time_format() ) : '';

			return apply_filters( $this->get_hook_prefix() . 'formatted_date', date_i18n( $format, $this->$getter() ), $date_type, $this );
		}

		/**
		 * Return the formatted "from" date.
		 *
		 * @return string
		 */
		public function get_formatted_from() {
			return $this->has_time() ? yith_wcbk_datetime( $this->get_from() ) : yith_wcbk_date( $this->get_from() );
		}

		/**
		 * Return the formatted "to" date.
		 *
		 * @return string
		 */
		public function get_formatted_to() {
			return $this->has_time() ? yith_wcbk_datetime( $this->get_to() ) : yith_wcbk_date( $this->get_to() );
		}

		/**
		 * Retrieve a formatted name by a "format" parameter.
		 *
		 * @param string $format The format.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		abstract public function get_formatted_name( $format );

		/**
		 *
		 * Check if the booking can change status to $status
		 *
		 * @param string $status the status.
		 *
		 * @return bool
		 */
		abstract public function can_be( $status );

		/**
		 * Return true if the booking has time
		 *
		 * @return bool
		 */
		abstract public function has_time();

		/**
		 * Checks the booking status against a passed in status.
		 *
		 * @param string $status The status.
		 *
		 * @return bool
		 */
		abstract public function has_status( $status );
	}
}
