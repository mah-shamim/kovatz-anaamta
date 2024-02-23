<?php

if ( empty( $settings ) || empty( $sorting_options ) || empty( $container_data_atts ) ) {
	return;
}

$class               = 'jet-sorting';
$class_containter    = $class . ' ' . ( ! empty( $settings['label_block'] ) ? $class . '--flex-column' : $class . '--flex-row' );
$accessibility_label = ! empty( $settings['label'] ) ? $settings['label'] : __( 'Sort filter', 'jet-smart-filters' );

?>
<div class="jet-smart-filters-sorting jet-filter">
	<div class="<?php echo $class_containter ?>" <?php echo $container_data_atts; ?>>
		<?php if ( $label ) : ?>
			<div class="<?php echo $class ?>-label"><?php echo $label ?></div>
		<?php endif; ?>
		<select
			class="<?php echo $class ?>-select"
			name="select-name"
			<?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>
			aria-label="<?php echo $accessibility_label; ?>"
		>
			<?php if ( $placeholder ) : ?>
				<option value=""><?php echo $placeholder ?></option>
			<?php endif; ?>
			<?php foreach ( $sorting_options as $option ) : ?>
				<?php $selected = isset( $option['current'] ) ? ' selected' : ''; ?>
				<option
					value="<?php echo $option['value']; ?>"
					<?php echo $selected; ?>
				><?php echo $option['title']; ?></option>
			<?php endforeach; ?>
		</select>
	</div>
</div>