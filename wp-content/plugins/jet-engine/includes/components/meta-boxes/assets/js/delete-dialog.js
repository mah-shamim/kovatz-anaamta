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
			postsAction: 'none',
			attachTo: '',
			postTypes: window.JetEngineCPTDeleteDialog.types
		};
	},
	watch: {
		value: function( val ) {
			this.setVisibility( val );
		}
	},
	computed: {
		availablePostTypes: function() {
			var self = this;
			return self.postTypes.filter( function( item ) {
				return item.value !== self.postTypeSlug;
			} );
		},
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
				path: JetEngineCPTDeleteDialog.api_path + self.itemId,
			} ).then( function( response ) {

				if ( response.success ) {
					window.location = JetEngineCPTDeleteDialog.redirect;
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