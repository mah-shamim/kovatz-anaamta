<?php
namespace ElementsKit\Modules\Particles;

defined( 'ABSPATH' ) || exit;

class Init {

	private $dir;
	private $url;

	public function __construct() {

		// get current directory path
		$this->dir = dirname(__FILE__) . '/';

		// get current module's url
		$this->url = \ElementsKit::plugin_url() . 'modules/particles/';

		// enqueue script , styles
		add_action( 'elementor/frontend/before_enqueue_scripts', [$this, 'enqueue_particles'] );

		// include all necessary files
		$this->include_files();

		// calling the particles controls
		new \Elementor\ElementsKit_Particles();
	}

	public function enqueue_particles() {
		wp_enqueue_style('ekit-particles', $this->url . 'assets/css/particles.css', [], \ElementsKit::version());
		wp_enqueue_script( 'particles', $this->url . 'assets/js/particles.min.js' , [], \ElementsKit::version(), true );
		wp_enqueue_script( 'ekit-particles', $this->url . 'assets/js/ekit-particles.js' , ['elementor-frontend', 'particles'], \ElementsKit::version(), true );
	}

	public function include_files() {
		include $this->dir . 'particles.php';
	}
}