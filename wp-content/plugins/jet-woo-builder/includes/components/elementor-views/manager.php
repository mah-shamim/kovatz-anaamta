<?php
/**
 * Elementor views manager class.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Elementor_Views' ) ) {

	class Jet_Woo_Builder_Elementor_Views {

		function __construct() {

			add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );

			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
				add_action( 'elementor/widgets/register', [ $this, 'include_wc_hooks' ], 0 );
				add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ], 10 );
			} else {
				add_action( 'elementor/widgets/widgets_registered', [ $this, 'include_wc_hooks' ], 0 );
				add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ], 10 );
			}

			if ( ! empty( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] && is_admin() ) {
				add_action( 'init', [ $this, 'register_frontend_wc_hooks' ], 5 );
			}

			add_action( 'elementor/page_templates/canvas/before_content', [ $this, 'open_canvas_wrap' ] );
			add_action( 'elementor/page_templates/canvas/after_content', [ $this, 'close_canvas_wrap' ] );

			add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'editor_styles' ] );
			add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'init_editor_wc_cart' ] );

			add_filter( 'jet-woo-builder/shortcodes/query-types', [ $this, 'set_additional_query_types' ] );

			$controls_register_action = 'elementor/controls/controls_registered';

			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
				$controls_register_action = 'elementor/controls/register';
			}

			add_action( $controls_register_action, [ $this, 'controls_register' ], 10 );

			add_filter( 'post_class', [ $this, 'add_post_class' ], 20 );

			add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_single_template_css' ] );

			require jet_woo_builder()->plugin_path( 'includes/components/elementor-views/dynamic-tags/manager.php' );
			require jet_woo_builder()->plugin_path( 'includes/components/elementor-views/frontend.php' );

			jet_woo_builder()->dynamic_tags = new Jet_Woo_Builder_Dynamic_Tags_Manager();
			new Jet_Woo_Builder_Elementor_Frontend();

			// Init Jet Elementor Extension module.
			$ext_module_data = jet_woo_builder()->module_loader->get_included_module_data( 'jet-elementor-extension.php' );
			Jet_Elementor_Extension\Module::get_instance( $ext_module_data );

			// Elementor Pro compatibility.
			if ( class_exists( '\ElementorPro\Plugin' ) ) {
				add_filter( 'jet-woo-builder/integration/register-widgets', [ $this, 'maybe_enable_widgets' ], 10, 2 );
				add_filter( 'jet-woo-builder/documents/is-document-type', [ $this, 'maybe_enable_widgets' ], 10, 2 );
			}

		}

		/**
		 * Registering a new widget category.
		 *
		 * Register JetWooBuilder category for elementor if not exists.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @param object $elements_manager Elements manager instance.
		 *
		 * @return void
		 */
		public function register_category( $elements_manager ) {
			$elements_manager->add_category(
				'jet-woo-builder',
				[
					'title' => __( 'JetWooBuilder', 'jet-woo-builder' ),
					'icon'  => 'eicon-font',
				]
			);
		}

		/**
		 * Add new controls.
		 *
		 * Register custom Elementor controls.
		 *
		 * @since  1.1.0
		 * @access public
		 *
		 * @param object $controls_manager Controls manager instance.
		 *
		 * @return void
		 */
		public function controls_register( $controls_manager ) {

			$grouped = [
				'jet-woo-box-style' => 'Jet_Woo_Group_Control_Box_Style',
			];

			foreach ( $grouped as $control_id => $class_name ) {
				if ( $this->include_control( $class_name, true ) ) {
					$controls_manager->add_group_control( $control_id, new $class_name() );
				}
			}

		}

		/**
		 * Include control.
		 *
		 * Include control file by class name.
		 *
		 * @since  1.1.0
		 * @access public
		 *
		 * @param string $class_name Control class name.
		 * @param bool   $grouped    Control type.
		 *
		 * @return bool
		 */
		public function include_control( $class_name = '', $grouped = false ) {

			$filename = sprintf(
				'includes/components/elementor-views/controls/%s%s.php',
				$grouped ? 'groups/' : '',
				str_replace( '_', '-', strtolower( $class_name ) )
			);

			if ( ! file_exists( jet_woo_builder()->plugin_path( $filename ) ) ) {
				return false;
			}

			require jet_woo_builder()->plugin_path( $filename );

			return true;

		}

		/**
		 * Frontend hooks.
		 *
		 * Include WC frontend hooks.
		 *
		 * @since  1.7.0
		 * @access public
		 */
		public function register_frontend_wc_hooks() {
			WC()->frontend_includes();
		}

		/**
		 * Include hooks.
		 *
		 * Include woocommerce front-end hooks.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function include_wc_hooks() {

			$elementor    = Elementor\Plugin::instance();
			$is_edit_mode = $elementor->editor->is_edit_mode();

			if ( ! $is_edit_mode || ! defined( 'WC_ABSPATH' ) || ! file_exists( WC_ABSPATH . 'includes/wc-template-hooks.php' ) ) {
				return;
			}

			$rewrite = apply_filters( 'jet-woo-builder/integration/rewrite-frontend-hooks', false );

			if ( ! $rewrite ) {
				include_once WC_ABSPATH . 'includes/wc-template-hooks.php';
			}

			remove_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );

		}

		/**
		 * Register widgets.
		 *
		 * Register plugin widgets.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @param object $widgets_manager Elementor widgets manager instance.
		 *
		 * @return void
		 */
		public function register_widgets( $widgets_manager ) {

			$available_widgets = [
				'global'    => jet_woo_builder_settings()->get( 'global_available_widgets' ),
				'single'    => jet_woo_builder_settings()->get( 'single_product_available_widgets' ),
				'archive'   => jet_woo_builder_settings()->get( 'archive_product_available_widgets' ),
				'category'  => jet_woo_builder_settings()->get( 'archive_category_available_widgets' ),
				'shop'      => jet_woo_builder_settings()->get( 'shop_product_available_widgets' ),
				'cart'      => jet_woo_builder_settings()->get( 'cart_available_widgets' ),
				'checkout'  => jet_woo_builder_settings()->get( 'checkout_available_widgets' ),
				'thankyou'  => jet_woo_builder_settings()->get( 'thankyou_available_widgets' ),
				'myaccount' => jet_woo_builder_settings()->get( 'myaccount_available_widgets' ),
			];

			require_once jet_woo_builder()->plugin_path( 'includes/components/elementor-views/widget-base.php' );

			foreach ( glob( jet_woo_builder()->plugin_path( 'includes/widgets/global/' ) . '*.php' ) as $file ) {
				$slug    = basename( $file, '.php' );
				$enabled = isset( $available_widgets['global'][ $slug ] ) ? $available_widgets['global'][ $slug ] : '';

				if ( filter_var( $enabled, FILTER_VALIDATE_BOOLEAN ) || ! $available_widgets['global'] ) {
					$this->register_widget( $file, $widgets_manager );
				}
			}

			$doc_type = jet_woo_builder()->documents->get_current_type();

			if ( ! $doc_type && get_post_type() === jet_woo_builder_post_type()->slug() ) {
				$doc_type = get_post_meta( get_the_ID(), '_elementor_template_type', true );
			}

			$doc_type  = apply_filters( 'jet-woo-builder/integration/doc-type', $doc_type );
			$doc_types = jet_woo_builder()->documents->get_document_types();

			foreach ( $doc_types as $type => $value ) {
				$template_enable  = 'custom_' . $type . '_page';
				$template_export  = isset( $_GET['action'] ) && 'jet_woo_builder_export_template' === $_GET['action'];
				$register_widgets = apply_filters( 'jet-woo-builder/integration/register-widgets', false, $type );

				switch ( $type ) {
					case 'single':
					case 'archive':
						$widgets_folder = $type . '-product';
						break;

					case 'category':
						$template_enable = 'custom_archive_category_page';
						$widgets_folder  = 'archive-category';

						break;

					default:
						$widgets_folder = $type;
						break;
				}

				if ( $this->is_setting_enabled( $template_enable ) || $value['slug'] === $doc_type || $template_export || $register_widgets ) {
					foreach ( glob( jet_woo_builder()->plugin_path( 'includes/widgets/' . $widgets_folder . '/' ) . '*.php' ) as $file ) {
						$slug    = basename( $file, '.php' );
						$enabled = isset( $available_widgets[ $type ][ $slug ] ) ? $available_widgets[ $type ][ $slug ] : '';

						if ( filter_var( $enabled, FILTER_VALIDATE_BOOLEAN ) || ! $available_widgets[ $type ] ) {
							$this->register_widget( $file, $widgets_manager );
						}
					}
				}
			}

		}

		/**
		 * Register widget.
		 *
		 * Register addon by file name.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @param string $file            File name.
		 * @param object $widgets_manager Widgets manager instance.
		 *
		 * @return void
		 */
		public function register_widget( $file, $widgets_manager ) {

			$base  = basename( str_replace( '.php', '', $file ) );
			$class = ucwords( str_replace( '-', ' ', $base ) );
			$class = str_replace( ' ', '_', $class );
			$class = sprintf( 'Elementor\%s', $class );

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
		 * Check settings availability.
		 *
		 * Return true if certain option is enabled.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @param string $type Template type.
		 *
		 * @return bool
		 */
		public function is_setting_enabled( $type = 'custom_single_page' ) {
			return filter_var( jet_woo_builder_shop_settings()->get( $type ), FILTER_VALIDATE_BOOLEAN );
		}

		/**
		 * Open wrap.
		 *
		 * Open wrapper for canvas page template for product templates.
		 *
		 * @since  1.3.0
		 * @access public
		 *
		 * @return void
		 */
		public function open_canvas_wrap() {

			if ( ! is_singular( jet_woo_builder_post_type()->slug() ) ) {
				return;
			}

			echo '<div class="product">';

		}

		/**
		 * Close wrap.
		 *
		 * Close wrapper for canvas page template for product templates.
		 *
		 * @since  1.3.0
		 * @access public
		 *
		 * @return void
		 */
		public function close_canvas_wrap() {

			if ( ! is_singular( jet_woo_builder_post_type()->slug() ) ) {
				return;
			}

			echo '</div>';

		}

		/**
		 * Editor styles.
		 *
		 * Enqueue editor styles.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function editor_styles() {

			wp_enqueue_style(
				'jet-woo-builder-editor-icons',
				jet_woo_builder()->plugin_url( 'assets/css/editor/icons.css' ),
				[],
				jet_woo_builder()->get_version()
			);

			wp_enqueue_style(
				'jet-woo-builder-icons',
				jet_woo_builder()->plugin_url( 'assets/css/lib/jet-woo-builder-icons/jet-woo-builder-icons.css' ),
				[],
				jet_woo_builder()->get_version()
			);

			wp_enqueue_style(
				'jet-woo-builder-editor-styles',
				jet_woo_builder()->plugin_url( 'assets/css/editor/editor.css' ),
				[],
				jet_woo_builder()->get_version()
			);

		}

		/**
		 * Init WC cart.
		 *
		 * Initialize WooCommerce cart for elementor page builder.
		 *
		 * @since  1.7.0
		 * @access public
		 *
		 * @return void
		 * @throws Exception
		 */
		public function init_editor_wc_cart() {

			$has_cart = is_a( WC()->cart, 'WC_Cart' );

			if ( ! $has_cart ) {
				$wc_session = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );

				WC()->session = new $wc_session();

				WC()->session->init();

				WC()->cart     = new WC_Cart();
				WC()->customer = new WC_Customer( get_current_user_id(), true );
			}

		}

		/**
		 * Enqueue single template css.
		 *
		 * Enqueue single template css if needed.
		 *
		 * @since  1.4.2
		 * @access public
		 */
		public function maybe_enqueue_single_template_css() {

			$current_template = jet_woo_builder()->woocommerce->get_custom_single_template();

			if ( ! is_product() ) {
				return;
			}

			if ( ! $current_template ) {
				return;
			}

			if ( class_exists( 'Elementor\Core\Files\CSS\Post' ) ) {
				$css_file = new Elementor\Core\Files\CSS\Post( $current_template );
			} else {
				$css_file = new Elementor\Post_CSS_File( $current_template );
			}

			$css_file->enqueue();

		}

		/**
		 * Add post classes.
		 *
		 * Added post classes at single product page.
		 *
		 * @since  1.4.2
		 * @access public
		 *
		 * @param array $classes List of post classes.
		 *
		 * @return mixed
		 */
		public function add_post_class( $classes ) {

			if ( is_archive() || 'related' === wc_get_loop_prop( 'name' ) || 'up-sells' === wc_get_loop_prop( 'name' ) || 'cross-sells' === wc_get_loop_prop( 'name' ) ) {
				if ( filter_var( jet_woo_builder_settings()->get( 'enable_product_thumb_effect' ), FILTER_VALIDATE_BOOLEAN ) ) {
					$classes[] = 'jet-woo-thumb-with-effect';
				}
			}

			return $classes;

		}

		/**
		 * Maybe enable widgets.
		 *
		 * Enable some JetWooBuilder widgets for appropriate Elementor Pro document types.
		 *
		 * @since 2.1.8
		 *
		 * @param boolean $status Enable status.
		 * @param string  $type   Widget template type.
		 *
		 * @return bool
		 */
		public function maybe_enable_widgets( $status, $type ) {

			$doc_type = get_post_meta( get_the_ID(), '_elementor_template_type', true );

			if ( 'shop' === $type && 'product-archive' === $doc_type || 'single' === $type && 'product' === $doc_type ) {
				return true;
			}

			return $status;

		}

		/**
		 * In Elementor Editor.
		 *
		 * Check if Elementor editor mode active.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return bool
		 */
		public function in_elementor() {

			$result = false;

			if ( wp_doing_ajax() ) {
				$result = $this->is_editor_ajax();
			} elseif ( Elementor\Plugin::instance()->editor->is_edit_mode() || Elementor\Plugin::instance()->preview->is_preview_mode() ) {
				$result = true;
			}

			return apply_filters( 'jet-woo-builder/in-elementor', $result );

		}

		/**
		 * Is editor ajax.
		 *
		 * Check if editor send ajax request.
		 *
		 * @since  1.7.2
		 * @access public
		 *
		 * @return bool
		 */
		public function is_editor_ajax() {
			return is_admin() && isset( $_REQUEST['action'] ) && 'elementor_ajax' === $_REQUEST['action'];
		}

		/**
		 * Set additional query types.
		 *
		 * Returns extended list of query types for Elementor editor documents.
		 *
		 * @since  2.1.4
		 * @access public
		 *
		 * @param array $query_types List of query types.
		 *
		 * @return array|mixed|object|string
		 */
		public function set_additional_query_types( $query_types ) {

			$document = \Elementor\Plugin::$instance->documents->get_current();

			if ( ! $document ) {
				return $query_types;
			}

			$single_query_types = [
				'related'     => __( 'Related', 'jet-woo-builder' ),
				'up-sells'    => __( 'Up Sells', 'jet-woo-builder' ),
				'cross-sells' => __( 'Cross Sells', 'jet-woo-builder' ),
			];

			if ( 'product' === $document->get_template_type() ) {
				$query_types = wp_parse_args( $single_query_types, $query_types );
			}

			return $query_types;

		}

	}

}
