( function () {

	'use strict';

	Vue.component( 'plugin-settings-toggle', {

		template: '#jet-dashboard-plugin-settings-toggle',

		props: {
			config: [ Object, Boolean ]
		},

		data: function() {
			return {
				fold: false,
				pageModule: this.$root.pageModule || false,
				subPageModule: this.$root.subPageModule || false,
			};
		},

		mounted: function() {
			let activeSettingsCategory = JetDasboard.getLocalStorageData( 'activeSettingsCategory', false );

			if ( this.config.hasOwnProperty( 'moduleList' ) ) {
				let moduleCheck = this.config.moduleList.some( ( moduleData ) => {
					return moduleData.page === this.subPageModule;
				} );

				if ( moduleCheck) {
					this.fold = true;
				}
			}
		},

		computed: {
			linksList: function() {
				return this.config.moduleList;
			},
			islinksEmpty: function() {
				return 0 !== this.linksList.length;
			},
			linksVisible: function() {
				return this.fold && this.islinksEmpty;
			}
		},

		methods: {
			toggleHandler: function( moduleSlug ) {
				this.fold = ! this.fold;
			},

			navigateHandler: function( link, moduleSlug, category ) {

				window.JetDashboardEventBus.$emit( 'settingsPage/navigateHandler', {
					'link': link,
					'moduleSlug': moduleSlug,
					'category': category,
				} );
			}
		}
	} );

	Vue.component( 'settings-page', {

		template: '#jet-dashboard-settings-page',

		props: {
			subpage: [ String, Boolean ]
		},

		data: function() {
			return {
				categoryList: window.JetDashboardConfig.categoryList || [],
				proccesingState: false
			};
		},

		mounted: function() {
			window.JetDashboardEventBus.$on( 'settingsPage/navigateHandler', this.navigateHandler );
		},

		methods: {
			navigateHandler: function( payload ) {
				let link       = payload.link || false,
					moduleSlug = payload.moduleSlug || false,
					category   = payload.category || false;

				if ( ! link || ! moduleSlug ) {
					return false;
				}

				if ( moduleSlug !== this.$root.subPageModule ) {
					this.proccesingState = true;
					JetDasboard.setLocalStorageData( 'activeSettingsCategory', category );
					window.open( link, '_self' );
				}
			}
		}
	} );

} )();
