<?php
namespace Jet_Engine\Modules\Custom_Content_Types;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Item_Handler class
 */
class Item_Handler {

	private $factory;
	private $item_id;
	private $update_status = false;

	/**
	 * Constructor for the class
	 *
	 * @param [type] $action_key   [description]
	 * @param array  $actions_list [description]
	 */
	public function __construct( $action_key = null, $actions_list = array(), $factory = null ) {

		$actions_list = array_merge( array(
			'save'   => false,
			'delete' => false,
			'clone'  => false,
		), $actions_list );

		$this->factory = $factory;

		if ( $this->factory->admin_pages ) {
			$this->item_id = $this->factory->admin_pages->get_item_id();
		}

		if ( ! $action_key || empty( $actions_list ) ) {
			return;
		}

		switch ( $_REQUEST[ $action_key ] ) {

			case $actions_list['save']:
				add_action( 'admin_init', array( $this, 'save_item' ) );
				break;

			case $actions_list['delete']:
				add_action( 'admin_init', array( $this, 'delete_item' ) );
				break;

			case $actions_list['clone']:
				add_action( 'admin_init', array( $this, 'clone_item' ) );
				break;

		}

		if ( ! empty( $actions_list['quick_edit'] ) ) {
			add_action( 'wp_ajax_' . $actions_list['quick_edit'], array( $this, 'quick_edit_save_item' ) );
		}

	}

	/**
	 * Get factory instance
	 *
	 * @return mixed
	 */
	public function get_factory() {
		return $this->factory;
	}

