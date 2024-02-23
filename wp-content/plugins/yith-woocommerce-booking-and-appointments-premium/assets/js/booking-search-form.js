jQuery( function ( $ ) {
	"use strict";

	var block_params = {
		message        : bk.loader_svg,
		blockMsgClass  : 'yith-wcbk-block-ui-element',
		css            : {
			border    : 'none',
			background: 'transparent'
		},
		overlayCSS     : {
			background: '#fff',
			opacity   : 0.7
		},
		ignoreIfBlocked: true
	};

	// Select 2.
	$( document ).on(
		'yith-wcbk-init-fields:select2',
		function () {
			$( '.yith-wcbk-select2:not(.yith-wcbk-select2--initialized)' ).each( function () {
				$( this ).select2( { width: '100%' } );
				$( this ).addClass( 'yith-wcbk-select2--initialized' );
			} );
		}
	).trigger( 'yith-wcbk-init-fields:select2' );

	/**
	 * Select2 - add class to stylize it with the new plugin-fw style
	 */
	$( document ).on( 'select2:open', function ( e ) {
		if ( $( e.target ).is( '.yith-wcbk-select2' ) ) {
			$( '.select2-results' ).closest( '.select2-container' ).addClass( 'yith-wcbk-select2-results-container' );
		}
	} );

	// Google Maps Autocomplete.
	$( document ).on(
		'yith-wcbk-init-fields:gm-places-autocomplete',
		function () {
			$( '.yith-wcbk-google-maps-places-autocomplete:not(.yith-wcbk-google-maps-places-autocomplete--initialized)' ).each( function () {
				$( this ).addClass( 'yith-wcbk-google-maps-places-autocomplete--initialized' );
				new google.maps.places.Autocomplete( this );
			} );
		}
	).trigger( 'yith-wcbk-init-fields:gm-places-autocomplete' );

	$( document ).on( 'submit', '.yith-wcbk-booking-search-form.show-results-popup form', function ( event ) {
		event.preventDefault();

		var form  = $( this ),
			popup = $.fn.yith_wcbk_popup( {
											  popup_class: 'yith-wcbk-booking-search-form-result yith-wcbk-popup woocommerce woocommerce-page',
											  ajax       : true,
											  ajax_data  : form.serialize(),
											  url        : bk.ajaxurl
										  } );

	} );

	$( document ).on( 'click', '.yith-wcbk-search-form-result-product-thumb-actions span', function ( event ) {
		var target        = $( event.target ),
			thumb_wrapper = target.closest( '.yith-wcbk-search-form-result-product-thumb-wrapper' ),
			images        = thumb_wrapper.find( '.yith-wcbk-thumb' ),
			images_count  = images.length,
			current       = thumb_wrapper.find( '.yith-wcbk-thumb.current' );

		if ( images_count > 1 ) {
			if ( current.length < 1 ) {
				current = images.first();
				current.addClass( 'current' );
			}

			var image_to_show;

			if ( target.is( '.yith-wcbk-search-form-result-product-thumb-action-next' ) ) {
				image_to_show = current.next( '.yith-wcbk-thumb' );

				if ( image_to_show.length < 1 ) {
					image_to_show = images.first();
				}
			} else {
				image_to_show = current.prev( '.yith-wcbk-thumb' );

				if ( image_to_show.length < 1 ) {
					image_to_show = images.last();
				}
			}

			current.removeClass( 'current' );
			image_to_show.addClass( 'current' );
		}
	} );

	$( document ).on( 'click', '.yith-wcbk-search-form-results-show-more', function ( event ) {
		var target          = $( event.target ),
			product_ids     = target.data( 'product-ids' ),
			page            = target.data( 'page' ),
			booking_request = target.data( 'booking-request' ),
			last_page       = target.data( 'last-page' ),
			next_page       = page + 1,
			results         = target.closest( '.yith-wcbk-booking-search-form-result' ).find( '.yith-wcbk-search-form-result-products' );

		target.block( block_params );
		$.ajax( {
					data   : {
						product_ids    : product_ids,
						page           : next_page,
						booking_request: booking_request,
						action         : 'yith_wcbk_search_booking_products_paged',
						security       : bk.nonces.searchBookingProductsPaged
					},
					url    : bk.ajaxurl,
					success: function ( data ) {
						results.append( data );
						if ( last_page <= next_page ) {
							target.remove();
						} else {
							target.data( 'page', next_page );
							target.unblock();
						}

					}
				} );
	} );

	/**
	 * Integration with YITH Ajax Product Filters
	 */
	$( document ).on(
		'yith-wcan-ajax-filtered',
		function () {
			$( document ).trigger( 'yith-wcbk-init-fields:select2' );
			$( document ).trigger( 'yith-wcbk-init-fields:gm-places-autocomplete' );

			// todo: add a specific trigger for initializing date-pickers.
			$( document ).trigger( 'yith-wcbk-init-booking-form' ); // for date-pickers.
		}
	);
} );