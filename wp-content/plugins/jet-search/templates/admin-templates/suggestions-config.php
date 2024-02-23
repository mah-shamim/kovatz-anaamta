<div
	class="jet-search-suggestions-config"
	v-click-outside="hidePopUp"
>
    <cx-vui-button
        @click="showPopUp()"
		class="jet-search-suggestions-button-config"
        button-style="link-accent"
        size="link"
    >
        <template slot="label">
			<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M13.9498 8.78C13.9798 8.53 13.9998 8.27 13.9998 8C13.9998 7.73 13.9798 7.47 13.9398 7.22L15.6298 5.9C15.7798 5.78 15.8198 5.56 15.7298 5.39L14.1298 2.62C14.0298 2.44 13.8198 2.38 13.6398 2.44L11.6498 3.24C11.2298 2.92 10.7898 2.66 10.2998 2.46L9.99976 0.34C9.96976 0.14 9.79976 0 9.59976 0H6.39976C6.19976 0 6.03976 0.14 6.00976 0.34L5.70976 2.46C5.21976 2.66 4.76976 2.93 4.35976 3.24L2.36976 2.44C2.18976 2.37 1.97976 2.44 1.87976 2.62L0.279763 5.39C0.179763 5.57 0.219763 5.78 0.379763 5.9L2.06976 7.22C2.02976 7.47 1.99976 7.74 1.99976 8C1.99976 8.26 2.01976 8.53 2.05976 8.78L0.369763 10.1C0.219763 10.22 0.179763 10.44 0.269763 10.61L1.86976 13.38C1.96976 13.56 2.17976 13.62 2.35976 13.56L4.34976 12.76C4.76976 13.08 5.20976 13.34 5.69976 13.54L5.99976 15.66C6.03976 15.86 6.19976 16 6.39976 16H9.59976C9.79976 16 9.96976 15.86 9.98976 15.66L10.2898 13.54C10.7798 13.34 11.2298 13.07 11.6398 12.76L13.6298 13.56C13.8098 13.63 14.0198 13.56 14.1198 13.38L15.7198 10.61C15.8198 10.43 15.7798 10.22 15.6198 10.1L13.9498 8.78ZM7.99976 11C6.34976 11 4.99976 9.65 4.99976 8C4.99976 6.35 6.34976 5 7.99976 5C9.64976 5 10.9998 6.35 10.9998 8C10.9998 9.65 9.64976 11 7.99976 11Z" fill="#007CBA"/>
			</svg>
			Settings
        </template>
    </cx-vui-button>
	<div
		class="jet-search-suggestions-config-popup"
		v-show="configVisible"
	>
		<div class="jet-search-suggestions-config-popup-setting">
			<div class="jet-search-suggestions-config-popup-setting-wrapper">
				<h4 class="jet-search-suggestions-config-popup-setting__title" >{{ generalSettingsLabels.use_session.title }}</h4>
				<p class="jet-search-suggestions-config-popup-setting__desc">{{ generalSettingsLabels.use_session.desc }}</p>
				<p class="jet-search-suggestions-config-popup-setting__info" v-html="generalSettingsLabels.use_session.info"></p>

				<div class="jet-search-suggestions-config-popup-setting-item jet-search-suggestions-config-popup-setting-item--use-session">
					<div class="jet-search-suggestions-config-popup-setting-item__label">{{ generalSettingsLabels.use_session.label }}</div>
					<div class="jet-search-suggestions-config-popup-setting-item__content">
						<input
							type="checkbox"
							@change="checkboxValidation"
							v-model="settings['use_session']"
						>
					</div>
				</div>
			</div>
			<div class="jet-search-suggestions-config-popup-setting-wrapper" v-show="settings['use_session']">
				<h4 class="jet-search-suggestions-config-popup-setting__title" >{{ generalSettingsLabels.records_limit.title }}</h4>
				<p class="jet-search-suggestions-config-popup-setting__desc">{{ generalSettingsLabels.records_limit.desc }}</p>
				<p class="jet-search-suggestions-config-popup-setting__info" v-html="generalSettingsLabels.records_limit.info"></p>

				<div class="jet-search-suggestions-config-popup-setting-fields">
					<div class="jet-search-suggestions-config-popup-setting-item jet-search-suggestions-config-popup-setting-item--records-limit">
						<div class="jet-search-suggestions-config-popup-setting-item__label">{{ generalSettingsLabels.records_limit.label }}</div>
						<div class="jet-search-suggestions-config-popup-setting-item__content">
							<cx-vui-input
								type="number"
								v-model="settings['records_limit']"
								:min="0"
								:step="1"
								@input-validation="limitValidationHandler"
								@on-blur-validation="limitBlurValidationHandler"
							></cx-vui-input>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="jet-search-suggestions-config-popup-actions">
			<cx-vui-button
				class="jet-search-suggestions-config-popup-button-cancel"
				@click="hidePopUp()"
				button-style="accent-border"
				size="mini"
			>
				<template slot="label"><?php esc_html_e('Cancel', 'jet-search'); ?></template>
			</cx-vui-button>

			<cx-vui-button
				class="jet-search-suggestions-config-popup-button-save"
				@click="saveSettings()"
				button-style="accent"
				size="mini"
				:disabled="saveButtonDisabled"
			>
				<template slot="label"><?php esc_html_e('Save', 'jet-search'); ?></template>
			</cx-vui-button>
		</div>
	</div>
</div>