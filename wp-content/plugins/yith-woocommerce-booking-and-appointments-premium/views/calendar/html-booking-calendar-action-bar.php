<?php
/**
 * Booking Calendar Action Bar
 *
 * @var string $view           The view.
 * @var int    $month          The month.
 * @var int    $year           The year.
 * @var int    $date           The date for daily view.
 * @var string $time_step      The time step.
 * @var array  $url_query_args URL query args of the calendar page.
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

if ( 'day' === $view ) {
	$prev_date = gmdate( 'Y-m-d', strtotime( '-1 day', strtotime( $date ) ) );
	$next_date = gmdate( 'Y-m-d', strtotime( '+1 day', strtotime( $date ) ) );
	$prev_link = add_query_arg( array( 'date' => $prev_date ) );
	$next_link = add_query_arg( array( 'date' => $next_date ) );

	$current_date_header = yith_wcbk_date( strtotime( $date ) );
} else {
	$next_month = $month + 1;
	$next_year  = $year;
	$prev_month = $month - 1;
	$prev_year  = $year;
	if ( $next_month < 1 ) {
		$next_month = 12;
		$next_year  = $year - 1;
	}
	if ( $prev_month < 1 ) {
		$prev_month = 12;
		$prev_year  = $year - 1;
	}
	if ( $next_month > 12 ) {
		$next_month = 1;
		$next_year  = $year + 1;
	}
	$next_link = add_query_arg(
		array(
			'month' => $next_month,
			'year'  => $next_year,
		)
	);
	$prev_link = add_query_arg(
		array(
			'month' => $prev_month,
			'year'  => $prev_year,
		)
	);

	$current_date_header = date_i18n( 'F Y', strtotime( "{$year}-{$month}-01" ) );
}

$day_view_url   = 'day' !== $view ? add_query_arg( array( 'view' => 'day' ) ) : '#';
$month_view_url = 'month' !== $view ? remove_query_arg( 'time_step', add_query_arg( array( 'view' => 'month' ) ) ) : '#';
$product_id     = ! empty( $_REQUEST['product_id'] ) ? absint( $_REQUEST['product_id'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$time_steps     = YITH_WCBK_Booking_Calendar::get_time_steps();

?>
<form method="get">
	<div id="yith-wcbk-booking-calendar-action-bar">
		<div id="yith-wcbk-booking-calendar-action-bar-left">

			<?php foreach ( $url_query_args as $key => $value ) : ?>
				<input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
			<?php endforeach; ?>

			<input type="hidden" name="view" value="<?php echo esc_attr( $view ); ?>"/>

			<?php
			$select2_args = array(
				'class'            => 'yith-booking-product-search',
				'id'               => 'product_id',
				'name'             => 'product_id',
				'data-placeholder' => __( 'Filter by product...', 'yith-booking-for-woocommerce' ),
				'data-allow_clear' => true,
				'data-multiple'    => false,
				'style'            => 'width:200px',
			);
			if ( $product_id ) {
				$_product = wc_get_product( $product_id );
				// translators: %s is the product ID.
				$_product_title = ! ! $_product ? $_product->get_formatted_name() : sprintf( __( 'Deleted Product #%s', 'yith-booking-for-woocommerce' ), $product_id );

				$select2_args['value']         = $product_id;
				$select2_args['data-selected'] = array( $product_id => $_product_title );
			}
			yit_add_select2_fields( $select2_args );
			?>

			<span><?php esc_html_e( 'Filter by date', 'yith-booking-for-woocommerce' ); ?></span>
			<?php if ( 'day' === $view ) : ?>
				<span class="yith-wcbk-admin-date-picker__container">
					<input type="text" name="date" class="yith-wcbk-admin-date-picker" value="<?php echo esc_attr( $date ); ?>"/>
				</span>
			<?php else : ?>
				<select name="month">
					<?php foreach ( yith_wcbk_get_months_array() as $month_id => $month_name ) : ?>
						<option value="<?php echo esc_attr( $month_id ); ?>" <?php selected( absint( $month_id ) === $month ); ?>><?php echo esc_html( $month_name ); ?></option>
					<?php endforeach; ?>
				</select>
				<select name="year">
					<?php for ( $current_year = $year - 10; $current_year < $year + 11; $current_year ++ ) : ?>
						<option value="<?php echo esc_attr( $current_year ); ?>" <?php selected( $current_year === $year ); ?>><?php echo esc_html( $current_year ); ?></option>
					<?php endfor; ?>
				</select>
			<?php endif ?>

			<button type="submit" class="button action"><?php esc_html_e( 'Filter', 'yith-booking-for-woocommerce' ); ?></button>
		</div>

		<div id="yith-wcbk-booking-calendar-action-bar-center">
			<div class="yith-wcbk-booking-calendar-action-bar-change-view__container">
				<div class="yith-wcbk-booking-calendar-action-bar-change-view <?php echo 'month' === $view ? 'current' : ''; ?>">
					<a href="<?php echo esc_url( $month_view_url ); ?>"><?php esc_html_e( 'Month', 'yith-booking-for-woocommerce' ); ?></a>
				</div>

				<div class="yith-wcbk-booking-calendar-action-bar-change-view <?php echo 'day' === $view ? 'current' : ''; ?>">
					<a href="<?php echo esc_url( $day_view_url ); ?>"><?php esc_html_e( 'Day', 'yith-booking-for-woocommerce' ); ?></a>
				</div>
			</div>
			<?php if ( 'day' === $view ) : ?>
				<div class="yith-wcbk-booking-calendar-action-bar-change-time-step__container">
					<?php foreach ( $time_steps as $time_step_key => $time_step_label ) : ?>
						<?php
						$time_step_link = add_query_arg( array( 'time_step' => $time_step_key ) );
						?>
						<div class="yith-wcbk-booking-calendar-action-bar-time-step <?php echo $time_step === $time_step_key ? 'current' : ''; ?>">
							<a href="<?php echo esc_url( $time_step_link ); ?>"><?php echo esc_html( $time_step_label ); ?></a>
						</div>
					<?php endforeach; ?>

				</div>
			<?php endif ?>
		</div>

		<div id="yith-wcbk-booking-calendar-action-bar-right">
			<input type="text" id="yith-wcbk-booking-calendar-fast-search" placeholder="<?php esc_html_e( 'Quick search...', 'yith-booking-for-woocommerce' ); ?>"/>
		</div>
	</div>
	<div id="yith-wcbk-booking-calendar-action-date">
		<a href="<?php echo esc_url( $prev_link ); ?>">
			<span class="yith-wcbk-booking-calendar-action yith-wcbk-booking-calendar-action-prev dashicons dashicons-arrow-left-alt2"></span>
		</a>

		<div class="yith-wcbk-booking-calendar-action-date__current">
			<?php echo esc_html( $current_date_header ); ?>
		</div>

		<a href="<?php echo esc_url( $next_link ); ?>">
			<span class="yith-wcbk-booking-calendar-action yith-wcbk-booking-calendar-action-next dashicons dashicons-arrow-right-alt2"></span>
		</a>
	</div>
</form>
