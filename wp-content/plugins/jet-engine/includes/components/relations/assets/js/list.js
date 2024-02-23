(function( $, JetEngineCPTListConfig ) {

	'use strict';

	window.JetEngineCPTList = new Vue( {
		el: '#jet_cpt_list',
		template: '#jet-cpt-list',
		data: {
			itemsList: [],
			errorNotices: [],
			editLink: JetEngineCPTListConfig.edit_link,
			relationsTypes: JetEngineCPTListConfig.relations_types,
			showDeleteDialog: false,
			deletedItem: {},
		},
		mounted: function() {

			var self = this;

			wp.apiFetch( {
				method: 'get',
				path: JetEngineCPTListConfig.api_path,
			} ).then( function( response ) {

				if ( response.success && response.data ) {
					for ( var itemID in response.data ) {
						var item = response.data[ itemID ];
						self.itemsList.push( item );
					}
				} else {
					if ( response.notices.length ) {
						response.notices.forEach( function( notice ) {
							self.errorNotices.push( notice.message );
						} );
					}
				}
			} ).catch( function( e ) {
				self.errorNotices.push( e.message );
			} );
		},
		methods: {
			copyItem: function( item ) {

				if ( !item ) {
					return;
				}

				var self = this,
					itemData = JSON.parse( JSON.stringify( item ) );

				if ( itemData.args.labels.name ) {
					itemData.args.labels.name = itemData.args.labels.name + ' (Copy)';
				}

				if ( itemData.args.name ) {
					itemData.args.name = itemData.args.name + ' (Copy)';
				}

				itemData.name = itemData.name + ' (Copy)';

				wp.apiFetch( {
					method: 'post',
					path: JetEngineCPTListConfig.api_path_add,
					data: {
						args: itemData.args,
					},
				} ).then( function( response ) {

					if ( response.success && response.item_id ) {

						itemData.id = response.item_id;

						self.itemsList.unshift( itemData );

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
			getEditLink: function( id ) {
				return this.editLink.replace( /%id%/, id );
			},
		}
	} );

})( jQuery, window.JetEngineCPTListConfig );
