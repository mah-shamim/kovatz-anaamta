<?php
/**
 * Handle JetWooBuilder ajax requests
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Ajax_Handlers' ) ) {

	/**
	 * Define Jet_Woo_Builder_Ajax_Handlers class
	 */
	class Jet_Woo_Builder_Ajax_Handlers {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * Init Handler
		 */
		public function __construct() {
			add_action( 'wp_ajax_jet_woo_builder_get_layout', [ $this, 'get_switcher_template' ] );
			add_action( 'wp_ajax_nopriv_jet_woo_builder_get_layout', [ $this, 'get_switcher_template' ] );

			add_action( 'wp_ajax_jet_woo_builder_add_cart_single_product', [ $this, 'add_cart_single_product_ajax' ] );
			add_action( 'wp_ajax_nopriv_jet_woo_builder_add_cart_single_product', [ $this, 'add_cart_single_product_ajax' ] );
		}

		/**
		 * Processing switcher template ajax
		 */
		public function get_switcher_template() {

			$args                = json_decode( stripslashes( $_POST['query'] ), true );
			$args['post_status'] = 'publish';
			$layout              = isset( $_POST['layout'] ) ? absint( $_POST['layout'] ) : '';
			$filters_query       = ! empty( $_POST['filters'] ) ? $_POST['filters'] : [];
			$response            = [];

			switch ( $args['orderby'] ) {
				case 'price' :
					$args['meta_key'] = '_price';
					$args['orderby']  = 'meta_value_num';

					break;

				case 'rating':
					$args['meta_key'] = '_wc_average_rating';
					$args['orderby']  = 'meta_value_num';
					$args['order']    = 'DESC';

					break;

				case 'popularity':
					$args['meta_key'] = 'total_sales';
					$args['orderby']  = 'meta_value_num ID';
					$args['order']    = 'DESC';

					break;

				default:
					break;
			}

			wc_setcookie( 'jet_woo_builder_layout', $layout, strtotime( '+1 year' ) );

			if ( ! empty( $filters_query ) && function_exists( 'jet_smart_filters' ) ) {
				jet_smart_filters()->query->get_query_from_request( $filters_query );

				foreach ( jet_smart_filters()->query->_query as $key => $var ) {
					$args[ $key ] = $var;
				}
			}

			if ( ! class_exists( 'Elementor\Jet_Woo_Builder_Base' ) ) {
				require_once jet_woo_builder()->plugin_path( 'includes/components/elementor-views/widget-base.php' );
			}

			if ( ! class_exists( 'Elementor\Jet_Woo_Builder_Products_Loop' ) ) {
				require_once jet_woo_builder()->plugin_path( 'includes/widgets/shop/jet-woo-builder-products-loop.php' );
			}

			ob_start();
			query_posts( $args );

			jet_woo_builder()->woocommerce->products_loop_template_rewrite = true;
			jet_woo_builder()->woocommerce->current_template_archive       = $layout;

			Elementor\Jet_Woo_Builder_Products_Loop::products_loop();

			$response['html'] = ob_get_clean();

			$response = apply_filters( 'jet-woo-builder/ajax-handler/get-switcher-template/response', $response );

			wp_send_json_success( $response );

		}

		/**
		 * Single Product add to cart ajax request.
		 *
		 * @since 1.8.1
		 *
		 * @return void.
		 */
		public function add_cart_single_product_ajax() {

			add_action( 'wp_loaded', [ 'WC_Form_Handler', 'add_to_cart_action' ], 20 );

			if ( is_callable( [ 'WC_AJAX', 'get_refreshed_fragments' ] ) ) {
				WC_AJAX::get_refreshed_fragments();
			}

			die();

		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
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