(function( $, mapsSettings ) {

	'use strict';

	Vue.component( 'jet-engine-maps-google-validate-api-key', {
		props: [ 'settings' ],
		template: `
		<cx-vui-component-wrapper
			label="Validate API key"
			description="Make test request to check if Geocoding API is configured correctly for your API key"
			:wrapper-css="[ 'equalwidth' ]"
		>
			<div
				v-if="validated"
				:class="{
					'validatation-result': true,
					'validatation-result--success': validateResult.success,
					'validatation-result--error': ! validateResult.success,
				}"
				v-html="validateResult.message"
			></div>
			<cx-vui-button
				button-style="accent"
				:loading="validating"
				@click="validateKey"
			>
				<span slot="label">Validate Google maps API key</span>
			</cx-vui-button>
		</cx-vui-component-wrapper>
		`,
		data: function() {
			return {
				validating: false,
				validated: false,
				validateResult: {
					success: true,
					message: 'We successfully get coordinates for random address with your API key. You can use this key for address geocoding!'
				},
			};
		},
		methods: {
			getRandStreet: function() {
				var streets = [
					'Lazurna St',
					'Ozerna St',
					'Henerala Karpenka St',
					'Bila St',
					'Central Ave',
					'Halyny Petrovoi St',
					'Hvardiiska St',
					'Nikol\'s\'ka St',
					'Terasna St',
					'Admirala Makarova St',
					'Observatorna St',
					'Shosseina St',
					'3-ya Poperechna St',
					'Dunajeva St',
					'Zashchuka St',
					'Ryumina St',
					'Sadova St',
					'Chkalova St',
					'7th Slobids\'ka St',
					'Pohranychna St',
					'Kolodyazna St',
				];

				var streetIndex = Math.floor( Math.random() * 20 );
				var street = streets[0];

				if ( streets[ streetIndex ] ) {
					street = streets[ streetIndex ];
				}

				return street + ', Mykolaiv, Mykolaiv Oblast, Ukraine';

			},
			validateKey: function() {

				var self = this,
					apiKey = false;

				self.validating = true;
				self.validated = false;

				if ( self.settings.use_geocoding_key ) {
					apiKey = self.settings.geocoding_key;
				} else {
					apiKey = self.settings.api_key;
				}

				if ( ! apiKey ) {
					self.validated = true;
					self.$set( self.validateResult, 'success', false );
					self.$set( self.validateResult, 'message', 'Please set API key before' );
				}

				jQuery.ajax({
					url: 'https://maps.googleapis.com/maps/api/geocode/json',
					type: 'GET',
					dataType: 'json',
					data: {
						address: encodeURI( self.getRandStreet() ),
						key: encodeURI( apiKey )
					},
				}).done( function( response ) {

					self.validating = false;
					self.validated = true;

					if ( response.status && 'OK' === response.status ) {
						self.$set( self.validateResult, 'success', true );
						self.$set( self.validateResult, 'message', 'We successfully get coordinates for random address with your API key. You can use this key for address geocoding!' );
						return;
					} else if ( response.error_message ) {
						self.$set( self.validateResult, 'success', false );
						self.$set( self.validateResult, 'message', response.error_message );
					} else{
						self.$set( self.validateResult, 'success', false );
						self.$set( self.validateResult, 'message', 'Unknown error, please check your key and try again.' );
					}

				} ).fail( function( jqXHR, textStatus, errorThrown ) {
					self.validating = false;
					self.validated = true;
					self.$set( self.validateResult, 'success', false );
					self.$set( self.validateResult, 'message', errorThrown );
				} );

			},
		}
	} );

})( jQuery, window.JetEngineMapsSettings );
