<?php
namespace Jet_Engine\Query_Builder\Queries;

use Jet_Engine\Query_Builder\Manager;

class Comments_Query extends Base_Query {

	use Traits\Meta_Query_Trait;
	use Traits\Date_Query_Trait;

	public $current_comments_query = null;
	public $current_count_query = null;

	/**
	 * Returns queries items
	 *
	 * @return [type] [description]
	 */
	public function _get_items() {

		$current_query = $this->get_current_wp_query();
		$result        = array();

		if ( $current_query ) {
			$result = $current_query->get_comments();
		}

		return $result;

	}

	public function get_current_wp_query( $is_count = false ) {

		if ( ! $is_count && null !== $this->current_comments_query ) {
			return $this->current_comments_query;
		}

		if ( $is_count && null !== $this->current_count_query ) {
			return $this->current_count_query;
		}

		$args = $this->final_query;

		if ( $is_count ) {
			$args['count'] = true;
			$args['number'] = 0;
		}

		if ( ! empty( $args['meta_query'] ) ) {
			$args['meta_query'] = $this->prepare_meta_query_args( $args );
		}

		if ( ! empty( $args['date_query'] ) ) {
			$args['date_query'] = $this->prepare_date_query_args( $args );
		}

		if ( isset( $args['paged'] ) && empty( $args['paged'] ) ) {
			unset( $args['paged'] );
		}

		$query = new \WP_Comment_Query( $args );

		if ( $is_count ) {
			$this->current_count_query = $query;
			return $this->current_count_query;
		} else {
			$this->current_comments_query = $query;
			return $this->current_comments_query;
		}

	}

	public function get_current_items_page() {

		$query = $this->get_current_wp_query();
		$page  = ! empty( $query->query_vars['paged'] ) ? absint( $query->query_vars['paged'] ) : 1;

		return $page;

	}

	/**
	 * Returns total found items count
	 *
	 * @return [type] [description]
	 */
	public function get_items_total_count() {

		$cached = $this->get_cached_data( 'count' );

		if ( false !== $cached ) {
			return $cached;
		}

		$query = $this->get_current_wp_query( true );
		$result = $query->get_comments();


		$this->update_query_cache( $result, 'count' );

		return $result;

	}

	/**
	 * Returns count of the items visible per single listing grid loop/page
	 * @return [type] [description]
	 */
	public function get_items_per_page() {

		$query  = $this->get_current_wp_query();
		$number = ! empty( $query->query_vars['number'] ) ? absint( $query->query_vars['number'] ) : 0;

		return $number;
	}

	/**
	 * Returns queried items count per page
	 *
	 * @return [type] [description]
	 */
	public function get_items_page_count() {
		$total = $this->get_items_total_count();
		return $total;
	}

	/**
	 * Returns queried items pages count
	 *
	 * @return [type] [description]
	 */
	public function get_items_pages_count() {

		$query    = $this->get_current_wp_query();
		$per_page = $this->get_items_per_page();
		$total    = $this->get_items_total_count();

		if ( ! $per_page || ! $total ) {
			return 1;
		} else {
			return ceil( $total / $per_page );
		}

	}

	public function set_filtered_prop( $prop = '', $value = null ) {

		switch ( $prop ) {

			case '_page':
				$this->final_query['paged'] = $value;
				break;

			case 'meta_query':
				$this->replace_meta_query_row( $value );
				break;

			default:
				$this->merge_default_props( $prop, $value );
				break;
		}

	}

	/**
	 * Adds date range query arguments to given query parameters.
	 * Required to allow ech query to ensure compatibility with Dynamic Calendar
	 * 
	 * @param array $args [description]
	 */
	public function add_date_range_args( $args = array(), $dates_range = array(), $settings = array() ) {

		$group_by = $settings['group_by'];

		switch ( $group_by ) {

			case 'item_date':

				if ( isset( $args['date_query'] ) ) {
					$date_query = $args['date_query'];
				} else {
					$date_query = array();
				}

				$date_query = array_merge( $date_query, array(
					array(
						'column'    => 'comment_date',
						'after'     => date( 'Y-m-d', $dates_range['start'] ),
						'before'    => date( 'Y-m-d', $dates_range['end'] ),
						'inclusive' => true,
					),
				) );

				$args['date_query'] = $date_query;

				break;

			case 'meta_date':
				$args['meta_query'] = $this->get_dates_range_meta_query( $args, $dates_range, $settings );
				break;

		}

		return $args;

	}

	/**
	 * Array of arguments where string should be exploded into array
	 *
	 * @return [type] [description]
	 */
	public function get_args_to_explode() {
		return array(
			'type__in',
			'type__not_in',
		);
	}

	public function reset_query() {
		$this->current_comments_query = null;
		$this->current_count_query = null;
	}

}
