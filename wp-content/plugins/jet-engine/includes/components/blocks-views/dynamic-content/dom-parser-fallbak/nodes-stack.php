<?php
namespace Jet_Engine\Blocks_Views\Dynamic_Content\Blocks\DOM_Parser_Fallbak;

class Nodes_Stack {

	protected $html  = null;
	protected $nodes = array();
	protected $attrs = array();

	public function __construct( $html = '' ) {

		$this->html = $html;

		if ( ! $this->getCachedNodes() ) {
			$this->nodesReducer( $this->html );
		}

	}

	/**
	 * Returns key to store current nodes tree in the cache
	 *
	 * @return [type] [description]
	 */
	public function cacheKey() {
		return md5( $this->html ) . sha1( $this->html );
	}

	/**
	 * Tries to return cached nodes for current string
	 *
	 * @return [type] [description]
	 */
	public function getCachedNodes() {

		$key = $this->cacheKey();

		if ( ! $key ) {
			return false;
		} else {

			$nodes = wp_cache_get( $key, 'jet-engine' );

			if ( false !== $nodes ) {
				$this->attrs = $nodes;
				return true;
			} else {
				return false;
			}

		}

	}

	/**
	 * Returns inner nodes of the element
	 *
	 * @return array
	 */
	public function getNodes() {
		return $this->nodes;
	}

	/**
	 * Generate HTML string from element nodes
	 *
	 * @return [type] [description]
	 */
	public function saveHTML() {

		$result = '';

		foreach ( $this->getNodes() as $node ) {
			$result .= $node->saveHTML();
		}

		return $result;

	}

	/**
	 * Recursive function to get tag nodes from the string
	 *
	 * @param  string $reduce [description]
	 * @return [type]         [description]
	 */
	public function nodesReducer( $reduce = '' ) {

		while ( $reduce ) {
			$reduce = $this->parseNodes( $reduce );
		}

		$key = $this->cacheKey();

		if ( $key ) {
			wp_cache_set( $key, $this->attrs, 'jet-engine' );
		}

	}

	/**
	 * Parse top-level nodes from HTML string
	 * @param  [type] $reduce [description]
	 * @return [type]         [description]
	 */
	public function parseNodes( $reduce ) {

		preg_match( '/<(\w+)\s?(.*?)[\/]?>/s', $reduce, $open_matches );

		if ( ! empty( $open_matches ) ) {

			$tag           = $open_matches[1];
			$attrs         = $open_matches[2];
			$to_remove     = $open_matches[0];
			$inner_content = '';
			$closing       = false;

			// search for closing tag if exists
			preg_match( '/<' . $tag . '\s?(.*?)[\/]?>(.*)<\/' . $tag . '>/s', $reduce, $full_matches );

			if ( ! empty( $full_matches ) ) {
				$to_remove     = $full_matches[0];
				$inner_content = $full_matches[2];
				$closing       = true;
			}

			$this->nodes[] = new Element( $tag, $attrs, $closing, $inner_content );

			$reduce = str_replace( $to_remove, '', $reduce );

		} else {
			$reduce = '';
		}

		return $reduce;

	}

}
