<?php
/**
 * Elementor views frontend class.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Elementor_Frontend' ) ) {

	class Jet_Woo_Builder_Elementor_Frontend {

		public function __construct() {
			// Modify archive item content.
			add_filter( 'jet-woo-builder/elementor-views/frontend/archive-item-content', [ $this, 'add_link_to_content' ], 10, 3 );
		}

		/**
		 * Add link to content.
		 *
		 * Returns  archive item content wrapped into link.
		 *
		 * @since  2.1.0
		 * @access public
		 *
		 * @param string        $content     Archive card content.
		 * @param string|number $template_id Archive template ID.
		 * @param object        $object      Archive item instance.
		 *
		 * @return string
		 */
		public function add_link_to_content( $content, $template_id, $object ) {

			$document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend( $template_id );

			if ( ! $document ) {
				return $content;
			}

			$settings = $document->get_settings();

			if ( empty( $settings ) || empty( $settings['archive_link'] ) ) {
				return $content;
			}

			if ( is_a( $object, 'WC_Product' ) ) {
				$object_permalink = $object->get_permalink();
			} else {
				$object_permalink = get_term_link( $object->term_id, $object->taxonomy );
			}

			$link_attrs = [
				'class' => 'jet-woo-item-overlay-link',
				'href'  => $object_permalink,
			];

			$overlay_attrs = [
				'class'    => 'jet-woo-item-overlay-wrap',
				'data-url' => $object_permalink,
			];

			$open_in_new_window = $settings['archive_link_open_in_new_window'] ?? '';

			if ( filter_var( $open_in_new_window, FILTER_VALIDATE_BOOLEAN ) ) {
				$link_attrs['target']         = '_blank';
				$overlay_attrs['data-target'] = '_blank';
			}

			return sprintf(
				'<div %3$s>%1$s<a %2$s></a></div>',
				$content,
				jet_woo_builder_tools()->get_attr_string( $link_attrs ),
				jet_woo_builder_tools()->get_attr_string( $overlay_attrs )
			);

		}

	}

}
