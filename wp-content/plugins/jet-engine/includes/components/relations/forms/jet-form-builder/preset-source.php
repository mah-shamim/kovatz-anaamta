<?php


namespace Jet_Engine\Relations\Forms\Jet_Form_Builder_Forms;


use Jet_Engine\Relations\Forms\Manager as Forms;
use Jet_Form_Builder\Exceptions\Preset_Exception;
use Jet_Form_Builder\Presets\Sources\Base_Source;

class Preset_Source extends Base_Source {

	public function get_id() {
		return Forms::instance()->slug();
	}

	public function is_need_prop() {
		return false;
	}

	/**
	 * @return false
	 * @throws Preset_Exception
	 */
	public function query_source() {
		$preset = Forms::instance()->get_preset_items( $this->preset_data );

		if ( ! $preset ) {
			throw new Preset_Exception( 'Empty relations preset', $preset );
		}

		return $preset;
	}
}