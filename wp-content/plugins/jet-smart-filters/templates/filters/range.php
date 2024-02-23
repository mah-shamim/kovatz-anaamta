<?php

if ( empty( $args ) ) {
	return;
}

$container_class = 'jet-range';
if ( wp_is_mobile() ) {
	$container_class .= ' jet-range--mobile';
}

$query_var           = $args['query_var'];
$inputs_enabled      = $args['inputs_enabled'];
$inputs_type         = $args['inputs_separators_enabled'] ? 'text' : 'number';
$prefix              = $args['prefix'];
$suffix              = $args['suffix'];
$current             = $this->get_current_filter_value( $args );
$accessibility_label = $args['accessibility_label'];

if ( $current ) {
	$slider_val = explode( '_', $current );
} else {
	$slider_val = array( $args['min'], $args['max'] );
}

?>

<div class="<?php echo $container_class; ?>" <?php $this->filter_data_atts( $args ); ?>>
	<fieldset class="jet-range__slider">
		<legend style="display:none;"><?php printf( __( '%s - slider', 'jet-smart-filters' ), $accessibility_label ); ?></legend>
		<div class="jet-range__slider__track">
			<div class="jet-range__slider__track__range"></div>
		</div>
		<input type="range" class="jet-range__slider__input jet-range__slider__input--min" step="<?php echo $args['step']; ?>" min="<?php echo $args['min']; ?>" max="<?php echo $args['max']; ?>" value="<?php echo $slider_val[0] ?>" aria-label="<?php _e( 'Minimal value', 'jet-smart-filters' ); ?>" <?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>>
		<input type="range" class="jet-range__slider__input jet-range__slider__input--max" step="<?php echo $args['step']; ?>" min="<?php echo $args['min']; ?>" max="<?php echo $args['max']; ?>" value="<?php echo $slider_val[1] ?>" aria-label="<?php _e( 'Maximum value', 'jet-smart-filters' ); ?>" <?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>>
	</fieldset>
	<?php if ( $inputs_enabled ) : ?>
	<div class="jet-range__inputs">
		<fieldset class="jet-range__inputs__container">
			<legend style="display:none;"><?php printf( __( '%s - inputs', 'jet-smart-filters' ), $accessibility_label ); ?></legend>
			<div class="jet-range__inputs__group">
				<?php if ( $prefix ) : ?>
				<span class="jet-range__inputs__group__text"><?php echo $prefix; ?></span>
				<?php endif; ?>
				<input type="<?php echo $inputs_type; ?>" class="jet-range__inputs__min" min-range step="<?php echo $args['step']; ?>" min="<?php echo $args['min']; ?>" max="<?php echo $args['max']; ?>" value="<?php echo $slider_val[0]; ?>" aria-label="<?php _e( 'Minimal value', 'jet-smart-filters' ); ?>" <?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>/>
				<?php if ( $suffix ) : ?>
				<span class="jet-range__inputs__group__text"><?php echo $suffix ?></span>
				<?php endif; ?>
			</div>
			<div class="jet-range__inputs__group">
				<?php if ( $prefix ) : ?>
				<span class="jet-range__inputs__group__text"><?php echo $prefix; ?></span>
				<?php endif; ?>
				<input type="<?php echo $inputs_type; ?>" class="jet-range__inputs__max" max-range step="<?php echo $args['step']; ?>" min="<?php echo $args['min']; ?>" max="<?php echo $args['max']; ?>" value="<?php echo $slider_val[1]; ?>" aria-label="<?php _e( 'Maximum value', 'jet-smart-filters' ); ?>" <?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>/>
				<?php if ( $suffix ) : ?>
				<span class="jet-range__inputs__group__text"><?php echo $suffix ?></span>
				<?php endif; ?>
			</div>
		</fieldset>
	</div>
	<?php else : ?>
	<div class="jet-range__values">
		<span class="jet-range__values-prefix"><?php
			echo $prefix;
		?></span><span class="jet-range__values-min"><?php
			echo number_format(
				$slider_val[0],
				$args['format']['decimal_num'],
				$args['format']['decimal_sep'],
				$args['format']['thousands_sep']
			);
		?></span><span class="jet-range__values-suffix"><?php
			echo $suffix;
		?></span> — <span class="jet-range__values-prefix"><?php
			echo $prefix;
		?></span><span class="jet-range__values-max"><?php
			echo number_format(
				$slider_val[1],
				$args['format']['decimal_num'],
				$args['format']['decimal_sep'],
				$args['format']['thousands_sep']
			);;
		?></span><span class="jet-range__values-suffix"><?php
			echo $suffix;
		?></span>
	</div>
	<?php endif; ?>
</div>