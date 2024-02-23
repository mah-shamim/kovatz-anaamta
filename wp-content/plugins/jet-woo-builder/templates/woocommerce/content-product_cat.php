<?php
/**
 * The template for displaying product category thumbnails within loops
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/woocommerce/content-product-cat.php.
 *
 * @version 4.7.0
 */

$template = apply_filters( 'jet-woo-builder/current-template/template-id', jet_woo_builder()->woocommerce->get_custom_archive_category_template() );
$content  = jet_woo_builder()->parser->get_template_content( $template, false, $category );

jet_woo_builder()->admin_bar->register_post_item( $template );
?>

<li <?php wc_product_cat_class( ' jet-woo-builder-archive-item-' . $category->term_id, $category ); ?>>
	<?php echo apply_filters( 'jet-woo-builder/elementor-views/frontend/archive-item-content', $content, $template, $category ); ?>
</li>