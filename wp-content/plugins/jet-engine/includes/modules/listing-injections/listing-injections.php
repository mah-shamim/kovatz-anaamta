<?php
/**
 * Listing injections module
 */

use Jet_Engine\Bricks_Views\Helpers;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


if ( ! class_exists( 'Jet_Engine_Module_Listing_Injections' ) ) {

	/**
	 * Define Jet_Engine_Module_Listing_Injections class
	 */
	class Jet_Engine_Module_Listing_Injections extends Jet_Engine_Module_Base {

		private $injected_counter = array();
		private $injected_indexes = array();
		private $parent_injected_counter = array();
		private $parent_injected_indexes = array();
		private $is_last_static_hooked = false;
		private $static_items_to_print = array();
		private $static_injections = array();
		private $static_items_post_ids = array();
		private $injected_item = false;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'listing-injections';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Listing Grid injections', 'jet-engine' );
		}

		/**
		 * Returns detailed information about current module for the dashboard page
		 * @return [type] [description]
		 */
		public function get_module_details() {
			return '<p>After activation, in the General block of Content settings tab of the Listing Grid widget in Elementor page builder appears the “Inject alternative listing items” option.</p>
				<p>This option allows you to use different Listing Templates within one Listing Grid widget.</p>';
		}

		public function get_video_embed() {
			return 'https://www.youtube.com/embed/u2QiQsBwUB8?start=57';
		}

		/**
		 * Returns array links to the module-related resources
		 * @return array
		 */
		public function get_module_links() {
			return array(
				array(
					'label' => 'How to Use Alternative Listing Template Functionality in Listing Grid',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/how-to-use-the-alternative-listing-template-functionality-in-listing-grid/',
				),
			);
		}

		/**
		 * Module init
		 *
		 * @return void
		 */
		public function module_init() {

			add_action( 'jet-engine/listing/after-general-settings', array( $this, 'add_settings' ) );
			add_action( 'jet-engine/listing/bricks/after-general-settings', array( $this, 'add_bricks_settings' ) );
			add_action( 'jet-engine/listing/grid/before', array( $this, 'reset_injected_counter' ) );
			add_action( 'jet-engine/listing/grid/after', array( $this, 'set_parent_injected_counter' ) );
			add_action( 'jet-engine/elementor-views/frontend/after_enqueue_listing_css', array(
				$this,
				'maybe_enqueue_injection_css'
			), 10, 3 );

			add_filter( 'jet-engine/listing/pre-get-item-content', array( $this, 'maybe_inject_item' ), 10, 5 );
			add_filter( 'jet-engine/listing/item-classes', array( $this, 'maybe_add_colspan' ), 10, 5 );
			add_filter( 'jet-engine/listing/grid/nav-widget-settings', array( $this, 'store_nav_settings' ), 10, 2 );
			add_filter( 'jet-engine/blocks-views/listing-injections-config', array(
				$this,
				'blocks_injections_config'
			) );
			add_filter( 'jet-engine/blocks-views/listing-grid/attributes', array( $this, 'block_atts' ) );

			add_filter( 'jet-engine/listing/item-classes', array( $this, 'update_static_item_classes' ), 10, 5 );
			add_filter( 'jet-engine/listing/item-post-id', array( $this, 'update_static_item_post_id' ), 10, 5 );

		}

		/**
		 * Injeactions config for the block editor
		 *
		 * @param array $data [description]
		 *
		 * @return [type]       [description]
		 */
		public function blocks_injections_config( $data = array() ) {
			return array(
				'enabled' => true,
			);
		}

		/**
		 * Register additional block attributes
		 *
		 * @param  [type] $atts [description]
		 *
		 * @return [type]       [description]
		 */
		public function block_atts( $atts ) {

			$atts['inject_alternative_items'] = array(
				'type'    => 'boolean',
				'default' => false,
			);

			$atts['injection_items'] = array(
				'type'    => 'array',
				'default' => array(),
			);

			return $atts;

		}

		/**
		 * Store injection-specific settings for nav
		 *
		 * @return [type] [description]
		 */
		public function store_nav_settings( $nav_settngs = array(), $settings = array() ) {

			$nav_settngs['inject_alternative_items'] = ! empty( $settings['inject_alternative_items'] ) ? $settings['inject_alternative_items'] : '';
			$nav_settngs['injection_items']          = ! empty( $settings['injection_items'] ) ? $settings['injection_items'] : array();

			return $nav_settngs;

		}

		/**
		 * Reset injected counter
		 *
		 * @return [type] [description]
		 */
		public function reset_injected_counter() {

			if ( ! empty( $this->injected_counter ) ) {
				$this->parent_injected_counter = $this->injected_counter;
				$this->parent_injected_indexes = $this->injected_indexes;
			}

			$this->injected_counter = array();
			$this->injected_indexes = array();
			$this->is_last_static_hooked = false;
		}

		public function set_parent_injected_counter() {

			if ( empty( $this->parent_injected_counter ) ) {
				return;
			}

			$this->injected_counter = $this->parent_injected_counter;
			$this->injected_indexes = $this->parent_injected_indexes;

			$this->parent_injected_counter = array();
			$this->parent_injected_indexes = array();
		}

		/**
		 * Maybe inject new listing item
		 */
		public function maybe_inject_item( $content = false, $post = null, $i = 0, $widget = false, $query = null ) {

			$this->injected_item = false;

			$settings      = $widget->get_settings();
			$injected_item = $this->get_injected_item( $settings, $post, $i, $widget, count( $query ) );

			if ( ! $injected_item ) {
				return $content;
			} else {
				$this->injected_item = $injected_item;
				add_filter( 'jet-engine/listing/item-classes', array( $this, 'apply_item_class' ) );
				return $this->get_injected_item_content( $injected_item, $post );
			}

		}

		/**
		 * Add class with injected listing ID to listing classes 
		 * 
		 * @param  [type] $classes [description]
		 * @return [type]          [description]
		 */
		public function apply_item_class( $classes ) {

			if ( $this->injected_item ) {
				$classes[] = 'jet-listing-grid--' . $this->injected_item;
			}

			return $classes;
		}

		/**
		 * Returns injected item ID
		 *
		 * @return [type] [description]
		 */
		public function get_injected_item_content( $item_id, $post ) {

			jet_engine()->admin_bar->register_post_item( $item_id );

			jet_engine()->frontend->set_listing( $item_id );

			ob_start();
			$listing_item = jet_engine()->frontend->get_listing_item( $post );
			$inline_css   = ob_get_clean();

			return $inline_css . $listing_item;

		}

		/**
		 * Maybe add columns colspan on appropriate indexes
		 *
		 * @param  [type] $classes [description]
		 * @param  [type] $post    [description]
		 * @param  [type] $i       [description]
		 * @param  [type] $widget  [description]
		 *
		 * @return [type]          [description]
		 */
		public function maybe_add_colspan( $classes = array(), $post = null, $i = null, $widget = null, $is_static = false ) {

			if ( empty( $this->injected_indexes[ $i ] ) ) {
				return $classes;
			}

			$item = $this->injected_indexes[ $i ];

			if ( ! empty( $item['static_item'] ) && ! $is_static ) {
				return $classes;
			}

			if ( empty( $item['item_colspan'] ) ) {
				return $classes;
			}

			$colspan = absint( $item['item_colspan'] );

			if ( 1 < $colspan ) {

				$columns = $widget->get_settings( 'columns' );

				if ( ! $columns ) {
					$columns = 3;
				}

				$columns = absint( $columns );

				if ( $colspan > $columns ) {
					$final_colspan = '1';
				} elseif ( $columns === $colspan ) {
					$final_colspan = '1';
				} else {
					$final_colspan = $colspan . '-' . $columns;
				}

				$classes[] = 'colspan-' . $final_colspan;
			}

			return $classes;

		}

		public function update_static_item_classes( $classes = array(), $post = null, $i = null, $widget = null, $is_static = false ) {

			$static_post_id = $this->get_static_item_post_id( $i, $is_static );

			if ( ! $static_post_id ) {
				return $classes;
			}

			$initial_id = jet_engine()->listings->data->get_current_object_id( $post );

			if ( $initial_id == $static_post_id ) {
				return $classes;
			}

			$classes = array_filter( $classes, function ( $class ) {
				return false === strpos( $class, 'jet-listing-dynamic-post-' );
			} );

			$classes[] = 'jet-listing-dynamic-post-' . $static_post_id;

			return $classes;
		}

		public function update_static_item_post_id( $id = null, $post = null, $i = null, $widget = null, $is_static = false ) {

			$static_post_id = $this->get_static_item_post_id( $i, $is_static );

			if ( ! $static_post_id ) {
				return $id;
			}

			return $static_post_id;
		}

		public function get_static_item_post_id( $i, $is_static ) {

			if ( ! $is_static ) {
				return false;
			}

			if ( empty( $this->injected_indexes[ $i ] ) ) {
				return false;
			}

			$item          = $this->injected_indexes[ $i ];
			$injected_hash = $this->get_injected_hash( $item );

			if ( empty( $this->static_items_post_ids[ $injected_hash ] ) ) {
				return false;
			}

			if ( empty( $this->static_items_post_ids[ $injected_hash ][ $i ] ) ) {
				return false;
			}

			return $this->static_items_post_ids[ $injected_hash ][ $i ];
		}

		/**
		 * Check if current iterator is matched with required number
		 *
		 * @param  [type]  $i          [description]
		 * @param  [type]  $number     [description]
		 * @param  [type]  $from_first [description]
		 *
		 * @return boolean             [description]
		 */
		public function is_matched_num( $i = 1, $number = 2, $from_first = false, $once = false, $total = 1, $listing_id = false ) {

			if ( empty( $number ) ) {
				return false;
			}

			if ( 0 > $number && $listing_id && ! empty( $this->static_injections[ $listing_id ] ) ) {
				$total = $total + $this->static_injections[ $listing_id ];
			}

			if ( empty( $once ) ) {
				if ( $from_first ) {
					if ( 0 <= $number ) {
						return ( 1 === $i || 0 === ( $i - 1 ) % $number );
					} else {
						return ( $total === $i || 0 === ( $total - $i ) % absint( $number ) );
					}
				} else {
					if ( 0 <= $number ) {
						return ( 0 === $i % $number );
					} else {
						return ( 0 === ( $total - ( $i - 1 ) ) % absint( $number ) );
					}
				}
			} else {
				if ( $from_first ) {
					if ( 0 <= $number ) {
						return ( 1 === $i );
					} else {
						return ( $total === $i );
					}
				} else {
					if ( 0 <= $number ) {
						return ( absint( $number ) === $i );
					} else {
						return ( absint( $number ) === ( $total - ( $i - 1 ) ) );
					}
				}
			}

		}

		/**
		 * Check if we need to inject item on this moment
		 *
		 * @return [type] [description]
		 */
		public function get_injected_item( $settings, $post, $i, $widget, $total ) {

			$inject = ! empty( $settings['inject_alternative_items'] ) ? $settings['inject_alternative_items'] : false;
			$inject = filter_var( $inject, FILTER_VALIDATE_BOOLEAN );

			if ( ! $inject ) {
				return false;
			}

			$items = ! empty( $settings['injection_items'] ) ? $settings['injection_items'] : array();

			if ( empty( $items ) || ! is_array( $items ) ) {
				return false;
			}

			$i          = absint( $i );
			$items      = $this->sort_items( $items );
			$listing_id = ! empty( $settings['lisitng_id'] ) ? absint( $settings['lisitng_id'] ) : false;

			if ( $listing_id !== jet_engine()->listings->data->get_listing()->get_main_id() ) {
				return false;
			}

			foreach ( array_unique( $items, SORT_REGULAR ) as $item ) {

				$result = false;

				if ( empty( $item['item'] ) ) {
					continue;
				}

				$type = ! empty( $item['item_condition_type'] ) ? $item['item_condition_type'] : 'on_item';
				$once = ! empty( $item['inject_once'] ) ? $item['inject_once'] : false;

				switch ( $type ) {

					case 'on_item':

						$num        = ! empty( $item['item_num'] ) ? intval( $item['item_num'] ) : 2;
						$from_first = ! empty( $item['start_from_first'] ) ? true : false;

						if ( $this->is_matched_num( $i, $num, $from_first, $once, $total, $listing_id ) ) {
							$this->increase_count( $item['item'], $i, $item );
							$result = $item['item'];
						}

						break;

					case 'item_meta':

						$meta_key     = ! empty( $item['meta_key'] ) ? $item['meta_key'] : false;
						$meta_compare = ! empty( $item['meta_key_compare'] ) ? $item['meta_key_compare'] : '=';
						$compare_val  = ! empty( $item['meta_key_val'] ) ? $item['meta_key_val'] : false;

						if ( $meta_key ) {

							$class = get_class( $post );

							switch ( $class ) {
								case 'WP_Post':
									$meta_val = get_post_meta( $post->ID, $meta_key );
									break;

								case 'WP_User':
									$meta_val = get_user_meta( $post->ID, $meta_key );
									break;

								case 'WP_Term':
									$meta_val = get_term_meta( $post->term_id, $meta_key );
									break;

								default:
									$meta_val = apply_filters( 'jet-engine/listing-injections/item-meta-value', false, $post, $meta_key );
							}

							$exists   = ! empty( $meta_val ) ? true : false;
							$meta_val = $exists ? $meta_val[0] : false;
							$matched  = false;

							$compare_val = do_shortcode( jet_engine()->listings->macros->do_macros( $compare_val ) );

							switch ( $meta_compare ) {
								case '=':
									if ( $meta_val == $compare_val ) {
										$matched = true;
									}
									break;

								case '!=':
									if ( $meta_val != $compare_val ) {
										$matched = true;
									}
									break;

								case '>':
									if ( $meta_val > $compare_val ) {
										$matched = true;
									}
									break;

								case '<':
									if ( $meta_val < $compare_val ) {
										$matched = true;
									}
									break;

								case '>=':
									if ( $meta_val >= $compare_val ) {
										$matched = true;
									}
									break;

								case '<=':
									if ( $meta_val <= $compare_val ) {
										$matched = true;
									}
									break;

								case 'LIKE':
									if ( false !== strpos( $compare_val, $meta_val ) ) {
										$matched = true;
									}
									break;

								case 'NOT LIKE':
									if ( false === strpos( $compare_val, $meta_val ) ) {
										$matched = true;
									}
									break;

								case 'IN':
									$compare_val = explode( ',', $compare_val );
									$compare_val = array_map( 'trim', $compare_val );

									if ( in_array( $meta_val, $compare_val ) ) {
										$matched = true;
									}

									break;

								case 'NOT IN':
									$compare_val = explode( ',', $compare_val );
									$compare_val = array_map( 'trim', $compare_val );

									if ( ! in_array( $meta_val, $compare_val ) ) {
										$matched = true;
									}

									break;

								case 'BETWEEN':

									$compare_val = explode( ',', $compare_val );
									$compare_val = array_map( 'trim', $compare_val );

									$from     = isset( $compare_val[0] ) ? floatval( $compare_val[0] ) : 0;
									$to       = isset( $compare_val[1] ) ? floatval( $compare_val[1] ) : 0;
									$meta_val = floatval( $meta_val );

									if ( ( $from <= $meta_val ) && ( $meta_val <= $to ) ) {
										$matched = true;
									}

									break;

								case 'NOT BETWEEN':

									$compare_val = explode( ',', $compare_val );
									$compare_val = array_map( 'trim', $compare_val );

									$from     = isset( $compare_val[0] ) ? floatval( $compare_val[0] ) : 0;
									$to       = isset( $compare_val[1] ) ? floatval( $compare_val[1] ) : 0;
									$meta_val = floatval( $meta_val );

									if ( ( $meta_val < $from ) || ( $to < $meta_val ) ) {
										$matched = true;
									}

									break;

							}

							if ( $matched ) {
								if ( $once ) {
									if ( ! isset( $this->injected_counter[ $item['item'] ] ) ) {
										$this->increase_count( $item['item'], $i, $item );
										$result = $item['item'];
									}
								} else {
									$this->increase_count( $item['item'], $i, $item );
									$result = $item['item'];
								}
							}

						}

						break;

					case 'has_terms':

						$tax   = ! empty( $item['tax'] ) ? $item['tax'] : 'category';
						$terms = ! empty( $item['terms'] ) ? explode( ',', $item['terms'] ) : array();
						$terms = array_map( 'trim', $terms );

						if ( ! empty( $terms ) && has_term( $terms, $tax, $post ) ) {
							if ( $once ) {
								if ( ! isset( $this->injected_counter[ $item['item'] ] ) ) {
									$this->increase_count( $item['item'], $i, $item );
									$result = $item['item'];
								}
							} else {
								$this->increase_count( $item['item'], $i, $item );
								$result = $item['item'];
							}
						}

						break;

					case 'post_type':

						$post_type = ! empty( $item['post_type'] ) ? $item['post_type'] : 'post';

						if ( ! empty( $post_type ) && isset( $post->post_type ) && $post_type === $post->post_type ) {
							if ( $once ) {
								if ( ! isset( $this->injected_counter[ $item['item'] ] ) ) {
									$this->increase_count( $item['item'], $i, $item );
									$result = $item['item'];
								}
							} else {
								$this->increase_count( $item['item'], $i, $item );
								$result = $item['item'];
							}
						}

						break;

					case 'term_tax':

						$tax  = ! empty( $item['tax'] ) ? $item['tax'] : 'category';
						$term = $post;

						if ( isset( $term->taxonomy ) && $tax === $term->taxonomy ) {
							if ( $once ) {
								if ( ! isset( $this->injected_counter[ $item['item'] ] ) ) {
									$this->increase_count( $item['item'], $i, $item );
									$result = $item['item'];
								}
							} else {
								$this->increase_count( $item['item'], $i, $item );
								$result = $item['item'];
							}
						}

						break;
				}

				if ( $result ) {

					if ( ! empty( $item['static_item'] ) ) {

						if ( ! empty( $item['static_item_context'] ) && 'default_object' !== $item['static_item_context'] ) {
							$post = jet_engine()->listings->data->get_object_by_context( $item['static_item_context'] );
						}

						$post = apply_filters( 'jet-engine/listing-injections/static-item-post', $post, $item, $settings, $widget );

						if ( $post ) {

							// increase inserted static items counter
							if ( $listing_id ) {
								$this->static_injections[ $listing_id ] = isset( $this->static_injections[ $listing_id ] ) ? $this->static_injections[ $listing_id ] ++ : 1;
							}

							$injected_hash = $this->get_injected_hash( $item );

							if ( empty( $this->static_items_post_ids[ $injected_hash ] ) ) {
								$this->static_items_post_ids[ $injected_hash ] = array();
							}

							$this->static_items_post_ids[ $injected_hash ][ $i ] = jet_engine()->listings->data->get_current_object_id( $post );

							$this->print_static_result( $result, $post, $item, $i, $listing_id );
						}

						//return false;
					} else {
						return $result;
					}

				}

			}

			return false;

		}

		public function get_injected_hash( $item ) {
			return md5( json_encode( $item ) );
		}

		public function print_static_result( $result, $post, $item, $i, $listing_id = false ) {

			$type = ! empty( $item['item_condition_type'] ) ? $item['item_condition_type'] : 'on_item';
			$num  = ! empty( $item['item_num'] ) ? intval( $item['item_num'] ) : 2;

			if ( 'on_item' === $type && 0 > $num ) {

				if ( ! $this->is_last_static_hooked ) {

					$this->is_last_static_hooked = true;

					add_action(
						'jet-engine/listing/after-grid-item',
						function ( $post, $widget, $i ) use ( $listing_id ) {
							if ( ! $listing_id || $listing_id === jet_engine()->listings->data->get_listing()->get_main_id() ) {
								$this->print_static_result_cb( $post, $widget, $i );
							}
						},
						10, 3
					);

				}

				if ( empty( $this->static_items_to_print[ $i ] ) ) {
					$this->static_items_to_print[ $i ] = array();
				}

				$this->static_items_to_print[ $i ][] = array( $result, $post );

			} else {
				echo $this->get_injected_item_content( $result, $post );
			}

		}

		public function print_static_result_cb( $post, $widget, $i ) {

			if ( ! empty( $this->static_items_to_print[ $i ] ) ) {

				foreach ( $this->static_items_to_print[ $i ] as $item ) {

					$classes    = array( 'jet-listing-grid__item', 'jet-listing-dynamic-post-' . $post->ID );
					$equal_cols = $widget->get_settings( 'equal_columns_height' );

					if ( ! empty( $equal_cols ) ) {
						$classes[] = 'jet-equal-columns';
					}

					$static_classes = apply_filters(
						'jet-engine/listing/item-classes',
						$classes, $post, $i, $widget, true
					);

					printf(
						'<div class="%1$s" data-post-id="%2$s">',
						implode( ' ', array_filter( $static_classes ) ),
						$post->ID
					);

					echo $this->get_injected_item_content( $item[0], $item[1] );

					echo '</div>';
				}

				unset( $this->static_items_to_print[ $i ] );

			}

		}

		/**
		 * Sort items. Move static items to the top of the list of items.
		 *
		 * @param array $items
		 *
		 * @return array
		 */
		public function sort_items( $items ) {

			$static_items = array();

			for ( $i = 0; $i < count( $items ); $i ++ ) {
				if ( empty( $items[ $i ]['static_item'] ) ) {
					$static_items[] = '';
				} else {
					$static_items[] = 'yes';
				}
			}

			array_multisort( $items, SORT_DESC, $static_items );

			return $items;
		}

		/**
		 * Increase injected items counter
		 *
		 * @return [type] [description]
		 */
		public function increase_count( $item_id, $i, $item ) {

			if ( ! isset( $this->injected_counter[ $item_id ] ) ) {
				$this->injected_counter[ $item_id ] = 0;
			}

			$this->injected_counter[ $item_id ] ++;
			$this->injected_indexes[ $i ] = $item;

		}

		public function get_injection_settings( $items_repeater, $widget ) {

			$settings = array();

			$settings['inject_alternative_items'] = array(
				'label'        => __( 'Inject alternative listing items', 'jet-engine' ),
				'type'         => 'switcher',
				'description'  => '',
				'return_value' => 'yes',
				'default'      => '',
			);

			$items_repeater->add_control(
				'item',
				array(
					'label'      => __( 'Listing template', 'jet-engine' ),
					'type'       => 'jet-query',
					'query_type' => 'post',
					'query'      => array(
						'post_type' => jet_engine()->post_type->slug(),
					),
				)
			);

			$items_repeater->add_control(
				'item_condition_type',
				array(
					'label'   => __( 'Inject on', 'jet-engine' ),
					'type'    => 'select',
					'default' => 'on_item',
					'options' => array(
						'on_item'   => __( 'On each N item', 'jet-engine' ),
						'item_meta' => __( 'Depends on item meta field value', 'jet-engine' ),
						'has_terms' => __( 'If post has terms', 'jet-engine' ),
						'post_type' => __( 'If post type is', 'jet-engine' ),
						'term_tax'  => __( 'If term taxonomy is', 'jet-engine' ),

					),
				)
			);

			$items_repeater->add_control(
				'item_num',
				array(
					'label'       => __( 'Item number', 'jet-engine' ),
					'type'        => 'number',
					'default'     => 2,
					'min'         => - 1000,
					'max'         => 1000,
					'step'        => 1,
					'description' => __( 'Use negative numbers to start count from the last item', 'jet-engine' ),
					'condition'   => array(
						'item_condition_type' => 'on_item',
					),
				)
			);

			$items_repeater->add_control(
				'start_from_first',
				array(
					'label'        => __( 'Start from first', 'jet-engine' ),
					'type'         => 'switcher',
					'return_value' => 'yes',
					'description'  => __( 'If checked - alternative item will be injected on first item and then on each N item after first. If not - on each N item from start. If "Item number" is negative converts into "Start from last"', 'jet-engine' ),
					'default'      => '',
					'condition'    => array(
						'item_condition_type' => 'on_item',
					),
				)
			);

			$items_repeater->add_control(
				'meta_key',
				array(
					'label'     => __( 'Key (name/ID)', 'jet-engine' ),
					'type'      => 'text',
					'default'   => '',
					'condition' => array(
						'item_condition_type' => 'item_meta'
					),
				)
			);

			$items_repeater->add_control(
				'meta_key_compare',
				array(
					'label'     => __( 'Operator', 'jet-engine' ),
					'type'      => 'select',
					'default'   => '=',
					'options'   => array(
						'='           => __( 'Equal', 'jet-engine' ),
						'!='          => __( 'Not equal', 'jet-engine' ),
						'>'           => __( 'Greater than', 'jet-engine' ),
						'>='          => __( 'Greater or equal', 'jet-engine' ),
						'<'           => __( 'Less than', 'jet-engine' ),
						'<='          => __( 'Equal or less', 'jet-engine' ),
						'LIKE'        => __( 'Like', 'jet-engine' ),
						'NOT LIKE'    => __( 'Not like', 'jet-engine' ),
						'IN'          => __( 'In', 'jet-engine' ),
						'NOT IN'      => __( 'Not in', 'jet-engine' ),
						'BETWEEN'     => __( 'Between', 'jet-engine' ),
						'NOT BETWEEN' => __( 'Not between', 'jet-engine' ),
					),
					'condition' => array(
						'item_condition_type' => 'item_meta'
					),
				)
			);

			$items_repeater->add_control(
				'meta_key_val',
				array(
					'label'       => __( 'Value', 'jet-engine' ),
					'type'        => 'text',
					'default'     => '',
					'label_block' => true,
					'description' => __( 'For <b>In</b>, <b>Not in</b>, <b>Between</b> and <b>Not between</b> compare separate multiple values with comma', 'jet-engine' ),
					'condition'   => array(
						'item_condition_type' => 'item_meta'
					),
				)
			);

			$items_repeater->add_control(
				'tax',
				array(
					'label'     => __( 'Taxonomy', 'jet-engine' ),
					'type'      => 'select',
					'options'   => jet_engine()->listings->get_taxonomies_for_options(),
					'default'   => '',
					'condition' => array(
						'item_condition_type' => array( 'has_terms', 'term_tax' ),
					),
				)
			);

			$items_repeater->add_control(
				'terms',
				array(
					'label'       => __( 'Terms', 'jet-engine' ),
					'description' => __( 'Comma-separated string of term ids or slugs', 'jet-engine' ),
					'label_block' => true,
					'type'        => 'text',
					'default'     => '',
					'condition'   => array(
						'item_condition_type' => 'has_terms'
					),
				)
			);

			$items_repeater->add_control(
				'post_type',
				array(
					'label'     => __( 'Post type', 'jet-engine' ),
					'type'      => 'select',
					'options'   => jet_engine()->listings->get_post_types_for_options(),
					'default'   => 'post',
					'condition' => array(
						'item_condition_type' => 'post_type'
					),
				)
			);

			$items_repeater->add_control(
				'inject_once',
				array(
					'label'        => __( 'Inject this item only once', 'jet-engine' ),
					'type'         => 'switcher',
					'description'  => '',
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$items_repeater->add_control(
				'item_colspan',
				array(
					'label'       => __( 'Column span', 'jet-engine' ),
					'type'        => 'select',
					'default'     => 1,
					'description' => __( 'Note: Can\'t be bigger than Columns Number value', 'jet-engine' ),
					'options'     => array(
						1 => 1,
						2 => 2,
						3 => 3,
						4 => 4,
						5 => 5,
						6 => 6,
					),
				)
			);

			$items_repeater->add_control(
				'static_item',
				array(
					'label'        => __( 'Static item', 'jet-engine' ),
					'type'         => 'switcher',
					'return_value' => 'yes',
					'description'  => __( 'If checked - alternative item will be injected without current post context. Use this to inject static items into listing.', 'jet-engine' ),
					'default'      => '',
				)
			);

			$items_repeater->add_control(
				'static_item_context',
				array(
					'label'       => __( 'Context', 'jet-engine' ),
					'description' => __( 'Select object to to use as default inside static item', 'jet-engine' ),
					'type'        => 'select',
					'default'     => 'default_object',
					'options'     => jet_engine()->listings->allowed_context_list(),
					'condition'   => array(
						'static_item' => 'yes'
					),
				)
			);

			do_action( 'jet-engine/listing-injections/item-controls', $items_repeater, $widget );

			$settings['injection_items'] = array(
				'type'      => 'repeater',
				'fields'    => $items_repeater->get_controls(),
				'default'   => array(),
				'condition' => array(
					'inject_alternative_items' => 'yes',
				)
			);

			return $settings;

		}

		/**
		 * Register listing injection settings
		 *
		 * @param [type] $widget [description]
		 */
		public function add_bricks_settings( $element ) {

			$injection_settings = $this->get_injection_settings( new Helpers\Repeater(), $element );

			foreach ( $injection_settings as $setting => $data ) {
				$element->register_jet_control(
					$setting,
					Helpers\Options_Converter::convert( $data )
				);
			}

		}

		/**
		 * Register listing injection settings
		 *
		 * @param [type] $widget [description]
		 */
		public function add_settings( $widget ) {

			foreach ( $this->get_injection_settings( new \Elementor\Repeater(), $widget ) as $setting => $data ) {
				$widget->add_control( $setting, $data );
			}

		}

		/**
		 * Check if current page build with elementor and contain injection listing - enqueue listing CSS in header
		 * Do this to avoid unstyled content flashing on page load
		 *
		 * @param $elem_view_front
		 * @param $post_id
		 * @param $elementor_data
		 */
		public function maybe_enqueue_injection_css( $elem_view_front = null, $post_id = null, $elementor_data = null ) {

			$css_added = array();

			preg_match_all( '/[\'\"]inject_alternative_items[\'\"]\:[\'\"]yes[\'\"]\,[\'\"]injection_items[\'\"]\:((?=\[)\[[^]]*\]|(?=\{)\{[^\}]*\}|\"[^"]*\")/', $elementor_data, $matches );

			if ( empty( $matches[1] ) ) {
				return;
			}

			foreach ( $matches[1] as $injection_items ) {

				preg_match_all( '/[\'\"]item[\'\"]\:[\'\"](\d+)[\'\"]/', $injection_items, $items_matches );

				if ( empty( $items_matches[1] ) ) {
					continue;
				}

				foreach ( $items_matches[1] as $inject_item_id ) {

					if ( in_array( $inject_item_id, $css_added ) ) {
						continue;
					}

					$inject_item_id = apply_filters( 'jet-engine/compatibility/translate/post', $inject_item_id );

					if ( class_exists( 'Elementor\Core\Files\CSS\Post' ) ) {
						$css_file = new Elementor\Core\Files\CSS\Post( $inject_item_id );
					} else {
						$css_file = new Elementor\Post_CSS_File( $inject_item_id );
					}

					$css_file->enqueue();

					$css_added[] = $inject_item_id;
					$elem_view_front->add_to_css_added( $inject_item_id );
				}
			}
		}

	}

}
