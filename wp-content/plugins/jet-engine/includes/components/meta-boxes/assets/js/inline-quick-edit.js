(function($) {

	var $wp_inline_edit = inlineEditPost.edit,
		$document       = $( document );

	$document.on(
		'input',
		'.inline-edit-row .cx-radio-custom-value, .inline-edit-row .cx-checkbox-custom-value',
		function() {
			var $this = $( this ),
				type  = 'radio',
				val   = $this.val();

			if ( $this.hasClass( 'cx-checkbox-custom-value' ) ) {
				type = 'checkbox';
			}

			$this.closest( '.cx-' + type + '-item' ).find( '.cx-' + type + '-input' ).val( val ).attr( 'checked', true );

		}
	);

	$document.on( 'click', '.inline-edit-row .cx-checkbox-add-button', function() {

		var $this = $( this );
		var template = document.getElementById( 'cx_checkbox_custom_template_' + $this.data( 'index' ) );
		var newContent = document.importNode( template.content, true );

		$this.before( newContent );
		$this.prev().find( 'input[type="text"]' ).focus();

	} );

	inlineEditPost.edit = function( id ) {

		$wp_inline_edit.apply( this, arguments );

		var $post_id = 0;

		if ( typeof( id ) == 'object' ) {
			$post_id = parseInt( this.getId( id ) );
		}


		if ( $post_id > 0 ) {

			var $edit_row = $( '#edit-' + $post_id ),
				$post_row = $( '#post-' + $post_id ),
				$controls = $( '[data-jet-engine-quick-edit-val]', $post_row );

			$controls.each( function() {

				var $this = $( this ),
					name  = $this.data( 'jet-engine-quick-edit-val' ),
					type  = $this.data( 'jet-engine-quick-edit-type' ),
					val   = JSON.parse( $this.text() );

				switch ( type ) {

					case 'text':
					case 'date':
					case 'time':
					case 'stepper':
					case 'number':
					case 'datetime-local':
						$( '[data-control-name="' + name + '"] input', $edit_row ).val( val );
						break;

					case 'textarea':
						$( '[data-control-name="' + name + '"] textarea', $edit_row ).val( val );
						break;

					case 'checkbox-raw':

						var $control    = $( 'div[data-control-name="' + name + '"]', $edit_row ),
							options     = $control.find( '.cx-ui-control-container' ).data( 'options' ),
							allowCustom = $control.find( '.cx-ui-control-container' ).data( 'allow-custom' );

						if ( val.length ) {

							for ( var i = 0; i < val.length; i++ ) {
								if ( options.includes( val[ i ] ) ) {
									$control.find( 'input[value="' + val[ i ] + '"]' ).attr( 'checked', true );
								} else if ( allowCustom ) {

									var $button = $( '.cx-checkbox-add-button', $control ),
										template = document.getElementById( 'cx_checkbox_custom_template_' + $button.data( 'index' ) ),
										newContent = document.importNode( template.content, true );

									$button.before( newContent );

									$button.prev().find( 'input[type="checkbox"]' ).val( val[ i ] );
									$button.prev().find( 'input[type="text"]' ).val( val[ i ] );
								}
							}

						}

						break;

					case 'radio':
						var $control = $( '[data-control-name="' + name + '"] input[value="' + val + '"]', $edit_row );

						if ( $control.length ) {
							$control.attr( 'checked', true );
						} else {
							var $cInput   = $( '[data-control-name="' + name + '"] .cx-radio-custom-value', $edit_row );

							if ( $cInput.length ) {
								$cInput.val( val );
								$cInput.closest( '.cx-radio-item' ).find( '.cx-radio-input' ).val( val ).attr( 'checked', true );
							}
						}
						break;

					case 'select':

						var $control = $( '[data-control-name="' + name + '"] select', $edit_row );

						if ( $control.attr( 'multiple' ) ) {
							if ( val.length ) {
								for ( var i = 0; i < val.length; i++ ) {
									$control.find( 'option[value="' + val[ i ] + '"]' ).attr( 'selected', true );
								}
							}
						} else {
							$control.val( val );
							$control.find( 'option[value="' + val + '"]' ).attr( 'selected', true );
						}

						break;


				}


			} );

		}
	};

})(jQuery);
