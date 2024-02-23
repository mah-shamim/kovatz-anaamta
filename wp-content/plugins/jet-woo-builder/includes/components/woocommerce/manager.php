<?php
/**
 * WooCommerce compatibility package class.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Woocommerce' ) ) {

	class Jet_Woo_Builder_Woocommerce {

		/**
		 * Templates.
		 *
		 * Holds all the custom templates.
		 *
		 * @since  1.7.0
		 * @access public|private
		 *
		 * @var null
		 */
		private $current_template                     = null;
		public  $current_template_archive             = null;
		private $current_template_archive_category    = null;
		private $current_template_shop                = null;
		private $current_template_taxonomy            = null;
		private $current_template_cart                = null;
		private $current_template_empty_cart          = null;
		private $current_template_checkout            = null;
		private $current_top_template_checkout        = null;
		private $current_template_thankyou            = null;
		private $current_template_myaccount           = null;
		private $current_template_myaccount_dashboard = null;
		private $current_template_myaccount_orders    = null;
		private $current_template_myaccount_downloads = null;
		private $current_template_myaccount_address   = null;
		private $current_template_myaccount_account   = null;
		private $current_template_form_login          = null;

		/**
		 * Current loop.
		 *
		 * Holds current products loop type.
		 *
		 * @since  1.2.0
		 * @access private
		 *
		 * @var null
		 */
		private $current_loop = null;

		/**
		 * Category arguments.
		 *
		 * Holds current products category arguments.
		 *
		 * @since  1.3.0
		 * @access private
		 *
		 * @var array
		 */
		private $current_category_args = [];

		/**
		 * Loop template rewrite.
		 *
		 * Products loop widget template rewrite status.
		 *
		 * @since  1.7.12
		 * @access public
		 *
		 * @var bool
		 */
		public $products_loop_template_rewrite = false;

		public function __construct() {

			add_action( 'init', [ $this, 'register_instances' ] );
			add_action( 'init', [ $this, 'product_meta' ], 99 );
			add_action( 'init', [ $this, 'taxonomy_meta' ], 99 );

			add_filter( 'wc_get_template_part', [ $this, 'rewrite_product_templates' ], 10, 3 );

			add_filter( 'wc_get_template', [ $this, 'rewrite_product_cat_templates' ], 10, 3 );
			add_filter( 'wc_get_template', [ $this, 'rewrite_wc_pages_templates' ], 10, 2 );
			add_filter( 'wc_get_template', [ $this, 'force_wc_native_templates' ], 10, 2 );

			add_filter( 'template_include', [ $this, 'set_product_page_template' ], 9999 );
			add_filter( 'template_include', [ $this, 'set_product_archive_page_template' ], 9999 );
			add_filter( 'template_include', [ $this, 'set_wc_pages_template' ], 9999 );

			if ( ! empty( $_GET['elementor-preview'] ) ) {
				add_action( 'template_include', [ $this, 'set_archive_items_editor_template' ], 9999 );
			}

			add_action( 'template_redirect', [ $this, 'set_track_product_view' ], 20 );

			add_filter( 'woocommerce_product_loop_start', [ $this, 'product_archive_item_template_custom_columns' ] );
			add_filter( 'woocommerce_post_class', [ $this, 'product_archive_item_template_class' ], 10 );
			add_filter( 'product_cat_class', [ $this, 'product_category_archive_item_template_class' ], 10 );

			// Additional products loop options.
			add_filter( 'woocommerce_output_related_products_args', [ $this, 'set_related_products_output_count' ] );
			add_filter( 'woocommerce_upsell_display_args', [ $this, 'set_up_sells_products_output_count' ] );
			add_filter( 'woocommerce_cross_sells_total', [ $this, 'set_cross_sells_products_output_count' ] );

			add_filter( 'jet-woo-builder/custom-single-template', [ $this, 'force_preview_template' ] );
			add_filter( 'jet-woo-builder/integration/doc-type', [ $this, 'force_preview_doc_type' ] );
			add_filter( 'jet-woo-builder/integration/doc-type', [ $this, 'force_frontend_doc_type' ] );

			// Shop template hooks.
			add_filter( 'jet-woo-builder/render-callback/custom-args', [ $this, 'get_archive_category_args' ] );
			add_action( 'jet-woo-builder/woocommerce/before-main-content', 'woocommerce_output_content_wrapper', 10 );
			add_action( 'jet-woo-builder/woocommerce/after-main-content', 'woocommerce_output_content_wrapper_end', 10 );

			//Products Navigation Hooks
			add_filter( 'previous_posts_link_attributes', [ $this, 'set_previous_product_link_class' ] );
			add_filter( 'next_posts_link_attributes', [ $this, 'set_next_product_link_class' ] );

			if ( filter_var( jet_woo_builder_shop_settings()->get( 'use_ajax_add_to_cart' ), FILTER_VALIDATE_BOOLEAN ) ) {
				add_filter( 'woocommerce_add_to_cart_fragments', [ $this, 'ajax_single_add_to_cart_fragments' ] );
			}

			// Init WC pages manager.
			require jet_woo_builder()->plugin_path( 'includes/components/woocommerce/wc-pages/manager.php' );
			new Jet_Woo_Builder_WC_Pages_Manager();

		}

		/**
		 * Register instances.
		 *
		 * Register woocommerce component instances where it required.
		 *
		 * @since  1.13.0
		 * @access public
		 *
		 * @return void
		 */
		public function register_instances() {
			do_action( 'jet-woo-builder/components/woocommerce/init', $this );
		}

		/**
		 * Product meta.
		 *
		 * Initialize product meta box.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function product_meta() {
			new Cherry_X_Post_Meta(
				[
					'id'            => 'template-settings',
					'title'         => __( 'JetWooBuilder Template Settings', 'jet-woo-builder' ),
					'page'          => [ 'product' ],
					'context'       => 'side',
					'priority'      => 'low',
					'callback_args' => false,
					'builder_cb'    => [ jet_woo_builder_post_type(), 'get_builder' ],
					'fields'        => [
						'_jet_woo_template' => [
							'type'              => 'select',
							'element'           => 'control',
							'options'           => false,
							'options_callback'  => [ $this, 'get_single_templates' ],
							'label'             => __( 'Custom Template', 'jet-woo-builder' ),
							'sanitize_callback' => 'esc_attr',
						],
						'_template_type'    => [
							'type'              => 'select',
							'element'           => 'control',
							'default'           => 'default',
							'options'           => [
								'default'    => __( 'Default', 'jet-woo-builder' ),
								'canvas'     => __( 'Canvas', 'jet-woo-builder' ),
								'full_width' => __( 'Full Width', 'jet-woo-builder' ),
							],
							'label'             => __( 'Template Type', 'jet-woo-builder' ),
							'sanitize_callback' => 'esc_attr',
						],
					],
				]
			);
		}

		/**
		 * Get single templates.
		 *
		 * Returns list of single product templates.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return array
		 */
		public function get_single_templates() {
			return jet_woo_builder_post_type()->get_templates_list_for_options( 'single' );
		}

		/**
		 * Taxonomy meta.
		 *
		 * Initialize, edit and update JetWoo Builder templates meta box.
		 *
		 * @since  1.7.10
		 * @since  2.1.6 Refactored. Added handling for different types of objects.
		 * @access public
		 *
		 * @return void
		 */
		public function taxonomy_meta() {

			if ( ! is_admin() || 'yes' !== jet_woo_builder_shop_settings()->get( 'custom_taxonomy_template' ) ) {
				return;
			}

			$taxonomies = [];
			$types      = apply_filters( 'jet-woo-builder/integration/taxonomy-meta/object-types', false );

			if ( $types ) {
				$taxonomies = get_taxonomies( [
					'object_type' => array_merge( [ 'product' ], $types ),
					'public'      => true,
				] );
			}

			$default_taxonomies = get_taxonomies( [
				'object_type' => [ 'product' ],
				'public'      => true,
			] );

			$taxonomies = array_merge( $default_taxonomies, $taxonomies );

			foreach ( $taxonomies as $taxonomy ) {
				add_action( $taxonomy . '_add_form_fields', [ $this, 'taxonomy_add_form_fields' ] );
				add_action( $taxonomy . '_edit_form_fields', [ $this, 'taxonomy_edit_forms_fields' ] );

				add_action( 'edited_' . $taxonomy, [ $this, 'save_taxonomy_custom_meta' ] );
				add_action( 'create_' . $taxonomy, [ $this, 'save_taxonomy_custom_meta' ] );
			}

		}

		/**
		 * Add form fields.
		 *
		 * Add fields in taxonomy create form.
		 *
		 * @since  1.7.10
		 * @since  2.0.4 Updated markup.
		 * @access public
		 *
		 * @return void
		 */
		public function taxonomy_add_form_fields() {

			$templates = jet_woo_builder_post_type()->get_templates_list_for_options( 'shop' );
			?>

			<div class="form-field term-custom-template">
				<label for="jet_woo_builder_template">
					<strong><?php echo __( 'Custom Template', 'jet-woo-builder' ); ?></strong>
				</label>
				<select name="jet_woo_builder_template" id="jet_woo_builder_template">
					<?php foreach ( $templates as $template_id => $template_title ) : ?>
						<option value="<?php echo esc_attr( $template_id ); ?>">
							<?php echo esc_attr( $template_title ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p><?php echo __( 'The selected custom template will be applied to this term archive.', 'jet-woo-builder' ); ?></p>
			</div>

			<?php
		}

		/**
		 * Edit form fields.
		 *
		 * Add fields in taxonomy edit form.
		 *
		 * @since  1.7.10
		 * @since  2.0.4 Updated markup.
		 * @access public
		 *
		 * @param object $term Taxonomy instance.
		 *
		 * @return void
		 */
		public function taxonomy_edit_forms_fields( $term ) {

			$term_id         = $term->term_id;
			$templates       = jet_woo_builder_post_type()->get_templates_list_for_options( 'shop' );
			$custom_template = get_term_meta( $term_id, 'jet_woo_builder_template', true );
			?>

			<tr class="form-field term-custom-template">
				<th scope="row">
					<label for="jet_woo_builder_template"><?php echo __( 'Custom Template', 'jet-woo-builder' ); ?></label>
				</th>
				<td>
					<select name="jet_woo_builder_template" id="jet_woo_builder_template">
						<?php foreach ( $templates as $template_id => $template_title ) : ?>
							<option value="<?php echo esc_attr( $template_id ); ?>" <?php selected( $custom_template, $template_id ); ?> >
								<?php echo esc_attr( $template_title ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p><?php echo __( 'The selected custom template will be applied to this term archive.', 'jet-woo-builder' ); ?></p>
				</td>
			</tr>

			<?php
		}

		/**
		 * Save tax meta.
		 *
		 * Save extra taxonomy fields callback function.
		 *
		 * @since  1.7.10
		 * @access public
		 *
		 * @param int $term_id Taxonomy ID.
		 *
		 * @return void
		 */
		public function save_taxonomy_custom_meta( $term_id ) {

			$jet_woo_builder_template = filter_input( INPUT_POST, 'jet_woo_builder_template' );

			update_term_meta( $term_id, 'jet_woo_builder_template', $jet_woo_builder_template );

		}

		/**
		 * Single template.
		 *
		 * Returns custom single template.
		 *
		 * @since  1.2.0
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_single_template() {

			if ( null !== $this->current_template ) {
				return $this->current_template;
			}

			$enabled          = jet_woo_builder_shop_settings()->get( 'custom_single_page' );
			$product_template = get_post_meta( get_the_ID(), '_jet_woo_template', true );

			if ( ! empty( $product_template ) ) {
				return apply_filters( 'jet-woo-builder/custom-single-template', $product_template );
			}

			$custom_template = false;

			if ( 'yes' === $enabled && 'default' !== jet_woo_builder_shop_settings()->get( 'single_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( 'single_template' );
			}

			$this->current_template = apply_filters( 'jet-woo-builder/custom-single-template', $custom_template );

			return $this->current_template;

		}

		/**
		 * Archive template.
		 *
		 * Returns custom archive item template.
		 *
		 * @since  1.2.0
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_archive_template() {

			if ( null !== $this->current_template_archive && $this->products_loop_template_rewrite ) {
				return $this->current_template_archive;
			}

			$enabled         = jet_woo_builder_shop_settings()->get( 'custom_archive_page' );
			$loop            = $this->get_current_loop();
			$custom_template = false;

			if ( 'yes' === $enabled && 'default' !== jet_woo_builder_shop_settings()->get( $loop . '_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( $loop . '_template' );
			}

			$layout          = ! empty( $_COOKIE['jet_woo_builder_layout'] ) ? absint( $_COOKIE['jet_woo_builder_layout'] ) : false;
			$switcher_enable = apply_filters( 'jet-woo-builder/jet-products-loop/switcher-option-enable', false );

			if ( $layout && $switcher_enable && 'archive' === $loop ) {
				$this->current_template_archive = $layout;
			} else {
				$this->current_template_archive = apply_filters( 'jet-woo-builder/custom-archive-template', $custom_template );
			}

			return apply_filters( 'jet-woo-builder/final-custom-archive-template', $this->current_template_archive );

		}

		/**
		 * Archive category template.
		 *
		 * Returns custom archive category template.
		 *
		 * @since  1.3.0
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_archive_category_template() {

			if ( null !== $this->current_template_archive_category ) {
				return $this->current_template_archive_category;
			}

			$enabled         = jet_woo_builder_shop_settings()->get( 'custom_archive_category_page' );
			$custom_template = false;

			if ( 'yes' === $enabled && 'default' !== jet_woo_builder_shop_settings()->get( 'category_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( 'category_template' );
			}

			$this->current_template_archive_category = apply_filters( 'jet-woo-builder/custom-archive-category-template', $custom_template );

			return $this->current_template_archive_category;

		}

		/**
		 * Shop template.
		 *
		 * Returns custom shop template.
		 *
		 * @since  1.3.0
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_shop_template() {

			if ( null !== $this->current_template_shop ) {
				return $this->current_template_shop;
			}

			$enabled         = jet_woo_builder_shop_settings()->get( 'custom_shop_page' );
			$custom_template = false;

			if ( 'yes' === $enabled && 'default' !== jet_woo_builder_shop_settings()->get( 'shop_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( 'shop_template' );
			}

			$this->current_template_shop = apply_filters( 'jet-woo-builder/custom-shop-template', $custom_template );

			return $this->current_template_shop;

		}

		/**
		 * Product taxonomy template.
		 *
		 * Returns custom product taxonomy template.
		 *
		 * @since 1.9.0
		 * @access
		 *
		 * @return string
		 */
		public function get_custom_product_taxonomy_template() {

			if ( null !== $this->current_template_taxonomy ) {
				return $this->current_template_taxonomy;
			}

			$enabled         = jet_woo_builder_shop_settings()->get( 'custom_taxonomy_template' );
			$custom_template = false;

			if ( 'yes' === $enabled ) {
				$custom_template = get_term_meta( get_queried_object_id(), 'jet_woo_builder_template', true );
			}

			if ( ! $custom_template ) {
				$custom_template = $this->get_custom_shop_template();
			}

			if ( ! empty( $custom_template ) ) {
				$this->current_template_taxonomy = apply_filters( 'jet-woo-builder/custom-taxonomy-template', $custom_template );
			}

			return $this->current_template_taxonomy;

		}

		/**
		 * Cart template.
		 *
		 * Returns custom cart page template.
		 *
		 * @since  1.7.0
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_cart_template() {

			if ( null !== $this->current_template_cart ) {
				return $this->current_template_cart;
			}

			$enabled         = jet_woo_builder_shop_settings()->get( 'custom_cart_page' );
			$custom_template = false;

			if ( 'yes' === $enabled && 'default' !== jet_woo_builder_shop_settings()->get( 'cart_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( 'cart_template' );
			}

			$this->current_template_cart = apply_filters( 'jet-woo-builder/custom-cart-template', $custom_template );

			return $this->current_template_cart;

		}

		/**
		 * Empty cart template.
		 *
		 * Returns custom empty cart page template.
		 *
		 * @since  1.7.0
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_empty_cart_template() {

			if ( null !== $this->current_template_empty_cart ) {
				return $this->current_template_empty_cart;
			}

			$enabled         = jet_woo_builder_shop_settings()->get( 'custom_cart_page' );
			$custom_template = false;

			if ( 'yes' === $enabled && 'default' !== jet_woo_builder_shop_settings()->get( 'empty_cart_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( 'empty_cart_template' );
			}

			$this->current_template_empty_cart = apply_filters( 'jet-woo-builder/custom-empty-cart-template', $custom_template );

			return $this->current_template_empty_cart;

		}

		/**
		 * Checkout template.
		 *
		 * Returns custom checkout page template.
		 *
		 * @since  1.7.0
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_checkout_template() {

			if ( null !== $this->current_template_checkout ) {
				return $this->current_template_checkout;
			}

			$enabled         = jet_woo_builder_shop_settings()->get( 'custom_checkout_page' );
			$custom_template = false;

			if ( 'yes' === $enabled && 'default' !== jet_woo_builder_shop_settings()->get( 'checkout_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( 'checkout_template' );
			}

			$this->current_template_checkout = apply_filters( 'jet-woo-builder/custom-checkout-template', $custom_template );

			return $this->current_template_checkout;

		}

		/**
		 * Checkout to template.
		 *
		 * Returns custom checkout top section template.
		 *
		 * @since  1.7.0
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_top_checkout_template() {

			if ( null !== $this->current_top_template_checkout ) {
				return $this->current_top_template_checkout;
			}

			$enabled         = jet_woo_builder_shop_settings()->get( 'custom_checkout_page' );
			$custom_template = false;

			if ( 'yes' === $enabled && 'default' !== jet_woo_builder_shop_settings()->get( 'checkout_top_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( 'checkout_top_template' );
			}

			$this->current_top_template_checkout = apply_filters( 'jet-woo-builder/custom-top-checkout-template', $custom_template );

			return $this->current_top_template_checkout;

		}

		/**
		 * Thank you template.
		 *
		 * Returns custom thank you page template.
		 *
		 * @since  1.7.0
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_thankyou_template() {

			if ( null !== $this->current_template_thankyou ) {
				return $this->current_template_thankyou;
			}

			$enabled         = jet_woo_builder_shop_settings()->get( 'custom_thankyou_page' );
			$custom_template = false;

			if ( 'yes' === $enabled && 'default' !== jet_woo_builder_shop_settings()->get( 'thankyou_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( 'thankyou_template' );
			}

			$this->current_template_thankyou = apply_filters( 'jet-woo-builder/custom-thankyou-template', $custom_template );

			return $this->current_template_thankyou;

		}

		/**
		 * My account template.
		 *
		 * Returns custom my account page template.
		 *
		 * @since  1.7.0
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_myaccount_template() {

			if ( null !== $this->current_template_myaccount ) {
				return $this->current_template_myaccount;
			}

			$enabled         = jet_woo_builder_shop_settings()->get( 'custom_myaccount_page' );
			$custom_template = false;

			if ( 'yes' === $enabled && 'default' !== jet_woo_builder_shop_settings()->get( 'myaccount_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( 'myaccount_template' );
			}

			$this->current_template_myaccount = apply_filters( 'jet-woo-builder/custom-myaccount-template', $custom_template );

			return $this->current_template_myaccount;

		}

		/**
		 * My account dashboard template.
		 *
		 * Returns custom my account dashboard endpoint template.
		 *
		 * @since  1.7.4
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_myaccount_dashboard_template() {

			if ( null !== $this->current_template_myaccount_dashboard ) {
				return $this->current_template_myaccount_dashboard;
			}

			$enabled           = jet_woo_builder_shop_settings()->get( 'custom_myaccount_page' );
			$enabled_endpoints = jet_woo_builder_shop_settings()->get( 'custom_myaccount_page_endpoints' );
			$custom_template   = false;

			if ( 'yes' === $enabled && 'yes' === $enabled_endpoints && 'default' !== jet_woo_builder_shop_settings()->get( 'myaccount_dashboard_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( 'myaccount_dashboard_template' );
			}

			$this->current_template_myaccount_dashboard = apply_filters( 'jet-woo-builder/custom-myaccount-dashboard-template', $custom_template );

			return $this->current_template_myaccount_dashboard;

		}

		/**
		 * My account orders template.
		 *
		 * Returns custom my account orders endpoint template.
		 *
		 * @since  1.7.4
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_myaccount_orders_template() {

			if ( null !== $this->current_template_myaccount_orders ) {
				return $this->current_template_myaccount_orders;
			}

			$enabled           = jet_woo_builder_shop_settings()->get( 'custom_myaccount_page' );
			$enabled_endpoints = jet_woo_builder_shop_settings()->get( 'custom_myaccount_page_endpoints' );
			$custom_template   = false;

			if ( 'yes' === $enabled && 'yes' === $enabled_endpoints && 'default' !== jet_woo_builder_shop_settings()->get( 'myaccount_orders_endpoint_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( 'myaccount_orders_endpoint_template' );
			}

			$this->current_template_myaccount_orders = apply_filters( 'jet-woo-builder/custom-myaccount-orders-endpoint-template', $custom_template );

			return $this->current_template_myaccount_orders;

		}

		/**
		 * My account downloads template.
		 *
		 * Returns custom my account downloads endpoint template.
		 *
		 * @since  1.7.4
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_myaccount_downloads_template() {

			if ( null !== $this->current_template_myaccount_downloads ) {
				return $this->current_template_myaccount_downloads;
			}

			$enabled           = jet_woo_builder_shop_settings()->get( 'custom_myaccount_page' );
			$enabled_endpoints = jet_woo_builder_shop_settings()->get( 'custom_myaccount_page_endpoints' );
			$custom_template   = false;

			if ( 'yes' === $enabled && 'yes' === $enabled_endpoints && 'default' !== jet_woo_builder_shop_settings()->get( 'myaccount_downloads_endpoint_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( 'myaccount_downloads_endpoint_template' );
			}

			$this->current_template_myaccount_downloads = apply_filters( 'jet-woo-builder/custom-myaccount-downloads-endpoint-template', $custom_template );

			return $this->current_template_myaccount_downloads;

		}

		/**
		 * My account edit address template.
		 *
		 * Returns custom my account edit address endpoint template.
		 *
		 * @since  1.7.4
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_myaccount_address_template() {

			if ( null !== $this->current_template_myaccount_address ) {
				return $this->current_template_myaccount_address;
			}

			$enabled           = jet_woo_builder_shop_settings()->get( 'custom_myaccount_page' );
			$enabled_endpoints = jet_woo_builder_shop_settings()->get( 'custom_myaccount_page_endpoints' );
			$custom_template   = false;

			if ( 'yes' === $enabled && 'yes' === $enabled_endpoints && 'default' !== jet_woo_builder_shop_settings()->get( 'myaccount_edit_address_endpoint_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( 'myaccount_edit_address_endpoint_template' );
			}

			$this->current_template_myaccount_address = apply_filters( 'jet-woo-builder/custom-myaccount-edit-address-endpoint-template', $custom_template );

			return $this->current_template_myaccount_address;

		}

		/**
		 * My account edit account template.
		 *
		 * Returns custom my account edit account endpoint template.
		 *
		 * @since  1.7.4
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_myaccount_account_template() {

			if ( null !== $this->current_template_myaccount_account ) {
				return $this->current_template_myaccount_account;
			}

			$enabled           = jet_woo_builder_shop_settings()->get( 'custom_myaccount_page' );
			$enabled_endpoints = jet_woo_builder_shop_settings()->get( 'custom_myaccount_page_endpoints' );
			$custom_template   = false;

			if ( 'yes' === $enabled && 'yes' === $enabled_endpoints && 'default' !== jet_woo_builder_shop_settings()->get( 'myaccount_edit_account_endpoint_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( 'myaccount_edit_account_endpoint_template' );
			}

			$this->current_template_myaccount_account = apply_filters( 'jet-woo-builder/custom-myaccount-edit-account-endpoint-template', $custom_template );

			return $this->current_template_myaccount_account;

		}

		/**
		 * Form login template.
		 *
		 * Returns custom form login page template.
		 *
		 * @since  1.7.0
		 * @access public
		 *
		 * @return mixed
		 */
		public function get_custom_form_login_template() {

			if ( null !== $this->current_template_form_login ) {
				return $this->current_template_form_login;
			}

			$enabled         = jet_woo_builder_shop_settings()->get( 'custom_myaccount_page' );
			$custom_template = false;

			if ( 'yes' === $enabled && 'default' !== jet_woo_builder_shop_settings()->get( 'form_login_template' ) ) {
				$custom_template = jet_woo_builder_shop_settings()->get( 'form_login_template' );
			}

			$this->current_template_form_login = apply_filters( 'jet-woo-builder/custom-form-login-template', $custom_template );

			return $this->current_template_form_login;

		}

		/**
		 * Rewrite templates.
		 *
		 * Rewrite default single product and archive product item templates.
		 *
		 * @since  1.3.0
		 * @access public
		 *
		 * @param string $template Template path.
		 * @param string $slug     Template slug.
		 * @param string $name     Template name.
		 *
		 * @return string
		 */
		public function rewrite_product_templates( $template, $slug, $name ) {

			if ( 'content' === $slug && 'single-product' === $name ) {
				$custom_template = $this->get_custom_single_template();

				if ( $custom_template && 'default' !== $custom_template ) {
					$template = jet_woo_builder()->get_template( 'woocommerce/content-single-product.php' );
				}
			}

			if ( 'content' === $slug && 'product' === $name ) {
				$custom_template = $this->get_custom_archive_template();

				if ( $custom_template && 'default' !== $custom_template ) {
					$template = jet_woo_builder()->get_template( 'woocommerce/content-product.php' );
				}
			}

			return $template;

		}

		/**
		 * Rewrite product category item template.
		 *
		 * Rewrite product category item template location.
		 *
		 * @since  1.3.0
		 * @access public
		 *
		 * @param string $located       Template location.
		 * @param string $template_name Template name.
		 * @param array  $args          Category arguments.
		 *
		 * @return mixed
		 */
		public function rewrite_product_cat_templates( $located, $template_name, $args ) {

			if ( 'yes' === jet_woo_builder_shop_settings()->get( 'custom_archive_category_page' ) && 'content-product_cat.php' === $template_name ) {
				$custom_template = $this->get_custom_archive_category_template();

				if ( $custom_template && 'default' !== $custom_template ) {
					$this->current_category_args = $args;
					$located                     = jet_woo_builder()->get_template( 'woocommerce/content-product_cat.php' );
				}
			}

			return $located;

		}

		/**
		 * Rewrite wc pages templates.
		 *
		 * Rewrite default WooCommerce pages templates.
		 *
		 * @since  1.7.0
		 * @access public
		 *
		 * @param string $located       Template location.
		 * @param string $template_name Template name.
		 *
		 * @return mixed|string
		 */
		public function rewrite_wc_pages_templates( $located, $template_name ) {

			// Cart template
			if ( 'yes' === jet_woo_builder_shop_settings()->get( 'custom_cart_page' ) ) {
				if ( $template_name === 'cart/cart.php' ) {
					$custom_template = $this->get_custom_cart_template();

					if ( $custom_template && 'default' !== $custom_template ) {
						$located = jet_woo_builder()->get_template( 'woocommerce/cart/cart.php' );
					}
				}

				if ( $template_name === 'cart/cart-empty.php' ) {
					$custom_template = $this->get_custom_empty_cart_template();

					if ( $custom_template && 'default' !== $custom_template ) {
						$located = jet_woo_builder()->get_template( 'woocommerce/cart/cart-empty.php' );
					}
				}
			}

			// Checkout template
			if ( 'yes' === jet_woo_builder_shop_settings()->get( 'custom_checkout_page' ) && $template_name === 'checkout/form-checkout.php' ) {
				$custom_template = $this->get_custom_checkout_template();

				if ( $custom_template && 'default' !== $custom_template ) {
					$this->current_top_template_checkout = $this->get_custom_top_checkout_template();
					$located                             = jet_woo_builder()->get_template( 'woocommerce/checkout/form-checkout.php' );
				}
			}

			// Thank you template
			if ( 'yes' === jet_woo_builder_shop_settings()->get( 'custom_thankyou_page' ) && $template_name === 'checkout/thankyou.php' ) {
				$custom_template = $this->get_custom_thankyou_template();

				if ( $custom_template && 'default' !== $custom_template ) {
					$located = jet_woo_builder()->get_template( 'woocommerce/checkout/thankyou.php' );
				}
			}

			// My account template
			if ( 'yes' === jet_woo_builder_shop_settings()->get( 'custom_myaccount_page' ) ) {
				$endpoint_enable = 'yes' === jet_woo_builder_shop_settings()->get( 'custom_myaccount_page_endpoints' );

				if ( $endpoint_enable ) {
					switch ( $template_name ) {
						case 'myaccount/dashboard.php':
							$custom_template = $this->get_custom_myaccount_dashboard_template();

							if ( $custom_template && 'default' !== $custom_template ) {
								$located = jet_woo_builder()->get_template( 'woocommerce/myaccount/dashboard.php' );
							}
							break;
						case 'myaccount/orders.php':
							$custom_template = $this->get_custom_myaccount_orders_template();

							if ( $custom_template && 'default' !== $custom_template ) {
								$located = jet_woo_builder()->get_template( 'woocommerce/myaccount/orders.php' );
							}
							break;
						case 'myaccount/downloads.php':
							$custom_template = $this->get_custom_myaccount_downloads_template();

							if ( $custom_template && 'default' !== $custom_template ) {
								$located = jet_woo_builder()->get_template( 'woocommerce/myaccount/downloads.php' );
							}
							break;
						case 'myaccount/my-address.php':
							$custom_template = $this->get_custom_myaccount_address_template();

							if ( $custom_template && 'default' !== $custom_template ) {
								$located = jet_woo_builder()->get_template( 'woocommerce/myaccount/my-address.php' );
							}
							break;
						case 'myaccount/form-edit-account.php':
							$custom_template = $this->get_custom_myaccount_account_template();

							if ( $custom_template && 'default' !== $custom_template ) {
								$located = jet_woo_builder()->get_template( 'woocommerce/myaccount/form-edit-account.php' );
							}
							break;
						default:
							break;
					}
				}

				if ( $template_name === 'myaccount/my-account.php' ) {
					$custom_template = $this->get_custom_myaccount_template();

					if ( $custom_template && 'default' !== $custom_template ) {
						$located = jet_woo_builder()->get_template( 'woocommerce/myaccount/my-account.php' );
					}
				}

				if ( $template_name === 'myaccount/form-login.php' ) {
					$custom_template = $this->get_custom_form_login_template();

					if ( $custom_template && 'default' !== $custom_template ) {
						$located = jet_woo_builder()->get_template( 'woocommerce/myaccount/form-login.php' );
					}
				}
			}

			return $located;

		}

		/**
		 * WC native templates.
		 *
		 * Force to use default WooCommerce templates.
		 *
		 * @since  1.13.0
		 * @access public
		 *
		 * @param string $located       Template location.
		 * @param string $template_name Template name.
		 *
		 * @return mixed|string
		 */
		public function force_wc_native_templates( $located, $template_name ) {

			if ( 'yes' === jet_woo_builder_shop_settings()->get( 'use_native_templates' ) && false !== strpos( $template_name, 'woocommerce/single-product/' ) ) {
				$default_path = WC()->plugin_path() . '/templates/';
				$located      = $default_path . $template_name;
			}

			return $located;

		}

		/**
		 * Set product page layout.
		 *
		 * Set single product template layout and product editor content template layout.
		 *
		 * @since  1.9.0
		 * @access public
		 *
		 * @param string $template Template layout.
		 *
		 * @return string
		 */
		public function set_product_page_template( $template ) {

			if ( is_singular( [ jet_woo_builder_post_type()->slug(), 'product' ] ) ) {
				$custom_template = $this->get_custom_single_template();

				if ( $custom_template && 'default' !== $custom_template ) {
					if ( is_singular( jet_woo_builder_post_type()->slug() ) && $custom_template !== get_the_ID() ) {
						return $template;
					}

					$template_type = get_post_meta( get_the_ID(), '_template_type', true );
					$settings      = get_post_meta( $custom_template, '_elementor_page_settings', true );
					$layout        = isset( $settings['template_layout'] ) ? $settings['template_layout'] : 'default';

					if ( $template_type && 'default' !== $template_type ) {
						$layout = $template_type;
					}

					switch ( $layout ) {
						case 'canvas':
						case 'elementor_canvas':
							$template = jet_woo_builder()->plugin_path( 'templates/template-types/product/canvas.php' );
							do_action( 'jet-woo-builder/template-include/found' );

							break;

						case 'full_width':
						case 'elementor_header_footer':
							$template = jet_woo_builder()->plugin_path( 'templates/template-types/product/header-footer.php' );
							do_action( 'jet-woo-builder/template-include/found' );

							break;

						default:
							break;
					}
				}
			}

			if ( is_singular( 'product' ) && isset( $_GET['elementor-preview'] ) ) {
				$template = jet_woo_builder()->plugin_path( 'templates/template-types/product/canvas.php' );
				do_action( 'jet-woo-builder/template-include/found' );
			}

			return $template;

		}

		/**
		 * Product archive page template.
		 *
		 * Set product archive page template layout.
		 *
		 * @since  1.9.0
		 * @since  2.0.4 Additional `custom_taxonomy_template` check.
		 * @access public
		 *
		 * @param string $template Template layout.
		 *
		 * @return string
		 */
		public function set_product_archive_page_template( $template ) {

			if ( 'yes' === jet_woo_builder_shop_settings()->get( 'custom_shop_page' ) || 'yes' === jet_woo_builder_shop_settings()->get( 'custom_taxonomy_template' ) ) {
				if ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) || is_product_taxonomy() ) {
					$custom_template = $this->get_custom_product_taxonomy_template();

					if ( $custom_template && 'default' !== $custom_template ) {
						$settings = get_post_meta( $custom_template, '_elementor_page_settings', true );
						$layout   = isset( $settings['template_layout'] ) ? $settings['template_layout'] : '';

						switch ( $layout ) {
							case 'elementor_header_footer':
								$template = jet_woo_builder()->plugin_path( 'templates/template-types/archive/header-footer.php' );
								do_action( 'jet-woo-builder/template-include/found' );

								break;

							case 'elementor_canvas':
								$template = jet_woo_builder()->plugin_path( 'templates/template-types/archive/canvas.php' );
								do_action( 'jet-woo-builder/template-include/found' );

								break;

							default:
								$template = jet_woo_builder()->get_template( 'woocommerce/archive-product.php' );

								break;
						}
					}
				}
			}

			return $template;

		}

		/**
		 * WC pages templates.
		 *
		 * Set default WooCommerce pages templates layouts.
		 *
		 * @since  1.9.0
		 * @access public
		 *
		 * @param string $template Template layout.
		 *
		 * @return mixed|string
		 */
		public function set_wc_pages_template( $template ) {

			$custom_template = null;

			if ( is_cart() ) {
				$custom_template = $this->get_custom_cart_template();
			} elseif ( is_checkout() ) {
				$custom_template = $this->get_custom_checkout_template();

				if ( ! empty( is_wc_endpoint_url( 'order-received' ) ) ) {
					$custom_template = $this->get_custom_thankyou_template();
				}
			} elseif ( is_account_page() ) {
				if ( is_user_logged_in() ) {
					$custom_template = $this->get_custom_myaccount_template();
				} else {
					$custom_template = $this->get_custom_form_login_template();
				}
			}

			if ( $custom_template || ( ! empty( $_GET['elementor-preview'] ) && is_singular( jet_woo_builder_post_type()->slug() ) ) ) {
				if ( ! $custom_template ) {
					$custom_template = $_GET['elementor-preview'];
				}

				$settings = get_post_meta( $custom_template, '_elementor_page_settings', true );
				$layout   = isset( $settings['template_layout'] ) ? $settings['template_layout'] : '';

				switch ( $layout ) {
					case 'elementor_header_footer':
						$template = jet_woo_builder()->plugin_path( 'templates/template-types/page/header-footer.php' );
						do_action( 'jet-woo-builder/template-include/found' );

						break;

					case 'elementor_canvas':
						$template = jet_woo_builder()->plugin_path( 'templates/template-types/page/canvas.php' );
						do_action( 'jet-woo-builder/template-include/found' );

						break;

					default:
						break;
				}
			}

			return $template;

		}

		/**
		 * Archive items editor templates.
		 *
		 * Set Elementor editor archive cards canvas templates.
		 *
		 * @since  2.0.0
		 * @access public
		 *
		 * @param string $template Current template name.
		 *
		 * @return string
		 */
		public function set_archive_items_editor_template( $template ) {

			$found    = false;
			$document = Elementor\Plugin::instance()->documents->get( get_the_ID() );

			if ( is_singular( jet_woo_builder_post_type()->slug() ) ) {
				if ( 'jet-woo-builder-archive' === $document->get_name() || 'jet-woo-builder-category' === $document->get_name() ) {
					$found    = true;
					$template = jet_woo_builder()->plugin_path( 'templates/template-types/page/canvas.php' );
				}
			}

			if ( $found ) {
				do_action( 'jet-woo-builder/editor-template/found' );
			}

			return $template;

		}

		/**
		 * Product archive item template custom columns.
		 *
		 * Add custom columns for product archive item template
		 *
		 * @since  1.13.0
		 * @since  2.1.3 Added Additional option check.
		 * @access public
		 *
		 * @param string $content Products loop content.
		 *
		 * @return string
		 */
		public function product_archive_item_template_custom_columns( $content ) {

			if ( 'shortcode' === $this->get_current_loop() ) {
				return $content;
			}

			$template_id        = apply_filters( 'jet-woo-builder/woocommerce/products-loop/custom-archive-template', $this->get_custom_archive_template() );
			$settings           = get_post_meta( $template_id, '_elementor_page_settings', true );
			$use_custom_columns = isset( $settings['use_custom_template_columns'] ) ? filter_var( $settings['use_custom_template_columns'], FILTER_VALIDATE_BOOLEAN ) : false;
			$classes            = [ 'products', 'jet-woo-builder-layout-' . $template_id ];

			$settings_cat           = get_post_meta( $this->get_custom_archive_category_template(), '_elementor_page_settings', true );
			$use_custom_cat_columns = isset( $settings_cat['use_custom_template_category_columns'] ) ? filter_var( $settings_cat['use_custom_template_category_columns'], FILTER_VALIDATE_BOOLEAN ) : false;
			$classes_cat            = [ 'products' ];

			if ( ! $settings && ! $settings_cat ) {
				return $content;
			}

			if ( ! $use_custom_cat_columns && ! $use_custom_columns ) {
				return $content;
			}

			remove_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );

			$content_cat = '';

			if ( ! empty( woocommerce_maybe_show_product_subcategories() ) ) {
				$classes_cat = implode( ' ', $classes_cat );

				if ( $use_custom_cat_columns ) {
					$before = sprintf( '<ul class="jet-woo-builder-categories--columns %s">', $classes_cat );
					$after  = '</ul>';
				} else {
					$before = '<ul class="products columns-' . esc_attr( wc_get_loop_prop( 'columns' ) ) . '">';
					$after  = '</ul>';
				}

				$content_cat = $before . woocommerce_maybe_show_product_subcategories() . $after;
			}

			if ( $use_custom_columns ) {
				$content = sprintf( '<ul class="jet-woo-builder-products--columns %s">', implode( ' ', $classes ) );
			} else {
				$classes      = 'products columns-' . esc_attr( wc_get_loop_prop( 'columns' ) );
				$display_type = woocommerce_get_loop_display_mode();

				// If displaying just categories, append to the loop.
				if ( 'subcategories' === $display_type ) {
					$classes .= ' jet-woo-builder-hide';
				}

				$content = sprintf( '<ul class="%s">', $classes );
			}

			$content = $content_cat . $content;

			return $content;

		}

		/**
		 * Product archive item template class.
		 *
		 * Returns equal product archive item template class if proper option enable.
		 *
		 * @since  1.13.0
		 * @access public
		 *
		 * @param array $classes WooCommerce post classes list.
		 *
		 * @return mixed
		 */
		public function product_archive_item_template_class( $classes ) {

			if ( 'shortcode' === $this->get_current_loop() ) {
				return $classes;
			}

			$settings           = get_post_meta( $this->get_custom_archive_template(), '_elementor_page_settings', true );
			$use_custom_columns = $settings['use_custom_template_columns'] ?? '';
			$equal_columns      = $settings['equal_columns_height'] ?? '';

			if ( 'yes' === $use_custom_columns && 'yes' === $equal_columns ) {
				array_push( $classes, 'jet-equal-columns' );
			}

			return $classes;

		}

		/**
		 * Product archive item template class.
		 *
		 * Returns equal category archive item template class if proper option enable.
		 *
		 * @since  1.13.0
		 * @access public
		 *
		 * @param array $classes Product category classes list.
		 *
		 * @return mixed
		 */
		public function product_category_archive_item_template_class( $classes ) {

			if ( 'shortcode' === $this->get_current_loop() ) {
				return $classes;
			}

			$settings           = get_post_meta( $this->get_custom_archive_category_template(), '_elementor_page_settings', true );
			$use_custom_columns = isset( $settings['use_custom_template_category_columns'] ) ? $settings['use_custom_template_category_columns'] : '';
			$equal_columns      = isset( $settings['equal_columns_height'] ) ? $settings['equal_columns_height'] : '';

			if ( 'yes' === $use_custom_columns && 'yes' === $equal_columns ) {
				array_push( $classes, 'jet-equal-columns' );
			}

			return $classes;

		}

		/**
		 * Current loop.
		 *
		 * Get current loop type.
		 *
		 * @since  1.2.0
		 * @access public
		 *
		 * @return string
		 */
		public function get_current_loop() {

			$loop = 'archive';

			if ( wc_get_loop_prop( 'is_shortcode' ) ) {
				$loop = 'shortcode';
			}

			if ( wc_get_loop_prop( 'is_search' ) ) {
				$loop = 'search';
			}

			if ( 'related' === wc_get_loop_prop( 'name' ) || 'up-sells' === wc_get_loop_prop( 'name' ) ) {
				$loop = 'related';
			}

			if ( 'cross-sells' === wc_get_loop_prop( 'name' ) ) {
				$loop = 'cross_sells';
			}

			return $this->current_loop = $loop;

		}

		/**
		 * Reset current loop.
		 *
		 * Reset current loop type.
		 *
		 * @since  1.2.0
		 * @access public
		 *
		 * @return null
		 */
		public function reset_current_loop() {
			return $this->current_loop = null;
		}

		/**
		 * Related products output count.
		 *
		 * Set count of products displayed in related products section
		 *
		 * @since  1.2.0
		 * @access public
		 *
		 * @param array $args Related product arguments.
		 *
		 * @return array
		 */
		public function set_related_products_output_count( $args ) {

			$posts_per_page = jet_woo_builder_shop_settings()->get( 'related_products_per_page' );
			$posts_per_page = isset( $posts_per_page ) ? $posts_per_page : 4;

			$defaults = [
				'posts_per_page' => $posts_per_page,
			];

			return wp_parse_args( $defaults, $args );

		}

		/**
		 * Upsells products output count.
		 *
		 * Set count of products displayed in upsells products section.
		 *
		 * @since  1.2.0
		 * @access public
		 *
		 * @param array $args Upsells product arguments.
		 *
		 * @return array
		 */
		public function set_up_sells_products_output_count( $args ) {

			$posts_per_page = jet_woo_builder_shop_settings()->get( 'up_sells_products_per_page' );
			$posts_per_page = $posts_per_page ?? 4;

			$defaults = [
				'posts_per_page' => $posts_per_page,
			];

			return wp_parse_args( $defaults, $args );

		}

		/**
		 * Cross sells product output count.
		 *
		 * Set count of products displayed in cross sells products section.
		 *
		 * @since  1.2.0
		 * @access public
		 *
		 * @return int
		 */
		public function set_cross_sells_products_output_count() {

			$posts_per_page = jet_woo_builder_shop_settings()->get( 'cross_sells_products_per_page' );

			return isset( $posts_per_page ) ? $posts_per_page : 4;

		}

		/**
		 * Preview Template.
		 *
		 * Force preview template.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @param int $custom_template Template ID.
		 *
		 * @return int
		 */
		public function force_preview_template( $custom_template ) {
			if ( ! empty( $_GET['jet_woo_template'] ) && isset( $_GET['preview_nonce'] ) ) {
				return absint( $_GET['jet_woo_template'] );
			} else {
				return $custom_template;
			}
		}

		/**
		 * Preview doc type.
		 *
		 * Force preview document type.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @param string $doc_type Current document type.
		 *
		 * @return mixed
		 */
		public function force_preview_doc_type( $doc_type ) {
			if ( ! empty( $_GET['jet_woo_template'] ) && isset( $_GET['preview_nonce'] ) ) {
				return get_post_meta( absint( $_GET['jet_woo_template'] ), '_elementor_template_type', true );
			} else {
				return $doc_type;
			}
		}

		/**
		 * Frontend doc type.
		 *
		 * Force frontend document type.
		 *
		 * @since  1.13.0
		 * @access public
		 *
		 * @param string $doc_type Product document type.
		 *
		 * @return string
		 */
		public function force_frontend_doc_type( $doc_type ) {
			if ( ! $doc_type && null !== get_post_meta( get_the_ID(), '_jet_woo_template', true ) ) {

				$queried_obj = get_queried_object();

				if ( is_post_type_archive( 'product' )
					 || ( isset( $queried_obj->ID ) && is_page( wc_get_page_id( 'shop' ) ) )
					 || is_product_taxonomy()
				) {
					return 'jet-woo-builder-shop';
				}

				if ( 'product' === get_post_type() ) {
					return 'jet-woo-builder';
				}
			} else {
				return $doc_type;
			}
		}

		/**
		 * Archive category arguments.
		 *
		 * Return arguments for current category.
		 *
		 * @since  1.3.5
		 * @access public
		 *
		 * @param array $args Category arguments list.
		 *
		 * @return array
		 */
		public function get_archive_category_args( $args ) {

			if ( ! empty( $this->current_category_args ) ) {
				$args = wp_parse_args( $this->current_category_args, $args );
			}

			return $args;

		}

		/**
		 * Category arguments.
		 *
		 * Returns processed categories arguments.
		 *
		 * @since  1.3.5
		 * @access public
		 *
		 * @return array
		 */
		public function get_current_args() {
			return $this->current_category_args;
		}

		/**
		 * Prev link class.
		 *
		 * Set previous product navigation link class.
		 *
		 * @since  1.3.7
		 * @access public
		 *
		 * @param string $args Link arguments.
		 *
		 * @return string
		 */
		public function set_previous_product_link_class( $args ) {

			$args .= 'class="jet-woo-builder-navigation-prev"';

			return $args;

		}

		/**
		 * Next link class.
		 *
		 * Set next product navigation link class.
		 *
		 * @since  1.3.7
		 * @access public
		 *
		 * @param string $args Link arguments.
		 *
		 * @return string
		 */
		public function set_next_product_link_class( $args ) {

			$args .= 'class="jet-woo-builder-navigation-next"';

			return $args;

		}

		/**
		 * Track views.
		 *
		 * Track product views.
		 *
		 * @since  1.3.0
		 * @access public
		 *
		 * @return void
		 */
		public function set_track_product_view() {

			if ( ! is_singular( 'product' ) ) {
				return;
			}

			global $post;

			if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) ) {
				$viewed_products = array();
			} else {
				$viewed_products = (array)explode( '|', $_COOKIE['woocommerce_recently_viewed'] );
			}

			if ( ! in_array( $post->ID, $viewed_products ) ) {
				$viewed_products[] = $post->ID;
			}

			if ( sizeof( $viewed_products ) > 30 ) {
				array_shift( $viewed_products );
			}

			// Store for session only
			wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );

		}

		/**
		 * Remove action callbacks.
		 *
		 * Remove all hooked callbacks for specific action.
		 *
		 * @since  2.0.0
		 * @access public
		 *
		 * @param string $action Hook name.
		 *
		 * @return void
		 */
		public function remove_action_hooked_callbacks( $action ) {

			$callbacks = $this->get_woocommerce_action_hooked_callbacks();

			if ( ! isset( $callbacks[ $action ] ) ) {
				return;
			}

			foreach ( $callbacks[ $action ] as $callback => $args ) {
				$allowed = apply_filters( 'jet-woo-builder/tools/woocommerce-actions/callback-allowed', false, $action, $callback, $args );

				if ( ! $allowed ) {
					if ( is_array( $args ) ) {
						remove_action( $action, [ $args[0], $callback ], $args[1] );
					} else {
						remove_action( $action, $callback, $args );
					}
				}
			}

		}

		/**
		 * AJAX single add to cart fragments.
		 *
		 * Return modified list of fragments after single AJAX add to cart.
		 *
		 * @since  2.1.0
		 * @access public
		 *
		 * @param array $fragments List of fragments.
		 *
		 * @return mixed
		 */
		public function ajax_single_add_to_cart_fragments( $fragments ) {

			$all_notices  = WC()->session->get( 'wc_notices', [] );
			$notice_types = apply_filters( 'woocommerce_notice_types', [ 'error', 'notice', 'success' ] );

			ob_start();

			foreach ( $notice_types as $notice_type ) {
				if ( wc_notice_count( $notice_type ) > 0 ) {
					wc_get_template( 'notices/' . $notice_type . '.php', [
						'notices' => array_filter( $all_notices[ $notice_type ] ),
					] );
				}
			}

			$fragments['notices_html'] = ob_get_clean();

			wc_clear_notices();

			return $fragments;

		}

		/**
		 * WooCommerce action hooked callbacks.
		 *
		 * Returns the list of woocommerce action and hooked callbacks fot it.
		 *
		 * @since  2.0.0
		 * @access public
		 *
		 * @return array
		 */
		public function get_woocommerce_action_hooked_callbacks() {
			return apply_filters( 'jet-woo-builder/tools/woocommerce-actions/callbacks', [
				'woocommerce_before_single_product_summary' => [
					'woocommerce_show_product_sale_flash' => 10,
					'woocommerce_show_product_images'     => 20,
				],
				'woocommerce_single_product_summary'        => [
					'woocommerce_template_single_title'       => 5,
					'woocommerce_template_single_rating'      => 10,
					'woocommerce_template_single_price'       => 10,
					'woocommerce_template_single_excerpt'     => 20,
					'woocommerce_template_single_add_to_cart' => 30,
					'woocommerce_template_single_meta'        => 40,
					'woocommerce_template_single_sharing'     => 50,
					'generate_product_data'                   => [ $GLOBALS['woocommerce']->structured_data, 60 ],
				],
				'woocommerce_after_single_product_summary'  => [
					'woocommerce_output_product_data_tabs' => 10,
					'woocommerce_upsell_display'           => 15,
					'woocommerce_output_related_products'  => 20,
				],
				'woocommerce_before_shop_loop_item'         => [
					'woocommerce_template_loop_product_link_open' => 10,
				],
				'woocommerce_before_shop_loop_item_title'   => [
					'woocommerce_show_product_loop_sale_flash'    => 10,
					'woocommerce_template_loop_product_thumbnail' => 10,
				],
				'woocommerce_shop_loop_item_title'          => [
					'woocommerce_template_loop_product_title' => 10,
				],
				'woocommerce_after_shop_loop_item_title'    => [
					'woocommerce_template_loop_rating' => 5,
					'woocommerce_template_loop_price'  => 10,
				],
				'woocommerce_after_shop_loop_item'          => [
					'woocommerce_template_loop_product_link_close' => 5,
					'woocommerce_template_loop_add_to_cart'        => 10,
				],
			] );
		}

	}

}