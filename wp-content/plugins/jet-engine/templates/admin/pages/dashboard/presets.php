<?php
/**
 * Export skin component template
 */
?>
<div
	:class="{
		'jet-engine-skins': true,
		'jet-engine-skins--active': isActive,
	}"
>
	<div
		class="jet-engine-skins__header"
		@click="isActive = !isActive"
	>
		<div class="jet-engine-skins__header-label">
			<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 13.9999L14 -0.00012207L0 -0.000121458L6.11959e-07 13.9999L14 13.9999Z" fill="white"/><path d="M5.32911 1L11 7L5.32911 13L4 11.5938L8.34177 7L4 2.40625L5.32911 1Z" fill="#007CBA"/></svg>
			<?php _e( 'Presets', 'jet-engine' ); ?>
		</div>
		<div class="jet-engine-skins__header-desc"><?php
			_e( 'Import configuration part from preset', 'jet-engine' );
		?></div>
	</div>
	<div
		class="jet-engine-skins__content"
		v-if="isActive"
	>
		<div
			v-for="( preset, presetID ) in presets"
			class="cx-vui-component jet-import-preset"
			:key="presetID"
		>
			<div class="cx-vui-component__meta">
				<div class="cx-vui-component__label">{{ preset.title }}</div>
				<div class="cx-vui-component__desc">
					<div v-html="preset.desc" style="padding: 5px 0 0 0;"></div>
					<div v-if="missDeps( presetID )">
						<b>Warning:</b> this preset requires next modules to be activated before import: <b>{{ getDeps( presetID ) }}</b>
					</div>
				</div>
			</div>
			<div class="cx-vui-component__control">
				<cx-vui-button
					button-style="accent"
					:loading="isLoading( presetID )"
					:disabled="isDisabled( presetID )"
					@click="importPreset( presetID )"
				>
					<span
						slot="label"
					><?php _e( 'Import', 'jet-engine' ); ?></span>
				</cx-vui-button>
			</div>
		</div>
	</div>
	<cx-vui-popup
		v-model="successDialog"
		body-width="600px"
		:show-ok="false"
		cancel-label="<?php _e( 'Close', 'jet-engine' ) ?>"
	>
		<div class="cx-vui-subtitle" slot="title"><?php
			_e( 'Preset Imported!', 'jet-engine' );
		?></div>
		<div slot="content" v-html="successMessage">
		</div>
	</cx-vui-popup>
</div>