Vue.component( 'jet-cpt-delete-dialog', {
	name: 'jet-cpt-delete-dialog',
	template: '#jet-cpt-delete-dialog',
	props: {
		value: {
			type: Boolean,
			default: false,
		},
		itemId: {
			type: String,
		},
		itemName: {
			type: String,
			default: '',
		},
	},
	data: function() {
		return {
			isVisible: this.value,
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
				method: 'DELETE',
				path: JetEngineRelationDeleteDialog.api_path + self.itemId,
				data: {}
			} ).then( function( response ) {

				if ( response.success ) {
					window.location = JetEngineRelationDeleteDialog.redirect;
				} else {
					self.$emit( 'on-error', response.notices );
				}

				self.$emit( 'input', false );
				self.$emit( 'on-ok' );

			} ).catch( function( e ) {

				self.$emit( 'input', false );
				self.$emit( 'on-error', [ {
					type: 'error',
					message: e
				} ] );

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
