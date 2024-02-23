const JetMapboxMarkerClusterer = function( data ) {

	this.map = data.map;
	this.activeMarkers = {};

	this.setMarkers = function( markers ) {
		
		this.markers = markers;
		this.markersMap = {};

		this.sourceData = {
			type: 'FeatureCollection',
			features: [],
		};

		for ( var i = 0; i < this.markers.length; i++ ) {

			this.sourceData.features.push( {
				type: 'Feature',
				properties: {
					markerKey: '' + this.markers[i].getLngLat().lng + this.markers[i].getLngLat().lat
				},
				geometry: {
					type: 'Point',
					coordinates: [ this.markers[i].getLngLat().lng, this.markers[i].getLngLat().lat ]
				}
			} );

			this.markersMap[ '' + this.markers[i].getLngLat().lng + this.markers[i].getLngLat().lat ] = this.markers[ i ];

		}

	}

	this.setMapData = function() {
		
		this.clearMapData();

		this.map.addSource( 'markerClusters', {
			type: 'geojson',
			data: this.sourceData,
			cluster: true,
			clusterMaxZoom: data.clusterMaxZoom ? data.clusterMaxZoom : 14, // Max zoom to cluster points on
			clusterRadius: data.clusterRadius ? data.clusterRadius : 50 // Radius of each cluster when clustering points (defaults to 50)
		} );

		this.map.addLayer( {
			id: 'clusters',
			type: 'circle',
			source: 'markerClusters',
			filter: [ 'has', 'point_count' ],
			paint: {
				'circle-color': [
					'step',
					[ 'get', 'point_count' ],
					'#51bbd6',
					100,
					'#f1f075',
					750,
					'#f28cb1'
				],
				'circle-radius': [
					'step',
					['get', 'point_count'],
					20,
					100,
					30,
					750,
					40
				]
			}
		} );
	 
		this.map.addLayer( {
			id: 'cluster-count',
			type: 'symbol',
			source: 'markerClusters',
			filter: [ 'has', 'point_count' ],
			layout: {
				'text-field': '{point_count_abbreviated}',
				'text-font': [ 'DIN Offc Pro Medium', 'Arial Unicode MS Bold' ],
				'text-size': 12
			}
		} );

		this.map.addLayer({
			id: 'unclustered-point',
			type: 'circle',
			source: 'markerClusters',
			filter: ['!', ['has', 'point_count']],
			paint: {
				'circle-color': '#11b4da',
				'circle-radius': 1,
				'circle-opacity': 0,
				'circle-stroke-width': 0,
			}
		});

	}

	this.clearMapData = function() {
		
		try {
		
			if ( this.map.getLayer( 'clusters' ) ) {
				this.map.removeLayer( 'clusters' );
			}

			if ( this.map.getLayer( 'cluster-count' ) ) {
				this.map.removeLayer( 'cluster-count' );
			}

			if ( this.map.getLayer( 'unclustered-point' ) ) {
				this.map.removeLayer( 'unclustered-point' );
			}

			this.map.removeSource( 'markerClusters' );
		} catch ( e ) {
			this.error = e;
		}
	}

	this.getError = function() {
		return this.error;
	}

	this.removeMarkers = function() {
		
		this.markersMap = {};
		this.activeMarkers = {};

		this.clearMapData()
	}

	this.setMarkers( data.markers );

	this.map.on('load', () => {
		this.setMapData();
	});

	this.map.on( 'idle', () => {

		if ( ! this.markers.length ) {
			return;
		}
			
		const features = this.map.queryRenderedFeatures( null, {
			layers: [ 'unclustered-point' ],
		});

		const toShow = [];

		for ( var i = 0; i < features.length; i++ ) {
			
			let key = features[i].properties.markerKey;

			if ( 0 > toShow.indexOf( key ) ) {
				toShow.push( key );
			}
		}

		for ( markerKey in this.activeMarkers ) {
			if ( 0 > toShow.indexOf( markerKey ) ) {
				this.activeMarkers[ markerKey ].remove();
				delete this.activeMarkers[ markerKey ];
			}
		}

		for ( var i = 0; i < toShow.length; i++ ) {
			if ( this.markersMap[ toShow[ i ] ] && ! this.activeMarkers[ toShow[ i ] ] ) {
				this.markersMap[ toShow[ i ] ].addTo( this.map );
				this.activeMarkers[ toShow[ i ] ] = this.markersMap[ toShow[ i ] ];
			}
		}

	});
 
	// inspect a cluster on click
	this.map.on( 'click', 'clusters', ( e ) => {
		
		const features = this.map.queryRenderedFeatures( e.point, {
			layers: ['clusters']
		});

		const clusterId = features[0].properties.cluster_id;

		this.map.getSource( 'markerClusters' ).getClusterExpansionZoom(
			clusterId,
			( err, zoom ) => {
				if (err) return;
		 
				this.map.easeTo({
					center: features[0].geometry.coordinates,
					zoom: zoom
				});
			}
		);
	});
 
	this.map.on( 'mouseenter', 'clusters', () => {
		this.map.getCanvas().style.cursor = 'pointer';
	});

	this.map.on( 'mouseleave', 'clusters', () => {
		this.map.getCanvas().style.cursor = '';
	});

}