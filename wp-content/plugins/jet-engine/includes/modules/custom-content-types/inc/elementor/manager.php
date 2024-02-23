<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Elementor;

use Jet_Engine\Modules\Custom_Content_Types\Module;

class Manager {

	public function __construct() {

		$register_controls_action = 'elementor/controls/controls_registered';

		if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
			$register_controls_action = 'elementor/controls/register';
		}

		add_action( 'jet-engine/elementor-views/dynamic-tags/register', array( $this, 'register_dynamic_tags' ), 10, 2 );
		add_action( $register_controls_action, array( $this, 'add_controls' ), 10 );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'editor_scripts' ), 0 );
		add_action( 'jet-engine/listing/custom-query-settings', array( $this, 'register_query_settings' ) );
		add_action( 'jet-engine/map-listing/custom-query-settings', array( $this, 'register_query_settings' ) );

		add_action( 'elementor/element/jet-smart-filters-sorting/section_sorting_list/before_section_end', array( $this, 'add_sorting_filter_notice' ) );
	}

	public function add_sorting_filter_notice( $widget ) {

		$widget->add_control(
			'cct_sorting_notice',
			array(
				'type'      => \Elementor\Controls_Manager::RAW_HTML,
				'separator' => 'before',
				'raw'       => __( '<i><b>Custom Content Type listing note:</b><br><br>Use <b>Meta key numeric</b> (for numeric fields) or <b>Meta Key</b> (for the rest of the fields) choices from the <b>Order By</b> option and put CCT field name into <b>Meta key</b> option</i>', 'jet-engine' ),
			)
		);

	}

	public function register_query_settings( $widget ) {

		$widget->start_controls_section(
			'section_jet_cct_query',
			array(
				'label' => __( 'Content Types Query', 'jet-appointments-booking' ),
			)
		);

		$widget->add_control(
			'jet_cct_query',
			array(
				'label'        => __( 'Set up query', 'jet-engine' ),
				'button_label' => __( 'Query Settings', 'jet-engine' ),
				'type'         => 'jet_query_dialog',
			)
		);

		do_action( 'jet-engine/custom-content-types/elementor/after-query-control', $widget );

		$widget->end_controls_section();

	}

	public function register_dynamic_tags( $dynamic_tags, $tags_module ) {

		require_once Module::instance()->module_path( 'elementor/dynamic-tags/field-tag.php' );
		require_once Module::instance()->module_path( 'elementor/dynamic-tags/image-tag.php' );
		require_once Module::instance()->module_path( 'elementor/dynamic-tags/gallery-tag.php' );

		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tags\Field_Tag() );
		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tags\Image_Tag() );
		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tags\Gallery_Tag() );

	}

	public function add_controls( $controls_manager ) {

		require_once Module::instance()->module_path( 'elementor/controls/query-dialog.php' );

		if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
			$controls_manager->register( new Controls\Query_Dialog_Control() );
		} else {
			$controls_manager->register_control( 'jet_query_dialog', new Controls\Query_Dialog_Control() );
		}

	}

	public function editor_scripts() {
		Module::instance()->query_dialog()->assets();

		$ui_theme = \Elementor\Core\Settings\Manager::get_settings_managers( 'editorPreferences' )->get_model()->get_settings( 'ui_theme' );

		if ( in_array( $ui_theme, array( 'auto', 'dark' ) ) ) {
			$media_queries = false;

			if ( 'auto' === $ui_theme ) {
				$media_queries = '(prefers-color-scheme: dark)';
			}

			Module::instance()->query_dialog()->add_dark_theme_style( $media_queries );
		}
	}

}
