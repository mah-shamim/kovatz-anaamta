<?php
namespace Jet_Engine\Modules\Custom_Content_Types;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Factory class
 */
class Factory {

	public $args    = array();
	public $fields  = array();
	public $db      = null;
	public $page    = null;
	public $type_id = null;

	public $admin_pages         = null;
	public $_admin_columns      = null;
	public $_quick_edit_columns = null;
	private $_formatted_fields  = null;

	public function __construct( $args = array(), $fields = array(), $type_id = 0 ) {

		$fields = array_merge(
			$fields,
			Module::instance()->manager->data->get_service_fields( $args )
		);

		$sql_fields = Module::instance()->manager->data->get_sql_columns_from_fields( $fields );

		$this->db      = new DB( $args['slug'], $sql_fields );
		$this->args    = $args;
		$this->fields  = apply_filters( 'jet-engine/custom-content-types/factory/raw-fields', $fields, $this );
		$this->type_id = $type_id;

		if ( is_admin() ) {
			$init_priority = Module::instance()->manager->init_priority + 1;
			add_action( 'init', array( $this, 'init_pages' ), $init_priority );
		}

		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );

		new Single_Item_Factory( $this );

	}

	public function init_pages() {
		$this->admin_pages = new Type_Pages( $this );
		$this->admin_pages->init();
	}

	/**
	 * Register REST API endpoints if enabled
	 *
	 * @return [type] [description]
	 */
	public function register_endpoints() {

		$get    = $this->get_arg( 'rest_get_enabled' );
		$create = $this->get_arg( 'rest_put_enabled' );
		$edit   = $this->get_arg( 'rest_post_enabled' );
		$delete = $this->get_arg( 'rest_delete_enabled' );

		if ( $get || $create || $edit || $delete ) {
			Module::instance()->rest_controller->register_routes( array(
				'slug'   => $this->get_arg( 'slug' ),
				'get'    => $get,
				'create' => $create,
				'edit'   => $edit,
				'delete' => $delete,
			) );
		}

	}

	/**
	 * Check if user is enabled to perform actions with current content type
	 *
	 * @return [type] [description]
	 */
	public function user_has_access() {
		return apply_filters(
			'jet-engine/custom-content-types/user-has-access',
			current_user_can( $this->user_cap() ),
			$this
		);
	}

	public function user_cap() {

		$cap = $this->get_arg( 'capability', 'manage_options' );

		if ( ! $cap ) {
			$cap = 'manage_options';
		}

		return apply_filters( 'jet-engine/custom-content-types/user-capability', $cap );

	}

	/**
	 * Prepare query arguments
	 *
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	public function prepare_query_args( $args ) {
		$args       = wp_unslash( $args );
		$prepared   = array();
		$all_fields = $this->get_formatted_fields();

		foreach ( $args as $key => $arg ) {

			if ( ! empty( $arg['relation'] ) ) {
				$prepared[ $key ] = $arg;
				continue;
			}

			$field_name = ( is_array( $arg ) && ! empty( $arg['field'] ) ) ? $arg['field'] : false;

			if ( ! isset( $all_fields[ $field_name ] ) ) {
				continue;
			}

			if ( is_array( $arg ) && ! empty( $arg['field'] ) ) {

				$field_data = isset( $all_fields[ $arg['field'] ] ) ? $all_fields[ $arg['field'] ] : false;

				if ( $field_data ) {

					$type = isset( $arg['type'] ) ? $arg['type'] : 'auto';

					// Adjust default WP meta types to CCT
					switch ( $type ) {
						case 'NUMERIC':
							$type = 'integer';
							break;

						case 'DECIMAL':
							$type = 'float';
							break;

						case 'DATETIME':
						case 'DATE':
							$type = 'timestamp';
							break;
					}

					if ( ! $type || 'auto' === $type ) {
						$type = $field_data['sql_type'];
					}

					$arg['type'] = $type;

				}

				$operator = ! empty( $arg['operator'] ) ? $arg['operator'] : '=';
				$value    = ! empty( $arg['value'] ) ? $arg['value'] : '';

				if ( ! is_array( $value ) ) {
					$value = preg_split( '/(?<=[^,]),[\s]?/', $value );
				}

				array_walk( $value, function( &$item ) {
					$item = str_replace( ',,', ',', $item );
				});

				if ( 1 === count( $value ) ) {
					$value = $value[0];
				}

				$arg['value'] = $value;

			}

			$prepared[ $key ] = $arg;

		}

		return apply_filters( 'jet-engine/custom-content-types/prepared-query-args', $prepared, $args, $this );

	}

	/**
	 * Returns handler instance
	 *
	 * @param  [type] $action_key   [description]
	 * @param  array  $actions_list [description]
	 * @return [type]               [description]
	 */
	public function get_item_handler( $action_key = false, $actions_list = array() ) {

		if ( ! class_exists( '\Jet_Engine\Modules\Custom_Content_Types\Item_Handler' ) ) {
			require Module::instance()->module_path( 'item-handler.php' );
		}

		return new Item_Handler( $action_key, $actions_list, $this );

	}

	/**
	 * Returns formatted fields list
	 * @return [type] [description]
	 */
	public function get_formatted_fields() {

		if ( null === $this->_formatted_fields ) {

			$formatted_fields = array();
			$default          = array( array(
				'title'       => __( 'Item ID', 'jet-engine' ),
				'name'        => '_ID',
				'object_type' => 'field',
				'width'       => '100%',
				'type'        => 'number',
				'isNested'    => false,
				'is_required' => true,
			) );

			$all_fields           = array_merge( $default, $this->fields );
			$skip_types           = array( 'html' );
			$allowed_object_types = array( 'field', 'service_field' );

			foreach ( $all_fields as $field ) {

				if ( ! empty( $field['object_type'] ) && ! in_array( $field['object_type'], $allowed_object_types ) ) {
					continue;
				}

				if ( in_array( $field['type'], $skip_types ) ) {
					continue;
				}

				switch ( $field['type'] ) {

					case 'datetime':
					case 'datetime-local':

						if ( ! empty( $field['is_timestamp'] ) ) {
							$field['sql_type'] = 'timestamp';
						} else {
							$field['sql_type'] = 'date';
						}

						break;

					case 'date':
					case 'time':

						if ( ! empty( $field['is_timestamp'] ) ) {
							$field['sql_type'] = 'timestamp';
						} else {
							$field['sql_type'] = false;
						}

						break;

					case 'number':

						$sql_type = 'integer';

						if ( ! empty( $field['step_value'] ) ) {
							$step = floatval( $field['step_value'] );
							$dec  = $step - floor( $step );

							if ( $dec ) {
								$sql_type = 'float';
							}

						}

						$field['sql_type'] = $sql_type;
						break;

					case 'media':

						$sql_type = 'integer';

						if ( ! empty( $field['value_format'] ) && 'id' !== $field['value_format'] ) {
							$sql_type = false;
						}

						$field['sql_type'] = $sql_type;
						break;

					default:
						$field['sql_type'] = false;
						break;
				}

				$formatted_fields[ $field['name'] ] = $field;

			}

			$this->_formatted_fields = $formatted_fields;

		}

		return $this->_formatted_fields;
	}

	public function format_value_by_type( $field = null, $value = null ) {

		if ( ! $field ) {
			return $value;
		}

		$all_fields = $this->get_formatted_fields();

		if ( empty( $all_fields[ $field ] ) ) {
			return $value;
		}

		$data = $all_fields[ $field ];

		switch ( $data['type'] ) {

			case 'date':
				if ( ! empty( $data['is_timestamp'] ) ) {
					$format = get_option( 'date_format' );
					$value  = date_i18n( $format, $value );
				}
				break;

			case 'time':
				if ( ! empty( $data['is_timestamp'] ) ) {
					$format = get_option( 'time_format' );
					$value  = date_i18n( $format, $value );
				}
				break;

			case 'datetime':
			case 'datetime-local':
				if ( ! empty( $data['is_timestamp'] ) ) {
					$format = get_option( 'date_format' ) . ', ' . get_option( 'time_format' );
					$value  = date_i18n( $format, $value );
				}
				break;

			case 'checkbox':

				if ( ! empty( $data['is_array'] )  ) {
					$value = implode( ', ', $value );
				} else {

					$value = array_filter( $value, function( $value_part ) {
						return filter_var( $value_part, FILTER_VALIDATE_BOOLEAN );
					} );

					if ( $value ) {
						$value = implode( ', ', array_keys( $value ) );
					}

				}

				break;

			case 'media':

				if ( is_numeric( $value ) ) {
					$value = wp_get_attachment_url( $value );
				} elseif ( is_array( $value ) && isset( $value['url'] ) ) {
					$value = $value['url'];
				}

				break;

			case 'gallery':

				if ( empty( $data['value_format'] ) || 'url' !== $data['value_format'] ) {

					if ( ! is_array( $value ) ) {
						$value = explode( ',', $value );
					}

					$value = array_map( function ( $item ) {

						if ( is_numeric( $item ) ) {
							$item = wp_get_attachment_url( $item );
						} elseif ( is_array( $item ) && isset( $item['url'] ) ) {
							$item = $item['url'];
						}

						return $item;

					}, $value );

					$value = implode( ',', $value );

				}

				break;

		}

		return $value;

	}

	/**
	 * Returns registered fields list
	 * @return [type] [description]
	 */
	public function get_fields_list( $context = 'plain', $where = 'elementor' ) {

		$fields        = $this->get_formatted_fields();
		$result        = array();
		$blocks_result = array();

		foreach ( $fields as $name => $field_data ) {

			$title = ! empty( $field_data['title'] ) ? $field_data['title'] : $name;

			if ( 'html' === $field_data['type'] ) {
				continue;
			}

			switch ( $context ) {

				case 'all':

					$result[ $name ] = $title;
					$blocks_result[] = array(
						'value' => $name,
						'label' => $title,
					);

					break;

				case 'plain':

					if ( 'repeater' !== $field_data['type'] ) {
						$result[ $name ] = $title;
						$blocks_result[] = array(
							'value' => $name,
							'label' => $title,
						);

					}

					break;

				case 'repeater':

					if ( 'repeater' === $field_data['type'] ) {
						$result[ $name ] = $title;
						$blocks_result[] = array(
							'value' => $name,
							'label' => $title,
						);
					}

					break;

				case 'media':

					if ( 'media' === $field_data['type'] ) {
						$result[ $name ] = $title;
						$blocks_result[] = array(
							'value' => $name,
							'label' => $title,
						);

					}

					break;

				case 'gallery':

					if ( 'gallery' === $field_data['type'] ) {
						$result[ $name ] = $title;
						$blocks_result[] = array(
							'value' => $name,
							'label' => $title,
						);
					}

					break;

				case 'text':

					$text_types = array(
						'text',
						'textarea',
						'wysiwyg',
						'radio',
						'select',
					);

					if ( in_array( $field_data['type'], $text_types ) ) {
						$result[ $name ] = $title;
						$blocks_result[] = array(
							'value' => $name,
							'label' => $title,
						);
					}

					break;

				case 'custom':

					$service_columns = Module::instance()->manager->get_service_fields( array(
						'add_id_field' => true,
						'has_single'   => $this->get_arg( 'has_single' ),
					) );

					$service_fields = array();

					foreach ( $service_columns as $index => $s_column ) {
						$service_fields[] = $s_column['name'];
					}

					if ( ! in_array( $field_data['name'], $service_fields ) ) {
						$result[ $name ] = $title;
						$blocks_result[] = array(
							'value' => $name,
							'label' => $title,
						);
					}

					break;

			}
		}

		if ( 'blocks' === $where ) {
			return $blocks_result;
		} else {
			return $result;
		}

	}

	/**
	 * Returns DB instatnce
	 * @return [type] [description]
	 */
	public function get_db() {
		return $this->db;
	}

	/**
	 * Returns argument by key
	 *
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function get_arg( $key = '', $default = false ) {
		return isset( $this->args[ $key ] ) ? $this->args[ $key ] : $default;
	}

	public function get_quick_edit_columns() {

		if ( null === $this->_quick_edit_columns ) {

			$this->_quick_edit_columns = array();

			foreach ( $this->fields as $field ) {
				if ( ! empty( $field['quick_editable'] ) ) {

					if ( 'checkbox' === $field['type'] && empty( $field['is_array'] ) ) {
						continue;
					}

					if ( 'checkbox' === $field['type'] ) {
						$field['type'] = 'checkbox-raw';
					}

					$this->_quick_edit_columns[ $field['name'] ] = $field;

				}
			}

		}

		return $this->_quick_edit_columns;
	}

	public function get_admin_columns() {

		if ( null === $this->_admin_columns ) {

			$this->_admin_columns = array();

			$columns = isset( $this->args['admin_columns'] ) ? $this->args['admin_columns'] : array();

			if ( empty( $columns ) ) {
				return $this->_admin_columns;
			}

			$service_columns = Module::instance()->manager->get_service_fields( array(
				'add_id_field' => true,
				'has_single'   => $this->get_arg( 'has_single' ),
			) );

			$service_fields = array();

			foreach ( $service_columns as $index => $s_column ) {
				$s_column['order'] = absint( $index );
				$service_fields[ $s_column['name'] ] = $s_column;
			}

			// Ensure _ID column exists for the backward compatibility
			if ( ! isset( $columns['_ID'] ) ) {
				$columns['_ID'] = array(
					'enabled' => true,
					'prefix' => '#',
					'is_sortable' => true,
					'is_num' => true,
				);
			}

			foreach ( $columns as $name => $column ) {

				if ( empty( $column['enabled'] ) ) {
					continue;
				}

				$field = Module::instance()->manager->data->get_field_by_name( $name, $this->fields );

				if ( ! $field ) {

					$field = ! empty( $service_fields[ $name ] ) ? $service_fields[ $name ] : false;

					if ( $field ) {
						if ( 0 === $field['order'] ) {
							$field['order'] = -1;
						}
					}

				}

				if ( ! $field ) {
					continue;
				}

				$column['title'] = $field['title'];
				$column['order'] = $field['order'];

				switch ( $field['type'] ) {

					case 'date':
						if ( ! empty( $field['is_timestamp'] ) ) {
							$column['_cb'] = 'date_i18n';
							$column['date_format'] = get_option( 'date_format' );
						}
						break;

					case 'time':
						if ( ! empty( $field['is_timestamp'] ) ) {
							$column['_cb'] = 'date_i18n';
							$column['date_format'] = get_option( 'time_format' );
						}
						break;

					case 'datetime':
					case 'datetime-local':
						if ( ! empty( $field['is_timestamp'] ) ) {
							$column['_cb'] = 'date_i18n';
							$column['date_format'] = get_option( 'date_format' ) . ', ' . get_option( 'time_format' );
						}
						break;

					case 'media':
						$column['_cb']        = 'wp_get_attachment_image';
						$column['image_size'] = array( 50, 50 );
						break;

					case 'gallery':
						$column['_cb']        = 'jet_engine_render_simple_gallery';
						$column['image_size'] = 50;

						break;
				}

				// Add service columns callbacks
				switch ( $name ) {
					case 'cct_author_id':
						$column['_cb'] = array( $this, 'get_item_author_link' );
						break;

					case 'cct_single_post_id':
						$column['_cb'] = array( $this, 'get_item_post_link' );
						break;
				}

				$this->_admin_columns[ $name ] = $column;

			}

			$this->_admin_columns = apply_filters(
				'jet-engine/custom-content-types/admin-columns',
				$this->_admin_columns,
				$this
			);

			$default_order = count( $this->_admin_columns );

			uasort( $this->_admin_columns, function( $a, $b ) use ( $default_order ) {

				$a_order = isset( $a['order'] ) ? intval( $a['order'] ) : $default_order;
				$b_order = isset( $b['order'] ) ? intval( $b['order'] ) : $default_order;

				if ( $a_order == $b_order ) {
					return 0;
				}

				return ( $a_order < $b_order ) ? -1 : 1;

			} );

		}

		return $this->_admin_columns;

	}

	public function get_item_post_link( $post_id ) {

		$title = get_the_title( $post_id );

		if ( current_user_can( 'edit_post', $post_id ) ) {
			$post_link = get_edit_post_link( absint( $post_id ), 'url' );
			return sprintf( '<a href="%1$s" target="_blank">%2$s</a>', $post_link, $title );
		} else {
			return $title;
		}

	}

	public function get_item_author_link( $user_id ) {

		$user = get_userdata( $user_id );

		if ( empty( $user ) ) {
			return null;
		}

		if ( current_user_can( 'edit_users' ) ) {
			return sprintf( '<a href="%1$s" target="_blank">%2$s</a>', get_edit_user_link( $user_id ), $user->data->user_login );
		} else {
			return $user->data->user_login;
		}

	}

	/**
	 * Return available statuses list
	 *
	 * @return [type] [description]
	 */
	public function get_statuses() {
		return array(
			'publish' => __( 'Publish', 'jet-engine' ),
			'draft'   => __( 'Draft', 'jet-engine' ),
		);
	}

	/**
	 * Maybe convert date value to timestamp
	 *
	 * @param  [type] $value [description]
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function maybe_to_timestamp( $value, $field ) {

		$input_type = ! empty( $field['input_type'] ) ? $field['input_type'] : false;

		if ( ! $input_type ) {
			$input_type = ! empty( $field['type'] ) ? $field['type'] : false;
		}

		switch ( $input_type ) {

			case 'date':
			case 'datetime':
			case 'datetime-local':

				if ( ! empty( $field['is_timestamp'] ) && ! \Jet_Engine_Tools::is_valid_timestamp( $value ) ) {
					$value = apply_filters( 
						'jet-engine/custom-content-types/strtotime',
						strtotime( $value ),
						$value
					);

					if ( ! $value ) {
						$value = null;
					}
				}

				break;

		}

		return $value;

	}

	/**
	 * Maybe convert date value from timestamp
	 *
	 * @param  [type] $value [description]
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function maybe_from_timestamp( $value, $field ) {

		$input_type = ! empty( $field['input_type'] ) ? $field['input_type'] : false;

		if ( ! $input_type ) {
			$input_type = ! empty( $field['type'] ) ? $field['type'] : $input_type;
		}

		switch ( $input_type ) {

			case 'date':
				if ( ! empty( $field['is_timestamp'] ) && \Jet_Engine_Tools::is_valid_timestamp( $value ) ) {
					$value = date( 'Y-m-d', $value );
				}
				break;

			case 'datetime':
			case 'datetime-local':
				if ( ! empty( $field['is_timestamp'] ) && \Jet_Engine_Tools::is_valid_timestamp( $value ) ) {
					$value = date( 'Y-m-d\TH:i', $value );
				}
				break;

		}

		return $value;

	}

	/**
	 * Returns date converted from timestamp
	 * 
	 * @return [type] [description]
	 */
	public function get_date( $format, $time ) {
		return apply_filters( 'jet-engine/custom-content-types/date', date( $format, $time ), $time, $format );
	}

}
