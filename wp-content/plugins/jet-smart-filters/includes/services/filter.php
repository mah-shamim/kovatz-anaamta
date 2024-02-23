<?php
/**
 * Filter service class
 */

class Jet_Smart_Filters_Service_Filter {

	public $serialized_data_keys;
	private $_adata;

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_action( 'init', function() {
			// Init serialized keys
			$this->serialized_data_keys = apply_filters( 'jet-smart-filters/service/filter/serialized-keys', array(
				'_source_manual_input',
				'_source_color_image_input',
				'_source_manual_input_range',
				'_ih_source_map',
				'_data_exclude_include'
			));

			// Init admin data
			if ( isset( jet_smart_filters()->admin->data ) ) {
				$this->_adata = jet_smart_filters()->admin->data;
			} else {
				require_once jet_smart_filters()->plugin_path( 'admin/includes/data.php' );
				$this->_adata = new Jet_Smart_Filters_Admin_Data();
			}

			if ( isset( jet_smart_filters()->admin->multilingual_support ) ) {
				$this->_multilingual = jet_smart_filters()->admin->multilingual_support;
			} else {
				require_once jet_smart_filters()->plugin_path( 'admin/includes/multilingual-support.php' );
				$this->_multilingual = new Jet_Smart_Filters_Admin_Multilingual_Support();
			}
		}, 9999 );
	}

	public function get( $id ) {
		global $wpdb;

		// escapes data for use in a MySQL query
		$id = absint( $id );

		$output_data               = false;
		$registered_settings_names = $this->_adata->registered_settings_names();

		$sql = "
		SELECT $wpdb->posts.ID, $wpdb->posts.post_title as title, $wpdb->posts.post_date as date, $wpdb->postmeta.meta_key, $wpdb->postmeta.meta_value
			FROM $wpdb->posts, $wpdb->postmeta
			WHERE $wpdb->posts.ID = '$id'
			AND $wpdb->posts.ID = $wpdb->postmeta.post_ID
			AND $wpdb->postmeta.meta_key IN ('" . implode( "','", $registered_settings_names ) . "')
			AND $wpdb->posts.post_type='jet-smart-filters'";
		$sql_result = $wpdb->get_results( $sql, ARRAY_A );

		if ( count( $sql_result ) ) {
			$output_data = array();

			$output_data['ID']    = $sql_result[0]['ID'];
			$output_data['title'] = $sql_result[0]['title'];
			$output_data['date']  = $sql_result[0]['date'];

			foreach ( $sql_result as $filter ) {
				$key   = $filter['meta_key'];
				$value = $filter['meta_value'];

				if ( $value && in_array( $key, $this->serialized_data_keys ) ) {
					$value = unserialize($value);
				}

				$output_data[$key] = $value;
			}
		}

		if ( $this->_multilingual->is_Enabled ) {
			$this->_multilingual->add_data_to_filter( $output_data );
		}

		return $output_data;
	}

	public function update( $id, $data ) {

		if ( $id === 'new' ) {
			return $this->add_new( $data );
		}

		$new_data = $this->process_data( $data );
		$new_data['ID'] = $id;

		return wp_update_post( $new_data );
	}

	public function add_new( $data ) {

		$new_data = $this->process_data( $data );

		$new_data['post_status'] = 'publish';
		$new_data['post_type']   = jet_smart_filters()->post_type->slug();

		if ( ! isset( $new_data['meta_input'] ) ) {
			$new_data['meta_input'] =  array();
		}

		$new_data['meta_input'] = array_merge(
			array(
				'_filter_type' => '',
				'_data_source' => ''
			),
			$this->_adata->default_settings_values(),
			$new_data['meta_input']
		);

		return wp_insert_post( $new_data );
	}

	private function process_data( $data ) {

		$processed_data = array();

		if ( isset( $data['title'] ) ) {
			$processed_data['post_title'] = $data['title'];

			unset( $data['title'] );
		}

		if ( isset( $data['date'] ) ) {
			$processed_data['post_date'] = $data['date'];

			unset( $data['date'] );
		}

		foreach ( $data as $key => $value ) {
			/* if ( in_array( $key, $this->serialized_data_keys ) ) {
				$value = serialize( $value );
			} */
			$processed_data['meta_input'][$key] = $value;
		}

		return $processed_data;
	}
}
