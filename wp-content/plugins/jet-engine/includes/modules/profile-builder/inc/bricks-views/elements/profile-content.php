<?php

namespace Jet_Engine\Modules\Profile_Builder\Bricks_Views\Elements;

use Jet_Engine\Bricks_Views\Elements\Base;
use Jet_Engine\Modules\Profile_Builder\Module;

class Profile_Content extends Base {
	// Element properties
	public $category = 'jetengine'; // Use predefined element category 'general'
	public $name = 'jet-engine-profile-content'; // Make sure to prefix your elements
	public $icon = 'jet-engine-icon-profile-content'; // Themify icon font class
	public $css_selector = ''; // Default CSS selector
	public $scripts = [ 'jetEngineBricks' ]; // Script(s) run when element is rendered on frontend or updated in builder

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Profile Subpage Content', 'jet-engine' );
	}

	// Render element HTML
	public function render() {
		echo "<div {$this->render_attributes( '_root' )}>";
		Module::instance()->frontend->render_page_content();
		echo "</div>";
	}
}