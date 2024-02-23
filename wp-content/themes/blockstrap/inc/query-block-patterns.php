<?php
/**
 * Block patterns
 *
 * @package BlockStrap
 * @since 1.2.2
 */

/**
 * Register Block Pattern Category.
 */
if ( function_exists( 'register_block_pattern_category' ) ) {
	register_block_pattern_category(
		'blockstrap-blog',
		array( 'label' => esc_html__( 'Blog / post list', 'blockstrap' ) )
	);
}
