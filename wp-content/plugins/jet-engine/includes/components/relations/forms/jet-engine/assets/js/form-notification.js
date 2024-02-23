Vue.component( 'jet-connect-rel-notification', {
	template: '#jet-connect-rel-notification',
	props: [ 'value', 'fields', 'relations' ],
	data: function () {
		return {
			result: {},
			formFields: [],
			typeFields: [],
			isLoading: false,
		};
	},
	created: function() {

		this.result = { ...this.value };
		this.formFields = [ ...this.fields ];

		if ( ! this.result ) {
			this.result = {};
		}

	},
	methods: {
		setField: function( $event, key ) {
			var value = $event.target.value;
			this.setFieldValue( value, key );
		},
		setFieldValue: function( value, key ) {

			this.$set( this.result, key, JSON.parse( JSON.stringify( value ) ) );
			this.$emit( 'input', JSON.parse( JSON.stringify( this.result ) ) );

		},
	}
});
