<?php
namespace Jet_Dashboard\Compatibility;

use Jet_Dashboard\Dashboard as Dashboard;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Compatibility Manager
 */
class Manager {

	/**
	 * Modules map
	 *
	 * @var array
	 */
	private $registered_themes = array();

	/**
	 * [$registered_subpage_modules description]
	 * @var array
	 */
	private $registered_plugins = array();

	/**
	 * [__construct description]
	 */
	public function __construct() {

		$this->registered_themes = apply_filters( 'jet-dashboard/compatibility-manager/registered-themes', array(
			'helloelementor' => array(
				'class' => '\\Jet_Dashboard\\Compatibility\\Theme\\Hello',
				'args'  => array(),
			),
		) );

		$this->registered_plugins = apply_filters( 'jet-dashboard/compatibility-manager/registered-plugins', array() );

		$this->maybe_load_theme_module();
	}

	/**
	 * [maybe_load_theme_module description]
	 * @return [type] [description]
	 */
	public function maybe_load_theme_module() {
		$style_parent_theme = wp_get_theme( get_template() );

		$theme_slug = strtolower( preg_replace('/\s+/', '', $style_parent_theme->get('Name') ) );

		$this->load_theme_compat_class( $theme_slug );
	}

	/**
	 * Load module by slug
	 *
	 * @param  [type] $module [description]
	 * @return [type]         [description]
	 */
	public function load_theme_compat_class( $theme ) {

		$theme_module_data = $this->get_theme_compatibility_module( $theme );

		if ( ! $theme_module_data ) {
			return;
		}

		$theme_module_class = $theme_module_data['class'];
		$theme_module_args = isset( $theme_module_data['args'] ) ? $theme_module_data['args'] : array();
		$theme_module_instance = new $theme_module_class( $theme_module_args );

		return $theme_module_instance;
	}

	/**
	 * [get_module description]
	 * @param  [type] $module [description]
	 * @return [type]         [description]
	 */
	public function get_theme_compatibility_module( $theme ) {

		if ( ! isset( $this->registered_themes[ $theme ] ) ) {
			return false;
		}

		return $this->registered_themes[ $theme ];
	}
}
