<?php

if ( empty( $args ) ) {
	return;
}

$options             = $args['options'];
$query_var           = $args['query_var'];
$scroll_height_style = $args['scroll_height'] ? 'style="max-height:' . $args['scroll_height'] . 'px"' : false;
$show_decorator      = isset( $args['display_options']['show_decorator'] ) ? filter_var( $args['display_options']['show_decorator'], FILTER_VALIDATE_BOOLEAN ) : false;
$checked_icon        = apply_filters( 'jet-smart-filters/templates/check-range/checked-icon', 'fa fa-check' );
$accessibility_label = $args['accessibility_label'];

if ( ! $options ) {
	return;
}

$current = $this->get_current_filter_value( $args );

?>
<div class="jet-checkboxes-list" <?php $this->filter_data_atts( $args ); ?>><?php
	include jet_smart_filters()->get_template( 'common/filter-items-search.php' );

	if ( $scroll_height_style ) {echo '<div class="jet-filter-items-scroll" ' . $scroll_height_style . '><div class="jet-filter-items-scroll-container">'; }

	echo '<fieldset class="jet-checkboxes-list-wrapper">';
	echo '<legend style="display:none;">' . $accessibility_label . '</legend>';
	foreach ( $options as $value => $label ) {

		$checked = '';

		if ( $current ) {

			if ( is_array( $current ) && in_array( $value, $current ) ) {
				$checked = 'checked';
			}

			if ( ! is_array( $current ) && $value == $current ) {
				$checked = 'checked';
			}

		}

		?>
		<div class="jet-checkboxes-list__row jet-filter-row">
			<label class="jet-checkboxes-list__item" <?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>>
				<input
					type="checkbox"
					class="jet-checkboxes-list__input"
					name="<?php echo $query_var; ?>"
					value="<?php echo $value; ?>"
					data-label="<?php echo $label; ?>"
					aria-label="<?php echo $label; ?>"
					<?php echo $checked; ?>
				>
				<div class="jet-checkboxes-list__button">
					<?php if ( $show_decorator ) : ?>
						<span class="jet-checkboxes-list__decorator"><i class="jet-checkboxes-list__checked-icon <?php echo $checked_icon ?>"></i></span>
					<?php endif; ?>
					<span class="jet-checkboxes-list__label"><?php echo $label; ?></span>
					<?php do_action('jet-smart-filter/templates/counter', $args ); ?>
				</div>
			</label>
		</div>
		<?php
	}
	echo '</fieldset>';

	if ( $scroll_height_style ) { echo '</div></div>'; }

	include jet_smart_filters()->get_template( 'common/filter-items-moreless.php' );
?></div>
