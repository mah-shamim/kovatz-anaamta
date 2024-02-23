<?php
/**
 * Header block patterns
 *
 * @package BlockStrap
 * @since 1.2.0
 */

/*
 * Register Block Pattern Category.
 */
if ( function_exists( 'register_block_pattern_category' ) ) {
	register_block_pattern_category(
		'blockstrap-site-header',
		array( 'label' => esc_html__( 'Site headers', 'blockstrap' ) )
	);
}

/**
 * Register Block Patterns.
 */
if ( function_exists( 'register_block_pattern' ) ) {

	register_block_pattern(
		'blockstrap/header-default',
		array(
			'title'      => esc_html__( 'Default Header', 'blockstrap' ),
			'categories' => array( 'blockstrap-site-header' ),
			'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
				'blockstrap_pattern_header_default',
				''
			) : '<!-- wp:site-title /-->',
		)
	);

}
