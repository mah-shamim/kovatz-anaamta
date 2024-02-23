<?php
namespace Jet_Engine\Query_Builder\Queries;

use Jet_Engine\Query_Builder\Manager;

class Repeater_Query extends Base_Query {

	public $_instance_fields = array();
	private $parent_object   = null;
	private $object_id       = null;

	use Traits\Meta_Query_Trait;

	public function maybe_setup_repeater_preview() {

		if ( ! \Jet_Engine_Listings_Preview::$is_preview ) {
			return;
		}

		$post_id = ! empty( $this->preview['post_id'] ) ? $this->preview['post_id'] : false;

		if ( ! $post_id ) {
			return;
		}

		$this->setup_current_object( get_post( $post_id ) );

		if ( ! empty( $this->preview['query_string'] ) ) {

			parse_str( $this->preview['query_string'], $query_array );

			if ( ! empty( $query_array ) ) {
				foreach ( $query_array as $key => $value ) {
					$_GET[ $key ]     = $value;
					$_REQUEST[ $key ] = $value;
				}
			}

		}

		// Update query
		$this->setup_query();

	}

	/**
	 * Returns queries items
	 *
	 * @return [type] [description]
	 */
	public function _get_items( $count = false ) {

		$this->maybe_setup_repeater_preview();

		$items     = array();
		$args      = $this->final_query;
		$object_id = ! empty( $args['object_id'] ) ? $args['object_id'] : false;

		$this->setup_current_object( $object_id );

		$current_object_id = jet_engine()->listings->data->get_current_object_id();

		switch ( $args['source'] ) {
			case 'jet_engine':

				$field = ! empty( $args['jet_engine_field'] ) ? $args['jet_engine_field'] : '';
				$field_data = explode( '::', $field );

				if ( ! empty( $field_data[1] ) ) {
					$items = jet_engine()->listings->data->get_meta( $field_data[1] );
				}

				break;

			case 'jet_engine_option':

				$field = ! empty( $args['jet_engine_option_field'] ) ? $args['jet_engine_option_field'] : '';

				if ( ! empty( $field ) ) {
					$items = jet_engine()->listings->data->get_option( $field );
				}

				$current_object_id = 0;

				break;

			case 'custom':

				$field = ! empty( $args['custom_field'] ) ? $args['custom_field'] : '';

				if ( $field ) {

					$items = jet_engine()->listings->data->get_meta( $field );

					if ( ! empty( $items ) && ! is_array( $items ) ) {
						$items = json_decode( $items );
					}

					$items = wp_unslash( $items );
				}

				$current_object_id = jet_engine()->listings->data->get_current_object_id();

				break;

			default:

				$items = apply_filters(
					'jet-engine/query-builder/types/repeater-query/items/' . $args['source'],
					$items, $args, $this
				);

				break;
		}

		$this->reset_current_object( $object_id );

		if ( empty( $items ) || ! is_array( $items ) ) {
			$items = array();
		}

		if ( ! class_exists( '\Jet_Engine_Queried_Repeater_Item' ) ) {
			require_once jet_engine()->plugin_path( 'includes/classes/repeater-item.php' );
		}

		if ( ! empty( $args['meta_query'] ) ) {
			$args['meta_query'] = $this->prepare_meta_query_args( $args );
		}

		$items = array_values( $items );
		$items = array_filter( array_map( function( $item, $index ) use ( $args, $current_object_id ) {

			$item = (object) $item;

			if ( ! $this->is_item_met_requested_args( $item, $args ) ) {
				return false;
			}

			return new \Jet_Engine_Queried_Repeater_Item( $item, $index, $current_object_id, $this->id );

		}, $items, array_keys( $items ) ) );

		// Pagination
		$limit  = $this->get_items_per_page();
		$offset = ! empty( $this->final_query['offset'] ) ? absint( $this->final_query['offset'] ) : 0;

		if ( ! $count ) {
			$page   = $this->get_current_items_page();
			$offset = $offset + ( $page - 1 ) * $limit;
		}

		$limit = ( ! empty( $limit ) && ! $count ) ? $limit : null;

		$items = array_slice( $items, $offset, $limit );

		if ( empty( $items ) ) {
			$items = array();
		}

		return array_values( $items );
	}

