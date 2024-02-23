<?php
/**
 * JetWooBuilder Single Tabs widget template.
 */

echo '<div class="jet-single-tabs__wrap">';
printf( '<div class="jet-single-tabs__loading">%s</div>', __( 'Loading...', 'jet-woo-builder' ) );
woocommerce_output_product_data_tabs();
echo '</div>';
