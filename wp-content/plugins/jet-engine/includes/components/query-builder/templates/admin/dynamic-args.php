<?php
/**
 * Dynamic args tempalte
 */
?>
<div
	class="jet-query-macros"
	v-click-outside.capture="onClickOutside"
	v-click-outside:mousedown.capture="onClickOutside"
	v-click-outside:touchstart.capture="onClickOutside"
	@keydown.esc="onClickOutside"
	tabindex="-1"
>
	<div class="jet-query-macros__trigger" @click="switchIsActive()">
		<svg v-if="! isActive && ! result.macrosName" class="jet-query-macros__trigger-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M14 10c2.2 0 4-1.8 4-4s-1.8-4-4-4-4 1.8-4 4 1.8 4 4 4zm-1-5V3h2v2h2v2h-2v2h-2V7h-2V5h2zM9 6c0-1.6.8-3 2-4h-1c-3.9 0-7 .9-7 2 0 1 2.6 1.8 6 2zm1 9c-3.9 0-7-.9-7-2v3c0 1.1 3.1 2 7 2s7-.9 7-2v-3c0 1.1-3.1 2-7 2zm2.8-4.2c-.9.1-1.9.2-2.8.2-3.9 0-7-.9-7-2v3c0 1.1 3.1 2 7 2s7-.9 7-2v-2c-.9.7-1.9 1-3 1-.4 0-.8-.1-1.2-.2zM10 10h1c-1-.7-1.7-1.8-1.9-3C5.7 6.9 3 6 3 5v3c0 1.1 3.1 2 7 2z"/></g></svg>
		<svg v-if="! isActive && result.macrosName" class="jet-query-macros__trigger-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M9 6c0-1.6.8-3 2-4h-1c-3.9 0-7 .9-7 2 0 1 2.6 1.8 6 2zm3.8 4.8c-.9.1-1.9.2-2.8.2-3.9 0-7-.9-7-2v3c0 1.1 3.1 2 7 2s7-.9 7-2v-2c-.9.7-1.9 1-3 1-.4 0-.8-.1-1.2-.2zM10 15c-3.9 0-7-.9-7-2v3c0 1.1 3.1 2 7 2s7-.9 7-2v-3c0 1.1-3.1 2-7 2zm0-5h1c-1-.7-1.7-1.8-1.9-3C5.7 6.9 3 6 3 5v3c0 1.1 3.1 2 7 2zm4 0c2.2 0 4-1.8 4-4s-1.8-4-4-4-4 1.8-4 4 1.8 4 4 4zm-2.3-4.4l1.7 1.7 2.9-2.9.7.7-3.6 3.6L11 6.3l.7-.7z"/></g></svg>
		<svg v-if="isActive" class="jet-query-macros__trigger-close" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M14.95 6.46L11.41 10l3.54 3.54-1.41 1.41L10 11.42l-3.53 3.53-1.42-1.42L8.58 10 5.05 6.47l1.42-1.42L10 8.58l3.54-3.53z"/></g></svg>
	</div>
	<div class="jet-query-macros__value" v-if="result.macrosName">
		<i @click="switchIsActive()">{{ result.macrosName }}</i>
		<div class="jet-query-macros__value-clear" @click="clearResult">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M12 4h3c.6 0 1 .4 1 1v1H3V5c0-.6.5-1 1-1h3c.2-1.1 1.3-2 2.5-2s2.3.9 2.5 2zM8 4h3c-.2-.6-.9-1-1.5-1S8.2 3.4 8 4zM4 7h11l-.9 10.1c0 .5-.5.9-1 .9H5.9c-.5 0-.9-.4-1-.9L4 7z"/></g></svg>
		</div>
	</div>
	<div class="jet-query-macros__popup" v-if="isActive">
		<div class="jet-query-macros__config">
			<span class="jet-query-macros__config-trigger" v-if="! editSettings" @click="advancedSettingsPanel( true )"><?php 
				_e( 'Advanced settings', 'jet-engine' );
			?></span>
			<span class="jet-query-macros__config-trigger" v-else @click="advancedSettingsPanel( false )"><?php 
				_e( 'Back', 'jet-engine' );
			?></span>
		</div>
		<div class="jet-query-macros__content" v-if="editSettings">
			<cx-vui-select
				label="<?php _e( 'Context', 'jet-engine' ); ?>"
				:wrapper-css="[ 'mini-label' ]"
				:options-list="contextList"
				size="fullwidth"
				v-model="advancedSettings.context"
			></cx-vui-select>
			<cx-vui-input
				label="<?php _e( 'Fallback', 'jet-engine' ); ?>"
				:wrapper-css="[ 'mini-label' ]"
				size="fullwidth"
				v-model="advancedSettings.fallback"
			></cx-vui-input>
			<cx-vui-input
				label="<?php _e( 'Before', 'jet-engine' ); ?>"
				:wrapper-css="[ 'mini-label' ]"
				size="fullwidth"
				v-model="advancedSettings.before"
			></cx-vui-input>
			<cx-vui-input
				label="<?php _e( 'After', 'jet-engine' ); ?>"
				:wrapper-css="[ 'mini-label' ]"
				size="fullwidth"
				v-model="advancedSettings.after"
			></cx-vui-input>
		</div>
		<div class="jet-query-macros__content" v-else-if="editMacros">
			<div class="jet-query-macros__title">
				<span class="jet-query-macros__back" @click="resetEdit()"><?php _e( 'All Macros', 'jet-engine' ); ?></span> > {{ currentMacros.name }}:
			</div>
			<div class="jet-query-macros__controls">
				<div class="jet-query-macros__control" v-for="control in getPreparedControls()">
					<component
						:is="control.type"
						:options-list="control.optionsList"
						:groups-list="control.groupsList"
						:label="control.label"
						:wrapper-css="[ 'mini-label' ]"
						size="fullwidth"
						v-if="checkCondition( control.condition )"
						v-model="result[ control.name ]"
					/>
				</div>
			</div>
			<cx-vui-button
				button-style="accent"
				size="mini"
				@click="applyMacros( false, true )"
			><span slot="label"><?php _e( 'Apply', 'jet-engine' ); ?></span></cx-vui-button>
		</div>
		<div class="jet-query-macros__content" v-else-if="! editMacros && ! editSettings">
			<div class="jet-query-macros__list">
				<div class="jet-query-macros-item" v-for="macros in macrosList">
					<div class="jet-query-macros-item__name" @click="applyMacros( macros )">
						<span class="jet-query-macros-item__mark">≫</span>
						{{ macros.name }}
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>
