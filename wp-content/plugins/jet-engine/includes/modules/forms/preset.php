<?php
/**
 * Captcha manager class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Booking_Forms_Preset' ) ) {

	/**
	 * Define Jet_Engine_Booking_Forms_Preset class
	 */
	class Jet_Engine_Booking_Forms_Preset {

		private $meta_key  = '_preset';
		private $form_id   = null;
		private $data      = null;
		private $source    = null;
		private $defaults  = array(
			'enabled'    => false,
			'from'       => 'post',
			'post_from'  => 'current_post',
			'user_from'  => 'current_user',
			'query_var'  => '_post_id',
			'fields_map' => array(),
		);

		public function __construct( $form_id = null ) {
			$this->form_id = $form_id;
		}

		/**
		 * Update captcha related meta with data from $_POST array
		 * @return [type] [description]
		 */
		public function update_meta() {

			if ( ! $this->form_id ) {
				return;
			}

			$preset = isset( $_POST[ $this->meta_key ] ) ? $_POST[ $this->meta_key ] : $this->defaults;
			update_post_meta( $this->form_id, $this->meta_key, $preset );

		}

		/**
		 * Sanitize prest source
		 *
		 * @return [type] [description]
		 */
		public function sanitize_source() {
			return apply_filters( 'jet-engine/forms/preset/sanitize-source', $this->_sanitize_source(), $this );
		}

		/**
		 * Internal method to sanitize preset source.
		 * Result of this method could be filtered by 3rd party from $this->sanitize_source();
		 *
		 * @return [type] [description]
		 */
		private function _sanitize_source() {

			$data = $this->get_data();

			if ( empty( $data['enabled'] ) ) {
				return true;
			}

			$from   = ! empty( $data['from'] ) ? $data['from'] : $this->defaults['from'];
			$source = $this->get_source( $data );

			if ( ! $source ) {
				return true;
			}

			if ( 'post' === $from ) {

				if ( ! is_user_logged_in() ) {
					return false;
				}

				if ( absint( $source->post_author ) !== get_current_user_id() && ! current_user_can( 'edit_others_posts' ) ) {
					return false;
				}


			} elseif ( 'user' === $from ) {

				if ( ! is_user_logged_in() ) {
					return false;
				}

				if ( get_current_user_id() !== $source->ID && ! current_user_can( 'edit_users' ) ) {
					return false;
				}

			}

			return true;

		}

		public function is_repeater_val( $value ) {
			if ( is_array( $value ) && ! empty( $value ) ) {
				$value = array_values( $value );
				return is_array( $value[0] );
			} else {
				return false;
			}
		}

		public function get_key_from_map( $map, $repeater_key ) {

			foreach ( $map as $field => $data ) {

				$prop = ! empty( $data['prop'] ) ? $data['prop'] : 'post_title';

				if ( 'post_meta' === $prop && ! empty( $data['key'] ) && $repeater_key == $data['key'] ) {
					return $field;
				}

			}

			return $repeater_key;
		}

		public function parse_dynamic_preset( $value ) {

			if ( is_array( $value ) ) {
				return false;
			}

			$dynamic_preset = json_decode( $value, true );

			if ( empty( $dynamic_preset ) || empty( $dynamic_preset['jet_preset'] ) ) {
				return false;
			} else {
				return $dynamic_preset;
			}
		}

		public function field_dynamic_preset( $args ) {

			if ( empty( $args['default'] ) ) {
				return false;
			}

			return $this->parse_dynamic_preset( $args['default'] );
		}

		/**
		 * Returns preset value
		 *
		 * @param string     $field
		 * @param array      $args
		 * @param array      $data
		 * @param bool|array $dynamic_preset
		 *
		 * @return array
		 */
		public function get_preset_value( $field = null, $args = array(), $data = array(), $dynamic_preset = false ) {

			$result = array(
				'rewrite' => false,
				'value'   => null,
			);

			if ( ! $field ) {
				return $result;
			}

			if ( empty( $data['enabled'] ) && ! $dynamic_preset ) {
				return $result;
			}

			$source = $this->get_source( $data, $dynamic_preset );

			if ( ! empty( $dynamic_preset ) ) {
				$data = $dynamic_preset;
			}

			$from = ! empty( $data['from'] ) ? $data['from'] : $this->defaults['from'];
			$map  = ! empty( $data['fields_map'] ) ? $data['fields_map'] : $this->defaults['fields_map'];

			if ( ! empty( $dynamic_preset ) ) {
				$map = array(
					$field => array(
						'prop' => ! empty( $data['current_field_prop'] ) ? $data['current_field_prop'] : '',
						'key'  => ! empty( $data['current_field_key'] ) ? $data['current_field_key'] : '',
					),
				);
			}

			$field_data = isset( $map[ $field ] ) ? $map[ $field ] : '';

			if ( ! $source ) {

				if ( ! empty( $dynamic_preset ) ) {

					$value = apply_filters( 'jet-engine/forms/preset-value/' . $from, '', $field_data, $data, $source );

					$result['rewrite'] = true;
					$result['value']   = $value;
				}

				return $result;

			}

			if ( empty( $map[ $field ] ) || ( empty( $map[ $field ]['prop'] ) && empty( $map[ $field ]['key'] ) ) ) {

				if ( ! empty( $dynamic_preset ) ) {
					$result['rewrite'] = true;
					$result['value']   = '';
				}
				return $result;
			}

			$value         = null;
			$array_allowed = in_array( $args['type'], array( 'checkboxes' ) ) || ! empty( $args['array_allowed'] );

			if ( 'post' === $from ) {

				if ( empty( $dynamic_preset ) && absint( $source->post_author ) !== get_current_user_id()
				     && ! current_user_can( 'edit_others_posts' )
				) {
					return $result;
				}

				$prop = ! empty( $field_data['prop'] ) ? $field_data['prop'] : 'post_title';

				if ( 'post_meta' === $prop ) {

					if ( ! empty( $field_data['key'] ) ) {

						$value = get_post_meta( $source->ID, $field_data['key'], true );

						if ( $this->is_repeater_val( $value ) ) {

							$prepared_value = array();

							foreach ( $value as $index => $row ) {

								$prepared_row = array();

								foreach ( $row as $item_key => $item_value ) {

									$item_key = $this->get_key_from_map( $map, $item_key );

									$prepared_row[ $item_key ] = $item_value;
								}

								$prepared_value[] = $prepared_row;

							}

							$value = $prepared_value;

						}

						if ( jet_engine()->relations && jet_engine()->relations->legacy->is_relation_key( $field_data['key'] ) ) {

							$info = jet_engine()->relations->legacy->get_relation_info( $field_data['key'] );

							if ( ! $info ) {
								return $result;
							}

							$args = array(
								'post_id'     => $source->ID,
								'post_type_1' => $info['post_type_1'],
								'post_type_2' => $info['post_type_2'],
							);

							if ( $source->post_type === $info['post_type_1'] ) {
								$args['from'] = $info['post_type_2'];
							} else {
								$args['from'] = $info['post_type_1'];
							}

							$value = jet_engine()->relations->legacy->get_related_posts( $args );

						} else {
							$value = get_post_meta( $source->ID, $field_data['key'], true );
						}

					} else {
						return $result;
					}
				} elseif ( 'post_terms' === $prop ) {
					if ( ! empty( $field_data['key'] ) ) {

						$value = wp_get_post_terms( $source->ID, $field_data['key'] );

						if ( empty( $value ) || is_wp_error( $value ) ) {
							return $result;
						} else {
							if ( $array_allowed ) {
								$value = array_map( function( $term ) {
									return strval( $term->term_id );
								}, $value );
							} else {
								$value = $value[0];
								$value = $value->term_id;
							}
						}

					} else {
						return $result;
					}
				} elseif ( 'post_thumb' === $prop ) {
					$value = get_post_thumbnail_id( $source->ID );
				} else {
					$value = isset( $source->$prop ) ? $source->$prop : null;
				}

			} elseif ( 'user' === $from ) {

				if ( ! $source || is_wp_error( $source ) ) {
					return $result;
				}

				if ( ! is_user_logged_in() ) {
					return $result;
				}

				if ( empty( $dynamic_preset ) && get_current_user_id() !== $source->ID
				     && ! current_user_can( 'edit_users' )
				) {
					return $result;
				}

				$prop = ! empty( $field_data['prop'] ) ? $field_data['prop'] : 'post_title';

				if ( 'user_meta' === $prop ) {
					if ( ! empty( $field_data['key'] ) ) {
						$value = get_user_meta( $source->ID, $field_data['key'], true );
					} else {
						return $result;
					}
				} else {

					// adjust props
					switch ( $prop ) {
						case 'email':
							$prop = 'user_email';
							break;

						case 'login':
							$prop = 'user_login';
							break;
					}

					if ( isset( $source->data->$prop ) ) {
						$value = $source->data->$prop;
					} elseif ( isset( $source->$prop ) ) {
						$value = $source->$prop;
					} else {
						$value = null;
					}

				}

			} elseif ( 'option_page' === $from ) {

				$key = ! empty( $field_data['key'] ) ? $field_data['key'] : false;

				if ( $key ) {
					$value = jet_engine()->listings->data->get_option( $key );
				}

			} else {

				$key = ! empty( $field_data['key'] ) ? $field_data['key'] : false;

				if ( $key && is_array( $source ) ) {
					$value = isset( $source[ $key ] ) ? $source[ $key ] : null;
				}

				$value = apply_filters( 'jet-engine/forms/preset-value/' . $from, $value, $field_data, $data, $source );

			}

			// Prepare value for date field
			if ( in_array( $args['type'], array( 'date', 'datetime-local' ) )
			     && ! Jet_Engine_Tools::is_valid_timestamp( $value )
			) {
				$value = strtotime( $value );
			}

			if ( 'date' === $args['type'] && $value ) {
				$value = date_i18n( 'Y-m-d', $value );
			}

			if ( 'datetime-local' === $args['type'] && $value ) {
				$value = date_i18n( 'Y-m-d\TH:i', $value );
			}

			if ( null === $value && ! empty( $dynamic_preset ) ) {
				$value = '';
			}

			if ( null !== $value ) {
				$result['rewrite'] = true;
				$result['value']   = $value;
			}

			return $result;

		}

		/**
		 * Returns field value
		 *
		 * @return [type] [description]
		 */
		public function get_field_value( $field = null, $args = array() ) {

			$result = array(
				'rewrite' => false,
				'value'   => null,
			);

			if ( ! $field ) {
				return $result;
			}

			$data           = $this->get_data();
			$dynamic_preset = $this->field_dynamic_preset( $args );

			return $this->get_preset_value( $field, $args, $data, $dynamic_preset );
		}

		/**
		 * Returns condition dynamic value.
		 *
		 * @param array $cond_args
		 * @param array $field_args
		 *
		 * @return array
		 */
		public function get_condition_value( $cond_args = array(), $field_args = array() ) {

			$result = array(
				'rewrite' => false,
				'value'   => null,
			);

			if ( empty( $cond_args['value'] ) || empty( $cond_args['field'] ) ) {
				return $result;
			}

			$dynamic_preset = $this->parse_dynamic_preset( $cond_args['value'] );

			if ( empty( $dynamic_preset ) ) {
				return $result;
			}

			return $this->get_preset_value( $cond_args['field'], $field_args, false, $dynamic_preset );
		}

		/**
		 * Returns field source
		 *
		 * @return [type] [description]
		 */
		public function get_source( $data = array(), $dynamic_preset = false ) {

			if ( null !== $this->source && ! $dynamic_preset ) {
				return $this->source;
			}

			if ( ! empty( $dynamic_preset ) ) {
				$data = $dynamic_preset;
			}

			$from = ! empty( $data['from'] ) ? $data['from'] : $this->defaults['from'];
			$source = $this->source;

			switch ( $from ) {
				case 'query_vars':
					$source = $_GET;
					break;

				case 'user':
					$user_from = ! empty( $data['user_from'] ) ? $data['user_from'] : $this->defaults['user_from'];

					if ( 'current_user' === $user_from ) {
						if ( is_user_logged_in() ) {
							$source = wp_get_current_user();
						}
					} else {

						$var     = ! empty( $data['query_var'] ) ? $data['query_var'] : $this->defaults['query_var'];
						$user_id = ( $var && isset( $_REQUEST[ $var ] ) ) ? $_REQUEST[ $var ] : false;

						$source = get_user_by( 'ID', $user_id );

					}

					break;

				case 'option_page':

					$post_id = get_the_ID();

					if ( $post_id ) {
						$source = get_post( $post_id );
					}

					break;

				case 'post':

					$post_from = ! empty( $data['post_from'] ) ? $data['post_from'] : $this->defaults['post_from'];

					if ( 'current_post' === $post_from ) {
						$post_id = get_the_ID();
					} else {
						$var     = ! empty( $data['query_var'] ) ? $data['query_var'] : $this->defaults['query_var'];
						$post_id = ( $var && isset( $_REQUEST[ $var ] ) ) ? $_REQUEST[ $var ] : false;
					}

					if ( $post_id ) {
						$source = get_post( $post_id );
					}

					break;

				default:
					$source = apply_filters( 'jet-engine/forms/preset-source/' . $from, null, $data );
			}

			if ( ! $dynamic_preset ) {
				$this->source = $source;
				return $this->source;
			} else {
				return $source;
			}

		}

		/**
		 * Returns captcha settings for passed form ID
		 *
		 * @param  [type] $post_id [description]
		 * @return [type]          [description]
		 */
		public function get_data() {

			if ( null !== $this->data ) {
				return $this->data;
			}

			if ( ! $this->form_id ) {
				$this->form_id = get_the_ID();
			}

			$preset = get_post_meta( $this->form_id, $this->meta_key, true );

			if ( ! $preset || ! is_array( $preset ) ) {
				$this->data = $this->defaults;
			} else {
				$this->data = wp_parse_args( $preset, $this->defaults );
			}

			return $this->data;

		}

	}

}