	public function is_item_met_requested_args( $item, $args ) {

		$result = true;

		if ( empty( $args['meta_query'] ) ) {
			return $result;
		}

		$meta_query = $args['meta_query'];
		$relation   = ! empty( $meta_query['relation'] ) ? strtolower( $meta_query['relation'] ) : 'and';

		unset( $meta_query['relation'] );

		if ( empty( $meta_query ) ) {
			return $result;
		}

		$result = 'or' !== $relation;

		foreach ( $meta_query as $clause ) {

			if ( ! empty( $clause['relation'] ) ) {

				$matched = $this->is_item_met_requested_args( $item,
					array(
						'meta_query' => $clause,
					)
				);

			} else {

				$key = ! empty( $clause['key'] ) ? $clause['key'] : '';

				if ( ! $key ) {
					continue;
				}

				$compare = ! empty( $clause['compare'] ) ? $clause['compare'] : '=';
				$value   = ! \Jet_Engine_Tools::is_empty( $clause['value'] ) ? $clause['value'] : '';

				$matched = false;

				switch ( $compare ) {
					case '=':
						if ( isset( $item->$key ) ) {
							$matched = $item->$key == $value;
						}
						break;

					case '!=' :
						if ( isset( $item->$key ) ) {
							$matched = $item->$key != $value;
						} else {
							$matched = true;
						}
						break;

					case '>':
						if ( isset( $item->$key ) ) {
							$matched = $item->$key > $value;
						}
						break;

					case '>=':
						if ( isset( $item->$key ) ) {
							$matched = $item->$key >= $value;
						}
						break;

					case '<':
						if ( isset( $item->$key ) ) {
							$matched = $item->$key < $value;
						}
						break;

					case '<=':
						if ( isset( $item->$key ) ) {
							$matched = $item->$key <= $value;
						}
						break;

					case 'LIKE':
						if ( isset( $item->$key ) ) {

							if ( is_array( $item->$key ) ) { // for Checkbox, Select2 ... fields
								$matched = in_array( $value, $item->$key );
							} else {
								$matched = false !== strpos( $item->$key, $value );
							}
						}
						break;

					case 'NOT LIKE':
						if ( isset( $item->$key ) ) {

							if ( is_array( $item->$key ) ) { // for Checkbox, Select2 ... fields
								$matched = ! in_array( $value, $item->$key );
							} else {
								$matched = false === strpos( $item->$key, $value );
							}

						} else {
							$matched = true;
						}
						break;

					case 'IN':
						if ( isset( $item->$key ) ) {
							$value   = $this->value_to_array( $value );
							$matched = in_array( $item->$key, $value );
						}
						break;

					case 'NOT IN':
						if ( isset( $item->$key ) ) {
							$value   = $this->value_to_array( $value );
							$matched = ! in_array( $item->$key, $value );
						} else {
							$matched = true;
						}
						break;

					case 'BETWEEN':
						if ( isset( $item->$key ) ) {
							$value = $this->value_to_array( $value );
							if ( isset( $value[1] ) ) {
								$matched = ( $value[0] <= $item->$key && $item->$key <= $value[1] );
							}
						}
						break;

					case 'NOT BETWEEN':
						if ( isset( $item->$key ) ) {
							$value = $this->value_to_array( $value );
							if ( isset( $value[1] ) ) {
								$matched = ( $value[0] > $item->$key || $item->$key > $value[1] );
							}
						} else {
							$matched = true;
						}

						break;

					case 'EXISTS':
						$matched = ! empty( $item->$key );
						break;

					case 'NOT EXISTS':
						$matched = empty( $item->$key );
						break;

					case 'REGEXP':

						if ( isset( $item->$key ) ) {
							$subject = $item->$key;

							if ( is_array( $item->$key ) ) {
								// Serialize item value if filtered by checkbox
								if ( false !== strpos( $value, ';s:4:"true"' ) ) {
									$subject = maybe_serialize( $item->$key );
								} else {
									$subject = json_encode( $item->$key );
								}
							}

							$value = trim( $value, '/' );
							preg_match( '/' . $value . '/', $subject, $matched );
							$matched = ! empty( $matched );
						}

						break;

					case 'NOT REGEXP':

						if ( isset( $item->$key ) ) {

							if ( is_array( $item->$key ) ) {
								$item->$key = json_encode( $item->$key );
							}

							$value = trim( $value, '/' );
							preg_match( '/' . $value . '/', $item->$key, $matched );
							$matched = empty( $matched );
						} else {
							$matched = true;
						}

						break;

				}

			}

			if ( 'or' === $relation && $matched ) {
				$result = true;
			} elseif ( 'or' !== $relation && ! $matched ) {
				$result = false;
			}

		}

		return $result;

	}

	public function value_to_array( $value ) {

		if ( ! is_array( $value ) ) {
			$value = explode( ',', $value );
			$value = array_map( 'trim', $value );
		}

		return $value;
	}

