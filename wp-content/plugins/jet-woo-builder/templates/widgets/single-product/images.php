<?php
/**
 * JetWooBuilder Single Images widget template.
 */

$settings          = $this->get_settings_for_display();
$nav_direction     = $settings['control_nav_direction'] ?? 'horizontal';
$nav_direction     = apply_filters( 'jet-woo-builder/jet-single-image-template/navigation-direction', $nav_direction );
$vertical_position = $settings['control_nav_v_position'] ?? 'left';
$nav_position      = 'vertical' === $nav_direction ? 'jet-single-images-nav-' . $vertical_position : '';

printf( '<div class="jet-single-images__wrap jet-single-images-nav-%s %s">', $nav_direction, $nav_position );
printf( '<div class="jet-single-images__loading">%s</div>', __( 'Loading...', 'jet-woo-builder' ) );
woocommerce_show_product_images();
echo '</div>';
