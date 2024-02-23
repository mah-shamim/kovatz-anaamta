<?php

namespace ElementsKit\Modules\Pro_Form_Reset_Button;

defined('ABSPATH') || exit;

class Init
{
	private $dir;
	private $url;

	public function __construct()
	{
		if(!class_exists('\ElementorPro\Core\App\App' ) ){
			return;
		}
        // get current directory path
        $this->dir = dirname(__FILE__) . '/';

		// get current module's url
		$this->url = \ElementsKit::plugin_url() . 'modules/pro-form-reset-button/';

		// enqueue scripts
		add_action('elementor/frontend/before_enqueue_scripts', [$this, 'editor_scripts']);

		// // include all necessary files
		$this->include_files();

		// calling the sticky controls
		new Reset_Button();
	}

	public function include_files()
	{
		include $this->dir . 'reset-button.php';
	}

	public function editor_scripts()
	{
		wp_enqueue_style('elementskit-reset-button-for-pro-form-css', $this->url . 'assets/css/elementskit-reset-button.css', [], \ElementsKit::version());

		wp_enqueue_script('elementskit-reset-button', $this->url . 'assets/js/elementskit-reset-button.js', array('jquery', 'elementor-frontend'), \ElementsKit::version(), true);
	}
}
