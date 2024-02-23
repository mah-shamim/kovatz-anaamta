<?php
/**
 * Theme support
 *
 * @package BlockStrap
 * @since 1.0.0
 */

/**
 * Register Block Patterns
 *
 * @since 1.0.0
 */
class BlockStrap_Patterns {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
	}

	public function includes() {

		// Header block patterns.
		require_once __DIR__ . '/../inc/header-block-patterns.php';

		// Footer block patterns.
		require_once __DIR__ . '/../inc/footer-block-patterns.php';

		// Hero block patterns.
		require_once __DIR__ . '/../inc/hero-block-patterns.php';

		// Section block patterns.
		require_once __DIR__ . '/../inc/section-block-patterns.php';

		// Content block patterns.
		require_once __DIR__ . '/../inc/content-block-patterns.php';

		// Content block patterns.
		require_once __DIR__ . '/../inc/template-part-block-patterns.php';

		// Page layout block patterns.
		require_once __DIR__ . '/../inc/page-layout-block-patterns.php';

		// Query block patterns.
		require_once __DIR__ . '/../inc/query-block-patterns.php';
	}

}

new BlockStrap_Patterns();
