Vue.component( 'jet-rest-notification', {
	template: '#jet-rest-notification',
	props: [ 'value', 'fields' ],
	data: function () {
		return {
			result: {},
			formFields: [],
			authTypes: window.JetEngineRestData.auth_types,
		};
	},
	created: function() {

		this.result = this.value;

		if ( ! this.result ) {
			this.result = {};
		}

	},
	computed: {
		fieldsString: function() {

			var macros = [];

			for (var i = 0; i < this.fields.length; i++) {
				macros.push( '%' + this.fields[ i ] + '%' );
			}

			return macros.join( ', ' );

		}
	},
	methods: {
		setField: function( $event, key ) {

			var value;

			if ( 'checkbox' === $event.target.type  ) {
				value = $event.target.checked;
			} else {
				value = $event.target.value;
			}

			this.$set( this.result, key, value );
			this.$emit( 'input', this.result );

		},
	}
});