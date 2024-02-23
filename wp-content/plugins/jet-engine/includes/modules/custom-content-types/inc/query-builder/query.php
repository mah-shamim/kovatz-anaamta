<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Query_Builder;

use Jet_Engine\Modules\Custom_Content_Types\Module;

class CCT_Query extends \Jet_Engine\Query_Builder\Queries\Base_Query {

	public $current_query = null;

	/**
	 * Returns queries items
	 *
	 * @return [type] [description]
	 */
	public function _get_items() {

		$result = array();

		$type = ! empty( $this->final_query['content_type'] ) ? $this->final_query['content_type'] : false;

		if ( ! $type ) {
			return $result;
		}

		$content_type = Module::instance()->manager->get_content_types( $type );

		if ( ! $content_type ) {
			return $result;
		}

		$order  = ! empty( $this->final_query['order'] ) ? $this->final_query['order'] : array();
		$args   = ! empty( $this->final_query['args'] ) ? $this->final_query['args'] : array();
		$offset = ! empty( $this->final_query['offset'] ) ? absint( $this->final_query['offset'] ) : 0;
		$status = ! empty( $this->final_query['status'] ) ? $this->final_query['status'] : '';
		$limit  = ! empty( $this->final_query['number'] ) ? absint( $this->final_query['number'] ) : 0;

		$flag = \OBJECT;
		$content_type->db->set_format_flag( $flag );

		if ( $status ) {
			$args[] = array(
				'field'    => 'cct_status',
				'operator' => '=',
				'value'    => $status,
			);
		}

		$content_type->db->set_query_object( $this );

		$args   = $content_type->prepare_query_args( $args );
		$result = $content_type->db->query( $args, $limit, $offset, $order );

		return $result;

	}

	/**
	 * Allows to return any query specific data that may be used by abstract 3rd parties
	 *
	 * @return [type] [description]
	 */
	public function get_query_meta() {
		$type = ! empty( $this->final_query['content_type'] ) ? $this->final_query['content_type'] : false;
		return array(
			'content_type' => $type,
		);
	}

	/**
	 * Returns currently displayed page number
	 *
	 * @return [type] [description]
	 */
	public function get_current_items_page() {

		$offset = ! empty( $this->final_query['offset'] ) ? absint( $this->final_query['offset'] ) : 0;
		$per_page = $this->get_items_per_page();

		if ( ! $offset || ! $per_page ) {
			return 1;
		} else {
			return ceil( $offset / $per_page ) + 1;
		}

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

		$result = 0;
		$type = ! empty( $this->final_query['content_type'] ) ? $this->final_query['content_type'] : false;

		if ( ! $type ) {
			return $result;
		}

		$content_type = Module::instance()->manager->get_content_types( $type );

		if ( ! $content_type ) {
			return $result;
		}

		$args   = ! empty( $this->final_query['args'] ) ? $this->final_query['args'] : array();
		$status = ! empty( $this->final_query['status'] ) ? $this->final_query['status'] : '';

		if ( $status ) {
			$args[] = array(
				'field'    => 'cct_status',
				'operator' => '=',
				'value'    => $status,
			);
		}

		$content_type->db->set_query_object( $this );

		$args   = $content_type->prepare_query_args( $args );
		$result = $content_type->db->count( $args );


		$this->update_query_cache( $result, 'count' );

		return $result;

	}

	/**
	 * Returns count of the items visible per single listing grid loop/page
	 * @return [type] [description]
	 */
	public function get_items_per_page() {

		$this->setup_query();
		$limit = 0;

		if ( ! empty( $this->final_query['number'] ) ) {
			$limit = absint( $this->final_query['number'] );
		}

		return $limit;

	}

	/**
	 * Returns queried items count per page
	 *
	 * @return [type] [description]
	 */
	public function get_items_page_count() {

		$result   = $this->get_items_total_count();
		$per_page = $this->get_items_per_page();

		if ( $per_page < $result ) {

			$page  = $this->get_current_items_page();
			$pages = $this->get_items_pages_count();

			if ( $page < $pages ) {
				$result = $per_page;
			} elseif ( $page == $pages ) {
				$offset = ! empty( $this->final_query['offset'] ) ? absint( $this->final_query['offset'] ) : 0;
				$result = $result - $offset;
			}

		}

		return $result;
	}

