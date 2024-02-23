Vue.component( 'jet-cct-defaults-editor', {
	template: '#jet-cct-defaults-editor',
	props: [ 'value', 'fields' ],
	data: function () {
		return {
			result: {},
		};
	},
	created: function() {

		var values = { ...this.value };

		//console.log( values );

		for ( var field in values ) {
			this.$set( this.result, field, {
				enabled: true,
				value: values[ field ],
			} );
		}

	},
	methods: {
		getFormattedResult: function() {

			var result = {};
			var items  = JSON.parse( JSON.stringify( this.result ) );

			for ( var property in items ) {
				if ( items[ property ].enabled ) {
					this.$set( result, property, items[ property ].value );
				}
			}

			return JSON.parse( JSON.stringify( result ) );

		},
		swithFieldStatus: function( $event, field ) {

			var data = { ...this.result[ field ] } || {};

			this.$set( data, 'enabled', $event.target.checked );
			this.$set( this.result, field, data );

			//console.log( this.getFormattedResult() );

			this.$emit( 'input', this.getFormattedResult() );

		},
		getCurrentVal: function( field ) {

			var data = this.result[ field ];

			if ( data ) {
				return data.value || '';
			} else {
				return '';
			}

		},
		isEnabled: function( field ) {

			var data = this.result[ field ];

			if ( data ) {
				return data.enabled;
			} else {
				return false;
			}

		},
		setFieldValue: function( value, field ) {

			var data = { ...this.result[ field ] } || {};
			this.$set( data, 'value', value );
			this.$set( this.result, field, data );

			this.$emit( 'input', this.getFormattedResult() );

		},
	},
} );

Vue.component( 'jet-cct-notification', {
	template: '#jet-cct-notification',
	props: [ 'value', 'fields', 'statuses', 'contentTypes', 'fetchPath' ],
	data: function () {
		return {
			result: {},
			formFields: [],
			typeFields: [],
			isLoading: false,
		};
	},
	created: function() {

		this.result = this.value;
		this.formFields = this.fields;

		//console.log( this.value );

		if ( ! this.result ) {
			this.result = {};
		}

		if ( ! this.result.fields_map || 'object' !== typeof this.result.fields_map ) {
			this.$set( this.result, 'fields_map', {} );
		}

		if ( ! this.result.default_fields || 'object' !== typeof this.result.default_fields ) {
			this.$set( this.result, 'default_fields', {} );
		}

		this.fetchTypeFields();

	},
	methods: {
		setField: function( $event, key ) {
			var value = $event.target.value;
			this.setFieldValue( value, key );
		},
		setFieldValue: function( value, key ) {

			this.$set( this.result, key, JSON.parse( JSON.stringify( value ) ) );
			//console.log( JSON.parse( JSON.stringify( this.result ) ) );
			this.$emit( 'input', JSON.parse( JSON.stringify( this.result ) ) );

			if ( 'type' === key ) {
				this.fetchTypeFields();
			}

		},
		setDefaultField: function( $event, key, index ) {

			var value = $event.target.value;
			var fields = Array.from( this.result.default_fields );
			var item = fields[ index ];

			this.$set( item, key, value );
			fields.splice( index, 1, item );
			this.$set( this.result, 'default_fields', Array.from( fields ) );

			this.$emit( 'input', JSON.parse( JSON.stringify( this.result ) ) );

		},
		setFieldsMap: function( $event, field ) {
			var value = $event.target.value;
			this.$set( this.result.fields_map, field, value );
			this.$emit( 'input', JSON.parse( JSON.stringify( this.result ) ) );
		},
		fetchTypeFields: function() {

			if ( ! this.result.type ) {
				return;
			}

			this.isLoading = true;

			wp.apiFetch( {
				method: 'get',
				path: this.fetchPath + '?type=' + this.result.type,
			} ).then( ( response ) => {

				if ( response.success && response.fields ) {

					var typeFields = [];

					for ( var i = 0; i < response.fields.length; i++ ) {

						if ( '_ID' === response.fields[ i ].value ) {
							response.fields[ i ].label += ' (will update the item)';
						}

						typeFields.push( response.fields[ i ] );
					};

					this.typeFields = typeFields;

				} else {

					let message = '';

					for ( var i = 0; i < response.notices.length; i++) {
						message += response.notices[ i ] + '; ';
					};

					alert( message );

				}

				this.isLoading = false;
				this.$forceUpdate();

			} ).catch( ( e ) => {
				console.log( e );
				this.isLoading = false;
				alert( e );
			} );

		},
	}
});