	/**
	 * Process item deletion
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function delete_item( $item_id = false, $redirect = true ) {

		if ( ! $item_id ) {
			$item_id = $this->item_id;
		}

		if ( ! $item_id ) {
			wp_die( 'Item ID not found in the request', 'Error' );
		}

		if ( ! $this->factory->user_has_access() ) {
			wp_die( 'You don`t have permissions to fo this', 'Error' );
		}

		$item = $this->factory->db->get_item( $item_id );

		if ( ! empty( $item['cct_single_post_id'] ) ) {
			wp_delete_post( absint( $item['cct_single_post_id'] ), true );
		}

		$this->factory->db->delete( array( '_ID' => $item_id ) );

		do_action( 'jet-engine/custom-content-types/delete-item/' . $this->factory->get_arg( 'slug' ), $item_id, $item, $this );

		if ( $redirect ) {
			if ( $this->factory->admin_pages ) {
				wp_redirect( $this->factory->admin_pages->page_url( false ) );
				die();
			}
		}

	}

	public function clone_item( $item_id = false ) {

		if ( ! $item_id ) {
			$item_id = $this->item_id;
		}

		if ( ! $this->factory->user_has_access() ) {
			wp_die( 'You don`t have permissions to fo this', 'Error' );
		}

		if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], 'jet-cct-nonce' ) ) {
			wp_die( 'Your link is expired, please return to the previous page and try again', 'Error' );
		}

		$itemarr = $_POST;

		if ( ! $item_id ) {
			wp_die( 'Item ID to clone not found in the request', 'Error' );
		}

		$itemarr = $this->factory->db->get_item( $item_id );

		if ( ! $itemarr ) {
			wp_die( 'Can`t get item data to clone', 'Error' );
		}

		if ( isset( $itemarr['_ID'] ) ) {
			unset( $itemarr['_ID'] );
		}

		if ( isset( $itemarr['cct_single_post_id'] ) ) {
			unset( $itemarr['cct_single_post_id'] );
		}

		$new_id = $this->update_item( $itemarr );

		if ( ! $new_id ) {
			if ( $this->factory->admin_pages ) {
				wp_redirect( $this->factory->admin_pages->page_url( 'add', false, 'error' ) );
				die();
			}
		} elseif ( is_wp_error( $new_id ) ) {
			if ( $this->factory->admin_pages ) {
				wp_die( $new_id->get_error_message(), 'Error' );
			}
		}

		if ( $this->factory->admin_pages ) {
			wp_redirect( $this->factory->admin_pages->page_url( 'edit', $new_id, $this->update_status ) );
			die();
		}

	}

	public function quick_edit_save_item() {

		$item_id = $this->item_id;

		if ( ! $item_id ) {
			wp_send_json_error( 'Updated item ID was not found in the request' );
		}

		if ( ! $this->factory->user_has_access() ) {
			wp_send_json_error( 'You don`t have permissions to fo this' );
		}

		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'jet-cct-nonce' ) ) {
			wp_send_json_error( 'Your link is expired, please return to the previous page and try again' );
		}

		$itemarr     = $_POST['item_data'];
		$old_itemarr = $this->factory->db->get_item( $item_id );

		$fields = $this->factory->get_formatted_fields();
		$skip   = array( 'cct_author_id', 'cct_created', 'cct_modified' );

		foreach ( $fields as $field ) {

			if ( in_array( $field['name'], $skip ) ) {
				continue;
			}

			if ( ! isset( $itemarr[ $field['name'] ] ) && isset( $old_itemarr[ $field['name'] ] ) ) {
				$itemarr[ $field['name'] ] = $old_itemarr[ $field['name'] ];
			}
		}

		$itemarr['_ID'] = $item_id;

		$item_id = $this->update_item( $itemarr );

		if ( ! $item_id ) {
			wp_send_json_error( 'Internal error. Please try again.' );
		} elseif ( is_wp_error( $item_id ) ) {
			wp_send_json_error( $item_id->get_error_message() );
		} else {

			// Render new row with updated data to insert instead of old one
			require Module::instance()->module_path( 'list-table.php' );
			$items_table = new List_Table( array( 'screen' => 'ajax-quick-edit' ) );
			$items_table->set_factory( $this->factory );
			$items_table->prepare_items( array( '_ID' => $item_id ) );

			if ( empty( $items_table->items ) ) {
				wp_send_json_error( 'Item processing went wrong, please reload page and try again' );
			}

			ob_start();
			$items_table->single_row( $items_table->items[0] );
			wp_send_json_success( ob_get_clean() );

		}

	}

	/**
	 * Process item saving
	 *
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function save_item( $item_id = false ) {

		if ( ! $item_id ) {
			$item_id = $this->item_id;
		}

		if ( ! $this->factory->user_has_access() ) {
			wp_die( 'You don`t have permissions to fo this', 'Error' );
		}

		if ( empty( $_POST['cct_nonce'] ) || ! wp_verify_nonce( $_POST['cct_nonce'], 'jet-cct-nonce' ) ) {
			wp_die( 'Your link is expired, please return to the previous page and try again', 'Error' );
		}

		$itemarr = $_POST;

		if ( $item_id ) {
			$itemarr['_ID'] = $item_id;
		}

		$fields = $this->factory->get_formatted_fields();
		$skip   = array( 'cct_author_id', 'cct_created', 'cct_modified' );

		foreach ( $fields as $field ) {

			if ( in_array( $field['name'], $skip ) ) {
				continue;
			}

			if ( ! isset( $itemarr[ $field['name'] ] ) ) {
				$itemarr[ $field['name'] ] = '';
			}
		}

		$item_id = $this->update_item( $itemarr );

		if ( ! $item_id ) {
			if ( $this->factory->admin_pages ) {
				wp_redirect( $this->factory->admin_pages->page_url( 'add', false, 'error' ) );
				die();
			}
		} elseif ( is_wp_error( $item_id ) ) {
			if ( $this->factory->admin_pages ) {
				wp_die( $item_id->get_error_message(), 'Error' );
			}
		}

		if ( $this->factory->admin_pages ) {
			wp_redirect( $this->factory->admin_pages->page_url( 'edit', $item_id, $this->update_status ) );
			die();
		}

	}

	/**
	 * Insert or update item
	 *
	 * @param  [type] $item [description]
	 * @return [type]       [description]
	 */
	public function update_item( $itemarr = array() ) {

		if ( empty( $itemarr ) ) {
			return false;
		}

		$fields  = $this->factory->get_formatted_fields();
		$item_id = ! empty( $itemarr['_ID'] ) ? absint( $itemarr['_ID'] ) : false;
		$item    = array();
		$prev_item = false;

		if ( $item_id ) {
			$prev_item = $this->factory->db->get_item( $item_id );
		}

		if ( $prev_item ) {
			$itemarr = wp_parse_args( $itemarr, $prev_item );
		} else {
			$item_id = false;
		}

		foreach ( $fields as $field_name => $field_data ) {

			if ( isset( $itemarr[ $field_name ] ) ) {
				$value = $itemarr[ $field_name ];
			} else {
				$value = ! empty( $field['default_val'] ) ? $field['default_val'] : '';
			}

			$type  = isset( $field_data['type'] ) ? $field_data['type'] : false;
			$value = $this->sanitize_field_value( $value, $field_data );
			$value = apply_filters( 'jet-engine/custom-content-types/update-item/sanitize-field-value', $value, $field_name, $field_data );

			$item[ $field_name ] = $value;
		}

		if ( ! empty( $itemarr['cct_status'] ) ) {
			$status           = esc_attr( $itemarr['cct_status'] );
			$allowed_statuses = $this->factory->get_statuses();
			$status           = isset( $allowed_statuses[ $status ] ) ? $status : 'publish';
		} else {
			$status = 'publish';
		}

		$item['cct_status'] = $status;

		$has_single     = $this->factory->get_arg( 'has_single' );
		$single_post_id = false;

		if ( $item_id ) {

			if ( empty( $prev_item['cct_author_id'] ) ) {
				$item['cct_author_id'] = get_current_user_id();
			}

		}

		if ( $has_single ) {

			if ( $item_id && $prev_item ) {
				$single_post_id = isset( $prev_item['cct_single_post_id'] ) ? $prev_item['cct_single_post_id'] : false;
			}

			$update_single_post = ! ! $single_post_id;

			if ( ! $single_post_id ) {
				$single_post_id = $this->process_single_post( $item );
			}

			if ( $single_post_id ) {
				$item['cct_single_post_id'] = $single_post_id;
			}

			// Update single post.
			if ( $single_post_id && $update_single_post ) {
				$this->process_single_post( $item );
			}

		}

		$item = apply_filters( 'jet-engine/custom-content-types/item-to-update', $item, $fields, $this );

		if ( $item_id ) {

			$item['cct_modified'] = current_time( 'mysql' );

			if ( empty( $item['cct_created'] ) ) {
				unset( $item['cct_created'] );
			}

			do_action( 'jet-engine/custom-content-types/update-item/' . $this->factory->get_arg( 'slug' ), $item, $prev_item, $this );

			$this->factory->db->update( $item, array( '_ID' => $item_id ) );

			do_action( 
				'jet-engine/custom-content-types/updated-item/' . $this->factory->get_arg( 'slug' ), 
				$item, 
				$prev_item, 
				$this 
			);

			$error               = $this->factory->db->get_errors();
			$this->update_status = 'updated';

			if ( $error ) {
				return new \WP_Error( 400, 'Database error. ' . $error . '. Please go to Content Type settings page and try to update current Content Type. If error still exists - please contact Crocoblock support' );
			}

		} else {

			$item['_ID'] = null; // added to prevent error on some mysql versions
			$item['cct_author_id'] = ! empty( $item['cct_author_id'] ) ? $item['cct_author_id'] : get_current_user_id();
			$item['cct_created']   = current_time( 'mysql' );
			$item['cct_modified']  = $item['cct_created'];

			do_action( 'jet-engine/custom-content-types/create-item/' . $this->factory->get_arg( 'slug' ), $item, $this );

			$item_id = $this->factory->db->insert( $item );
			$error   = $this->factory->db->get_errors();

			do_action(
				'jet-engine/custom-content-types/created-item/' . $this->factory->get_arg( 'slug' ),
				$item,
				$item_id,
				$this
			);

			$item['_ID'] = $item_id;

			do_action(
				'jet-engine/custom-content-types/updated-item/' . $this->factory->get_arg( 'slug' ), 
				$item, 
				array(), 
				$this 
			);

			if ( ! $item_id ) {
				if ( ! $error ) {
					return false;
				} else {
					return new \WP_Error( 400, 'Database error. ' . $error . '. Please go to Content Type settings page and try to update current Content Type. If error still exists - please contact Crocoblock support' );
				}
			} elseif ( $error ) {
				return new \WP_Error( 400, 'Item was inserted, but Database error triggered. ' . $error . '. Please go to Content Type settings page and try to update current Content Type. If error still exists - please contact Crocoblock support' );
			}

			$this->update_status = 'added';

		}

		return $item_id;

	}

