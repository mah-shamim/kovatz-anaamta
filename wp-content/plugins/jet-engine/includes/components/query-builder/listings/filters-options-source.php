<?php
namespace Jet_Engine\Query_Builder\Listings;

use Jet_Engine\Query_Builder\Manager;

class Filters_Options_Source {

	public function __construct() {
		add_filter( 'jet-smart-filters/post-type/options-data-sources', array( $this, 'register_source' ) );
		add_filter( 'jet-smart-filters/post-type/meta-fields-settings', array( $this, 'register_controls' ) );
		add_filter( 'jet-smart-filters/filters/filter-options', array( $this, 'apply_options' ), 10, 3 );
	}

	public function register_source( $sources = array() ) {
		$sources['query_builder'] = __( 'JetEngine Query Builder', 'jet-engine' );
		return $sources;
	}

	public function register_controls( $fields ) {

		$insert = array(
			'_query_builder_query' => array(
				'title'   => __( 'Select Query', 'jet-engine' ),
				'type'    => 'select',
				'element' => 'control',
				'options' => Manager::instance()->get_queries_for_options(),
				'conditions' => array(
					'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
					'_data_source' => 'query_builder',
				),
			),
			'_query_builder_value_prop' => array(
				'title'   => __( 'Property to get Value from', 'jet-engine' ),
				'type'    => 'text',
				'element' => 'control',
				'value' => 'ID',
				'conditions' => array(
					'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
					'_data_source' => 'query_builder',
				),
			),
			'_query_builder_label_prop' => array(
				'title'   => __( 'Property to get Label from', 'jet-engine' ),
				'type'    => 'text',
				'element' => 'control',
				'value' => 'post_title',
				'conditions' => array(
					'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
					'_data_source' => 'query_builder',
				),
			),
		);

		if ( isset( jet_smart_filters()->utils ) && is_callable( array( jet_smart_filters()->utils, 'add_control_condition' ) ) ) {
			$fields = jet_smart_filters()->utils->add_control_condition( 
				$fields, 
				'_source_color_image_input',
				'_data_source!',
				'query_builder'
			);
		}

		$fields = $this->insert_after( $fields, '_data_source', $insert );

		$color_image_fields = array(
			'_query_builder_color_prop' => array(
				'title'   => __( 'Property to get Color from', 'jet-engine' ),
				'type'    => 'text',
				'element' => 'control',
				'value' => '',
				'conditions' => array(
					'_filter_type'      => array( 'color-image' ),
					'_color_image_type' => 'color',
					'_data_source'      => 'query_builder',
				),
			),
			'_query_builder_image_prop' => array(
				'title'   => __( 'Property to get Image from', 'jet-engine' ),
				'type'    => 'text',
				'element' => 'control',
				'value' => '',
				'conditions' => array(
					'_filter_type'      => array( 'color-image' ),
					'_color_image_type' => 'image',
					'_data_source'      => 'query_builder',
				),
			),
		);

		$fields = $this->insert_after( $fields, '_color_image_type', $color_image_fields );

		return $fields;
	}

	public function apply_options( $options, $filter_id, $filter ) {

		$source = get_post_meta( $filter_id, '_data_source', true );

		if ( 'query_builder' !== $source ) {
			return $options;
		}

		$query_id = get_post_meta( $filter_id, '_query_builder_query', true );

		if ( ! $query_id ) {
			return $options;
		}

		$query = Manager::instance()->get_query_by_id( $query_id );
		
		if ( ! $query ) {
			return $options;
		}

		$type        = get_post_meta( $filter_id, '_filter_type', true );
		$value_field = get_post_meta( $filter_id, '_query_builder_value_prop', true );
		$label_field = get_post_meta( $filter_id, '_query_builder_label_prop', true );
		$color_field = get_post_meta( $filter_id, '_query_builder_color_prop', true );
		$image_field = get_post_meta( $filter_id, '_query_builder_image_prop', true );

		if ( ! $value_field || ! $label_field ) {
			return $options;
		}

		$items       = $query->get_items();
		$new_options = array();

		foreach ( $items as $item ) {

			$value = isset( $item->$value_field ) ? $item->$value_field : false;
			$label = isset( $item->$label_field ) ? $item->$label_field : false;

			if ( ! $this->is_valid_value( $value ) || ! $this->is_valid_value( $label ) ) {
				continue;
			}

			if ( 'color-image' === $type ) {

				$color = false;
				$image = false;

				if ( $color_field && isset( $item->$color_field ) ) {
					$color = $this->is_valid_value( $item->$color_field ) ? $item->$color_field : false;
				}

				if ( $image_field && isset( $item->$image_field ) ) {
					$image = $this->is_valid_value( $item->$image_field ) ? $item->$image_field : false;
				}

				$new_options[ $item->$value_field ] = array(
					'label' => $item->$label_field,
					'color' => $color,
					'image' => $image,
				);
			} else {
				$new_options[ $item->$value_field ] = $item->$label_field;
			}

		}

		if ( 'select' === $type ) {

			$placeholder = get_post_meta( $filter_id, '_placeholder', true );

			if ( ! $placeholder ) {
				$placeholder = __( 'Select...', 'jet-engine' );
			}

			$new_options = array( '' => $placeholder ) + $new_options;

		}

		if ( 'radio' === $type ) {
			
			$add_all_option   = filter_var( get_post_meta( $filter_id, '_add_all_option', true ), FILTER_VALIDATE_BOOLEAN );
			$all_option_label = $add_all_option ? get_post_meta( $filter_id, '_all_option_label', true ) : false;

			if ( $all_option_label ) {
				$new_options = array( 'all' => $all_option_label ) + $new_options;
			}

		}

		return $new_options;

	}

	public function is_valid_value( $value ) {

		if ( is_array( $value ) ) {
			return false;
		}
		
		$disallowed = apply_filters( 
			'jet-engine/query-builder/filter-options-source/disallowe-values', 
			array( false, '', null )
		);

		return ( ! in_array( $value, $disallowed, true ) ? true : false );
	}

	public function insert_after( $source = array(), $after = null, $insert = array() ) {
		return \Jet_Engine_Tools::insert_after( $source, $after, $insert );
	}

}
