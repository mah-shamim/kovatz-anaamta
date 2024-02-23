<?php
namespace Jet_Engine\Blocks_Views\Dynamic_Content\Blocks\DOM_Parser_Fallbak;

if ( ! class_exists( __NAMESPACE__ . '\Nodes_Stack' ) ) {
	require jet_engine()->blocks_views->component_path( 'dynamic-content/dom-parser-fallbak/nodes-stack.php' );
}

if ( ! class_exists( __NAMESPACE__ . '\Element' ) ) {
	require jet_engine()->blocks_views->component_path( 'dynamic-content/dom-parser-fallbak/element.php' );
}

class Document extends Nodes_Stack {

	/**
	 * Returns elemnts list from HTML tag
	 *
	 * @param  [type] $tag [description]
	 * @return [type]      [description]
	 */
	public function getElementsByTagName( $tag, $nodes = false ) {

		if ( false === $nodes ) {
			$nodes = $this->nodes;
		}

		if ( empty( $nodes ) ) {
			return array();
		}

		foreach ( $nodes as $element ) {
			if ( $element->getTag() === $tag ) {
				return array( $element );
			} else {

				$result = $this->getElementsByTagName( $tag, $element->getNodes() );

				if ( ! empty( $result ) ) {
					return $result;
				}

			}
		}

		return array();

	}

}
