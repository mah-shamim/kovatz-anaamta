(function( $, JetEngineCCTListConfig ) {

	'use strict';

	window.JetEngineCCTList = new Vue( {
		el: '#jet_cct_list',
		template: '#jet-cct-list',
		data: {
			itemsList: [],
			errorNotices: [],
			editLink: JetEngineCCTListConfig.edit_link,
			showDeleteDialog: false,
			deletedItem: {},
			prefix: JetEngineCCTListConfig.db_prefix,
			showCopyDialog: false,
			copiedItem: {},
		},
		computed: {
			slugsList: function() {
				var result = [];

				for ( var i = 0; i < this.itemsList.length; i++ ) {
					result.push( this.itemsList[i].slug );
				}

				return result;
			},
		},
		mounted: function() {

			var self = this;

			wp.apiFetch( {
				method: 'get',
				path: JetEngineCCTListConfig.api_path,
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
				this.copiedItem     = item;
				this.showCopyDialog = true;
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

})( jQuery, window.JetEngineCCTListConfig );
