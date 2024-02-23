(function( $, JetEngineRelationConfig ) {

	'use strict';

	window.JetEngineCPT = new Vue( {
		el: '#jet_cpt_form',
		template: '#jet-cpt-form',
		data: {
			args: JetEngineRelationConfig.args,
			isLegacy: false,
			postTypes: JetEngineRelationConfig.post_types,
			objectTypes: JetEngineRelationConfig.object_types,
			relationsTypes: JetEngineRelationConfig.relations_types,
			buttonLabel: JetEngineRelationConfig.edit_button_label,
			isEdit: JetEngineRelationConfig.item_id,
			helpLinks: JetEngineRelationConfig.help_links,
			existingRelations: JetEngineRelationConfig.existing_relations,
			legacyRelations: JetEngineRelationConfig.legacy_relations,
			showConvertDialog: false,
			showDeleteDialog: false,
			saving: false,
			converting: false,
			loaded: false,
			errors: {
				name: false,
				slug: false,
			},
			errorNotices: [],
		},
		created() {

			this.$set( this.args, 'meta_fields', [] );

			if ( JetEngineRelationConfig.item_id ) {

				wp.apiFetch( {
					method: 'get',
					path: JetEngineRelationConfig.api_path_get + JetEngineRelationConfig.item_id,
				} ).then( ( response ) => {

					if ( response.success && response.data ) {

						this.args     = _.assign( {}, response.data.args );
						this.isLegacy = response.data.args.is_legacy;
						this.loaded   = true;

						if ( ! this.args.meta_fields || ! this.args.meta_fields.length ) {
							this.$set( this.args, 'meta_fields', [] );
						}

					} else {
						if ( response.notices.length ) {
							response.notices.forEach( ( notice ) => {

								this.$CXNotice.add( {
									message: notice.message,
									type: 'error',
									duration: 15000,
								} );

							} );
						}
					}
				} );

			} else {
				this.loaded = true;
			}

		},
		computed: {
			availableParentRelations() {
				var result = [
					{
						value: '',
						label: 'Select...',
					}
				];

				for ( var relationKey in this.legacyRelations ) {
					result.push( {
						value: relationKey,
						label: this.legacyRelations[ relationKey ],
					} );
				}

				return result;
			},
		},
		methods: {
			handleDeletionError( errors ) {

				errors.forEach( ( error ) => {
					this.errorNotices.push( error.message );
				} );

			},
			handleFocus( where ) {

				if ( this.errors[ where ] ) {
					this.$set( this.errors, where, false );
					this.$CXNotice.close( where );
				}

			},
			convertCurrentRel() {

				this.converting = true;

				window.wp.ajax.send(
					'jet_engine_relations_convert',
					{
						type: 'GET',
						data: {
							_nonce: JetEngineRelationConfig.nonce,
							relID: JetEngineRelationConfig.item_id,
						},
						success: ( response ) => {

							this.converting = false;

							this.$CXNotice.add( {
								message: 'Done! Load new data...',
								type: 'success',
								duration: 3000,
							} );

							window.location.reload();

						},
						error: ( data, errorCode, errorText ) => {

							this.converting = false;

							this.$CXNotice.add( {
								message: data,
								type: 'error',
								duration: 15000,
							} );
						}
					}
				);

			},
			save() {

				var hasErrors = false,
					path      = JetEngineRelationConfig.api_path_edit;

				if ( JetEngineRelationConfig.item_id ) {
					path += JetEngineRelationConfig.item_id;
				}

				if ( hasErrors ) {
					return;
				}

				this.saving = true;

				wp.apiFetch( {
					method: 'post',
					path: path,
					data: {
						args: this.args,
					}
				} ).then( ( response ) => {

					if ( response.success ) {
						if ( JetEngineRelationConfig.redirect ) {
							window.location = JetEngineRelationConfig.redirect.replace( /%id%/, response.item_id );
						} else {

							this.$CXNotice.add( {
								message: JetEngineRelationConfig.notices.success,
								type: 'success',
							} );

							this.saving = false;
						}
					} else {

						if ( response.notices.length ) {
							response.notices.forEach( ( notice ) => {

								this.$CXNotice.add( {
									message: notice.message,
									type: 'error',
									duration: 7000,
								} );

								//self.errorNotices.push( notice.message );
							} );
						}

						this.saving = false;
					}
				} ).catch( ( response ) => {
					//self.errorNotices.push( response.message );

					this.$CXNotice.add( {
						message: response.message,
						type: 'error',
						duration: 7000,
					} );

					this.saving = false;
				} );

			},
		}
	} );

})( jQuery, window.JetEngineRelationConfig );
