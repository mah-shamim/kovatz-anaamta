<?php
/**
 * Block patterns
 *
 * @package BlockStrap
 * @since 1.2.0
 */

/**
 * Register Block Pattern Category.
 */
if ( function_exists( 'register_block_pattern_category' ) ) {
	register_block_pattern_category(
		'blockstrap-site-footer',
		array( 'label' => esc_html__( 'Site footers', 'blockstrap' ) )
	);
}

/**
 * Register Block Patterns.
 */
if ( function_exists( 'register_block_pattern' ) ) {

	register_block_pattern(
		'blockstrap/footer-default',
		array(
			'title'      => esc_html__( 'Default Footer', 'blockstrap' ),
			'categories' => array( 'blockstrap-site-footer' ),
			'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
				'blockstrap_pattern_footer_default',
				''
			) : '',
		)
	);

}
