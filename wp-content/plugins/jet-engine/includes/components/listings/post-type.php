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

if ( ! class_exists( 'Jet_Engine_Listings_Post_Type' ) ) {

	/**
	 * Define Jet_Engine_Listings_Post_Type class
	 */
	class Jet_Engine_Listings_Post_Type {

		/**
		 * Post type slug.
		 *
		 * @var string
		 */
		public $post_type = 'jet-engine';

		public $admin_screen = null;

		private $nonce_action = 'jet-engine-listings';

		/**
		 * Constructor for the class
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'register_post_type' ) );

			if ( ! empty( $_GET['elementor-preview'] ) ) {
				add_action( 'template_include', array( $this, 'set_editor_template' ), 9999 );
			}

			if ( is_admin() ) {
				
				add_action( 'admin_menu', array( $this, 'add_templates_page' ), 20 );
				add_action( 'add_meta_boxes_' . $this->slug(), array( $this, 'disable_metaboxes' ), 9999 );
				add_action( 'admin_enqueue_scripts', array( $this, 'listings_page_assets' ) );

				add_filter( 'post_row_actions', array( $this, 'remove_view_action' ), 10, 2 );
				add_action( 'current_screen', array( $this, 'no_elementor_notice' ) );

			}

			require_once jet_engine()->plugin_path( 'includes/components/listings/admin-screen.php' );
			$this->admin_screen = new Jet_Engine_Listing_Admin_Screen( $this->slug() );

			add_action( 'wp', array( $this, 'set_singular_preview_object' ) );

		}

		/**
		 * Setup correct preview object when opening listing item on the front-end directly
		 * Required for correct rendering of the editor mode for some builders or for public preview
		 */
		public function set_singular_preview_object() {
			if ( is_singular( $this->slug() ) ) {
				// Setup preview instance for current listing
				$preview = new Jet_Engine_Listings_Preview( array(), get_the_ID() );
				// Store preview object as root insted of listing item WP_Post object
				jet_engine()->listings->objects_stack->set_root_object( $preview->get_preview_object() );
				// Avoid JetEngine from trying to set current object by itself (causing reseting of current object)
				remove_action( 'the_post', array( jet_engine()->listings->data, 'maybe_set_current_object' ), 10, 2 );
			}
		}

		/**
		 * Add notice on listings page if Elementor not installed
		 *
		 * @return void
		 */
		public function no_elementor_notice() {

			if ( jet_engine()->has_elementor() ) {
				return;
			}

			$screen = get_current_screen();

			if ( $screen->id !== 'edit-' . $this->slug() ) {
				return;
			}

		}

		/**
		 * Actions posts
		 *
		 * @param  [type] $actions [description]
		 * @param  [type] $post    [description]
		 * @return [type]          [description]
		 */
		public function remove_view_action( $actions, $post ) {

			if ( $this->slug() === $post->post_type ) {
				unset( $actions['view'] );
			}

			return $actions;

		}

		public function listing_form_assets( $force_print_templates = false, $vars = array() ) {
			wp_enqueue_script(
				'jet-listings-form',
				jet_engine()->plugin_url( 'assets/js/admin/listings-popup.js' ),
				array( 'jquery' ),
				jet_engine()->get_version(),
				true
			);

			wp_localize_script( 'jet-listings-form', 'JetListingsSettings', array_merge( array(
				'hasElementor' => jet_engine()->has_elementor(),
				'exclude'      => array(),
				'defaults'     => array(),
				'_nonce'       => wp_create_nonce( $this->nonce_action ),
			), $vars ) );

			wp_enqueue_style(
				'jet-listings-form',
				jet_engine()->plugin_url( 'assets/css/admin/listings.css' ),
				array(),
				jet_engine()->get_version()
			);

			if ( $force_print_templates ) {
				$this->print_listings_popup();
			} else {
				add_action( 'admin_footer', array( $this, 'print_listings_popup' ), 999 );
			}
			
		}

		public function get_nonce_action() {
			return $this->nonce_action;
		}

		public function listings_page_assets() {

			$screen = get_current_screen();

			if ( $screen->id !== 'edit-' . $this->slug() ) {
				return;
			}

			$this->listing_form_assets();

			jet_engine()->get_video_help_popup( array(
				'popup_title' => __( 'What is Listing Grid?', 'jet-engine' ),
				'embed' => 'https://www.youtube.com/embed/JxvtMzwHGIw',
			) )->wp_page_popup();

		}

		/**
		 * Returns available listing sources list
		 *
		 * @return [type] [description]
		 */
		public function get_listing_item_sources() {
			return apply_filters( 'jet-engine/templates/listing-sources', array(
				'posts'    => __( 'Posts', 'jet-engine' ),
				'query'    => __( 'Query Builder', 'jet-engine' ),
				'terms'    => __( 'Terms', 'jet-engine' ),
				'users'    => __( 'Users', 'jet-engine' ),
				'repeater' => __( 'Repeater Field', 'jet-engine' ),
			) );
		}

		public function get_listing_views() {
			return apply_filters( 'jet-engine/templates/listing-views', array() );
		}

		/**
		 * Print template type form HTML
		 *
		 * @return void
		 */
		public function print_listings_popup() {
			echo $this->admin_screen->get_listing_popup();
		}

		/**
		 * Templates post type slug
		 *
		 * @return string
		 */
		public function slug() {
			return $this->post_type;
		}

		/**
		 * Disable metaboxes from Jet Templates
		 *
		 * @return void
		 */
		public function disable_metaboxes() {
			global $wp_meta_boxes;
			unset( $wp_meta_boxes[ $this->slug() ]['side']['core']['pageparentdiv'] );
		}

		/**
		 * Register templates post type
		 *
		 * @return void
		 */
		public function register_post_type() {

			$args = array(
				'labels' => array(
					'name'               => esc_html__( 'Listing Items', 'jet-engine' ),
					'singular_name'      => esc_html__( 'Listing Item', 'jet-engine' ),
					'add_new'            => esc_html__( 'Add New', 'jet-engine' ),
					'add_new_item'       => esc_html__( 'Add New Item', 'jet-engine' ),
					'edit_item'          => esc_html__( 'Edit Item', 'jet-engine' ),
					'new_item'           => esc_html__( 'Add New Item', 'jet-engine' ),
					'view_item'          => esc_html__( 'View Item', 'jet-engine' ),
					'search_items'       => esc_html__( 'Search Item', 'jet-engine' ),
					'not_found'          => esc_html__( 'No Templates Found', 'jet-engine' ),
					'not_found_in_trash' => esc_html__( 'No Templates Found In Trash', 'jet-engine' ),
					'menu_name'          => esc_html__( 'My Library', 'jet-engine' ),
				),
				'public'              => false,
				'hierarchical'        => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'show_in_nav_menus'   => false,
				'show_in_rest'        => true,
				'can_export'          => true,
				'exclude_from_search' => true,
				'capability_type'     => 'post',
				'rewrite'             => false,
				'supports'            => array( 'title', 'editor', 'thumbnail', 'author', 'elementor', 'custom-fields' ),
			);

			if ( current_user_can( 'edit_posts' ) ) {
				$args['public'] = true;
			}

			register_post_type(
				$this->slug(),
				apply_filters( 'jet-engine/templates/post-type/args', $args )
			);

		}

		/**
		 * Menu page
		 */
		public function add_templates_page() {

			$views = $this->get_listing_views();

			if ( empty( $views ) ) {
				return;
			}

			add_submenu_page(
				jet_engine()->admin_page,
				esc_html__( 'Listings', 'jet-engine' ),
				esc_html__( 'Listings', 'jet-engine' ),
				'edit_pages',
				'edit.php?post_type=' . $this->slug()
			);

		}

		/**
		 * Editor templates.
		 *
		 * @param  string $template Current template name.
		 * @return string
		 */
		public function set_editor_template( $template ) {

			$found = false;

			if ( is_singular( $this->slug() ) ) {
				$found    = true;
				$template = jet_engine()->plugin_path( 'templates/blank.php' );
			}

			if ( $found ) {
				do_action( 'jet-engine/post-type/editor-template/found' );
			}

			return $template;

		}

	}

}
