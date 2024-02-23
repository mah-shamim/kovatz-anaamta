jQuery( function ( $ ) {
	"use strict";

	var widget         = $( '.yith_wcbk_booking_product_form_widget.yith_wcbk_booking_product_form_widget--mobile-fixed' ),
		mouseTrap      = $( '.yith_wcbk_widget_booking_form_mouse_trap' ),
		closeBtn       = $( '.yith_wcbk_widget_booking_form_close' ),
		body           = $( 'body' ),
		isInFooter     = false,
		friends        = {
			parent: widget.parent(),
			next  : widget.next(),
			prev  : widget.prev()
		},
		open           = function () {
			show_overlay();
			widget.addClass( 'yith_wcbk_booking_product_form_widget__opened' );
		},
		close          = function () {
			widget.removeClass( 'yith_wcbk_booking_product_form_widget__opened' );
			hide_overlay();
		},
		show_overlay   = function () {
			var overlay = $( '.yith_wcbk_widget_booking_form_overlay' );
			if ( overlay.length < 1 ) {
				overlay = $( '<div class="yith_wcbk_widget_booking_form_overlay"></div>' );
				$( 'body' ).append( overlay );
			}

			overlay.show();
		},
		hide_overlay   = function () {
			$( '.yith_wcbk_widget_booking_form_overlay' ).hide();
		},
		handlePosition = function () {
			if ( 'fixed' === widget.css( 'position' ) ) {
				if ( !isInFooter ) {
					isInFooter = true;
					body.append( widget );
				}
			} else {
				if ( isInFooter ) {
					isInFooter = false;
					if ( friends.prev.length ) {
						friends.prev.after( widget );
					} else if ( friends.next.length ) {
						friends.next.before( widget );
					} else {
						friends.parent.append( widget );
					}
				}
			}
		};

	if ( widget.length > 0 ) {
		$( document ).on( 'click', '.yith_wcbk_widget_booking_form_overlay', close );

		mouseTrap.on( 'click', open );
		closeBtn.on( 'click', close );

		/**
		 * Move the widget to the footer in mobile to avoid issues with theme and z-index.
		 * This can be disabled through 'yith_wcbk_product_form_widget_mobile_move_to_footer' filter.
		 *
		 * @since 3.0.1
		 */
		if ( widget.hasClass( 'yith_wcbk_booking_product_form_widget--mobile-move-to-footer' ) ) {
			handlePosition();
			$( window ).on( 'resize', handlePosition );
		}
	}

} );
