<?php

namespace AcfBetterSearch\Search;

use AcfBetterSearch\HookableInterface;
use AcfBetterSearch\Settings\Options;

/**
 * .
 */
class Where implements HookableInterface {

	/**
	 * @var \wpdb
	 */
	private $wpdb = null;

	/**
	 * @var mixed[]
	 */
	private $config = null;

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_filter( 'posts_search', [ $this, 'sql_where' ], 0, 2 );
	}

	/**
	 * @return void
	 */
	private function load_settings() {
		if ( ( $this->wpdb !== null ) && ( $this->config !== null ) ) {
			return;
		}

		global $wpdb;
		$this->wpdb   = $wpdb;
		$this->config = apply_filters( 'acfbs_config', [] );
	}

	public function sql_where( string $where, \WP_Query $query ): string {
		if ( ! isset( $query->query_vars['s'] ) || empty( $query->query_vars['s'] )
			|| ! apply_filters( 'acfbs_search_is_available', true, $query ) ) {
			return $where;
		}

		$this->load_settings();

		$list   = [];
		$list[] = $this->get_acf_conditions( $query->query_vars['s'] );
		$list[] = $this->get_default_wordpress_conditions( $query->query_vars['s'] );

		if ( in_array( 'file', $this->config[ Options::OPTION_FIELDS_TYPES ] ) ) {
			$list[] = $this->get_file_conditions( $query->query_vars['s'] );
		}

		$where = ' AND ( ' . implode( ' OR ', array_filter( $list ) ) . ' ) ';
		return apply_filters( 'acfbs_sql_where', $where, $this->wpdb );
	}

	private function get_acf_conditions( string $words ): string {
		if ( ! $this->config[ Options::OPTION_FIELDS_TYPES ] && ! $this->config[ Options::OPTION_MODE_LITE ] ) {
			return '( 1 = 2 )';
		}

		$words = ! $this->config[ Options::OPTION_WHOLE_PHRASES ] ? explode( ' ', $words ) : [ $words ];
		$list  = [];

		foreach ( $words as $word ) {
			$list[] = 'a.meta_value ' . $this->get_phrase_regex( $word );
		}

		return sprintf(
			'( ( b.meta_id IS NOT NULL ) %1$s AND ( %2$s ) )',
			( ! $this->config[ Options::OPTION_MODE_LITE ] ) ? 'AND ( c.ID IS NOT NULL )' : '',
			implode( ' ) AND ( ', $list )
		);
	}

	private function get_default_wordpress_conditions( string $words ): string {
		$words   = ! $this->config[ Options::OPTION_WHOLE_PHRASES ] ? explode( ' ', $words ) : [ $words ];
		$columns = apply_filters( 'acfbs_search_post_object_fields', [ 'post_title', 'post_content', 'post_excerpt' ] );
		if ( ! $columns ) {
			return '';
		}

		$list = [];
		foreach ( $words as $word ) {
			$conditions = [];

			foreach ( $columns as $column ) {
				$conditions[] = sprintf(
					'( %s.%s %s )',
					$this->wpdb->posts,
					$column,
					$this->get_phrase_regex( $word )
				);
			}

			$list[] = '( ' . implode( ' OR ', $conditions ) . ' )';
		}

		if ( count( $list ) > 1 ) {
			return '( ' . implode( ' AND ', $list ) . ' )';
		} else {
			return $list[0];
		}
	}

	private function get_file_conditions( string $words ): string {
		$words = ! $this->config[ Options::OPTION_WHOLE_PHRASES ] ? explode( ' ', $words ) : [ $words ];
		$list  = [];

		foreach ( $words as $word ) {
			$list[] = 'd.post_title ' . $this->get_phrase_regex( $word );
		}

		return '( ' . implode( ' ) AND ( ', $list ) . ' )';
	}

	private function get_phrase_regex( string $phrase ): string {
		if ( $this->config[ Options::OPTION_WHOLE_WORDS ] && $this->config[ Options::OPTION_REGEX_SPENCER ] ) {
			return 'REGEXP \'\\\\b' . $this->wpdb->_real_escape( $phrase ) . '\\\\b\'';
		} elseif ( $this->config[ Options::OPTION_WHOLE_WORDS ] ) {
			return 'REGEXP \'[[:<:]]' . $this->wpdb->_real_escape( $phrase ) . '[[:>:]]\'';
		} else {
			return 'LIKE \'%' . $this->wpdb->_real_escape( $phrase ) . '%\'';
		}
	}
}
