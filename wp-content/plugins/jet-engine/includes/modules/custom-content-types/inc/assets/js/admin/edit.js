(function( $, JetEngineCCTConfig ) {

	'use strict';

	window.JetEngineMB = new Vue( {
		el: '#jet_cct_form',
		template: '#jet-cct-form',
		data: {
			generalSettings: {},
			metaFields: JetEngineCCTConfig.meta_fields,
			postTypes: JetEngineCCTConfig.post_types,
			buttonLabel: JetEngineCCTConfig.edit_button_label,
			isEdit: JetEngineCCTConfig.item_id,
			helpLinks: JetEngineCCTConfig.help_links,
			showDeleteDialog: false,
			prefix: JetEngineCCTConfig.db_prefix,
			icons: JetEngineCCTConfig.icons,
			availablePositions: JetEngineCCTConfig.positions,
			defaultMenuPosition: JetEngineCCTConfig.default_position,
			restBase: JetEngineCCTConfig.rest_base,
			saving: false,
			showAPIParamsInfo: false,
			commonAPIArgs: JetEngineCCTConfig.common_api_args,
			errors: {
				name: false,
				slug: false,
			},
			errorNotices: [],
		},
		created: function() {

			if ( ! this.generalSettings.admin_columns ) {
				this.$set( this.generalSettings, 'admin_columns', {} );
			}

			this.ensureServiceColumns();

		},
		mounted: function() {

			var self = this;

			if ( JetEngineCCTConfig.item_id ) {

				wp.apiFetch( {
					method: 'get',
					path: JetEngineCCTConfig.api_path_get + JetEngineCCTConfig.item_id,
				} ).then( function( response ) {

					if ( response.success && response.data ) {

						self.generalSettings = response.data.args;

						if ( ! self.generalSettings.admin_columns ) {
							self.$set( self.generalSettings, 'admin_columns', {} );
						}

						self.ensureServiceColumns();

						self.metaFields = response.data.meta_fields;

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
				self.$set(
					self.generalSettings,
					'position',
					parseInt( self.defaultMenuPosition, 10 )
				);
			}
		},
		watch: {
			metaFields: function( fields ) {

				if ( ! this.generalSettings.admin_columns ) {
					this.$set( this.generalSettings, 'admin_columns', {} );
				}

				for ( var i = 0; i < fields.length; i++ ) {

					if ( ! fields[ i ].name ) {
						continue;
					}

					if ( ! this.generalSettings.admin_columns[ fields[ i ].name ] ) {
						this.$set( this.generalSettings.admin_columns, fields[ i ].name, {
							enabled: false,
						} );
					}
				}
			}
		},
		computed: {
			slugDisabled: function() {
				var res = false;
				if ( this.isEdit ) {
					res = true;
				}
				return res;
			},
			fieldsList: function() {

				var result = [
					{
						value: '',
						label: '--',
					}
				];

				for ( var i = 0; i < this.metaFields.length; i++ ) {

					if ( 'html' === this.metaFields[ i ].type ) {
						continue;
					}

					if ( 'field' !== this.metaFields[ i ].object_type ) {
						continue;
					}

					result.push( {
						value: this.metaFields[ i ].name,
						label: this.metaFields[ i ].title,
					} );
				}

				return result;

			},
			fieldsForRelatedSettings: function() {
				var result = [
						{
							value: '',
							label: '--',
						}
					],
					allowedTypes = [ 'text', 'textarea', 'wysiwyg', 'radio', 'select' ];

				for ( var i = 0; i < this.metaFields.length; i++ ) {

					if ( 'field' !== this.metaFields[i].object_type ) {
						continue;
					}

					if ( -1 === allowedTypes.indexOf( this.metaFields[i].type ) ) {
						continue;
					}

					if ( 'select' === this.metaFields[i].type && this.metaFields[i].is_multiple ) {
						continue;
					}

					result.push( {
						value: this.metaFields[i].name,
						label: this.metaFields[i].title,
					} );
				}

				return result;

			},
			fieldsForColumns: function() {

				var result = [ JetEngineCCTConfig.service_fields[0] ];

				for ( var i = 0; i < this.metaFields.length; i++ ) {
					result.push( this.metaFields[ i ] );
				}

				for ( var j = 1; j < JetEngineCCTConfig.service_fields.length; j++ ) {

					if ( 'cct_single_post_id' === JetEngineCCTConfig.service_fields[ j ].name && ! this.generalSettings.has_single ) {
						continue;
					}

					result.push( JetEngineCCTConfig.service_fields[ j ] );

				}

				return result;

			},
		},
		methods: {
			isAllowedForAdminCols: function( field ) {

				if ( ! field.name ) {
					return false;
				}

				if ( 'html' === field.type ) {
					return false;
				}

				var allowedObjectTypes = [ 'field', 'service_field' ];

				if ( 0 > allowedObjectTypes.indexOf( field.object_type ) ) {
					return false;
				}

				return true;

			},
			ensureServiceColumns: function() {
				for ( var i = 0; i < JetEngineCCTConfig.service_fields.length; i++ ) {
					if ( ! this.generalSettings.admin_columns[ JetEngineCCTConfig.service_fields[ i ].name ] ) {
						if ( 0 === i ) {
							this.$set( this.generalSettings.admin_columns, JetEngineCCTConfig.service_fields[ i ].name, {
								enabled: true,
								prefix: '#',
								is_sortable: true,
								is_num: true,
							} );
						} else {
							this.$set( this.generalSettings.admin_columns, JetEngineCCTConfig.service_fields[ i ].name, {
								enabled: false
							} );
						}
					}
				};
			},
			preSetSlug: function() {

				if ( ! this.generalSettings.slug ) {

					var regex = /\s+/g,
						slug  = this.generalSettings.name.toLowerCase().replace( regex, '_' );

					// Replace accents
					slug = slug.normalize( 'NFD' ).replace( /[\u0300-\u036f\(\)\*\/\\\~\`\!\@\#\$\%\^\:\&\|\[\]\?\"\']/g, "" );

					// Replace cyrillic
					slug = window.JetEngineTools.maybeCyrToLatin( slug );

					if ( 20 < slug.length ) {
						slug = slug.substr( 0, 20 );

						if ( '-' === slug.slice( -1 ) ) {
							slug = slug.slice( 0, -1 );
						}
					}

					this.$set( this.generalSettings, 'slug', slug );

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
					path      = JetEngineCCTConfig.api_path_edit;

				if ( JetEngineCCTConfig.item_id ) {
					path += JetEngineCCTConfig.item_id;
				}

				for ( var errKey in this.errors ) {

					if ( ! self.generalSettings[ errKey ] ) {
						self.$set( this.errors, errKey, true );

						self.$CXNotice.add( {
							message: JetEngineCCTConfig.notices[ errKey ],
							type: 'error',
							duration: 7000,
						}, 'name' );

						//self.errorNotices.push( JetEngineCCTConfig.notices.name );
						hasErrors = true;
					}

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
						meta_fields: self.metaFields,
					}
				} ).then( function( response ) {

					if ( response.success ) {
						if ( JetEngineCCTConfig.redirect ) {
							window.location = JetEngineCCTConfig.redirect.replace( /%id%/, response.item_id );
						} else {

							self.$CXNotice.add( {
								message: JetEngineCCTConfig.notices.success,
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

							} );

							self.saving = false;
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

})( jQuery, window.JetEngineCCTConfig );
