const eventHub = new Vue(),
	buildQuery = function ( params ) {
		return Object.keys( params ).map(function ( key ) {
			return key + '=' + params[key];
		} ).join( '&' );
	};

Vue.directive( 'click-outside', {
	bind: function ( el, binding, vnode ) {
		el.clickOutsideEvent = function ( event ) {

			if ( ! ( el == event.target || el.contains( event.target ) ) ) {
				vnode.context[ binding.expression ]( event );
			}
		};
		document.body.addEventListener( 'click', el.clickOutsideEvent )
	},
	unbind: function ( el ) {
		document.body.removeEventListener( 'click', el.clickOutsideEvent )
	}
} );

Vue.component( 'jet-search-suggestions-settings', {

	template: '#jet-dashboard-jet-search-suggestions-settings',

	data: function() {
		return {
			settings: window.JetSearchSettingsConfig,
			generalSettings: {
				records_limit: "5",
				use_session: false,
			},
			isLoading: true,
			itemsList: [],
			parentsList: [],
			totalItems: 0,
			offset: 0,
			perPage: 30,
			onPage: 0,
			pageNumber: 1,
			//currentQueryByName: '',
			currentFilterQuery: {},
			columnsIDs: [
				'id',
				'type',
				'name',
				'weight',
				'actions'
			],
			labels: {
				id: 'ID',
				type: 'Type',
				name : 'Name',
				weight: 'Weight',
				actions: 'Actions'
			},
			curentSort: 'id',
			sortBy: {
				orderby: 'id',
				order: 'ASC',
			},
			notSortable: [
				'type',
				'actions',
			],
			popUpState: '',
			popUpContent: {},
			popUpShow: false,
			nameError: false
		};
	},
	methods: {
		getItemLabel: function ( key ) {
			let lable = this.labels[key] ? this.labels[key] : key;
			return lable;
		},
		getItemColumnValue: function ( item, key ) {
			if ( 'parent' != key ) {
				return item[key];
			}
		},
		sortColumn: function ( column ) {
			if ( this.notSortable.includes( column ) ) {
				return false;
			}

			this.curentSort = column;

			let newSortBy = {
				orderby: column,
				order: "DESC" === this.sortBy.order ? "ASC" : "DESC"
			};

			this.sortBy = newSortBy;

			this.getItems( this.currentFilterQuery );
		},
		getItems : function( filterQuery = {} ) {

			this.isLoading = true;

			let query = buildQuery( {
				per_page: this.perPage,
				offset: this.offset,
				sort: JSON.stringify( this.sortBy ),
				filter: JSON.stringify( filterQuery )
			} ),
			queryPath = `${this.settings.getSuggestionsUrl}?${query}`;

			wp.apiFetch( {
				method: 'GET',
				url: queryPath,
			} ).then( ( response ) => {
				let item_list = response.items_list;

				item_list.map( item => {
					item['parent'] = '0' != item['parent'] ? item['parent'] : "0";
				} );

				this.itemsList   = item_list;
				this.totalItems  = response.total;
				this.onPage      = response.on_page;

				if ( 0 < response.parents_list.length && response.success ) {
					this.parentsList.push( ...response.parents_list );
					this.parentsList = new Set( this.parentsList );
					this.parentsList = [...this.parentsList];
				}

				this.isLoading = false;

			} ).catch( function ( e ) {
				eventHub.$CXNotice.add( {
					message: e.message,
					type: 'error',
					duration: 7000,
				} );
			} );
		},
		addItem: function ( content ) {

			this.isLoading = true;

			wp.apiFetch( {
				method: 'POST',
				url: this.settings.addSuggestionUrl,
				data: content,
			} ).then( response => {
				let message = response.data,
					type = !response.success ? 'error' : 'success';

				eventHub.$CXNotice.add( {
					message: message,
					type: type,
					duration: 7000,
				} );

				if ( response.success ) {
					this.cancelPopup();
					this.nameError = false;
				} else {
					this.nameError = true;
				}

				this.getItems( this.currentFilterQuery );

			} ).catch( function ( e ) {
				eventHub.$CXNotice.add({
					message: e.message,
					type: 'error',
					duration: 7000,
				} );
			} );
		},
		updateItem: function ( content ) {
			this.isLoading = true;

			wp.apiFetch( {
				method: 'POST',
				url: this.settings.updateSuggestionUrl,
				data: content,
			} ).then( response => {
				let message = response.data,
					type    = !response.success ? 'error' : 'success';

				eventHub.$CXNotice.add( {
					message: message,
					type: type,
					duration: 7000,
				} );

				if ( response.success ) {
					this.cancelPopup();
					this.nameError = false;
				} else {
					this.nameError = true;
				}

				this.getItems( this.currentFilterQuery );

			} ).catch( function ( e ) {
				eventHub.$CXNotice.add( {
					message: e.message,
					type: 'error',
					duration: 7000,
				} );

				this.isLoading = false;
			} );
		},
		deleteItem: function ( content ) {
			this.isLoading = true;

			wp.apiFetch( {
				method: 'POST',
				url: this.settings.deleteSuggestionUrl,
				data: content,
			} ).then( response => {
				let message = response.data,
					type    = !response.success ? 'error' : 'success';

				eventHub.$CXNotice.add({
					message: message,
					type: type,
					duration: 7000,
				} );

				this.getItems();

			} ).catch( function ( e ) {
				eventHub.$CXNotice.add( {
					message: e.message,
					type: 'error',
					duration: 7000,
				} );

				this.isLoading = false;
			} );
		},
		changePage: function( value ) {
			this.offset     = this.perPage * (value - 1);
			this.pageNumber = value;

			this.getItems( this.currentFilterQuery );
		},
		changePerPage: function( value ) {
			this.offset     = 0;
			this.perPage    = value;
			this.pageNumber = 1;

			this.getItems( this.currentFilterQuery );
		},
		classColumn: function ( column ) {
			return {
				'list-table-heading__cell-content': true,
				'list-table-heading__cell-clickable': !this.notSortable.includes(column),
				'jet-search-suggestions-active-column': column === this.curentSort,
				'jet-search-suggestions-active-column-asc': column === this.curentSort && "DESC" === this.sortBy.order,
				'jet-search-suggestions-active-column-desc': column === this.curentSort && "ASC" === this.sortBy.order,
			};
		},
		callPopup: function ( state, item ) {
			this.popUpState   = state;
			this.popUpContent = item;
			this.popUpShow    = true;
		},
		cancelPopup: function () {
			this.popUpShow    = false ;
			this.popUpContent = {};
		},
		popUpActions: function( action, content ) {
			switch ( action ) {
				case 'add':
					this.addItem( content );
					break;
				case 'update':
					this.updateItem( content );
					break;
				case 'delete':
					this.deleteItem( content );
					break;
			}
		},
		// updateParentList: function( newList ) {
		// 	this.parentList = newList;
		// 	this.$emit('parentListUpdated', newList );
		// },
		clearFilter: function() {
			this.currentFilterQuery = {};
			this.getItems( this.currentFilterQuery );
		},
		updateFilters: function( filter ) {

			this.currentFilterQuery = filter;

			if ( '' === filter['search'] && '' === filter['searchType'] ) {
				this.offset     = 0;
				this.pageNumber = 1;
			}

			this.getItems( this.currentFilterQuery );
		},
		getSettings: function() {
			let ajaxUrl          = this.settings.ajaxUrl,
				nonce            = this.settings.nonce,
				$this = this;

			jQuery.ajax( {
				url: ajaxUrl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'suggestions_get_settings',
					nonce: nonce,
				},
			} ).done( function( response ) {
				let settings = response.data.settings;

				settings['use_session'] = JSON.parse( settings['use_session'] );

				$this.generalSettings = settings;

			} ).fail( function( jqXHR, textStatus, errorThrown ) {
				eventHub.$CXNotice.add({
					message: jqXHR.statusText,
					type: 'error',
					duration: 7000,
				} );
			} );
		},
		saveSettings : function( settings ) {
			let ajaxUrl          = this.settings.ajaxUrl,
				nonce            = this.settings.nonce;

			jQuery.ajax( {
				url: ajaxUrl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'suggestions_save_settings',
					nonce: nonce,
					settings: settings,
				},
			} ).done( function( response ) {
				let message = response.data.message,
					type    = !response.success ? 'error' : 'success';

				if ( response.success ) {
					this.generalSettings = settings;
				}

				eventHub.$CXNotice.add({
					message: message,
					type: type,
					duration: 7000,
				} );

			} ).fail( function( jqXHR, textStatus, errorThrown ) {
				eventHub.$CXNotice.add({
					message: jqXHR.statusText,
					type: 'error',
					duration: 7000,
				} );
			} );
		},
	},
	mounted: function() {
		this.getSettings();
		this.getItems();
	}
} );

