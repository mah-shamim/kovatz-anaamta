<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Engine_Dynamic_Tags_Module extends Elementor\Modules\DynamicTags\Module {

	const JET_GROUP = 'jet_engine';

	const JET_ACTION_GROUP = 'jet_engine_action';

	const JET_MACROS_CATEGORY = 'jet_engine_macros';

	public function get_tag_classes_names() {
		return apply_filters( 'jet-engine/elementor-views/dynamic-tags/default-tags', array(
			'Jet_Engine_Custom_Image_Tag',
			'Jet_Engine_Custom_Field_Tag',
			'Jet_Engine_Custom_Gallery_Tag',
			'Jet_Engine_Term_Field_Tag',
			'Jet_Engine_Term_Image_Tag',
			'Jet_Engine_Options_Tag',
			'Jet_Engine_Options_Image_Tag',
			'Jet_Engine_Options_Gallery_Tag',
			'Jet_Engine_User_Field_Tag',
			'Jet_Engine_User_Image_Tag',
			'Jet_Engine_Dynamic_Function_Tag',
			'Jet_Engine_Macros_Tag',
			'Jet_Engine_Object_Property_Tag',
			'Jet_Engine_Object_Property_Image_Tag',
		) );
	}

	public function get_groups() {
		return array(
			self::JET_GROUP => array(
				'title' => __( 'JetEngine', 'jet-engine' ),
			),
			self::JET_ACTION_GROUP => array(
				'title' => __( 'JetEngine Actions', 'jet-engine' ),
			),
		);
	}

	/**
	 * Register tags.
	 *
	 * Add all the available dynamic tags.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param Manager $dynamic_tags
	 */
	public function register_tags( $dynamic_tags ) {

		foreach ( $this->get_tag_classes_names() as $tag_class ) {

			$file     = str_replace( 'Jet_Engine_', '', $tag_class );
			$file     = str_replace( '_', '-', strtolower( $file ) ) . '.php';
			$filepath = jet_engine()->plugin_path( 'includes/components/elementor-views/dynamic-tags/tags/' . $file );

			if ( file_exists( $filepath ) ) {
				require $filepath;
			}

			if ( class_exists( $tag_class ) ) {
				$this->register_tag( $dynamic_tags, new $tag_class );
			}

		}

		do_action( 'jet-engine/elementor-views/dynamic-tags/register', $dynamic_tags, $this );

	}

	public function register_tag( $dynamic_tags, $tag_class ) {

		// `register_tag` method is deprecated since v3.5.0
		if ( method_exists( $dynamic_tags, 'register' ) ) {
			$dynamic_tags->register( $tag_class );
		} else {
			$dynamic_tags->register_tag( $tag_class );
		}
	}
}