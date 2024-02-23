<?php
/**
 * Alphabet list item template
 */

$label = strtoupper( $value );

?>
<div class="jet-alphabet-list__row jet-filter-row">
	<label class="jet-alphabet-list__item" <?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>>
		<input
			type="<?php echo $filter_type; ?>"
			class="jet-alphabet-list__input"
			name="<?php echo $query_var; ?>"
			value="<?php echo $value; ?>"
			data-label="<?php echo $label; ?>"
			aria-label="<?php echo $label; ?>"
			<?php echo $checked; ?>
		>
		<span class="jet-alphabet-list__button"><?php echo $label; ?></span>
	</label>
</div>