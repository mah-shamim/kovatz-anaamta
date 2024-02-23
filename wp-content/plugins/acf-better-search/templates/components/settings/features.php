<?php
/**
 * .
 *
 * @var mixed[] $features .
 *
 * @package ACF: Better Search
 */

?>

<?php
foreach ( $features as $value => $feature ) :
	$is_checked = ( isset( $config[ $value ] ) && $config[ $value ] );
	?>
	<div class="acfbsField">
		<input type="checkbox"
			name="acfbs_features[]"
			value="<?php echo esc_attr( $value ); ?>"
			id="acfbs-<?php echo esc_attr( $value ); ?>"
			class="acfbsField__input acfbsField__input--checkbox"
			<?php echo ( ! $feature['is_active'] ) ? 'disabled' : ''; ?>
			<?php echo ( $is_checked ) ? 'checked' : ''; ?>
		>
		<label for="acfbs-<?php echo esc_attr( $value ); ?>"></label>
		<span class="acfbsField__label"><?php echo wp_kses_post( $feature['label'] ); ?></span>
	</div>
<?php endforeach; ?>
