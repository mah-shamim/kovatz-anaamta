<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Blocks_Views_Ajax_Handlers' ) ) {

	/**
	 * Define Jet_Engine_Blocks_Views_Ajax_Handlers class
	 */
	class Jet_Engine_Blocks_Views_Ajax_Handlers {

		public function __construct() {
			add_filter( 'jet-engine/listings/ajax/settings-by-id/blocks', array( $this, 'find_block_by_id' ), 10, 3 );
		}

		public function find_block_by_id( $result = array(), $element_id = null, $post_id = null ) {

			$post = get_post( $post_id );

			if ( ! $post ) {
				return $result;
			}

			$blocks = parse_blocks( $post->post_content );
			$attrs  = $this->recursive_find_block( $blocks, $element_id );

			if ( ! empty( $attrs ) ) {
				return $attrs;
			} else {
				return $result;
			}

		}

		public function recursive_find_block( $blocks = array(), $element_id = null ) {

			if ( empty( $blocks ) ) {
				return false;
			}

			foreach ( $blocks as $block ) {
				if ( ! empty( $block['attrs']['_block_id'] ) && $element_id === $block['attrs']['_block_id'] ) {
					return $block['attrs'];
				} elseif ( ! empty( $block['innerBlocks'] ) ) {
					$attrs = $this->recursive_find_block( $block['innerBlocks'], $element_id );

					if ( $attrs ) {
						return $attrs;
					}

				}
			}

			return false;

		}

	}

}
