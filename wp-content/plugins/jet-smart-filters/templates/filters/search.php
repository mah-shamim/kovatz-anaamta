<?php

if ( empty( $args ) ) {
	return;
}

$query_var           = $args['query_var'];
$placeholder         = $args['placeholder'];
$hide_apply_button   = $args['hide_apply_button'];
$current             = $this->get_current_filter_value( $args );
$accessibility_label = $args['accessibility_label'];
$classes = array(
	'jet-search-filter'
);

if ( '' !== $args['button_icon'] ) {
	$classes[] = 'button-icon-position-' . $args['button_icon_position'];
}
?>
<div class="<?php echo implode( ' ', $classes ) ?>" <?php $this->filter_data_atts( $args ); if ( $args['min_letters_count'] ) : ?> data-min-letters-count="<?php echo $args['min_letters_count']; ?>"<?php endif; ?>>
	<div class="jet-search-filter__input-wrapper">
		<input
			class="jet-search-filter__input"
			type="search"
			autocomplete="off"
			name="<?php echo $query_var; ?>"
			value="<?php echo $current; ?>"
			placeholder="<?php echo $placeholder; ?>"
			aria-label="<?php echo $accessibility_label; ?>"
			<?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>
		>
		<?php if ( 'ajax-ontyping' === $args['apply_type'] ) : ?>
			<div class="jet-search-filter__input-clear"></div>
			<div class="jet-search-filter__input-loading"></div>
		<?php endif; ?>
	</div>
	<?php if ( ! $hide_apply_button && 'ajax-ontyping' !== $args['apply_type'] ) : ?>
		<button
			type="button"
			class="jet-search-filter__submit apply-filters__button"
			<?php echo jet_smart_filters()->data->get_tabindex_attr(); ?>
		>
			<?php echo 'left' === $args['button_icon_position'] ? $args['button_icon'] : ''; ?>
			<span class="jet-search-filter__submit-text"><?php echo $args['button_text']; ?></span>
			<?php echo 'right' === $args['button_icon_position'] ? $args['button_icon'] : ''; ?>
		</button>
	<?php endif; ?>
</div>
