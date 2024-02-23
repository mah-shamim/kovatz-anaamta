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
		computed: {
			slugsList: function() {
				var result = [];

				for ( var i = 0; i < this.itemsList.length; i++ ) {
					result.push( this.itemsList[i].slug );
				}

				return result;
			}
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

				var result = '';

				if ( 'post' === item.args.object_type ) {
					if ( item.args.allowed_post_type && item.args.allowed_post_type.length ) {
						result = item.args.allowed_post_type.join( ', ' );
					}
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

				var self = this,
					itemData = JSON.parse( JSON.stringify( item ) ),
					newSlug = itemData.slug + '_copy',
					newItemData = {
						args:   itemData.args,
						fields: itemData.fields,
						slug:   -1 === this.slugsList.indexOf( newSlug ) ? newSlug : newSlug + '_' + Math.floor( ( Math.random() * 99 )  + 1 ),
						labels: {
							name:      itemData.labels.name + ' (Copy)',
							menu_name: itemData.labels.menu_name + ' (Copy)',
						},
					};

				wp.apiFetch( {
					method: 'post',
					path: JetEngineCPTListConfig.add_api_path,
					data: {
						general_settings: Object.assign(
							{},
							newItemData.labels,
							newItemData.args,
							{
								slug: newItemData.slug,
							},
						),
						fields: newItemData.fields,
					},
				} ).then( function( response ) {

					if ( response.success && response.item_id ) {

						newItemData.id = response.item_id;

						self.itemsList.unshift( newItemData );

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
