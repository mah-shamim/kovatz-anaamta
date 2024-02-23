<?php
namespace Jet_Engine\Modules\Custom_Content_Types;

/**
 * Define Data class
 */
class Data extends \Jet_Engine_Base_Data {

	/**
	 * Table name
	 *
	 * @var string
	 */
	public $table = 'post_types';

	/**
	 * Query arguments
	 *
	 * @var array
	 */
	public $query_args = array(
		'status' => 'content-type',
	);

	/**
	 * Store old item data before update
	 * @var array
	 */
	public $prev_item = array();

	/**
	 * Table format
	 *
	 * @var string
	 */
	public $table_format = array( '%s', '%s', '%s', '%s', '%s' );

	/**
	 * Returns blacklisted post types slugs
	 *
	 * @return array
	 */
	public function items_blacklist() {
		return array();
	}

	/**
	 * Returns blacklisted post types slugs
	 *
	 * @return array
	 */
	public function meta_blacklist() {
		return array();
	}

	/**
	 * Sanitizr post type request
	 *
	 * @return void
	 */
	public function sanitize_item_request() {

		$valid = true;

		if ( empty( $this->request['slug'] ) ) {
			$valid = false;
			$this->parent->add_notice(
				'error',
				__( 'Please set Content Type slug', 'jet-engine' )
			);
		}

		$this->request['slug']         = sanitize_title( $this->request['slug'] );
		$this->request['slug']         = str_replace( array( ' ', '-' ), '_', $this->request['slug'] );
		$this->request['args']['slug'] = $this->request['slug'];

		if ( empty( $this->request['name'] ) ) {
			$valid = false;
			$this->parent->add_notice(
				'error',
				__( 'Please set Content Type name', 'jet-engine' )
			);
		}

		if ( empty( $this->request['id'] ) && DB::custom_table_exists( $this->request['slug'] ) ) {
			$valid = false;
			$this->parent->add_notice(
				'error',
				__( 'Please change Content Type slug. Current is already in use', 'jet-engine' )
			);
		}

		/**
		 * @todo  fix validation
		 */

		return $valid;

	}

	/**
	 * Prepare post data from request to write into database
	 *
	 * @return array
	 */
	public function sanitize_item_from_request() {

		$request = $this->request;

		$result = array(
			'slug'        => '',
			'status'      => 'content-type',
			'labels'      => array(),
			'args'        => array(),
			'meta_fields' => array(),
		);

		$slug = ! empty( $request['slug'] ) ? $this->sanitize_slug( $request['slug'] ) : false;
		$name = ! empty( $request['name'] ) ? esc_html( $request['name'] ) : false;

		if ( ! $slug ) {
			return false;
		}

		$labels = null;

		$args        = array();
		$ensure_bool = array(
			'has_single',
			'create_index',
			'rest_get_enabled',
			'rest_put_enabled',
			'rest_post_enabled',
			'rest_delete_enabled',
			'hide_field_names',
		);

		foreach ( $ensure_bool as $key ) {
			$val = ! empty( $request['args'][ $key ] ) ? $request['args'][ $key ] : false;
			$args[ $key ] = filter_var( $val, FILTER_VALIDATE_BOOLEAN );
		}

		$regular_args = array(
			'name'                      => '',
			'slug'                      => '',
			'position'                  => null,
			'icon'                      => 'dashicons-list-view',
			'capability'                => 'manage_options',
			'related_post_type'         => '',
			'related_post_type_title'   => '',
			'related_post_type_content' => '',
			'rest_get_access'           => '',
			'rest_put_access'           => 'edit_posts',
			'rest_post_access'          => 'edit_posts',
			'rest_delete_access'        => 'edit_posts',
		);

		foreach ( $regular_args as $key => $default ) {
			$args[ $key ] = ! empty( $request['args'][ $key ] ) ? $request['args'][ $key ] : $default;
		}

		$meta_fields = $this->sanitize_meta_fields( $request['meta_fields'] );

		if ( ! empty( $request['args']['admin_columns'] ) ) {
			$args['admin_columns'] = $this->sanitize_admin_columns( $request['args']['admin_columns'], $meta_fields );
		}

		$result['slug']        = $slug;
		$result['labels']      = $labels;
		$result['args']        = $args;
		$result['meta_fields'] = $meta_fields;

		return $result;

	}

