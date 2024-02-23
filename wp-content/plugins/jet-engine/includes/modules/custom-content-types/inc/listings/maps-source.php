<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Listings;

use Jet_Engine\Modules\Custom_Content_Types\Module;

class CCT_Maps_Source extends \Jet_Engine\Modules\Maps_Listings\Source\Base {

	/**
	 * Returns source ID
	 *
	 * @return string
	 */
	public function get_id() {
		return Module::instance()->listings->source;
	}

	public function get_obj_by_id( $id ) {

		$listing = jet_engine()->listings->data->get_listing();
		$type    = false;

		if ( 'query' === $listing->get_settings( 'listing_source' ) ) {
			$query_id = $listing->get_settings( '_query_id' );
			$query    = \Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $query_id );

			if ( $query ) {
				$type = ! empty( $query->query['content_type'] ) ? $query->query['content_type'] : false;
			}

		} else {
			$type = jet_engine()->listings->data->get_listing_post_type();
		}

		if ( ! $type ) {
			return null;
		}

		$content_type = Module::instance()->manager->get_content_types( $type );

		if ( ! $content_type ) {
			return null;
		}

		$flag = \OBJECT;
		$content_type->db->set_format_flag( $flag );

		return $content_type->db->get_item( $id );
	}

	public function delete_field_value( $obj, $field ) {
		// keep old data for better backward compatibility
		return;
	}

	public function get_field_value( $obj, $field ) {

		if ( is_object( $obj ) ) {
			$obj = get_object_vars( $obj );
		}

		if ( ! isset( $obj['cct_slug'] ) ) {
			return '';
		}

		if ( ! isset( $obj[ $field ] ) ) {
			return '';
		}

		return $obj[ $field ];
	}

	/**
	 * Returns coordinates data based on multiple fields (support preloaded values and map control)
	 * 
	 * @param  [type]
	 * @param  string
	 * @param  [type]
	 * @return [type]
	 */
	public function get_field_coordinates( $obj, $location_string = '', $field_name = null ) {

		if ( ! $field_name ) {
			$field_name = $this->lat_lng->meta_key;
		}

		$field_name = str_replace( '+', '_', $field_name );

		$location_hash = $this->get_field_value( $obj, $field_name . '_hash' );

		// Try to get legacy preloaded data and update it
		if ( ! $location_hash ) {
			
			$legacy_data = $this->get_field_value( $obj, $this->lat_lng->meta_key );

			if ( ! empty( $legacy_data ) ) {
				$location_hash = $legacy_data['key'];
				$this->update_field_value( $obj, $field_name, $legacy_data );
				$this->delete_field_value( $obj, $this->lat_lng->meta_key );
			}
			
		}

		if ( ! $location_hash ) {
			return;
		}

		return array(
			'key'   => $location_hash,
			'coord' => array(
				'lat' => $this->get_field_value( $obj, $field_name . '_lat' ),
				'lng' => $this->get_field_value( $obj, $field_name . '_lng' ),
			),
		);
		
	}

	public function update_field_value( $obj, $field, $value ) {

		if ( ! isset( $obj->cct_slug ) || ! isset( $obj->_ID ) ) {
			return;
		}

		$field = str_replace( '+', '_', $field );

		$content_type = Module::instance()->manager->get_content_types( $obj->cct_slug );

		if ( ! $content_type ) {
			return;
		}

		$hash_col = $field . '_hash';
		$lat_col  = $field . '_lat';
		$lng_col  = $field . '_lng';

		if ( ! $content_type->db->column_exists( $hash_col ) ) {
			$content_type->db->insert_table_columns( array( $hash_col => 'text' ) );
		}

		if ( ! $content_type->db->column_exists( $lat_col ) ) {
			$content_type->db->insert_table_columns( array( $lat_col => 'text' ) );
		}

		if ( ! $content_type->db->column_exists( $lng_col ) ) {
			$content_type->db->insert_table_columns( array( $lng_col => 'text' ) );
		}

		$content_type->db->update( array( 
			$hash_col => $value['key'],
			$lat_col  => $value['coord']['lat'],
			$lng_col  => $value['coord']['lng'],
		), array( '_ID' => $obj->_ID ) );

	}

	public function get_failure_key( $obj ) {

		if ( ! isset( $obj->cct_slug ) || ! isset( $obj->_ID ) ) {
			return '';
		}

		return sprintf( 'CCT(%1$s) #%2$s', $obj->cct_slug, $obj->_ID );
	}

	public function add_preload_hooks( $preload_fields ) {

		foreach ( $preload_fields as $field ) {

			$fields = explode( '+', $field );
			$fields = array_map( function ( $field_item ) {
				return str_replace( 'cct::', '', $field_item );
			}, $fields );

			$field_data = explode( '__', $fields[0] );

			$type = $field_data[0];

			$fields = array_map( function ( $field_item ) use ( $type ) {
				return str_replace( $type . '__', '', $field_item );
			}, $fields );

			add_action( 'jet-engine/custom-content-types/updated-item/' . $type, function ( $item, $prev_item, $handler ) use ( $fields ) {

				if ( empty( $item['_ID'] ) ) {
					return;
				}

				$cct_item = (object) $handler->get_factory()->db->get_item( $item['_ID'] );

				$this->lat_lng->set_current_source( $this->get_id() );
				$address = $this->lat_lng->get_address_from_fields_group( $cct_item, $fields );
				

				if ( ! $address ) {
					return;
				}

				// get remote or local data (if location the same)
				$coord = $this->lat_lng->get( $cct_item, $address );

				if ( $coord ) {
					
					$field = implode( '+', $fields );

					// write this data into appropriate service columns for current field
					$this->update_field_value( $cct_item, $field, array(
						'key'   => md5( $address ),
						'coord' => $coord,
					) );
				}

			}, 10, 3 );

			// Prevent deletions address columns after updated CCT.
			$col_prefix = implode( '_', $fields );

			$cols = array(
				$col_prefix . '_hash',
				$col_prefix . '_lat',
				$col_prefix . '_lng'
			);

			add_filter( 'jet-engine/custom-content-types/db/exclude-fields', function ( $exclude ) use ( $cols ) {
				return array_merge( $exclude, $cols );
			} );

		}
	}

	public function filtered_preload_fields( $field ) {
		return false !== strpos( $field, 'cct::' );
	}

}
