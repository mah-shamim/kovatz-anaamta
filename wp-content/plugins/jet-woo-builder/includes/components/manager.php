<?php
/**
 * Components manager.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Components' ) ) {

	/**
	 * Define class.
	 */
	class Jet_Woo_Builder_Components {

		// Components holder.
		private $_components = [];

		public function __construct() {
			add_action( 'init', [ $this, 'register_components' ], -2 );
			add_action( 'init', [ $this, 'init_components' ], -1 );
		}

		/**
		 * Register components.
		 *
		 * Register components before run init to allow unregister before init.
		 *
		 * @since  1.13.0
		 * @access public
		 *
		 * @return void
		 */
		public function register_components() {

			$default_components = [
				'elementor_views' => [
					'filepath'   => jet_woo_builder()->plugin_path( 'includes/components/elementor-views/manager.php' ),
					'class_name' => 'Jet_Woo_Builder_Elementor_Views',
				],
				'woocommerce' => [
					'filepath'   => jet_woo_builder()->plugin_path( 'includes/components/woocommerce/manager.php' ),
					'class_name' => 'Jet_Woo_Builder_Woocommerce',
				],
			];

			foreach ( $default_components as $component_slug => $component_data ) {
				$this->register_component( $component_slug, $component_data );
			}

			do_action( 'jet-woo-builder/components/registered', $this );

		}

		/**
		 * Register component.
		 *
		 * Register JetWooBuilder single component.
		 *
		 * @since  1.13.0
		 * @access public
		 *
		 * @param string $slug Component slug
		 * @param array  $data Component data
		 *
		 * @return void
		 */
		public function register_component( $slug = '', $data = [] ) {
			$this->_components[ $slug ] = $data;
		}

		/**
		 * Init components.
		 *
		 * Initialize JetWooBuilder main components.
		 *
		 * @since  1.13.0
		 * @access public
		 *
		 * @return void
		 */
		public function init_components() {

			foreach ( $this->_components as $slug => $data ) {
				if ( ( empty( $data['class_name'] ) || ! class_exists( $data['class_name'] ) ) && file_exists( $data['filepath'] ) ) {
					$class_name = ! empty( $data['class_name'] ) ? $data['class_name'] : false;

					require_once $data['filepath'];

					if ( $class_name ) {
						jet_woo_builder()->$slug = new $class_name();
					}
				}
			}

			do_action( 'jet-woo-builder/components/init', $this, jet_woo_builder() );

		}

	}

}