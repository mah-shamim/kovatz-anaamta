<?php
namespace Jet_Engine\Modules\Maps_Listings;

class Map_Field {

	public $field_type = 'map';

	public $assets_added = false;
	public $cct_map_cols = array();

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		add_filter( 'jet-engine/meta-fields/config', array( $this, 'register_field_type' ) );

		add_filter( 'jet-engine/meta-fields/' . $this->field_type . '/args',          array( $this, 'prepare_field_args' ), 10, 3 );
		add_filter( 'jet-engine/meta-fields/repeater/' . $this->field_type . '/args', array( $this, 'prepare_field_args' ), 10, 3 );

		add_filter( 'jet-engine/meta-boxes/raw-fields',                   array( $this, 'add_lat_lng_fields' ), 10, 2 );
		add_filter( 'jet-engine/options-pages/raw-fields',                array( $this, 'add_lat_lng_fields' ), 10, 2 );
		add_filter( 'jet-engine/custom-content-types/factory/raw-fields', array( $this, 'add_lat_lng_fields' ), 10, 2 );

		add_filter( 'jet-engine/meta-boxes/rest-api/fields/field-type', array( $this, 'prepare_rest_api_field_type' ), 10, 2 );
		add_filter( 'jet-engine/meta-boxes/rest-api/fields/schema',     array( $this, 'prepare_rest_api_schema' ), 10, 3 );
		add_filter( 'jet-engine/options-pages/rest-api/fields/value',   array( $this, 'prepare_rest_api_option_val' ), 10, 2 );

		add_action( 'jet-engine/meta-boxes/templates/fields/controls',          array( $this, 'add_controls' ) );
		add_action( 'jet-engine/meta-boxes/templates/fields/repeater/controls', array( $this, 'add_repeater_controls' ) );

