(function() {

	'use strict';

	Vue.component( 'jet-engine-query-builder-geosearch-control', {
		props: [ 'value' ],
		template: `
		<cx-vui-component-wrapper
			:label="label"
			:description="description"
			:wrapper-css="[ 'vertical-fullwidth', 'geosearch' ]"
		>
			<div v-if="this.value && this.value.lat && this.value.lng" style="display: flex; justify-content: space-between; padding: 0 0 5px; align-items: center;">
				<div>
					<i title="Lat">{{ this.value.lat }}</i>,
					<i title="Lng">{{ this.value.lng }}</i>
				</div>
				<cx-vui-button
					button-style="link-error"
					size="link"
					@click="resetLocation"
				>
					<span slot="label">× Reset location</span>
				</cx-vui-button>
			</div>
			<div ref="map" style="height: 300px;"></div>
			<div style="padding: 3px 0 0 0;"><i v-if="! value || ! value.lat">{{ help }}</i></div>
		</cx-vui-component-wrapper>
		`,
		data: function() {
			return {
				mapProvider: null,
				map: null,
				label: JetEngineGeoSearch.label,
				description: JetEngineGeoSearch.description,
				help: JetEngineGeoSearch.help,
				mapDefaults: {
					center: { lat: 41, lng: 71 },
					zoom: 1,
				},
				marker: null,
				markerDefaults: {
					content: '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24"><path d="M12 0c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z" fill="#C92C2C"/></svg>',
					shadow: false,
				},
			};
		},
		beforeMount: function() {
			this.mapProvider = new window.JetEngineMapsProvider();
		},
		mounted: function() {

			if ( this.value ) {
				this.$set( this.mapDefaults, 'center',  this.value );
				this.$set( this.mapDefaults, 'zoom',  14 );
			}

			this.map = this.mapProvider.initMap( this.$refs.map, this.mapDefaults );

			if ( this.value ) {
				this.marker = this.mapProvider.addMarker( Object.assign( this.markerDefaults, {
					position: this.value,
					map: this.map,
				} ) );
			}

			this.mapProvider.markerOnClick( this.map, this.markerDefaults, ( marker ) => {
				
				if ( this.marker ) {
					this.mapProvider.removeMarker( this.marker );
				}
				
				this.marker = marker;
				this.$emit( 'input', this.mapProvider.getMarkerPosition( marker, true ) );

				let position = this.mapProvider.getMarkerPosition( marker, true );

				wp.apiFetch( {
					method: 'get',
					path: JetEngineGeoSearch.api + '?lat=' + position.lat + '&lng=' + position.lng,
				} ).then( function( response ) {
					console.log( response );
				} ).catch( function( e ) {
					console.log( e );
				} );

			} );
		},
		methods: {
			resetLocation: function() {
				this.mapProvider.removeMarker( this.marker );
				this.$emit( 'input', null );
			}
		}
	} );

})();
