<div
	class="jet-dashboard-welcome-page"
	:class="{ 'proccesing-state': proccesingState }"
>
	<div class="jet-dashboard-welcome-page__sidebar">
		<div class="jet-dashboard-page__panel jet-dashboard-welcome-page__updates">
			<div class="jet-dashboard-page__panel-header">
				<div class="panel-header-icon">
					<svg width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M11.55 4.55L7 0V3.07C3.06 3.56 0 6.92 0 11C0 15.08 3.05 18.44 7 18.93V16.91C4.16 16.43 2 13.97 2 11C2 8.03 4.16 5.57 7 5.09V9L11.55 4.55ZM15.93 10C15.76 8.61 15.21 7.27 14.31 6.11L12.89 7.53C13.43 8.28 13.77 9.13 13.91 10H15.93ZM9 16.9V18.92C10.39 18.75 11.74 18.21 12.9 17.31L11.46 15.87C10.71 16.41 9.87 16.76 9 16.9ZM12.89 14.48L14.31 15.89C15.21 14.73 15.76 13.39 15.93 12H13.91C13.77 12.87 13.43 13.72 12.89 14.48Z" fill="#7B7E81"/>
					</svg>
					<div
						class="jet-dashboard-page-badge"
						v-if="0 !== updatePluginCount"
					>
						<span>{{ updatePluginCount }}</span>
					</div>
				</div>
				<div class="panel-header-content">
					<span class="panel-header-desc"><?php _e( 'Manage plugins', 'jet-dashboard' ); ?></span>
					<div class="panel-header-title"><?php _e( 'Updates', 'jet-dashboard' ); ?></div>
				</div>
			</div>
			<div class="jet-dashboard-page__panel-content">
				<div class="jet-dashboard-welcome-page__short-info" v-html="updatesMessage"></div>
			</div>
			<div class="jet-dashboard-page__panel-controls" v-if="0 !== updatePluginCount">
				<cx-vui-button
					button-style="link-accent"
					size="link"
					@click="navigateToLicensePage"
					v-if="isLicenseActivated"
				>
					<span slot="label">
						<span><?php _e( 'Update Now', 'jet-dashboard' ); ?></span>
					</span>
				</cx-vui-button>
				<cx-vui-button
					button-style="link-accent"
					size="link"
					@click="navigateToLicenseManager"
					v-if="!isLicenseActivated"
				>
					<span slot="label">
						<span><?php _e( 'Activate Now', 'jet-dashboard' ); ?></span>
					</span>
				</cx-vui-button>
			</div>
		</div>
		<div
			class="jet-dashboard-page__panel jet-dashboard-welcome-page__licenses"
		>
			<div class="jet-dashboard-page__panel-header">
				<div class="panel-header-icon">
					<svg width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M11.65 4C10.83 1.67 8.61 0 6 0C2.69 0 0 2.69 0 6C0 9.31 2.69 12 6 12C8.61 12 10.83 10.33 11.65 8H16V12H20V8H22V4H11.65ZM6 8C4.9 8 4 7.1 4 6C4 4.9 4.9 4 6 4C7.1 4 8 4.9 8 6C8 7.1 7.1 8 6 8Z" fill="#7B7E81"/>
					</svg>
				</div>
				<div class="panel-header-content">
					<span class="panel-header-desc"><?php _e( 'Manage license', 'jet-dashboard' ); ?></span>
					<div class="panel-header-title"><?php _e( 'Active Licence', 'jet-dashboard' ); ?></div>
				</div>
			</div>
			<div class="jet-dashboard-page__panel-content">
				<div class="licence-list">
					<div
						class="licence-item"
						v-for="(licenceItem, index) in licenseList"
						:key="index"
					>
						<div class="licence-item-icon">
							<svg v-if="'crocoblock' === licenceItem.licenseDetails.type" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <circle cx="12" cy="12" r="12" fill="url(#croco_icon_linear_gradient)"/><path d="M12.5499 8.85524C13.4047 8.85524 14.0975 8.16229 14.0975 7.3076C14.0975 6.4529 13.4047 5.75995 12.5499 5.75995C12.5484 5.75995 12.5471 5.76015 12.5457 5.76015C9.12877 5.76238 6.35938 8.53299 6.35938 11.9505C6.35938 15.3695 9.131 18.141 12.5499 18.141C13.4047 18.141 14.0975 17.4481 14.0975 16.5934C14.0975 15.7387 13.4047 15.0457 12.5499 15.0457C12.5494 15.0457 12.5489 15.0458 12.5484 15.0458C10.8397 15.045 9.45457 13.6595 9.45457 11.9505C9.45457 10.2411 10.8404 8.85524 12.5499 8.85524Z" fill="white"/><defs><linearGradient id="croco_icon_linear_gradient" x1="21.75" y1="5.625" x2="3.5625" y2="20.4375" gradientUnits="userSpaceOnUse"><stop stop-color="#3DDDC1"/><stop offset="1" stop-color="#5099E6"/></linearGradient></defs></svg>
							<svg v-if="'tm' === licenceItem.licenseDetails.type" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="12" fill="#329EF4"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16.0231 14.3843L17.6892 15.6128C18.2629 14.9999 18.7487 14.2952 19.1244 13.5208C19.0325 13.1324 18.9133 12.7555 18.7683 12.3929C18.633 12.0545 18.4754 11.7286 18.298 11.4168C18.4373 11.0828 18.5535 10.7358 18.6446 10.3778C18.7977 9.77536 18.8799 9.1422 18.8799 8.48869C18.8799 8.14032 18.8561 7.7979 18.8112 7.46263L16.4286 9.22006C16.1918 9.03072 15.9439 8.85616 15.686 8.69776C14.6026 8.03235 13.3435 7.65097 12 7.65097C10.6565 7.65097 9.39743 8.03235 8.31398 8.69776C8.05606 8.85616 7.80815 9.03072 7.57141 9.22006L5.18871 7.46263C5.14381 7.7979 5.12009 8.14032 5.12009 8.48869C5.12009 9.1422 5.20225 9.77536 5.35542 10.3778C5.44643 10.7358 5.56264 11.0828 5.702 11.4168C5.52449 11.7286 5.36692 12.0545 5.23166 12.3928C5.08668 12.7555 4.96744 13.1324 4.87557 13.5208C5.25126 14.2952 5.73711 14.9999 6.31077 15.6128L7.97751 14.3837L7.8451 16.895C9.05613 17.6738 10.4785 18.1225 12 18.1225C13.5218 18.1225 14.9444 17.6736 16.1555 16.8945L16.0231 14.3843ZM19.2191 11.3546C19.3674 11.6486 19.5002 11.9525 19.616 12.2655C19.7779 12.703 19.9069 13.1578 20 13.6267C19.8056 14.0646 19.5804 14.4843 19.3265 14.8819C19.0443 15.3237 18.7273 15.7385 18.3796 16.122C18.184 16.3377 17.9788 16.5434 17.7647 16.7386L16.9478 16.1361L17.0118 17.3526C16.7531 17.5413 16.4848 17.7163 16.2071 17.8755C14.9564 18.5926 13.5232 19 12 19C10.4771 19 9.04411 18.5928 7.79347 17.8758C7.51581 17.7167 7.24748 17.5417 6.9888 17.3531L7.05289 16.1355L6.23529 16.7386C6.02111 16.5434 5.81591 16.3377 5.6204 16.122C5.27271 15.7384 4.9556 15.3236 4.67348 14.8819C4.41954 14.4843 4.19437 14.0646 4 13.6267C4.09308 13.1578 4.22203 12.703 4.38391 12.2655C4.49975 11.9525 4.63259 11.6486 4.78083 11.3546C4.46338 10.4631 4.28929 9.49742 4.28929 8.48869C4.28929 7.95465 4.33849 7.43286 4.43138 6.92752L4.43514 6.90681C4.49274 6.5979 4.56686 6.2953 4.65649 6L7.57098 8.14971C8.84723 7.27907 10.3676 6.77347 12 6.77347C13.6323 6.77347 15.1527 7.27907 16.429 8.14971L19.3435 6C19.4331 6.2953 19.5072 6.5979 19.5648 6.90681L19.5685 6.92749C19.6615 7.43283 19.7106 7.95465 19.7106 8.48869C19.7106 9.49742 19.5365 10.4631 19.2191 11.3546ZM9.45223 11.3756C9.53785 12.7835 10.6455 13.8977 12.0001 13.8977C13.3548 13.8977 14.4624 12.7835 14.5481 11.3756C14.2666 11.4415 13.9813 11.4966 13.6927 11.5407C13.6357 11.8582 13.5008 12.1465 13.3096 12.3825L12.442 11.7425L12.505 12.9407C12.3454 12.9923 12.1758 13.0202 12.0001 13.0202C11.1587 13.0202 10.4586 12.383 10.3076 11.5407C10.019 11.4966 9.7337 11.4414 9.45223 11.3756ZM15.3845 10.2252H15.3847V11.2023C15.3837 13.1758 13.8688 14.7752 12.0001 14.7752C10.1309 14.7752 8.61551 13.1746 8.61551 11.2002V10.2252H8.6158C8.65469 10.2387 8.69374 10.2518 8.73284 10.2648C8.99606 10.3521 9.26351 10.4292 9.53489 10.4956C9.80423 10.5614 10.0774 10.6166 10.354 10.6609C10.8909 10.7469 11.4406 10.7915 12.0001 10.7915C12.5597 10.7915 13.1094 10.7469 13.6463 10.6609C13.9228 10.6166 14.1961 10.5614 14.4654 10.4956C14.7368 10.4292 15.0042 10.3521 15.2674 10.2648C15.3065 10.2518 15.3456 10.2387 15.3845 10.2252Z" fill="white"/></svg>
							<svg v-if="'envato' === licenceItem.licenseDetails.type" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="12" fill="#7DB443"/><path d="M17 11.967C16.9862 12.6641 16.9862 13.4399 16.7585 14.1745C16.2893 15.6961 15.4959 16.9441 14.0884 17.6112C13.3191 17.9748 12.4946 18.0272 11.6701 17.9898C10.7697 17.9485 9.91074 17.7162 9.13454 17.1952C8.03752 16.4607 7.41311 15.3738 7.14058 14.0283C6.96464 13.1438 6.93014 12.2593 7.17508 11.3823C7.39931 10.5803 7.82708 9.91694 8.35489 9.32104C8.37904 9.2948 8.40664 9.26857 8.43424 9.24983C8.52048 9.18986 8.61018 9.11865 8.72057 9.18237C8.83096 9.24233 8.83441 9.35852 8.83441 9.4747C8.83441 9.70332 8.75507 9.91319 8.70677 10.1306C8.53428 10.9289 8.40319 11.7309 8.48254 12.5592C8.53083 13.0764 8.66537 13.5598 8.98275 13.9683C9.01725 14.0096 9.05175 14.0508 9.08969 14.092C9.14834 14.1557 9.21389 14.2157 9.30358 14.1745C9.38292 14.137 9.39327 14.0508 9.39327 13.9683C9.40362 13.7322 9.39672 13.4924 9.41742 13.2563C9.60716 11.0825 10.2799 9.1299 11.6943 7.53707C12.4739 6.65258 13.4502 6.18785 14.5507 6.0192C15.1371 5.92925 15.5925 6.15412 15.903 6.72004C16.3307 7.50334 16.5791 8.3541 16.7516 9.23484C16.9241 10.1118 16.9897 11.0001 17 11.967Z" fill="#FEFFFE"/></svg>
						</div>
						<div class="licence-item-info">
							<div class="licence-item-name">{{ licenceItem.licenseDetails.product_name }}</div>
							<div class="licence-item-expire" v-html="getLicenseExpireMessage( licenceItem.licenseDetails.expire )"></div>
						</div>
					</div>
				</div>
				<div
					class="jet-dashboard-welcome-page__short-info"
					v-if="!isLicenseActivated"
				><?php _e( 'License not active', 'jet-dashboard' ); ?></div>
			</div>
			<div class="jet-dashboard-page__panel-controls">
				<cx-vui-button
					button-style="link-accent"
					size="link"
					@click="navigateToLicenseManager"
				>
					<span slot="label">
						<span v-if="isLicenseActivated"><?php _e( 'Manage Now', 'jet-dashboard' ); ?></span>
						<span v-if="!isLicenseActivated"><?php _e( 'Activate Now', 'jet-dashboard' ); ?></span>
					</span>
				</cx-vui-button>
			</div>
		</div>
		<jet-dashboard-inner-component
			:config="innerComponentConfig"
		></jet-dashboard-inner-component>
	</div>
	<div class="jet-dashboard-welcome-page__content">
		<div
			class="jet-dashboard-page__panel jet-dashboard-welcome-page__registered-plugins"
		>
			<div class="cx-vui-subtitle">
				<span class="cx-vui-subtitle__label"><?php _e( 'Installed JetPlugins', 'jet-dashboard' ); ?></span>
			</div>
			<div class="plugin-list plugin-list--registered-plugins">
				<plugin-item-registered
					v-for="( pluginData, index ) in registeredPluginList"
					:key="index"
					:plugin-data="pluginData"
				></plugin-item-registered>
			</div>
		</div>
		<div
			class="jet-dashboard-page__panel jet-dashboard-welcome-page__avaliable-plugins"
			v-if="0 !== avaliablePluginCount"
		>
			<div class="cx-vui-subtitle">
				<span class="cx-vui-subtitle__label" v-html="avaliableToInstallMessage"></span>
				<div class="cx-vui-subtitle__buttons">
					<cx-vui-button
						button-style="accent"
						size="mini"
						@click="navigateToLicensePage"
					>
						<span slot="label">
							<svg class="button-icon" width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M11.8332 5.5H8.49984V0.5H3.49984V5.5H0.166504L5.99984 11.3333L11.8332 5.5ZM0.166504 13V14.6667H11.8332V13H0.166504Z" fill="#007CBA"/>
							</svg>
							<span><?php _e( 'Install', 'jet-dashboard' ); ?></span>
						</span>
					</cx-vui-button>
				</div>
			</div>
		</div>

		<div
			class="jet-dashboard-page__panel jet-dashboard-welcome-page__wizard-section"
			v-if="wizardSectionsVisible"
		>
			<div class="cx-vui-subtitle">

				<span class="cx-vui-subtitle__label">
					<svg width="54" height="54" viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M0 4C0 1.79086 1.79086 0 4 0H50C52.2091 0 54 1.79086 54 4V50C54 52.2091 52.2091 54 50 54H4C1.79086 54 0 52.2091 0 50V4Z" fill="#F3F5FC"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M23.178 10.7416C23.2928 10.2404 23.8432 9.9168 24.4074 10.0188L42.1644 13.2281C42.9239 13.3653 43.2552 14.1646 42.7774 14.7066L33.3593 25.3901C32.8815 25.9321 33.2128 26.7313 33.9722 26.8686L38.7133 27.7255C39.5018 27.868 39.819 28.7165 39.2832 29.2497L23.7697 44.6868C23.061 45.392 21.7582 44.8117 21.9704 43.8854L24.7349 31.8146C24.8497 31.3134 24.4854 30.8244 23.9212 30.7224L19.8349 29.9839C19.2707 29.8819 18.9063 29.393 19.0211 28.8918L23.178 10.7416Z" fill="#8446F9"/>
						<path d="M26.2369 22.288L25.8609 21.9584L26.2369 22.288C26.5157 21.9699 26.5653 21.5564 26.4216 21.2078C26.2801 20.8646 25.9645 20.6094 25.5672 20.5371L14.469 18.5199C13.8993 18.4163 13.2648 18.7358 13.1237 19.3552L10.5257 30.7638C10.3794 31.406 10.8522 31.9478 11.4324 32.0533L13.9863 32.5175C14.0426 32.5277 14.074 32.5542 14.0874 32.5723C14.0996 32.5887 14.0978 32.5967 14.0968 32.601L12.369 40.1884C12.2367 40.7694 12.5974 41.2124 12.9985 41.3921C13.3969 41.5706 13.9424 41.5492 14.3347 41.1565L24.0307 31.4532C24.3399 31.1438 24.4136 30.7193 24.2779 30.3545C24.1445 29.9954 23.8226 29.7247 23.4102 29.6498L20.4471 29.1112C20.3698 29.0971 20.3445 29.058 20.3384 29.0433C20.3349 29.0349 20.3353 29.0301 20.3356 29.0283C20.3357 29.0279 20.3365 29.0194 20.3506 29.0034L26.2369 22.288Z" fill="#EC39C4" stroke="#F3F5FC"/>
					</svg>
					<span v-html="wizardSectionsTitle"></span>
				</span>
				<div class="cx-vui-subtitle__buttons">
					<cx-vui-button
						button-style="accent"
						size="mini"
						@click="wizardActionHandle"
						:loading="actionPluginProcessed"
						v-if="!isWizardInstalled"
					>
						<span slot="label">
							<svg class="button-icon" width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M11.8332 5.5H8.49984V0.5H3.49984V5.5H0.166504L5.99984 11.3333L11.8332 5.5ZM0.166504 13V14.6667H11.8332V13H0.166504Z" fill="#007CBA"/>
							</svg>
							<span><?php _e( 'Install Wizard', 'jet-dashboard' ); ?></span>
						</span>
					</cx-vui-button>
					<cx-vui-button
						button-style="accent"
						size="mini"
						@click="wizardActionHandle"
						:loading="actionPluginProcessed"
						v-if="isWizardInstalled && !isWizardActivated"
					>
						<span slot="label">
							<span><?php _e( 'Activate Wizard', 'jet-dashboard' ); ?></span>
						</span>
					</cx-vui-button>
				</div>
			</div>
		</div>

		<div
			class="jet-dashboard-page__panel jet-dashboard-welcome-page__avaliable-extras"
			v-if="extrasVisible"
		>
			<div class="cx-vui-subtitle">
				<span class="cx-vui-subtitle__label"><?php _e( 'Enjoy the perks of All-inclusive', 'jet-dashboard' ); ?></span>
			</div>
			<div class="extras-list">
				<extras-item
					v-for="( extraData, index ) in avaliableExtras"
					:key="index"
					:config="extraData"
				></extras-item>
			</div>
		</div>

		<div
			class="jet-dashboard-page__panel jet-dashboard-welcome-page__more-plugins"
			v-if="morePluginsVisible"
		>
			<div class="cx-vui-subtitle">
				<span class="cx-vui-subtitle__label"><?php _e( 'Get More Plugins', 'jet-dashboard' ); ?></span>
			</div>
			<div class="plugin-list plugin-list--more-plugins">
				<plugin-item-more
					v-for="( pluginData, index ) in morePluginList"
					:key="index"
					:plugin-data="pluginData"
				></plugin-item-more>
			</div>
		</div>

		<div
			class="jet-dashboard-page__panel jet-dashboard-welcome-page__get-more-banner"
			v-if="getMoreBannerVisible"
		>
			<div class="get-more-banner-content">
				<div class="desc"><?php _e( 'Get a JetPlugins bundle', 'jet-dashboard' ); ?></div>
				<div class="title"><?php _e( 'Save up to 60%', 'jet-dashboard' ); ?></div>
				<cx-vui-button
					class="cx-vui-button--style-accent"
					button-style="default"
					size="mini"
					:url="getMoreBannerLink"
					tag-name="a"
					target="_blank"
				>
					<span slot="label">
						<span><?php _e( 'See all plans', 'jet-dashboard' ); ?></span>
					</span>
				</cx-vui-button>
			</div>

		</div>

		<div
			class="jet-dashboard-page__panel jet-dashboard-welcome-page__avaliable-offers"
			v-if="offersVisible"
		>
			<div class="cx-vui-subtitle">
				<span class="cx-vui-subtitle__label"><?php _e( 'Hot Deals', 'jet-dashboard' ); ?></span>
			</div>
			<div class="offers-list">
				<offers-item
					v-for="( offerData, index ) in avaliableOffers"
					:key="index"
					:config="offerData"
				></offers-item>
			</div>
		</div>

	</div>
</div>
