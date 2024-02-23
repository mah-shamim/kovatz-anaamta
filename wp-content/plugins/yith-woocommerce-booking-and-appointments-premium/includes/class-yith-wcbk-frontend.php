<?php
/**
 * Frontend class.
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Frontend' ) ) {
	/**
	 * Class YITH_WCBK_Frontend
	 * handle all frontend behavior
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Frontend {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * Cart instance.
		 *
		 * @var YITH_WCBK_Cart
		 */
		public $cart;

		/**
		 * YITH_WCBK_Frontend constructor.
		 */
		private function __construct() {

			$this->cart = YITH_WCBK_Cart::get_instance();
			YITH_WCBK_Frontend_Action_Handler::init();
			YITH_WCBK_Search_Form_Frontend::get_instance();
			YITH_WCBK_Frontend_Assets::get_instance();

			$booking_form_position = get_option( 'yith-wcbk-booking-form-position', 'default' );
			switch ( $booking_form_position ) {
				case 'default':
					add_action( 'woocommerce_booking_add_to_cart', array( $this, 'print_add_to_cart_template' ) );
					break;
				case 'before_summary':
					add_action( 'woocommerce_before_single_product_summary', array( $this, 'print_add_to_cart_template' ) );
					break;
				case 'after_title':
					add_action( 'woocommerce_single_product_summary', array( $this, 'print_add_to_cart_template' ), 7 );
					break;
				case 'before_description':
					add_action( 'woocommerce_single_product_summary', array( $this, 'print_add_to_cart_template' ), 15 );
					break;
				case 'after_description':
					add_action( 'woocommerce_single_product_summary', array( $this, 'print_add_to_cart_template' ), 25 );
					break;
				case 'after_summary':
					add_action( 'woocommerce_after_single_product_summary', array( $this, 'print_add_to_cart_template' ) );
					break;
				case 'widget':
					add_action( 'woocommerce_before_single_product', array( $this, 'remove_actions_if_booking_form_in_widget' ) );
					break;
			}

			add_action( 'yith_wcbk_booking_add_to_cart_form', array( $this, 'print_add_to_cart_template' ) );

			add_filter( 'body_class', array( $this, 'add_classes_to_body' ) );

			add_filter( 'is_active_sidebar', array( $this, 'is_active_sidebar' ), 10, 2 );

			if ( 'yes' === get_option( 'yith-wcbk-hide-add-to-cart-button-in-loop', 'no' ) ) {
				add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'hide_add_to_cart_in_loop' ), 10, 2 );
			}
		}

		/**
		 * Hide add-to-cart button in loop for Booking products.
		 *
		 * @param string     $link_html The HTML link.
		 * @param WC_Product $product   The product.
		 *
		 * @since 3.0.0
		 */
		public function hide_add_to_cart_in_loop( $link_html, $product ) {
			if ( $product && yith_wcbk_is_booking_product( $product ) ) {
				$link_html = '';
			}

			return $link_html;
		}

		/**
		 * Filter the is_active_sidebar
		 * to exclude the Product Form widget if is not a booking product
		 *
		 * @param bool $active Active flag.
		 * @param int  $index  Index.
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		public function is_active_sidebar( $active, $index ) {
			if ( $active ) {
				$product    = function_exists( 'wc_get_product' ) && is_product() ? wc_get_product() : false;
				$is_booking = $product && $product->is_type( 'booking' );

				if ( ! $is_booking ) {
					$sidebars_widgets = wp_get_sidebars_widgets();
					$widgets          = $sidebars_widgets[ $index ];
					foreach ( $widgets as $key => $widget ) {
						if ( strpos( $widget, 'yith_wcbk_product_form' ) !== false ) {
							unset( $widgets[ $key ] );
						}
					}
					$active = ! empty( $widgets );
				}
			}

			return $active;
		}

		/**
		 * Remove actions if booking form position is 'widget'
		 * remove price and rating
		 */
		public function remove_actions_if_booking_form_in_widget() {
			global $product;
			if ( $product && yith_wcbk_is_booking_product( $product ) ) {
				$actions_to_remove = array(
					array( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' ),
					array( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating' ),
				);

				foreach ( $actions_to_remove as $action_to_remove ) {
					$priority = has_action( $action_to_remove[0], $action_to_remove[1] );
					if ( $priority ) {
						remove_action( $action_to_remove[0], $action_to_remove[1], $priority );
					}
				}
			}
		}

		/**
		 * Print add-to-cart template for Booking product
		 *
		 * @return void
		 */
		public function print_add_to_cart_template() {
			global $product;
			if ( YITH_WCBK_Product_Post_Type_Admin::is_booking( $product ) ) {
				wc_get_template( 'single-product/add-to-cart/booking.php', array(), '', YITH_WCBK_TEMPLATE_PATH );
			}
		}

		/**
		 * Add classes in body
		 *
		 * @param array $classes Body classes.
		 *
		 * @return array
		 */
		public function add_classes_to_body( $classes ) {
			$booking_classes = array( 'yith-booking' );

			return array_merge( $classes, $booking_classes );
		}
	}
}
/**
 * Unique access to instance of YITH_WCBK_Frontend class
 *
 * @return YITH_WCBK_Frontend
 */
function yith_wcbk_frontend() {
	return YITH_WCBK_Frontend::get_instance();
}
