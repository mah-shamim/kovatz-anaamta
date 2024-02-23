(function( $, JetEngineRestListingsConfig ) {

	'use strict';

	Vue.component( 'jet-engine-rest-api-listings', {
		template: '#jet_engine_rest_api_listings',
		data: function() {
			return {
				items: JetEngineRestListingsConfig.items,
				isBusy: false,
				editID: false,
				authTypes: JetEngineRestListingsConfig.auth_types,
				nonce: JetEngineRestListingsConfig._nonce,
				deleteID: false,
			};
		},
		methods: {
			setEdit: function( itemID ) {
				if ( itemID === this.editID ) {
					this.editID = false;
				} else {
					this.editID = itemID;
				}
			},
			deleteEndpoint: function( itemID, itemIndex ) {

				var self = this;

				self.items.splice( itemIndex, 1 );

				jQuery.ajax({
					url: window.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_engine_api_endpoint_delete',
						nonce: self.nonce,
						item_id: itemID,
					},
				}).done( function( response ) {
					if ( ! response.success ) {
						if ( response.data ) {
							self.$CXNotice.add( {
								message: response.data.message,
								type: 'error',
								duration: 15000,
							} );
						} else {
							self.$CXNotice.add( {
								message: 'Unknown error. Please try again later or contact our support.',
								type: 'error',
								duration: 15000,
							} );
						}
					} else {
						self.$CXNotice.add( {
							message: response.data.message,
							type: 'success',
							duration: 7000,
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
			newEndpoint: function( event, isSample ) {

				var self = this;

				self.isBusy = true;

				var item = {
					name: '',
					url: '',
					authorization: false,
				};

				if ( isSample ) {
					item = JSON.parse( JSON.stringify( JetEngineRestListingsConfig.sample_item ) );
				}

				jQuery.ajax({
					url: window.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_engine_api_endpoint_save',
						nonce: self.nonce,
						item: item,
						item_id: false,
					},
				}).done( function( response ) {
					if ( ! response.success ) {
						if ( response.data ) {
							self.$CXNotice.add( {
								message: response.data.message,
								type: 'error',
								duration: 15000,
							} );
						} else {
							self.$CXNotice.add( {
								message: 'Unknown error. Please try again later or contact our support.',
								type: 'error',
								duration: 15000,
							} );
						}
					} else {

						item.id = response.data.item_id;
						self.items.push( item );
						self.setEdit( response.data.item_id );

						self.$CXNotice.add( {
							message: response.data.message,
							type: 'success',
							duration: 7000,
						} );

					}

					self.isBusy = false;

				} ).fail( function( jqXHR, textStatus, errorThrown ) {

					self.$CXNotice.add( {
						message: errorThrown,
						type: 'error',
						duration: 15000,
					} );

					self.isBusy = false;

				} );

			}
		}
	} );

	Vue.component( 'jet-engine-rest-api-listing-item', {
		template: '#jet_engine_rest_api_listing_item',
		props: {
			value: {
				type: Object,
				default: function() {
					return {};
				},
			},
			isBusy: {
				type: Boolean,
				default: false,
			}
		},
		data: function() {
			return {
				settings: {},
				saving: false,
				authTypes: JetEngineRestListingsConfig.auth_types,
				nonce: JetEngineRestListingsConfig._nonce,
				saveLabel: JetEngineRestListingsConfig.save_label,
				savingLabel: JetEngineRestListingsConfig.saving_label,
				sampleRequestError: null,
				sampleRequestSuccess: null,
				makingSampleRequest: false,
			};
		},
		mounted: function() {
			this.settings = this.value;
		},
		methods: {
			makeSampleRequest: function( event ) {
				this.saveEndpoint( event, true );
			},
			isSaving: function() {
				return this.saving || this.isBusy;
			},
			isDisabled: function() {
				return this.makingSampleRequest || this.isBusy;
			},
			buttonLabel: function() {
				if ( this.isSaving() ) {
					return this.savingLabel;
				} else {
					return this.saveLabel;
				}
			},
			saveEndpoint: function( event, withSampleRequest ) {

				var self = this;

				if ( withSampleRequest ) {
					self.makingSampleRequest = true;
				} else {
					self.saving = true;
				}

				jQuery.ajax({
					url: window.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_engine_api_endpoint_save',
						nonce: self.nonce,
						item: self.settings,
						item_id: self.settings.id,
						with_sample_request: withSampleRequest,
					},
				}).done( function( response ) {
					if ( ! response.success ) {
						if ( response.data ) {
							if ( withSampleRequest ) {
								self.sampleRequestSuccess = null;
								self.sampleRequestError = response.data.message;
							} else {
								self.$CXNotice.add( {
									message: response.data.message,
									type: 'error',
									duration: 15000,
								} );
							}
						} else {
							self.$CXNotice.add( {
								message: 'Unknown error. Please try again later or contact our support.',
								type: 'error',
								duration: 15000,
							} );
						}
					} else {

						if ( withSampleRequest ) {
							self.sampleRequestError = null;
							self.sampleRequestSuccess = response.data.message;
							self.$set( self.settings, 'connected', true );
						} else {
							self.$CXNotice.add( {
								message: response.data.message,
								type: 'success',
								duration: 7000,
							} );
						}

						self.$emit( 'input', self.settings );
					}

					if ( withSampleRequest ) {
						self.makingSampleRequest = false;
					} else {
						self.saving = false;
					}

				} ).fail( function( jqXHR, textStatus, errorThrown ) {

					self.$CXNotice.add( {
						message: errorThrown,
						type: 'error',
						duration: 15000,
					} );

					if ( withSampleRequest ) {
						self.makingSampleRequest = false;
					} else {
						self.saving = false;
					}

				} );

			}
		}
	} );

})( jQuery, window.JetEngineRestListingsConfig );
