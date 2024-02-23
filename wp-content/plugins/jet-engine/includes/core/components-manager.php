<?php
/**
 * Components manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Components' ) ) {

	/**
	 * Define Jet_Engine_Components class
	 */
	class Jet_Engine_Components {

		private $_components = [];

		/**
		 * Run component registartion
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_components' ), -2 );
			add_action( 'init', array( $this, 'init_components' ), -1 );
		}

		/**
		 * Register components before run init to allow unregister before init
		 *
		 * @return void
		 */
		public function register_components() {

			$default_components = array(
				'listings' => array(
					'filepath'   => jet_engine()->plugin_path( 'includes/components/listings/manager.php' ),
					'class_name' => 'Jet_Engine_Listings',
				),
				'cpt' => array(
					'filepath'   => jet_engine()->plugin_path( 'includes/components/post-types/manager.php' ),
					'class_name' => 'Jet_Engine_CPT',
					'base'       => array(
						'file'  => 'base-wp-instance',
						'class' => 'Jet_Engine_Base_WP_Intance',
					),
				),
				'meta_boxes' => array(
					'filepath'   => jet_engine()->plugin_path( 'includes/components/meta-boxes/manager.php' ),
					'class_name' => 'Jet_Engine_Meta_Boxes',
					'base'       => array(
						'file'  => 'base-wp-instance',
						'class' => 'Jet_Engine_Base_WP_Intance',
					),
				),
				'taxonomies' => array(
					'filepath'   => jet_engine()->plugin_path( 'includes/components/taxonomies/manager.php' ),
					'class_name' => 'Jet_Engine_CPT_Tax',
					'base'       => array(
						'file'  => 'base-wp-instance',
						'class' => 'Jet_Engine_Base_WP_Intance',
					),
				),
				'relations' => array(
					'filepath'   => jet_engine()->plugin_path( 'includes/components/relations/manager.php' ),
					'class_name' => '\Jet_Engine\Relations\Manager',
					'base'       => array(
						'file'  => 'base-wp-instance',
						'class' => 'Jet_Engine_Base_WP_Intance',
					),
				),
				'options_pages' => array(
					'filepath'   => jet_engine()->plugin_path( 'includes/components/options-pages/manager.php' ),
					'class_name' => 'Jet_Engine_Options_Pages',
					'base'       => array(
						'file'  => 'base-wp-instance',
						'class' => 'Jet_Engine_Base_WP_Intance',
					),
				),
				'glossaries' => array(
					'filepath'   => jet_engine()->plugin_path( 'includes/components/glossaries/manager.php' ),
					'class_name' => '\Jet_Engine\Glossaries\Manager',
				),
				'query_builder' => array(
					'filepath' => jet_engine()->plugin_path( 'includes/components/query-builder/manager.php' ),
					'base'     => array(
						'file'  => 'base-wp-instance',
						'class' => 'Jet_Engine_Base_WP_Intance',
					),
				),
				'elementor_views' => array(
					'filepath'   => jet_engine()->plugin_path( 'includes/components/elementor-views/manager.php' ),
					'class_name' => 'Jet_Engine_Elementor_Views',
				),
				'blocks_views' => array(
					'filepath'   => jet_engine()->plugin_path( 'includes/components/blocks-views/manager.php' ),
					'class_name' => 'Jet_Engine_Blocks_Views',
				),
				'bricks_views' => array(
					'filepath'   => jet_engine()->plugin_path( 'includes/components/bricks-views/manager.php' ),
					'class_name' => '\Jet_Engine\Bricks_Views\Manager',
				),
				'timber_views' => array(
					'filepath'   => jet_engine()->plugin_path( 'includes/components/timber-views/timber.php' ),
					'class_name' => '\Jet_Engine\Timber_Views\Package',
					'singleton'  => true,
				),
			);

			foreach ( $default_components as $component_slug => $component_data ) {
				$this->register_component( $component_slug, $component_data );
			}

			do_action( 'jet-engine/components/registered', $this );

		}

		/**
		 * Check if passed component is active
		 *
		 * @param  string  $slug [description]
		 * @return boolean       [description]
		 */
		public function is_component_active( $slug = '' ) {

			if ( ! $slug ) {
				return false;
			} else {
				return null !== jet_engine()->$slug;
			}

		}

		/**
		 * Initialize main components
		 *
		 * @return [type] [description]
		 */
		public function init_components() {

			foreach ( $this->_components as $slug => $data ) {

				if ( ( empty( $data['class_name'] ) || ! class_exists( $data['class_name'] ) ) && file_exists( $data['filepath'] ) ) {

					$class_name = ! empty( $data['class_name'] ) ? $data['class_name'] : false;

					if ( ! empty( $data['base'] ) && ! class_exists( $data['base']['class'] ) ) {
						require_once jet_engine()->plugin_path( 'includes/base/' . $data['base']['file'] . '.php' );
					}

					require_once $data['filepath'];

					if ( $class_name && empty( $data['singleton'] ) ) {
						jet_engine()->$slug = new $class_name();
					}

				}
			}

			do_action( 'jet-engine/components/init', $this, jet_engine() );

		}

		/**
		 * Register JetEngine component
		 *
		 * @param  string $slug Component slug
		 * @param  array  $data Component data
		 * @return void
		 */
		public function register_component( $slug = '', $data = array() ) {
			$this->_components[ $slug ] = $data;
		}

		/**
		 * Unregister JetEngine component
		 *
		 * @param  string $slug Component slug
		 * @return void
		 */
		public function deregister_component( $slug = '' ) {

			if ( isset( $this->_components[ $slug ] ) ) {
				unset( $this->_components[ $slug ] );
			}

		}

	}

}
