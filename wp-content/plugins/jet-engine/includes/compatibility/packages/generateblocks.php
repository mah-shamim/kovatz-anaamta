<?php
/**
 * GenerateBlocks compatibility class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Generate_Blocks_Package' ) ) {

	/**
	 * Define Jet_Engine_Generate_Blocks_Package class
	 */
	class Jet_Engine_Generate_Blocks_Package {

		private $is_lazy_load = false;
		private $printed_css = array();

		/**
		 * Constructor for the class
		 */
		public function __construct() {

			if ( ! class_exists( 'GenerateBlocks_Enqueue_CSS' ) ) {
				return;
			}

			add_action( 'jet-engine/ajax-handlers/before-do-ajax', array( $this, 'enable_enqueue_css_for_lazy_load' ) );

			add_filter( 'jet-engine/blocks-views/render/listing-content', array( $this, 'print_inline_css' ), 10, 2 );
		}

		public function enable_enqueue_css_for_lazy_load () {

			if ( empty( $_REQUEST['handler'] ) || 'get_listing' !== $_REQUEST['handler'] ) {
				return;
			}

			if ( empty( $_REQUEST['widget_settings'] ) || empty( $_REQUEST['widget_settings']['lazy_load'] ) ) {
				return;
			}

			$this->is_lazy_load = true;

			// To enable enqueue css
			GenerateBlocks_Enqueue_CSS::get_instance()->print_inline_css();
		}

		public function print_inline_css ( $content, $listing_id ) {

			if ( ! $this->is_lazy_load ) {
				return $content;
			}

			if ( ! in_array( $listing_id, $this->printed_css ) ) {
				$css = generateblocks_get_frontend_block_css();

				if ( ! empty( $css ) ) {
					wp_register_style( 'generateblocks-' . $listing_id, false );
					wp_enqueue_style( 'generateblocks-' . $listing_id );

					wp_add_inline_style(
						'generateblocks-' . $listing_id,
						wp_strip_all_tags( $css )
					);
				}

				$this->printed_css[] = $listing_id;
			}

			return $content;
		}

	}

}

new Jet_Engine_Generate_Blocks_Package();
