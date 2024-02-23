<?php

$placeholder         = ! empty( $args['placeholder'] ) ? $args['placeholder'] : '';
$geolocation_verbose = ! empty( $args['geolocation_verbose'] ) ? $args['geolocation_verbose'] : '';
$current_location    = ! empty( $args['current_value'] ) ? htmlspecialchars( json_encode( $args['current_value'] ) ) : '';
?>
<div class="jet-search-filter jsf-location-distance" <?php $this->filter_data_atts( $args ); ?> data-current-location="<?php echo $current_location; ?>" data-geolocation-placeholder="<?php echo $geolocation_verbose; ?>">
	<div class="jsf-location-distance__location jsf-show-locate">
		<input type="text" class="jsf-location-distance__location-input" placeholder="<?php echo $placeholder; ?>">
		<div class="jsf-location-distance__location-clear jsf-location-distance__location-control">
			<svg class="jsf-location-distance__location-icon" clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 10.93 5.719-5.72c.146-.146.339-.219.531-.219.404 0 .75.324.75.749 0 .193-.073.385-.219.532l-5.72 5.719 5.719 5.719c.147.147.22.339.22.531 0 .427-.349.75-.75.75-.192 0-.385-.073-.531-.219l-5.719-5.719-5.719 5.719c-.146.146-.339.219-.531.219-.401 0-.75-.323-.75-.75 0-.192.073-.384.22-.531l5.719-5.719-5.72-5.719c-.146-.147-.219-.339-.219-.532 0-.425.346-.749.75-.749.192 0 .385.073.531.219z"/></svg>
		</div>
		<div class="jsf-location-distance__location-locate jsf-location-distance__location-control" data-geolocation-verbose="<?php echo $geolocation_verbose; ?>">
			<svg class="jsf-location-distance__location-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M24 11h-2.051c-.469-4.725-4.224-8.48-8.949-8.95v-2.05h-2v2.05c-4.725.47-8.48 4.225-8.949 8.95h-2.051v2h2.051c.469 4.725 4.224 8.48 8.949 8.95v2.05h2v-2.05c4.725-.469 8.48-4.225 8.949-8.95h2.051v-2zm-11 8.931v-3.931h-2v3.931c-3.611-.454-6.478-3.32-6.931-6.931h3.931v-2h-3.931c.453-3.611 3.32-6.477 6.931-6.931v3.931h2v-3.931c3.611.454 6.478 3.319 6.931 6.931h-3.931v2h3.931c-.453 3.611-3.32 6.477-6.931 6.931zm1-7.931c0 1.104-.896 2-2 2s-2-.896-2-2 .896-2 2-2 2 .896 2 2z"/></svg>
			<?php 
				if ( $geolocation_verbose ) {
					printf( '<div class="jsf-location-distance__tooltip">%s</div>', $geolocation_verbose );
				}
			?>
		</div>
		<div class="jsf-location-distance__location-loading jsf-location-distance__location-control">
			<svg class="jsf-location-distance__location-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 10.996c.484-5.852 5.145-10.512 10.996-10.996v2.009c-4.737.473-8.515 4.25-8.987 8.987h-2.009zm13.004-8.987c4.737.473 8.515 4.25 8.987 8.987h2.009c-.484-5.852-5.145-10.512-10.996-10.996v2.009zm-2.008 19.982c-4.737-.473-8.515-4.25-8.987-8.987h-2.009c.484 5.852 5.145 10.512 10.996 10.996v-2.009zm10.995-8.987c-.473 4.737-4.25 8.514-8.987 8.987v2.009c5.851-.484 10.512-5.144 10.996-10.996h-2.009z"/></svg>
		</div>
		<div class="jsf-location-distance__location-dropdown"></div>
	</div>
	<select class="jsf-location-distance__distance" data-default="<?php echo $args['distance_list'][0]; ?>" data-units="<?php echo $args['distance_units']; ?>"><?php
		foreach ( $args['distance_list'] as $dist ) {
			printf( '<option value="%1$s">%1$s%2$s</option>', $dist, $args['distance_units'] );
		}
	?></select>
</div>