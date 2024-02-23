<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms;


use Jet_Engine\Modules\Custom_Content_Types\Module;

class Query_Cct_Data {

	public static function cct_list() {
		$content_types = array();

		foreach ( Module::instance()->manager->get_content_types() as $slug => $type ) {
			$name = $type->get_arg( 'name' );
			$name = $name ? $name : $slug;

			$content_types[] = array(
				'value' => $slug,
				'label' => $name,
			);
		}

		return $content_types;
	}

	public static function cct_statuses_list() {
		$types = Module::instance()->manager->get_content_types();

		if ( empty( $types ) ) {
			return array();
		}

		$first_type = array_shift( $types );

		return $first_type->get_statuses();
	}

}