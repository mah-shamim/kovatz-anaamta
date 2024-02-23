<?php
/**
 * Booking form dates
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/dates.php.
 *
 * @var WC_Product_Booking $product The booking product.
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.
?>
<div class="yith-wcbk-form-section-dates-wrapper yith-wcbk-form-section-wrapper">
	<?php

	do_action( 'yith_wcbk_booking_form_dates_date_fields', $product );

	do_action( 'yith_wcbk_booking_form_dates_duration', $product );

	?>
</div>
