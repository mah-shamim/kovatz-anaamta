<?php
namespace Jet_Engine\Modules\Data_Stores\Dynamic_Tags;

use Jet_Engine\Modules\Data_Stores\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Store_Count extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'jet-data-store-store-count';
	}

	public function get_title() {
		return __( 'Data Stores: Store Count', 'jet-engine' );
	}

	public function get_group() {
		return \Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::NUMBER_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::URL_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::POST_META_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::COLOR_CATEGORY
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

	public function render() {

		$store = $this->get_settings( 'data_store' );

		if ( ! $store ) {
			return;
		}

		echo Module::instance()->render->store_count( $store );
		
	}

}
