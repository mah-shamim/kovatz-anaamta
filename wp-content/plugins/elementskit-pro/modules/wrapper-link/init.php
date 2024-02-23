<?php
namespace ElementsKit\Modules\Wrapper_Link;

defined( 'ABSPATH' ) || exit;

class Init {
	private $dir;
	private $url;

	public function __construct() {

		// get current directory path
		$this->dir = dirname(__FILE__) . '/';

		// get current module's url
		$this->url = \ElementsKit::plugin_url() . 'modules/wrapper-link/';

		// enqueue scripts
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_wrapper_scripts' ] );

		// include all necessary files
		$this->include_files();

		// calling the wrapper controls
		new \Elementor\ElementsKit_Wrapper_Link();
		
	}

	public function include_files() {
		include $this->dir . 'wrapper-link.php';
	}

	public function enqueue_wrapper_scripts() {
		wp_enqueue_script( 'elementskit-wrapper', $this->url . 'assets/js/wrapper.js' , ['jquery'], \ElementsKit::version(), true );
	}
}