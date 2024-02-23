<?php
namespace Jet_Engine\Modules\Profile_Builder;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Profile_Content_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'jet-engine-profile-content';
	}

	public function get_title() {
		return __( 'Profile Subpage Content', 'jet-engine' );
	}

	public function get_icon() {
		return 'jet-engine-icon-profile-content';
	}

	public function get_categories() {
		return array( 'jet-listing-elements' );
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/article-category/jet-engine/?utm_source=jetengine&utm_medium=profile-menu&utm_campaign=need-help';
	}

	protected function register_controls() {
	}

	protected function render() {
		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			echo 'Profile content';
		} else {
			Module::instance()->frontend->render_page_content();
		}
	}

}
