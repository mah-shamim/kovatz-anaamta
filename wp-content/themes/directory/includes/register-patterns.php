<?php

/**
 * Register Block Patterns.
 */
if ( function_exists( 'register_block_pattern' ) ) {

	register_block_pattern(
		'directory/header-transparent',
		array(
			'title'      => esc_html__( 'Header Transparent', 'directory' ),
			'categories' => array( 'site-header' ),
			'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
				'directory_pattern_header_transparent',
				'<!-- wp:site-title /-->'
			) : '<!-- wp:site-title /-->',
		)
	);
}