<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Query_Builder;

use Jet_Engine\Modules\Custom_Content_Types\Module;

class Repeater_Query {

	public $source = 'custom_content_type_field';

	public function __construct() {
		add_filter(
			'jet-engine/query-builder/types/repeater-query/data',
			array( $this, 'register_source' )
		);

		add_action(
			'jet-engine/query-builder/repeater/controls',
			array( $this, 'register_editor_controls' )
		);

		add_filter(
			'jet-engine/query-builder/types/repeater-query/items/' . $this->source,
			array( $this, 'get_items' ),
			10, 2
		);

		add_filter(
			'jet-engine/query-builder/types/repeater-query/fields/' . $this->source,
			array( $this, 'get_instance_fields' ),
			10, 3
		);
	}

	public function register_source( $data ) {
		$data['sources'][] = array(
			'value' => $this->source,
			'label' => __( 'Custom Content Type Field', 'jet-engine' ),
		);

		return $data;
	}

	public function register_editor_controls() {

		$cct_fields = array();

		foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {
			$fields = $instance->get_fields_list( 'repeater' );
			$prefixed_fields = array();

			if ( empty( $fields ) ) {
				continue;
			}

			foreach ( $fields as $key => $label ) {
				$prefixed_fields[] = array(
					'label' => $label,
					'value' => $type . '__' . $key,
				);
			}

			$cct_fields[] = array(
				'label'   => __( 'Content Type:', 'jet-engine' ) . ' ' . $instance->get_arg( 'name' ),
				'options' => $prefixed_fields,
			);
		}
		?>
		<cx-vui-select
			label="<?php _e( 'Custom Content Type Field', 'jet-engine' ); ?>"
			description="<?php _e( 'Enter Custom Content Type field name to use as items source', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:groups-list="<?php echo htmlspecialchars( json_encode( $cct_fields ) ); ?>"
			size="fullwidth"
			key="custom_content_type_field"
			name="custom_content_type_field"
			v-if="'custom_content_type_field' === query.source"
			v-model="query.custom_content_type_field"
		></cx-vui-select>
		<?php
	}

	public function get_items( $items, $args ) {

		if ( empty( $args[ $this->source ] ) ) {
			return $items;
		}

		$items = jet_engine()->listings->data->get_prop( $args[ $this->source ] );

		return wp_unslash( $items );
	}

	public function get_instance_fields( $fields, $args, $query ) {

		if ( empty( $args[ $this->source ] ) ) {
			return $fields;
		}

		$field_data = explode( '__', $args[ $this->source ] );
		$type       = isset( $field_data[0] ) ? $field_data[0] : false;
		$field_name = isset( $field_data[1] ) ? $field_data[1] : false;

		if ( ! $type || ! $field_name ) {
			return $fields;
		}

		$content_type = Module::instance()->manager->get_content_types( $type );

		return $query->get_options_from_fields_data( $field_name, $content_type->fields );
	}
}