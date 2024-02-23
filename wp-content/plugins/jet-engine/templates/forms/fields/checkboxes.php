<?php
/**
 * input[type="hidden"] template
 */
$required      = $this->get_required_val( $args );
$name          = $this->get_field_name( $args['name'] );
$default       = ! Jet_Engine_Tools::is_empty( $args['default'] ) ? $args['default'] : false;
$field_classes = array( 'jet-form__field', 'checkboxes-field', 'checkradio-field' );

if ( $required ) {
	$required = 'required="required"';
}

if ( ! empty( $args['field_options'] ) ) {

	if ( 1 < count( $args['field_options'] ) ) {
		$name_suffix = '[]';
	} else {
		$name_suffix = '';
	}

	if ( ! empty( $required ) ) {
		$field_classes[] = ( 1 < count( $args['field_options'] ) ) ? 'checkboxes-group-required' : 'checkboxes-required';
	}

	echo '<div class="jet-form__fields-group checkradio-wrap">';

	foreach ( $args['field_options'] as $value => $option ) {

		$checked = '';
		$calc    = '';

		if ( is_array( $option ) ) {
			$val   = isset( $option['value'] ) ? $option['value'] : $value;
			$label = isset( $option['label'] ) ? $option['label'] : $val;
		} else {
			$val   = $value;
			$label = $option;
		}

		if ( $default || '0' === $default ) {
			if ( is_array( $default ) ) {
				$checked = in_array( $val, $default ) ? 'checked' : '';
			} else {
				$checked = checked( $default, $val, false );
			}
		}

		if ( is_array( $option ) && isset( $option['calculate'] ) ) {
			$calc = ' data-calculate="' . $option['calculate'] . '"';
		}

		$custom_template = false;

		if ( ! empty( $args['custom_item_template'] ) ) {
			$custom_template = $this->get_custom_template( $val, $args, $checked );
		}

		?>
		<div class="jet-form__field-wrap checkboxes-wrap checkradio-wrap">
			<?php if ( $custom_template ) {
				echo $custom_template;
			} ?>
			<label class="jet-form__field-label">
				<input
					type="checkbox"
					name="<?php echo $name . $name_suffix; ?>"
					class="<?php echo join( ' ', $field_classes ); ?>"
					value="<?php echo $val; ?>"
					data-field-name="<?php echo $args['name']; ?>"
					<?php echo $checked; ?>
					<?php echo $required; ?>
					<?php echo $calc; ?>
				>
				<?php echo $label; ?>
			</label>
		</div>
		<?php

	}

	if ( $custom_template ) {
		wp_reset_postdata();
		wp_reset_query();
	}

	echo '</div>';

}
