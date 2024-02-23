<?php
/**
 * Result Area template
 */

$show = '';
$custom_area_styles = '';


if ( $this->preview_results() ) {
	$show = ' show';

	if ( 'custom' === $settings['results_area_width_by'] && isset( $settings['results_area_custom_width'] ) ) {
		$custom_area_width = $settings['results_area_custom_width'];

		if ( '' != $custom_area_width ) {
			$custom_area_width_style = 'width: ' . $custom_area_width . 'px;';
		}
	}

	if ( 'custom' === $settings['results_area_width_by'] && isset( $settings['results_area_custom_position'] ) ) {

		$result_area_custom_width_position = $settings['results_area_custom_position'];
		$custom_area_width_position_style  = '';

		switch( $result_area_custom_width_position ) {
			case 'left':
				$custom_area_width_position_style = 'left: 0; right: auto';
				break;
			case 'center':
				$custom_area_width_position_style = 'left: 50%; right: auto; -webkit-transform:translateX(-55%); transform:translateX(-50%);';
				break;
			case 'right':
				$custom_area_width_position_style = 'left: auto; right: 0';
				break;
		}
	}

	$custom_area_styles .= 'style="' . $custom_area_width_style . $custom_area_width_position_style . '"';
}

?>

<div class="jet-ajax-search__results-area<?php echo $show; ?>" <?php echo $custom_area_styles; ?>>
	<div class="jet-ajax-search__results-holder<?php echo $show; ?>">
		<div class="jet-ajax-search__results-header">
			<?php $this->glob_inc_if( 'results-count', array( 'show_results_counter' ) ); ?>
			<div class="jet-ajax-search__navigation-holder"><?php
				$this->preview_navigation_template( 'top' );
			?></div>
		</div>
		<div class="jet-ajax-search__results-list">
			<div class="jet-ajax-search__results-list-inner"><?php
				$this->preview_template();
			?></div>
		</div>
		<div class="jet-ajax-search__results-footer">
			<?php $this->html( 'full_results_btn_text', '<button class="jet-ajax-search__full-results">%s</button>' ); ?>
			<div class="jet-ajax-search__navigation-holder"><?php
				$this->preview_navigation_template( 'bottom' );
			?></div>
		</div>
	</div>
	<div class="jet-ajax-search__message"></div>
	<?php
		if ( ! $show ) {
			include $this->get_global_template( 'spinner' );
		}
	?>
</div>
