(function( $, JetEngineRestListingsConfig ) {

	'use strict';

	Vue.component( 'jet-engine-media', {
		template: '#jet_engine_media',
		data: function() {
			return {
				fileData: {},
				mediaFrame: null,
			};
		},
		props: [ 'value' ],
		created: function() {

			if ( ! this.value || ! this.value.id ) {
				this.fileData = {
					id: null,
					name: null,
					url: null,
				};
			} else {
				this.fileData = this.value;
			}

			this.mediaFrame = window.wp.media({
				title: 'Select or Upload File for Glossary',
				button: {
					text: 'Use this file'
				},
				multiple: false,
			});

			this.mediaFrame.on( 'select', () => {

				var attachment = this.mediaFrame.state().get( 'selection' ).first().toJSON();

				this.$set( this.fileData, 'id', attachment.id );
				this.$set( this.fileData, 'name', attachment.filename );
				this.$set( this.fileData, 'url', attachment.url );

				this.$emit( 'input', this.fileData );

			} );

		},
		methods: {
			selectFile: function() {
				this.mediaFrame.open();
			},
		},
	} );

	Vue.component( 'jet-engine-glossaries', {
		template: '#jet_engine_glossaries',
		data: function() {
			return {
				items: JetEngineGlossariesConfig.items,
				isBusy: false,
				editID: false,
				nonce: JetEngineGlossariesConfig._nonce,
				deleteID: false,
				ordersAjaxRequest: null,

			};
		},
		directives: { handle: window.VueSlicksort.HandleDirective },
		components: {
			'slick-list': window.VueSlicksort.SlickList,
			'slick-item': window.VueSlicksort.SlickItem,
		},
		computed: {
			itemsOrders: function() {
				return this.items.map( function( item ) {
					return item.id;
				} );
			}
		},
		watch: {
			itemsOrders: function( value, oldValue ) {
				var isEqual = value.length && oldValue.length && value.length === oldValue.length;

				if ( isEqual ) {
					isEqual = value.every( function( element, index ) {
						return element === oldValue[index];
					} );
				}

				if ( isEqual ) {
					return;
				}

				var self = this;

				this.ordersAjaxRequest = jQuery.ajax( {
					url: window.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_engine_glossary_save_orders',
						orders: value,
						nonce: self.nonce,
					},
					beforeSend: function() {
						if ( null !== self.ordersAjaxRequest ) {
							self.ordersAjaxRequest.abort();
						}
					},
				} ).done( function( response ) {
					//console.log( response );
				} ).fail( function( jqXHR, textStatus, errorThrown ) {

				} );
			}
		},
		methods: {
			setEdit: function( itemID ) {
				if ( itemID === this.editID ) {
					this.editID = false;
				} else {
					this.editID = itemID;
				}
			},
			deleteItem: function( itemID, itemIndex ) {

				var self = this;

				self.items.splice( itemIndex, 1 );

				jQuery.ajax({
					url: window.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_engine_glossary_delete',
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
			newItem: function( event, isSample ) {

				var self = this;

				self.isBusy = true;

				var item = {
					name: '',
					fields: [],
				};

				jQuery.ajax({
					url: window.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_engine_glossary_save',
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

	Vue.component( 'jet-engine-glossary', {
		template: '#jet_engine_glossary',
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
				nonce: JetEngineGlossariesConfig._nonce,
				saveLabel: JetEngineGlossariesConfig.save_label,
				savingLabel: JetEngineGlossariesConfig.saving_label,
				isConverting: false,
			};
		},
		mounted: function() {
			this.settings = this.value;
		},
		methods: {
			isSaving: function() {
				return this.saving;
			},
			isDisabled: function() {
				return this.isSaving();
			},
			buttonLabel: function() {
				if ( this.isSaving() ) {
					return this.savingLabel;
				} else {
					return this.saveLabel;
				}
			},
			cloneField: function( index ) {

				var field    = JSON.parse( JSON.stringify( this.settings.fields[ index ] ) ),
					newField = {
						'label':      field.label + ' (Copy)',
						'value':      field.value + '_copy',
						'is_checked': field.is_checked,
					};

				//this.onInput();

				this.settings.fields.splice( index + 1, 0, newField );

			},
			deleteField: function( index ) {
				this.settings.fields.splice( index, 1 );
			},
			addNewField: function( event ) {

				var field = {
					label: '',
					value: '',
					is_checked: false,
					collapsed: false,
				};

				this.settings.fields.push( field );

			},
			setFieldProp: function( index, key, value ) {

				var field = this.settings.fields[ index ];

				field[ key ] = value;

				this.settings.fields.splice( index, 1, field );

			},
			isCollapsed: function( object ) {
				if ( undefined === object.collapsed || true === object.collapsed ) {
					return true;
				} else {
					return false;
				}
			},
			saveItem: function( event, withSampleRequest ) {

				var self = this;

				self.saving = true;

				jQuery.ajax({
					url: window.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_engine_glossary_save',
						nonce: self.nonce,
						item: self.settings,
						item_id: self.settings.id,
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

						self.$emit( 'input', self.settings );
					}

					self.saving = false;

				} ).fail( function( jqXHR, textStatus, errorThrown ) {

					self.$CXNotice.add( {
						message: errorThrown,
						type: 'error',
						duration: 15000,
					} );

					self.saving = false;

				} );

			},

			convertSource: function() {
				var self = this;

				self.isConverting = true;

				jQuery.ajax( {
					url: window.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_engine_glossary_get_fields_from_file',
						nonce: self.nonce,
						item: self.settings,
					},
				} ).done( function( response ) {

					if ( ! response.success ) {

						var errorMessage = response.data ? response.data.message : 'Unknown error. Please try again later or contact our support.';

						self.$CXNotice.add( {
							message: errorMessage,
							type: 'error',
							duration: 15000,
						} );

					} else {

						if ( response.data && response.data.fields ) {

							self.$set( self.settings, 'source', 'manual' );
							self.$set( self.settings, 'fields', response.data.fields );

							self.$emit( 'input', self.settings );

							self.$CXNotice.add( {
								message: response.data.message,
								type: 'success',
								duration: 7000,
							} );

						} else {

							self.$CXNotice.add( {
								message: 'Unknown error. Please try again later or contact our support.',
								type: 'error',
								duration: 15000,
							} );

						}
					}

					self.isConverting = false;

				} ).fail( function( jqXHR, textStatus, errorThrown ) {

					self.$CXNotice.add( {
						message: errorThrown,
						type: 'error',
						duration: 15000,
					} );

					self.isConverting = false;

				} );
			}
		}
	} );

})( jQuery, window.JetEngineGlossariesConfig );
