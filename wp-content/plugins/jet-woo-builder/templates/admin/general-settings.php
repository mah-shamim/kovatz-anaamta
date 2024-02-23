<div class="jet-woo-builder-settings-page jet-woo-builder-settings-page__general">
	<cx-vui-switcher
		name="enable_product_thumb_effect"
		label="<?php _e( 'Enable Thumbnail Effect', 'jet-woo-builder' ); ?>"
		description="<?php _e( 'Enable product thumbnails to switch on hover', 'jet-woo-builder' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		return-true="true"
		return-false="false"
		v-model="pageOptions['enable_product_thumb_effect'].value"
	>
	</cx-vui-switcher>

	<cx-vui-select
		v-if="'true' === pageOptions['enable_product_thumb_effect'].value"
		name="product_thumb_effect"
		label="<?php _e( 'Thumbnail Effect', 'jet-woo-builder' ); ?>"
		description="<?php _e( 'Choose a hover effect for product thumbnails switching', 'jet-woo-builder' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="pageOptions.product_thumb_effect.options"
		v-model="pageOptions.product_thumb_effect.value"
	>
	</cx-vui-select>

	<cx-vui-switcher
		name="enable_inline_templates_styles"
		label="<?php _e( 'Print Custom Templates CSS Inline', 'jet-woo-builder' ); ?>"
		description="<?php _e( 'Styles will be displayed in front of the template content. This will prevent jumps in styles during page load, but will affect the speed of its loading. This option will affect all custom templates.', 'jet-woo-builder' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		return-true="true"
		return-false="false"
		v-model="pageOptions['enable_inline_templates_styles'].value"
	>
	</cx-vui-switcher>
</div>
