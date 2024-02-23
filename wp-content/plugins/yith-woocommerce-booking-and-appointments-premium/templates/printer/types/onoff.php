<?php
/**
 * On-off field.
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

$enabled = 'yes' === $value;
$value   = $enabled ? 'yes' : 'no';

$classes = array(
	'yith-wcbk-printer-field__on-off',
	$enabled ? 'yith-wcbk-printer-field__on-off--enabled' : '',
	$class ?? '',
);
$class   = implode( ' ', array_filter( $classes ) );

?>
<span
		id="<?php echo esc_attr( $id ?? '' ); ?>"
		class="<?php echo esc_attr( $class ?? '' ); ?>"

	<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
	<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
>
	<input type="hidden"
			class="yith-wcbk-printer-field__on-off__value"

		<?php if ( ! ! $name ) : ?>
			name="<?php echo esc_attr( $name ); ?>"
		<?php endif; ?>

			value="<?php echo esc_attr( $value ); ?>"
	/>
	<span></span>
</span>
