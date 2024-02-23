<?php
namespace Jet_Engine\Blocks_Views\Dynamic_Content\Blocks;

abstract class Base {

	private $parser = null;

	/**
	 * Set current block parser
	 * 
	 * @param [type] $parser [description]
	 */
	public function set_parser( $parser ) {
		$this->parser = $parser;
	}

	/**
	 * Set current block parser
	 * 
	 * @param [type] $parser [description]
	 */
	public function get_parser() {
		return $this->parser;
	}

	/**
	 * Returns block name to register dynamic attributes for
	 *
	 * @return string
	 */
	abstract public function block_name();

	/**
	 * Returns attributes array
	 *
	 * @return array
	 */
	abstract public function get_attrs();

	/**
	 * Returns blocks attributes prepared to use in JS object
	 *
	 * @return array
	 */
	public function get_block_atts() {

		$raw_atts = $this->get_attrs();
		$result   = array();

		foreach ( $raw_atts as $attr ) {

			if ( ! empty( $attr['replace_callback'] ) ) {
				unset( $attr['replace_callback'] );
			}

			$result[] = $attr;
		}

		return $result;

	}

	/**
	 * Returns new DOM parser instance for given content
	 *
	 * @param  string $content Content to parse
	 * @return object DOM_Parser
	 */
	public function get_parser_instance( $content = '' ) {

		if ( ! class_exists( __NAMESPACE__ . '\DOM_Parser' ) ) {
			require jet_engine()->blocks_views->component_path( 'dynamic-content/dom-parser.php' );
		}

		return new DOM_Parser( $content );
	}

	/**
	 * Find and replace block attribute with dynamic value in the block content
	 *
	 * @param  string $attr        Attribute name/key to find and replace
	 * @param  string $value       Dynamic value to insert
	 * @param  string $content     Block content
	 * @param  array  $block_attrs Parsed block attributes
	 * @return string - content with dynamic values applied
	 */
	public function replace_attr_content( $attr = null, $value = null, $content = '', $dynamic_attrs = array(), $parsed_attrs = array() ) {

		$raw_atts = $this->get_attrs();

		foreach ( $raw_atts as $attr_data ) {

			if ( $attr_data['attr'] !== $attr ) {
				continue;
			}

			if ( ! empty( $attr_data['replace_callback'] ) && is_callable( $attr_data['replace_callback'] ) ) {
				return call_user_func( 
					$attr_data['replace_callback'],
					$value,
					$content,
					$dynamic_attrs,
					$parsed_attrs
				);
			} elseif ( ! empty( $attr_data['replace'] ) ) {



				$parser                = $this->get_parser_instance( $content );
				$replace_data          = $attr_data['replace'];
				$replace_data['value'] = $value;

				return $parser->replace( $replace_data );

			}

		}

		return $content;
	}

}
