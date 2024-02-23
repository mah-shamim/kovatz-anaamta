<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms\Converting_Jfb_Fields;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Base_Convert_Field {

	public $current_type = '';
	public $raw_field = array();
	public $prepared_field = array();

	/**
	 * @return array
	 */
	abstract public function block_names();

	protected function save_attr( $name, $value ) {
		if ( ! isset( $this->prepared_field['attrs'] ) ) {
			$this->prepared_field['attrs'] = array();
		}
		$this->prepared_field['attrs'][ $name ] = $value;

		return $this;
	}

	protected function isset_attr( $name ) {
		return isset( $this->prepared_field['attrs'][ $name ] );
	}

	protected function is_type( $name ) {
		return ( $name === $this->current_type );
	}

	protected function get_attr( $name ) {
		return $this->prepared_field['attrs'][ $name ];
	}

	protected function manual_attrs() {
		return array(
			'title'       => array(
				'name' => 'label',
			),
			'name'        => array(
				'name' => 'name',
			),
			'description' => array(
				'name' => 'desc',
			),
			'is_required' => array(
				'name' => 'required'
			),
			'default_val' => array(
				'name' => 'default'
			),
		);
	}

	protected function convert_manual_attrs() {
		foreach ( $this->manual_attrs() as $raw_attr => $prepared ) {
			if ( ! isset( $this->raw_field[ $raw_attr ] ) || empty( $this->raw_field[ $raw_attr ] ) ) {
				continue;
			}
			$value = $this->raw_field[ $raw_attr ];

			if ( isset( $prepared['callable'] ) && is_callable( $prepared['callable'] ) ) {
				$value = call_user_func( $prepared['callable'], $value );
			}

			$this->save_attr( $prepared['name'], $value );
		}
	}

	public function custom_converting() {
	}

	/**
	 * @param $raw_field
	 *
	 * @return array
	 * @throws Convert_Field_Exception
	 */
	public function get_prepared_field( $raw_field ) {
		$this->raw_field = $raw_field;
		$this->set_block_name( $raw_field['type'] );
		$this->convert_manual_attrs();
		$this->custom_converting();

		return $this->prepared_field;
	}

	/**
	 * @param $type
	 *
	 * @throws Convert_Field_Exception
	 */
	public function set_block_name( $type ) {
		if ( ! array_key_exists( $type, $this->block_names() ) ) {
			throw new Convert_Field_Exception( "Please set block_name for this type: {$type}" );
		}
		$this->current_type                = $type;
		$this->prepared_field['blockName'] = $this->block_names()[ $type ];
	}

}