<?php
namespace Jet_Engine\Modules\Maps_Listings\Geosearch\Controls;

class Base {

	public function register_orderby_option( $for ) {
		add_filter( 'jet-engine/query-builder/' . $for . '/orderby-options', function( $options ) {
			
			$options[] = array(
				'value' => 'distance',
				'label' => __( 'Distance (for Geo Search queries)', 'jet-engine' ),
			);

			return $options;
		} );
	}

	public function geosearch_controls() {
		?>
		<cx-vui-tabs-panel
			name="geosearch"
			:label="isInUseMark( [ 'geosearch_field', 'geosearch_distance', 'geosearch_location', 'geosearch_units' ] ) + '<?php _e( 'Geo Search', 'jet-engine' ); ?>'"
			key="geosearch"
		>
			<?php $this->geosearch_controls_list(); ?>
		</cx-vui-tabs-panel>
		<?php
	}

	public function geosearch_controls_list() {
		?>
		<jet-engine-query-builder-geosearch-control
			v-model="query.geosearch_location"
		/>
		<cx-vui-input
			label="<?php _e( 'Address Field', 'jet-engine' ); ?>"
			description="<?php _e( 'If you set address fields to preload in Maps Settings - you can use them there. To get latitude and longitude values from separate meta fields - separate these field names with `,` sign, latitude field should be first, longitude - second', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			v-model="query.geosearch_field"
		></cx-vui-input>
		<cx-vui-input
			label="<?php _e( 'Distance', 'jet-engine' ); ?>"
			description="<?php _e( 'Set radius to search around selected center', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			v-model="query.geosearch_distance"
		></cx-vui-input>
		<cx-vui-select
			label="<?php _e( 'Units', 'jet-engine' ); ?>"
			description="<?php _e( 'Distance units', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="[
				{
					value: 'miles',
					label: '<?php _e( 'Miles', 'jet-engine' ); ?>'
				},
				{
					value: 'kilometers',
					label: '<?php _e( 'Kilometers', 'jet-engine' ); ?>'
				}
			]"
			size="fullwidth"
			v-model="query.geosearch_units"
		></cx-vui-select>
		<?php
	}

}
