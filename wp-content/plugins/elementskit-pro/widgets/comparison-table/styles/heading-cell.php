<?php if (!empty($item['ekit_cp_table_heading_cell_icons']['value'])) : ?>
	<?php Elementor\Icons_Manager::render_icon( $item['ekit_cp_table_heading_cell_icons'], [ 'aria-hidden' => 'true' ] ); ?>
<?php endif; ?>

<?php if (!empty($item['ekit_cp_table_heading_cell_text'])) : ?>
	<?php echo wp_kses($item['ekit_cp_table_heading_cell_text'], \ElementsKit_Lite\Utils::get_kses_array()); ?>
<?php endif; ?>

<?php if (!empty($item['ekit_cp_table_heading_cell_image']['url'])) : ?>
	<img src="<?php echo esc_url($item['ekit_cp_table_heading_cell_image']['url']); ?>" alt="">
<?php endif; ?>