<?php
/**
 * Block filters to change the appearance of some core blocks.
 *
 * @package BlockStrap
 * @since 1.0.0
 */

/**
 * Add theme support
 *
 * @since 1.0.0
 */
class BlockStrap_Block_Filters {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		add_filter( 'render_block', array( $this, 'post_template' ), 10, 2 );
	}

	public function post_template( $block_content, $block ) {
		global $wp_version;

		$block_name = version_compare($wp_version,'6.3','<') ? 'query' : 'post-template';
		if ( 'core/'.$block_name === $block['blockName'] ) {

			// new WP ver > 6.3
			if ( isset( $block['attrs']['layout']['columnCount'] ) ) {
				$columns = isset( $block['attrs']['layout']['columnCount']  ) ? absint( $block['attrs']['layout']['columnCount']  ) : 1;
				$colCount = isset( $block['attrs']['layout']['type'] ) && $block['attrs']['layout']['type'] === 'grid' ? $columns : 1;
				$colMd    = ' row-cols-md-' . $colCount;
				$colSm    = ' row-cols-sm-' . $colCount > 1 ? ( $colCount - 1 ) : $colCount;
				$rowClass = ' row list-unstyled row-cols-1 ' . $colSm . $colMd;
			}else{
				$columns = isset( $block['attrs']['displayLayout']['columns'] ) ? absint( $block['attrs']['displayLayout']['columns'] ) : 1;
				$colCount = isset( $block['attrs']['displayLayout']['type'] ) && $block['attrs']['displayLayout']['type'] === 'flex' ? $columns : 1;
				$colMd    = ' row-cols-md-' . $colCount;
				$colSm    = ' row-cols-sm-' . $colCount > 1 ? ( $colCount - 1 ) : $colCount;
				$rowClass = ' row list-unstyled row-cols-1 ' . $colSm . $colMd;
			}

			$block_content = str_replace(
				array(
					'wp-block-post-template',
					'wp-block-post ',
				),
				array(
					'row list-unstyled row-cols-1' . $rowClass,
					'wp-block-post col mb-4 ',
				),
				$block_content
			);
		}

		return $block_content;
	}


}

new BlockStrap_Block_Filters();
