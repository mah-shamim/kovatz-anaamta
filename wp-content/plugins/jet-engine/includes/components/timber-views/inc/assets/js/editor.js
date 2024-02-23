(function( $ ) {

	'use strict';

	window.controlsHelper = {
		methods: {
			getPreparedControls( instance ) {

				const controls = [];

				for ( const controlID in instance ) {

					let control     = instance[ controlID ];
					let optionsList = [];
					let type        = control.type;
					let label       = control.label;
					let defaultVal  = control.default;
					let groupsList  = [];
					let condition   = control.condition || {};
					let multiple    = false;

					switch ( control.type ) {

						case 'switcher':
							type = 'cx-vui-switcher';
							break;

						case 'text':
						case 'number':
							type = 'cx-vui-input';
							break;

						case 'textarea':
							type = 'cx-vui-textarea';
							break;

						case 'select':
						case 'select2':

							if ( 'select' === control.type ) {
								type = 'cx-vui-select';
							} else {
								type = 'cx-vui-f-select';
								multiple = true;
							}

							if ( control.groups ) {

								for ( var i = 0; i < control.groups.length; i++) {

									let group = control.groups[ i ];

									if ( group.values ) {
										groupsList.push( {
											label: group.label,
											options: group.values,
										} );
									} else {
										groupsList.push( {
											label: group.label,
											options: group.options,
										} );
									}
									

								}
							}

							/* else {
								for ( const optionValue in control.options ) {
									optionsList.push( {
										value: optionValue,
										label: control.options[ optionValue ],
									} );
								}
							}*/

							break;
					}

					controls.push( {
						type: type,
						name: controlID,
						label: label,
						description: control.description,
						default: defaultVal,
						optionsList: control.options,
						groupsList: groupsList,
						condition: condition,
						multiple: multiple,
					} );

				}

				return controls;
			},
			checkCondition: function( condition, settings ) {

				let checkResult = true;

				condition = condition || {};

				for ( let [ fieldName, check ] of Object.entries( condition ) ) {
					
					let isNegative = false;
					
					if ( fieldName.includes( '!' ) ) {
						fieldName = fieldName.replace( '!', '' );
						isNegative = true;
					}

					if ( check && check.length ) {
						if ( isNegative ) {
							if ( check.includes( settings[ fieldName ] ) ) {
								checkResult = false;
							}
						} else {
							if ( ! settings[ fieldName ] || ! check.includes( settings[ fieldName ] ) ) {
								checkResult = false;
							}
						}
						
					} else {
						if ( isNegative ) {
							if ( check == settings[ fieldName ] ) {
								checkResult = false;
							}
						} else {
							if ( check != settings[ fieldName ] ) {
								checkResult = false;
							}
						}
					}
				}

				return checkResult;

			}
		}
	}

	window.popupHelper = {
		data() {
			return {
				showPopup: false,
			};
		},
		methods: {
			closePopup() {
				if ( this.showPopup ) {
					this.switchPopup();
				}
			},
			switchPopup() {
				if ( this.showPopup ) {
					this.showPopup = false;
					this.onPopupClose();
				} else {
					this.onPopupShow();
					this.showPopup = true;
				}
			},
			onPopupClose() {
			},
			onPopupShow() {
			}
		}
	}

	Vue.component( 'jet-style', {
		render: function (createElement) {
			return createElement( 'style', this.replaceSelector( this.$slots.default[0].text ) );
		},
		props: [ 'listingId' ],
		methods: {
			replaceSelector( css ) {
				return css.replaceAll( 'selector', '.jet-listing-' + this.listingId );
			}
		}
	} );

	Vue.component( 'jet-engine-timber-chained-control', {
		template: `<div class="jet-engine-timber-dynamic-data__chain-level">
			<cx-vui-select
				label="Select data to show"
				:options-list="optionsList"
				placeholder="Select..."
				:value="result[ depth ].prop"
				:wrapper-css="[ 'mini-label' ]"
				size="fullwidth"
				@input="selectProperty"
			>
			<jet-engine-timber-chained-control
				v-if="currentChildren"
				:depth="nextDepth()"
				:children="currentChildren"
				:value="result"
				@input="updateResult"
			></jet-engine-timber-chained-control>
			<div
				v-if="currentArgs"
				class="jet-engine-timber-dynamic-data__single-item-control"
				v-for="control in getPreparedControls( currentArgs )"
			>
				<component
					:is="control.type"
					:options-list="control.optionsList"
					:groups-list="control.groupsList"
					:label="control.label"
					:wrapper-css="[ 'mini-label' ]"
					size="fullwidth"
					v-if="checkCondition( control.condition, result )"
					:value="result[ depth ].args[ control.name ]"
					@input="setArgs( $event, control.name )"
				><small v-if="control.description">{{ control.description }}</small></component>
			</div>
		</div>`,
		props: [ 'children', 'value', 'depth' ],
		mixins: [ window.controlsHelper ],
		data() {
			return {
				result: [],
				currentChildren: false,
				currentArgs: false,
				currentProp: false,
			};
		},
		computed: {
			optionsList() {
				
				const result = [];
				
				for ( const child in this.children ) {
					result.push( {
						value: child,
						label: this.children[ child ].label,
					} );
				}

				return result;

			}
		},
		created() {

			this.result = [ ...this.value ];

			if ( ! this.result[ this.depth ] ) {
				this.$set( this.result, this.depth, { prop: '', args: {} } );
			}

		},
		methods: {
			updateResult( value ) {
				this.result = value;
				this.$emit( 'input', this.result );
			},
			selectProperty( value ) {

				const selectedProp = this.children[ value ];

				if ( selectedProp && selectedProp.children ) {
					if ( 'string' === typeof selectedProp.children ) {
						this.currentChildren = { ...window.JetEngineTimberEditor.functions[ selectedProp.children ].children } || false;
					} else {
						this.currentChildren = { ...selectedProp.children };
					}
				}

				if ( selectedProp && selectedProp.args ) {
					this.currentArgs = { ...selectedProp.args };
				}

				this.$set( this.result[ this.depth ], 'prop', value );
				this.$emit( 'input', this.result );

			},
			setArgs( value, name ) {
				this.$set( this.result[ this.depth ].args, name, value );
			},
			nextDepth() {
				return parseInt( this.depth, 10 ) + 1;
			}
		}
	} );

	Vue.component( 'jet-engine-timber-settings', {
		template: '#jet_engine_timber_editor_settings_template',
		directives: { clickOutside: window.JetVueUIClickOutside },
		props: [ 'listingId', 'value' ],
		data() {
			return {
				showPopup: false,
				settings: {},
			};
		},
		mounted() {
			this.settings = { ...this.value };
		},
		watch: {
			settings: {
				handler( newSettings, oldSettings ) {
					
					this.switchControls( newSettings );

					// it's not initial setup
					if ( oldSettings.listing_source ) {
						this.$emit( 'input', newSettings );
					}

				},
				deep: true
			}
		},
		methods: {
			closePopup() {
				if ( this.showPopup ) {
					this.switchPopup();
				}
			},
			switchPopup() {

				this.showPopup = ! this.showPopup;

				if ( this.showPopup ) {
					this.$nextTick( () => {
						this.switchControls();
					} );
				}

			},
			switchControls( settings ) {

				if ( ! this.$refs.popup ) {
					return;
				}

				settings = settings || this.settings;

				let $allControls = this.$refs.popup.querySelectorAll( ".jet-template-listing" );

				$allControls.forEach( ( $item ) => {

					$item.style.display = 'none';

					if ( $item.classList.contains( "jet-template-" + settings.listing_source ) ) {
						$item.style.display = 'block';
					}

				} );

			},
		}
	} );

	Vue.component( 'jet-engine-timber-dynamic-data', {
		template: '#jet_engine_timber_editor_dynamic_data_template',
		directives: { clickOutside: window.JetVueUIClickOutside },
		props: [ 'mode' ],
		mixins: [ window.popupHelper, window.controlsHelper ],
		data() {
			return {
				showPopup: false,
				functions: window.JetEngineTimberEditor.functions,
				filters: window.JetEngineTimberEditor.filters,
				currentFunction: false,
				currentFilter: false,
				defaults: {},
				result: {},
				chainedResult: [],
				filterDefaults: {},
				filterResult: {},
				currentMode: false,
			};
		},
		methods: {
			onPopupClose() {
				this.currentMode = false;
				this.resetEdit();
			},
			onPopupShow() {
				this.currentMode = this.mode;
			},
			selectFunction( functionData ) {
				this.currentFunction = functionData;
				for ( const controlID in this.currentFunction.args ) {
					
					let control = this.currentFunction.args[ controlID ];

					if ( control.default ) {
						this.$set( this.result, controlID, control.default );
						this.$set( this.defaults, controlID, control.default )
					}
				}
			},
			selectFilter( filterData ) {
				this.currentFilter = filterData;
				for ( const controlID in this.currentFilter.args ) {
					
					let control = this.currentFilter.args[ controlID ];

					if ( undefined !== control.default ) {
						this.$set( this.filterResult, controlID, control.default );
						this.$set( this.filterDefaults, controlID, control.default )
					}
				}
			},
			resetEdit( switchToMode ) {
				
				if ( 'functions' === this.currentMode || ! this.currentMode ) {
					this.currentFunction = false;
					this.result = {};
					this.defaults = {};
				}

				if ( 'filters' === this.currentMode || ! this.currentMode ) {
					this.currentFilter = false;
					this.filterDefaults = {};
					this.filterResult = {};
				}

				if ( switchToMode ) {
					this.currentMode = switchToMode;
				}

			},
			goToFilter() {
				this.currentMode = 'filters';
			},
			insertFunction() {

				this.$emit( 'insert', this.getFunctionToInsert() );

				this.closePopup();

			},
			getFunctionToInsert() {
				
				if ( ! this.currentFunction ) {
					return '';
				}

				if ( this.currentFunction.chained ) {
					return '{{ ' + this.getChainedFunctionToInsert() + ' }}';
				}

				let result = '{{ ' 
				result += this.currentFunction.name + '(args=';
				result += this.getArgsString( this.result, this.defaults );
				result += ') }}';

				return result;

			},
			getArgsString( values, defaults ) {
				
				let args = [];

				for ( const arg in values ) {
					
					if ( values[ arg ] !== defaults[ arg ] ) {
						args.push( arg + ":'" + values[ arg ] + "'" );
					}

				}

				return '{' + args.join( ',' ) + '}';

			},
			getChainedFunctionToInsert() {

				let result = this.currentFunction.name;

				for ( var i = 0; i < this.chainedResult.length; i++ ) {
					result += '.' + this.chainedResult[ i ].prop;
					let args = Object.values( this.chainedResult[ i ].args );

					if ( args.length ) {
						
						let argsStr = args.map( ( item ) => {
							if ( 'string' === typeof item ) {
								item = "'" + item + "'";
							}
							return item;
						} ).join( ',' );

						result += '(' + argsStr + ')';

					}
				}

				return result;
			},
			insertFilter() {

				this.$emit( 'insert', this.getFilterToInsert() );

				this.closePopup();

			},
			getFilterToInsert() {

				if ( ! this.currentFilter ) {
					return '';
				}
				
				let args = {};

				for ( const arg in this.filterResult ) {
					
					if ( this.filterResult[ arg ] !== this.filterDefaults[ arg ] ) {
						args[ arg ] = this.filterResult[ arg ];
					}

				}

				// for non-variadic filters ensure defaults is set
				if ( ! this.currentFilter.variadic ) {
					args = { ...this.filterDefaults, ...args };
				}

				let result = '|' 
				result += this.currentFilter.name
				
				if ( this.currentFilter.args ) {
					result += '(';
					
					if ( this.currentFilter.variadic ) {
						result += 'args=' + this.getArgsString( this.filterResult, this.filterDefaults );
					} else {
						result += Object.values( args ).map( ( item ) => {
							if ( 'string' === typeof item ) {
								item = "'" + item + "'";
							}
							return item;
						} ).join( ',' );
					}

					result += ')';
				}

				let functionToInsert = this.getFunctionToInsert();

				if ( functionToInsert ) {
					result = functionToInsert.replace( ' }}', result + ' }}' );
				}

				return result;

			},
		}
	} );

	Vue.component( 'jet-engine-timber-presets', {
		template: `<div 
			class="jet-engine-timber-editor-presets jet-engine-timber-dynamic-data"
			v-click-outside.capture="closePopup"
			v-click-outside:mousedown.capture="closePopup"
			v-click-outside:touchstart.capture="closePopup"
			@keydown.esc="closePopup"
		>
			<cx-vui-button
				button-style="accent-border"
				@click="switchPopup"
				button-style="link-accent"
				size="link"
			>
				<svg 
					xmlns="http://www.w3.org/2000/svg" 
					width="16"
					height="16"
					viewBox="0 0 24 24"
					slot="label"
				>
					<path d="M1.926 7l-.556-3h21.256l-.556 3h-20.144zm1.514-5l-.439-1.999h17.994l-.439 1.999h-17.116zm-3.44 7l2.035 14.999h19.868l2.097-14.999h-24zm3.782 13l-1.221-9h18.86l-1.259 9h-16.38z"/>
				</svg>
				<span slot="label">Presets</span>
			</cx-vui-button>
			<div
				class="jet-engine-timber-dynamic-data__popup jet-engine-timber-editor-popup"
				v-if="showPopup"
				tabindex="-1"
			>
				<div class="jet-engine-timber-presets-list">
					<div
						:class="{
							'jet-engine-timber-presets-list__item': true,
							'jet-engine-timber-presets-list__item--active': presetIndex === currentPreset
						}" 
						v-for="( preset, presetIndex ) in presets"
					>
						<div 
							class="jet-engine-timber-presets-list__item-preview"
							@click.prevent="currentPreset = presetIndex" 
							v-html="preset.preview"
						></div>
						<div 
							class="jet-engine-timber-presets-list__item-cover"
							v-if="presetIndex === currentPreset"
						>
							<div class="jet-engine-timber-presets-list__item-cover-name">
								Use this preset?
							</div>
							<div class="jet-engine-timber-presets-list__item-cover-actions">
								<cx-vui-button
									button-style="accent"
									size="mini"
									@click.prevent="insertPreset"
								><span slot="label">Yes</span></cx-vui-button>
								<cx-vui-button
									button-style="default"
									size="mini"
									:disabled="false === currentPreset"
									@click.prevent="currentPreset = false"
								><span slot="label">Cancel</span></cx-vui-button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>`,
		directives: { clickOutside: window.JetVueUIClickOutside },
		mixins: [ window.popupHelper ],
		data() {
			return {
				showPopup: false,
				presets: window.JetEngineTimberEditor.presets,
				currentPreset: false,
			};
		},
		methods: {
			onPopupClose() {
				this.currentPreset = false;
			},
			onPopupShow() {
			},
			insertPreset() {
				this.$emit( 'insert', this.presets[ this.currentPreset ] );
				this.closePopup();
			},
		}
	} );

	new Vue( {
		el: '#jet_engine_timber_editor',
		template: '#jet_engine_timber_editor_template',
		data: {
			settings: window.JetEngineTimberEditor.settings,
			postTitle: window.JetEngineTimberEditor.post_title,
			postID: window.JetEngineTimberEditor.ID,
			html: window.JetEngineTimberEditor.listing_html,
			htmlSettings: window.JetEngineTimberEditor.html_settings,
			htmlEditor: null,
			css: window.JetEngineTimberEditor.listing_css,
			cssSettings: window.JetEngineTimberEditor.css_settings,
			cssEditor: null,
			previewHTML: '',
			previewSettings: {
				width: 50,
				units: '%',
			},
			saving: false,
			reloading: false,
		},
		created() {
			if ( window.JetEngineTimberEditor.preview_settings ) {
				this.previewSettings = { ...window.JetEngineTimberEditor.preview_settings };
			}
		},
		mounted() {

			this.cssSettings.codemirror.gutters = this.htmlSettings.codemirror.gutters;

			this.cssEditor = wp.codeEditor.initialize( this.$refs.css, this.cssSettings );
			this.htmlEditor = wp.codeEditor.initialize( this.$refs.html, this.htmlSettings );

			this.htmlEditor.codemirror.on( 'change', ( editor ) => {
				this.html = editor.getValue();
			} );

			this.cssEditor.codemirror.on( 'change', ( editor ) => {
				this.css = editor.getValue();
			} );

			this.$refs.previewBody.addEventListener( 'click', ( event ) => {
				if ( ( 'A' === event.target.nodeName && ! event.target.classList.contains( 'clickable' ) ) 
					|| event.target.closest( 'a:not(.clickable)' ) 
				) {
					event.preventDefault();
					return false;
				}
			} );

			this.reloadPreview();

		},
		methods: {
			getPreviewWidth() {
				return '' + this.previewSettings.width + this.previewSettings.units;
			},
			insertDynamicData( newData ) {

				let doc = this.htmlEditor.codemirror.getDoc();
				let cursor = doc.getCursor();

				if ( 0 === cursor.ch && 0 === cursor.line ) {
					newData += "\n";
				}

				doc.replaceRange( newData, cursor );

			},
			applyPreset( data ) {

				let htmlDoc = this.htmlEditor.codemirror.getDoc();
				let cssDoc  = this.cssEditor.codemirror.getDoc();

				htmlDoc.setValue( data.html );
				cssDoc.setValue( data.css );

				this.reloadPreview();

			},
			save() {

				this.saving = true;

				jQuery.ajax({
					url: window.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_engine_timber_save',
						settings: this.settings,
						title: this.postTitle,
						html: this.html,
						css: this.css,
						preview_settings: this.previewSettings,
						id: this.postID,
						nonce: window.JetEngineTimberEditor.nonce,
					}
				}).done( ( response ) => {
					this.saving = false;
				} ).fail( ( jqXHR, textStatus, errorThrown ) => {
					this.saving = false;
				} );
			},
			reloadingStyles() {

				const styles = {
					position: 'relative',
					overflow: 'hidden',
				};

				if ( this.reloading ) {
					styles.opacity = '0.3';
				}

				return styles;
			},
			reloadPreview() {

				this.reloading = true;

				jQuery.ajax({
					url: window.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_engine_timber_reload_preview',
						settings: this.settings,
						html: this.html,
						id: this.postID,
						nonce: window.JetEngineTimberEditor.nonce,
					}
				}).done( ( response ) => {

					this.previewHTML = response.data.preview;
					this.reloading = false;

					/*
					
					Disable scripts init in preview for now
					
					setTimeout( () => { 
						window.JetPlugins.init( jQuery( this.$refs.previewBody ), [ {
							block: 'jet-engine/dynamic-field',
							callback: window.JetEngine.widgetDynamicField
						} ] );
					} );
					*/

				} ).fail( ( jqXHR, textStatus, errorThrown ) => {
					this.reloading = false;
					alert( errorThrown );
				} );
			}
		}
	} );

})( jQuery );
