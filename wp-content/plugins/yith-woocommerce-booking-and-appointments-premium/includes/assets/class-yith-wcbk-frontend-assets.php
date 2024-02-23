<?php
/**
 * Class YITH_WCBK_Frontend_Assets
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Frontend_Assets' ) ) {
	/**
	 * Class YITH_WCBK_Frontend_Assets
	 * Register and enqueue styles and scripts in Frontend
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Frontend_Assets {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Frontend_Assets constructor.
		 */
		protected function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ), 11 );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 11 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 11 );

		}

		/**
		 * Register styles
		 */
		public function register_styles() {
			wp_register_style( 'yith-wcbk-frontend-style', YITH_WCBK_ASSETS_URL . '/css/frontend/frontend.css', array( 'yith-wcbk' ), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-popup', YITH_WCBK_ASSETS_URL . '/css/frontend/popup.css', array( 'yith-wcbk' ), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-search-form', YITH_WCBK_ASSETS_URL . '/css/frontend/booking-search-form.css', array( 'yith-wcbk' ), YITH_WCBK_VERSION );

			wp_register_style( 'yith-wcbk-frontend-rtl', YITH_WCBK_ASSETS_URL . '/css/frontend/frontend-rtl.css', array(), YITH_WCBK_VERSION );
		}

		/**
		 * Register scripts
		 */
		public function register_scripts() {
			$bk = YITH_WCBK_Common_Assets::get_bk_global_params( 'frontend' );

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_register_script( 'yith-wcbk-product-form-widget', YITH_WCBK_ASSETS_URL . '/js/product-form-widget' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );
			wp_register_script( 'yith-wcbk-confirm-button', YITH_WCBK_ASSETS_URL . '/js/confirm-button' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );
			wp_register_script( 'yith-wcbk-popup', YITH_WCBK_ASSETS_URL . '/js/yith-wcbk-popup' . $suffix . '.js', array( 'jquery', 'jquery-blockui' ), YITH_WCBK_VERSION, true );
			wp_register_script( 'yith-wcbk-services-selector', YITH_WCBK_ASSETS_URL . '/js/yith-wcbk-services-selector' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );

			wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );

			wp_register_script( 'yith-wcbk-booking-search-form', YITH_WCBK_ASSETS_URL . '/js/booking-search-form' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'yith-wcbk-popup', 'select2', 'google-maps' ), YITH_WCBK_VERSION, true );
			wp_localize_script( 'yith-wcbk-booking-search-form', 'bk', $bk );
			wp_localize_script( 'yith-wcbk-popup', 'bk', $bk );

			$google_maps_key = get_option( 'yith-wcbk-google-maps-api-key', '' );
			$google_maps_key = ! ! $google_maps_key ? "&key=$google_maps_key" : '';
			wp_register_script( 'google-maps', "//maps.google.com/maps/api/js?libraries=places$google_maps_key", false, '3', true );
			wp_register_script( 'yith-wcbk-booking-map', YITH_WCBK_ASSETS_URL . '/js/booking-map' . $suffix . '.js', array( 'jquery', 'google-maps' ), YITH_WCBK_VERSION, true );
		}

		/**
		 * Enqueue styles and scripts
		 */
		public function enqueue() {
			global $wp_scripts;

			wp_enqueue_script( 'yith-wcbk-booking-form' );
			wp_enqueue_script( 'yith-wcbk-confirm-button' );
			wp_enqueue_script( 'yith-wcbk-services-selector' );

			wp_enqueue_style( 'yith-wcbk-booking-form' );
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'yith-plugin-fw-icon-font' );
			wp_enqueue_style( 'yith-wcbk-frontend-style' );
			wp_enqueue_style( 'yith-wcbk-popup' );
			wp_enqueue_style( 'yith-wcbk-search-form' );
			wp_enqueue_style( 'yith-wcbk-datepicker' );
			wp_enqueue_style( 'yith-wcbk-people-selector' );
			wp_enqueue_style( 'yith-wcbk-date-range-picker' );

			$jquery_version = $wp_scripts->registered['jquery-ui-core']->ver ?? '1.9.2';
			wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );

			if ( ! wp_style_is( 'select2', 'registered' ) ) {
				$wc_assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
				wp_register_style( 'select2', $wc_assets_path . 'css/select2.css', array(), defined( 'WC_VERSION' ) ? WC_VERSION : YITH_WCBK_VERSION );
			}

			wp_enqueue_style( 'select2' );

			// Booking form styles.
			$booking_form_styles = $this->get_booking_form_styles();
			wp_add_inline_style( 'yith-wcbk-frontend-style', $booking_form_styles );

			if ( is_rtl() ) {
				wp_enqueue_style( 'yith-wcbk-frontend-rtl' );
			}
		}

		/**
		 * Custom search form styles.
		 *
		 * @return string
		 * @deprecated 3.0.0
		 */
		public function get_search_form_styles() {
			$css = '';

			$search_forms = yith_wcbk()->search_form_helper->get_forms();
			if ( ! ! $search_forms && is_array( $search_forms ) ) {
				foreach ( $search_forms as $form_id ) {
					$search_form = yith_wcbk_get_search_form( $form_id );

					$css .= $search_form->get_css_style();
				}
			}

			return $css;
		}

		/**
		 * Custom booking form styles
		 *
		 * @return string
		 */
		public function get_booking_form_styles() {
			$css                           = '';
			$person_type_columns           = max( 1, absint( get_option( 'yith-wcbk-person-type-columns', 1 ) ) );
			$calendar_range_picker_columns = max( 1, absint( get_option( 'yith-wcbk-calendar-range-picker-columns', 1 ) ) );

			if ( ! yith_wcbk()->settings->is_people_selector_enabled() && $person_type_columns > 1 ) {
				$width_percentage = absint( 100 / $person_type_columns ) - 1;

				$css .= ".yith-wcbk-form-section.yith-wcbk-form-section-person-types {
                            width: {$width_percentage}%;
                            display: inline-block;
                        }
                ";
			}

			if ( $calendar_range_picker_columns > 1 ) {
				$css .= '.yith-wcbk-form-section.yith-wcbk-form-section-dates.calendar-day-range-picker {
                            width: calc(50% - 5px);
                            display: block;
                            float: left;
                        }
                        
                        .yith-wcbk-form-section.yith-wcbk-form-section-dates.calendar-day-range-picker + .yith-wcbk-form-section.yith-wcbk-form-section-dates.calendar-day-range-picker{
                        	margin-left: 10px;
                        }
                        
                        .yith-wcbk-form-section.yith-wcbk-form-section-dates.calendar-day-range-picker + .yith-wcbk-form-section.yith-wcbk-form-section-dates.calendar-day-range-picker .yith-wcbk-datepicker--static{
                        	right: 0;
                        }
                ';
			}

			return $css;
		}
	}
}
