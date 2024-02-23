/* global yith, wcbk_admin, bk, ajaxurl, wp */
( function ( $ ) {
	var template    = wp.template( 'yith-wcbk-create-booking' ),
		blockParams = bk.blockParams;

	$( document ).on( 'click', '.yith-wcbk-create-booking', function ( e ) {
		e.preventDefault();
		var content = $( template( {} ) ),
			cancel  = content.find( '.yith-wcbk-create-booking__cancel' ),
			modal;

		modal = yith.ui.modal(
			{
				title                     : wcbk_admin.i18n.create_booking,
				content                   : content,
				classes                   : {
					wrap: 'yith-wcbk-create-booking-modal'
				},
				scrollContent             : false,
				width                     : 'auto',
				closeWhenClickingOnOverlay: true
			}
		);

		cancel.on( 'click', modal.close );

		$( document.body ).trigger( 'yith-framework-enhanced-select-init' );
		$( document.body ).trigger( 'wc-enhanced-select-init' );

		content.find( '#yith-wcbk-create-booking__assign-order' ).trigger( 'change' );
	} );

	$( document ).on( 'change', '#yith-wcbk-create-booking__product-id', function () {
		var wrapper            = $( this ).closest( '.yith-wcbk-create-booking__wrapper' ),
			bookingFormWrapper = wrapper.find( '.yith-wcbk-create-booking__booking-form' ),
			product_id         = $( this ).val(),
			post_data          = {
				product_id     : product_id,
				action         : 'yith_wcbk_get_product_booking_form',
				security       : wcbk_admin.nonces.get_booking_form,
				show_price     : true,
				bk_context     : 'create_booking',
				additional_data: {
					bk_page: 'create_booking'
				}
			};

		bookingFormWrapper.block( blockParams );

		$.ajax( {
					type    : "POST",
					data    : post_data,
					url     : ajaxurl,
					success : function ( response ) {
						bookingFormWrapper.html( response );
						$( document.body ).trigger( 'yith-wcbk-init-booking-form' );
						$( document ).trigger( 'yith-wcbk-init-fields:help-tip' );
					},
					complete: function () {
						bookingFormWrapper.unblock();
					}
				} );

	} );

	$( document ).on( 'change', '#yith-wcbk-create-booking__assign-order', function () {
		var assignOrder  = $( this ),
			optionsTable = assignOrder.closest( '.yith-wcbk-create-booking__options' ),
			orderIdRow   = optionsTable.find( '.yith-wcbk-create-booking__order-id__row' );

		if ( 'specific' === assignOrder.val() ) {
			orderIdRow.show();
		} else {
			orderIdRow.hide();
		}
	} );

} )( jQuery );