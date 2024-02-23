<?php
/**
 * QR Code embed module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Module_QR_Code' ) ) {

	/**
	 * Define Jet_Engine_Module_QR_Code class
	 */
	class Jet_Engine_Module_QR_Code extends Jet_Engine_Module_Base {

		private $qr_code_api = 'https://api.qrserver.com/v1/create-qr-code/';

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'qr-code';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'QR Code for Dynamic Field widget', 'jet-engine' );
		}

		/**
		 * Returns detailed information about current module for the dashboard page
		 * @return [type] [description]
		 */
		public function get_module_details() {
			return '<p>After activation, in the Meta Field drop-down menu of the Content settings tab of the Dynamic Field widget in Elementor page builder appears a “QR Code” option.</p>
					<p>This option allows you to choose any text data placed to the meta field and display it as a QR code.</p>';
		}

		public function get_video_embed() {
			return 'https://www.youtube.com/embed/uz5bJ8D36BQ';
		}

		/**
		 * Returns array links to the module-related resources
		 * @return array
		 */
		public function get_module_links() {
			return array(
				array(
					'label' => 'JetEngine: How to Enable Extra Modules to Work with a Dynamic Calendar, QR Codes, and Galleries',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-how-to-enable-extra-modules-to-work-with-a-dynamic-calendar-qr-codes-and-galleries/',
				),
				array(
					'label' => 'How to display QR codes with the Dynamic Field widget',
					'url'   => 'https://www.youtube.com/watch?v=uz5bJ8D36BQ',
					'is_video' => true,
				),
			);
		}

		/**
		 * Module init
		 *
		 * @return void
		 */
		public function module_init() {

			add_filter( 'jet-engine/listings/allowed-callbacks', array( $this, 'add_qr_code_cb' ) );
			add_filter( 'jet-engine/listing/dynamic-field/callback-args', array( $this, 'cb_args' ), 10, 4 );
			add_action( 'jet-engine/listings/allowed-callbacks-args', array( $this, 'cb_controls' ) );

		}

		/**
		 * Add QR code to callbacks
		 *
		 * @param  array $callbacks
		 * @return array
		 */
		public function add_qr_code_cb( $callbacks = array() ) {
			$callbacks['jet_engine_get_qr_code'] = __( 'QR Code', 'jet-engine' );
			return $callbacks;
		}

		/**
		 * Add call-back related controls
		 *
		 * @param  array $args
		 * @return array
		 */
		public function cb_controls( $args ) {

			$args['qr_code_size'] = array(
				'label' => esc_html__( 'QR Code Size', 'jet-engine' ),
				'type'  => 'slider',
				'range' => array(
					'px' => array(
						'min' => 50,
						'max' => 400,
					),
				),
				'condition' => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_get_qr_code' ),
				),
			);

			return $args;
		}

		/**
		 * Callback arguments
		 *
		 * @param  [type] $args     [description]
		 * @param  [type] $callback [description]
		 * @param  [type] $settings [description]
		 * @param  [type] $widget   [description]
		 * @return [type]           [description]
		 */
		public function cb_args( $args, $callback, $settings, $widget ) {

			if ( 'jet_engine_get_qr_code' !== $callback ) {
				return $args;
			}

			if ( ! empty( $settings['qr_code_size'] ) && is_array( $settings['qr_code_size'] ) ) {
				$size = ! empty( $settings['qr_code_size']['size'] ) ? absint( $settings['qr_code_size']['size'] ) : 150;
			} else if ( ! empty( $settings['qr_code_size'] ) ) {
				$size = absint( $settings['qr_code_size'] );
			} else {
				$size = 150;
			}

			// Convert object ID to object link.
			if ( ! empty( $args[0] ) 
				&& isset( $settings['dynamic_field_source'] ) 
				&& 'object' === $settings['dynamic_field_source'] 
			) {

				switch ( $settings['dynamic_field_post_object'] ) {
					case 'post_id':
						$args[0] = get_permalink( $args[0] );
						break;

					case 'term_id':
						$args[0] = get_term_link( $args[0] );
						break;
				}

			}

			return array_merge( $args, array( $size ) );

		}

		/**
		 * Get QR Code for meta key
		 *
		 * @param  [type]  $meta_value [description]
		 * @param  integer $size       [description]
		 * @return [type]              [description]
		 */
		public function get_qr_code( $value = null, $size = 150 ) {

			$hash   = 'qr_' . md5( $size . $value );
			$cached = get_transient( $hash );

			if ( $cached ) {
				return $cached;
			}

			// Fixed '&' double-encoding bug
			$value = str_replace( '&amp;', '&', $value );

			$request = add_query_arg(
				array(
					'size'   => $size . 'x' . $size,
					'data'   => urlencode( $value ),
					'format' => 'svg',
				),
				$this->qr_code_api
			);

			$response = wp_remote_get( $request );
			$svg      = wp_remote_retrieve_body( $response );

			set_transient( $hash, $svg, DAY_IN_SECONDS );

			return $svg;
		}

	}

}
