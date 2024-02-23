<?php
/**
 * Services Functions
 *
 * @author  YITH
 * @package YITH\Booking\Functions
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_get_service_type_labels' ) ) {
	/**
	 * Get service type labels.
	 *
	 * @return array
	 */
	function yith_wcbk_get_service_type_labels() {
		$services_labels = array(
			'additional' => yith_wcbk_get_label( 'additional-services' ),
			'included'   => yith_wcbk_get_label( 'included-services' ),
		);

		return apply_filters( 'yith_wcbk_get_service_type_labels', $services_labels );
	}
}

if ( ! function_exists( 'yith_wcbk_split_services_by_type' ) ) {
	/**
	 * Split services by type.
	 *
	 * @param int[]|YITH_WCBK_Service[] $services       Services.
	 * @param bool                      $include_hidden Include hidden flag.
	 *
	 * @return mixed|void
	 */
	function yith_wcbk_split_services_by_type( $services, $include_hidden = true ) {
		$split_services = array(
			'additional' => array(),
			'included'   => array(),
		);
		if ( ! ! $services && is_array( $services ) ) {
			foreach ( $services as $service_id ) {
				$service = yith_get_booking_service( $service_id );

				if ( ! $service->is_valid() || ( ! $include_hidden && $service->is_hidden() ) ) {
					continue;
				}

				if ( $service->is_optional() ) {
					$split_services['additional'][] = $service;
				} else {
					$split_services['included'][] = $service;
				}
			}
		}

		return apply_filters( 'yith_wcbk_split_services_by_type', $split_services );
	}
}

if ( ! function_exists( 'yith_wcbk_booking_services_html' ) ) {
	/**
	 * Booking services HTML.
	 *
	 * @param string[] $service_names Service names.
	 *
	 * @return string
	 */
	function yith_wcbk_booking_services_html( $service_names ) {
		$separator = apply_filters( 'yith_wcbk_booking_services_separator', ', ' );

		return apply_filters( 'yith_wcbk_booking_services_html', implode( $separator, $service_names ) );
	}
}
