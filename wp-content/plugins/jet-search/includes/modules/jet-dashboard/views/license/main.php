<div
	class="jet-dashboard-license-page"
	:class="{ 'proccesing-state': proccesingState }"
>
	<div
		class="installed-plugins jet-dashboard-page__panel"
		v-if="installedPluginListVisible"
	>
		<div class="cx-vui-subtitle cx-vui-subtitle--controls">
			<span class="cx-vui-subtitle__label"><?php _e( 'Your Installed JetPlugins', 'jet-dashboard' ); ?></span>
			<div class="cx-vui-subtitle__buttons">
				<cx-vui-button
					button-style="accent"
					size="mini"
					:loading="checkUpdatesProcessed"
					@click="checkPluginsUpdate"
				>
					<span slot="label">
						<svg class="button-icon" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M11.7085 2.29171C10.5001 1.08337 8.8418 0.333374 7.00013 0.333374C3.3168 0.333374 0.341797 3.31671 0.341797 7.00004C0.341797 10.6834 3.3168 13.6667 7.00013 13.6667C10.1085 13.6667 12.7001 11.5417 13.4418 8.66671H11.7085C11.0251 10.6084 9.17513 12 7.00013 12C4.2418 12 2.00013 9.75837 2.00013 7.00004C2.00013 4.24171 4.2418 2.00004 7.00013 2.00004C8.38346 2.00004 9.6168 2.57504 10.5168 3.48337L7.83346 6.16671H13.6668V0.333374L11.7085 2.29171Z" fill="#007CBA"/>
						</svg>
						<span><?php _e( 'Check For Updates', 'jet-dashboard' ); ?></span>
					</span>
				</cx-vui-button>
				<cx-vui-button
					class="license-manager-button"
					button-style="accent"
					size="mini"
					@click="showLicenseManager"
				>
					<span slot="label">
						<svg class="button-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M15.4985 0H12.4897C12.4166 0 12.3487 0.0156709 12.286 0.0470127C12.2338 0.0679073 12.1867 0.104473 12.145 0.156709L5.7669 6.47209C5.62063 6.44074 5.46392 6.41463 5.29677 6.39373C5.12961 6.37284 4.96768 6.36239 4.81097 6.36239C4.16324 6.36239 3.54685 6.48776 2.9618 6.73849C2.37675 6.97878 1.85961 7.32354 1.41038 7.77277C0.961149 8.222 0.611166 8.73914 0.360431 9.32419C0.120144 9.90924 0 10.5309 0 11.189C0 11.8368 0.120144 12.4532 0.360431 13.0382C0.611166 13.6232 0.961149 14.1404 1.41038 14.5896C1.85961 15.0389 2.37675 15.3836 2.9618 15.6239C3.54685 15.8746 4.16324 16 4.81097 16C5.46915 16 6.09076 15.8746 6.67581 15.6239C7.26086 15.3836 7.778 15.0389 8.22723 14.5896C8.80183 14.015 9.19882 13.3464 9.41822 12.5837C9.64806 11.8211 9.68462 11.0375 9.52791 10.2331L10.8913 8.86974C10.9331 8.82795 10.9644 8.78093 10.9853 8.7287C11.0167 8.66601 11.0323 8.59811 11.0323 8.52498V7.02057H12.5367C12.6934 7.02057 12.8136 6.97356 12.8972 6.87953C12.9912 6.7855 13.0382 6.66536 13.0382 6.5191V5.01469H14.5426C14.6157 5.01469 14.6784 5.00424 14.7307 4.98335C14.7933 4.95201 14.8508 4.91022 14.903 4.85798L15.906 3.85504C15.9269 3.81326 15.9478 3.76624 15.9687 3.71401C15.9896 3.65132 16 3.58342 16 3.51028V0.501469C16 0.355207 15.953 0.235064 15.859 0.141038C15.7649 0.0470127 15.6448 0 15.4985 0ZM4.96768 12.7875C4.79008 12.9651 4.5968 13.0957 4.38786 13.1792C4.18936 13.2524 3.96474 13.2889 3.71401 13.2889C3.46327 13.2889 3.23343 13.2419 3.02449 13.1479C2.82599 13.0539 2.63794 12.9337 2.46033 12.7875C2.28273 12.6099 2.15214 12.4218 2.06856 12.2233C1.99543 12.0144 1.95886 11.7845 1.95886 11.5338C1.95886 11.2831 2.00588 11.0584 2.0999 10.8599C2.19393 10.651 2.31407 10.4577 2.46033 10.2801C2.7842 9.95625 3.19164 9.79432 3.68266 9.79432C4.18413 9.79432 4.5968 9.95625 4.92067 10.2801C5.09827 10.4577 5.22364 10.651 5.29677 10.8599C5.38035 11.0584 5.42214 11.2831 5.42214 11.5338C5.42214 11.7845 5.38035 12.0144 5.29677 12.2233C5.22364 12.4218 5.11394 12.6099 4.96768 12.7875Z" fill="#D3D3D3"/>
						</svg>
						<span><?php _e( 'License Manager', 'jet-dashboard' ); ?></span>
					</span>
				</cx-vui-button>
			</div>
		</div>
		<div class="plugin-list plugin-list--installed-plugins">
			<plugin-item-installed
				v-for="( pluginData, index ) in installedPluginList"
				:key="index"
				:plugin-data="pluginData"
			></plugin-item-installed>
		</div>
	</div>

	<div
		class="avaliable-plugins jet-dashboard-page__panel"
		v-if="avaliablePluginListVisible"
	>
		<div class="cx-vui-subtitle">
			<span class="cx-vui-subtitle__label"><?php _e( 'More included JetPlugins with your licence', 'jet-dashboard' ); ?></span>
		</div>
		<div class="plugin-list plugin-list--avaliable-plugins">
			<plugin-item-avaliable
				v-for="( pluginData, index ) in avaliablePluginList"
				:key="index"
				:plugin-data="pluginData"
			></plugin-item-avaliable>
		</div>
	</div>

	<div
		class="more-plugins jet-dashboard-page__panel"
		v-if="morePluginListVisible"
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

	<transition name="popup">
		<cx-vui-popup
			class="license-activation-popup"
			v-model="licensePopupVisible"
			:header="false"
			:footer="false"
			@on-cancel='maybeClearSubpageParam'
		>
			<div class="license-manager" slot="content">
				<license-item
					:license-data="newlicenseData"
					type="single-item"
				></license-item>
			</div>
		</cx-vui-popup>
	</transition>
	<transition name="popup">
		<cx-vui-popup
			class="license-deactivation-popup"
			v-model="deactivatePopupVisible"
			:footer="false"
			body-width="520px"
		>
			<div slot="title">
				<div class="cx-vui-popup__header-label"><?php _e( 'JetPlugins License Deactivation', 'jet-dashboard' ); ?></div>
			</div>
			<div slot="content">
				<p><?php _e( 'Your license includes several plugins within the package. License deactivation in one plugin disables it in the rest of them. You can manage it through the License Manager.', 'jet-dashboard' ); ?></p>
				<cx-vui-button
					class="show-license-manager"
					button-style="accent"
					size="mini"
					@click="showLicenseManager"
				>
					<span slot="label"><?php _e( 'License Manager', 'jet-dashboard' ); ?></span>
				</cx-vui-button>
			</div>
		</cx-vui-popup>
	</transition>
	<transition name="popup">
		<cx-vui-popup
			class="update-check-popup"
			v-model="updateCheckPopupVisible"
			:footer="false"
			body-width="520px"
		>
			<div slot="title">
				<div class="cx-vui-popup__header-label"><?php _e( 'JetPlugin’s Update', 'jet-dashboard' ); ?></div>
			</div>
			<div slot="content">
				<svg width="91" height="100" viewBox="0 0 91 100" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M16.748 43.3361C20.4863 43.3361 23.4143 38.9426 23.4143 33.336C23.4143 27.7301 20.4863 23.3365 16.748 23.3365C13.0098 23.3365 10.0818 27.7301 10.0818 33.336C10.0818 38.9426 13.0098 43.3361 16.748 43.3361ZM16.748 26.6697C18.3212 26.6697 20.0812 29.5199 20.0812 33.336C20.0812 37.1528 18.3212 40.0022 16.748 40.0022C15.1749 40.0022 13.4149 37.1528 13.4149 33.336C13.4149 29.5199 15.1749 26.6697 16.748 26.6697Z" fill="black"/>
					<path d="M48.4132 43.3361C52.1515 43.3361 55.0795 38.9426 55.0795 33.336C55.0795 27.7301 52.1515 23.3365 48.4132 23.3365C44.675 23.3365 41.747 27.7301 41.747 33.336C41.747 38.9426 44.675 43.3361 48.4132 43.3361ZM48.4132 26.6697C49.9871 26.6697 51.7464 29.5199 51.7464 33.336C51.7464 37.1528 49.9871 40.0022 48.4132 40.0022C46.8401 40.0022 45.0801 37.1528 45.0801 33.336C45.0801 29.5199 46.8401 26.6697 48.4132 26.6697V26.6697Z" fill="black"/>
					<path d="M45.914 100C67.2212 100 83.2537 92.375 89.9115 79.079C90.2007 78.5015 90.1305 77.8088 89.7307 77.3015C89.331 76.7941 88.6741 76.5637 88.0455 76.7094C80.154 78.5427 76.682 74.2094 75.546 72.2648C74.1049 69.6915 73.3687 66.7833 73.4122 63.8346V36.6691C73.4137 26.8467 69.4733 17.4347 62.4751 10.5418C55.477 3.64972 46.0055 -0.1465 36.1839 0.00455508C16.2781 0.307429 0.0823603 17.1066 0.0823603 37.4579V70.0012C0.0960926 73.1375 0.813225 76.2311 2.18035 79.0539C8.18214 91.5823 25.758 100 45.914 100ZM3.4155 37.4579C3.4155 18.9185 18.1381 3.61386 36.2327 3.33769H36.7476C55.1558 3.33769 70.079 18.2609 70.079 36.6691V63.8346C70.0355 67.3699 70.9274 70.8534 72.6638 73.9325C75.2538 78.3543 80.1585 80.8886 85.2639 80.4439C78.3871 90.8003 64.2809 96.6671 45.914 96.6671C27.3074 96.6671 10.5601 88.8336 5.18697 77.6196C4.03498 75.2439 3.42923 72.6409 3.4155 70.0012V37.4579Z" fill="black"/>
					<path d="M16.748 80.0007C21.3484 79.9953 25.0759 76.2678 25.0813 71.6682V63.335C25.0813 62.4142 24.3351 61.668 23.4143 61.668C22.4943 61.668 21.7481 62.4142 21.7481 63.335V71.6682C21.7481 74.4292 19.5098 76.6675 16.748 76.6675C13.9871 76.6675 11.7487 74.4292 11.7487 71.6682V63.335C11.7487 62.4142 11.0026 61.668 10.0818 61.668C9.16171 61.668 8.41559 62.4142 8.41559 63.335V71.6682C8.42093 76.2678 12.1485 79.9953 16.748 80.0007Z" fill="black"/>
					<path d="M50.0802 80.0007C54.6798 79.9953 58.4073 76.2678 58.4134 71.6682V63.335C58.4134 62.4142 57.6665 61.668 56.7465 61.668C55.8256 61.668 55.0795 62.4142 55.0795 63.335V71.6682C55.0795 74.4292 52.8412 76.6675 50.0802 76.6675C47.3185 76.6675 45.0801 74.4292 45.0801 71.6682V63.335C45.0801 62.4142 44.334 61.668 43.4139 61.668C42.4931 61.668 41.747 62.4142 41.747 63.335V71.6682C41.7523 76.2678 45.4799 79.9953 50.0802 80.0007V80.0007Z" fill="black"/>
					<path d="M26.1631 49.8971C26.5773 50.0528 27.0366 50.0367 27.4387 49.8529C27.8415 49.6698 28.155 49.3333 28.3091 48.9191C28.5891 48.1539 29.0095 47.4474 29.5473 46.8356C30.0409 46.2695 30.6436 45.8087 31.3196 45.4807C32.644 44.8436 34.1866 44.8436 35.511 45.4807C36.1854 45.8087 36.7873 46.2695 37.2809 46.8356C37.8188 47.4467 38.2391 48.1524 38.5191 48.9176C38.7617 49.5698 39.3842 50.0024 40.0808 50.0024C40.2799 50.0024 40.4783 49.9666 40.6652 49.8971C41.5272 49.5744 41.9644 48.6139 41.6424 47.7526C41.2183 46.6052 40.5858 45.5463 39.7771 44.6293C38.9875 43.7283 38.024 42.9959 36.9437 42.4756C34.7084 41.406 32.1084 41.406 29.8723 42.4756C28.7928 42.9951 27.8293 43.7275 27.0397 44.6293C26.2302 45.5463 25.5985 46.6052 25.1744 47.7526C25.0202 48.1676 25.0378 48.6277 25.2232 49.0297C25.4086 49.4325 25.7465 49.7445 26.1631 49.8971Z" fill="black"/>
				</svg>
				<p><span>Ooops!</span><?php _e( 'Sorry, but you need to activate license to update your JetPlugin', 'jet-dashboard' ); ?></p>
				<cx-vui-button
					class="cx-vui-button--style-accent"
					button-style="default"
					size="mini"
					@click="showPopupActivation"
				>
					<span slot="label"><?php _e( 'Activate License', 'jet-dashboard' ); ?></span>
				</cx-vui-button>
			</div>
		</cx-vui-popup>
	</transition>

	<transition name="popup">
		<cx-vui-popup
			class="license-manager-popup"
			v-model="licenseManagerVisible"
			:footer="false"
			@on-cancel='maybeClearSubpageParam'
		>
			<div class="cx-vui-popup__header-inner" slot="title">
				<div class="cx-vui-popup__header-label"><?php _e( 'Your Licenses', 'jet-dashboard' ); ?></div>
				<cx-vui-button
					class="add-new-license"
					button-style="accent"
					size="mini"
					@click="addNewLicense"
				>
					<span slot="label">
						<span class="dashicons dashicons-plus"></span>
						<span><?php _e( 'Add New License', 'jet-dashboard' ); ?></span>
					</span>
				</cx-vui-button>
			</div>
			<div class="license-manager" slot="content">
				<p v-if="licenseList.length === 0"><?php _e( 'Add and Activate license for automatic updates, awesome support, useful features and more', 'jet-dashboard' ); ?></p>
				<div
					class="license-list"
				>
					<license-item
						v-for="( license, index ) in licenseList"
						:key="index"
						:license-data="license"
						type="listing-item"
					></license-item>
				</div>
			</div>
		</cx-vui-popup>
	</transition>

	<transition name="popup">
		<cx-vui-popup
			class="responce-data-popup"
			v-model="responcePopupVisible"
			:header="false"
			:footer="false"
			body-width="450px"
		>
			<responce-info
				slot="content"
				:responce-data="responceData"

			></responce-info>
		</cx-vui-popup>
	</transition>

</div>
