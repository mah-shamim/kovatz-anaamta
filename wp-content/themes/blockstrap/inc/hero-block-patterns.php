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
		'blockstrap-hero-sections',
		array( 'label' => esc_html__( 'Hero Sections', 'blockstrap' ) )
	);
}

/**
 * Register Block Patterns.
 */
if ( function_exists( 'register_block_pattern' ) ) {

	register_block_pattern(
		'blockstrap/hero-home-default',
		array(
			'title'      => esc_html__( 'Hero home', 'blockstrap' ),
			'categories' => array( 'blockstrap-hero-sections' ),
			'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
				'blockstrap_pattern_hero_home_default',
				''
			) : '',
		)
	);

	register_block_pattern(
		'blockstrap/hero-404-default',
		array(
			'title'      => esc_html__( 'Hero 404', 'blockstrap' ),
			'categories' => array( 'blockstrap-hero-sections' ),
			'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
				'blockstrap_pattern_hero_404_default',
				''
			) : '',
		)
	);

	register_block_pattern(
		'blockstrap/hero-archive-default',
		array(
			'title'      => esc_html__( 'Hero Archive', 'blockstrap' ),
			'categories' => array( 'blockstrap-hero-sections' ),
			'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
				'blockstrap_pattern_hero_archive_default',
				''
			) : '',
		)
	);

	register_block_pattern(
		'blockstrap/hero-page-default',
		array(
			'title'      => esc_html__( 'Hero Page', 'blockstrap' ),
			'categories' => array( 'blockstrap-hero-sections' ),
			'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
				'blockstrap_pattern_hero_page_default',
				''
			) : '',
		)
	);

	register_block_pattern(
		'blockstrap/hero-post-default',
		array(
			'title'      => esc_html__( 'Hero Post', 'blockstrap' ),
			'categories' => array( 'blockstrap-hero-sections' ),
			'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
				'blockstrap_pattern_hero_post_default',
				''
			) : '',
		)
	);


}
