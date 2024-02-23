<?php
/**
 * Booking Notes Meta-box
 *
 * @var YITH_WCBK_Booking $booking
 * @var array             $notes
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

?>
<ul class="booking-notes">
	<?php if ( ! ! $notes && is_array( $notes ) ) : ?>
		<?php foreach ( $notes as $note ) : ?>
			<?php
			$note_classes   = 'note ' . $note->type;
			$note_timestamp = strtotime( $note->note_date ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
			?>
			<li rel="<?php echo absint( $note->id ); ?>" class="<?php echo esc_attr( $note_classes ); ?>">
				<div class="note_content">
					<?php echo wp_kses_post( wpautop( wptexturize( $note->description ) ) ); ?>
				</div>
				<p class="meta">
					<abbr class="exact-date" title="<?php echo esc_attr( gmdate( 'Y-m-d h:i:s', $note_timestamp ) ); ?>">
						<?php
						// translators: 1. date; 2. time.
						echo esc_html( sprintf( __( 'added on %1$s at %2$s', 'yith-booking-for-woocommerce' ), date_i18n( wc_date_format(), $note_timestamp ), date_i18n( wc_time_format(), $note_timestamp ) ) );
						?>
					</abbr>
					<a href="#" class="delete-booking-note"><?php esc_html_e( 'Delete note', 'yith-booking-for-woocommerce' ); ?></a>
				</p>
			</li>
		<?php endforeach; ?>
	<?php else : ?>
		<li> <?php esc_html_e( 'There are no notes.', 'yith-booking-for-woocommerce' ); ?></li>
	<?php endif; ?>
</ul>
<div class="add-booking-note__container">
	<p>
		<label for="booking-note"><?php esc_html_e( 'Add note', 'yith-booking-for-woocommerce' ); ?><?php echo wc_help_tip( __( 'Add a note for your reference, or add a customer note (the user will be notified).', 'yith-booking-for-woocommerce' ) ); ?></label>
		<textarea type="text" name="booking_note" id="booking-note" class="input-text" cols="20" rows="5"></textarea>
	</p>
	<p>
		<label for="booking-note-type" class="screen-reader-text"><?php esc_html_e( 'Note type', 'yith-booking-for-woocommerce' ); ?></label>
		<select name="booking_note_type" id="booking-note-type">
			<option value="admin"><?php esc_html_e( 'Private note', 'yith-booking-for-woocommerce' ); ?></option>
			<option value="customer"><?php esc_html_e( 'Note to customer', 'yith-booking-for-woocommerce' ); ?></option>
		</select>
		<button type="button" class="add-booking-note yith-wcbk-admin-button yith-wcbk-admin-button--small"><?php esc_html_e( 'Add', 'yith-booking-for-woocommerce' ); ?></button>
	</p>
</div>
