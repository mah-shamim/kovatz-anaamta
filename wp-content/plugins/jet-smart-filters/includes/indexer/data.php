<?php
/**
 * Jet Smart Filters Indexer Data class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Indexer_Data' ) ) {
	/**
	 * Define Jet_Smart_Filters_Indexer_Data class
	 */
	class Jet_Smart_Filters_Indexer_Data {

		public $indexing_data = array();

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 */
		public function __construct() {

			add_filter( 'jet-smart-filters/filters/localized-data', array( $this, 'prepare_localized_data' ) );
			add_filter( 'jet-smart-filters/render/ajax/data', array( $this, 'prepare_ajax_data' ) );
		}

		/**
		 * Prepare localized data
		 */
		public function prepare_localized_data( $args ) {

			do_action( 'jet-smart-filters/indexer/before-prepare-data' );

			$indexed_data = array();

			foreach ( jet_smart_filters()->query->get_default_queries() as $provider => $queries ) {
				foreach ( $queries as $query_id => $query_args ) {
					$query_args   = jet_smart_filters()->utils->merge_query_args( $query_args, jet_smart_filters()->query->get_query_args() );
					$provider_key = $provider . '/' . $query_id;

					if ( empty( $this->indexing_data[$provider_key] ) ) {
						continue;
					}

					$provider_indexed_data = $this->get_indexed_data( $provider_key, $query_args );

					if ( $provider_indexed_data ) {
						$indexed_data[$provider_key] = $provider_indexed_data;
					}
				}
			}

			if ( ! empty( $indexed_data ) ) {
				$args['jetFiltersIndexedData'] = apply_filters( 'jet-smart-filters/filters/indexed-data', $indexed_data );
			}

			do_action( 'jet-smart-filters/indexer/after-prepare-data' );

			return $args;
		}

		/**
		 * Prepare data for ajax actions
		 */
		public function prepare_ajax_data( $args ) {

			do_action( 'jet-smart-filters/indexer/before-prepare-data' );

			$provider_key     = isset( $_REQUEST['provider'] ) ? $_REQUEST['provider'] : false;
			$indexing_filters = isset( $_REQUEST['indexing_filters'] ) ? json_decode( stripcslashes( $_REQUEST['indexing_filters'] ), true ) : false;

			if ( ! ( $provider_key && $indexing_filters ) ) {
				return $args;
			}

			$indexed_data = array();

			foreach ( $indexing_filters as $filter_id ) {
				$this->add_indexing_data_from_filter( $provider_key, $filter_id );
			}

			if ( ! empty( $this->indexing_data[$provider_key] ) ) {
				$query_args = jet_smart_filters()->utils->merge_query_args(
					jet_smart_filters()->query->get_default_queries(),
					jet_smart_filters()->query->get_query_args()
				);
				//$query_args   = jet_smart_filters()->query->get_query_args();
				$indexed_data = $this->get_indexed_data( $provider_key, $query_args );
			}

			$args['jetFiltersIndexedData'] = apply_filters( 'jet-smart-filters/filters/indexed-data', array(
				$provider_key => $indexed_data
			) );

			do_action( 'jet-smart-filters/indexer/after-prepare-data' );

			return $args;
		}

		public function get_indexed_data( $provider_key, $query_args ) {

			$indexed_data  = array();
			$queried_ids   = $this->get_queried_ids( $query_args );
			$indexing_data = $this->indexing_data[$provider_key];
			$type          = ! empty( $query_args['_query_type'] ) && $query_args['_query_type'] === 'users' ? 'user' : 'post';
			$sql_and       = '';

			$pre_get_data = apply_filters( 'jet-smart-filters/pre-get-indexed-data', false, $provider_key, $query_args, $this );

			if ( false !== $pre_get_data ) {
				return $pre_get_data;
			}

			foreach ( $indexing_data as $query_type => $query_data ) {
				switch ( $query_type ) {
					case 'tax_query':
						foreach ( $query_data as $tax_key => $tax_data ) {
							$sql_and .= $sql_and ? ' OR ' : '';
							$sql_and .= "(item_query = '$query_type' AND item_key = '$tax_key' AND item_value IN ('" . implode( "','", $tax_data ) . "'))";
						}

						break;

					case 'meta_query':
						foreach ( $query_data as $meta_key => $meta_data ) {
							$sql_and .= $sql_and ? ' OR ' : '';

							if ( strpos( $meta_key, '|' ) ) {
								$suffix_data = explode( '|', $meta_key, 2 );

								switch ( $suffix_data[1] ) {
									case 'range':
										$range_data = array();

										foreach ( $meta_data as $value ) {
											if ( ! isset( $range_data['min'] ) || $range_data['min'] > $value['min'] ) {
												$range_data['min'] = $value['min'];
											}

											if ( ! isset( $range_data['max'] ) || $range_data['max'] < $value['max'] ) {
												$range_data['max'] = $value['max'];
											}
										}

										$item_key_condition = strpos( $suffix_data[0], ',' )
											? "item_key IN ('" . str_replace( [",",' '], ["','". ''], $suffix_data[0] ) . "')"
											: "item_key = '" . $suffix_data[0] . "'";

										$sql_and .= "(item_query = '$query_type' AND $item_key_condition AND item_value >= " . $range_data['min'] . " AND item_value <= " . $range_data['max'] . ")";

									break;
								}
							} else {
								$item_key_condition = strpos( $meta_key, ',' )
									? "item_key IN ('" . str_replace( [",",' '], ["','". ''], $meta_key ) . "')"
									: "item_key = '$meta_key'";
								if ( $meta_data ) {
									$sql_and .= "(item_query = '$query_type' AND $item_key_condition AND item_value IN ('" . implode( "','", $meta_data ) . "'))";
								}
							}
						}

						break;
				}
			}

			if ( $sql_and ) {
				$sql_and = "AND ($sql_and)";
			}

			global $wpdb;
			$sql = "
			SELECT MAX(item_query) as item_query, MAX(item_key) as item_key, item_value, COUNT(item_id) as count
				FROM " . jet_smart_filters()->indexer->table_name . "
				WHERE item_id IN (" . implode( ",", $queried_ids ) . ")
				AND (type = '$type')
				$sql_and
				GROUP BY item_key, item_value
				ORDER BY item_value ASC";
			$result = $wpdb->get_results( $sql, ARRAY_A );

			foreach ( $this->indexing_data[$provider_key] as $query_type => $query_type_data ) {
				$indexed_data[$query_type] = array();

				foreach ( $query_type_data as $key => $data ) {

					if ( strpos( $key, '|' ) ) {
						$suffix_data = explode( '|', $key, 2 );

						switch ( $suffix_data[1] ) {
							case 'range':
								foreach ( $data as $range_key => $range_data ) {
									foreach ( $result as $row ) {
										$is_current_key = strpos( $suffix_data[0], ',' )
											? boolval( preg_match( "/\b" . $row['item_key'] . "\b/i", $suffix_data[0] ) )
											: $row['item_key'] === $suffix_data[0];

										if ( ! $is_current_key || $row['item_query'] !== $query_type || $row['item_value'] < $range_data['min'] || $row['item_value'] > $range_data['max'] ) {
											continue;
										}

										if ( ! isset( $indexed_data[$query_type][$suffix_data[0]] ) ) {
											$indexed_data[$query_type][$suffix_data[0]] = array();
										}

										if ( ! isset( $indexed_data[$query_type][$suffix_data[0]][$range_key] ) ) {
											$indexed_data[$query_type][$suffix_data[0]][$range_key] = 0;
										}

										$indexed_data[$query_type][$suffix_data[0]][$range_key] += $row['count'];
									}
								}

							break;
						}
					} elseif ( ! empty( $data ) ) {
						foreach ( $data as $item ) {
							foreach ( $result as $row ) {
								$is_current_key = strpos( $key, ',' )
									? boolval( preg_match( "/\b" . $row['item_key'] . "\b/i", $key ) )
									: $row['item_key'] === $key;

								if ( ! $is_current_key || $row['item_query'] !== $query_type || $row['item_value'] != $item ) {
									continue;
								}

								if ( ! isset( $indexed_data[$query_type][$key] ) ) {
									$indexed_data[$query_type][$key] = array();
								}

								$indexed_data[$query_type][$key][$item] = $row['count'];
							}
						}
					}

					$indexed_data[ $query_type ] = apply_filters(
						'jet-smart-filters/filters/indexed-data/query-type-data',
						$indexed_data[ $query_type ],
						$query_type,
						$key,
						$data,
						$this
					);
				}
			}

			return $indexed_data;
		}

		/**
		 * Add indexing data from filter by id
		 */
		public function add_indexing_data_from_filter( $provider_key, $filter_id ) {

			$data        = array();
			$query_type  = '';
			$query_var   = '';
			// filter source for getting indexed data
			$source      = apply_filters( 'jet-smart-filters/indexer/filter-source', get_post_meta( $filter_id ) );
			$data_source = ! empty( $source['_data_source'] ) ? $source['_data_source'][0] : false;

			if ( $source['_filter_type'][0] === 'check-range' ) {
				$query_type = 'meta_query';
				$query_var  = $source['_query_var'][0];

				if ( ! $query_var ) {
					return;
				}

				$query_var .= '|range';

				if ( ! isset( $this->indexing_data[$provider_key][$query_type] ) ) {
					$this->indexing_data[$provider_key][$query_type] = array();
				}

				if ( ! isset( $this->indexing_data[$provider_key][$query_type][$query_var] ) ) {
					$this->indexing_data[$provider_key][$query_type][$query_var] = array();
				}

				foreach ( unserialize( $source['_source_manual_input_range'][0] ) as $range_data ) {
					$min = ! empty( $range_data['min'] ) ? intval( $range_data['min'] ) : 0;
					$max = ! empty( $range_data['max'] ) || $range_data['max'] === '0' ? intval( $range_data['max'] ) : 100;
					$key = $min . '_' . $max;

					if ( ! isset( $this->indexing_data[$provider_key][$query_type][$query_var][$key] ) ) {
						$this->indexing_data[$provider_key][$query_type][$query_var][$key] = array(
							'min' => $min,
							'max' => $max,
						);
					}
				}

				return;
			} else if ( $source['_filter_type'][0] === 'select' && filter_var( $source['_is_hierarchical'][0], FILTER_VALIDATE_BOOLEAN ) ) {
				$query_type  = 'tax_query';

				foreach ( unserialize( $source['_ih_source_map'][0] ) as $item ) {
					$tax = $item['tax'];
					$data[$tax] = $this->get_terms_by_tax( $tax );
				}
			} else {
				switch ( $data_source ) {
					case 'taxonomies':
						$query_type = 'tax_query';
						$tax        = ! empty( $source['_source_taxonomy'] ) ? $source['_source_taxonomy'][0] : false;

						$exclude_include      = isset( $source['_use_exclude_include'] ) && isset( $source['_use_exclude_include'][0] ) ? $source['_use_exclude_include'][0] : '';
						$data_exclude_include = unserialize( $source['_data_exclude_include'][0] );

						if ( $exclude_include === 'include' ) {
							$data[$tax] = is_array( $data_exclude_include ) && ! empty( $data_exclude_include )
								? $data_exclude_include
								: $this->get_terms_by_tax( $tax );
						} else {
							$data[$tax] = $this->get_terms_by_tax( $tax );

							if ( $exclude_include === 'exclude' && is_array( $data_exclude_include ) && ! empty( $data_exclude_include ) ) {
								$data[$tax] = array_diff( $data[$tax], $data_exclude_include );
							}
						}

						if ( ! empty( $source['_is_custom_query_var'] ) && filter_var( $source['_is_custom_query_var'][0], FILTER_VALIDATE_BOOLEAN ) && $source['_custom_query_var'][0] ) {
							$query_type = 'meta_query';
							$query_var  = $source['_custom_query_var'][0];

							$data[$query_var] = $data[$tax];
							unset( $data[$tax] );
						}

						break;

					case 'manual_input':
						$query_type = 'meta_query';
						$query_var  = $source['_query_var'][0];

						if ( $source['_filter_type'][0] === 'color-image' ) {
							$input_data = $source['_source_color_image_input'][0];
						} else {
							$input_data = $source['_source_manual_input'][0];
						}

						if ( ! $input_data ) {
							return;
						}

						foreach ( unserialize( $input_data ) as $input_item ) {
							$data[$query_var][] = $input_item['value'];
						}

						break;

					case 'posts':

						$query_type = 'meta_query';
						$query_var  = $source['_query_var'][0];
						$post_type  = isset( $source['_source_post_type'] ) ? $source['_source_post_type'][0] : false;

						if ( ! $post_type ) {
							break;
						}

						global $wpdb;

						$posts_table = $wpdb->posts;
						$post_type   = esc_sql( $post_type );
						$input_data  = $wpdb->get_results( "SELECT ID FROM $posts_table WHERE post_type = '$post_type' AND post_status = 'publish';" );

						if ( empty( $input_data ) ) {
							break;
						}

						foreach ( $input_data as $input_item ) {
							$data[$query_var][] = $input_item->ID;
						}

						break;

					case 'custom_fields':
						$query_type = 'meta_query';
						$query_var  = $source['_query_var'][0];
						$custom_field_options = jet_smart_filters()->data->get_choices_from_field_data( array(
							'field_key' => $source['_source_custom_field'][0],
							'source'    => $source['_custom_field_source_plugin'][0],
						) );

						$data[$query_var] = array_keys( $custom_field_options );

						break;

					default:
						$query_var   = $source['_query_var'][0];
						$custom_args = apply_filters( 'jet-smart-filters/indexer/custom-args', array(), $filter_id );
						$query_type  = isset( $custom_args['query_type'] ) ? $custom_args['query_type'] : 'meta_query';
						$options     = ! empty( $custom_args['options'] ) ? $custom_args['options'] : false;

						if ( empty( $options ) ) {
						
							$options = apply_filters( 'jet-smart-filters/filters/filter-options', $options, $filter_id, false );

							if ( ! empty( $options ) ) {
								$options = array_filter( array_keys( $options ), function( $value ) {
									return '' !== $value;
								} );
							}

						}

						$data[$query_var] = $options;

						break;
				}
			}

			if ( $query_type && $data ) {
				if ( ! isset( $this->indexing_data[$provider_key] ) ) {
					$this->indexing_data[$provider_key] = array();
				}

				if ( ! isset( $this->indexing_data[$provider_key][$query_type] ) ) {
					$this->indexing_data[$provider_key][$query_type] = array();
				}

				if ( $query_type === 'tax_query' ) {
					$this->indexing_data[$provider_key][$query_type] = array_merge_recursive( $this->indexing_data[$provider_key][$query_type], $data );
				} else {
					$this->indexing_data[$provider_key][$query_type] = array_merge( $this->indexing_data[$provider_key][$query_type], $data );
				}
			}

		}

		public function get_terms_by_tax( $tax ) {

			global $wpdb;
			$terms = array();
			$sql = "
				SELECT term_id
				FROM $wpdb->term_taxonomy
				WHERE taxonomy = '$tax'";

			$result = $wpdb->get_results( $sql, ARRAY_A );
			foreach ( $result as $term_id ) {
				array_push( $terms, $term_id['term_id'] );
			}

			return $terms;
		}

		public function get_queried_ids( $args ) {

			$ids  = array( -1 );
			$type = ! empty( $args['_query_type'] ) ? $args['_query_type'] : 'posts';

			unset( $args['jet_smart_filters'] );
			unset( $args['paged'] );
			unset( $args['nopaging'] );

			switch ( $type ) {
				case 'posts':
				case 'current-wp-query':
					// arguments that are better to remove to optimize the query
					unset( $args['wc_query'] );

					$post_main_args = [
						'post_status'    => 'publish',
						'posts_per_page' => -1,
						'fields'         => 'ids'
					];

					$query = new WP_Query( wp_parse_args( $post_main_args, $args ) );

					if ( ! empty( $query->posts ) ) {
						$ids = $query->posts;
					}

					break;

				case 'users':
					$user_main_args = [
						'number'      => -1,
						'count_total' => false,
						'fields'      => 'ID'
					];

					$users_ids =  get_users( wp_parse_args( $user_main_args, $args ) ) ;

					if ( ! empty( $users_ids ) ) {
						$ids = $users_ids;
					}

					break;

				case 'wc-product-query':
					$wc_main_args = [
						'status' => 'publish',
						'limit'  => -1,
						'return' => 'ids'
					];

					$wc_ids =  wc_get_products( wp_parse_args( $wc_main_args, $args ) ) ;

					if ( ! empty( $wc_ids->products ) ) {
						$ids = $wc_ids->products;
					}

					break;
			}

			return $ids;
		}
	}
}
