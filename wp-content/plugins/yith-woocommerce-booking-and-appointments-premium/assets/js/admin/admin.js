/* global wcbk_admin, adminpage */
jQuery( function ( $ ) {
	"use strict";

	/**
	 * Onoff
	 */
	var yith_wcbk_onoff = {
		init  : function () {
			$( document ).on( 'click', '.yith-wcbk-printer-field__on-off', yith_wcbk_onoff.update );
		},
		update: function ( event ) {
			var onoff        = $( event.target ).closest( '.yith-wcbk-printer-field__on-off' ),
				hidden_input = onoff.find( '.yith-wcbk-printer-field__on-off__value' ).first(),
				value        = hidden_input ? hidden_input.val() : 'no';

			if ( value === 'yes' ) {
				hidden_input.val( 'no' );
				onoff.removeClass( 'yith-wcbk-printer-field__on-off--enabled' );
			} else {
				hidden_input.val( 'yes' );
				onoff.addClass( 'yith-wcbk-printer-field__on-off--enabled' );
			}
			hidden_input.trigger( 'change' );
		}
	};

	yith_wcbk_onoff.init();

	/**
	 * Time Select
	 */
	var yith_wcbk_timeselect = {
		container: '.yith-wcbk-time-select__container',
		hour     : '.yith-wcbk-time-select-hour',
		minute   : '.yith-wcbk-time-select-minute',
		separator: ':',
		init     : function () {
			var self = yith_wcbk_timeselect;

			$( document ).on( 'change', self.hour + ', ' + self.minute, self.update );
		},
		update   : function ( event ) {
			var self      = yith_wcbk_timeselect,
				container = $( event.target ).closest( self.container ),
				hour      = container.find( self.hour ).first(),
				minute    = container.find( self.minute ).first(),
				input     = container.find( 'input' ).first();

			input.val( hour.val() + self.separator + minute.val() ).trigger( 'change' );
		}
	};

	yith_wcbk_timeselect.init();


	/**
	 * Select2 - Select All | Deselect All
	 */
	var select_all_btn   = $( '.yith-wcbk-select2-select-all' ),
		deselect_all_btn = $( '.yith-wcbk-select2-deselect-all' );

	deselect_all_btn.each( function () {
		var _currentButton      = $( this ),
			select_id           = $( this ).data( 'select-id' ),
			target_select       = $( '#' + select_id ),
			_checkForVisibility = function () {
				if ( target_select.val() && target_select.val().length ) {
					_currentButton.show();
				} else {
					_currentButton.hide();
				}
			};

		target_select.on( 'change', _checkForVisibility );
		_checkForVisibility();
	} );

	select_all_btn.on( 'click', function () {
		var select_id     = $( this ).data( 'select-id' ),
			target_select = $( '#' + select_id );

		target_select.find( 'option' ).prop( 'selected', true );
		target_select.trigger( 'change' );
	} );

	deselect_all_btn.on( 'click', function () {
		var select_id     = $( this ).data( 'select-id' ),
			target_select = $( '#' + select_id );

		target_select.find( 'option' ).prop( 'selected', false );
		target_select.trigger( 'change' );
	} );


	/**
	 * Delete Logs Confirmation
	 */
	$( '#yith-wcbk-logs' ).on( 'click', 'h2 a.page-title-action', function ( event ) {
		event.stopImmediatePropagation();
		return window.confirm( wcbk_admin.i18n_delete_log_confirmation );
	} );


	/**
	 * Tip Tip
	 */
	$( document ).on( 'yith-wcbk-init-tiptip', function () {
		// Remove any lingering tooltips
		$( '#tiptip_holder' ).removeAttr( 'style' );
		$( '#tiptip_arrow' ).removeAttr( 'style' );
		$( '.tips' ).tipTip( {
								 'attribute': 'data-tip',
								 'fadeIn'   : 50,
								 'fadeOut'  : 50,
								 'delay'    : 200
							 } );
	} ).trigger( 'yith-wcbk-init-tiptip' );


	/**
	 * Date Picker
	 */
	$( document ).on( 'yith-wcbk-init-datepickers', function () {
		$( '.yith-wcbk-admin-date-picker' ).yith_wcbk_datepicker();
	} ).trigger( 'yith-wcbk-init-datepickers' );


	/**
	 *  Copy on Clipboard
	 */
	var copy_to_clipboard_tip = false;
	$( document ).on( 'click', '.yith-wcbk-copy-to-clipboard', function ( event ) {
		var target           = $( this ),
			selector_to_copy = target.data( 'selector-to-copy' ),
			obj_to_copy      = $( selector_to_copy );

		if ( obj_to_copy.length > 0 ) {
			copy_to_clipboard_tip && copy_to_clipboard_tip.remove() && ( copy_to_clipboard_tip = false );

			if ( !copy_to_clipboard_tip ) {
				copy_to_clipboard_tip = $( '<div id="yith-wcbk-copy-to-clipboard__copied">' + wcbk_admin.i18n_copied + '</div>' );
				$( 'body' ).append( copy_to_clipboard_tip );
			}

			copy_to_clipboard_tip.hide();


			var temp  = $( "<input>" ),
				value = obj_to_copy.is( 'input' ) ? obj_to_copy.val() : obj_to_copy.html();
			$( 'body' ).append( temp );

			temp.val( value ).select();
			document.execCommand( "copy" );

			temp.remove();

			copy_to_clipboard_tip.css( {
										   left: target.offset().left + target.outerWidth() / 2 - copy_to_clipboard_tip.outerWidth() / 2,
										   top : target.offset().top - copy_to_clipboard_tip.outerHeight() - 7
									   } )
				.fadeIn().delay( 1000 ).fadeOut();
		}
	} );


	/**
	 *  Show conditional: show/hide element based on other element value
	 */
	$( '.yith-wcbk-show-conditional' ).hide().each( function () {
		var $show_conditional = $( this ),
			field_id          = $show_conditional.data( 'field-id' ),
			$field            = $( '#' + field_id ),
			value             = $show_conditional.data( 'value' ),
			_to_compare, _is_checkbox, _is_onoff;

		if ( $field.length ) {
			_is_checkbox = $field.is( 'input[type=checkbox]' );
			_is_checkbox && ( value = value !== 'no' );

			_is_onoff = $field.is( '.yith-wcbk-printer-field__on-off' );
			_is_onoff && ( $field = $field.find( 'input' ) );

			$field.on( 'change keyup', function () {
				_to_compare = !_is_checkbox ? $field.val() : $field.is( ':checked' );
				if ( _to_compare === value ) {
					$show_conditional.show();
				} else {
					$show_conditional.hide();
				}
			} ).trigger( 'change' );
		}
	} );


	/**
	 *  Move
	 */
	$( '.yith-wcbk-move' ).each( function () {
		var $to_move = $( this ),
			after    = $to_move.data( 'after' );

		if ( after.length > 0 ) {
			$to_move.insertAfter( after ).show();
		}
	} );


	/**
	 *  Date Time Fields
	 */
	$( '.yith-wcbk-date-time-field' ).each( function () {
		var $dateTime  = $( this ),
			dateAnchor = $( this ).data( 'date' ),
			timeAnchor = $( this ).data( 'time' ),
			$date      = $( dateAnchor ).first(),
			$time      = $( timeAnchor ).first(),
			update     = function () {
				$dateTime.val( $date.val() + ' ' + $time.val() );
			};

		$date.on( 'change', update );
		$time.on( 'change', update );
	} );

	/**
	 *  Logs
	 */
	$( document ).on( 'click', '#yith-wcbk-logs-tab-table td.description-column .expand:not(.disabled)', function ( e ) {
		var open               = $( e.target ),
			description_column = open.closest( 'td.description-column' );
		description_column.toggleClass( 'expanded' );
	} );

	/**
	 * Google Calendar: Float button saving
	 */
	$( document ).on( 'yith-plugin-fw-float-save-button-after-saving', function ( event, response ) {
		var googleCalendarLeft = $( '#yith-wcbk-google-calendar-tab__main' );

		if ( googleCalendarLeft.length && response ) {
			var newContent = $( response ).find( '#yith-wcbk-google-calendar-tab__main' );
			if ( newContent.length ) {
				googleCalendarLeft.html( newContent.html() );
			}
		}

	} );

	/**
	 * Fields and deps
	 */
	var costsIncludedInShownPrice                   = $( '#yith-wcbk-costs-included-in-shown-price input[type=checkbox]' ),
		showDurationUnitInPrice                     = $( '#yith-wcbk-show-duration-unit-in-price' ),
		showDurationUnitInPriceRow                  = showDurationUnitInPrice.closest( 'tr.yith-plugin-fw-panel-wc-row' ),
		replaceDaysWithWeeks                        = $( '#yith-wcbk-replace-days-with-weeks-in-price' ),
		replaceDaysWithWeeksRow                     = replaceDaysWithWeeks.closest( 'tr.yith-plugin-fw-panel-wc-row' ),
		checkCostsIncludedInShowPriceDepsVisibility = function () {
			var checkedCosts      = costsIncludedInShownPrice.filter( ':checked' ),
				checkedCostValues = checkedCosts.map(
					function () {
						return $( this ).val();
					}
				).get();

			if ( checkedCostValues.includes( 'base-price' ) && !checkedCostValues.includes( 'extra-costs' ) && !checkedCostValues.includes( 'services' ) ) {
				showDurationUnitInPriceRow.show();
				if ( 'yes' === showDurationUnitInPrice.val() ) {
					replaceDaysWithWeeksRow.show();
				} else {
					replaceDaysWithWeeksRow.hide();
				}
			} else {
				showDurationUnitInPriceRow.hide();
				replaceDaysWithWeeksRow.hide();
			}
		};

	costsIncludedInShownPrice.on( 'change', function () {
		var checkedCosts = costsIncludedInShownPrice.filter( ':checked' );
		if ( !checkedCosts.length ) {
			$( this ).prop( "checked", true );
		}

		checkCostsIncludedInShowPriceDepsVisibility();
	} );

	showDurationUnitInPrice.on( 'change', checkCostsIncludedInShowPriceDepsVisibility );

	checkCostsIncludedInShowPriceDepsVisibility();

	/**
	 * Day-Month field
	 */
	var bkDayMonth = {
		init       : function () {
			$( document ).on( 'change', '.yith-wcbk-day-month__day, .yith-wcbk-day-month__month ', bkDayMonth.update );
		},
		update     : function ( event ) {
			var wrap  = $( event.target ).closest( '.yith-wcbk-day-month' ),
				input = wrap.find( '.yith-wcbk-day-month__value' ).first(),
				day   = wrap.find( '.yith-wcbk-day-month__day' ).val(),
				month = wrap.find( '.yith-wcbk-day-month__month' ).val(),
				value = bkDayMonth.formatValue( month, day );

			wrap.data( 'value', value );
			input.val( value ).trigger( 'change' );
		},
		formatValue: function ( month, day ) {
			if ( day < 10 ) {
				day = '0' + day;
			}

			if ( month < 10 ) {
				month = '0' + month;
			}

			return month + '-' + day;
		}
	};

	bkDayMonth.init();

	/**
	 * CPT Publish button
	 */
	if ( typeof adminpage !== 'undefined' && ['post-php', 'post-new-php'].indexOf( adminpage ) >= 0 ) {
		var publishButton = $( '#publish' );

		if ( publishButton.length ) {
			$( document ).on( 'click', '#yith-wcbk-cpt-save', function ( e ) {
				e.preventDefault();

				publishButton.trigger( 'click' );

				$( this ).block( {
									 message   : null,
									 overlayCSS: {
										 background: 'transparent',
										 opacity   : 0.6
									 }
								 } );
			} );
		}
	}

	// Disable WooCommerce check for changes
	$( function () {
		if ( wcbk_admin.disableWcCheckForChanges ) {
			$( 'input, textarea, select, checkbox' ).on( 'change', function () {
				window.onbeforeunload = '';
			} );
		}
	} );

} );