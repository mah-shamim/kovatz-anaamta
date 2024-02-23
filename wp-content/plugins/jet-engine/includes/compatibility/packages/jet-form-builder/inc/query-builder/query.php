<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Form_Builder\Query_Builder;

use \Jet_Engine\Query_Builder\Queries\Traits\Meta_Query_Trait;

class Form_Query extends \Jet_Engine\Query_Builder\Queries\SQL_Query {

	use Meta_Query_Trait;

	/**
	 * Returns queries items
	 *
	 * @return [type] [description]
	 */
	public function _get_items() {

		$sql    = $this->build_sql_query();
		$result = $this->wpdb()->get_results( $sql );

		if ( empty( $result ) ) {
			return $result;
		}

		$with_ids = array();

		foreach ( $result as $data ) {
			$with_ids[ $data->jfb_ID ] = $data;
		}

		$fields_table = $this->get_fields_table();
		$ids_str = implode( ', ', array_keys( $with_ids ) );
		$fields = $this->wpdb()->get_results(
			"SELECT record_id, field_name, field_value FROM $fields_table WHERE record_id IN ( $ids_str )"
		);

		foreach ( $fields as $field ) {
			
			if ( ! isset( $with_ids[ $field->record_id ]->jfb_fields ) ) {
				$with_ids[ $field->record_id ]->jfb_fields = array();
			}

			$decoded = json_decode( wp_unslash( $field->field_value ), true );

			$with_ids[ $field->record_id ]->jfb_fields[ $field->field_name ] = ( NULL !== $decoded ) ? $decoded : $field->field_value;
		}

		return array_values( $with_ids );

	}

	public function get_records_table() {
		return $this->wpdb()->prefix . 'jet_fb_records';
	}

	public function get_fields_table() {
		return $this->wpdb()->prefix . 'jet_fb_records_fields';
	}

	public function add_fields_prefix( $fields = array(), $prefix = 'r' ) {
		$res = array_map( function( $row ) use ( $prefix ) {
			$row['column'] = $prefix . '.' . $row['column'];
			return $row;
		}, $fields );

		return $res;
	}

	public function set_filtered_prop( $prop = '', $value = null ) {

		switch ( $prop ) {

			case '_page':

				$page = absint( $value );

				if ( 0 < $page ) {
					$this->final_query['_page']  = $page;
				}

				break;

			case 'orderby':
			case 'order':
			case 'meta_key':

				$key = $prop;

				if ( 'orderby' === $prop ) {
					$key = 'type';
					$value = ( in_array( $value, array( 'meta_key', 'meta_value' ) ) ) ? 'CHAR' : 'DECIMAL';
				} elseif ( 'meta_key' === $prop ) {
					$key = 'orderby';
				}

				$this->set_filtered_order( $key, $value );
				break;

			case 'meta_query':
				$this->replace_meta_query_row( $value );
				break;
		}

	}

