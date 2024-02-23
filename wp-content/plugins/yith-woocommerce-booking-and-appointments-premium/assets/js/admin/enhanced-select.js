/* global yith_wcbk_enhanced_select_params */

jQuery( function ( $ ) {
    "use strict";

    // Order Search
    $( '.yith-wcbk-order-search' ).filter( ':not(.enhanced)' ).each( function () {
        var select2_args = {
            allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
            placeholder       : $( this ).data( 'placeholder' ),
            minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
            escapeMarkup      : function ( m ) {
                return m;
            },
            ajax              : {
                url           : ajaxurl,
                dataType      : 'json',
                quietMillis   : 250,
                data          : function ( params ) {
                    return {
                        term    : params.term,
                        action  : $( this ).data( 'action' ) || 'yith_wcbk_json_search_order',
                        security: yith_wcbk_enhanced_select_params.search_orders_nonce,
                        exclude : $( this ).data( 'exclude' ),
                        include : $( this ).data( 'include' ),
                        limit   : $( this ).data( 'limit' ),

                    };
                },
                processResults: function ( data ) {
                    var terms = [];
                    if ( data ) {
                        $.each( data, function ( id, text ) {
                            terms.push( { id: id, text: text } );
                        } );
                    }
                    return {
                        results: terms
                    };
                },
                cache         : true
            }
        };

        $( this ).select2( select2_args ).addClass( 'enhanced' );

        if ( $( this ).data( 'sortable' ) ) {
            var $select = $( this );
            var $list   = $( this ).next( '.select2-container' ).find( 'ul.select2-selection__rendered' );

            $list.sortable( {
                                placeholder         : 'ui-state-highlight select2-selection__choice',
                                forcePlaceholderSize: true,
                                items               : 'li:not(.select2-search__field)',
                                tolerance           : 'pointer',
                                stop                : function () {
                                    $( $list.find( '.select2-selection__choice' ).get().reverse() ).each( function () {
                                        var id     = $( this ).data( 'data' ).id;
                                        var option = $select.find( 'option[value="' + id + '"]' )[ 0 ];
                                        $select.prepend( option );
                                    } );
                                }
                            } );
        }
    } );


    // Booking Product Search
    $( '.yith-booking-product-search' ).filter( ':not(.enhanced)' ).each( function () {
        var select2_args = {
            allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
            placeholder       : $( this ).data( 'placeholder' ),
            minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
            escapeMarkup      : function ( m ) {
                return m;
            },
            ajax              : {
                url           : ajaxurl,
                dataType      : 'json',
                quietMillis   : 250,
                data          : function ( params ) {
                    return {
                        term    : params.term,
                        action  : $( this ).data( 'action' ) || 'yith_wcbk_json_search_booking_products',
                        security: yith_wcbk_enhanced_select_params.search_bookings_nonce,
                        exclude : $( this ).data( 'exclude' ),
                        include : $( this ).data( 'include' ),
                        limit   : $( this ).data( 'limit' ),

                    };
                },
                processResults: function ( data ) {
                    var terms = [];
                    if ( data ) {
                        $.each( data, function ( id, text ) {
                            terms.push( { id: id, text: text } );
                        } );
                    }
                    return {
                        results: terms
                    };
                },
                cache         : true
            }
        };

        $( this ).select2( select2_args ).addClass( 'enhanced' );

        if ( $( this ).data( 'sortable' ) ) {
            var $select = $( this );
            var $list   = $( this ).next( '.select2-container' ).find( 'ul.select2-selection__rendered' );

            $list.sortable( {
                                placeholder         : 'ui-state-highlight select2-selection__choice',
                                forcePlaceholderSize: true,
                                items               : 'li:not(.select2-search__field)',
                                tolerance           : 'pointer',
                                stop                : function () {
                                    $( $list.find( '.select2-selection__choice' ).get().reverse() ).each( function () {
                                        var id     = $( this ).data( 'data' ).id;
                                        var option = $select.find( 'option[value="' + id + '"]' )[ 0 ];
                                        $select.prepend( option );
                                    } );
                                }
                            } );
        }
    } );
} );