<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Smart_Filters_Elementor_Dynamic_Tags_Module extends Elementor\Modules\DynamicTags\Module {

	const JET_SMART_FILTERS_GROUP = 'jet_smart_filters';

	public function get_tag_classes_names() {

		return array(
			'Jet_Smart_Filters_Elementor_Filter_URL_Tag',
		);
	}

	public function get_groups() {

		return array(
			self::JET_SMART_FILTERS_GROUP => array(
				'title' => __( 'JetSmartFilters', 'jet-engine' ),
			),
		);
	}

	/**
	 * Register tags.
	 *
	 * Add all the available dynamic tags.
	 */
	public function register_tags( $dynamic_tags ) {

		foreach ( $this->get_tag_classes_names() as $tag_class ) {
			$file     = str_replace( 'Jet_Smart_Filters_Elementor_', '', $tag_class );
			$file     = str_replace( '_', '-', strtolower( $file ) ) . '.php';
			$filepath = require jet_smart_filters()->plugin_path( 'includes/elementor/dynamic-tags/tags/' . $file );

			if ( file_exists( $filepath ) ) {
				require $filepath;
			}

			if ( class_exists( $tag_class ) ) {

				// `register_tag` method is deprecated since v3.5.0
				if ( method_exists( $dynamic_tags, 'register' ) ) {
					$dynamic_tags->register( new $tag_class );
				} else {
					$dynamic_tags->register_tag( $tag_class );
				}
			}
		}
	}
}
