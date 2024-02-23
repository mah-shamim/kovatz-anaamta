(function( $, JetEnginePageConfig ) {

	'use strict';

	window.JetEngineMB = new Vue( {
		el: '#jet_cpt_form',
		template: '#jet-cpt-form',
		data: {
			generalSettings: JetEnginePageConfig.general_settings,
			fieldsList: JetEnginePageConfig.fields,
			icons: JetEnginePageConfig.icons,
			buttonLabel: JetEnginePageConfig.edit_button_label,
			isEdit: JetEnginePageConfig.item_id,
			allParents: JetEnginePageConfig.parents,
			availableCaps: JetEnginePageConfig.capabilities,
			availablePositions: JetEnginePageConfig.positions,
			defaultMenuPosition: JetEnginePageConfig.default_position,
			helpLinks: JetEnginePageConfig.help_links,
			initialStorageType: null,
			updateOptions: '',
			showDeleteDialog: false,
			saving: false,
			errors: {
				name: false,
				slug: false,
			},
			errorNotices: [],
		},
		mounted: function() {

			var self = this;

			if ( JetEnginePageConfig.item_id ) {

				wp.apiFetch( {
					method: 'get',
					path: JetEnginePageConfig.api_path_get + JetEnginePageConfig.item_id,
				} ).then( function( response ) {

					if ( response.success && response.data ) {

						self.generalSettings    = response.data.general_settings;
						self.fieldsList         = response.data.fields;
						self.initialStorageType = self.storageType;

					} else {
						if ( response.notices.length ) {
							response.notices.forEach( function( notice ) {

								self.$CXNotice.add( {
									message: notice.message,
									type: 'error',
									duration: 15000,
								} );

								//self.errorNotices.push( notice.message );
							} );
						}
					}
				} ).catch( function( e ) {
					console.log( e );
				} );

			} else {
				self.$set( self.generalSettings, 'position', parseInt( self.defaultMenuPosition, 10 ) );
			}
		},
		computed: {
			availableParents: function() {
				var self = this,
					parents;

				parents = self.allParents.filter( function( page ) {
					if ( ! self.generalSettings.slug ) {
						return true;
					} else {
						return self.generalSettings.slug !== page.value;
					}
				} );

				parents.unshift( {
					value: '',
					label: '',
				} );

				return parents;
			},
			storageType: function() {
				var storageType = this.generalSettings.storage_type;

				if ( 'separate' === storageType && this.generalSettings.option_prefix ) {
					storageType += '_with_prefix';
				}

				return storageType;
			},
			storageTypeIsChanged: function() {
				if ( ! this.isEdit ) {
					return false;
				} else if ( ! this.initialStorageType ) {
					return false;
				} else {
					return this.initialStorageType !== this.storageType;
				}
			},
		},
		methods: {
			preSetSlug: function() {

				if ( ! this.generalSettings.slug ) {

					var regex = /\s+/g,
						slug  = this.generalSettings.name.toLowerCase().replace( regex, '-' );

					// Replace accents
					slug = slug.normalize( 'NFD' ).replace( /[\u0300-\u036f]/g, "" );

					// Replace cyrillic
					slug = window.JetEngineTools.maybeCyrToLatin( slug );

					this.$set( this.generalSettings, 'slug', slug );

				}

				if ( ! this.generalSettings.menu_name ) {
					this.$set( this.generalSettings, 'menu_name', this.generalSettings.name );
				}

			},
			handleFocus: function( where ) {

				if ( this.errors[ where ] ) {
					this.$set( this.errors, where, false );
					this.$CXNotice.close( where );
					//this.errorNotices.splice( 0, this.errorNotices.length );
				}

			},
			save: function() {

				var self      = this,
					hasErrors = false,
					path      = JetEnginePageConfig.api_path_edit;

				if ( JetEnginePageConfig.item_id ) {
					path += JetEnginePageConfig.item_id;
				}

				if ( ! self.generalSettings.name ) {
					self.$set( this.errors, 'name', true );

					self.$CXNotice.add( {
						message: JetEnginePageConfig.notices.name,
						type: 'error',
						duration: 7000,
					}, 'name' );

					//self.errorNotices.push( JetEnginePageConfig.notices.name );
					hasErrors = true;
				}

				if ( hasErrors ) {
					return;
				}

				self.saving = true;

				wp.apiFetch( {
					method: 'post',
					path: path,
					data: {
						general_settings: self.generalSettings,
						fields: self.fieldsList,
						initial_storage_type: this.initialStorageType,
						update_options: this.updateOptions,
					}
				} ).then( function( response ) {

					if ( response.success ) {
						if ( JetEnginePageConfig.redirect ) {
							window.location = JetEnginePageConfig.redirect.replace( /%id%/, response.item_id );
						} else {

							self.$CXNotice.add( {
								message: JetEnginePageConfig.notices.success,
								type: 'success',
							} );

							self.saving = false;
						}
					} else {
						if ( response.notices.length ) {
							response.notices.forEach( function( notice ) {

								self.$CXNotice.add( {
									message: notice.message,
									type: 'error',
									duration: 7000,
								} );

								//self.errorNotices.push( notice.message );
							} );
						}
					}
				} ).catch( function( response ) {
					//self.errorNotices.push( response.message );

					self.$CXNotice.add( {
						message: response.message,
						type: 'error',
						duration: 7000,
					} );

					self.saving = false;
				} );

			},
		}
	} );

})( jQuery, window.JetEnginePageConfig );
