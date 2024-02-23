<?php
/**
 * JetWooBuilder Categories Grid widget loop item layout 5 template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/categories-grid/presets/preset-5.php.
 */
?>

<div class="jet-woo-categories-thumbnail__wrap">
	<?php include $this->get_template( 'item-thumb' ); ?>
</div>

<div class="jet-woo-categories-content">
	<div class="jet-woo-category-content__inner">
		<?php
		include $this->get_template( 'item-title' );
		include $this->get_template( 'item-description' );
		?>
	</div>

	<div class="jet-woo-category-count__wrap">
		<?php include $this->get_template( 'item-count' ); ?>
	</div>
</div>