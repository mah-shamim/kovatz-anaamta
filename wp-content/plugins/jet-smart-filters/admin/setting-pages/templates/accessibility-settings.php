<div
	class="jet-smart-filters-settings-page jet-smart-filters-settings-page__accessibility"
>
	<div class="tabindex">
		<div class="tabindex__header">
			<cx-vui-switcher
				name="use_tabindex"
				label="<?php _e( 'Tabindex', 'jet-smart-filters' ); ?>"
				description="<?php _e( 'Enable filters tabindex functionality', 'jet-smart-filters' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				return-true="true"
				return-false="false"
				v-model="settings.use_tabindex">
			</cx-vui-switcher>
		</div>
		<div class="tabindex__body"
			 v-if="settings.use_tabindex === 'true'"
		>
			<cx-vui-colorpicker
				name="tabindex_color"
				label="<?php _e( 'Tabindex focus color', 'jet-smart-filters' ); ?>"
				type="hex"
				:wrapper-css="[ 'equalwidth' ]"
				v-model="settings.tabindex_color">
			</cx-vui-colorpicker>
		</div>
	</div>
</div>
