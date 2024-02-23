<?php
namespace Jet_Engine\Glossaries;
/**
 * Glossaries data controller class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Options_Data class
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
		'status' => 'glossary',
	);

	/**
	 * Table format
	 *
	 * @var string
	 */
	public $table_format = array( '%s', '%s', '%s', '%s', '%s' );

	/**
	 * Found items
	 *
	 * @var array
	 */
	public $found_items = array();

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
		return true;
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
			'status'      => 'glossary',
			'labels'      => array(),
			'args'        => array(),
			'meta_fields' => array(),
		);

		$name = ! empty( $request['name'] ) ? sanitize_text_field( $request['name'] ) : 'Untitled glossary';

		$labels = array(
			'name' => $name,
		);

		$allowed_args = array(
			'source',
			'source_file',
			'value_col',
			'label_col',
		);

		$args = array();

		foreach ( $request as $key => $value ) {
			if ( in_array( $key, $allowed_args ) ) {
				$args[ $key ] = $value;
			}
		}

		/**
		 * @todo Validate meta fields before saving - ensure that used correct types and all names was set.
		 */
		$meta_fields = ! empty( $request['fields'] ) ? $request['fields'] : array();

		$result['slug']        = null;
		$result['labels']      = $labels;
		$result['args']        = $args;
		$result['meta_fields'] = $this->sanitize_meta_fields( $meta_fields );

		return $result;

	}

	/**
	 * Sanitize meta fields
	 *
	 * @param  [type] $meta_fields [description]
	 * @return [type]              [description]
	 */
	public function sanitize_meta_fields( $meta_fields ) {

		foreach ( $meta_fields as $key => $field ) {

			$sanitized_field = array(
				'value'      => ! \Jet_Engine_Tools::is_empty( $field['value'] ) ? $field['value'] : '',
				'label'      => ! \Jet_Engine_Tools::is_empty( $field['label'] ) ? $field['label'] : '',
				'is_checked' => isset( $field['is_checked'] ) ? filter_var( $field['is_checked'], FILTER_VALIDATE_BOOLEAN ) : false,
			);

			$meta_fields[ $key ] = $sanitized_field;

		}

		return $meta_fields;
	}

	/**
	 * Filter post type for register
	 *
	 * @return array
	 */
	public function filter_item_for_register( $item ) {

		$result         = array();
		$args           = maybe_unserialize( $item['args'] );
		$labels         = maybe_unserialize( $item['labels'] );
		$item['fields'] = maybe_unserialize( $item['meta_fields'] );
		$result         = array_merge( $item, $args, $labels );

		unset( $result['args'] );
		unset( $result['status'] );
		unset( $result['meta_fields'] );

		return $result;

	}

	/**
	 * Filter post type for edit
	 *
	 * @return array
	 */
	public function filter_item_for_edit( $item ) {

		$result         = array();
		$args           = maybe_unserialize( $item['args'] );
		$labels         = maybe_unserialize( $item['labels'] );
		$item['fields'] = maybe_unserialize( $item['meta_fields'] );
		$result         = array_merge( $item, $args, $labels );

		unset( $result['args'] );
		unset( $result['status'] );
		unset( $result['meta_fields'] );

		return $result;
	}

	public function get_item_for_edit( $id ) {

		if ( isset( $this->found_items[ $id ] ) ) {
			return $this->found_items[ $id ];
		}

		$item = parent::get_item_for_edit( $id );

		if ( ! empty( $item['source'] ) && 'file' === $item['source'] ) {
			$item['fields'] = $this->get_fields_from_file( $item );
		}

		if ( empty( $item ) || empty( $item['fields'] ) ) {
			return $item;
		}

		$item['fields'] = array_map( function ( $field ) {

			if ( ! empty( $field['label'] ) ) {
				$field['label'] = apply_filters(
					'jet-engine/compatibility/translate-string',
					wp_unslash( $field['label'] )
				);
			}

			if ( ! empty( $field['value'] ) ) {
				$field['value'] = apply_filters(
					'jet-engine/compatibility/translate-string',
					wp_unslash( $field['value'] )
				);
			}

			return $field;
		}, $item['fields'] );

		$this->found_items[ $id ] = $item;

		return $item;
	}

	public function get_fields_from_file( $item ) {
		$file    = ! empty( $item['source_file'] ) ? $item['source_file'] : array();
		$file_id = ! empty( $file['id'] ) ? absint( $file['id'] ) : false;
		$fields  = array();

		if ( $file_id ) {

			$file_path = get_attached_file( $file_id );
			$mime      = get_post_mime_type( $file_id );
			$label_col = ! empty( $item['label_col'] ) ? $item['label_col'] : false;
			$value_col = ! empty( $item['value_col'] ) ? $item['value_col'] : false;

			switch ( $mime ) {
				case 'text/csv':

					$handle      = fopen( $file_path, "r" );
					$label_index = false;
					$value_index = false;
					$index       = 0;

					while ( false !== ( $row = fgetcsv( $handle, 0 ) ) ) {

						if ( ! $index ) {

							$index++;

							if ( $label_col ) {
								$label_index = array_search( $label_col, $row );
							}
							if ( $value_col ) {
								$value_index = array_search( $value_col, $row );
							}

							if ( false !== $label_index || false !== $value_index ) {
								continue;
							}

						}

						$default_val   = $row[0];
						$default_label = isset( $row[1] ) ? $row[1] : $row[0];

						$value    = ( false !== $value_index ) ? $row[ $value_index ] : $default_val;
						$label    = ( false !== $label_index ) ? $row[ $label_index ] : $default_label;
						$fields[] = array(
							'value' => $value,
							'label' => $label,
						);
					}

					break;

				case 'application/json':

					ob_start();
					include $file_path;
					$content = ob_get_clean();
					$data = json_decode( $content, true );

					if ( ! empty( $data ) ) {
						$data = array_values( $data );
						foreach ( $data as $row ) {
							$row_values = array_values( $row );

							$value = ( false !== $value_col && isset( $row[ $value_col ] ) ) ? $row[ $value_col ] : $row_values[0];
							$label = ( false !== $label_col && isset( $row[ $label_col ] ) ) ? $row[ $label_col ] : $row_values[1];

							if ( is_array( $value ) || is_array( $label ) ) {
								continue;
							}

							$fields[] = array(
								'value' => $value,
								'label' => $label,
							);
						}
					}

					break;
			}
		}

		return $fields;
	}

}
