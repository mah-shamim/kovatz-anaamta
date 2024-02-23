<?php
namespace ElementsKit\Modules\Masking;

defined( 'ABSPATH' ) || exit;

class Init {

	private $dir;
	private $url;

	public function __construct(){

		// get current directory path
		$this->dir = dirname(__FILE__) . '/';

		// get current module's url
		$this->url = \ElementsKit::plugin_url() . 'modules/masking/';

		// // include all necessary files
		$this->include_files();

		// // calling the sticky controls
		new \Elementor\ElementsKit_Extend_Masking();
	}
	
	public function include_files() {

		include $this->dir . 'extend-controls.php';
	}
}