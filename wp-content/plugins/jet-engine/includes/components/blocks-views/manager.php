<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Blocks_Views' ) ) {

	/**
	 * Define Jet_Engine_Blocks_Views class
	 */
	class Jet_Engine_Blocks_Views {

		public $editor;
		public $render;
		public $block_types;
		public $dynamic_content;

		/**
		 * Constructor for the class
		 */
		function __construct() {

			if ( ! \Jet_Engine\Modules\Performance\Module::instance()->is_tweak_active( 'enable_blocks_views' ) ) {
				return;
			}

			if ( ! jet_engine()->components->is_component_active( 'listings' ) ) {
				return;
			}

			add_filter( 'jet-engine/templates/listing-views', array( $this, 'add_listing_view' ), 11 );
			add_filter( 'upload_mimes', array( $this, 'allow_svg' ) );
			add_filter( 'jet-engine/templates/create/data', array( $this, 'inject_listing_settings' ), 0 );

			if ( is_admin() ) {
				require $this->component_path( 'editor.php' );
				$this->editor = new Jet_Engine_Blocks_Views_Editor();
			}

			require $this->component_path( 'render.php' );
			require $this->component_path( 'block-types.php' );
			require $this->component_path( 'ajax-handlers.php' );
			require $this->component_path( 'dynamic-content/manager.php' );

			$this->render          = new Jet_Engine_Blocks_Views_Render();
			$this->block_types     = new Jet_Engine_Blocks_Views_Types();
			$this->dynamic_content = new \Jet_Engine\Blocks_Views\Dynamic_Content\Manager();

			new Jet_Engine_Blocks_Views_Ajax_Handlers();
		}

		/**
		 * Register listing view
		 * 
		 * @param [type] $views [description]
		 */
		public function add_listing_view( $views ) {
			$views['blocks'] = __( 'Blocks (Gutenberg)', 'jet-engine' );
			return $views;
		}

		/**
		 * Allow SVG images uploading
		 *
		 * @return array
		 */
		public function allow_svg( $mimes ) {
			$mimes['svg'] = 'image/svg+xml';
			return $mimes;
		}

		/**
		 * Return path to file inside component
		 *
		 * @param  [type] $path_inside_component [description]
		 * @return [type]                        [description]
		 */
		public function component_path( $path_inside_component ) {
			return jet_engine()->plugin_path( 'includes/components/blocks-views/' . $path_inside_component );
		}

		/**
		 * Return listing template ediit URL to redirect on
		 * @return [type] [description]
		 */
		public function get_redirect_url( $template_id ) {
			return get_edit_post_link( $template_id, '' );
		}

		/**
		 * Check if current listing is rendered with blocks
		 * @param  [type]  $listing_id [description]
		 * @return boolean             [description]
		 */
		public function is_blocks_listing( $listing_id ) {

			/**
			 * Do not use get_listing_type() method here 
			 * because this method itself used inside get_listing_type() to define what listing type was used
			 */
			$meta = get_post_meta( $listing_id, '_listing_type', true );

			if ( ! $meta ) {
				$post    = get_post( $listing_id );
				$content = $post ? $post->post_content : '';

				if ( false !== strrpos( $content, 'wp:jet-engine' ) ) {
					$meta = 'blocks';
					update_post_meta( $listing_id, '_listing_type', $meta );
				}

			}

			return ( 'blocks' === $meta );
		}

		/**
		 * Inject listing settings from template into _elementor_page_settings meta
		 * @param  [type] $template_data [description]
		 * @return [type]                [description]
		 */
		public function inject_listing_settings( $template_data ) {

			if ( empty( $_REQUEST['listing_view_type'] ) || 'blocks' !== $_REQUEST['listing_view_type'] ) {
				return $template_data;
			}

			if ( ! isset( $_REQUEST['listing_source'] ) ) {
				return $template_data;
			}

			return $template_data;

		}

	}

}
