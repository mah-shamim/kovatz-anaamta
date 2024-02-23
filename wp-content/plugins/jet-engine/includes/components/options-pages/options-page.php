<?php
/**
 * Meta oxes mamager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CPT_Meta' ) ) {
	require jet_engine()->plugin_path( 'includes/components/meta-boxes/post.php' );
}

if ( ! class_exists( 'Jet_Engine_Options_Page_Factory' ) ) {

	/**
	 * Define Jet_Engine_Options_Page_Factory class
	 */
	class Jet_Engine_Options_Page_Factory extends Jet_Engine_CPT_Meta {

		/**
		 * Current page data
		 *
		 * @var null
		 */
		public $page = null;

		/**
		 * Current page slug
		 *
		 * @var null
		 */
		public $slug = null;

		/**
		 * Prepared fields array
		 *
		 * @var null
		 */
		public $prepared_fields = null;

		/**
		 * Holder for is page or not is page now prop
		 *
		 * @var null
		 */
		public $is_page_now = null;

		/**
		 * Inerface builder instance
		 *
		 * @var null
		 */
		public $builder = null;

		/**
		 * Saved options holder
		 *
		 * @var null
		 */
		public $options = null;

		/**
		 * Save trigger
		 *
		 * @var string
		 */
		public $save_action = 'jet-engine-op-save-settings';

		public $layout_now        = false;
		public $current_component = false;
		public $current_panel     = false;
		public $custom_css        = array();
		public $storage_type      = 'default';

		/**
		 * Constructor for the class
		 */
		public function __construct( $page ) {

			$this->page = $page;
			$this->slug = $page['slug'];

			if ( ! empty( $page['hide_field_names'] ) ) {
				$this->hide_field_names = $page['hide_field_names'];
			}

			if ( ! empty( $page['storage_type'] ) ) {
				$this->storage_type = $page['storage_type'];
			}

			$page['fields'] = apply_filters( 'jet-engine/options-pages/raw-fields', $page['fields'], $this );

			$this->meta_box       = $page['fields'];
			$this->page['fields'] = $this->prepare_meta_fields( $page['fields'] );

			if ( ! empty( $this->show_in_rest ) ) {
				
				if ( ! class_exists( 'Jet_Engine_Rest_Settings' ) ) {
					require jet_engine()->options_pages->component_path( 'rest-api/fields/site-settings.php' );
				}
				
				foreach ( $this->show_in_rest as $field ) {
					new Jet_Engine_Rest_Settings( $field, $this->slug, $this );
				}
			}

			if ( empty( $this->page['position'] ) && 0 !== $this->page['position'] ) {
				$this->page['position'] = null;
			}

			add_action( 'admin_menu', array( $this, 'register_menu_page' ), 99 );

			if ( $this->is_page_now() ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'init_builder' ), 0 );
				add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_inline_js' ), 20 );
				add_action( 'admin_init', array( $this, 'save' ), 40 );
				add_action( 'admin_notices', array( $this, 'saved_notice' ) );
			}

		}

		/**
		 * Check if current options page is processed now
		 *
		 * @return boolean [description]
		 */
		public function is_page_now() {

			if ( null !== $this->is_page_now ) {
				return $this->is_page_now;
			}

			if ( isset( $_GET['page'] ) && $this->slug === $_GET['page'] ) {
				$this->is_page_now = true;
			} else {
				$this->is_page_now = false;
			}

			return $this->is_page_now;

		}

		/**
		 * Register avalable menu pages
		 *
		 * @return [type] [description]
		 */
		public function register_menu_page() {

			if ( ! empty( $this->page['parent'] ) ) {
				add_submenu_page(
					$this->page['parent'],
					$this->page['labels']['name'],
					$this->page['labels']['menu_name'],
					$this->page['capability'],
					$this->page['slug'],
					array( $this, 'render_page' )
				);
			} else {
				add_menu_page(
					$this->page['labels']['name'],
					$this->page['labels']['menu_name'],
					$this->page['capability'],
					$this->page['slug'],
					array( $this, 'render_page' ),
					$this->page['icon'],
					$this->page['position']
				);

			}
		}

		/**
		 * Process options saving
		 *
		 * @return [type] [description]
		 */
		public function save() {

			if ( ! isset( $_REQUEST['action'] ) || $this->save_action !== $_REQUEST['action'] ) {
				return;
			}

			$capability = $this->page['capability'];

			if ( ! current_user_can( $capability ) ) {
				return;
			}

			$this->update_options( $_REQUEST );

			/**
			 * Global hook fires after saving options of the page
			 */
			do_action( 'jet-engine/options-pages/after-save', $this );

			/**
			 * Page-specific hook fires after saving options of the page
			 */
			do_action( 'jet-engine/options-pages/after-save/' . $this->page['slug'], $this );

			$redirect = add_query_arg(
				array(
					'page'         => $this->slug,
					'dialog-saved' => true,
				),
				esc_url( admin_url( 'admin.php' ) )
			);

			wp_redirect( $redirect );
			die();

		}

		/**
		 * Update options.
		 *
		 * @param array $data     Data array.
		 * @param bool  $rewrite  Rewrite or not fields not isset in data array.
		 * @param bool  $sanitize Apply or not the sanitize callbacks to raw values.
		 */
		public function update_options( $data = array(), $rewrite = true, $sanitize = true ) {

			if ( empty( $data ) ) {
				return;
			}

			if ( 'default' === $this->storage_type ) {
				$current = get_option( $this->slug, array() );
			}

			$fields = $this->get_prepared_fields();

			if ( ! empty( $fields ) ) {
				foreach ( $fields as $key => $field ) {

					if ( isset( $data[ $key ] ) ) {

						$value = $data[ $key ];

						if ( $sanitize ) {
							$value = $this->maybe_apply_sanitize_callback( $value, $field );
						}

					} else {
						$value = null;
					}

					if ( ! isset( $data[ $key ] ) && ! $rewrite ) {
						continue;
					}

					if ( 'default' === $this->storage_type ) {
						$current[ $key ] = $value;
					} elseif ( 'separate' === $this->storage_type ) {
						update_option( $this->get_separate_option_name( $key ), $value );
					}
				}
			}

			if ( 'default' === $this->storage_type && isset( $current ) ) {
				update_option( $this->slug, $current );
			}

			/**
			 * Fires after the values of a specific options page has been successfully updated.
			 * The dynamic portion of the hook name, `$slug`, refers to the slug of options page.
			 *
			 * @since 3.2.7
			 * @param Jet_Engine_Options_Page_Factory $page The options page instance.
			 */
			do_action( 'jet-engine/options-pages/updated/' . $this->slug, $this );

			/**
			 * Fires after the values of an options page has been successfully updated.
			 *
			 * @since 3.2.7
			 * @param Jet_Engine_Options_Page_Factory $page The options page instance.
			 */
			do_action( 'jet-engine/options-pages/updated', $this );
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
		 * Maybe apply sanitize callback
		 *
		 * @param mixed $value
		 * @param array $field
		 *
		 * @return mixed
		 */
		public function maybe_apply_sanitize_callback( $value, $field ) {

			if ( is_array( $value ) && 'repeater' === $field['type'] && ! empty( $field['fields'] ) ) {
				foreach ( $value as $item_id => $item ) {
					foreach ( $item as $sub_item_id => $sub_item_value ) {
						$value[ $item_id ][ $sub_item_id ] = $this->maybe_apply_sanitize_callback( $sub_item_value, $field['fields'][ $sub_item_id ] );
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
				return apply_filters( 'jet-engine/options-pages/strtotime', strtotime( $value ), $value );
			}

			if ( ! empty( $field['sanitize_callback'] ) && is_callable( $field['sanitize_callback'] ) ) {
				$value = call_user_func( $field['sanitize_callback'], $value, $field['name'], $field );
			}

			return $value;
		}

		/**
		 * Show saved notice
		 *
		 * @return bool
		 */
		public function saved_notice() {

			if ( ! isset( $_GET['dialog-saved'] ) ) {
				return false;
			}

			$message = __( 'Saved', 'jet-engine' );

			printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', $message );

			return true;

		}

		/**
		 * Initialize page builder
		 *
		 * @return [type] [description]
		 */
		public function init_builder() {

			$builder_data = jet_engine()->framework->get_included_module_data( 'cherry-x-interface-builder.php' );

			$this->builder = new \CX_Interface_Builder(
				array(
					'path' => $builder_data['path'],
					'url'  => $builder_data['url'],
				)
			);

			$slug = $this->page['slug'];

			$this->builder->register_section(
				array(
					$slug => array(
						'type'   => 'section',
						'scroll' => false,
						'title'  => apply_filters( 'jet-engine/compatibility/translate-string', $this->page['labels']['name'] ),
					),
				)
			);

			$this->builder->register_form(
				array(
					$slug . '_form' => array(
						'type'   => 'form',
						'parent' => $slug,
						'action' => add_query_arg(
							array(
								'page'   => $slug,
								'action' => $this->save_action,
							),
							esc_url( admin_url( 'admin.php' ) )
						),
					),
				)
			);

			$this->builder->register_settings(
				array(
					'settings_top' => array(
						'type'   => 'settings',
						'parent' => $slug . '_form',
					),
					'settings_bottom' => array(
						'type'   => 'settings',
						'parent' => $slug . '_form',
					),
				)
			);

			if ( ! empty( $this->page['fields'] ) ) {

				$this->builder->register_control( $this->get_prepared_fields() );

			}

			$label = __( 'Save', 'jet-engine' );

			$this->builder->register_html(
				array(
					'save_button' => array(
						'type'   => 'html',
						'parent' => 'settings_bottom',
						'class'  => 'cx-component dialog-save',
						'html'   => '<button type="submit" class="cx-button cx-button-primary-style">' . $label . '</button>',
					),
				)
			);

			$this->print_custom_css();

		}

		/**
		 * Print custom CSS
		 *
		 * @return void
		 */
		public function print_custom_css() {
			$this->maybe_enqueue_custom_css( null );
		}

		/**
		 * Get CSS wrapper selector.
		 *
		 * @return string
		 */
		public function get_css_wrapper_selector() {
			return '#settings_top.cx-settings__content ';
		}

		/**
		 * Get saved options
		 *
		 * @param  [type]  $option [description]
		 * @param  boolean $default [description]
		 * @return [type]           [description]
		 */
		public function get( $option = '', $default = false, $field = array() ) {

			if ( 'separate' === $this->storage_type ) {

				if ( isset( $this->options[ $option ] ) ) {
					return $this->options[ $option ];
				}

				$result = get_option( $this->get_separate_option_name( $option ), $default );
				$this->options[ $option ] = wp_unslash( $result );

				return $this->options[ $option ];
			}

			if ( null === $this->options ) {
				$this->options = get_option( $this->slug, array() );
			}

			return isset( $this->options[ $option ] ) ? wp_unslash( $this->options[ $option ] ) : $default;

		}

		/**
		 * Get separate option name.
		 *
		 * @param  $option
		 * @return string
		 */
		public function get_separate_option_name( $option ) {

			if ( ! empty( $this->page['option_prefix'] ) ) {
				return $this->slug . '_' . $option;
			}

			return $option;
		}

		/**
		 * Render options page
		 *
		 * @return [type] [description]
		 */
		public function render_page() {
			echo '<div class="jet-engine-options-page-wrap">';
			$this->builder->render();
			echo '</div>';
		}

		/**
		 * Prepare stored fields array to be registered in interface builder
		 *
		 * @return array
		 */
		public function get_prepared_fields() {

			if ( null !== $this->prepared_fields ) {
				return $this->prepared_fields;
			}

			$result = $this->page['fields'];

			// Prepare fields array to use in Options Page.
			foreach ( $result as $field_name => $field_args ) {

				if ( empty( $field_args['parent'] ) ) {
					$result[ $field_name ]['parent'] = 'settings_top';
				}

				if ( ! empty( $field_args['element'] ) && 'control' === $field_args['element'] ) {
					$result[ $field_name ]['id']   = $field_name;
					$result[ $field_name ]['name'] = $field_name;

					$result[ $field_name ]['value'] = $this->get(
						$field_name,
						isset( $result[ $field_name ]['value'] ) ? $result[ $field_name ]['value'] : false,
						$result[ $field_name ]
					);

					$result[ $field_name ]['value'] = $this->prepare_field_value(
						$result[ $field_name ],
						$result[ $field_name ]['value']
					);

					if ( 'separate' === $this->storage_type && ! empty( $this->page['option_prefix'] )
						 && ! empty( $result[ $field_name ]['description'] )
					) {
						$result[ $field_name ]['description'] = str_replace(
							$field_name,
							$this->get_separate_option_name( $field_name ),
							$result[ $field_name ]['description']
						);
					}
				}
			}

			$this->prepared_fields = $result;

			return $result;

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
				case 'checkbox-raw':

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

		public function get_date( $format, $time ) {
			return apply_filters( 'jet-engine/options-pages/date', date( $format, $time ), $time, $format );
		}

		public function is_allowed_on_current_admin_hook( $hook ) {
			return $this->is_page_now();
		}

		/**
		 * Get options list for use as select options
		 *
		 * @return [type] [description]
		 */
		public function get_options_for_select() {

			$fields     = array();
			$skip_types = array( 'component-tab-vertical', 'component-tab-horizontal', 'component-accordion', 'settings' );

			if ( ! empty( $this->meta_box ) ) {
				foreach ( $this->meta_box as $field ) {

					if ( ! empty( $field['type'] ) && in_array( $field['type'], $skip_types ) ) {
						continue;
					}

					$key = $this->slug . '::' . $field['name'];

					$fields[ $key ] = array(
						'title' => $field['title'],
						'type'  => ( 'field' === $field['object_type'] ) ? $field['type'] : $field['object_type'],
					);
				}
			}

			return array(
				'label'   => $this->page['labels']['name'],
				'options' => $fields,
			);

		}

	}

}
