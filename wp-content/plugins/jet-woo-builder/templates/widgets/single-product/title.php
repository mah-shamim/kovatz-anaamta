<?php
/**
 * JetWooBuilder Single Product Title widget template.
 */

$settings      = $this->get_settings_for_display();
$title_tooltip = '';
$full_title    = jet_woo_builder_template_functions()->get_product_title();
$title         = jet_woo_builder_tools()->trim_text(
	$full_title,
	isset( $settings['title_length'] ) ? $settings['title_length'] : 1,
	$settings['title_trim_type'],
	'...'
);


if ( -1 !== $settings['title_length'] && 'yes' === $settings['title_tooltip'] ) {
	$title_tooltip = 'title="' . $full_title . '"';
}

printf( '<h1 class="product_title entry-title" %s >%s</h1>', $title_tooltip, $title );
