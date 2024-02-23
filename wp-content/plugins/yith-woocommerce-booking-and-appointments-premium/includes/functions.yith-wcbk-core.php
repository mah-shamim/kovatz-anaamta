<?php
/**
 * Core Functions
 *
 * @author  YITH
 * @package YITH\Booking\Functions
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_get_duration_units' ) ) {
	/**
	 * Retrieve duration units
	 *
	 * @param int $plural_control Plural flag.
	 *
	 * @return array
	 */
	function yith_wcbk_get_duration_units( $plural_control = 1 ) {
		$duration_units = array(
			'month'  => _n( 'month', 'months', $plural_control, 'yith-booking-for-woocommerce' ),
			'day'    => _n( 'day', 'days', $plural_control, 'yith-booking-for-woocommerce' ),
			'hour'   => _n( 'hour', 'hours', $plural_control, 'yith-booking-for-woocommerce' ),
			'minute' => _n( 'minute', 'minutes', $plural_control, 'yith-booking-for-woocommerce' ),
		);

		return apply_filters( 'yith_wcbk_get_duration_units', $duration_units, $plural_control );
	}
}

if ( ! function_exists( 'yith_wcbk_get_cancel_duration_units' ) ) {
	/**
	 * Get the available cancellation units for duration
	 *
	 * @return array
	 * @since 3.0.0
	 */
	function yith_wcbk_get_cancel_duration_units() {
		$duration_units = array(
			'day'   => __( 'Day(s)', 'yith-booking-for-woocommerce' ),
			'month' => __( 'Month(s)', 'yith-booking-for-woocommerce' ),
		);

		return apply_filters( 'yith_wcbk_get_cancel_duration_units', $duration_units );
	}
}

if ( ! function_exists( 'yith_wcbk_booking_admin_screen_ids' ) ) {

	/**
	 * Return booking screen ids
	 *
	 * @return array
	 */
	function yith_wcbk_booking_admin_screen_ids() {
		$screen_ids = array(
			YITH_WCBK_Post_Types::BOOKING,
			'edit-' . YITH_WCBK_Post_Types::BOOKING,
			YITH_WCBK_Post_Types::SEARCH_FORM,
			'edit-' . YITH_WCBK_Post_Types::SEARCH_FORM,
			YITH_WCBK_Post_Types::PERSON_TYPE,
			'edit-' . YITH_WCBK_Post_Types::PERSON_TYPE,
			YITH_WCBK_Post_Types::EXTRA_COST,
			'edit-' . YITH_WCBK_Post_Types::EXTRA_COST,
			'edit-' . YITH_WCBK_Post_Types::SERVICE_TAX,
			'product',
			'edit-product',
		);

		return apply_filters( 'yith_wcbk_booking_admin_screen_ids', $screen_ids );
	}
}

if ( ! function_exists( 'yith_wcbk_get_minimum_minute_increment' ) ) {
	/**
	 * Get the minimum minute increment: default 15
	 *
	 * @return string
	 * @since 2.0.5
	 */
	function yith_wcbk_get_minimum_minute_increment() {
		return apply_filters( 'yith_wcbk_get_minimum_minute_increment', 15 );
	}
}

if ( ! function_exists( 'yith_wcbk_get_max_months_to_load' ) ) {
	/**
	 * Get max month to load.
	 *
	 * @param string $unit The unit.
	 *
	 * @return mixed|void
	 */
	function yith_wcbk_get_max_months_to_load( $unit = 'day' ) {
		$months_to_load = 12;
		if ( 'hour' === $unit ) {
			$months_to_load = 3;
		} elseif ( 'minute' === $unit ) {
			$months_to_load = 1;
		}

		return apply_filters( 'yith_wcbk_get_max_months_to_load', $months_to_load, $unit );
	}
}

