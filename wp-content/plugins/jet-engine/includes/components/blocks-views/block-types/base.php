<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Blocks_Views_Type_Base' ) ) {

	/**
	 * Define Jet_Engine_Blocks_Views_Type_Base class
	 */
	abstract class Jet_Engine_Blocks_Views_Type_Base {

		use \Jet_Engine\Modules\Performance\Traits\Prevent_Wrap;

		protected $namespace = 'jet-engine/';

		public $block_manager    = null;
		public $controls_manager = null;
		public $block_data       = null;
		public $_root            = [];

		public function __construct() {

			$attributes = $this->get_attributes();

			if ( $this->has_style_manager() ) {

				$this->set_style_manager_instance();
				$this->add_style_manager_options();
				do_action( 'jet-engine/blocks-views/' . $this->get_name() . '/add-extra-style-options', $this );

				//add_action( 'enqueue_block_editor_assets', array( $this, 'add_style_manager_options' ), -1 );
				
				if ( $this->prevent_wrap() ) {
					add_filter(
						'jet_style_manager/gutenberg/prevent_block_wrap/' . $this->get_block_name(),
						'__return_true'
					);
				}

			}

			/**
			 * Set default blocks attributes to avoid errors
			 */
			$attributes['className'] = array(
				'type' => 'string',
				'default' => '',
			);

			$file = $this->block_file();
			$args = array(
				'editor_style' => 'jet-engine-frontend',
			);

			if ( $file ) {

				$block = $file;
				$render_callback = $this->get_file_data( 'render_callback' );

				if ( $render_callback ) {
					$args['render_callback'] = array( $this, '_render_callback' );
				}

			} else {

				$block = $this->get_block_name();

				$args['attributes']      = $attributes;
				$args['render_callback'] = array( $this, '_render_callback' );

			}

			register_block_type( $block, $args );

		}

		/**
		 * Check is has style manager instance
		 *
		 * @return [type] [description]
		 */
		public function has_style_manager() {
			return ( class_exists( '\JET_SM\Gutenberg\Block_Manager' ) && class_exists( '\JET_SM\Gutenberg\Block_Manager' ) );
		}

		/**
		 * Returns fiull name of the block
		 *
		 * @return [type] [description]
		 */
		public function get_block_name() {
			return $this->namespace . $this->get_name();
		}

		abstract public function get_name();

		/**
		 * Return attributes array
		 *
		 * @return array
		 */
		abstract public function get_attributes();

		/**
		 * Returns path to JSON file with block configuration
		 *
		 * @return string
		 */
		public function block_file() {
			return false;
		}

		/**
		 * Returns block data from JSON file
		 *
		 * @return [type] [description]
		 */
		public function get_file_data( $key, $default = false ) {

			if ( null === $this->block_data ) {

				$file = $this->block_file();

				if ( $file && file_exists( $file ) ) {

					ob_start();
					include $file;
					$contents = ob_get_clean();
					$contents = json_decode( $contents, true );

					$this->block_data = is_array( $contents ) ? $contents : array();

				} else {
					$this->block_data = array();
				}

			}

			return isset( $this->block_data[ $key ] ) ? $this->block_data[ $key ] : $default;

		}

		/**
		 * Retruns attra from input array if not isset, get from defaults
		 *
		 * @return [type] [description]
		 */
		public function get_attr( $attr = '', $all = array() ) {
			if ( isset( $all[ $attr ] ) ) {
				return $all[ $attr ];
			} else {
				$defaults = $this->get_attributes();
				return isset( $defaults[ $attr ]['default'] ) ? $defaults[ $attr ]['default'] : '';
			}
		}

		/**
		 * Check if is blocks edit mode
		 *
		 * @return boolean [description]
		 */
		public function is_edit_mode() {
			return ( isset( $_GET['context'] ) && 'edit' === $_GET['context'] && isset( $_GET['attributes'] ) && $_GET['_locale'] );
		}

		/**
		 * Allow to filter raw attributes from block type instance to adjust JS and PHP attributes format
		 *
		 * @param  [type] $attributes [description]
		 * @return [type]             [description]
		 */
		public function prepare_attributes( $attributes ) {
			return $attributes;
		}

		/**
		 * Set style manager class instance
		 *
		 * @return boolean
		 */
		public function set_style_manager_instance(){

			$name = $this->get_block_name();

			$this->block_manager    = \JET_SM\Gutenberg\Block_Manager::get_instance();
			$this->controls_manager = new \JET_SM\Gutenberg\Controls_Manager( $name );
		}

		public function css_selector( $el = '' ) {
			return sprintf( '{{WRAPPER}} %1$s', $el );
		}

		/**
		 * Add style block options
		 *
		 * @return boolean
		 */
		public function add_style_manager_options() {}

		/**
		 * Set css classes
		 *
		 * @return boolean
		 */
		public function set_css_scheme() {
			$this->css_scheme = [];
		}

		public function get_render_instance( $attributes ) {
			return jet_engine()->listings->get_render_instance( $this->get_name(), $attributes );
		}

		public function reset_root() {
			$this->_root = [
				'class' => [],
			];
		}

		public function get_root_attr_string() {
			
			$result = [];

			foreach ( $this->_root as $attr => $value ) {
				if ( is_array( $value ) ) {
					$value = implode( ' ', array_unique( array_filter( $value ) ) );
				}
				$result[] = sprintf( '%1$s="%2$s"', $attr, esc_attr( $value ) );
			}

			return implode( $result );
		}

		public function render_callback( $attributes = array() ) {

			$item       = $this->get_name();
			$listing    = isset( $_REQUEST['listing'] ) ? $_REQUEST['listing'] : false;
			$listing_id = isset( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : false;
			$object_id  = isset( $_REQUEST['object'] ) ? absint( $_REQUEST['object'] ) : jet_engine()->listings->data->get_current_object();
			$attributes = $this->prepare_attributes( $attributes );
			$render     = $this->get_render_instance( $attributes );

			if ( ! $render ) {
				return __( 'Item renderer class not found', 'jet-engine' );
			}

			if ( ! $listing_id ) {
				$listing_id = jet_engine()->blocks_views->render->get_current_listing_id();
			}

			if ( $listing_id ) {
				$render->setup_listing( $listing, $object_id, true, $listing_id );
			}

			$content = $render->get_content();
			$el_id = ! empty( $attributes['_element_id'] ) ? $attributes['_element_id'] : '';

			if ( $el_id ) {
				$this->_root['id'] = esc_attr( $el_id );
			}

			$this->_root['data-is-block'] = $this->get_block_name();

			if ( ! empty( $attributes['className'] ) ) {
				$this->_root['class'][] = esc_attr( $attributes['className'] );
			}

			$content = sprintf(
				'<div %1$s>%2$s</div>',
				$this->get_root_attr_string(),
				$content
			);

			return $content;

		}

		public function _render_callback( $attributes ) {
			$result = $this->render_callback( $attributes );
			$this->reset_root();
			return $result;
		}

	}

}
