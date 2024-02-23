(function( $, JetMapFieldsSettings ) {

	'use strict';

	class JetEngineAdvancedDateFields {

		constructor() {
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

			$( '.jet-engine-advanced-date-field.cx-ui-container', $scope ).each( function() {

				const $this    = $( this );
				const observer = new IntersectionObserver(
					function( entries, observer ) {

						entries.forEach( function( entry ) {

							if ( entry.isIntersecting ) {
								new JetEngineRenderAdvancedDateField( $this );

								// Detach observer after the first render the field
								observer.unobserve( entry.target );
							}
						} );
					}
				);

				observer.observe( $this[0] );

			} );
		}
	}

	class JetEngineRenderAdvancedDateField {

		constructor( selector ) {
			this.setup( selector );
			this.render();
			this.events();
		}

		setup( selector, mapProvider ) {
			this.$container = $( '<div></div>' ).appendTo( selector );
			this.$input = selector.find( 'input[type="hidden"]' );
			this.fieldName = this.$input.attr( 'name' );
			this.required = this.$input.attr( 'required' ) || false;
			this.value = this.$input.val() || '{}';

			try {
				this.value = JSON.parse( this.value );
			} catch( e ) {
				console.log(e);
				this.value = {};
			}

			this.date = this.value.date || '';
			this.isEndDate = this.value.is_end_date || false;
			this.endDate = this.value.end_date || '';
			this.isRecurring = this.value.is_recurring || false;
			this.recurring = this.value.recurring || 'weekly';
			this.recurringPeriod = this.value.recurring_period || 1;
			this.weekDays = this.value.week_days || [];
			this.monthlyType = this.value.monthly_type || 'on_day';
			this.monthDay = this.value.month_day || 1;
			this.monthDayType = this.value.month_day_type || 'first';
			this.monthDayTypeValue = this.value.month_day_type_value || 'Sun';
			this.month = this.value.month || 'Jan';
			this.end = this.value.end || 'after';
			this.endAfter = this.value.end_after || 5;
			this.endAfterDate = this.value.end_after_date || '';

			// fix saved switchers
			const switchers = [
				'isEndDate',
				'isRecurring'
			];
			
			for ( var i = 0; i < switchers.length; i++ ) {
				if ( 1 == this[ switchers[ i ] ] ) {
					this[ switchers[ i ] ] = true;
				} else {
					this[ switchers[ i ] ] = false;
				}
			}

		}

		getProps() {
			return {
				date: this.date,
				is_end_date: this.isEndDate,
				end_date: this.endDate,
				is_recurring: this.isRecurring,
				recurring: this.recurring,
				recurring_period: this.recurringPeriod,
				week_days: this.weekDays,
				monthly_type: this.monthlyType,
				month_day: this.monthDay,
				month_day_type: this.monthDayType,
				month_day_type_value: this.monthDayTypeValue,
				month: this.month,
				end: this.end,
				end_after: this.endAfter,
				end_after_date: this.endAfterDate,
			}
		}

		render() {
			
			const fieldTemplate = wp.template( 'jet-engine-advanced-date-field' );
			
			const templateData = {
				weekdaysConfig: [ {
					value: 1,
					label: 'Mon',
				}, {
					value: 2,
					label: 'Tue',
				}, {
					value: 3,
					label: 'Wed',
				}, {
					value: 4,
					label: 'Thu',
				}, {
					value: 5,
					label: 'Fri',
				}, {
					value: 6,
					label: 'Sat'
				},{
					value: 7,
					label: 'Sun',
				} ],
				months: [ {
					value: 1,
					label: 'Jan',
				}, {
					value: 2,
					label: 'Feb',
				}, {
					value: 3,
					label: 'Mar',
				}, {
					value: 4,
					label: 'Apr',
				}, {
					value: 5,
					label: 'May',
				}, {
					value: 6,
					label: 'Jun',
				}, {
					value: 7,
					label: 'Jul',
				}, {
					value: 8,
					label: 'Aug',
				}, {
					value: 9,
					label: 'Sep',
				}, {
					value: 10,
					label: 'Oct',
				}, {
					value: 11,
					label: 'Nov',
				}, {
					value: 12,
					label: 'Dec'
				} ],
				recurrings: [
					{ value: 'daily', label: 'Daily', sublabel: 'day(s)' },
					{ value: 'weekly', label: 'Weekly', sublabel: 'week(s)' },
					{ value: 'monthly', label: 'Monthly', sublabel: 'month(s)' },
					{ value: 'yearly', label: 'Yearly', sublabel: 'year(s)' }
				],
				required: this.required,
				fieldName: this.fieldName,
				date: this.date,
				isEndDate: this.isEndDate,
				endDate: this.endDate,
				isRecurring: this.isRecurring,
				recurring: this.recurring,
				recurringPeriod: this.recurringPeriod,
				weekDays: this.weekDays,
				monthlyType: this.monthlyType,
				monthDay: this.monthDay,
				monthDayType: this.monthDayType,
				monthDayTypeValue: this.monthDayTypeValue,
				month: this.month,
				end: this.end,
				endAfter: this.endAfter,
				endAfterDate: this.endAfterDate,
			};

			this.$container.html( fieldTemplate( templateData ) );

			window.JetEngineMetaBoxes.initDateFields( this.$container );

		}

		selectors( which ) {

			const selectors = {
				date: 'input[name="' + this.fieldName + '[date]"]',
				isEndDate: 'input[name="' + this.fieldName + '[is_end_date]"]',
				endDate: 'input[name="' + this.fieldName + '[end_date]"]',
				isRecurring: 'input[name="' + this.fieldName + '[is_recurring]"]',
				recurring: 'select[name="' + this.fieldName + '[recurring]"]',
				recurringPeriod: 'input[name="' + this.fieldName + '[recurring_period]"]',
				weekDays: 'input[name="' + this.fieldName + '[week_days][]"]',
				monthlyType: 'input[name="' + this.fieldName + '[monthly_type]"]',
				monthDay: 'select[name="' + this.fieldName + '[month_day]"]',
				monthDayType: 'select[name="' + this.fieldName + '[month_day_type]"]',
				monthDayTypeValue: 'select[name="' + this.fieldName + '[month_day_type_value]"]',
				month: 'select[name="' + this.fieldName + '[month]"]',
				end: 'select[name="' + this.fieldName + '[end]"]',
				endAfter: 'input[name="' + this.fieldName + '[end_after]"]',
				endAfterDate: 'input[name="' + this.fieldName + '[end_after_date]"]',
			}

			if ( which ) {
				return selectors[ which ];
			} else {
				return selectors;
			}

		}

		update( data, silent ) {

			silent = silent || false;

			let updated = false;

			for ( const key in data ) {
				if ( this[ key ] !== data[ key ] ) {
					this[ key ] = data[ key ];
					updated = true;
				}
			}

			this.$input.attr( 'value', JSON.stringify( this.getProps() ) );

			if ( ! silent && updated ) {
				this.render();
			}

		}

		events() {

			const switchers = [
				'isEndDate',
				'isRecurring'
			];

			const regularEnvents = [
				'end',
				'recurring',
				'month'
			];

			const silentEvents = [
				'recurringPeriod',
				'monthlyType',
				'monthDay',
				'monthDayType',
				'monthDayTypeValue',
				'endAfter',
			];

			const dates = {
				date: this.fieldName + '[date]',
				endDate: this.fieldName + '[end_date]',
				endAfterDate: this.fieldName + '[end_after_date]'
			};

			for ( var i = 0; i < switchers.length; i++ ) {
				this.$container.on( 'change', this.selectors( switchers[ i ] ), ( ( key, event ) => {
					this.update( { [ key ]: event.target.checked } );
				} ).bind( undefined, switchers[ i ] ) );
			}

			for ( var i = 0; i < regularEnvents.length; i++ ) {
				this.$container.on( 'change', this.selectors( regularEnvents[ i ] ), ( ( key, event ) => {
					this.update( { [ key ]: event.target.value } );
				} ).bind( undefined, regularEnvents[ i ] ) );
			}

			for ( var i = 0; i < silentEvents.length; i++ ) {
				this.$container.on( 'change', this.selectors( silentEvents[ i ] ), ( ( key, event ) => {
					this.update( { [ key ]: event.target.value }, true );
				} ).bind( undefined, silentEvents[ i ] ) );
			}
			
			for ( const prop in dates ) {
				$( window ).on( 'cx-control-change', ( ( key, name, event ) => {
					if ( event.controlName == name ) {
						this.update( { [ key ]: event.controlStatus }, true );
					}
				} ).bind( undefined, prop, dates[ prop ] ) );
			}

			this.$container.on( 'change', this.selectors( 'weekDays' ), ( event ) => {

				const newDays = [];
				const checked = document.querySelectorAll( this.selectors( 'weekDays' ) + ':checked' );

				if ( checked && checked.length ) {
					for ( var i = 0; i < checked.length; i++ ) {
						newDays.push( checked[ i ].value );
					}
				}

				this.update( { weekDays: newDays } );

			} );

		}

	}

	// Run on document ready.
	$( function () {
		new JetEngineAdvancedDateFields();
	} );


})( jQuery );
