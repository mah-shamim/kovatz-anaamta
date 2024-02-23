<?php
namespace Jet_Dashboard\Modules;

use Jet_Dashboard\Dashboard as Dashboard;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * Modules map
	 *
	 * @var array
	 */
	private $registered_page_modules = array();

	/**
	 * [$page_instance_modules description]
	 * @var array
	 */
	private $page_instance_modules = array();

	/**
	 * [$registered_subpage_modules description]
	 * @var array
	 */
	private $registered_subpage_modules = array();

	/**
	 * [$registered_module_category description]
	 * @var array
	 */
	private $registered_category = array();

	/**
	 * [__construct description]
	 */
	public function __construct() {

		$this->registered_category = apply_filters( 'jet-dashboard/module-manager/registered-module-category', array(
			'other' => array(
				'name'     => 'Other',
				'slug'     => 'other',
				'priority' => 100
			),
		) );

		$this->registered_page_modules = apply_filters( 'jet-dashboard/module-manager/registered-page-modules', array(
			'welcome-page' => array(
				'class' => '\\Jet_Dashboard\\Modules\\Welcome\\Module',
				'args'  => array(),
			),
			'license-page' => array(
				'class' => '\\Jet_Dashboard\\Modules\\License\\Module',
				'args'  => array(),
			),
			'settings-page' => array(
				'class' => '\\Jet_Dashboard\\Modules\\Settings\\Module',
				'args'  => array(),
			),
			'upsale-page' => array(
				'class' => '\\Jet_Dashboard\\Modules\\Upsale\\Module',
				'args'  => array(),
			),
		) );

		$this->registered_subpage_modules = apply_filters( 'jet-dashboard/module-manager/registered-subpage-modules', array(
			'dev-test-page' => array(
				'class' => '\\Jet_Dashboard\\Modules\\Welcome\\Dev_Test',
				'args'  => array(),
			),
		) );

		add_action( 'init', array( $this, 'init_modules' ), 999 );
	}

	/**
	 * Initialize modules on aproppriate AJAX or on module page
	 *
	 * @return [type] [description]
	 */
	public function init_modules() {

		$registered_page_modules = $this->get_registered_page_modules();

		foreach ( $registered_page_modules as $module_slug => $module_data ) {
			$page_module_class = $module_data['class'];

			if ( class_exists( $page_module_class ) ) {
				$page_module_args = isset( $module_data['args'] ) ? $module_data['args'] : array();
				$this->page_instance_modules[ $module_slug ] = new $page_module_class( $page_module_args );
			}
		}

		$this->maybe_load_page_module();
		$this->maybe_load_subpage_module();
	}

	/**
	 * [register_module_category description]
	 * @param  boolean $config [description]
	 * @return [type]          [description]
	 */
	public function register_module_category( $config = false ) {

		$default_config = array(
			'name'     => 'Other',
			'slug'     => 'other',
			'priority' => 100
		);

		if ( ! $config || ! isset( $config['slug'] ) || ! isset( $config['name'] ) ) {
			return false;
		}

		$category_config = wp_parse_args( $config, $default_config );

		if ( array_key_exists( $category_config['slug'], $this->registered_category ) ) {
			return;
		}

		$this->registered_category[ $category_config['slug'] ] = $category_config;

	}

	/**
	 * [register_page_module description]
	 * @return [type] [description]
	 */
	public function register_page_module( $page = false, $page_data = false ) {

		if ( ! $page || ! $page_data ) {
			return;
		}

		if ( array_key_exists( $page, $this->registered_page_modules ) ) {
			return;
		}

		if ( ! isset( $page_data['class'] ) || ! class_exists( $page_data['class'] ) ) {
			return;
		}

		$this->registered_page_modules[ $page ] = $page_data;
	}

	/**
	 * [register_subpage_module description]
	 * @param  boolean $subpage      [description]
	 * @param  boolean $subpage_data [description]
	 * @return [type]                [description]
	 */
	public function register_subpage_module( $subpage = false, $subpage_data = false ) {

		if ( ! $subpage || ! $subpage_data ) {
			return;
		}

		if ( array_key_exists( $subpage, $this->registered_subpage_modules ) ) {
			return;
		}

		if ( ! isset( $subpage_data['class'] ) || ! class_exists( $subpage_data['class'] ) ) {
			return;
		}

		$this->registered_subpage_modules[ $subpage ] = $subpage_data;
	}

	/**
	 * [get_subpage_modules description]
	 * @return [type] [description]
	 */
	public function get_registered_category() {
		return $this->registered_category;
	}

	/**
	 * [get_page_modules description]
	 * @return [type] [description]
	 */
	public function get_registered_page_modules() {
		return $this->registered_page_modules;
	}

	/**
	 * [get_subpage_modules description]
	 * @return [type] [description]
	 */
	public function get_registered_subpage_modules() {
		return $this->registered_subpage_modules;
	}

	/**
	 * Maybe load on regular request
	 *
	 * @return [type] [description]
	 */
	public function maybe_load_page_module() {

		if ( ! Dashboard::get_instance()->is_dashboard_page() ) {
			return;
		}

		$page_module = Dashboard::get_instance()->get_page();

		$this->load_page_module( $page_module );
	}

	/**
	 * Load module by slug
	 *
	 * @param  [type] $module [description]
	 * @return [type]         [description]
	 */
	public function load_page_module( $page_module ) {

		if ( ! isset( $this->page_instance_modules[ $page_module ] ) ) {
			return;
		}

		$page_module_instance = $this->page_instance_modules[ $page_module ];

		return $page_module_instance->init_module();
	}

	/**
	 * [maybe_load_subpage_module description]
	 * @return [type] [description]
	 */
	public function maybe_load_subpage_module() {

		if ( ! Dashboard::get_instance()->is_dashboard_page() ) {
			return;
		}

		$subpage_module = Dashboard::get_instance()->get_subpage();

		$this->load_subpage_module( $subpage_module );
	}

	/**
	 * [load_subpage_module description]
	 * @param  [type] $subpage_module [description]
	 * @return [type]                 [description]
	 */
	public function load_subpage_module( $subpage_module ) {

		if ( ! isset( $this->registered_subpage_modules[ $subpage_module ] ) ) {
			return;
		}

		$subpage_module_data = $this->get_subpage_module( $subpage_module );

		if ( ! $subpage_module_data ) {
			return;
		}

		$subpage_module_class = $subpage_module_data['class'];
		$subpage_module_args = isset( $subpage_module_data['args'] ) ? $subpage_module_data['args'] : array();

		$subpage_module_instance = new $subpage_module_class( $subpage_module_args );

		return $subpage_module_instance->init_module();
	}

	/**
	 * [get_module description]
	 * @param  [type] $module [description]
	 * @return [type]         [description]
	 */
	public function get_page_module( $page_module ) {

		if ( ! isset( $this->registered_page_modules[ $page_module ] ) ) {
			return false;
		}

		return $this->registered_page_modules[ $page_module ];
	}

	/**
	 * [get_subpage_module description]
	 * @param  [type] $subpage_module [description]
	 * @return [type]                 [description]
	 */
	public function get_subpage_module( $subpage_module ) {

		if ( ! isset( $this->registered_subpage_modules[ $subpage_module ] ) ) {
			return false;
		}

		return $this->registered_subpage_modules[ $subpage_module ];
	}

	/**
	 * [get_subpage_module_list description]
	 * @param  [type] $page_module [description]
	 * @return [type]              [description]
	 */
	public function get_subpage_module_list( $page_module = false ) {
		$subpage_modules = $this->get_registered_subpage_modules();

		$subpage_module_list = array();

		foreach ( $subpage_modules as $subpage_slug => $subpage_data ) {
			$subpage_module_class = $subpage_data['class'];
			$subpage_module_args = isset( $subpage_data['args'] ) ? $subpage_data['args'] : array();
			$subpage_module_instance = new $subpage_module_class( $subpage_module_args );

			$subpage_module_list[] = array(
				'name'     => $subpage_module_instance->get_page_name(),
				'parent'   => $subpage_module_instance->get_parent_slug(),
				'page'     => $subpage_module_instance->get_page_slug(),
				'link'     => $subpage_module_instance->get_page_link(),
				'category' => $subpage_module_instance->get_category(),
			);
		}

		if ( $page_module ) {
			$subpage_module_list = array_filter( $subpage_module_list, function( $subpage_module ) use ( $page_module ) {
				return $page_module === $subpage_module['parent'];
			} );
		}

		return $subpage_module_list;
	}

	/**
	 * [get_subpage_category_list description]
	 * @return [type] [description]
	 */
	public function get_subpage_category_list( $page_module ) {
		$subpage_module_list = $this->get_subpage_module_list( $page_module );

		$registered_category = $this->get_registered_category();
		$subpage_category_list = array();

		if ( array( $registered_category ) && ! empty( $registered_category ) ) {

			foreach ( $registered_category as $category => $category_data ) {

				$module_list = array();

				foreach ( $subpage_module_list as $key => $module_data ) {

					if ( $category === $module_data['category'] ) {
						$module_list[] = $module_data;
					}
				}

				if ( ! empty( $module_list ) ) {
					$category_data['moduleList'] = $module_list;
					$subpage_category_list[] = $category_data;
				}

			}

			usort( $subpage_category_list, function( $a, $b ) {
				return $a['priority'] - $b['priority'];
			} );
		}

		return $subpage_category_list;
	}

}
