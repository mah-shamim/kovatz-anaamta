Vue.component( 'jet-meta-field-options', {
	name: 'jet-meta-field-options',
	template: '#jet-meta-field-options',
	props: {
		value: {
			type: Array,
			default: function() {
				return [];
			},
		},
		field: {
			type: Object,
			default: function() {
				return {};
			},
		}
	},
	data() {
		return {
			options: []
		};
	},
	created() {
		this.options = [ ...this.value ];
	},
	watch: {
		options: {
			handler: function( val ) {
				this.$emit( 'input', val );
			},
			deep: true,
		},
	},
	methods: {
		setOptionProp: function( optionIndex, key, value ) {

			var options = [ ...this.options ];

			if ( 'is_checked' === key && ( 'radio' === this.field.type || ( 'select' === this.field.type && ! this.field.is_multiple ) ) ) {
				for ( var i = 0; i < options.length; i++ ) {
					if ( options[ i ].is_checked ) {
						options[ i ].is_checked = false;
					}
				}
			}

			options[ optionIndex ][ key ] = value;

			this.options = [ ...options ];
			//this.$emit( 'input', this.options );

		},
		getOptionSubtitle: function( option ) {

			var result = option.key;

			if ( option.is_checked ) {
				result += ' (checked)';
			}

			return result;

		},
		cloneOption: function( optionIndex ) {

			var option    = this.options[ optionIndex ],
				newOption = {
					key: option.key + '_copy',
					value: option.value + '(Copy)',
					id: this.getRandomID(),
				};

			this.options.splice( optionIndex + 1, 0, newOption );
			//this.$emit( 'input', this.options );

		},
		deleteOption: function( optionIndex ) {
			this.options.splice( optionIndex, 1 );
			//this.$emit( 'input', this.options );
		},
		addNewFieldOption: function( $event, index ) {

			var option = {
				key: '',
				value: '',
				collapsed: false,
				id: this.getRandomID(),
			};

			this.options.push( option );
			//this.$emit( 'input', this.options );

		},
		getRandomID: function() {
			return Math.floor( Math.random() * 8999 ) + 1000;
		},
		isCollapsed: function( object ) {
			if ( undefined === object.collapsed || true === object.collapsed ) {
				return true;
			} else {
				return false;
			}
		},
	}
} );

