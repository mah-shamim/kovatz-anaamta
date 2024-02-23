<?php
/**
 * Booking form persons template.
 *
 * @var WC_Product_Booking $product
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! $product->has_people() ) {
	return;
}

?>
<div class="yith-wcbk-form-section-persons-wrapper yith-wcbk-form-section-wrapper">
	<?php
	if ( ! $product->has_people_types_enabled() ) {
		wc_get_template(
			'/single-product/add-to-cart/booking-form/persons/persons.php',
			compact( 'product' ),
			'',
			YITH_WCBK_TEMPLATE_PATH
		);
	} else {
		$person_types = $product->get_enabled_people_types();

		if ( yith_wcbk()->settings->is_people_selector_enabled() ) {
			wc_get_template(
				'/single-product/add-to-cart/booking-form/persons/people-selector.php',
				compact( 'person_types', 'product' ),
				'',
				YITH_WCBK_TEMPLATE_PATH
			);
		} else {
			wc_get_template(
				'/single-product/add-to-cart/booking-form/persons/person-types.php',
				compact( 'person_types', 'product' ),
				'',
				YITH_WCBK_TEMPLATE_PATH
			);

		}
	}
	?>
</div>

