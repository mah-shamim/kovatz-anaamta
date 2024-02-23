<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Render_Dynamic_Field' ) ) {

	class Jet_Engine_Render_Dynamic_Field extends Jet_Engine_Render_Base {

		public $show_field     = true;
		public $show_fallback  = false;
		public $more_string    = '...';
		public $excerpt_length = '...';
		public $prevent_icon   = false;
		public $need_sanitize  = true;

		public function get_name() {
			return 'jet-listing-dynamic-field';
		}

		public function default_settings() {
			return array(
				'dynamic_field_source'             => 'object',
				'dynamic_field_post_object'        => 'post_title',
				'dynamic_field_relation_type'      => 'grandparents',
				'dynamic_field_post_meta_custom'   => '',
				'dynamic_field_relation_post_type' => '',
				'dynamic_excerpt_length'           => '',
				'field_tag'                        => 'div',
				'hide_if_empty'                    => false,
				'dynamic_field_filter'             => false,
				'filter_callbacks'                 => array(),
				'date_format'                      => 'F j, Y',
				'num_dec_point'                    => '.',
				'num_thousands_sep'                => ',',
				'num_decimals'                     => 2,
				'related_list_is_single'           => false,
				'related_list_is_linked'           => true,
				'related_list_tag'                 => 'ul',
				'multiselect_delimiter'            => ',',
				'dynamic_field_custom'             => false,
				'dynamic_field_format'             => '%s',
				'object_context'                   => 'default_object',
			);
		}

		/**
		 * Custom excerpt more link
		 *
		 * @return [type] [description]
		 */
		public function excerpt_more() {
			return $this->more_string;
		}

		/**
		 * Custom excerpt more link
		 *
		 * @return [type] [description]
		 */
		public function excerpt_length() {
			return absint( $this->excerpt_length );
		}

		public function get_field_content( $settings ) {

			$source         = $this->get( 'dynamic_field_source' );
			$object_context = $this->get( 'object_context' );
			$result         = apply_filters(
				'jet-engine/listings/dynamic-field/custom-value',
				null,
				$settings,
				$this
			);

			if ( ! $result ) {

				switch ( $source ) {
					case 'object':

						$field = $this->get( 'dynamic_field_post_object' );
						$auto  = $this->get( 'dynamic_field_wp_excerpt', '' );

						if ( ! empty( $settings['dynamic_field_post_meta_custom'] ) ) {
							$field = $settings['dynamic_field_post_meta_custom'];
						}

						if ( 'post_excerpt' === $field && ! empty( $auto ) ) {

							$this->more_string = ! empty( $settings['dynamic_excerpt_more'] ) ? $settings['dynamic_excerpt_more'] : '';
							$this->excerpt_length = ! empty( $settings['dynamic_excerpt_length'] ) ? $settings['dynamic_excerpt_length'] : '';

							add_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );

							if ( $this->excerpt_length ) {
								add_filter( 'excerpt_length', array( $this, 'excerpt_length' ), 9999 );
							}

							$post_object = jet_engine()->listings->data->get_object_by_context( $object_context );

							if ( ! $post_object ) {
								$post_object = jet_engine()->listings->data->get_current_object();
							}

							$result = get_the_excerpt( $post_object );

							// If a post has excerpt, the filters `excerpt_more` and `excerpt_length` are not applied.
							if ( has_excerpt( $post_object ) ) {
								$result = wp_trim_words( $result, $this->excerpt_length, $this->more_string );
							}

							remove_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );

							if ( $this->excerpt_length ) {
								remove_filter( 'excerpt_length', array( $this, 'excerpt_length' ), 9999 );
							}

						} else {

							$result = jet_engine()->listings->data->get_prop(
								$field,
								jet_engine()->listings->data->get_object_by_context( $object_context )
							);
						}

						if ( 'post_content' === $field ) {

							$this->need_sanitize = false;
							$object              = jet_engine()->listings->data->get_object_by_context( $object_context );
							$post_id             = jet_engine()->listings->data->get_current_object_id( $object );

							if ( ! jet_engine()->listings->did_posts->did_post( $post_id ) ) {

								jet_engine()->listings->did_posts->do_post( $post_id );

								if ( jet_engine()->has_elementor() && Elementor\Plugin::$instance->documents->get( $post_id )
									&& Elementor\Plugin::$instance->documents->get( $post_id )->is_built_with_elementor()
								) {
									$editor       = Elementor\Plugin::$instance->editor;
									$is_edit_mode = $editor->is_edit_mode();

									$editor->set_edit_mode( false );

									$result = Elementor\Plugin::$instance->frontend->get_builder_content( $post_id, $is_edit_mode );

									$editor->set_edit_mode( $is_edit_mode );
								} else {
									$result = apply_filters( 'the_content', $result );
								}
							} else {
								$result = null;
							}
						}

						break;

					case 'meta':

						$field = ! empty( $settings['dynamic_field_post_meta_custom'] ) ? $settings['dynamic_field_post_meta_custom'] : false;

						if ( ! $field && isset( $settings['dynamic_field_post_meta'] ) ) {
							$field = ! empty( $settings['dynamic_field_post_meta'] ) ? $settings['dynamic_field_post_meta'] : false;
						}

						if ( $field ) {
							$result = jet_engine()->listings->data->get_meta_by_context( $field, $object_context );
						}

						break;

					case 'options_page':

						$option = ! empty( $settings['dynamic_field_option'] ) ? $settings['dynamic_field_option'] : false;

						if ( $option ) {
							$result = jet_engine()->listings->data->get_option( $option );
						}

						break;

					case 'relations_hierarchy':

						$rel_type = ! empty( $settings['dynamic_field_relation_type'] ) ? $settings['dynamic_field_relation_type'] : 'grandparents';
						$post_type = ! empty( $settings['dynamic_field_relation_post_type'] ) ? $settings['dynamic_field_relation_post_type'] : '';

						if ( ! $post_type ) {
							return __( 'Please select post type', 'jet-engine' );
						}

						if ( 'grandparents' === $rel_type ) {
							$result = jet_engine()->relations->legacy->hierarchy->get_grandparent( $post_type );
						} else {
							$result = jet_engine()->relations->legacy->hierarchy->get_grandchild( $post_type );
						}

						break;

					case 'repeater_field':

						$field = ! empty( $settings['dynamic_field_post_meta_custom'] ) ? $settings['dynamic_field_post_meta_custom'] : false;

						if ( $field ) {
							$field  = trim( $field );
							$result = jet_engine()->listings->data->get_meta_by_context( $field, $object_context );
						}

						break;

					case 'query_var':

						$variable = ! empty( $settings['dynamic_field_var_name'] ) ? trim( $settings['dynamic_field_var_name'] ) : false;

						if ( $variable ) {

							global $wp_query;

							if ( isset( $wp_query->query_vars[ $variable ] ) ) {
								$result = $wp_query->query_vars[ $variable ];
							} elseif ( isset( $_REQUEST[ $variable ] ) ) {
								if ( ! is_array( $_REQUEST[ $variable ] ) ) {
									$result = esc_attr( $_REQUEST[ $variable ] );
								} else {
									$result = $_REQUEST[ $variable ];
								}
							}
						}

						break;

					default:

						$result = apply_filters( 'jet-engine/listings/dynamic-field/field-value', null, $settings );
						break;
				}

			}

			if ( is_array( $result ) ) {

				$result = array_filter( $result, function ( $val ) {
					return ! Jet_Engine_Tools::is_empty( $val );
				} );

				// For Checkboxes array like `array( 'key1' => 'false', 'key2' => 'false', ... )`
				if ( in_array( 'false', $result ) ) {

					$all_values_empty = true;

					foreach ( $result as $key => $val ) {
						if ( filter_var( $val, FILTER_VALIDATE_BOOLEAN ) ) {
							$all_values_empty = false;
							break;
						}
					}

					if ( $all_values_empty ) {
						$result = false;
					}
				}

			}

			if ( 'false' === $result ) {
				$result = false;
			}

			$hide_if_empty = isset( $settings['hide_if_empty'] ) ? $settings['hide_if_empty'] : false;
			$hide_if_empty = filter_var( $hide_if_empty, FILTER_VALIDATE_BOOLEAN );

			if ( $hide_if_empty && Jet_Engine_Tools::is_empty( $result ) ) {
				$this->show_field = false;
				return null;
			} elseif ( Jet_Engine_Tools::is_empty( $result ) && ! Jet_Engine_Tools::is_empty( $settings, 'field_fallback' ) ) {
				$this->show_fallback = true;
				return wp_kses_post( $settings['field_fallback'] );
			}

			$this->need_sanitize = apply_filters(
				'jet-engine/listings/dynamic-field/sanitize-output', 
				$this->need_sanitize, $this 
			);

			if ( $this->need_sanitize && is_string( $result ) ) {
				$result = wp_kses_post( $result );
			}

			return $result;

		}

		/**
		 * Render post/term field content
		 *
		 * @param  array $settings Widget settings.
		 * @return void
		 */
		public function render_field_content( $settings ) {

			$result = $this->get_field_content( $settings );

			if ( ! $this->show_field || $this->show_fallback ) {
				echo $result;
				return;
			}

			$this->render_filtered_result( $result, $settings );

		}

		/**
		 * Render result with applied format from settings
		 *
		 * @param  [type] $result   [description]
		 * @param  [type] $settings [description]
		 * @return [type]           [description]
		 */
		public function render_filtered_result( $result, $settings ) {

			$is_filtered = isset( $settings['dynamic_field_filter'] ) ? $settings['dynamic_field_filter'] : false;
			$is_filtered = filter_var( $is_filtered, FILTER_VALIDATE_BOOLEAN );

			if ( $is_filtered ) {
				$result = $this->apply_callback( $result, $settings );
			}

			if ( is_wp_error( $result ) ) {
				_e( '<strong>Warning:</strong> Error appears on callback applying. Please select other callback to filter field value.', 'jet-engine' );
				return;
			}

			$is_custom = isset( $settings['dynamic_field_custom'] ) ? $settings['dynamic_field_custom'] : false;

			if ( $is_custom && ! empty( $settings['dynamic_field_format'] ) ) {

				if ( false === strpos( $settings['dynamic_field_format'], '%s' ) && false === strpos( $settings['dynamic_field_format'], '%1$s' ) ) {

					echo __( '<b>Error:</b> the field format must contains "%s" or "%1$s".', 'jet-engine' );

					return;
				}

				$result = ! is_array( $result ) && ! is_object( $result ) ? $result : '';

				try {
					$result = sprintf( $settings['dynamic_field_format'], $result );
				} catch ( ArgumentCountError $e ) {
					printf( '<b>%1$s:</b> %2$s', esc_html__( 'Error', 'jet-engine' ), $e->getMessage() );

					return;
				}

				$result = jet_engine()->listings->macros->do_macros( $result );
				$result = do_shortcode( $result );
			}

			if ( is_object( $result ) ) {

				echo __( '<b>Error:</b> can\'t render field data in the current format. You can try "Get child value" callback. Available children: ', 'jet-engine' ) . implode( ', ', array_keys( get_object_vars( $result ) ) ) . '. ' . __( 'Or one of array-related callbacks - "Multiple select field values", "Checkbox field values", "Checked values list" etc', 'jet-engine' );

				return;
			}

			if ( is_array( $result ) ) {
				echo __( '<b>Error:</b> can\'t render field data in the current format. You can try "Get child value" callback. Available children: ', 'jet-engine' ) . implode( ', ', array_keys( $result ) ) . '. ' . __( 'Or one of array-related callbacks - "Multiple select field values", "Checkbox field values", "Checked values list" etc', 'jet-engine' );

				return;
			}

			echo $result;

		}

		/**
		 * Apply filter callback
		 * @param  [type] $result   [description]
		 * @param  [type] $settings [description]
		 * @return [type]           [description]
		 */
		public function apply_callback( $result, $settings ) {
			
			// Check if multiple callbacks applied
			$callbacks = isset( $settings['filter_callbacks'] ) ? $settings['filter_callbacks'] : array();
			$callback = isset( $settings['filter_callback'] ) ? $settings['filter_callback'] : '';
			
			if ( ! empty( $callbacks ) ) {
				foreach ( $callbacks as $cb_data ) {
					$cb     = isset( $cb_data['filter_callback'] ) ? $cb_data['filter_callback'] : '';
					$result = jet_engine()->listings->apply_callback( $result, $cb, array_merge( $settings, $cb_data ), $this );
				}
			} elseif ( $callback ) {
				$result = jet_engine()->listings->apply_callback( $result, $callback, $settings, $this );
			}

			
			return $result;

		}

		/**
		 * Check if is valid timestamp
		 *
		 * @deprecated Use Jet_Engine_Tools::is_valid_timestamp()
		 *
		 * @param  [type]  $timestamp [description]
		 * @return boolean            [description]
		 */
		public function is_valid_timestamp( $timestamp ) {
			return ( ( string ) ( int ) $timestamp === $timestamp || ( int ) $timestamp === $timestamp )
				&& ( $timestamp <= PHP_INT_MAX )
				&& ( $timestamp >= ~PHP_INT_MAX );
		}

		public function get_wrapper_classes() {

			$classes       = parent::get_wrapper_classes();
			$settings      = $this->get_settings();
			$field_display = ! empty( $settings['field_display'] ) ? esc_attr( $settings['field_display'] ) : 'inline';

			$classes[] = 'display-' . $field_display;

			if ( $this->prevent_wrap() && 'inline' === $field_display ) {
				$classes[] = $this->get_name() . '__inline-wrap';
			}

			return $classes;

		}

		public function render() {

			$this->show_field = true;

			$base_class    = $this->get_name();
			$settings      = $this->get_settings();
			$tag           = ! empty( $settings['field_tag'] ) ? esc_attr( $settings['field_tag'] ) : 'div';
			$tag           = Jet_Engine_Tools::sanitize_html_tag( $tag );
			$field_display = ! empty( $settings['field_display'] ) ? esc_attr( $settings['field_display'] ) : 'inline';
			$field_icon    = ! empty( $settings['field_icon'] ) ? esc_attr( $settings['field_icon'] ) : false;
			$new_icon      = ! empty( $settings['selected_field_icon'] ) ? $settings['selected_field_icon'] : false;

			ob_start();
			$this->render_field_content( $settings );
			$field_content = ob_get_clean();

			ob_start();

			$classes = $this->get_wrapper_classes();

			if ( ! $this->prevent_wrap() ) {
				printf( '<div class="%s">', implode( ' ', $classes ) );
			}

				if ( ! $this->prevent_wrap() && 'inline' === $field_display ) {
					printf( '<div class="%s__inline-wrap">', $base_class );
				}

				if ( ! $this->prevent_icon ) {

					$new_icon_html = Jet_Engine_Tools::render_icon( $new_icon, $base_class . '__icon' );

					if ( $new_icon_html ) {
						echo $new_icon_html;
					} elseif ( $field_icon ) {
						printf( '<i class="%1$s %2$s__icon"></i>', $field_icon, $base_class );
					}

				}

				do_action( 'jet-engine/listing/dynamic-field/before-field', $this );

				printf( '<%1$s class="%2$s__content">', $tag, $base_class );
					echo $field_content;
				printf( '</%s>', $tag );

				do_action( 'jet-engine/listing/dynamic-field/after-field', $this );

				if ( ! $this->prevent_wrap() && 'inline' === $field_display ) {
					echo '</div>';
				}

			if ( ! $this->prevent_wrap() ) {
				echo '</div>';
			}

			$content = ob_get_clean();

			if ( $this->show_field ) {
				echo $content;
			}

		}

	}

}