	/**
	 * Returns queried items pages count
	 *
	 * @return [type] [description]
	 */
	public function get_items_pages_count() {

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

				$page = absint( $value );

				if ( 0 < $page ) {
					$offset = ( $page - 1 ) * $this->get_items_per_page();
					$this->final_query['offset'] = $offset;
				}

				break;

			case 'orderby':
			case 'order':
			case 'meta_key':

				$key = $prop;

				if ( 'orderby' === $prop ) {
					$key = 'type';
					$value = ( 'meta_value' === $value ) ? 'CHAR' : 'DECIMAL';
				} elseif ( 'meta_key' === $prop ) {
					$key = 'orderby';
				}

				$this->set_filtered_order( $key, $value );
				break;

			case 'meta_query':

				foreach ( $value as $row ) {
					$this->update_args_row( $this->prepare_args_row( $row ) );
				}

				break;

			default: 
				
				$this->merge_default_props( $prop, $value );
				break;

		}

	}

	/**
	 * Prepare arguments row.
	 *
	 * @param  array $row
	 * @return array
	 */
	public function prepare_args_row( $row ) {

		if ( ! empty( $row['relation'] ) ) {

			$prepared_row = array(
				'relation' => $row['relation'],
			);

			unset( $row['relation'] );

			foreach ( $row as $inner_row ) {
				$prepared_row[] = $this->prepare_args_row( $inner_row );
			}

		} else {
			$prepared_row = array(
				'field'    => ! empty( $row['key'] ) ? $row['key'] : false,
				'operator' => ! empty( $row['compare'] ) ? $row['compare'] : '=',
				'value'    => ! empty( $row['value'] ) ? $row['value'] : '',
				'type'     => ! empty( $row['type'] ) ? $row['type'] : false,
			);
		}

		return $prepared_row;
	}

	/**
	 * Set new order from filters query
	 *
	 * @param [type] $key   [description]
	 * @param [type] $value [description]
	 */
	public function set_filtered_order( $key, $value ) {

		if ( empty( $this->final_query['order'] ) ) {
			$this->final_query['order'] = array();
		}

		if ( ! isset( $this->final_query['order']['custom'] ) ) {
			$this->final_query['order'] = array_merge( array( 'custom' => array() ), $this->final_query['order'] );
		}

		$this->final_query['order']['custom'][ $key ] = $value;

	}

	/**
	 * Update arguments row in the arguments list of the final query
	 *
	 * @param  [type] $row [description]
	 * @return [type]      [description]
	 */
	public function update_args_row( $row ) {

		if ( empty( $this->final_query['args'] ) ) {
			$this->final_query['args'] = array();
		}

		foreach ( $this->final_query['args'] as $index => $existing_row ) {
			if ( ( isset( $existing_row['field'] ) && isset( $row['field'] ) ) && $existing_row['field'] === $row['field'] && $existing_row['operator'] === $row['operator'] ) {

				if ( ! empty( $row['type'] ) && 'TIMESTAMP' === $row['type'] ) {
					$row['type']  = 'NUMERIC';
					$row['value'] = \Jet_Engine_Tools::is_valid_timestamp( $row['value'] ) ? $row['value'] : strtotime( $row['value'] );
				}

				$this->final_query['args'][ $index ] = $row;
				return;
			}
		}

		$this->final_query['args'][] = $row;

	}

	/**
	 * Adds date range query arguments to given query parameters.
	 * Required to allow ech query to ensure compatibility with Dynamic Calendar
	 * 
	 * @param array $args [description]
	 */
	public function add_date_range_args( $args = array(), $dates_range = array(), $settings = array() ) {

		$group_by = $settings['group_by'];

		if ( empty( $args['args'] ) ) {
			$args['args'] = array();
		}

		switch ( $group_by ) {

			case 'item_date':

				$args['args'][] = array(
					'field'    => 'cct_created',
					'operator' => 'BETWEEN',
					'value'    => array( date( 'Y-m-d H:i:s', $dates_range['start'] ), date( 'Y-m-d H:i:s', $dates_range['end'] ) ),
				);

				break;

			case 'meta_date':

				if ( $settings['group_by_key'] ) {
					$meta_key = esc_attr( $settings['group_by_key'] );
				}
				
				$calendar_query = array();

				if ( $meta_key ) {

					$calendar_query = array_merge( $calendar_query, array(
						array(
							'field'    => $meta_key,
							'operator' => 'BETWEEN',
							'value'    => array( $dates_range['start'], $dates_range['end'] ),
						),
					) );

				}

				if ( ! empty( $settings['allow_multiday'] ) && ! empty( $settings['end_date_key'] ) ) {

					$calendar_query = array_merge( $calendar_query, array(
						array(
							'field'    => esc_attr( $settings['end_date_key'] ),
							'value'    => array( $dates_range['start'], $dates_range['end'] ),
							'operator' => 'BETWEEN',
						),
						array(
							'relation' => 'AND',
							array(
								'field'    => $meta_key,
								'value'    => $dates_range['start'],
								'operator' => '<'
							),
							array(
								'field'    => esc_attr( $settings['end_date_key'] ),
								'value'    => $dates_range['end'],
								'operator' => '>'
							)
						),
					) );

					$calendar_query['relation'] = 'OR';

				}

				if ( 1 === count( $calendar_query ) ) {
					$args['args'][] = $calendar_query[0];
				} else {
					$args['args'][] = $calendar_query;
				}

				break;

		}

		return $args;

	}

}
