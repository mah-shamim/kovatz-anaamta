(function( $, JetEngineCPTConfig ) {

	'use strict';

	Vue.component( 'jet-engine-admin-filters', {
		name: 'jet-engine-admin-filters',
		template: '#jet-engine-admin-filters',
		props: [ 'value', 'metaFields', 'postType' ],
		data() {
			return {
				adminFilters: [],
				adminFiltersTypes: JetEngineCPTConfig.admin_filters_types,
				taxonomies: JetEngineCPTConfig.taxonomies_full_data,
				allFields: JetEngineCPTConfig.all_fields,
				glossariesList: JetEngineCPTConfig.glossaries_list
			};
		},
		created() {

			let val = [];

			if ( this.value && this.value.length ) {
				val = this.value;
			}

			this.adminFilters = [ ...val ];

		},
		watch: {
			value( newVal ) {
				if ( ! this.adminFilters.length ) {
					this.adminFilters = [ ...newVal ];
				}
			}
		},
		computed: {
			currentTypeFields() {

				var result = [];
				var existing = [];
				var skip = [ 'repeater' ];

				for ( var i = 0; i < this.metaFields.length; i++ ) {

					if ( skip.includes( this.metaFields[ i ].type ) || ( this.metaFields[ i ].object_type && 'field' !== this.metaFields[ i ].object_type ) ) {
						continue;
					}

					result.push( {
						value: this.metaFields[ i ].name,
						label: this.metaFields[ i ].title,
						type: this.metaFields[ i ].type,
					} );

					existing.push( this.metaFields[ i ].name );

				}

				if ( this.postType && this.allFields[ this.postType ] ) {

					var postTypeFields = this.allFields[ this.postType ];

					for ( var i = 0; i < postTypeFields.length; i++ ) {

						if ( skip.includes( postTypeFields[ i ].type ) || ( postTypeFields[ i ].object_type && 'field' !== postTypeFields[ i ].object_type ) ) {
							continue;
						}

						if ( ! existing.includes( postTypeFields[ i ].name ) ) {
							result.push( {
								value: postTypeFields[ i ].name,
								label: postTypeFields[ i ].title,
								type: postTypeFields[ i ].type
							} );
						}
					}
				}

				return result;
			},
			taxonomiesData() {
				var result = [];

				for ( var i = 0; i < this.taxonomies.length; i++ ) {
					let tax = this.taxonomies[ i ];

					if ( tax.objects.includes( this.postType ) ) {
						result.push( tax );
					}

				}

				return result;

			}
		},
		methods: {
			optionsSources( filter ) {

				var result = [
					{
						value: 'glossary',
						label: 'Glossary',
					},
				];

				if ( ! filter.custom_meta_key && filter.meta_key ) {

					for ( var i = 0; i < this.currentTypeFields.length; i++ ) {

						var currentField = this.currentTypeFields[ i ];

						if ( filter.meta_key === currentField.value ) {

							switch ( currentField.type ) {
								case 'select':
								case 'radio':

									result.push( {
										value: 'db',
										label: 'Select from database',
									} );

									result.push( {
										value: 'field_options',
										label: 'Field options',
									} );

									break;

								case 'checkbox':
									result.push( {
										value: 'field_options',
										label: 'Field options',
									} );

									break;

								default:
									result.push( {
										value: 'db',
										label: 'Select from database',
									} );
							}

						}

					}
				} else {
					result.push( {
						value: 'db',
						label: 'Select from database',
					} );
				}

				return result;

			},
			isHierarchical( taxSlug ) {

				for (var i = this.taxonomiesData.length - 1; i >= 0; i--) {
					let tax = this.taxonomiesData[ i ];
					if ( taxSlug === tax.slug ) {
						return tax.hierarchical;
					};
				}

				return false;

			},
			taxonomiesList() {

				var result = [];

				for ( var i = 0; i < this.taxonomiesData.length; i++ ) {

					let tax = this.taxonomiesData[ i ];

					result.push( {
						value: tax.slug,
						label: tax.name
					} );

				}

				return result;
			},
			hasTaxonomies() {

				if ( ! this.postType ) {
					return false;
				}

				return this.taxonomiesData.length;

			},
			onInput() {
				this.$emit( 'input', this.adminFilters );
			},
			allowedFieldsForOptions() {

				const res = [ {
					value: '',
					label: 'Select field...',
				} ];

				for ( var i = 0; i < this.metaFields.length; i++ ) {
					res.push( {
						value: this.metaFields[i],
						label: this.metaFields[i],
					} );
				}
				return res;
			},
			addNewField( event, props, parent, callback ) {

				props = props || [];

				var field = {};

				for ( var i = 0; i < props.length; i++ ) {
					field[ props[ i ] ] = '';
				}

				field._id = Math.round( Math.random() * 1000000 );
				field.collapsed = false;

				parent.push( field );

				if ( callback && 'function' === typeof callback ) {
					callback( field, parent );
				}

				this.onInput();

			},
			setFieldProp( id, key, value, parent ) {

				let index = this.searchByID( id, parent );

				if ( false === index ) {
					return;
				}

				let field = parent[ index ];

				field[ key ] = value;

				parent.splice( index, 1, field );

				this.onInput();

			},
			cloneField( index, id, parent, callback ) {

				let field = JSON.parse( JSON.stringify( parent[ index ] ) );

				field.collapsed = false;
				field._id = Math.round( Math.random() * 1000000 );

				parent.splice( index + 1, 0, field );

				if ( callback && 'function' === typeof callback ) {
					callback( field, parent, id );
				}

				this.onInput();

			},
			deleteField( index, id, parent, callback ) {

				index = this.searchByID( id, parent );

				if ( false === index ) {
					return;
				}

				parent.splice( index, 1 );

				if ( callback && 'function' === typeof callback ) {
					callback( id, index, parent );
				}

				this.onInput();

			},
			isCollapsed( parent ) {
				if ( undefined === parent.collapsed || true === parent.collapsed ) {
					return true;
				} else {
					return false;
				}
			},
			searchByID( id, list ) {

				for ( var i = 0; i < list.length; i++ ) {
					if ( id == list[ i ]._id ) {
						return i;
					}
				}

				return false;

			}
		}
	} );

	window.JetEngineCPT = new Vue( {
		el: '#jet_cpt_form',
		template: '#jet-cpt-form',
		data: {
			generalSettings: JetEngineCPTConfig.general_settings,
			labels: JetEngineCPTConfig.labels,
			advancedSettings: JetEngineCPTConfig.advanced_settings,
			metaFields: JetEngineCPTConfig.meta_fields,
			adminColumns: JetEngineCPTConfig.admin_columns,
			adminColumnsTypes: JetEngineCPTConfig.columns_types,
			adminFilters: [],
			icons: JetEngineCPTConfig.icons,
			supports: JetEngineCPTConfig.supports,
			metaFieldsEnabled: JetEngineCPTConfig.meta_fields_enabled,
			labelsList: JetEngineCPTConfig.labels_list,
			buttonLabel: JetEngineCPTConfig.edit_button_label,
			isEdit: JetEngineCPTConfig.item_id,
			helpLinks: JetEngineCPTConfig.help_links,
			availablePositions: JetEngineCPTConfig.positions,
			defaultMenuPosition: JetEngineCPTConfig.default_position,
			positionIDs: [],
			showDeleteDialog: false,
			isBuiltIn: false,
			saving: false,
			resetDialog: false,
			initialSlug: null,
			updatePosts: false,
			incorrectSlugMessage: JetEngineCPTConfig.slug_error,
			showIncorrectSlug: false,
			callbacks: {
				list: JetEngineCPTConfig.admin_columns_cb,
				showPopup: false,
				currentArgs: false,
				activeColumn: false,
				current: false,
				showOk: false,
			},
			errors: {
				name: false,
				slug: false,
			},
			errorNotices: [],
		},
		mounted: function() {

			var self = this,
				path = null;

			if ( JetEngineCPTConfig.is_built_in ) {
				self.isBuiltIn = true;
			}

			for ( var i = 0; i < this.availablePositions.length; i++ ) {
				this.positionIDs.push( this.availablePositions[ i ].value );
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

						if ( response.data.advanced_settings.menu_position ) {
							response.data.advanced_settings.menu_position = parseInt( response.data.advanced_settings.menu_position );
						}

						self.advancedSettings = response.data.advanced_settings;
						self.metaFields       = response.data.meta_fields;
						self.adminColumns     = response.data.admin_columns;
						self.adminFilters     = response.data.admin_filters;
						self.initialSlug      = self.generalSettings.slug;

						self.$refs.supports.setValues( self.advancedSettings.supports );

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
				self.$set(
					self.advancedSettings,
					'menu_position',
					parseInt( self.defaultMenuPosition, 10 )
				);
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
			selectCallback: function( index ) {
				this.$set( this.callbacks, 'activeColumn', index );
				this.$set( this.callbacks, 'showPopup', true );
			},
			handleCallbackPopupCancel: function() {
				this.$set( this.callbacks, 'activeColumn', false );
				this.$set( this.callbacks, 'showPopup', false );
				this.$set( this.callbacks, 'current', false );
				this.$set( this.callbacks, 'currentArgs', false );
				this.$set( this.callbacks, 'showOk', false );
			},
			handleCallbackPopupOk: function() {

				if ( this.callbacks.current ) {
					var callback = this.callbacks.current.name;

					if ( this.callbacks.currentArgs ) {
						for ( var arg in this.callbacks.currentArgs ) {
							callback += '::' + this.callbacks.currentArgs[ arg ].value;
						}
					}

					this.setColumnProp( this.callbacks.activeColumn, 'callback', callback );
					this.handleCallbackPopupCancel();
				}

			},
			handleFocus: function( where ) {

				if ( this.errors[ where ] ) {
					this.$set( this.errors, where, false );
					this.$CXNotice.close( where );
					//this.errorNotices.splice( 0, this.errorNotices.length );
				}

			},
			setCurrent: function( cbName, cbData ) {

				this.callbacks.current = {
					name: cbName,
					data: cbData,
				};

				if ( ! cbData.args ) {
					this.handleCallbackPopupOk();
				} else {
					this.$set( this.callbacks, 'currentArgs', cbData.args );
					this.$set( this.callbacks, 'showOk', true );
				}

			},
			setCBArg: function( key, value ) {
				this.$set( this.callbacks.currentArgs[ key ], 'value', value );
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

				if ( self.errorNotices.length ) {
					self.errorNotices.splice( 0, self.errorNotices.length );
				}

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
					//self.errorNotices.push( JetEngineCPTConfig.notices.name );

					self.$CXNotice.add( {
						message: JetEngineCPTConfig.notices.name,
						type: 'error',
						duration: 7000,
					}, 'name' );

					hasErrors = true;
				}

				if ( ! self.generalSettings.slug ) {
					self.$set( this.errors, 'slug', true );
					//self.errorNotices.push( JetEngineCPTConfig.notices.slug );

					self.$CXNotice.add( {
						message: JetEngineCPTConfig.notices.slug,
						type: 'error',
						duration: 7000,
					}, 'slug' );

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
						admin_columns: self.adminColumns,
						admin_filters: self.adminFilters,
						update_posts: self.updatePosts,
						initial_slug: self.initialSlug,
					}
				} ).then( function( response ) {

					if ( response.success ) {
						if ( JetEngineCPTConfig.redirect ) {
							window.location = JetEngineCPTConfig.redirect.replace( /%id%/, response.item_id );
						} else {
							self.saving = false;

							self.$CXNotice.add( {
								message: JetEngineCPTConfig.notices.success,
								type: 'success',
							} );

						}

						if ( response.item_id ) {
							self.$set( self.generalSettings, 'id', response.item_id );
						} else {
							self.$set( self.generalSettings, 'id', false );
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
						self.saving = false;
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

					if ( 20 < slug.length ) {
						slug = slug.substr( 0, 20 );

						if ( '-' === slug.slice( -1 ) ) {
							slug = slug.slice( 0, -1 );
						}
					}

					this.$set( this.generalSettings, 'slug', slug );

				}

			},
			checkSlug: function() {
				this.showIncorrectSlug = ( 20 < this.generalSettings.slug.length );
			},
			addNewAdminColumn: function() {

				var newCol = {
					type: 'meta_value',
					collapsed: false,
				};

				this.adminColumns.push( newCol );

			},
			cloneColumn: function( index ) {

				var column    = this.adminColumns[ index ],
					newColumn = {
						label: column.title + ' (Copy)',
						type: column.type,
						meta_field: column.meta_field,
						taxonomy: column.taxonomy,
						callback: column.callback,
						position: column.position,
						prefix: column.prefix,
						suffix: column.suffix,
						is_sortable: column.is_sortable,
						sort_by_field: column.sort_by_field,
						is_num: column.is_num
					};

				this.adminColumns.push( newColumn );

			},
			deleteColumn: function( index ) {
				this.adminColumns.splice( index, 1 );
			},
			setColumnProp: function( index, key, value ) {
				var column = this.adminColumns[ index ];
				column[ key ] = value;
				this.adminColumns.splice( index, 1, column );

			},
			isCollapsed: function( object ) {

				if ( undefined === object.collapsed || true === object.collapsed ) {
					return true;
				} else {
					return false;
				}

			},
			getArgComponent: function( data ) {

				switch ( data.type ) {
					case 'select':
						return 'cx-vui-select';
				}

				return 'cx-vui-input';
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
