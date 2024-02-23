<?php
/**
 * JetWooBuilder Categories Grid widget loop item layout 2 template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/categories-grid/presets/preset-2.php.
 */

include $this->get_template( 'item-thumb' );
?>

<div class="jet-woo-categories-content">
	<?php
	include $this->get_template( 'item-title' );
	include $this->get_template( 'item-description' );
	?>

	<div class="jet-woo-category-count__wrap">
		<?php include $this->get_template( 'item-count' ); ?>
	</div>
</div>
