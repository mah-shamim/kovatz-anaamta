<?php
namespace Jet_Engine\Macros;

/**
 * Returns ID of current post author.
 */
class Author_Id extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'author_id';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Post author ID', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {

		$author_id = null;

		global $authordata;

		if ( $authordata ) {
			$author_id = get_the_author_meta( 'ID' );
		} else {

			$post = get_post();

			if ( $post ) {
				$author_id = get_the_author_meta( 'ID', $post->post_author );
			}
		}

		return $author_id;
	}
}