<?php
/**
 * Meta boxes mamager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CPT_Meta' ) ) {
	require jet_engine()->plugin_path( 'includes/components/meta-boxes/post.php' );
}

if ( ! class_exists( 'Jet_Engine_CPT_User_Meta' ) ) {

	/**
	 * Define Jet_Engine_CPT_User_Meta class
	 */
	class Jet_Engine_CPT_User_Meta extends Jet_Engine_CPT_Meta {

		private $args;
		private $fields;

		public $builder;

		/**
		 * Constructor for the class
		 */
		public function __construct( $args, $meta_box ) {

			$this->args     = $args;
			$this->meta_box = $meta_box;

			if ( ! empty( $args['hide_field_names'] ) ) {
				$this->hide_field_names = $args['hide_field_names'];
			}

			$fields = $this->prepare_meta_fields( $meta_box );
			$this->fields = $fields;

			if ( ! empty( $this->show_in_rest ) ) {
				
				if ( ! class_exists( 'Jet_Engine_Rest_User_Meta' ) ) {
					require jet_engine()->meta_boxes->component_path( 'rest-api/fields/user-meta.php' );
				}
				
				foreach ( $this->show_in_rest as $field ) {
					new Jet_Engine_Rest_User_Meta( $field, null );
				}
			}

			if ( ! is_admin() ) {
				return;
			}

			if ( ! jet_engine()->meta_boxes->conditions->check_conditions( $this->get_box_id(), $args ) ) {
				return;
			}

			add_action( 'current_screen', array( $this, 'init_on_allowed_screens' ) );

		}

		/**
		 * Returns processed user ID
		 * @return [type] [description]
		 */
		public function get_user_id() {

			global $current_screen;

			if ( $current_screen && 'user-edit' === $current_screen->base ) {
				$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : false;
			} elseif ( $current_screen && 'profile' === $current_screen->base ) {
				$user_id = get_current_user_id();
			} else {
				$user_id = false;
			}

			return apply_filters( 'jet-engine/user-meta/current-user-id', $user_id, $this );
		}

		/**
		 * Initialize on allowed screens
		 *
		 * @return [type] [description]
		 */
		public function init_on_allowed_screens( $current_screen ) {

			$allowed_screens = ! empty( $this->args['allowed_user_screens'] ) ? $this->args['allowed_user_screens'] : 'edit';

			switch ( $allowed_screens ) {
				case 'edit':

					if ( 'user-edit' === $current_screen->base ) {
						$this->register_fields();
					}

					break;

				case 'edit-profile':

					if ( in_array( $current_screen->base, array( 'user-edit', 'profile' ) ) ) {
						$this->register_fields( true );
					}

					break;
			}

		}

		/**
		 * Register user meta fields
		 *
		 * @param  boolean $profile [description]
		 * @return [type]           [description]
		 */
		public function register_fields( $profile = false ) {

			add_action( 'edit_user_profile', array( $this, 'render_fields' ), 20 );
			add_action( 'edit_user_profile_update', array( $this, 'edit_user_update' ) );

			if ( $profile ) {
				add_action( 'show_user_profile', array( $this, 'render_fields' ), 20 );
				add_action( 'personal_options_update', array( $this, 'personal_profile_update' ) );
			}

			add_action( 'admin_enqueue_scripts', array( $this, 'init_builder' ), 0 );
			add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_custom_css' ), 0 );
			add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_inline_js' ), 20 );

		}

		/**
		 * Initialize builder and register fields
		 *
		 * @return [type] [description]
		 */
		public function init_builder() {

			$this->builder = $this->get_builder_for_meta();

			$zero_allowed = array(
				'stepper',
				'slider',
			);

			$parent = 'user_meta_' . rand( 100, 999 );

			$name = ! empty( $this->args['name'] ) ? esc_attr( $this->args['name'] ) : false;

			if ( $this->edit_link ) {
				$name .= sprintf( 
					'<a href="%s" class="jet-engine-edit-box-link" target="_blank"><span class="dashicons dashicons-admin-generic"></span></a>', 
					$this->edit_link
				);
			}

			$this->builder->register_section( array(
				$parent => array(
					'type'   => 'section',
					'scroll' => true,
					'title'  => apply_filters( 'jet-engine/compatibility/translate-string', $name ),
				)
			) );

			$this->builder->register_settings(
				array(
					$parent . '_settings' => array(
						'type'   => 'settings',
						'parent' => $parent,
					),
				)
			);

			foreach ( $this->fields as $key => $field ) {

				if ( ! $key ) {
					continue;
				}

				$default = $this->get_arg( $field, 'value', '' );
				$value   = $this->get_meta( $key, $default, $field );

				if ( isset( $field['options_callback'] ) ) {
					$field['options'] = call_user_func( $field['options_callback'] );
				}

				$value = $this->prepare_field_value( $field, $value );

				$element         = $this->get_arg( $field, 'element', 'control' );
				$field['id']     = $this->get_arg( $field, 'id', $key );
				$field['parent'] = $this->get_arg( $field, 'parent', $parent . '_settings' );
				$field['name']   = $this->get_arg( $field, 'name', $key );
				$field['type']   = $this->get_arg( $field, 'type', '' );
				$field['value']  = $value;

				// Fix zero values for stepper and slider
				if ( ! $value && in_array( $field['type'], $zero_allowed ) ) {
					$field['value'] = 0;
				}

				$register_callback = 'register_' . $element;

				if ( method_exists( $this->builder, $register_callback ) ) {
					call_user_func( array( $this->builder, $register_callback ), $field );
				}
			}

		}

		/**
		 * Prepare field value.
		 *
		 * @param $field
		 * @param $value
		 *
		 * @return array
		 */
		public function prepare_field_value( $field, $value ) {

			switch ( $field['type'] ) {
				case 'repeater':

					if ( is_array( $value ) && ! empty( $field['fields'] ) ) {

						$repeater_fields =  $field['fields'];

						foreach ( $value as $item_id => $item_value ) {
							foreach ( $item_value as $repeater_field_id => $repeater_field_value ) {
								$value[ $item_id ][ $repeater_field_id ] = $this->prepare_field_value( $repeater_fields[ $repeater_field_id ], $repeater_field_value );
							}
						}
					}

					break;

				case 'checkbox':

					if ( ! empty( $field['is_array'] ) && ! empty( $field['options'] ) && ! empty( $value ) ) {

						$adjusted = array();

						if ( ! is_array( $value ) ) {
							$value = array( $value );
						}

						foreach ( $value as $val ) {
							$adjusted[ $val ] = 'true';
						}

						foreach ( $field['options'] as $opt_val => $opt_label ) {
							if ( ! in_array( $opt_val, $value ) ) {
								$adjusted[ $opt_val ] = 'false';
							}
						}

						$value = $adjusted;
					}

					break;

				case 'text':

					if ( ! empty( $value ) && $this->to_timestamp( $field ) && is_numeric( $value ) ) {

						switch ( $field['input_type'] ) {
							case 'date':
								$value = $this->get_date( 'Y-m-d', $value );
								break;

							case 'datetime-local':
								$value = $this->get_date( 'Y-m-d\TH:i', $value );
								break;
						}
					}

					break;
			}

			return $value;
		}

		/**
		 * Returns date converted from timestamp
		 * 
		 * @return [type] [description]
		 */
		public function get_date( $format, $time ) {
			return apply_filters( 'cx_user_meta/date', date( $format, $time ), $time, $format );
		}

		/**
		 * Safely get attribute from field settings array.
		 *
		 * @since  1.0.0
		 * @param  array            $field   arguments array.
		 * @param  string|int|float $arg     argument key.
		 * @param  mixed            $default default argument value.
		 * @return mixed
		 */
		public function get_arg( $field = array(), $arg = '', $default = '' ) {
			if ( is_array( $field ) && isset( $field[ $arg ] ) ) {
				return $field[ $arg ];
			}
			return $default;
		}

		/**
		 * Retrieve post meta field.
		 *
		 * @since  1.1.0
		 * @since  1.2.0 Process default value.
		 *
		 * @param  object $post    Current post object.
		 * @param  string $key     The meta key to retrieve.
		 * @param  mixed  $default Default value.
		 * @param  array  $field   Meta field apropriate to current key.
		 * @return string
		 */
		public function get_meta( $key = '', $default = false, $field = array() ) {

			$user_id = $this->get_user_id();

			if ( ! $user_id ) {
				return $default;
			}

			$pre_value = apply_filters(
				'jet-engine/user-meta/pre-get-meta/' . $key, false, $user_id, $key, $default, $field
			);

			if ( false !== $pre_value ) {
				return $pre_value;
			}

			$meta = get_user_meta( $user_id, $key, false );

			return ( empty( $meta ) ) ? $default : $meta[0];

		}

		/**
		 * Is date field
		 *
		 * @param  [type]  $input_type [description]
		 * @return boolean             [description]
		 */
		public function to_timestamp( $field ) {

			if ( empty( $field['input_type'] ) ) {
				return false;
			}

			if ( empty( $field['is_timestamp'] ) ) {
				return false;
			}

			if ( ! in_array( $field['input_type'], array( 'date', 'datetime-local' ) ) ) {
				return false;
			}

			return ( true === $field['is_timestamp'] );

		}

		/**
		 * Render fields
		 *
		 * @return [type] [description]
		 */
		public function render_fields() {
			$this->open_meta_wrap();
			$this->builder->render();
			$this->close_meta_wrap();
		}

		/**
		 * Open meta wrap
		 * @return void
		 */
		public function open_meta_wrap() {
			echo '<div class="jet-engine-user-meta-wrap">';
		}

		/**
		 * Get CSS wrapper selector.
		 *
		 * @return string
		 */
		public function get_css_wrapper_selector() {
			return '.jet-engine-user-meta-wrap ';
		}

		/**
		 * Fires on users edited by admin
		 *
		 * @param  [type] $user_id [description]
		 * @return [type]          [description]
		 */
		public function edit_user_update( $user_id ) {

			if ( ! current_user_can( 'edit_users' ) ) {
				return;
			}

			if ( ! current_user_can( 'edit_user', $user_id ) ) {
				return;
			}

			$this->update_meta( $user_id );

		}

		/**
		 * Fires when user editing own profile
		 *
		 * @return [type] [description]
		 */
		public function personal_profile_update( $user_id ) {

			if ( ! current_user_can( 'edit_user', $user_id ) ) {
				return;
			}

			$this->update_meta( $user_id );

		}

		/**
		 * Update user data
		 *
		 * @return [type] [description]
		 */
		public function update_meta( $user_id ) {

			/**
			 * Hook on before current metabox saving
			 */
			do_action( 'jet-engine/user-meta/before-save/', $user_id, $this );

			foreach ( $this->fields as $key => $field ) {

				if ( isset( $field['element'] ) && 'control' !== $field['element'] ) {
					continue;
				}

				$pre_processed = apply_filters( 'jet-engine/user-meta/preprocess/' . $key, false, $user_id, $this );

				if ( $pre_processed ) {
					continue;
				}

				if ( ! isset( $_POST[ $key ] ) || '' === $_POST[ $key ] ) {

					/**
					 * Fires before specific key will be deleted
					 */
					do_action( 'jet-engine/user-meta/before-delete/' . $key, $user_id, false, $this );

					update_user_meta( $user_id, $key, false );

					continue;
				}

				$value = $this->sanitize_meta( $field, $_POST[ $key ] );

				do_action( 'jet-engine/user-meta/before-save/' . $key, $user_id, $value, $key, $this );

				update_user_meta( $user_id, $key, $value );

			}

			/**
			 * Hook on after current metabox saving
			 */
			do_action( 'jet-engine/user-meta/after-save', $user_id, $this );

		}

		/**
		 * Sanitize passed meta value
		 *
		 * @since  1.1.3
		 * @param  array $field Meta field to sanitize.
		 * @param  mixed $value Meta value.
		 * @return mixed
		 */
		public function sanitize_meta( $field, $value ) {

			if ( 'repeater' === $field['type'] && ! empty( $field['fields'] ) && is_array( $value ) ) {
				$repeater_fields = $field['fields'];

				foreach ( $value as $item_id => $item_value ) {
					foreach ( $item_value as $repeater_field_id => $repeater_field_value ) {
						$value[ $item_id ][ $repeater_field_id ] = $this->sanitize_meta( $repeater_fields[ $repeater_field_id ], $repeater_field_value );
					}
				}
			}

			if ( 'checkbox' === $field['type'] && ! empty( $field['is_array'] ) ) {
				$raw    = ! empty( $value ) ? $value : array();
				$result = array();

				if ( is_array( $raw ) ) {
					foreach ( $raw as $raw_key => $raw_value ) {
						$bool_value = filter_var( $raw_value, FILTER_VALIDATE_BOOLEAN );
						if ( $bool_value ) {
							$result[] = $raw_key;
						}
					}
				}

				return $result;
			}

			if ( $this->to_timestamp( $field ) ) {
				return apply_filters( 'cx_user_meta/strtotime', strtotime( $value ), $value );
			}

			if ( empty( $field['sanitize_callback'] ) ) {
				return $this->sanitize_deafult( $value );
			}

			if ( ! is_callable( $field['sanitize_callback'] ) ) {
				return $this->sanitize_deafult( $value );
			}

			$key = ! empty( $field['name'] ) ? $field['name'] : null;

			return call_user_func(
				$field['sanitize_callback'],
				$value,
				$key,
				$field
			);

		}

		/**
		 * Cleare value with sanitize_text_field if not is array
		 *
		 * @since  1.1.3
		 * @param  mixed $value Passed value.
		 * @return mixed
		 */
		public function sanitize_deafult( $value ) {
			return is_array( $value ) ? $value : sanitize_text_field( $value );
		}

		public function is_allowed_on_current_admin_hook( $hook ) {
			if ( null !== $this->is_allowed_on_admin_hook ) {
				return $this->is_allowed_on_admin_hook;
			}

			$allowed_hooks = array(
				'user-edit.php',
				'profile.php',
			);

			if ( ! in_array( $hook, $allowed_hooks ) ) {
				$this->is_allowed_on_admin_hook = false;
				return $this->is_allowed_on_admin_hook;
			}

			$this->is_allowed_on_admin_hook = true;
			return $this->is_allowed_on_admin_hook;
		}

	}

}
