<?php
namespace Jet_Engine\Query_Builder\Query_Editor;

use Jet_Engine\Query_Builder\Manager;

class Repeater_Query extends Base_Query {

	/**
	 * Qery type ID
	 */
	public function get_id() {
		return 'repeater';
	}

	/**
	 * Qery type name
	 */
	public function get_name() {
		return __( 'Repeater Query', 'jet-engine' );
	}

	/**
	 * Returns Vue component name for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_name() {
		return 'jet-repeater-query';
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_data() {

		if ( jet_engine()->options_pages ) {
			add_filter( 'jet-engine/meta-boxes/fields-for-select/name', array( $this, 'name_format' ), 10, 3 );
			$options_fields = $this->remap_options( jet_engine()->options_pages->get_options_for_select( 'repeater', 'blocks' ) );
			remove_filter( 'jet-engine/meta-boxes/fields-for-select/name', array( $this, 'name_format' ), 10, 3 );
		} else {
			$options_fields = array();
		}

		if ( jet_engine()->meta_boxes ) {
			add_filter( 'jet-engine/meta-boxes/fields-for-select/name', array( $this, 'name_format' ), 10, 3 );
			$meta_fields = $this->remap_options( jet_engine()->meta_boxes->get_fields_for_select( 'repeater', 'blocks' ) );
			remove_filter( 'jet-engine/meta-boxes/fields-for-select/name', array( $this, 'name_format' ), 10, 3 );
		} else {
			$meta_fields = array();
		}

		$sources = array(
			array(
				'value' => 'jet_engine',
				'label' => __( 'JetEngine Meta Field', 'jet-engine' ),
			),
			array(
				'value' => 'jet_engine_option',
				'label' => __( 'JetEngine Option Field', 'jet-engine' ),
			),
			array(
				'value' => 'custom',
				'label' => __( 'Custom Field', 'jet-engine' ),
			),
			
		);

		$sources = array_merge( array( array( 'value' => '', 'label' => __( 'Select source...', 'jet-engine' ) ) ), $sources );

		return apply_filters( 'jet-engine/query-builder/types/repeater-query/data', array(
			'meta_fields'    => $meta_fields,
			'options_fields' => $options_fields,
			'sources'        => $sources,
		) );

	}

	public function name_format( $name, $field, $parent_slug ) {
		return $parent_slug . '::' . $name;
	}

	public function remap_options( $options = array() ) {
		return array_map( function( $option ) {
			
			if ( ! empty( $option['values'] ) ) {
				$option['options'] = $option['values'];
				unset( $option['values'] );
			}

			return $option;

		}, $options ); 
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_template() {
		ob_start();
		include Manager::instance()->component_path( 'templates/admin/types/repeater.php' );
		return ob_get_clean();
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_file() {
		return Manager::instance()->component_url( 'assets/js/admin/types/repeater.js' );
	}

}
