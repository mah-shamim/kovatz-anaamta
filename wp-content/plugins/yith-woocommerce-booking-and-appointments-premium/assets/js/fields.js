jQuery( function ( $ ) {
	"use strict";
	/**
	 * Select Alt
	 */
	var selectAltParams = {
		containerSelector: '.yith-wcbk-select-alt__container',
		openedClass      : 'yith-wcbk-select-alt__container--opened',
		unselectedClass  : 'yith-wcbk-select-alt__container--unselected',
		open             : function () {
			$( this ).closest( selectAltParams.containerSelector ).addClass( selectAltParams.openedClass );
		},
		close            : function () {
			$( this ).closest( selectAltParams.containerSelector ).removeClass( selectAltParams.openedClass );
		},
		blur             : function () {
			$( this ).trigger( 'blur' );
		}
	};

	$( document )
		.on( 'focusin', selectAltParams.containerSelector + ' select', selectAltParams.open )
		.on( 'focusout change', selectAltParams.containerSelector + ' select', selectAltParams.close )
		.on( 'change', selectAltParams.containerSelector + ' select', selectAltParams.blur );

	/**
	 * Tip tip
	 */
	$( document ).on( 'yith-wcbk-init-fields:help-tip', function () {
		$( '.yith-wcbk-help-tip:not(.yith-wcbk-help-tip--initialized)' ).each( function () {
			$( this ).tipTip( {
								  'attribute': 'data-tip',
								  'fadeIn'   : 50,
								  'fadeOut'  : 50,
								  'delay'    : 200
							  } );
			$( this ).addClass( 'yith-wcbk-help-tip--initialized' );
		} );
	} ).trigger( 'yith-wcbk-init-fields:help-tip' );
} );