	/**
	 * Sanitize field value.
	 *
	 * @param mixed $value
	 * @param array $field
	 *
	 * @return array|mixed
	 */
	public function sanitize_field_value( $value, $field ) {

		$type = isset( $field['type'] ) ? $field['type'] : false;

		switch ( $type ) {

			case 'repeater':

				if ( is_array( $value ) && ! empty( $field['repeater-fields'] ) ) {

					$repeater_names  = wp_list_pluck( $field['repeater-fields'], 'name' );
					$repeater_fields = array_combine( $repeater_names, $field['repeater-fields'] );

					foreach ( $value as $item_id => $item ) {
						foreach ( $item as $sub_item_id => $sub_item_value ) {
							$value[ $item_id ][ $sub_item_id ] = $this->sanitize_field_value( $sub_item_value, $repeater_fields[ $sub_item_id ] );
						}
					}
				}

				break;

			case 'checkbox':
			case 'checkbox-raw':

				if ( ! empty( $field['is_array'] ) ) {

					$raw    = ! empty( $value ) ? $value : array();
					$result = array();

					if ( ! is_array( $raw ) ) {
						$raw = array( $raw => 'true' );
					}

					if ( in_array( 'true', $raw ) || in_array( 'false', $raw ) ) {

						foreach ( $raw as $raw_key => $raw_value ) {
							$bool_value = filter_var( $raw_value, FILTER_VALIDATE_BOOLEAN );
							if ( $bool_value ) {
								$result[] = $raw_key;
							}
						}

						$value = $result;

					}

				} else {
					if ( ! is_array( $value ) ) {
						$value = array( $value => 'true' );
					}
				}

				break;

			case 'media':
			case 'gallery':

				if ( empty( $value ) ) {
					$value = null;
				} elseif ( ! empty( $field['value_format'] ) && 'both' === $field['value_format'] ) {
					$value = jet_engine_sanitize_media_json( $value );
				}

				break;

			case 'wysiwyg':
				$value = jet_engine_sanitize_wysiwyg( $value );

				break;

			default:
				$value = $this->factory->maybe_to_timestamp( $value, $field );
		}

		return $value;
	}

