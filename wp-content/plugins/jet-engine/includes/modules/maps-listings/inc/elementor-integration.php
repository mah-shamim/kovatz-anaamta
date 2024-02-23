<?php
namespace Jet_Engine\Modules\Maps_Listings;

class Elementor_Integration {

	use Preview_Trait;

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		if ( ! jet_engine()->has_elementor() ) {
			return;
		}

		add_action( 'jet-engine/elementor-views/widgets/register', array( $this, 'register_widgets' ), 99, 2 );

		add_action( 'jet-engine/listings/preview-scripts', array( $this, 'preview_scripts' ) );

		add_action( 'jet-engine/elementor-views/dynamic-tags/register', array( $this, 'register_dynamic_tags' ), 10, 2 );
	}

	/**
	 * Register widgets
	 */
	public function register_widgets( $widgets_manager, $elementor_views ) {

		$elementor_views->register_widget(
			jet_engine()->modules->modules_path( 'maps-listings/inc/widgets/maps-listings-widget.php' ),
			$widgets_manager,
			__NAMESPACE__ . '\Maps_Listings_Widget'
		);

	}

	/**
	 * Register dynamic tags
	 *
	 * @param $dynamic_tags
	 * @param $tags_module
	 */
	public function register_dynamic_tags( $dynamic_tags, $tags_module ) {

		require_once jet_engine()->modules->modules_path( 'maps-listings/inc/dynamic-tags/open-map-popup.php' );

		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tags\Open_Map_Popup() );

	}

}