if ( ! function_exists( 'yith_wcbk_array_add' ) ) {
	/**
	 * Add key and value after a specific key in array
	 *
	 * @param array  $array  The array.
	 * @param string $search The key to search for.
	 * @param string $key    The key to add.
	 * @param mixed  $value  The value to add.
	 * @param bool   $after  The value to add.
	 */
	function yith_wcbk_array_add( &$array, $search, $key, $value, $after = true ) {
		$position = array_search( $search, array_keys( $array ), true );
		if ( false !== $position ) {
			$position = $after ? $position + 1 : $position;
			$first    = array_slice( $array, 0, $position, true );
			$current  = array( $key => $value );
			$last     = array_slice( $array, $position, count( $array ), true );
			$array    = array_merge( $first, $current, $last );
		} else {
			$array = array_merge( $array, array( $key => $value ) );
		}
	}
}

if ( ! function_exists( 'yith_wcbk_array_add_after' ) ) {
	/**
	 * Add key and value after a specific key in array
	 *
	 * @param array  $array  The array.
	 * @param string $search The key to search for.
	 * @param string $key    The key to add.
	 * @param mixed  $value  The value to add.
	 */
	function yith_wcbk_array_add_after( &$array, $search, $key, $value ) {
		yith_wcbk_array_add( $array, $search, $key, $value, true );
	}
}

if ( ! function_exists( 'yith_wcbk_array_add_before' ) ) {
	/**
	 * Add key and value after a specific key in array.
	 *
	 * @param array  $array  The array.
	 * @param string $search The key to search for.
	 * @param string $key    The key to add.
	 * @param mixed  $value  The value to add.
	 */
	function yith_wcbk_array_add_before( &$array, $search, $key, $value ) {
		yith_wcbk_array_add( $array, $search, $key, $value, false );
	}
}

if ( ! function_exists( 'yith_wcbk_booking_person_types_to_list' ) ) {
	/**
	 * Transform person types array to list.
	 *
	 * @param array $person_types Person types array.
	 *
	 * @return array
	 */
	function yith_wcbk_booking_person_types_to_list( $person_types ) {
		if ( $person_types && is_array( $person_types ) ) {
			$new_person_types = array();
			$is_a_list        = is_array( current( $person_types ) );

			if ( ! $is_a_list ) {
				foreach ( $person_types as $person_type_id => $person_type_number ) {
					$person_type_title  = get_the_title( $person_type_id );
					$new_person_types[] = array(
						'id'     => $person_type_id,
						'title'  => $person_type_title,
						'number' => $person_type_number,
					);
				}
			} else {
				$new_person_types = $person_types;
			}

			return $new_person_types;
		}

		return array();
	}
}

if ( ! function_exists( 'yith_wcbk_booking_person_types_to_id_number_array' ) ) {
	/**
	 * Transform person types to id-number array.
	 *
	 * @param array $person_types Person types array.
	 *
	 * @return array
	 */
	function yith_wcbk_booking_person_types_to_id_number_array( $person_types ) {
		if ( $person_types && is_array( $person_types ) ) {
			$new_person_types      = array();
			$is_an_id_number_array = ! is_array( current( $person_types ) );

			if ( ! $is_an_id_number_array ) {
				foreach ( $person_types as $person_type ) {
					$new_person_types[ $person_type['id'] ] = $person_type['number'];
				}
			} else {
				$new_person_types = $person_types;
			}

			return $new_person_types;
		}

		return array();
	}
}


if ( ! function_exists( 'yith_wcbk_get_person_type_title' ) ) {
	/**
	 * Get person type title.
	 *
	 * @param int $person_type_id Person type ID.
	 *
	 * @return string
	 */
	function yith_wcbk_get_person_type_title( $person_type_id ) {
		return yith_wcbk()->person_type_helper->get_person_type_title( $person_type_id );
	}
}

/**
 * Conditionals
 * --------------------------------------------------
 */
if ( ! function_exists( 'yith_wcbk_is_debug' ) ) {
	/**
	 * Return true if debug is active
	 *
	 * @return bool
	 */
	function yith_wcbk_is_debug() {
		return 'yes' === get_option( 'yith-wcbk-debug', 'no' );
	}
}

if ( ! function_exists( 'yith_wcbk_is_in_search_form_result' ) ) {
	/**
	 * Return true if we're in search form results.
	 *
	 * @return bool
	 */
	function yith_wcbk_is_in_search_form_result() {
		return defined( 'YITH_WCBK_IS_IN_AJAX_SEARCH_FORM_RESULTS' ) && YITH_WCBK_IS_IN_AJAX_SEARCH_FORM_RESULTS;
	}
}


