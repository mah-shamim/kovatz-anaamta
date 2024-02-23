<?php
namespace Jet_Engine\Compatibility\Packages;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Star_Rating_Block_Package class
 */
class Star_Rating_Block_Package {

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_action( 'jet-engine/blocks-views/dynamic-content/init-blocks', array( $this, 'register_block' ) );
	}

	/**
	 * Register Star Rating dynamic block
	 *
	 * @param  [type] $manager [description]
	 * @return [type]          [description]
	 */
	public function register_block( $manager ) {
		require jet_engine()->plugin_path( 'includes/compatibility/packages/jet-star-rating-block/dynamic-block/star-rating.php' );
		$manager->register_block( new Star_Rating() );
	}

}

new Star_Rating_Block_Package();
