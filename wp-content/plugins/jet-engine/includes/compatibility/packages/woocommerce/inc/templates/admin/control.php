<?php
/**
 * Control template.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$required_class = ! empty( $args['required'] ) ? ' cx-control-required' : '';
?>
<div class="cx-ui-kit cx-control cx-control-<?php echo esc_attr( $args['type'] ); ?><?php echo $required_class; ?>" data-control-name="<?php echo esc_attr( $args['id'] ); ?>">
	<?php if ( ! empty( $args['title'] ) || ! empty( $args['description'] ) ) { ?>
		<div class="cx-control__info">
			<?php if ( ! empty( $args['title'] ) ) { ?>
				<div class="cx-ui-kit__title cx-control__title" role="banner" >
					<?php echo wp_kses_post( $args['title'] ); ?>
					<?php echo ! empty( $args['required'] ) ? ' <span class="cx-control__required">*</span>' : '' ?>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
	<?php if ( ! empty( $args['children'] ) ) { ?>
		<div class="cx-ui-kit__content cx-control__content" role="group" >
			<?php echo $args['children']; ?>
		</div>
	<?php } ?>
	<?php if ( ! empty( $args['description'] ) ) { ?>
		<?php echo wc_help_tip( wp_kses_post( $args['description'] ) ); ?>
	<?php } ?>
</div>
