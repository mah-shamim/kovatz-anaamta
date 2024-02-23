<?php
namespace Jet_Engine\Modules\Data_Stores\Dynamic_Tags;

use Jet_Engine\Modules\Data_Stores\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Get_Store extends \Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'jet-data-store-get-store';
	}

	public function get_title() {
		return __( 'Data Stores: Get Store', 'jet-engine' );
	}

	public function get_group() {
		return \Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::POST_META_CATEGORY,
		);
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {

		$this->add_control(
			'data_store',
			array(
				'label'   => __( 'Store', 'jet-engine' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => Module::instance()->elementor_integration->get_store_options(),
			)
		);

	}

	public function get_value( array $options = array() ) {

		$store = $this->get_settings( 'data_store' );

		if ( ! $store ) {
			return;
		}

		$store_instance = Module::instance()->stores->get_store( $store );

		if ( ! $store_instance ) {
			return;
		}

		$store_items = $store_instance->get_store();

		if ( empty( $store_items ) ) {
			$store_items = array( 'not-found' );
		}

		return implode( ',', $store_items );

	}

}