//ADD NEW

Vue.component( 'jet-search-add-new-suggestion', {
	template: '#jet-dashboard-jet-search-suggestions-add-new',
	data: function () {
		return {
			item: {
				name : '',
				weight: 1,
				parent: "0",
			}
		}
	},
	methods: {
		callPopup: function ( state = false, item = false ) {
			this.$emit( 'callPopup', state, item );

			this.item = {
				name : '',
				weight: 1,
				parent: "0",
			}
		},
	}
} );

// PAGINATION

Vue.component( 'jet-search-suggestions-pagination', {
	template: '#jet-dashboard-jet-search-suggestions-pagination',
	props: {
		totalItems: {
			type: Number
		},
		perPage: {
			type: Number
		},
		pageNumber: {
			type: Number
		},
		offset: {
			type: Number
		},
		onPage: {
			type: Number
		}
	},
	computed: {
		currentPerPage: {
			get() {
				return this.perPage;
			},
			set( value ) {
				let newValue = Math.abs( value );

				newValue = newValue <= 0 || newValue > 1000 ? 25 : parseInt( newValue );

				this.$emit( 'changePerPage', newValue );
			}
		},
		currentPageNumber: {
			get() {
				return this.pageNumber;
			}
		}
	},
	methods: {
		perPageInfo: function () {
			let total = this.totalItems,
				from  = total ? this.offset + 1 : 0,
				to    = total ? this.offset + this.onPage : 0;

			return __( `Showing results ${from} - ${to} of ${total}`, 'jet-search' );
		},
		changePage: function ( value ) {
			this.$emit( 'changePage', value );
		},
	},
} );