	/**
	 * Process single post
	 *
	 * @param  array  $item [description]
	 * @return [type]       [description]
	 */
	public function process_single_post( $item = array() ) {

		$post_id = ! empty( $item['cct_single_post_id'] ) ? absint( $item['cct_single_post_id'] ) : false;

		$post_type     = $this->factory->get_arg( 'related_post_type' );
		$title_field   = $this->factory->get_arg( 'related_post_type_title' );
		$content_field = $this->factory->get_arg( 'related_post_type_content' );

		if ( ! $post_type ) {
			return false;
		}

		$postarr = array(
			'post_type'   => $post_type,
			'post_status' => $item['cct_status'],
		);

		if ( $title_field ) {
			$postarr['post_title'] = isset( $item[ $title_field ] ) ? $item[ $title_field ] : '';
		}

		if ( $content_field ) {
			$postarr['post_content'] = isset( $item[ $content_field ] ) ? $item[ $content_field ] : '';
		}

		if ( ! empty( $item['cct_author_id'] ) ) {
			$postarr['post_author'] = $item['cct_author_id'];
		}

		if ( $post_id ) {

			$post = get_post( $post_id );

			if ( ! $post || is_wp_error( $post ) ) {
				$post_id = wp_insert_post( $postarr );
			} else {
				$postarr['ID'] = $post_id;
				wp_update_post( $postarr );
			}

		} else {
			$post_id = wp_insert_post( $postarr );
		}

		if ( is_wp_error( $post_id ) ) {
			return false;
		}

		return $post_id;

	}

}
