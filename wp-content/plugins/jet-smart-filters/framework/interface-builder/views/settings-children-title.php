<?php
/**
 * Settings title template.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div class="h3-style cx-ui-kit__title cx-settings__title" role="banner" ><?php echo wp_kses_post( $args['title'] ); ?></div>
