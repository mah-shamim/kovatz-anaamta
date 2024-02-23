Vue.component( 'jet-meta-field-conditions-dialog', {
	name: 'jet-meta-field-conditions-dialog',
	template: '#jet-meta-field-conditions-dialog',
	props: {
		value: {
			type: Object,
			default: function() {
				return {
					isEnabled: false,
					conditions: [],
					relation: 'AND',
				};
			},
		},
		field: {
			type: Object,
			default: function() {
				return {};
			}
		},
		fieldsList: {
			type: Array,
			default: function() {
				return [];
			},
		},
	},
	data: function() {
		return {
			isVisible: true,
			isEnabled: this.value.isEnabled,
			conditions: JSON.parse( JSON.stringify( this.value.conditions ) ),
			relation: this.value.relation,
			i18n: JetEngineFieldsConfig.i18n,
			operatorsList: JetEngineFieldsConfig.condition_operators,
		};
	},
	created: function() {

		for ( var i = 0; i < this.conditions.length; i++ ) {

			if ( undefined === this.conditions[ i ].collapsed ) {
				continue;
			}

			delete this.conditions[ i ].collapsed;
		}
	},
	watch: {
		valueObj: {
			handler: function( val ) {
				this.$emit( 'input', val );
			},
			deep: true,
		},
	},
	computed: {
		valueObj: function() {
			return {
				isEnabled: this.isEnabled,
				conditions: this.conditions,
				relation: this.relation,
			};
		},
		fieldsNames: function() {
			var result = [];

			for ( var i = 0; i < this.fieldsList.length; i++ ) {
				result.push( this.fieldsList[i].name );
			}

			return result;
		},
		fieldsOptionList: function() {
			var result = [],
				blackTypesList = [ 'html' ];

			for ( var i = 0; i < this.fieldsList.length; i++ ) {

				if ( this.fieldsList[i].object_type && 'field' !== this.fieldsList[i].object_type ) {
					continue;
				}

				if ( -1 !== blackTypesList.indexOf( this.fieldsList[i].type ) ) {
					continue;
				}

				result.push( {
					value: this.fieldsList[i].name,
					label: this.fieldsList[i].title,
				} );
			}

			return result;
		}
	},
	methods: {
		handleCancel: function() {
			this.isVisible = false;
			this.$emit( 'on-close' );
		},

		addNewCondition: function() {
			var condition = {
					field: '',
					operator: '',
					value: '',
					values: [],
					collapsed: false,
					id: this.getRandomID(),
				};

			if ( ! this.conditions ) {
				this.conditions = [];
			}

			this.conditions.push( condition );
		},

		cloneCondition: function( conditionIndex ) {
			var newCondition = JSON.parse( JSON.stringify( this.conditions[conditionIndex] ) );

			newCondition.id = this.getRandomID();

			this.conditions.splice( conditionIndex + 1, 0, newCondition );
		},

		deleteCondition: function( conditionIndex ) {
			this.conditions.splice( conditionIndex, 1 );
		},

		setConditionProp: function( conditionIndex, key, value ) {
			var condition = this.conditions[ conditionIndex ];

			if ( 'value' === key && Array.isArray( value ) ) {
				value = value[0];
			}

			condition[ key ] = value;

			this.conditions.splice( conditionIndex, 1, condition );
		},

		getConditionFieldsList: function() {
			var optionsList = this.fieldsOptionList,
				currentFieldName = this.field.name;

			optionsList = optionsList.filter( function( item ) {
				return item.value !== currentFieldName;
			} );

			optionsList.unshift( {
				value: '',
				label: this.i18n.select_field,
			} );

			return optionsList;
		},

		getConditionValuesList: function( conditionIndex ) {
			var selectedField = this.conditions[ conditionIndex ].field,
				selectedFieldIndex,
				selectedFieldOptions,
				result = [],
				optionsSource = 'manual';

			if ( ! selectedField ) {
				return result;
			}

			selectedFieldIndex = this.fieldsNames.indexOf( selectedField );

			if ( -1 === selectedFieldIndex ) {
				return result;
			}

			if ( this.fieldsList[ selectedFieldIndex ].options_source ) {
				optionsSource = this.fieldsList[ selectedFieldIndex ].options_source;
			}

			switch ( optionsSource ) {
				case 'manual':

					if ( undefined === this.fieldsList[ selectedFieldIndex ].options ) {
						return result;
					}

					selectedFieldOptions = this.fieldsList[ selectedFieldIndex ].options;

					for ( var i = 0; i < selectedFieldOptions.length; i++ ) {
						result.push( {
							value: selectedFieldOptions[i].key,
							label: selectedFieldOptions[i].value,
						} );
					}

					break;

				case 'manual_bulk':

					if ( undefined === this.fieldsList[ selectedFieldIndex ].bulk_options ) {
						return result;
					}

					selectedFieldOptions = this.fieldsList[ selectedFieldIndex ].bulk_options;
					selectedFieldOptions = selectedFieldOptions.split( '\n' );

					for ( var i = 0; i < selectedFieldOptions.length; i++ ) {
						var optionValue = selectedFieldOptions[i],
							optionLabel = selectedFieldOptions[i];

						if ( -1 !== selectedFieldOptions[i].indexOf( '::' ) ) {
							var optionData = selectedFieldOptions[i].split( '::' );
							optionValue = optionData[0];
							optionLabel = optionData[1];
						}

						result.push( {
							value: optionValue,
							label: optionLabel,
						} );
					}

					break;
			}

			return result;
		},

		getConditionFieldType: function( conditionIndex ) {
			var selectedField = this.conditions[ conditionIndex ].field,
				selectedFieldIndex;

			if ( ! selectedField ) {
				return '';
			}

			selectedFieldIndex = this.fieldsNames.indexOf( selectedField );

			if ( -1 === selectedFieldIndex ) {
				return '';
			}

			return this.fieldsList[ selectedFieldIndex ].type;
		},

		getOperatorsList: function( conditionIndex ) {
			var fieldType = this.getConditionFieldType( conditionIndex ),
				result = [
					{
						value: '',
						label: this.i18n.select_operator,
					}
				];

			if ( ! fieldType ) {
				return result;
			}

			this.operatorsList.forEach( function( item ) {

				if ( item.fields && -1 === item.fields.indexOf( fieldType ) ) {
					return;
				}

				if ( item.not_fields && -1 !== item.not_fields.indexOf( fieldType ) ) {
					return;
				}

				result.push( {
					value: item.value,
					label: item.label,
				} );
			} );

			return result;
		},

		useRemoteCb: function( conditionIndex ) {
			return this.isGlossaryField( conditionIndex ) || this.isQueryField( conditionIndex );
		},

		getRemoteFields: function( conditionIndex, query, values ) {

			if ( this.isGlossaryField( conditionIndex ) ) {
				return this.getGlossaryFields( conditionIndex, query, values );
			}

			return this.getQueryFields( conditionIndex, query, values );
		},

		isGlossaryField: function( conditionIndex ) {
			var selectedField = this.conditions[ conditionIndex ].field,
				selectedFieldIndex;

			if ( ! selectedField ) {
				return false;
			}

			selectedFieldIndex = this.fieldsNames.indexOf( selectedField );

			if ( -1 === selectedFieldIndex ) {
				return false;
			}

			if ( this.fieldsList[ selectedFieldIndex ].options_source && 'glossary' == this.fieldsList[ selectedFieldIndex ].options_source ) {
				return true;
			} else if ( this.fieldsList[ selectedFieldIndex ].options_source && 'glossary' !== this.fieldsList[ selectedFieldIndex ].options_source ) {
				return false;
			} else {
				return true === this.fieldsList[ selectedFieldIndex ].options_from_glossary;
			}
		},

		getGlossaryFields: function( conditionIndex, query, values ) {
			var selectedField = this.conditions[ conditionIndex ].field,
				selectedFieldIndex,
				glossaryId;

			if ( ! selectedField ) {
				return;
			}

			selectedFieldIndex = this.fieldsNames.indexOf( selectedField );

			if ( -1 === selectedFieldIndex ) {
				return;
			}

			glossaryId = this.fieldsList[ selectedFieldIndex ].glossary_id;

			if ( ! glossaryId ) {
				return;
			}

			if ( values.length ) {
				values = values.join( ',' );
			}

			return wp.apiFetch( {
				method: 'get',
				path: JetEngineFieldsConfig.api_path_search_glossary_fields + '?' + window.JetEngineTools.buildQuery( {
					query: query,
					glossary_id: glossaryId,
					values: values,
				} )
			} );
		},

		isQueryField: function( conditionIndex ) {
			var selectedField = this.conditions[ conditionIndex ].field,
				selectedFieldIndex;

			if ( ! selectedField ) {
				return false;
			}

			selectedFieldIndex = this.fieldsNames.indexOf( selectedField );

			if ( -1 === selectedFieldIndex ) {
				return false;
			}

			if ( undefined === this.fieldsList[ selectedFieldIndex ].options_source ) {
				return false;
			}

			return ( 'query' === this.fieldsList[selectedFieldIndex].options_source );
		},

		getQueryFields: function( conditionIndex, query, values ) {
			var selectedField = this.conditions[ conditionIndex ].field,
				selectedFieldIndex,
				queryId,
				valueField,
				labelField;

			if ( ! selectedField ) {
				return;
			}

			selectedFieldIndex = this.fieldsNames.indexOf( selectedField );

			if ( -1 === selectedFieldIndex ) {
				return;
			}

			queryId = this.fieldsList[ selectedFieldIndex ].query_id;

			if ( ! queryId ) {
				return;
			}

			valueField = this.fieldsList[ selectedFieldIndex ].query_value_field ?? '';
			labelField = this.fieldsList[ selectedFieldIndex ].query_label_field ?? '';

			if ( values.length ) {
				values = values.join( ',' );
			}

			return wp.apiFetch( {
				method: 'get',
				path: JetEngineFieldsConfig.api_path_search_query_field_options + '?' + window.JetEngineTools.buildQuery( {
					q: query,
					query_id: queryId,
					value_field: valueField,
					label_field: labelField,
					values: values,
				} )
			} );
		},

		getConditionFieldTitle: function( conditionIndex ) {
			var selectedField = this.conditions[ conditionIndex ].field,
				selectedFieldIndex;

			if ( ! selectedField ) {
				return '';
			}

			selectedFieldIndex = this.fieldsNames.indexOf( selectedField );

			if ( -1 === selectedFieldIndex ) {
				return '';
			}

			return this.fieldsList[ selectedFieldIndex ].title;
		},

		getConditionFieldSubTitle: function( conditionIndex ) {
			var operator = this.conditions[ conditionIndex ].operator,
				result = '';

			if ( ! operator ) {
				return result;
			}

			var operatorLabel = '',
				fieldType = this.getConditionFieldType( conditionIndex );

			this.operatorsList.every( function( item ) {

				if ( item.value && item.value === operator ) {
					operatorLabel = item.label;
					return false;
				}

				return true;
			} );

			result += operatorLabel;

			if ( -1 !== [ 'empty', '!empty' ].indexOf( operator ) ) {
				return result;
			}

			result += '<span class="jet-engine-condition-field-value">';

			if ( -1 !== [ 'checkbox', 'radio', 'select' ].indexOf( fieldType ) && -1 !== [ 'in', 'not_in' ].indexOf( operator ) ) {
				result += this.conditions[ conditionIndex ].values ? this.conditions[ conditionIndex ].values.join( ', ' ) : '';
			} else {
				result += this.conditions[ conditionIndex ].value ? this.conditions[ conditionIndex ].value : '';
			}

			result += '</span>';

			return result;
		},

		getRandomID: function() {
			return Math.floor( Math.random() * 8999 ) + 1000;
		},

		isCollapsed: function( object ) {
			return undefined === object.collapsed || true === object.collapsed;
		},
	},
} );
