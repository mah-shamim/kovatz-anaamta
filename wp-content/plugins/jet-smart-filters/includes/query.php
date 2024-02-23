<?php
/**
 * Query manager class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Query_Manager' ) ) {

	/**
	 * Define Jet_Smart_Filters_Query_Manager class
	 */
	class Jet_Smart_Filters_Query_Manager {

		public  $_query            = array();
		private $_default_query    = array();
		private $_query_settings   = array();
		private $_props            = array();

		private $provider          = null;
		private $is_ajax_filter    = null;

		/**
		 * Constructor for the class
		 */
		public function __construct() {

			add_filter( 'the_posts', array( $this, 'query_props_handler' ), 999, 2 );
			add_filter( 'posts_pre_query', array( $this, 'set_found_rows' ), 10, 2 );

			/**
			 * Alphabet filter
			 * Note: Moved to the __construct to better compatibility with JetEngine LoadMore and LazyLoad
			 */
			add_filter( 'posts_where', array( $this, 'set_query_where' ), 10, 2 );
		}

		/**
		 * Set no_found_rows to false
		 */
		public function set_found_rows( $posts, $query ) {

			if ( $query->get( 'jet_smart_filters' ) ) {
				$query->set( 'no_found_rows', false );
			}

			return $posts;
		}

		/**
		 * Store default query for passed provider
		 */
		public function store_provider_default_query( $provider_id, $query_args, $query_id = false, $force_rewrite = false ) {

			if ( ! $query_id ) {
				$query_id = 'default';
			}

			if ( empty( $this->_default_query[ $provider_id ] ) ) {
				$this->_default_query[ $provider_id ] = array();
			}

			if ( ! $force_rewrite && isset( $this->_default_query[ $provider_id ][ $query_id ] ) ) {
				return;
			}

			if ( isset( $_REQUEST['jet-smart-filters-redirect'] ) ) {
				unset( $query_args['meta_query'] );
				unset( $query_args['tax_query'] );
			}

			if ( $this->is_ajax_filter() ) {
		
				$request_provider = $this->get_current_provider();

				if ( ! $request_provider ) {
					return;
				}

				if ( ! $query_id ) {
					$query_id = 'default';
				}
				
				// store default query only if we trying to set default query for requested provider
				// its important because multiple different queries could trigger this method during AJAX filters request
				if ( $request_provider['provider'] === $provider_id && $request_provider['query_id'] === $query_id ) {
					$this->_default_query = $query_args;
				}

				return;
			}

			$this->_default_query[ $provider_id ][ $query_id ] = $query_args;
		}

		/**
		 * Return default queries array
		 */
		public function get_default_queries() {
			return $this->_default_query;
		}

		/**
		 * Returns query settings
		 */
		public function get_query_settings() {

			return $this->_query_settings;
		}

		/**
		 * Query vars
		 */
		public function query_vars() {

			return apply_filters( 'jet-smart-filters/query/vars', array(
				'plain_query',
				'tax_query',
				'meta_query',
				'date_query',
				'_s',
				'sort',
				'alphabet'
			) );
		}

		/**
		 * Return parsed query arguments
		 */
		public function get_query_args() {

			if ( $this->is_ajax_filter() && ! empty( $this->_default_query ) ) {
				return array_merge( $this->_default_query, $this->_query );
			} else {
				return $this->_query;
			}
		}

		
		/**
		 * Check if is ajax filter processed
		 */
		public function is_ajax_filter() {

			if ( null !== $this->is_ajax_filter ) {
				return $this->is_ajax_filter;
			}

			$allowed_actions = apply_filters( 'jet-smart-filters/query/allowed-ajax-actions', array(
				'jet_smart_filters',
				'jet_smart_filters_refresh_controls',
				'jet_smart_filters_refresh_controls_reload',
			) );

			if ( isset( $_REQUEST['jet_engine_action'] ) && in_array( $_REQUEST['jet_engine_action'], $allowed_actions ) ) {
				$this->is_ajax_filter = true;
				return $this->is_ajax_filter;
			}

			if ( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $allowed_actions ) ) {
				$this->is_ajax_filter = true;
				return $this->is_ajax_filter;
			}

			if ( ! wp_doing_ajax() ) {
				$this->is_ajax_filter = false;
				return $this->is_ajax_filter;
			}

			$this->is_ajax_filter = false;

			return $this->is_ajax_filter;
		}

		/**
		 * Store query properties
		 */
		public function query_props_handler( $posts, $query ) {

			if ( $query->get( 'jet_smart_filters' ) ) {
				$this->store_query_props( $query );
			}

			return $posts;
		}

		/**
		 * Store query properites
		 */
		public function store_query_props( $query ) {

			$provider_data = $this->decode_provider_data( $query->get( 'jet_smart_filters' ) );
			$provider      = $provider_data['provider'];
			$query_id      = $provider_data['query_id'];

			if ( empty( $this->_props[ $provider ] ) ) {
				$this->_props[ $provider ] = array();
			}

			if ( empty( $this->_props[ $provider ][ $query_id ] ) ) {
				$this->_props[ $provider ][ $query_id ] = array();
			}
			
			do_action( 'jet-smart-filters/query/store-query-props/' . $provider, $this, $query_id );

			$this->_props[ $provider ][ $query_id ]['found_posts']   = $query->found_posts;
			$this->_props[ $provider ][ $query_id ]['max_num_pages'] = $query->max_num_pages;
			$this->_props[ $provider ][ $query_id ]['page']          = $query->get( 'paged' );
		}

		/**
		 * Encode provider data
		 */
		public function encode_provider_data( $provider, $query_id = 'default' ) {

			if ( ! $query_id ) {
				$query_id = 'default';
			}

			return $provider . '/' . $query_id;
		}

		/**
		 * Decode provider data
		 */
		public function decode_provider_data( $provider ) {

			$data   = explode( '/', $provider );
			$result = array();

			if ( empty( $data ) ) {
				$result['provider'] = $provider;
				$result['query_id'] = 'default';
			} elseif ( ! empty( $data[0] ) && empty( $data[1] ) ) {
				$result['provider'] = $data[0];
				$result['query_id'] = 'default';
			} else {
				$result['provider'] = $data[0];
				$result['query_id'] = $data[1];
			}

			return $result;
		}

		/**
		 * Store properties array for provider
		 */
		public function set_props( $provider, $props, $query_id = 'default' ) {

			if ( ! $query_id ) {
				$query_id = 'default';
			}

			if ( empty( $this->_props[ $provider ] ) ) {
				$this->_props[ $provider ] = array();
			}

			$this->_props[ $provider ][ $query_id ] = $props;
		}

		/**
		 * Store properties array for provider
		 */
		public function add_prop( $provider, $prop, $value, $query_id = 'default' ) {

			if ( ! $query_id ) {
				$query_id = 'default';
			}

			if ( empty( $this->_props[ $provider ] ) ) {
				$this->_props[ $provider ] = array();
			}

			$this->_props[ $provider ][ $query_id ][ $prop ] = $value;
		}
		/**
		 * Query properties provider
		 */
		public function get_query_props( $provider = null, $query_id = 'default' ) {

			if ( ! $provider ) {
				return $this->_props;
			}

			if ( ! $query_id ) {
				$query_id = 'default';
			}

			return isset( $this->_props[ $provider ][ $query_id ] ) ? $this->_props[ $provider ][ $query_id ] : array();
		}

		/**
		 * Force is_ajax_filter to true
		 */
		public function set_is_ajax_filter() {

			$this->is_ajax_filter = true;
		}

		/**
		 * Set current provider info
		 */
		public function set_provider( $provider = '' ) {

			$this->provider = $provider;
		}

		/**
		 * Set current provider from request
		 */
		public function set_provider_from_request( $provider = '' ) {

			if ( preg_match( '/[\/:]/', $provider ) ) {
				$delimiter = strpos( $provider, '/' ) !== false ? '/' : ':';
				$provider_data = explode( $delimiter, $provider, 2 );
				$provider_name = $provider_data[0];
				$provider_id   = $provider_data[1];

				$this->set_provider( $provider_name . '/' . $provider_id );
			} else {
				$this->set_provider( $provider . '/default' );
			}
		}

		/**
		 * Get current provider ID.
		 */
		public function get_current_provider( $return = null ) {

			$provider = false;

			if ( ! empty( $this->provider ) ) {
				$provider = $this->provider;
			} elseif ( $this->is_ajax_filter() && ! empty( $_REQUEST['provider'] ) ) {
				$provider = $_REQUEST['provider'];
			}

			if ( ! $provider ) {
				return false;
			}

			if ( 'raw' === $return ) {
				return $provider;
			}

			$data = $this->decode_provider_data( $provider );

			if ( ! $return ) {
				return $data;
			} else {
				return isset( $data[ $return ] ) ? $data[ $return ] : false;
			}
		}

		/**
		 * Return properties for current query
		 */
		public function get_current_query_props() {

			$data = $this->get_current_provider();

			return $this->get_query_props( $data['provider'], $data['query_id'] );
		}

		/**
		 * Set parsed query var value to request
		 */
		public function set_query_var_to_request( $query_var, $query_var_value ) {

			if ( empty( $query_var_value ) ) {
				return;
			}

			switch ( $query_var ) {
				case 'tax':
				case 'meta':
					foreach ( explode( ';', $query_var_value ) as $data ) {
						preg_match( '/(.+?):(.+)/', $data, $key_value );
						array_shift( $key_value );

						if ( count( $key_value ) < 2 ) {
							continue;
						}

						$key   = str_replace( '!', '|', $key_value[0] ); // replace query var suffix separator
						$value = strpos( $key_value[1], ',' ) ? explode( ',', $key_value[1] ) : $key_value[1];

						$_REQUEST[ '_' . $query_var . '_query_' . $key ] = $value;
					}
					break;

				case 'plain_query':

					$key_data = explode( ':', $query_var_value, 2 );
					$value    = strpos( $key_data[1], ',' ) ? explode( ',', $key_data[1] ) : $key_data[1];
					$_REQUEST[ '_' . $query_var . '_' . $key_data[0] ] = $value;
					break;

				case 'date':
					$_REQUEST['_' . $query_var . '_query_|date'] = $query_var_value;

					break;

				case 'sort':
					$sort_props = array();

					foreach ( explode( ';', $query_var_value ) as $data) {
						$sort_data = explode( ':', $data );

						if ( count( $sort_data ) < 2 ) {
							continue;
						}

						$sort_props[$sort_data[0]] = $sort_data[1];
					}

					$_REQUEST['_sort_standard'] = json_encode( $sort_props );

					break;

				case '_s':
				case 'search':
					$search_key   = '__s_query|search';
					$search_value = $query_var_value;

					if ( strpos( $search_value, '!' ) ) {
						$search_data = explode( '!', $search_value, 2 );
						$search_meta = explode( '=', $search_data[1], 2 );

						if ($search_meta[0] === 'meta' && $search_meta[1]) {
							$search_key   = '_meta_query_' . $search_meta[1] . '|search';
							$search_value = $search_data[0];
						}
					}

					$_REQUEST[ $search_key ] = $search_value;

					break;

				case 'pagenum':
					$_REQUEST['jet_paged'] = $query_var_value;

					break;

				case 'alphabet':
					$_REQUEST['_alphabet_'] = strpos( $query_var_value, ',' ) ? explode( ',', $query_var_value ) : $query_var_value;

					break;

				default:
					$_REQUEST[ '_' . $query_var . '_' ] = $query_var_value;
					break;
			}
		}

		/**
		 * Query
		 */
		public function get_query_from_request( $request = array() ) {

			if ( empty( $request ) ) {
				$request = $_REQUEST;
			}

			$request = apply_filters( 'jet-smart-filters/query/request', $request, $this );

			$this->_query = array(
				'jet_smart_filters' => $this->get_current_provider( 'raw' ),
				'suppress_filters'  => false,
			);

			if ( $this->is_ajax_filter() ) {
				$this->_default_query  = ! empty( $request['defaults'] ) ? $request['defaults'] : array();
				$this->_query_settings = ! empty( $request['settings'] ) ? $request['settings'] : array();
			}

			foreach ( $this->query_vars() as $var ) {
				if ( $this->is_ajax_filter() ) {
					$data = isset( $request['query'] ) ? $request['query'] : array();
				} else {
					$data = $request;
				}

				if ( ! $data ) {
					$data = array();
				}

				array_walk( $data, function( $value, $key ) use ( $var ) {

					if ( $key === $var || strpos( $key, '_' . $var ) !== false ) {
						switch ( $var ) {
							case 'tax_query':
								$this->add_tax_query_var( $value, $this->clear_key( $key, $var ) );

								break;

							case 'date_query':
								$this->add_date_query_var( $value );

								break;

							case 'meta_query':
								$key         = $this->clear_key( $key, $var );
								$with_suffix = explode( '|', $key );
								$suffix      = false;

								if ( isset( $with_suffix[1] ) ) {
									$key    = $with_suffix[0];
									$suffix = $this->process_suffix( $with_suffix[1] );
									$value  = $this->apply_suffix( $suffix, $value );
								}

								$this->add_meta_query_var( $value, $key, $suffix );

								break;

							case '_s':
								if ( false !== strpos( $key, '__s_query' ) ) {
									$this->_query['s'] = $value;
								}

								break;

							case 'sort':
								$sort_data = array();

								if ( ! is_array( $value ) ) {
									$value = array( $value );
								}

								foreach ( $value as $data ) {
									$data = json_decode( wp_unslash( $data ), true );

									if ( ! $data ) {
										continue;
									}

									if ( ! empty( $data['orderby'] ) ) {
										switch ( $data['orderby'] ) {
											case 'price':
												$data['orderby']  = 'meta_value_num';
												$data['meta_key'] = '_price';
	
												break;
	
											case 'sales_number':
												$data['orderby']  = 'meta_value_num';
												$data['meta_key'] = 'total_sales';
	
												break;
	
											case 'rating':
												$data['orderby']  = 'meta_value_num';
												$data['meta_key'] = '_wc_average_rating';
	
												break;
	
											case 'reviews_number':
												$data['orderby']  = 'meta_value_num';
												$data['meta_key'] = '_wc_review_count';
	
												break;
										}
									}

									array_push( $sort_data, $data );
								}

								if ( count( $sort_data ) === 1 ) {
									$data = $sort_data[0];

									if ( $data['orderby'] === 'clause_value' ) {
										$this->_query['orderby'] = array(
											$data['meta_key'] => $data['order']
										);
									} else {
										foreach ( array_keys( $data ) as $key ) {
											$this->_query[$key] = $data[$key];
										}
									}
								}
								if ( count( $sort_data ) > 1 ) {
									$this->_query['orderby'] = array();

									foreach ( $sort_data as $data ) {
										$key   = $data['orderby'];
										$order = $data['order'];

										if ( in_array( $key, ['meta_value', 'meta_value_num'] ) ) {
											$key =  $data['meta_key'] . '_clause';

											if ( ! isset( $this->_query['meta_query'] ) ) {
												$this->_query['meta_query'] = array();
											}

											$this->_query['meta_query'][$key] = array(
												'key' => $data['meta_key']
											);
										}

										if ( $key === 'clause_value' ) {
											$key = $data['meta_key'];
										}

										$this->_query['orderby'][$key] = $order;
									}
								}

								break;

							case 'alphabet':
								$this->_query[ $var ] = $value;
								//add_filter( 'posts_where', array( $this, 'set_query_where' ), 10, 2 );

								break;

							case 'plain_query':

								if ( $key === $var ) {
								//	$var = '_' . $var . '_' . 
								}

								$this->_query[ $this->clear_key( $key, $var ) ] = $value;
								break;


							default:

								$this->_query[ $var ] = apply_filters(
									'jet-smart-filters/query/add-var',
									$value,
									$key,
									$var,
									$this
								);

								break;
						}
					}
				} );
			}

			// Page number
			$paged = false;

			if ( $this->is_ajax_filter() ) {
				if ( isset( $request['paged'] ) && 'false' !== $request['paged'] ) {
					$paged = absint( $request['paged'] );
				}
			} else {
				if ( isset( $request['jet_paged'] ) ) {
					$paged = absint( $request['jet_paged'] );
				}
			}

			if ( $paged ) {
				$this->_query['paged'] = $paged;
			}

			$this->_query = apply_filters( 'jet-smart-filters/query/final-query', $this->_query );
			return $this->_query;
		}

		/**
		 * Clear key from varaible prefix
		 */
		public function clear_key( $key, $query_var ) {

			return str_replace( '_' . $query_var . '_', '', $key );
		}

		/**
		 * Return raw key
		 */
		public function raw_key( $key, $query_var ) {

			$key        = str_replace( '_' . $query_var . '_', '', $key );
			$has_filter = explode( '|', $key );

			if ( isset( $has_filter[1] ) ) {
				return $has_filter[0];
			} else {
				return $key;
			}
		}

		/**
		 * Get taxonomy operator from value
		 */
		public function get_operator( &$data ) {

			$operator = false;

			if ( ! is_array( $data ) ) {
				return $operator;
			}

			foreach ( $data as $key => $value ) {
				if ( false !== strpos( $value, 'operator_' ) ) {
					$operator = str_replace( 'operator_', '', $value );
					unset( $data[ $key ] );
				}
			}

			return $operator;
		}

		/**
		 * Add tax query varibales
		 */
		public function add_tax_query_var( $value, $key ) {

			$operator = $this->get_operator( $value );
			$tax_query = isset( $this->_query['tax_query'] ) ? $this->_query['tax_query'] : array();

			if ( ! isset( $tax_query[ $key ] ) ) {
				$tax_query[ $key ] = array(
					'taxonomy' => $key,
					'field'    => 'term_id',
					'terms'    => $value,
				);
			} else {
				if ( ! is_array( $value ) ) {
					$value = array( $value );
				}

				if ( ! is_array( $tax_query[ $key ]['terms'] ) ) {
					$tax_query[ $key ]['terms'] = array( $tax_query[ $key ]['terms'] );
				}

				$tax_query[ $key ]['terms'] = array_merge( $tax_query[ $key ]['terms'], $value );
			}

			if ( $operator ) {
				$tax_query[ $key ]['operator'] = $operator;

				if ( $operator === 'AND' ) {
					$tax_query[ $key ]['include_children'] = false;
				}
			}

			if ( ! empty( $this->_default_query['tax_query'] ) ) {
				$this->_query['tax_query'] = array_merge( $this->_default_query['tax_query'], $tax_query );
			} else {
				$this->_query['tax_query'] = $tax_query;
			}
		}

		/**
		 * Add date query varibales
		 */
		public function add_date_query_var( $value ) {

			$current_query = array(
				'inclusive' => true
			);

			if ( str_contains( $value, '-' ) ) {
				$value = explode( '-', $value );

				if ( ! empty( $value[0] ) ) {
					$after       = explode( '.', $value[0] );
					$after_query = array(
						'year'  => isset( $after[0] ) ? $after[0] : false,
						'month' => isset( $after[1] ) ? $after[1] : false,
						'day'   => isset( $after[2] ) ? $after[2] : false,
					);
				} else {
					$after_query = false;
				}

				if ( ! empty( $value[1] ) ) {
					$before       = explode( '.', $value[1] );
					$before_query = array(
						'year'  => isset( $before[0] ) ? $before[0] : false,
						'month' => isset( $before[1] ) ? $before[1] : false,
						'day'   => isset( $before[2] ) ? $before[2] : false,
					);
				} else {
					$before_query = false;
				}

				if ( $after_query ) {
					$current_query['after'] = $after_query;
				}
				if ( $before_query ) {
					$current_query['before'] = $before_query;
				}
			} else {
				$date = explode( '.', $value );

				$current_query['year']  = isset( $date[0] ) ? $date[0] : false;
				$current_query['month'] = isset( $date[1] ) ? $date[1] : false;
				$current_query['day']   = isset( $date[2] ) ? $date[2] : false;
			}

			if ( !empty( $this->_default_query['date_query'] ) ) {
				$this->_query['date_query'] = array_merge( $this->_default_query['date_query'], $current_query );
			} else {
				$this->_query['date_query'] = $current_query;
			}
		}

		/**
		 * Process suffix data
		 */

		public function process_suffix( $suffix ) {

			$suffix_data = array();

			foreach ( explode( ',', $suffix ) as $item ) {
				if ( in_array( $item, ['search', 'range', 'check-range', 'rating', 'date'] ) ) {
					$suffix_data['filter_type'] = $item;
				}

				if ( 'is_custom_checkbox' === $item ) {
					$suffix_data['is_custom_checkbox'] = true;
				}

				if ( strpos( $item, '-' ) ) {
					$exploded_item = explode( '-', $item );
					$suffix_data[$exploded_item[0]] = $exploded_item[1];
				}
			}

			return $suffix_data;
		}

		/**
		 * Apply value suffix
		 */
		public function apply_suffix( $suffix, $value ) {

			if ( ! isset( $suffix['filter_type'] ) ) {
				return $value;
			}

			switch ( $suffix['filter_type'] ) {
				case 'range':
					return explode( '_', $value );

				case 'check-range':
					$result = array();

					if ( is_array( $value ) ) {
						foreach ( $value as $row ) {
							$result[] = explode( '_', $row );
						}
					} else {
						$result[] = explode( '_', $value );
					}

					return $result;

				default:
					return apply_filters( 'jet-smart-filters/apply-suffix/' . $suffix['filter_type'], $value, $this );
			}
		}

		/**
		 * Add tax query varibales
		 */
		public function add_meta_query_var( $value, $key, $additional_options = array() ) {

			$meta_query         = array();
			$filter_type        = isset( $additional_options['filter_type'] ) ? $additional_options['filter_type'] : false;
			$is_custom_checkbox = isset( $additional_options['is_custom_checkbox'] ) ? true : false;
			$keys               = explode( ',', $key );

			if ( count( $keys ) > 1 ) {
				$meta_query['relation'] = 'OR';
			}

			foreach ($keys as $key) {
				$key = trim( $key );
				
				if ( 'check-range' === $filter_type || ( $is_custom_checkbox && is_array( $value ) ) ) {
					$nested_query = array(
						'relation' => 'OR',
					);
	
					foreach ( $value as $value_row ) {
						$nested_query[] = $this->prepare_meta_query_row( $value_row, $key, $additional_options );
					}
	
					$meta_query[] = $nested_query;
				} else {
					$meta_query[] = $this->prepare_meta_query_row( $value, $key, $additional_options );
				}
			}

			if ( ! isset( $this->_query['meta_query'] ) ) {
				$this->_query['meta_query'] = array();
			}

			if ( isset( $meta_query['relation'] ) ) {
				$this->_query['meta_query'][] = $meta_query;
			} else {
				$this->_query['meta_query'] = array_merge( $this->_query['meta_query'], $meta_query );
			}

			if ( !empty( $this->_default_query['meta_query'] ) ) {
				$this->_query['meta_query'] = array_merge( $this->_default_query['meta_query'], $this->_query['meta_query'] );
			}
		}

		/**
		 * Preapre single meta query item
		 */
		public function prepare_meta_query_row( $value, $key, $additional_options = array() ) {

			$filter_type        = isset( $additional_options['filter_type'] ) ? $additional_options['filter_type'] : false;
			$is_custom_checkbox = isset( $additional_options['is_custom_checkbox'] ) ? true : false;
			$compare_operand    = isset( $additional_options['compare'] ) ? $additional_options['compare'] : 'equal';
			$custom_type        = false;

			if ( is_array( $value ) ) {
				$compare = 'IN';
			} else {
				switch ( $compare_operand ) {
					case 'less' :
						$compare     = '<=';
						$custom_type = 'DECIMAL(16,4)';

						break;

					case 'greater' :
						$compare     = '>=';
						$custom_type = 'DECIMAL(16,4)';

						break;

					case 'like' :
						$compare = 'LIKE';

						break;

					case 'in' :
						$compare = 'IN';

						break;

					case 'between' :
						$compare = 'BETWEEN';

						break;

					case 'exists' :
						$compare = 'EXISTS';

						break;

					case 'regexp' :
						$compare = 'REGEXP';

						break;

					default:
						$compare = '=';

						break;
				}
			}

			$current_row = array(
				'key'     => $key,
				'value'   => $value,
				'compare' => $compare,
			);

			if ( $filter_type ) {
				switch ( $filter_type ) {
					case 'search':
						$current_row['value']   = stripslashes( $value );
						$current_row['compare'] = 'LIKE';
						$current_row['type']    = 'CHAR';

						break;

					case 'range':
					case 'check-range':
						$current_row['compare'] = 'BETWEEN';
						$current_row['type']    = 'DECIMAL(16,4)';

						break;

					case 'date':
						$start_date = false;
						$end_date   = false;

						if ( strpos( $value, '-' ) !== false ) {
							$date_value = explode( '-', $value );
						} else {
							$date_value = array( $value, $value );
						}

						if ( ! empty( $date_value[0] ) ) {
							$start_date = strtotime( str_replace( '.', '-', $date_value[0] ) );
						}
						if ( ! empty( $date_value[1] ) ) {
							$end_date = strtotime( str_replace( '.', '-', $date_value[1] ) ) + ( 24*60*60 ) -1;
						}

						if ( $start_date !== false && $end_date !== false ) {
							$current_row['value'] = array( $start_date, $end_date );
							$current_row['compare'] = 'BETWEEN';
						} else if ( $start_date !== false ) {
							$current_row['value'] = $start_date;
							$current_row['compare'] = '>=';
						} else if ( $end_date !== false ) {
							$current_row['value'] = $end_date;
							$current_row['compare'] = '<=';
						}

						$current_row['type'] = 'NUMERIC';

						break;

					case 'rating':
						$current_row['type'] = 'DECIMAL(16,4)';

						break;
				}
			}

			if ( $is_custom_checkbox ) {
				$value = preg_quote( $value );
				$regex = '\:[\'\"]?' . $value . '[\'\"]?;s:4:"true"|\:[\'\"]?' . $value . '[\'\"]?;[^s]';
				$current_row['value'] = $regex;
				$current_row['compare'] = 'REGEXP';
			}

			if ( $custom_type ) {
				$current_row['type'] = $custom_type;
			}

			return apply_filters( 'jet-smart-filters/query/meta-query-row', $current_row, $this, $additional_options );
		}

		public function set_query_where( $where, $query ) {

			if ( $query->get( 'jet_smart_filters' ) && $query->get( 'alphabet' ) ) {
				$letter = $query->get( 'alphabet' );

				if ( is_array( $letter ) ) {
					$letter = implode( '|', $letter );
				}

				$letter = mb_strtolower($letter);

				global $wpdb;
				$where .= " AND LOWER( " . $wpdb->posts . ".post_title) REGEXP '^($letter)'";

				//add_action( 'posts_selection', array( $this, 'remove_query_where' ) );
			}

			return $where;
		}

		public function remove_query_where() {

			remove_filter( 'posts_where', array( $this, 'set_query_where' ) );
		}
	}
}
