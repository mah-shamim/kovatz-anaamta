const JetMapboxPopup = function( data ) {
	
	this.popup = data.popup;
	this.popupContent = null;
	this.map = data.map || null;

	this.contentIsSet = function() {
		return null !== this.popupContent;
	}

	this.close = function() {
		// runs automatically
		return;
	}

	this.setMap = function( map ) {
		this.map = map;
	}

	this.draw = function() {
		// runs automatically
		return;
	}

	this.open = function( map, marker ) {
		// runs automatically
		return;
	}

	this.setContent = function( content ) {
		this.popupContent = content;
		this.popup.setHTML( content );
	}

	return this;

};

window.JetEngineMapsProvider = function() {

	this._activePopup = null;

	this.getId = function() {
		return 'mapbox';
	}

	this.initMap = function( container, settings ) {

		settings = settings || {};

		let settingsMap = {
			zoom: 'zoom',
			center: 'center',
			cooperativeGestures: 'scrollwheel',
			zoomControl: 'zoomControl',
			style: 'styles',
			maxZoom: 'maxZoom',
			minZoom: 'minZoom',
		};
		
		let parsedSettings = {}

		for ( const [ mKey, settingsKey ] of Object.entries( settingsMap ) ) {
			if ( undefined !== settings[ settingsKey ] ) {
				parsedSettings[ mKey ] = settings[ settingsKey ];
			}
		}

		if ( parsedSettings.center ) {
			parsedSettings.center = { lon: parsedSettings.center.lng, lat: parsedSettings.center.lat };
		}

		if ( container.id ) {
			parsedSettings.container = container.id;
		} else {
			parsedSettings.container = container;
		}
		

		if ( ! parsedSettings.style ) {
			parsedSettings.style = 'mapbox://styles/mapbox/streets-v11';
		}

		mapboxgl.accessToken = window.JetEngineMapboxData.token;

		const map = new mapboxgl.Map( parsedSettings );

		return map;
	}

	this.initBounds = function() {
		const bounds = new mapboxgl.LngLatBounds();
		return bounds;
	}

	this.getMarkerPosition = function( marker, toJSON ) {
		return marker.getLngLat();
	}

	this.fitMapBounds = function( data ) {
		data.map.fitBounds( data.bounds, {
			padding: { top: 20, bottom: 20, left: 20, right: 20 },
			duration: 0
		} );
	}

	this.addMarker = function( data ) {

		const el = document.createElement('div');
		
		el.className   = 'jet-map-marker';
		el.offsetWidth = 32;
		el.innerHTML   = data.content;

		var marker = new mapboxgl.Marker( el ).setLngLat( [ data.position.lng, data.position.lat ] );

		if ( ! data.markerClustering ) {
			marker.addTo( data.map );
		}
		
		return marker;
	}

	this.removeMarker = function( marker ) {
		marker.remove();
	}

	this.markerOnClick = function( map, data, callback ) {

		data = data || {};

		data.map    = map;
		data.shadow = false;

		map.on( "click", ( event ) => {

			data.position = {
				lat: event.lngLat.lat,
				lng: event.lngLat.lng,
			};

			if ( callback ) {
				callback( this.addMarker( data ) );
			}

		} );

	}

	this.closePopup = function( infoBox, callback ) {
		infoBox.popup.on( 'close', () => {
			callback();
		} );
	}

	this.openPopup = function( trigger, callback, infobox, map, openOn ) {

		infobox.popup.on( 'open', () => {
			callback();
			this._activePopup = infobox.popup;
		} );

		trigger.setPopup( infobox.popup );

		if ( 'hover' === openOn ) {
			const markerDiv = trigger.getElement();

			markerDiv.addEventListener( 'mouseenter', () => {
				this.triggerOpenPopup( trigger );
			} );
		}
	}

	this.triggerOpenPopup = function( trigger ) {

		if ( ! trigger._map ) {
			return;
		}

		// Close active popup.
		if ( this._activePopup ) {
			this._activePopup.remove();
		}

		if ( ! trigger.getPopup().isOpen() ) {
			trigger.togglePopup();
		}
	}

	this.getMarkerCluster = function( data ) {
		return new JetMapboxMarkerClusterer( data );
	}

	this.addMarkers = function( markerCluster, markers ) {
		markerCluster.setMarkers( markers );
		markerCluster.setMapData();
	}

	this.removeMarkers = function( markerCluster, markers ) {
		markerCluster.removeMarkers();
	}

	this.setCenterByPosition = function( data ) {
		data.map.jumpTo( {
			center: data.position,
			zoom:   data.zoom,
		} );
	}

	this.getMapZoom = function( map ) {
		return map.getZoom();
	}

	this.setAutoCenter = function( data ) {
		this.fitMapBounds( data );
	}

	this.addPopup = function( data ) {

		const popup = new mapboxgl.Popup( {
			maxWidth: data.width + 'px',
			minWidth: data.width + 'px',
			offset: data.offset,
			focusAfterOpen: true,
			className: 'jet-map-box',
		} );

		return new JetMapboxPopup( {
			popup: popup
		} );

	}

	this.getMarkerMap = function( marker ) {
		return marker._map;
	}

	this.fitMapToMarker = function( marker, markersClusterer, zoom ) {

		const map = markersClusterer.map;

		if ( ! zoom ) {
			zoom = 10;
		}

		const idleHandler = () => {

			if ( ! marker._map ) {
				zoom++;
				this.fitMapToMarker( marker, markersClusterer, zoom );
			} else {
				this.triggerOpenPopup( marker );
			}

			map.off( 'idle', idleHandler );
		}

		// Close active popup.
		if ( this._activePopup ) {
			this._activePopup.remove();
		}

		this.panTo( {
			map: map,
			position: this.getMarkerPosition( marker ),
			zoom: zoom
		} )

		map.on( 'idle', idleHandler );
	}

	this.panTo = function( data ) {
		data.map.flyTo( {
			center: data.position,
			zoom:   ( data.zoom && data.zoom > data.map.getZoom() ) ? data.zoom : data.map.getZoom(),
		} );
	}

}
