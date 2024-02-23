<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! trait_exists( 'Jet_Engine_Get_Data_Sources_Trait' ) ) {
	require_once jet_engine()->plugin_path( 'includes/traits/get-data-sources.php' );
}

if ( ! class_exists( 'Jet_Engine_Blocks_Views_Editor' ) ) {

	/**
	 * Define Jet_Engine_Blocks_Views_Editor class
	 */
	class Jet_Engine_Blocks_Views_Editor {

		use Jet_Engine_Get_Data_Sources_Trait;

		public function __construct() {

			add_action( 'enqueue_block_editor_assets', array( $this, 'blocks_assets' ), -1 );

			add_action( 'add_meta_boxes', array( $this, 'add_css_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_meta' ) );

		}

		public function save_meta( $post_id ) {

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			if ( isset( $_POST['_jet_engine_listing_css'] ) ) {
				$css = esc_attr( $_POST['_jet_engine_listing_css'] );
				update_post_meta( $post_id, '_jet_engine_listing_css', $css );
			}

			$settings_keys = array(
				'jet_engine_listing_source',
				'jet_engine_listing_post_type',
				'jet_engine_listing_tax',

				'jet_engine_listing_repeater_source',
				'jet_engine_listing_repeater_field',
				'jet_engine_listing_repeater_option',

				'jet_engine_listing_link',
				'jet_engine_listing_link_source',
				'jet_engine_listing_link_option',
				'jet_engine_listing_link_open_in_new',
				'jet_engine_listing_link_rel_attr',
				'jet_engine_listing_link_aria_label',
				'jet_engine_listing_link_prefix',
			);

			$settings_to_store    = array();
			$el_settings_to_store = array();

			foreach ( $settings_keys as $key ) {
				if ( isset( $_POST[ $key ] ) ) {
					$store_key = str_ireplace( 'jet_engine_listing_', '', $key );

					if ( in_array( $store_key, array( 'source', 'post_type', 'tax' ) ) ) {
						$settings_to_store[ $store_key ] = esc_attr( $_POST[ $key ] );
						$el_settings_to_store[ 'listing_' . $store_key ] = esc_attr( $_POST[ $key ] );
					} elseif ( false !== strpos( $store_key, 'repeater_' ) ) {
						// repeater settings store only to `_elementor_page_settings` without `listing_` prefix
						$el_settings_to_store[ $store_key ] = esc_attr( $_POST[ $key ] );
					} else {
						// link settings
						$el_settings_to_store[ 'listing_' . $store_key ] = sanitize_text_field( $_POST[ $key ] );
					}
				}
			}

			if ( ! empty( $settings_to_store ) ) {

				$listing_settings = get_post_meta( $post_id, '_listing_data', true );
				$elementor_page_settings = get_post_meta( $post_id, '_elementor_page_settings', true );

				if ( empty( $listing_settings ) ) {
					$listing_settings = array();
				}

				if ( empty( $elementor_page_settings ) ) {
					$elementor_page_settings = array();
				}

				$listing_settings        = array_merge( $listing_settings, $settings_to_store );
				$elementor_page_settings = array_merge( $elementor_page_settings, $el_settings_to_store );

				update_post_meta( $post_id, '_listing_data', $listing_settings );
				update_post_meta( $post_id, '_elementor_page_settings', $elementor_page_settings );

			}

			do_action( 'jet-engine/blocks/editor/save-settings', $post_id );

		}

		/**
		 * Add listing item CSS metabox
		 */
		public function add_css_meta_box() {

			add_meta_box(
				'jet_engine_lisitng_settings',
				__( 'Listing Item Settings', 'jet-engine' ),
				array( $this, 'render_settings_box' ),
				jet_engine()->listings->post_type->slug(),
				'side'
			);

			add_meta_box(
				'jet_engine_lisitng_css',
				__( 'Listing Items CSS', 'jet-engine' ),
				array( $this, 'render_css_box' ),
				jet_engine()->listings->post_type->slug(),
				'side'
			);

		}

		/**
		 * Render box settings HTML
		 *
		 * @return [type] [description]
		 */
		public function render_settings_box( $post ) {

			$settings      = get_post_meta( $post->ID, '_listing_data', true );
			$page_settings = get_post_meta( $post->ID, '_elementor_page_settings', true );

			if ( empty( $settings ) ) {
				$settings = array();
			}

			$source = ! empty( $settings['source'] ) ? $settings['source'] : 'posts';

			$controls = array(
				'jet_engine_listing_source' => array(
					'label'   => __( 'Listing Source', 'jet-engine' ),
					'options' => jet_engine()->listings->post_type->get_listing_item_sources(),
					'value'   => $source,
				),
				'jet_engine_listing_post_type' => array(
					'label'     => __( 'Listing Post Type', 'jet-engine' ),
					'options'   => jet_engine()->listings->get_post_types_for_options(),
					'value'     => ! empty( $settings['post_type'] ) ? $settings['post_type'] : 'post',
					'condition' => array(
						'jet_engine_listing_source' => array( 'posts', 'repeater' ),
					),
				),
				'jet_engine_listing_tax' => array(
					'label'     => __( 'Listing Taxonomy', 'jet-engine' ),
					'options'   => jet_engine()->listings->get_taxonomies_for_options(),
					'value'     => ! empty( $settings['tax'] ) ? $settings['tax'] : 'category',
					'condition' => array(
						'jet_engine_listing_source' => array( 'terms' ),
					),
				),
				'jet_engine_listing_repeater_source' => array(
					'label'     => __( 'Repeater source', 'jet-engine' ),
					'options'   => jet_engine()->listings->repeater_sources(),
					'value'     => ! empty( $page_settings['repeater_source'] ) ? $page_settings['repeater_source'] : 'jet_engine',
					'condition' => array(
						'jet_engine_listing_source' => array( 'repeater' ),
					),
				),
				'jet_engine_listing_repeater_field' => array(
					'label'       => __( 'Repeater field', 'jet-engine' ),
					'description' => __( 'If JetEngine, or ACF, or etc selected as source.', 'jet-engine' ),
					'value'       => ! empty( $page_settings['repeater_field'] ) ? $page_settings['repeater_field'] : '',
					'condition'   => array(
						'jet_engine_listing_source' => array( 'repeater' ),
						'jet_engine_listing_repeater_source!' => 'jet_engine_options',
					),
				),
				'jet_engine_listing_repeater_option' => array(
					'label'       => __( 'Repeater option', 'jet-engine' ),
					'description' => __( 'If <b>JetEngine Options Page</b> selected as source.', 'jet-engine' ),
					'groups'      => jet_engine()->options_pages->get_options_for_select( 'repeater' ),
					'value'       => ! empty( $page_settings['repeater_option'] ) ? $page_settings['repeater_option'] : '',
					'condition'   => array(
						'jet_engine_listing_source' => array( 'repeater' ),
						'jet_engine_listing_repeater_source' => 'jet_engine_options',
					),
				),
			);

			$controls = apply_filters( 'jet-engine/blocks/editor/controls/settings', $controls, $settings, $post );

			$link_controls = array(
				'jet_engine_listing_link' => array(
					'label'   => __( 'Make listing item clickable', 'jet-engine' ),
					'value'   => ! empty( $page_settings['listing_link'] ) ? $page_settings['listing_link'] : '',
					'options' => array(
						''    => __( 'No', 'jet-engine' ),
						'yes' => __( 'Yes', 'jet-engine' ),
					),
				),
				'jet_engine_listing_link_source' => array(
					'label'     => __( 'Link source', 'jet-engine' ),
					'value'     => ! empty( $page_settings['listing_link_source'] ) ? $page_settings['listing_link_source'] : '',
					'groups'    => jet_engine()->listings->get_listing_link_sources(),
					'condition' => array(
						'jet_engine_listing_link' => 'yes',
					),
				),
				'jet_engine_listing_link_open_in_new' => array(
					'label'   => __( 'Open in new window', 'jet-engine' ),
					'value'   => ! empty( $page_settings['listing_link_open_in_new'] ) ? $page_settings['listing_link_open_in_new'] : '',
					'options' => array(
						''    => __( 'No', 'jet-engine' ),
						'yes' => __( 'Yes', 'jet-engine' ),
					),
					'condition' => array(
						'jet_engine_listing_link' => 'yes',
					),
				),
				'jet_engine_listing_link_rel_attr' => array(
					'label'     => __( 'Add "rel" attr', 'jet-engine' ),
					'value'     => ! empty( $page_settings['listing_link_rel_attr'] ) ? $page_settings['listing_link_rel_attr'] : '',
					'options'   => \Jet_Engine_Tools::get_rel_attr_options(),
					'condition' => array(
						'jet_engine_listing_link' => 'yes',
					),
				),
				'jet_engine_listing_link_aria_label' => array(
					'label'       => __( 'Aria label attr / Link text', 'jet-engine' ),
					'description' => __( 'Use <b>Shortcode Generator</b> or <b>Macros Generator</b> to pass a dynamic value', 'jet-engine' ),
					'value'       => ! empty( $page_settings['listing_link_aria_label'] ) ? $page_settings['listing_link_aria_label'] : '',
					'condition'   => array(
						'jet_engine_listing_link' => 'yes',
					),
				),
				'jet_engine_listing_link_prefix' => array(
					'label'     => __( 'Link prefix', 'jet-engine' ),
					'value'     => ! empty( $page_settings['listing_link_prefix'] ) ? $page_settings['listing_link_prefix'] : '',
					'condition' => array(
						'jet_engine_listing_link' => 'yes',
					),
				),
			);

			if ( jet_engine()->options_pages ) {
				$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'plain' );

				if ( ! empty( $options_pages_select ) ) {

					$options_link_controls = array(
						'jet_engine_listing_link_option' => array(
							'label'     => __( 'Option', 'jet-engine' ),
							'groups'    => $options_pages_select,
							'value'     => ! empty( $page_settings['listing_link_option'] ) ? $page_settings['listing_link_option'] : '',
							'condition' => array(
								'jet_engine_listing_link'        => 'yes',
								'jet_engine_listing_link_source' => 'options_page',
							),
						),
					);

					$link_controls = \Jet_Engine_Tools::array_insert_after( $link_controls, 'jet_engine_listing_link_source', $options_link_controls );
				}
			}

			$link_controls = apply_filters( 'jet-engine/blocks/editor/controls/link-settings', $link_controls, $page_settings, $post );
			$all_controls  = array_merge( $controls, $link_controls );

			$conditions = array();

			echo '<style>
				.jet-engine-base-control select,
				.jet-engine-base-control input {
					box-sizing: border-box;
					margin: 0;
				}
				.jet-engine-base-control select {
					width: 100%;
				}
				.jet-engine-base-control .components-base-control__field {
					margin: 0 0 10px;
				}
				.jet-engine-base-control .components-base-control__label {
					display: block;
					font-weight: bold;
					padding: 0 0 5px;
				}
				.jet-engine-base-control .components-base-control__help {
					font-size: 12px;
					font-style: normal;
					color: #757575;
					margin: 5px 0 0;
				}
				.jet-engine-condition-setting {
					display: none;
				}
				.jet-engine-condition-setting-show {
					display: block;
				}
			</style>';
			echo '<div class="components-base-control jet-engine-base-control">';

				foreach ( $all_controls as $control_name => $control_args ) {

					$field_classes = array(
						'components-base-control__field',
					);

					// for backward compatibility
					if ( ! empty( $control_args['source'] ) ) {
						$control_args['condition'] = array(
							'jet_engine_listing_source' => $control_args['source'],
						);
					}

					if ( ! empty( $control_args['condition'] ) ) {
						$conditions[ $control_name ] = $control_args['condition'];

						$field_classes[] = 'jet-engine-condition-setting';

						$is_visible = true;

						foreach ( $control_args['condition'] as $cond_field => $cond_value ) {

							$is_negative = false !== strpos( $cond_field, '!' );

							if ( $is_negative ) {
								$cond_field = str_replace( '!', '', $cond_field );
							}

							$current_value = $all_controls[ $cond_field ]['value'];

							if ( is_array( $cond_value ) ) {
								$check = in_array( $current_value, $cond_value );
							} else {
								$check = $current_value == $cond_value;
							}

							if ( $is_negative ) {
								$check = ! $check;
							}

							if ( ! $check ) {
								$is_visible = false;
								break;
							}
						}

						if ( $is_visible ) {
							$field_classes[] = 'jet-engine-condition-setting-show';
						}
					}

					echo '<div class="' . join( ' ', $field_classes ) . '">';
						echo '<label class="components-base-control__label" for="' . $control_name . '">';
							echo $control_args['label'];
						echo '</label>';

						if ( ! empty( $control_args['groups'] ) || ! empty( $control_args['options'] ) ) {
							echo '<select id="' . $control_name . '" name="' . $control_name . '" class="components-select-control__input">';

								if ( ! empty( $control_args['groups'] ) ) {

									foreach ( $control_args['groups'] as $group_key => $group ) {

										if ( empty( $group ) ) {
											continue;
										}

										if ( ! empty( $group['options'] ) ) {
											echo '<optgroup label="' . $group['label'] . '">';

											foreach ( $group['options'] as $option_key => $option_label ) {
												printf( '<option value="%1$s"%3$s>%2$s</option>',
													$option_key,
													$option_label,
													selected( $control_args['value'], $option_key, false )
												);
											}

											echo '</optgroup>';

										} elseif ( is_string( $group ) ) {
											printf( '<option value="%1$s"%3$s>%2$s</option>',
												$group_key,
												$group,
												selected( $control_args['value'], $group_key, false )
											);
										}
									}

								} else {
									foreach ( $control_args['options'] as $option_key => $option_label ) {
										printf( '<option value="%1$s"%3$s>%2$s</option>',
											$option_key,
											$option_label,
											selected( $control_args['value'], $option_key, false )
										);
									}
								}

							echo '</select>';
						} else {
							$input_type = ! empty( $control_args['input_type'] ) ? $control_args['input_type'] : 'text';
							printf( '<input type="%1$s" id="%2$s" name="%2$s" class="components-text-control__input" value="%3$s">',
								esc_attr( $input_type ),
								$control_name,
								esc_attr( $control_args['value'] )
							);
						}

						if ( ! empty( $control_args['description'] ) ) {
							echo '<p class="components-base-control__help">' . $control_args['description'] . '</p>';
						}

					echo '</div>';
				}

				do_action( 'jet-engine/blocks/editor/settings-meta-box', $post );

				echo '<p>';
					_e( 'You need to reload page after saving to apply new settings', 'jet-engine' );
				echo '</p>';
			echo '</div>';

			echo "<script>
					var JetEngineListingConditions = " . json_encode( $conditions ) . ";
					
					jQuery( '[name^=\"jet_engine_listing_\"]' ).on( 'change', function( e ) {
						var fieldName = jQuery( e.currentTarget ).attr('name');
						
						for ( var field in JetEngineListingConditions ) {
							
							if ( field === fieldName ) {
								continue;
							}
							
							var conditions = JetEngineListingConditions[ field ];
							
							if ( -1 === Object.keys( conditions ).indexOf( fieldName ) && -1 === Object.keys( conditions ).indexOf( fieldName + '!' ) ) {
								continue;
							}
							
							var isVisible = true,
								fieldWrapper = jQuery( '[name=\"' + field + '\"]' ).closest( '.jet-engine-condition-setting' );

							for ( var conditionField in conditions ) {
									
								var isNegative = -1 !== conditionField.indexOf( '!' ),
									conditionFieldName = conditionField;
								
								if ( isNegative ) {
									conditionFieldName = conditionField.replace( '!', '' );
								}
								
								var currentValue   = jQuery( '[name=\"' + conditionFieldName + '\"]' ).val(),
									conditionValue = conditions[ conditionField ];
								
								if ( Array.isArray( conditionValue ) ) {
									isVisible = -1 !== conditionValue.indexOf( currentValue )
								} else {
									isVisible = conditionValue == currentValue;
								}
								
								if ( isNegative ) {
									isVisible = !isVisible;
								}
								
								if ( !isVisible ) {
									break;
								}
							}
							
							if ( isVisible )  {
								fieldWrapper.addClass( 'jet-engine-condition-setting-show' );
							} else {
								fieldWrapper.removeClass( 'jet-engine-condition-setting-show' );
							}
						}
					} );
				</script>";
		}

		/**
		 * Render CSS metabox
		 *
		 * @return [type] [description]
		 */
		public function render_css_box( $post ) {

			$css = get_post_meta( $post->ID, '_jet_engine_listing_css', true );

			if ( ! $css ) {
				$css = '';
			}

			?>
			<div class="jet-engine-listing-css">
				<p><?php
					_e( 'When targeting your specific element, add <code>selector</code> before the tags and classes you want to exclusively target, i.e: <code>selector a { color: red;}</code>', 'jet-engine' );
				?></p>
				<textarea class="components-textarea-control__input jet_engine_listing_css" name="_jet_engine_listing_css" rows="16" style="width:100%"><?php
					echo $css;
				?></textarea>
			</div>
			<?php

		}

		/**
		 * Get meta fields for post type
		 *
		 * @return array
		 */
		public function get_meta_fields() {

			if ( jet_engine()->meta_boxes ) {
				return jet_engine()->meta_boxes->get_fields_for_select( 'plain', 'blocks' );
			} else {
				return array();
			}

		}

		/**
		 * Get meta fields for post type
		 *
		 * @return array
		 */
		public function get_repeater_fields() {

			if ( jet_engine()->meta_boxes ) {
				$groups = jet_engine()->meta_boxes->get_fields_for_select( 'repeater', 'blocks' );
			} else {
				$groups = array();
			}

			if ( jet_engine()->options_pages ) {
				$groups[] = array(
					'label'  => __( 'Other', 'jet-engine' ),
					'values' => array(
						array(
							'value' => 'options_page',
							'label' => __( 'Options' ),
						),
					),
				);
			}

			$extra_fields = apply_filters( 'jet-engine/listings/dynamic-repeater/fields', array() );

			if ( ! empty( $extra_fields ) ) {

				foreach ( $extra_fields as $key => $data ) {

					if ( ! is_array( $data ) ) {

						$groups[] = array(
							'label'  => $data,
							'values' => array(
								array(
									'value' => $key,
									'label' => $data,
								),
							),
						);

						continue;
					}

					$values = array();

					if ( ! empty( $data['options'] ) ) {
						foreach ( $data['options'] as $val => $label ) {
							$values[] = array(
								'value' => $val,
								'label' => $label,
							);
						}
					}

					$groups[] = array(
						'label'  => $data['label'],
						'values' => $values,
					);
				}
			}

			return $groups;

		}

		/**
		 * Get registered options fields
		 *
		 * @return array
		 */
		public function get_options_fields( $type = 'plain' ) {
			if ( jet_engine()->options_pages ) {
				return jet_engine()->options_pages->get_options_for_select( $type, 'blocks' );
			} else {
				return array();
			}
		}

		/**
		 * Returns filter callbacks list
		 *
		 * @return [type] [description]
		 */
		public function get_filter_callbacks() {

			$callbacks = jet_engine()->listings->get_allowed_callbacks();
			$result    = array( array(
				'value' => '',
				'label' => '--',
			) );

			foreach ( $callbacks as $function => $label ) {
				$result[] = array(
					'value' => $function,
					'label' => $label,
				);
			}

			return $result;

		}

		public function get_filter_callbacks_args() {

			$result     = array();
			$disallowed = array( 'checklist_divider_color' );

			foreach ( jet_engine()->listings->get_callbacks_args() as $key => $args ) {

				if ( in_array( $key, $disallowed ) ) {
					continue;
				}

				$args['prop'] = $key;

				if ( ! empty( $args['description'] ) ) {
					$args['description'] = wp_kses_post( $args['description'] );
				}

				if ( 'select' === $args['type'] ) {

					$options = $args['options'];
					$args['options'] = array();

					foreach ( $options as $value => $label ) {
						$args['options'][] = array(
							'value' => $value,
							'label' => $label,
						);
					}
				}

				// Convert `slider` control to `number` control.
				if ( 'slider' === $args['type'] ) {
					$args['type'] = 'number';

					if ( ! empty( $args['range'] ) ) {

						$first_unit = $this->get_first_key( $args['range'] );

						foreach ( array( 'min', 'max', 'step' ) as $range_arg ) {
							if ( isset( $args['range'][ $first_unit ][ $range_arg ] ) ) {
								$args[ $range_arg ] = $args['range'][ $first_unit ][ $range_arg ];
							}
						}

						unset( $args['range'] );
					}
				}

				$args['condition'] = $args['condition']['filter_callback'];

				$result[] = $args;
			}

			return $result;
		}

		public function get_first_key( $array = array() ) {

			if ( function_exists( 'array_key_first' ) ) {
				return array_key_first( $array );
			} else {
				$keys = array_keys( $array );
				return $keys[0];
			}

		}

		/**
		 * Returns all taxonomies list for options
		 *
		 * @return [type] [description]
		 */
		public function get_taxonomies_for_options() {

			$result     = array();
			$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

			foreach ( $taxonomies as $taxonomy ) {

				if ( empty( $taxonomy->object_type ) || ! is_array( $taxonomy->object_type ) ) {
					continue;
				}

				foreach ( $taxonomy->object_type as $object ) {
					if ( empty( $result[ $object ] ) ) {
						$post_type = get_post_type_object( $object );

						if ( ! $post_type ) {
							continue;
						}

						$result[ $object ] = array(
							'label'  => $post_type->labels->name,
							'values' => array(),
						);
					}

					$result[ $object ]['values'][] = array(
						'value' => $taxonomy->name,
						'label' => $taxonomy->labels->name,
					);

				};
			}

			return array_values( $result );

		}

		/**
		 * Register plugin sidebar
		 *
		 * @return [type] [description]
		 */
		public function blocks_assets() {

			//if ( 'jet-engine' !== get_post_type() ) {
			//	return;
			//}

			do_action( 'jet-engine/blocks-views/editor-script/before' );

			wp_enqueue_script(
				'jet-engine-blocks-views',
				jet_engine()->plugin_url( 'assets/js/admin/blocks-views/blocks.js' ),
				array( 'wp-components', 'wp-element', 'wp-blocks', 'wp-block-editor', 'lodash' ),
				jet_engine()->get_version(),
				true
			);

			wp_enqueue_style(
				'jet-engine-blocks-views',
				jet_engine()->plugin_url( 'assets/css/admin/blocks-views.css' ),
				array(),
				jet_engine()->get_version()
			);

			do_action( 'jet-engine/blocks-views/editor-script/after' );

			global $post;

			$settings = array();
			$post_id  = false;

			if ( $post ) {
				$settings = get_post_meta( $post->ID, '_elementor_page_settings', true );
				$post_id  = $post->ID;
			}

			if ( empty( $settings ) ) {
				$settings = array();
			}

			$source     = ! empty( $settings['listing_source'] ) ? $settings['listing_source'] : 'posts';
			$post_type  = ! empty( $settings['listing_post_type'] ) ? $settings['listing_post_type'] : 'post';
			$tax        = ! empty( $settings['listing_tax'] ) ? $settings['listing_tax'] : 'category';
			$rep_source = ! empty( $settings['repeater_source'] ) ? esc_attr( $settings['repeater_source'] ) : '';
			$rep_field  = ! empty( $settings['repeater_field'] ) ? esc_attr( $settings['repeater_field'] ) : '';
			$rep_option = ! empty( $settings['repeater_option'] ) ? esc_attr( $settings['repeater_option'] ) : '';

			jet_engine()->listings->data->set_listing( jet_engine()->listings->get_new_doc( array(
				'listing_source'    => $source,
				'listing_post_type' => $post_type,
				'listing_tax'       => $tax,
				'repeater_source'   => $rep_source,
				'repeater_field'    => $rep_field,
				'repeater_option'   => $rep_option,
				'is_main'           => true,
			), $post_id ) );

			$current_object_id = $this->get_current_object();
			$field_sources     = jet_engine()->listings->data->get_field_sources();
			$sources           = array();

			foreach ( $field_sources as $value => $label ) {
				$sources[] = array(
					'value' => $value,
					'label' => $label,
				);
			}

			$link_sources = $this->get_dynamic_sources( 'plain' );
			$link_sources = apply_filters( 'jet-engine/blocks-views/dynamic-link-sources', $link_sources );

			$media_sources = $this->get_dynamic_sources( 'media' );
			$media_sources = apply_filters( 'jet-engine/blocks-views/dynamic-media-sources', $media_sources );

			/**
			 * Format:
			 * array(
			 *  	'block-type-name' => array(
			 *  		array(
			 * 				'prop' => 'prop-name-to-set',
			 * 				'label' => 'control-label',
			 * 				'condition' => array(
			 * 					'prop' => array( 'value' ),
			 * 				)
			 * 			)
			 *  	)
			 *  )
			 */
			$custom_controls = apply_filters( 'jet-engine/blocks-views/custom-blocks-controls', array() );
			$custom_panles   = array();

			$config = apply_filters( 'jet-engine/blocks-views/editor-data', array(
				'isJetEnginePostType'   => 'jet-engine' === get_post_type(),
				'settings'              => $settings,
				'object_id'             => $current_object_id,
				'fieldSources'          => $sources,
				'imageSizes'            => jet_engine()->listings->get_image_sizes( 'blocks' ),
				'metaFields'            => $this->get_meta_fields(),
				'repeaterFields'        => $this->get_repeater_fields(),
				'mediaFields'           => $media_sources,
				'linkFields'            => $link_sources,
				'optionsFields'         => $this->get_options_fields( 'plain' ),
				'mediaOptionsFields'    => $this->get_options_fields( 'media' ),
				'userRoles'             => Jet_Engine_Tools::get_user_roles_for_js(),
				'repeaterOptionsFields' => $this->get_options_fields( 'repeater' ),
				'filterCallbacks'       => $this->get_filter_callbacks(),
				'filterCallbacksArgs'   => $this->get_filter_callbacks_args(),
				'taxonomies'            => $this->get_taxonomies_for_options(),
				'queriesList'           => \Jet_Engine\Query_Builder\Manager::instance()->get_queries_for_options( true ),
				'objectFields'          => jet_engine()->listings->data->get_object_fields( 'blocks' ),
				'postTypes'             => Jet_Engine_Tools::get_post_types_for_js(),
				'legacy'                => array(
					'is_disabled' => jet_engine()->listings->legacy->is_disabled(),
					'message'     => jet_engine()->listings->legacy->get_notice(),
				),
				'glossariesList'        => jet_engine()->glossaries->get_glossaries_for_js(),
				'atts'                  => array(
					'dynamicField'    => jet_engine()->blocks_views->block_types->get_block_atts( 'dynamic-field' ),
					'dynamicLink'     => jet_engine()->blocks_views->block_types->get_block_atts( 'dynamic-link' ),
					'dynamicImage'    => jet_engine()->blocks_views->block_types->get_block_atts( 'dynamic-image' ),
					'dynamicRepeater' => jet_engine()->blocks_views->block_types->get_block_atts( 'dynamic-repeater' ),
					'dynamicTerms'    => jet_engine()->blocks_views->block_types->get_block_atts( 'dynamic-terms' ),
					'listingGrid'     => jet_engine()->blocks_views->block_types->get_block_atts( 'listing-grid' ),
				),
				'customPanles'          => $custom_panles,
				'customControls'        => $custom_controls,
				'injections'            => apply_filters( 'jet-engine/blocks-views/listing-injections-config', array(
					'enabled' => false,
				) ),
				'relationsTypes'        => array(
					array(
						'value' => 'grandparents',
						'label' => __( 'Grandparent Posts', 'jet-engine' ),
					),
					array(
						'value' => 'grandchildren',
						'label' => __( 'Grandchildren Posts', 'jet-engine' ),
					),
				),
				'listingOptions'   => jet_engine()->listings->get_listings_for_options( 'blocks' ),
				'hideOptions'      => jet_engine()->listings->get_widget_hide_options( 'blocks' ),
				'activeModules'    => jet_engine()->modules->get_active_modules(),
				'blocksWithIdAttr' => jet_engine()->blocks_views->block_types->get_blocks_with_id_attr(),
				'preventWrap'      => \Jet_Engine\Modules\Performance\Module::instance()->is_tweak_active( 'optimized_dom' ),
			) );

			wp_localize_script(
				'jet-engine-blocks-views',
				'JetEngineListingData',
				apply_filters( 'jet-engine/blocks-views/editor/config', $config )
			);

		}

		/**
		 * Returns information about current object
		 *
		 * @param  [type] $source [description]
		 * @return [type]         [description]
		 */
		public function get_current_object() {

			if ( 'jet-engine' !== get_post_type() ) {
				return get_the_ID();
			}

			$source    = jet_engine()->listings->data->get_listing_source();
			$object_id = null;

			switch ( $source ) {

				case 'posts':
				case 'repeater':

					$post_type = jet_engine()->listings->data->get_listing_post_type();

					$posts = get_posts( array(
						'post_type'        => $post_type,
						'numberposts'      => 1,
						'orderby'          => 'date',
						'order'            => 'DESC',
						'suppress_filters' => false,
					) );

					if ( ! empty( $posts ) ) {
						$post = $posts[0];
						jet_engine()->listings->data->set_current_object( $post );
						$object_id = $post->ID;
					}

					break;

				case 'terms':

					$tax   = jet_engine()->listings->data->get_listing_tax();
					$terms = get_terms( array(
						'taxonomy'   => $tax,
						'hide_empty' => false,
					) );

					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
						$term = $terms[0];
						jet_engine()->listings->data->set_current_object( $term );
						$object_id = $term->term_id;
					}

					break;

				case 'users':

					$object_id = get_current_user_id();
					jet_engine()->listings->data->set_current_object( wp_get_current_user() );

					break;

				default:

					$object_id = apply_filters(
						'jet-engine/blocks-views/editor/config/object/' . $source,
						false,
						$this
					);

					break;

			}

			return $object_id;

		}

	}

}