	public function sanitize_admin_columns( $columns = array(), $fields = array() ) {

		$service_columns = Module::instance()->manager->get_service_fields( array(
			'add_id_field' => true,
			'has_single'   => true,
		) );

		$service_keys = array();

		foreach ( $service_columns as $column ) {
			$service_keys[] = $column['name'];
		}

		foreach ( $columns as $name => $data ) {
			if ( ! $this->get_field_by_name( $name, $fields ) && ! in_array( $name, $service_keys ) ) {
				unset( $columns[ $name ] );
			} else {

				$data = wp_parse_args( $data, array(
					'enabled'     => false,
					'is_sortable' => false,
					'is_num'      => false,
				) );

				$data['enabled']     = filter_var( $data['enabled'], FILTER_VALIDATE_BOOLEAN );
				$data['is_sortable'] = filter_var( $data['is_sortable'], FILTER_VALIDATE_BOOLEAN );
				$data['is_num']      = filter_var( $data['is_num'], FILTER_VALIDATE_BOOLEAN );
				$columns[ $name ]    = $data;
			}
		}

		return $columns;

	}

	public function get_field_by_name( $field_name, $fields ) {

		foreach ( $fields as $index => $field ) {
			if ( $field['name'] === $field_name ) {
				$field['order'] = absint( $index );
				return $field;
			}
		}

		return false;

	}

	public function get_unique_name( $name = 'field', $initial = 'field', $list = array() ) {

		if ( ! in_array( $name, $list ) ) {
			return $name;
		} else {

			if ( $name === $initial ) {
				$name .= '_1';
			} else {

				$name = preg_replace_callback( '/_(\d)$/', function( $matches ) {

					if ( ! empty( $matches[1] ) ) {
						$i = intval( $matches[1] );
					}

					return '_' . $i;

				}, $name );

			}

			return $this->get_unique_name( $name, $initial, $list );
		}
	}

	/**
	 * Sanitize meta fields
	 *
	 * @param  [type] $meta_fields [description]
	 * @return [type]              [description]
	 */
	public function sanitize_meta_fields( $meta_fields ) {

		$unique_names = array();

		foreach ( $meta_fields as $index => $field ) {

			$name = ! empty( $field['name'] ) ? $field['name'] : 'field';
			$name = str_replace( '-', '_', sanitize_title( $name ) );
			$name = $this->get_unique_name( $name, $name, $unique_names );

			$meta_fields[ $index ]['name'] = $name;

			$unique_names[] = $name;

		}

		return parent::sanitize_meta_fields( $meta_fields );
	}

	public function get_item_by_id( $id ) {

		$item = $this->db->query(
			$this->table,
			array( 'id' => $id ),
			array( $this, 'filter_item_for_register' )
		);

		if ( ! empty( $item ) ) {
			return $item[0];
		} else {
			return false;
		}
	}

	/**
	 * Filter post type for register
	 *
	 * @return array
	 */
	public function filter_item_for_register( $item ) {

		$result      = array();
		$args        = maybe_unserialize( $item['args'] );
		$meta_fields = maybe_unserialize( $item['meta_fields'] );

		$result['args']        = $args;
		$result['meta_fields'] = $meta_fields;
		$result['id']          = absint( $item['id'] );

		unset( $result['labels'] );
		unset( $result['status'] );

		return $result;

	}

	/**
	 * Filter post type for edit
	 *
	 * @return array
	 */
	public function filter_item_for_edit( $item ) {

		$args        = maybe_unserialize( $item['args'] );
		$meta_fields = maybe_unserialize( $item['meta_fields'] );

		if ( empty( $args ) ) {
			$args = array();
		}

		if ( empty( $meta_fields ) ) {
			$meta_fields = array();
		}

		if ( jet_engine()->meta_boxes ) {
			$meta_fields = jet_engine()->meta_boxes->data->sanitize_repeater_fields( $meta_fields );
		}

		$item['name']        = ( ! empty( $args['name'] ) ) ? $args['name'] : '';
		$item['args']        = $args;
		$item['meta_fields'] = $meta_fields;

		return $item;
	}

