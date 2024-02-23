<?php
/**
 * Popup compatibility package
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Popup_Package' ) ) {

	/**
	 * Define Jet_Engine_Popup_Package class
	 */
	class Jet_Engine_Popup_Package {

		public function __construct() {

			add_action(
				'jet-popup/editor/widget-extension/after-base-controls',
				array( $this, 'register_controls' ),
				10, 2
			);

			add_filter(
				'jet-popup/widget-extension/widget-before-render-settings',
				array( $this, 'pass_engine_trigger' ),
				10, 2
			);

			add_filter(
				'jet-popup/ajax-request/get-default-content',
				array( $this, 'get_popup_content' ),
				10, 2
			);

			add_filter(
				'jet-popup/ajax-request/get-elementor-content',
				array( $this, 'get_popup_content' ),
				10, 2
			);

			add_action( 'jet-popup/data-attributes/register', function( $attributes ) {
				$attributes->register_attribute( [
					'name'        => 'jetPopupIsJetEngine',
					'type'        => 'switcher',
					'dataType'    => 'boolean',
					'dataAttr'    => 'data-popup-is-jet-engine',
					'default'     => false,
					'label'       => __( 'JetEngine Listing popup' ),
					'description' => __( 'Enable this to use this popup inside Listing Grid items' ),
				] );
			} );

			// Listing item clickable hooks
			add_filter(
				'jet-engine/listings/link/sources',
				array( $this, 'add_listing_link_source' )
			);

			add_action(
				'jet-engine/listings/document/custom-link-source-controls',
				array( $this, 'register_listing_link_controls' )
			);

			add_action(
				'elementor/element/jet-listing-items/jet_listing_settings/after_section_end',
				array( $this, 'update_listing_link_controls' )
			);

			add_filter(
				'jet-engine/blocks/editor/controls/link-settings',
				array( $this, 'register_blocks_listing_link_controls' ),
				10, 2
			);

			add_action(
				'jet-engine/blocks/editor/save-settings',
				array( $this, 'save_blocks_editor_settings' )
			);

			add_filter(
				'jet-engine/listings/frontend/custom-listing-url',
				array( $this, 'custom_listing_link' ),
				10, 2
			);

			add_filter(
				'jet-engine/listings/frontend/listing-link/overlay-attrs',
				array( $this, 'add_listing_overlay_attrs' ),
				10, 2
			);

		}

		/**
		 * Register Engine trigger
		 * @return [type] [description]
		 */
		public function register_controls( $manager ) {

			$manager->add_control(
				'jet_engine_dynamic_popup',
				array(
					'label'        => __( 'Jet Engine Listing popup', 'jet-engine' ),
					'type'         => Elementor\Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

		}

		/**
		 * If jet_engine_dynamic_popup enabled - set appropriate key in localized popup data
		 *
		 * @param  [type] $data     [description]
		 * @param  [type] $settings [description]
		 * @return [type]           [description]
		 */
		public function pass_engine_trigger( $data, $settings ) {

			$engine_trigger = ! empty( $settings['jet_engine_dynamic_popup'] ) ? true : false;

			if ( $engine_trigger ) {
				$data['is-jet-engine'] = $engine_trigger;
				$data = apply_filters( 'jet-engine/compatibility/popup-package/request-data', $data, $settings );
			}

			return $data;

		}

		/**
		 * Get dynamic content related to passed post ID
		 *
		 * @param  [type] $content    [description]
		 * @param  [type] $popup_data [description]
		 * @return [type]             [description]
		 */
		public function get_popup_content( $content, $popup_data ) {

			if ( empty( $popup_data['isJetEngine'] ) || empty( $popup_data['postId'] ) ) {
				return $content;
			}

			$popup_id     = $popup_data['popup_id'];
			$content_type = ( false !== strpos( current_filter(), 'elementor' ) ) ? 'elementor' : 'default';

			$popup_data['content_type'] = $content_type;

			if ( empty( $popup_id ) ) {
				return $content;
			}

			do_action( 'jet-engine/compatibility/popup-package/get-content', $content, $popup_data );

			$source   = ! empty( $popup_data['listingSource'] ) ? $popup_data['listingSource'] : 'posts';
			$query_id = ! empty( $popup_data['queryId'] ) ? $popup_data['queryId'] : false;
			$post_obj = false;

			if ( $query_id ) {
				$query = \Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $query_id );

				if ( $query ) {
					$source = $query->query_type;

					switch ( $query->query_type ) {
						case 'repeater':
							$id_data = explode( '-', $popup_data['postId'] );

							if ( 3 === count( $id_data ) ) {
								$object_id = $id_data[1];
								$item_index = $id_data[2];
							} else {
								$object_id = false;
								$item_index = $id_data[1];
							}

							$query->setup_query();

							if ( $object_id ) {
								$query->final_query['object_id'] = $object_id;
							}

							$items    = $query->get_items();
							$post_obj = isset( $items[ $item_index ] ) ? $items[ $item_index ] : false;

							break;

						case 'sql':
							$cast_object_to = ! empty( $query->query['cast_object_to'] ) ? $query->query['cast_object_to'] : false;

							if ( ! empty( $cast_object_to ) ) {
								$source = $cast_object_to;
							} else {
								$id_data    = explode( '-', $popup_data['postId'] );
								$item_index = absint( $id_data[1] );

								$query->setup_query();

								if ( function_exists( 'jet_smart_filters' ) && ! empty( $popup_data['filtered_query'] ) ) {
									$filtered_query = jet_smart_filters()->query->get_query_from_request( $popup_data['filtered_query'] );

									if ( ! empty( $filtered_query ) ) {
										foreach ( $filtered_query as $prop => $value ) {
											$query->set_filtered_prop( $prop, $value );
										}
									}
								}

								$advanced_query = $query->get_advanced_query();

								if ( ! $advanced_query ) {
									$offset = ! empty( $query->final_query['offset'] ) ? absint( $query->final_query['offset'] ) : 0;

									$query->final_query['limit_per_page'] = 1;
									$query->final_query['offset'] = $offset + $item_index;

									$item_index = 0;
								}

								$items    = $query->get_items();
								$post_obj = isset( $items[ $item_index ] ) ? $items[ $item_index ] : false;
							}

							break;

						default:
							$post_obj = apply_filters(
								'jet-engine/compatibility/popup-package/query/' . $query->query_type . '/post-object',
								$post_obj, $popup_data, $query, $query_id
							);
					}
				}
			}

			if ( ! $post_obj ) {
				switch ( $source ) {
					case 'terms':
					case 'WP_Term':
						$post_obj = get_term( $popup_data['postId'] );
						break;

					case 'users':
					case 'WP_User':
						$post_obj = get_user_by( 'ID', $popup_data['postId'] );
						break;

					case 'comments':
					case 'WP_Comment':
						$post_obj = get_comment( $popup_data['postId'] );
						break;

					default:
						$custom_content = apply_filters( 'jet-engine/compatibility/popup-package/custom-content', false, $popup_data );

						if ( $custom_content ) {
							return $custom_content;
						}

						$post_obj = get_post( $popup_data['postId'] );

						break;
				}
			}

			$post_obj = apply_filters( 'jet-engine/compatibility/popup-package/post-object', $post_obj, $popup_data );

			global $wp_query;
			$default_object = $wp_query->queried_object;
			$wp_query->queried_object = $post_obj;
			$wp_query->queried_object_id = $popup_data['postId'];

			if ( $post_obj && 'WP_Post' === get_class( $post_obj ) ) {
				global $post;

				$post = $post_obj;
				setup_postdata( $post );

			}

			jet_engine()->listings->data->set_current_object( $post_obj, true );

			if ( 'elementor' === $content_type && jet_engine()->has_elementor() ) {
				$content = Elementor\Plugin::instance()->frontend->get_builder_content( $popup_id );
			} else {
				$popup_post = get_post( $popup_id );

				if ( $popup_post ) {
					$content = do_blocks( $popup_post->post_content );
					$content = do_shortcode( $content );
				}
			}

			$content = apply_filters( 'jet-engine/compatibility/popup-package/the_content', $content, $popup_data );

			if ( $post_obj && 'WP_Post' === get_class( $post_obj ) ) {
				wp_reset_postdata();
			}

			$wp_query->queried_object = $default_object;

			return $content;

		}

		public function add_listing_link_source( $sources = array() ) {
			$sources[0]['options']['jet_popup'] = esc_html__( 'Open JetPopup', 'jet-engine' );
			return $sources;
		}

		public function register_listing_link_controls( $document ) {

			$document->add_control(
				'jet_attached_popup',
				array(
					'label'      => esc_html__( 'JetPopup', 'jet-popup' ),
					'type'       => 'jet-query',
					'query_type' => 'post',
					'query'      => apply_filters( 'jet_popup_default_query_args',
						array(
							'post_type'      => jet_popup()->post_type->slug(),
							'order'          => 'DESC',
							'orderby'        => 'date',
							'posts_per_page' => - 1,
							'post_status'    => 'publish',
						)
					),
					'edit_button' => array(
						'active' => true,
						'label'  => esc_html__( 'Edit Popup', 'jet-popup' ),
					),
					'condition' => array(
						'listing_link'        => 'yes',
						'listing_link_source' => 'jet_popup',
					),
				)
			);

		}

		public function update_listing_link_controls( $document ) {

			$args = $document->get_controls( 'listing_link_open_in_new' );

			$conditions = $args['condition'];
			$conditions['listing_link_source!'][] = 'jet_popup';

			$document->update_control(
				'listing_link_open_in_new',
				array(
					'condition' => $conditions
				)
			);

		}

		public function register_blocks_listing_link_controls( $link_controls, $settings ) {

			$popup_list = Jet_Popup_Utils::get_avaliable_popups();

			if ( empty( $popup_list ) ) {
				$popup_list = array(
					'' => esc_html__( 'Not Selected', 'jet-engine' ),
				);
			}

			$popup_controls = array(
				'jet_engine_listing_jet_attached_popup' => array(
					'label'     => esc_html__( 'Open JetPopup', 'jet-engine' ),
					'options'   => $popup_list,
					'value'     => ! empty( $settings['jet_attached_popup'] ) ? $settings['jet_attached_popup'] : '',
					'condition' => array(
						'jet_engine_listing_link'        => 'yes',
						'jet_engine_listing_link_source' => 'jet_popup',
					),
				)
			);

			$link_controls = \Jet_Engine_Tools::array_insert_after( $link_controls, 'jet_engine_listing_link_source', $popup_controls );

			$link_controls['jet_engine_listing_link_open_in_new']['condition']['jet_engine_listing_link_source!'][] = 'jet_popup';

			return $link_controls;
		}

		public function save_blocks_editor_settings( $post_id ) {

			if ( ! isset( $_POST['jet_engine_listing_jet_attached_popup'] ) ) {
				return;
			}

			$elementor_page_settings = get_post_meta( $post_id, '_elementor_page_settings', true );

			$elementor_page_settings['jet_attached_popup'] = esc_attr( $_POST[ 'jet_engine_listing_jet_attached_popup' ] );

			update_post_meta( $post_id, '_elementor_page_settings', $elementor_page_settings );
		}

		public function custom_listing_link( $url, $settings ) {

			if ( empty( $settings['listing_link_source'] ) || 'jet_popup' !== $settings['listing_link_source'] ) {
				return $url;
			}

			return '#';
		}

		public function add_listing_overlay_attrs( $attrs, $settings ) {

			if ( empty( $settings['listing_link_source'] ) || 'jet_popup' !== $settings['listing_link_source'] ) {
				return $attrs;
			}

			if ( empty( $settings['jet_attached_popup'] ) ) {
				return $attrs;
			}

			// Prevent adding popup attr in elementor editor.
			if ( jet_engine()->has_elementor() ) {
				$is_editor_mode = ! empty( $_GET['action'] ) && 'elementor' === $_GET['action'] && ! empty( $_GET['post'] );
				$is_editor_ajax = jet_engine()->elementor_views && jet_engine()->elementor_views->is_editor_ajax();

				if ( $is_editor_mode || $is_editor_ajax ) {
					return $attrs;
				}
			}

			$attrs['class'] .= ' jet-popup-target';

			$attrs['data-popup-instance']      = $settings['jet_attached_popup'];
			$attrs['data-popup-trigger-type']  = 'click-self';
			$attrs['data-popup-is-jet-engine'] = true;

			return $attrs;
		}

	}

}

new Jet_Engine_Popup_Package();
