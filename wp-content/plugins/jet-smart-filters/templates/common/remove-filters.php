<?php
/**
 * Remove filters button
 */

$extra_classes = '';

if ( !$edit_mode ) {
	$extra_classes = 'hide';
}

?>
<div class="jet-remove-all-filters <?php echo $extra_classes; ?>">
	<button
		type="button"
		class="jet-remove-all-filters__button"
		data-content-provider="<?php echo $provider; ?>"
		data-additional-providers="<?php echo $additional_providers; ?>"
		data-apply-type="<?php echo $settings['apply_type']; ?>"
		data-query-id="<?php echo $query_id; ?>"
		<?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>
	>
		<?php echo $settings['remove_filters_text']; ?>
	</button>
</div>