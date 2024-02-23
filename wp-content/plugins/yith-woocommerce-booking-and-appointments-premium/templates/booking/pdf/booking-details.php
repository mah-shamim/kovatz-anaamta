<?php
/**
 * Booking details PDF Template
 *
 * @var YITH_WCBK_Booking $booking  The booking.
 * @var bool              $is_admin Is admin flag.
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$product   = $booking->get_product();
$order_id  = apply_filters( 'yith_wcbk_pdf_booking_details_order_id', $booking->get_order_id(), $booking, $is_admin );
$the_order = ! ! $order_id ? wc_get_order( $order_id ) : false;
$services  = $booking->get_service_names( $is_admin );
?>
<table class="booking-table">
	<tr>
		<th scope="row"><?php esc_html_e( 'Status', 'yith-booking-for-woocommerce' ); ?></th>
		<td><?php echo esc_html( $booking->get_status_text() ); ?></td>
	</tr>
	<?php if ( $product ) : ?>
		<?php
		$product_link  = $is_admin ? get_edit_post_link( $product->get_id() ) : $product->get_permalink();
		$product_title = $product->get_title();
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Product', 'yith-booking-for-woocommerce' ); ?></th>
			<td>
				<a href="<?php echo esc_url( $product_link ); ?>"><?php echo esc_html( $product_title ); ?></a>
			</td>
		</tr>
	<?php endif ?>

	<?php if ( $the_order ) : ?>
		<?php
		$order_link  = $is_admin ? get_edit_post_link( $order_id ) : $the_order->get_view_order_url();
		$order_title = _x( '#', 'hash before order number', 'woocommerce' ) . $the_order->get_order_number();
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Order', 'yith-booking-for-woocommerce' ); ?></th>
			<td>
				<a href="<?php echo esc_url( $order_link ); ?>"><?php echo esc_html( $order_title ); ?></a>
			</td>
		</tr>
	<?php endif ?>

	<tr>
		<th scope="row"><?php echo esc_html( yith_wcbk_get_label( 'duration' ) ); ?></th>
		<td><?php echo esc_html( $booking->get_duration_html() ); ?></td>
	</tr>
	<tr>
		<th scope="row"><?php echo esc_html( yith_wcbk_get_label( 'from' ) ); ?></th>
		<td><?php echo esc_html( $booking->get_formatted_from() ); ?></td>
	</tr>
	<tr>
		<th scope="row"><?php echo esc_html( yith_wcbk_get_label( 'to' ) ); ?></th>
		<td><?php echo esc_html( $booking->get_formatted_to() ); ?></td>
	</tr>
	<?php if ( $booking->has_persons() && ! $booking->has_person_types() ) : ?>
		<tr>
			<th scope="row"><?php echo esc_html( yith_wcbk_get_label( 'people' ) ); ?></th>
			<td><?php echo esc_html( $booking->get_persons() ); ?></td>
		</tr>
	<?php endif; ?>

	<?php if ( $services ) : ?>
		<tr>
			<th scope="row"><?php echo esc_html( yith_wcbk_get_label( 'services' ) ); ?></th>
			<td><?php echo esc_html( yith_wcbk_booking_services_html( $services ) ); ?></td>
		</tr>
	<?php endif; ?>
</table>

<?php if ( $booking->has_persons() && $booking->has_person_types() ) : ?>
	<h3><?php echo esc_html( yith_wcbk_get_label( 'people' ) ); ?></h3>
	<table class="booking-table booking_person_types_details">
		<?php foreach ( $booking->get_person_types() as $person_type ) : ?>
			<?php
			if ( ! $person_type['number'] ) {
				continue;
			}
			$person_type_id     = absint( $person_type['id'] );
			$person_type_title  = yith_wcbk()->person_type_helper->get_person_type_title( $person_type_id );
			$person_type_title  = ! ! $person_type_title ? $person_type_title : $person_type['title'];
			$person_type_number = absint( $person_type['number'] );
			?>
			<tr>
				<th scope="row"><?php echo esc_html( $person_type_title ); ?></th>
				<td><?php echo esc_html( $person_type_number ); ?></td>
			</tr>
		<?php endforeach; ?>
		<tr class="person-tot">
			<th scope="row"><?php echo esc_html( yith_wcbk_get_label( 'total-people' ) ); ?></th>
			<td><?php echo esc_html( $booking->get_persons() ); ?></td>
		</tr>
	</table>
<?php endif ?>
