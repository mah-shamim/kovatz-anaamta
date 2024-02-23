Vue.component( 'jet-cct-delete-dialog', {
	name: 'jet-cct-delete-dialog',
	template: '#jet-cct-delete-dialog',
	props: {
		value: {
			type: Boolean,
			default: false,
		},
		itemId: {
			type: String,
			default: '',
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
				method: 'delete',
				path: JetEngineCCTDeleteDialog.api_path + self.itemId,
			} ).then( function( response ) {

				if ( response.success ) {
					window.location = JetEngineCCTDeleteDialog.redirect;
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