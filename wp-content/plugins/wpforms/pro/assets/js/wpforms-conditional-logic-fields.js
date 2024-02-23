/* globals wpforms_conditional_logic */
( function( $ ) {

	'use strict';

	var WPFormsConditionals = {

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init: function() {

			// Document ready.
			$( document ).ready( WPFormsConditionals.ready );

			WPFormsConditionals.bindUIActions();
		},

		/**
		 * Document ready.
		 *
		 * @since 1.1.2
		 */
		ready: function() {

			$( '.wpforms-form' ).each( function() {
				WPFormsConditionals.processConditionals( $( this ), false );
			} );
		},

		/**
		 * Element bindings.
		 *
		 * @since 1.0.0
		 */
		bindUIActions: function() {

			$( document ).on( 'change', '.wpforms-conditional-trigger input, .wpforms-conditional-trigger select', function() {
				WPFormsConditionals.processConditionals( $( this ), true );
			} );

			$( document ).on( 'input', '.wpforms-conditional-trigger input[type=text], .wpforms-conditional-trigger input[type=email], .wpforms-conditional-trigger input[type=url], .wpforms-conditional-trigger input[type=number], .wpforms-conditional-trigger textarea', function() {
				WPFormsConditionals.processConditionals( $( this ), true );
			} );

			$( '.wpforms-form' ).submit( function() {
				WPFormsConditionals.resetHiddenFields( $( this ) );
			} );
		},

		/**
		 * Reset any form elements that are inside hidden conditional fields.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} el The form.
		 */
		resetHiddenFields: function( el ) {

			if ( window.location.hash && '#wpformsdebug' === window.location.hash ) {
				console.log( 'Resetting hidden fields...' );
			}

			var $form = $( el );

			$form.find( '.wpforms-conditional-hide :input' ).each( function() {
				switch ( $( this ).attr( 'type' ) ) {
					case 'button':
					case 'submit':
					case 'reset':
					case 'hidden':
						break;
					case 'checkbox':
					case 'radio':
						$( this ).closest( 'ul' ).find( 'li' ).removeClass( 'wpforms-selected' );
						if ( $( this ).is( ':checked' ) ) {
							$( this ).prop( 'checked', false ).trigger( 'change' );
						}
						break;
					case 'select':
							$( this ).find( 'option:selected' ).prop( 'selected', 'false' ).trigger( 'change' );
						break;
					default:
						if ( $( this ).val() !== '' ) {
							$( this ).val( '' ).trigger( 'input' );
						}
						break;
				}
			} );
		},

		/**
		 * Process conditionals for a form.
		 *
		 * @since 1.0.0
		 *
		 * @param {element} el Any element inside the targeted form.
		 * @param {boolean} initial Initial run of processing.
		 *
		 * @returns {void}|{boolean}
		 */
		processConditionals: function( el, initial ) {

			var $this   = $( el ),
				$form   = $this.closest( '.wpforms-form' ),
				formID  = $form.data( 'formid' ),
				hidden  = false;

			if ( typeof wpforms_conditional_logic === 'undefined' || typeof wpforms_conditional_logic[formID] === 'undefined' ) {
				return false;
			}

			var fields = wpforms_conditional_logic[formID];

			// Fields.
			for ( var fieldID in fields ) {
				if ( ! fields.hasOwnProperty( fieldID ) ) {
					continue;
				}

				if ( window.location.hash && '#wpformsdebug' === window.location.hash ) {
					console.log( 'Processing conditionals for Field #' + fieldID + '...' );
				}

				var field  = fields[fieldID].logic,
					action = fields[fieldID].action,
					pass   = false;

				// Groups.
				for ( var groupID in field ) {
					if ( ! field.hasOwnProperty( groupID ) ) {
						continue;
					}

					var group      = field[groupID],
						pass_group = true;

					// Rules.
					for ( var ruleID in group ) {
						if ( ! group.hasOwnProperty( ruleID ) ) {
							continue;
						}

						var rule      = group[ruleID],
							val       = '',
							pass_rule = false,
							left      = '',
							right     = '',
							$check;

						if ( window.location.hash && '#wpformsdebug' === window.location.hash ) {
							console.log( rule );
						}

						if ( ! rule.field ) {
							continue;
						}

						if ( rule.operator === 'e' || rule.operator === '!e' ) {

							rule.value = '';

							if ( [  'radio',
									'checkbox',
									'payment-multiple',
									'payment-checkbox',
									'rating',
									'net_promoter_score' ].indexOf( rule.type ) > -1
							) {
								$check = $form.find( '#wpforms-' + formID + '-field_' + rule.field + '-container input:checked' );
								if ( $check.length ) {
									val = true;
								}
							} else {
								val = $form.find( '#wpforms-' + formID + '-field_' + rule.field ).val();
								if ( ! val  ) {
									val = '';
								}
							}

						} else {

							if ( [  'radio',
									'checkbox',
									'payment-multiple',
									'payment-checkbox',
									'rating',
									'net_promoter_score' ].indexOf( rule.type ) > -1
							) {
								$check = $form.find( '#wpforms-' + formID + '-field_' + rule.field + '-container input:checked' );
								if ( $check.length ) {
									$.each( $check, function() {
										var escapeVal = WPFormsConditionals.escapeText( $( this ).val() );
										if ( [ 'checkbox', 'payment-checkbox' ].indexOf( rule.type ) > -1 ) {
											if ( rule.value === escapeVal ) {
												val = escapeVal;
											}
										} else {
											val = escapeVal;
										}
									} );
								}
							} else {

								// text, textarea, number, select.
								val = $form.find( '#wpforms-' + formID + '-field_' + rule.field ).val();
								if ( [ 'select', 'payment-select' ].indexOf( rule.type ) > -1 ) {
									val = WPFormsConditionals.escapeText( val );
								}
							}
						}

						if ( null === val ) {
							val = '';
						}
						left  = $.trim( val.toString().toLowerCase() );
						right = $.trim( rule.value.toString().toLowerCase() );

						switch ( rule.operator ) {
							case '==' :
								pass_rule = ( left === right );
								break;
							case '!=' :
								pass_rule = ( left !== right );
								break;
							case 'c' :
								pass_rule = ( left.indexOf( right ) > -1 && left.length > 0 );
								break;
							case '!c' :
								pass_rule = ( left.indexOf( right ) === -1 && right.length > 0 );
								break;
							case '^' :
								pass_rule = ( left.lastIndexOf( right, 0 ) === 0 );
								break;
							case '~' :
								pass_rule = ( left.indexOf( right, left.length - right.length ) !== -1 );
								break;
							case 'e' :
								pass_rule = ( left.length === 0 );
								break;
							case '!e' :
								pass_rule = ( left.length > 0 );
								break;
							case '>' :
								left      = left.replace( /[^0-9.]/g, '' );
								pass_rule = ( '' !== left ) && ( WPFormsConditionals.floatval( left ) > WPFormsConditionals.floatval( right ) );
								break;
							case '<' :
								left      = left.replace( /[^0-9.]/g, '' );
								pass_rule = ( '' !== left ) && ( WPFormsConditionals.floatval( left ) < WPFormsConditionals.floatval( right ) );
								break;
						}

						if ( ! pass_rule ) {
							pass_group = false;
							break;
						}
					}

					if ( pass_group ) {
						pass = true;
					}
				}

				if ( window.location.hash && '#wpformsdebug' === window.location.hash ) {
					console.log( 'Result: ' + pass );
				}

				if ( ( pass && action === 'hide' ) || ( ! pass && action !== 'hide' ) ) {
					$form
						.find( '#wpforms-' + formID + '-field_' + fieldID + '-container' )
						.hide()
						.addClass( 'wpforms-conditional-hide' )
						.removeClass( 'wpforms-conditional-show' );
					hidden = true;
				} else {
					$form
						.find( '#wpforms-' + formID + '-field_' + fieldID + '-container' )
						.show()
						.removeClass( 'wpforms-conditional-hide' )
						.addClass( 'wpforms-conditional-show' );
				}

				$( document ).trigger( 'wpformsProcessConditionalsField', [ formID, fieldID, pass, action ] );
			}

			if ( hidden ) {
				WPFormsConditionals.resetHiddenFields( $form );
				if ( initial ) {
					if ( window.location.hash && '#wpformsdebug' === window.location.hash ) {
						console.log( 'Final Processing' );
					}
					WPFormsConditionals.processConditionals( $this, false );
				}
			}

			$( document ).trigger( 'wpformsProcessConditionals', [ $this, $form, formID ] );
		},

		/**
		 * Escape text similar to PHP htmlspecialchars().
		 *
		 * @since 1.0.5
		 *
		 * @param {string} text String to escape.
		 *
		 * @returns {string|boolean} Escaped text.
		 */
		escapeText: function( text ) {

			if ( null == text || ! text.length ) {
				return null;
			}

			var map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;',
			};

			return text.replace( /[&<>"']/g, function( m ) {
				return map[ m ];
			} );
		},

		/**
		 * Parse float. Returns 0 instead of NaN. Similar to PHP floatval().
		 *
		 * @since 1.4.7.1
		 *
		 * @param {mixed} mixedVar Probably string.
		 *
		 * @returns {float} parseFloat
		 */
		floatval: function( mixedVar ) {

			return ( parseFloat( mixedVar ) || 0 );
		},
	};

	WPFormsConditionals.init();

	window.wpformsconditionals = WPFormsConditionals;

} ) ( jQuery );
