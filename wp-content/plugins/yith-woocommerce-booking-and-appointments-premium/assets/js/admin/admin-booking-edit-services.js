/* globals pagenow */
jQuery( function ( $ ) {
	"use strict";

	var form       = $( '#addtag' ),
		submit_btn = form.find( '#submit' );

	$( document ).on( 'yith_wcbk_service_taxonomy_form_reset', function () {
		var numbers  = form.find( '.form-field input[type=number]' ),
			checkbox = form.find( '.form-field input[type=checkbox]' );

		numbers.val( '' );
		checkbox.prop( 'checked', false );
	} );

	submit_btn.on( 'click', function () {
		$( document ).trigger( 'yith_wcbk_service_taxonomy_form_reset' );
	} );

	if ( 'edit-yith_booking_service' === pagenow ) {
		$( 'form#edittag table.form-table' ).addClass( 'yith-plugin-ui' );

		$( document ).on( 'click', '.yith-wcbk-booking-service-form-section-checkbox span.description', function () {
			console.log($( this ).siblings( 'input[type=checkbox]' ));
			$( this ).siblings( 'input[type=checkbox]' ).first().trigger( 'click' );
		} );
	}
} );
