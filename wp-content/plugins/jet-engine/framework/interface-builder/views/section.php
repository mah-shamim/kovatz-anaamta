<?php
/**
 * Section template.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div class="cx-ui-kit cx-section <?php echo esc_attr( $args['class'] ); ?>" onclick="void(0)">
	<div class="cx-section__holder">
		<div class="cx-section__inner">
			<div class="cx-section__info">
				<?php if ( ! empty( $args['title'] ) ) { ?>
					<div class="h1-style cx-ui-kit__title cx-section__title" role="banner" ><?php echo wp_kses_post( $args['title'] ); ?></div>
				<?php } ?>
				<?php if ( ! empty( $args['description'] ) ) { ?>
					<div class="cx-ui-kit__description cx-section__description " role="note" ><?php echo wp_kses_post( $args['description'] ); ?></div>
				<?php } ?>
			</div>
			<?php if ( ! empty( $args['children'] ) ) { ?>
				<div class="cx-ui-kit__content cx-section__content" role="group" >
					<?php echo $args['children']; ?>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
