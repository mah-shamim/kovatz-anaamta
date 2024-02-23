<?php
/**
 * Select field.
 *
 * @var string       $id
 * @var string       $name
 * @var string       $class
 * @var string|array $value
 * @var array        $data
 * @var array        $custom_attributes
 * @var bool         $multiple
 * @var array        $options
 *
 * @package YITH\Booking\Templates\Fields
 */

defined( 'YITH_WCBK' ) || exit;

$options  = $options ?? array();
$multiple = ! empty( $multiple );

if ( ! ! $multiple ) {
	$value = ! empty( $value ) && is_array( $value ) ? $value : array();
}
?>

<select
		id="<?php echo esc_attr( $id ?? '' ); ?>"

	<?php if ( ! ! $name ) : ?>
		name="<?php echo esc_attr( $name ); ?>"
	<?php endif; ?>

		class="<?php echo esc_attr( $class ?? '' ); ?>"

	<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
	<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>

	<?php if ( ! ! $multiple ) : ?>
		multiple
	<?php endif; ?>
>
	<?php foreach ( $options as $option_value => $option_title ) : ?>
		<?php if ( is_array( $option_title ) ) : ?>

			<optgroup label="<?php echo esc_attr( $option_title['title'] ?? '' ); ?>">
				<?php
				$sub_options = $option_title['options'] ?? array();
				$sub_options = is_array( $sub_options ) ? $sub_options : array();
				?>
				<?php foreach ( $sub_options as $sub_option_value => $sub_option_title ) : ?>
					<option
							value="<?php echo esc_attr( $sub_option_value ); ?>"
						<?php
						if ( $multiple ) {
							selected( in_array( $sub_option_value, $value ), true, true ); // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
						} else {
							selected( $sub_option_value, $value, true );
						}
						?>
					><?php echo esc_html( $sub_option_title ); ?></option>
				<?php endforeach; ?>
			</optgroup>
		<?php else : ?>
			<option
					value="<?php echo esc_attr( $option_value ); ?>"
				<?php
				if ( $multiple ) {
					selected( in_array( $option_value, $value ), true, true ); // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				} else {
					selected( $option_value, $value, true );
				}
				?>
			><?php echo esc_html( $option_title ); ?></option>
		<?php endif; ?>
	<?php endforeach; ?>
</select>
