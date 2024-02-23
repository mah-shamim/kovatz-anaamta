<?php
namespace Jet_Engine\Glossaries;
/**
 * Options pages manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! trait_exists( '\Jet_Engine_Notices_Trait' ) ) {
	require_once jet_engine()->plugin_path( 'includes/traits/notices.php' );
}

/**
 * Define Jet_Engine_Glossaries class
 */
class Manager {

	use \Jet_Engine_Notices_Trait;

	public $data;
	public $settings;
	public $meta_fields;
	public $forms;
	public $filters;

	public function __construct() {

		$this->init_data();

		require_once $this->component_path( 'settings.php' );
		require_once $this->component_path( 'forms.php' );
		require_once $this->component_path( 'filters.php' );

		$this->settings    = new Settings();
		$this->forms       = new Forms();
		$this->filters     = new Filters();

		add_action( 'jet-engine/rest-api/init-endpoints', array( $this, 'init_rest' ) );
		add_filter( 'mime_types', array( $this, 'ensure_allowed_import_mimes' ) );
		add_action( 'jet-engine/meta-boxes/init-options-sources', array( $this, 'init_options_source' ) );

	}

	public function init_options_source() {
		require_once $this->component_path( 'meta-fields.php' );
		$this->meta_fields = new Meta_Fields();
	}

	public function ensure_allowed_import_mimes( $mimes ) {

		$mimes['json'] = 'application/json';
		$mimes['csv']  = 'text/csv';

		return $mimes;
	}

	/**
	 * Init data instance
	 *
	 * @return [type] [description]
	 */
	public function init_data() {

		if ( ! class_exists( '\Jet_Engine_Base_Data' ) ) {
			require_once jet_engine()->plugin_path( 'includes/base/base-data.php' );
		}

		require $this->component_path( 'data.php' );

		$this->data = new Data( $this );

	}

	public function init_rest( $api_manager ) {

		require_once $this->component_path( 'rest-api/search-fields.php' );

		$api_manager->register_endpoint( new Rest\Search_Fields() );

	}

	/**
	 * Return path to file inside component
	 *
	 * @param  [type] $path_inside_component [description]
	 * @return [type]                        [description]
	 */
	public function component_path( $path_inside_component ) {
		return jet_engine()->plugin_path( 'includes/components/glossaries/' . $path_inside_component );
	}

	/**
	 * Return URL of the file inside component
	 *
	 * @param  [type] $path_inside_component [description]
	 * @return [type]                        [description]
	 */
	public function component_url( $path_inside_component ) {
		return jet_engine()->plugin_url( 'includes/components/glossaries/' . $path_inside_component );
	}

	/**
	 * Returns glossaries
	 *
	 * @return [type] [description]
	 */
	public function get_glossaries_for_js() {

		$items = array_merge(
			array( array( 'id' => '', 'name' => __( 'Select glossary', 'jet-engine' ) ) ),
			$this->settings->get()
		);

		return \Jet_Engine_Tools::prepare_list_for_js( $items, 'id', 'name' );

	}

	/**
	 * Returns labels from selected glossary for given values
	 *
	 * @param  [type] $value       [description]
	 * @param  [type] $glossary_id [description]
	 * @param  string $delimiter   [description]
	 * @return [type]              [description]
	 */
	public function get_labels_for_values( $value = null, $glossary_id = null, $delimiter = ', ' ) {

		if ( ! $glossary_id ) {
			return $value;
		}

		$glossary = jet_engine()->glossaries->data->get_item_for_edit( absint( $glossary_id ) );

		if ( ! $glossary ) {
			return $value;
		}

		if ( ! is_array( $value ) ) {
			return $this->search_label( $value, $glossary );
		}

		$result = array();

		foreach ( $value as $val_index => $val ) {
			if ( in_array( $val, array( 'true', 'false' ) ) && ! is_numeric( $val_index ) ) {
				$val = filter_var( $val, FILTER_VALIDATE_BOOLEAN );
				if ( $val ) {
					$result[] = $this->search_label( $val_index, $glossary );
				}
			} else {
				$result[] = $this->search_label( $val, $glossary );
			}
		}

		return implode( $delimiter, $result );

	}

	/**
	 * Search label by value in the given glossary data
	 *
	 * @param  [type] $value    [description]
	 * @param  array  $glossary [description]
	 * @return [type]           [description]
	 */
	public function search_label( $value = null, $glossary = array() ) {

		$fields = ! empty( $glossary['fields'] ) ? $glossary['fields'] : array();

		foreach ( $fields as $field ) {
			if ( $field['value'] == $value ) {
				return $field['label'];
			}
		}

		return $value;

	}

}
