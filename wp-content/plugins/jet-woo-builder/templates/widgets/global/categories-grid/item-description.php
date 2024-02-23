<?php
/**
 * JetWooBuilder Categories Grid widget loop item description template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/categories-grid/item-description.php.
 */

$trim_after  = esc_html__( $this->get_attr( 'desc_after_text' ), 'jet-woo-builder' );
$description = jet_woo_builder_tools()->trim_text( $category->description, $this->get_attr( 'desc_length' ), 'word', $trim_after );

if ( empty( $description ) ) {
	return;
}
?>

<div class="jet-woo-category-excerpt"><?php echo $description; ?></div>