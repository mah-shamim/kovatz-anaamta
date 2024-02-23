<?php

if ( empty( $args ) ) {
	return;
}

$options             = $args['options'];
$query_var           = $args['query_var'];
$is_hierarchical     = $args['is_hierarchical'];
$classes             = array( 'jet-select__control' );
$current             = $this->get_current_filter_value( $args );
$display_options     = ! empty( $args['display_options'] ) ? $args['display_options'] : false;
$counter_prefix      = ! empty( $display_options['counter_prefix'] ) ? $display_options['counter_prefix'] : false;
$counter_suffix      = ! empty( $display_options['counter_suffix'] ) ? $display_options['counter_suffix'] : false;
$accessibility_label = $args['accessibility_label'];

?>
<div class="jet-select" <?php $this->filter_data_atts( $args ); ?>>
	<?php
	// is hierarchical
	if ( $is_hierarchical ) {
		$current = false;

		if ( ! empty( $args['current_value'] ) ) {
			$current = $args['current_value'];
		}

		$classes[] = 'depth-' . $args['depth'];

		$filter_label = $args['filter_label'];
		if ( $filter_label ) {
			$accessibility_label = $filter_label;
			include jet_smart_filters()->get_template( 'common/filter-label.php' );
		}
	}
	?>
	<?php if ( ! empty( $options ) || $is_hierarchical ) : ?>
		<select
			class="<?php echo implode( ' ', $classes ); ?>"
			name="<?php echo $query_var; ?>"
			<?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>
			aria-label="<?php echo $accessibility_label; ?>"
		>
		<?php if ( ! empty( $args['placeholder'] ) ) { ?>
			<option value=""><?php echo $args['placeholder']; ?></option>
		<?php } ?>

		<?php

		foreach ( $options as $value => $label ) {

			$selected = '';

			if ( $current ) {
				if ( is_array( $current ) && in_array( $value, $current ) ) {
					$selected = ' selected';
				}

				if ( ! is_array( $current ) && $value == $current ) {
					$selected = ' selected';
				}
			}
			?>
			<option
				value="<?php echo $value; ?>"
				data-label="<?php echo $label; ?>"
				data-counter-prefix="<?php echo $counter_prefix; ?>"
				data-counter-suffix="<?php echo $counter_suffix; ?>"
				<?php echo $selected; ?>
			><?php echo $label; ?></option>
			<?php
		}
		?></select>
	<?php endif; ?>
</div>
