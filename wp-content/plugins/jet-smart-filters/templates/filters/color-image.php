<?php

if ( empty( $args ) ) {
	return;
}

$options             = $args['options'];
$display_options     = $args['display_options'];
$type                = $args['type'];
$filter_type         = ! empty( $args['behavior'] ) ? $args['behavior'] : 'checkbox';
$query_var           = $args['query_var'];
$scroll_height_style = $args['scroll_height'] ? 'style="max-height:' . $args['scroll_height'] . 'px"' : false;
$extra_classes       = '';
$accessibility_label = $args['accessibility_label'];

if ( ! $options ) {
	return;
}

$current = $this->get_current_filter_value( $args );

?>
<div class="jet-color-image-list" <?php $this->filter_data_atts( $args ); ?>><?php
	include jet_smart_filters()->get_template( 'common/filter-items-search.php' );

	if ( $scroll_height_style ) {echo '<div class="jet-filter-items-scroll" ' . $scroll_height_style . '><div class="jet-filter-items-scroll-container">'; }

	echo '<form class="jet-color-image-list-wrapper">';
	echo '<fieldset>';
	echo '<legend style="display:none;">' . $accessibility_label . '</legend>';
	foreach ( $options as $value => $option ) {
		$checked = '';

		if ( $current ) {
			if ( is_array( $current ) && in_array( $value, $current ) ) {
				$checked = 'checked';
			}

			if ( ! is_array( $current ) && $value == $current ) {
				$checked = 'checked';
			}
		}

		if ( '' !== $value ) {
			include jet_smart_filters()->get_template( 'filters/color-image-item.php' );
		}
	}
	echo '</fieldset>';
	echo '</form>';

	if ( $scroll_height_style ) { echo '</div></div>'; }

	include jet_smart_filters()->get_template( 'common/filter-items-moreless.php' );
?></div>
