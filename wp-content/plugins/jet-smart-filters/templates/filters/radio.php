<?php

if ( empty( $args ) ) {
	return;
}

$options             = $args['options'];
$query_var           = $args['query_var'];
$by_parents          = $args['by_parents'];
$scroll_height_style = $args['scroll_height'] ? 'style="max-height:' . $args['scroll_height'] . 'px"' : false;
$show_decorator      = isset( $args['display_options']['show_decorator'] ) ? filter_var( $args['display_options']['show_decorator'], FILTER_VALIDATE_BOOLEAN ) : false;
$extra_classes       = '';
$accessibility_label = $args['accessibility_label'];

if ( ! $options ) {
	return;
}

$current = $this->get_current_filter_value( $args );

?>
<div class="jet-radio-list" <?php $this->filter_data_atts( $args ); ?>><?php

	include jet_smart_filters()->get_template( 'common/filter-items-search.php' );

	if ( $scroll_height_style ) {echo '<div class="jet-filter-items-scroll" ' . $scroll_height_style . '><div class="jet-filter-items-scroll-container">'; }

	echo '<form class="jet-radio-list-wrapper">';
	echo '<fieldset>';
	echo '<legend style="display:none;">' . $accessibility_label . '</legend>';
	if ( $by_parents ) {
		if ( ! class_exists( 'Jet_Smart_Filters_Terms_Walker' ) ) {
			require_once jet_smart_filters()->plugin_path( 'includes/walkers/terms-walker.php' );
		}

		$walker = new Jet_Smart_Filters_Terms_Walker();

		$walker->tree_type = $query_var;

		$args['item_template'] = jet_smart_filters()->get_template( 'filters/radio-item.php' );
		$args['current']       = $current;
		$args['decorator']     = $show_decorator;

		echo '<div class="jet-list-tree">';
		echo $walker->walk( $options, 0, $args );
		echo '</div>';
	} else {
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

			include jet_smart_filters()->get_template( 'filters/radio-item.php' );
		}
	}
	echo '</fieldset>';
	echo '</form>';

	if ( $scroll_height_style ) { echo '</div></div>'; }

	include jet_smart_filters()->get_template( 'common/filter-items-moreless.php' );
?></div>
