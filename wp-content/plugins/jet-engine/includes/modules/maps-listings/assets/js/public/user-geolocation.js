( function( $ ) {

	"use strict";

	const initGeolocationFilter = function() {

		window.JetSmartFilters.filtersList.JetEngineUserGeolocation = 'jet-smart-filters-user-geolocation';
		window.JetSmartFilters.filters.JetEngineUserGeolocation = class JetEngineUserGeolocation extends window.JetSmartFilters.filters.Search {

			name = 'user-geolocation';

			constructor( $container ) {
				
				const $filter = $container.find( '.geolocation-data' );
				
				super( $container, $filter );

				if ( navigator.geolocation ) {
					
					navigator.geolocation.getCurrentPosition( ( position ) => {
						
						this.dataValue = {
							latitude: position.coords.latitude,
							longitude: position.coords.longitude,
						}

						// To prevent adds to Active Filters.
						window.JetSmartFilters?.filterGroups?.[ this.provider + '/' + this.queryId ]?.activeItemsExceptions.push( this.name );

						this.emitFitersApply();

					} );
				}

			}

			processData() {
			}

			reset() {
				// Left empty to prevent reset when clicking the Remove filters button
			}

		};

	}

	if ( window.JetMapListingGeolocationFilterData && 'jet-smart-filters/before-init' === window.JetMapListingGeolocationFilterData.initEvent ) {
		document.addEventListener( 'jet-smart-filters/before-init', ( e ) => {
			initGeolocationFilter();
		});
	} else {
		window.addEventListener( 'DOMContentLoaded', ( e ) => {
			initGeolocationFilter();
		});
	}

}( jQuery ) );
