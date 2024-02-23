<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

\Elementor\Plugin::$instance->frontend->add_body_class( 'elementor-template-full-width' );

get_header( 'shop' );

do_action( 'jet-woo-builder/full-width-page/before-content' );

while ( have_posts() ) {
	the_post();

	if ( ! empty( $_GET['elementor-preview'] ) ) {
		the_content();
	} else {
		wc_get_template_part( 'content', 'single-product' );
	}
}

do_action( 'jet-woo-builder/full-width-page/after-content' );

get_footer( 'shop' );
