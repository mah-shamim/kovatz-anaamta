<div
	class="license-item"
	:class="{ 'license-activated': isLicenseActive }"
>
	<div
		class="license-item__label"
		v-if="type === 'single-item'"
	>
		<span v-if="!isLicenseActive"><?php _e( 'JetPlugins License Activation', 'jet-dashboard' ); ?></span>
		<span v-if="isLicenseActive"><?php _e( 'Congratulations!', 'jet-dashboard' ); ?><br/><?php _e( 'Your license has been activated!', 'jet-dashboard' ); ?></span>
	</div>
	<div class="license-item__control" v-if="!isLicenseActive">
		<span class="license-item__activation-message"><?php _e( 'Activate license for automatic updates and awesome support', 'jet-dashboard' ); ?></span>
		<cx-vui-input
			class="license-item__key"
			size="fullwidth"
			type="password"
			:autofocus="true"
			:prevent-wrap="true"
			placeholder="Just paste it here"
			:disabled="isLicenseActive"
			v-model="licenseKey"
		></cx-vui-input>

		<cx-vui-button
			class="license-item__action-button"
			button-style="accent"
			size="mini"
			:loading="activationStatus"
			@click="licenseAction"
		>
			<span slot="label" v-if="!isLicenseActive"><?php _e( 'Activate License', 'jet-dashboard' ); ?></span>
			<span slot="label" v-if="isLicenseActive"><?php _e( 'Deactivate License', 'jet-dashboard' ); ?></span>
		</cx-vui-button>
	</div>
	<div
		class="license-item__details"
		v-if="isLicenseActive"
	>
		<!-- <span class="license-details__label">Your License Information</span> -->
		<div class="license-details__fields">

			<div class="license-details__field license-key">
				<span class="label"><?php _e( 'License Key:', 'jet-dashboard' ); ?></span>{{ maskedLicenseKey }}
			</div>

			<div class="license-details__field license-name">
				<span class="label"><?php _e( 'Product Name:', 'jet-dashboard' ); ?></span>{{ productName }}
			</div>

			<div class="license-details__field license-status">
				<span class="label"><?php _e( 'Status:', 'jet-dashboard' ); ?></span>{{ licenseStatus }}
			</div>

			<div class="license-details__field license-type">
				<span class="label"><?php _e( 'Type:', 'jet-dashboard' ); ?></span>{{ licenseType }}
			</div>

			<div class="license-details__field license-expiration-date">
				<span class="label"><?php _e( 'Expiration Date:', 'jet-dashboard' ); ?></span>{{ expireDate }}
			</div>

			<div class="license-details__field license-plugins">
				<span class="label"><?php _e( 'Included Plugins:', 'jet-dashboard' ); ?></span>
				<div class="included-plugin-list">
					<div
						class="included-plugin"
						v-for="( plugin, index ) in licensePlugins"
						:key="index"
					>
						<svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M12 0.799997L10.4 0L4.39998 5.19998L1.59999 3.19999L0 4.39998L4.39998 9.19996L12 0.799997Z" fill="#34D7A1"/>
						</svg>
						<span>{{ plugin.name }}</span>
					</div>
				</div>
			</div>
			<div class="cx-vui-alert info-type">
				<div class="cx-vui-alert__icon">
					<svg width="18" height="21" viewBox="0 0 18 21" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M9 20.5C10.3672 20.5 11.4609 19.4062 11.4609 18H6.5C6.5 19.4062 7.59375 20.5 9 20.5ZM17.3984 14.6797C16.6562 13.8594 15.2109 12.6484 15.2109 8.625C15.2109 5.61719 13.1016 3.19531 10.2109 2.57031V1.75C10.2109 1.08594 9.66406 0.5 9 0.5C8.29688 0.5 7.75 1.08594 7.75 1.75V2.57031C4.85938 3.19531 2.75 5.61719 2.75 8.625C2.75 12.6484 1.30469 13.8594 0.5625 14.6797C0.328125 14.9141 0.210938 15.2266 0.25 15.5C0.25 16.1641 0.71875 16.75 1.5 16.75H16.4609C17.2422 16.75 17.7109 16.1641 17.75 15.5C17.75 15.2266 17.6328 14.9141 17.3984 14.6797Z"/>
					</svg>
				</div>
				<div class="cx-vui-alert__message"><?php _e( 'This license will activate licenses for all plugins included in this set.', 'jet-dashboard' ); ?></div>
			</div>
			<div class="license-details__actions">
				<cx-vui-button
					class="show-license-manager"
					button-style="link-accent"
					size="link"
					@click="showLicenseManager"
					v-if="type === 'single-item'"
				>
					<span slot="label"><?php _e( 'License Manager', 'jet-dashboard' ); ?></span>
				</cx-vui-button>
				<cx-vui-button
					class="cx-vui-button--style-danger"
					button-style="accent"
					size="mini"
					:loading="activationStatus"
					@click="licenseAction"
				>
					<span slot="label" v-if="!isLicenseActive"><?php _e( 'Activate License', 'jet-dashboard' ); ?></span>
					<span slot="label" v-if="isLicenseActive"><?php _e( 'Deactivate License', 'jet-dashboard' ); ?></span>
				</cx-vui-button>
			</div>
		</div>
	</div>
</div>


