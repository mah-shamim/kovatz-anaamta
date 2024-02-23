<?php
/**
 * Shortcode
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Forms_Shortcode' ) ) {

	class Jet_Engine_Forms_Shortcode {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		public function __construct() {
			add_filter( 'jet-engine/shortcodes/default-atts', array( $this, 'add_forms_default_atts' ) );
			add_filter( 'jet-engine/shortcodes/forms/result', array( $this, 'do_shortcode' ), 10, 2 );

			if ( is_admin() ) {
				add_action( 'init', array( $this, 'init_admin_columns_hooks' ) );
			}
		}

		public function init_admin_columns_hooks() {
			add_filter( 'manage_' . jet_engine()->forms->slug() . '_posts_columns',       array( $this, 'edit_columns' ) );
			add_action( 'manage_' . jet_engine()->forms->slug() . '_posts_custom_column', array(  $this, 'manage_columns' ), 10, 2 );
		}

		public function add_forms_default_atts( $atts = array() ) {
			$form_atts = array(
				'_form_id'         => '',
				'fields_layout'    => 'row',
				'fields_label_tag' => 'div',
				'submit_type'      => 'reload',
				'cache_form'       => false,
			);

			return array_merge( $atts, $form_atts );
		}

		public function do_shortcode( $result = '', $atts = array() ) {

			if ( empty( $atts['_form_id'] ) ) {
				return $result;
			}

			$block_instance = jet_engine()->blocks_views->block_types->get_block_type_instance( 'booking-form' );

			if ( ! $block_instance ) {
				return $result;
			}

			return $block_instance->render_callback( $atts );
		}

		public function edit_columns( $columns = array() ) {

			$columns['form-shortcode'] = esc_html__( 'Shortcode', 'jet-engine' );

			return $columns;
		}

		public function manage_columns( $column, $post_id ) {

			if ( 'form-shortcode' !== $column ) {
				return;
			}

			$shortcode = sprintf( '[jet_engine component="forms" _form_id="%d"]', $post_id );

			printf(
				'<input type="text" readonly value="%s" style="%s" />',
				esc_attr( $shortcode ),
				'width:100%'
			);
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
		 */
		public static function instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

	}

}