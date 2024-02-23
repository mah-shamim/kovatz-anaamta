<?php
/**
 * The Template for displaying profile page
 */

defined( 'ABSPATH' ) || exit;

get_header( 'profile' );

/**
 * Hook before main page content output.
 * Add template wrappers start on this hook
 */
do_action( 'jet-engine/profile-builder/template/before-main-content' );

/**
 * Hoor to display main page content
 */
do_action( 'jet-engine/profile-builder/template/main-content' );

/**
 * Hook before main page content output.
 * Add template wrappers start on this hook
 */
do_action( 'jet-engine/profile-builder/template/after-main-content' );

get_footer( 'profile' );
