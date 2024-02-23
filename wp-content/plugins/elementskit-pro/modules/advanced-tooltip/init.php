<?php
namespace ElementsKit\Modules\Advanced_Tooltip;

defined( 'ABSPATH' ) || exit;

class Init {
	private $dir;
	private $url;
	
	public function __construct() {
		global $post;

		// get current directory path
		$this->dir = dirname(__FILE__) . '/';

		// get current module's url
		$this->url = \ElementsKit::plugin_url() . 'modules/advanced-tooltip/';

		// Register Editor Scripts
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		// add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_editor_styles' ], 9999999 );

		// include all necessary files
		$this->include_files();

		// calling the advanced tooltip
		new \Elementor\ElementsKit_Extend_Advanced_Tooltip();
	}

	public function include_files() {
		include $this->dir . 'extend-controls.php';
	}

	/**
	 * Always load on Editor
	 * !need optimization: similar method is also written on modules/advanced-tooltip/extend-controls.php file.
	 */
	public function enqueue_scripts() {
		if ( !\Elementor\Plugin::$instance->preview->is_preview_mode() ) return;

		wp_enqueue_style( 'tippy-custom', $this->url . 'assets/css/tippy-custom.css', [], \ElementsKit::version() );

		wp_deregister_script( 'popper' );
		wp_deregister_script( 'tippyjs' );

		wp_enqueue_script( 'popper-defer', $this->url . 'assets/js/popper.min.js', ['jquery'], \ElementsKit::version(), true );
		wp_enqueue_script( 'tippyjs-defer', $this->url . 'assets/js/tippy.min.js', ['jquery'], \ElementsKit::version(), true );
		wp_enqueue_script( 'ekit-adv-tooltip-defer', $this->url . 'assets/js/init.js', ['jquery', 'elementor-frontend'], \ElementsKit::version(), true );
	}
}
