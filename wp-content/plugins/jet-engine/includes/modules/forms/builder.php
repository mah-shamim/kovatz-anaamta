<?php
/**
 * Form builder class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Booking_Forms_Builder' ) ) {

	/**
	 * Define Jet_Engine_Booking_Forms_Builder class
	 */
	class Jet_Engine_Booking_Forms_Builder {

		public $form_id            = null;
		public $post               = null;
		public $fields             = array();
		public $fields_settings    = array();
		public $args               = array();
		public $settings           = array();
		public $attrs              = array();
		public $rows               = array();
		public $captcha            = false;
		public $preset             = false;
		public $is_hidden_row      = true;
		public $is_submit_row      = false;
		public $is_page_break_row  = false;
		public $rendered_rows      = 0;
		public $pages              = 0;
		public $page               = 0;
		public $has_prev           = false;
		public $start_new_page     = true;
		public $manager            = null;
		public $current_repeater   = false;
		public $current_repeater_i = false;

		/**
		 * Constructor for the class
		 */
		function __construct( $form_id = null, $fields = false, $args = array(), $captcha = false ) {

			if ( ! $form_id ) {
				return;
			}

			$this->form_id = $form_id;

			$this->setup_fields( $fields );

			$this->args = wp_parse_args( $args, array(
				'fields_layout' => 'column',
				'label_tag'     => 'div',
				'rows_divider'  => false,
				'required_mark' => '*',
				'submit_type'   => 'reload',
			) );

			if ( empty( $post ) ) {
				global $post;
			}

			$this->post    = $post;
			$this->captcha = $captcha;
			$this->preset  = new Jet_Engine_Booking_Forms_Preset( $this->form_id );

		}

		/**
		 * Set manager instance
		 *
		 * @param [type] $manager [description]
		 */
		public function set_manager( $manager ) {
			$this->manager = $manager;
		}

		/**
		 * Setup fields prop
		 */
		public function setup_fields( $fields = false ) {

			$raw_fields = '';

			if ( $fields ) {
				$raw_fields = $fields;
			} else {
				$raw_fields = get_post_meta( $this->form_id, '_form_data', true );
				$raw_fields = json_decode( wp_unslash( $raw_fields ), true );
			}

			if ( empty( $raw_fields ) ) {
				return;
			}

			// Ensure fields sorted by rows
			usort( $raw_fields, function( $a, $b ) {

				if ( $a['y'] == $b['y'] ) {
					return 0;
				}
				return ( $a['y'] < $b['y'] ) ? -1 : 1;

			} );

			$repeater_index = false;

			foreach ( $raw_fields as $index => $field ) {

				if ( $this->is_repeater_start( $field['settings'] ) ) {
					$repeater_index = $index;
					continue;
				}

				if ( $this->is_repeater_end( $field['settings'] ) ) {
					$repeater_index = false;
					unset( $raw_fields[ $index ] );
					continue;
				}

				if ( false !== $repeater_index ) {

					if ( empty( $raw_fields[ $repeater_index ]['settings']['repeater_fields'] ) ) {
						$raw_fields[ $repeater_index ]['settings']['repeater_fields'] = array();
					}

					$raw_fields[ $repeater_index ]['settings']['repeater_fields'][] = $field;
					unset( $raw_fields[ $index ] );
				}

			}

			$this->fields = $raw_fields;
			$this->fields_settings = wp_list_pluck( $raw_fields, 'settings' );

			$this->rows = $this->get_sorted_fields( $raw_fields );

		}

		/**
		 * Public function get sorted form fields
		 */
		public function get_sorted_fields( $raw_fields = array() ) {

			$sorted = array();
			$y      = false;

			foreach ( $raw_fields as $field ) {

				$is_page_break = ! empty( $field['settings']['is_page_break'] ) ? true : false;

				if ( $is_page_break ) {
					$this->pages++;
				}

				if ( false === $y ) {
					$y = $field['y'];
				}

				if ( $field['y'] === $y ) {

					if ( empty( $sorted[ $y ] ) ) {
						$sorted[ $y ] = array();
					}

					$sorted[ $y ][] = $field;

				} else {

					usort( $sorted[ $y ], function( $a, $b ) {

						if ( $a['x'] == $b['x'] ) {
							return 0;
						}
						return ( $a['x'] < $b['x'] ) ? -1 : 1;

					} );

					$y = $field['y'];

					$sorted[ $y ][] = $field;

				}

			}

			// Ensure last row is sorted
			usort( $sorted[ $y ], function( $a, $b ) {

				if ( $a['x'] == $b['x'] ) {
					return 0;
				}
				return ( $a['x'] < $b['x'] ) ? -1 : 1;

			} );

			return $sorted;
		}

		public function get_author_meta( $key ) {

			$post_id = get_the_ID();

			if ( ! $post_id ) {
				return null;
			}

			global $authordata;

			if ( $authordata ) {
				return get_the_author_meta( $key );
			}

			$post = get_post( $post_id );

			if ( ! $post ) {
				return null;
			}

			return get_the_author_meta( $key, $post->post_author );

		}

		/**
		 * Get hidden value
		 *
		 * @return string
		 */
		public function get_hidden_val( $args = array() ) {

			$from = isset( $args['hidden_value'] ) ? $args['hidden_value'] : '';

			switch ( $from ) {

				case 'post_id':
					if ( ! $this->post ) {
						return null;
					} else {
						return $this->post->ID;
					}

				case 'post_title':
					if ( ! $this->post ) {
						return null;
					} else {
						return get_the_title( $this->post->ID );
					}

				case 'post_url':
					if ( ! $this->post ) {
						return null;
					} else {
						return get_permalink( $this->post->ID );
					}

				case 'post_meta':

					if ( ! $this->post ) {
						return null;
					}

					$key = ! empty( $args['hidden_value_field'] ) ? $args['hidden_value_field'] : '';

					if ( ! $key ) {
						return null;
					} else {
						return get_post_meta( $this->post->ID, $key, true );
					}

				case 'query_var':

					$key = ! empty( $args['query_var_key'] ) ? $args['query_var_key'] : '';

					if ( ! $key ) {
						return null;
					} else {
						return isset( $_GET[ $key ] ) ? esc_attr( $_GET[ $key ] ) : null;
					}

				case 'user_id':
					if ( ! is_user_logged_in() ) {
						return null;
					} else {
						return get_current_user_id();
					}

				case 'user_email':
					if ( ! is_user_logged_in() ) {
						return null;
					} else {
						$user = wp_get_current_user();
						return $user->user_email;
					}

				case 'user_name':
					if ( ! is_user_logged_in() ) {
						return null;
					} else {
						$user = wp_get_current_user();
						return $user->display_name;
					}

				case 'user_meta':

					$key = ! empty( $args['hidden_value_field'] ) ? $args['hidden_value_field'] : '';

					if ( ! $key ) {
						return null;
					}

					if ( ! is_user_logged_in() ) {
						return null;
					} else {
						return get_user_meta( get_current_user_id(), $key, true );
					}

				case 'author_id':

					return $this->get_author_meta( 'ID' );

				case 'author_email':

					return $this->get_author_meta( 'user_email' );

				case 'author_name':

					return $this->get_author_meta( 'display_name' );

				case 'current_date':

					$format = ! empty( $args['date_format'] ) ? $args['date_format'] : get_option( 'date_format' );

					return date_i18n( $format );

				case 'manual_input':
					return ! empty( $args['default'] ) ? $args['default'] : 0;

				default:

					$value = ! empty( $args['default'] ) ? $args['default'] : '';
					return apply_filters( 'jet-engine/forms/hidden-value/' . $from, $value );

			}

		}

		/**
		 * Render custom form item template
		 *
		 * @param int|string  $object_id Object ID
		 * @param array       $args      Field arguments
		 * @param bool|string $checked
		 * @return string
		 */
		public function get_custom_template( $object_id = null, $args = array(), $checked = false ) {

			$listing_id = ! empty( $args['custom_item_template_id'] ) ? $args['custom_item_template_id'] : false;
			$listing_id = absint( $listing_id );

			if ( ! $listing_id ) {
				return __( 'Please select template', 'jet-engine' ) . '<br>';
			}

			global $wp_query;
			$default_object = $wp_query->queried_object;

			$options_from = ! empty( $args['field_options_from'] ) ? $args['field_options_from'] : 'posts';

			if ( 'terms' === $options_from ) {
				$object = get_term( $object_id );
			} else {
				$object = get_post( $object_id );
			}

			$classes = array(
				'jet-form__field-template',
				'jet-listing-dynamic-post-' . $object_id,
			);

			if ( $checked ) {
				$classes[] = 'jet-form__field-template--checked';
			}

			$wp_query->queried_object = $object;
			jet_engine()->listings->data->set_current_object( $object );

			jet_engine()->frontend->set_listing( $listing_id );

			$content = jet_engine()->frontend->get_listing_item( $object );

			$result = sprintf(
				'<div class="%3$s" data-value="%1$d">%2$s</div>',
				$object_id,
				apply_filters( 'jet-engine/forms/custom-template-content', $content, $object_id, $listing_id ),
				join( ' ', $classes )
			);

			$wp_query->queried_object = $default_object;
			jet_engine()->listings->data->set_current_object( $wp_query->queried_object );

			return $result;

		}

		/**
		 * Get required attribute value
		 *
		 * @param  [type] $args [description]
		 * @return [type]       [description]
		 */
		public function get_required_val( $args ) {

			if ( ! empty( $args['required'] ) && ( 'required' === $args['required'] || true === $args['required'] ) ) {
				return 'required';
			}

			return '';

		}

		/**
		 * Get calulation formula for calculated field
		 *
		 * @return [type] [description]
		 */
		public function get_calculated_data( $args ) {

			if ( empty( $args['calc_formula'] ) ) {
				return '';
			}

			$listen_fields = array();

			$formula = preg_replace_callback(
				'/%([a-zA-Z-_]+)::([a-zA-Z0-9-_]+)%/',
				function( $matches ) use ( &$listen_fields ) {

					switch ( strtolower( $matches[1] ) ) {
						case 'field':

							$listen_fields[] = $matches[2];
							return '%' . $matches[2] . '%';

						case 'meta':

							return get_post_meta( $this->post->ID, $matches[2], true );

						default:
							$macros_name = $matches[1];
							$field_key   = isset( $matches[2] ) ? $matches[2] : '' ;

							if( $field_key ){
								$listen_fields[] = $field_key;
							}

							return apply_filters( "jet-engine/calculated-data/$macros_name", $matches[0], $matches );
					}

				},
				$args['calc_formula']
			);

			return array(
				'formula'       => $formula,
				'listen_fields' => $listen_fields,
				'listen_to'     => $listen_fields,
			);

		}

		/**
		 * Add attribute
		 */
		public function add_attribute( $attr = null, $value = null ) {

			if ( '' === $value ) {
				return;
			}

			if ( in_array( $attr, array( 'value', 'placeholder' ) ) ) {
				$value = esc_attr( $value );
			}

			if ( ! isset( $this->attrs[ $attr ] ) ) {
				$this->attrs[ $attr ] = $value;
			} else {
				$this->attrs[ $attr ] .= ' ' . $value;
			}

		}

		/**
		 * Returns field name with repeater prefix if needed
		 */
		public function get_field_name( $name ) {

			if ( $this->current_repeater ) {
				$repeater_name = ! empty( $this->current_repeater['name'] ) ? $this->current_repeater['name'] : 'repeater';
				$index = ( false !== $this->current_repeater_i ) ? $this->current_repeater_i : '__i__';
				$name = sprintf( '%1$s[%2$s][%3$s]', $repeater_name, $index, $name );
			}

			return $name;

		}

		/**
		 * Returns field ID with repeater prefix if needed
		 */
		public function get_field_id( $name ) {

			if ( is_array( $name ) ) {
				$name = $name['name'];
			}

			if ( $this->current_repeater ) {
				$repeater_name = ! empty( $this->current_repeater['name'] ) ? $this->current_repeater['name'] : 'repeater';
				$index = ( false !== $this->current_repeater_i ) ? $this->current_repeater_i : '__i__';
				$name = sprintf( '%1$s_%2$s_%3$s', $repeater_name, $index, $name );
			}

			return $name;

		}

		/**
		 * Reset attributes array
		 */
		public function reset_attributes() {
			$this->attrs = array();
		}

		/**
		 * Render current attributes string
		 *
		 * @return [type] [description]
		 */
		public function render_attributes_string() {

			foreach ( $this->attrs as $attr => $value ) {
				printf( ' %1$s="%2$s"', $attr, $value );
			}

			$this->attrs = array();

		}

		/**
		 * Render current repeater row
		 */
		public function render_repeater_row( $children = array(), $index = false, $manage_items = 'manually', $calc_dataset = '' ) {

			if ( false !== $index ) {
				$this->current_repeater_i = $index;
			} else {
				$index = 0;
				$this->current_repeater_i = false;
			}

			echo '<div class="jet-form-repeater__row" data-repeater-row="1" data-index="' . $index . '"' . $calc_dataset . '>';

			echo '<div class="jet-form-repeater__row-fields">';

			foreach ( $children as $row ) {

				$this->is_hidden_row     = true;
				$this->is_submit_row     = false;
				$this->is_page_break_row = false;

				ob_start();
				$this->render_row( $row );
				$rendered_row = ob_get_clean();

				//$this->maybe_start_page();

				$this->start_form_row( $row );

				echo $rendered_row;

				$this->end_form_row( $row );

				//$this->maybe_end_page();

			}

			echo '</div>';

			if ( 'manually' === $manage_items ) {
				echo '<div class="jet-form-repeater__row-remove">';
				echo '<button type="button" class="jet-form-repeater__remove">&times;</button>';
				echo '</div>';
			}

			echo '</div>';

		}

		/**
		 * Render repeater fields group
		 */
		public function render_repeater_fields( $args = array() ) {

			ob_start();

			$children = ! empty( $args['repeater_fields'] ) ? $args['repeater_fields'] : array();

			$manage_items = ! empty( $args['manage_items_count'] ) ? $args['manage_items_count'] : 'manually';
			$items_field = ! empty( $args['manage_items_count_field'] ) ? $args['manage_items_count_field'] : false;

			if ( empty( $children ) ) {
				return;
			}

			/*
			Disabled because getting preset value moved before rendering fields in render_filed method
			$preset_value = $this->preset->get_field_value( $args['name'], $args );

			if ( $preset_value['rewrite'] ) {
				$args['default'] = $preset_value['value'];
			} else {
				$args['default'] = $this->maybe_adjust_value( $args );
			}*/

			$this->current_repeater = $args;

			$children = $this->get_sorted_fields( $children );
			$repeater_calc_type = ! empty( $args['repeater_calc_type'] ) ? $args['repeater_calc_type'] : 'default';
			$calc_data = false;

			if ( 'custom' === $repeater_calc_type ) {
				$calc_data = $this->get_calculated_data( $args );
			}

			$settings = htmlspecialchars( json_encode( array(
				'manageItems' => $manage_items,
				'itemsField'  => $items_field,
				'calcType'    => $repeater_calc_type,
			) ) );

			$calc_dataset = '';

			if ( $calc_data ) {
				foreach ( $calc_data as $data_key => $data_value ) {

					if ( is_array( $data_value ) ) {
						$data_value = json_encode( $data_value );
					}

					$calc_dataset .= sprintf( ' data-%1$s="%2$s"', $data_key, htmlspecialchars( $data_value ) );
				}
			}

			echo '<div class="jet-form-repeater" data-repeater="1" data-field-name="' . $args['name'] . '" name="' . $args['name'] . '" data-settings="' . $settings . '"' . $calc_dataset . '>';

			echo '<template class="jet-form-repeater__initial">';

			$this->render_repeater_row( $children, false, $manage_items, $calc_dataset );

			echo '</template>';

			echo '<div class="jet-form-repeater__items">';

			if ( ! empty( $args['default'] ) && is_array( $args['default'] ) ) {
				$i = 0;
				foreach ( $args['default'] as $item ) {
					$this->current_repeater['values'] = $item;
					$this->render_repeater_row( $children, $i, $manage_items, $calc_dataset );
					$i++;
				}
				$this->current_repeater['values'] = false;
			}

			echo '</div>';

			if ( 'manually' === $manage_items ) {
				echo '<div class="jet-form-repeater__actions">';
				$new_item_label = ! empty( $args['new_item_label'] ) ? $args['new_item_label'] : __( 'Add new', 'jet-engine' );
				printf( '<button type="button" class="jet-form-repeater__new">%1$s</button>', $new_item_label );
				echo '</div>';
			}

			echo '</div>';

			$this->current_repeater = false;

			return ob_get_clean();

		}

		/**
		 * Render form field by passed arguments.
		 *
		 * @param  array  $args [description]
		 * @return [type]       [description]
		 */
		public function render_field( $args = array() ) {

			if ( empty( $args['type'] ) ) {
				return;
			}

			$defaults = array(
				'default'     => '',
				'name'        => '',
				'placeholder' => '',
				'required'    => false,
			);

			$template = null;

			$name = ! empty( $args['name'] ) ? $args['name'] : '';
			$preset_value = $this->preset->get_field_value( $name, $args );

			if ( ! empty( $preset_value ) && $preset_value['rewrite'] ) {
				$args['default'] = $preset_value['value'];
			} else {
				$args['default'] = $this->maybe_adjust_value( $args );
			}

			// Repeater defaults prsed inside repeater field so here we need to get the data from repeater
			if ( $this->current_repeater && ! empty( $this->current_repeater['values'] ) && isset( $this->current_repeater['values'][ $args['name'] ] ) ) {
				$args['default'] = $this->current_repeater['values'][ $args['name'] ];
			}

			// Prepare defaults
			switch ( $args['type'] ) {

				case 'repeater_start':
					$template = $this->render_repeater_fields( $args );

					break;

				case 'hidden':

					$val = $this->get_hidden_val( $args );

					$defaults['default'] = $val;
					$args['default']     = $val;

					break;

				case 'number':
				case 'range':

					$defaults['min'] = '';
					$defaults['max'] = '';
					$defaults['step'] = 1;

					break;

				case 'text':

					$defaults['field_type'] = 'text';

					if ( ! empty( $args['enable_input_mask'] ) && ! empty( $args['input_mask'] ) ) {

						wp_enqueue_script( 'jet-engine-inputmask' );

						add_filter(
							'jet-engine/compatibility/popup-package/the_content',
							array( $this, 'ensure_mask_js' ), 10, 2
						);

					}

					break;

				case 'calculated':

					$defaults['formula']  = '';
					$args['required']     = false;

					break;

				case 'submit':

					$defaults['label']      = __( 'Submit', 'jet-engine' );
					$defaults['class_name'] = '';

					$this->is_submit_row = true;

					break;

				case 'page_break':

					$defaults['label']      = __( 'Submit', 'jet-engine' );
					$defaults['class_name'] = '';

					$this->is_page_break_row = true;

					break;

				case 'media':
					Jet_Engine_Forms_File_Upload::instance()->set_custom_messages( $this->form_id );
					Jet_Engine_Forms_File_Upload::instance()->enqueue_upload_script();

					add_filter(
						'jet-engine/compatibility/popup-package/the_content',
						array( Jet_Engine_Forms_File_Upload::instance(), 'ensure_media_js' ), 10, 2
					);

					break;

				case 'wysiwyg':

					wp_enqueue_editor();

					wp_localize_script(
						'jet-engine-frontend-forms',
						'JetEngineFormsEditor',
						array(
							'hasEditor' => true,
						)
					);

					add_filter(
						'jet-engine/compatibility/popup-package/the_content',
						array( $this, 'ensure_wysiwyg_js' ), 10, 2
					);

					// Ensure template not rewritten
					$template = false;

					break;

				case 'textarea':
				case 'select':
				case 'checkboxes':
				case 'radio':
				case 'date':
				case 'time':
				case 'datetime-local':
				case 'heading':
				case 'group_break':

					// Ensure template not rewritten
					$template = false;
					break;

				default:

					if ( 'hidden' !== $args['type'] ) {
						$this->is_hidden_row = false;
					}

					/**
					 * Render custom field
					 */
					do_action( 'jet-engine/forms/booking/render-field/' . $args['type'], $args, $this );

					/**
					 * Or just get custom template for field
					 */
					$template = apply_filters(
						'jet-engine/forms/booking/field-template/' . $args['type'],
						$template,
						$args,
						$this
					);

					if ( ! $template ) {
						return;
					} else {
						break;
					}

			}

			$sanitized_args = array();

			foreach ( $args as $key => $value ) {
				$sanitized_args[ $key ] = $value;
			}

			$args = wp_parse_args( $sanitized_args, $defaults );

			if ( ! $template ) {
				$template_name = str_replace( '_', '-', $args['type'] );
				$template      = jet_engine()->get_template( 'forms/fields/' . $template_name . '.php' );
			}

			// Ensure args
			switch ( $args['type'] ) {

				case 'select':
				case 'checkboxes':
				case 'radio':

					$args['field_options'] = $this->get_field_options( $args );

					break;
			}

			$label  = $this->get_field_label( $args );
			$desc   = $this->get_field_desc( $args );
			$layout = $this->args['fields_layout'];

			$args = apply_filters( "jet-engine/forms/render/{$args['type']}", $args, $this );

			if ( 'column' === $layout ) {
				include jet_engine()->get_template( 'forms/common/field-column.php' );
			} else {
				include jet_engine()->get_template( 'forms/common/field-row.php' );
			}

			if ( 'hidden' !== $args['type'] && ! $this->is_hidden_calc_field( $args ) ) {
				$this->is_hidden_row = false;
			}

		}

		/**
		 * Ensure mask JS is enqueued
		 *
		 * @param  [type] $content [description]
		 * @return [type]          [description]
		 */
		public function ensure_mask_js( $content = null, $popup_data = array() ) {

			ob_start();


			wp_register_script(
				'jet-engine-inputmask',
				jet_engine()->plugin_url( 'assets/lib/inputmask/jquery.inputmask.min.js' ),
				array(),
				jet_engine()->get_version(),
				true
			);

			wp_scripts()->print_scripts( 'jet-engine-inputmask' );

			return $content . ob_get_clean();
		}

		/**
		 * Ensure wysiwyg JS is enqueued
		 *
		 * @param  [type] $content [description]
		 * @return [type]          [description]
		 */
		public function ensure_wysiwyg_js( $content = null, $popup_data = array() ) {

			if ( ! empty( $popup_data['hasEditor'] ) ) {
				return $content;
			}

			ob_start();

			_WP_Editors::editor_js();
			_WP_Editors::force_uncompressed_tinymce();
			_WP_Editors::enqueue_scripts();

			wp_enqueue_editor();

			wp_scripts()->done[] = 'jquery-core';
			wp_scripts()->done[] = 'jquery-migrate';
			wp_scripts()->done[] = 'jquery';

			print_footer_scripts();

			return $content . ob_get_clean();

		}

		/**
		 * Try to get values from request if passed
		 * @param  [type] $args [description]
		 * @return [type]       [description]
		 */
		public function maybe_adjust_value( $args ) {

			if ( 'hidden' === $args['type'] ) {
				return isset( $args['default'] ) ? $args['default'] : '';
			}

			$value       = isset( $args['default'] ) ? $args['default'] : false;
			$request_val = ! empty( $_REQUEST['values'] ) ? $_REQUEST['values'] : array();

			if ( ! empty( $request_val[ $args['name'] ] ) ) {
				$value = $request_val[ $args['name'] ];
			}

			return $value;

		}

		/**
		 * Returns field label
		 *
		 * @return [type] [description]
		 */
		public function get_field_label( $args ) {

			$no_labels = $this->get_no_labels_types();

			ob_start();

			if ( ! empty( $args['label'] ) && ! in_array( $args['type'], $no_labels ) && ! $this->is_hidden_calc_field( $args ) ) {
				include jet_engine()->get_template( 'forms/common/field-label.php' );
			}

			return ob_get_clean();

		}

		/**
		 * Returns field description
		 *
		 * @return [type] [description]
		 */
		public function get_field_desc( $args ) {

			$no_labels = $this->get_no_labels_types();

			ob_start();
			if ( ! empty( $args['desc'] ) && ! in_array( $args['type'], $no_labels ) && ! $this->is_hidden_calc_field( $args ) ) {
				include jet_engine()->get_template( 'forms/common/field-description.php' );
			}
			return ob_get_clean();

		}

		/**
		 * Return field types without labels
		 *
		 * @return [type] [description]
		 */
		public function get_no_labels_types() {
			return array( 'submit', 'hidden', 'page_break', 'group_break' );
		}

		/**
		 * Is hidden calculated field
		 *
		 * @param $args
		 *
		 * @return bool
		 */
		public function is_hidden_calc_field( $args ) {
			$is_hidden = false;

			if ( 'calculated' !== $args['type'] ) {
				return $is_hidden;
			}

			$is_hidden = isset( $args['calc_hidden'] ) ? filter_var( $args['calc_hidden'], FILTER_VALIDATE_BOOLEAN ) : false;

			return $is_hidden;
		}

		/**
		 * Returns field options list
		 *
		 * @return array
		 */
		public function get_field_options( $args ) {

			$options_from = ! empty( $args['field_options_from'] ) ? $args['field_options_from'] : 'manual_input';
			$options      = array();
			$value_from   = ! empty( $args['value_from_key'] ) ? $args['value_from_key'] : false;
			$calc_from    = ! empty( $args['calculated_value_from_key'] ) ? $args['calculated_value_from_key'] : false;

			if ( 'manual_input' === $options_from ) {

				if ( ! empty( $args['field_options'] ) ) {

					foreach ( $args['field_options'] as $option ) {

						$item = array(
							'value' => $option['value'],
							'label' => $option['label'],
						);

						if ( isset( $option['calculate'] ) && '' !== $option['calculate'] ) {
							$item['calculate'] = $option['calculate'];
						}

						$options[] = $item;
					}

				}

			} elseif ( 'posts' === $options_from ) {

				$post_type = ! empty( $args['field_options_post_type'] ) ? $args['field_options_post_type'] : false;

				if ( ! $post_type ) {
					return $options;
				}

				$posts = get_posts( apply_filters( 'jet-engine/compatibility/get-posts/args', array(
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'post_type'      => $post_type,
				) ) );

				if ( empty( $posts ) ) {
					return $options;
				}

				$result = array();
				$post_props = array( 'post_title', 'post_content', 'post_name', 'post_excerpt' );

				foreach ( $posts as $post ) {

					$item = array(
						'value' => $post->ID,
						'label' => $post->post_title,
					);

					if ( ! empty( $value_from ) ) {
						if ( in_array( $value_from, $post_props ) ) {
							$item['value'] = $post->$value_from;
						} else {
							$item['value'] = get_post_meta( $post->ID, $value_from, true );
						}
					}

					if ( ! empty( $calc_from ) ) {
						if ( in_array( $calc_from, $post_props ) ) {
							$item['calculate'] = $post->$calc_from;
						} else {
							$item['calculate'] = get_post_meta( $post->ID, $calc_from, true );
						}
					}

					$result[] = $item;

				}

				$options = $result;

			} elseif ( 'terms' === $options_from ) {

				$tax = ! empty( $args['field_options_tax'] ) ? $args['field_options_tax'] : false;

				if ( ! $tax ) {
					return $options;
				}

				$terms = get_terms( array(
					'taxonomy'   => $tax,
					'hide_empty' => false,
				) );

				if ( empty( $terms ) || is_wp_error( $terms ) ) {
					return $options;
				}

				$result = array();

				foreach ( $terms as $term ) {

					$item = array(
						'value' => $term->term_id,
						'label' => $term->name,
					);

					if ( ! empty( $value_from ) ) {
						$item['value'] = get_term_meta( $term->term_id, $value_from, true );
					}

					if ( ! empty( $calc_from ) ) {
						$item['calculate'] = get_term_meta( $term->term_id, $calc_from, true );
					}

					$result[] = $item;

				}

				$options = $result;

			} elseif ( 'generate' === $options_from ) {

				$generator = ! empty( $args['generator_function'] ) ? $args['generator_function'] : false;
				$field     = ! empty( $args['generator_field'] ) ? $args['generator_field'] : false;

				if ( ! $generator ) {
					return $options;
				}

				if ( ! $this->manager ) {
					return $options;
				}

				$generators         = $this->manager->get_options_generators();
				$generator_instance = isset( $generators[ $generator ] ) ? $generators[ $generator ] : false;

				if ( ! $generator_instance ) {
					return $options;
				}

				$generated = $generator_instance->generate( $field );
				$result = array();

				if ( ! empty( $value_from || ! empty( $calc_from ) ) ) {
					foreach ( $generated as $key => $data ) {

						if ( is_array( $data ) ) {
							$item = $data;
						} else {
							$item = array(
								'value' => $key,
								'label' => $data,
							);
						}

						$post_id = $item['value'];

						if ( ! empty( $value_from ) ) {
							$item['value'] = get_post_meta( $post_id, $value_from, true );
						}

						if ( ! empty( $calc_from ) ) {
							$item['calculate'] = get_post_meta( $post_id, $calc_from, true );
						}

						$result[] = $item;

					}

					$options = $result;

				} else {
					$options = $generated;
				}

			} else {

				$key = ! empty( $args['field_options_key'] ) ? $args['field_options_key'] : '';

				if ( $key ) {
					$options = get_post_meta( $this->post->ID, $key, true );
					$options = $this->maybe_parse_repeater_options( $options );
				}

			}

			return apply_filters( 'jet-engine/forms/field-options', $options, $args, $this );

		}

		/**
		 * Returns form action url
		 *
		 * @return [type] [description]
		 */
		public function get_form_action_url() {

			if ( ! wp_doing_ajax() ) {
				$action = add_query_arg( array(
					'jet_engine_action' => 'book',
					'nocache'           => time(),
				) );
			} else {
				$action = add_query_arg(
					array(
						'jet_engine_action' => 'book',
						'_jet_form_is_ajax' => true,
					),
					home_url( '/' )
				);
			}

			return apply_filters( 'jet-engine/forms/booking/form-action-url', $action, $this );

		}

		/**
		 * Returns form refer url
		 *
		 * @return [type] [description]
		 */
		public function get_form_refer_url() {

			global $wp;
			$refer = home_url( $wp->request );

			if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
				$refer = trailingslashit( $refer ) . '?' . $_SERVER['QUERY_STRING'];
			}

			return apply_filters( 'jet-engine/forms/booking/form-refer-url', $refer, $this );

		}

		/**
		 * Open form wrapper
		 *
		 * @return [type] [description]
		 */
		public function start_form() {

			do_action( 'jet-engine/forms/booking/before-start-form', $this );

			$this->add_attribute( 'class', 'jet-form' );
			$this->add_attribute( 'class', 'layout-' . $this->args['fields_layout'] );
			$this->add_attribute( 'class', 'submit-type-' . $this->args['submit_type'] );
			$this->add_attribute( 'action', $this->get_form_action_url() );
			$this->add_attribute( 'method', 'POST' );
			$this->add_attribute( 'data-form-id', $this->form_id );

			$this->rendered_rows = 0;
			$this->page = 0;
			$this->has_prev = false;

			include jet_engine()->get_template( 'forms/common/start-form.php' );

			do_action( 'jet-engine/forms/booking/after-start-form', $this );

		}

		/**
		 * Open form wrapper
		 *
		 * @return [type] [description]
		 */
		public function start_form_row() {

			if ( ! $this->is_hidden_row ) {
				$this->rendered_rows++;
			}

			if ( true === $this->args['rows_divider'] && 1 < $this->rendered_rows && ! $this->is_hidden_row ) {
				echo '<div class="jet-form__divider"></div>';
			}

			do_action( 'jet-engine/forms/booking/before-start-form-row', $this );

			$this->add_attribute( 'class', 'jet-form-row' );

			if ( $this->is_hidden_row ) {
				$this->add_attribute( 'class', 'jet-form-row--hidden' );
			}

			if ( $this->is_submit_row ) {
				$this->add_attribute( 'class', 'jet-form-row--submit' );
			}

			if ( $this->is_page_break_row ) {
				$this->add_attribute( 'class', 'jet-form-row--page-break' );
			}

			if ( 1 === $this->rendered_rows ) {
				$this->add_attribute( 'class', 'jet-form-row--first-visible' );
			}

			include jet_engine()->get_template( 'forms/common/start-form-row.php' );

			do_action( 'jet-engine/forms/booking/after-start-form-row', $this );

		}

		/**
		 * Close form wrapper
		 *
		 * @return [type] [description]
		 */
		public function end_form() {

			do_action( 'jet-engine/forms/booking/before-end-form', $this );

			include jet_engine()->get_template( 'forms/common/end-form.php' );

			do_action( 'jet-engine/forms/booking/after-end-form', $this );

		}

		/**
		 * Close form wrapper
		 *
		 * @return [type] [description]
		 */
		public function end_form_row() {

			do_action( 'jet-engine/forms/booking/before-end-form-row', $this );

			include jet_engine()->get_template( 'forms/common/end-form-row.php' );

			do_action( 'jet-engine/forms/booking/after-end-form-row', $this );

		}

		/**
		 * Render passed form row
		 *
		 * @param  [type] $row [description]
		 * @return [type]      [description]
		 */
		public function render_row( $row ) {

			$filled = 0;

			foreach ( $row as $field ) {

				$push  = '';
				$col   = 'jet-form-col jet-form-col-' . $field['w'];

				if ( 0 < $filled ) {
					if ( $filled < $field['x'] ) {
						$push   = $field['x'] - $filled;
						$filled = $filled + $push;
						$push   = 'jet-form-push-' . $push;
					}
				} else {
					if ( 0 < $field['x'] ) {
						$push   = 'jet-form-push-' . $field['x'];
						$filled = $filled + $field['x'];
					}
				}

				if ( $this->is_field_visible( $field['settings'] ) ) {

					$type       = ! empty( $field['settings']['type'] ) ? $field['settings']['type'] : 'text';
					$class_name = ! empty( $field['settings']['class_name'] ) ? $field['settings']['class_name'] : '';

					$classes = array(
						$col,
						$push,
						'field-type-' . $type,
						$class_name,
						'jet-form-field-container'
					);

					$conditional = ! empty( $field['conditionals'] ) ? $field['conditionals'] : false;

					if ( $conditional && is_array( $conditional ) ) {
						foreach ( $conditional as $index => $condition ) {

							if ( empty( $condition['operator'] ) ) {
								$condition['operator'] = 'equal';
							}

							if ( empty( $condition['type'] ) ) {
								$condition['type'] = 'show';
							}

							if ( false !== strpos( $condition['value'], 'jet_preset' ) && ! empty( $condition['field'] ) ) {

								$_field_key  = array_search( $condition['field'], wp_list_pluck( $this->fields_settings, 'name' ) );
								$_field_args = $this->fields_settings[ $_field_key ];

								$_field_args['array_allowed'] = 'one_of' === $condition['operator'];

								$preset_value = $this->preset->get_condition_value( $condition, $_field_args );

								if ( $preset_value['rewrite'] ) {
									$condition['value'] = $preset_value['value'];
								}
							}

							if ( in_array( $condition['operator'], array( 'between', 'one_of' ) ) && ! is_array( $condition['value'] ) ) {

								if ( ! empty( $condition['value'] ) ) {
									$value = explode( ',', $condition['value'] );
									$value = array_map( 'trim', $value );
								} else {
									$value = array();
								}

								$condition['value'] = $value;

							}

							if ( $this->current_repeater && false !== strpos( $condition['field'], '::' ) ) {
								$parse_field_name = explode( '::', $condition['field']  );
								$repeater_name    = ! empty( $parse_field_name[0] ) ? $parse_field_name[0] : 'repeater';
								$cond_field_name  = ! empty( $parse_field_name[1] ) ? $parse_field_name[1] : '';
								$repeater_index   = ( false !== $this->current_repeater_i ) ? $this->current_repeater_i : '__i__';

								$condition['field'] = sprintf( '%1$s\\[%2$s\\]\\[%3$s\\]', $repeater_name, $repeater_index, $cond_field_name );
							}

							$conditional[ $index ] = $condition;
						}
					}

					$conditional = htmlspecialchars( json_encode( $conditional ) );

					echo '<div class="' . implode( ' ', $classes ) . '" data-field="' . $field['settings']['name'] . '" data-conditional="' . $conditional . '">';

					$this->render_field( $field['settings'] );

					echo '</div>';

				}

				$filled = $filled + $field['w'];

			}

		}

		/**
		 * Check if is repeater start now
		 *
		 * @param  array   $args [description]
		 * @return boolean       [description]
		 */
		public function is_repeater_start( $args = array() ) {
			return ( ! empty( $args['type'] ) && 'repeater_start' === $args['type'] );
		}

		/**
		 * Check if is repeater end now
		 *
		 * @param  array   $args [description]
		 * @return boolean       [description]
		 */
		public function is_repeater_end( $args = array() ) {
			return ( ! empty( $args['type'] ) && 'repeater_end' === $args['type'] );
		}

		/**
		 * Returns true if field is visible
		 *
		 * @param  array   $field [description]
		 * @return boolean        [description]
		 */
		public function is_field_visible( $field = array() ) {

			// For backward compatibility and hidden fields
			if ( empty( $field['visibility'] ) ) {
				return true;
			}

			// If is visible for all - show field
			if ( 'all' === $field['visibility'] ) {
				return true;
			}

			// If is visible for logged in users and user is logged in - show field
			if ( 'logged_id' === $field['visibility'] && is_user_logged_in() ) {
				return true;
			}

			// If is visible for not logged in users and user is not logged in - show field
			if ( 'not_logged_in' === $field['visibility'] && ! is_user_logged_in() ) {
				return true;
			}

			return false;

		}

		/**
		 * Render from HTML
		 * @return [type] [description]
		 */
		public function render_form( $force_update = false ) {

			$pre_render = apply_filters( 'jet-engine/forms/pre-render/' . $this->form_id, false );

			if ( $pre_render ) {
				return;
			}

			if ( ! $this->preset->sanitize_source() ) {
				echo 'You are not permitted to submit this form!';
				return;
			}

			if ( ! $force_update ) {

				$cached = $this->get_form_cache();

				if ( $cached ) {
					echo $this->add_nonce_field( $cached );
					return;
				}

			}

			ob_start();

			$this->start_form();

			$this->render_field( array(
				'type'    => 'hidden',
				'default' => $this->form_id,
				'name'    => '_jet_engine_booking_form_id',
			) );

			$this->render_field( array(
				'type'    => 'hidden',
				'default' => $this->get_form_refer_url(),
				'name'    => '_jet_engine_refer',
			) );

			$token = jet_engine()->forms->handler->get_session_token( $this->form_id );

			if ( $token ) {
				$this->render_field( array(
					'type'    => 'hidden',
					'default' => $token,
					'name'    => '_jet_engine_form_token',
				) );
			}

			foreach ( $this->rows as $row ) {

				$this->is_hidden_row     = true;
				$this->is_submit_row     = false;
				$this->is_page_break_row = false;

				ob_start();
				$this->render_row( $row );
				$rendered_row = ob_get_clean();

				$this->maybe_start_page();

				$this->start_form_row( $row );

				echo $rendered_row;

				$this->end_form_row( $row );

				$this->maybe_end_page();

			}

			if ( $this->captcha ) {
				$this->captcha->render( $this->form_id );
			}

			$this->maybe_end_page( true );

			$this->end_form();

			$form = ob_get_clean();

			$this->set_form_cache( $form );

			echo $this->add_nonce_field( $form );

		}

		public function add_nonce_field( $form_html ) {

			ob_start();

			$this->render_field( array(
				'type'    => 'hidden',
				'default' => wp_create_nonce( '_jet_engine_booking_form' ),
				'name'    => '_jet_engine_nonce',
			) );

			$nonce_field = ob_get_clean();

			return str_replace( '</form>', $nonce_field . '</form>', $form_html );
		}

		/**
		 * Maybe start new page
		 *
		 * @return [type] [description]
		 */
		public function maybe_start_page() {

			if ( 0 >= $this->pages ) {
				return;
			}

			if ( false === $this->start_new_page ) {
				return;
			}

			$this->start_new_page = false;

			$this->page++;

			do_action( 'jet-engine/forms/before-page-start', $this );

			$hidden_class = '';

			if ( 1 < $this->page ) {
				$hidden_class = 'jet-form-page--hidden';
			}

			include jet_engine()->get_template( 'forms/common/start-page.php' );

			do_action( 'jet-engine/forms/after-page-start', $this );

		}

		/**
		 * Maybe start new page
		 *
		 * @return [type] [description]
		 */
		public function maybe_end_page( $is_last = false ) {

			if ( 0 >= $this->pages ) {
				return;
			}

			if ( ! $is_last && ! $this->is_page_break_row ) {
				return;
			}

			$this->start_new_page = true;
			$this->has_prev       = true;

			do_action( 'jet-engine/forms/before-page-end', $this );

			include jet_engine()->get_template( 'forms/common/end-page.php' );

			do_action( 'jet-engine/forms/after-page-end', $this );

		}

		/**
		 * Get rendered form
		 * @return [type] [description]
		 */
		public function get_form_cache() {
			return apply_filters(
				'jet-engine/forms/booking/form-cache',
				get_post_meta( $this->form_id, '_rendered_form', true ),
				$this->form_id
			);
		}

		/**
		 * Store rendered form
		 * @param [type] $content [description]
		 */
		public function set_form_cache( $content = null ) {
			update_post_meta( $this->form_id, '_rendered_form', $content );
		}

		/**
		 * Prepare repeater options fields
		 *
		 * @param  [type] $options [description]
		 * @return [type]          [description]
		 */
		public function maybe_parse_repeater_options( $options ) {

			$result = array();

			if ( empty( $options ) ) {
				return $result;
			}

			if ( ! is_array( $options ) ) {
				$options = array( $options );
			}

			if ( in_array( 'true', $options ) || in_array( 'false', $options ) ) {
				return $this->get_checked_options( $options );
			}

			$option_values = array_values( $options );

			if ( ! is_array( $option_values[0] ) ) {

				foreach ( $options as $key => $value ) {
					$result[] = array(
						'value' => is_string( $key ) ? $key : $value,
						'label' => $value,
					);
				}

				return $result;
			}

			foreach ( $options as $option ) {

				$values = array_values( $option );

				if ( ! isset( $values[0] ) ) {
					continue;
				}

				$result[] = array(
					'value' => $values[0],
					'label' => isset( $values[1] ) ? $values[1] : $values[0],
				);

			}

			return $result;

		}

		/**
		 * Returns checked options
		 */
		public function get_checked_options( $options ) {

			$result = array();

			foreach ( $options as $label => $checked ) {
				$checked = filter_var( $checked, FILTER_VALIDATE_BOOLEAN );

				if ( $checked ) {
					$result[] = array(
						'value' => $label,
						'label' => $label,
					);
				}

			}

			return $result;

		}

	}

}
