<?php

if ( empty( $args ) ) {
	return;
}

$options             = $args['options'];
$widget_id           = $args['__widget_id'];
$query_var           = $args['query_var'];
$accessibility_label = $args['accessibility_label'];

if ( ! $options ) {
	return;
}

$current = $this->get_current_filter_value( $args );

?>
<div class="jet-rating" <?php $this->filter_data_atts( $args ); ?>>
	<div class="jet-rating__control">
		<div class="jet-rating-stars">
			<fieldset class="jet-rating-stars__fields">
			<legend style="display:none;"><?php echo $accessibility_label; ?></legend>
		<?php

		$options = array_reverse( $options );

		foreach ( $options as $key => $value ) {

			$checked = '';

			if ( $current ) {
				if ( is_array( $current ) && in_array( $value, $current ) ) {
					$checked = ' checked';
				}

				if ( ! is_array( $current ) && $value == $current ) {
					$checked = ' checked';
				}
			}

			?>
			<input
				class="jet-rating-star__input<?php echo $checked ? ' is-checked' : '' ?>"
				type="radio"
				id="jet-rating-<?php echo $widget_id . '-' . $value ?>"
				name="<?php echo $query_var; ?>"

				value="<?php echo $value; ?>"
				aria-label="<?php echo $value; ?>"
				<?php echo $checked; ?>
			/>
			<label class="jet-rating-star__label" for="jet-rating-<?php echo $widget_id . '-' . $value ?>" <?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>><span class="jet-rating-star__icon"><?php echo $args['rating_icon']; ?></span></label>
		<?php } ?>
			</fieldset>
		</div>
	</div>
</div>
