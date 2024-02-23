<div
	class="jet-smart-filters-settings-page jet-smart-filters-settings-page__provider-preloader"
>
	<div class="provider-preloader">
		<div class="provider-preloader__header">
			<cx-vui-switcher
				label="<?php _e( 'Provider preloader', 'jet-smart-filters' ); ?>"
				description="<?php _e( 'Enable provider preloader while filtering request is in progress', 'jet-smart-filters' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				return-true="true"
				return-false=""
				v-model="settings.use_provider_preloader"
			/>
		</div>
		<div class="provider-preloader__body"
			 v-if="settings.use_provider_preloader === 'true'"
		>
			<cx-vui-switcher
				label="<?php _e( 'Fixed position', 'jet-smart-filters' ); ?>"
				description="<?php _e( 'Fixes position of the preloader in the center of the screen inside the provider', 'jet-smart-filters' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				return-true="true"
				return-false=""
				v-model="settings.provider_preloader_fixed_position"
			/>
			<cx-vui-input v-if="settings.provider_preloader_fixed_position"
				type="number"
				label="<?php _e( 'Edge gap', 'jet-smart-filters' ); ?>"
				v-model="settings.provider_preloader_fixed_edge_gap"
			/>
			<cx-vui-select
				label="<?php _e( 'Type', 'jet-smart-filters' ); ?>"
				:optionsList="data.provider_preloader_type_options"
				size="fullwidth"
				v-model="settings.provider_preloader_type"
				@on-change="changeProviderPreloaderTemplate(settings.provider_preloader_type)"
			/>
			<cx-vui-input
				type="number"
				label="<?php _e( 'Size', 'jet-smart-filters' ); ?>"
				placeholder="45"
				v-model="settings.provider_preloader_styles.size"
				@on-change="updateProviderPreloaderCSS"
			/>
			<cx-vui-colorpicker
				label="<?php _e( 'Color', 'jet-smart-filters' ); ?>"
				type="hex"
				v-model="settings.provider_preloader_styles.color"
				@on-change="updateProviderPreloaderCSS"
			/>
			<cx-vui-switcher
				label="<?php _e( 'Use background plane', 'jet-smart-filters' ); ?>"
				return-true="true"
				return-false=""
				v-model="settings.provider_preloader_styles.use_bg"
				@on-change="updateProviderPreloaderCSS"
			/>
			<template v-if="settings.provider_preloader_styles.use_bg">
				<cx-vui-dimensions
					label="<?php _e( 'Padding', 'jet-smart-filters' ); ?>"
					:units="[]"
					v-model="settings.provider_preloader_styles.padding"
					@on-change="updateProviderPreloaderCSS"
				/>
				<cx-vui-input
					type="number"
					label="<?php _e( 'Border radius', 'jet-smart-filters' ); ?>"
					v-model="settings.provider_preloader_styles.border_radius"
					@on-change="updateProviderPreloaderCSS"
				/>
				<cx-vui-colorpicker
					label="<?php _e( 'Background color', 'jet-smart-filters' ); ?>"
					type="hex"
					v-model="settings.provider_preloader_styles.bg_color"
					@on-change="updateProviderPreloaderCSS"
				/>
			</template>
			<div class="provider-preloader-preview">
				<div class="provider-preloader-preview__label">
					<?php _e( 'Preloader preview', 'jet-smart-filters' ); ?>
				</div>
				<v-style id="provider-preloader-preview__style">{{ settings.provider_preloader_css }}</v-style>
				<div class="provider-preloader-preview__container">
					<?php echo jet_smart_filters()->provider_preloader->get_template() ?>
				</div>
			</div>
		</div>
	</div>
</div>