/**
 * Print fields and templates functions
 * --------------------------------------------------
 */
if ( ! function_exists( 'yith_wcbk_print_field' ) ) {
	/**
	 * Print field.
	 *
	 * @param array $args Arguments.
	 * @param bool  $echo Echo flag.
	 *
	 * @return false|string
	 */
	function yith_wcbk_print_field( $args = array(), $echo = true ) {
		if ( ! $echo ) {
			ob_start();
		}

		yith_wcbk_printer()->print_field( $args );

		if ( ! $echo ) {
			return ob_get_clean();
		}

		return '';
	}
}

if ( ! function_exists( 'yith_wcbk_print_svg' ) ) {
	/**
	 * Print an svg.
	 *
	 * @param string $svg  The SVG name.
	 * @param bool   $echo Echo flag.
	 *
	 * @return false|string
	 */
	function yith_wcbk_print_svg( $svg, $echo = true ) {
		return yith_wcbk_print_field(
			array(
				'type' => 'svg',
				'svg'  => $svg,
			),
			$echo
		);
	}
}

if ( ! function_exists( 'yith_wcbk_print_fields' ) ) {
	/**
	 * Print fields
	 *
	 * @param array $fields Fields.
	 */
	function yith_wcbk_print_fields( $fields = array() ) {
		yith_wcbk_printer()->print_fields( $fields );
	}
}

if ( ! function_exists( 'yith_wcbk_print_notice' ) ) {
	/**
	 * Print notice
	 *
	 * @param string $notice      The notice.
	 * @param string $type        Type.
	 * @param false  $dismissible Dismissible flag.
	 * @param string $key         The key.
	 */
	function yith_wcbk_print_notice( $notice, $type = 'info', $dismissible = false, $key = '' ) {
		$class = "yith-wcbk-admin-notice notice notice-{$type}";

		$class .= ! ! $dismissible ? ' is-dismissible' : '';

		if ( ! $key ) {
			$key = md5( $notice . '_' . $type );
		}
		$key    = sanitize_key( $key );
		$cookie = 'yith_wcbk_notice_dismiss_' . $key;
		$id     = 'yith-wcbk-notice-' . $key;

		if ( $dismissible && ! empty( $_COOKIE[ $cookie ] ) ) {
			return;
		}

		echo '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '"><p>' . wp_kses_post( $notice ) . '</p></div>';

		if ( $dismissible ) {
			?>
			<script>
				jQuery( '#<?php echo esc_attr( $id ); ?>' ).on( 'click', '.notice-dismiss', function () {
					document.cookie = "<?php echo esc_attr( $cookie ); ?>=1";
				} );
			</script>
			<?php
		}
	}
}

if ( ! function_exists( 'yith_wcbk_get_view' ) ) {
	/**
	 * Print a view
	 *
	 * @param string $view The view.
	 * @param array  $args Arguments.
	 */
	function yith_wcbk_get_view( $view, $args = array() ) {
		$view_path = trailingslashit( YITH_WCBK_VIEWS_PATH ) . $view;
		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		if ( file_exists( $view_path ) ) {
			include $view_path;
		}
	}
}

if ( ! function_exists( 'yith_wcbk_print_login_form' ) ) {
	/**
	 * Print the WooCommerce login form.
	 *
	 * @param bool $check_logged_in           Check logged-in flag.
	 * @param bool $add_woocommerce_container Add WooCommerce container flag.
	 *
	 * @since 1.0.5
	 */
	function yith_wcbk_print_login_form( $check_logged_in = false, $add_woocommerce_container = true ) {
		if ( ! $check_logged_in || ! is_user_logged_in() ) {
			echo ! ! $add_woocommerce_container ? '<div class="woocommerce">' : '';
			wc_get_template( 'myaccount/form-login.php' );
			echo ! ! $add_woocommerce_container ? '</div>' : '';
		}
	}
}

