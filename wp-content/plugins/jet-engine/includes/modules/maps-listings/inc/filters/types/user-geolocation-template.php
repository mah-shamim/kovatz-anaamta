<div class="geolocation-data jet-search-filter" <?php $this->filter_data_atts( $args ); ?>><?php
	if ( ! empty( $_GET['context'] ) && 'edit' === $_GET['context'] ) {
		_e( 'User Geolocation filter placeholder. Won`t be displayed on the front-end.', 'jet-engine' );
	}
?></div>