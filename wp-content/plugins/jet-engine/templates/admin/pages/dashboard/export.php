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
			<?php _e( 'Export Skin', 'jet-engine' ); ?>
		</div>
		<div class="jet-engine-skins__header-desc"><?php
			_e( 'Export combination of post types, related taxonomies and listing items as new skin', 'jet-engine' );
		?></div>
	</div>
	<div
		class="jet-engine-skins__content"
		v-if="isActive"
	>
		<div class="cx-vui-inner-panel">
			<div class="cx-vui-subtitle"><?php
				_e( 'Export settings', 'jet-engine' );
			?></div>
			<div class="jet-engine-skins-settings-grid">
				<div class="jet-engine-skins-settings-item">
					<cx-vui-checkbox
						name="post_types"
						label="<?php _e( 'Post types', 'jet-engine' ); ?>"
						return-type="array"
						:wrapper-css="[ 'vertical-fullwidth' ]"
						:options-list="postTypes"
						v-model="skin.post_types"
					></cx-vui-checkbox>
				</div>
				<div class="jet-engine-skins-settings-item">
					<cx-vui-checkbox
						name="taxonomies"
						label="<?php _e( 'Taxonomies', 'jet-engine' ); ?>"
						return-type="array"
						:wrapper-css="[ 'vertical-fullwidth' ]"
						:options-list="taxonomies"
						v-model="skin.taxonomies"
					></cx-vui-checkbox>
				</div>
				<div class="jet-engine-skins-settings-item">
					<cx-vui-checkbox
						name="meta_boxes"
						label="<?php _e( 'Meta boxes', 'jet-engine' ); ?>"
						return-type="array"
						:wrapper-css="[ 'vertical-fullwidth' ]"
						:options-list="metaBoxes"
						v-model="skin.meta_boxes"
					></cx-vui-checkbox>
				</div>
				<div class="jet-engine-skins-settings-item">
					<cx-vui-checkbox
						name="relations"
						label="<?php _e( 'Relations', 'jet-engine' ); ?>"
						return-type="array"
						:wrapper-css="[ 'vertical-fullwidth' ]"
						:options-list="relations"
						v-model="skin.relations"
					></cx-vui-checkbox>
				</div>
				<div class="jet-engine-skins-settings-item">
					<cx-vui-checkbox
						name="options_pages"
						label="<?php _e( 'Options Pages', 'jet-engine' ); ?>"
						return-type="array"
						:wrapper-css="[ 'vertical-fullwidth' ]"
						:options-list="optionsPages"
						v-model="skin.options_pages"
					></cx-vui-checkbox>
				</div>
				<div class="jet-engine-skins-settings-item">
					<cx-vui-checkbox
						name="glossaries"
						label="<?php _e( 'Glossaries', 'jet-engine' ); ?>"
						return-type="array"
						:wrapper-css="[ 'vertical-fullwidth' ]"
						:options-list="glossariesList"
						v-model="skin.glossaries"
					></cx-vui-checkbox>
				</div>
				<div class="jet-engine-skins-settings-item">
					<cx-vui-checkbox
						name="queries"
						label="<?php _e( 'Queries', 'jet-engine' ); ?>"
						return-type="array"
						:wrapper-css="[ 'vertical-fullwidth' ]"
						:options-list="queriesList"
						v-model="skin.queries"
					></cx-vui-checkbox>
				</div>
				<div class="jet-engine-skins-settings-item">
					<cx-vui-checkbox
						name="listing_items"
						label="<?php _e( 'Listing items', 'jet-engine' ); ?>"
						return-type="array"
						:wrapper-css="[ 'vertical-fullwidth' ]"
						:options-list="listingItems"
						v-model="skin.listing_items"
					></cx-vui-checkbox>
				</div>

				<?php do_action( 'jet-engine/dashboard/templates/export/after-items' ); ?>

			</div>
			<cx-vui-switcher
				name="sample_content"
				:wrapper-css="[ 'sample-content' ]"
				label="<?php _e( 'Sample content', 'jet-engine' ); ?>"
				v-model="skin.sample_content"
			></cx-vui-switcher>
			<cx-vui-button
				button-style="accent"
				@click="goToExport"
			><span slot="label"><?php _e( 'Export', 'jet-engine' ); ?></span></cx-vui-button>
		</div>
	</div>
</div>
