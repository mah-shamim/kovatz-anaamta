<?php
/**
 * JetWooBuilder Products Grid widget loop item layout 6 template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-grid/presets/preset-6.php.
 */
?>

<div class="jet-woo-products__thumb-wrap">
	<?php include $this->get_template( 'item-thumb' ); ?>

	<div class="jet-woo-products__item-content">
		<div class="jet-woo-product-img-overlay"></div>

		<?php
		include $this->get_template( 'item-categories' );
		include $this->get_template( 'item-sku' );
		include $this->get_template( 'item-stock-status' );
		include $this->get_template( 'item-title' );
		include $this->get_template( 'item-price' );
		?>

		<div class="hovered-content">
			<?php
			include $this->get_template( 'item-rating' );
			include $this->get_template( 'item-content' );
			include $this->get_template( 'item-button' );
			include $this->get_template( 'item-tags' );
			?>
		</div>
	</div>
</div>

<div class="jet-woo-products-cqw-wrapper">
	<?php
	include $this->get_template( 'item-compare' );
	include $this->get_template( 'item-wishlist' );
	include $this->get_template( 'item-quick-view' );
	?>
</div>
