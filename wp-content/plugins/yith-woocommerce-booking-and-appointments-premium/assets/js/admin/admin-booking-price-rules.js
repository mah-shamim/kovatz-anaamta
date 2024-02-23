jQuery( function ( $ ) {
	"use strict";

	var yith_wcbk_price_rules = {
		lastIndex               : 1,
		rulesContainer          : false,
		rulesList               : false,
		preNewRuleContainer     : $( '#yith-wcbk-price-rules__pre-new-rule' ),
		expandCollapseButton    : $( '.yith-wcbk-price-rules__expand-collapse' ),
		saveSettingsButton      : $( '#yith-wcbk-settings-tab-actions-save' ),
		init                    : function () {
			this._initParams();

			$( document ).on( 'change yith_wcbk_admin_booking_price_rule_condition_type_change', '.yith-wcbk-price-rule__condition__type', this.conditionTypeChanged );
			$( '.yith-wcbk-price-rules__new-rule' ).on( 'click', this.preAddRule );
			this.preNewRuleContainer.on( 'click', '.yith-wcbk-price-rules__add-rule', this.addRule );
			$( document ).on( 'click', '.yith-wcbk-price-rules__delete-rule', this.deleteRule );

			$( 'form#post, form#yith-wcbk-global-price-rules' ).on( 'submit', this.emptyPreAddRule );

			$( document ).on( 'click', '.yith-wcbk-price-rule__conditions__new-condition', this.addCondition );
			$( document ).on( 'click', '.yith-wcbk-price-rule__condition__delete-condition', this.deleteCondition );

			this.expandCollapseButton.on( 'click', this.expandCollapseAll );

			this.checkConditionVisibility();

			this.expandCollapseVisibility();
		},
		_initParams             : function () {
			this.rulesContainer = $( '.yith-wcbk-price-rules' ).first();
			this.rulesList      = this.rulesContainer.find( '.yith-wcbk-price-rules__list' );
			this.lastIndex      = this.rulesList.find( '.yith-wcbk-price-rule' ).length || 0;
		},
		nextIndex               : function () {
			return ++yith_wcbk_price_rules.lastIndex;
		},
		checkConditionVisibility: function ( el ) {
			el = el || $( '.yith-wcbk-price-rule__condition__type' );
			if ( el.is( '.yith-wcbk-price-rule' ) || el.is( '.yith-wcbk-price-rule__condition' ) ) {
				el = el.find( '.yith-wcbk-price-rule__condition__type' );
			}
			el.trigger( 'yith_wcbk_admin_booking_price_rule_condition_type_change' );
		},
		conditionTypeChanged    : function ( event ) {
			//yith-wcbk-price-rule__condition--show-if-
			var condition_type      = $( event.target ),
				value               = condition_type.val() || 'custom',
				condition_container = condition_type.closest( '.yith-wcbk-price-rule__condition' ),
				fields              = {
					custom : condition_container.find( '.yith-wcbk-price-rule__condition--show-if-custom' ),
					month  : condition_container.find( '.yith-wcbk-price-rule__condition--show-if-month' ),
					week   : condition_container.find( '.yith-wcbk-price-rule__condition--show-if-week' ),
					day    : condition_container.find( '.yith-wcbk-price-rule__condition--show-if-day' ),
					time   : condition_container.find( '.yith-wcbk-price-rule__condition--show-if-time input' ),
					numeric: condition_container.find( '.yith-wcbk-price-rule__condition--show-if-numeric' )
				},
				types               = ['custom', 'month', 'week', 'day', 'time', 'numeric'], i, type,
				inArray             = function ( value, array ) {
					return $.inArray( value, array ) !== -1;
				};

			if ( inArray( value, ['custom', 'month', 'week', 'day', 'time', 'numeric'] ) ) {
				for ( i in types ) {
					type = types[ i ];
					if ( value !== type ) {
						fields[ type ].attr( 'disabled', 'disabled' );
						inArray( type, ['custom', 'time'] ) ? fields[ type ].parent().hide() : fields[ type ].hide();
					}
				}

				fields[ value ].removeAttr( 'disabled' );
				inArray( value, ['custom', 'time'] ) ? fields[ value ].parent().show() : fields[ value ].show();

			} else {
				fields.custom.attr( 'disabled', 'disabled' );
				fields.month.attr( 'disabled', 'disabled' );
				fields.week.attr( 'disabled', 'disabled' );
				fields.day.attr( 'disabled', 'disabled' );
				fields.time.attr( 'disabled', 'disabled' );


				fields.custom.parent().hide();
				fields.month.hide();
				fields.week.hide();
				fields.day.hide();
				fields.time.parent().hide();

				fields.numeric.removeAttr( 'disabled' );
				fields.numeric.show();
			}
		},
		getRuleToAdd            : function () {
			var rule = yith_wcbk_price_rules.preNewRuleContainer.find( '.yith-wcbk-price-rule' ).first();
			return rule.length ? rule : false;
		},
		emptyPreAddRule         : function () {
			yith_wcbk_price_rules.preNewRuleContainer.html( '' );
		},
		preAddRule              : function ( event ) {
			event.preventDefault();
			var button = $( event.target ), _offset;
			if ( !yith_wcbk_price_rules.getRuleToAdd() ) {

				var template = button.data( 'template' ),
					index    = yith_wcbk_price_rules.nextIndex(),
					new_rule;

				yith_wcbk_price_rules.preNewRuleContainer.hide();

				template = template.replace( new RegExp( '{{INDEX}}', 'g' ), index );
				new_rule = $( template );
				yith_wcbk_price_rules.preNewRuleContainer.append( new_rule );

				new_rule.find( '.yith-wcbk-admin-date-picker' ).yith_wcbk_datepicker();

				bkOperatorAndAmount.initOperators( new_rule );
				bkFakeOnoff.initVisibility( new_rule );

				yith_wcbk_price_rules.checkConditionVisibility( new_rule );
				$( document ).trigger( 'yith_wcbk_product_metabox_dynamic_durations' );
				yith_wcbk_price_rules.preNewRuleContainer.addClass( 'bk--open' ).slideDown();
			} else {
				if ( yith_wcbk_price_rules.preNewRuleContainer.is( '.bk--open' ) ) {
					yith_wcbk_price_rules.preNewRuleContainer.removeClass( 'bk--open' ).slideUp();
				} else {
					yith_wcbk_price_rules.preNewRuleContainer.addClass( 'bk--open' ).slideDown();
				}
			}

			if ( yith_wcbk_price_rules.preNewRuleContainer.is( '.bk--open' ) ) {
				_offset = button.offset();
				if ( _offset && _offset.top ) {
					$( 'html, body' ).animate( { scrollTop: _offset.top - button.outerHeight() - 20 } );
				}
			}

		},
		addRule                 : function ( event ) {
			event.preventDefault();
			var rule = $( this ).closest( '.yith-wcbk-price-rule' );
			rule.find( '.yith-wcbk-price-rules__add-rule' ).remove();
			yith_wcbk_price_rules.rulesList.append( rule );

			yith_wcbk_price_rules.expandCollapseVisibility();
			yith_wcbk_price_rules.attentionForSaving();
		},
		deleteRule              : function ( event ) {
			event.preventDefault();
			var rule = $( event.target ).closest( '.yith-wcbk-price-rule' );
			rule
				.animate( { opacity: .3 }, 200 )
				.delay( 200 )
				.slideUp( 300, function () {
					$( this ).remove();
					yith_wcbk_price_rules.expandCollapseVisibility();
					yith_wcbk_price_rules.attentionForSaving();
				} );
		},
		addCondition            : function ( event ) {
			event.preventDefault();

			var button          = $( event.target ),
				template        = wp.template( 'yith-wcbk-price-rule-condition' ),
				conditions      = button.closest( '.yith-wcbk-price-rule__conditions' ),
				conditions_list = conditions.find( '.yith-wcbk-price-rule__conditions__list' ),
				rule            = button.closest( '.yith-wcbk-price-rule' ),
				index           = rule.data( 'index' ) || 1,
				condition_index = 1,
				new_condition;


			if ( conditions_list.data( 'last-index' ) ) {
				condition_index = conditions_list.data( 'last-index' ) + 1;
			} else {
				condition_index = conditions_list.find( '.yith-wcbk-price-rule__condition' ).length || 0;
				condition_index += 1;
			}

			conditions_list.data( 'last-index', condition_index );

			new_condition = $( template( { ruleIndex: index, conditionIndex: condition_index } ) );
			conditions_list.append( new_condition );

			new_condition.find( '.yith-wcbk-admin-date-picker' ).yith_wcbk_datepicker();
			yith_wcbk_price_rules.checkConditionVisibility( new_condition );
		},
		deleteCondition         : function ( event ) {
			event.preventDefault();
			$( event.target ).closest( '.yith-wcbk-price-rule__condition' ).remove();
		},
		expandCollapseAll       : function ( event ) {
			var button     = $( event.target ).closest( '.yith-wcbk-price-rules__expand-collapse' ),
				rules_list = yith_wcbk_price_rules.rulesList;

			if ( button.is( '.yith-wcbk-price-rules__expand-collapse--collapse' ) ) {
				button.removeClass( 'yith-wcbk-price-rules__expand-collapse--collapse' );
				rules_list.find( '.yith-wcbk-settings-section-box:not(.yith-wcbk-settings-section-box--closed) .yith-wcbk-settings-section-box__toggle' ).click();
			} else {
				button.addClass( 'yith-wcbk-price-rules__expand-collapse--collapse' );
				rules_list.find( '.yith-wcbk-settings-section-box.yith-wcbk-settings-section-box--closed .yith-wcbk-settings-section-box__toggle' ).click();
			}
		},
		expandCollapseVisibility: function () {
			if ( yith_wcbk_price_rules.rulesList.find( '.yith-wcbk-price-rule' ).length ) {
				yith_wcbk_price_rules.expandCollapseButton.show();
			} else {
				yith_wcbk_price_rules.expandCollapseButton.hide();
			}
		},
		attentionForSaving      : function () {
			if ( yith_wcbk_price_rules.saveSettingsButton.length ) {
				yith_wcbk_price_rules.saveSettingsButton.removeClass( 'yith-wcbk-effect--wiggle' );
				yith_wcbk_price_rules.saveSettingsButton.outerWidth(); // this is useful to allow restarting animation
				yith_wcbk_price_rules.saveSettingsButton.addClass( 'yith-wcbk-effect--wiggle' );
			}
		}
	};

	yith_wcbk_price_rules.init();


	var bkOperatorAndAmount = {
		init                : function () {
			var self = this;
			$( document ).on( 'change', '.yith-wcbk-operator-and-amount-fields__operator', self.handleOperatorChange );

			self.initOperators();
		},
		initOperators       : function ( _parent ) {
			_parent = typeof _parent !== 'undefined' ? _parent : $( document );

			_parent.find( '.yith-wcbk-operator-and-amount-fields__operator' ).each( function () {
				bkOperatorAndAmount.handleOperatorChange.call( $( this ) );
			} );
		},
		handleOperatorChange: function () {
			var wrap           = $( this ).closest( '.yith-wcbk-operator-and-amount-fields' ),
				currencySymbol = wrap.data( 'currency-symbol' ),
				symbol         = wrap.find( '.yith-wcbk-operator-and-amount-fields__symbol' ),
				value          = $( this ).val();

			if ( ['add', 'sub', 'set-to'].includes( value ) ) {
				symbol.html( currencySymbol );
			} else if ( ['add-percentage', 'sub-percentage'].includes( value ) ) {
				symbol.html( '%' );
			} else {
				symbol.html( '' );
			}
		}
	};
	bkOperatorAndAmount.init();

	var bkFakeOnoff = {
		init           : function () {
			var self = this;
			$( document ).on( 'change', '.yith-wcbk-price-rule__base-fee-enabled input', self.toggleBaseFee );
			$( document ).on( 'change', '.yith-wcbk-price-rule__base-price-enabled input', self.toggleBasePrice );

			self.initVisibility();
		},
		initVisibility : function ( _parent ) {
			_parent = typeof _parent !== 'undefined' ? _parent : $( document );

			_parent.find( '.yith-wcbk-price-rule__base-fee-enabled input' ).each( function () {
				bkFakeOnoff.toggleBaseFee.call( $( this ) );
			} );

			_parent.find( '.yith-wcbk-price-rule__base-price-enabled input' ).each( function () {
				bkFakeOnoff.toggleBasePrice.call( $( this ) );
			} );
		},
		_togglePrice   : function ( _on_off, _selector ) {
			var rule       = _on_off.closest( '.yith-wcbk-price-rule' ),
				fieldsWrap = rule.find( _selector ),
				fields     = fieldsWrap.find( 'input, select' );
			console.log(
				{
					'value'    : _on_off.val(),
					'_selector': _selector
				}
			);
			if ( _on_off.val() === 'yes' ) {
				fields.prop( 'disabled', false );
				fieldsWrap.show();
			} else {
				fields.prop( 'disabled', true );
				fieldsWrap.hide();
			}
		},
		toggleBaseFee  : function () {
			bkFakeOnoff._togglePrice( $( this ), '.yith-wcbk-price-rule__base-fee-fields' );
		},
		toggleBasePrice: function () {
			bkFakeOnoff._togglePrice( $( this ), '.yith-wcbk-price-rule__base-price-fields' );
		}
	};

	bkFakeOnoff.init();

} );