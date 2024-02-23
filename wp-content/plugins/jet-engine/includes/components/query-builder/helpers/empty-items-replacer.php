<?php
namespace Jet_Engine\Query_Builder\Helpers;

/**
 * Check if query arguments includes parameters which should be prevented from processing as empty.
 * For example, if you dynamically getting alues for post__in query argument and it returned empty result -
 * query will return all post, but for this context we need to retun nothing
 */
class Empty_Items_Replacer {

	/**
	 * All query arguments
	 * @var array
	 */
	protected $args = array();

	/**
	 * Query arguments we need to check and replace
	 * @var array
	 */
	protected $replace = array();

	public function __construct( $args = array(), $replace = array() ) {
		$this->args    = $args;
		$this->replace = $replace;
	}

	/**
	 * Search and replace required data
	 * @return [type] [description]
	 */
	public function replace() {
		foreach ( $this->replace as $replace_arg ) {
			if ( isset( $this->args[ $replace_arg ] ) ) {
				$this->args[ $replace_arg ] = $this->replace_single( $this->args[ $replace_arg ], $replace_arg );
			}
		}

		return $this->args;
	}

	/**
	 * Replace single value according existing rules.
	 * 
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function replace_single( $value, $key ) {
		
		if ( ( is_array( $value ) && in_array( 'not-found', $value ) ) || ( ! is_array( $value ) && 'not-found' === $value ) ) {
			$value = PHP_INT_MAX;
		}

		return apply_filters( 'jet-engine/query-args/replace-empty-item', $value, $key );

	}

}