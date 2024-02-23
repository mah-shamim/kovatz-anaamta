<?php


// Pattern filters
require_once 'includes/pattern-filters.php';
require_once 'includes/pattern-filters/menu.php';
require_once 'includes/pattern-filters/header.php';
require_once 'includes/pattern-filters/footer.php';
require_once 'includes/pattern-filters/hero.php';
require_once 'includes/pattern-filters/content.php';

// Register patterns
require_once 'includes/register-patterns.php';

// Downgrade functions
require_once 'includes/downgrade-functions.php';

add_action( 'wp_enqueue_scripts', 'directory_enqueue_styles' );
function directory_enqueue_styles() {
	 wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}


/**
 * Loads the translation files for WordPress.
 *
 * @since 3.0.0
 */
function directory_theme_setup()
{
	load_child_theme_textdomain( 'directory', get_stylesheet_directory() . '/languages' );
}

add_action('after_setup_theme', 'directory_theme_setup');


/**
 * Loads the Blockstrap Directory admin functionalities.
 *
 * This function checks if the user is in the admin area and then loads the necessary
 * admin files for Blockstrap School theme.
 *
 * @return void
 */
function blockstrap_school_load_admin(){
	if ( is_admin() ) {
		// Theme admin stuff
		require_once 'includes/class-blockstrap-admin-child.php';
	}
}

add_action('after_setup_theme','blockstrap_school_load_admin');