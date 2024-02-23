(function () {

	'use strict';

	Vue.component( 'license-item', {
		template: '#jet-dashboard-license-item',

		props: {
			licenseData: Object,
			type: String
		},

		data: function() {
			return {
				licenseKey: this.licenseData.licenseKey,
				licenseStatus: this.licenseData.licenseStatus,
				licenseDetails: this.licenseData.licenseDetails,
				activationStatus: false,
				ajaxLicenseAction: null,
			}
		},

		computed: {

			isLicenseActive: function() {
				return 'active' === this.licenseStatus ? true : false;
			},

			licenseActionType: function() {
				return ! this.isLicenseActive ? 'activate' : 'deactivate';
			},

			maskedLicenseKey: function() {
				let licenseKey      = this.licenseKey,
					licenseKeyArray = licenseKey.split(''),
					maskerLicenseArray = [];

				maskerLicenseArray = licenseKeyArray.map( ( item, index ) => {

					if ( index > 4 && index < licenseKeyArray.length - 4 ) {
						return '*';
					}

					return item;
				} );

				return maskerLicenseArray.join('');
			},

			/*licenseStatus: function() {
				return this.isLicenseActive ? 'activated' : 'not-activated';
			},*/

			licenseType: function() {
				return this.licenseDetails.type ? this.licenseDetails.type : '';
			},

			productName: function() {
				return this.licenseDetails.product_name ? this.licenseDetails.product_name : '';
			},

			isLicenseExpired: function() {
				return 'expired' === this.licenseStatus ? true : false;
			},

			expireDate: function() {

				let expireCases = [
					'0000-00-00 00:00:00',
					'1000-01-01 00:00:00',
					'lifetime'
				];

				if ( expireCases.includes( this.licenseDetails.expire ) ) {
					return 'Lifetime';
				}

				return this.licenseDetails.expire;
			},

			licensePlugins: function() {
				return this.licenseDetails.plugins ? this.licenseDetails.plugins : [];
			},
		},

		methods: {
			showLicenseManager: function() {
				window.JetDashboardEventBus.$emit( 'showLicenseManager' );
			},

			licenseAction: function() {
				var self       = this,
					actionType = self.licenseActionType;

				self.activationStatus = true;

				self.ajaxLicenseAction = jQuery.ajax( {
					type: 'POST',
					url: window.JetDashboardConfig.ajaxUrl,
					dataType: 'json',
					data: {
						action: 'jet_license_action',
						data: {
							license: self.licenseKey,
							action: actionType,
							nonce: window.JetDashboardConfig.nonce,
						}
					},
					beforeSend: function( jqXHR, ajaxSettings ) {

						if ( null !== self.ajaxLicenseAction ) {
							self.ajaxLicenseAction.abort();
						}
					},
					success: function( responce, textStatus, jqXHR ) {
						self.activationStatus = false;

						self.$CXNotice.add( {
							message: responce.message,
							type: responce.status,
							duration: 4000,
						} );

						let licenseStatus  = responce.status,
							licenseDetails = responce.data;

						if ( 'success' === licenseStatus ) {

							if ( 'activate' === actionType ) {

								self.licenseStatus = 'active';
								self.licenseDetails = licenseDetails;

								window.JetDashboardEventBus.$emit( 'addLicenseItem', {
									'licenseKey': self.licenseKey,
									'licenseStatus': 'active',
									'licenseDetails': licenseDetails,
								} );
							}

							if ( 'deactivate' === actionType ) {
								window.JetDashboardEventBus.$emit( 'removeLicenseItem', self.licenseKey );
							}
						}

						if ( 'error' === licenseStatus ) {
							if ( 'limit_exceeded' === responce.code ) {
								window.JetDashboardEventBus.$emit( 'showResponcePopup', responce );
							}
						}
					}
				} );
			}
		}
	} );

	Vue.component( 'plugin-item-installed', {
		template: '#jet-dashboard-plugin-item-installed',

		props: {
			pluginData: Object
		},

		data: function() {
			return {
				actionPlugin: false,
				actionPluginRequest: null,
				actionPluginProcessed: false,
				licenseActionProcessed: false,
				updatePluginProcessed: false,
				rollbackPluginProcessed: false,
				licenseKey: '',
				ajaxLicenseAction: null,
				rollbackPopupVisible: false,
				rollbackVersion: this.pluginData['version']
			}
		},

		computed: {

			deactivateAvaliable: function() {
				return ( this.pluginData['isInstalled'] && this.pluginData['isActivated'] ) ? true : false;
			},

			activateAvaliable: function() {
				return ( this.pluginData['isInstalled'] && !this.pluginData['isActivated'] ) ? true : false;
			},

			updateAvaliable: function() {
				return ( this.pluginData['updateAvaliable'] ) ? true : false;
			},

			updateActionAvaliable: function() {
				return ( this.pluginData['licenseActivated'] && this.pluginData['updateAvaliable'] ) ? true : false;
			},

			activateLicenseVisible: function() {
				return ( this.pluginData['licenseControl'] && !this.pluginData['licenseActivated'] ) ? true : false;
			},

			deactivateLicenseVisible: function() {
				return ( this.pluginData['licenseActivated'] ) ? true : false;
			},

			versionRollbackAvaliable: function() {
				return ( 0 < this.pluginData['versions'].length ) && this.pluginData['licenseActivated'] && this.deactivateAvaliable ? true : false;
			},

			rollbackButtonVisible: function() {
				return this.rollbackVersion !== this.pluginData['currentVersion'];
			},

			proccesingState: function() {
				return this.actionPluginProcessed || this.updatePluginProcessed || this.rollbackPluginProcessed;
			},

			rollbackOptions: function() {

				return this.pluginData['versions'].map( ( version ) => {
					let label = version;

					if ( label === this.pluginData['currentVersion'] ) {
						label = label + ' - Current Version';
					}

					if ( label === this.pluginData['version'] ) {
						label = label + ' - Latest Version';
					}

					return {
						label: label,
						value: version,
					}
				} );
			}
		},

		methods: {

			deactivatePlugin: function() {
				this.actionPlugin = 'deactivate';
				this.pluginAction();
			},

			activatePlugin: function() {
				this.actionPlugin = 'activate';
				this.pluginAction();
			},

			updatePlugin: function() {

				if ( this.updateActionAvaliable ) {

					this.actionPlugin = 'update';
					this.pluginAction();
				} else {
					window.JetDashboardEventBus.$emit( 'showPopupUpdateCheck' );
				}

			},

			showPopupActivation: function() {
				window.JetDashboardEventBus.$emit( 'showPopupActivation', this.pluginData['slug'] );
			},

			showRollbackPopup: function() {
				this.rollbackPopupVisible = true;
			},

			rollbackPluginVersion: function() {
				this.actionPlugin = 'rollback';
				this.pluginAction();
			},

			pluginAction: function() {
				let self = this;

				self.actionPluginRequest = jQuery.ajax( {
					type: 'POST',
					url: window.JetDashboardConfig.ajaxUrl,
					dataType: 'json',
					data: {
						action: 'jet_dashboard_plugin_action',
						data: {
							action: self.actionPlugin,
							plugin: self.pluginData['slug'],
							version: self.rollbackVersion,
							nonce: window.JetDashboardConfig.nonce,
						}
					},
					beforeSend: function( jqXHR, ajaxSettings ) {

						if ( null !== self.actionPluginRequest ) {
							self.actionPluginRequest.abort();
						}

						switch( self.actionPlugin ) {

							case 'activate':
							case 'deactivate':
								self.actionPluginProcessed = true;
							break;

							case 'update':
								self.updatePluginProcessed = true;
							break;

							case 'rollback':
								self.rollbackPluginProcessed = true;
							break;
						}
					},
					success: function( responce, textStatus, jqXHR ) {

						switch(  self.actionPlugin ) {

							case 'activate':
							case 'deactivate':
								self.actionPluginProcessed = false;
							break;

							case 'update':
								self.updatePluginProcessed = false;
							break;

							case 'rollback':
								self.rollbackPluginProcessed = false;
							break;
						}

						self.$CXNotice.add( {
							message: responce.message,
							type: responce.status,
							duration: 3000,
						} );

						if ( 'success' === responce.status ) {
							self.rollbackPopupVisible = false;

							window.JetDashboardEventBus.$emit( 'updateUserPluginData', {
								'slug': self.pluginData['slug'],
								'pluginData': responce.data,
							} );
						}
					}
				} );
			},

			deactivateLicense: function() {
				window.JetDashboardEventBus.$emit( 'showPopupDeactivation', this.pluginData['slug'] );
			}
		}
	} );

	Vue.component( 'plugin-item-avaliable', {
		template: '#jet-dashboard-plugin-item-avaliable',

		props: {
			pluginData: Object,
		},

		data: function() {
			return {
				pluginActionRequest: null,
				pluginActionType: false,
				pluginActionProcessed: false,
			}
		},

		computed: {
			installAvaliable: function() {
				return !this.pluginData['isInstalled'] ? true : false;
			},
		},

		methods: {

			installPlugin: function() {
				this.pluginActionType = 'install';
				this.pluginAction();
			},

			pluginAction: function() {
				let self = this;

				self.pluginActionRequest = jQuery.ajax( {
					type: 'POST',
					url: window.JetDashboardConfig.ajaxUrl,
					dataType: 'json',
					data: {
						action: 'jet_dashboard_plugin_action',
						data: {
							action: self.pluginActionType,
							plugin: self.pluginData['slug'],
							nonce: window.JetDashboardConfig.nonce,
						}
					},
					beforeSend: function( jqXHR, ajaxSettings ) {

						if ( null !== self.pluginActionRequest ) {
							self.pluginActionRequest.abort();
						}

						self.pluginActionProcessed = true;
					},
					success: function( responce, textStatus, jqXHR ) {
						self.pluginActionProcessed = false;

						self.$CXNotice.add( {
							message: responce.message,
							type: responce.status,
							duration: 3000,
						} );

						if ( 'success' === responce.status ) {
							window.JetDashboardEventBus.$emit( 'updateUserPluginData', {
								'slug': self.pluginData['slug'],
								'pluginData': responce.data,
							} );
						}
					}
				} );
			}
		}
	} );

	Vue.component( 'responce-info', {
		template: '#jet-dashboard-responce-info',

		props: {
			responceData: Object
		},

		data: function() {
			return {}
		},

		computed: {
			isResponceEmpty: function() {
				return 0 === Object.entries( this.responceData ).length ? true : false;
			},

			type: function() {
				return this.responceData.hasOwnProperty('status') ? this.responceData.status : 'error';
			},

			code: function() {
				return this.responceData.hasOwnProperty('code') ? this.responceData.code : 'error';
			},

			title: function() {
				return this.responceData.hasOwnProperty('message') ? this.responceData.message : '';
			},

			responceDetails: function() {
				return this.responceData.hasOwnProperty('data') ? this.responceData.data : {};
			},

			activationLimit: function() {

				if ( ! this.responceDetails.hasOwnProperty('activation_limit') ) {
					return 1;
				}

				return 0 !== this.responceDetails['activation_limit'] ? this.responceDetails['activation_limit'] : 'unlimited';
			},

			activatedSites: function() {

				if ( ! this.responceDetails.hasOwnProperty('sites') ) {
					return [];
				}

				return 0 !== this.responceDetails['sites'] ? this.responceDetails['sites'] : [];
			}
		}
	} );

	Vue.component( 'license-page', {
		template: '#jet-dashboard-license-page',

		props: {
			subpage: [ String, Boolean ]
		},

		data: function() {
			return {
				allJetPlugins: window.JetDashboardConfig.allJetPlugins || {},
				licenseList: window.JetDashboardConfig.licenseList || [],
				licenseManagerVisible: false,
				licensePopupVisible: false,
				deactivatePopupVisible: false,
				updateCheckPopupVisible: false,
				debugConsoleVisible: false,
				responcePopupVisible: false,
				licenseActionProcessed: false,
				ajaxLicenseAction: null,
				activatingPluginSlug: false,
				responceData: {},
				proccesingState: false,
				checkUpdatesAction: null,
				checkUpdatesProcessed: false
			};
		},

		mounted: function() {

			window.JetDashboardEventBus.$on( 'addLicenseItem', this.addLicense );
			window.JetDashboardEventBus.$on( 'removeLicenseItem', this.removeLicense );
			window.JetDashboardEventBus.$on( 'updateUserPluginData', this.updateUserPluginData );
			window.JetDashboardEventBus.$on( 'showLicenseManager', this.showLicenseManager );
			window.JetDashboardEventBus.$on( 'showPopupActivation', this.showPopupActivation );
			window.JetDashboardEventBus.$on( 'showPopupDeactivation', this.showPopupDeactivation );
			window.JetDashboardEventBus.$on( 'showPopupUpdateCheck', this.showPopupUpdateCheck );
			window.JetDashboardEventBus.$on( 'showResponcePopup', this.showResponcePopup );

			if ( window.location.href ) {
				const urlParams = new URLSearchParams( window.location.href );

				switch( urlParams.get('subpage') ) {
					case 'license-manager':
						this.showLicenseManager();
					break;
				}
			}
		},

		computed: {

			newlicenseData: function() {
				return {
					'licenseStatus': 'inactive',
					'licenseKey': '',
					'licenseDetails': {},
				};
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

			installedPluginList: function() {
				let installedPluginList = {};

				for ( let pluginSlug in this.allJetPlugins ) {

					if ( this.allJetPlugins[ pluginSlug ][ 'isInstalled' ] ) {

						let pluginData = this.allJetPlugins[ pluginSlug ];

						let licenseActivated = this.licencePluginList.hasOwnProperty( pluginSlug ) ? true : false;

						this.$set( pluginData, 'licenseActivated', licenseActivated );

						installedPluginList[ pluginSlug ] = pluginData;
					}
				}

				return installedPluginList;
			},

			installedPluginListVisible: function() {
				return 0 !== Object.keys( this.installedPluginList ).length ? true : false;
			},

			avaliablePluginList: function() {

				let avaliablePluginList = {};

				for ( let pluginSlug in this.allJetPlugins ) {

					if ( ( ! this.allJetPlugins[ pluginSlug ]['isInstalled'] )
						&& this.licencePluginList.hasOwnProperty( pluginSlug ) ) {

						let pluginData = this.allJetPlugins[ pluginSlug ];

						let licenseActivated = this.licencePluginList.hasOwnProperty( pluginSlug ) ? true : false;

						this.$set( pluginData, 'licenseActivated', licenseActivated );

						avaliablePluginList[ pluginSlug ] = pluginData;
					}
				}

				return avaliablePluginList;
			},

			avaliablePluginListVisible: function() {
				return 0 !== Object.keys( this.avaliablePluginList ).length ? true : false;
			},

			morePluginList: function() {

				let morePluginList = {};

				for ( let pluginSlug in this.allJetPlugins ) {

					if ( ( ! this.allJetPlugins[ pluginSlug ]['isInstalled'] ) &&
						( ! this.licencePluginList.hasOwnProperty( pluginSlug ) ) ) {

						let pluginData = this.allJetPlugins[ pluginSlug ];

						let licenseActivated = this.licencePluginList.hasOwnProperty( pluginSlug ) ? true : false;

						this.$set( pluginData, 'licenseActivated', licenseActivated );

						morePluginList[ pluginSlug ] = pluginData;
					}
				}

				return morePluginList;
			},

			morePluginListVisible: function() {
				return Object.keys( this.morePluginList ).length ? true : false;
			},
		},

		methods: {

			showLicenseManager: function() {
				this.deactivatePopupVisible = false;
				this.licensePopupVisible = false;
				this.licenseManagerVisible = true;
			},

			maybeClearSubpageParam: function() {

				if ( window.location.href ) {

					let urlParams = new URLSearchParams( window.location.search );

					if ( urlParams.get( 'subpage' ) ) {
						urlParams.delete( 'subpage' );

						let clearedParams = urlParams.toString();

						window.open( `${ window.location.origin + window.location.pathname }?${ clearedParams }`, '_self' );
					}
				}
			},

			showPopupActivation: function( slug ) {
				this.activatingPluginSlug = slug;
				this.updateCheckPopupVisible = false;
				this.licensePopupVisible = true;
			},

			showPopupDeactivation: function( slug ) {
				this.deactivatePopupVisible = true;
			},

			showPopupUpdateCheck: function() {
				this.updateCheckPopupVisible = true;
			},

			showResponcePopup: function( responceData ) {
				this.deactivatePopupVisible = false;
				this.licensePopupVisible = false;
				this.licenseManagerVisible = false;
				this.responcePopupVisible = true;

				this.responceData = responceData;
			},

			addNewLicense: function() {
				this.licenseManagerVisible = false;
				this.licensePopupVisible = true;
			},

			addLicense: function( licenseData ) {
				this.licenseList.push( licenseData );

				self.proccesingState = true;

				setTimeout( function() {
					window.location.reload();
				}, 3000 );
			},

			removeLicense: function( licenceKey ) {

				let removingIndex = false;

				for ( let licenceIndex in this.licenseList ) {
					let licenseData =  this.licenseList[ licenceIndex ];

					if ( licenseData['licenseKey'] === licenceKey ) {
						removingIndex = licenceIndex;

						break;
					}
				}

				if ( removingIndex ) {
					this.licenseList.splice( removingIndex, 1 );
				}

				this.licensePopupVisible = false;

				setTimeout( function() {
					window.location.reload();
				}, 500 );
			},

			checkPluginsUpdate: function() {
				var self = this;

				self.checkUpdatesAction = jQuery.ajax( {
					type: 'POST',
					url: window.JetDashboardConfig.ajaxUrl,
					dataType: 'json',
					data: {
						action: 'jet_dashboard_debug_action',
						data: {
							action: 'check-plugin-update',
							nonce: window.JetDashboardConfig.nonce,
						}
					},
					beforeSend: function( jqXHR, ajaxSettings ) {

						if ( null !== self.checkUpdatesAction ) {
							self.checkUpdatesAction.abort();
						}

						self.checkUpdatesProcessed = true;
					},
					success: function( responce, textStatus, jqXHR ) {
						self.checkUpdatesProcessed = false;

						self.$CXNotice.add( {
							message: responce.message,
							type: responce.status,
							duration: 3000,
						} );

						self.proccesingState = true;

						setTimeout( function() {
							window.location.reload();
						}, 1000 );
					}
				} );
			},

			updateUserPluginData: function( data ) {
				let slug       = data.slug,
					pluginData = data.pluginData;

				this.allJetPlugins[ slug ] = Object.assign( {}, this.allJetPlugins[ slug ], pluginData );

				this.proccesingState = true;

				setTimeout( function() {
					window.location.reload();
				}, 1000 );
			},

			licenseAction: function() {
				var self = this;

				self.ajaxLicenseAction = jQuery.ajax( {
					type: 'POST',
					url: window.JetDashboardConfig.ajaxUrl,
					dataType: 'json',
					data: {
						action: 'jet_license_action',
						data: {
							license: self.licenseKey,
							action: 'activate',
							nonce: window.JetDashboardConfig.nonce,
						}
					},
					beforeSend: function( jqXHR, ajaxSettings ) {

						if ( null !== self.ajaxLicenseAction ) {
							self.ajaxLicenseAction.abort();
						}

						self.licenseActionProcessed = true;
					},
					success: function( responce, textStatus, jqXHR ) {
						self.licenseActionProcessed = false;

						self.$CXNotice.add( {
							message: responce.message,
							type: responce.status,
							duration: 3000,
						} );

						if ( 'success' === responce.status ) {

							self.addLicense( {
								'licenseKey': self.licenseKey,
								'licenseStatus': 'active',
								'licenseDetails': responce.data,
							} );
						}
					}
				} );
			}
		}

	} );

})();