Vue.component( 'jet-meta-field', {
	name: 'jet-meta-field',
	template: '#jet-meta-field',
	props: {
		value: {
			type: Object,
			default: function() {
				return {};
			},
		},
		hideOptions: {
			type: Array,
			default: function() {
				return [];
			},
		},
		fieldTypes: {
			type: Array,
			default: function() {
				return [];
			},
		},
		fieldsNames: {
			type: Array,
			default: function() {
				return [];
			},
		},
		disabledFields: {
			type: Array,
			default: function() {
				return [];
			}
		},
		slugDelimiter: {
			type: String,
			default: function() {
				return '-';
			},
		},
		index: {
			type: Number,
			default: 0,
		}
	},
	data() {
		return {
			field: {},
			glossariesList: JetEngineFieldsConfig.glossaries,
			queriesList: JetEngineFieldsConfig.queries,
			allowedSources: JetEngineFieldsConfig.allowed_sources,
			postTypes: JetEngineFieldsConfig.post_types,
			i18n: JetEngineFieldsConfig.i18n,
			quickEditSupports: JetEngineFieldsConfig.quick_edit_supports,
		};
	},
	created() {
		this.field = { ...this.value };

		// Ensure options_source migrated correctly
		if ( ! this.field.options_source ) {
			if ( this.field.options_from_glossary ) {
				this.field.options_source = 'glossary';
			} else {
				this.field.options_source = 'manual';
			}
		}

		// Ensure options_source migrated correctly for repeater fields
		if ( 'repeater' === this.field.type ) {
			for ( var i = 0; i < this.field['repeater-fields'].length; i++ ) {
				if ( ! this.field['repeater-fields'][ i ].options_source ) {
					if ( this.field['repeater-fields'][ i ].options_from_glossary ) {
						this.field['repeater-fields'][ i ].options_source = 'glossary';
					} else {
						this.field['repeater-fields'][ i ].options_source = 'manual';
					}
				}
			}
		}

	},
	computed: {
		repeaterFieldTypes: function() {
			var skipTypes = [ 'repeater', 'html' ];
			return this.fieldTypes.filter( function( field ) {
				return ! skipTypes.includes( field.value ) && ! field.skip_repeater;
			} );
		},
	},
	methods: {
		setFieldProp: function( key, value ) {
			this.$set( this.field, key, value );
			this.$emit( 'input', this.field );
		},
		getFilteredFieldConditions: function( conditions, fieldOption ) {
			return window.JetPlugins.hooks.applyFilters( 
				'jetEngine.metaFields.fieldConditions',
				conditions,
				fieldOption,
				this.field,
				this
			);
		},
		getFilteredObjectTypes: function( objectTypes ) {
			return window.JetPlugins.hooks.applyFilters( 
				'jetEngine.metaFields.allowedObjectTypes',
				objectTypes,
				this.field,
				this
			);
		},
		getFilteredFieldTypes: function( fieldTypes ) {
			return window.JetPlugins.hooks.applyFilters( 
				'jetEngine.metaFields.allowedFieldTypes',
				fieldTypes,
				this.field,
				this
			);
		},
		preSetFieldName: function() {

			if ( ! this.field.name && this.field.title ) {
				this.sanitizeFieldName();
			}

		},
		sanitizeFieldName: function() {

			var names = this.fieldsNames;
			var regex = /\s+/g;
			var name  = this.field.name || this.field.title;
			
			name = name.toLowerCase().replace( regex, this.slugDelimiter );;
			name = window.JetEngineTools.maybeCyrToLatin( name );
			names.splice( this.index, 1 );

			if ( -1 !== names.indexOf( name ) ) {
				name = name + '_' + Math.floor( Math.random() * Math.floor( 999 ) );
			}

			this.$set( this.field, 'name', name );

		},
		preSetRepeaterFieldName: function( repeaterFieldIndex ) {

			var repeaterField = this.field['repeater-fields'][ repeaterFieldIndex ];

			if ( ! repeaterField.name && repeaterField.title ) {
				repeaterField.name = repeaterField.title;
				this.field['repeater-fields'].splice( repeaterFieldIndex, 1, repeaterField );
				this.sanitizeRepeaterFieldName( repeaterFieldIndex );
			}

		},
		sanitizeRepeaterFieldName: function( repeaterFieldIndex ) {

			var repeaterField  = this.field['repeater-fields'][ repeaterFieldIndex ],
				needModifyName = false;

			var regex = /\s+/g;
			repeaterField.name = repeaterField.name.toLowerCase().replace( regex, this.slugDelimiter );
			repeaterField.name = window.JetEngineTools.maybeCyrToLatin( repeaterField.name );

			for ( var i = 0; i < this.field['repeater-fields'].length; i++ ) {

				if ( i === repeaterFieldIndex ) {
					continue;
				}

				if ( this.field['repeater-fields'][i].name === repeaterField.name ) {
					needModifyName = true;
					break;
				}
			}

			if ( needModifyName ) {
				repeaterField.name = repeaterField.name + '_' + Math.floor( Math.random() * Math.floor( 999 ) );
			}

			this.field['repeater-fields'].splice( repeaterFieldIndex, 1, repeaterField );
			this.$emit( 'input', this.field );

		},
		setRepeaterFieldProp: function( repeaterFieldIndex, key, value ) {

			var repeaterField = this.field['repeater-fields'][ repeaterFieldIndex ];

			repeaterField[ key ] = value;

			this.field['repeater-fields'].splice( repeaterFieldIndex, 1, repeaterField );
			this.$emit( 'input', this.field );

		},

		cloneRepeaterField: function( childIndex ) {

			var newField = { ...this.field['repeater-fields'][ childIndex ] };

			newField.title = newField.title + ' (Copy)';
			newField.name  = newField.name + '_copy';
			newField.id    = this.getRandomID();

			this.field['repeater-fields'].splice( childIndex + 1, 0, newField );
			this.$emit( 'input', this.field );

		},
		deleteRepeaterField: function( childIndex ) {

			// Maybe clear a `Title Field` value.
			this.maybeClearRepeaterTitleField( childIndex );

			// Remove conditions dependency
			this.deleteRepeaterConditionsDependency( this.field['repeater-fields'][ childIndex ].name );

			this.field['repeater-fields'].splice( childIndex, 1 );
			this.$emit( 'input', this.field );
		},
		addNewRepeaterField: function( $event ) {

			var rField = {
				title: '',
				name: '',
				type: 'text',
				options_source: 'manual',
				collapsed: false,
				id: this.getRandomID(),
			};

			if ( ! this.field['repeater-fields'] ) {
				this.field['repeater-fields'] = [];
			}

			this.field['repeater-fields'].push( rField );
			this.$emit( 'input', this.field )

		},

		addNewRepeaterFieldOption: function( $event, rIndex, index ) {
			var option = {
				key: '',
				value: '',
				collapsed: false,
				id: this.getRandomID(),
			};

			if ( ! this.fieldsList[ index ]['repeater-fields'][ rIndex ].options ) {
				this.$set( this.fieldsList[ index ]['repeater-fields'][ rIndex ], 'options', [] );
			}

			this.fieldsList[ index ]['repeater-fields'][ rIndex ].options.push( option );
			//this.onInput();
		},
		cloneRepeaterFieldOption: function( optionIndex, rFieldIndex ) {
			var field     = this.fieldsList[ fieldIndex ]['repeater-fields'][ rFieldIndex ],
				option    = field.options[ optionIndex ],
				newOption = {
					key: option.key + '_copy',
					value: option.value + '(Copy)',
					id: this.getRandomID(),
				};

			field.options.splice( optionIndex + 1, 0, newOption );

			this.fieldsList[ fieldIndex ]['repeater-fields'].splice( rFieldIndex, 1, field );
			//this.onInput();
		},
		deleteRepeaterFieldOption: function( optionIndex, rFieldIndex, fieldIndex ) {
			this.fieldsList[ fieldIndex ]['repeater-fields'][ rFieldIndex ].options.splice( optionIndex, 1 );
		},
		setRepeaterFieldOptionProp: function( fieldIndex, rFieldIndex, optionIndex, key, value ) {
			var field  = this.fieldsList[ fieldIndex ]['repeater-fields'][ rFieldIndex ],
				option = field.options[ optionIndex ];

			if ( 'is_checked' === key && ( 'radio' === field.type || ( 'select' === field.type && ! field.is_multiple ) ) ) {
				for ( var i = 0; i < field.options.length; i++ ) {
					if ( field.options[ i ].is_checked ) {
						field.options[ i ].is_checked = false;
					}
				}
			}

			option[ key ] = value;

			field.options.splice( optionIndex, 1, option );

			this.fieldsList[ fieldIndex ]['repeater-fields'].splice( rFieldIndex, 1, field );
			//this.onInput();
		},

		getRepeaterTitleFields: function() {
			var allowedTypes = [ 'text', 'textarea', 'radio', 'select', 'posts' ],
				titleFieldsList = [],
				repeaterFields;

			if ( 'field' !== this.field.object_type || 'repeater' !== this.field.type ) {
				return titleFieldsList;
			}

			titleFieldsList.push( {
				value: '',
				label: this.i18n.select_field,
			} );

			if ( undefined === this.field['repeater-fields'] ) {
				return titleFieldsList;
			}

			repeaterFields = this.field['repeater-fields'];

			if ( ! repeaterFields.length ) {
				return titleFieldsList;
			}

			for ( var i = 0; i < repeaterFields.length; i++ ) {

				if ( -1 === allowedTypes.indexOf( repeaterFields[i].type ) ) {
					continue;
				}

				titleFieldsList.push( {
					value: repeaterFields[i].name,
					label: repeaterFields[i].title,
				} );

			}

			return titleFieldsList;

		},
		maybeClearRepeaterTitleField: function( childIndex ) {
			if ( this.field.repeater_title_field === this.field['repeater-fields'][ childIndex ].name ) {
				this.field.repeater_title_field = '';
			}
		},
		deleteRepeaterConditionsDependency: function( fieldName ) {
			for ( var i = 0; i < this.field['repeater-fields'].length; i++ ) {

				if ( undefined === this.field['repeater-fields'][i].conditions ) {
					continue;
				}

				if ( ! this.field['repeater-fields'][i].conditions.length ) {
					continue;
				}

				for ( var j = 0; j < this.field['repeater-fields'][i].conditions.length; j++ ) {

					if ( fieldName !== this.field['repeater-fields'][i].conditions[j].field ) {
						continue;
					}

					this.field['repeater-fields'][i].conditions.splice( j, 1 );
				}
			}
		},
		showCondition: function( field ) {

			var result     = true,
				objectType = field.object_type ? field.object_type : 'field';

			if ( -1 !== this.disabledFields.indexOf( 'conditional_logic' ) ) {
				result = false;
			} else if ( 'endpoint' === objectType ) {
				result = false;
			} else if ( 'field' === objectType && 'html' === field.type ) {
				result = false;
			}

			return window.JetPlugins.hooks.applyFilters(
				'jetEngine.metaFields.showConditionsEditor',
				result,
				field
			);
		},
		showConditionPopup: function() {
			this.$emit( 'show-condition-popup' );
		},
		showRepeaterConditionPopup: function( rFieldIndex ) {
			this.$emit( 'show-repeater-condition-popup', rFieldIndex );
		},
		hasConditions: function( object ) {
			return object.conditional_logic && object.conditions && object.conditions.length;
		},
		getRandomID: function() {
			return Math.floor( Math.random() * 8999 ) + 1000;
		},
		isCollapsed: function( object ) {
			if ( undefined === object.collapsed || true === object.collapsed ) {
				return true;
			} else {
				return false;
			}
		},
	}
} );

