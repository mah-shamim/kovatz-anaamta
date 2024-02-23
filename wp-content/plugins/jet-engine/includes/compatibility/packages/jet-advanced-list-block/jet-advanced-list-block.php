<?php
namespace Jet_Engine\Compatibility\Packages;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Advanced_List_Block_Package class
 */
class Advanced_List_Block_Package {

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_action( 'jet-engine/blocks-views/dynamic-content/init-blocks', array( $this, 'register_blocks' ) );
	}

	/**
	 * Register Star Rating dynamic block
	 *
	 * @param  [type] $manager [description]
	 * @return [type]          [description]
	 */
	public function register_blocks( $manager ) {

		require jet_engine()->plugin_path( 'includes/compatibility/packages/jet-advanced-list-block/dynamic-block/list-block.php' );
		require jet_engine()->plugin_path( 'includes/compatibility/packages/jet-advanced-list-block/dynamic-block/list-item-block.php' );

		$manager->register_block( new Advanced_List_Block() );
		$manager->register_block( new Advanced_List_Item_Block() );

	}

}

new Advanced_List_Block_Package();
