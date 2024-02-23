<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Elementor_Pro_Package' ) ) {

	/**
	 * Define Jet_Engine_Elementor_Pro_Package class
	 */
	class Jet_Engine_Elementor_Pro_Package {

		/**
		 * Constructor for the class
		 */
		public function __construct() {
			add_filter( 'jet-engine/listing/data/custom-listing',  array( $this, 'set_locations_listings' ), 10, 3 );
			add_filter( 'jet-engine/listings/data/default-object', array( $this, 'set_default_object_on_form_submit' ), 10, 2 );

			add_action( 'elementor/init', array( $this, 'on_elementor_init' ) );

			add_filter( 'jet-engine/listings/data/the-post/is-main-query', array( $this, 'maybe_modify_is_main_query' ), 10, 3 );
			add_filter( 'jet-engine/listings/data/default-object',         array( $this, 'set_default_object_on_ajax' ) );

		}

		public function on_elementor_init() {
			$is_active_page_transitions = Elementor\Plugin::instance()->experiments->is_feature_active( 'page-transitions' );

			if ( $is_active_page_transitions ) {
				add_filter(
					'jet-engine/listings/dynamic-link/custom-attrs',
					array( $this, 'disable_page_transition_for_delete_link' ),
					10, 2
				);
			}
		}

		public function disable_page_transition_for_delete_link( $attrs, $render ) {

			if ( ! $render->is_delete_link() ) {
				return $attrs;
			}

			$attrs .= ' data-e-disable-page-transition';

			return $attrs;
		}

		/**
		 * Set locations listings
		 */
		public function set_locations_listings( $listing, $data_manager, $default_object ) {

			if ( ! isset( $default_object->post_type ) ) {
				return $listing;
			}

			if ( 'elementor_library' !== $default_object->post_type ) {
				return $listing;
			}

			if ( ! class_exists( 'Elementor\Plugin' ) ) {
				return $listing;
			}

			$elementor = Elementor\Plugin::instance();

			if ( ! $elementor->editor->is_edit_mode() ) {
				return $listing;
			}

			$document = $elementor->documents->get_doc_or_auto_save( $default_object->ID );

			if ( ! $document ) {
				return $listing;
			}

			$settings = $document->get_settings();

			if ( empty( $settings['preview_type'] ) ) {
				return $listing;
			}

			if ( false === strpos( $settings['preview_type'], 'single' ) ) {
				return $listing;
			}

			$preview = explode( '/', $settings['preview_type'] );

			if ( empty( $preview[1] ) ) {
				return $listing;
			}

			return array(
				'listing_source'    => 'posts',
				'listing_post_type' => $preview[1],
				'listing_tax'       => 'category',
			);

		}

		public function set_default_object_on_form_submit( $default_object, $data_instance ) {

			if ( ! class_exists( 'ElementorPro\Modules\Forms\Classes\Ajax_Handler' ) ) {
				return $default_object;
			}

			if ( ElementorPro\Modules\Forms\Classes\Ajax_Handler::is_form_submitted() && ! empty( $_REQUEST['queried_id'] ) ) {
				$post_id = $_REQUEST['queried_id'];
				$default_object = get_post( $post_id );
			}

			return $default_object;
		}

		public function maybe_modify_is_main_query( $is_main_query, $post, $query ) {

			if ( ! class_exists( 'Elementor\Plugin' ) ) {
				return $is_main_query;
			}

			$elementor = Elementor\Plugin::instance();

			if ( ! $elementor->editor->is_edit_mode() ) {
				return $is_main_query;
			}

			$document = $elementor->documents->get_current();

			if ( ! $document || ! $document instanceof ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document ) {
				return $is_main_query;
			}

			$settings = $document->get_settings();

			if ( empty( $settings['preview_type'] ) ) {
				return $is_main_query;
			}

			if ( false === strpos( $settings['preview_type'], 'taxonomy' ) ) {
				return $is_main_query;
			}

			if ( empty( $settings['preview_id'] ) ) {
				return $is_main_query;
			}

			if ( $query->query === $document->get_preview_as_query_args() ) {
				return true;
			}

			return $is_main_query;
		}

		public function set_default_object_on_ajax( $default_object ) {

			if ( ! wp_doing_ajax() ) {
				return $default_object;
			}

			if ( empty( $_REQUEST['action'] ) || 'elementor_ajax' !== $_REQUEST['action'] ) {
				return $default_object;
			}

			if ( empty( $_REQUEST['initial_document_id'] ) ) {
				return $default_object;
			}

			$elementor = Elementor\Plugin::instance();
			$document  = $elementor->documents->get_doc_or_auto_save( $_REQUEST['initial_document_id'] );

			if ( ! $document || ! $document instanceof ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document ) {
				return $default_object;
			}

			$settings = $document->get_settings();

			if ( empty( $settings['preview_type'] ) ) {
				return $default_object;
			}

			if ( false === strpos( $settings['preview_type'], 'taxonomy' ) ) {
				return $default_object;
			}

			if ( empty( $settings['preview_id'] ) ) {
				return $default_object;
			}

			$term = get_term( $settings['preview_id'] );

			if ( $term && ! is_wp_error( $term ) ) {
				return $term;
			}

			return $default_object;
		}

	}

}

new Jet_Engine_Elementor_Pro_Package();
