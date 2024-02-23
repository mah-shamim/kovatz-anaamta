<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Elementor_Views' ) ) {

	/**
	 * Define Jet_Engine_Elementor_Views class
	 */
	class Jet_Engine_Elementor_Views {

		/**
		 * Elementor Frontend instance
		 *
		 * @var null
		 */
		public $frontend = null;

		/**
		 * Constructor for the class
		 */
		function __construct() {

			if ( ! jet_engine()->has_elementor() ) {
				add_filter( 'jet-engine/data/listing-type', array( $this, 'reset_listing_types' ) );
				return;
			}

			if ( ! jet_engine()->components->is_component_active( 'listings' ) ) {
				return;
			}

			add_filter( 'jet-engine/templates/listing-views', array( $this, 'add_elementor_listing_view' ) );

			add_filter( 'jet-engine/templates/create/data', array( $this, 'inject_listing_settings' ) );

			add_action( 'elementor/documents/register', array( $this, 'register_document_type' ) );

			add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );

			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
				add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ), 10 );
			} else {
				add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ), 10 );
			}

			add_filter( 'body_class', array( $this, 'add_body_classes' ) );

			add_action( 'elementor/dynamic_tags/before_render', array( $this, 'switch_to_preview_query' ) );
			add_action( 'elementor/dynamic_tags/after_render', array( $this, 'restore_current_query' ) );

			add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_icons_styles' ) );
			add_action( 'elementor/preview/enqueue_styles',      array( $this, 'enqueue_icons_styles' ) );

			add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'set_editor_listing' ) );

			add_action( 'current_screen', array( $this, 'no_elementor_notice' ) );

			require jet_engine()->plugin_path( 'includes/components/elementor-views/dynamic-tags/manager.php' );
			require jet_engine()->plugin_path( 'includes/components/elementor-views/frontend.php' );

			jet_engine()->dynamic_tags = new Jet_Engine_Dynamic_Tags_Manager();
			$this->frontend            = new Jet_Engine_Elementor_Frontend();

			// Fix listing while widgets config set up
			add_action( 'elementor/ajax/register_actions', array( $this, 'set_listing_on_ajax' ), -1 );

			// Init Jet Elementor Extension module
			$ext_module_data = jet_engine()->framework->get_included_module_data( 'jet-elementor-extension.php' );

			Jet_Elementor_Extension\Module::get_instance(
				array(
					'path' => $ext_module_data['path'],
					'url'  => $ext_module_data['url'],
				)
			);


			add_filter( 'jet-engine/listings/dynamic-image/size',      array( $this, 'prepare_custom_image_size' ), 10, 3 );
			add_filter( 'jet-engine/listings/dynamic-image/link-attr', array( $this, 'add_lightbox_attr' ), 10, 2 );

			add_filter( 'jet-engine/gallery/lightbox-attr', array( $this, 'add_lightbox_attr_for_gallery' ), 10, 3 );

		}

		/**
		 * Reset given listing type if Elementor is not installed
		 */
		public function reset_listing_types( $listing_type ) {
			
			if ( 'elementor' === $listing_type ) {
				$listing_type = 'blocks';
			}
			
			return $listing_type;
		}

		/**
		 * Setup current listing for editor
		 */
		public function set_editor_listing() {

			$post_id = \Elementor\Plugin::instance()->editor->get_post_id();
			$this->setup_listing_doc( $post_id );

			jet_engine()->listings->post_type->listing_form_assets( true, array(
				'isAjax'   => true,
				'exclude'  => array( 'listing_view_type' ),
				'button'   => array(
					'css_class' => 'elementor-button elementor-button-default elementor-button-success',
				),
				'defaults' => array(
					'listing_view_type' => 'elementor',
				)
			) );

			// Removed the click event on `.page-title-action` selector in the Elementor editor to prevent conflicts.
			$inline_script = "jQuery( document ).off( 'click.JetListings', '.page-title-action', window.JetListings.openPopup );";
			wp_add_inline_script( 'jet-listings-form', $inline_script );
		}

		/**
		 * Setup default main listing document
		 *
		 * @param  [type] $post_id [description]
		 * @return [type]          [description]
		 */
		public function setup_listing_doc( $post_id ) {

			$settings = get_post_meta( $post_id, '_elementor_page_settings', true );

			if ( empty( $settings ) || ! isset( $settings['listing_source'] ) ) {

				$post_type = get_post_type( $post_id );

				if ( jet_engine()->post_type->slug() !== $post_type ) {
					jet_engine()->listings->data->set_listing( jet_engine()->listings->get_new_doc( array(
						'listing_source'    => 'posts',
						'listing_post_type' => $post_type,
						'listing_tax'       => false,
						'is_main'           => true,
					), $post_id ) );
				}

			} else {
				$source = ! empty( $settings['listing_source'] ) ? esc_attr( $settings['listing_source'] ) : 'posts';
				$post_type = ! empty( $settings['listing_post_type'] ) ? esc_attr( $settings['listing_post_type'] ) : get_post_type( $post_id );
				$tax = ! empty( $settings['listing_tax'] ) ? esc_attr( $settings['listing_tax'] ) : '';

				jet_engine()->listings->data->set_listing( jet_engine()->listings->get_new_doc( array(
					'listing_source'    => $source,
					'listing_post_type' => $post_type,
					'listing_tax'       => $tax,
					'repeater_source'   => ! empty( $settings['repeater_source'] ) ? $settings['repeater_source'] : '',
					'repeater_field'    => ! empty( $settings['repeater_field'] ) ? $settings['repeater_field'] : '',
					'repeater_option'   => ! empty( $settings['repeater_option'] ) ? $settings['repeater_option'] : '',
					'is_main'           => true,
				), $post_id ) );
			}



		}

		/**
		 * Set listing on ajax widgets cnfig updating
		 */
		public function set_listing_on_ajax( $ajax_manager ) {

			if ( empty( $_REQUEST['actions'] ) ) {
				return;
			}

			if ( false === strpos( $_REQUEST['actions'], 'get_widgets_config' ) ) {
				return;
			}

			if ( empty( $_REQUEST['editor_post_id'] ) ) {
				return;
			}

			$post_id = $_REQUEST['editor_post_id'];

			$this->setup_listing_doc( $post_id );
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

			if ( $screen->id !== 'edit-' . jet_engine()->post_type->slug() ) {
				return;
			}

			add_action( 'admin_notices', array( $this, 'no_elementor_warning' ) );

		}

		/**
		 * Print no elementor notice
		 *
		 * @return [type] [description]
		 */
		public function no_elementor_warning() {

			$install_url = add_query_arg(
				array(
					's'    => 'elementor',
					'tab'  => 'search',
					'type' => 'term',
				),
				admin_url( 'plugin-install.php' )
			);

			?>
			<div class="notice notice-warning">
				<p><?php
					_e( 'You need an <b>Elementor Page Builder</b> plugin to create and edit listing items', 'jet-engine' );
				?></p>
				<p>
					<a href="<?php echo $install_url; ?>">
						<b><?php _e( 'Install Elementor Page Builder', 'jet-engine' ); ?></b>
					</a>
				</p>
			</div>
			<?php
		}

		/**
		 * Enqueue icons styles
		 *
		 * @return void
		 */
		public function enqueue_icons_styles() {

			wp_enqueue_style(
				'jet-engine-icons',
				jet_engine()->plugin_url( 'assets/lib/jetengine-icons/icons.css' ),
				array(),
				jet_engine()->get_version()
			);

		}

		/**
		 * Switch to specific preview query
		 *
		 * @return void
		 */
		public function switch_to_preview_query() {

			$current_post_id = get_the_ID();

			if ( jet_engine()->post_type->slug() !== get_post_type( $current_post_id ) ) {
				return;
			}

			$document = Elementor\Plugin::instance()->documents->get_doc_or_auto_save( $current_post_id );

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
		 * Add body classes
		 */
		public function add_body_classes( $classes ) {

			$template_type = get_post_meta( get_the_ID(), '_elementor_template_type', true );

			if ( 'jet-listing-items' === $template_type ) {
				$classes[] = 'jet-listing-item';
			}

			return $classes;
		}

		/**
		 * Register cherry category for elementor if not exists
		 *
		 * @return void
		 */
		public function register_category( $elements_manager ) {
			$elements_manager->add_category(
				'jet-listing-elements',
				array(
					'title' => esc_html__( 'Listing Elements', 'jet-engine' ),
					'icon'  => 'font',
				)
			);
		}

		public function include_base_widget() {
		
			if ( ! class_exists( '\Jet_Elementor_Widgets_Storage' ) ) {
				require jet_engine()->plugin_path( 'includes/components/elementor-views/widgets-storage.php' );
			}

			if ( ! class_exists( '\Jet_Listing_Dynamic_Widget' ) ) {
				require jet_engine()->plugin_path( 'includes/components/elementor-views/dynamic-widgets/dynamic-widget.php' );
			}
			
		}

		/**
		 * Register listing widgets
		 *
		 * @return void
		 */
		public function register_widgets( $widgets_manager ) {

			$base = jet_engine()->plugin_path( 'includes/components/elementor-views/' );

			$this->include_base_widget();

			foreach ( glob( $base . 'dynamic-widgets/*.php' ) as $file ) {
				$this->register_widget( $file, $widgets_manager );
			}

			foreach ( glob( $base . 'static-widgets/*.php' ) as $file ) {
				$this->register_widget( $file, $widgets_manager );
			}

			do_action( 'jet-engine/elementor-views/widgets/register', $widgets_manager, $this );

		}


		/**
		 * Register new widget
		 *
		 * @return void
		 */
		public function register_widget( $file = '', $widgets_manager = null, $class = false ) {

			if ( ! $class ) {
				$base  = basename( str_replace( '.php', '', $file ) );
				$class = ucwords( str_replace( '-', ' ', $base ) );
				$class = str_replace( ' ', '_', $class );
				$class = sprintf( 'Elementor\Jet_Listing_%s_Widget', $class );
			}

			require_once $file;

			if ( class_exists( $class ) ) {

				if ( method_exists( $widgets_manager, 'register' ) ) {
					$widgets_manager->register( new $class );
				} else {
					$widgets_manager->register_widget_type( new $class );
				}
			}

		}

		/**
		 * Register apropriate Document Types for listing items
		 *
		 * @return void
		 */
		public function register_document_type( $documents_manager ) {

			$base_path = jet_engine()->plugin_path( 'includes/components/elementor-views/document-types/' );

			require $base_path . 'listing-item.php';
			require $base_path . 'not-supported.php';

			$documents_manager->register_document_type(
				jet_engine()->listings->get_id(),
				'Jet_Listing_Item_Document'
			);

			$documents_manager->register_document_type(
				'jet-engine-not-supported',
				'Jet_Engine_Not_Supported'
			);

		}

		/**
		 * Return listing template ediit URL to redirect on
		 * @return [type] [description]
		 */
		public function get_redirect_url( $template_id ) {

			if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
				return '';
			}

			if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '<' ) ) {
				$redirect = Elementor\Utils::get_edit_link( $template_id );
			} else {
				$document = Elementor\Plugin::$instance->documents->get( $template_id );
				$redirect = $document ? $document->get_edit_url() : false;
			}

			return $redirect;
		}

		/**
		 * Inject listing settings from template into _elementor_page_settings meta
		 * @param  [type] $template_data [description]
		 * @return [type]                [description]
		 */
		public function inject_listing_settings( $template_data ) {

			if ( empty( $_REQUEST['listing_view_type'] ) || 'elementor' !== $_REQUEST['listing_view_type'] ) {
				return $template_data;
			}

			if ( ! class_exists( 'Elementor\Plugin' ) ) {
				wp_die(
					__( 'Please install <a href="https://wordpress.org/plugins/elementor/" target="_blank">Elementor page builder</a> to manage listings layout', 'jet-engine' ),
					__( 'Elementor missed', 'jet-engine' )
				);
			}

			$documents = Elementor\Plugin::instance()->documents;
			$doc_type  = $documents->get_document_type( jet_engine()->listings->get_id() );

			if ( ! $doc_type ) {
				wp_die(
					esc_html__( 'Incorrect template type. Please try again.', 'jet-engine' ),
					esc_html__( 'Error', 'jet-engine' )
				);
			}

			if ( ! isset( $_REQUEST['listing_source'] ) ) {
				return $template_data;
			}

			$template_data['meta_input']['_elementor_edit_mode'] = 'builder';
			$template_data['meta_input'][ $doc_type::TYPE_META_KEY ] = jet_engine()->listings->get_id();

			return $template_data;

		}

		/**
		 * Add the Elementor listing view type
		 *
		 * @param  array $views
		 * @return array
		 */
		public function add_elementor_listing_view( $views ) {
			$views = array( 'elementor' => __( 'Elementor', 'jet-engine' ) ) + $views;
			return $views;
		}

		/**
		 * Is editor ajax.
		 *
		 * @return bool
		 */
		public function is_editor_ajax() {
			return is_admin() && isset( $_REQUEST['action'] ) && 'elementor_ajax' === $_REQUEST['action'];
		}

		/**
		 * Prepare custom image size.
		 *
		 * @param string $size
		 * @param string $img_size_key
		 * @param array  $settings
		 *
		 * @return array|string
		 */
		public function prepare_custom_image_size( $size, $img_size_key, $settings ) {

			if ( 'custom' !== $size ) {
				return $size;
			}

			if ( empty( $settings[ $img_size_key . '_custom_dimension' ] ) ) {
				return $size;
			}

			// Use BFI_Thumb script
			require_once ELEMENTOR_PATH . 'includes/libraries/bfi-thumb/bfi-thumb.php';

			$custom_dimension = $settings[ $img_size_key . '_custom_dimension' ];

			$attachment_size = array(
				// Defaults sizes
				0 => null, // Width.
				1 => null, // Height.

				'bfi_thumb' => true,
				'crop'      => true,
			);

			$has_custom_size = false;

			if ( ! empty( $custom_dimension['width'] ) ) {
				$has_custom_size = true;
				$attachment_size[0] = $custom_dimension['width'];
			}

			if ( ! empty( $custom_dimension['height'] ) ) {
				$has_custom_size = true;
				$attachment_size[1] = $custom_dimension['height'];
			}

			if ( ! $has_custom_size ) {
				$attachment_size = 'full';
			}

			return $attachment_size;
		}

		/**
		 * Add lightbox attr for Dynamic Image Link.
		 *
		 * @param $attr
		 * @param $settings
		 *
		 * @return mixed
		 */
		public function add_lightbox_attr( $attr, $settings ) {

			if ( empty( $settings['image_link_source'] ) ) {
				return $attr;
			}

			if ( '_file' !== $settings['image_link_source'] ) {
				return $attr;
			}

			if ( ! isset( $settings['lightbox'] ) ) {
				return $attr;
			}

			$lightbox = filter_var( $settings['lightbox'], FILTER_VALIDATE_BOOLEAN );

			$attr['data-elementor-open-lightbox'] = $lightbox ? 'yes' : 'no';

			return $attr;
		}

		public function add_custom_size_unit( $units ) {

			if ( version_compare( ELEMENTOR_VERSION, '3.10.0', '>=' ) ) {
				$units[] = 'custom';
			}

			return $units;
		}

		/**
		 * Add lightbox attr for Slider and Grid Gallery.
		 *
		 * @param array  $attr
		 * @param array  $img_data
		 * @param string $gallery_id
		 *
		 * @return mixed
		 */
		public function add_lightbox_attr_for_gallery( $attr, $img_data, $gallery_id ) {

			$attr['data-elementor-open-lightbox'] = 'yes';

			if ( ! empty( $gallery_id ) ) {
				$attr['data-elementor-lightbox-slideshow='] = $gallery_id;
			}

			if ( ! empty( $img_data['id'] ) ) {
				$lightbox_image_attr = \Elementor\Plugin::instance()->images_manager->get_lightbox_image_attributes( $img_data['id'] );

				if ( isset( $lightbox_image_attr['title'] ) ) {
					$attr['data-elementor-lightbox-title'] = $lightbox_image_attr['title'];
				}

				if ( isset( $lightbox_image_attr['description'] ) ) {
					$attr['data-elementor-lightbox-description'] = $lightbox_image_attr['description'];
				}
			}

			return $attr;
		}

	}

}
