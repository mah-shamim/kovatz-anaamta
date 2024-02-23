<?php
/**
 * Repeater Query type compatibility for ACF
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_ACF_Repeater_Query class
 */
class Jet_Engine_ACF_Repeater_Query {

	private $acf_package = null;

	public function __construct( $acf_package ) {
		$this->acf_package = $acf_package;
		add_filter( 'jet-engine/query-builder/types/repeater-query/data', [ $this, 'register_source' ] );
		add_action( 'jet-engine/query-builder/repeater/controls', [ $this, 'register_editor_controls' ] );
		add_filter( 'jet-engine/query-builder/types/repeater-query/fields/acf_field', [ $this, 'get_repeater_sub_fields' ], 10, 2 );
		add_filter( 'jet-engine/query-builder/types/repeater-query/items/acf_field', [ $this, 'get_repeater_value' ], 10, 2 );
	}

	public function get_repeater_value( $items, $args ) {

		if ( empty( $args['acf_field'] ) ) {
			return $items;
		}

		$field_data = explode( '::', $args['acf_field'] );
		$field_id   = $field_data[0];
		$field_name = $field_data[1];

		$count      = jet_engine()->listings->data->get_meta( $field_name );
		$sub_fields = $this->get_repeater_sub_fields( [], $args );

		if ( ! $count ) {
			return $items;
		}

		$items = [];

		for ( $i = 0; $i < absint( $count ); $i++ ) {

			$item = array();

			foreach ( $sub_fields as $field => $label ) {
				$item[ $field ] = jet_engine()->listings->data->get_meta(
					$field_name . '_' . $i . '_' . $field
				);
			}


			$items[] = $item;
		}

		return $items;
		
	}

	public function get_repeater_sub_fields( $fields, $args ) {

		if ( empty( $args['acf_field'] ) ) {
			return $fields;
		}

		$field_data = explode( '::', $args['acf_field'] );
		$field_id   = $field_data[0];
		$field_name = $field_data[1];

		$field = acf_get_field( $field_id );

		if ( ! $field || empty( $field['sub_fields'] ) ) {
			return $fields;
		}

		foreach ( $field['sub_fields'] as $sub_field ) {
			$fields[ $sub_field['name'] ] = $sub_field['label'];
		}

		return $fields;
	}

	public function register_source( $data ) {
		
		$data['sources'][] = [
			'value' => 'acf_field',
			'label' => __( 'ACF Field', 'jet-engine' ),
		];

		return $data;
	}

	public function register_editor_controls() {

		$acf_fields = $this->acf_package->get_fields_goups( 'repeater', 'blocks' );

		if ( ! empty( $acf_fields ) ) {

			$acf_fields = array_map( function( $option ) {
			
				if ( ! empty( $option['values'] ) ) {
					$option['options'] = $option['values'];
					unset( $option['values'] );
				}

				return $option;

			}, $acf_fields );

		}

		?>
		<cx-vui-select
			label="<?php _e( 'ACF Field', 'jet-engine' ); ?>"
			description="<?php _e( 'Select ACF Field to use as items source', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:groups-list="<?php echo htmlspecialchars( json_encode( $acf_fields ) ); ?>"
			size="fullwidth"
			name="acf_field"
			key="acf_field"
			v-if="'acf_field' === query.source"
			v-model="query.acf_field"
		></cx-vui-select>
		<?php
	}

}
