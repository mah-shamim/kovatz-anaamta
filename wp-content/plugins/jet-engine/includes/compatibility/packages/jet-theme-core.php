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

if ( ! class_exists( 'Jet_Engine_Theme_Core_Package' ) ) {

	/**
	 * Define Jet_Engine_Theme_Core_Package class
	 */
	class Jet_Engine_Theme_Core_Package {

		private $current_template = array();

		/**
		 * Constructor for the class
		 */
		public function __construct() {
			add_filter( 'jet-engine/listing/data/custom-listing', array( $this, 'set_locations_listings' ), 10, 3 );

			add_action( 'jet-theme-core/theme-builder/render/location/before', array( $this, 'set_current_template' ), 10, 3 );

			$locations = array(
				'header',
				'footer',
				'single',
				'page',
				'archive',
				'products-archive',
				'products-card',
				'account-page',
				'products-checkout-endpoint',
				'single-product',
				'products-checkout',
			);

			foreach ( $locations as $location ) {
				add_filter( "jet-theme-core/theme-builder/render/{$location}-location/after", array( $this, 'reset_current_template' ) );
			}

			add_filter( 'jet-engine/listing/grid/lazy-load/post-id', array( $this, 'maybe_set_post_id_for_lazy_load' ) );
		}

		/**
		 * Set locations listings
		 */
		public function set_locations_listings( $listing, $data_manager, $default_object ) {

			if ( ! isset( $default_object->post_type ) ) {
				return $listing;
			}

			if ( 'jet-theme-core' !== $default_object->post_type ) {
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

			if ( empty( $settings['preview_post_type'] ) ) {
				return $listing;
			}

			return array(
				'listing_source'    => 'posts',
				'listing_post_type' => $settings['preview_post_type'],
				'listing_tax'       => 'category',
			);

		}

		public function set_current_template( $location, $template_id, $content_type ) {
			$this->current_template = array(
				'template_id'  => $template_id,
				'location'     => $location,
				'content_type' => $content_type,
			);
		}

		public function reset_current_template() {
			$this->current_template = array();
		}

		public function maybe_set_post_id_for_lazy_load( $post_id ) {

			if ( empty( $this->current_template ) ) {
				return $post_id;
			}

			if ( empty( $this->current_template['content_type'] ) || 'default' !== $this->current_template['content_type'] ) {
				return $post_id;
			}

			if ( empty( $this->current_template['template_id'] ) ) {
				return $post_id;
			}

			return $this->current_template['template_id'];
		}

	}

}

new Jet_Engine_Theme_Core_Package();
