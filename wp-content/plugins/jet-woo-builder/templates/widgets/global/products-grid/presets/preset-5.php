<?php
/**
 * JetWooBuilder Products Grid widget loop item layout 5 template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-grid/presets/preset-5.php.
 */
?>

<div class="jet-woo-products__thumb-wrap">
	<?php include $this->get_template( 'item-thumb' ); ?>

	<div class="jet-woo-products__item-content hovered-content">
		<div class="jet-woo-products-cqw-wrapper">
			<?php
			include $this->get_template( 'item-compare' );
			include $this->get_template( 'item-wishlist' );
			include $this->get_template( 'item-quick-view' );
			?>
		</div>

		<?php
		include $this->get_template( 'item-categories' );
		include $this->get_template( 'item-title' );
		include $this->get_template( 'item-sku' );
		include $this->get_template( 'item-stock-status' );
		include $this->get_template( 'item-price' );
		include $this->get_template( 'item-content' );
		include $this->get_template( 'item-button' );
		include $this->get_template( 'item-rating' );
		include $this->get_template( 'item-tags' );
		?>
	</div>
</div>