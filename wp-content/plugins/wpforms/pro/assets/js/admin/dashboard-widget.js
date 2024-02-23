/* globals wpforms_dashboard_widget, ajaxurl, moment, Chart */
/**
 * WPForms Dashboard Widget function.
 *
 * @since 1.5.0
 */

'use strict';

var WPFormsDashboardWidget = window.WPFormsDashboardWidget || ( function( document, window, $ ) {

	/**
	 * Elements reference.
	 *
	 * @since 1.5.0
	 *
	 * @type {Object}
	 */
	var el = {

		$widget              : $( '#wpforms_reports_widget_pro' ),
		$chartTitle          : $( '#wpforms-dash-widget-chart-title' ),
		$chartResetBtn       : $( '#wpforms-dash-widget-reset-chart' ),
		$DaysSelect          : $( '#wpforms-dash-widget-timespan' ),
		$canvas              : $( '#wpforms-dash-widget-chart' ),
		$formsListBlock      : $( '#wpforms-dash-widget-forms-list-block' ),
		$recomBlockDismissBtn: $( '#wpforms-dash-widget-dismiss-recommended-plugin-block' ),
	};

	/**
	 * Chart.js functions and properties.
	 *
	 * @since 1.5.0
	 *
	 * @type {Object}
	 */
	var chart = {

		/**
		 * Chart.js instance.
		 *
		 * @since 1.5.0
		 */
		instance: null,

		/**
		 * Chart.js settings.
		 *
		 * @since 1.5.0
		 */
		settings: {
			type   : 'line',
			data   : {
				labels  : [],
				datasets: [ {
					label               : wpforms_dashboard_widget.i18n.entries,
					data                : [],
					backgroundColor     : 'rgba(255, 129, 0, 0.135)',
					borderColor         : 'rgba(211, 126, 71, 1)',
					borderWidth         : 2,
					pointRadius         : 4,
					pointBorderWidth    : 1,
					pointBackgroundColor: 'rgba(255, 255, 255, 1)',
				} ],
			},
			options: {
				scales                     : {
					xAxes: [ {
						type        : 'time',
						time        : {
							unit: 'day',
						},
						distribution: 'series',
						ticks       : {
							beginAtZero: true,
							source     : 'labels',
							padding    : 10,
							minRotation: 25,
							maxRotation: 25,
							callback   : function( value, index, values ) {

								// Distribute the ticks equally starting from a right side of xAxis.
								var gap = Math.floor( values.length / 7 );

								if ( gap < 1 ) {
									return value;
								}
								if ( ( values.length - index - 1 ) % gap === 0 ) {
									return value;
								}
							},
						},
					} ],
					yAxes: [ {
						ticks: {
							beginAtZero  : true,
							maxTicksLimit: 6,
							padding      : 20,
							callback     : function( value ) {

								// Make sure the tick value has no decimals.
								if ( Math.floor( value ) === value ) {
									return value;
								}
							},
						},
					} ],
				},
				elements                   : {
					line: {
						tension: 0,
					},
				},
				animation                  : {
					duration: 0,
				},
				hover                      : {
					animationDuration: 0,
				},
				legend                     : {
					display: false,
				},
				tooltips                   : {
					displayColors: false,
				},
				responsiveAnimationDuration: 0,
			},
		},

		/**
		 * Init Chart.js.
		 *
		 * @since 1.5.0
		 */
		init: function() {

			var ctx;

			if ( ! el.$canvas.length ) {
				return;
			}

			ctx = el.$canvas[ 0 ].getContext( '2d' );

			chart.instance = new Chart( ctx, chart.settings );

			chart.updateUI( wpforms_dashboard_widget.chart_data );
		},

		/**
		 * Update Chart.js with a new AJAX data.
		 *
		 * @since 1.5.0
		 *
		 * @param {Number} days Timespan (in days) to fetch the data for.
		 * @param {Number} formId
		 */
		ajaxUpdate: function( days, formId ) {

			var data = {
				_wpnonce: wpforms_dashboard_widget.nonce,
				action  : 'wpforms_dash_widget_get_chart_data',
				days    : days,
				form_id : formId,
			};

			app.addOverlay( $( chart.instance.canvas ) );

			$.post( ajaxurl, data, function( response ) {
				chart.updateUI( response );
			} );
		},

		/**
		 * Update Chart.js canvas.
		 *
		 * @since 1.5.0
		 *
		 * @param {Object} data Dataset for the chart.
		 */
		updateUI: function( data ) {

			app.removeOverlay( el.$canvas );

			if ( $.isEmptyObject( data ) ) {
				chart.updateWithDummyData();
				chart.showEmptyDataMessage();
			} else {
				chart.updateData( data );
				chart.removeEmptyDataMessage();
			}

			chart.instance.data.labels = chart.settings.data.labels;
			chart.instance.data.datasets[ 0 ].data = chart.settings.data.datasets[ 0 ].data;

			chart.instance.update();
		},

		/**
		 * Update Chart.js settings data.
		 *
		 * @since 1.5.0
		 *
		 * @param {Object} data Dataset for the chart.
		 */
		updateData: function( data ) {

			chart.settings.data.labels = [];
			chart.settings.data.datasets[ 0 ].data = [];

			$.each( data, function( index, value ) {

				var date = moment( value.day );

				chart.settings.data.labels.push( date );
				chart.settings.data.datasets[ 0 ].data.push( {
					t: date,
					y: value.count,
				} );
			} );
		},

		/**
		 * Update Chart.js settings with dummy data.
		 *
		 * @since 1.5.0
		 */
		updateWithDummyData: function() {

			chart.settings.data.labels = [];
			chart.settings.data.datasets[ 0 ].data = [];

			var end = moment().startOf( 'day' );
			var days = el.$DaysSelect.val() || 7;
			var date;

			var minY = 5;
			var maxY = 20;
			var i;

			for ( i = 1; i <= days; i ++ ) {

				date = end.clone().subtract( i, 'days' );

				chart.settings.data.labels.push( date );
				chart.settings.data.datasets[ 0 ].data.push( {
					t: date,
					y: Math.floor( Math.random() * ( maxY - minY + 1 ) ) + minY,
				} );
			}
		},

		/**
		 * Display an error message if the chart data is empty.
		 *
		 * @since 1.5.0
		 */
		showEmptyDataMessage: function() {

			chart.removeEmptyDataMessage();
			el.$canvas.after( wpforms_dashboard_widget.empty_chart_html );
		},

		/**
		 * Remove all empty data error messages.
		 *
		 * @since 1.5.0
		 */
		removeEmptyDataMessage: function() {

			el.$canvas.siblings( '.wpforms-error' ).remove();
		},

		/**
		 * Chart related event callbacks.
		 *
		 * @since 1.5.0
		 */
		events: {

			/**
			 * Update a chart on a timespan change.
			 *
			 * @since 1.5.0
			 */
			daysChanged: function() {

				var days = el.$DaysSelect.val();
				var formId = el.$DaysSelect.attr( 'data-active-form-id' ) || 0;

				chart.ajaxUpdate( days, formId );
				app.saveWidgetMeta( 'timespan', days );
			},

			/**
			 * Display a single for data only.
			 *
			 * @since 1.5.0
			 *
			 * @param {Object} $el Forms list "single form chart" button jQuery element.
			 */
			singleFormView: function( $el ) {

				var days = el.$DaysSelect.val();
				var formId = $el.closest( 'tr' ).attr( 'data-form-id' );
				var formTitle = $el.closest( 'tr' ).find( '.wpforms-dash-widget-form-title' ).text();

				el.$DaysSelect.attr( 'data-active-form-id', formId );
				el.$chartTitle.text( formTitle );
				el.$chartResetBtn.show();

				chart.ajaxUpdate( days, formId );
				app.saveWidgetMeta( 'active_form_id', formId );
			},

			/**
			 * Reset a chart to display all forms data.
			 *
			 * @since 1.5.0
			 */
			resetToGeneralView: function() {

				var days = el.$DaysSelect.val();

				el.$DaysSelect.removeAttr( 'data-active-form-id' );
				el.$chartTitle.text( wpforms_dashboard_widget.i18n.total_entries );
				el.$chartResetBtn.hide();

				chart.ajaxUpdate( days, 0 );
				app.saveWidgetMeta( 'active_form_id', 0 );
			},
		},
	};

	/**
	 * Public functions and properties.
	 *
	 * @since 1.5.0
	 *
	 * @type {Object}
	 */
	var app = {

		/**
		 * Publicly accessible Chart.js functions and properties.
		 *
		 * @since 1.5.0
		 */
		chart: chart,

		/**
		 * Start the engine.
		 *
		 * @since 1.5.0
		 */
		init: function() {
			$( document ).ready( app.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 1.5.0
		 */
		ready: function() {

			chart.init();
			app.events();
		},

		/**
		 * Register JS events.
		 *
		 * @since 1.5.0
		 */
		events: function() {

			app.chartEvents();
			app.formsListEvents();
			app.miscEvents();
		},

		/**
		 * Register chart area JS events.
		 *
		 * @since 1.5.0
		 */
		chartEvents: function() {

			el.$DaysSelect.change( function() {
				chart.events.daysChanged();
			} );

			el.$chartResetBtn.click( function() {
				chart.events.resetToGeneralView();
			} );
		},

		/**
		 * Register forms list area JS events.
		 *
		 * @since 1.5.0
		 */
		formsListEvents: function() {

			el.$DaysSelect.change( function() {
				app.updateFormsList( $( this ).val() );
			} );

			el.$widget.on( 'click', '.wpforms-dash-widget-single-chart-btn', function() {
				chart.events.singleFormView( $( this ) );
			} );

			el.$widget.on( 'click', '#wpforms-dash-widget-forms-more', function() {
				app.toggleCompleteFormsList();
			} );
		},

		/**
		 * Register other JS events.
		 *
		 * @since 1.5.0.4
		 */
		miscEvents: function() {

			el.$recomBlockDismissBtn.click( function() {
				app.dismissRecommendedBlock();
			} );
		},

		/**
		 * Update forms list with a new AJAX data.
		 *
		 * @since 1.5.0
		 *
		 * @param {Number} days Timespan (in days) to fetch the data for.
		 */
		updateFormsList: function( days ) {

			var data = {
				_wpnonce: wpforms_dashboard_widget.nonce,
				action  : 'wpforms_dash_widget_get_forms_list',
				days    : days,
			};

			app.addOverlay( el.$formsListBlock.children().first() );

			$.post( ajaxurl, data, function( response ) {

				el.$formsListBlock.html( response );
				app.saveWidgetMeta( 'timespan', days );
			} );
		},

		/**
		 * Toggle forms list hidden entries.
		 *
		 * @since 1.5.0.4
		 */
		toggleCompleteFormsList: function() {

			$( '#wpforms-dash-widget-forms-list-table .wpforms-dash-widget-forms-list-hidden-el' ).toggle();
			$( '#wpforms-dash-widget-forms-more' ).html( function( i, html ) {
				return html === wpforms_dashboard_widget.show_less_html ? wpforms_dashboard_widget.show_more_html : wpforms_dashboard_widget.show_less_html;
			} );
		},

		/**
		 * Save dashboard widget meta on a backend.
		 *
		 * @since 1.5.0
		 *
		 * @param {String} meta Meta name to save.
		 * @param {Number} value Value to save.
		 */
		saveWidgetMeta: function( meta, value ) {

			var data = {
				_wpnonce: wpforms_dashboard_widget.nonce,
				action  : 'wpforms_dash_widget_save_widget_meta',
				meta    : meta,
				value   : value,
			};

			$.post( ajaxurl, data );
		},

		/**
		 * Add an overlay to a widget block containing $el.
		 *
		 * @since 1.5.0
		 *
		 * @param {Object} $el jQuery element inside a widget block.
		 */
		addOverlay: function( $el ) {

			if ( ! $el.parent().closest( '.wpforms-dash-widget-block' ).length ) {
				return;
			}

			app.removeOverlay( $el );
			$el.after( '<div class="wpforms-dash-widget-overlay"></div>' );
		},

		/**
		 * Remove an overlay from a widget block containing $el.
		 *
		 * @since 1.5.0
		 *
		 * @param {Object} $el jQuery element inside a widget block.
		 */
		removeOverlay: function( $el ) {
			$el.siblings( '.wpforms-dash-widget-overlay' ).remove();
		},

		/**
		 * Dismiss recommended plugin block.
		 *
		 * @since 1.5.0.4
		 */
		dismissRecommendedBlock: function() {

			$( '.wpforms-dash-widget-recommended-plugin-block' ).remove();
			app.saveWidgetMeta( 'hide_recommended_block', 1 );
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPFormsDashboardWidget.init();
