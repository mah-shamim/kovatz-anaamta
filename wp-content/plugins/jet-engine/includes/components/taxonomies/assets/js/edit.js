(function( $, JetEngineCPTConfig ) {

	'use strict';

	window.JetEngineCPT = new Vue( {
		el: '#jet_cpt_form',
		template: '#jet-cpt-form',
		data: {
			generalSettings: JetEngineCPTConfig.general_settings,
			labels: JetEngineCPTConfig.labels,
			advancedSettings: JetEngineCPTConfig.advanced_settings,
			metaFields: JetEngineCPTConfig.meta_fields,
			postTypes: JetEngineCPTConfig.post_types,
			metaFieldsEnabled: JetEngineCPTConfig.meta_fields_enabled,
			labelsList: JetEngineCPTConfig.labels_list,
			buttonLabel: JetEngineCPTConfig.edit_button_label,
			isEdit: JetEngineCPTConfig.item_id,
			helpLinks: JetEngineCPTConfig.help_links,
			showDeleteDialog: false,
			initialSlug: null,
			updateTerms: false,
			resetDialog: false,
			isBuiltIn: false,
			saving: false,
			errors: {
				name: false,
				slug: false,
				post_type: false,
			},
			errorNotices: [],
			incorrectSlugMessage: JetEngineCPTConfig.slug_error,
			showIncorrectSlug: false,
		},
		mounted: function() {

			var self = this,
				path = null;

			if ( JetEngineCPTConfig.is_built_in ) {
				self.isBuiltIn = true;
			}

			if ( JetEngineCPTConfig.item_id ) {

				if ( JetEngineCPTConfig.item_id > 0 ) {
					path = JetEngineCPTConfig.api_path_get + JetEngineCPTConfig.item_id;
				} else {
					path = JetEngineCPTConfig.api_path_get;
				}

				wp.apiFetch( {
					method: 'get',
					path: path,
				} ).then( function( response ) {

					if ( response.success && response.data ) {

						self.generalSettings  = response.data.general_settings;
						self.labels           = response.data.labels;
						self.advancedSettings = response.data.advanced_settings;
						self.metaFields       = response.data.meta_fields;
						self.initialSlug      = self.generalSettings.slug;

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
				} );

			} else {
				self.preSetIsPublicDeps();
			}
		},
		methods: {
			slugIsChanged: function() {
				if ( ! this.isEdit ) {
					return false;
				} else if ( ! this.initialSlug ) {
					return false;
				} else {
					return this.initialSlug !== this.generalSettings.slug;
				}
			},
			handleDeletionError: function( errors ) {

				var self = this;

				errors.forEach( function( error ) {
					self.$CXNotice.add( {
						message: error.message,
						type: 'error',
						duration: 7000,
					} );
					//self.errorNotices.push( error.message );
				} );
			},
			handleFocus: function( where ) {

				if ( this.errors[ where ] ) {
					this.$set( this.errors, where, false );
					this.$CXNotice.close( where );
					//this.errorNotices.splice( 0, this.errorNotices.length );
				}

			},
			handleLabelFocus: function( key, isSingular, defaultMask ) {

				var name          = 'post',
					defaultString = '';

				if ( 'singular_name' === key ) {
					return;
				}

				if ( this.labels[ key ] ) {
					return;
				}

				if ( ! defaultMask ) {
					return;
				}

				if ( isSingular ) {

					if ( this.labels.singular_name ) {
						name = this.labels.singular_name;
					} else if ( this.generalSettings.name ) {
						if ( 's' === this.generalSettings.name.slice( -1 ) ) {
							name = this.generalSettings.name.substring( 0, this.generalSettings.name - 1 );
						} else {
							name = this.generalSettings.name;
						}

					}

				} else {
					name = this.generalSettings.name;
				}

				defaultString = defaultMask.replace( /%s%/, name );

				this.$set( this.labels, key, defaultString );

			},
			savePostType: function() {

				var self      = this,
					hasErrors = false,
					path      = JetEngineCPTConfig.api_path_edit;

				if ( JetEngineCPTConfig.item_id ) {
					if ( self.isBuiltIn ) {
						path += self.generalSettings.slug;
					} else {
						path += JetEngineCPTConfig.item_id;
					}
				}

				if ( this.showIncorrectSlug ) {

					self.$CXNotice.add( {
						message: this.incorrectSlugMessage,
						type: 'error',
						duration: 7000,
					}, 'name' );

					hasErrors = true;
				}

				if ( ! self.generalSettings.name ) {
					self.$set( this.errors, 'name', true );

					self.$CXNotice.add( {
						message: JetEngineCPTConfig.notices.name,
						type: 'error',
						duration: 7000,
					}, 'name' );

					//self.errorNotices.push( JetEngineCPTConfig.notices.name );
					hasErrors = true;
				}

				if ( ! self.generalSettings.slug ) {
					self.$set( this.errors, 'slug', true );

					self.$CXNotice.add( {
						message: JetEngineCPTConfig.notices.slug,
						type: 'error',
						duration: 7000,
					}, 'slug' );

					//self.errorNotices.push( JetEngineCPTConfig.notices.slug );
					hasErrors = true;
				}

				if ( ! self.generalSettings.object_type ) {
					self.$set( this.errors, 'post_type', true );

					self.$CXNotice.add( {
						message: JetEngineCPTConfig.notices.post_type,
						type: 'error',
						duration: 7000,
					}, 'post_type' );

					//self.errorNotices.push( JetEngineCPTConfig.notices.post_type );
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
						labels: self.labels,
						advanced_settings: self.advancedSettings,
						meta_fields: self.metaFields,
						update_terms: self.updateTerms,
						initial_slug: self.initialSlug,
					}
				} ).then( function( response ) {

					if ( response.success ) {
						if ( JetEngineCPTConfig.redirect ) {
							window.location = JetEngineCPTConfig.redirect.replace( /%id%/, response.item_id );
						} else {

							self.$CXNotice.add( {
								message: JetEngineCPTConfig.notices.success,
								type: 'success',
							} );

							self.$set( self.generalSettings, 'id', response.item_id );
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
			preSetIsPublicDeps: function() {

				this.$set( this.advancedSettings, 'exclude_from_search', ! this.advancedSettings.public );
				this.$set( this.advancedSettings, 'publicly_queryable', this.advancedSettings.public );
				this.$set( this.advancedSettings, 'show_in_nav_menus', this.advancedSettings.public );
				this.$set( this.advancedSettings, 'show_ui', this.advancedSettings.public );

			},
			preSetSlug: function() {

				if ( ! this.generalSettings.slug ) {

					var regex = /\s+/g,
						slug  = this.generalSettings.name.toLowerCase().replace( regex, '-' );

					// Replace accents
					slug = slug.normalize( 'NFD' ).replace( /[\u0300-\u036f]/g, "" );

					// Replace cyrillic
					slug = window.JetEngineTools.maybeCyrToLatin( slug );

					if ( 32 < slug.length ) {
						slug = slug.substr( 0, 32 );

						if ( '-' === slug.slice( -1 ) ) {
							slug = slug.slice( 0, -1 );
						}
					}

					this.$set( this.generalSettings, 'slug', slug );

				}

			},
			checkSlug: function() {
				this.showIncorrectSlug = ( 32 < this.generalSettings.slug.length );
			},
			isCollapsed: function( object ) {

				if ( undefined === object.collapsed || true === object.collapsed ) {
					return true;
				} else {
					return false;
				}

			},
			resetToDefaults: function() {

				var self = this;

				self.resetDialog = false;

				if ( self.errorNotices.length ) {
					self.errorNotices.splice( 0, self.errorNotices.length );
				}

				wp.apiFetch( {
					method: 'delete',
					path: JetEngineCPTConfig.api_path_reset + self.generalSettings.slug,
					data: {},
				} ).then( function( response ) {

					if ( response.success ) {
						window.location.reload();
					} else {
						if ( response.notices.length ) {
							response.notices.forEach( function( notice ) {
								self.errorNotices.push( notice.message );
							} );
						}
					}

				} ).catch( function( response ) {
					self.errorNotices.push( response.message );
				} );

			}
		}
	} );

})( jQuery, window.JetEngineCPTConfig );
