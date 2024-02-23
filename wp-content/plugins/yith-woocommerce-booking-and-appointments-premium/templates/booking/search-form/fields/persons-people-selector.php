<?php
/**
 * Booking Search Form Field Persons
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/persons-persons.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$person_types          = yith_wcbk()->person_type_helper->get_person_type_ids();
$searched_person_types = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( 'person_types' );
$searched_person_types = ! ! $searched_person_types && is_array( $searched_person_types ) ? $searched_person_types : array();
?>
<?php if ( $person_types && is_array( $person_types ) ) : ?>
	<div class="yith-wcbk-booking-search-form__row yith-wcbk-booking-search-form__row--persons">
		<label class="yith-wcbk-booking-search-form__row__label">
			<?php echo esc_html( yith_wcbk_get_label( 'people' ) ); ?>
		</label>
		<div class="yith-wcbk-booking-search-form__row__content">
			<div class="yith-wcbk-people-selector">
				<div class="yith-wcbk-people-selector__toggle-handler">
					<span class="yith-wcbk-people-selector__totals"></span>
				</span>
				</div>
				<div class="yith-wcbk-people-selector__fields-container">
					<?php foreach ( $person_types as $person_type_id ) : ?>
						<?php
						if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
							$person_type_id = YITH_WCBK_Wpml_Integration::get_instance( true, true )->get_language_id( $person_type_id );
						}
						$quantity = $searched_person_types[ $person_type_id ] ?? '';
						?>
						<div id="yith-wcbk-booking-persons-type-<?php echo esc_attr( $person_type_id ); ?>"
								class="yith-wcbk-people-selector__field yith-wcbk-clearfix"
								data-min="0" data-value="<?php echo esc_attr( $quantity ); ?>"
						>
							<div class="yith-wcbk-people-selector__field__title"><?php echo esc_html( get_the_title( $person_type_id ) ); ?></div>
							<div class="yith-wcbk-people-selector__field__totals">
								<span class="yith-wcbk-people-selector__field__minus">
									<span class="yith-wcbk-people-selector__field__minus-wrap">
										<?php yith_wcbk_print_svg( 'minus' ); ?>
									</span>
								</span>
								<span class="yith-wcbk-people-selector__field__total"></span>
								<span class="yith-wcbk-people-selector__field__plus">
									<span class="yith-wcbk-people-selector__field__plus-wrap">
										<?php yith_wcbk_print_svg( 'plus' ); ?>
									</span>
								</span>
							</div>

							<input type="hidden"
									name="person_types[<?php echo esc_attr( $person_type_id ); ?>]"
									class="yith-wcbk-people-selector__field__value yith-wcbk-booking-person-types"
									data-person-type-id="<?php echo esc_attr( $person_type_id ); ?>" value="<?php echo esc_attr( $quantity ); ?>"/>
						</div>
					<?php endforeach; ?>
					<div class="yith-wcbk-people-selector__fields-container__footer yith-wcbk-clearfix">
						<span class="yith-wcbk-people-selector__close-handler"><?php esc_html_e( 'Close', 'yith-booking-for-woocommerce' ); ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
