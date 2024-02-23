(function( $, JetEngineCPTListConfig ) {

	'use strict';

	window.JetEngineCPTList = new Vue( {
		el: '#jet_cpt_list',
		template: '#jet-cpt-list',
		data: {
			errorNotices: [],
			editLink: JetEngineCPTListConfig.edit_link,
			showDeleteDialog: false,
			deletedItem: {},
			showTypes: 'jet-engine',
			builtInTypes: JetEngineCPTListConfig.built_in_types,
			engineTypes: JetEngineCPTListConfig.engine_types
		},
		computed: {
			itemsList: function() {
				var result = [];

				if ( 'jet-engine' === this.showTypes ) {
					result = this.engineTypes;
				} else {
					result = this.builtInTypes;
				}

				return result;
			},
		},
		methods: {
			switchType: function() {
				if ( 'jet-engine' === this.showTypes ) {
					this.showTypes = 'built-in';
				} else {
					this.showTypes = 'jet-engine';
				}
			},
			copyItem: function( item ) {

				if ( !item || !item.id ) {
					return;
				}

				var self = this;

				wp.apiFetch( {
					method: 'post',
					path: JetEngineCPTListConfig.api_path_copy + item.id,
				} ).then( function( response ) {

					if ( response.success && response.item ) {

						self.engineTypes.unshift( response.item );

						self.$CXNotice.add( {
							message: JetEngineCPTListConfig.notices.copied,
							type: 'success',
						} );

					} else {
						if ( response.notices.length ) {
							response.notices.forEach( function( notice ) {

								self.$CXNotice.add( {
									message: notice.message,
									type: 'error',
									duration: 7000,
								} );


							} );
						}
					}
				} ).catch( function( response ) {

					self.$CXNotice.add( {
						message: response.message,
						type: 'error',
						duration: 7000,
					} );

				} );

			},
			deleteItem: function( item ) {
				this.deletedItem      = item;
				this.showDeleteDialog = true;
			},
			getEditLink: function( id, slug ) {

				var editLink = this.editLink.replace( /%id%/, id );

				if ( 'built-in' === this.showTypes ) {
					editLink += '&edit-type=built-in&post-type=' + slug;
				}

				return editLink;

			}
		}
	} );

})( jQuery, window.JetEngineCPTListConfig );
