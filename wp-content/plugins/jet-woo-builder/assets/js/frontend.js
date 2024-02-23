( function( $, elementorFrontend ) {

	"use strict";

	let JetWooBuilder = {

		init: function() {

			let widgets = {
				'jet-single-images.default' : JetWooBuilder.widgetProductImages,
				'jet-single-add-to-cart.default' : JetWooBuilder.widgetSingleAddToCart,
				'jet-woo-builder-archive-add-to-cart.default' : JetWooBuilder.widgetArchiveAddToCart,
				'jet-single-tabs.default' : JetWooBuilder.widgetProductTabs,
				'jet-woo-products.default' : JetWooBuilder.widgetProductsGrid,
				'jet-woo-products-list.default' : JetWooBuilder.widgetProductsList,
				'jet-woo-categories.default' : JetWooBuilder.widgetCategories,
				'jet-cart-table.default' : JetWooBuilder.widgetCartTable,
				'jet-woo-builder-products-loop.default' : JetWooBuilder.widgetProductsLoop,
			};

			$.each( widgets, function( widget, callback ) {
				elementorFrontend.hooks.addAction( 'frontend/element_ready/' + widget, callback );
			});

			elementorFrontend.hooks.addFilter( 'jet-popup/widget-extensions/popup-data', JetWooBuilder.prepareJetPopup );

			$( window ).on( 'jet-popup/render-content/ajax/success', JetWooBuilder.jetPopupLoaded );

			$( document )
				.on( 'wc_update_cart added_to_cart', JetWooBuilder.handleJetPopupWithWCEvents )
				.on( 'jet-filter-content-rendered', function ( _, $scope ) {
					JetWooBuilder.widgetProductsGrid( $scope );
					JetWooBuilder.handleInputQuantityValue( $scope );
				} )
				.on( 'click.JetWooBuilder', '.jet-woo-item-overlay-wrap', JetWooBuilder.handleListingItemClick );

		},

		commonInit: function () {

			if ( window.jetWooBuilderData.single_ajax_add_to_cart ) {
				let $product = $( '.woocommerce div.product' );

				if ( ! $product.hasClass( 'product-type-external' ) ) {
					$( document ).on( 'click.JetWooBuilder', '.single_add_to_cart_button:not(.disabled)', JetWooBuilder.singleProductAjaxAddToCart );
				}
			}

			if ( navigator.userAgent.indexOf('Safari') !== -1 && navigator.userAgent.indexOf('Chrome') === -1 ) {
				document.addEventListener( 'click', function ( event ) {

					if ( event.target.matches( '.add_to_cart_button .button-text' ) ) {
						event.target.parentNode.focus();
					}

					if ( event.target.matches( '.add_to_cart_button' ) || event.target.matches( '.single_add_to_cart_button' ) ) {
						event.target.focus();
					}

				} );
			}

			$( document.body ).bind( 'country_to_state_changing', function ( event, country, wrapper ) {
				setTimeout( function () {
					JetWooBuilder.setAddressFieldsRequiredValidation( wrapper );
				}, 500 );
			} );

		},

		setAddressFieldsRequiredValidation: function ( wrapper ) {

			let $widget = wrapper.closest( '.elementor-element' ),
				settings = JetWooBuilder.getElementorElementSettings( $widget );

			if ( settings && settings.modify_field ) {
				let locale_fields = $.parseJSON( wc_address_i18n_params.locale_fields );

				if ( locale_fields ) {
					$.each( locale_fields, function( key, value ) {

						let fields_ids = value.split( ',' );

						$.each( fields_ids, function ( index, id ) {

							let field = wrapper.find( id.trim() );

							if ( field.length ) {
								if ( field.hasClass( 'jwb-field-required' ) ) {
									JetWooBuilder.fieldIsRequired( field, true );
								} else if ( field.hasClass( 'jwb-field-optional' ) ) {
									JetWooBuilder.fieldIsRequired( field, false );
								}
							}

						} );

					} );
				}

			}

		},

		fieldIsRequired: function ( field, isRequired ) {

			JetWooBuilder.modifyFieldLabelWhitespace( field );

			if ( isRequired ) {
				field.find( 'label .optional' ).remove();
				field.addClass( 'validate-required' );

				if ( 0 === field.find( 'label .required' ).length ) {
					field.find( 'label' ).append( '&nbsp;<abbr class="required" title="' + wc_address_i18n_params.i18n_required_text + '">*</abbr>' );
				}
			} else {
				field.find( 'label .required' ).remove();
				field.removeClass( 'validate-required woocommerce-invalid woocommerce-invalid-required-field' );

				if ( 0 === field.find( 'label .optional' ).length ) {
					field.find( 'label' ).append( '&nbsp;<span class="optional">(' + wc_address_i18n_params.i18n_optional_text + ')</span>' );
				}
			}

		},

		modifyFieldLabelWhitespace: function ( field ) {

			let label = field.find( 'label' ).html();

			if ( label ) {
				field.find( 'label' ).html( label.replace( /&nbsp;/g, '' ).trim() );
			}

		},

		widgetProductsLoop: function( $scope ) {

			let settings = JetWooBuilder.getElementorElementSettings( $scope );

			if ( settings && settings.switcher_enable ) {
				let $productsWrapper = $scope.find( '.jet-woo-products-wrapper' ),
					$switcherControl = $scope.find( '.jet-woo-switcher-controls-wrapper .jet-woo-switcher-btn' );

				$switcherControl.on( 'click.JetWooBuilder', function( event ) {

					event.preventDefault();

					let $thisBtn = $( this ),
						activeLayout = $thisBtn.hasClass( 'jet-woo-switcher-btn-main' ) ? settings.main_layout : settings.secondary_layout,
						filterQuery;

					if ( window.JetSmartFilters && window.JetSmartFilters.filterGroups['woocommerce-archive/default'] ) {
						filterQuery = window.JetSmartFilters.filterGroups['woocommerce-archive/default'].query;
					}

					$productsWrapper.addClass( 'jet-layout-loading' );

					$.ajax( {
						type: 'POST',
						url: window.jetWooBuilderData.ajax_url,
						data: {
							action: 'jet_woo_builder_get_layout',
							query: window.jetWooBuilderData.products,
							layout: activeLayout,
							filters: filterQuery
						},
					} ).done( function( response ) {
						$productsWrapper.removeClass( 'jet-layout-loading' );
						$productsWrapper.html( response.data.html );

						JetWooBuilder.elementorFrontendInit( $productsWrapper );

						if ( ! $thisBtn.hasClass( 'active' ) ) {
							$thisBtn.addClass( 'active' );
							$thisBtn.siblings().removeClass( 'active' );
						}

						$( document ).trigger( 'jet-woo-builder-content-rendered', [ this, response ] );
					} );

				} );
			}

		},

		handleInputQuantityValue: function( $scope ) {

			let $eWidget = $scope.closest( '.elementor-widget' ),
				settings = JetWooBuilder.getElementorElementSettings( $eWidget );

			if ( settings && 'yes' === settings.show_quantity ) {
				let $cartForm = $scope.find( 'form.cart' );

				$cartForm.on( 'change', 'input.qty', function() {

					if ( '0' === this.value && ! $( this.form ).hasClass( 'grouped_form' ) ) {
						this.value = '1';
					}

					let $button = $( this.form ).find( 'button[data-quantity]' );

					$button.attr( 'data-quantity', this.value );

					if ( this.max ) {
						if ( +this.value > +this.max ) {
							$button.removeClass( 'ajax_add_to_cart' );
						} else if ( ! $button.hasClass( 'ajax_add_to_cart' ) ) {
							$button.addClass( 'ajax_add_to_cart' );
						}
					}

				} );
			}

		},

		jetPopupLoaded : function( event, popupData){

			if ( ! popupData.data.isJetWooBuilder ) {
				return;
			}

			let $jetPopup = $( '#' + popupData.data.popupId );

			setTimeout( function() {

				$( window ).trigger('resize');

				$jetPopup.addClass( 'woocommerce product single-product quick-view-product' );
				$jetPopup.find( '.jet-popup__container-content' ).addClass( 'product' );

				$( '.jet-popup .variations_form' ).each( function() {
					$( this ).wc_variation_form();
				} );

				$( '.jet-popup .woocommerce-product-gallery.images' ).each( function() {
					$( this ).wc_product_gallery();
				} );

			}, 500 );

		},

		prepareJetPopup: function( popupData, widgetData, $scope, event ) {

			if ( widgetData['is-jet-woo-builder'] ) {
				let $product;

				popupData['isJetWooBuilder'] = true;
				popupData['templateId'] = widgetData['jet-woo-builder-qv-template'];

				if ( $scope.hasClass( 'elementor-widget-jet-woo-products' ) || $scope.hasClass( 'elementor-widget-jet-woo-products-list' ) ) {
					$product = $( event.target ).parents( '.jet-woo-builder-product' );
				} else {
					$product = $scope.parents( '.jet-woo-builder-product' );
				}

				if ( $product.length ) {
					popupData['productId'] = $product.data( 'product-id' );
				}
			}

			return popupData;

		},

		widgetProductImages: function( $scope ) {

			$scope.find( '.jet-single-images__loading' ).remove();

			if ( $('body').hasClass( 'single-product' ) ) {
				return;
			}

			$scope.find( '.woocommerce-product-gallery' ).each( function() {
				$( this ).wc_product_gallery();
			} );

		},

		widgetSingleAddToCart: function( $scope ) {

			if ( $('body').hasClass( 'single-product' ) ) {
				return;
			}

			if ( 'undefined' !== typeof wc_add_to_cart_variation_params ) {
				$scope.find( '.variations_form' ).each( function() {
					$( this ).wc_variation_form();
				} );
			}

		},

		widgetArchiveAddToCart: function ( $scope ) {
			JetWooBuilder.handleInputQuantityValue( $scope );
		},

		widgetProductTabs: function( $scope ) {

			$scope.find( '.jet-single-tabs__loading' ).remove();

			if ( $('body').hasClass( 'single-product' ) ) {
				return;
			}

			let hash  = window.location.hash,
				url   = window.location.href,
				$tabs = $scope.find( '.wc-tabs, ul.tabs' ).first();

			$tabs.find( 'a' ).addClass( 'elementor-clickable' );

			$scope.find( '.wc-tab, .woocommerce-tabs .panel:not(.panel .panel)' ).hide();

			if ( hash.toLowerCase().indexOf( 'comment-' ) >= 0 || hash === '#reviews' || hash === '#tab-reviews' ) {
				$tabs.find( 'li.reviews_tab a' ).trigger( 'click' );
			} else if ( url.indexOf( 'comment-page-' ) > 0 || url.indexOf( 'cpage=' ) > 0 ) {
				$tabs.find( 'li.reviews_tab a' ).trigger( 'click' );
			} else if ( hash === '#tab-additional_information' ) {
				$tabs.find( 'li.additional_information_tab a' ).trigger( 'click' );
			} else {
				$tabs.find( 'li:first a' ).trigger( 'click' );
			}

		},

		widgetProductsGrid: function ( $scope ) {

			JetWooBuilder.handleInputQuantityValue( $scope );

			let $carousel = $scope.find( '.jet-woo-carousel' ),
				$wrapper = $scope.find( '.jet-woo-products' ),
				mobileHover = $wrapper.data( 'mobile-hover' ),
				$productItem = $wrapper.find( '.jet-woo-products__item' ),
				$cqwWrapper = $productItem.find( '.jet-woo-products-cqw-wrapper' ),
				$hoveredContent = $productItem.find( '.hovered-content' ),
				cqwWrapperExist = false,
				hoveredContentExist = false;

			if ( $cqwWrapper.length > 0 && $cqwWrapper.html().trim().length > 0 ) {
				cqwWrapperExist = true;
			}

			if ( $hoveredContent.length > 0 && $hoveredContent.html().trim().length > 0 ) {
				hoveredContentExist = true;
			}

			if ( ( cqwWrapperExist || hoveredContentExist ) && mobileHover ) {
				JetWooBuilder.mobileHoverOnTouch( $productItem, '.jet-woo-product-thumbnail' );
			}

			if ( $carousel.length ) {
				JetWooBuilder.initCarousel( $carousel, $carousel.data( 'slider_options' ) );
			}

		},

		widgetProductsList: function ( $scope ) {
			JetWooBuilder.handleInputQuantityValue( $scope );
		},

		widgetCategories: function ( $scope ) {

			let $carousel = $scope.find( '.jet-woo-carousel' ),
				$wrapper = $scope.find( '.jet-woo-categories' ),
				mobileHover = $wrapper.data( 'mobile-hover' ),
				$categoryItem = $wrapper.find( '.jet-woo-categories__item' ),
				$count = $categoryItem.find( '.jet-woo-category-count' );

			if ( ( $wrapper.hasClass( 'jet-woo-categories--preset-2' ) && $count.length > 0 || $wrapper.hasClass( 'jet-woo-categories--preset-3' ) ) && mobileHover ) {
				JetWooBuilder.mobileHoverOnTouch( $categoryItem, '.jet-woo-category-thumbnail' );
			}

			if ( $carousel.length ) {
				JetWooBuilder.initCarousel( $carousel, $carousel.data( 'slider_options' ) );
			}

		},

		mobileHoverOnTouch: function( $item, thumbnail ) {
			if ( 'undefined' !== typeof window.ontouchstart ) {
				$item.each( function() {

					let $this = $( this ),
						$thumbnailLink = $this.find( thumbnail + ' a' ),
						$adjacentItems = $this.siblings();

					if ( $this.hasClass( 'jet-woo-products__item' ) ) {
						let $itemContent = $this.not( thumbnail );

						$itemContent.each( function() {
							let $currentItem = $( this );

							JetWooBuilder.mobileTouchEvent( $this, $currentItem, $adjacentItems );
						} );
					}

					JetWooBuilder.mobileTouchEvent( $this, $thumbnailLink, $adjacentItems );

				} );
			}
		},

		mobileTouchEvent: function( $target, $item, $adjacentItems ) {
			$item.on( 'click', function( event ) {
				if ( ! $target.hasClass( 'mobile-hover' ) ) {
					event.preventDefault();

					$adjacentItems.each( function() {
						if ( $( this ).hasClass( 'mobile-hover' ) ) {
							$( this ).removeClass( 'mobile-hover' );
						}
					} );

					$target.addClass( 'mobile-hover' );
				}
			} );
		},

		initCarousel: function( $target, options ) {

			let $eWidget = $target.closest( '.elementor-widget' ),
				slidesCount = $target.find( '.swiper-slide' ).length,
				settings = JetWooBuilder.getElementorElementSettings( $eWidget ),
				eBreakpoints = window.elementorFrontend.config.responsive.activeBreakpoints,
				defaultOptions = {},
				slidesToShow = +settings.columns || 4,
				slideOverflow = settings.slides_overflow_enabled && settings.slides_overflow ? +settings.slides_overflow : 0,
				spaceBetween = undefined !== settings.space_between_slides ? +settings.space_between_slides : 10,
				defaultSlidesToShowMap = {
					mobile: 1,
					tablet: 2
				};

			defaultOptions = {
				slidesPerView: slidesToShow + slideOverflow,
				spaceBetween: spaceBetween,
				crossFade: 'fade' === options.effect,
				handleElementorBreakpoints: true
			}

			defaultOptions.breakpoints = {};

			let lastBreakpointSlidesToShowValue = slidesToShow;

			Object.keys( eBreakpoints ).reverse().forEach( breakpointName => {

				const defaultSlidesToShow = defaultSlidesToShowMap[ breakpointName ] ? defaultSlidesToShowMap[ breakpointName ] : lastBreakpointSlidesToShowValue;
				const bpSlidesToShow = +settings[ 'columns_' + breakpointName ] || defaultSlidesToShow;
				const bpSlideOverflow = settings.slides_overflow_enabled && settings[ 'slides_overflow_' + breakpointName ] ? +settings[ 'slides_overflow_' + breakpointName ] : slideOverflow;

				defaultOptions.breakpoints[ eBreakpoints[ breakpointName ].value ] = {
					slidesPerView: bpSlidesToShow + bpSlideOverflow,
					slidesPerGroup: +settings[ 'slides_to_scroll_' + breakpointName ] || options.slidesPerGroup,
					spaceBetween: undefined !== settings['space_between_slides_' + breakpointName] ? +settings['space_between_slides_' + breakpointName] : spaceBetween
				};

				lastBreakpointSlidesToShowValue = +settings[ 'columns_' + breakpointName ] || defaultSlidesToShow;

			} );

			if ( options.paginationEnable ) {
				defaultOptions.pagination = {
					el: '.swiper-pagination',
					clickable: true,
					dynamicBullets: options.dynamicBullets
				}
			}

			if ( options.navigationEnable ) {
				defaultOptions.navigation = {
					nextEl: '.jet-swiper-button-next',
					prevEl: '.jet-swiper-button-prev',
				}
			}

			let currentDeviceSlidePerView = +settings[ 'columns_' + elementorFrontend.getCurrentDeviceMode() ] || +settings['columns'];

			if ( slidesCount > currentDeviceSlidePerView ) {
				const Swiper = elementorFrontend.utils.swiper;

				new Swiper( $target, $.extend( {}, defaultOptions, options ) ).then( swiper => {
					$( document ).trigger( 'jet-woo-builder-swiper-initialized', swiper );

					if ( 'vertical' === options.direction && options.paginationEnable && options.dynamicBullets ) {
						$target.find( '.swiper-pagination' ).css( 'width', $target.find( '.swiper-pagination-bullet-active' ).width() );
					}
				} );

				$target.find( '.jet-arrow' ).show();
			} else if ( options.direction === 'vertical' ) {
				$target.addClass( 'swiper-container-vertical' );
				$target.find( '.jet-arrow' ).hide();
			} else {
				$target.find( '.jet-arrow' ).hide();
			}

		},

		handleJetPopupWithWCEvents: function ( event, fragments, hash, button ) {

			let popupWrapper = $( button ).closest( '.jet-popup' );

			if ( popupWrapper.length && popupWrapper.hasClass( 'quick-view-product' ) ) {
				$( window ).trigger( {
					type: 'jet-popup-close-trigger',
					popupData: {
						popupId: popupWrapper.attr( 'id' ),
						constantly: false
					}
				} );
			}

			let purchasePopupData = $( button ).closest( '[data-purchase-popup-id]' );

			if ( purchasePopupData.length ) {
				let popupId = purchasePopupData.data( 'purchase-popup-id' );

				if ( popupId ) {
					$( window ).trigger( {
						type: 'jet-popup-open-trigger',
						popupData: {
							popupId: 'jet-popup-' + popupId
						}
					} );
				}
			}

		},

		widgetCartTable: function ( $scope ) {

			$scope.find( '.cart-collaterals' ).filter( function() {
				return $( this ).children().length === 0;
			} ).hide();

			let settings = JetWooBuilder.getElementorElementSettings( $scope );

			if ( 'yes' === settings.cart_update_automatically ) {
				let timeout;

				$('.woocommerce').on('change', 'input.qty', function() {
					if ( timeout !== undefined ) {
						clearTimeout( timeout );
					}

					timeout = setTimeout(function() {
						$( '[name="update_cart"]' ).trigger( 'click' );
					}, 300 );
				} );
			}

		},

		singleProductAjaxAddToCart: function( event ) {

			if ( event ) {
				event.preventDefault();
			}

			let $form = $( this ).closest('form');

			if( ! $form[0].checkValidity() ) {
				$form[0].reportValidity();

				return false;
			}

			let $thisBtn = $( this ),
				product_id = $thisBtn.val() || '',
				cartFormData = $form.serialize();

			$.ajax( {
				type: 'POST',
				url: window.jetWooBuilderData.ajax_url,
				data: 'action=jet_woo_builder_add_cart_single_product&add-to-cart=' + product_id + '&' + cartFormData,
				beforeSend: function () {
					$thisBtn.removeClass( 'added' ).addClass( 'loading' );
				},
				complete: function () {
					$thisBtn.addClass( 'added' ).removeClass( 'loading' );
				},
				success: function ( response ) {

					if ( ! response ) {
						return;
					}

					if ( response.error && response.product_url ) {
						window.location = response.product_url;

						return;
					}

					if ( 'undefined' === typeof wc_add_to_cart_params ) {
						return;
					}

					$( document.body ).trigger( 'wc_fragment_refresh' );
					$( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, $thisBtn ] );

					$( '.woocommerce-notices-wrapper' ).html( response.fragments.notices_html );

				},
			} );

			return false;

		},

		handleListingItemClick: function ( event ) {

			let url = $( this ).data( 'url' ),
				target = $( this ).data( 'target' ) || false;

			if ( url ) {
				event.preventDefault();

				if (
					(window.elementorFrontend && window.elementorFrontend.isEditMode())
					|| $( event.target ).parents( '.jet-compare-button__link' ).length
					|| $( event.target ).parents( '.jet-wishlist-button__link' ).length
					|| $( event.target ).parents( '.jet-quickview-button__link' ).length
				) {
					return;
				}

				if ( '_blank' === target ) {
					window.open( url );
					return;
				}

				window.location = url;
			}

		},

		getElementorElementSettings: function( $scope ) {

			if ( window.elementorFrontend && window.elementorFrontend.isEditMode() && $scope.hasClass( 'elementor-element-edit-mode' ) ) {
				return JetWooBuilder.getEditorElementSettings( $scope );
			}

			return $scope.data( 'settings' ) || {};

		},

		getEditorElementSettings: function( $scope ) {

			let modelCID = $scope.data( 'model-cid' ),
				elementData;

			if ( ! modelCID ) {
				return {};
			}

			if ( ! window.elementorFrontend.hasOwnProperty( 'config' ) ) {
				return {};
			}

			if ( ! window.elementorFrontend.config.hasOwnProperty( 'elements' ) ) {
				return {};
			}

			if ( ! window.elementorFrontend.config.elements.hasOwnProperty( 'data' ) ) {
				return {};
			}

			elementData = window.elementorFrontend.config.elements.data[ modelCID ];

			if ( ! elementData ) {
				return {};
			}

			return elementData.toJSON();

		},

		elementorFrontendInit: function( $content ) {

			$content.find( '[data-element_type]' ).each( function() {

				let $this       = $( this ),
					elementType = $this.data( 'element_type' );

				if ( ! elementType ) {
					return;
				}

				if ( 'widget' === elementType ) {
					elementType = $this.data( 'widget_type' );

					window.elementorFrontend.hooks.doAction( 'frontend/element_ready/widget', $this, $ );
				}

				window.elementorFrontend.hooks.doAction( 'frontend/element_ready/global', $this, $ );
				window.elementorFrontend.hooks.doAction( 'frontend/element_ready/' + elementType, $this, $ );

			} );

		}

	};

	$( window ).on( 'elementor/frontend/init', JetWooBuilder.init );

	JetWooBuilder.commonInit();

	window.JetWooBuilder = JetWooBuilder;

}( jQuery, window.elementorFrontend ) );