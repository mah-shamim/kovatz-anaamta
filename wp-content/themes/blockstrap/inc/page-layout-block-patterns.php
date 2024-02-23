<?php
/**
 * Page layout block patterns
 *
 * @package BlockStrap
 * @since 1.2
 */

/**
 * Register Block Pattern Category.
 */
if ( function_exists( 'register_block_pattern_category' ) ) {
	register_block_pattern_category(
		'blockstrap-layout',
		array( 'label' => esc_html__( 'Page layouts', 'blockstrap' ) )
	);
}
