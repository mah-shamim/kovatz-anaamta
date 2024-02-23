var JetQueryDialog = (function () {

	"use strict";

	function JetQueryDialog( options ) {

		this.$overlay = null;
		this.$content = null;
		this.$applyButton = null;
		this.$cancelButton = null;
		this.$controls = {};

		this.options = {
			contentType: null,
			listing: null,
			fields: [],
			fetchFields: true,
			fetchPath: null,
			popupTarget: null,
			resultTarget: null,
			trigger: null,
			value: {},
			onSend: false,
			hasOffset: true,
		};

		this.repeaters = [];

		var baseClass   = 'jet-query-dialog';
		var domInstance = null;
		var controls    = [
			{
				name: 'offset',
				label: 'Offset',
				type: 'input',
				inputType: 'number',
				layout: 'row',
				min: 0,
				max: 200,
			},
			{
				label: 'Status',
				name: 'status',
				type: 'select',
				layout: 'row',
				options: [
					{
						value: '',
						label: 'Any',
					},
					{
						value: 'publish',
						label: 'Publish',
					},
					{
						value: 'draft',
						label: 'Draft',
					},
				],
			},
			{
				label: 'Order',
				name: 'order',
				type: 'order_repeater',
			},
			{
				label: 'Query',
				name: 'args',
				type: 'args_repeater',
			},
		];

		for ( var k in options ) {
			if ( this.options.hasOwnProperty( k ) ) {
				this.options[ k ] = options[ k ];
			}
		}

		if ( ! this.options.value ) {
			this.options.value = {};
		}

		if ( ! this.options.popupTarget ) {
			this.options.popupTarget = document.querySelector( 'body' );
		}

		this.setOptions = function( newOptions ) {

			newOptions = newOptions || {};

			for ( var option in newOptions ) {
				this.options[ option ] = newOptions[ option ];
			}

		}

		this.setValue = function( newVal ) {
			newVal = newVal || {};
			this.options.value = JSON.parse( JSON.stringify( newVal ) );
		}

		this.createDOMInstance = function() {

			this.$el      = document.createElement( 'div' );
			this.$overlay = document.createElement( 'div' );
			this.$content = document.createElement( 'div' );

			this.$el.className = baseClass;
			this.$overlay.className = baseClass + '__overlay';
			this.$content.className = baseClass + '__content';

			this.$el.appendChild( this.$overlay );
			this.$el.appendChild( this.$content );

			this.options.popupTarget.appendChild( this.$el );

			this.$overlay.addEventListener( 'click', this.remove.bind( this ) );

		};

		this.getOrderByOptions = function() {

			let result = this.options.fields || [],
				orderByOptions = window.jetQueryDialogConfig.orderByOptions || [];

			return result.concat( orderByOptions );
		};

		this.createControls = function() {

			//console.log( this.options.value );

			controls.forEach( control => {

				let val = this.options.value[ control.name ];
				let children;
				let repeater;

				if ( ! this.options.hasOffset && 'offset' === control.name ) {
					return;
				}

				switch ( control.type ) {

					case 'order_repeater':

						children = [
							{
								name: 'orderby',
								type: 'select',
								label: 'Order By',
								options: this.getOrderByOptions(),
								width: 33,
							},
							{
								name: 'order',
								type: 'select',
								label: 'Order',
								options: [
									{
										value: 'asc',
										label: 'ASC',
									},
									{
										value: 'desc',
										label: 'DESC',
									},
								],
								width: 33,
							},
							{
								name: 'type',
								type: 'select',
								label: 'Type',
								options: window.jetQueryDialogConfig.dataTypes,
								width: 33,
							},
						];

						repeater = this.newRepeater( control, children, val, this );
						this.$content.appendChild( repeater.getRepeater() );

						break;

					case 'args_repeater':

						children = [
							{
								name: 'field',
								label: 'Field',
								type: 'select',
								options: this.options.fields,
								width: 25,
							},
							{
								name: 'operator',
								type: 'select',
								label: 'Operator',
								options: window.jetQueryDialogConfig.operators,
								width: 25,
							},
							{
								name: 'value',
								type: 'textarea',
								label: 'Value',
								description: 'Separate multiple values with commas',
								width: 25,
							},
							{
								name: 'type',
								type: 'select',
								label: 'Type',
								options: window.jetQueryDialogConfig.dataTypes,
								width: 25,
							},
						];

						repeater = this.newRepeater( control, children, val, this );
						this.$content.appendChild( repeater.getRepeater() );

						break;

					case 'select':
						control.value = val;
						this.$content.appendChild( this.newSelect( control ) );
						break;

					case 'input':
						control.value = val;
						this.$content.appendChild( this.newInput( control ) );
						break;

					case 'textarea':
						control.value = val;
						this.$content.appendChild( this.newTextarea( control ) );
						break;
				}
			} );

			let actions       = document.createElement( 'div' );
			actions.className = 'jet-query-actions';

			this.$applyButton             = document.createElement( 'button' );
			this.$applyButton.className   = 'jet-query-action-done elementor-button elementor-button-default elementor-button-success';
			this.$applyButton.type        = 'button';
			this.$applyButton.textContent = 'Apply';

			this.$applyButton.addEventListener( 'click', this.sendResult.bind( this ) );

			actions.appendChild( this.$applyButton );

			this.$cancelButton           = document.createElement( 'button' );
			this.$cancelButton.className = 'jet-query-action-cancel';
			this.$cancelButton.type      = 'button';
			this.$cancelButton.innerHTML = '&times; Cancel';

			this.$cancelButton.addEventListener( 'click', this.remove.bind( this ) );

			actions.appendChild( this.$cancelButton );

			this.$content.appendChild( actions );

		};

		this.sendResult = function() {

			let result = {};

			for ( var name in this.$controls ) {
				let data = this.$controls[ name ];

				if ( ! data.children ) {
					result[ name ] = data.control.value;
				} else {
					result[ name ] = [];

					for ( var i = 0; i < data.children.length; i++ ) {

						let childRow  = data.children[ i ];
						let childItem = {};

						for ( var j = 0; j < childRow.children.length; j++ ) {
							let childData = childRow.children[ j ];
							childItem[ childData.name ] = childData.control.value;
						}

						result[ name ].push( childItem )

					};

				}

			}

			let changeEvent = new Event( 'change' );
			let inputEvent = new Event( 'input' );
			let docEvent = new Event( 'jet-query-apply', { queryData: result, queryDialog: this } );

			if ( this.options.resultTarget ) {

				this.options.resultTarget.value = JSON.stringify( result );

				this.options.resultTarget.dispatchEvent( inputEvent );
				this.options.resultTarget.dispatchEvent( changeEvent );

				document.dispatchEvent( docEvent );

			}

			if ( this.options.onSend ) {
				this.options.onSend( JSON.stringify( result ), inputEvent );
			}

			this.remove();
		};

		this.newRepeater = function( control, children, value, parent ) {

			var Repeater = function( control, children, value, parent ) {

				let $el     = document.createElement( 'div' );
				let $rows   = document.createElement( 'div' );
				let $button = document.createElement( 'button' );
				let $parent = parent;
				let name    = control.name;

				this.newRepeaterRow = function( name, children, value ) {

					let wrapper = this.newRepeaterRowWrapper( name );
					let columns = document.createElement( 'div' );

					columns.className = 'jet-query-repeater-row__columns';

					for ( var i = 0; i < children.length; i++ ) {

						let child = children[ i ];

						if ( value && value[ child.name ] ) {
							child.value = value[ child.name ];
						}

						columns.appendChild( this.newRepeaterColumn( children[ i ], wrapper ) );

					};

					let removeButton = document.createElement( 'button' );

					removeButton.className = 'jet-query-repeater-row__remove elementor-button elementor-button-default';
					removeButton.type      = 'button';
					removeButton.innerHTML = '&times;'

					wrapper.appendChild( columns );
					wrapper.appendChild( removeButton );

					removeButton.addEventListener( 'click', this.removeRow.bind( this ) );

					return wrapper;
				};

				this.removeRow = function( event ) {

					let row = event.target.parentNode;
					let rowIndex = $parent.findRow( row, $parent.$controls[ name ].children );

					if ( false !== rowIndex ) {
						$parent.$controls[ name ].children.splice( rowIndex, 1 );
					}

					event.target.removeEventListener( 'click', this.removeRow );
					event.target.parentNode.remove();
				};

				this.newRepeaterRowWrapper = function( name ) {

					let wrapper = document.createElement( 'div' );

					wrapper.className    = 'jet-query-repeater-row';
					wrapper.dataset.name = name;

					return wrapper;

				};

				this.newRepeaterColumn = function( data, row ) {

					let column = document.createElement( 'div' );
					let width  = data.width || 100;

					column.className = 'jet-query-column jet-query-column-' + width;

					data.parent    = $el;
					data.parentRow = row;

					switch ( data.type ) {
						case 'input':
							column.appendChild( $parent.newInput( data ) );
							break;

						case 'select':
							column.appendChild( $parent.newSelect( data ) );
							break;

						case 'textarea':
							column.appendChild( $parent.newTextarea( data ) );
							break;
					}

					return column;

				};

				this.newRow = function( node ) {
					$rows.appendChild( node );
				};

				this.addRow = function() {

					var newRow = [];

					for ( var i = 0; i < children.length; i++ ) {
						let item = children[ i ];
						item.value = null;
						newRow.push( item );
					}

					this.newRow( this.newRepeaterRow( name, newRow ) );
				};

				this.getRepeater = function() {
					return $el;
				};

				this.destroy = function() {
					$button.removeEventListener( 'click', this.addRow );
				};

				$el.className = 'jet-query-repeater';
				$el.dataset.name = name;

				$rows.className = 'jet-query-repeater-rows';

				$button.className = 'jet-query-repeater-add elementor-button elementor-button-default';
				$button.type = 'button';
				$button.textContent = '+ New Item';

				if ( value && value.length ) {
					for ( var i = 0; i < value.length; i++ ) {
						this.newRow( this.newRepeaterRow( control.name, children, value[ i ] ) );
					}
				}

				if ( control.label ) {
					let label = document.createElement( 'div' );
					label.className = 'jet-query-repeater-label';
					label.textContent = control.label;
					$el.appendChild( label );
				}

				$el.appendChild( $rows );
				$el.appendChild( $button );

				$button.addEventListener( 'click', this.addRow.bind( this ) );

			};

			return new Repeater( control, children, value, parent );

		};

		this.newInput = function( data ) {

			let field   = this.newField( data );
			let control = document.createElement( 'input' );
			let controlWrap = document.createElement( 'div' );
			let type = 'text';

			if ( data.inputType ) {
				type = data.inputType;
			}

			control.type = type;
			control.setAttribute( 'name', data.name );

			if ( data.value ) {
				control.value = data.value;
			}

			switch ( data.inputType ) {
				case 'number':
					control.setAttribute( 'min', data.min );
					control.setAttribute( 'max', data.max );
					break;
			}

			this.storeControl( control, data );

			controlWrap.className = 'jet-query-control';
			controlWrap.appendChild( control );

			field.appendChild( controlWrap );

			return field;
		};

		this.newTextarea = function( data ) {

			let field   = this.newField( data );
			let control = document.createElement( 'textarea' );
			let controlWrap = document.createElement( 'div' );

			control.setAttribute( 'name', data.name );
			controlWrap.className = 'jet-query-control';

			if ( data.value ) {
				control.value = data.value;
			}

			controlWrap.appendChild( control );

			this.storeControl( control, data );

			field.appendChild( controlWrap );

			return field;
		};

		this.newSelect = function( data ) {

			let field       = this.newField( data );
			let control     = document.createElement( 'select' );
			let controlWrap = document.createElement( 'div' );

			control.setAttribute( 'name', data.name );
			controlWrap.className = 'jet-query-control';

			if ( data.value ) {
				control.value = data.value;
			}

			for ( var i = 0; i < data.options.length; i++ ) {
				let option = data.options[ i ];
				let optionNode = document.createElement( 'option' );
				optionNode.setAttribute( 'value', option.value );

				if ( data.value && option.value === data.value ) {
					optionNode.setAttribute( 'selected', true );
				}

				optionNode.textContent = option.label;
				control.appendChild( optionNode );
			};

			this.storeControl( control, data );

			controlWrap.appendChild( control );

			field.appendChild( controlWrap );

			return field;
		};

		this.storeControl = function( $control, data ) {

			if ( data.parent && data.parentRow ) {

				let parentName = data.parent.dataset.name;

				if ( ! this.$controls[ parentName ] ) {
					this.$controls[ parentName ] = {
						control: data.parent,
						children: [],
					};
				}

				let rowIndex = this.findRow( data.parentRow, this.$controls[ parentName ].children );

				if ( false === rowIndex ) {
					this.$controls[ parentName ].children.push( {
						control: data.parentRow,
						children: [ {
							name: data.name,
							control: $control,
						} ],
					} );
				} else {
					this.$controls[ parentName ].children[ rowIndex ].children.push( {
						name: data.name,
						control: $control,
					} );
				}

			} else {
				this.$controls[ data.name ] = {
					control: $control
				};
			}

		};

		this.findRow = function( row, children ) {

			for ( var i = 0; i < children.length; i++ ) {
				if ( row === children[ i ].control ) {
					return i;
				}
			}

			return false;

		};

		this.newField = function( data ) {

			let field = document.createElement( 'div' );
			let label = document.createElement( 'label' );

			field.className = 'jet-query-field';
			label.className = 'jet-query-label';

			if ( data.inputType ) {
				field.className += ' jet-query-field--input-type-' + data.inputType;
			}

			if ( data.layout ) {
				field.className += ' jet-query-field--layout-' + data.layout;
			}

			if ( data.label ) {
				label.textContent = data.label;
			}

			field.appendChild( label );

			if ( data.description ) {
				let desc = document.createElement( 'div' );

				desc.className   = 'jet-query-field-description';
				desc.textContent = data.description;

				field.appendChild( desc );
			}

			return field;
		};

		this.errorMessage = function( notices ) {

			let errors = document.createElement( 'div' );
			errors.className = 'jet-query-errors';

			for ( var i = 0; i < notices.length; i++ ) {

				let notice = notices[ i ];
				let error  = document.createElement( 'div' );

				error.className = 'jet-query-error';
				error.innerHTML = notice.message;

				errors.appendChild( error );

			};

			this.$cancelButton           = document.createElement( 'button' );
			this.$cancelButton.className = 'jet-query-errors-cancel elementor-button elementor-button-default';
			this.$cancelButton.type      = 'button';
			this.$cancelButton.innerHTML = '&times; Cancel';

			this.$cancelButton.addEventListener( 'click', this.remove.bind( this ) );

			errors.appendChild( this.$cancelButton );

			this.$content.appendChild( errors );
		};

		this.fetchFields = function() {

			if ( ! this.options.fields || ! this.options.fetchPath ) {
				return;
			}

			if ( this.options.fields.length ) {
				this.createControls();
				return;
			}

			let queryKey = false;
			let queryVal = false;

			//console.log( this.options );

			if ( this.options.contentType ) {
				queryKey = 'type';
				queryVal = this.options.contentType;
			} else if ( this.options.listing ) {
				queryKey = 'listing';
				queryVal = this.options.listing;
			}

			if ( ! queryKey && ! queryVal ) {
				return;
			}

			var fetched = false;

			wp.apiFetch( {
				method: 'get',
				path: this.options.fetchPath + '?' + queryKey + '=' + queryVal,
			} ).then( ( response ) => {

				if ( response.success && response.fields ) {
					this.options.fields = response.fields;
					fetched = true;
				} else {
					if ( response.notices.length ) {
						this.errorMessage( response.notices );
					}
				}

			} ).then( ( response ) => {
				if ( fetched ) {
					this.createControls();
				}
			} ).catch( ( e ) => {
				console.log( e );
				this.errorMessage( [ e ] );
			} );

		};

		this.create = function() {

			if ( this.options.resultTarget && this.options.resultTarget.value ) {

				let value;

				try {
					value = JSON.parse( this.options.resultTarget.value );
				} catch ( e ) {
					console.trace();
					value;
				}

				this.options.value = value;

				if ( ! this.options.value || 'object' !== typeof this.options.value || null === this.options.value ) {
					this.options.value = {};
				}

			}

			this.createDOMInstance();
			this.fetchFields();
		}

		this.init = function() {

			if ( this.options.trigger ) {
				this.options.trigger.addEventListener( 'click', ( e ) => {
					this.create();
				} );
			}

		};

		this.remove = function() {

			for ( var i = 0; i < this.repeaters.length; i++ ) {
				this.repeaters[ i ].destroy();
			};

			this.repeaters = [];

			if ( this.$overlay ) {
				this.$overlay.removeEventListener( 'click', this.remove );
			}

			if ( this.$applyButton ) {
				this.$applyButton.removeEventListener( 'click', this.sendResult );
			}

			if ( this.$cancelButton ) {
				this.$cancelButton.removeEventListener( 'click', this.remove );
			}

			this.$controls = {};

			if ( this.$el ) {
				this.$el.remove();
			}
		},

		this.init();

	}

	return JetQueryDialog;

})();

