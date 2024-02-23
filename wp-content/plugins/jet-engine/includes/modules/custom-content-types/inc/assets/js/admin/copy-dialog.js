Vue.component( 'jet-cct-copy-dialog', {
	name: 'jet-cct-copy-dialog',
	template: '#jet-cct-copy-dialog',
	props: {
		value: {
			type: Boolean,
			default: false,
		},
		item: {
			type: Object,
			default: {},
		},
		slugsList: {
			type: Array,
			default: [],
		},
	},
	data: function() {
		return {
			isVisible: this.value,
			copiedItemName: this.item.args.name,
			copiedItemSlug: this.item.slug,
			slugError: false,
			slugErrorNotice: JetEngineCCTCopyDialog.notices.slug_error,
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

			this.checkSlug();

			if ( this.slugError ) {
				this.setVisibility( true );
				return;
			}

			var self = this,
				item = JSON.parse( JSON.stringify( this.item ) );

			item.args.name = this.copiedItemName;
			item.args.slug = this.copiedItemSlug;
			item.slug      = this.copiedItemSlug;

			self.setVisibility( false );

			wp.apiFetch( {
				method: 'post',
				path: JetEngineCCTCopyDialog.api_path,
				data: {
					general_settings: item.args,
					meta_fields:      item.meta_fields,
				},
			} ).then( function( response ) {

				if ( response.success && response.item_id ) {
					item.id = response.item_id;

					window.JetEngineCCTList.itemsList.unshift( item );

					self.$CXNotice.add( {
						message: JetEngineCCTCopyDialog.notices.copied,
						type: 'success',
					} );
				}

				self.$emit( 'input', false );
				self.$emit( 'on-ok' );

			} ).catch( function( e ) {

				self.$emit( 'input', false );
				self.$emit( 'on-ok' );

				console.log(e);

			} );

		},
		checkSlug: function() {
			if ( !this.copiedItemSlug || -1 !== this.slugsList.indexOf( this.copiedItemSlug ) ) {
				this.slugError = true;
			}
		},
		handleSlugBlur: function() {
			this.checkSlug();
		},
		handleSlugFocus: function() {
			this.slugError = false;
		},
		setVisibility: function( value ) {

			if ( this.isVisible === value ) {
				return;
			}

			this.isVisible = value;
		},
	},
} );