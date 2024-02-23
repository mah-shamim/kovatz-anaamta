( function( $, document, vars ) {

	'use strict';

	var last_ids = [],
		/* input element that should recieve the data from the conditions box */
		target,
		/* string copy of the current values in "target" while Conditions box is open */
		currentValues = '',
		/* conditions lightbox element */
		lightbox,
		overlay;

	function getKeys(e){
		var t=[];for(var n in e){if(!e.hasOwnProperty(n))continue;t.push(n);}return t;
	}

	function show_loader( container = document.body ) {
		let el = container.getElementsByClassName( 'themify_cm_loader' )[0];
		if ( ! el ) {
			el = document.createElement( 'div' );
			el.className = 'themify_cm_loader';
			container.prepend( el );
		}
		el.classList.add( 'busy' );
	}

	function hide_loader( container = document.body ) {
		const el = container.getElementsByClassName( 'themify_cm_loader' )[0];
		if ( el ) {
			el.classList.remove( 'busy' );
		}
	}

	/* update inputs inside lightbox */
	function update_inputs( container ) {
		if ( ! container ) {
			container = lightbox;
		}
		const inputs = container.querySelectorAll( 'input[type="checkbox"]' );
		for ( let i = 0; i < inputs.length; i++ ) {
			inputs[ i ].checked = currentValues.includes( inputs[ i ].name + '=on' );
		}
	}

	function getDocHeight() {
		var D = document;
		return Math.max(
			Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
			Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
			Math.max(D.body.clientHeight, D.documentElement.clientHeight)
		);
	}

	// Generate inner page for tab
	function getPaginationPages(e) {
		let type = this.dataset.type;
		let tab = lightbox.querySelector( this.getAttribute( 'href' ) );
		if ( ! type ) {
			/* look for sub tabs */
			tab.querySelector( '.inline-tabs .ui-tabs-active a' ).click();
			return;
		}

		if ( tab.getElementsByClassName( 'themify-visibility-items-page-1' )[0] ) {
			return;
		}

		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				action: 'themify_cm_create_inner_page',
				nonce : vars.nonce,
				type: type
			},
			beforeSend: function() {
				show_loader();
			},
			success: function(data){
				hide_loader();
				tab.firstElementChild.innerHTML = data;
				update_inputs( tab );
			}
		});
	}

	function add_conditions_button( $el, input_name, id, value ) {
		value = value || '';
		$el.find( '.locations-row-links' ).empty().html( '<input type="hidden" data-id="'+ id +'" name="'+ input_name +'" value=\''+ value +'\' /><a href="#" class="themify-cm-conditions">' + vars.lang.conditions + '</a> <a class="themify-cm-remove" href="#">x</a>' );
		return $el;
	};

	function add_assignment( $menu_row, new_id, selected_menu, condition_value ) {
		var clone = $menu_row.clone().removeClass( 'cm-location' );
		clone.find( '.menu-location-title' ).empty();
		var menu_id = clone.find( 'select' ).attr('name').match( /menu-locations\[(.*)\]/ )[1];
		if( new_id == null ) {
			if( typeof last_ids[menu_id] == 'undefined' ) {
				last_ids[menu_id] = parseInt( $( getKeys( vars.options[menu_id] ) ).last()[0] );
				if( ! $.isNumeric( last_ids[menu_id] ) )
					last_ids[menu_id] = 1;
			}
			new_id = last_ids[menu_id]++;
		}
		clone.find( 'select' ).find('option[value="0"]').text( vars.lang.disable_menu ).before( '<option value=""></option>' ).end().val( selected_menu ).attr( 'name', 'themify_cm[' + menu_id + '][' + new_id + '][menu]' );
		clone = add_conditions_button( clone, 'themify_cm[' + menu_id + '][' + new_id + '][condition]', new_id, condition_value );
		clone.insertBefore( jQuery( '.menu-locations tr[data-menu="'+ menu_id +'"]' ) );
		var menu_num = $('.menu-location-menus select').length,
			conditions_num = $('.cm-replacement-button').length + 1;
		clone.find('.menu-location-menus').attr('data-item', menu_id + new_id);
		if (menu_num === conditions_num) {
			$('.themify-cm-conditions-container:first').addClass('themify-cm-conditions-container-' + menu_id + new_id).data('item', menu_id + new_id);
		}
		if (menu_num > conditions_num) {
			$('.themify-cm-conditions-container:first').clone().removeClass().addClass('themify-cm-conditions-container themify-admin-lightbox tf_clearfix themify-cm-conditions-container-' + menu_id + new_id).data('item', menu_id + new_id).insertAfter('.themify-cm-conditions-container:last');
		}
	}

	/* remove the Edit & Use New Menu links */
	$('.menu-locations .locations-row-links').empty();

	$('body').on( 'click', '.themify-cm-conditions', function(e){
		e.preventDefault();

		target = this.previousElementSibling;
		currentValues = decodeURI( target.value );
		var top = $(document).scrollTop() + 80;
		if ( ! lightbox ) {
			show_loader();
			$.ajax({
				'type' : 'POST',
				url: ajaxurl,
				async: false,
				data: {
					action: 'themify_cm_get_conditions',
					nonce : vars.nonce
				},
				success( data ) {
					hide_loader();
					const tmp = document.createElement( 'template' );
					tmp.innerHTML = data;
					document.body.appendChild( tmp.content );
				}
			});
			lightbox = document.getElementById( 'themify-cm-conditions' );
			overlay = document.getElementById( 'themify-cm-overlay' );
		}

		update_inputs();

		overlay.style.display = 'block';
		$( lightbox )
			.show()
			.css('top', getDocHeight())
			.animate({
				'top': top
			}, 800);

		$( '#visibility-tabs', lightbox ).tabs();
		$( '#visibility-tabs .themify-visibility-inner-tabs', lightbox ).tabs();

		return false;
	} )
	.on('click', '#visibility-tabs .themify_cm_load_ajax, #visibility-tabs .inline-tabs a', getPaginationPages)

	.on('click', '.themify-visibility-pagination a.page-numbers', function( e ) {
		e.preventDefault();
		const paged = parseInt( this.getAttribute( 'href' ) ),
			container = this.closest( '.themify-visibility-items-inner' ),
			panels = container.getElementsByClassName( 'themify-visibility-items-page' );

		if ( ! container.getElementsByClassName( 'themify-visibility-items-page-' + paged )[0] ) {
			$.ajax({
				url: ajaxurl,
				async : false,
				type: 'post',
				data: {
					action: 'themify_cm_create_inner_page',
					type: ( this.closest( '.themify-visibility-inner-tab' ) ? this.closest( '.themify-visibility-inner-tabs' ) : lightbox ).querySelector( '.ui-tabs-active a' ).dataset.type,
					paged: paged,
					nonce : vars.nonce
				},
				beforeSend: function() {
					show_loader();
				},
				success: function(data){
					hide_loader();
					const tmp = document.createElement('template');
					tmp.innerHTML = data;
					update_inputs( tmp.content );
					container.appendChild( tmp.content );
				}
			});
		}
		for ( let i = 0; i < panels.length; i++ ) {
			panels[ i ].style.display = panels[ i ].classList.contains( 'themify-visibility-items-page-' + paged ) ? 'block' : 'none';
		}
	} )
	.on( 'change', '.themify-cm-conditions-container input[type="checkbox"]', function() {
		if ( this.checked ) {
			currentValues += '&' + this.name + '=on';
		} else {
			currentValues = currentValues.replace( this.name + '=on', '' );
		}
	} )

	.on( 'click', '.themify-cm-close, #themify-cm-overlay', function(e){
		e.preventDefault();
		$( lightbox ).animate({
			'top': getDocHeight()
		}, 800, function() {
			overlay.style.display = 'none';
			lightbox.style.display = 'none';
			currentValues = '';
			update_inputs();
		});

		return false;
	})
	.on( 'click', '.themify-mc-add-assignment', function(){
		add_assignment( $( '#locations-' + $(this).closest( 'tr' ).attr( 'data-menu' ) ).closest('tr') );
		return false;
	}).on('click', '.themify-cm-save', function(){
		/* save the data from conditions lightbox */
		target.value = currentValues;
		$('.menu-location-menus[data-item=' + target.dataset.id + ']').val( currentValues );
		/* close conditions lightbox */
		overlay.click();
		return false;
	}).on('click', '.themify-cm-remove', function(){
		$(this).closest( 'tr' ).fadeOut(function(){
			$(this).remove();
		});
		return false;
	}).on('click', '.themify-cm-conditions-container .uncheck-all', function( e ) {
		e.preventDefault();
		currentValues = '';
		update_inputs();
	})
	.on('click', '.themify-cm-conditions-container .themify_apply_all_conditions', function(){
		
	});

	window.addEventListener( 'load', function() {
		/* add the Menu Replacement button */
		$.each( vars.nav_menus, function( i, v ){
			$( '#locations-' + v ).closest('tr').after( '<tr class="cm-replacement-button" data-menu="'+ v +'"><td>&nbsp;</td><td><a href="#" class="themify-mc-add-assignment">'+ vars.lang.add_assignment +'</a></td></tr>' );
		} );

		/* add the previously saved menu replacements */
		$.each( vars.options, function( menu, assignments ){
			if( typeof assignments == 'object' ) {
				$.each( assignments, function( id, value ){
					add_assignment( $( '#locations-' + menu ).closest( 'tr' ), id, value['menu'], value['condition'] );
					last_ids[menu] = ++id;
				} );
			}
		});
	} );

} )( jQuery, document, themify_cm );