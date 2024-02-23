<?php
/**
 * Feature block patterns
 *
 * @package BlockStrap
 * @since 1.2.0
 */

/*
 * Register Block Pattern Category.
 */
if ( function_exists( 'register_block_pattern_category' ) ) {
	register_block_pattern_category(
		'blockstrap-page-content',
		array( 'label' => esc_html__( 'Page Content', 'blockstrap' ) )
	);
}

/**
 * Register Block Patterns.
 */
if ( function_exists( 'register_block_pattern' ) ) {

	register_block_pattern(
		'blockstrap/page-content-404-default',
		array(
			'title'      => esc_html__( '404 Content', 'blockstrap' ),
			'categories' => array( 'blockstrap-page-content' ),
			'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
				'blockstrap_pattern_page_content_404_default',
				''
			) : '<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group"><!-- wp:paragraph -->
<p>' . __( 'It looks like nothing was found at this location.', 'blockstrap' ) . '</p>
<!-- /wp:paragraph --></main>
<!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'blockstrap/page-content-archive-default',
		array(
			'title'      => esc_html__( 'Archive Content', 'blockstrap' ),
			'categories' => array( 'blockstrap-page-content' ),
			'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
				'blockstrap_pattern_page_content_archive_default',
				''
			) : '<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group"><!-- wp:query-title {"type":"archive"} /-->

<!-- wp:query {"queryId":0,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
<div class="wp-block-query"><!-- wp:post-template -->
<!-- wp:post-title {"isLink":true} /-->

<!-- wp:post-date /-->
<!-- /wp:post-template -->

<!-- wp:query-pagination -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results."} -->
<p></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></main>
<!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'blockstrap/page-content-page-default',
		array(
			'title'      => esc_html__( 'Page Content', 'blockstrap' ),
			'categories' => array( 'blockstrap-page-content' ),
			'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
				'blockstrap_pattern_page_content_page_default',
				''
			) : '<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group"><!-- wp:post-title /-->

<!-- wp:post-content /-->

<!-- wp:template-part {"slug":"comments","area":"uncategorized"} /--></main>
<!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'blockstrap/page-content-post-default',
		array(
			'title'      => esc_html__( 'Post Content', 'blockstrap' ),
			'categories' => array( 'blockstrap-page-content' ),
			'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
				'blockstrap_pattern_page_content_post_default',
				''
			) : '<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group"><!-- wp:post-title /-->

<!-- wp:post-content /-->

<!-- wp:template-part {"slug":"comments","area":"uncategorized"} /--></main>
<!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'blockstrap/page-content-search-default',
		array(
			'title'      => esc_html__( 'Search Content', 'blockstrap' ),
			'categories' => array( 'blockstrap-page-content' ),
			'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
				'blockstrap_pattern_page_content_search_default',
				''
			) : '<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group"><!-- wp:query-title {"type":"search"} /-->

<!-- wp:search {"label":"Search","buttonText":"Search"} /-->

<!-- wp:query {"queryId":0,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
<div class="wp-block-query"><!-- wp:post-template -->
<!-- wp:post-title {"isLink":true} /-->

<!-- wp:post-date /-->
<!-- /wp:post-template -->

<!-- wp:query-pagination -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results."} -->
<p></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></main>
<!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'blockstrap/page-content-page-sidebar-left-default',
		array(
			'title'      => esc_html__( 'Page Sidebar Left Content', 'blockstrap' ),
			'categories' => array( 'blockstrap-page-content' ),
			'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
				'blockstrap_pattern_page_content_page_sidebar_left_default',
				''
			) : '<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:template-part {"slug":"sidebar-left","area":"uncategorized"} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:post-title /-->

<!-- wp:post-content /-->

<!-- wp:template-part {"slug":"comments","area":"uncategorized"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></main>
<!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'blockstrap/page-content-page-sidebar-right-default',
		array(
			'title'      => esc_html__( 'Page Sidebar Right Content', 'blockstrap' ),
			'categories' => array( 'blockstrap-page-content' ),
			'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
				'blockstrap_pattern_page_content_page_sidebar_right_default',
				''
			) : '<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:post-title /-->

<!-- wp:post-content /-->

<!-- wp:template-part {"slug":"comments","area":"uncategorized"} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:template-part {"slug":"sidebar-right","area":"uncategorized"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></main>
<!-- /wp:group -->',
		)
	);

	if ( defined( 'GEODIRECTORY_VERSION' ) && defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ) {
		register_block_pattern(
			'blockstrap/page-content-gd-archive-default',
			array(
				'title'      => esc_html__( 'GD Archive Content', 'blockstrap' ),
				'categories' => array( 'blockstrap-page-content' ),
				'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
					'blockstrap_pattern_page_content_gd_archive_default',
					''
				) : '<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group"><!-- wp:paragraph -->
<p>' . __( 'Thia template requires the GeoDirectory plugin to be installed', 'blockstrap' ) . '</p>
<!-- /wp:paragraph --></main>
<!-- /wp:group -->',
			)
		);

		register_block_pattern(
			'blockstrap/page-content-gd-search-default',
			array(
				'title'      => esc_html__( 'GD Search Content', 'blockstrap' ),
				'categories' => array( 'blockstrap-page-content' ),
				'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
					'blockstrap_pattern_page_content_gd_search_default',
					''
				) : '<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group"><!-- wp:paragraph -->
<p>' . __( 'Thia template requires the GeoDirectory plugin to be installed', 'blockstrap' ) . '</p>
<!-- /wp:paragraph --></main>
<!-- /wp:group -->',
			)
		);

		register_block_pattern(
			'blockstrap/page-content-gd-single-default',
			array(
				'title'      => esc_html__( 'GD Single Content', 'blockstrap' ),
				'categories' => array( 'blockstrap-page-content' ),
				'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
					'blockstrap_pattern_page_content_gd_single_default',
					''
				) : '<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group"><!-- wp:paragraph -->
<p>' . __( 'Thia template requires the GeoDirectory plugin to be installed', 'blockstrap' ) . '</p>
<!-- /wp:paragraph --></main>
<!-- /wp:group -->',
			)
		);

		register_block_pattern(
			'blockstrap/page-content-gd-add-listing-default',
			array(
				'title'      => esc_html__( 'GD Add Listing', 'blockstrap' ),
				'categories' => array( 'blockstrap-page-content' ),
				'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
					'blockstrap_pattern_page_content_gd_add_listing_default',
					''
				) : '<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group"><!-- wp:paragraph -->
<p>' . __( 'Thia template requires the GeoDirectory plugin to be installed', 'blockstrap' ) . '</p>
<!-- /wp:paragraph --></main>
<!-- /wp:group -->',
			)
		);

		register_block_pattern(
			'blockstrap/page-content-gd-location-default',
			array(
				'title'      => esc_html__( 'GD Location', 'blockstrap' ),
				'categories' => array( 'blockstrap-page-content' ),
				'content'    => defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) ? apply_filters(
					'blockstrap_pattern_page_content_gd_location_default',
					''
				) : '<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group"><!-- wp:paragraph -->
<p>' . __( 'Thia template requires the GeoDirectory plugin to be installed', 'blockstrap' ) . '</p>
<!-- /wp:paragraph --></main>
<!-- /wp:group -->',
			)
		);
	}
}
