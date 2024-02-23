<?php
/**
 * Filter items moreless template
 */

if ( empty( $args ) || ! $args['less_items_count'] || $args['less_items_count'] > count( $options ) ) {
	return;
}

$less_items_count = $args['less_items_count'];
$more_text        = $args['more_text'];
$less_text        = $args['less_text'];

?>
<div class="jet-filter-items-moreless" data-less-items-count="<?php echo $less_items_count; ?>" data-more-text="<?php echo $more_text; ?>" data-less-text="<?php echo $less_text; ?>" <?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>>
	<div class="jet-filter-items-moreless__toggle"><?php echo $more_text; ?></div>
</div>