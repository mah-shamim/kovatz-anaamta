<?php

if ( empty( $args ) ) {
	return;
}

$query_var   = $args['query_var'];
$current     = $this->get_current_filter_value( $args );

$date_format            = isset( $args['date_format'] ) ? $args['date_format'] : '';
$period_type            = isset( $args['period_type'] ) ? $args['period_type'] : 'week';
$datepicker_button_text = isset( $args['datepicker_button_text'] ) ? $args['datepicker_button_text'] : __( 'Select Date', 'jet-smart-filters' );
$period_duration        = isset( $args['period_duration'] ) ? $args['period_duration'] : '1';
$min_date_attr          = isset( $args['min_date'] ) ? 'data-mindate="' . $args['min_date'] . '"' : '';
$max_date_attr          = isset( $args['max_date'] ) ? 'data-maxdate="' . $args['max_date'] . '"' : '';
$accessibility_label    = $args['accessibility_label'];

$classes = array(
	'jet-date-period'
);

if ( '' !== $args['button_icon'] ) {
	$classes[] = 'button-icon-position-' . $args['button_icon_position'];
}

?>
<div class="<?php echo implode( ' ', $classes ) ?>" <?php $this->filter_data_atts( $args ); ?>>
	<div class="jet-date-period__wrapper">
		<div class="jet-date-period__prev" <?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>><i class="fa fa-chevron-left" aria-hidden="true"></i></div>
		<div class="jet-date-period__datepicker date">
			<div class="jet-date-period__datepicker-button input-group-addon" <?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>><?php echo $datepicker_button_text ?></div>
			<input
				class="jet-date-period__datepicker-input"
				name="<?php echo $query_var; ?>"
				value="<?php echo $current; ?>"
				aria-label="<?php echo $accessibility_label; ?>"
				type="hidden"
				tabindex="-1"
				data-format="<?php echo $date_format; ?>"
				<?php echo $min_date_attr; ?>
				<?php echo $max_date_attr; ?>
			>
		</div>
		<div class="jet-date-period__next" <?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>><i class="fa fa-chevron-right" aria-hidden="true"></i></div>
	</div>
</div>