//POPUP

Vue.component( 'jet-search-suggestions-popup', {
	template: '#jet-dashboard-jet-search-suggestions-popup',
	props: {
		state: {
			validator( value ) {
				return [ 'update', 'delete', 'new', '' ].includes( value );
			}
		},
		popUpContent: {
			type: Object
		},
		popUpShow: {
			type: Boolean
		},
		parentsList: {
			type: Array
		},
		nameError: {
			type: Boolean
		}
	},
	data: function () {
		return {
			settings: window.JetSearchSettingsConfig,
			isShow: this.popUpShow,
			popUpState: this.state,
			addButtonDisabled: false,
			updateButtonDisabled: false,
			columns: [
				'id',
				'name',
				'weight',
				'parent',
				'actions'
			],
			labels: {
				id: 'ID',
				name : 'Name',
				weight: 'Weight',
				parent: 'Parent',
				actions: 'Actions'
			},
			content: [],
			currentContent: [],
			preparedContent: [],
			contentParents: [],
			currentParentName: '',
			currentParentID: '',
			contentParentText: '',
			optionsList: [],
			items: [],
			notFoundMessage: 'Parent does not exist. Please enter a valid value',
			inputNameError: false,
			placeholder: {
				name: 'Enter from 1 to 120 char.',
				parent: ''
			},
		}
	},
	watch: {
		popUpShow( value ) {
			this.isShow = value;
		},
		state( value ) {
			this.popUpState = value;
		},
		popUpContent( value ) {
			this.content              = value ? JSON.parse( JSON.stringify( value ) ) : [];
			this.currentContent       = value ? JSON.parse( JSON.stringify( value ) ) : [];
			this.contentParents       = value.parent ? JSON.parse( JSON.stringify( value.parent ) ) : [];
			this.preparedContent      = [];
			this.currentParentName    = '';
			this.currentParentID      = '';
			this.addButtonDisabled    = true;
			this.updateButtonDisabled = true;

			if ( "0" != this.contentParents ) {
				this.placeholder[ 'parent' ] = '';
			} else {
				this.placeholder[ 'parent' ]     = "Enter at least 2 char."
				this.preparedContent[ 'parent' ] = "0"
			}
		},
		nameError( value ) {
			this.inputNameError = value;
		}
	},
	methods: {
		getItemLabel: function ( key ) {
			let lable = this.labels[key] ? this.labels[key] : key;
			return lable;
		},
		popUpActions: function( action, content ) {
			this.$emit( 'popUpActions', action, content );
		},
		cancelPopup: function () {
			this.$emit( 'cancelPopup' );
		},
		deleteItem: function () {
			let action = 'delete';

			this.$emit( 'popUpActions', action, this.content );
			this.$emit( 'cancelPopup' );
		},
		updateItem: function () {
			if ( ! this.checkEmptyFields() ) {
				return;
			}

			let action  = 'update',
				content = Object.assign( this.content, this.preparedContent );

			if ( '' === this.preparedContent['parent'] ) {
				content['parent'] = "0";
			}

			this.$emit( 'popUpActions', action, content );
		},
		addNewItem: function () {
			let content = Object.assign( this.content, this.preparedContent );

			if ( ! this.checkEmptyFields() ) {
				return;
			}

			let action = 'add';

			this.$emit( 'popUpActions', action, content );
		},
		blurInputQueryHandler: function( query, callback ) {

			if ( "0" === this.currentParentID || '' === this.currentParentID ) {
				callback( '' );
			}

			if ( this.contentParentText != this.currentParentName ) {
				if ( '' != this.contentParentText && '' != this.currentParentName ) {
					this.preparedContent[ 'parent' ] = this.currentParentID;
					callback( this.currentParentName );
				} else {
					if ( '' != this.currentParentName ) {
						callback( '' );

						this.preparedContent[ 'parent' ] = "0";

						if ( '' === this.contentParentText ) {
							this.placeholder['parent'] = "Enter at least 2 char."
						}
					}
				}
			}

			if ( '' === this.contentParentText ) {
				this.preparedContent[ 'parent' ] = "0";
			}

			if ( 'new' === this.popUpState ) {
				if ( "0" != this.preparedContent[ 'parent' ] || this.content[ 'name' ] != this.preparedContent[ 'name' ]) {
					this.addButtonDisabled    = false;
					this.updateButtonDisabled = false;
				} else {
					this.addButtonDisabled    = true;
					this.updateButtonDisabled = true;
				}
			} else {
				if ( this.content[ 'parent' ] != this.preparedContent[ 'parent' ] || ( this.preparedContent[ 'name' ] && this.content[ 'name' ] != this.preparedContent[ 'name' ] ) ) {
					this.addButtonDisabled    = false;
					this.updateButtonDisabled = false;
				} else {
					this.addButtonDisabled    = true;
					this.updateButtonDisabled = true;
				}
			}
		},
		selectedOptionsHandler: function( options ) {
			if ( options.length ) {
				this.currentParentName = options[0].label;
				this.currentParentID   = options[0].value;
			}
		},
		queryChange: function( value ) {

			let query         = value[0],
				currentValues = value[1],
				key           = 'parent';

			this.contentParentText = query.trim();

			if ( '' === this.contentParentText || 1 > this.contentParentText.length ) {
				this.preparedContent[ key ] = "0";
			} else {
				this.preparedContent[ key ] = currentValues[0];
			}

			if ( 'new' === this.popUpState ) {
				if ( '' != this.preparedContent[ 'name' ] || ( '' != this.preparedContent[ 'name' ] && "0" != this.preparedContent[ key ] ) ) {
					this.addButtonDisabled    = false;
					this.updateButtonDisabled = false;
				} else {
					this.addButtonDisabled    = true;
					this.updateButtonDisabled = true;
				}
			} else {
				if ( this.content[ key ] != this.preparedContent[ key ] ) {
					this.addButtonDisabled    = false;
					this.updateButtonDisabled = false;
				} else {
					this.addButtonDisabled    = true;
					this.updateButtonDisabled = true;
				}
			}
		},
		changeValue: function ( value, key, fieldType = '' ) {

			this.inputNameError = false;

			if ( 'name' === key ) {

				if ( 'new' === this.popUpState ) {
					this.preparedContent[ key ] = value;

					if ( '' != this.preparedContent[ key ] || "0" != this.preparedContent[ 'parent' ] ) {
						this.addButtonDisabled = false;
					} else {
						this.addButtonDisabled = true;
					}
				} else {
					this.preparedContent[ key ] = value;

					if ( this.content[ key ] != this.preparedContent[ key ] || ( this.content[ 'parent' ] && this.content[ 'parent' ] != this.preparedContent[ 'parent' ] ) ) {
						this.updateButtonDisabled = false;
					} else {
						this.updateButtonDisabled = true;
					}
				}
			}
		},
		validationHandler: function( value, callback ) {
			let maxLength = 6;

			if ( value.length  > maxLength ) {
				value = JSON.parse( JSON.stringify( this.preparedContent['weight'] ) );
			}

			this.preparedContent['weight'] = value;

			callback( value );

			if ( this.content['weight'] != this.preparedContent['weight'] ) {
				this.addButtonDisabled = false;
				this.updateButtonDisabled = false;
			} else {
				this.addButtonDisabled = true;
				this.updateButtonDisabled = true;
			}
		},
		blurValidationHandler: function( value, callback ) {
			if ( '' === value ) {
				value = '1';
				this.content['weight'] = value;
			} else if ( parseInt( value, 10 ) < 0 ) {
				value = Math.abs( value );
			}

			this.preparedContent['weight'] = value;
			callback( value );

			if ( this.content['weight'] != this.preparedContent['weight'] ) {
				this.addButtonDisabled = false;
				this.updateButtonDisabled = false;
			} else {
				this.addButtonDisabled = true;
				this.updateButtonDisabled = true;
			}
		},
		getOptionList: function( query, ids ) {
			let currentIds = [];

			this.optionsList = [];

			if ( ids.length ) {
				let addToList = [];

				ids.forEach( id => {

					if ( "0" != id ) {
						currentIds.push( id );

						let foundInParentList = this.parentsList.find( el => {

							if ( el.value === id ) {

								let foundInOptionsList = this.optionsList.find( el => el.value === id );

								if ( ! foundInOptionsList ) {
									this.optionsList.push( {
										value: el.value,
										label: el.label
									} );

									this.currentParentName = el.label;
									this.currentParentID   = el.value;
								}

								return true;
							} else {
								return false;
							}
						} );

						if ( ! foundInParentList ) {
							addToList.push( id );
						}
					}
				} );

				if ( 0 === addToList.length ) {
					let promise = new Promise( ( resolve, reject ) => resolve( this.optionsList ) ).then( value => value );
					return promise;
				} else {
					ids = addToList;
				}
			}

			queryPath = `${this.settings.getSuggestionsUrl}?` + buildQuery( {
				query: query,
				ids: ids
			} );

			return wp.apiFetch( {
				method: 'GET',
				url: queryPath,
			} ).then( ( response ) => {
				if ( currentIds.length ) {
					let currentParents = [];

					this.parentsList.push( ...response );

					currentIds.forEach( id => {
						this.parentsList.find( el => {
							if ( el.value === id ) {
								this.currentParentName = el.label;
								this.currentParentID   = el.value;

								currentParents.push( {
									value: el.value,
									label: el.label
								} );
							}
						} );
					} );

					return currentParents;
				} else {
					let currentParents = [];

					response.find( el => {
						if ( el.value !== this.content['id'] ) {
							currentParents.push( {
								value: el.value,
								label: el.label
							} );
						}
					} );
					return currentParents;
				}
			} ).catch( function ( e ) {
				eventHub.$CXNotice.add( {
					message: e.message,
					type: 'error',
					duration: 7000,
				} );
			} );
		},
		fieldType: function ( key, type = null ) {
			let needType = '';
			switch ( key ) {
				case 'parent':
					needType = 'f-select';
				break;
				case 'weight':
					needType = 'number';
				break;
				case 'name':
					needType = 'input';
				break;
				default:
					needType = type
				break;
			}

			if ( this.beEdited( key ) && type === needType ) {
				return true;
			} else {
				return false;
			}
		},
		beEdited: function ( key ) {
			switch ( key ) {
				case 'id':
				case 'actions':
					return false;
				case 'weight':
				case 'name':
				case 'parent':
					return true;
				default:
					return true;
			}
		},
		beVisible: function ( key ) {
			switch ( key ) {
				case 'actions':
					return false;
				case 'id':
					return false;
				default:
					return true;
			}
		},
		popupWidth: function() {
			return '400px';
		},
		contentClass: function() {
			let classes = [ 'jet-search-suggestions', `jet-search-suggestions-${ this.popUpState }` ];

			return classes;
		},
		checkEmptyFields: function () {
			let requiredFields = [ 'name', 'weight' ],
				emptyFields    = [];

			for ( let field of requiredFields ) {
				if (! this.content[field] ) {
					emptyFields.push( this.labels[ field ] ? this.labels[ field ] : field );
				}
			}

			if( ! emptyFields[0] ){
				return true;
			}

			emptyFields = emptyFields.join( ', ' ).toLowerCase();

			eventHub.$CXNotice.add( {
				message: wp.i18n.sprintf( __('Empty fields: %s', 'jet-search'), emptyFields ),
				type: 'error',
				duration: 7000,
			} );

			return false;
		},
	}
} );

