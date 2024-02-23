<?php
/**
 * Filter notes template
 */
?>
<p><b>*Query Variable</b> – <?php _e( 'you need to add the meta field name by which you want to filter the data, into the field. The Query Variable is set automatically for taxonomies, search filters and filters via the post publication date.', 'jet-smart-filters' ); ?></p>
<h5><?php _e( 'Popular plugins fields', 'jet-smart-filters' ); ?></h5>
<h5><?php _e( 'WooCommerce:', 'jet-smart-filters' ); ?></h5>
<ul><?php
	printf( '<li><b>_price</b>: %s</li>', __( 'filter by product price;', 'jet-smart-filters' ) );
	printf( '<li><b>_wc_average_rating</b>: %s</li>', __( 'filter by product rating;', 'jet-smart-filters' ) );
	printf( '<li><b>total_sales</b>: %s</li>', __( 'filter by sales count;', 'jet-smart-filters' ) );
	printf( '<li><b>_weight</b>: %s</li>', __( 'product weight;', 'jet-smart-filters' ) );
	printf( '<li><b>_length</b>: %s</li>', __( 'product length;', 'jet-smart-filters' ) );
	printf( '<li><b>_width</b>: %s</li>', __( 'product width;', 'jet-smart-filters' ) );
	printf( '<li><b>_height</b>: %s</li>', __( 'product height;', 'jet-smart-filters' ) );
	printf( '<li><b>_sale_price_dates_from</b>: %s</li>', __( 'filter by product sale start date;', 'jet-smart-filters' ) );
	printf( '<li><b>_sale_price_dates_to</b>: %s</li>', __( 'filter by product sale end date;', 'jet-smart-filters' ) );
?></ul>
<?php
	do_action( 'jet-smart-filters/post-type/filter-notes-after' );
?>