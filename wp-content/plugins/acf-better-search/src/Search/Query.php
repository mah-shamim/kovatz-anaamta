<?php

namespace AcfBetterSearch\Search;

use AcfBetterSearch\HookableInterface;

/**
 * .
 */
class Query implements HookableInterface {

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_filter( 'pre_get_posts', [ $this, 'query_args' ] );
	}

	public function query_args( \WP_Query $query ): \WP_Query {
		if ( ! isset( $query->query_vars['s'] ) || ( $query->query_vars['s'] === '' ) ) {
			return $query;
		}

		$query->query_vars['suppress_filters'] = false;
		return $query;
	}
}
