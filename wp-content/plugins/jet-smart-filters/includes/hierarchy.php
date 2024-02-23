<?php
/**
 * Data class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Hierarchy' ) ) {

	class Jet_Smart_Filters_Hierarchy {

		protected $filter_id  = 0;
		protected $depth      = 0;
		protected $values     = array();
		protected $args       = array();
		protected $filter     = null;
		protected $single_tax = null;
		protected $hierarchy  = null;

		/**
		 * Class constructor
		 */
		public function __construct( $filter = 0, $depth = 0, $values = array(), $args = array(), $use_query_args = false ) {

			if ( isset( $args['layout_options'] ) ) {
				$layout_options = $args['layout_options'];
				unset( $args['layout_options'] );
				$args = array_merge( $args, $layout_options );
			}

			if ( is_integer( $filter ) ) {
				$this->filter_id = $filter;
				$this->filter    = jet_smart_filters()->filter_types->get_filter_instance(
					$filter,
					null,
					$args
				);

			} else {
				$this->filter_id = $filter->get_filter_id();
				$this->filter    = $filter;
			}

			$this->depth     = $depth;
			$this->values    = $values;
			$this->args      = $args;
			$this->hierarchy = $this->get_hierarchy();

			if ( $use_query_args ) {
				$this->update_values_from_query_args();
			}
		}

		/**
		 * Returns current filter hieararchy map or false
		 */
		public function get_hierarchy() {

			if ( $this->hierarchy )
				return $this->hierarchy;

			$hierarchy = get_post_meta( $this->filter_id, '_ih_source_map', true );

			if ( empty( $hierarchy ) ) {
				return false;
			}

			$result = array();

			foreach ( array_values( $hierarchy ) as $depth => $data ) {

				$result[] = array(
					'depth'       => $depth,
					'tax'         => $data['tax'],
					'label'       => $data['label'],
					'placeholder' => $data['placeholder'],
					'options'     => false,
				);

			}

			return $result;
		}

		/**
		 * Returns hiearachy evels data starting from $this->depth
		 */
		public function get_levels() {
			if ( empty( $this->hierarchy ) ) {
				return;
			}

			$result     = array();
			$filter     = $this->filter;
			$from_depth = ( false !== $this->depth ) ? $this->depth : 0;

			for ( $i = $from_depth; $i <= count( $this->hierarchy ); $i++ ) {
				$level = ! empty( $this->hierarchy[ $i ] ) ? $this->hierarchy[ $i ] : false;

				if ( ! $level ) {
					continue;
				}

				$args       = $filter->get_args();
				$show_label = ! empty( $args['show_label'] ) ? filter_var( $args['show_label'], FILTER_VALIDATE_BOOLEAN ) : false;

				$args['depth']           = $level['depth'];
				$args['query_var']       = $level['tax'];
				$args['placeholder']     = ! empty( $level['placeholder'] ) ? $level['placeholder'] : __( 'Select...', 'jet-smart-filters' );
				$args['max_depth']       = count( $this->hierarchy ) - 1;
				$args['options']         = array();
				$args['filter_label']    = $show_label && ! empty( $level['label'] ) ? $level['label'] : '';
				$args['display_options'] = ! empty( $this->args['display_options'] ) ? $this->args['display_options'] : array();

				if ( isset( $this->values[$i]['value'] ) ) {
					$args['current_value'] = $this->values[$i]['value'];
				}

				if ( false === $this->depth ) {
					if ( $i <= count( $this->values ) ) {
						$args['options'] = $this->get_level_options( $i );
					}
				} elseif ( $i === $from_depth ) {
					$args['options'] = $this->get_level_options( $i );
				}

				$result[ 'level_' . $i ] = $this->filter->get_rendered_template( $args );
			}

			return $result;
		}

		/**
		 * Returns terms for options
		 */
		public function get_level_options( $i = 0 ) {

			$result     = array();
			$curr_level = isset( $this->hierarchy[$i] ) ? $this->hierarchy[$i] : false;

			if ( ! $curr_level ) {
				return $result;
			}

			$parent          = false;
			$prepared_values = array();

			/**
			 * Ensure we left only latest child of each taxonomy
			 */
			for ( $level_index = 0; $level_index < $i; $level_index++ ) {
				$level_val = $this->values[ $level_index ];
				$prepared_values[ $level_val['tax'] ] = $level_val['value'];
			}

			/**
			 * Сheck if subterm and define parent
			 */
			for ( $level_index = 0; $level_index < count( $this->hierarchy ); $level_index++ ) {
				if ( $level_index === $i ) {
					continue;
				}

				if ( $this->hierarchy[$level_index]['tax'] === $curr_level['tax'] ) {
					$parent = 0;

					for ( $parent_value_index = $curr_level['depth'] - 1; $parent_value_index >= 0 ; $parent_value_index--) {
						if ( ! isset( $this->values[$parent_value_index] ) ) {
							break;
						}

						if ( $this->values[$parent_value_index]['tax'] === $curr_level['tax'] ) {
							$parent = $this->values[$parent_value_index]['value'];

							break;
						}
					}

					break;
				}
			}

			if ( $parent !== false ) {
				$result = jet_smart_filters()->data->get_terms_for_options(
					$curr_level['tax'],
					false,
					array(
						'parent' => $parent,
					)
				);
			} else {
				global $wpdb;

				$from  = '';
				$on    = '';
				$where = '';
				$glue  = '';
				$index = 0;
				$ids   = false;

				foreach ( $prepared_values as $tax => $value ) {
					if ( $value ) {
						$posts_table              = $wpdb->posts;
						$term_relationships_table = $wpdb->term_relationships;
						$value                    = absint( $value );
						$term_taxonomy            = get_term( $value );
						$term_taxonomy_id         = ! is_wp_error($term_taxonomy) ? $term_taxonomy->term_taxonomy_id : false;

						if ( 0 === $index ) {
							$from  .= "SELECT ID FROM $posts_table AS p
										LEFT JOIN $term_relationships_table AS t0 ON (p.ID = t0.object_id)";
							$where .= " WHERE t0.term_taxonomy_id = {$term_taxonomy_id}
										AND p.post_status = 'publish'";
						} else {
							$from  .= " INNER JOIN $term_relationships_table AS t{$index}";
							$where .= " AND t{$index}.term_taxonomy_id = {$term_taxonomy_id}";
							$prev   = $index - 1;
							$on    .= "{$glue}t{$prev}.object_id = t{$index}.object_id";
							$glue   = ' AND ';
						}

						$index++;
					}
				}

				if ( ! empty( $on ) ) {
					$on = ' ON ( ' . $on . ' )';
				}

				if ( $from ) {
					$ids = $wpdb->get_results( $from . $on . $where, OBJECT_K );

					if ( empty( $ids ) ) {
						return $result;
					}
				}

				$terms_args = array();

				if ( ! empty( $ids ) ) {
					$terms_args['object_ids'] = array_keys( $ids );
				}

				$result = jet_smart_filters()->data->get_terms_for_options(
					$curr_level['tax'],
					false,
					$terms_args
				);
			}

			return $result;
		}

		/**
		 * Check if all previous hierarchy levels has same taxonomy.
		 * In this case we need get only direct children of latest value
		 */
		public function is_single_tax_hierarchy() {

			if ( null !== $this->single_tax ) {
				return $this->single_tax;
			}

			$single_tax = true;
			$tax        = null;
			$to_depth   = ( false !== $this->depth ) ? $this->depth : count( $this->values );

			for ( $i = 0; $i <= $to_depth; $i++ ) {
				$level = ! empty( $this->hierarchy[ $i ] ) ? $this->hierarchy[ $i ] : false;

				if ( ! $level ) {
					continue;
				}

				if ( ! $tax ) {
					$tax = $level['tax'];
				} elseif ( $tax !== $level['tax'] ) {
					$single_tax = false;
				}
			}

			if ( $single_tax ) {
				$this->single_tax = $tax;
			} else {
				$this->single_tax = false;
			}

			return $this->single_tax;
		}

		private function update_values_from_query_args() {

			$tax_args = array();

			if ( is_category() || is_tag() || is_tax() ) {
				$current_queried                      = get_queried_object();
				$tax_args[$current_queried->taxonomy] = $current_queried->term_taxonomy_id;
			}

			$query_args   = jet_smart_filters()->query->get_query_args();
			$provider_key = $this->args['content_provider'] . '/' . $this->args['query_id'];

			if (
				! empty( $query_args )
				&& isset( $query_args['jet_smart_filters'] )
				&& $query_args['jet_smart_filters'] === $provider_key
				&& isset( $query_args['tax_query'] )
			) {
				foreach ( $query_args['tax_query'] as $tax ) {
					$tax_args[$tax['taxonomy']] = is_array( $tax['terms'] )
						? end( $tax['terms'] )
						: $tax['terms'];
				}
			}

			// Get subterms hierarchical chain
			$subterms = array();

			for ( $i = 0; $i < count( $this->hierarchy ); $i++ ) { 
				$tax = $this->hierarchy[$i]['tax'];

				if ( ! isset( $tax_args[$tax] ) || isset( $subterms[$tax] ) ) {
					continue;
				}

				$term_id = $tax_args[$tax];

				for ( $j = $i + 1; $j < count( $this->hierarchy ); $j++ ) {
					if ( $tax === $this->hierarchy[$j]['tax'] ) {
						$ids = array_reverse( get_ancestors( $term_id, $tax ) );
						array_push( $ids, $term_id );

						$subterms[$tax] = $ids;
						unset( $tax_args[$tax] );

						break;
					}
				}
			}

			// Set values from query
			$values_from_query = array();

			for ( $i = 0; $i < count( $this->hierarchy ); $i++ ) { 
				$tax = $this->hierarchy[$i]['tax'];

				if ( ! empty( $subterms[$tax] ) ) {
					$values_from_query[$i] = array(
						'tax'   => $tax,
						'value' => array_shift( $subterms[$tax] )
					);
				} else if ( ! empty( $tax_args[$tax] ) ) {
					$values_from_query[$i] = array(
						'tax'   => $tax,
						'value' => $tax_args[$tax]
					);
				} else {
					break;
				}
			}

			$this->values = array_merge( $this->values, $values_from_query );
		}
	}
}
