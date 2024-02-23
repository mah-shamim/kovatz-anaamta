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
					:name="'post_type_name'"
					:label="'<?php _e( 'Post Type Name', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Set unique name for your post type. Eg. `Projects`', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					:error="errors.name"
					v-model="generalSettings.name"
					@on-focus="handleFocus( 'name' )"
					@on-input-change="preSetSlug"
				></cx-vui-input>
				<cx-vui-input
					:name="'post_type_slug'"
					:label="'<?php _e( 'Post Type Slug', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Set slug for your post type. Slug should contain only letters, numbers and `-` or `_` chars', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					:error="errors.slug"
					:disabled="isBuiltIn"
					v-model="generalSettings.slug"
					@on-focus="handleFocus( 'slug' )"
					@on-input-change="checkSlug"
				>
					<div class="jet-engine-slug-error" v-if="showIncorrectSlug">
						{{ incorrectSlugMessage }}
					</div>
				</cx-vui-input>
				<cx-vui-switcher
					label="<?php _e( 'Update Posts', 'jet-engine' ); ?>"
					description="<?php _e( 'Check this if you already have created post of this post type and want to automatically change post type for these posts.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-if="slugIsChanged()"
					v-model="updatePosts"
				></cx-vui-switcher>
				<cx-vui-switcher
					label="<?php _e( '`Edit post type/meta box` link', 'jet-engine' ); ?>"
					description="<?php _e( 'Add `Edit post type/meta box` link to post edit page.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="generalSettings.show_edit_link"
				></cx-vui-switcher>
				<cx-vui-switcher
					label="<?php _e( 'Hide meta field names', 'jet-engine' ); ?>"
					description="<?php _e( 'Hide meta field names on post edit page.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="generalSettings.hide_field_names"
				></cx-vui-switcher>
			</div>
		</cx-vui-collapse>
		<cx-vui-collapse
			:collapsed="true"
		>
			<h3 class="cx-vui-subtitle" slot="title"><?php _e( 'Labels', 'jet-engine' ); ?></h3>
			<div class="cx-vui-panel" slot="content">
				<cx-vui-input
					v-for="labelObject in labelsList"
					:name="labelObject.name"
					:label="labelObject.label"
					:description="labelObject.description"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					:key="'label_for_' + labelObject.name"
					v-model="labels[ labelObject.name ]"
					@on-focus="handleLabelFocus( labelObject.name, labelObject.is_singular, labelObject.default )"
				></cx-vui-input>
			</div>
		</cx-vui-collapse>
		<cx-vui-collapse
			:collapsed="true"
		>
			<h3 class="cx-vui-subtitle" slot="title">Advanced Settings</h3>
			<div class="cx-vui-panel" slot="content">
				<cx-vui-switcher
					:name="'public'"
					:label="'<?php _e( 'Is Public', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Controls how the type is visible to authors and readers', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="advancedSettings.public"
					@on-change="preSetIsPublicDeps"
				></cx-vui-switcher>
				<cx-vui-switcher
					:name="'exclude_from_search'"
					:label="'<?php _e( 'Exclude From Search', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Whether to exclude posts with this post type from front end search results', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="advancedSettings.exclude_from_search"
				></cx-vui-switcher>
				<cx-vui-switcher
					:name="'publicly_queryable'"
					:label="'<?php _e( 'Publicly Queryable', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Whether queries can be performed on the front end as part of parse_request()', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="advancedSettings.publicly_queryable"
				></cx-vui-switcher>
				<cx-vui-switcher
					:name="'show_ui'"
					:label="'<?php _e( 'Show Admin UI', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Whether to generate a default UI for managing this post type in the admin', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="advancedSettings.show_ui"
				></cx-vui-switcher>
				<cx-vui-switcher
					:name="'show_in_menu'"
					:label="'<?php _e( 'Show in Admin Menu', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Where to show the post type in the admin menu. show_ui must be true', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="advancedSettings.show_in_menu"
				></cx-vui-switcher>
				<cx-vui-switcher
					:name="'show_in_nav_menus'"
					:label="'<?php _e( 'Show in Nav Menu', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Whether post_type is available for selection in navigation menus', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="advancedSettings.show_in_nav_menus"
				></cx-vui-switcher>
				<cx-vui-switcher
					:name="'show_in_rest'"
					:label="'<?php _e( 'Show in Rest API', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Whether to expose this post type in the REST API. Also enable/disable Gutenberg editor for current post type', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="advancedSettings.show_in_rest"
				></cx-vui-switcher>
				<cx-vui-switcher
					:name="'query_var'"
					:label="'<?php _e( 'Register Query Var', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Sets the query_var key for this post type', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="advancedSettings.query_var"
				></cx-vui-switcher>
				<cx-vui-switcher
					:name="'rewrite'"
					:label="'<?php _e( 'Rewrite', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Triggers the handling of rewrites for this post type. To prevent rewrites, set to false', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="advancedSettings.rewrite"
				></cx-vui-switcher>
				<cx-vui-input
					:name="'rewrite_slug'"
					:label="'<?php _e( 'Rewrite Slug', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Customize the permalink structure slug. Defaults to the post type slug', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					:conditions="[
						{
							'input':    advancedSettings.rewrite,
							'compare': 'equal',
							'value':    true,
						}
					]"
					v-model="advancedSettings.rewrite_slug"
				></cx-vui-input>
				<cx-vui-switcher
					:name="'with_front'"
					:label="'<?php _e( 'Rewrite With Front', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Whether the permastruct should be prepended with WP_Rewrite::$front', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					:conditions="[
						{
							'input':    advancedSettings.rewrite,
							'compare': 'equal',
							'value':    true,
						}
					]"
					v-model="advancedSettings.with_front"
				></cx-vui-switcher>
				<cx-vui-input
					:name="'capability_type'"
					:label="'<?php _e( 'Capability Type', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'The string to use to build the read, edit, and delete capabilities', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					v-model="advancedSettings.capability_type"
				></cx-vui-input>
				<cx-vui-switcher
					:name="'map_meta_cap'"
					:label="'<?php _e( 'Map Meta Cap', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Whether to use the internal default meta capability handling', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="advancedSettings.map_meta_cap"
				></cx-vui-switcher>
				<cx-vui-switcher
					:name="'has_archive'"
					:label="'<?php _e( 'Has Archive', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Enables post type archives', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="advancedSettings.has_archive"
				></cx-vui-switcher>
				<cx-vui-switcher
					:name="'hierarchical'"
					:label="'<?php _e( 'Hierarchical', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Whether the post type is hierarchical (e.g. page)', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="advancedSettings.hierarchical"
				></cx-vui-switcher>
				<cx-vui-input
					label="<?php _e( 'Menu Position', 'jet-engine' ); ?>"
					description="<?php _e( 'The position in the menu order the post type should appear', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-if="advancedSettings.menu_position && ! positionIDs.includes( advancedSettings.menu_position )"
					size="fullwidth"
					v-model="advancedSettings.menu_position"
				></cx-vui-input>
				<cx-vui-select
					label="<?php _e( 'Menu position', 'jet-engine' ); ?>"
					description="<?php _e( 'Select existing menu item to add page after', 'jet-engine' ); ?>"
					v-if="! advancedSettings.menu_position || positionIDs.includes( advancedSettings.menu_position )"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					:options-list="availablePositions"
					v-model="advancedSettings.menu_position"
				></cx-vui-select>
				<cx-vui-iconpicker
					:name="'menu_icon'"
					:label="'<?php _e( 'Menu Icon', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Icon will be visible in admin menu', 'jet-engine' ); ?>'"
					:icon-base="'dashicons'"
					:icon-prefix="'dashicons-'"
					:icons="icons"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					v-model="advancedSettings.menu_icon"
				></cx-vui-iconpicker>
				<cx-vui-f-select
					:name="'supports'"
					:label="'<?php _e( 'Supports', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Registers support of certain feature(s) for a current post type', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					:placeholder="'Select option...'"
					:multiple="true"
					:options-list="supports"
					v-model="advancedSettings.supports"
					ref="supports"
				></cx-vui-f-select>
			</div>
		</cx-vui-collapse>
		<?php do_action( 'jet-engine/post-types/meta-fields' ); ?>
		<cx-vui-collapse
			:collapsed="true"
		>
			<h3 class="cx-vui-subtitle" slot="title">
				<?php _e( 'Admin Columns', 'jet-engine' ); ?> ({{ adminColumns.length }})
			</h3>
			<cx-vui-repeater
				slot="content"
				:button-label="'<?php _e( 'Add New', 'jet-engine' ); ?>'"
				:button-style="'accent'"
				:button-size="'default'"
				v-model="adminColumns"
				@add-new-item="addNewAdminColumn"
			>
				<cx-vui-repeater-item
					v-for="( column, index ) in adminColumns"
					:title="adminColumns[ index ].title"
					:subtitle="adminColumns[ index ].type"
					:collapsed="isCollapsed( column )"
					:index="index"
					@clone-item="cloneColumn( $event )"
					@delete-item="deleteColumn( $event )"
					:key="'admin_col_' + index"
				>
					<cx-vui-input
						:name="'column[' + index + '][title]'"
						:label="'<?php _e( 'Title', 'jet-engine' ); ?>'"
						:description="'<?php _e( 'Current column title. Will be shown at the top of the posts list table', 'jet-engine' ); ?>'"
						:wrapper-css="[ 'equalwidth' ]"
						:size="'fullwidth'"
						:value="adminColumns[ index ].title"
						@input="setColumnProp( index, 'title', $event )"
					></cx-vui-input>
					<cx-vui-select
						:name="'column[' + index + '][type]'"
						:label="'<?php _e( 'Type', 'jet-engine' ); ?>'"
						:description="'<?php _e( 'Select type of data to fill column cells', 'jet-engine' ); ?>'"
						:wrapper-css="[ 'equalwidth' ]"
						:size="'fullwidth'"
						:options-list="adminColumnsTypes"
						:value="adminColumns[ index ].type"
						@input="setColumnProp( index, 'type', $event )"
					></cx-vui-select>
					<cx-vui-input
						:name="'column[' + index + '][meta_field]'"
						:label="'<?php _e( 'Field Name', 'jet-engine' ); ?>'"
						:description="'<?php _e( 'Custom field name to get value from', 'jet-engine' ); ?>'"
						:wrapper-css="[ 'equalwidth' ]"
						:size="'fullwidth'"
						:conditions="[
							{
								input: adminColumns[ index ].type,
								compare: 'equal',
								value: 'meta_value'
							}
						]"
						:value="adminColumns[ index ].meta_field"
						@input="setColumnProp( index, 'meta_field', $event )"
					></cx-vui-input>
					<cx-vui-input
						:name="'column[' + index + '][taxonomy]'"
						:label="'<?php _e( 'Taxonomy', 'jet-engine' ); ?>'"
						:description="'<?php _e( 'Taxonomy slug to get post terms from', 'jet-engine' ); ?>'"
						:wrapper-css="[ 'equalwidth' ]"
						:size="'fullwidth'"
						:conditions="[
							{
								input: adminColumns[ index ].type,
								compare: 'equal',
								value: 'post_terms'
							}
						]"
						:value="adminColumns[ index ].taxonomy"
						@input="setColumnProp( index, 'taxonomy', $event )"
					></cx-vui-input>
					<cx-vui-input
						:name="'column[' + index + '][callback]'"
						:label="'<?php _e( 'Callback', 'jet-engine' ); ?>'"
						:description="'<?php _e( 'Custom function name to get column cell value from', 'jet-engine' ); ?>'"
						:wrapper-css="[ 'equalwidth' ]"
						:size="'fullwidth'"
						:conditions="[
							{
								input: adminColumns[ index ].type,
								compare: 'equal',
								value: 'custom_callback'
							}
						]"
						:value="adminColumns[ index ].callback"
						@input="setColumnProp( index, 'callback', $event )"
					>
						<div class="jet-engine-cb-trigger">
							<a
								href="#"
								@click.prevent="selectCallback( index )"
								><?php _e( 'Select from existing callbacks', 'jet-engine' ); ?></a>
						</div>
					</cx-vui-input>
					<cx-vui-input
						:name="'column[' + index + '][position]'"
						:label="'<?php _e( 'Column order', 'jet-engine' ); ?>'"
						:description="'<?php _e( 'Column order. Should be a positive integer', 'jet-engine' ); ?>'"
						:wrapper-css="[ 'equalwidth' ]"
						:size="'fullwidth'"
						:value="adminColumns[ index ].position"
						@input="setColumnProp( index, 'position', $event )"
					></cx-vui-input>
					<cx-vui-input
						:name="'column[' + index + '][prefix]'"
						:label="'<?php _e( 'Prefix', 'jet-engine' ); ?>'"
						:description="'<?php _e( 'Text to add before column cell value', 'jet-engine' ); ?>'"
						:wrapper-css="[ 'equalwidth' ]"
						:size="'fullwidth'"
						:value="adminColumns[ index ].prefix"
						@input="setColumnProp( index, 'prefix', $event )"
					></cx-vui-input>
					<cx-vui-input
						:name="'column[' + index + '][suffix]'"
						:label="'<?php _e( 'Suffix', 'jet-engine' ); ?>'"
						:description="'<?php _e( 'Text to add after column cell value', 'jet-engine' ); ?>'"
						:wrapper-css="[ 'equalwidth' ]"
						:size="'fullwidth'"
						:value="adminColumns[ index ].suffix"
						@input="setColumnProp( index, 'suffix', $event )"
					></cx-vui-input>
					<cx-vui-switcher
						name="is_sortable"
						label="<?php _e( 'Is Sortable', 'jet-engine' ); ?>"
						description="<?php _e( 'Make this column sortable', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						v-model="adminColumns[ index ].is_sortable"
						@input="setColumnProp( index, 'is_sortable', $event )"
						:conditions="[
							{
								input: adminColumns[ index ].type,
								compare: 'not_equal',
								value: 'post_terms'
							}
						]"
					></cx-vui-switcher>
					<cx-vui-input
						:name="'column[' + index + '][suffix]'"
						:label="'<?php _e( 'By field', 'jet-engine' ); ?>'"
						:description="'<?php _e( 'Sort can be done only by meta field value. Please set field name to sort posts by. If you are using `jet_engine_custom_cb_menu_order` callback leave this field empty', 'jet-engine' ); ?>'"
						:wrapper-css="[ 'equalwidth' ]"
						:size="'fullwidth'"
						:value="adminColumns[ index ].sort_by_field"
						@input="setColumnProp( index, 'sort_by_field', $event )"
						:conditions="[
							{
								input: adminColumns[ index ].type,
								compare: 'equal',
								value: 'custom_callback'
							},
							{
								input: adminColumns[ index ].is_sortable,
								compare: 'equal',
								value: true
							}
						]"
					></cx-vui-input>
					<cx-vui-switcher
						name="is_num"
						label="<?php _e( 'Is Numeric Field', 'jet-engine' ); ?>"
						description="<?php _e( 'Check this if field contain numbers. By default sorting will be alphabetical.', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						v-model="adminColumns[ index ].is_num"
						@input="setColumnProp( index, 'is_num', $event )"
						:conditions="[
							{
								input: adminColumns[ index ].type,
								compare: 'not_equal',
								value: 'post_terms'
							},
							{
								input: adminColumns[ index ].is_sortable,
								compare: 'equal',
								value: true
							}
						]"
					></cx-vui-switcher>
				</cx-vui-repeater-item>
			</cx-vui-repeater>
		</cx-vui-collapse>
		<cx-vui-collapse
			:collapsed="true"
		>
			<h3 class="cx-vui-subtitle has-help-link" slot="title">
				<?php _e( 'Admin Filters', 'jet-engine' ); ?> ({{ adminFilters.length }})
				<a href="https://crocoblock.com/knowledge-base/articles/jetengine-custom-post-type-admin-filters/?utm_source=jetengine&utm_medium=admin-filters&utm_campaign=need-help" class="jet-help-link" target="_blank">
					<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.4413 7.39906C10.9421 6.89828 11.1925 6.29734 11.1925 5.59624C11.1925 4.71987 10.8795 3.9687 10.2535 3.34272C9.62754 2.71674 8.87637 2.40376 8 2.40376C7.12363 2.40376 6.37246 2.71674 5.74648 3.34272C5.1205 3.9687 4.80751 4.71987 4.80751 5.59624H6.38498C6.38498 5.17058 6.54773 4.79499 6.87324 4.46948C7.19875 4.14398 7.57434 3.98122 8 3.98122C8.42566 3.98122 8.80125 4.14398 9.12676 4.46948C9.45227 4.79499 9.61502 5.17058 9.61502 5.59624C9.61502 6.02191 9.45227 6.3975 9.12676 6.723L8.15024 7.73709C7.52426 8.41315 7.21127 9.16432 7.21127 9.99061V10.4038H8.78873C8.78873 9.57747 9.10172 8.82629 9.7277 8.15024L10.4413 7.39906ZM8.78873 13.5962V12.0188H7.21127V13.5962H8.78873ZM2.32864 2.3662C3.9061 0.788732 5.79656 0 8 0C10.2034 0 12.0814 0.788732 13.6338 2.3662C15.2113 3.91862 16 5.79656 16 8C16 10.2034 15.2113 12.0939 13.6338 13.6714C12.0814 15.2238 10.2034 16 8 16C5.79656 16 3.9061 15.2238 2.32864 13.6714C0.776213 12.0939 0 10.2034 0 8C0 5.79656 0.776213 3.91862 2.32864 2.3662Z" fill="#007CBA"></path></svg>
					<?php _e( 'What is this?' ) ?>
				</a>
			</h3>
			<jet-engine-admin-filters
				slot="content"
				v-model="adminFilters"
				:meta-fields="metaFields"
				:post-type="generalSettings.slug"
			></jet-engine-admin-filters>
		</cx-vui-collapse>
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
						@click="savePostType"
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
						@click="resetDialog = true"
						v-if="isBuiltIn"
					>
						<span slot="label"><?php _e( 'Reset to defaults', 'jet-engine' ); ?></span>
					</cx-vui-button>
					<cx-vui-button
						:button-style="'link-error'"
						:size="'link'"
						@click="showDeleteDialog = true"
						v-else
					>
						<svg slot="label" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.28564 14.1921V3.42857H13.7142V14.1921C13.7142 14.6686 13.5208 15.089 13.1339 15.4534C12.747 15.8178 12.3005 16 11.7946 16H4.20529C3.69934 16 3.25291 15.8178 2.866 15.4534C2.4791 15.089 2.28564 14.6686 2.28564 14.1921Z"/><path d="M14.8571 1.14286V2.28571H1.14282V1.14286H4.57139L5.56085 0H10.4391L11.4285 1.14286H14.8571Z"/></svg>
						<span slot="label"><?php _e( 'Delete', 'jet-engine' ); ?></span>
					</cx-vui-button>
					<div
						v-if="resetDialog"
						class="cx-vui-tooltip"
					>
						<?php _e( 'Are you sure?', 'jet-engine' ); ?>
						<br>
						<span
							class="cx-vui-repeater-item__confrim-del"
							@click="resetToDefaults"
						><?php _e( 'Yes', 'jet-engine' ); ?></span>/<span
							class="cx-vui-repeater-item__cancel-del"
							@click="resetDialog = false"
						><?php _e( 'No', 'jet-engine' ); ?></span>
					</div>
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
	<cx-vui-popup
		v-model="callbacks.showPopup"
		ok-label="<?php _e( 'Apply', 'jet-engine' ) ?>"
		cancel-label="<?php _e( 'Cancel', 'jet-engine' ) ?>"
		:show-ok="callbacks.showOk"
		@on-cancel="handleCallbackPopupCancel"
		@on-ok="handleCallbackPopupOk"
	>
		<div class="cx-vui-subtitle" slot="title"><?php
			_e( 'Select one of predefined callbacks', 'jet-engine' );
		?></div>
		<div class="jet-engine-cb-list" slot="content" v-if="!callbacks.currentArgs">
			<div class="jet-engine-cb-list__label"><?php
				_e( 'Click on callback name to paste it.', 'jet-engine' );
			?></div>
			<div class="jet-engine-cb-list__item" v-for="( cbData, cbName ) in callbacks.list" :key="cbName">
				<code @click="setCurrent( cbName, cbData )">{{ cbName }}</code> - {{ cbData.description }}
			</div>
		</div>
		<div class="jet-engine-cb-args" slot="content" v-else>
			<component
				v-for="( argData, argName ) in callbacks.currentArgs"
				:is="getArgComponent( argData )"
				:key="argName"
				:name="argName"
				:label="argData.label"
				:description="argData.description"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				:options-list="argData.options"
				:value="callbacks.currentArgs[ argName ].value"
				@input="setCBArg( argName, $event )"
			></component >
		</div>
	</cx-vui-popup>
	<jet-cpt-delete-dialog
		v-if="showDeleteDialog"
		v-model="showDeleteDialog"
		:post-type-id="parseInt( isEdit, 10 )"
		:post-type-slug="generalSettings.slug"
	></jet-cpt-delete-dialog>
</div>
