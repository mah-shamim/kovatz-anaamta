<?php
/**
 * Class YITH_WCBK_Shortcodes
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Shortcodes' ) ) {
	/**
	 * Class YITH_WCBK_Shortcodes
	 * register and manage shortcodes
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Shortcodes {

		/**
		 * Init.
		 */
		public static function init() {
			$shortcodes = array(
				'booking_form'        => __CLASS__ . '::booking_form',
				'booking_search_form' => __CLASS__ . '::booking_search_form',
				'booking_map'         => __CLASS__ . '::booking_map',
				'booking_services'    => __CLASS__ . '::booking_services',
			);

			foreach ( $shortcodes as $shortcode => $function ) {
				add_shortcode( $shortcode, $function );
			}
		}

		/**
		 * Booking Form
		 *
		 * @param array $atts Attributes.
		 *
		 * @return string
		 */
		public static function booking_form( $atts ) {
			global $product;
			ob_start();
			$booking_id = $atts['id'] ?? 0;

			if ( ! $booking_id && $product && $product->get_id() ) {
				$booking_id = $product->get_id();
			}

			if ( $booking_id ) {
				$booking_product = wc_get_product( $booking_id );
				if ( $booking_product && $booking_product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
					global $product, $post;
					$old_product = $product;
					$old_post    = $post;
					$post        = get_post( $booking_product->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$product     = $booking_product;
					wc_get_template( 'shortcodes/booking-form.php', array(), '', YITH_WCBK_TEMPLATE_PATH );
					$product = $old_product;
					$post    = $old_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				}
			}

			return ob_get_clean();
		}

		/**
		 * Booking search form
		 *
		 * @param array $atts Attributes.
		 *
		 * @return string
		 */
		public static function booking_search_form( $atts ) {
			ob_start();
			$form_id = $atts['id'] ?? 0;
			if ( $form_id ) {
				$form = new YITH_WCBK_Search_Form( $form_id );
				$form->output( $atts );
			}

			return ob_get_clean();
		}

		/**
		 * Booking map
		 *
		 * @param array $atts Attributes.
		 *
		 * @return string
		 */
		public static function booking_map( $atts ) {
			$product_id = isset( $atts['id'] ) ? absint( $atts['id'] ) : false;
			ob_start();
			/**
			 * The booking product.
			 *
			 * @var WC_Product_Booking $product
			 */
			$product = wc_get_product( $product_id );

			if ( yith_wcbk_is_booking_product( $product ) ) {
				$coordinates = false;
				if ( isset( $atts['latitude'] ) && isset( $atts['longitude'] ) ) {
					$coordinates = array(
						'lat' => $atts['latitude'],
						'lng' => $atts['longitude'],
					);
				} elseif ( $product && $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
					$coordinates = $product->get_location_coordinates();
				}

				if ( $coordinates ) {
					$width  = $atts['width'] ?? '100%';
					$height = $atts['height'] ?? '500px';
					$zoom   = absint( $atts['zoom'] ?? 9 );
					$type   = $atts['type'] ?? 'ROADMAP';

					$width  = ( ! is_numeric( $width ) ) ? $width : $width . 'px';
					$height = ( ! is_numeric( $height ) ) ? $height : $height . 'px';

					wc_get_template( 'shortcodes/booking-map.php', compact( 'coordinates', 'product', 'width', 'height', 'zoom', 'type' ), '', YITH_WCBK_TEMPLATE_PATH );

				}
			}

			return ob_get_clean();

		}

		/**
		 * Booking services
		 *
		 * @param array $atts Attributes.
		 *
		 * @return string
		 */
		public static function booking_services( $atts ) {
			global $product;
			$html = '';
			/**
			 * The booking product.
			 *
			 * @var WC_Product_Booking $product
			 */
			if ( $product && yith_wcbk_is_booking_product( $product ) ) {
				$defaults        = array(
					'type'              => 'all',
					'show_title'        => 'yes',
					'show_prices'       => 'no',
					'show_descriptions' => 'yes',
				);
				$atts            = wp_parse_args( $atts, $defaults );
				$atts['product'] = $product;
				ob_start();

				wc_get_template( 'shortcodes/booking-services.php', $atts, '', YITH_WCBK_TEMPLATE_PATH );

				$html = ob_get_clean();
			}

			return $html;
		}
	}
}
