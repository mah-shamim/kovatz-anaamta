<?php

namespace Jet_Engine\Modules\Custom_Content_Types\Forms;

use Jet_Engine\Modules\Custom_Content_Types\Module;
use Jet_Form_Builder\Actions\Action_Handler;
use Jet_Form_Builder\Actions\Types\Base;
use Jet_Form_Builder\Exceptions\Action_Exception;

class Action extends Base {

	/**
	 * @return mixed
	 */
	public function get_id() {
		return 'insert_custom_content_type';
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return __( 'Insert/Update Custom Content Type Item', 'jet-engine' );
	}

	public function action_data() {
		require_once Module::instance()->module_path( 'forms/query-cct-data.php' );

		$types      = Query_Cct_Data::cct_list();
		$statuses   = Query_Cct_Data::cct_statuses_list();
		$fetch_path = Module::instance()->query_dialog()->api_path();

		$statuses = array_map( function ( $name, $label ) {
			return array( 'value' => $name, 'label' => $label );

		}, array_keys( $statuses ), $statuses );

		return array(
			'types'      => $types,
			'statuses'   => $statuses,
			'fetch_path' => $fetch_path,
		);
	}

	/**
	 * @return mixed
	 */
	public function visible_attributes_for_gateway_editor() {
		return array( 'type' );
	}

	/**
	 * @return mixed
	 */
	public function self_script_name() {
		return 'JetEngineCCT';
	}

	/**
	 * @return mixed
	 */
	public function editor_labels() {
		return array(
			'type'           => __( 'Content Type:', 'jet-engine' ),
			'status'         => __( 'Item Status:', 'jet-engine' ),
			'fields_map'     => __( 'Fields Map:', 'jet-engine' ),
			'default_fields' => __( 'Default Fields:', 'jet-engine' )
		);
	}

	public function editor_labels_help() {
		return array(
			'fields_map'     => __( 'Select content type fields to save appropriate form fields into', 'jet-engine' ),
			'default_fields' => __( 'Define default fields values which should be set on the CCT item creation', 'jet-engine' ),
		);
	}

	/**
	 * @param array $request
	 * @param Action_Handler $handler
	 *
	 * @return void
	 * @throws Action_Exception
	 */
	public function do_action( array $request, Action_Handler $handler ) {

		$type           = ! empty( $this->settings['type'] ) ? $this->settings['type'] : false;
		$status         = ! empty( $this->settings['status'] ) ? $this->settings['status'] : 'publish';
		$fields         = ! empty( $this->settings['fields_map'] ) ? $this->settings['fields_map'] : array();
		$default_fields = ! empty( $this->settings['default_fields'] ) ? $this->settings['default_fields'] : array();
		$type_object    = false;

		if ( $type ) {
			$type_object = Module::instance()->manager->get_content_types( $type );
		}

		if ( ! $type_object ) {
			throw ( new Action_Exception(
				'Internal error! Please contact website administrator. Error code: content_type_not_found',
				$this->settings
			) )->dynamic_error();
		}

		$item = array();

		foreach ( $fields as $form_field => $item_field ) {
			if ( isset( $request[ $form_field ] ) ) {
				$item[ $item_field ] = $this->recursive_parse_values( $request[ $form_field ] );
			}
		}

		if ( ! empty( $default_fields ) ) {
			foreach ( $default_fields as $field_name => $field_value ) {
				if ( '' !== $field_value ) {
					$item[ $field_name ] = $field_value;
				}
			}
		}

		$item['cct_status'] = $status;

		if ( empty( $item ) ) {
			throw ( new Action_Exception(
				'Internal error! Please contact website administrator. Error code: fields_mismatch'
			) )->dynamic_error();
		}

		if ( ! empty( $item['_ID'] ) ) {

			if ( ! is_user_logged_in() ) {
				throw ( new Action_Exception(
					'Only logged in users can update items'
				) )->dynamic_error();
			}

			$existing_item = $type_object->db->get_item( $item['_ID'] );

			if ( ! $existing_item ) {
				throw ( new Action_Exception(
					'You trying to update not existing item'
				) )->dynamic_error();
			}

			$author = absint( $existing_item['cct_author_id'] );
			$cct    = Module::instance()->manager->get_content_types( $existing_item['cct_slug'] );

			if ( ! $cct ) {
				throw ( new Action_Exception(
					'Content Type not exists'
				) )->dynamic_error();
			}

			if ( $author !== get_current_user_id() && ! $cct->user_has_access() ) {
				throw ( new Action_Exception(
					'Only item author can edit the item'
				) )->dynamic_error();
			}

		}

		$handler = $type_object->get_item_handler();
		$item_id = $handler->update_item( $item );


		if ( ! $item_id ) {
			throw ( new Action_Exception(
				'Internal error! Please contact website administrator. Error code: cant_update_item'
			) )->dynamic_error();
		}

		jet_fb_action_handler()->add_request(
			array(
				'inserted_cct_' . $type => (int) $item_id,
			)
		);
	}

	public function recursive_parse_values( $source ) {
		if ( ! is_array( $source ) ) {
			return wp_specialchars_decode(
				\Jet_Form_Builder\Classes\Tools::sanitize_text_field( $source ),
				ENT_COMPAT
			);
		}

		$response = array();
		foreach ( $source as $item_name => $item_value ) {
			$response[ $item_name ] = $this->recursive_parse_values( $item_value );
		}

		return $response;
	}

}