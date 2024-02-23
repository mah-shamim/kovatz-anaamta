<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms\Converting_Jfb_Fields;


use Jet_Engine\Modules\Custom_Content_Types\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * @property Base_Convert_Field[] _registered_fields
 *
 * Class Convert_Manager
 * @package Jet_Engine\Modules\Custom_Content_Types\Forms\Converting_Jfb_Fields
 */
class Convert_Manager {

	private $_registered_fields = array();
	public $fields_map = array();
	private static $instance = null;

	private function __construct() {
		$this->require_once_class( 'base-convert-field.php' );
		$this->require_once_class( 'block-generator.php' );
		$this->require_once_class( 'convert-field-exception.php' );
		$this->require_once_class( 'text-field.php' );
		$this->require_once_class( 'date-field.php' );
		$this->require_once_class( 'repeater-field.php' );
		$this->require_once_class( 'simple-field.php' );
		$this->require_once_class( 'switcher-field.php' );
		$this->require_once_class( 'media-field.php' );
		$this->require_once_class( 'checkbox-field.php' );
		$this->require_once_class( 'select-posts-field.php' );
		$this->require_once_class( 'number-field.php' );

		$this->_registered_fields = $this->_register_fields();
	}

	private function _register_fields() {
		return array(
			new Text_Field(),
			new Date_Field(),
			new Repeater_Field(),
			new Switcher_Field(),
			new Simple_Field(),
			new Media_Field(),
			new Checkbox_Field(),
			new Select_Posts_Field(),
			new Number_Field(),
		);
	}

	public function prepare_fields( $fields ) {
		$form_fields = array();

		foreach ( $fields as $field ) {
			if ( isset( $field['object_type'] ) && 'field' !== $field['object_type'] ) {
				continue;
			}
			try {
				$field_obj     = $this->get_field( $field['type'] );
				$form_fields[] = $field_obj->get_prepared_field( $field );
				if ( isset( $field['name'] ) ) {
					$this->fields_map[ $field['name'] ] = $field['name'];
				}

			} catch ( Convert_Field_Exception $exception ) {
				continue;
			}
		}

		return $form_fields;
	}

	/**
	 * @param $type
	 *
	 * @return Base_Convert_Field
	 * @throws Convert_Field_Exception
	 */
	private function get_field( $type ) {
		$found = array_filter( $this->_registered_fields, function ( $field ) use ( $type ) {
			return array_key_exists( $type, $field->block_names() );
		} );

		if ( empty( $found ) ) {
			throw new Convert_Field_Exception( "Undefined field type: {$type}" );
		}

		$field = array_shift( $found );

		return clone $field;
	}

	private function require_once_class( $path ) {
		require_once Module::instance()->module_path( "forms/converting-jfb-fields/{$path}" );
	}

	public static function instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}