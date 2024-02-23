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
			<?php _e( 'Import Skin', 'jet-engine' ); ?>
		</div>
		<div class="jet-engine-skins__header-desc"><?php
			_e( 'Import new post types, taxonomies and listing items from JSON file.', 'jet-engine' );
		?></div>
	</div>
	<div
		class="jet-engine-skins__content"
		v-if="isActive"
	>
		<template v-if="log">
			<div class="jet-engine-import__log">
				<div class="jet-engine-import__log-title">
					<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.38498 12.0188L13.5962 4.80751L12.4695 3.64319L6.38498 9.7277L3.53052 6.87324L2.40376 8L6.38498 12.0188ZM2.32864 2.3662C3.9061 0.788732 5.79656 0 8 0C10.2034 0 12.0814 0.788732 13.6338 2.3662C15.2113 3.91862 16 5.79656 16 8C16 10.2034 15.2113 12.0939 13.6338 13.6714C12.0814 15.2238 10.2034 16 8 16C5.79656 16 3.9061 15.2238 2.32864 13.6714C0.776213 12.0939 0 10.2034 0 8C0 5.79656 0.776213 3.91862 2.32864 2.3662Z" fill="#46B450"/></svg>
					<?php _e( 'Imported:', 'jet-engine' ); ?>
				</div>
				<div class="jet-engine-import__log-items">
					<div
						class="jet-engine-import__log-item"
						v-for="logItem in log.success"
					>
						<b v-html="logItem.label + ':'"></b>
						<span v-html="logItems( logItem.items )"></span>
					</div>
				</div>
			</div>
			<div v-if="log.errors.length" class="jet-engine-import__log jet-engine-import__log--error">
				<div class="jet-engine-import__log-title">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z" fill="#c92c2c"/></svg>
					<?php _e( 'Errors:', 'jet-engine' ); ?>
				</div>
				<div class="jet-engine-import__log-items">
					<div
						class="jet-engine-import__log-item"
						v-for="logItem in log.errors"
					>
						<span v-html="logItem"></span>
					</div>
				</div>
			</div>
		</template>
		<div v-else-if="error" class="jet-engine-import__log jet-engine-import__log--error">
			<div class="jet-engine-import__log-title">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z" fill="#c92c2c"/></svg>
				<?php _e( 'Error:', 'jet-engine' ); ?>
			</div>
			<div class="jet-engine-import__log-items">
				<div class="jet-engine-import__log-item">
					<span v-html="error"></span>
				</div>
			</div>
		</div>
		<div class="cx-vui-inner-panel" v-else>
			<p><?php
				_e( 'Choose an JetEngine skin JSON file, and add it to your website.', 'jet-engine' );
			?></p>
			<input type="file" accept=".json,application/json" @change="prepareToImport">
			<div class="jet-engine-import__btn">
				<cx-vui-button
					button-style="accent"
					@click="processImport"
					:loading="isLoading"
					:disabled="!readyToImport"
				><span slot="label"><?php _e( 'Import', 'jet-engine' ); ?></span></cx-vui-button>
			</div>
		</div>
	</div>
</div>