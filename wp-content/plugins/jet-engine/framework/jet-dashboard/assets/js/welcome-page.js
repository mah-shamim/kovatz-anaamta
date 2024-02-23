( function () {

	'use strict';

	Vue.component( 'plugin-item-registered', {
		template: '#jet-dashboard-plugin-item-registered',

		props: {
			pluginData: Object,
		},

		data: function() {
			return {
				usefulLinksToggle: false
			}
		},

		computed: {

			itemClass: function() {
				return [
					'plugin-item',
					'plugin-item--registered',
					this.dropdownVisible ? 'dropdown-visible' : ''
				];
			},

			usefulLinksEmpty: function() {
				return 0 === this.pluginData.usefulLinks.length;
			},

			mainLinkItem: function() {

				if ( ! this.usefulLinksEmpty ) {
					let firstItem = this.pluginData.usefulLinks[0];

					return {
						'url': firstItem.url,
						'label': firstItem.label,
						'target': firstItem.target,
					};
				}

				return {
					'url': this.pluginData.docs,
					'label': __( 'Go to docs', 'jet-dashboard' ),
					'target': '_blank'
				};
			},

			dropdownLinkItems: function() {
				let usefulLinks = this.pluginData.usefulLinks;

				return usefulLinks;
			},

			dropdownAvaliable: function() {
				return 1 < this.dropdownLinkItems.length;
			},

			dropdownVisible: function() {
				return this.usefulLinksToggle && 1 < this.dropdownLinkItems.length;
			},
		},

		methods: {
			mouseoverHandle: function() {
				this.usefulLinksToggle = true;
			},

			mouseleaveHandle: function() {
				this.usefulLinksToggle = false;
			},
		}
	} );

	Vue.component( 'offers-item', {
		template: '#jet-dashboard-offers-item',

		props: {
			config: Object,
		},

		data: function() {
			return {}
		},

		computed: {
			itemClass: function() {
				return [
					'offers-item',
				];
			},
			configDefined: function() {
				return this.config;
			},
			logo: function() {
				return this.configDefined && this.config['logo'] ? this.config['logo'] : false;
			},
			name: function() {
				return this.configDefined && this.config['name'] ? this.config['name'] : false;
			},
			desc: function() {
				return this.configDefined && this.config['desc'] ? this.config['desc'] : false;
			},
			actionConfig: function() {
				return this.configDefined && this.config['actionUrl'] && this.config['actionLabel'] ? {
					url: this.config['actionUrl'],
					label: this.config['actionLabel']
				} : false;
			},
		},
	} );

	Vue.component( 'extras-item', {
		template: '#jet-dashboard-extras-item',

		props: {
			config: Object,
		},

		data: function() {
			return {
				adminUrl: window.JetDashboardConfig.adminUrl || '#',
				userPlugins: window.JetDashboardConfig.userPlugins || {},
			}
		},

		computed: {
			itemClass: function() {
				return [
					'extras-item',
				];
			},
			configDefined: function() {
				return this.config || false;
			},
			logo: function() {
				return this.configDefined && this.config['logo'] ? this.config['logo'] : false;
			},
			name: function() {
				return this.configDefined && this.config['name'] ? this.config['name'] : false;
			},
			desc: function() {
				return this.configDefined && this.config['desc'] ? this.config['desc'] : false;
			},
			actionType: function() {
				return this.configDefined && this.config['actionType'] ? this.config['actionType'] : 'external';
			},
			requirementPlugin: function() {
				return this.configDefined && this.config['requirementPlugin'] ? this.config['requirementPlugin'] : false;
			},
			actionConfig: function() {
				let actionConfig = false;

				switch( this.actionType ) {

					case 'external':
						actionConfig = this.configDefined && this.config['externalUrl'] && this.config['externalLabel'] ? {
							url: this.config['externalUrl'],
							label: this.config['externalLabel'],
							target: '_blank'
						} : false;
					break;

					case 'dashboard':

						if ( this.requirementPlugin && this.userPlugins.hasOwnProperty( this.requirementPlugin ) && this.userPlugins[ this.requirementPlugin ].isActivated ) {
							actionConfig = this.configDefined && this.config['dashboardUrl'] && this.config['dashboardLabel'] ? {
								url: this.adminUrl + this.config['dashboardUrl'],
								label: this.config['dashboardLabel'],
								target: '_self'
							} : false;
						} else {
							actionConfig = this.configDefined && this.config['externalUrl'] && this.config['externalLabel'] ? {
								url: this.config['externalUrl'],
								label: this.config['externalLabel'],
								target: '_blank'
							} : false;
						}
					break;
				}

				return actionConfig;
			},
		},
	} );

	Vue.component( 'welcome-page', {

		template: '#jet-dashboard-welcome-page',

		props: {
			subpage: [ String, Boolean ]
		},

		data: function() {
			return {
				proccesingState: false,
				allJetPlugins: window.JetDashboardConfig.allJetPlugins || {},
				licenseList: window.JetDashboardConfig.licenseList || [],
				avaliableBanners: window.JetDashboardConfig.avaliableBanners || [],
				offersConfig: window.JetDashboardConfig.offersConfig || [],
				extrasConfig: window.JetDashboardConfig.extrasConfig || [],
				generalConfig: window.JetDashboardConfig.generalConfig || [],
				crocoWizardData: window.JetDashboardConfig.crocoWizardData || false,
				themeInfo: window.JetDashboardConfig.themeInfo || false,
				actionPlugin: false,
				actionPluginRequest: null,
				actionPluginProcessed: false,
			};
		},

		computed: {
			innerComponentConfig: function() {
				let innerComponentConfig = this.$root.pageConfig['inner-component'] || false,
					bannersData          = this.$root.getBannerListForArea( innerComponentConfig );

					innerComponentConfig = Object.assign( innerComponentConfig, {
						bannersData: bannersData
					} );

				return innerComponentConfig;
			},

			isLicenseActivated: function() {
				return 0 !== this.licenseList.length;
			},

			licencePluginList: function() {
				let licencePluginList = {};

				for ( let licence of this.licenseList ) {
					let plugins = licence['licenseDetails']['plugins'];

					for ( let plugin in plugins ) {

						let pluginData = plugins[ plugin ];
						let pluginSlug = pluginData.slug;

						if ( ! licencePluginList.hasOwnProperty( plugin ) ) {

							licencePluginList[ plugin ] = pluginData;
						}
					}
				}

				return licencePluginList;
			},

			registeredPluginList: function() {
				let registeredPluginList = {};

				for ( let pluginSlug in this.allJetPlugins ) {

					if ( this.allJetPlugins[ pluginSlug ][ 'isInstalled' ]
						&& this.allJetPlugins[ pluginSlug ][ 'isActivated' ]
					) {
						registeredPluginList[ pluginSlug ] = this.allJetPlugins[ pluginSlug ];
					}
				}

				return registeredPluginList;
			},

			avaliablePluginList: function() {

				let avaliablePluginList = {};

				for ( let pluginSlug in this.allJetPlugins ) {

					if ( ( ! this.allJetPlugins[ pluginSlug ]['isInstalled'] )
						&& this.licencePluginList.hasOwnProperty( pluginSlug )
					) {
						let pluginData = this.allJetPlugins[ pluginSlug ];

						avaliablePluginList[ pluginSlug ] = pluginData;
					}
				}

				return avaliablePluginList;
			},

			avaliablePluginCount: function() {
				return Object.keys( this.avaliablePluginList ).length;
			},

			morePluginList: function() {

				let morePluginList = {};

				for ( let pluginSlug in this.allJetPlugins ) {

					if ( ( ! this.allJetPlugins[ pluginSlug ]['isInstalled'] ) &&
						( ! this.licencePluginList.hasOwnProperty( pluginSlug ) ) ) {

						let pluginData = this.allJetPlugins[ pluginSlug ];

						morePluginList[ pluginSlug ] = pluginData;
					}
				}

				return morePluginList;
			},

			morePluginsVisible: function() {
				return 0 !== Object.keys( this.morePluginList ).length;
			},

			updatePluginList: function() {
				let updatePluginList = {};

				for ( let pluginSlug in this.registeredPluginList ) {

					if ( this.registeredPluginList[ pluginSlug ][ 'updateAvaliable' ] ) {

						updatePluginList[ pluginSlug ] = this.registeredPluginList[ pluginSlug ];
					}
				}

				return updatePluginList;
			},

			updatePluginCount: function() {
				return Object.keys( this.updatePluginList ).length;
			},

			updatesMessage: function() {
				let updateMessage    = sprintf( _n( 'You can update <b>%s plugin</b>', 'You can update <b>%s plugins</b>', this.updatePluginCount, 'jet-dashboard' ), this.updatePluginCount ),
					noUpdatesMessage = __( 'All plugins updated', 'jet-dashboard' ),
					message          = ( 0 === this.updatePluginCount ) ? noUpdatesMessage : updateMessage;

				if ( ! this.isLicenseActivated ) {
					message = __( 'Activate license to update your JetPlugins', 'jet-dashboard' );
				}

				return message;
			},

			avaliableToInstallMessage: function() {
				let avaliableMessage    = sprintf( _n( 'You can install <b>%s more Plugin</b> with your licence', 'You can install <b>%s more Plugins</b>  with your licence', this.avaliablePluginCount, 'jet-dashboard' ), this.avaliablePluginCount ),
					noAvaliableMessage = __( 'All available plugins are already installed', 'jet-dashboard' );

				return 0 === this.avaliablePluginCount ? noAvaliableMessage : avaliableMessage;
			},

			avaliableOffers: function() {
				return this.offersConfig.offerList || [];
			},

			isWizardInstalled: function() {
				return this.crocoWizardData.isInstalled || false;
			},

			isWizardActivated: function() {
				return this.crocoWizardData.isActivated || false;
			},

			wizardSectionsVisible: function() {
				let productType = this.$root.productType,
					visibleMap  = [
						'plugin-set',
						'all-inclusive',
						'lifetime',
					];

				return ( !this.isWizardInstalled || !this.isWizardActivated ) && visibleMap.includes( productType );
			},

			wizardSectionsTitle: function() {
				let productType = this.$root.productType,
					title = __( 'Try quickstart installation', 'jet-dashboard' );

				switch( productType ) {
					case 'lifetime':
					case 'all-inclusive':
						title = __( 'Unpack Free All-inclusive items', 'jet-dashboard' );
					break;
					case 'default':
						title = __( 'Try quickstart installation', 'jet-dashboard' );
					break;
				}

				return title;
			},

			offersVisible: function() {
				return 0 !== this.avaliableOffers.length;
			},

			avaliableExtras: function() {
				return this.extrasConfig.extraList || [];
			},

			extrasVisible: function() {
				let productType = this.$root.productType,
					visibleMap  = [
						'all-inclusive',
						'lifetime',
					];

				return 0 !== this.avaliableExtras.length && visibleMap.includes( productType ) && ! this.wizardSectionsVisible;
			},

			getMoreBannerVisible: function() {
				let productType = this.$root.productType,
					visibleMap  = [
						'not-activated',
						'theme-plugin-bundle',
						'single-plugin',
					];

				return visibleMap.includes( productType );
			},

			getMoreBannerLink: function() {
				let baseUrl = this.generalConfig.pricingPageUrl || 'https://crocoblock.com/pricing/',
					licenseType = `${ this.$root.licenseType }-license` || 'not-activated-license',
					themeAuthor = this.$root.themeInfo.authorSlug || 'unknow-author',
					utmString = window.JetDasboard.getUtmParamsString( {
						utm_source: `dashboard/${ this.$root.pageModule }`,
						utm_medium: `${ licenseType }/${ themeAuthor }`,
						utm_campaign: 'upsale-crocoblock',
						utm_content: 'banner-60-percent'
					} );

				if ( utmString ) {
					return `${ baseUrl }?${ utmString }`;
				}

				return baseUrl;
			},

			isLifetime: function() {
				let productType = this.$root.productType;

				return 'lifetime' === productType ? true : false;
			}
		},

		methods: {
			getLicenseExpireMessage: function( rawDate = '' ) {
				let convertedDate = this.convertDateFormat( rawDate ),
					expireCases = [
						'0000-00-00 00:00:00',
						'1000-01-01 00:00:00',
						'lifetime'
					];

				if ( expireCases.includes( rawDate ) ) {
					return __( '<b class="lifetime">Lifetime</b>', 'jet-dashboard' );
				}

				return sprintf( __( 'Licence expires <b class="expire-date">%s</b>', 'jet-dashboard' ), convertedDate );
			},

			convertDateFormat: function( _date = '' ) {
				let lifetimeCases = [
						'0000-00-00 00:00:00',
						'1000-01-01 00:00:00',
						'lifetime'
					],
					rawDate = lifetimeCases.includes( _date ) ? '1000-01-01 00:00:00' : _date,
					timeStamp = Date.parse( rawDate ),
					dateTimeFormat = new Intl.DateTimeFormat( 'en', { year: 'numeric', month: 'short', day: '2-digit' } ),
					[ { value: month },,{ value: day },,{ value: year } ] = dateTimeFormat.formatToParts( timeStamp ),
					convertedDate = `${ month }. ${ day }, ${ year }`;

				return convertedDate;
			},

			wizardActionHandle: function() {
				let self = this,
					action = false;

				if ( ! this.isWizardInstalled ) {
					action = 'install';
				}

				if ( this.isWizardInstalled && ! this.isWizardActivated ) {
					action = 'activate';
				}

				self.actionPluginRequest = jQuery.ajax( {
					type: 'POST',
					url: window.JetDashboardConfig.ajaxUrl,
					dataType: 'json',
					data: {
						action: 'jet_dashboard_wizard_plugin_action',
						data: {
							plugin: 'crocoblock-wizard/crocoblock-wizard.php',
							action: action,
							nonce: window.JetDashboardConfig.nonce,
						}
					},
					beforeSend: function( jqXHR, ajaxSettings ) {

						if ( null !== self.actionPluginRequest ) {
							self.actionPluginRequest.abort();
						}

						self.actionPluginProcessed = true;
					},
					success: function( responce, textStatus, jqXHR ) {
						self.actionPluginProcessed = false;

						self.$CXNotice.add( {
							message: responce.message,
							type: responce.status,
							duration: 3000,
						} );

						if ( 'success' === responce.status ) {

							self.proccesingState = true;

							setTimeout( function() {
								window.location.reload();
							}, 1000 );

							//self.crocoWizardData = responce.data;
						}
					}
				} );
			},

			pluginAction: function( plugin_file, action ) {

			},

			navigateToLicensePage: function() {
				window.location.href = window.JetDashboardConfig.licensePageUrl;
			},

			navigateToLicenseManager: function() {
				window.location.href = window.JetDashboardConfig.licenseManagerUrl;
			}
		}
	} );

} )();
