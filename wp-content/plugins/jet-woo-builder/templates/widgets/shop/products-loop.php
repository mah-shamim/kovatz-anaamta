<?php
/**
 * Products loop template with switcher enable
 */

$settings         = $this->get_settings();
$template_archive = jet_woo_builder_shop_settings()->get( 'archive_template' );
$default_template = '';

if ( ! empty( $settings['archive_item_layout'] ) ) {
	$default_template = $settings['archive_item_layout'];
} elseif ( 'default' !== $template_archive ) {
	$default_template = $template_archive;
}

$switcher_data    = [
	'main'      => ! empty( $settings['main_layout'] ) ? $settings['main_layout'] : $default_template,
	'secondary' => ! empty( $settings['secondary_layout'] ) ? $settings['secondary_layout'] : $default_template,
];

$main_layout_switcher_label      = ! empty( $settings['main_layout_switcher_label'] ) ? sprintf( '<span class="jet-woo-switcher-btn__label">%s</span>', esc_html( $settings['main_layout_switcher_label'] ) ) : '';
$secondary_layout_switcher_label = ! empty( $settings['secondary_layout_switcher_label'] ) ? sprintf( '<span class="jet-woo-switcher-btn__label">%s</span>', esc_html( $settings['secondary_layout_switcher_label'] ) ) : '';
$active_main_layout              = '';
$active_secondary_layout         = '';
$layout                          = ! empty( $_COOKIE['jet_woo_builder_layout'] ) ? absint( $_COOKIE['jet_woo_builder_layout'] ) : false;

if ( absint( $switcher_data['main'] ) !== $layout && absint( $switcher_data['secondary'] ) !== $layout ) {
	unset( $_COOKIE['jet_woo_builder_layout'] );

	$active_main_layout = 'active';
}

if ( $layout && ( absint( $switcher_data['main'] ) !== absint( $switcher_data['secondary'] ) ) ) {
	$active_main_layout      = $layout === absint( $switcher_data['main'] ) ? 'active' : '';
	$active_secondary_layout = $layout === absint( $switcher_data['secondary'] ) ? 'active' : '';
} else {
	$active_main_layout = 'active';
}
?>

<div class="jet-woo-builder-products-loop">
	<div class="jet-woo-switcher-controls-wrapper">
		<a type="button"
		   class="button jet-woo-switcher-btn jet-woo-switcher-btn-main <?php echo $active_main_layout; ?>">
			<span class="jet-woo-switcher-btn__icon">
				<?php $this->__render_icon( 'main_layout_switcher_icon', '%s', '', true ); ?>
			</span>
			<?php echo $main_layout_switcher_label; ?>
		</a>
		<a type="button"
		   class="button jet-woo-switcher-btn jet-woo-switcher-btn-secondary <?php echo $active_secondary_layout; ?>">
			<span class="jet-woo-switcher-btn__icon">
				<?php $this->__render_icon( 'secondary_layout_switcher_icon', '%s', '', true ); ?>
			</span>
			<?php echo $secondary_layout_switcher_label; ?>
		</a>
	</div>

	<div class="jet-woo-products-wrapper" data-layout-switcher='<?php echo esc_attr( json_encode( $switcher_data ) ); ?>'>
		<?php $this->products_loop(); ?>
	</div>
</div>
