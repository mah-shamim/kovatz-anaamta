<?php
/**
 * Filter label template
 */

if ( ! ( ( isset( $show_label ) && $show_label ) || ( isset( $filter_label ) && $filter_label ) ) ) {
	return;
}

if ( isset( $filter_label ) ) {
	$label = $filter_label;
	$filter_label = null;
} else {
	$label = get_post_meta( $filter_id, '_filter_label', true );
}

if ( empty( $label ) ) {
	return;
}

?>
<div class="jet-filter-label"><?php echo $label; ?></div>
