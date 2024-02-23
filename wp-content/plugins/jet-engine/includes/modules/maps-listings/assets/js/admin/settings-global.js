(function( $, mapsSettings ) {

	'use strict';

	Vue.component( 'jet-engine-maps-settings', {
		template: '#jet_engine_maps_settings',
		data: function() {
			return {
				settings: mapsSettings.settings,
				nonce: mapsSettings._nonce,
				sources: mapsSettings.sources,
				allFields: mapsSettings.fields,
				renderProviders: mapsSettings.renderProviders,
				geoProviders: mapsSettings.geoProviders,
				showPopup: false,
				currentPopupSource: '',
				currentPopupFields: [],
			};
		},
		methods: {
			updateSetting: function( value, setting ) {

				var self = this;

				self.$set( self.settings, setting, value );

				jQuery.ajax({
					url: window.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_engine_maps_save_settings',
						nonce: self.nonce,
						settings: self.settings,
					},
				}).done( function( response ) {
					if ( response.success ) {
						self.$CXNotice.add( {
							message: response.data.message,
							type: 'success',
							duration: 7000,
						} );
					} else {
						self.$CXNotice.add( {
							message: response.data.message,
							type: 'error',
							duration: 15000,
						} );
					}
				} ).fail( function( jqXHR, textStatus, errorThrown ) {
					self.$CXNotice.add( {
						message: errorThrown,
						type: 'error',
						duration: 15000,
					} );
				} );
			},
			handlePopupOk: function() {

				if ( this.currentPopupFields.length ) {

					var preloadMeta = this.settings.preload_meta;

					if ( preloadMeta ) {
						preloadMeta = preloadMeta + ',' + this.currentPopupFields.join( '+' );
					} else {
						preloadMeta = this.currentPopupFields.join( '+' );
					}

					this.updateSetting( preloadMeta, 'preload_meta' );
				}

				this.showPopup = false;
				this.currentPopupSource = '';
				this.currentPopupFields = [];
			},
			handlePopupCancel: function() {
				this.showPopup = false;
				this.currentPopupSource = '';
				this.currentPopupFields = [];
			},
			resetPopupFields: function() {
				this.currentPopupFields = [];
				this.$refs.current_popup_fields.setValues( [] );
			}
		}
	} );

})( jQuery, window.JetEngineMapsSettings );
