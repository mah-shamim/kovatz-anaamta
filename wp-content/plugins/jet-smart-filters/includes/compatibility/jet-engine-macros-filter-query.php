<?php
/**
 * Compatibility filters and actions
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Macros_Filter_Query extends Jet_Engine_Base_Macros {

	public function macros_tag() {
		return 'jsf_filter_query';
	}

	public function macros_name() {
		return __( 'JetSmartFilters Query', 'jet-smart-filters' );
	}

	public function macros_args() {
		return array(
			'query_var_type' => array(
				'label'       => __( 'Type', 'jet-smart-filters' ),
				'type'        => 'select',
				'default'     => 'query_var',
				'description' => __( 'Where in filters query we need to look for value.', 'jet-smart-filters' ),
				'options'     => array(
					'query_var'  => __( 'Plain Query Var', 'jet-smart-filters' ),
					'tax'        => __( 'Taxonomy', 'jet-smart-filters' ),
					'date'       => __( 'Date', 'jet-smart-filters' ),
					'meta'       => __( 'Meta field', 'jet-smart-filters' ),
				),
			),
			'query_var_name' => array(
				'label'       => __( 'Variable Name', 'jet-smart-filters' ),
				'description' => __( 'Name of query variable, meta field name or taxonmy slug to get value for', 'jet-smart-filters' ),
				'default'     => '',
				'type'        => 'text',
			),
			'date_var_name' => array(
				'label'       => __( 'Date Variable', 'jet-smart-filters' ),
				'description' => __( 'Variable from Date query to get value for', 'jet-smart-filters' ),
				'type'        => 'select',
				'default'     => 'after',
				'options'     => array(
					'after'  => __( 'After', 'jet-smart-filters' ),
					'before' => __( 'Before', 'jet-smart-filters' ),
				),
			),
			'result_format' => array(
				'label'       => __( 'Result Format', 'jet-smart-filters' ),
				'description' => __( 'How returned value should be prepared before use', 'jet-smart-filters' ),
				'type'        => 'select',
				'default'     => 'plain',
				'options'     => array(
					'plain' => __( 'Plain', 'jet-smart-filters' ),
					'sql'   => __( 'Prepare for SQL', 'jet-smart-filters' ),
				),
			),
		);
	}

	public function macros_callback( $args = array() ) {

		$type     = ! empty( $args['query_var_type'] ) ? $args['query_var_type'] : 'query_var';
		$var      = ! empty( $args['query_var_name'] ) ? $args['query_var_name'] : '';
		$date_var = ! empty( $args['date_var_name'] ) ? $args['date_var_name'] : 'after';
		$format   = ! empty( $args['result_format'] ) ? $args['result_format'] : 'plain';

		$query  = jet_smart_filters()->query->get_query_args();
		$result = '';

		switch ( $type ) {
			case 'tax':

				if ( ! $var ) {
					return;
				}

				$tax_query = isset( $query['tax_query'] ) ? $query['tax_query'] : [];

				foreach ( $tax_query as $tax_query_row ) {

					if ( is_array( $tax_query_row ) 
						&& isset( $tax_query_row['taxonomy'] )
						&& $var === $tax_query_row['taxonomy']
					) {
						$result = $tax_query_row['terms'];
					}

				}

				break;

			case 'date':

				if ( ! in_array( $date_var, [ 'after', 'before' ] ) ) {
					$date_var = 'after';
				}

				$date_query = isset( $query['date_query'] ) ? $query['date_query'] : [];
				$result     = isset( $date_query[ $date_var ] ) ? $date_query[ $date_var ] : '';

				if ( is_array( $result ) ) {
					$result = implode( '-', $result );
				}

				break;

			case 'meta':

				if ( ! $var ) {
					return;
				}

				$meta_query = isset( $query['meta_query'] ) ? $query['meta_query'] : [];

				foreach ( $meta_query as $meta_query_row ) {

					if ( is_array( $meta_query_row ) 
						&& isset( $meta_query_row['key'] )
						&& $var === $meta_query_row['key']
					) {
						$result = $meta_query_row['value'];
					}

				}

				break;

			default:

				if ( ! $var ) {
					return;
				}
				
				$result = isset( $query[ $var ] ) ? $query[ $var ] : '';
				break;
		}

		// Prepare results
		switch ( $format ) {
			case 'sql':
				if ( is_array( $result ) ) {
					$result = array_map( function( $item ) {
						return sprintf( '\'%1$s\'', $item );
					}, $result );
				} else {
					$result = sprintf( '\'%1$s\'', $result );
				}

				break;

		}

		if ( is_array( $result ) ) {
			$result = implode( ',', $result );
		}

		return $result;

	}
}
