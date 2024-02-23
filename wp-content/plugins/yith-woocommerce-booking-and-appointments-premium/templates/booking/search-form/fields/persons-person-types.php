<?php
/**
 * Booking Search Form Field Person Types
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/persons-person-types.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$person_types          = yith_wcbk()->person_type_helper->get_person_type_ids();
$searched_person_types = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( 'person_types' );
$searched_person_types = ! ! $searched_person_types && is_array( $searched_person_types ) ? $searched_person_types : array();

/**
 * The "WPML" integration instance.
 *
 * @var YITH_WCBK_Wpml_Integration $wpml_integration
 */
$wpml_integration = yith_wcbk()->integrations->get_integration( 'wpml' );
?>

<?php if ( $person_types && is_array( $person_types ) ) : ?>
	<?php foreach ( $person_types as $person_type_id ) : ?>
		<?php
		if ( $wpml_integration->is_enabled() ) {
			$person_type_id = $wpml_integration->get_language_id( $person_type_id );
		}
		$quantity = $searched_person_types[ $person_type_id ] ?? '';
		?>

		<div class="yith-wcbk-booking-search-form__row yith-wcbk-booking-search-form__row--person-type yith-wcbk-booking-search-form__row--person-type-<?php echo esc_attr( $person_type_id ); ?>">
			<label class="yith-wcbk-booking-search-form__row__label">
				<?php echo esc_html( get_the_title( $person_type_id ) ); ?>
			</label>
			<div class="yith-wcbk-booking-search-form__row__content">
				<input type="number" class="yith-wcbk-booking-person-types yith-wcbk-booking-field"
						name="person_types[<?php echo esc_attr( $person_type_id ); ?>]" min="0" step="1"
						data-person-type-id="<?php echo esc_attr( $person_type_id ); ?>" value="<?php echo esc_attr( $quantity ); ?>"/>
			</div>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
