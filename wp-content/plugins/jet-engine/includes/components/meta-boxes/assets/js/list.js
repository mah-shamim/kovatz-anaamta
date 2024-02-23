(function( $, JetEngineCPTListConfig ) {

	'use strict';

	window.JetEngineCPTList = new Vue( {
		el: '#jet_cpt_list',
		template: '#jet-cpt-list',
		data: {
			itemsList: [],
			errorNotices: [],
			editLink: JetEngineCPTListConfig.edit_link,
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
			verboseItemInfo: function( item ) {

				let result = '';

				if ( 'post' === item.args.object_type ) {
					if ( item.args.allowed_post_type && item.args.allowed_post_type.length ) {
						result = item.args.allowed_post_type.join( ', ' );
					}
				} else if ( 'user' === item.args.object_type ) {
					result = 'users';
				} else if ( 'woocommerce_product_data' === item.args.object_type ) {
					result = 'wc product data';
				} else if ( 'woocommerce_product_variation' === item.args.object_type ) {
					result = 'wc product variation';
				} else {
					if ( item.args.allowed_tax && item.args.allowed_tax.length ) {
						result = item.args.allowed_tax.join( ', ' );
					}
				}

				return result;

			},
			copyItem: function( item ) {

				if ( !item ) {
					return;
				}

				var self = this;

				item = JSON.parse( JSON.stringify( item ) );

				item.args.name = item.args.name + ' (Copy)';

				wp.apiFetch( {
					method: 'post',
					path: JetEngineCPTListConfig.api_path_add,
					data: {
						general_settings: item.args,
						meta_fields: item.meta_fields
					},
				} ).then( function( response ) {

					if ( response.success && response.item_id ) {

						item.id = response.item_id;

						self.itemsList.unshift( item );

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
