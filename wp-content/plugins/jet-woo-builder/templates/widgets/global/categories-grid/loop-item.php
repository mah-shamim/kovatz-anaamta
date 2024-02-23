<?php
/**
 * JetWooBuilder Categories Grid widget loop item template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/categories-grid/loop-item.php.
 */


$permalink      = jet_woo_builder_tools()->get_term_permalink( $category->term_id );
$clickable_item = filter_var( $this->get_attr( 'clickable_item' ), FILTER_VALIDATE_BOOLEAN );
$clickable_data = '';
$box_classes    = [ 'jet-woo-categories__inner-box' ];
$classes        = [];

if ( $carousel_enabled ) {
	$classes[] = 'swiper-slide';
}

if ( $clickable_item ) {
	$box_classes[]  = 'jet-woo-item-overlay-wrap';
	$clickable_data = 'data-url="' . $permalink . '"';

	if ( 'yes' === $this->get_attr( 'open_new_tab' ) ) {
		$clickable_data .= ' data-target="_blank"';
	}
}
?>

<div class="jet-woo-categories__item <?php echo implode( ' ', $classes ); ?>">
	<div class="<?php echo implode( ' ', $box_classes ); ?>" <?php echo $clickable_data; ?> >
		<?php include $this->get_category_preset_template(); ?>
	</div>

	<?php if ( $clickable_item ) : ?>
		<a href="<?php echo $permalink; ?>" class="jet-woo-item-overlay-link" <?php echo $target_attr; ?> ></a>
	<?php endif; ?>
</div>