Vue.component( 'jet-meta-fields', {
	name: 'jet-meta-fields',
	template: '#jet-meta-fields',
	props: {
		value: {
			type: Array,
			default: function() {
				return [];
			},
		},
		hideOptions: {
			type: Array,
			default: function() {
				return [];
			},
		},
		slugDelimiter: {
			type: String,
			default: function() {
				return '-';
			},
		},
	},
	data: function() {
		return {
			fieldsList: this.value,
			fieldTypes: JetEngineFieldsConfig.field_types,
			allowedFieldTypes: JetEngineFieldsConfig.allowed_types,
			postTypes: JetEngineFieldsConfig.post_types,
			blockTitle: JetEngineFieldsConfig.title,
			buttonLabel: JetEngineFieldsConfig.button,
			disabledFields: JetEngineFieldsConfig.disabled,
			i18n: JetEngineFieldsConfig.i18n,

			currentConditionIndex: null,
			currentConditionRepIndex: null,
			currentConditionField: null,
			isVisibleConditionPopup: false,
		};
	},
	created: function() {

		if ( this.allowedFieldTypes ) {

			const fields = this.fieldTypes.filter( ( item ) => {
				return this.allowedFieldTypes.includes( item.value );
			} );

			this.fieldTypes = fields;

		}

	},
	watch: {
		value: function( val ) {
			var openedTab = false;

			for ( var i = 0; i < val.length; i++ ) {
				switch ( val[i].object_type ) {
					case 'field':
						val[i].isNested = openedTab;
						break;

					case 'tab':
					case 'accordion':
						openedTab = true;
						val[i].isNested = false;
						break;

					case 'endpoint':
						openedTab = false;
						val[i].isNested = false;
						break;
				}
			}

			this.fieldsList = val;
		},
		fieldsList: {
			handler: function( val ) {
				this.$emit( 'input', val );
			},
			deep: true,
		},
	},
	computed: {
		fieldsNames: function() {
			var result = [];

			for ( var i = 0; i < this.fieldsList.length; i++ ) {
				result.push( this.fieldsList[i].name );
			}

			return result;
		},
	},
	methods: {
		onInput: function() {
			this.$emit( 'input', this.fieldsList );
		},
		isFieldCollapsed: function( fieldID ) {
			if ( ! this.$refs[ 'field' + fieldID ] ) {
				return false;
			} else {
				return this.$refs[ 'field' + fieldID ][0].isCollapsed;
			}
		},
		getFieldSubtitle: function( field ) {

			var result = field.name + ' (';

			if ( 'field' === field.object_type ) {
				result += field.type;
			} else {
				result += field.object_type;
			}

			result += ')';

			return result;

		},
		addNewField: function( event ) {

			var field = {
				title: '',
				name: '',
				object_type: 'field',
				width: '100%',
				options: [],
				'repeater-fields': [],
				type: 'text',
				collapsed: false,
				id: this.getRandomID(),
			};

			this.fieldsList.push( field );

		},
		
		cloneField: function( index ) {

			var newField = JSON.parse( JSON.stringify( this.fieldsList[index] ) );

			newField.title = newField.title + ' (Copy)';
			newField.name  = newField.name + '_copy';
			newField.id    = this.getRandomID();

			this.fieldsList.splice( index + 1, 0, newField );

		},
		deleteField: function( index ) {

			// Remove conditions dependency
			this.deleteConditionsDependency( this.fieldsList[index].name );

			this.fieldsList.splice( index, 1 );
		},

		showCondition: function( field ) {

			var result = true;

			if ( -1 !== this.disabledFields.indexOf( 'conditional_logic' ) ) {
				result = false;
			} else if ( 'endpoint' === field.object_type ) {
				result = false;
			} else if ( 'field' === field.object_type && 'html' === field.type ) {
				result = false;
			}

			return window.JetPlugins.hooks.applyFilters(
				'jetEngine.metaFields.showConditionsEditor',
				result,
				field
			);
		},
		showConditionPopup: function( fieldIndex, rFieldIndex ) {
			rFieldIndex = undefined !== rFieldIndex ? rFieldIndex : null;

			this.currentConditionIndex = fieldIndex;
			this.currentConditionRepIndex = rFieldIndex;
			this.currentConditionField = null !== rFieldIndex ? this.fieldsList[ fieldIndex ]['repeater-fields'][ rFieldIndex ] : this.fieldsList[ fieldIndex ];
			this.isVisibleConditionPopup = true;
		},
		hideConditionPopup: function() {
			this.currentConditionIndex = null;
			this.currentConditionRepIndex = null;
			this.currentConditionField = null;
			this.isVisibleConditionPopup = false;
		},
		hasConditions: function( object ) {
			return object.conditional_logic && object.conditions && object.conditions.length;
		},
		setConditionsFieldProps: function( fieldIndex, rFieldIndex, valueObj ) {

			var field = this.fieldsList[ fieldIndex ];

			if ( null !== rFieldIndex ) {

				var	repeaterField = field['repeater-fields'][ rFieldIndex ];

				repeaterField.conditional_logic    = valueObj.isEnabled;
				repeaterField.conditions           = valueObj.conditions;
				repeaterField.conditional_relation = valueObj.relation;

				field['repeater-fields'].splice( rFieldIndex, 1, repeaterField );

			} else {

				field.conditional_logic    = valueObj.isEnabled;
				field.conditions           = valueObj.conditions;
				field.conditional_relation = valueObj.relation;
			}

			this.fieldsList.splice( fieldIndex, 1, field );
		},

		deleteConditionsDependency: function( fieldName ) {
			for ( var i = 0; i < this.fieldsList.length; i++ ) {

				if ( 'field' !== this.fieldsList[i].object_type ) {
					continue;
				}

				if ( undefined === this.fieldsList[i].conditions ) {
					continue;
				}

				if ( !this.fieldsList[i].conditions.length ) {
					continue;
				}

				for ( var j = 0; j < this.fieldsList[i].conditions.length; j++ ) {

					if ( fieldName !== this.fieldsList[i].conditions[j].field ) {
						continue;
					}

					this.fieldsList[i].conditions.splice( j, 1 );
				}
			}
		},

		isCollapsed: function( object ) {
			if ( undefined === object.collapsed || true === object.collapsed ) {
				return true;
			} else {
				return false;
			}
		},

		isNestedField: function( field ) {
			if ( undefined !== field.isNested && field.isNested ) {
				return true;
			}

			return false;
		},

		getRandomID: function() {
			return Math.floor( Math.random() * 8999 ) + 1000;
		},
	},
} );

window.JetPlugins.hooks.addFilter( 
	'jetEngine.metaFields.fieldConditions', 
	'jetWCFields', 
	( conditions, fieldName, field, fields ) => {
		return conditions;
	}
);
