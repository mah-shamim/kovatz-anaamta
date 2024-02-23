( function( $ ) {

	'use strict';

	let JetWooTemplatesPopups = {

		init: function() {

			JetWooTemplatesPopups.initPopups();

			$( document )
				.on( 'change.JetWooTemplatesPopups', '#template_type', JetWooTemplatesPopups.predesignedTemplates )
				.on( 'click.JetWooTemplatesPopups', '.jet-woo-builder-create-form__item-uncheck', JetWooTemplatesPopups.uncheckItem )
				.on( 'click.JetWooTemplatesPopups', '.jet-woo-builder-create-form__label', JetWooTemplatesPopups.isCheckedItem );

		},

		initPopups: function() {

			let $createButton = $( '.page-title-action[href*="post-new.php?post_type=jet-woo-builder"]' ),
				$importButton = $( '#jet-woo-builder-import-trigger' );

			if ( ! $( '#wpbody-content .page-title-action' )[0] ) {
				return false;
			}

			$( '#wpbody-content' ).find( '.page-title-action:last' ).after( $importButton );

			$createButton.on( 'click', function( event ) {

				event.preventDefault();

				JetWooTemplatesPopups.uncheckAll();

				setTimeout( () => {
					JetWooTemplatesPopups.predesignedTemplates();
				}, 0 );

			} );

			let tippyProps = {
				arrow: true,
				placement: 'rtl' === window.document.dir ? 'bottom-end' : 'bottom-start',
				flipBehavior: 'clockwise',
				trigger: 'click',
				interactive: true,
				hideOnClick: true,
				theme: 'jet-woo-builder-light'
			}

			let createTemplatePopup = tippy( [ $createButton[0] ], {
				html: document.querySelector( '#jet-woo-builder-create-form' ),
				...tippyProps
			} );

			let importTemplatePopup = tippy( [ $importButton[0] ], {
				html: document.querySelector( '#jet-woo-builder-import-form' ),
				...tippyProps
			} );

		},

		predesignedTemplates: function() {

			let $select = $( '#template_type' ),
				value = $select.find( 'option:selected' ).val(),
				$subheading = $( '.jet-woo-builder-create-form__subheading' );

			if ( '' !== value ) {
				switch ( value ) {
					case 'jet-woo-builder-cart':
					case 'jet-woo-builder-thankyou':
					case 'jet-woo-builder-myaccount':
					case 'jet-woo-builder-checkout':
						$( '.predesigned-row' ).removeClass( 'is-active' );
						$subheading.hide();

						break;
					default:
						$( '.predesigned-row.template-' + value ).addClass( 'is-active' ).siblings().removeClass( 'is-active' );
						$subheading.show();

						break;
				}
			}

		},

		isCheckedItem: function() {

			let $this = $( this ),
				value = $this.find( 'input' ),
				checked = value.prop( 'checked' );

			JetWooTemplatesPopups.uncheckAll();

			if ( checked ) {
				$this.addClass( 'is--checked' );
			}

		},

		uncheckAll: function() {

			let $item = $( '.jet-woo-builder-create-form__label' );

			if ( $item.hasClass( 'is--checked' ) ) {
				$item.removeClass( 'is--checked' );
				$item.find( 'input' ).prop( 'checked', false );
			}

		},

		uncheckItem: function() {

			let $this = $( this ),
				label = $this.parent().find( '.jet-woo-builder-create-form__label' ),
				input = label.find( 'input' ),
				checked = input.prop( 'checked' );

			if ( checked ) {
				input.prop( 'checked', false );
				label.removeClass( 'is--checked' );
			}

		}

	};

	JetWooTemplatesPopups.init();

} )( jQuery );