	/**
	 * Returns SQL columns from meta fields
	 *
	 * @return [type] [description]
	 */
	public function get_sql_columns_from_fields( $fields = array() ) {

		$result = array();

		if ( ! is_array( $fields ) || empty( $fields ) ) {
			return $result;
		}

		$has_date             = false;
		$skip_types           = array( 'html' );
		$allowed_object_types = array( 'field', 'service_field' );

		foreach ( $fields as $field ) {

			if ( ! empty( $field['object_type'] ) && ! in_array( $field['object_type'], $allowed_object_types ) ) {
				continue;
			}

			if ( in_array( $field['type'], $skip_types ) ) {
				continue;
			}

			switch ( $field['type'] ) {

				case 'date':
				case 'time':
				case 'datetime':
				case 'datetime-local':

					if ( ! empty( $field['is_timestamp'] ) ) {
						$result[ $field['name'] ] = 'BIGINT';
					} else {
						$result[ $field['name'] ] = 'TEXT';
					}

					break;

				case 'sql-date':

					if ( ! $has_date ) {
						$result[ $field['name'] ] = 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP';
						$has_date = true;
					} else {
						$result[ $field['name'] ] = 'TIMESTAMP';
					}

					break;

				case 'number':

					$type = 'BIGINT';

					if ( ! empty( $field['step_value'] ) ) {

						$step = floatval( $field['step_value'] );
						$dec  = $step - floor( $step );

						if ( 1 > $dec && 0 < $dec ) {
							$length = strlen( str_replace( '0.', '', $dec ) );
							$m      = 10 + $length;
							$type   = 'DECIMAL(' . $m . ',' . $length . ')';

						}

					}

					$result[ $field['name'] ] = $type;
					break;

				case 'media':

					$type = 'BIGINT';

					if ( ! empty( $field['value_format'] ) && 'id' !== $field['value_format'] ) {
						$type = 'TEXT';
					}

					$result[ $field['name'] ] = $type;

					break;

				case 'wysiwyg':
				case 'textarea':
				case 'repeater':
					$result[ $field['name'] ] = 'LONGTEXT';
					break;

				default:
					$result[ $field['name'] ] = 'TEXT';
					break;
			}
		}

		return $result;

	}

	/**
	 * Returns services fields array
	 *
	 * @param  boolean $has_single [description]
	 * @return [type]              [description]
	 */
	public function get_service_fields( $args ) {
		return Module::instance()->manager->get_service_fields( $args );
	}

	public function before_item_update( $item ) {
		$this->prev_item = $this->get_item_for_edit( $item['id'] );
	}

	/**
	 * Rewrite this function in the child class to perform any actions on item update
	 */
	public function after_item_update( $item = array(), $is_new = false ) {

		$meta_fields = ! empty( $item['meta_fields'] ) ? $item['meta_fields'] : array();
		$meta_fields = array_merge(
			$meta_fields,
			$this->get_service_fields( $item['args'] )
		);

		$db = new DB( $item['slug'], $this->get_sql_columns_from_fields( $meta_fields ) );

		if ( ! $db->is_table_exists() ) {
			$db->install_table();
		}

		if ( ! $is_new ) {
			$old_fields = ! empty( $this->prev_item['meta_fields'] ) ? $this->prev_item['meta_fields'] : array();
			$new_fields = ! empty( $item['meta_fields'] ) ? $item['meta_fields'] : array();
			$old_schema = $this->get_sql_columns_from_fields( $old_fields );

			$db->adjusted_fields_map( $old_fields, $new_fields );
			$db->adjusted_fields_types( $old_schema, $old_fields, $new_fields );
			$db->adjust_fields_to_schema();
		}

	}

	/**
	 * Remove aproppriate DB table before content type deletion
	 *
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function before_item_delete( $item_id ) {

		$item   = $this->get_item_for_edit( $item_id );
		$slug   = ! empty( $item['slug'] ) ? $item['slug'] : false;
		$fields = ! empty( $item['meta_fields'] ) ? $item['meta_fields'] : array();

		if ( ! $slug ) {
			return;
		}

		$meta_fields = array_merge(
			$fields,
			$this->get_service_fields( $item['args'] )
		);

		$db = new DB( $slug, $this->get_sql_columns_from_fields( $meta_fields ) );

		if ( ! $db->is_table_exists() ) {
			return;
		}

		$db->drop_table();

	}

}
