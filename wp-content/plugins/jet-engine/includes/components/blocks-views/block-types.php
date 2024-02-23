<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Blocks_Views_Types' ) ) {

	/**
	 * Define Jet_Engine_Blocks_Views_Types class
	 */
	class Jet_Engine_Blocks_Views_Types {

		private $_types = array();

		public function __construct() {
			add_action( 'init', array( $this, 'register_block_types' ), 99 );
			add_filter( 'block_categories_all', array( $this, 'add_blocks_category' ) );
		}

		/**
		 * Add new category for JetEngine blocks
		 */
		function add_blocks_category( $categories ) {
			return array_merge(
				array(
					array(
						'slug'  => 'jet-engine',
						'title' => __( 'JetEngine', 'jet-engine' ),
						'icon'  => 'database-add',
					),
				),
				$categories
			);
		}

		/**
		 * Register block types
		 *
		 * @return [type] [description]
		 */
		public function register_block_types() {

			$types_dir = jet_engine()->plugin_path( 'includes/components/blocks-views/block-types/' );

			require $types_dir . 'base.php';
			require $types_dir . 'dynamic-field.php';
			require $types_dir . 'dynamic-image.php';
			require $types_dir . 'dynamic-link.php';
			require $types_dir . 'dynamic-repeater.php';
			require $types_dir . 'dynamic-meta.php';
			require $types_dir . 'dynamic-terms.php';
			require $types_dir . 'listing-grid.php';
			require $types_dir . 'container.php';
			require $types_dir . 'section.php';

			$types = array(
				new Jet_Engine_Blocks_Views_Type_Dynamic_Field(),
				new Jet_Engine_Blocks_Views_Type_Dynamic_Image(),
				new Jet_Engine_Blocks_Views_Type_Dynamic_Link(),
				new Jet_Engine_Blocks_Views_Type_Dynamic_Repeater(),
				new Jet_Engine_Blocks_Views_Type_Dynamic_Meta(),
				new Jet_Engine_Blocks_Views_Type_Dynamic_Terms(),
				new Jet_Engine_Blocks_Views_Type_Listing_Grid(),
				new Jet_Engine_Blocks_Views_Type_Container(),
				new Jet_Engine_Blocks_Views_Type_Section(),
			);

			foreach ( $types as $type ) {
				$this->_types[ $type->get_name() ] = $type;
			}

			do_action( 'jet-engine/blocks-views/register-block-types', $this );

		}

		public function register_block_type( $block_type ) {
			if ( isset( $this->_types[ $block_type->get_name() ] ) ) {
				return;
			}

			$this->_types[ $block_type->get_name() ] = $block_type;
		}

		/**
		 * Returns block attributes list
		 */
		public function get_block_atts( $block = null ) {

			if ( ! $block ) {
				return array();
			}

			$type = isset( $this->_types[ $block ] ) ? $this->_types[ $block ] : false;

			if ( ! $type ) {
				return array();
			}

			return $type->get_attributes();

		}

		public function get_blocks_with_id_attr() {

			$result = array();

			foreach ( $this->_types as $type ) {
				$attributes = $type->get_attributes();

				if ( isset( $attributes['_element_id'] ) ) {
					$result[] = $type->get_block_name();
				}
			}

			return $result;
		}

		public function get_allowed_callbacks_atts() {

			$atts       = array();
			$disallowed = array( 'checklist_divider_color' );

			foreach ( jet_engine()->listings->get_callbacks_args() as $key => $args ) {

				if ( in_array( $key, $disallowed ) ) {
					continue;
				}

				$attr = array();

				switch ( $args['type'] ) {
					case 'number':
						$attr['type'] = 'number';

						if ( isset( $args['default'] ) ) {
							$attr['default'] = intval( $args['default'] );
						}

						break;

					case 'slider':
						$attr['type'] = 'number';

						if ( isset( $args['default']['size'] ) ) {
							$attr['default'] = intval( $args['default']['size'] );
						}

						break;

					case 'switcher':
						$attr['type']    = 'boolean';
						$attr['default'] = ! empty( $args['default'] ) ? filter_var( $args['default'], FILTER_VALIDATE_BOOLEAN ) : false;

						break;

					default:
						$attr['type'] = 'string';
						$attr['default'] = ! empty( $args['default'] ) ? $args['default'] : '';
				}

				$atts[ $key ] = $attr;
			}

			return $atts;
		}

		public function get_block_type_instance( $block = null ) {

			if ( empty( $block ) ) {
				return null;
			}

			return isset( $this->_types[ $block ] ) ? $this->_types[ $block ] : null;
		}

	}

}
