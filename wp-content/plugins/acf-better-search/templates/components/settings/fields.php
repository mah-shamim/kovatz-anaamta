<?php
/**
 * .
 *
 * @var string[] $fields .
 * @var mixed[]  $config .
 *
 * @package ACF: Better Search
 */

?>

<?php
foreach ( $fields as $value => $label ) :
	$is_checked = ( isset( $config['fields_types'] ) && in_array( $value, $config['fields_types'] ) );
	?>
	<div class="acfbsField">
		<input type="checkbox"
			name="acfbs_fields_types[]"
			value="<?php echo esc_attr( $value ); ?>"
			id="acfbs-<?php echo esc_attr( $value ); ?>"
			class="acfbsField__input acfbsField__input--checkbox"
			<?php echo ( isset( $config['lite_mode'] ) && $config['lite_mode'] ) ? 'disabled' : ''; ?>
			<?php echo ( $is_checked ) ? 'checked' : ''; ?>
		>
		<label for="acfbs-<?php echo esc_attr( $value ); ?>"></label>
		<span class="acfbsField__label"><?php echo wp_kses_post( $label ); ?></span>
	</div>
<?php endforeach; ?>
