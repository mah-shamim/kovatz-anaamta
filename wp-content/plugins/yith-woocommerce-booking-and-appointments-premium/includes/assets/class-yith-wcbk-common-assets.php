<?php
/**
 * Common Assets class.
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Common_Assets' ) ) {
	/**
	 * Class YITH_WCBK_Common_Assets
	 * Register and enqueue styles and scripts in Admin and in Frontend
	 *
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since    2.0.0
	 */
	class YITH_WCBK_Common_Assets {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Common_Assets constructor.
		 */
		private function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Styles
		 */
		public function enqueue_styles() {
			wp_register_style( 'yith-wcbk', YITH_WCBK_ASSETS_URL . '/css/global.css', array(), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-people-selector', YITH_WCBK_ASSETS_URL . '/css/people-selector.css', array( 'yith-wcbk' ), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-date-range-picker', YITH_WCBK_ASSETS_URL . '/css/date-range-picker.css', array( 'yith-wcbk' ), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-datepicker', YITH_WCBK_ASSETS_URL . '/css/datepicker.css', array( 'yith-wcbk' ), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-fields', YITH_WCBK_ASSETS_URL . '/css/fields.css', array( 'yith-wcbk' ), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-booking-form', YITH_WCBK_ASSETS_URL . '/css/booking-form.css', array( 'yith-wcbk', 'yith-wcbk-fields', 'yith-wcbk-people-selector', 'yith-wcbk-date-range-picker', 'yith-plugin-fw-icon-font' ), YITH_WCBK_VERSION );

			if ( 'wp_enqueue_scripts' === current_action() ) {
				// Customize only frontend styles.
				$this->handle_style_customization();
			}
		}

		/**
		 * Handle style customization
		 *
		 * @since 3.0.0
		 */
		private function handle_style_customization() {
			$css = '';
			foreach ( yith_wcbk_get_colors() as $var => $value ) {
				$css .= '--yith-wcbk-' . $var . ':' . $value . ';';
			}

			$css = ':root{' . $css . '}';

			wp_add_inline_style( 'yith-wcbk', $css );
		}

		/**
		 * Get Booking global params.
		 *
		 * @param string $context The context.
		 *
		 * @return array
		 */
		public static function get_bk_global_params( $context = 'common' ) {
			$loader_svg = yith_wcbk_print_svg( 'loader', false );
			$bk         = array(
				'ajaxurl'             => admin_url( 'admin-ajax.php' ),
				'loader_svg'          => $loader_svg,
				'settings'            => array(
					'check_min_max_duration_in_calendar' => yith_wcbk()->settings->check_min_max_duration_in_calendar() ? 'yes' : 'no',
					'datepickerFormat'                   => yith_wcbk()->settings->get_date_picker_format(),
				),
				'blockParams'         => array(
					'message'         => $loader_svg,
					'blockMsgClass'   => 'yith-wcbk-block-ui-element',
					'css'             => array(
						'border'     => 'none',
						'background' => 'transparent',
					),
					'overlayCSS'      => array(
						'background' => '#ffffff',
						'opacity'    => '0.7',
					),
					'ignoreIfBlocked' => false,
				),
				'blockParamsNoLoader' => array(
					'message'         => '',
					'css'             => array(
						'border'     => 'none',
						'background' => 'transparent',
					),
					'overlayCSS'      => array(
						'background' => '#ffffff',
						'opacity'    => '0.7',
					),
					'ignoreIfBlocked' => false,
				),
				'blockParamsEmpty'    => array(
					'message'         => false,
					'overlayCSS'      => array(
						'opacity' => '0',
					),
					'ignoreIfBlocked' => false,
				),
				'blockParamsDisable'  => array(
					'message'         => ' ',
					'css'             => array(
						'border'     => 'none',
						'background' => '#fff',
						'top'        => '0',
						'left'       => '0',
						'height'     => '100%',
						'width'      => '100%',
						'opacity'    => '0.7',
						'cursor'     => 'default',
					),
					'overlayCSS'      => array(
						'opacity' => '0',
					),
					'ignoreIfBlocked' => true,
				),
				'i18n_durations'      => array(
					'month'  => array(
						'singular_unit' => yith_wcbk_get_duration_unit_label( 'month', 1 ),
						'plural_unit'   => yith_wcbk_get_duration_unit_label( 'month', 2 ),
						'singular'      => yith_wcbk_get_duration_label_string( 'month' ),
						'plural'        => yith_wcbk_get_duration_label_string( 'month', true ),
						'singular_qty'  => yith_wcbk_get_duration_label_string( 'month', false, 'unit' ),
						'plural_qty'    => yith_wcbk_get_duration_label_string( 'month', true, 'unit' ),
					),
					'day'    => array(
						'singular_unit' => yith_wcbk_get_duration_unit_label( 'day', 1 ),
						'plural_unit'   => yith_wcbk_get_duration_unit_label( 'day', 2 ),
						'singular'      => yith_wcbk_get_duration_label_string( 'day' ),
						'plural'        => yith_wcbk_get_duration_label_string( 'day', true ),
						'singular_qty'  => yith_wcbk_get_duration_label_string( 'day', false, 'unit' ),
						'plural_qty'    => yith_wcbk_get_duration_label_string( 'day', true, 'unit' ),
					),
					'hour'   => array(
						'singular_unit' => yith_wcbk_get_duration_unit_label( 'hour', 1 ),
						'plural_unit'   => yith_wcbk_get_duration_unit_label( 'hour', 2 ),
						'singular'      => yith_wcbk_get_duration_label_string( 'hour' ),
						'plural'        => yith_wcbk_get_duration_label_string( 'hour', true ),
						'singular_qty'  => yith_wcbk_get_duration_label_string( 'hour', false, 'unit' ),
						'plural_qty'    => yith_wcbk_get_duration_label_string( 'hour', true, 'unit' ),
					),
					'minute' => array(
						'singular_unit' => yith_wcbk_get_duration_unit_label( 'minute', 1 ),
						'plural_unit'   => yith_wcbk_get_duration_unit_label( 'minute', 2 ),
						'singular'      => yith_wcbk_get_duration_label_string( 'minute' ),
						'plural'        => yith_wcbk_get_duration_label_string( 'minute', true ),
						'singular_qty'  => yith_wcbk_get_duration_label_string( 'minute', false, 'unit' ),
						'plural_qty'    => yith_wcbk_get_duration_label_string( 'minute', true, 'unit' ),
					),
				),
				'nonces'              => array(
					'searchBookingProductsPaged'  => wp_create_nonce( 'search-booking-products-paged' ),
					'getBookingData'              => wp_create_nonce( 'get-booking-data' ),
					'getAvailableTimes'           => wp_create_nonce( 'get-available-times' ),
					'getProductNonAvailableDates' => wp_create_nonce( 'get-product-non-available-dates' ),
				),
			);

			return apply_filters( 'yith_wcbk_assets_bk_global_params', $bk, $context );
		}

		/**
		 * Scripts
		 */
		public function enqueue_scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			$bk = self::get_bk_global_params();

			wp_register_script( 'yith-wcbk-people-selector', YITH_WCBK_ASSETS_URL . '/js/yith-wcbk-people-selector' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );
			wp_localize_script(
				'yith-wcbk-people-selector',
				'yith_people_selector_params',
				apply_filters(
					'yith_wcbk_js_people_selector_params',
					array(
						'i18n_zero_person'  => __( 'Select people', 'yith-booking-for-woocommerce' ),
						'i18n_one_person'   => __( '1 person', 'yith-booking-for-woocommerce' ),
						// translators: %s is the number of persons.
						'i18n_more_persons' => __( '%s persons', 'yith-booking-for-woocommerce' ),
					)
				)
			);

			wp_register_script( 'yith-wcbk-monthpicker', YITH_WCBK_ASSETS_URL . '/js/monthpicker' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );
			wp_register_script( 'yith-wcbk-datepicker', YITH_WCBK_ASSETS_URL . '/js/datepicker' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-blockui', 'yith-wcbk-dates' ), YITH_WCBK_VERSION, true );
			wp_localize_script( 'yith-wcbk-datepicker', 'bk', $bk );
			wp_localize_script(
				'yith-wcbk-datepicker',
				'yith_wcbk_datepicker_params',
				array(
					'i18n_clear' => apply_filters( 'yith_wcbk_i18n_clear', __( 'Clear', 'yith-booking-for-woocommerce' ) ),
				)
			);
			wp_localize_script( 'yith-wcbk-people-selector', 'bk', $bk );


			wp_register_script( 'yith-wcbk-dates', YITH_WCBK_ASSETS_URL . '/js/yith-wcbk-dates' . $suffix . '.js', array(), YITH_WCBK_VERSION, true );

			wp_register_script( 'yith-wcbk-fields', YITH_WCBK_ASSETS_URL . '/js/fields' . $suffix . '.js', array( 'jquery-tiptip' ), YITH_WCBK_VERSION, true );

			wp_register_script( 'yith-wcbk-booking-form', YITH_WCBK_ASSETS_URL . '/js/booking_form' . $suffix . '.js', array( 'jquery', 'yith-wcbk-dates', 'yith-wcbk-datepicker', 'yith-wcbk-monthpicker', 'yith-wcbk-people-selector' ), YITH_WCBK_VERSION, true );

			$booking_form_params = array(
				'ajaxurl'                                 => admin_url( 'admin-ajax.php' ),
				'is_admin'                                => is_admin(),
				'form_error_handling'                     => yith_wcbk()->settings->get_form_error_handling(),
				'ajax_update_non_available_dates_on_load' => get_option( 'yith-wcbk-ajax-update-non-available-dates-on-load', 'no' ),
				'i18n_empty_duration'                     => __( 'Choose a duration', 'yith-booking-for-woocommerce' ),
				'i18n_empty_date'                         => __( 'Select a date', 'yith-booking-for-woocommerce' ),
				'i18n_empty_date_for_time'                => __( 'Select a date to choose the time', 'yith-booking-for-woocommerce' ),
				'i18n_empty_time'                         => __( 'Select Time', 'yith-booking-for-woocommerce' ),
				// translators: %s is the minimum number of people.
				'i18n_min_persons'                        => __( 'Minimum people: %s', 'yith-booking-for-woocommerce' ),
				// translators: %s is the maximum number of people.
				'i18n_max_persons'                        => __( 'Maximum people: %s', 'yith-booking-for-woocommerce' ),
				// translators: %s is the minimum duration.
				'i18n_min_duration'                       => __( 'Minimum duration: %s', 'yith-booking-for-woocommerce' ),
				// translators: %s is the maximum duration.
				'i18n_max_duration'                       => __( 'Maximum duration: %s', 'yith-booking-for-woocommerce' ),
				'i18n_days'                               => array(
					'singular' => yith_wcbk_get_duration_label_string( 'day' ),
					'plural'   => yith_wcbk_get_duration_label_string( 'day', true ),
				),
				'price_first_only'                        => 'yes',
				'dom'                                     => array(
					'product_container' => '.product',
					'price'             => '.price',
				),
			);

			$booking_form_params = apply_filters( 'yith_booking_form_params', $booking_form_params );
			wp_localize_script( 'yith-wcbk-booking-form', 'yith_booking_form_params', $booking_form_params );

			wp_localize_script( 'yith-wcbk-booking-form', 'bk', $bk );
		}
	}
}
