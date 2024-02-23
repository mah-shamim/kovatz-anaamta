<?php
/**
 * Plugin settings class
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Settings' ) ) {

	/**
	 * Define Jet_Woo_Builder_Settings class
	 */
	class Jet_Woo_Builder_Settings {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * Holds settings key
		 *
		 * @var string
		 */
		public $key = 'jet-woo-builder-settings';

		/**
		 * Holds settings
		 *
		 * @var null
		 */
		public $settings = null;

		/**
		 * Init page
		 */
		public function init() {
		}

		/**
		 * Returns localize plugin settings data
		 *
		 * @return array
		 */
		public function get_localize_data() {

			$global_available_widgets      = [];
			$default_global_active_widgets = [];

			foreach ( glob( jet_woo_builder()->plugin_path( 'includes/widgets/global/' ) . '*.php' ) as $file ) {
				$data = get_file_data( $file, array( 'class' => 'Class', 'name' => 'Name', 'slug' => 'Slug' ) );

				$slug = basename( $file, '.php' );

				$global_available_widgets[] = array(
					'label' => $data['name'],
					'value' => $slug,
				);

				$default_global_active_widgets[ $slug ] = 'true';
			}

			$single_product_available_widgets      = [];
			$default_single_product_active_widgets = [];

			foreach ( glob( jet_woo_builder()->plugin_path( 'includes/widgets/single-product/' ) . '*.php' ) as $file ) {
				$data = get_file_data( $file, array( 'class' => 'Class', 'name' => 'Name', 'slug' => 'Slug' ) );

				$slug = basename( $file, '.php' );

				$single_product_available_widgets[] = array(
					'label' => $data['name'],
					'value' => $slug,
				);

				$default_single_product_active_widgets[ $slug ] = 'true';
			}

			$archive_product_available_widgets      = [];
			$default_archive_product_active_widgets = [];

			foreach ( glob( jet_woo_builder()->plugin_path( 'includes/widgets/archive-product/' ) . '*.php' ) as $file ) {
				$data = get_file_data( $file, array( 'class' => 'Class', 'name' => 'Name', 'slug' => 'Slug' ) );

				$slug = basename( $file, '.php' );

				$archive_product_available_widgets[] = array(
					'label' => $data['name'],
					'value' => $slug,
				);

				$default_archive_product_active_widgets[ $slug ] = 'true';
			}

			$archive_category_available_widgets      = [];
			$default_archive_category_active_widgets = [];

			foreach ( glob( jet_woo_builder()->plugin_path( 'includes/widgets/archive-category/' ) . '*.php' ) as $file ) {
				$data = get_file_data( $file, array( 'class' => 'Class', 'name' => 'Name', 'slug' => 'Slug' ) );

				$slug = basename( $file, '.php' );

				$archive_category_available_widgets[] = array(
					'label' => $data['name'],
					'value' => $slug,
				);

				$default_archive_category_active_widgets[ $slug ] = 'true';
			}

			$shop_product_available_widgets      = [];
			$default_shop_product_active_widgets = [];

			foreach ( glob( jet_woo_builder()->plugin_path( 'includes/widgets/shop/' ) . '*.php' ) as $file ) {
				$data = get_file_data( $file, array( 'class' => 'Class', 'name' => 'Name', 'slug' => 'Slug' ) );

				$slug = basename( $file, '.php' );

				$shop_product_available_widgets[] = array(
					'label' => $data['name'],
					'value' => $slug,
				);

				$default_shop_product_active_widgets[ $slug ] = 'true';
			}

			$cart_available_widgets      = [];
			$default_cart_active_widgets = [];

			foreach ( glob( jet_woo_builder()->plugin_path( 'includes/widgets/cart/' ) . '*.php' ) as $file ) {
				$data = get_file_data( $file, array( 'class' => 'Class', 'name' => 'Name', 'slug' => 'Slug' ) );

				$slug = basename( $file, '.php' );

				$cart_available_widgets[] = array(
					'label' => $data['name'],
					'value' => $slug,
				);

				$default_cart_active_widgets[ $slug ] = 'true';
			}

			$checkout_available_widgets      = [];
			$default_checkout_active_widgets = [];

			foreach ( glob( jet_woo_builder()->plugin_path( 'includes/widgets/checkout/' ) . '*.php' ) as $file ) {
				$data = get_file_data( $file, array( 'class' => 'Class', 'name' => 'Name', 'slug' => 'Slug' ) );

				$slug = basename( $file, '.php' );

				$checkout_available_widgets[] = array(
					'label' => $data['name'],
					'value' => $slug,
				);

				$default_checkout_active_widgets[ $slug ] = 'true';
			}

			$thankyou_available_widgets      = [];
			$default_thankyou_active_widgets = [];

			foreach ( glob( jet_woo_builder()->plugin_path( 'includes/widgets/thankyou/' ) . '*.php' ) as $file ) {
				$data = get_file_data( $file, array( 'class' => 'Class', 'name' => 'Name', 'slug' => 'Slug' ) );

				$slug = basename( $file, '.php' );

				$thankyou_available_widgets[] = array(
					'label' => $data['name'],
					'value' => $slug,
				);

				$default_thankyou_active_widgets[ $slug ] = 'true';
			}

			$myaccount_available_widgets      = [];
			$default_myaccount_active_widgets = [];

			foreach ( glob( jet_woo_builder()->plugin_path( 'includes/widgets/myaccount/' ) . '*.php' ) as $file ) {
				$data = get_file_data( $file, array( 'class' => 'Class', 'name' => 'Name', 'slug' => 'Slug' ) );

				$slug = basename( $file, '.php' );

				$myaccount_available_widgets[] = array(
					'label' => $data['name'],
					'value' => $slug,
				);

				$default_myaccount_active_widgets[ $slug ] = 'true';
			}

			$product_thumb_effect_options = array(
				array(
					'label' => esc_html__( 'Slide Left', 'jet-woo-builder' ),
					'value' => 'slide-left',
				),
				array(
					'label' => esc_html__( 'Slide Right', 'jet-woo-builder' ),
					'value' => 'slide-right',
				),
				array(
					'label' => esc_html__( 'Slide Top', 'jet-woo-builder' ),
					'value' => 'slide-top',
				),
				array(
					'label' => esc_html__( 'Slide Bottom', 'jet-woo-builder' ),
					'value' => 'slide-bottom',
				),
				array(
					'label' => esc_html__( 'Fade', 'jet-woo-builder' ),
					'value' => 'fade',
				),
				array(
					'label' => esc_html__( 'Fade With Zoom', 'jet-woo-builder' ),
					'value' => 'fade-with-zoom',
				),
			);

			$rest_api_url = apply_filters( 'jet-woo-builder/rest/frontend/url', get_rest_url() );

			return array(
				'messages'       => array(
					'saveSuccess' => esc_html__( 'Saved', 'jet-woo-builder' ),
					'saveError'   => esc_html__( 'Error', 'jet-woo-builder' ),
				),
				'settingsApiUrl' => '/jet-woo-builder-api/v1/plugin-settings',
				'settingsData'   => array(
					'global_available_widgets'           => array(
						'value'   => $this->get( 'global_available_widgets', $default_global_active_widgets ),
						'options' => $global_available_widgets,
					),
					'single_product_available_widgets'   => array(
						'value'   => $this->get( 'single_product_available_widgets', $default_single_product_active_widgets ),
						'options' => $single_product_available_widgets,
					),
					'archive_product_available_widgets'  => array(
						'value'   => $this->get( 'archive_product_available_widgets', $default_archive_product_active_widgets ),
						'options' => $archive_product_available_widgets,
					),
					'archive_category_available_widgets' => array(
						'value'   => $this->get( 'archive_category_available_widgets', $default_archive_category_active_widgets ),
						'options' => $archive_category_available_widgets,
					),
					'shop_product_available_widgets'     => array(
						'value'   => $this->get( 'shop_product_available_widgets', $default_shop_product_active_widgets ),
						'options' => $shop_product_available_widgets,
					),
					'cart_available_widgets'             => array(
						'value'   => $this->get( 'cart_available_widgets', $default_cart_active_widgets ),
						'options' => $cart_available_widgets,
					),
					'checkout_available_widgets'         => array(
						'value'   => $this->get( 'checkout_available_widgets', $default_checkout_active_widgets ),
						'options' => $checkout_available_widgets,
					),
					'thankyou_available_widgets'         => array(
						'value'   => $this->get( 'thankyou_available_widgets', $default_thankyou_active_widgets ),
						'options' => $thankyou_available_widgets,
					),
					'myaccount_available_widgets'        => array(
						'value'   => $this->get( 'myaccount_available_widgets', $default_myaccount_active_widgets ),
						'options' => $myaccount_available_widgets,
					),
					'enable_product_thumb_effect'        => array(
						'value' => $this->get( 'enable_product_thumb_effect' ),
					),
					'product_thumb_effect'               => array(
						'value'   => $this->get( 'product_thumb_effect', 'slide-left' ),
						'options' => $product_thumb_effect_options,
					),
					'enable_inline_templates_styles'     => array(
						'value' => $this->get( 'enable_inline_templates_styles' ),
					),
				),
			);
		}

		/**
		 * Return settings page URL
		 *
		 * @return string
		 */
		public function get_settings_page_link() {

			return add_query_arg(
				array(
					'page' => $this->key,
				),
				esc_url( admin_url( 'admin.php' ) )
			);
		}

		/**
		 * Returns plugin admin settings
		 *
		 * @param $setting
		 * @param boolean $default
		 *
		 * @return bool|mixed
		 */
		public function get( $setting = '', $default = false ) {

			if ( null === $this->settings ) {
				$this->settings = get_option( $this->key, array() );
			}

			return isset( $this->settings[ $setting ] ) ? $this->settings[ $setting ] : $default;
		}

		/**
		 * Returns the instance.
		 *
		 * @return object
		 * @since  1.0.0
		 * @access public
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;

		}
	}
}

/**
 * Returns instance of Jet_Woo_Builder_Settings
 *
 * @return object
 */
function jet_woo_builder_settings() {
	return Jet_Woo_Builder_Settings::get_instance();
}
