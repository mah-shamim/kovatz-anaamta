<?php
/**
 * Base class for listing renderers
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Render_Base' ) ) {

	abstract class Jet_Engine_Render_Base {

		use \Jet_Engine\Modules\Performance\Traits\Prevent_Wrap;

		private $settings = null;

		public function __construct( $settings = array() ) {
			$parsed_settings = $this->get_parsed_settings( $settings );
			$this->settings  = apply_filters( 'jet-engine/listing/render/'. $this->get_name() . '/settings', $parsed_settings, $this );
		}

		public function get_settings( $setting = null ) {
			if ( $setting ) {
				return isset( $this->settings[ $setting ] ) ? $this->settings[ $setting ] : false;
			} else {
				return $this->settings;
			}
		}

		/**
		 * Returns parsed settings
		 *
		 * @param  array $settings
		 * @return array
		 */
		public function get_parsed_settings( $settings = array() ) {
			$defaults = $this->default_settings();
			$settings = wp_parse_args( $settings, $defaults );

			foreach ( $defaults as $key => $default_value ) {
				if ( null === $settings[ $key ] ) {
					$settings[ $key ] = $default_value;
				}
			}

			return $settings;
		}

		/**
		 * Returns plugin default settings
		 *
		 * @return array
		 */
		public function default_settings() {
			return array();
		}

		/**
		 * Returns required settings
		 *
		 * @return array
		 */
		public function get_required_settings() {
			$required = array();
			$settings = $this->get_settings();
			$default  = $this->default_settings();

			foreach ( $default as $key => $value ) {
				if ( isset( $settings[ $key ] ) ) {
					$required[ $key ] = $settings[ $key ];
				}
			}

			return $required;
		}

		public function get_default_cb_settings() {

			$settings   = array();
			$disallowed = array( 'checklist_divider_color' );

			foreach ( jet_engine()->listings->get_callbacks_args() as $key => $args ) {

				if ( in_array( $key, $disallowed ) ) {
					continue;
				}

				$settings[ $key ] = isset( $args['default'] ) ? $args['default'] : null;
			}

			return $settings;
		}

		public function get( $setting = null, $default = false ) {
			if ( isset( $this->settings[ $setting ] ) ) {
				return $this->settings[ $setting ];
			} else {
				$defaults = $this->default_settings();
				return isset( $defaults[ $setting ] ) ? $defaults[ $setting ] : $default;
			}
		}

		public function get_content() {
			ob_start();
			$this->render_content();
			return ob_get_clean();
		}

		/**
		 * Setup listing
		 * @param  [type] $listing_settings [description]
		 * @param  mixed $object_id         Can be passed object_id and method will try to setup object, or object itself and method just pass it
		 * @return [type]                   [description]
		 */
		public function setup_listing( $listing_settings = array(), $object_id = null, $glob = false, $listing_id = false ) {

			if ( ! empty( $listing_settings ) ) {
				jet_engine()->listings->data->set_listing( jet_engine()->listings->get_new_doc( $listing_settings, $listing_id ) );
			} else {
				$listing_settings = jet_engine()->listings->data->get_listing_settings( $listing_id );
			}

			$source = ! empty( $listing_settings['listing_source'] ) ? $listing_settings['listing_source'] : 'posts';

			switch ( $source ) {

				case 'posts':
				case 'repeater':

					if ( $glob ) {

						global $post;

						if ( ! is_object( $object_id ) ) {
							$post = get_post( $object_id );
							setup_postdata( $post );
							$object = $post;
						} else {
							$object = $object_id;
							$post   = $object;
							setup_postdata( $post );
						}

					} else {
						if ( ! is_object( $object_id ) ) {
							$object = get_post( $object_id );
						} else {
							$object = $object_id;
						}
					}

					break;

				case 'terms':

					$tax = ! empty( $listing_settings['listing_tax'] ) ? $listing_settings['listing_tax'] : '';

					if ( ! is_object( $object_id ) ) {
						$object = get_term( $object_id, $tax );
					} else {
						$object = $object_id;
					}

					break;

				case 'users':
					if ( ! is_object( $object_id ) ) {
						$object = get_user_by( 'ID', $object_id );
					} else {
						$object = $object_id;
					}
					break;

				default:

					if ( ! is_object( $object_id ) ) {
						$object = apply_filters(
							'jet-engine/listing/render/object/' . $source,
							false,
							$object_id,
							$listing_settings,
							$this
						);
					} else {
						$object = $object_id;
					}

					break;

			}

			jet_engine()->listings->data->set_current_object( $object );

		}

		public function get_wrapper_classes() {
			
			$base_class = $this->get_name();
			$settings   = $this->get_settings();
			$classes    = array(
				'jet-listing',
				$base_class,
			);

			if ( ! empty( $settings['className'] ) ) {
				$classes[] = esc_attr( $settings['className'] );
			}

			return $classes;

		}

		abstract public function get_name();

		/**
		 * Render listing item content
		 *
		 * @return [type] [description]
		 */
		abstract public function render();

		/**
		 * Call the render function from the exact Render instance
		 * @return [type] [description]
		 */
		public function render_content() {

			/**
			 * General hook fires before any JetEngine element render in any builder
			 */
			do_action( 'jet-engine/listing-element/before-render', $this );

			/**
			 * Specific hook for each JetEngine element fires before this element render
			 */
			do_action( 'jet-engine/listing-element/before-render/' . $this->get_name(), $this );

			$this->render();

			jet_engine()->frontend->footer_styles();
			jet_engine()->frontend->frontend_scripts();
		}

	}

}
