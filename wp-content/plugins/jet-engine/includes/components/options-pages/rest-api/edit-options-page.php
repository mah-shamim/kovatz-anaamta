<?php
/**
 * Update options page endpoint
 */

class Jet_Engine_Rest_Edit_Options_Page extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'edit-options-page';
	}

	public function safe_get( $args = array(), $group = '', $key = '', $default = false ) {
		return isset( $args[ $group ][ $key ] ) ? $args[ $group ][ $key ] : $default;
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params = $request->get_params();

		if ( empty( $params['id'] ) ) {

			jet_engine()->options_pages->add_notice(
				'error',
				__( 'Item ID not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->options_pages->get_notices(),
			) );

		}

		$initial_storage_type = ! empty( $params['initial_storage_type'] ) ? $params['initial_storage_type'] : false;
		$update_options       = ! empty( $params['update_options'] ) ? $params['update_options'] : false;

		$slug          = $this->safe_get( $params, 'general_settings', 'slug' );
		$storage_type  = $this->safe_get( $params, 'general_settings', 'storage_type' );
		$option_prefix = $this->safe_get( $params, 'general_settings', 'option_prefix' );
		$fields        = ! empty( $params['fields'] ) ? $params['fields'] : array();

		jet_engine()->options_pages->data->set_request( array(
			'id'               => $params['id'],
			'name'             => $this->safe_get( $params, 'general_settings', 'name' ),
			'slug'             => $slug,
			'menu_name'        => $this->safe_get( $params, 'general_settings', 'menu_name' ),
			'parent'           => $this->safe_get( $params, 'general_settings', 'parent' ),
			'icon'             => $this->safe_get( $params, 'general_settings', 'icon' ),
			'capability'       => $this->safe_get( $params, 'general_settings', 'capability' ),
			'position'         => $this->safe_get( $params, 'general_settings', 'position' ),
			'storage_type'     => $storage_type,
			'option_prefix'    => $option_prefix,
			'hide_field_names' => $this->safe_get( $params, 'general_settings', 'hide_field_names' ),
			'fields'           => $fields,
		) );

		$updated = jet_engine()->options_pages->data->edit_item( false );

		if ( 'separate' === $storage_type && $option_prefix ) {
			$storage_type .= '_with_prefix';
		}

		if ( $updated && $update_options && $storage_type !== $initial_storage_type ) {
			$this->update_options_storage_type( $update_options, $slug, $fields, $initial_storage_type, $storage_type );
		}

		return rest_ensure_response( array(
			'success' => $updated,
			'notices' => jet_engine()->options_pages->get_notices(),
		) );

	}

	public function update_options_storage_type( $action, $slug, $fields, $from, $to ) {

		if ( empty( $fields ) ) {
			return;
		}

		$fields = array_filter( $fields, function ( $field ) {

			if ( empty( $field['object_type'] ) ) {
				return false;
			}

			if ( 'field' !== $field['object_type'] ) {
				return false;
			}

			if ( empty( $field['type'] ) ) {
				return false;
			}

			if ( 'html' === $field['type'] ) {
				return false;
			}

			return true;
		} );

		$option_keys = wp_list_pluck( $fields, 'name' );
		$options     = array();

		switch ( $from ) {
			case 'default':
				$options = get_option( $slug, array() );

				if ( 'update_and_delete' === $action ) {
					delete_option( $slug );
				}

				break;

			case 'separate':

				foreach ( $option_keys as $key ) {
					$options[ $key ] = get_option( $key );

					if ( 'update_and_delete' === $action ) {
						delete_option( $key );
					}
				}

				break;

			case 'separate_with_prefix':

				foreach ( $option_keys as $key ) {
					$options[ $key ] = get_option( $slug . '_' . $key );

					if ( 'update_and_delete' === $action ) {
						delete_option( $slug . '_' .$key );
					}
				}

				break;
		}

		if ( empty( $options ) ) {
			return;
		}

		switch ( $to ) {
			case 'default':
				update_option( $slug, $options );
				break;

			case 'separate':

				foreach ( $options as $key => $value ) {
					update_option( $key, $value );
				}

				break;

			case 'separate_with_prefix':

				foreach ( $options as $key => $value ) {
					update_option( $slug . '_' . $key, $value );
				}

				break;
		}
	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELETE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'POST';
	}

	/**
	 * Check user access to current end-popint
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get query param. Regex with query parameters
	 *
	 * @return string
	 */
	public function get_query_params() {
		return '(?P<id>[\d]+)';
	}

}
