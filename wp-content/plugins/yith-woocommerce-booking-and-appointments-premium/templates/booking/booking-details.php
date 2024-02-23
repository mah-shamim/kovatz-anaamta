<?php
/**
 * Booking Details Template
 *
 * Shows booking details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/booking-details.php.
 *
 * @var YITH_WCBK_Booking $booking    The booking.
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

$product       = $booking->get_product();
$order_id      = apply_filters( 'yith_wcbk_booking_details_order_id', $booking->get_order_id(), $booking );
$the_order     = ! ! $order_id ? wc_get_order( $order_id ) : false;
$service_names = $booking->get_service_names( false );
?>

<h2><?php esc_html_e( 'Booking Details', 'yith-booking-for-woocommerce' ); ?></h2>
<table class="shop_table booking_details">
	<tr>
		<th scope="row"><?php esc_html_e( 'Status', 'yith-booking-for-woocommerce' ); ?></th>
		<td><?php echo esc_html( $booking->get_status_text() ); ?></td>
	</tr>

	<?php if ( $product ) : ?>
		<?php
		$product_link  = $product->get_permalink();
		$product_title = apply_filters( 'yith_wcbk_booking_details_product_title', $product->get_title(), $booking );
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
		$order_link  = $the_order->get_view_order_url();
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

	<?php if ( $service_names ) : ?>
		<tr>
			<th scope="row"><?php echo esc_html( yith_wcbk_get_label( 'services' ) ); ?></th>
			<td><?php echo esc_html( yith_wcbk_booking_services_html( $service_names ) ); ?></td>
		</tr>
	<?php endif; ?>
</table>

<?php if ( $booking->has_persons() && $booking->has_person_types() ) : ?>
	<h3><?php echo esc_html( yith_wcbk_get_label( 'people' ) ); ?></h3>
	<table class="shop_table booking_person_types_details">
		<tbody>
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
		</tbody>
		<tfoot>
		<tr>
			<th scope="row"><?php echo esc_html( yith_wcbk_get_label( 'total-people' ) ); ?></th>
			<td><?php echo esc_html( $booking->get_persons() ); ?></td>
		</tr>
		</tfoot>
	</table>
<?php endif ?>

<?php do_action( 'yith_wcbk_booking_details_after_booking_table', $booking ); ?>
