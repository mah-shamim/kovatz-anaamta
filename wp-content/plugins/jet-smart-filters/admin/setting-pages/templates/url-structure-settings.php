<div class="jet-smart-filters-settings-page jet-smart-filters-settings-page__url-structure">
	<div class="url-structure-type">
		<div class="url-structure-type__header">
			<div class="cx-vui-title"><?php _e( 'URL Structure Type', 'jet-smart-filters' ); ?></div>
			<div class="cx-vui-subtitle"><?php _e( 'List of URL structure types', 'jet-smart-filters' ); ?></div>
			<cx-vui-radio
				name="url_structure_type"
				v-model="settings.url_structure_type"
				:optionsList="data.url_structure_type_options"
			>
			</cx-vui-radio>
		</div>
		<div class="rewritable-post-types"
			v-if="settings.url_structure_type === 'permalink'"
		>
			<div class="rewritable-post-types__header">
				<div class="cx-vui-title"><?php _e( 'Rewritable Post Types', 'jet-smart-filters' ); ?></div>
				<div class="cx-vui-subtitle"><?php _e( 'Post Types and their Taxonomies for which permalinks will be rewritten', 'jet-smart-filters' ); ?></div>
			</div>
			<div class="rewritable-post-types__list">
				<div
					class="rewritable-post-types__item"
					v-for="( value, prop, index ) in data.rewritable_post_types_options"
				>
					<cx-vui-switcher
						:key="index"
						:name="`rewritable-post-types-${prop}`"
						:label="value"
						:wrapper-css="[ 'equalwidth' ]"
						return-true="true"
						return-false="false"
						v-model="settings.rewritable_post_types[prop]"
					>
					</cx-vui-switcher>
				</div>
			</div>
		</div>
		<div class="url-aliases-section">
			<cx-vui-switcher
				class="use-url-aliases"
				name="use-url-aliases"
				label="<?php _e( 'Use URL Aliases', 'jet-smart-filters' ); ?>"
				description="<?php _e( 'Allow to replace selected parts of the filtered URLs with any alias words you want', 'jet-smart-filters' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				return-true="true"
				return-false="false"
				v-model="settings.use_url_aliases">
			</cx-vui-switcher>
			<cx-vui-repeater
				v-if="settings.use_url_aliases === 'true'"
				class="url-aliases"
				name="url-aliases"
				buttonLabel="<?php _e( 'Add new alias', 'jet-smart-filters' ); ?>"
				buttonSize="mini"
				v-model="settings.url_aliases"
				@add-new-item="repeaterAddItem( {needle: '', replacement: ''}, settings.url_aliases )"
			>
				<cx-vui-repeater-item
					v-for="( alias, index ) in settings.url_aliases"
					class="url-alias"
					:class="{ 'cx-vui-repeater-item--last': settings.url_aliases.length == 1 }"
					:index="index"
					@delete-item="repeaterDeleteItem( index, settings.url_aliases )"
				>
					<cx-vui-input
						class="url-alias-needle"
						placeholder="<?php _e( 'Needle', 'jet-smart-filters' ); ?>"
						size="fullwidth"
						:value="alias.needle"
						@on-keypress="onAliasInputEvent($event, index, 'needle')"
						@on-blur="onAliasInputEvent($event, index, 'needle')"
					></cx-vui-input>
					<cx-vui-input
						class="url-alias-replacement"
						placeholder="<?php _e( 'Replacement', 'jet-smart-filters' ); ?>"
						size="fullwidth"
						:value="alias.replacement"
						@on-keypress="onAliasInputEvent($event, index, 'replacement')"
						@on-blur="onAliasInputEvent($event, index, 'replacement')"
					></cx-vui-input>
				</cx-vui-repeater-item>
			</cx-vui-repeater>
		</div>
		<div v-if="settings.use_url_aliases === 'true'"
			 class="url-aliases-example-section">
			<cx-vui-switcher
				class="use-url-aliases-example"
				name="use-url-aliases-example"
				label="<?php _e( 'See how the aliases will look', 'jet-smart-filters' ); ?>"
				return-true="true"
				return-false="false"
				v-model="settings.use_url_aliases_example">
			</cx-vui-switcher>
			<jsf-url-aliases-example
				v-if="settings.use_url_aliases_example === 'true'"
				v-model="settings.url_aliases_example"
				:aliases="settings.url_aliases"
				urlPrefix="<?php echo home_url(); ?>"
				:defaultUrl="data.url_aliases_example_default"
			/>
		</div>
	</div>
</div>
