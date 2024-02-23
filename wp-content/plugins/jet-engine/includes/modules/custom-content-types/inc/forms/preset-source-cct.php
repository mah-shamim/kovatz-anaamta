<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Forms;

use Jet_Engine\Modules\Custom_Content_Types\Module;
use Jet_Form_Builder\Presets\Sources\Base_Source;

class Preset_Source_Cct extends Base_Source {

	private $slug = '';

	public function query_source() {
		if ( empty( $this->slug ) ) {
			return false;
		}

		$item = Module::instance()->form_preset->get_content_type_item( $this->slug, $this->preset_data );

		if ( ! $item ) {
			return false;
		}

		return (object) $item;
	}

	protected function get_prop() {
		$prop = explode( '::', parent::get_prop() );

		if ( ! isset( $prop[1] ) ) {
			return false;
		}

		$this->slug = isset( $prop[0] ) ? $prop[0] : '_ID';

		return $prop[1];
	}

	protected function before_query_extra_field( $field ) {
		$this->prop = $field;
	}

	public function get_id() {
		return Module::instance()->form_preset->preset_source;
	}

	protected function can_get_preset() {
		
		if ( ! parent::can_get_preset() ) {
			return false;
		}
		
		$source = $this->src();

		if ( empty( $source->cct_author_id ) ) {
			return false;
		}

		if ( ! is_user_logged_in() ) {
			return false;
		}

		$cct = Module::instance()->manager->get_content_types( $source->cct_slug );

		if ( ! $cct ) {
			return false;
		}

		if ( $cct->user_has_access() ) {
			return true;
		}

		$author = absint( $source->cct_author_id );

		return $author === get_current_user_id();
	}
}