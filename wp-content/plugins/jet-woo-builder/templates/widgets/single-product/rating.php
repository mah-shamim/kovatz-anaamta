<?php
/**
 * JetWooBuilder Single Rating widget template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/single-product/rating.php
 */

global $product;

if ( ! is_a( $product, 'WC_Product' ) || 'no' === get_option( 'woocommerce_enable_review_rating' ) ) {
	return;
}

$settings = $this->get_settings_for_display();

$rating_icon  = $settings['rating_icon'] ?? 'jetwoo-front-icon-rating-1';
$empty_rating = $settings['show_single_empty_rating'] ?? false;
$rating       = jet_woo_builder_template_functions()->get_product_custom_rating( $rating_icon, $empty_rating );

if ( ! $rating ) {
	return;
}

$classes = [ 'woocommerce-product-rating' ];

if ( filter_var( $settings['show_single_empty_rating'], FILTER_VALIDATE_BOOLEAN ) && empty( $product->get_average_rating() ) ) {
	$classes[] = 'empty';
}

$review_count   = $product->get_review_count();
$single_caption = $settings['single_rating_reviews_link_caption_single'] ?? ' customer review';
$plural_caption = $settings['single_rating_reviews_link_caption_plural'] ?? ' customer reviews';
$before_caption = $settings['single_rating_reviews_link_before_caption'] ?? '(';
$after_caption  = $settings['single_rating_reviews_link_after_caption'] ?? ')';

$counter_html = sprintf(
	'%2$s' . _n( '<span class="count">%1$s</span>' . $single_caption, '<span class="count">%1$s</span>' . $plural_caption, $review_count, 'jet-woo-builder' ) . '%3$s',
	$review_count,
	esc_html( $before_caption ),
	esc_html( $after_caption )
);

$counter = apply_filters( 'jet-woo-builder/jet-single-rating/rating-counter-label', $counter_html, $review_count );
$url     = $settings['single_rating_reviews_link_url'] ?? '#reviews';
?>

<div class="<?php echo implode( ' ', $classes ); ?>">
	<?php echo $rating; ?>

	<?php if ( comments_open() ) : ?>
		<a href="<?php echo esc_url( $url ) ?>" class="woocommerce-review-link" rel="nofollow">
			<?php echo $counter; ?>
		</a>
	<?php endif; ?>
</div>