if ( ! function_exists( 'yith_wcbk_create_date_field' ) ) {
	/**
	 * Create date field with time.
	 *
	 * @param string $unit The unit.
	 * @param array  $args The arguments.
	 * @param bool   $echo Set true to print the field directly.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	function yith_wcbk_create_date_field( $unit, $args = array(), $echo = false ) {
		$value = $args['value'] ?? '';
		$id    = $args['id'] ?? '';
		$name  = $args['name'] ?? '';
		$admin = ! ! ( $args['admin'] ?? true );

		$datepicker_class = $admin ? 'yith-wcbk-admin-date-picker' : 'yith-wcbk-date-picker';

		if ( ! in_array( $unit, array( 'hour', 'minute' ), true ) ) {
			$current_value = date_i18n( 'Y-m-d', $value );
			$field         = '<input type="text" class="' . esc_attr( $datepicker_class ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" maxlength="10" value="' . esc_attr( $current_value ) . '" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])"/>';
		} else {
			$current_value = date_i18n( 'Y-m-d H:i', $value );
			$date_value    = date_i18n( 'Y-m-d', $value );
			$time_value    = date_i18n( 'H:i', $value );

			$time_field = yith_wcbk_print_field(
				array(
					'id'    => "$id-time",
					'type'  => 'time-select',
					'value' => $time_value,
				),
				false
			);
			$field      = '<input type="hidden" class="yith-wcbk-date-time-field" name="' . esc_attr( $name ) . '" data-date="#' . esc_attr( $id ) . '-date" data-time="#' . esc_attr( $id ) . '-time" value="' . esc_attr( $current_value ) . '" />';

			$field .= '<input type="text" class="' . esc_attr( $datepicker_class ) . '" id="' . esc_attr( $id ) . '-date"  maxlength="10" value="' . esc_attr( $date_value ) . '" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])"/>';
			$field .= "<span class='yith-wcbk-date-time-field-time'>{$time_field}</span>";
		}

		if ( $echo ) {
			echo $field; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		return $field;
	}
}

if ( ! function_exists( 'yith_wcbk_get_order_awaiting_payment' ) ) {
	/**
	 * Get the id of the order awaiting payment.
	 *
	 * @return int
	 */
	function yith_wcbk_get_order_awaiting_payment() {
		$cart = WC()->cart->get_cart_for_session();
		if ( $cart ) {
			$order_id  = absint( WC()->session->get( 'order_awaiting_payment' ) );
			$cart_hash = WC()->cart->get_cart_hash();
			$order     = $order_id ? wc_get_order( $order_id ) : null;

			$resuming_order = $order && $order->has_cart_hash( $cart_hash ) && $order->has_status( array( 'pending', 'failed' ) );

			if ( $resuming_order ) {
				return $order_id;
			}
		}

		return 0;
	}
}

