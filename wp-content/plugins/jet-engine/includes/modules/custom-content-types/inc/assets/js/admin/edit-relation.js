'use strict';

Vue.component( 'jet-cct-relation', {
	template: '#jet-cct-relation',
	props: [ 'value', 'relationArgs', 'objectTypes' ],
	data() {
		return {
			args: {},
			typesData: window.JetCCTRelationConfig.types,
		};
	},
	created() {

		if ( this.value ) {
			this.args = _.assign( {}, this.value );
		}

	},
	methods: {
		getActiveTypes() {

			const objects = [ this.relationArgs.parent_object, this.relationArgs.child_object ];
			const activeTypes = [];

			for ( var i = 0; i < objects.length; i++ ) {
				if ( objects[ i ] && objects[ i ].includes( 'cct::' ) ) {
					activeTypes.push( objects[ i ] );
				}
			}

			return activeTypes;
		},
		ensureArgs() {

			const activeTypes = this.getActiveTypes();

			for ( const [ key, value ] of Object.entries( this.args ) ) {
				if ( ! activeTypes.includes( key ) ) {
					delete this.args[ key ];
				}
			}

			for ( var i = 0; i < activeTypes.length; i++ ) {
				if ( ! this.args[ activeTypes[ i ] ] ) {
					this.$set( this.args, activeTypes[ i ], {
						title_field: '',
						create_fields: [],
					} );
				}
			}

			return true;
		},
		getTypeLabel( slug ) {

			if ( this.typesData[ slug ] ) {
				return this.typesData[ slug ].label;
			} else {
				return slug;
			}

		},
		getTypeFields( slug ) {
			if ( this.typesData[ slug ] ) {
				return this.typesData[ slug ].options;
			} else {
				return [];
			}
		},
		showCCTSettings() {
			const activeTypes = this.getActiveTypes();
			return ( 0 < activeTypes.length );
		},
		setArg( value, contentType, field ) {

			this.$set( this.args, contentType, _.assign( {}, this.args[ contentType ], {
				[ field ]: value
			} ) );

			this.$emit( 'input', this.args );
		}
	}
} );
