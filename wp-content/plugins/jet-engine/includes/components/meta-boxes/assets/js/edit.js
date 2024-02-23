(function( $, JetEngineMBConfig ) {

	'use strict';

	window.JetEngineMB = new Vue( {
		el: '#jet_mb_form',
		template: '#jet-mb-form',
		data: {
			generalSettings: JetEngineMBConfig.general_settings,
			metaFields: JetEngineMBConfig.meta_fields,
			allowedSources: JetEngineMBConfig.sources,
			postTypes: JetEngineMBConfig.post_types,
			taxonomies: JetEngineMBConfig.taxonomies,
			buttonLabel: JetEngineMBConfig.edit_button_label,
			isEdit: JetEngineMBConfig.item_id,
			helpLinks: JetEngineMBConfig.help_links,
			userRoles: JetEngineMBConfig.user_roles,
			showDeleteDialog: false,
			addingNewCondition: false,
			saving: false,
			errors: {
				name: false,
				slug: false,
			},
			errorNotices: [],
		},
		created: function() {
			this.ensureDefaultArrays();
		},
		mounted: function() {

			var self = this;

			//console.log( this.userRoles );

			if ( JetEngineMBConfig.item_id ) {

				wp.apiFetch( {
					method: 'get',
					path: JetEngineMBConfig.api_path_get + JetEngineMBConfig.item_id,
				} ).then( function( response ) {

					if ( response.success && response.data ) {

						self.generalSettings = response.data.general_settings;
						self.metaFields      = response.data.meta_fields;

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
				} ).then( function() {

					if ( self.$refs.allowed_posts.currentValues.length ) {
						self.$refs.allowed_posts.remoteUpdateSelected();
					}

					if ( self.$refs.excluded_posts.currentValues.length ) {
						self.$refs.excluded_posts.remoteUpdateSelected();
					}

				} ).catch( function( e ) {
					console.log( e );
				} );

			}
		},
		computed: {
			allowedConditions: function() {

				var result = [ {
					value: '',
					label: 'Select condition to add...',
				} ];

				for ( var i = 0; i < JetEngineMBConfig.conditions.length; i++ ) {
					if ( ! this.generalSettings.active_conditions.includes( JetEngineMBConfig.conditions[ i ].key ) && JetEngineMBConfig.conditions[ i ].sources.includes( this.generalSettings.object_type ) ) {
						result.push( {
							value: JetEngineMBConfig.conditions[ i ].key,
							label: JetEngineMBConfig.conditions[ i ].name,
						} );
					}
				}

				return result;

			}
		},
		methods: {
			conditionControlsInlineCSS: function( condition ) {
				return {
					order: this.generalSettings.active_conditions.indexOf( condition ),
				};
			},
			showConditionDialog: function() {

				var self = this;

				self.addingNewCondition = ! self.addingNewCondition;

				self.$nextTick().then( function() {
					self.$refs.new_condition.$el.getElementsByTagName( 'select' )[0].focus();
				} );

			},
			getHiddenOptions() {
				var options = [];

				if ( 'post' !== this.generalSettings.object_type ) {
					options.push( 'quick_editable' );
					options.push( 'revision_support' );
				}

				return options;
			},
			buildQuery: function( params ) {
				return Object.keys( params ).map(function( key ) {
					return key + '=' + params[ key ];
				}).join( '&' );
			},
			getIncludedTerms: function( query, ids ) {

				var tax = this.generalSettings.post_has_terms__tax;

				if ( ! tax ) {
					return false;
				}

				if ( ids.length ) {
					ids = ids.join( ',' );
				}

				return wp.apiFetch( {
					method: 'get',
					path: JetEngineMBConfig.api_path_search + '?' + this.buildQuery( {
						query: query,
						ids: ids,
						tax: tax,
						search_terms: true,
					} )
				} );
			},
			getPosts: function( query, ids ) {

				var postTypes = this.generalSettings.allowed_post_type.join( ',' );

				if ( ids.length ) {
					ids = ids.join( ',' );
				}

				return wp.apiFetch( {
					method: 'get',
					path: JetEngineMBConfig.api_path_search + '?' + this.buildQuery( {
						query: query,
						ids: ids,
						post_type: postTypes,
					} )
				} );

			},
			ensureDefaultArrays: function() {

				if ( ! this.generalSettings.allowed_tax ) {
					this.$set( this.generalSettings, 'allowed_tax', [] );
				}

				if ( ! this.generalSettings.allowed_post_type ) {
					this.$set( this.generalSettings, 'allowed_post_type', [] );
				}

				if ( ! this.generalSettings.allowed_posts ) {
					this.$set( this.generalSettings, 'allowed_posts', [] );
				}

				if ( ! this.generalSettings.excluded_posts ) {
					this.$set( this.generalSettings, 'excluded_posts', [] );
				}

				if ( ! this.generalSettings.active_conditions ) {
					this.$set( this.generalSettings, 'active_conditions', [] );
				}

			},
			newCondition: function( value ) {

				if ( ! this.generalSettings.active_conditions.includes( value ) ) {
					this.generalSettings.active_conditions.push( value );
				}

				this.addingNewCondition = ! this.addingNewCondition;

			},
			removeCondition: function( condition ) {

				this.$delete( this.generalSettings, condition );

				if ( this.generalSettings[ condition + '__tax' ] ) {
					this.$delete( this.generalSettings, condition + '__tax' );
				}

				if ( this.generalSettings[ condition + '__terms' ] ) {
					this.$delete( this.generalSettings, condition + '__terms' );
				}

				if ( this.generalSettings.active_conditions.includes( condition ) ) {
					this.generalSettings.active_conditions.splice(
						this.generalSettings.active_conditions.indexOf( condition ),
						1
					);
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
					path      = JetEngineMBConfig.api_path_edit;

				if ( JetEngineMBConfig.item_id ) {
					path += JetEngineMBConfig.item_id;
				}

				if ( ! self.generalSettings.name ) {
					self.$set( this.errors, 'name', true );

					self.$CXNotice.add( {
						message: JetEngineMBConfig.notices.name,
						type: 'error',
						duration: 7000,
					}, 'name' );

					//self.errorNotices.push( JetEngineMBConfig.notices.name );
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
						meta_fields: self.metaFields,
					}
				} ).then( function( response ) {

					if ( response.success ) {
						if ( JetEngineMBConfig.redirect ) {
							window.location = JetEngineMBConfig.redirect.replace( /%id%/, response.item_id );
						} else {

							self.$CXNotice.add( {
								message: JetEngineMBConfig.notices.success,
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

})( jQuery, window.JetEngineMBConfig );
