<?php
/**
 * Macros handler module
 *
 * Version: 1.0.0
 */
namespace Crocoblock;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( '\Crocoblock\Macros_Handler' ) ) {

	class Macros_Handler {

		private $macros_context      = null;
		private $fallback            = null;
		private $before              = null;
		private $after               = null;
		private $macros_list         = null;
		private $escaped_macros_list = null;
		private $namespace           = null;

		/**
		 * Setup namespace
		 * 
		 * @param string $namespace Namespace of the current instance, 
		 * which allows to call unique custom registration hooks
		 */
		public function __construct( $namespace = null ) {
			$this->namespace = $namespace;
		}

		/**
		 * Bulk register macros list
		 *
		 * Macros list format:
		 *
		 * [
		 * 		'macros_tag' => [
		 * 			'label' => 'Macros Name',
		 * 			'cb'    => 'callback_function_name',
		 * 			'args'  => [
		 * 				'arg_1' => [
		 * 					'label'   => __( 'Field', 'jet-engine' ),
		 * 					'type'    => 'select',
		 * 					'options' => $options, // This could be an array or callable function to retrieve options list only for UI
		 * 				]
		 * 				'arg_2' => [
		 * 					'label'   => __( 'Return', 'jet-engine' ),
		 * 					'type'    => 'text',
		 * 				],
		 * 			],
		 * 		],
		 * ]
		 *
		 * Triggers hook $this->namespace . '/register-macros' to register custom macros for you handler instance
		 * On hook you need to register macros by using Macros_Handler::register_macros();
		 * 
		 * @param  array  $macros_list array of macros list to register
		 * @return void
		 */
		public function register_macros_list( $macros_list = [] ) {

			if ( $this->namespace ) {
				/**
				 * Custom callback to register current instance macros based on namespace
				 */
				do_action( $this->namespace . '/register-macros', $this );
			}

			if ( null === $this->macros_list ) {
				$this->macros_list = $macros_list;
			} else {
				$this->macros_list = array_merge( $this->macros_list, $macros_list );
			}

			uasort( $this->macros_list, function( $a, $b ) {

				$name_a = ( is_array( $a ) && isset( $a['label'] ) ) ? $a['label'] : $this->to_string( $a );
				$name_b = ( is_array( $b ) && isset( $b['label'] ) ) ? $b['label'] : $this->to_string( $b );

				if ( $name_a == $name_b ) {
					return 0;
				}

				return ( $name_a < $name_b ) ? -1 : 1;

			} );

		}

		/**
		 * Register single macros into the current handler instance.
		 * This Method should be used to adding any new macros for your instance after bul registration is done
		 * 
		 * @param  object $macros_object Macros object
		 * @return [type]                [description]
		 */
		public function register_macros( $macros_object ) {

			$macros_data = array(
				'label' => $macros_object->macros_name(),
				'cb'    => [ $macros_object, '_macros_callback' ],
			);

			$args = $macros_object->get_macros_args();

			if ( ! empty( $args ) ) {
				$macros_data['args'] = $args;
			}

			$this->macros_list[ $macros_object->macros_tag() ] = $macros_data;

		}

		/**
		 * Returns plain $this->macros_list.
		 * 
		 * If you added any arguments where options is set as callback function - 
		 * this options will return not preapred to use in UI.
		 * 
		 * This method should be used anywhere where you need to get macros list without macros insertion UI
		 * 
		 * @return array
		 */
		public function get_raw_list() {
			return $this->macros_list;
		}

		/**
		 * Returns macros list where arguments options already prepared to use in UI.
		 *
		 * This method requires more resources, so should be used only in cases when you need macros list 
		 * with prepared arguments to use in some macros insertion UI
		 * 
		 * @return array
		 */
		public function get_escaped_list() {

			$macros_list = $this->get_raw_list();

			if ( empty( $macros_list ) ) {
				return [];
			}

			if ( null === $this->escaped_macros_list ) {

				foreach ( $macros_list as $key => $macros ) {
					if ( ! empty( $macros['args'] ) ) {
						foreach ( $macros['args'] as $arg => $data ) {

							if ( ! empty( $data['options'] ) && is_callable( $data['options'] ) ) {
								$data['options'] = call_user_func( $data['options'] );
								$macros['args'][ $arg ] = $data;
								$macros_list[ $key ] = $macros;
							}

							if ( ! empty( $data['groups'] ) && is_callable( $data['groups'] ) ) {
								$data['groups'] = call_user_func( $data['groups'] );
								$macros['args'][ $arg ] = $data;
								$macros_list[ $key ] = $macros;
							}

						}
					}
				}

				$this->escaped_macros_list = $macros_list;

			}

			return $this->escaped_macros_list;

		}

