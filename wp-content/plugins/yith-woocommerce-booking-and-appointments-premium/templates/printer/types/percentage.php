<?php
/**
 * Percentage field.
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $value
 * @var array  $data
 * @var array  $custom_attributes
 *
 * @package YITH\Booking\Templates\Fields
 */

defined( 'YITH_WCBK' ) || exit;

?>
<div class="yith-wcbk-printer-percentage__container">
	<span class="yith-wcbk-printer-percentage__icon">%</span>
	<input type="number"
			id="<?php echo esc_attr( $id ?? '' ); ?>"

		<?php if ( ! ! $name ) : ?>
			name="<?php echo esc_attr( $name ); ?>"
		<?php endif; ?>

			class="<?php echo esc_attr( $class ?? '' ); ?>"
			value="<?php echo esc_attr( $value ); ?>"

		<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
		<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
	/>
</div>
