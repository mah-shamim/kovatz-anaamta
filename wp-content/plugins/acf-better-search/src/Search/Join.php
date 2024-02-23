<?php

namespace AcfBetterSearch\Search;

use AcfBetterSearch\HookableInterface;
use AcfBetterSearch\Settings\Options;

/**
 * .
 */
class Join implements HookableInterface {

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
		add_filter( 'posts_join', [ $this, 'sql_join' ], 10, 2 );
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

	public function sql_join( string $join, \WP_Query $query ): string {
		if ( ! isset( $query->query_vars['s'] ) || empty( $query->query_vars['s'] )
			|| ! apply_filters( 'acfbs_search_is_available', true, $query ) ) {
			return $join;
		}

		$this->load_settings();
		if ( ! $this->config[ Options::OPTION_MODE_LITE ] && ! $this->config[ Options::OPTION_FIELDS_TYPES ] ) {
			return $join;
		}

		$parts   = [];
		$parts[] = sprintf(
			'INNER JOIN %1$s AS a ON ( a.post_id = %2$s.ID )',
			$this->wpdb->postmeta,
			$this->wpdb->posts
		);
		$parts[] = sprintf(
			'LEFT JOIN %1$s AS b ON ( %2$s )',
			$this->wpdb->postmeta,
			$this->get_postmeta_conditions()
		);

		if ( $conditions = $this->get_fields_conditions() ) {
			$parts[] = sprintf(
				'LEFT JOIN %1$s AS c ON %2$s',
				$this->wpdb->posts,
				$conditions
			);
		}

		if ( $this->check_file_field_conditions() ) {
			$parts[] = sprintf(
				'LEFT JOIN %1$s AS d ON ( d.ID = a.meta_value )',
				$this->wpdb->posts
			);
		}

		$join .= ' ' . implode( ' ', $parts ) . ' ';
		return apply_filters( 'acfbs_sql_join', $join, $this->wpdb );
	}

	private function get_postmeta_conditions(): string {
		$list = [];

		$list[] = '( b.post_id = a.post_id )';
		$list[] = '( b.meta_key LIKE CONCAT( \'\_\', a.meta_key ) )';

		return '(' . implode( ') AND (', $list ) . ')';
	}

	/**
	 * @return string|null
	 */
	private function get_fields_conditions() {
		if ( $this->config[ Options::OPTION_MODE_LITE ] ) {
			return null;
		}

		$list   = [];
		$list[] = '( c.post_name = b.meta_value )';
		$list[] = '( c.post_type = \'acf-field\' )';
		$list[] = $this->get_fields_types();

		return '( ' . implode( ' AND ', $list ) . ' )';
	}

	private function get_fields_types(): string {
		if ( $this->config[ Options::OPTION_MODE_SELECTED ] ) {
			return '( c.post_content LIKE \'%s:18:"acfbs_allow_search";i:1;%\' )';
		}

		$list = [];
		foreach ( $this->config[ Options::OPTION_FIELDS_TYPES ] as $type ) {
			$list[] = '( c.post_content LIKE \'%:"' . $this->wpdb->_real_escape( $type ) . '"%\' )';
		}

		return '(' . implode( ' OR ', $list ) . ')';
	}

	private function check_file_field_conditions(): bool {
		if ( $this->config[ Options::OPTION_MODE_LITE ]
			|| $this->config[ Options::OPTION_MODE_SELECTED ]
			|| ! in_array( 'file', $this->config[ Options::OPTION_FIELDS_TYPES ] ) ) {
			return false;
		}

		return true;
	}
}
