( function () {

	'use strict';


	Vue.component( 'upsale-page', {

		template: '#jet-dashboard-upsale-page',

		props: {
			subpage: [ String, Boolean ]
		},

		data: function() {
			return {
				generalConfig: window.JetDashboardConfig.generalConfig || [],
			};
		},

		computed: {
			getCrocoblockLink: function() {
				let baseUrl = this.generalConfig.pricingPageUrl || 'https://crocoblock.com/pricing/',
					licenseType = `${ this.$root.licenseType }-license` || 'not-activated-license',
					themeAuthor = this.$root.themeInfo.authorSlug || 'unknow-author',
					utmString = window.JetDasboard.getUtmParamsString( {
						utm_source: `dashboard/${ this.$root.pageModule }`,
						utm_medium: `${ licenseType }/${ themeAuthor }`,
						utm_campaign: 'upsale-crocoblock',
					} );

				if ( utmString ) {
					return `${ baseUrl }?${ utmString }`;
				}

				return baseUrl;
			},
		},

		methods: {
			navigateToLicensePage: function() {
				window.location.href = window.JetDashboardConfig.licensePageUrl;
			},

			navigateToLicenseManager: function() {
				window.location.href = window.JetDashboardConfig.licenseManagerUrl;
			}
		}
	} );

} )();