//FILTERS

Vue.component( 'jet-search-suggestions-filter', {
	template: '#jet-dashboard-jet-search-suggestions-filter',
	data: function () {
		return {
			curentFilters: {
				search: "",
				searchType: ""
			},
			filters: {
				search: {
					name: 'search',
					label: 'Search',
					placeholder: 'Enter name',
					label_button: 'Clear',
					value: '',
					type: 'input',
				},
				searchType: {
					name: 'search-type',
					label: 'Filter by type',
					value: '',
					type: 'select',
					options: [
						{
							'value': '',
							'label': wp.i18n.__('Select...', 'jet-search'),
						},
						{
							'value': 'parent',
							'label': wp.i18n.__('Parent', 'jet-search'),
						},
						{
							'value': 'child',
							'label': wp.i18n.__('Child', 'jet-search'),
						},
						{
							'value': 'unassigned',
							'label': wp.i18n.__('Unassigned', 'jet-search'),
						},
					]
				}
			},
			filterButtonDisabled: true
		};
	},
	methods: {
		updateFilters: function ( value, name, type ) {
			let filterValue = value.target ? value.target.value : value,
				newFilters = Object.assign({}, this.curentFilters, {[name]: filterValue});

			this.curentFilters = newFilters;

			this.$emit( 'updateFilters', this.curentFilters );

			if ( '' != this.curentFilters['search'] || '' != this.curentFilters['searchType'] ) {
				this.filterButtonDisabled = false;
			} else {
				this.filterButtonDisabled = true;
			}
		},
		clearFilter: function () {
			this.curentFilters = {
				search: "",
				searchType: ""
			};

			this.$emit( 'updateFilters', '' );
			this.$emit( 'clearFilter' );

			this.filterButtonDisabled = true;
		},
	},
} );

