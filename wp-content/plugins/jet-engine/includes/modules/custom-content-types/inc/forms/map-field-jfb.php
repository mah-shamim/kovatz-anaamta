<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms;


use Jet_Engine\Modules\Custom_Content_Types\Module;
use Jet_Form_Builder\Blocks\Types\Map_Field;
use Jet_Form_Builder\Presets\Sources\Base_Source;

class Map_Field_Jfb extends Map_Field {

	public function get_extra_fields( Base_Source $source ): array {
		require_once Module::instance()->module_path( "forms/preset-source-cct.php" );

		if ( ! is_a( $source, Preset_Source_Cct::class ) ) {
			return parent::get_extra_fields( $source );
		}

		return array(
			'self' => '%prop%',
			'lat'  => '%prop%_lat',
			'lng'  => '%prop%_lng',
		);
	}

}