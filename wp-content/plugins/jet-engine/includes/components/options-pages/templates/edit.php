<div
	class="jet-engine-edit-page jet-engine-edit-page--loading"
	:class="{
		'jet-engine-edit-page--loaded': true,
	}"
>
	<div class="jet-engine-edit-page__fields">
		<cx-vui-collapse
			:collapsed="false"
		>
			<h3 class="cx-vui-subtitle" slot="title"><?php _e( 'General Settings', 'jet-engine' ); ?></h3>
			<div class="cx-vui-panel" slot="content">
				<cx-vui-input
					name="page_name"
					label="<?php _e( 'Page title', 'jet-engine' ); ?>"
					description="<?php _e( 'Set unique name for your options page', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					:error="errors.name"
					v-model="generalSettings.name"
					@on-focus="handleFocus( 'name' )"
					@on-input-change="preSetSlug"
				></cx-vui-input>
				<cx-vui-input
					name="page_slug"
					label="<?php _e( 'Page slug', 'jet-engine' ); ?>"
					description="<?php _e( 'Set slug for your options page. Slug should contain only letters, numbers and `-` or `_` chars. This slug also be used to store options into database', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					:error="errors.slug"
					v-model="generalSettings.slug"
					@on-focus="handleFocus( 'slug' )"
				></cx-vui-input>
				<cx-vui-input
					name="menu_name"
					label="<?php _e( 'Menu name', 'jet-engine' ); ?>"
					description="<?php _e( 'Set unique name for your options page', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-model="generalSettings.menu_name"
				></cx-vui-input>
				<cx-vui-select
					label="<?php _e( 'Parent page', 'jet-engine' ); ?>"
					description="<?php _e( 'Leave empty to create top level menu page or select a parent for current page', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					:options-list="availableParents"
					v-model="generalSettings.parent"
				></cx-vui-select>
				<cx-vui-iconpicker
					name="icon"
					label="<?php _e( 'Menu Icon', 'jet-engine' ); ?>"
					description="<?php _e( 'Icon will be visible in admin menu', 'jet-engine' ); ?>"
					icon-base="dashicons"
					icon-prefix="dashicons-"
					:icons="icons"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-model="generalSettings.icon"
					:conditions="[
						{
							input: generalSettings.parent,
							compare: 'equal',
							value: ''
						}
					]"
				></cx-vui-iconpicker>
				<cx-vui-select
					label="<?php _e( 'Access capability', 'jet-engine' ); ?>"
					description="<?php _e( 'Select capability type to restrict access to created page', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					:options-list="availableCaps"
					v-model="generalSettings.capability"
				></cx-vui-select>
				<cx-vui-select
					label="<?php _e( 'Menu position', 'jet-engine' ); ?>"
					description="<?php _e( 'Select existing menu item to add page after', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					:options-list="availablePositions"
					v-model="generalSettings.position"
					:conditions="[
						{
							input: generalSettings.parent,
							compare: 'equal',
							value: ''
						}
					]"
				></cx-vui-select>
				<cx-vui-select
					label="<?php _e( 'Fields storage type', 'jet-engine' ); ?>"
					description="<?php _e( 'Select `Separate` to store field values in separate options', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					:options-list="[
						{
							value: 'default',
							label: '<?php _e( 'Default (as array)', 'jet-engine' ); ?>',
						},
						{
							value: 'separate',
							label: '<?php _e( 'Separate', 'jet-engine' ); ?>',
						}
					]"
					v-model="generalSettings.storage_type"
				></cx-vui-select>
				<cx-vui-switcher
					label="<?php _e( 'Add prefix for separate options', 'jet-engine' ); ?>"
					description="<?php _e( 'Toggle this option to add page slug as prefix for separate options', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="generalSettings.option_prefix"
					:conditions="[
						{
							input: generalSettings.storage_type,
							compare: 'equal',
							value: 'separate'
						}
					]"
				></cx-vui-switcher>
				<cx-vui-select
					label="<?php _e( 'Update options', 'jet-engine' ); ?>"
					description="<?php _e( 'Toggle this if you already have added options of this option page and want to automatically change storage type for these options', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					:options-list="[
						{
							value: '',
							label: '<?php _e( 'No', 'jet-engine' ); ?>',
						},
						{
							value: 'update',
							label: '<?php _e( 'Update and leave options saved in the previous storage type without changes', 'jet-engine' ); ?>',
						},
						{
							value: 'update_and_delete',
							label: '<?php _e( 'Update and delete options saved in the previous storage type', 'jet-engine' ); ?>',
						}
					]"
					v-if="storageTypeIsChanged"
					v-model="updateOptions"
				></cx-vui-select>
				<cx-vui-switcher
					label="<?php _e( 'Hide field names', 'jet-engine' ); ?>"
					description="<?php _e( 'Hide field names on option page', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="generalSettings.hide_field_names"
				></cx-vui-switcher>
			</div>
		</cx-vui-collapse>
		<jet-meta-fields v-model="fieldsList" :hide-options="[ 'quick_editable', 'revision_support' ]"></jet-meta-fields>
	</div>
	<div class="jet-engine-edit-page__actions">
		<div class="jet-engine-edit-page__actions-panel">
			<div class="cx-vui-subtitle"><?php _e( 'Actions', 'jet-engine' ); ?></div>
			<div class="cx-vui-text"><?php
				_e( 'If you are not sure where to start, please check tutorials list below this block', 'jet-engine' );
			?></div>
			<div class="jet-engine-edit-page__actions-buttons">
				<div class="jet-engine-edit-page__actions-save">
					<cx-vui-button
						:button-style="'accent'"
						:custom-css="'fullwidth'"
						:loading="saving"
						@click="save"
					>
						<svg slot="label" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.6667 5.33333V1.79167H1.79167V5.33333H10.6667ZM6.125 13.4167C6.65278 13.9444 7.27778 14.2083 8 14.2083C8.72222 14.2083 9.34722 13.9444 9.875 13.4167C10.4028 12.8889 10.6667 12.2639 10.6667 11.5417C10.6667 10.8194 10.4028 10.1944 9.875 9.66667C9.34722 9.13889 8.72222 8.875 8 8.875C7.27778 8.875 6.65278 9.13889 6.125 9.66667C5.59722 10.1944 5.33333 10.8194 5.33333 11.5417C5.33333 12.2639 5.59722 12.8889 6.125 13.4167ZM12.4583 0L16 3.54167V14.2083C16 14.6806 15.8194 15.0972 15.4583 15.4583C15.0972 15.8194 14.6806 16 14.2083 16H1.79167C1.29167 16 0.861111 15.8194 0.5 15.4583C0.166667 15.0972 0 14.6806 0 14.2083V1.79167C0 1.31944 0.166667 0.902778 0.5 0.541667C0.861111 0.180556 1.29167 0 1.79167 0H12.4583Z" fill="white"/></svg>
						<span slot="label">{{ buttonLabel }}</span>
					</cx-vui-button>
				</div>
				<div
					class="jet-engine-edit-page__actions-delete"
					v-if="isEdit"
				>
					<cx-vui-button
						:button-style="'link-error'"
						:size="'link'"
						@click="showDeleteDialog = true"
					>
						<svg slot="label" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.28564 14.1921V3.42857H13.7142V14.1921C13.7142 14.6686 13.5208 15.089 13.1339 15.4534C12.747 15.8178 12.3005 16 11.7946 16H4.20529C3.69934 16 3.25291 15.8178 2.866 15.4534C2.4791 15.089 2.28564 14.6686 2.28564 14.1921Z"/><path d="M14.8571 1.14286V2.28571H1.14282V1.14286H4.57139L5.56085 0H10.4391L11.4285 1.14286H14.8571Z"/></svg>
						<span slot="label"><?php _e( 'Delete', 'jet-engine' ); ?></span>
					</cx-vui-button>
				</div>
			</div>
			<div
				class="jet-engine-edit-page__notice-error"
				v-if="this.errorNotices.length"
			>
				<div class="jet-engine-edit-page__notice-error-content">
					<div class="jet-engine-edit-page__notice-error-items">
						<div
							v-for="( notice, index ) in errorNotices"
							:key="'notice_' + index"
						>{{ notice }}</div>
					</div>
				</div>
			</div>
			<div class="cx-vui-hr"></div>
			<div class="cx-vui-subtitle jet-engine-help-list-title"><?php _e( 'Need Help?', 'jet-engine' ); ?></div>
			<div class="cx-vui-panel">
				<div class="jet-engine-help-list">
					<div class="jet-engine-help-list__item" v-for="link in helpLinks">
						<a :href="link.url" target="_blank">
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.4413 7.39906C10.9421 6.89828 11.1925 6.29734 11.1925 5.59624C11.1925 4.71987 10.8795 3.9687 10.2535 3.34272C9.62754 2.71674 8.87637 2.40376 8 2.40376C7.12363 2.40376 6.37246 2.71674 5.74648 3.34272C5.1205 3.9687 4.80751 4.71987 4.80751 5.59624H6.38498C6.38498 5.17058 6.54773 4.79499 6.87324 4.46948C7.19875 4.14398 7.57434 3.98122 8 3.98122C8.42566 3.98122 8.80125 4.14398 9.12676 4.46948C9.45227 4.79499 9.61502 5.17058 9.61502 5.59624C9.61502 6.02191 9.45227 6.3975 9.12676 6.723L8.15024 7.73709C7.52426 8.41315 7.21127 9.16432 7.21127 9.99061V10.4038H8.78873C8.78873 9.57747 9.10172 8.82629 9.7277 8.15024L10.4413 7.39906ZM8.78873 13.5962V12.0188H7.21127V13.5962H8.78873ZM2.32864 2.3662C3.9061 0.788732 5.79656 0 8 0C10.2034 0 12.0814 0.788732 13.6338 2.3662C15.2113 3.91862 16 5.79656 16 8C16 10.2034 15.2113 12.0939 13.6338 13.6714C12.0814 15.2238 10.2034 16 8 16C5.79656 16 3.9061 15.2238 2.32864 13.6714C0.776213 12.0939 0 10.2034 0 8C0 5.79656 0.776213 3.91862 2.32864 2.3662Z" fill="#007CBA"/></svg>
							{{ link.label }}
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<jet-cpt-delete-dialog
		v-if="showDeleteDialog"
		v-model="showDeleteDialog"
		:item-id="parseInt( isEdit, 10 )"
	></jet-cpt-delete-dialog>
</div>
