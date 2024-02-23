<?php
namespace Jet_Engine\Blocks_Views\Dynamic_Content\Blocks;

/**
 * Wraps DOM-parsing related operations
 */
class DOM_Parser {

	protected $replaced = false;

	public function __construct( $content ) {
		$this->content = $content;
	}

	/**
	 * Returns available parser instance
	 *
	 * @return [type] [description]
	 */
	public function get_document_instance( $content = '' ) {

		if ( ! $content ) {
			return false;
		}

		if ( class_exists( '\DOMDocument' ) && false === strpos( $content, '<svg' ) ) {
			$document = new \DOMDocument();
			$document->loadHTML( $content );
		} else {

			if ( ! class_exists( __NAMESPACE__ . '\DOM_Parser_Fallbak\Document' ) ) {
				require jet_engine()->blocks_views->component_path( 'dynamic-content/dom-parser-fallbak/document.php' );
			}

			$document = new DOM_Parser_Fallbak\Document( $content );

		}

		return $document;

	}

	/**
	 * Replace data in the $this->content by given data
	 *
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function replace( $data = array() ) {

		$source   = ! empty( $data['source'] ) ? $data['source'] : false;
		$selector = ! empty( $data['selector'] ) ? $data['selector'] : false;
		$value    = isset( $data['value'] ) ? $data['value'] : '';

		if ( ! $source || ! $selector ) {
			return $this->content;
		}

		if ( ! apply_filters( 'jet-engine/blocks-views/dynamic-content/use-document-parser', true ) ) {
			return $this->plain_replace( $this->content, $data );
		}

		$document = $this->get_document_instance( $this->content );

		if ( ! $document ) {
			return $this->content;
		}

		switch ( $source ) {

			case 'attribute':

				$attribute = ! empty( $data['attribute'] ) ? $data['attribute'] : false;

				if ( $attribute ) {

					$tags = $document->getElementsByTagName( $selector );

					foreach ( $tags as $tag ) {

						if ( $this->replaced ) {
							break;
						}

						$this->replaced = true;
						$tag->setAttribute( $attribute, $value );
					}

				}

				break;
		}

		if ( $this->replaced ) {

			if ( method_exists( $document, 'removeChild' ) ) {
				$document->removeChild( $document->doctype );
				$result = str_replace( array( '<html>', '</html>', '<body>', '</body>' ), '', $document->saveHTML() );
			} else {
				$result = $document->saveHTML();
			}

			$this->content = $result;
		}

		return $this->content;

	}

	/**
	 * Make attributes plain replace (only with regex)
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function plain_replace( $content, $data = array() ) {

		$source   = ! empty( $data['source'] ) ? $data['source'] : false;
		$selector = ! empty( $data['selector'] ) ? $data['selector'] : false;
		$value    = isset( $data['value'] ) ? $data['value'] : '';


		$attribute = ! empty( $data['attribute'] ) ? $data['attribute'] : false;

		if ( ! $attribute ) {
			return $content;
		}

		$pattern = '/<' . $selector . '(.*?)>/s';
		preg_match( $pattern, $content, $matches );

		if ( ! empty( $matches ) ) {

			$tag = $matches[0];

			if ( false !== strpos( $tag, $attribute . '=' ) ) {
				$tag = preg_replace(
					'/' . wp_slash( $attribute ) . '=[\"\\\'](.*?)[\"\\\']/', $attribute . '="' . $value . '"', $tag );
			} else {
				$tag = preg_replace( '/<' . $selector . '/', '<' . $selector . ' ' . $attribute . '="' . $value . '"', $tag );
			}

		}

		return preg_replace( $pattern, $tag, $content, 1 );

	}

	/**
	 * Returns $replaced property value to check if replacemnet was successfull
	 *
	 * @return bool
	 */
	public function get_replaced() {
		return $this->replaced;
	}

}
