<?php
namespace Jet_Engine\Query_Builder\Dynamic_Tags;

use Jet_Engine\Query_Builder\Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Query_Count_Tag extends \Elementor\Core\DynamicTags\Tag {

	use \Jet_Engine\Query_Builder\Traits\Query_Count_Trait;

	public function get_name() {
		return 'jet-query-count';
	}

	public function get_group() {
		return \Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::NUMBER_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::POST_META_CATEGORY,
		);
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {
		foreach ( $this->get_args() as $control_id => $control_args ) {
			$this->add_control( $control_id, $control_args );
		}
	}

	public function render() {
		echo $this->get_result( $this->get_settings() );
	}

}