//CONFIG

Vue.component('jet-search-suggestions-config', {
	template: '#jet-dashboard-jet-search-suggestions-config',
	props: {
		generalSettings: {
			type: Object
		},
	},
	data: function () {
		return {
			//settings: window.JetSearchSettingsConfig,
			showSaveButton: false,
			configVisible: false,
			settings: {},
			currentSettings: {},
			preparedGeneralSettings: {},
			generalSettingsLabels: {
				records_limit: {
					title: 'Suggestions limit settings',
					label: 'Set the limits',
					desc: 'Limit on adding new entries submitted through a Search Suggestions widget per session.',
					info: '<strong>Pay attention!</strong> If the limit is set to 0, there is no restriction on adding new suggestions.',
				},
				use_session: {
					title: 'Session usage settings',
					label: 'Enable session usage',
					desc: 'Enable the Sessions feature to implement access control and limit the number of allowed entries when adding new suggestions via the Search Suggestion widget.',
					info: '<strong>Important:</strong> Using Sessions may cause potential website caching issues.',
				}
			},
			saveButtonDisabled: true,
		}
	},
	watch: {
		generalSettings( value ) {
			this.settings                = JSON.parse( JSON.stringify( value ) );
			this.currentSettings         = JSON.parse( JSON.stringify( value ) );
			this.preparedGeneralSettings = JSON.parse( JSON.stringify( value ) );
		}
	},
	methods: {
		showPopUp: function() {
			this.settings           = JSON.parse( JSON.stringify( this.generalSettings ) );
			this.currentSettings    = JSON.parse( JSON.stringify( this.generalSettings ) );
			this.saveButtonDisabled = true;
			this.configVisible      = !this.configVisible;
		},
		hidePopUp: function () {
			this.configVisible = false;
		},
		limitValidationHandler: function( value, callback ) {
			let maxLength = 6;

			value = !isNaN( value ) ? parseInt( value, 10 ).toString() : '0';

			if ( value.length  > maxLength ) {
				value = JSON.parse( JSON.stringify( this.preparedGeneralSettings['records_limit'] ) );
			} else if ( '' !== value && parseInt( value, 10 ) < 0 ) {
				value = 0;
			} else if ( isNaN( value ) ) {
				value = 0;
			}

			this.settings['records_limit']                = value;
			this.preparedGeneralSettings['records_limit'] = value;

			callback( value );

			this.settingsChangeValidation();
		},
		limitBlurValidationHandler: function( value, callback ) {
			if ( ! value ) {
				this.settings['records_limit']                = '0';
				this.preparedGeneralSettings['records_limit'] = '0';
				callback( 0 );
			}
		},
		checkboxValidation: function( item ) {

			this.preparedGeneralSettings['use_session'] = item.target.checked;

			this.settingsChangeValidation();
		},
		settingsChangeValidation: function() {
			if ( undefined != this.preparedGeneralSettings['use_session'] && this.preparedGeneralSettings['use_session'] !== this.currentSettings['use_session'] ) {
				this.saveButtonDisabled = false;
				return;
			}

			if ( true === this.preparedGeneralSettings['use_session'] ) {
				if ( undefined != this.preparedGeneralSettings['records_limit'] &&  this.currentSettings['records_limit'] != this.preparedGeneralSettings['records_limit'] ) {
					this.saveButtonDisabled = false;
				} else {
					this.saveButtonDisabled = true;
				}
			} else {
				this.saveButtonDisabled = true;
			}
		},
		saveSettings: function() {
			this.$emit( 'saveSettings', this.preparedGeneralSettings );

			this.settings           = JSON.parse( JSON.stringify( this.preparedGeneralSettings ) );
			this.currentSettings    = JSON.parse( JSON.stringify( this.preparedGeneralSettings ) );
			this.saveButtonDisabled = true;
		}
	},
} );