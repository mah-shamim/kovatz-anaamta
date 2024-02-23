(function( $, JetMapFieldsSettings ) {

	'use strict';

	class JetEngineMapFields {

		constructor() {
			this.mapProvider = new window.JetEngineMapsProvider();

			this.events();
		}

		events() {
			const self = this;

			self.initFields( $( '.cx-control' ) );

			$( document ).on( 'cx-control-init', function ( event, data ) {
				if ( data?.target ) {
					self.initFields( $( data.target ) );
				}
			} );
		}

		initFields( $scope ) {
			const self = this;

			$( '.jet-engine-map-field.cx-ui-container', $scope ).each( function() {

				const $this    = $( this );
				const observer = new IntersectionObserver(
					function( entries, observer ) {

						entries.forEach( function( entry ) {
							if ( entry.isIntersecting ) {
								new JetEngineRenderMapField( $this, self.mapProvider );

								// Detach observer after the first render the map
								observer.unobserve( entry.target );
							}
						} );
					}
				);

				observer.observe( $this[0] );

			} );
		}
	}

	class JetEngineRenderMapField {

		constructor( selector, mapProvider ) {

			this.setup( selector, mapProvider );

			this.render();
			this.events();
		}

		setup( selector, mapProvider ) {
			this.$container = selector;
			this.$input = selector.find( 'input[name]' );
			this.value = this.$input.val();
			this.isRepeaterField = !! this.$input.closest( '.cx-ui-repeater-item-control' ).length;
			this.fieldSettings = Object.assign( {
				height: '300',
				format: 'location_string',
				field_prefix: false,
			}, this.$input.data( 'settings' ) );

			const field_suffix = ! this.isRepeaterField ? '' : '-' + this.$input.closest( '.cx-ui-repeater-item' ).data( 'item-index' );

			this.$inputHash = this.fieldSettings.field_prefix ? $( '#' + this.fieldSettings.field_prefix + '_hash' + field_suffix ) : false;
			this.$inputLat  = this.fieldSettings.field_prefix ? $( '#' + this.fieldSettings.field_prefix + '_lat' + field_suffix ) : false;
			this.$inputLng  = this.fieldSettings.field_prefix ? $( '#' + this.fieldSettings.field_prefix + '_lng' + field_suffix ) : false;

			// Map props.
			this.mapProvider = mapProvider;
			this.map = null;
			this.mapDefaults = {
				center: { lat: 41, lng: 71 },
				zoom: 1,
			};
			this.marker = null;
			this.markerDefaults = {
				content: '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24"><path d="M12 0c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z" fill="#C92C2C"/></svg>',
				shadow: false,
			};
		}

		render() {
			const fieldTemplate = wp.template( 'jet-engine-map-field' );
			const templateData = {
				height:      this.fieldSettings.height,
				fieldPrefix: this.fieldSettings.field_prefix,
				isRepeater:  this.isRepeaterField
			};

			this.$container.append( fieldTemplate( templateData ) );

			this.$preview      = this.$container.find( '.jet-engine-map-field__preview' );
			this.$position     = this.$container.find( '.jet-engine-map-field__position' );
			this.$search       = this.$container.find( '.jet-engine-map-field__search' );
			this.$searchInput  = this.$container.find( '.jet-engine-map-field__search input' );
			this.$searchLoader = this.$container.find( '.jet-engine-map-field__search-loader' );
			this.$searchList   = this.$container.find( '.jet-engine-map-field__search-list' );
			this.$mapFrame     = this.$container.find( '.jet-engine-map-field__frame' );

			let defaultPos,
				valueFormat = false;

			if ( this.value ) {
				// Set preview from input value.
				try {
					// `location_array` value format
					const jsonValue = JSON.parse( this.value );

					defaultPos = jsonValue;
					this.setPreview( jsonValue );

					valueFormat = 'location_array';

				} catch (e) {

					const valueParts = this.value.split( ',' );

					if ( 2 === valueParts.length && Number( valueParts[0] ) && Number( valueParts[1] ) ) {
						// `location_string` value format
						defaultPos = { lat: Number( valueParts[0] ), lng: Number( valueParts[1] ) };
						this.setPreview( defaultPos );

						valueFormat = 'location_string';

					} else {
						// `location_address` value format
						defaultPos = this.getPositionFromHashFields();
						this.setPreview( this.value );

						valueFormat = 'location_address';
					}
				}

				// Convert value format
				if ( valueFormat !== this.fieldSettings.format ) {
					this.setValue( defaultPos );
				}
			}

			if ( defaultPos ) {
				this.mapDefaults.center = defaultPos;
				this.mapDefaults.zoom = 14;
			}

			this.map = this.mapProvider.initMap( this.$mapFrame[0], this.mapDefaults );

			if ( defaultPos ) {
				this.marker = this.mapProvider.addMarker( Object.assign( this.markerDefaults, {
					position: defaultPos,
					map: this.map,
				} ) );
			}

			this.mapProvider.markerOnClick( this.map, this.markerDefaults, ( marker ) => {

				if ( this.marker ) {
					this.mapProvider.removeMarker( this.marker );
				}

				this.marker = marker;

				let position = this.mapProvider.getMarkerPosition( marker, true );

				this.setValue( position );

				this.$searchInput.val( null );
			} );
		}

		setValue( position ) {
			const self = this;
			let location = '';

			this.setPreview( JetMapFieldsSettings.i18n.loading );

			switch ( this.fieldSettings.format ) {
				case 'location_string':

					location = position.lat + ',' + position.lng;

					this.updateHashFieldPromise( location ).then( function() {
						self.$input.val( location ).trigger( 'change' );
						self.setPreview( position );
					} );

					break;

				case 'location_array':

					location = JSON.stringify( position );

					this.updateHashFieldPromise( location ).then( function() {
						self.$input.val( location ).trigger( 'change' );
						self.setPreview( position );
					} );

					break;

				case 'location_address':

					wp.apiFetch( {
						method: 'get',
						path: JetMapFieldsSettings.api + '?lat=' + position.lat + '&lng=' + position.lng,
					} ).then( function( response ) {

						if ( response.success ) {

							if ( response.data ) {

								self.updateHashFieldPromise( response.data ).then( function() {
									self.$input.val( response.data ).trigger( 'change' );
									self.setPreview( response.data );
								} );

							} else {
								self.$input.val( null ).trigger( 'change' );
								self.setPreview( JetMapFieldsSettings.i18n.notFound );
							}

						} else {
							self.$input.val( null ).trigger( 'change' );
							self.setPreview( response.html );
						}

					} ).catch( function( e ) {
						console.log( e );
					} );

					break;
			}

			if ( this.$inputLat && this.$inputLng  ) {
				this.$inputLat.val( position.lat );
				this.$inputLng.val( position.lng );
			}
		}

		setPreview( position ) {
			let positionText;

			if ( position && position.lat && position.lng ) {
				positionText = '<span title="Lat">' + position.lat + '</span>, <span title="Lng">' + position.lng + '</span>';
			} else {
				positionText = position;
			}

			this.$position.html( positionText );

			if ( position ) {
				this.$preview.addClass( 'show' );
			} else {
				this.$preview.removeClass( 'show' );
			}
		}

		events() {
			this.$container.on( 'click', '.jet-engine-map-field__reset', this.resetLocation.bind( this ) );
			this.$input.on( 'change', this.changeInputHandler.bind( this ) );

			this.$searchInput.on( 'input',    this.inputSearchHandler.bind( this ) );
			this.$searchInput.on( 'focus',    this.focusSearchHandler.bind( this ) );
			this.$searchInput.on( 'keypress', this.keypressSearchHandler.bind( this ) );

			this.$searchList.on( 'click', '.jet-engine-map-field__search-item', this.searchItemClickHandler.bind( this ) );

			// Hide list on click outside.
			this.$search.on( 'click',   this.clickSearchHandler );
			this.$search.on( 'touchend', this.clickSearchHandler );

			$( document ).on( 'click',    this.hideSearchList.bind( this ) );
			$( document ).on( 'touchend', this.hideSearchList.bind( this ) );

		}

		resetLocation() {
			this.mapProvider.removeMarker( this.marker );
			this.setPreview( null );
			this.$input.val( null ).trigger( 'change' );

			if ( this.$inputLat && this.$inputLng  ) {
				this.$inputLat.val( null );
				this.$inputLng.val( null );
			}

			this.$searchInput.val( null );
		}

		changeInputHandler( event ) {
			const $this = $( event.target );

			$( window ).trigger( {
				type: 'cx-control-change',
				controlName: $this.attr( 'name' ),
				controlStatus: $this.val(),
			} );
		}

		updateHashFieldPromise( location ) {
			const self = this;

			if ( ! this.$inputHash ) {
				return new Promise( function( resolve ) {
					resolve();
				} );
			}

			return wp.apiFetch( {
				method: 'get',
				path: JetMapFieldsSettings.apiHash + '?loc=' + location,
			} ).then( function( response ) {

				if ( response.success ) {
					self.$inputHash.val( response.data );
				}

			} ).catch( function( e ) {
				console.log( e );
			} );
		}

		getPositionFromHashFields() {

			if ( !this.$inputLat || !this.$inputLng  ) {
				return false;
			}

			const lat = this.$inputLat.val(),
				  lng = this.$inputLng.val();

			if ( !lat || !lng ) {
				return false;
			}

			return { lat: Number( lat ), lng: Number( lng ) };
		}

		geocodeSearch() {
			const self = this;
			const value = this.$searchInput.val();

			wp.apiFetch( {
				method: 'get',
				path: JetMapFieldsSettings.apiLocation + '?address=' + value,
			} ).then( function( response ) {

				if ( response.success ) {
					self.addMarkerByPosition( response.data );
				} else {
					window.alert( response.html );
				}

			} ).catch( function( e ) {
				console.log( e );
			} );
		}

		addMarkerByPosition( position ) {

			// Maybe convert string coordinates to numeric coordinates.
			Object.keys( position ).forEach( function( key, index ) {
				position[key] = Number( position[key] );
			} );

			if ( this.marker ) {
				this.mapProvider.removeMarker( this.marker );
			}

			this.marker = this.mapProvider.addMarker( Object.assign( this.markerDefaults, {
				position: position,
				map: this.map,
			} ) );

			this.setValue( position );

			this.mapProvider.setCenterByPosition( {
				position: position,
				map: this.map,
				zoom: 12,
			} );

		}

		inputSearchHandler( event ) {
			const self = this;
			const value = $( event.target ).val().trim();

			if ( this.currentSearchQuery && this.currentSearchQuery === value ) {
				return false;
			}

			if ( this.searchController ) {
				this.searchController.abort();
			}

			if ( 2 > value.length ) {
				this.hideSearchLoader();
				this.hideSearchList();
				return false;
			}

			this.searchController = new AbortController();

			this.showSearchLoader();

			this.currentSearchQuery = value;

			wp.apiFetch( {
				method: 'get',
				path: JetMapFieldsSettings.apiAutocomplete + '?query=' + value,
				signal: this.searchController.signal,
			} ).then( function( response ) {

				let itemsHtml = '';

				if ( response.success ) {

					for ( let item in response.data ) {
						let attrs = '';

						if ( response.data[item].lat && response.data[item].lng ) {
							attrs += ' data-lat="' + response.data[item].lat + '"';
							attrs += ' data-lng="' + response.data[item].lng + '"';
						}

						itemsHtml += '<li class="jet-engine-map-field__search-item"' + attrs + '>' + response.data[item].address + '</li>';
					}

				} else {
					itemsHtml = '<li class="jet-engine-map-field__search-no-results">' + response.html + '</li>';
				}

				self.hideSearchLoader();
				self.showSearchList();

				self.$searchList.html( itemsHtml );

			} ).catch( function( e ) {
				console.log( e );
			} );
		}

		keypressSearchHandler( event ) {

			if ( 13 !== event.keyCode ) {
				return;
			}

			this.hideSearchList();

			this.geocodeSearch();
		}

		searchItemClickHandler( event ) {

			const $searchItem = $( event.target );

			this.$searchInput.val( $searchItem.text() );

			this.hideSearchList();

			if ( $searchItem.data( 'lat' ) && $searchItem.data( 'lng' ) ) {

				this.addMarkerByPosition( {
					lat: $searchItem.data( 'lat' ),
					lng: $searchItem.data( 'lng' ),
				} );

			} else {
				this.geocodeSearch();
			}
		}

		focusSearchHandler( event ) {
			const value = $( event.target ).val();

			if ( 2 > value.length ) {
				return;
			}

			this.showSearchList();
		}

		clickSearchHandler( event ) {
			event.stopPropagation();
		}

		showSearchList() {
			this.$searchList.addClass( 'show' );
		}

		hideSearchList() {
			this.$searchList.removeClass( 'show' );
		}

		showSearchLoader() {
			this.$searchLoader.addClass( 'show' );
		}

		hideSearchLoader() {
			this.$searchLoader.removeClass( 'show' );
		}

	}

	// Run on document ready.
	$( function () {
		new JetEngineMapFields();
	} );


})( jQuery, window.JetMapFieldsSettings );