window.JetQueryDialog = JetQueryDialog;

/*var $el = document.getElementById( 'test_dialog_trigger' );
var $result = document.getElementById( 'test_dialog' );
var value;

if ( $result.value ) {
	value = JSON.parse( $result.value );
}

window.testDialog = new JetQueryDialog( {
	listing: 897,
	trigger: $el,
	resultTarget: $result,
	fetchPath: $el.dataset.fetchPath,
	value: value,
} );*/

// Elementor integration
jQuery( window ).on( 'elementor:init', function() {

	dialogView = window.elementor.modules.controls.BaseData.extend( {

		jetDialog: null,

		onReady: function() {

			var self = this;
			var $trigger = self.el.querySelector( '.jet-query-trigger' );
			var $result = self.ui.input[0];

			self.jetDialog = new JetQueryDialog({
				trigger: $trigger,
				listing: self.elementSettingsModel.get( 'lisitng_id' ),
				resultTarget: $result,
				fetchPath: $trigger.dataset.fetchPath,
				value: self.model.get( 'value' ),
				onSend: function( value, inputEvent ) {
					self.updateElementModel( value, self.ui.input );
					self.triggerMethod( 'input:change', inputEvent );

				}
			});

		},

		onBeforeDestroy: function() {
			this.jetDialog.remove();
			this.$el.remove();
		}

	} );

	window.elementor.addControlView( 'jet_query_dialog', dialogView );

} );
