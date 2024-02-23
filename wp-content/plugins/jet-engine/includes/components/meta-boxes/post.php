<?php
/**
 * Meta boxes manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CPT_Meta' ) ) {

	/**
	 * Define Jet_Engine_CPT_Meta class
	 */
	class Jet_Engine_CPT_Meta {

		public static $index = 0;

		public $post_type;
		public $meta_box;

		public $custom_css = array();
		public $is_allowed_on_admin_hook = null;

		public static $wrappers_hooked = false;
		public static $edit_styles_rendered = false;

		public $box_id            = null;
		public $layout_now        = false;
		public $current_component = false;
		public $current_panel     = false;
		public $edit_link         = false;
		public $show_in_rest      = array();
		public $hide_field_names  = false;

		/**
		 * Trigger to define which fields format should be used - plain or blocks
		 * for plain - all options stored in value => label format
		 * for blocks - options stored as array with value and label keys
		 * array(
		 * 'value' => 'val',
		 * 'label' => 'label',
		 * )
		 * @var boolean
		 */
		public $blocks_flag = false;

		/**
		 * Constructor for the class
		 */
		function __construct( $post_type = null, $meta_box = null, $title = '', $context = 'normal', $priority = 'high', $args = array() ) {

			if ( ! $post_type || ! $meta_box ) {
				return;
			}

			$this->post_type = $post_type;
			$this->meta_box  = $meta_box;

			$args['allowed_post_type'] = array( $post_type );

			if ( ! empty( $args['hide_field_names'] ) ) {
				$this->hide_field_names = $args['hide_field_names'];
			}

			$fields = $this->prepare_meta_fields( $meta_box );

			if ( ! empty( $this->show_in_rest ) ) {
				
				if ( ! class_exists( 'Jet_Engine_Rest_Post_Meta' ) ) {
					require jet_engine()->meta_boxes->component_path( 'rest-api/fields/post-meta.php' );
				}
				
				foreach ( $this->show_in_rest as $field ) {
					new Jet_Engine_Rest_Post_Meta( $field, $post_type );
				}
			}

			if ( ! jet_engine()->meta_boxes->conditions->check_conditions( $this->get_box_id(), $args ) ) {
				return;
			}

			if ( ! $title ) {
				$title = esc_html__( 'Settings', 'jet-engine' );
			}

			new Cherry_X_Post_Meta( array(
				'id'            => $this->get_box_id(),
				'title'         => apply_filters( 'jet-engine/compatibility/translate-string', $title ),
				'page'          => array( $post_type ),
				'context'       => $context,
				'priority'      => $priority,
				'callback_args' => false,
				'builder_cb'    => array( $this, 'get_builder_for_meta' ),
				'fields'        => $fields,
			) );

			add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_custom_css' ), 0 );
			add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_inline_js' ), 20 );
			add_filter( 'cx_post_meta/custom_box', array( $this, 'maybe_hook_render_link' ), 10, 3 );

		}

		/**
		 * Enable blocks flag
		 */
		public function set_blocks_flag() {
			$this->blocks_flag = true;
		}

		/**
		 * Returns blocks flag
		 */
		public function is_blocks() {
			return $this->blocks_flag;
		}

		public function add_edit_link( $link ) {
			$this->edit_link = $link;
		}

		public function maybe_hook_render_link( $res, $post, $meta_box ) {

			if ( ! $this->edit_link ) {
				return $res;
			}

			if ( $meta_box['id'] === $this->get_box_id() ) {
				add_action( 'cx_post_meta/meta_box/before', array( $this, 'render_edit_link' ) );
			}

			return $res;

		}

		public function render_edit_link() {

			printf(
				'<a href="%s" class="jet-engine-edit-box-link" target="_blank"><span class="dashicons dashicons-admin-generic"></span></a>',
				$this->edit_link
			);

			remove_action( 'cx_post_meta/meta_box/before', array( $this, 'render_edit_link' ) );

		}

		public function get_box_id() {

			if ( null === $this->box_id ) {
				self::$index++;
				$this->box_id = 'jet-engine-cpt-' . self::$index;
			}

			return $this->box_id;

		}

		/**
		 * Returns builder for meta.
		 *
		 * @since  3.2.0 Added `$args` parameter.
		 * @access public
		 *
		 * @param array $args List of custom arguments.
		 *
		 * @return CX_Interface_Builder
		 */
		public function get_builder_for_meta( $args = [] ) {

			if ( ! self::$wrappers_hooked ) {
				$this->add_wrappers_hooks();

				self::$wrappers_hooked = true;
			}

			$builder_data = jet_engine()->framework->get_included_module_data( 'cherry-x-interface-builder.php' );

			$default_args = [
				'path' => $builder_data['path'],
				'url'  => $builder_data['url'],
			];

			return new CX_Interface_Builder( wp_parse_args( $args, $default_args ) );

		}

		/**
		 * Add wrappers hooks.
		 */
		public function add_wrappers_hooks() {
			add_action( 'cx_post_meta/meta_box/before', array( $this, 'open_meta_wrap' ) );
			add_action( 'cx_post_meta/meta_box/after', array( $this, 'close_meta_wrap' ) );
		}

		/**
		 * Open meta wrap
		 * @return void
		 */
		public function open_meta_wrap() {
			echo '<div class="jet-engine-meta-wrap">';
		}

		/**
		 * Open meta wrap
		 * @return void
		 */
		public function close_meta_wrap() {
			echo '</div>';
		}

		/**
		 * Get CSS classses for the field by given field data
		 *
		 * @param  array  $field Field data
		 * @return string
		 */
		public function get_field_css_class( $field = array() ) {

			$css_classes = array();

			if ( ! empty( $field['class'] ) ) {
				$css_classes[] = esc_attr( $field['class'] );
			}

			if ( ! empty( $field['html_css_class'] ) ) {
				$css_classes[] = esc_attr( $field['html_css_class'] );
			}

			return implode( ' ', $css_classes );

		}

		/**
		 * Prepare meta fields for registering
		 *
		 * @param  array  $meta_box Meta box fields list
		 * @return [type]           [description]
		 */
		public function prepare_meta_fields( $meta_box = array() ) {

			$result            = array();
			$date_assets_added = false;

			foreach ( $meta_box as $field ) {

				if ( isset( $field['title'] ) ) {
					$title = $field['title'];
				} elseif ( isset( $field['label'] ) ) {
					$title = $field['label'];
				} else {
					$title = '';
				}

				if ( ! empty( $field['object_type'] ) && 'field' !== $field['object_type'] ) {

					// process endpoint
					if ( 'endpoint' === $field['object_type'] ) {
						$this->current_component = false;
						$this->current_panel     = false;
						continue;
					}

					if ( $this->current_component && $this->layout_now !== $field['object_type'] ) {
						$this->current_component = false;
						$this->layout_now        = false;
					}

					// Start layout element
					if ( ! $this->current_component ) {

						$this->current_component = $field['name'] . '_' . $field['object_type'];
						$this->layout_now        = $field['object_type'];

						$type = false;

						switch ( $field['object_type'] ) {
							case 'tab':
								$layout = ! empty( $field['tab_layout'] ) ? $field['tab_layout'] : 'horizontal';
								$type   = 'component-tab-' . $layout;
								break;

							case 'accordion':
								$type = 'component-accordion';
								break;
						}

						if ( $type ) {
							$result[ $this->current_component ] = array(
								'type' => $type,
							);
						}

					}

					// Start new group inside layout element
					$this->current_panel = $field['name'];

					$result[ $this->current_panel ] = array(
						'type'   => 'settings',
						'parent' => $this->current_component,
						'title'  => apply_filters( 'jet-engine/compatibility/translate-string', $title ),
					);

					if ( ! empty( $field['conditional_logic'] ) && filter_var( $field['conditional_logic'], FILTER_VALIDATE_BOOLEAN ) ) {
						$conditions = $this->prepare_field_conditions( $field, $meta_box );

						if ( ! empty( $conditions ) ) {
							$result[ $this->current_panel ]['conditions'] = $conditions;
						}
					}

					continue;

				}

				$result[ $field['name'] ] = array(
					'type'        => $field['type'],
					'object_type' => ! empty( $field['object_type'] ) ? $field['object_type'] : 'field',
					'name'        => $field['name'],
					'element'     => 'control',
					'title'       => apply_filters( 'jet-engine/compatibility/translate-string', $title ),
					'class'       => $this->get_field_css_class( $field ),
				);

				if ( ! empty( $this->current_panel ) ) {
					$result[ $field['name'] ]['parent'] = $this->current_panel;
				}

				if ( ! empty( $field['width'] ) && '100%' !== $field['width'] ) {

					if ( 'html' === $field['type'] ) {
						$selector = '.cx-html.' . $field['name'];
					} else {
						$selector = '.cx-control[data-control-name="' . $field['name'] . '"]';
					}

					$this->custom_css[ $selector ] = $field['width'];
				}

				$description = '';

				if ( ! empty( $field['description'] ) ) {
					$description = apply_filters( 'jet-engine/compatibility/translate-string', $field['description'] );
				}

				if ( ! $this->hide_field_names ) {

					if ( ! empty( $description ) ) {
						$description = rtrim( $description, '.' ) . ' <br>';
					}

					$description .= sprintf(
						'<span>%1$s<span class="je-field-name">%2$s</span></span>',
						__( 'Name: ', 'jet-engine' ),
						$field['name']
					);
				}

				if ( ! empty( $description ) ) {
					$result[ $field['name'] ]['description'] = $description;
				}

				if ( ! empty( $field['is_required'] ) ) {
					$result[ $field['name'] ]['required'] = true;
				}

				if ( ! Jet_Engine_Tools::is_empty( $field, 'default_val' ) ) {
					$result[ $field['name'] ]['value'] = $field['default_val'];
				}

				if ( ! empty( $field['conditional_logic'] ) && filter_var( $field['conditional_logic'], FILTER_VALIDATE_BOOLEAN ) ) {
					$conditions = $this->prepare_field_conditions( $field, $meta_box );

					if ( ! empty( $conditions ) ) {
						$result[ $field['name'] ]['conditions'] = $conditions;
					}
				}

				switch ( $field['type'] ) {
					case 'select':

						if ( empty( $field['options'] ) ) {
							$field['options'] = array();
						}

						$prepared_options = $this->prepare_select_options( $field );
						$result[ $field['name'] ]['options'] = $prepared_options['options'];

						if ( ! empty( $prepared_options['options_callback'] ) 
							&& is_callable( $prepared_options['options_callback'] ) 
						) {
							$result[ $field['name'] ]['options_callback'] = $prepared_options['options_callback'];
						}

						if ( ! empty( $prepared_options['default'] ) ) {
							$result[ $field['name'] ]['value'] = $prepared_options['default'];
						}

						$multiple = ! empty( $field['is_multiple'] ) ? $field['is_multiple'] : false;
						$multiple = filter_var( $multiple, FILTER_VALIDATE_BOOLEAN );

						if ( $multiple ) {
							$result[ $field['name'] ]['multiple'] = true;
						}

						break;

					case 'checkbox':
					case 'checkbox-raw':

						if ( empty( $field['options'] ) ) {
							$field['options'] = array();
						}

						$prepared_options = $this->prepare_select_options( $field );

						if ( ! empty( $prepared_options['options_callback'] ) 
							&& is_callable( $prepared_options['options_callback'] ) 
						) {
							$result[ $field['name'] ]['options_callback'] = $prepared_options['options_callback'];
						}

						$result[ $field['name'] ]['options'] = $prepared_options['options'];
						$result[ $field['name'] ]['add_button_label'] = esc_html__( 'Add custom value', 'jet-engine' );

						if ( ! empty( $prepared_options['default'] ) ) {
							$result[ $field['name'] ]['value'] = $prepared_options['default'];
						}

						$field['is_array'] = ! empty( $field['is_array'] ) ? $field['is_array'] : false;
						$field['is_array'] = filter_var( $field['is_array'], FILTER_VALIDATE_BOOLEAN );

						$result[ $field['name'] ]['is_array'] = $field['is_array'];

						if ( ! empty( $field['allow_custom'] ) && filter_var( $field['allow_custom'], FILTER_VALIDATE_BOOLEAN ) ) {
							$result[ $field['name'] ]['allow_custom_value'] = true;
						}

						if ( ! Jet_Engine_Tools::is_empty( $field, 'check_radio_layout' ) ) {
							$result[ $field['name'] ]['layout'] = $field['check_radio_layout'];
						}

						break;

					case 'radio':

						$prepared_options = $this->prepare_radio_options( [], $field );

						if ( ! empty( $prepared_options['options_callback'] ) 
							&& is_callable( $prepared_options['options_callback'] ) 
						) {
							$result[ $field['name'] ]['options_callback'] = $prepared_options['options_callback'];
						}

						$result[ $field['name'] ]['options'] = $prepared_options['options'];

						if ( ! Jet_Engine_Tools::is_empty( $prepared_options['default'] ) ) {
							$result[ $field['name'] ]['value'] = $prepared_options['default'];
						}

						if ( ! empty( $field['allow_custom'] ) && filter_var( $field['allow_custom'], FILTER_VALIDATE_BOOLEAN ) ) {
							$result[ $field['name'] ]['allow_custom_value'] = true;
						}

						if ( ! Jet_Engine_Tools::is_empty( $field, 'check_radio_layout' ) ) {
							$result[ $field['name'] ]['layout'] = $field['check_radio_layout'];
						}

						break;

					case 'repeater':

						if ( empty( $field['repeater-fields'] ) ) {
							$field['repeater-fields'] = array();
						}

						$result[ $field['name'] ]['add_label'] = esc_html__( 'Add Item', 'jet-engine' );

						$result[ $field['name'] ]['fields'] = $this->prepare_repeater_fields(
							$field['repeater-fields']
						);

						if ( ! empty( $field['repeater_title_field'] ) ) {
							$result[ $field['name'] ]['title_field'] = $field['repeater_title_field'];
						}

						if ( ! empty( $field['repeater_collapsed'] ) ) {
							$result[ $field['name'] ]['collapsed'] = filter_var( $field['repeater_collapsed'], FILTER_VALIDATE_BOOLEAN );
						}

						break;

					case 'iconpicker':

						$result[ $field['name'] ]['icon_data'] = $this->get_icon_data();

						break;

					case 'wysiwyg':

						$result[ $field['name'] ]['sanitize_callback'] = 'jet_engine_sanitize_wysiwyg';

						break;

					case 'textarea':

						$result[ $field['name'] ]['sanitize_callback'] = 'jet_engine_sanitize_textarea';

						if ( ! empty( $field['max_length'] ) ) {
							$result[ $field['name'] ]['maxlength'] = absint( $field['max_length'] );
						}

						break;

					case 'text':

						$result[ $field['name'] ]['sanitize_callback'] = 'wp_kses_post';

						if ( ! empty( $field['max_length'] ) ) {
							$result[ $field['name'] ]['maxlength'] = absint( $field['max_length'] );
						}

						break;

					case 'posts':

						$multiple  = ! empty( $field['is_multiple'] ) ? $field['is_multiple'] : false;
						$multiple  = filter_var( $multiple, FILTER_VALIDATE_BOOLEAN );
						$post_type = isset( $field['search_post_type'] ) ? $field['search_post_type'] : 'any';

						$result[ $field['name'] ]['action']       = 'cx_search_posts';
						$result[ $field['name'] ]['post_type']    = $post_type;
						$result[ $field['name'] ]['inline_style'] = 'width: 100%;';
						$result[ $field['name'] ]['multiple']     = $multiple;

						break;

					case 'media':

						$result[ $field['name'] ]['multi_upload'] = false;
						$result[ $field['name'] ]['upload_button_text'] = esc_html__( 'Choose Media', 'jet-engine' );

						if ( ! empty( $field['value_format'] ) ) {
							$result[ $field['name'] ]['value_format'] = $field['value_format'];

							if ( 'both' === $field['value_format'] ) {
								$result[ $field['name'] ]['sanitize_callback'] = 'jet_engine_sanitize_media_json';
							}
						}

						break;

					case 'gallery':

						$result[ $field['name'] ]['type']         = 'media';
						$result[ $field['name'] ]['multi_upload'] = 'add';
						$result[ $field['name'] ]['upload_button_text'] = esc_html__( 'Choose Media', 'jet-engine' );

						if ( ! empty( $field['value_format'] ) ) {
							$result[ $field['name'] ]['value_format'] = $field['value_format'];

							if ( 'both' === $field['value_format'] ) {
								$result[ $field['name'] ]['sanitize_callback'] = 'jet_engine_sanitize_media_json';
							}
						}

						break;

					case 'date':
					case 'time':
					case 'datetime':
					case 'datetime-local':

						$key = $field['name'];

						$result[ $key ]['type']         = 'text';
						$result[ $key ]['input_type']   = $field['type'];
						$result[ $key ]['autocomplete'] = 'off';

						$field['is_timestamp'] = ! empty( $field['is_timestamp'] ) ? $field['is_timestamp'] : false;
						$field['is_timestamp'] = filter_var( $field['is_timestamp'], FILTER_VALIDATE_BOOLEAN );

						if ( $field['is_timestamp'] ) {
							$result[ $key ]['is_timestamp'] = true;
						}

						if ( ! empty( $result[ $key ]['value'] ) ) {
							$val = strtotime( $result[ $key ]['value'] );

							if ( $val ) {
								$result[ $key ]['value'] = date( 'Y-m-d', $val );
							}

						}

						if ( ! $date_assets_added ) {
							add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_date_assets' ) );
							$date_assets_added = true;
						}

						break;

					case 'number':

						$result[ $field['name'] ]['type'] = 'stepper';

						if ( ! Jet_Engine_Tools::is_empty( $field, 'min_value' ) ) {
							$result[ $field['name'] ]['min_value'] = $field['min_value'];
						}

						if ( ! Jet_Engine_Tools::is_empty( $field, 'max_value' ) ) {
							$result[ $field['name'] ]['max_value'] = $field['max_value'];
						}

						if ( ! Jet_Engine_Tools::is_empty( $field, 'step_value' ) ) {
							$result[ $field['name'] ]['step_value'] = $field['step_value'];
						}

						break;

					case 'switcher':

						$result[ $field['name'] ]['toggle'] = array(
							'true_toggle'  => esc_html__( 'On', 'jet-engine' ),
							'false_toggle' => esc_html__( 'Off', 'jet-engine' ),
						);

						// Set default value
						$result[ $field['name'] ]['value'] = false;

						break;

					case 'colorpicker':

						$alpha_mode = isset( $field['alpha_mode'] ) ? filter_var( $field['alpha_mode'], FILTER_VALIDATE_BOOLEAN ) : false;
						$result[ $field['name'] ]['alpha'] = $alpha_mode;

						break;

					case 'html':

						$result[ $field['name'] ]['element'] = 'html';
						$result[ $field['name'] ]['html']    = isset( $field['html'] ) ? $field['html'] : '';
						$result[ $field['name'] ]['class']  .= ' cx-component cx-html';

						break;

					case 'hidden':
						$result[ $field['name'] ]['view_wrapping'] = false;
						break;

					default:
						$result[ $field['name'] ] = apply_filters( 'jet-engine/meta-fields/' . $field['type'] . '/args', $result[ $field['name'] ], $field, $this );
				}

				$result[ $field['name'] ] = apply_filters( 'jet-engine/meta-fields/field/args', $result[ $field['name'] ], $field, $this );

				if ( ! empty( $field['show_in_rest'] ) ) {
					
					if ( ! $this->show_in_rest ) {
						$this->show_in_rest = array();
					}

					$this->show_in_rest[] = array_merge( array( 'name' => $field['name'] ), $result[ $field['name'] ] );
					
				}

				if ( $this->post_type && ! empty( $field['quick_editable'] ) ) {

					if ( ! class_exists( 'Jet_Engine_CPT_Quick_Edit' ) ) {
						require jet_engine()->meta_boxes->component_path( 'quick-edit.php' );
					}

					if ( 'checkbox' !== $field['type'] || ( 'checkbox' === $field['type'] && $field['is_array'] ) ) {
						new Jet_Engine_CPT_Quick_Edit( $this->post_type, array_merge(
							array( 'name' => $field['name'] ),
							$result[ $field['name'] ]
						) );
					}

				}

				if ( $this->post_type && ! empty( $field['revision_support'] ) ) {

					if ( ! class_exists( 'Jet_Engine_CPT_Revisions' ) ) {
						require jet_engine()->meta_boxes->component_path( 'revisions.php' );
					}

					new Jet_Engine_CPT_Revisions( $this->post_type, $result[ $field['name'] ] );
				}

			}

			return $result;

		}

		public function is_allowed_on_current_admin_hook( $hook ) {

			if ( null !== $this->is_allowed_on_admin_hook ) {
				return $this->is_allowed_on_admin_hook;
			}

			$allowed_hooks = array(
				'post-new.php',
				'post.php',
			);

			if ( ! in_array( $hook, $allowed_hooks ) ) {
				$this->is_allowed_on_admin_hook = false;
				return $this->is_allowed_on_admin_hook;
			}

			if ( get_post_type() !== $this->post_type ) {
				$this->is_allowed_on_admin_hook = false;
				return $this->is_allowed_on_admin_hook;
			}

			$this->is_allowed_on_admin_hook = true;
			return $this->is_allowed_on_admin_hook;

		}

		/**
		 * Maybe add custom css
		 *
		 * @return [type] [description]
		 */
		public function maybe_enqueue_custom_css( $hook ) {

			if ( ! $this->is_allowed_on_current_admin_hook( $hook ) ) {
				return;
			}

			wp_enqueue_style(
				'jet-engine-meta-boxes',
				jet_engine()->plugin_url( 'assets/css/admin/meta-boxes.css' ),
				array(),
				jet_engine()->get_version()
			);

			if ( ! empty( $this->custom_css ) ) {

				$custom_css  = '';
				$css_wrapper = $this->get_css_wrapper_selector();

				foreach ( $this->custom_css as $selector => $width ) {
					$custom_css .= $css_wrapper . $selector . ' { max-width: ' . $width . '; flex: 0 0 ' . $width . '; }';
				}

				wp_add_inline_style( 'jet-engine-meta-boxes', $custom_css );

			}

		}

		/**
		 * Maybe add inline js
		 */
		public function maybe_enqueue_inline_js( $hook ) {

			if ( ! $this->is_allowed_on_current_admin_hook( $hook ) ) {
				return;
			}

			static $printed = false;

			if ( $printed ) {
				return;
			}

			$inline_js = "
				(function( $ ) {
					if ( undefined !== navigator.clipboard && undefined !== navigator.clipboard.writeText ) {
	
						$( document ).on( 'click', '.je-field-name', function( event ) {
							var field = $( event.target ),
								fieldName = field.text();
	
							navigator.clipboard.writeText( fieldName ).then( function() {
								// clipboard successfully set
								
								field.addClass( 'je-copied' );
								
								setTimeout( function() {
									field.removeClass( 'je-copied' );
								}, 1500 );
							
							}, function() {
								// clipboard write failed
							} );
						} );
				
					}
				})( jQuery );
			";

			$printed = true;

			wp_add_inline_script( 'cx-interface-builder', $inline_js );
		}

		/**
		 * Get CSS wrapper selector.
		 *
		 * @return string
		 */
		public function get_css_wrapper_selector() {
			return '';
		}

		/**
		 * Enqueue date-related assets
		 *
		 * @param  [type] $hook [description]
		 * @return [type]       [description]
		 */
		public function enqueue_date_assets( $hook = false ) {

			if ( ! $this->is_allowed_on_current_admin_hook( $hook ) ) {
				return;
			}

			$this->date_assets();

		}

		public function date_assets() {
			
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-ui-slider' );

			wp_enqueue_script(
				'jquery-ui-timepicker-addon',
				jet_engine()->plugin_url( 'assets/lib/jquery-ui-timepicker/jquery-ui-timepicker-addon.min.js' ),
				array(),
				jet_engine()->get_version(),
				true
			);

			wp_enqueue_script(
				'jet-engine-meta-boxes',
				jet_engine()->plugin_url( 'assets/js/admin/meta-boxes.js' ),
				array( 'jquery' ),
				jet_engine()->get_version(),
				true
			);

			wp_enqueue_style(
				'jquery-ui-timepicker-addon',
				jet_engine()->plugin_url( 'assets/lib/jquery-ui-timepicker/jquery-ui-timepicker-addon.min.css' ),
				array(),
				jet_engine()->get_version()
			);

			global $wp_locale;

			wp_localize_script(
				'jet-engine-meta-boxes',
				'JetEngineMetaBoxesConfig',
				array(
					'isRTL'      => is_rtl(),
					'dateFormat' => Jet_Engine_Tools::convert_date_format_php_to_js( get_option( 'date_format' ) ),
					'timeFormat' => Jet_Engine_Tools::convert_date_format_php_to_js( get_option( 'time_format' ) ),
					'i18n'  => array(
						'timeOnlyTitle'   => esc_html__( 'Choose Time', 'jet-engine' ),
						'timeText'        => esc_html__( 'Time', 'jet-engine' ),
						'hourText'        => esc_html__( 'Hour', 'jet-engine' ),
						'minuteText'      => esc_html__( 'Minute', 'jet-engine' ),
						'currentText'     => esc_html__( 'Now', 'jet-engine' ),
						'closeText'       => esc_html__( 'Done', 'jet-engine' ),
						'monthNames'      => array_values( $wp_locale->month ),
						'monthNamesShort' => array_values( $wp_locale->month_abbrev ),
					),
				)
			);

		}

		/**
		 * Returns default icon data
		 *
		 * @return array
		 */
		public function get_icon_data() {

			ob_start();

			include jet_engine()->plugin_path( 'assets/js/admin/icons.json' );
			$json = ob_get_clean();

			$icons_list = array();
			$icons      = json_decode( $json, true );

			foreach ( $icons['icons'] as $icon ) {
				$icons_list[] = $icon['id'];
			}

			$icons_url = false;

			if ( defined( 'ELEMENTOR_ASSETS_URL' ) ) {
				$icons_url = ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/font-awesome.min.css';
			}

			return array(
				'icon_set'    => 'jetFontAwesome',
				'icon_css'    => $icons_url,
				'icon_base'   => 'fa',
				'icon_prefix' => 'fa-',
				'icons'       => $icons_list,
			);

		}

		public function prepare_repeater_fields( $repeater_fields = array() ) {

			if ( ! $repeater_fields ) {
				$repeater_fields = array();
			}

			$result            = array();
			$date_assets_added = false;

			foreach ( $repeater_fields as $field ) {

				$field_title = isset( $field['title'] ) ? $field['title'] : '';

				$label = apply_filters( 'jet-engine/compatibility/translate-string', $field_title );

				if ( ! $this->hide_field_names ) {
					$label .= sprintf(
						' <span>(%1$s<span class="je-field-name">%2$s</span>)</span>',
						__( 'name: ', 'jet-engine' ),
						$field['name']
					);
				}

				$result[ $field['name'] ] = array(
					'type'  => $field['type'],
					'id'    => $field['name'],
					'name'  => $field['name'],
					'class' => $this->get_field_css_class( $field ),
					'label' => $label,
				);

				if ( ! empty( $field['conditional_logic'] ) && filter_var( $field['conditional_logic'], FILTER_VALIDATE_BOOLEAN ) ) {
					$conditions = $this->prepare_field_conditions( $field, $repeater_fields );

					if ( ! empty( $conditions ) ) {
						$result[ $field['name'] ]['conditions'] = $conditions;
					}
				}

				switch ( $field['type'] ) {

					case 'iconpicker':

						$result[ $field['name'] ]['icon_data'] = $this->get_icon_data();

						break;

					case 'wysiwyg':

						$result[ $field['name'] ]['sanitize_callback'] = 'jet_engine_sanitize_wysiwyg';

						break;

					case 'textarea':

						$result[ $field['name'] ]['sanitize_callback'] = 'jet_engine_sanitize_textarea';

						break;

					case 'text':

						$result[ $field['name'] ]['sanitize_callback'] = 'wp_kses_post';

						break;

					case 'media':

						$result[ $field['name'] ]['multi_upload'] = false;
						$result[ $field['name'] ]['upload_button_text'] = esc_html__( 'Choose Media', 'jet-engine' );

						if ( ! empty( $field['value_format'] ) ) {
							$result[ $field['name'] ]['value_format'] = $field['value_format'];

							if ( 'both' === $field['value_format'] ) {
								$result[ $field['name'] ]['sanitize_callback'] = 'jet_engine_sanitize_media_json';
							}
						}

						break;

					case 'gallery':

						$result[ $field['name'] ]['type']         = 'media';
						$result[ $field['name'] ]['multi_upload'] = 'add';
						$result[ $field['name'] ]['upload_button_text'] = esc_html__( 'Choose Media', 'jet-engine' );

						if ( ! empty( $field['value_format'] ) ) {
							$result[ $field['name'] ]['value_format'] = $field['value_format'];

							if ( 'both' === $field['value_format'] ) {
								$result[ $field['name'] ]['sanitize_callback'] = 'jet_engine_sanitize_media_json';
							}
						}

						break;

					case 'posts':

						$multiple  = ! empty( $field['is_multiple'] ) ? $field['is_multiple'] : false;
						$multiple  = filter_var( $multiple, FILTER_VALIDATE_BOOLEAN );
						$post_type = isset( $field['search_post_type'] ) ? $field['search_post_type'] : 'any';

						$result[ $field['name'] ]['action']       = 'cx_search_posts';
						$result[ $field['name'] ]['post_type']    = $post_type;
						$result[ $field['name'] ]['inline_style'] = 'width: 100%;';
						$result[ $field['name'] ]['multiple']     = $multiple;

						break;

					case 'date':
					case 'time':
					case 'datetime':
					case 'datetime-local':

						$result[ $field['name'] ]['type']         = 'text';
						$result[ $field['name'] ]['input_type']   = $field['type'];
						$result[ $field['name'] ]['autocomplete'] = 'off';

						$field['is_timestamp'] = ! empty( $field['is_timestamp'] ) ? $field['is_timestamp'] : false;
						$field['is_timestamp'] = filter_var( $field['is_timestamp'], FILTER_VALIDATE_BOOLEAN );

						if ( $field['is_timestamp'] ) {
							$result[ $field['name'] ]['is_timestamp'] = true;
						}

						if ( ! $date_assets_added ) {
							add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_date_assets' ) );
							$date_assets_added = true;
						}

						break;

					case 'select':

						if ( empty( $field['options'] ) ) {
							$field['options'] = array();
						}

						$prepared_options = $this->prepare_select_options( $field );

						if ( ! empty( $prepared_options['options_callback'] ) 
							&& is_callable( $prepared_options['options_callback'] ) 
						) {
							$field['options_callback'] = $prepared_options['options_callback'];
						}

						$result[ $field['name'] ]['options'] = $prepared_options['options'];

						if ( ! empty( $prepared_options['default'] ) ) {
							$result[ $field['name'] ]['value'] = $prepared_options['default'];
						}

						if ( ! empty( $field['options_callback'] ) ) {
							$result[ $field['name'] ]['options_callback'] = $field['options_callback'];
						}

						$multiple = ! empty( $field['is_multiple'] ) ? $field['is_multiple'] : false;
						$multiple = filter_var( $multiple, FILTER_VALIDATE_BOOLEAN );

						if ( $multiple ) {
							$result[ $field['name'] ]['multiple'] = true;
						}

						break;

					case 'checkbox':
					case 'checkbox-raw':

						if ( empty( $field['options'] ) ) {
							$field['options'] = array();
						}

						$prepared_options = $this->prepare_select_options( $field );

						if ( ! empty( $prepared_options['options_callback'] ) 
							&& is_callable( $prepared_options['options_callback'] ) 
						) {
							$field['options_callback'] = $prepared_options['options_callback'];
						}

						$result[ $field['name'] ]['options'] = $prepared_options['options'];

						if ( ! empty( $prepared_options['default'] ) ) {
							$result[ $field['name'] ]['value'] = $prepared_options['default'];
						}

						if ( ! empty( $field['options_callback'] ) ) {
							$result[ $field['name'] ]['options_callback'] = $field['options_callback'];
						}

						$field['is_array'] = ! empty( $field['is_array'] ) ? $field['is_array'] : false;
						$field['is_array'] = filter_var( $field['is_array'], FILTER_VALIDATE_BOOLEAN );

						$result[ $field['name'] ]['is_array'] = $field['is_array'];

						if ( ! Jet_Engine_Tools::is_empty( $field, 'check_radio_layout' ) ) {
							$result[ $field['name'] ]['layout'] = $field['check_radio_layout'];
						}

						break;

					case 'radio':

						if ( empty( $field['options'] ) ) {
							$field['options'] = array();
						}

						$prepared_options = $this->prepare_radio_options( $field['options'], $field );
						
						if ( ! empty( $prepared_options['options_callback'] ) 
							&& is_callable( $prepared_options['options_callback'] ) 
						) {
							$field['options_callback'] = $prepared_options['options_callback'];
						}

						$result[ $field['name'] ]['options'] = $prepared_options['options'];

						if ( ! Jet_Engine_Tools::is_empty( $prepared_options['default'] ) ) {
							$result[ $field['name'] ]['value'] = $prepared_options['default'];
						}

						if ( ! empty( $field['options_callback'] ) ) {
							$result[ $field['name'] ]['options_callback'] = $field['options_callback'];
						}

						if ( ! Jet_Engine_Tools::is_empty( $field, 'check_radio_layout' ) ) {
							$result[ $field['name'] ]['layout'] = $field['check_radio_layout'];
						}

						break;

					case 'number':

						$result[ $field['name'] ]['type'] = 'stepper';

						if ( ! Jet_Engine_Tools::is_empty( $field, 'min_value' ) ) {
							$result[ $field['name'] ]['min_value'] = $field['min_value'];
						}

						if ( ! Jet_Engine_Tools::is_empty( $field, 'max_value' ) ) {
							$result[ $field['name'] ]['max_value'] = $field['max_value'];
						}

						if ( ! Jet_Engine_Tools::is_empty( $field, 'step_value' ) ) {
							$result[ $field['name'] ]['step_value'] = $field['step_value'];
						}

						break;

					case 'switcher':

						$result[ $field['name'] ]['toggle'] = array(
							'true_toggle'  => esc_html__( 'On', 'jet-engine' ),
							'false_toggle' => esc_html__( 'Off', 'jet-engine' ),
						);

						// Set default value
						$result[ $field['name'] ]['value'] = false;

						break;

					case 'colorpicker':

						$alpha_mode = isset( $field['alpha_mode'] ) ? filter_var( $field['alpha_mode'], FILTER_VALIDATE_BOOLEAN ) : false;
						$result[ $field['name'] ]['alpha'] = $alpha_mode;

						break;

					case 'html':

						$result[ $field['name'] ]['element'] = 'html';
						$result[ $field['name'] ]['html']    = isset( $field['html'] ) ? $field['html'] : '';

						break;

					case 'hidden':
						$result[ $field['name'] ]['view_wrapping'] = false;
						break;

					default:
						$result[ $field['name'] ] = apply_filters( 'jet-engine/meta-fields/repeater/' . $field['type'] . '/args', $result[ $field['name'] ], $field, $this );
				}

				$result[ $field['name'] ] = apply_filters( 'jet-engine/meta-fields/repeater/field/args', $result[ $field['name'] ], $field, $this );

			}

			return $result;

		}

		public function prepare_radio_options( $options = array(), $field = array() ) {

			$result = array(
				'options' => array(),
				'default' => false,
			);

			$options = $this->filter_options_list( $options, $field );

			if ( empty( $options ) ) {
				return $result;
			}

			if ( is_callable( $options ) ) {
				$result['options_callback'] = $options;
				return $result;
			}

			foreach ( $options as $index => $option ) {

				if ( ! isset( $option['key'] ) || ! is_array( $option ) ) {
					$key   = $index;
					$value = $option;
				} else {
					$key   = $option['key'];
					$value = $option['value'];
				}

				$label = apply_filters( 'jet-engine/compatibility/translate-string', $value );

				if ( $this->is_blocks() ) {

					$result['options'][] = array(
						'value' => $key,
						'label' => $label,
					);

				} else {
					/**
					 * Such format used due to radio control configuration which allows also pass
					 * image source to use as visual radio choice. Could be implemented in the future
					 */
					$result['options'][ $key ] = array(
						'label' => $label,
					);
				}

				if ( ! empty( $option['is_checked'] ) ) {
					$result['default'] = $key;
				}

			}

			return $result;

		}

		/**
		 * Prepare options for select
		 * @return [type] [description]
		 */
		public function prepare_select_options( $field = array() ) {

			$result = array(
				'options' => array(),
				'default' => false,
			);

			$options = $this->filter_options_list( [], $field );

			if ( empty( $options ) ) {
				return $result;
			}

			if ( is_callable( $options ) ) {
				$result['options_callback'] = $options;
				return $result;
			}

			$check_is_array = false;

			if ( 'checkbox' === $field['type'] ) {
				$multiple = true;
				$check_is_array = ! empty( $field['is_array'] ) ? filter_var( $field['is_array'], FILTER_VALIDATE_BOOLEAN ) : false;

			} else {
				$multiple = ! empty( $field['is_multiple'] ) ? $field['is_multiple'] : false;
				$multiple = filter_var( $multiple, FILTER_VALIDATE_BOOLEAN );
			}

			if ( $multiple ) {
				$result['default'] = array();
			}

			if ( ! empty( $field['type'] ) && 'select' === $field['type'] && ! empty( $field['placeholder'] ) ) {
				if ( $this->is_blocks() ) {
					$result['options'][] = array(
						'value' => '',
						'label' => $field['placeholder']
					);
				} else {
					$result['options'][''] = $field['placeholder'];
				}
			}

			foreach ( $options as $index => $option ) {

				if ( ! isset( $option['key'] ) || ! is_array( $option ) ) {
					$key   = $index;
					$value = $option;
				} else {
					$key   = $option['key'];
					$value = $option['value'];
				}

				/*
				Looks like this code not needed anymore, but keep commented just in case
				if ( $multiple && empty( $option['key'] ) ) {
					continue;
				}
				*/

				$key   = apply_filters( 'jet-engine/compatibility/translate-string', $key );
				$value = apply_filters( 'jet-engine/compatibility/translate-string', $value );

				if ( $this->is_blocks() ) {
					$result['options'][] = array(
						'value' => $key,
						'label' => $value
					);
				} else {
					$result['options'][ $key ] = $value;
				}

				if ( ! empty( $option['is_checked'] ) ) {
					if ( $multiple ) {
						if ( 'checkbox' === $field['type'] && ! $check_is_array ) {
							$result['default'][ $key ] = 'true';
						} else {
							$result['default'][] = $key;
						}
					} else {
						$result['default'] = $key;
					}
				}

			}

			return $result;

		}

		/**
		 * Prepare field conditions.
		 *
		 * @param  array $field
		 * @param  array $all_fields
		 * @return array
		 */
		public function prepare_field_conditions( $field = array(), $all_fields = array() ) {
			$result = array();

			if ( empty( $field['conditions'] ) ) {
				return $result;
			}

			$result['__relation__'] = ! empty( $field['conditional_relation'] ) ? $field['conditional_relation'] : 'AND';
			$result['__terms__']    = array();

			foreach ( $field['conditions'] as $condition ) {

				if ( empty( $condition['field'] ) || empty( $condition['operator'] ) ) {
					continue;
				}

				$condition_field = $this->get_field_args_by_name( $condition['field'], $all_fields );

				switch( $condition_field['type'] ) {
					case 'switcher':
						$value = ! empty( $condition['value'] ) ? $condition['value'] : false;
						$value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
						break;

					case 'checkbox':
					case 'checkbox-raw':
					case 'radio':
					case 'select':

						if ( in_array( $condition['operator'], array( 'in', 'not_in' ) ) ) {
							$value = ! empty( $condition['values'] ) ? $condition['values'] : array();
						} else {
							$value = ! empty( $condition['value'] ) ? $condition['value'] : '';
						}

						break;

					default:

						$value = ! empty( $condition['value'] ) ? $condition['value'] : '';

						if ( in_array( $condition['operator'], array( 'in', 'not_in' ) ) ) {
							$value = explode( ',', $value );
							$value = array_map( 'trim', $value );
						}
				}

				if ( in_array( $condition['operator'], array( 'empty', '!empty' ) ) ) {
					$value = '';
				}

				$is_checkbox     = ( 'checkbox' === $condition_field['type'] );
				$is_multi_select = ( 'select' === $condition_field['type'] )
									&& ! empty( $condition_field['is_multiple'] )
									&& filter_var( $condition_field['is_multiple'], FILTER_VALIDATE_BOOLEAN );

				switch( $condition['operator'] ) {
					case 'equal':
						$operator = '==';

						if ( $is_checkbox || $is_multi_select ) {
							$operator = 'contains';
						}

						break;

					case 'not_equal':
						$operator = '!=';

						if ( $is_checkbox || $is_multi_select ) {
							$operator = '!contains';
						}

						break;

					case 'in':
						$operator = 'in';

						if ( $is_checkbox || $is_multi_select ) {
							$operator = 'intersect';
						}

						break;

					case 'not_in':
						$operator = '!in';

						if ( $is_checkbox || $is_multi_select ) {
							$operator = '!intersect';
						}

						break;

					case 'greater_than':
						$operator = '>';

						break;

					case 'less_than':
						$operator = '<';

						break;

					case 'chars_greater_than':
						$operator = 'length_greater';

						break;

					case 'chars_less_than':
						$operator = 'length_less';

						break;

					default:
						$operator = $condition['operator'];
				}

				$result['__terms__'][] = array(
					'name'     => $condition['field'],
					'operator' => $operator,
					'value'    => $value
				);
			}

			return $result;
		}

		/**
		 * Return filtered options list
		 *
		 * @param  array  $options [description]
		 * @param  array  $field   [description]
		 * @return [type]          [description]
		 */
		public function filter_options_list( $options = array(), $field = array() ) {
			return apply_filters( 'jet-engine/meta-fields/field-options', $options, $field, $this );
		}

		/**
		 * Get field type by name.
		 *
		 * @param  string $name
		 * @return string|null
		 */
		public function get_field_type_by_name( $name ) {

			$list = wp_list_pluck( $this->meta_box, 'type', 'name' );

			return isset( $list[ $name ] ) ? $list[ $name ] : null;
		}

		/**
		 * Get field args by name.
		 *
		 * @param  string $name
		 * @param  array  $fields
		 * @return string|null
		 */
		public function get_field_args_by_name( $name = null, $fields = array() ) {

			$names = wp_list_pluck( $fields, 'name' );
			$list  = array_combine( $names, $fields );

			return isset( $list[ $name ] ) ? $list[ $name ] : null;
		}

	}

}
