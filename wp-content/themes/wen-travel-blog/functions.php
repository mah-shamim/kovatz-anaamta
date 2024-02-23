<?php
/*
 * This is the child theme for Signify theme.
 */

/**
 * Enqueue default CSS styles
 */
function wen_travel_blog_enqueue_styles() {
    // Include parent theme CSS.
    wp_enqueue_style( 'wen-travel-style', get_template_directory_uri() . '/style.css', null, date( 'Ymd-Gis', filemtime( get_template_directory() . '/style.css' ) ) );

    // Include child theme CSS.
    wp_enqueue_style( 'wen-travel-blog-style', get_stylesheet_directory_uri() . '/style.css', array( 'wen-travel-style' ), date( 'Ymd-Gis', filemtime( get_stylesheet_directory() . '/style.css' ) ) );
    
    // Load rtl css.
    if ( is_rtl() ) {
        wp_enqueue_style( 'wen-travel-rtl', get_template_directory_uri() . '/rtl.css', array( 'wen-travel-style' ), filemtime( get_stylesheet_directory() . '/rtl.css' ) );
    }

    // Enqueue child block styles after parent block style.
    wp_enqueue_style( 'wen-travel-blog-block-style', get_stylesheet_directory_uri() . '/assets/css/child-blocks.css', array( 'wen-travel-block-style' ), date( 'Ymd-Gis', filemtime( get_stylesheet_directory() . '/assets/css/child-blocks.css' ) ) );
}
add_action( 'wp_enqueue_scripts', 'wen_travel_blog_enqueue_styles' );

/**
 * Add child theme editor styles
 */
function wen_travel_blog_editor_style() {
    add_editor_style( array(
            'assets/css/child-editor-style.css',
            wen_travel_fonts_url(),
            get_theme_file_uri( 'assets/css/font-awesome/css/font-awesome.css' ),
        )
    );
}
add_action( 'after_setup_theme', 'wen_travel_blog_editor_style', 11 );

/**
 * Enqueue editor styles for Gutenberg
 */
function wen_travel_blog_block_editor_styles() {
    // Enqueue child block editor style after parent editor block css.
    wp_enqueue_style( 'wen-travel-blog-block-editor-style', get_stylesheet_directory_uri() . '/assets/css/child-editor-blocks.css', array( 'wen-travel-block-editor-style' ), date( 'Ymd-Gis', filemtime( get_stylesheet_directory() . '/assets/css/child-editor-blocks.css' ) ) );
}
add_action( 'enqueue_block_editor_assets', 'wen_travel_blog_block_editor_styles', 11 );

/**
 * Theme Setup
 */
function wen_travel_blog_setup() {
    /**
     * Register New menus for header top
     */
    register_nav_menus( array(
        'menu-top'   => esc_html__( 'Header Top Menu', 'wen-travel-blog' ),
        'social-top' => esc_html__( 'Social Menu at Top', 'wen-travel-blog' ),
    ) );

    /**
     * Load the child theme textdomain
     */
    load_child_theme_textdomain( 'wen-travel-blog', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'wen_travel_blog_setup', 11 );

/**
 * Change default header image
 */
function wen_travel_blog_header_default_image( $args ) {
    $args['default-image'] =  get_theme_file_uri( 'assets/images/header-image.jpg' );

    return $args;
}
add_filter( 'wen_travel_custom_header_args', 'wen_travel_blog_header_default_image' );

/**
 * Add Header Layout Class to body class
 *
 * @since 1.0.0
 *
 * @param array $classes Classes for the body element.
 * @return array (Maybe) filtered body classes.
 */
function wen_travel_blog_body_classes( $classes ) {
    // Added color scheme to body class.
    $classes['color-scheme']       = 'color-scheme-blog';
    $classes['header-style']       = 'header-style-two';
    $classes['transparent-header'] = '';

    return $classes;
}
add_filter( 'body_class', 'wen_travel_blog_body_classes', 100 );

/**
 * Display Sections on header and footer with respect to the section option set in wen_travel_sections_sort
 */
function wen_travel_sections( $selector = 'header' ) {
    get_template_part( 'template-parts/header/header-media' );
    get_template_part( 'template-parts/slider/display-slider' );
    get_template_part( 'third-party/wp-travel/template-parts/trip-filter' );
    get_template_part( 'third-party/wp-travel/template-parts/featured-trips' );
    get_template_part( 'template-parts/featured-content/display-featured' );
    get_template_part( 'template-parts/hero-content/content-hero' );
    get_template_part( 'template-parts/service/display-service' );
    get_template_part( 'template-parts/portfolio/display-portfolio' );
    get_template_part( 'template-parts/testimonial/display-testimonial' );
    get_template_part( 'third-party/wp-travel/template-parts/latest-trips' );    
}