		add_filter( 'jet-engine/custom-content-types/item-to-update',    array( $this, 'ensure_cct_data_on_save' ), 10, 2 );
		add_filter( 'jet-engine/custom-content-types/db/exclude-fields', array( $this, 'exclude_cct_map_fields' ) );

	}

	public function ensure_cct_data_on_save( $item, $fields ) {

		foreach ( $fields as $field_id => $field_data ) {
			if ( 'map' === $field_data['type'] && isset( $item[ $field_id . '_hash' ] ) && empty( $item[ $field_id . '_hash' ] ) && ! empty( $item[ $field_id ] ) ) {
				$item[ $field_id . '_hash' ] = md5( $item[ $field_id ] );
			}
		}

		return $item;

	}

	public function register_field_type( $config ) {

		$config['field_types'][] = array(
			'value' => $this->field_type,
			'label' => __( 'Map', 'jet-engine' ),
		);

		// Added the map field for specific condition operators.
		foreach ( $config['condition_operators'] as &$condition_operator ) {

			if ( empty( $condition_operator['value'] ) ) {
				continue;
			}

			if ( in_array( $condition_operator['value'], array( 'equal', 'not_equal' ) ) && isset( $condition_operator['not_fields'] ) ) {
				$condition_operator['not_fields'][] = $this->field_type;
			}

			if ( in_array( $condition_operator['value'], array( 'contains', '!contains' ) ) && isset( $condition_operator['fields'] ) ) {
				$condition_operator['fields'][] = $this->field_type;
			}

		}

		unset( $condition_operator );

		return $config;
	}

	public function prepare_field_args( $args, $field, $instance ) {

		$args['type']         = 'text';
		$args['input_type']   = 'hidden';
		$args['autocomplete'] = 'off';
		$args['class']        = 'jet-engine-map-field';

		$value_format = ! empty( $field['map_value_format'] ) ? $field['map_value_format'] : 'location_string';
		$args['map_value_format'] = $value_format;

		$is_cct_field = 'Jet_Engine\Modules\Custom_Content_Types\Pages\Edit_Item_Page' === get_class( $instance );
		$is_repeater_field = 'jet-engine/meta-fields/repeater/' . $this->field_type . '/args' === current_filter();

		if ( $is_cct_field || $is_repeater_field ) {
			$field_prefix = $field['name'];
		} else {
			$field_prefix = md5( $field['name'] );
		}

		if ( ! $is_repeater_field ) {

			if ( empty( $args['description'] ) ) {
				$args['description'] = '';
			}

			$args['description'] .= $this->get_field_description( $field_prefix );
		}

		$field_settings = array(
			'height'       => ! empty( $field['map_height'] ) ? $field['map_height'] : '300',
			'format'       => $value_format,
			'field_prefix' => $field_prefix,
		);

		$args['extra_attr'] = array(
			'data-settings' => htmlentities( json_encode( $field_settings ) ),
		);

		add_action( 'admin_enqueue_scripts', function ( $hook ) use ( $instance ) {

			if ( ! $instance->is_allowed_on_current_admin_hook( $hook ) ) {
				return;
			}

			if ( $this->assets_added ) {
				return;
			}

			$this->enqueue_assets();

			$this->assets_added = true;
		} );

		return $args;
	}

	public function get_field_description( $prefix = '' ) {

		$result = '<p><b>' . esc_html__( 'Lat and Lng are separately stored in the following fields', 'jet-engine' ) . ':</b></p>';
		$result .= '<ul>';
		$result .= sprintf( '<li>%1$s: <span class="je-field-name">%2$s</span></li>', esc_html__( 'Lat', 'jet-engine' ), $prefix . '_lat' );
		$result .= sprintf( '<li>%1$s: <span class="je-field-name">%2$s</span></li>', esc_html__( 'Lng', 'jet-engine' ), $prefix . '_lng' );
		$result .= '</ul>';

		return $result;
	}

	public function enqueue_assets() {

		$provider = Module::instance()->providers->get_active_map_provider();

		$provider->register_public_assets();
		$provider->public_assets( null, array( 'marker_clustering' => false ), null );

		wp_enqueue_style(
			'jet-engine-map-field',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/css/map-field.css' ),
			array(),
			jet_engine()->get_version()
		);

		wp_enqueue_script(
			'jet-engine-map-field',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/js/admin/map-field.js' ),
			array( 'jquery', 'wp-api-fetch', 'wp-util' ),
			jet_engine()->get_version(),
			true
		);

		wp_localize_script( 'jet-engine-map-field', 'JetMapFieldsSettings', array(
			'api'             => jet_engine()->api->get_route( 'get-map-point-data' ),
			'apiHash'         => jet_engine()->api->get_route( 'get-map-location-hash' ),
			'apiLocation'     => jet_engine()->api->get_route( 'get-map-location-data' ),
			'apiAutocomplete' => jet_engine()->api->get_route( 'get-map-autocomplete-data' ),
			'i18n' => array(
				'loading'  => esc_html__( 'Loading ...', 'jet-engine' ),
				'notFound' => esc_html__( 'Address not found', 'jet-engine' ),
			),
		) );

		add_action( 'admin_print_footer_scripts', array( $this, 'print_field_js_template' ) );
	}

	public function print_field_js_template() {
		?>
		<script type="text/html" id="tmpl-jet-engine-map-field">
			<div class="jet-engine-map-field__preview">
				<address class="jet-engine-map-field__position"></address>
				<div class="jet-engine-map-field__reset" role="button">× <?php _e( 'Reset location', 'jet-engine' ); ?></div>
			</div>
			<div class="jet-engine-map-field__search">
				<input type="text" class="widefat cx-ui-text" placeholder="<?php _e( 'Search...', 'jet-engine' ); ?>">
				<div class="jet-engine-map-field__search-loader"></div>
				<ul class="jet-engine-map-field__search-list"></ul>
			</div>
			<div class="jet-engine-map-field__frame" style="height:{{{data.height}}}px"></div>
			<# if ( data.isRepeater ) { #>
			<div class="jet-engine-map-field__description">
				<p>
					<?php _e( 'Lat and Lng are separately stored in the following fields', 'jet-engine' ); ?>:&nbsp;
					<span class="je-field-name">{{{data.fieldPrefix}}}_lat</span>, <span class="je-field-name">{{{data.fieldPrefix}}}_lng</span>
				</p>
			</div>
			<# } #>
		</script>
		<?php
	}

	public function add_lat_lng_fields( $fields = array(), $instance = null ) {

		if ( empty( $fields ) ) {
			return $fields;
		}

		$_fields = $fields;

		foreach ( $_fields as $index => $field ) {

			if ( empty( $field['object_type'] ) || 'field' !== $field['object_type'] ) {
				continue;
			}

			if ( empty( $field['type'] ) ) {
				return false;
			}

			if ( $this->field_type === $field['type'] ) {

				$hash = md5( $field['name'] );

				$field_prefix = $hash;

				if ( 'Jet_Engine\Modules\Custom_Content_Types\Factory' === get_class( $instance ) ) {
					$field_prefix = $field['name'];

					$hash_col = $field_prefix . '_hash';
					$lat_col  = $field_prefix . '_lat';
					$lng_col  = $field_prefix . '_lng';

					$this->cct_map_cols[] = $hash_col;
					$this->cct_map_cols[] = $lat_col;
					$this->cct_map_cols[] = $lng_col;

					if ( ! $instance->db->column_exists( $hash_col ) ) {
						$instance->db->insert_table_columns( array( $hash_col => 'text' ) );
					}

					if ( ! $instance->db->column_exists( $lat_col ) ) {
						$instance->db->insert_table_columns( array( $lat_col => 'text' ) );
					}

					if ( ! $instance->db->column_exists( $lng_col ) ) {
						$instance->db->insert_table_columns( array( $lng_col => 'text' ) );
					}
				}

				$fields[] = array(
					'title'       => $field['title'] . ' Hash',
					'name'        => $field_prefix . '_hash',
					'object_type' => 'field',
					'type'        => 'hidden',
					'default_val' => $hash,
				);

				$fields[] = array(
					'title'       => $field['title'] . ' Lat',
					'name'        => $field_prefix . '_lat',
					'object_type' => 'field',
					'type'        => 'hidden',
				);

				$fields[] = array(
					'title'       => $field['title'] . ' Lng',
					'name'        => $field_prefix . '_lng',
					'object_type' => 'field',
					'type'        => 'hidden',
				);
			}

			if ( 'repeater' === $field['type'] && ! empty( $field['repeater-fields'] ) ) {

				foreach ( $field['repeater-fields'] as $repeater_field ) {

					if ( $this->field_type !== $repeater_field['type'] ) {
						continue;
					}

					$fields[ $index ]['repeater-fields'][] = array(
						'title' => $repeater_field['title'] . ' Hash',
						'name'  => $repeater_field['name'] . '_hash',
						'type'  => 'hidden',
					);

					$fields[ $index ]['repeater-fields'][] = array(
						'title' => $repeater_field['title'] . ' Lat',
						'name'  => $repeater_field['name'] . '_lat',
						'type'  => 'hidden',
					);

					$fields[ $index ]['repeater-fields'][] = array(
						'title' => $repeater_field['title'] . ' Lng',
						'name'  => $repeater_field['name'] . '_lng',
						'type'  => 'hidden',
					);
				}
			}
		}

		return $fields;
	}

	public function prepare_rest_api_field_type( $type, $field ) {

		if ( ! $this->is_map_field( $field ) ) {
			return $type;
		}

		if ( 'location_array' === $field['map_value_format'] ) {
			$type = 'object';
		}

		return $type;
	}

	public function prepare_rest_api_option_val( $value, $field ) {

		if ( ! $this->is_map_field( $field ) ) {
			return $value;
		}

		if ( 'location_array' === $field['map_value_format'] ) {
			return json_decode( wp_unslash( $value ), true );
		} else {
			return $value;
		}

	}

	public function prepare_rest_api_schema( $schema, $field_type, $field ) {

		if ( ! $this->is_map_field( $field ) ) {
			return $schema;
		}

		if ( 'location_array' === $field['map_value_format'] ) {
			$schema = array( 
				'type'             => 'object',
				'properties'       => array(
					'lat' => array( 'type' => array( 'string', 'float' ) ),
					'lng' => array( 'type' => array( 'string', 'float' ) ),
				),
				'prepare_callback' => function( $value, $request, $args ) {
					return json_decode( $value );
				}
			);
		}

		return $schema;
	}

	public function is_map_field( $field = array() ) {

		if ( empty( $field['type'] ) || 'text' !== $field['type'] ) {
			return false;
		}

		if ( empty( $field['input_type'] ) || 'hidden' !== $field['input_type'] ) {
			return false;
		}

		if ( empty( $field['map_value_format'] ) ) {
			return false;
		}

		return true;
	}

	public function add_controls() {
		?>
		<cx-vui-select
			label="<?php _e( 'Value format', 'jet-engine' ); ?>"
			description="<?php _e( 'Set the format of the value will be stored in the database', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			:options-list="[
				{
					value: 'location_string',
					label: '<?php _e( 'String with location Lat and Lng separated by coma', 'jet-engine' ); ?>'
				},
				{
					value: 'location_array',
					label: '<?php _e( 'Array with location Lat and Lng', 'jet-engine' ); ?>'
				},
				{
					value: 'location_address',
					label: '<?php _e( 'Location Address', 'jet-engine' ); ?>'
				}
			]"
			:value="field.map_value_format"
			@input="setFieldProp( 'map_value_format', $event )"
			:conditions="[
				{
					'input':   field.object_type,
					'compare': 'equal',
					'value':   'field',
				},
				{
					'input':   field.type,
					'compare': 'equal',
					'value':   'map',
				}
			]"
		></cx-vui-select>
		<cx-vui-input
			label="<?php _e( 'Map Height', 'jet-engine' ); ?>"
			description="<?php _e( 'Set the height of the map. Default is 300px.', 'jet-engine' ); ?>"
			type="number"
			:min="Number(100)"
			:step="Number(10)"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			:value="field.map_height"
			@input="setFieldProp( 'map_height', $event )"
			:conditions="[
				{
					'input':   field.object_type,
					'compare': 'equal',
					'value':   'field',
				},
				{
					'input':   field.type,
					'compare': 'equal',
					'value':   'map',
				}
			]"
		></cx-vui-input>
		<?php
	}

	public function add_repeater_controls() {
		?>
		<cx-vui-select
			label="<?php _e( 'Value format', 'jet-engine' ); ?>"
			description="<?php _e( 'Set the format of the value will be stored in the database', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			:options-list="[
				{
					value: 'location_string',
					label: '<?php _e( 'String with location Lat and Lng separated by coma', 'jet-engine' ); ?>'
				},
				{
					value: 'location_array',
					label: '<?php _e( 'Array with location Lat and Lng', 'jet-engine' ); ?>'
				},
				{
					value: 'location_address',
					label: '<?php _e( 'Location Address', 'jet-engine' ); ?>'
				}
			]"
			:value="field['repeater-fields'][ rFieldIndex ].map_value_format"
			@input="setRepeaterFieldProp( rFieldIndex, 'map_value_format', $event )"
			:conditions="[
				{
					'input':   field['repeater-fields'][ rFieldIndex ].type,
					'compare': 'equal',
					'value':   'map',
				}
			]"
		></cx-vui-select>
		<cx-vui-input
			label="<?php _e( 'Map Height', 'jet-engine' ); ?>"
			description="<?php _e( 'Set the height of the map. Default is 300px.', 'jet-engine' ); ?>"
			type="number"
			:min="Number(100)"
			:step="Number(10)"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			:value="field['repeater-fields'][ rFieldIndex ].map_height"
			@input="setRepeaterFieldProp( rFieldIndex, 'map_height', $event )"
			:conditions="[
				{
					'input':   field['repeater-fields'][ rFieldIndex ].type,
					'compare': 'equal',
					'value':   'map',
				}
			]"
		></cx-vui-input>
		<?php
	}

	public function exclude_cct_map_fields( $exclude ) {

		if ( empty( $this->cct_map_cols ) ) {
			return $exclude;
		}

		return array_merge( $exclude, array_unique( $this->cct_map_cols ) );
	}

}