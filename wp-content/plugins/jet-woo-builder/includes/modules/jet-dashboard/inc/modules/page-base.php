<?php
namespace Jet_Dashboard\Base;

use Jet_Dashboard\Dashboard as Dashboard;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Page_Module {

	/**
	 * [$args description]
	 * @var array
	 */
	public $args = array();

	/**
	 * [get_page_slug description]
	 * @return [type] [description]
	 */
	abstract public function get_page_slug();

	/**
	 * [get_subpage_slug description]
	 * @return [type] [description]
	 */
	abstract public function get_parent_slug();

	/**
	 * [get_page_name description]
	 * @return [type] [description]
	 */
	abstract public function get_page_name();

	/**
	 * [get_category description]
	 * @return [type] [description]
	 */
	abstract public function get_category();

	/**
	 * [get_page_link description]
	 * @return [type] [description]
	 */
	abstract public function get_page_link();

	/**
	 * [__construct description]
	 * @param array $args [description]
	 */
	public function __construct( $args = array() ) {
		$this->args = wp_parse_args( $args, $this->args );

		$this->create();
	}

	/**
	 * Initialize module-specific parts
	 *
	 * @return [type] [description]
	 */
	public function init_module() {

		$this->init();

		add_action( 'jet-dashboard/before-enqueue-assets', array( $this, 'assets' ) );

		return $this;
	}

	/**
	 * [create description]
	 * @return [type] [description]
	 */
	public function create() {}

	/**
	 * [init description]
	 * @return [type] [description]
	 */
	public function init() {}

	/**
	 * Register module assets
	 *
	 * @return [type] [description]
	 */
	public function assets() {

		$this->enqueue_module_assets();

		add_filter( 'jet-dashboard/js-page-config', array( $this, 'page_config' ), 10, 3 );

		add_filter( 'jet-dashboard/js-page-templates', array( $this, 'page_templates' ), 10, 3 );
	}

	/**
	 * Enqueue module-specific assets
	 *
	 * @return void
	 */
	public function enqueue_module_assets() {}

	/**
	 * Modify page config
	 *
	 * @param  [type] $config [description]
	 * @return [type]         [description]
	 */
	public function page_config( $config = array(), $page = false, $subpage = false ) {
		return $config;
	}

	/**
	 * Add page templates
	 *
	 * @param  [type] $config [description]
	 * @return [type]         [description]
	 */
	public function page_templates( $templates = array(), $page = false, $subpage = false ) {
		return $templates;
	}

}
