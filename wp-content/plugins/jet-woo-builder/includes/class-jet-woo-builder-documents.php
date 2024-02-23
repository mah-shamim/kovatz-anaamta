<?php
/**
 * JetWooBuilder Documents Class
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

use Elementor\Core\Editor\Editor;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Documents' ) ) {

	/**
	 * Define Jet_Woo_Builder_Documents class
	 */
	class Jet_Woo_Builder_Documents {

		protected $current_type = null;

		/**
		 * JetWooBuilder template-library post-type slug.
		 */
		protected $custom_post_type = 'jet-woo-builder';

		/**
		 * Constructor for the class
		 */
		function __construct() {

			add_action( 'elementor/documents/register', array( $this, 'register_elementor_document_types' ) );

			if ( ! class_exists( 'Jet_Theme_Core' ) && ! class_exists( 'Jet_Engine' ) ) {
				add_action( 'elementor/dynamic_tags/before_render', array( $this, 'switch_to_preview_query' ) );
				add_action( 'elementor/dynamic_tags/after_render', array( $this, 'restore_current_query' ) );
			}

			add_filter( 'admin_body_class', array( $this, 'set_admin_body_class' ) );

			add_action( 'template_redirect', [ $this, 'block_template_frontend' ] );

		}

		/**
		 * Block template frontend
		 *
		 * Don't display the single view of the template library post type in the
		 * frontend, for users that don't have the proper permissions.
		 *
		 * Fired by `template_redirect` action.
		 *
		 * @since  1.8.0
		 * @access public
		 */
		public function block_template_frontend() {
			if ( is_singular( $this->custom_post_type ) && ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
				wp_safe_redirect( site_url(), 301 );
				die;
			}
		}

		/**
		 * Set admin body classes
		 *
		 * @param $classes
		 *
		 * @return string
		 */
		function set_admin_body_class( $classes ) {

			if ( is_admin() ) {
				$document = Elementor\Plugin::instance()->documents->get( get_the_ID() );

				if ( $document ) {
					$classes .= ' ' . $document->get_name() . '-document';
				}
			}

			return $classes;

		}

		/**
		 * Set currently processed document type
		 *
		 * @param $type
		 */
		public function set_current_type( $type ) {
			$this->current_type = $type;
		}

		/**
		 * Get currently processed document type
		 *
		 * @return null
		 */
		public function get_current_type() {
			return $this->current_type;
		}

		/**
		 * Return true if currently processed certain type.
		 *
		 * @param string $type
		 *
		 * @return bool
		 */
		public function is_document_type( $type = 'single' ) {

			$doc_types = $this->get_document_types();

			if ( $doc_types[ $type ]['slug'] === $this->get_current_type() ) {
				return true;
			}

			return apply_filters( 'jet-woo-builder/documents/is-document-type', false, $type );

		}

		/**
		 * Switch to specific preview query
		 *
		 * @return void
		 */
		public function switch_to_preview_query() {

			$current_post_id = get_the_ID();
			$document        = Elementor\Plugin::instance()->documents->get_doc_or_auto_save( $current_post_id );

			if ( ! is_object( $document ) || ! method_exists( $document, 'get_preview_as_query_args' ) ) {
				return;
			}

			$new_query_vars = $document->get_preview_as_query_args();

			if ( empty( $new_query_vars ) ) {
				return;
			}

			Elementor\Plugin::instance()->db->switch_to_query( $new_query_vars );

		}

		/**
		 * Restore default query
		 *
		 * @return void
		 */
		public function restore_current_query() {
			Elementor\Plugin::instance()->db->restore_current_query();
		}

		/**
		 * Get registered document types
		 *
		 * @return array
		 */
		public function get_document_types() {
			return array(
				'single'    => array(
					'slug'  => jet_woo_builder_post_type()->slug(),
					'name'  => __( 'Single', 'jet-woo-builder' ),
					'file'  => 'includes/documents/class-jet-woo-builder-document-single.php',
					'class' => 'Jet_Woo_Builder_Document',
				),
				'archive'   => array(
					'slug'  => jet_woo_builder_post_type()->slug() . '-archive',
					'name'  => __( 'Archive Item', 'jet-woo-builder' ),
					'file'  => 'includes/documents/class-jet-woo-builder-document-archive-product.php',
					'class' => 'Jet_Woo_Builder_Archive_Document_Product',
				),
				'category'  => array(
					'slug'  => jet_woo_builder_post_type()->slug() . '-category',
					'name'  => __( 'Category Item', 'jet-woo-builder' ),
					'file'  => 'includes/documents/class-jet-woo-builder-document-archive-category.php',
					'class' => 'Jet_Woo_Builder_Archive_Document_Category',
				),
				'shop'      => array(
					'slug'  => jet_woo_builder_post_type()->slug() . '-shop',
					'name'  => __( 'Shop', 'jet-woo-builder' ),
					'file'  => 'includes/documents/class-jet-woo-builder-document-archive.php',
					'class' => 'Jet_Woo_Builder_Shop_Document',
				),
				'cart'      => array(
					'slug'  => jet_woo_builder_post_type()->slug() . '-cart',
					'name'  => __( 'Cart', 'jet-woo-builder' ),
					'file'  => 'includes/documents/class-jet-woo-builder-document-cart.php',
					'class' => 'Jet_Woo_Builder_Cart_Document',
				),
				'checkout'  => array(
					'slug'  => jet_woo_builder_post_type()->slug() . '-checkout',
					'name'  => __( 'Checkout', 'jet-woo-builder' ),
					'file'  => 'includes/documents/class-jet-woo-builder-document-checkout.php',
					'class' => 'Jet_Woo_Builder_Checkout_Document',
				),
				'thankyou'  => array(
					'slug'  => jet_woo_builder_post_type()->slug() . '-thankyou',
					'name'  => __( 'Thank You', 'jet-woo-builder' ),
					'file'  => 'includes/documents/class-jet-woo-builder-document-thankyou.php',
					'class' => 'Jet_Woo_Builder_ThankYou_Document',
				),
				'myaccount' => array(
					'slug'  => jet_woo_builder_post_type()->slug() . '-myaccount',
					'name'  => __( 'My Account', 'jet-woo-builder' ),
					'file'  => 'includes/documents/class-jet-woo-builder-document-myaccount.php',
					'class' => 'Jet_Woo_Builder_MyAccount_Document',
				),
			);
		}

		/**
		 * Register appropriate document types for 'jet-woo-builder' post type
		 *
		 * @param Elementor\Core\Documents_Manager $documents_manager [description]
		 *
		 * @return void
		 */
		public function register_elementor_document_types( $documents_manager ) {

			require jet_woo_builder()->plugin_path( 'includes/documents/class-jet-woo-builder-not-supported.php' );
			$documents_manager->register_document_type( 'jet-woo-builder-not-supported', 'Jet_Woo_Builder_Document_Not_Supported' );

			require jet_woo_builder()->plugin_path( 'includes/documents/class-jet-woo-builder-document-base.php' );

			$doc_types = $this->get_document_types();

			foreach ( $doc_types as $doc_type ) {
				require jet_woo_builder()->plugin_path( $doc_type['file'] );

				$documents_manager->register_document_type( $doc_type['slug'], $doc_type['class'] );
			}

		}

	}

}
