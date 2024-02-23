<?php
namespace Jet_Engine\Blocks_Views\Dynamic_Content\Blocks;

class Cover extends Base {

	/**
	 * Returns block name to register dynamic attributes for
	 *
	 * @return [type] [description]
	 */
	public function block_name() {
		return 'core/cover';
	}

	/**
	 * Returns attributes array
	 *
	 * @return [type] [description]
	 */
	public function get_attrs() {
		return array(
			array(
				'attr'             => 'url',
				'label'            => 'Image URL',
				'type'             => 'image',
				'replace_callback' => array( $this, 'replace_cover_image' ),
			),
			array(
				'attr'      => 'alt',
				'label'     => 'Image Alt',
				'replace'   => array(
					'source'    => 'attribute',
					'selector'  => 'img',
					'attribute' => 'alt',
				),
			)
		);
	}

	/**
	 * Replace image URL with dyanmic URL in the given content
	 *
	 * @param  string $value       Dynamic value to insert
	 * @param  string $content     Block content
	 * @param  array  $block_attrs Parsed block attributes
	 * @return string
	 */
	public function replace_cover_image( $value, $content, $attrs = array(), $parsed_attrs = array() ) {

		$parser = $this->get_parser_instance( $content );

		$content = $parser->replace( array(
			'source'    => 'attribute',
			'selector'  => 'img',
			'attribute' => 'src',
			'value'     => $value,
		) );

		if ( ! $parser->get_replaced() && false !== strpos( $content, 'background-image:url(' ) ) {
			$content = preg_replace( '/background-image:url\(.*?\)/', 'background-image:url(' . $value . ')', $content );
		}

		return $content;
	}

}