if ( ! function_exists( 'yith_wcbk_admin_order_info_html' ) ) {
	/**
	 * Retrieve the admin order info html
	 *
	 * @param YITH_WCBK_Booking $booking The booking.
	 * @param array             $args    Array of arguments.
	 * @param bool              $echo    Set to true to print directly.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_admin_order_info_html( $booking, $args = array(), $echo = true ) {
		$html     = '';
		$order_id = $booking->get_order_id();

		$defaults = array(
			'show_email'  => true,
			'show_status' => true,
		);
		$args     = wp_parse_args( $args, $defaults );

		if ( $order_id ) {
			$order = $booking->get_order();
			if ( $order ) {
				$username_format   = '%1$s %2$s';
				$the_order_user_id = $order->get_user_id();
				$user_info         = ! empty( $the_order_user_id ) ? get_userdata( $the_order_user_id ) : false;

				if ( ! ! $user_info ) {
					$username = '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';

					if ( $user_info->first_name || $user_info->last_name ) {
						$username .= esc_html( sprintf( $username_format, ucfirst( $user_info->first_name ), ucfirst( $user_info->last_name ) ) );
					} else {
						$username .= esc_html( ucfirst( $user_info->display_name ) );
					}

					$username .= '</a>';
				} else {
					if ( $order->get_billing_first_name() || $order->get_billing_last_name() ) {
						$username = trim( sprintf( $username_format, $order->get_billing_first_name(), $order->get_billing_last_name() ) );
					} else {
						$username = __( 'Guest', 'yith-booking-for-woocommerce' );
					}
				}

				// translators: 1. order number with link; 2. user name.
				$html .= sprintf( _x( '%1$s by %2$s', 'Order number by X', 'yith-booking-for-woocommerce' ), '<a href="' . admin_url( 'post.php?post=' . absint( $order_id ) . '&action=edit' ) . '" class="row-title"><strong>#' . esc_attr( $order->get_order_number() ) . '</strong></a>', $username );

				if ( $args['show_email'] && $order->get_billing_email() ) {
					$html .= '<small class="meta email"><a href="' . esc_url( 'mailto:' . $order->get_billing_email() ) . '">' . esc_html( $order->get_billing_email() ) . '</a></small>';
				}

				if ( $args['show_status'] ) {
					$html .= sprintf(
						'<mark class="order-status %1$s"><span>%2$s</span></mark>',
						esc_attr( sanitize_html_class( 'status-' . $order->get_status() ) ),
						esc_html( wc_get_order_status_name( $order->get_status() ) )
					);
				}
			} else {
				// translators: %s is the order ID.
				$html .= sprintf( _x( '#%s (deleted)', 'Deleted Order:#123 (deleted)', 'yith-booking-for-woocommerce' ), $order_id );
			}
		} else {
			$html .= '&ndash;';
		}

		if ( $echo ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		return $html;
	}
}

if ( ! function_exists( 'yith_wcbk_get_user_name' ) ) {
	/**
	 * Retrieve the user name to display.
	 *
	 * @param WP_User $user The user.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_get_user_name( $user ) {
		$name = '';
		if ( $user ) {
			if ( $user->first_name || $user->last_name ) {
				$name = sprintf( '%1$s %2$s', ucfirst( $user->first_name ), ucfirst( $user->last_name ) );
			} else {
				$name = ucfirst( $user->display_name );
			}
		}

		return $name;
	}
}

if ( ! function_exists( 'yith_wcbk_admin_user_info_html' ) ) {
	/**
	 * Retrieve the user order info html
	 *
	 * @param YITH_WCBK_Booking $booking The booking.
	 * @param bool              $echo    Set to true to print directly.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_admin_user_info_html( $booking, $echo = true ) {
		$html = '';

		$user = $booking->get_user();
		if ( $user ) {
			$html = '<a href="user-edit.php?user_id=' . absint( $user->ID ) . '">';

			$html .= yith_wcbk_get_user_name( $user );
			$html .= '</a>';
			$html .= '<small class="meta email"><a href="' . esc_url( 'mailto:' . $user->user_email ) . '">' . esc_html( $user->user_email ) . '</a></small>';
		} else {
			$html = '&ndash;';
		}

		if ( $echo ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		return $html;
	}
}

if ( ! function_exists( 'yith_wcbk_get_current_screen_id' ) ) {
	/**
	 * Retrieve the current screen ID.
	 *
	 * @return string|false
	 * @since 3.0.0
	 */
	function yith_wcbk_get_current_screen_id() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;

		return ! ! $screen && is_a( $screen, 'WP_Screen' ) ? $screen->id : false;
	}
}

if ( ! function_exists( 'yith_wcbk_current_screen_is' ) ) {
	/**
	 * Return true if current screen is one of the $ids.
	 *
	 * @param string|string[] $ids The screen ID(s).
	 *
	 * @return bool
	 * @since 3.0.0
	 */
	function yith_wcbk_current_screen_is( $ids ) {
		$ids       = (array) $ids;
		$screen_id = yith_wcbk_get_current_screen_id();

		return $screen_id && in_array( $screen_id, $ids, true );
	}
}