	public function build_sql_query( $is_count = false ) {

		$select_query = "SELECT ";

		if ( $is_count ) {
			$select_query .= " COUNT(r.id) ";
		} else {

			$select_query .= " r.id as jfb_ID, r.form_id AS jfb_form_id, r.user_id AS jfb_user_id, r.from_content_id AS jfb_from_content_id, r.from_content_type AS jfb_from_content_type, r.status AS jfb_status, r.referrer AS jfb_referrer, r.submit_type AS jfb_submit_type, r.created_at AS jfb_submit_date";

		}

		if ( null === $this->current_query ) {

			$prefixed_table = $this->get_records_table();
			$current_query  = "";

			$current_query .= " FROM $prefixed_table AS r ";

			$meta_where = "";

			if ( ! empty( $this->final_query['meta_query'] ) ) {
				
				$fields_table   = $this->get_fields_table();
				$current_query .= " INNER JOIN $fields_table AS rf ON r.id = rf.record_id ";
				$clauses        = array();

				foreach ( $this->final_query['meta_query'] as $row ) {
					$clauses[] = $this->add_where_args( 
						$this->add_fields_prefix( array(
							array(
								'column'  => 'field_name',
								'compare' => '=',
								'value'   => ! empty( $row['key'] ) ? $row['key'] : '',
							),
							array(
								'column'  => 'field_value',
								'compare' => ! empty( $row['compare'] ) ? $row['compare'] : '=',
								'value'   => ! empty( $row['value'] ) ? $row['value'] : '',
								'type'    => ! empty( $row['type'] ) ? $row['type'] : false,
							),
						), 'rf' ), 
						'AND', 
						false 
					);
				}

				$rel = ! empty( $this->final_query['meta_query_relation'] ) ? $this->final_query['meta_query_relation'] : 'AND';

				$meta_where = implode( ' ' . $rel . ' ', $clauses );
			}

			$where = array();

			if ( ! empty( $this->final_query['form_id'] ) ) {
				$where[] = array(
					'column'   => 'form_id',
					'compare'  => 'IN',
					'value'    => $this->final_query['form_id'],
				);
			}

			if ( ! empty( $this->final_query['user_id'] ) ) {
				$where[] = array(
					'column'   => 'user_id',
					'compare'  => '=',
					'value'    => $this->final_query['user_id'],
				);
			}

			if ( ! empty( $this->final_query['record__in'] ) ) {
				$where[] = array(
					'column'   => 'id',
					'compare'  => 'IN',
					'value'    => implode( ',', array_map( 'absint', $this->final_query['record__in'] ) ),
				);
			}

			if ( ! empty( $this->final_query['status'] ) ) {
				if ( 'success' === $this->final_query['status'] ) {
					$where[] = array(
						'column'   => 'status',
						'compare'  => '=',
						'value'    => 'success',
					);
				} else {
					$where[] = array(
						'column'   => 'status',
						'compare'  => '!=',
						'value'    => 'success',
					);
				}
				
			}

			if ( ! empty( $this->final_query['date_query'] ) ) {
				
				$date_from = ! empty( $this->final_query['date_from'] ) ? strtotime( $this->final_query['date_from'] ) : time();
				$date_to   = ! empty( $this->final_query['date_to'] ) ? strtotime( $this->final_query['date_to'] ) : time();

				switch ( $this->final_query['date_query'] ) {
					case 'after':
						$where[] = array(
							'column'   => 'created_at',
							'compare'  => '>=',
							'value'    => wp_date( 'Y-m-d H:i:s', $date_from ),
						);
						break;
					
					case 'before':
						$where[] = array(
							'column'   => 'created_at',
							'compare'  => '<=',
							'value'    => wp_date( 'Y-m-d H:i:s', $date_to ),
						);
						break;

					case 'between':
						$where[] = array(
							'column'   => 'created_at',
							'compare'  => '>=',
							'value'    => wp_date( 'Y-m-d H:i:s', $date_from ),
						);
						$where[] = array(
							'column'   => 'created_at',
							'compare'  => '<=',
							'value'    => wp_date( 'Y-m-d H:i:s', $date_to ),
						);
						break;
				}

			}

			$current_query .= "WHERE 1=1 ";

			$where_str = $this->add_where_args( $this->add_fields_prefix( $where ), 'AND', false );

			if ( $where_str ) {
				$current_query .= " AND $where_str ";
			}

			if ( $meta_where ) {
				$current_query .= " AND ( $meta_where ) ";
			}

			$this->current_query = $current_query;

		}

		$limit_offset = "";

		if ( ! $is_count ) {
			$limit = $this->get_items_per_page();
		} else {
			$limit = ! empty( $this->final_query['limit'] ) ? absint( $this->final_query['limit'] ) : 0;
		}

		if ( $limit ) {
			$limit_offset .= " LIMIT";
			$offset = ! empty( $this->final_query['offset'] ) ? absint( $this->final_query['offset'] ) : 0;

			if ( ! $is_count && ! empty( $this->final_query['_page'] ) ) {
				$page    = absint( $this->final_query['_page'] );
				$pages   = $this->get_items_pages_count();
				$_offset = ( $page - 1 ) * $this->get_items_per_page();
				$offset  = $offset + $_offset;

				// Fixed the following issue:
				// The last page has an incorrect number of posts if the `Total Query Limit` number
				// is not a multiple of the `Per Page Limit` number.
				if ( $page == $pages ) {
					$limit = $this->get_items_total_count() - $_offset;
				}
			}

			if ( $offset ) {
				$limit_offset .= " $offset, $limit";
			} else {
				$limit_offset .= " $limit";
			}
		}

		$result = apply_filters(
			'jet-engine/query-builder/build-form-records-query/result',
			$select_query . $this->current_query . $limit_offset . ";",
			$this,
			$is_count
		);

		return $result;

	}

	/**
	 * Array of arguments where string should be exploded into array
	 *
	 * @return string[]
	 */
	public function get_args_to_explode() {
		return array();
	}

}
