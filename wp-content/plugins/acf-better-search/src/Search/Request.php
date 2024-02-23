<?php

namespace AcfBetterSearch\Search;

use AcfBetterSearch\HookableInterface;

/**
 * .
 */
class Request implements HookableInterface {

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_filter( 'posts_distinct', [ $this, 'set_query_distinct' ], 10, 2 );
	}

	public function set_query_distinct( string $distinct, \WP_Query $query ): string {
		if ( ! isset( $query->query_vars['s'] ) || empty( $query->query_vars['s'] )
			|| ! apply_filters( 'acfbs_search_is_available', true, $query ) ) {
			return $distinct;
		}

		return 'DISTINCT';
	}
}