if ( ! function_exists( 'yith_wcbk_get_admin_calendar_url' ) ) {

	/**
	 * Get the calendar URL
	 *
	 * @param int|false $product_id The product ID. Set to false if you want to retrieve the general calendar URL.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_get_admin_calendar_url( $product_id ) {
		return YITH_WCBK_Booking_Calendar::get_url( $product_id );
	}
}

add_action( 'yith_wcbk_run_callback', 'yith_wcbk_run_callback', 10, 2 );

/**
 * Run callback.
 *
 * @param callable $callback The callback.
 * @param array    $args     Arguments.
 *
 * @since 3.0.0
 */
function yith_wcbk_run_callback( $callback, $args = array() ) {
	if ( is_callable( $callback ) ) {
		if ( ! ! $args ) {
			call_user_func_array( $callback, $args );
		} else {
			call_user_func( $callback );
		}
	}
}

if ( ! function_exists( 'yith_wcbk_number' ) ) {
	/**
	 * Format a number.
	 *
	 * @param int|float|string $number The number.
	 * @param array            $args   Arguments.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_number( $number, array $args = array() ): string {
		$args = apply_filters(
			'yith_wcbk_number_args',
			wp_parse_args(
				$args,
				array(
					'decimal_separator'  => wc_get_price_decimal_separator(),
					'thousand_separator' => '',
					'decimals'           => 2,
				)
			)
		);

		// Convert to float to avoid issues on PHP 8.
		$number   = (float) $number;
		$negative = $number < 0;

		$number = $negative ? $number * - 1 : $number;
		$number = number_format( $number, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );

		if ( apply_filters( 'yith_wcbk_number_trim_zeros', true ) && $args['decimal_separator'] && $args['decimals'] > 0 ) {
			$number = preg_replace( '/' . preg_quote( $args['decimal_separator'], '/' ) . '0++$/', '', $number );
			$number = preg_replace( '/(' . preg_quote( $args['decimal_separator'], '/' ) . '[0-9]+)(0++)$/', '$1', $number );
		}

		$formatted_number = ( $negative ? '-' : '' ) . $number;

		return (string) apply_filters( 'yith_wcbk_number', $formatted_number, $number, $args );
	}
}

if ( ! function_exists( 'yith_wcbk_css' ) ) {
	/**
	 * Format a number.
	 *
	 * @param array $styles Styles arguments.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_css( array $styles ): string {
		$styles = array_map(
			function ( $style ) {
				$selector        = ! ! $style['selector'] ? $style['selector'] : '';
				$parents         = $style['parents'] ?? array();
				$styles          = $style['styles'] ?? array();
				$important       = $style['important'] ?? false;
				$maybe_important = ! ! $important ? ' !important' : '';

				if ( $parents ) {
					$selector = implode(
						', ',
						array_map(
							function ( $parent ) use ( $selector ) {
								return esc_attr( implode( ' ', array_filter( array( $parent, $selector ) ) ) );
							},
							$parents
						)
					);
				}

				$css = $selector . '{';

				$css_styles = array();

				foreach ( $styles as $prop => $value ) {
					$css_styles[] = esc_attr( $prop ) . ': ' . esc_attr( $value ) . $maybe_important;
				}

				$css .= implode( '; ', $css_styles );
				$css .= '}';

				return $css;
			},
			$styles
		);

		return implode( ' ', $styles );
	}
}

if ( ! function_exists( 'yith_wcbk_get_default_colors' ) ) {
	/**
	 * Get default colors.
	 *
	 * @return array
	 * @since 3.0.0
	 */
	function yith_wcbk_get_default_colors(): array {
		$defaults = array(
			'primary'            => '#00a7b7',
			'primary-light'      => '#00cbe0',
			'primary-contrast'   => '#ffffff',
			'border-color'       => '#d1d1d1',
			'border-color-focus' => '#a7d9ec',
			'shadow-color-focus' => 'rgba(167, 217, 236, .35)',
			'underlined-bg'      => '#e8eff1',
			'underlined-text'    => '#4e8ba2',
		);

		$colors = (array) apply_filters( 'yith_wcbk_default_colors', $defaults );

		// Default colors are mandatory.
		$colors = wp_parse_args( $colors, $defaults );

		return $colors;
	}
}

if ( ! function_exists( 'yith_wcbk_get_colors' ) ) {
	/**
	 * Get colors.
	 *
	 * @return array
	 * @since 3.0.0
	 */
	function yith_wcbk_get_colors(): array {
		$colors = get_option( 'yith-wcbk-colors', array() );
		$colors = ! ! $colors && is_array( $colors ) ? $colors : array();

		return wp_parse_args( $colors, yith_wcbk_get_default_colors() );
	}
}
