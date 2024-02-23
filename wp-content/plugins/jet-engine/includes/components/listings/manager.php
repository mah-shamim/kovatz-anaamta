<?php
/**
 * Listings manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Listings' ) ) {

	/**
	 * Define Jet_Engine_Listings class
	 */
	class Jet_Engine_Listings {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Library items id for tabs and options list
		 *
		 * @var string
		 */
		private $_id= 'jet-listing-items';

		/**
		 * Macros manager instance
		 *
		 * @var null
		 */
		public $macros = null;

		/**
		 * Did posts watcher instance
		 *
		 * @var null
		 */
		public $did_posts_watcher = null;

		/**
		 * Filters manager instance
		 *
		 * @var null
		 */
		public $filters = null;

		/**
		 * Data manager instance
		 *
		 * @var null
		 */
		public $data = null;

		/**
		 * Holder for created listings
		 *
		 * @var null
		 */
		public $listings = null;

		/**
		 * Listings post type object
		 *
		 * @var null
		 */
		public $post_type = null;

		/**
		 * Renderers list
		 *
		 * @var array
		 */
		private $_renderers = array();

		/**
		 * Holder for Delete post instance.
		 *
		 * @var null
		 */
		public $delete_post = null;

		/**
		 * Holder for Did posts instance.
		 *
		 * @var null
		 */
		public $did_posts = null;

		/**
		 * Holder for objects stack instance.
		 *
		 * @var null
		 */
		public $objects_stack = null;

		/**
		 * Holder for legacy instance.
		 *
		 * @var null
		 */
		public $legacy = null;

		/**
		 * Holder for ajax handlers instance.
		 *
		 * @var null
		 */
		public $ajax_handlers = null;

		/**
		 * Holds Jet_Engine_Listings_Callbacks instance
		 * @var null
		 */
		public $callbacks = null;

		/**
		 * Constructor for the class
		 */
		function __construct() {

			require jet_engine()->plugin_path( 'includes/components/listings/post-type.php' );
			require jet_engine()->plugin_path( 'includes/components/listings/macros.php' );
			require jet_engine()->plugin_path( 'includes/components/listings/filters.php' );
			require jet_engine()->plugin_path( 'includes/components/listings/data.php' );
			require jet_engine()->plugin_path( 'includes/components/listings/delete-post.php' );
			require jet_engine()->plugin_path( 'includes/components/listings/did-posts-watcher.php' );
			require jet_engine()->plugin_path( 'includes/components/listings/objects-stack.php' );
			require jet_engine()->plugin_path( 'includes/components/listings/legacy.php' );
			require jet_engine()->plugin_path( 'includes/components/listings/preview.php' );
			require jet_engine()->plugin_path( 'includes/classes/url-shemes-manager.php' );

			$this->post_type     = new Jet_Engine_Listings_Post_Type();
			$this->macros        = new Jet_Engine_Listings_Macros();
			$this->filters       = new Jet_Engine_Listings_Filters();
			$this->data          = new Jet_Engine_Listings_Data();
			$this->delete_post   = new Jet_Engine_Delete_Post();
			$this->did_posts     = new Jet_Engine_Did_Posts_Watcher();
			$this->objects_stack = new Jet_Engine_Objects_Stack();
			$this->legacy        = new Jet_Engine_Listings_Legacy();

			// Ensure backward compatibility
			jet_engine()->post_type = $this->post_type;

			// Frontend
			require jet_engine()->plugin_path( 'includes/components/listings/frontend.php' );
			jet_engine()->frontend = new Jet_Engine_Frontend();

			require jet_engine()->plugin_path( 'includes/components/listings/ajax-handlers.php' );
			$this->ajax_handlers = new Jet_Engine_Listings_Ajax_Handlers();

			add_action( 'init', array( $this, 'register_renderers' ) );
			add_action( 'init', array( $this, 'register_callbacks' ) );

		}

		public function register_callbacks() {
			require jet_engine()->plugin_path( 'includes/components/listings/callbacks.php' );
			$this->callbacks = new Jet_Engine_Listings_Callbacks();
		}

		/**
		 * Check if is AJAX listing request
		 */
		public function is_listing_ajax( $handler = false ) {
			return $this->ajax_handlers->is_listing_ajax( $handler );
		}

		public function repeater_sources() {
			return apply_filters( 'jet-engine/listing/repeater-sources', array(
				'jet_engine'         => __( 'JetEngine', 'jet-engine' ),
				'jet_engine_options' => __( 'JetEngine Options Page', 'jet-engine' ),
				'acf'                => __( 'ACF', 'jet-engine' ),
			) );
		}

		public function ensure_listing_doc_class() {
			if ( ! class_exists( 'Jet_Engine_Listings_Document' ) ) {
				require jet_engine()->plugin_path( 'includes/components/listings/document.php' );
			}
		}

		/**
		 * Returns new listing document
		 *
		 * @param  array  $setting [description]
		 * @return [type]          [description]
		 */
		public function get_new_doc( $setting = array(), $id = null ) {

			$this->ensure_listing_doc_class();

			return new Jet_Engine_Listings_Document( $setting, $id );
		}

		/**
		 * Return registered listings
		 *
		 * @return [type] [description]
		 */
		public function get_listings() {

			if ( null === $this->listings ) {
				$this->listings = get_posts( array(
					'post_type'      => jet_engine()->post_type->slug(),
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'orderby'        => 'ID',
					'order'          => 'DESC',
				) );
			}

			return $this->listings;
		}

		/**
		 * Get listings list for options.
		 *
		 * @param string $context Context: elementor or blocks
		 *
		 * @return array
		 */
		public function get_listings_for_options( $context = 'elementor' ) {
			$listings = $this->get_listings();
			$list = wp_list_pluck( $listings, 'post_title', 'ID' );

			$result = array();

			if ( 'blocks' === $context ) {

				$result[] = array(
					'value' => '',
					'label' => esc_html__( 'Select...', 'jet-engine' ),
				);

				foreach ( $list as $value => $label ) {
					$result[] = array(
						'value' => $value,
						'label' => $label,
					);
				}

			} else {
				$result = array( '' => esc_html__( 'Select...', 'jet-engine' ) ) + $list;
			}

			return $result;
		}

		/**
		 * Get widget hide options.
		 *
		 * @param string $context Context: elementor or blocks
		 *
		 * @return array
		 */
		public function get_widget_hide_options( $context = 'elementor' ) {

			$hide_options = apply_filters( 'jet-engine/listing/grid/widget-hide-options', array(
				''            => __( 'Always show', 'jet-engine' ),
				'empty_query' => __( 'Query is empty', 'jet-engine' ),
			) );

			$result = array();

			if ( 'blocks' === $context ) {
				foreach ( $hide_options as $value => $label ) {
					$result[] = array(
						'value' => $value,
						'label' => $label,
					);
				}

			} else {
				$result = $hide_options;
			}

			return $result;
		}

		/**
		 * Return Listings items slug/ID
		 *
		 * @return [type] [description]
		 */
		public function get_id() {
			return $this->_id;
		}

		/**
		 * Get post types list for options.
		 *
		 * @return array
		 */
		public function get_post_types_for_options() {
			$post_types = get_post_types( array(), 'objects', 'and' );
			$post_types = wp_list_pluck( $post_types, 'label', 'name' );

			if ( isset( $post_types[ jet_engine()->post_type->slug() ] ) ) {
				unset( $post_types[ jet_engine()->post_type->slug() ] );
			}

			return $post_types;

		}

		/**
		 * Returns image size array in slug => name format
		 *
		 * @return  array
		 */
		public function get_image_sizes( $context = 'elementor' ) {
			return Jet_Engine_Tools::get_image_sizes( $context );
		}

		/**
		 * Get post taxonomies for options.
		 *
		 * @return array
		 */
		public function get_taxonomies_for_options() {

			$args = array(
				'public' => true,
			);

			$taxonomies = get_taxonomies( $args, 'objects', 'and' );

			return apply_filters(
				'jet-engine/listings/taxonomies-for-options',
				wp_list_pluck( $taxonomies, 'label', 'name' )
			);
		}

		/**
		 * Register renderers classes.
		 */
		public function register_renderers() {
			$default_renderers = array(
				'dynamic-field'    => 'Jet_Engine_Render_Dynamic_Field',
				'dynamic-image'    => 'Jet_Engine_Render_Dynamic_Image',
				'dynamic-repeater' => 'Jet_Engine_Render_Dynamic_Repeater',
				'dynamic-meta'     => 'Jet_Engine_Render_Dynamic_Meta',
				'dynamic-link'     => 'Jet_Engine_Render_Dynamic_Link',
				'dynamic-terms'    => 'Jet_Engine_Render_Dynamic_Terms',
				'listing-grid'     => 'Jet_Engine_Render_Listing_Grid',
			);

			foreach ( $default_renderers as $render_name => $render_class ) {
				$render_data = array(
					'class_name' => $render_class,
					'path'       => jet_engine()->plugin_path( 'includes/components/listings/render/' . $render_name . '.php' ),
				);

				$this->register_render_class( $render_name, $render_data );
			}

			do_action( 'jet-engine/listings/renderers/registered', $this );
		}

		/**
		 * Register render class.
		 *
		 * @param string $name Render item name
		 * @param array  $data {
		 *     Array of arguments for registering a render class.
		 *
		 *     @type string $class_name Class name.
		 *     @type string $path       File path.
		 *     @type array  $deps       Optional. Dependencies items.
		 * }
		 */
		public function register_render_class( $name, $data ) {
			$this->_renderers[ $name ] = $data;
		}

		/**
		 * Returns current render instance
		 *
		 * @param null  $item
		 * @param array $settings
		 *
		 * @return object|void
		 */
		public function get_render_instance( $item = null, $settings = array() ) {

			$current_renderer = isset( $this->_renderers[ $item ] ) ? $this->_renderers[ $item ] : false;

			if ( ! $current_renderer ) {
				return;
			}

			if ( empty( $current_renderer['class_name'] ) || empty( $current_renderer['path'] ) ) {
				return;
			}

			if ( ! class_exists( 'Jet_Engine_Render_Base' ) ) {
				require jet_engine()->plugin_path( 'includes/components/listings/render/base.php' );
			}

			// Include deps classes
			if ( ! empty( $current_renderer['deps'] ) ) {
				foreach ( (array) $current_renderer['deps'] as $dep ) {

					$dep_renderer = isset( $this->_renderers[ $dep ] ) ? $this->_renderers[ $dep ] : false;

					if ( ! $dep_renderer ) {
						continue;
					}

					if ( empty( $dep_renderer['class_name'] ) || empty( $dep_renderer['path'] ) ) {
						continue;
					}

					if ( ! class_exists( $dep_renderer['class_name'] ) ) {
						require $dep_renderer['path'];
					}

				}
			}

			$renderer_class = $current_renderer['class_name'];

			if ( ! class_exists( $renderer_class ) ) {
				require $current_renderer['path'];
			}

			return new $renderer_class( $settings );
		}

		/**
		 * Render listing
		 *
		 * @param array $settings
		 */
		public function render_listing( $settings = array() ) {

			$instance = $this->get_render_instance( 'listing-grid', $settings );

			$instance->before_listing_grid();
			$instance->render_content();
			$instance->after_listing_grid();

		}

		/**
		 * Render new listing item part
		 *
		 * @param  [type] $item     [description]
		 * @param  [type] $settings [description]
		 * @return [type]           [description]
		 */
		public function render_item( $item = null, $settings = array() ) {
			$instance = $this->get_render_instance( $item, $settings );
			$instance->render_content();
		}

		/**
		 * Returns allowed fields callbacks
		 *
		 * @return [type] [description]
		 */
		public function get_allowed_callbacks() {
			return $this->callbacks ? $this->callbacks->get_cllbacks_for_options() : array();
		}

		/**
		 * Returns allowed callback arguments list
		 *
		 * @return [type] [description]
		 */
		public function get_callbacks_args( $for = 'elementor' ) {
			return $this->callbacks ? $this->callbacks->get_callbacks_args( $for ) : array();
		}

		/**
		 * Apply filter callback
		 *
		 * @return [type] [description]
		 */
		public function apply_callback( $input = null, $callback = null, $settings = array(), $widget = null ) {
			return $this->callbacks->apply_callback( $input, $callback, $settings, $widget );
		}

		public function allowed_context_list( $for = 'elementor' ) {

			$context = apply_filters( 'jet-engine/listings/allowed-context-list', array(
				'default_object'      => __( 'Default Object', 'jet-engine' ),
				'wp_user'             => __( 'Current User (global)', 'jet-engine' ),
				'current_user'        => __( 'Current User (for current scope)', 'jet-engine' ),
				'queried_user'        => __( 'Queried User', 'jet-engine' ),
				'current_post_author' => __( 'Current Post Author', 'jet-engine' ),
				'wp_object'           => __( 'Default WordPress Object (for current page)', 'jet-engine' ),
			) );

			if ( 'blocks' === $for ) {
				$for_blocks = array();

				foreach ( $context as $value => $label ) {
					$for_blocks[] = array(
						'value' => $value,
						'label' => $label,
					);
				}

				return $for_blocks;

			} else {
				return $context;
			}

		}

		/**
		 * Returns allowed link sources for listing item.
		 *
		 * @return array
		 */
		public function get_listing_link_sources() {

			$default = array(
				'label'   => __( 'General', 'jet-engine' ),
				'options' => array(
					'_permalink' => __( 'Permalink', 'jet-engine' ),
				),
			);

			$meta_fields = array();

			if ( jet_engine()->options_pages ) {
				$default['options']['options_page'] = __( 'Options', 'jet-engine' );
			}

			if ( jet_engine()->meta_boxes ) {
				$meta_fields = jet_engine()->meta_boxes->get_fields_for_select( 'plain' );
			}

			$link_sources = apply_filters(
				'jet-engine/listings/link/sources',
				array_merge( array( $default ), $meta_fields )
			);

			return apply_filters(
				'jet-engine/listings/dynamic-link/fields',
				$link_sources
			);
		}

	}

}
