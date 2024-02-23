<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

\Elementor\Plugin::$instance->frontend->add_body_class( 'elementor-template-full-width' );

get_header();

do_action( 'jet-woo-builder/full-width-page/before-content' );

while ( have_posts() ) {
	the_post();
	the_content();
}

do_action( 'jet-woo-builder/full-width-page/after-content' );

get_footer();
