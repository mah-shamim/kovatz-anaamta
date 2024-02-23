<?php
/**
 * Checkbox list item template
 */

$checked_icon = apply_filters( 'jet-smart-filters/templates/radio/checked-icon', 'fa fa-check' );

?>
<div class="jet-radio-list__row jet-filter-row<?php echo $extra_classes; ?>">
	<label class="jet-radio-list__item" <?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>>
		<input
			type="radio"
			class="jet-radio-list__input"
			name="<?php echo $query_var; ?>"
			value="<?php echo $value; ?>"
			data-label="<?php echo $label; ?>"
			aria-label="<?php echo $label; ?>"
			<?php echo $checked; ?>
		>
		<div class="jet-radio-list__button">
			<?php if ( $show_decorator ) : ?>
				<span class="jet-radio-list__decorator"><i class="jet-radio-list__checked-icon <?php echo $checked_icon ?>"></i></span>
			<?php endif; ?>
			<span class="jet-radio-list__label"><?php echo $label; ?></span>
			<?php
				// print counter if not all option
				if ( $value !== 'all' ) {
					do_action('jet-smart-filter/templates/counter', $args );
				}
			?>
		</div>
	</label>
</div>