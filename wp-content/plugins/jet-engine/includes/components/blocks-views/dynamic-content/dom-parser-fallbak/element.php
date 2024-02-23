<?php
namespace Jet_Engine\Blocks_Views\Dynamic_Content\Blocks\DOM_Parser_Fallbak;

class Element extends Nodes_Stack {

	protected $tag     = null;
	protected $closing = false;
	protected $attrs   = array();

	public function __construct( $tag, $attributes_string, $closing = false, $html = '' ) {

		parent::__construct( $html );

		$this->tag     = $tag;
		$this->closing = $closing;

		$this->parse_attrs( $attributes_string );

	}

	public function saveHTML() {

		$result = '<' . $this->tag;

		if ( ! empty( $this->attrs ) ) {
			$result .= ' ' . $this->attrsToString();
		}

		if ( ! $this->closing ) {
			$result .= '/';
		}

		$result .= '>';

		if ( $this->closing ) {

			if ( empty( $this->nodes ) ) {
				$result .= $this->html;
			} else {
				foreach ( $this->nodes as $node ) {
					$result .= $node->saveHTML();
				}
			}

			$result .= '</' . $this->tag . '>';
		}

		return $result;

	}

	public function attrsToString() {

		$result = array();

		foreach ( $this->attrs as $key => $value ) {
			$result[] = sprintf( '%1$s="%2$s"', $key, $value );
		}

		return implode( ' ', $result );

	}

	/**
	 * Retuanns current element tag name
	 *
	 * @return [type] [description]
	 */
	public function getTag() {
		return $this->tag;
	}

	/**
	 * Set attribute value
	 *
	 * @param [type] $attr  [description]
	 * @param string $value [description]
	 */
	public function setAttribute( $attr, $value = '' ) {
		$this->attrs[ $attr ] = $value;
	}

	/**
	 * Parse attributes string
	 * @return [type] [description]
	 */
	public function parse_attrs( $attributes_string = '' ) {

		$attributes_string = trim( $attributes_string );

		if ( ! $attributes_string ) {
			return;
		}

		preg_match_all( '/([\w-]+)(=[\'\"](.*?)[\'\"])?/', $attributes_string, $matches, PREG_SET_ORDER );

		if ( ! empty( $matches ) ) {
			foreach ( $matches as $match ) {
				if ( ! empty( $match[1] ) ) {
					$this->attrs[ $match[1] ] = isset( $match[3] ) ? $match[3] : '';
				}
			}
		}

	}

}
