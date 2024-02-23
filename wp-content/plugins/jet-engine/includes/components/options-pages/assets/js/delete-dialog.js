Vue.component( 'jet-cpt-delete-dialog', {
	name: 'jet-cpt-delete-dialog',
	template: '#jet-cpt-delete-dialog',
	props: {
		value: {
			type: Boolean,
			default: false,
		},
		itemId: {
			type: Number,
			default: 0,
		},
		itemName: {
			type: String,
			default: '',
		},
	},
	data: function() {
		return {
			isVisible: this.value,
			pageAction: 'none',
		};
	},
	watch: {
		value: function( val ) {
			this.setVisibility( val );
		}
	},
	methods: {
		handleCancel: function() {
			this.setVisibility( false );
			this.$emit( 'input', false );
			this.$emit( 'on-cancel' );
		},
		handleOk: function() {

			var self = this;

			self.setVisibility( false );

			wp.apiFetch( {
				method: 'delete',
				path: JetEngineDeleteDialog.api_path + self.itemId,
				data: {
					action: self.pageAction,
				}
			} ).then( function( response ) {

				if ( response.success ) {
					window.location = JetEngineDeleteDialog.redirect;
				}

				self.$emit( 'input', false );
				self.$emit( 'on-ok' );

			} ).catch( function( e ) {

				self.$emit( 'input', false );
				self.$emit( 'on-ok' );

				console.log(e);

			} );

		},
		setVisibility: function( value ) {

			if ( this.isVisible === value ) {
				return;
			}

			this.isVisible = value;
		},
	},
} );