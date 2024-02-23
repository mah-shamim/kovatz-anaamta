/**
 * Interface Builder
 */
;( function( $, underscore ) {

	'use strict';

	var cxInterfaceBuilder = {

		init: function() {
			// Component Init
			this.component.init();
			$( document ).on( 'cxFramework:interfaceBuilder:component', this.component.init.bind( this.component ) );

			// Control Init
			this.control.init();
			$( document ).on( 'cxFramework:interfaceBuilder:control', this.control.init.bind( this.control ) );

			// Control Validation
			this.controlValidation.init();
		},

		component: {
			tabClass:           '.cx-tab',
			accordionClass:     '.cx-accordion',
			toggleClass:        '.cx-toggle',

			buttonClass:        '.cx-component__button',
			contentClass:       '.cx-settings__content',

			buttonActiveClass:  'active',
			showClass:          'show',

			localStorage:        {},

			controlConditions:   window.cxInterfaceBuilder.conditions || {},

			controlValues:       window.cxInterfaceBuilder.fields || {},

			conditionState:      {},

			init: function () {
				this.localStorage = this.getState() || {};

				this.componentInit( this.tabClass );
				this.componentInit( this.accordionClass );
				this.componentInit( this.toggleClass );

				this.addEvent();
				this.conditionsHandleInit();
			},

			addEvent: function() {
				$( 'body' )
					.off( 'click.cxInterfaceBuilder' )
					.on( 'click.cxInterfaceBuilder',
						this.tabClass + ' ' + this.buttonClass + ', ' +
						this.toggleClass + ' ' + this.buttonClass + ', ' +
						this.accordionClass + ' ' + this.buttonClass,

						this.componentClick.bind( this )
					);
			},

			conditionsHandleInit: function() {
				var self = this;

				$( window ).on( 'cx-switcher-change', function( event ) {
					var controlName   = event.controlName,
						controlStatus = event.controlStatus;

					self.updateConditionRules( controlName, controlStatus );
					self.renderConditionRules();
				});

				$( window ).on( 'cx-select-change', function( event ) {
					var controlName   = event.controlName,
						controlStatus = event.controlStatus;

					self.updateConditionRules( controlName, controlStatus );
					self.renderConditionRules();
				});

				$( window ).on( 'cx-select2-change', function( event ) {
					var controlName   = event.controlName,
						controlStatus = event.controlStatus;

					self.updateConditionRules( controlName, controlStatus );
					self.renderConditionRules();
				});

				$( window ).on( 'cx-radio-change', function( event ) {
					var controlName   = event.controlName,
						controlStatus = event.controlStatus;

					self.updateConditionRules( controlName, controlStatus );
					self.renderConditionRules();
				});

				$( window ).on( 'cx-checkbox-change', function( event ) {
					var controlName   = event.controlName,
						controlStatus = event.controlStatus,
						updatedStatus = {};

					$.each( controlStatus[ controlName ], function( checkbox, value ) {
						updatedStatus[ checkbox ] = cxInterfaceBuilder.utils.filterBoolValue( value );
					} );

					self.updateConditionRules( controlName, updatedStatus );
					self.renderConditionRules();
				});

				$( window ).on( 'cx-control-change', function( event ) {
					var controlName   = event.controlName,
						controlStatus = event.controlStatus;

					self.updateConditionRules( controlName, controlStatus );
					self.renderConditionRules();
				});

				this.generateConditionRules();
				self.renderConditionRules();

			},

			getControlNameParts: function( controlName ) {
				return controlName.match(/([a-zA-Z0-9_-]+)?(!?)$/i);
			},

			generateConditionRules: function() {
				var self = this;

				$.each( this.controlConditions, function( control, conditions ) {
					$.each( conditions, function( control, value ) {

						var controlNameParts = self.getControlNameParts( control );

						control = controlNameParts[1];

						if ( self.controlValues.hasOwnProperty( control ) ) {
							self.conditionState[ control ] = self.controlValues[ control ];
						}
					} );
				} );
			},

			updateConditionRules: function( name, status ) {
				this.conditionState[ name ] = status;
			},

			renderConditionRules: function() {
				var self = this;

				$.each( this.controlConditions, function( control, conditions ) {
					var $selector = $( '.cx-control[data-control-name="' + control + '"]' ),
						hidden    = true;

					$selector.addClass( 'cx-control-hidden' );

					$.each( conditions, function( control, value ) {
						hidden = true;

						var controlNameParts = self.getControlNameParts( control ),
							isNegativeCondition;

						control = controlNameParts[1];
						isNegativeCondition = !!controlNameParts[2];

						if ( self.conditionState.hasOwnProperty( control ) ) {
							var type = typeof value;

							switch ( type ) {
								case 'string':
									if ( self.conditionState[control].toString() === value ) {
										hidden = false;
									}
									break;
								case 'boolean':
									if ( self.conditionState[control].toString() === value.toString() ) {
										hidden = false;
									}
									break;
								default :
									if ( 'string' === typeof self.conditionState[ control ] ){
										if ( 'true' === self.conditionState[ control ] ){
											self.conditionState[ control ] = true;
										}
										if ( 'false' === self.conditionState[ control ] ){
											self.conditionState[ control ] = false;
										}
									}
									if ( -1 !== value.indexOf( self.conditionState[control] ) ) {
										hidden = false;
									}
									break;
							}

							if ( $.isArray( self.conditionState[ control ] ) ) { // for Select-2 control

								$.each( self.conditionState[ control ], function( index, val ) {

									if ( val && -1 !== value.indexOf( val ) ) {

										hidden = false;

										return false;
									}
								} );

							} else if ( 'object' === typeof self.conditionState[ control ] ) { // for Checkboxes control

								$.each( self.conditionState[ control ], function( prop, val ) {

									if ( cxInterfaceBuilder.utils.filterBoolValue( val ) && -1 !== value.indexOf( prop ) ) {

										hidden = false;

										return false;
									}
								} );
							}
						}

						if ( isNegativeCondition ) {
							hidden = ! hidden;
						}

						if ( hidden ) {
							return false;
						}

					} );

					if ( hidden ) {
						$selector.addClass( 'cx-control-hidden' );
						$selector.find( '[required]' )
								.removeAttr( 'required' )
								.attr( 'data-required', 1 );
					} else {
						$selector.removeClass( 'cx-control-hidden' );
						$selector.find( '[data-required="1"]' )
								.removeAttr( 'data-required' )
								.attr( 'required', true );
					}
				} );
			},

			componentInit: function( componentClass ) {
				var _this = this,
					components = $( componentClass ),
					componentId = null,
					button = null,
					contentId = null,
					notShow = '';

				components.each( function( index, component ) {
					component   = $( component );
					componentId = component.data( 'compotent-id' );

					switch ( componentClass ) {
						case _this.toggleClass:
							if ( _this.localStorage[ componentId ] && _this.localStorage[ componentId ].length ) {
								notShow = _this.localStorage[ componentId ].join( ', ' );
							}

							$( _this.contentClass, component )
								.not( notShow )
								.addClass( _this.showClass )
								.prevAll( _this.buttonClass )
								.addClass( _this.buttonActiveClass );
						break;

						case _this.tabClass:
						case _this.accordionClass:
							if ( _this.localStorage[ componentId ] ) {
								contentId = _this.localStorage[ componentId ][ 0 ];
								button = $( '[data-content-id="' + contentId + '"]', component );
							} else {
								button = $( _this.buttonClass, component ).eq( 0 );
								contentId = button.data( 'content-id' );
							}

							_this.showElement( button, component, contentId );
						break;
					}
				} );
			},

			componentClick: function( event ) {
				var $target      = $( event.target ),
					$parent      = $target.closest( this.tabClass + ', ' + this.accordionClass + ', ' + this.toggleClass ),
					expr          = new RegExp( this.tabClass + '|' + this.accordionClass + '|' + this.toggleClass ),
					componentName = $parent[0].className.match( expr )[ 0 ].replace( ' ', '.' ),
					contentId     = $target.data( 'content-id' ),
					componentId   = $parent.data( 'compotent-id' ),
					activeFlag    = $target.hasClass( this.buttonActiveClass ),
					itemClosed;

				switch ( componentName ) {
					case this.tabClass:
						if ( ! activeFlag ) {
							this.hideElement( $parent );
							this.showElement( $target, $parent, contentId );

							this.localStorage[ componentId ] = new Array( contentId );
							this.setState();
						}
					break;

					case this.accordionClass:
						this.hideElement( $parent );

						if ( ! activeFlag ) {
							this.showElement( $target, $parent, contentId );

							this.localStorage[ componentId ] = new Array( contentId );
						} else {
							this.localStorage[ componentId ] = {};
						}
						this.setState();
					break;

					case this.toggleClass:
						$target
							.toggleClass( this.buttonActiveClass )
							.nextAll( contentId )
							.toggleClass( this.showClass );

						if ( Array.isArray( this.localStorage[ componentId ] ) ) {
							itemClosed = this.localStorage[ componentId ].indexOf( contentId );

							if ( -1 !== itemClosed ) {
								this.localStorage[ componentId ].splice( itemClosed, 1 );
							} else {
								this.localStorage[ componentId ].push( contentId );
							}

						} else {
							this.localStorage[ componentId ] = new Array( contentId );
						}

						this.setState();
					break;
				}
				$target.blur();

				return false;
			},

			showElement: function ( button, holder, contentId ) {
				button
					.addClass( this.buttonActiveClass );

				holder
					.data( 'content-id', contentId );

				$( contentId, holder )
					.addClass( this.showClass );
			},

			hideElement: function ( holder ) {
				var contsntId = holder.data( 'content-id' );

				$( '[data-content-id="' + contsntId + '"]', holder )
					.removeClass( this.buttonActiveClass );

				$( contsntId, holder )
					.removeClass( this.showClass );
			},

			getState: function() {
				try {
					return JSON.parse( localStorage.getItem( 'interface-builder' ) );
				} catch ( e ) {
					return false;
				}
			},

			setState: function() {
				try {
					localStorage.setItem( 'interface-builder', JSON.stringify( this.localStorage ) );
				} catch ( e ) {
					return false;
				}
			}
		},

		control: {
			init: function () {
				this.switcher.init();
				this.checkbox.init();
				this.radio.init();
				this.slider.init();
				this.select.init();
				this.media.init();
				this.colorpicker.init();
				this.iconpicker.init();
				this.dimensions.init();
				this.wysiwyg.init();
				this.repeater.init();
				this.text.init();
			},

			// CX-Switcher
			switcher: {
				switcherClass: '.cx-switcher-wrap',
				trueClass: '.cx-input-switcher-true',
				falseClass: '.cx-input-switcher-false',

				init: function() {
					$( 'body' ).on( 'click.cxSwitcher', this.switcherClass, this.switchState.bind( this ) );
				},

				switchState: function( event ) {
					var $this       = $( event.currentTarget ),
						$inputTrue  = $( this.trueClass, $this ),
						$inputFalse = $( this.falseClass, $this ),
						status      = $inputTrue[0].checked,
						$parent     = $( event.currentTarget ).closest( '.cx-control-switcher' ),
						name        = $parent.data( 'control-name' );

					$inputTrue.prop( 'checked', ( status ) ? false : true );
					$inputFalse.prop( 'checked', ( ! status ) ? false : true );

					status = $inputTrue[0].checked;

					$( window ).trigger( {
						type: 'cx-switcher-change',
						controlName: name,
						controlStatus: status
					} );
				}

			},//End CX-Switcher

			// CX-Checkbox
			checkbox: {
				inputClass: '.cx-checkbox-input[type="hidden"]:not([name*="__i__"])',
				itemClass: '.cx-checkbox-label, .cx-checkbox-item',
				itemWrapClass: '.cx-checkbox-item-wrap',
				addButtonClass: '.cx-checkbox-add-button',
				customValueInputClass: '.cx-checkbox-custom-value',

				init: function() {
					$( 'body' )
						.on( 'click.cxCheckbox', this.itemClass, this.switchState.bind( this ) )
						.on( 'click.cxCheckbox', this.addButtonClass, this.addCustomCheckbox.bind( this ) )
						.on( 'input.cxCheckbox', this.customValueInputClass, this.updateCustomValue.bind( this ) );

					this.resetOnEditTagsPage();
				},

				switchState: function( event ) {
					var $_input           = $( event.currentTarget ).siblings( this.inputClass ),
						$customValueInput = $( event.target ).closest( this.customValueInputClass ),
						//status            = $_input[0].checked,
						status            = cxInterfaceBuilder.utils.filterBoolValue( $_input.val() ),
						$parent           = $( event.currentTarget ).closest( '.cx-control-checkbox' ),
						name              = $parent.data( 'control-name' ),
						statusData        = {};

					if ( $customValueInput[0] ) {
						return;
					}

					$_input.val( ! status ? 'true' : 'false' ).attr( 'checked', ! status ? true : false );

					if ( !$parent[0] ) {
						return;
					}

					statusData = cxInterfaceBuilder.utils.serializeObject( $parent );

					$( window ).trigger( {
						type: 'cx-checkbox-change',
						controlName: name,
						controlStatus: statusData
					} );
				},
				addCustomCheckbox: function( event ) {
					var $addButton = $( event.currentTarget ),
						html;

					event.preventDefault();

					html = '<div class="cx-checkbox-item-wrap">';
						html += '<span class="cx-label-content">';
							html += '<input type="hidden" class="cx-checkbox-input" checked value="true">';
							html += '<span class="cx-checkbox-item"><span class="marker dashicons dashicons-yes"></span></span>';
							html += '<label class="cx-checkbox-label"><input type="text" class="cx-checkbox-custom-value cx-ui-text"></label>';
						html += '</span>';
					html += '</div>';

					$addButton.before( html );
				},
				updateCustomValue: function( event ) {
					var $this   = $( event.currentTarget ),
						value   = $this.val(),
						$label  = $this.closest( '.cx-checkbox-label' ),
						$_input = $label.siblings( this.inputClass ),
						$parent = $this.closest( '.cx-control-checkbox' ),
						name    = $parent.data( 'control-name' );

					$_input.attr( 'name', value ? name + '[' + value + ']' : '' );
				},
				resetOnEditTagsPage: function() {
					var self = this;

					if ( -1 === window.location.href.indexOf( 'edit-tags.php' ) ) {
						return;
					}

					var $input = $( self.inputClass ),
						defaultCheckInputs = [];

					if ( !$input[0] ) {
						return;
					}

					$input.each( function() {
						if ( 'true' !== $( this ).val() ) {
							return;
						}

						defaultCheckInputs.push( $( this ).attr( 'name' ) );
					} );

					$( document ).ajaxComplete( function( event, xhr, settings ) {

						if ( ! settings.data || -1 === settings.data.indexOf( 'action=add-tag' ) ) {
							return;
						}

						if ( -1 !== xhr.responseText.indexOf( 'wp_error' ) ) {
							return;
						}

						var $customFields = $( self.customValueInputClass );

						if ( $customFields[0] ) {
							$customFields.closest( self.itemWrapClass ).remove();
						}

						$input.each( function() {
							if ( -1 !== defaultCheckInputs.indexOf( $( this ).attr( 'name' ) ) ) {
								$( this ).val( 'true' ).attr( 'checked', true );
							} else {
								$( this ).val( 'false' ).attr( 'checked', false );
							}
						} );
					} );
				}
			},//End CX-Checkbox

			// CX-Radio
			radio: {
				inputClass: '.cx-radio-input:not([name*="__i__"])',
				customValueInputClass: '.cx-radio-custom-value',

				init: function() {
					$( 'body' )
						.on( 'click.cxRadio', this.inputClass, this.switchState.bind( this ) )
						.on( 'input.cxRadio', this.customValueInputClass, this.updateCustomValue.bind( this ) );

					this.resetOnEditTagsPage();
				},

				switchState: function( event ) {
					var $this             = $( event.currentTarget ),
						$parent           = $( event.currentTarget ).closest( '.cx-control-radio' ),
						$customValueInput = $( event.currentTarget ).siblings( this.customValueInputClass ),
						name              = $parent.data( 'control-name' );

					if ( $customValueInput[0] ) {
						$customValueInput.focus();
					}

					$( window ).trigger( {
						type: 'cx-radio-change',
						controlName: name,
						controlStatus: $( $this ).val()
					} );
				},

				updateCustomValue: function( event ) {
					var $this   = $( event.currentTarget ),
						value   = $this.val(),
						$_input = $this.siblings( this.inputClass );

					$_input.attr( 'value', value );
				},
				resetOnEditTagsPage: function() {
					var self = this;

					if ( -1 === window.location.href.indexOf( 'edit-tags.php' ) ) {
						return;
					}

					var $input = $( self.inputClass ),
						defaultCheckInputs = [];

					if ( !$input[0] ) {
						return;
					}

					$input.each( function() {
						if ( ! $( this ).prop( 'checked' ) ) {
							return;
						}

						defaultCheckInputs.push( $( this ).attr( 'name' ) + '[' + $( this ).val() + ']' );
					} );

					$( document ).ajaxComplete( function( event, xhr, settings ) {

						if ( ! settings.data || -1 === settings.data.indexOf( 'action=add-tag' ) ) {
							return;
						}

						if ( -1 !== xhr.responseText.indexOf( 'wp_error' ) ) {
							return;
						}

						var $customFields = $( self.customValueInputClass );

						if ( $customFields[0] ) {
							$customFields.siblings( self.inputClass ).val( '' );
						}

						$input.each( function() {
							if ( -1 !== defaultCheckInputs.indexOf( $( this ).attr( 'name' ) + '[' + $( this ).val() + ']' ) ) {
								$( this ).prop( 'checked', true );
							} else {
								$( this ).prop( 'checked', false );
							}
						} );
					} );
				}
			},//End CX-Radio

			// CX-Slider
			slider: {
				init: function() {
					$( 'body' ).on( 'input.cxSlider change.cxSlider', '.cx-slider-unit, .cx-ui-stepper-input', this.changeHandler.bind( this ) );
				},

				changeHandler: function( event ) {
					var $this            = $( event.currentTarget ),
						$thisVal         = $this.val(),
						$sliderWrapper   = $this.closest( '.cx-slider-wrap' ),
						$sliderContainer = $this.closest( '.cx-ui-container' ),
						$sliderSettings  = $sliderContainer.data( 'settings' ) || {},
						$stepperInput    = $( '.cx-ui-stepper-input', $sliderContainer ),
						controlName      = $stepperInput.attr( 'name' ),
						rangeLabel       = $sliderSettings['range_label'] || false,
						targetClass      = ( ! $this.hasClass( 'cx-slider-unit' ) ) ? '.cx-slider-unit' : '.cx-ui-stepper-input';

					$( targetClass, $sliderWrapper ).val( $thisVal );

					if ( controlName ) {
						$( window ).trigger( {
							type: 'cx-control-change',
							controlName: controlName,
							controlStatus: $thisVal
						} );
					}

					if ( rangeLabel ) {
						var $rangeLabel = $( '.cx-slider-range-label', $sliderWrapper ),
							rangeLabels = $sliderSettings['range_labels'];

						if ( 0 === +$thisVal ) {
							$rangeLabel.html( rangeLabels[+$thisVal]['label'] );
							$rangeLabel.css( 'color', rangeLabels[+$thisVal]['color'] );

							return false;
						}

						Object.keys(rangeLabels).reduce( function( prev, current, index, array ) {

							if ( ( +$thisVal > +prev && +$thisVal <= +current ) ) {
								$rangeLabel.html( rangeLabels[+current]['label'] );
								$rangeLabel.css( 'color', rangeLabels[+current]['color'] );
							}

							return current;
						} );
					}
				}
			},//End CX-Slider

			// CX-Select
			select: {
				selectWrapClass: '.cx-ui-select-wrapper',
				selectClass: '.cx-ui-select[data-filter="false"]:not([name*="__i__"])',
				select2Class: '.cx-ui-select[data-filter="true"]:not([name*="__i__"]), .cx-ui-select[multiple]:not([name*="__i__"])',
				selectClearClass: '.cx-ui-select-clear',

				init: function() {

					$( this.selectRender.bind( this ) );

					$( document )
						//.on( 'ready.cxSelect', this.selectRender.bind( this ) )
						.on( 'cx-control-init', this.selectRender.bind( this ) )
						.on( 'click.cxSelect', this.selectClearClass, this.clearSelect );

				},

				clearSelect: function( event ) {
					event.preventDefault();
					var $select = $( this ).siblings( 'select' );
					$select.find( ':selected' ).removeAttr( 'selected' );
					$select.val( null ).trigger( 'change' );
				},

				selectRender: function( event ) {
					var $target = ( event._target ) ? event._target : $( 'body' );

					$( this.selectClass, $target ).each( this.selectInit.bind( this ) );
					$( this.select2Class, $target ).each( this.select2Init.bind( this ) );
				},

				selectInit: function ( index, element ) {
					var $this = $( element ),
						name  = $this.attr( 'id' );

					$this.change( function( event ) {
						$( window ).trigger( {
							type: 'cx-select-change',
							controlName: name,
							controlStatus: $( event.target ).val()
						} );
					});
				},

				select2Init: function ( index, element ) {
					var $this    = $( element ),
						$wrapper = $this.closest( this.selectWrapClass ),
						name     = $this.attr( 'id' ),
						settings = { placeholder: $this.data( 'placeholder' ), dropdownCssClass: 'cx-ui-select2-dropdown' },
						postType = $this.data( 'post-type' ),
						exclude  = $this.data( 'exclude' ),
						action   = $this.data( 'action' );

					if ( action && postType ) {

						settings.ajax = {
							url: function() {
								return ajaxurl + '?action=' + action + '&post_type=' + $this.data( 'post-type' ) + '&exclude=' + exclude;
							},
							dataType: 'json'
						};

						settings.minimumInputLength = 3;

					}

					$this.select2( settings ).on( 'change.cxSelect2', function( event ) {
						$( window ).trigger( {
							type: 'cx-select2-change',
							controlName: name,
							controlStatus: $( event.target ).val()
						} );
					} );
				}
			},//End CX-Select

			// CX-Media
			media: {
				inputClass: 'input.cx-upload-input:not([name*="__i__"])',

				init: function() {

					$( this.mediaRender.bind( this ) );

					$( document )
						//.on( 'ready.cxMedia', this.mediaRender.bind( this ) )
						.on( 'cx-control-init', this.mediaRender.bind( this ) );

					$( 'body' )
						.on( 'change.cxMedia', this.inputClass, cxInterfaceBuilder.control.text.changeHandler.bind( this ) );
				},

				mediaRender: function( event ) {
					var target   = ( event._target ) ? event._target : $( 'body' ),
						$buttons = $( '.cx-upload-button', target ),
						prepareInputValue = function( input_value, settings ) {

							if ( !input_value.length ) {
								return '';
							}

							if ( 'both' === settings.value_format ) {
								if ( !settings.multiple ) {
									input_value = input_value[0];
								}

								input_value = JSON.stringify( input_value );
							} else {
								input_value = input_value.join( ',' );
							}

							return input_value;
						};

					var $postId = $( '#post_ID' );

					// Added for attach a media file to post.
					if ( $postId.length && wp.media.view && wp.media.view.settings && wp.media.view.settings.post && ! wp.media.view.settings.post.id ) {
						wp.media.view.settings.post.id = $postId.val();
					}

					$buttons.each( function() {
						var button = $( this ),
							buttonParent = button.closest('.cx-ui-media-wrap'),
							settings = {
								input: $( '.cx-upload-input', buttonParent ),
								img_holder: $( '.cx-upload-preview', buttonParent ),
								title_text: button.data('title'),
								multiple: button.data('multi-upload'),
								library_type: button.data('library-type'),
								value_format: button.data('value-format') || 'id',
							},
							cx_uploader = wp.media.frames.file_frame = wp.media({
								title: settings.title_text,
								button: { text: settings.title_text },
								multiple: settings.multiple,
								library : { type : settings.library_type }
							});

						if ( ! buttonParent.has('input[name*="__i__"]')[ 0 ] ) {

							button.off( 'click.cx-media' ).on( 'click.cx-media', function() {
								cx_uploader.open();
								return !1;
							} ); // end click

							if ( button.data( 'multi-upload' ) ) {
								cx_uploader.on( 'open', function() {

									var selection = cx_uploader.state().get( 'selection' );
									var selected  = settings.input.attr( 'data-ids-attr' );

									if ( selected ) {
										selected = selected.split(',');
										selected.forEach( function( imgID ) {
											selection.add( wp.media.attachment( imgID ) );
										} );
									}
								});
							}

							cx_uploader.on('select', function() {
								var attachment       = cx_uploader.state().get( 'selection' ).toJSON(),
									count            = 0,
									input_value      = [],
									input_ids        = [],
									new_img_object   = $( '.cx-all-images-wrap', settings.img_holder ),
									new_img          = '',
									fetchAttachments = [];

								attachment.forEach( function( attachmentData, index ) {

									if ( !attachmentData.url && attachmentData.id ) {
										fetchAttachments.push(
											wp.media.attachment( attachmentData.id ).fetch().then( function( data ) {
												attachment[index] = data;
											} )
										);
									}
								} );

								Promise.all( fetchAttachments ).then( function() {
									while ( attachment[count] ) {
										var attachment_data = attachment[count],
											attachment_id   = attachment_data.id,
											attachment_url  = attachment_data.url,
											mimeType        = attachment_data.mime,
											return_data     = '',
											img_src         = '',
											thumb           = '',
											thumb_type      = 'icon';

										if ( 'both' === settings.value_format ) {
											return_data = {
												id:  attachment_id,
												url: attachment_url,
											}
										} else {
											return_data = attachment_data[settings.value_format];
										}

										switch ( mimeType ) {
											case 'image/jpeg':
											case 'image/png':
											case 'image/gif':
											case 'image/svg+xml':
											case 'image/webp':
												if ( attachment_data.sizes !== undefined ) {
													img_src = attachment_data.sizes.thumbnail ? attachment_data.sizes.thumbnail.url : attachment_data.sizes.full.url;
												} else {
													img_src = attachment_url;
												}

												thumb = '<img  src="' + img_src + '" alt="" data-img-attr="' + attachment_id + '">';
												thumb_type = 'image';
												break;
											case 'application/pdf':
												thumb = '<span class="dashicons dashicons-media-document"></span>';
												break;
											case 'image/x-icon':
												thumb = '<span class="dashicons dashicons-format-image"></span>';
												break;
											case 'video/mpeg':
											case 'video/mp4':
											case 'video/quicktime':
											case 'video/webm':
											case 'video/ogg':
												thumb = '<span class="dashicons dashicons-format-video"></span>';
												break;
											case 'audio/mpeg':
											case 'audio/wav':
											case 'audio/ogg':
												thumb = '<span class="dashicons dashicons-format-audio"></span>';
												break;
										}

										new_img += '<div class="cx-image-wrap cx-image-wrap--' + thumb_type + '">' +
														'<div class="inner">' +
															'<div class="preview-holder" data-id-attr="' + attachment_id + '" data-url-attr="' + attachment_url + '"><div class="centered">' + thumb + '</div></div>' +
															'<a class="cx-remove-image" href="#"><i class="dashicons dashicons-no"></i></a>' +
															'<span class="title">' + attachment_data.title + '</span>' +
														'</div>' +
													'</div>';

										input_value.push( return_data );
										input_ids.push( attachment_id );
										count++;
									}

									settings.input.val( prepareInputValue( input_value, settings ) ).attr( 'data-ids-attr', input_ids.join( ',' ) ).trigger( 'change' );
									new_img_object.html( new_img );
								} );
							} );

							var removeMediaPreview = function( item ) {
								var buttonParent = item.closest( '.cx-ui-media-wrap' ),
									input         = $( '.cx-upload-input', buttonParent ),
									img_holder    = item.parent().parent( '.cx-image-wrap' ),
									img_attr      = $( '.preview-holder', img_holder ).data( 'id-attr' ),
									input_value   = input.attr( 'value' ),
									input_ids     = [];

									if ( ! input_value ) {
										return;
									}

									img_holder.remove();
									input_value = [];

									buttonParent.find( '.cx-image-wrap' ).each( function() {
										var attachment_id  = $( '.preview-holder', this ).data( 'id-attr' ),
											attachment_url = $( '.preview-holder', this ).data( 'url-attr' );

										input_ids.push( attachment_id );

										switch ( settings.value_format ) {
											case 'id':
												input_value.push( attachment_id );
												break;

											case 'url':
												input_value.push( attachment_url );
												break;

											case 'both':
												input_value.push( {
													id:  attachment_id,
													url: attachment_url,
												} );
												break;
										}
									} );

									input.attr( {
										'value': prepareInputValue( input_value, settings ),
										'data-ids-attr': input_ids.join( ',' ),
									} ).trigger( 'change' );
							};

							// This function remove upload image
							buttonParent.on( 'click', '.cx-remove-image', function () {
								removeMediaPreview( $( this ) );
								return !1;
							});
						}
					} ); // end each

					// Image ordering
					if ( $buttons[0] ) {
						$('.cx-all-images-wrap', target).sortable( {
							items: 'div.cx-image-wrap',
							cursor: 'move',
							scrollSensitivity: 40,
							forcePlaceholderSize: true,
							forceHelperSize: false,
							helper: 'clone',
							opacity: 0.65,
							placeholder: 'cx-media-thumb-sortable-placeholder',
							start:function(){},
							stop:function(){},
							update: function() {
								var input_value = [],
									input_ids = [],
									input = $( this ).parent().siblings( '.cx-element-wrap' ).find( 'input.cx-upload-input' ),
									button = $( this ).parent().siblings( '.cx-element-wrap' ).find( 'button.cx-upload-button' ),
									settings = {
										multiple:     button.data( 'multi-upload' ),
										value_format: button.data( 'value-format' ),
									};

								$( '.cx-image-wrap', this ).each( function() {
									var attachment_id  = $( '.preview-holder', this ).data( 'id-attr' ),
										attachment_url = $( '.preview-holder', this ).data( 'url-attr' );

									input_ids.push( attachment_id );

									switch ( settings.value_format ) {
										case 'id':
											input_value.push( attachment_id );
											break;

										case 'url':
											input_value.push( attachment_url );
											break;

										case 'both':
											input_value.push( {
												id:  attachment_id,
												url: attachment_url,
											} );
											break;
									}
								} );

								input.val( prepareInputValue( input_value, settings ) ).attr( 'data-ids-attr', input_ids.join( ',' ) ).trigger( 'change' );
							}
						} );
					}
				}
			},//End CX-Media

			// CX-Colorpicker
			colorpicker: {
				inputClass: 'input.cx-ui-colorpicker:not([name*="__i__"])',

				init: function() {

					$( this.render.bind( this ) );

					$( document )
						//.on( 'ready.cxColorpicker', this.render.bind( this ) )
						.on( 'cx-control-init', this.render.bind( this ) );
				},

				render: function( event ) {
					var target = ( event._target ) ? event._target : $( 'body' ),
						input = $( this.inputClass, target );

					if ( input[0] ) {
						input.wpColorPicker( {
							change: this.changeHandler
						} );
					}
				},

				changeHandler: function( event, ui ) {
					var $this = $( event.target ),
						name  = $this.attr( 'name' );

					if ( ! name ) {
						return;
					}

					setTimeout( function() {
						$( window ).trigger( {
							type:          'cx-control-change',
							controlName:   name,
							controlStatus: $this.val()
						} );
					} );
				}
			},//End CX-Colorpicker

			// CX-Iconpicker
			iconpicker: {
				iconSets: {},
				iconSetsKey: 'cx-icon-sets',

				init: function() {

					$( this.setIconsSets.bind( this, window.CxIconSets ) );
					$( this.render.bind( this ) );

					$( document )
						//.on( 'ready.cxIconpicker', this.setIconsSets.bind( this, window.CxIconSets ) )
						//.on( 'ready.cxIconpicker', this.render.bind( this ) )
						.on( 'cx-control-init', this.render.bind( this ) );
				},

				setIconsSets: function( iconSets ) {
					var icons,
						_this = this;

					if ( iconSets ) {
						icons  = ( iconSets.response ) ? iconSets.response.CxIconSets : iconSets;

						$.each( icons, function( name, data ) {
							_this.iconSets[name] = data;
						} );

						_this.setState( _this.iconSetsKey, _this.iconSets );
					}
				},

				getIconsSets: function() {
					var iconSets = this.getState( this.iconSetsKey );

					if ( iconSets ) {
						this.iconSets = iconSets;
					}
				},

				render: function( event ) {
					var target = ( event._target ) ? event._target : $( 'body' ),
						$picker = $( '.cx-ui-iconpicker:not([name*="__i__"])', target ),
						$this,
						set,
						setData,
						_this = this;

					if ( $picker[0] ) {
						this.getIconsSets();

						$picker.each( function() {
							$this   = $( this );
							set     = $this.data( 'set' );
							setData = _this.iconSets[set];

							if ( $this.length && setData.icons ) {
								$this.iconpicker({
									icons: setData.icons,
									iconBaseClass: setData.iconBase,
									iconClassPrefix: setData.iconPrefix,
									animation: false,
									fullClassFormatter: function( val ) {
										return setData.iconBase + ' ' + setData.iconPrefix + val;
									}
								}).on( 'iconpickerUpdated', function() {
									$( this ).trigger( 'change' );
								});
							}

							if ( setData ) {
								$( 'head' ).append( '<link rel="stylesheet" type="text/css" href="' + setData.iconCSS + '"">' );
							}
						} );
					}
				},

				getState: function( key ) {
					try {
						return JSON.parse( window.sessionStorage.getItem( key ) );
					} catch ( e ) {
						return false;
					}
				},

				setState: function( key, data ) {
					try {
						window.sessionStorage.setItem( key, JSON.stringify( data ) );
					} catch ( e ) {
						return false;
					}
				}
			},//End CX-Iconpicker

			// CX-Dimensions
			dimensions: {
				container: '.cx-ui-dimensions',
				isLinked: '.cx-ui-dimensions__is-linked',
				units: '.cx-ui-dimensions__unit',
				unitsInput: 'input[name*="[units]"]',
				linkedInput: 'input[name*="[is_linked]"]',
				valuesInput: '.cx-ui-dimensions__val',

				init: function() {
					$( 'body' )
						.on( 'click', this.isLinked, { 'self': this }, this.switchLinked )
						.on( 'click', this.units, { 'self': this }, this.switchUnits )
						.on( 'input', this.valuesInput + '.is-linked', { 'self': this }, this.changeLinked );
				},

				render: function( event ) {

				},

				switchLinked: function( event ) {

					var self       = event.data.self,
						$this      = $( this ),
						$container = $this.closest( self.container ),
						$input     = $container.find( self.linkedInput ),
						$values    = $container.find( self.valuesInput ),
						isLinked   = $input.val();

					if ( 0 === parseInt( isLinked ) ) {
						$input.val(1);
						$this.addClass( 'is-linked' );
						$values.addClass( 'is-linked' );
					} else {
						$input.val(0);
						$this.removeClass( 'is-linked' );
						$values.removeClass( 'is-linked' );
					}

				},

				switchUnits: function( event ) {
					var self       = event.data.self,
						$this      = $( this ),
						unit       = $this.data( 'unit' ),
						$container = $this.closest( self.container ),
						$input     = $container.find( self.unitsInput ),
						$values    = $container.find( self.valuesInput ),
						range      = $container.data( 'range' );

					if ( $this.hasClass( 'is-active' ) ) {
						return;
					}

					$this.addClass( 'is-active' ).siblings( self.units ).removeClass( 'is-active' );
					$input.val( unit );
					$values.attr({
						min: range[ unit ].min,
						max: range[ unit ].max,
						step: range[ unit ].step
					});

				},

				changeLinked: function( event ) {
					var self  = event.data.self,
						$this = $( this ),
						$container = $this.closest( '.cx-ui-dimensions__values' );

					$( self.valuesInput, $container ).val( $this.val() )
				}
			},//End CX-Dimensions

			// CX-Wysiwyg
			wysiwyg: {

				defaultEditorSettings: {
					tinymce: {
						wpautop: true,
						toolbar1: 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,wp_adv,dfw',
						toolbar2: 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help'
					},
					quicktags: {
						buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,dfw'
					},
					mediaButtons: true
				},

				editorSettings: false,

				init: function() {

					var self = this;

					$( window ).on( 'load', function() {
						setTimeout( function() {
							$( self.render.bind( self ) );
						} )
					} );

					$( document )
						.on( 'cx-control-init', this.render.bind( this ) );

					$( window )
						.on( 'cx-repeater-sortable-stop', this.reInit.bind( this ) );
				},
				render: function( event ) {
					var self = this,
						target = ( event._target ) ? event._target : $( 'body' ),
						textarea = $( 'textarea.cx-ui-wysiwyg:not([name*="__i__"])', target );

					if ( textarea[0] ) {
						textarea.each( function() {
							var $this = $( this ),
								id    = $this.attr( 'id' );

							if ( $this.data( 'init' ) ) {
								return;
							}

							if ( typeof window.wp.editor.initialize !== 'undefined' ) {
								window.wp.editor.initialize( id, self.getEditorSettings() );
							} else {
								window.wp.oldEditor.initialize( id, self.getEditorSettings() );
							}

							var editor = window.tinymce.get( id );

							if ( editor ) {
								editor.on( 'change', function( event ) {
									$( window ).trigger( {
										type:          'cx-control-change',
										controlName:   $this.attr( 'name' ),
										controlStatus: editor.getContent()
									} );
								} );
							}

							self.addSaveTriggerOnEditTagsPage( id );

							$this.data( 'init', true );
						} );
					}
				},
				reInit: function( event ) {
					var self = this,
						target = event._item,
						textarea = $( 'textarea.wp-editor-area', target );

					if ( textarea[0] ) {
						textarea.each( function() {
							var $this = $( this ),
								id    = $this.attr( 'id' );

							if ( typeof window.wp.editor.initialize !== 'undefined' ) {
								window.wp.editor.remove( id );
								window.wp.editor.initialize( id, self.getEditorSettings() );
							} else {
								window.wp.oldEditor.remove( id );
								window.wp.oldEditor.initialize( id, self.getEditorSettings() );
							}
						} );
					}
				},
				getEditorSettings: function() {
					if ( this.editorSettings ) {
						return this.editorSettings;
					}

					this.editorSettings = this.defaultEditorSettings;

					if ( window.tinyMCEPreInit ) {
						if ( window.tinyMCEPreInit.mceInit && window.tinyMCEPreInit.mceInit.cx_wysiwyg ) {
							this.editorSettings.tinymce = window.tinyMCEPreInit.mceInit.cx_wysiwyg;
						}

						if ( window.tinyMCEPreInit.qtInit && window.tinyMCEPreInit.qtInit.cx_wysiwyg ) {
							this.editorSettings.quicktags = window.tinyMCEPreInit.qtInit.cx_wysiwyg;
						}
					}

					return this.editorSettings;
				},
				addSaveTriggerOnEditTagsPage: function( id ) {

					if ( -1 === window.location.href.indexOf( 'edit-tags.php' ) ) {
						return;
					}

					if ( window.tinymce ) {
						var editor = window.tinymce.get( id );

						if ( editor ) {
							editor.on( 'change', function() {
								editor.save();
							} );
						}

						// Reset editor content after added new term.
						$( document ).ajaxComplete( function( event, xhr, settings ) {

							if ( ! settings.data || -1 === settings.data.indexOf( 'action=add-tag' ) ) {
								return;
							}

							if ( -1 !== xhr.responseText.indexOf( 'wp_error' ) ) {
								return;
							}

							editor.setContent( '' );

						} );
					}
				},
			},//End CX-Wysiwyg

			// CX-Repeater
			repeater: {
				repeaterContainerClass: '.cx-ui-repeater-container',
				repeaterListClass: '.cx-ui-repeater-list',
				repeaterItemClass: '.cx-ui-repeater-item',
				repeaterItemHandleClass: '.cx-ui-repeater-actions-box',
				repeaterTitleClass: '.cx-ui-repeater-title',
				addItemButtonClass: '.cx-ui-repeater-add',
				removeItemButtonClass: '.cx-ui-repeater-remove',
				removeConfirmItemButtonClass: '.cx-ui-repeater-remove__confirm',
				removeCancelItemButtonClass: '.cx-ui-repeater-remove__cancel',
				copyItemButtonClass: '.cx-ui-repeater-copy',
				toggleItemButtonClass: '.cx-ui-repeater-toggle',
				minItemClass: 'cx-ui-repeater-min',
				sortablePlaceholderClass: 'sortable-placeholder',

				init: function() {

					$( this.addEvents.bind( this ) );
					//$( document ).on( 'ready.cxRepeat', this.addEvents.bind( this ) );
				},

				addEvents: function() {
					$( 'body' )
						.on( 'click', this.addItemButtonClass, { 'self': this }, this.addItem )
						.on( 'click', this.removeItemButtonClass, { 'self': this }, this.showRemoveItemTooltip )
						.on( 'click', this.removeConfirmItemButtonClass, { 'self': this }, this.removeItem )
						.on( 'click', this.removeCancelItemButtonClass, { 'self': this }, this.hideRemoveItemTooltip )
						.on( 'click', this.copyItemButtonClass, { 'self': this }, this.copyItem )
						.on( 'click', this.toggleItemButtonClass, { 'self': this }, this.toggleItem )
						.on( 'change', this.repeaterListClass + ' input, ' + this.repeaterListClass + ' textarea, ' + this.repeaterListClass + ' select', { 'self': this }, this.changeWrapperLable )
						.on( 'sortable-init', { 'self': this }, this.sortableItem );

					$( document )
						.on( 'cx-control-init', { 'self': this }, this.sortableItem );

					this.triggers();
				},

				triggers: function( $target ) {
					$( 'body' ).trigger( 'sortable-init' );

					if ( $target ) {
						$( document ).trigger( 'cx-control-init', { 'target': $target } );
					}

					return this;
				},

				addItem: function( event ) {
					var self        = event.data.self,
						$list       = $( this ).prev( self.repeaterListClass ),
						index       = $list.data( 'index' ),
						tmplName    = $list.data( 'name' ),
						rowTemplate = wp.template( tmplName ),
						widgetId    = $list.data( 'widget-id' ),
						data        = { index: index },
						$parent     = $list.parent().closest( self.repeaterListClass );

					widgetId = '__i__' !== widgetId ? widgetId : $list.attr( 'id' ) ;

					if ( widgetId ) {
						data.widgetId = widgetId;
					}

					if ( $parent.length ) {
						data.parentIndex = parseInt( $parent.data( 'index' ), 10 ) - 1;
					}

					$list.append( rowTemplate( data ) );

					index++;
					$list.data( 'index', index );

					self.triggers( $( self.repeaterItemClass + ':last', $list ) ).stopDefaultEvent( event );
				},

				copyItem: function( event ) {
					var self        = event.data.self,
						$item       = $( this ).closest( self.repeaterItemClass ),
						$list       = $( this ).closest( self.repeaterListClass ),
						$parent     = $list.parent().closest( self.repeaterListClass ),
						itemIndex   = $item.data( 'item-index' ),
						newIndex    = $list.data( 'index' ),
						tmplName    = $list.data( 'name' ),
						widgetId    = $list.data( 'widget-id' ),
						rowTemplate = wp.template( tmplName ),
						data        = { index: newIndex },
						newItemHtml,
						$newItem;

					widgetId = '__i__' !== widgetId ? widgetId : $list.attr( 'id' ) ;

					if ( widgetId ) {
						data.widgetId = widgetId;
					}

					if ( $parent.length ) {
						data.parentIndex = parseInt( $parent.data( 'index' ), 10 ) - 1;
					}

					$newItem = $( rowTemplate( data ) );

					// Set values.
					$item.find( '.cx-ui-repeater-item-control' ).each( function() {
						var controlName = $( this ).data( 'repeater-control-name' ),
							$field      = $( this ).find( '[name^="' + widgetId + '\[item-' + itemIndex + '\]\[' + controlName + '\]"]' );

						// Set value for checkbox, radio, switcher fields.
						if ( $field.filter( '.cx-checkbox-input, .cx-radio-input, .cx-input-switcher' ).length ) {

							$field.each( function() {
								var $this       = $( this ),
									checked     = $this.prop( 'checked' ),
									value       = $this.val(),
									nameAttr    = $this.attr( 'name' ),
									newNameAttr = nameAttr.replace( '[item-' + itemIndex + ']', '[item-' + newIndex + ']' );

								if ( $this.hasClass( 'cx-checkbox-input' ) ) {
									$newItem.find( '[name="' + newNameAttr + '"]' ).val( value ).attr( 'checked', checked );
								} else {
									$newItem.find( '[name="' + newNameAttr + '"][value="' + value + '"]' ).prop( 'checked', checked );
								}
							} );

						// Set value for select fields.
						} else if ( $field.filter( '.cx-ui-select' ).length ) {
							var hasFilter  = $field.data( 'filter' );

							if ( hasFilter ) {
								$newItem
									.find( '.cx-ui-select[name^="' + widgetId + '\[item-' + newIndex + '\]\[' + controlName + '\]"]' )
									.html( $field.html() );
							} else {
								$newItem
									.find( '.cx-ui-select[name^="' + widgetId + '\[item-' + newIndex + '\]\[' + controlName + '\]"]' )
									.val( $field.val() );
							}

						} else {
							$newItem
								.find( '[name="' + widgetId + '\[item-' + newIndex + '\]\[' + controlName + '\]"]' )
								.val( $field.val() );
						}

						// Add media preview.
						var $mediaWrap = $( this ).find( '.cx-ui-media-wrap' );

						if ( $mediaWrap.length ) {
							var previewHtml = $mediaWrap.find( '.cx-upload-preview' ).html();

							$newItem
								.find( '.cx-ui-repeater-item-control[data-repeater-control-name="' + controlName + '"] .cx-upload-preview' )
								.html( previewHtml );
						}
					} );

					// Add repeater title.
					$newItem.find( '.cx-ui-repeater-title' ).html( $item.find( '.cx-ui-repeater-title' ).html() );

					$item.after( $newItem );

					newIndex++;
					$list.data( 'index', newIndex );

					self.triggers( $newItem )
						.stopDefaultEvent( event );
				},

				showRemoveItemTooltip: function( event ) {
					var self = event.data.self;

					$( this ).find( '.cx-tooltip' ).addClass( 'cx-tooltip--show' );

					self.stopDefaultEvent( event );
				},

				hideRemoveItemTooltip: function( event ) {
					var self = event.data.self;

					$( this ).closest( '.cx-tooltip' ).removeClass( 'cx-tooltip--show' );

					self.stopDefaultEvent( event );
				},

				removeItem: function( event ) {
					var self  = event.data.self,
						$list = $( this ).closest( self.repeaterListClass );

					self.applyChanges( $list );

					$( this ).closest( self.repeaterItemClass ).remove();

					self
						.triggers()
						.stopDefaultEvent( event );
				},

				toggleItem: function( event ) {
					var self = event.data.self,
						$container = $( this ).closest( self.repeaterItemClass );

					$container.toggleClass( self.minItemClass );

					self.stopDefaultEvent( event );
				},

				sortableItem: function( event ) {
					var self  = event.data.self,
						$list = $( self.repeaterListClass ),
						$this,
						initFlag;

					$list.each( function( indx, element ) {
						$this    = $( element );
						initFlag = $( element ).data( 'sortable-init' );

						if ( ! initFlag ) {
							$this.sortable( {
								items: self.repeaterItemClass,
								handle: self.repeaterItemHandleClass,
								cursor: 'move',
								scrollSensitivity: 40,
								forcePlaceholderSize: true,
								forceHelperSize: false,
								distance: 2,
								tolerance: 'pointer',
								helper: function( event, element ) {
									return element.clone()
										.find( ':input' )
										.attr( 'name', function( i, currentName ) {
											return 'sort_' + parseInt( Math.random() * 100000, 10 ).toString() + '_' + currentName;
										} )
										.end();
								},
								start:function( event, ui ){
									$( window ).trigger( {
										type: 'cx-repeater-sortable-start',
										_item: ui.item
									} );
								},
								stop:function( event, ui ){
									$( window ).trigger( {
										type: 'cx-repeater-sortable-stop',
										_item: ui.item
									} );
								},
								opacity: 0.65,
								placeholder: self.sortablePlaceholderClass,
								create: function() {
									$this.data( 'sortable-init', true );
								},
								update: function( event, ui ) {
									var target = $( event.target );

									self.applyChanges( target );
								}
							} );
						} else {
							$this.sortable( 'refresh' );
						}
					} );
				},

				changeWrapperLable: function( event ) {
					var self        = event.data.self,
						$list       = $( self.repeaterListClass ),
						titleFilds  = $list.data( 'title-field' ),
						$this       = $( this ),
						value,
						parentItem;

					if ( titleFilds && $this.closest( '.' + titleFilds + '-wrap' )[0] ) {
						value       = $this.val(),
						parentItem  = $this.closest( self.repeaterItemClass );

						$( self.repeaterTitleClass, parentItem ).html( value );
					}

					self.stopDefaultEvent( event );
				},

				applyChanges: function( target ) {
					if ( undefined !== wp.customize ) {
						$( 'input[name]:first, select[name]:first', target ).change();
					}

					return this;
				},

				stopDefaultEvent: function( event ) {
					event.preventDefault();
					event.stopImmediatePropagation();
					event.stopPropagation();

					return this;
				}

			},

			// CX-Text, CX-Textarea
			text: {
				inputClass: '.cx-ui-text:not([name*="__i__"]), .cx-ui-textarea:not([name*="__i__"])',

				init: function() {
					$( 'body' )
						.on( 'input.cxText, change.cxText', this.inputClass, this.changeHandler.bind( this ) );
				},

				changeHandler: function( event ) {
					var $this = $( event.currentTarget ),
						name  = $this.attr( 'name' );

					if ( ! name ) {
						return;
					}

					$( window ).trigger( {
						type: 'cx-control-change',
						controlName: name,
						controlStatus: $this.val()
					} );
				}
			}
		},

		utils: {

			/**
			 * Serialize form into
			 *
			 * @return {Object}
			 */
			serializeObject: function( selector ) {

				var self = this,
					json = {},
					pushCounters = {},
					patterns = {
						'validate': /^[a-zA-Z_][a-zA-Z0-9_-]*(?:\[(?:\d*|[a-zA-Z0-9\s_-]+)\])*$/,
						'key':      /[a-zA-Z0-9\s_-]+|(?=\[\])/g,
						'push':     /^$/,
						'fixed':    /^\d+$/,
						'named':    /^[a-zA-Z0-9\s_-]+$/
					},
					serialized;

				this.build = function( base, key, value ) {
					base[ key ] = value;

					return base;
				};

				this.push_counter = function( key ) {
					if ( undefined === pushCounters[ key ] ) {
						pushCounters[ key ] = 0;
					}

					return pushCounters[ key ]++;
				};

				if ( 'FORM' === selector[0].tagName ) {
					serialized = selector.serializeArray();
				} else {
					serialized = selector.find( 'input, textarea, select' ).serializeArray();
				}

				$.each( serialized, function() {
					var k, keys, merge, reverseKey;

					// Skip invalid keys
					if ( ! patterns.validate.test( this.name ) ) {
						return;
					}

					keys = this.name.match( patterns.key );
					merge = this.value;
					reverseKey = this.name;

					while ( undefined !== ( k = keys.pop() ) ) {

						// Adjust reverseKey
						reverseKey = reverseKey.replace( new RegExp( '\\[' + k + '\\]$' ), '' );

						// Push
						if ( k.match( patterns.push ) ) {
							merge = self.build( [], self.push_counter( reverseKey ), merge );
						} else if ( k.match( patterns.fixed ) ) {
							merge = self.build( {}, k, merge );
						} else if ( k.match( patterns.named ) ) {
							merge = self.build( {}, k, merge );
						}
					}

					json = $.extend( true, json, merge );
				});

				return json;
			},

			/**
			 * Boolean value check
			 *
			 * @return {Boolean}
			 */
			filterBoolValue: function( value ) {
				var num = +value;

				return ! isNaN( num ) ? !! num : !! String( value ).toLowerCase().replace( !!0, '' );
			}
		},

		controlValidation: {

			errorMessages: {
				required: window.cxInterfaceBuilder.i18n.requiredError,
				min:      window.cxInterfaceBuilder.i18n.minError,
				max:      window.cxInterfaceBuilder.i18n.maxError,
				step:     window.cxInterfaceBuilder.i18n.stepError,
			},

			init: function() {

				if ( this.isBlockEditor() ) {
					this.onBlockEditorSavePost();
				} else {
					$( '#post, #edittag, #your-profile, .cx-form' ).on( 'submit', this.onSubmitForm.bind( this ) );
				}

				cxInterfaceBuilder.filters.addFilter( 'cxInterfaceBuilder/form/validation', this.requiredValidation.bind( this ) );
				cxInterfaceBuilder.filters.addFilter( 'cxInterfaceBuilder/form/validation', this.numberValidation.bind( this ) );

				$( window ).on(
					'cx-control-change cx-checkbox-change cx-radio-change cx-select-change cx-select2-change',
					this.removeFieldErrorOnChange.bind( this )
				);

				$( '.cx-control-repeater' ).on( 'focusin', this.removeRepeaterErrorOnChange.bind( this ) );
			},

			isBlockEditor: function() {
				return $( 'body' ).hasClass( 'block-editor-page' );
			},

			onBlockEditorSavePost: function() {
				var self     = this,
					editor   = wp.data.dispatch( 'core/editor' ),
					savePost = editor.savePost;

					editor.savePost = function( options ) {
						options = options || {};

						if ( options.isAutosave || options.isPreview ) {
							savePost( options );
							return;
						}

						self.beforeValidation();

						var validation = cxInterfaceBuilder.filters.applyFilters( 'cxInterfaceBuilder/form/validation', true, $( '#editor' ) );

						if ( validation ) {
							savePost( options );
						} else {
							self.scrollToFirstErrorField();
						}
					};
			},

			onSubmitForm: function( event ) {

				this.beforeValidation();

				var validation = cxInterfaceBuilder.filters.applyFilters( 'cxInterfaceBuilder/form/validation', true, $( event.target ) );

				if ( ! validation ) {
					this.scrollToFirstErrorField();
					event.preventDefault();
				}
			},

			beforeValidation: function() {
				this.removeAllFieldsErrors();

				if ( 'undefined' !== typeof window.tinyMCE ) {
					window.tinyMCE.triggerSave();
				}
			},

			requiredValidation: function( validation, $form ) {

				if ( ! validation ) {
					return validation;
				}

				var self            = this,
					$requiredFields = $form.find( '.cx-control-required:not(.cx-control-hidden)' ),
					hasEmptyFields  = false;

				if ( ! $requiredFields.length ) {
					return validation;
				}

				$requiredFields.each( function() {
					var $field      = $( this ),
						controlName = $field.data( 'control-name' ),
						controlVal  = false;

					if ( $field.hasClass( 'cx-control-checkbox' ) || $field.hasClass( 'cx-control-radio' ) ) {
						controlVal = !! $field.find( '[name^="' + controlName + '"]' ).filter( ':checked' ).length;
					} else if ( $field.hasClass( 'cx-control-repeater' ) ) {
						controlVal = !! $field.find( '.cx-ui-repeater-item' ).length;
					} else {
						controlVal = $field.find( '[name^="' + controlName +'"]' ).val();
					}

					if ( Array.isArray( controlVal ) ) {
						controlVal = !! controlVal.length;
					}

					if ( ! controlVal ) {
						self.addFieldError( $field, self.errorMessages.required );
						hasEmptyFields = true;
					}
				} );

				if ( hasEmptyFields ) {
					return false;
				}

				return validation;
			},

			numberValidation: function( validation, $form ) {

				if ( ! validation ) {
					return validation;
				}

				if ( ! this.isBlockEditor() ) {
					return validation;
				}

				var self             = this,
					$numberFields    = $form.find( '.cx-control-stepper:not(.cx-control-hidden)' ),
					hasInValidFields = false;

				if ( ! $numberFields.length ) {
					return validation;
				}

				$numberFields.each( function() {
					var $field   = $( this ),
						$input   = $field.find( 'input.cx-ui-stepper-input' ),
						minAttr  = $input.attr( 'min' ),
						maxAttr  = $input.attr( 'max' ),
						stepAttr = $input.attr( 'step' ),
						value    = $input.val();

					if ( '' !== minAttr && value && Number( value ) < Number( minAttr ) ) {
						self.addFieldError( $field, self.errorMessages.min.replace( '%s', minAttr ) );
						hasInValidFields = true;
					} else if ( '' !== maxAttr && value && Number( value ) > Number( maxAttr ) ) {
						self.addFieldError( $field, self.errorMessages.max.replace( '%s', maxAttr ) );
						hasInValidFields = true;
					} else if ( '' !== stepAttr && value && 0 !== ( Number( value ) % Number( stepAttr ) ) ) {
						self.addFieldError( $field, self.errorMessages.step.replace( '%s', stepAttr ) );
						hasInValidFields = true;
					}
				} );

				if ( hasInValidFields ) {
					return false;
				}

				return validation;
			},

			addFieldError: function( $field, message ) {
				var $error = $field.find( '.cx-control__error' );

				if ( $error.length ) {
					$error.html( message );
				} else {
					$field.find('.cx-control__content').append( '<div class="cx-control__error">' + message + '</div>' );
				}

				$field.addClass( 'cx-control--error' );
			},

			removeFieldError: function( $field ) {
				$field.find( '.cx-control__error' ).remove();
				$field.removeClass( 'cx-control--error' );
			},

			removeFieldErrorOnChange: function( event ) {
				var $field = $( '.cx-control[data-control-name="' + event.controlName + '"]' );

				if ( ! $field.hasClass( 'cx-control--error' ) ) {
					return;
				}

				this.removeFieldError( $field );
			},

			removeRepeaterErrorOnChange: function( event ) {
				var $field = $( event.currentTarget ).closest( '.cx-control' );

				if ( ! $field.hasClass( 'cx-control--error' ) ) {
					return;
				}

				this.removeFieldError( $field );
			},

			removeAllFieldsErrors: function() {
				var self = this,
					$errorFields = $( '.cx-control--error' );

				if ( $errorFields.length ) {
					$errorFields.each( function() {
						self.removeFieldError( $( this ) );
					} );
				}
			},

			scrollToFirstErrorField: function() {
				var $field = $( '.cx-control--error' ).first();

				if ( ! $field.is( ':visible' ) ) {

					// Field inside hidden component.
					var $parentComponent = $field.closest( '.cx-component' );

					if ( $parentComponent.length ) {
						var componentID = $field.closest( '.cx-settings__content' ).attr( 'id' );
						$parentComponent.find( '[data-content-id="#' + componentID + '"]' ).trigger( 'click' );
					}

					// Field inside hidden postbox.
					var $postbox = $field.closest( '.postbox.closed' );

					if ( $postbox.length ) {
						$postbox.find( 'button.handlediv' ).trigger( 'click' );
					}
				}

				var $scrollSelector = $( 'html, body' ),
					scrollTop = $field.offset().top,
					offset = 40;

				if ( this.isBlockEditor() ) {

					if ( $( 'body' ).hasClass( 'is-fullscreen-mode' ) ) {
						offset += 20;
					} else {
						offset += 60;
					}

					if ( $field.closest( '.interface-interface-skeleton__sidebar' ).length ) {
						$scrollSelector = $( '#editor .interface-interface-skeleton__sidebar' );
						offset += 50;
					} else {
						$scrollSelector = $( '#editor .interface-interface-skeleton__content' );
					}

					scrollTop += $scrollSelector.scrollTop();
				}

				$scrollSelector.stop().animate( { scrollTop: scrollTop - offset }, 500 );
			}
		},

		filters: ( function() {

			var callbacks = {};

			return {

				addFilter: function( name, callback ) {

					if ( ! callbacks.hasOwnProperty( name ) ) {
						callbacks[name] = [];
					}

					callbacks[name].push(callback);

				},

				applyFilters: function( name, value, args ) {

					if ( ! callbacks.hasOwnProperty( name ) ) {
						return value;
					}

					if ( args === undefined ) {
						args = [];
					}

					var container = callbacks[ name ];
					var cbLen     = container.length;

					for (var i = 0; i < cbLen; i++) {
						if (typeof container[i] === 'function') {
							value = container[i](value, args);
						}
					}

					return value;
				}
			};

		})()

	};

	cxInterfaceBuilder.init();

	window.cxInterfaceBuilderAPI = cxInterfaceBuilder;

}( jQuery, window._ ) );
