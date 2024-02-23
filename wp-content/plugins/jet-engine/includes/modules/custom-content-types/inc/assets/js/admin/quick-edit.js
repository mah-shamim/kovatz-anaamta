( function( $ ) {

	class JetEngineQuickEdit {

		$trigger;
		$template;
		$row;
		$quickEditRow;

		_ID = 0;
		fieldsData = {};

		constructor() {

			this.$template = document.getElementById( 'jet_engine_cct_quick_edit_template' );

			$( document ).on( 'click.JetEngineQuickEdit', '.jet-engine-cct-quick-edit', ( event ) => {

				this.$trigger = $( event.target );

				this.cancelEdit();

				this.$row       = this.$trigger.closest( 'tr' ).get( 0 );
				this._ID        = this.$trigger.data( 'quick-edit-id' );
				this.fieldsData = this.$trigger.data( 'quick-edit-values' );

				this.showQuickEditPanel();

			} );

			$( document ).on( 'click.JetEngineQuickEdit', '.jet-engine-cct-quick-edit--cancel', ( event ) => {
				this.cancelEdit();
			} );

			$( document ).on( 'click.JetEngineQuickEdit', '.jet-engine-cct-quick-edit--save', ( event ) => {
				event.preventDefault();
				this.updateItem();
			} );

		}

		updateItem() {

			var form     = this.$quickEditRow.querySelector( 'form' ),
				formData = new FormData( form ),
				itemData = {},
				$spinner = form.querySelector( '.spinner' );

			$spinner.style.visibility = 'visible';
			this.cleanErrors();

			for ( var entry of formData.entries() ) {
				let name    = entry[0];
				let value   = entry[1];
				let isArray = false;

				if ( name.includes( '[]' ) ) {
					name    = name.replace( '[]', '' );
					isArray = true;
				}

				if ( isArray ) {
					if ( undefined === itemData[ name ] ) {
						itemData[ name ] = [];
					}
					itemData[ name ].push( value );
				} else {
					itemData[ name ] = value;
				}

			}

			$.ajax({
				url: window.ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: this.$trigger.data( 'quick-edit-action' ),
					cct_action: 'quick-edit',
					nonce: this.$trigger.data( 'quick-edit-nonce' ),
					item_id: this.$trigger.data( 'quick-edit-id' ),
					item_data: itemData,
				},
			}).done( ( response ) => {

				$spinner.style.visibility = 'hidden';

				if ( ! response.success ) {
					this.addError( response.data );
				} else {
					this.cancelEdit();
					this.$row.outerHTML = response.data;
				}

			} ).fail( ( jqXHR, textStatus, errorThrown ) => {
				$spinner.style.visibility = 'hidden';
				this.addError( errorThrown );
			} );

		}

		addError( text ) {
			var $errorsWrap = this.$quickEditRow.querySelector( '.notice-error' ),
				$errorsText = $errorsWrap.querySelector( '.error' );

			$errorsWrap.classList.remove( 'hidden' );
			$errorsText.innerHTML = text;
		}

		cleanErrors() {
			var $errorsWrap = this.$quickEditRow.querySelector( '.notice-error' ),
				$errorsText = $errorsWrap.querySelector( '.error' );

			$errorsWrap.classList.add( 'hidden' );
			$errorsText.innerHTML = '';
		}

		cancelEdit() {
			if ( this.$quickEditRow ) {
				this.$row.style.display = 'table-row';
				this.cleanErrors();
				this.$quickEditRow.remove();
			}
		}

		showQuickEditPanel() {

			var quickEditContent = document.importNode( this.$template.content, true );
			var quickEditRow     = document.createElement( 'tr' );

			this.$quickEditRow = quickEditRow;
			this.$row.after( this.$quickEditRow );
			this.$row.style.display = 'none';

			this.$quickEditRow.classList.add( 'quick-edit-row' );
			this.$quickEditRow.appendChild( quickEditContent );

			for ( const field in this.fieldsData ) {

				let fieldProps = this.fieldsData[ field ];

				switch ( fieldProps.type ) {

					case 'text':
					case 'date':
					case 'time':
					case 'number':
					case 'datetime-local':
						$( '[data-control-name="' + field + '"] input', this.$quickEditRow ).val( fieldProps.value );
						break;

					case 'textarea':
						$( '[data-control-name="' + field + '"] textarea',this.$quickEditRow ).val( fieldProps.value );
						break;

					case 'checkbox-raw':

						var $control = $( 'div[data-control-name="' + field + '"]', this.$quickEditRow );

						if ( fieldProps.value.length ) {

							for ( var i = 0; i < fieldProps.value.length; i++ ) {
								$control.find( 'input[value="' + fieldProps.value[ i ] + '"]' ).attr( 'checked', true );
							}

						}

						break;

					case 'radio':
						var $control = $( '[data-control-name="' + field + '"] input[value="' + fieldProps.value + '"]', this.$quickEditRow );

						if ( $control.length ) {
							$control.attr( 'checked', true );
						}

						break;

					case 'select':

						var $control = $( '[data-control-name="' + field + '"] select', this.$quickEditRow );

						if ( $control.attr( 'multiple' ) ) {
							if ( fieldProps.value.length ) {
								for ( var i = 0; i < fieldProps.value.length; i++ ) {
									$control.find( 'option[value="' + fieldProps.value[ i ] + '"]' ).attr( 'selected', true );
								}
							}
						} else {
							$control.val( fieldProps.value );
							$control.find( 'option[value="' + fieldProps.value + '"]' ).attr( 'selected', true );
						}

						break;


				}
			}

		}

	}

	new JetEngineQuickEdit();

}( jQuery ) );