	public function setup_current_object( $object ) {

		if ( ! $object ) {
			// Added to clear a query cache if a repeater listing is inside another listing.
			$this->apply_macros( '%current_id%' );
			return;
		}

		if ( ! is_object( $object ) ) {
			$this->object_id = absint( $object );
			add_filter( 'jet-engine/listing/current-object-id', array( $this, 'replace_object_id' ) );

			if ( ! empty( $this->final_query['source'] )
				 && 'jet_engine' === $this->final_query['source']
				 && ! empty( $this->final_query['jet_engine_field'] )
			) {
				$field_data = explode( '::', $this->final_query['jet_engine_field'] );

				if ( ! empty( $field_data[0] ) ) {

					if ( taxonomy_exists( $field_data[0] ) ) {
						$object = get_term( $object );
					} else {
						$object = get_post( $object );
					}

				}
			}

			if ( ! is_object( $object )  ) {
				$object = get_post( $object );
			}

			$object = apply_filters( 'jet-engine/query-builder/repeater-query/object-by-id', $object );
		}

		if ( is_object( $object ) ) {
			$this->parent_object = jet_engine()->listings->data->get_current_object();
			jet_engine()->listings->data->set_current_object( $object );
		}

	}

	public function reset_current_object( $object ) {

		if ( ! $object ) {
			return;
		}

		if ( $this->object_id ) {
			$this->object_id = null;
			remove_filter( 'jet-engine/listing/current-object-id', array( $this, 'replace_object_id' ) );
		}

		if ( $this->parent_object ) {
			jet_engine()->listings->data->set_current_object( $this->parent_object );
			$this->parent_object = null;
		}

	}

	public function replace_object_id( $object_id ) {

		if ( $this->object_id ) {
			return $this->object_id;
		}

		return $object_id;

	}

	public function get_current_items_page() {

		if ( empty( $this->final_query['page'] ) ) {
			return 1;
		}

		return absint( $this->final_query['page'] );
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

		$this->setup_query();

		$items  = $this->_get_items( true );
		$result = count( $items );

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

		if ( ! empty( $this->final_query['per_page'] ) ) {
			$limit = absint( $this->final_query['per_page'] );
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
				$offset = ( $page - 1 ) * $per_page;
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
		}

		return ceil( $total / $per_page );
	}

	/**
	 * Get fields list are available for the current instance of this query
	 *
	 * @return [type] [description]
	 */
	public function get_instance_fields() {

		if ( ! empty( $this->_instance_fields ) ) {
			return $this->_instance_fields;
		}

		$result = array();
		$args = $this->query;

		if ( empty( $args['source'] ) ) {
			return $result;
		}

		switch ( $args['source'] ) {

			case 'jet_engine':

				$field = ! empty( $args['jet_engine_field'] ) ? $args['jet_engine_field'] : '';
				$field_data = explode( '::', $field );

				if ( ! empty( $field_data[1] ) ) {
					$fields = jet_engine()->meta_boxes->get_meta_fields_for_object( $field_data[0] );
					$result = $this->get_options_from_fields_data( $field_data[1], $fields );
				}

				break;

			case 'jet_engine_option':
				$field = ! empty( $args['jet_engine_option_field'] ) ? $args['jet_engine_option_field'] : '';
				$field_data = explode( '::', $field );

				if ( ! empty( $field_data[1] ) ) {
					$page = isset( jet_engine()->options_pages->registered_pages[ $field_data[0] ] ) ? jet_engine()->options_pages->registered_pages[ $field_data[0] ] : false;

					if ( $page ) {
						$result = $this->get_options_from_fields_data( $field_data[1], $page->meta_box );
					}

				}

				break;

			case 'custom':
				$fields_list = ! empty( $args['fields_list'] ) ? $args['fields_list'] : '';
				$fields_list = explode( ',', str_replace( ', ', ',', $fields_list ) );
				$result      = array_combine( $fields_list, $fields_list );
				break;

			default:
				$result = apply_filters(
					'jet-engine/query-builder/types/repeater-query/fields/' . $args['source'],
					$result, $args, $this
				);
				break;
		}

		$this->_instance_fields = $result;

		return $result;

	}

	public function get_options_from_fields_data( $search_field, $all_fields ) {

		$field_settings = $this->find_field( $search_field, $all_fields );
		$result         = array();

		$fields = ! empty( $field_settings['repeater-fields'] ) ? $field_settings['repeater-fields'] : false;
		$fields = ! $fields && ! empty( $field_settings['fields'] ) ? $field_settings['fields'] : $fields;

		if ( ! $fields ) {
			return $result;
		}

		foreach ( $fields as $field ) {

			$label = ! empty( $field['title'] ) ? $field['title'] : false;
			$label = ! $label && ! empty( $field['label'] ) ? $field['label'] : $field['name'];
			$result[ $field['name'] ] = $label;
		}

		return $result;

	}

	public function find_field( $name, $fields ) {

		foreach ( $fields as $field ) {
			if ( isset( $field['name'] ) && $name === $field['name'] ) {
				return $field;
			}
		}

		return array();
	}

	public function set_filtered_prop( $prop = '', $value = null ) {

		switch ( $prop ) {

			case '_page':
				$this->final_query['page'] = $value;
				break;

			case 'meta_query':
				$this->replace_meta_query_row( $value );
				break;

			default:
				$this->merge_default_props( $prop, $value );
				break;
		}

	}

}
