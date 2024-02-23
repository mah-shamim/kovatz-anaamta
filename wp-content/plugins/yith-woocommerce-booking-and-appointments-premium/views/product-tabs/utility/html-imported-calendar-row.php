<?php
/**
 * Imported calendar row.
 *
 * @var int    $index
 * @var string $name
 * @var string $url
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;
?>
<tr>
	<td>
		<input type="text" name="_yith_booking_external_calendars[<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $name ); ?>"/>
	</td>
	<td>
		<input type="text" name="_yith_booking_external_calendars[<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $url ); ?>"/>
	</td>
	<td class="yith-wcbk-product-sync-imported-calendars-table__delete-column"><span class="yith-wcbk-icon-trash delete"></span></td>
</tr>
