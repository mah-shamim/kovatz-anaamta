<?php
namespace Jet_Engine\Modules\Custom_Content_Types;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( '\Jet_Engine\Modules\Custom_Content_Types\Pages\Edit_Item_Page' ) ) {
	require_once Module::instance()->module_path( 'pages/edit-content-type-item.php' );
}

/**
 * Define Quick_Edit page class
 */
class Quick_Edit extends Pages\Edit_Item_Page {

	/**
	 * Current page data
	 *
	 * @var null
	 */
	public $page = null;

	/**
	 * Current page slug
	 *
	 * @var null
	 */
	public $slug = null;

	/**
	 * Prepared fields array
	 *
	 * @var null
	 */
	public $prepared_fields = null;

	/**
	 * Holder for is page or not is page now prop
	 *
	 * @var null
	 */
	public $is_page_now = null;

	/**
	 * Inerface builder instance
	 *
	 * @var null
	 */
	public $builder = null;

	/**
	 * Constructor for the class
	 */
	public function __construct( $page, $pages_manager ) {
		$this->page     = $page;
		$this->meta_box = $page['fields'];
	}

	/**
	 * Initialize page builder
	 *
	 * @return [type] [description]
	 */
	public function init_builder() {

		$builder_data = jet_engine()->framework->get_included_module_data( 'cherry-x-interface-builder.php' );

		$this->builder = new \CX_Interface_Builder(
			array(
				'path' => $builder_data['path'],
				'url'  => $builder_data['url'],
			)
		);

		$this->setup_page_fields();

		$fields = $this->get_prepared_fields();

		$this->builder->register_section(
			array(
				'settings_top' => array(
					'type'   => 'section',
					'scroll' => false,
					'class'  => 'fields-count-' . count( $fields ),
				),
			)
		);

		$this->builder->register_control( $fields );

	}

	public function render_fields() {
		$this->init_builder();
		$this->builder->render();
	}
}
