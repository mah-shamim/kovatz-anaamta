( function( $ ) {

	"use strict";

	const initLocationDistanceFilter = function() {


		window.JetSmartFilters.filtersList.JetEngineLocationDistance = 'jet-smart-filters-location-distance';
		window.JetSmartFilters.filters.JetEngineLocationDistance = class JetEngineLocationDistance extends window.JetSmartFilters.filters.Search {

			name = 'location-distance';
			currentLocationVerbose = 'Your current location';
			defaultLocation = null;
			$locationParent = null;
			$locationInput = null;
			$locationDropdown = null;
			$distanceInput = null;

			constructor( $container ) {
				
				const $filter = $container.find( '.jsf-location-distance' );

				super( $container, $filter );

				this.locationData = {};

				this.$locationParent = $filter.find( '.jsf-location-distance__location' );
				this.$locationInput = $filter.find( '.jsf-location-distance__location-input' );
				this.$locationDropdown = $filter.find( '.jsf-location-distance__location-dropdown' );
				this.$distanceInput = $filter.find( '.jsf-location-distance__distance' );

				if ( $filter.data( 'geolocation-placeholder' ) ) {
					this.currentLocationVerbose = $filter.data( 'geolocation-placeholder' );
				}

				this.$distanceInput.on( 'change', ( event ) => {
					this.updateLocationData( { distance: event.target.value } );
				} );

				this.defaultLocation = $filter.data( 'current-location' );

				if ( this.defaultLocation.address ) {
					this.$locationInput.val( this.defaultLocation.address );
				}

				if ( this.defaultLocation.distance ) {
					this.selectDistance( this.defaultLocation.distance );
				}

				this.$locationInput.on( 'keyup', ( event ) => {

					var keyCode = ( event.keyCode ? event.keyCode : event.which );

					if ( event.target.value.length ) {
						this.switchClear( true );
						this.switchLocate();
					} else {
						this.switchClear();
						this.switchLocate( true );
					}

					if ( 3 > event.target.value.length ) {
						this.clearDropdown();
						return;
					}

					this.updateLocationData( {
						address: event.target.value,
					}, true );

					if ( 13 === keyCode ) {
						this.maybeApplyFilter();
						this.clearDropdown();
						return;
					}

					$.ajax({
						url: window.JetMapListingLocationDistanceFilterData.apiAutocomplete,
						type: 'GET',
						dataType: 'json',
						data: { query: event.target.value },
					}).done( ( response ) => {

						this.clearDropdown();

						if ( ! response || ! response.success ) {
							return;
						}

						this.$locationDropdown.addClass( 'is-active' );

						for ( var i = 0; i < response.data.length; i++ ) {
							this.$locationDropdown.append( 
								'<div class="jsf-location-distance__location-dropdown-item" data-address="' + response.data[ i ].address + '">'
								+ response.data[ i ].address + 
								'</div>'
							);
						}

					});
					
				} );

				this.$locationDropdown.on( 'click', '.jsf-location-distance__location-dropdown-item', ( event ) => {

					const $dropdownItem = $( event.target );

					this.$locationInput.val( $dropdownItem.data( 'address' ) );
					this.clearDropdown();

					this.updateLocationData( {
						address: $dropdownItem.data( 'address' ),
						latitude: 0,
						longitude: 0,
					} );

				} );

				this.$locationParent.on( 'click', '.jsf-location-distance__location-clear', ( event ) => {
					//this.clearLocation();
					this.reset();
				} );

				this.$locationParent.on( 'click', '.jsf-location-distance__location-locate', ( event ) => {

					this.switchLocate();
					this.switchLoading( true );

					navigator.geolocation.getCurrentPosition( ( position ) => {

						this.switchLoading();
						this.$locationInput.val( this.currentLocationVerbose );
						this.switchClear( true );

						this.updateLocationData( {
							address: 0,
							latitude: position.coords.latitude,
							longitude: position.coords.longitude,
						} );

					} );

				} );

				$( document ).on( 'click', ( event ) => {

					const $parent = $( event.target ).closest( '.jsf-location-distance__location' );

					if ( ! $parent.length ) {
						this.clearDropdown();
					}

				} );

			}

			maybeApplyFilter() {

				const applyOn = [ 'ajax', 'mixed' ];

				if ( ! applyOn.includes( this.applyType ) ) {
					return;
				}

				this.emitFiterApply();

			}

			reset() {
				this.updateLocationData( {}, true, [ 'distance' ] );
				this.$distanceInput.find( 'option:selected' ).removeAttr( 'selected' );
				this.clearLocation();
			}

			setData( newData ) {

				if ( newData ) {
					this.updateLocationData( { ...newData }, true );
				}

				if ( newData && newData.address ) {

					if ( 0 == newData.address && newData.latitude && newData.longitude ) {
						this.$locationInput.val( this.currentLocationVerbose );
					} else {
						this.$locationInput.val( newData.address );
					}

					this.switchClear( true );
					this.switchLocate();

				}

				if ( newData && newData.distance ) {
					this.selectDistance( newData.distance );
				}

			}

			selectDistance( distance ) {
				this.$distanceInput.find( 'option[value="' + distance + '"]' ).attr( 'selected', true );
			}

			get activeValue() {

				if ( ! this.hasLocation() && this.defaultLocation.distance == this.locationData.distance ) {
					return false;
				}
				
				const activeValue = [];

				if ( this.locationData.address && 0 != this.locationData.address ) {
					activeValue.push( this.locationData.address );
				} else if ( this.locationData.latitude && this.locationData.longitude ) {
					activeValue.push( this.currentLocationVerbose );
				}

				activeValue.push( this.locationData.distance + this.locationData.units );

				return activeValue.join( ', ' );

			}

			locationDataIsEmpty() {
				
				if ( ! this.locationData || 0 === Object.keys( this.locationData ).length ) {
					return true;
				}

				if ( ! this.hasLocation() && ! this.locationData.distance ) {
					return true;
				}

				return false;
			}

			// Getters
			get data() {

				if ( this.dataValue && ! this.disabled ) {
					return { ...this.dataValue };
				} else if ( ! this.locationDataIsEmpty() && ! this.disabled ) {
					return { ...this.locationData };
				} else {
					//console.log( 'Empty' );
					return false;
				}

			}

			processData() {
				if ( this.hasLocation() ) {
					this.dataValue = { ...this.locationData };
				} else {
					this.dataValue = false;
				}
			}

			clearLocation() {

				this.$locationInput.val( '' );
				this.clearDropdown();

				this.switchClear();
				this.switchLocate( true );

				this.updateLocationData( {}, false, [ 'address', 'longitude', 'latitude' ] );

			}

			hasLocation() {
				if (
					! this.locationData
					|| ( ! this.locationData.address
					&& ! this.locationData.latitude
					&& ! this.locationData.longitude )
				) {
					return false;
				} else {
					return true;
				}
			}

			clearDropdown() {
				this.$locationDropdown.html( '' ).removeClass( 'is-active' );
			}

			switchClear( show ) {
				if ( show && ! this.$locationParent.hasClass( 'jsf-show-clear' ) ) {
					this.$locationParent.addClass( 'jsf-show-clear' );
				} else if ( ! show ) {
					this.$locationParent.removeClass( 'jsf-show-clear' );
				}
			}

			switchLoading( show ) {
				if ( show && ! this.$locationParent.hasClass( 'jsf-show-loading' ) ) {
					this.$locationParent.addClass( 'jsf-show-loading' );
				} else if ( ! show ) {
					this.$locationParent.removeClass( 'jsf-show-loading' );
				}
			}

			switchLocate( show ) {

				if ( ! navigator.geolocation ) {
					this.$locationParent.removeClass( 'jsf-show-locate' );
					return;
				}

				if ( show && ! this.$locationParent.hasClass( 'jsf-show-locate' ) ) {
					this.$locationParent.addClass( 'jsf-show-locate' );
				} else if ( ! show ) {
					this.$locationParent.removeClass( 'jsf-show-locate' );
				}
			}

			updateLocationData( newData, silent, deleteData ) {

				if ( 0 !== Object.keys( newData ).length ) {

					if ( 0 !== Object.keys( this.defaultLocation ).length ) {
						newData = { ...this.defaultLocation, ...newData };
					}

					for ( const prop in newData ) {
						this.locationData[ prop ] = newData[ prop ];
					}

					if ( ! this.locationData.distance ) {
						this.locationData.distance = this.$distanceInput.data( 'default' );
					}

					if ( ! this.locationData.units ) {
						this.locationData.units = this.$distanceInput.data( 'units' );
					}

				}

				if ( deleteData ) {
					for ( var i = 0; i < deleteData.length; i++ ) {
						delete this.locationData[ deleteData[ i ] ];
					}
				}

				if ( ! silent ) {
					this.maybeApplyFilter();
				}

			}

		};
	}

	if ( window.JetMapListingGeolocationFilterData && 'jet-smart-filters/before-init' === window.JetMapListingGeolocationFilterData.initEvent ) {
		document.addEventListener( 'jet-smart-filters/before-init', ( e ) => {
			initLocationDistanceFilter();
		});
	} else {
		window.addEventListener( 'DOMContentLoaded', ( e ) => {
			initLocationDistanceFilter();
		});
	}

}( jQuery ) );