		/**
		 * Returns macros list prepared to use as JS options list
		 * 
		 * @return array
		 */
		public function get_macros_for_js() {

			$res = array();

			foreach ( $this->get_escaped_list() as $macros_id => $data ) {

				$macros_data = array(
					'id' => $macros_id,
				);

				if ( ! is_array( $data ) || empty( $data['label'] ) ) {
					$macros_data['name'] = $macros_id;
				} elseif ( ! empty( $data['label'] ) ) {
					$macros_data['name'] = $data['label'];
				}

				if ( is_array( $data ) && ! empty( $data['args'] ) ) {
					$macros_data['controls'] = $data['args'];
				}

				$res[] = $macros_data;

			}

			return $res;
		}

		/**
		 * Set context to get current macros data in.
		 * This method is usable in combination with jet_engine plugin, 
		 * which could process custom contexts by string name
		 *
		 * Without jet_engine, when using this method you need to pass $context as object to use it later
		 * 
		 * @param string|object $context [description]
		 */
		public function set_macros_context( $context = null ) {
			$this->macros_context = $context;
		}

		/**
		 * Returns macros context saved in the current instance.
		 * 
		 * @return string|object $context Context object or a context name for JetEngine
		 */
		public function get_macros_context() {
			return $this->macros_context;
		}

		/**
		 * Set fallback value for macros. Fallback will be returned if macros itself return nothing
		 * 
		 * @param string $fallback Value to return if macros return an empty value
		 */
		public function set_fallback( $fallback = null ) {
			$this->fallback = $fallback;
		}

		/**
		 * Return fallback value stored in the current instance
		 * 
		 * @return string $fallback Value to return if macros value is empty
		 */
		public function get_fallback() {
			return $this->fallback;
		}

		/**
		 * Set text to add before raw macros value when returning macros result
		 * 
		 * @param string $before Text to add
		 */
		public function set_before( $before = null ) {
			$this->before = $before;
		}

		/**
		 * Return text to add before macros value stored in the current handler instance
		 * 
		 * @return string
		 */
		public function get_before() {
			return $this->before;
		}

		/**
		 * Set text to add after raw macros value when returning macros result
		 * 
		 * @param string $after Text to add
		 */
		public function set_after( $after = null ) {
			$this->after = $after;
		}

		/**
		 * Return text to add after macros value stored in the current handler instance
		 * 
		 * @return string
		 */
		public function get_after() {
			return $this->after;
		}

		/**
		 * Is $str is array - returns 0, in other cases returns $str
		 *
		 * @param  mixed $str
		 * @return mixed
		 */
		public function to_string( $str ) {

			if ( is_array( $str ) ) {
				return 0;
			} else {
				return $str;
			}

		}

		/**
		 * Get macros list for options in raw 'macros' => 'label' format
		 *
		 * @return array
		 */
		public function get_macros_list_for_options() {

			$all = $this->get_raw_list();
			$result = array();

			if ( empty( $all ) ) {
				return $result;
			}

			foreach ( $all as $key => $data ) {
				if ( is_array( $data ) ) {
					$result[ $key ] = ! empty( $data['label'] ) ? $data['label'] : $key;
				} else {
					$result[ $key ] = $key;
				}
			}

			return $result;

		}

		/**
		 * Return verbosed macros list
		 *
		 * @return string
		 */
		public function verbose_macros_list() {

			$macros = $this->get_raw_list();
			$result = '';
			$sep    = '';

			foreach ( $macros as $key => $data ) {
				$result .= $sep . '%' . $key . '%';
				$sep     = ', ';
			}

			return $result;

		}

		/**
		 * Return current macros object based on the stored context
		 *
		 * @return object|null
		 */
		public function get_macros_object() {

			$context = $this->get_macros_context();

			if ( is_object( $context ) ) {
				return $context;
			}

			if ( function_exists( 'jet_engine' ) ) {
				if ( ! $context || 'default_object' === $context ) {
					$object = jet_engine()->listings->data->get_current_object();
				} else {
					$object = jet_engine()->listings->data->get_object_by_context( $this->macros_context );
				}
			} else {
				$object = get_queried_object();
			}

			return $object;

		}

