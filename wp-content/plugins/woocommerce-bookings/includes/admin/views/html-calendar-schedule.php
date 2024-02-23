<div class="wrap woocommerce">
	<h2><?php esc_html_e( 'Schedule', 'woocommerce-bookings' ); ?></h2>

	<form method="get" id="mainform" enctype="multipart/form-data" class="wc_bookings_calendar_form">
		<input type="hidden" name="post_type" value="wc_booking" />
		<input type="hidden" name="page" value="booking_calendar" />
		<input type="hidden" name="view" value="<?php echo esc_attr( $view ); ?>" />
		<input type="hidden" name="tab" value="calendar" />
		<input type="hidden" name="calendar_day" value="<?php echo esc_attr( $day ); ?>" />
		<input type="hidden" name="calendar_month" value="<?php echo esc_attr( $month ); ?>" />

		<?php include 'html-calendar-nav.php'; ?>

		<ul class="wc-bookings-schedule-days">
			<?php foreach ( $this->days as $day ) : ?>
				<?php $on_today_class = current_time( 'Y-m-d' ) === $day->format( 'Y-m-d' ) ? 'wc-booking-schedule-today' : ''; ?>
				<li>
					<div class="wc-bookings-schedule-date <?php echo esc_attr( $on_today_class ); ?>">
						<div class="wc-bookings-schedule-day"><?php echo esc_html( $day->format( 'd' ) ); ?></div>
						<div class="wc-bookings-schedule-weekday"><?php echo esc_html( $day->format( 'M, D' ) ); ?></div>
					</div>
					<ul class="wc-bookings-schedule-day-events">
					<?php
					foreach ( $this->events_data as $index => $event_data ) :
						if ( ( date( 'Y-m-d', $event_data['start'] ) === $day->format( 'Y-m-d' ) && date( 'Y-m-d', $event_data['end'] ) === $day->format( 'Y-m-d' ) ) || $event_data['end'] < strtotime( $day->format( 'Y-m-d' ) ) ) {
							// Unset event to prevent unnecessary looping.
							unset( $this->events_data[ $index ] );
						}
						// Display events of the day.
						if ( date( 'Y-m-d', $event_data['start'] ) === $day->format( 'Y-m-d' ) || ( $event_data['start'] < strtotime( $day->format( 'Y-m-d' ) ) && $event_data['end'] > strtotime( $day->format( 'Y-m-d' ) ) ) ) :
						 	$description = ! empty( $event_data['customer'] ) ? '<span class="wc-bookings-schedule-customer-name">' . $event_data['customer'] . '</span>, ' . $event_data['title'] : $event_data['title'];
							?>
							<li>
								<a class="wc-bookings-schedule-event" href="<?php echo esc_url( $event_data['url'] ); ?>">
									<div class="wc-bookings-schedule-booking-duration">
										<?php echo esc_html( $event_data['time'] ); ?>
									</div>
									<div class="wc-bookings-schedule-booking-info">
										<div class="wc-bookings-schedule-booking-description">
											<?php echo wp_kses_post( $description ); ?>
										</div>
										<div class="wc-bookings-schedule-booking-details">
											<?php
											$resources = array();
											if ( ! empty( $event_data['resource'] ) ) {
												array_push( $resources, $event_data['resource'] );
											}
											if ( ! empty( $event_data['resources'] ) ) {
												echo esc_html( __( 'Resources: ', 'woocommerce-bookings' ) );
												echo esc_html( implode( ', ', $event_data['resources'] ) );
											}
											?>
										</div>
										<div class="wc-bookings-schedule-booking-details">
											<?php
											$persons   = '';
											if ( ! empty( $event_data['persons'] ) ) {
												$persons = $event_data['persons'];
											}
											if ( ! empty( $persons ) ) {
												// Persons from Booking data already contains label
												echo esc_html( $persons );
											}
											?>
										</div>
										<?php if ( ! empty( $event_data['note'] ) ) : ?>
											<div class="wc-bookings-schedule-booking-details">
												<?php echo esc_html(
													sprintf(
														/* translators: %s: Additional note added to a booking. */
														__( "Note: %s", 'woocommerce-bookings' ),
														$event_data['note']
													)
												); ?>
											</div>
										<?php endif; ?>
									</div>
								</a>
							</li>
							<?php
						endif;
					endforeach;
					?>
					</ul>
				</li>
			<?php endforeach; ?>
		</ul>
	</form>
</div>
