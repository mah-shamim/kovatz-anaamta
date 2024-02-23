Vue.component( 'jet-query-dynamic-args', {
	name: 'jet-query-dynamic-args',
	template: '#jet-query-dynamic-args',
	directives: { clickOutside: window.JetVueUIClickOutside },
	props: [ 'value' ],
	data: function() {
		return {
			isActive: false,
			macrosList: window.JetEngineQueryDynamicArgs.macros_list,
			contextList: window.JetEngineQueryDynamicArgs.context_list,
			currentMacros: {},
			editMacros: false,
			editSettings: false,
			result: {},
			advancedSettings: {},
		};
	},
	created: function() {

		if ( 'string' !== typeof this.value || ! this.value.includes( '%' ) ) {
			this.$set( this.advancedSettings, 'context', 'default_object' );
			return;
		}

		const regexp = /%([a-z_-]+)(\|[a-zA-Z0-9_\-\,\.\+\:\/\s\'\"\=\?\!\|\]\[\{\}%&]*)?%(\{.+\})?/;
		const parsedData = this.value.match( regexp ) || [];
		
		let macros = parsedData[1] || null;
		let data = null;

		if ( ! macros ) {
			console.warn( this.value + ' - incorrect macros value' );
			return;
		}

		if ( parsedData[2] ) {
			data = parsedData[2].substring( 1, parsedData[2].length );
			data = data.split( '|' );
		} else {
			data = [];
		}

		if ( parsedData[3] ) {
			this.advancedSettings = JSON.parse( parsedData[3] );
		} else {
			this.$set( this.advancedSettings, 'context', 'default_object' );
		}

		for ( var i = 0; i < this.macrosList.length; i++ ) {

			if ( macros === this.macrosList[ i ].id ) {

				this.result = {
					macros: macros,
					macrosName: this.macrosList[ i ].name,
					macrosControls: this.macrosList[ i ].controls,
				};

				if ( data.length && this.macrosList[ i ].controls ) {
					let index = 0;
					for ( const prop in this.macrosList[ i ].controls ) {

						if ( data[ index ] ) {
							this.$set( this.result, prop, data[ index ] );
						}

						index++;

					}
				}

				return;
			}
		}

	},
	watch: {
		advancedSettings: {
			handler: function( newSettings ) {
				this.$set( this.result, 'advancedSettings', newSettings );
			},
			deep: true,
		},
		result: {
			handler: function( newSettings ) {
				this.$emit( 'input', this.formatResult() );
			},
			deep: true,
		}
	},
	methods: {
		advancedSettingsPanel: function( state ) {
			this.editSettings = state;
		},
		applyMacros: function( macros, force ) {

			force = force || false;

			if ( macros ) {
				this.$set( this.result, 'macros', macros.id );
				this.$set( this.result, 'macrosName', macros.name );

				if ( macros.controls ) {
					this.$set( this.result, 'macrosControls', macros.controls );
				}
			}

			if ( macros && ! force && macros.controls ) {
				this.editMacros = true;
				this.currentMacros = macros;
				return;
			}

			this.$emit( 'input', this.formatResult() );
			this.isActive = false;

		},
		switchIsActive: function() {

			this.isActive = ! this.isActive;

			if ( this.isActive ) {
				if ( this.result.macros ) {
					for (var i = 0; i < this.macrosList.length; i++) {
						if ( this.result.macros === this.macrosList[ i ].id && this.macrosList[ i ].controls ) {
							this.currentMacros = this.macrosList[ i ];
							this.editMacros = true;
						}
					}
				}
			} else {
				this.resetEdit();
			}

		},
		clearResult: function() {
			this.result = {};
			this.$emit( 'input', '' );
		},
		formatResult: function() {

			if ( ! this.result.macros ) {
				return;
			}

			let res = '%';
			res += this.result.macros;

			if ( this.result.macrosControls ) {
				for ( const prop in this.result.macrosControls ) {
					res += '|';

					if ( undefined !== this.result[ prop ] ) {
						res += this.result[ prop ];
					}

				}
			}

			res += '%';

			if ( this.result.advancedSettings && ( this.result.advancedSettings.fallback || this.result.advancedSettings.context ) ) {
				res += JSON.stringify( this.result.advancedSettings );
			}

			return res;

		},
		onClickOutside: function() {
			this.isActive = false;
			this.editMacros = false;
			this.advancedSettingsPanel( false );
			this.currentMacros = {};
		},
		resetEdit: function() {
			this.editMacros = false;
			this.advancedSettingsPanel( false );
			this.currentMacros = {};
		},
		getPreparedControls: function() {

			controls = [];

			for ( const controlID in this.currentMacros.controls ) {

				let control     = this.currentMacros.controls[ controlID ];
				let optionsList = [];
				let type        = control.type;
				let label       = control.label;
				let defaultVal  = control.default;
				let groupsList  = [];
				let condition   = control.condition || {};

				switch ( control.type ) {

					case 'text':
						type = 'cx-vui-input';
						break;

					case 'textarea':
						type = 'cx-vui-textarea';
						break;

					case 'select':

						type = 'cx-vui-select';

						if ( control.groups ) {

							for ( var i = 0; i < control.groups.length; i++) {

								let group = control.groups[ i ];
								let groupOptions = [];

								for ( const optionValue in group.options ) {
									groupOptions.push( {
										value: optionValue,
										label: group.options[ optionValue ],
									} );
								}

								groupsList.push( {
									label: group.label,
									options: groupOptions,
								} );

							}
						} else {
							for ( const optionValue in control.options ) {
								optionsList.push( {
									value: optionValue,
									label: control.options[ optionValue ],
								} );
							}
						}

						break;
				}

				controls.push( {
					type: type,
					name: controlID,
					label: label,
					default: defaultVal,
					optionsList: optionsList,
					groupsList: groupsList,
					condition: condition,
				} );

			}

			return controls;
		},
		checkCondition: function( condition ) {

			let checkResult = true;

			condition = condition || {};

			for ( const [ fieldName, check ] of Object.entries( condition ) ) {
				if ( check && check.length ) {
					if ( ! check.includes( this.result[ fieldName ] ) ) {
						checkResult = false;
					}
				} else {
					if ( check != this.result[ fieldName ] ) {
						checkResult = false;
					}
				}
			}

			return checkResult;

		}
	},
} );