		/**
		 * Call macros callback by macros name and args array
		 *
		 * @param  [type] $macros [description]
		 * @param  array  $args   [description]
		 * @return [type]         [description]
		 */
		public function call_macros_func( $macros, $args = array() ) {

			$all_macros = $this->get_raw_list();

			if ( empty( $all_macros[ $macros ] ) ) {
				return;
			}

			$macros_data   = $all_macros[ $macros ];
			$prepared_args = array( false );
			$custom_args   = array();

			if ( is_callable( $macros_data ) ) {
				return call_user_func_array( $macros_data, $prepared_args );
			}

			if ( ! empty( $macros_data['args'] ) ) {

				foreach ( array_keys( $macros_data['args'] ) as $arg ) {
					$custom_args[] = isset( $args[ $arg ] ) ? $args[ $arg ] : null;
				}

			}

			$prepared_args[] = implode( '|', $custom_args );

			return call_user_func_array( $macros_data['cb'], $prepared_args );

		}

		/**
		 * Check if given value is empty
		 * 
		 * @param  [type]  $source [description]
		 * @param  [type]  $key    [description]
		 * @return boolean         [description]
		 */
		public function is_empty( $source = null, $key = null ) {

			if ( class_exists( '\Jet_Engine_Tools' ) ) {
				return \Jet_Engine_Tools::is_empty( $source, $key );
			} elseif ( is_array( $source ) && $key ) {

				if ( ! isset( $source[ $key ] ) ) {
					return true;
				}

				$source = $source[ $key ];

			}

			return empty( $source ) && '0' !== $source;

		}

		/**
		 * Find and replace macros inside string
		 *
		 * @param  [type] $string      [description]
		 * @param  [type] $field_value [description]
		 * @return [type]              [description]
		 */
		public function do_macros( $string = '', $field_value = null ) {

			if ( empty( $string ) ) {
				return $string;
			}

			$macros = $this->get_raw_list();

			return preg_replace_callback(
				'/%([a-z_-]+)(\|(?:\[.*?\]|[a-zA-Z0-9_\-\,\.\+\:\/\s\(\)|\[\]\'\"=\{\}&]+))?%(\{.*?\})?/',
				function( $matches ) use ( $macros, $field_value ) {

					$found = $matches[1];

					if ( ! isset( $macros[ $found ] ) ) {
						return $matches[0];
					}

					$cb = $macros[ $found ];

					if ( is_array( $cb ) && isset( $cb['cb'] ) ) {
						$cb = ! empty( $cb['cb'] ) ? $cb['cb'] : false;

						if ( ! $cb ) {
							return $matches[0];
						}
					}

					if ( ! is_callable( $cb ) ) {
						return $matches[0];
					}

					$args   = isset( $matches[2] ) ? ltrim( $matches[2], '|' ) : false;
					$config = isset( $matches[3] ) ? json_decode( $matches[3], true ) : false;

					// Store the initial configs
					$initial_fallback = $this->get_fallback();
					$initial_context  = $this->get_macros_context();
					$initial_before   = $this->get_before();
					$initial_after    = $this->get_after();

					// Reset the configs except macros context.
					$this->set_fallback( null );
					$this->set_before( null );
					$this->set_after( null );

					// Set the config of current macro
					if ( $config ) {
						
						if ( ! empty( $config['context'] ) ) {
							$this->set_macros_context( $config['context'] );
						}

						if ( ! $this->is_empty( $config, 'fallback' ) ) {
							$this->set_fallback( $config['fallback'] );
						}

						if ( ! $this->is_empty( $config, 'before' ) ) {
							$this->set_before( $config['before'] );
						}

						if ( ! $this->is_empty( $config, 'after' ) ) {
							$this->set_after( $config['after'] );
						}

					}
					
					$result   = call_user_func( $cb, $field_value, $args );
					$fallback = $this->get_fallback();
					$before   = $this->get_before();
					$after    = $this->get_after();

					$is_empty_result = empty( $result );

					/*
					 * If the result of macro is `not-found` or array( 'not-found' )
					 * and the fallback value is not empty, then the fallback value is returned.
					 *
					 * See: https://github.com/Crocoblock/issues-tracker/issues/3243
					 */
					if ( ! $is_empty_result ) {
						$is_not_found_result = ( ( is_array( $result ) && in_array( 'not-found', $result ) ) || ( ! is_array( $result ) && 'not-found' === $result ) );

						if ( $is_not_found_result && ! $this->is_empty( $fallback ) ) {
							$is_empty_result = true;
						}
					}

					if ( ! $is_empty_result ) {

						if ( is_array( $result ) ) {
							$result = implode( ',', $result );
						}

						if ( $before ) {
							$result = $before . $result;
						}

						if ( $after ) {
							$result .= $after;
						}

					} elseif ( ! $this->is_empty( $fallback ) ) {
						$result = $fallback;
					}

					// Set the initial configs
					$this->set_fallback( $initial_fallback );
					$this->set_macros_context( $initial_context );
					$this->set_before( $initial_before );
					$this->set_after( $initial_after );

					return $result;

				}, $string
			);

		}

	}

}
