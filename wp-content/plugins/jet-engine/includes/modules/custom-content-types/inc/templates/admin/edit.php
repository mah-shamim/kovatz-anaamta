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
					:label="'<?php _e( 'Name', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Name of Custom Content Type will be shown in the admin menu`', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					:error="errors.name"
					v-model="generalSettings.name"
					@on-focus="handleFocus( 'name' )"
					@on-input-change="preSetSlug"
				></cx-vui-input>
				<cx-vui-input
					:label="'<?php _e( 'Slug', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Slug will be used to create appropriate data base table and access Custom Content Type data. Only latin letters, `-` or `_` are allowed.', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					:error="errors.slug"
					:disabled="slugDisabled"
					v-model="generalSettings.slug"
					@on-focus="handleFocus( 'slug' )"
				></cx-vui-input>
				<cx-vui-component-wrapper
					label="<?php _e( 'DB Table Name', 'jet-engine' ); ?>"
					description="<?php _e( 'Full name of database table will be created for this Content Type', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
				>
					<div><code>{{ prefix }}{{ generalSettings.slug }}</code></div>
				</cx-vui-component-wrapper>
				<cx-vui-switcher
					label="<?php _e( 'Has Single Page', 'jet-engine' ); ?>"
					description="<?php _e( 'Check this if you need to separate single page for each of your Content Type item', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="generalSettings.has_single"
				></cx-vui-switcher>
				<cx-vui-select
					:label="'<?php _e( 'Related Post Type', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Select post type associated to this Content Type items to serve single posts', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="postTypes"
					:size="'fullwidth'"
					v-model="generalSettings.related_post_type"
					:conditions="[
						{
							'input':   generalSettings.has_single,
							'compare': 'equal',
							'value':   true,
						}
					]"
				></cx-vui-select>
				<cx-vui-select
					:label="'<?php _e( 'Title field', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Select content type field to get related single post title from', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="fieldsForRelatedSettings"
					:size="'fullwidth'"
					v-model="generalSettings.related_post_type_title"
					:conditions="[
						{
							'input':   generalSettings.has_single,
							'compare': 'equal',
							'value':   true,
						}
					]"
				></cx-vui-select>
				<cx-vui-select
					:label="'<?php _e( 'Content field', 'jet-engine' ); ?>'"
					:description="'<?php _e( 'Select content type field to get related single post content from', 'jet-engine' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="fieldsForRelatedSettings"
					:size="'fullwidth'"
					v-model="generalSettings.related_post_type_content"
					:conditions="[
						{
							'input':   generalSettings.has_single,
							'compare': 'equal',
							'value':   true,
						}
					]"
				></cx-vui-select>
				<?php /*<cx-vui-switcher
					label="<?php _e( 'Create Index', 'jet-engine' ); ?>"
					description="<?php _e( 'Create SQL index on Content Type DB table to speed up data retrieval. An index helps to speed up getting data from DB, but it slows down inserting and updating data.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="generalSettings.create_index"
				></cx-vui-switcher> */ ?>
				<cx-vui-iconpicker
					label="<?php _e( 'Menu Icon', 'jet-engine' ); ?>"
					description="<?php _e( 'Icon will be visible in admin menu', 'jet-engine' ); ?>"
					icon-base="dashicons"
					icon-prefix="dashicons-"
					:icons="icons"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-model="generalSettings.icon"
				></cx-vui-iconpicker>
				<cx-vui-select
					label="<?php _e( 'Menu position', 'jet-engine' ); ?>"
					description="<?php _e( 'Select existing menu item to add page after', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					:options-list="availablePositions"
					v-model="generalSettings.position"
				></cx-vui-select>
				<cx-vui-input
					:label="'<?php _e( 'Content Type UI Access Capability', 'jet-engine' ); ?>'"
					description="<?php _e( 'By default any CCT available only for users with `manage_options` capability. Here you can overwrite it with any capability you want. More about capabilities <a href=\'https://wordpress.org/support/article/roles-and-capabilities/\' target=\'_blank\'>here</a>.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					v-model="generalSettings.capability"
				></cx-vui-input>
				<cx-vui-switcher
					label="<?php _e( 'Register get items/item REST API Endpoint', 'jet-engine' ); ?>"
					description="<?php _e( 'Register Rest API endpoint to get content type items.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="generalSettings.rest_get_enabled"
				></cx-vui-switcher>
				<cx-vui-component-wrapper
					label="<?php _e( 'Endpoint URL', 'jet-engine' ); ?>"
					description="<?php _e( 'Get endpoint URL', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:conditions="[
						{
							'input':   generalSettings.rest_get_enabled,
							'compare': 'equal',
							'value':   true,
						}
					]"
				>
					<div>
						<code>GET {{ restBase }}{{ generalSettings.slug }}</code> - <?php _e( 'Items list', 'jet-engine' ) ?><br>
						<code>GET {{ restBase }}{{ generalSettings.slug }}/{_ID}</code> - <?php _e( 'Single item, where {_ID} you need to replace with actual item ID', 'jet-engine' ) ?>
					</div>
					<div class="jet-engine-cb-trigger" style="padding-left: 0;">
						<a
							href="#"
							@click.prevent="showAPIParamsInfo = true"
							><?php _e( 'Parameters Overview', 'jet-engine' ); ?></a>
					</div>
				</cx-vui-component-wrapper>
				<cx-vui-input
					:label="'<?php _e( 'Access Capability', 'jet-engine' ); ?>'"
					description="<?php _e( 'Leave empty to make public or set user access capability. More about capabilities <a href=\'https://wordpress.org/support/article/roles-and-capabilities/\' target=\'_blank\'>here</a>.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					v-model="generalSettings.rest_get_access"
					:conditions="[
						{
							'input':   generalSettings.rest_get_enabled,
							'compare': 'equal',
							'value':   true,
						}
					]"
				></cx-vui-input>
				<cx-vui-switcher
					label="<?php _e( 'Register create item REST API Endpoint', 'jet-engine' ); ?>"
					description="<?php _e( 'Register Rest API endpoint to insert new content type items.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="generalSettings.rest_put_enabled"
				></cx-vui-switcher>
				<cx-vui-component-wrapper
					label="<?php _e( 'Endpoint URL', 'jet-engine' ); ?>"
					description="<?php _e( 'Create endpoint URL', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:conditions="[
						{
							'input':   generalSettings.rest_put_enabled,
							'compare': 'equal',
							'value':   true,
						}
					]"
				>
					<div>
						<code>POST {{ restBase }}{{ generalSettings.slug }}</code>
					</div>
				</cx-vui-component-wrapper>
				<cx-vui-input
					:label="'<?php _e( 'Access Capability', 'jet-engine' ); ?>'"
					description="<?php _e( 'Leave empty to make public or set user access capability. More about capabilities <a href=\'https://wordpress.org/support/article/roles-and-capabilities/\' target=\'_blank\'>here</a>.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					v-model="generalSettings.rest_put_access"
					:conditions="[
						{
							'input':   generalSettings.rest_put_enabled,
							'compare': 'equal',
							'value':   true,
						}
					]"
				></cx-vui-input>
				<cx-vui-component-wrapper
					label="<?php _e( 'Note!', 'jet-engine' ); ?>"
					description="<?php _e( 'If you leave this endpoint as public anyone will can to insert new content type items into the your database', 'jet-engine' ); ?>"
					:wrapper-css="[ 'fullwidth' ]"
					:conditions="[
						{
							'input':   generalSettings.rest_put_enabled,
							'compare': 'equal',
							'value':   true,
						},
						{
							'input':   generalSettings.rest_put_access,
							'compare': 'equal',
							'value':   '',
						}
					]"
				></cx-vui-component-wrapper>
				<cx-vui-switcher
					label="<?php _e( 'Register update item REST API Endpoint', 'jet-engine' ); ?>"
					description="<?php _e( 'Register Rest API endpoint to update content type items.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="generalSettings.rest_post_enabled"
				></cx-vui-switcher>
				<cx-vui-component-wrapper
					label="<?php _e( 'Endpoint URL', 'jet-engine' ); ?>"
					description="<?php _e( 'Get endpoint URL', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:conditions="[
						{
							'input':   generalSettings.rest_post_enabled,
							'compare': 'equal',
							'value':   true,
						}
					]"
				>
					<div>
						<code>POST {{ restBase }}{{ generalSettings.slug }}/{_ID}</code> <?php _e( 'where {_ID} you need to replace with actual item ID', 'jet-engine' ) ?>
					</div>
				</cx-vui-component-wrapper>
				<cx-vui-input
					:label="'<?php _e( 'Access Capability', 'jet-engine' ); ?>'"
					description="<?php _e( 'Leave empty to make public or set user access capability. More about capabilities <a href=\'https://wordpress.org/support/article/roles-and-capabilities/\' target=\'_blank\'>here</a>.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					v-model="generalSettings.rest_post_access"
					:conditions="[
						{
							'input':   generalSettings.rest_post_enabled,
							'compare': 'equal',
							'value':   true,
						}
					]"
				></cx-vui-input>
				<cx-vui-component-wrapper
					label="<?php _e( 'Note!', 'jet-engine' ); ?>"
					description="<?php _e( 'If you leave this endpoint as public anyone will can to update new content type items in the your database', 'jet-engine' ); ?>"
					:wrapper-css="[ 'fullwidth' ]"
					:conditions="[
						{
							'input':   generalSettings.rest_post_enabled,
							'compare': 'equal',
							'value':   true,
						},
						{
							'input':   generalSettings.rest_post_access,
							'compare': 'equal',
							'value':   '',
						}
					]"
				></cx-vui-component-wrapper>
				<cx-vui-switcher
					label="<?php _e( 'Register delete item REST API Endpoint', 'jet-engine' ); ?>"
					description="<?php _e( 'Register Rest API endpoint to delete content type items.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="generalSettings.rest_delete_enabled"
				></cx-vui-switcher>
				<cx-vui-component-wrapper
					label="<?php _e( 'Endpoint URL', 'jet-engine' ); ?>"
					description="<?php _e( 'Get endpoint URL', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:conditions="[
						{
							'input':   generalSettings.rest_delete_enabled,
							'compare': 'equal',
							'value':   true,
						}
					]"
				>
					<div>
						<code>DELETE {{ restBase }}{{ generalSettings.slug }}/{_ID}</code> <?php _e( 'where {_ID} you need to replace with actual item ID', 'jet-engine' ) ?>
					</div>
				</cx-vui-component-wrapper>
				<cx-vui-input
					:label="'<?php _e( 'Access Capability', 'jet-engine' ); ?>'"
					description="<?php _e( 'Leave empty to make public or set user access capability. More about capabilities <a href=\'https://wordpress.org/support/article/roles-and-capabilities/\' target=\'_blank\'>here</a>.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					v-model="generalSettings.rest_delete_access"
					:conditions="[
						{
							'input':   generalSettings.rest_delete_enabled,
							'compare': 'equal',
							'value':   true,
						}
					]"
				></cx-vui-input>
				<cx-vui-component-wrapper
					label="<?php _e( 'Note!', 'jet-engine' ); ?>"
					description="<?php _e( 'If you leave this endpoint as public anyone will can to delete content type items from the your database', 'jet-engine' ); ?>"
					:wrapper-css="[ 'fullwidth' ]"
					:conditions="[
						{
							'input':   generalSettings.rest_delete_enabled,
							'compare': 'equal',
							'value':   true,
						},
						{
							'input':   generalSettings.rest_delete_access,
							'compare': 'equal',
							'value':   '',
						}
					]"
				></cx-vui-component-wrapper>
				<cx-vui-switcher
					label="<?php _e( 'Hide field names', 'jet-engine' ); ?>"
					description="<?php _e( 'Hide field names on content type edit page.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="generalSettings.hide_field_names"
				></cx-vui-switcher>
			</div>
		</cx-vui-collapse>
		<cx-vui-popup
			v-model="showAPIParamsInfo"
			body-width="800px"
			cancel-label="<?php _e( 'Close', 'jet-engine' ) ?>"
			:show-ok="false"
		>
			<div class="cx-vui-subtitle" slot="title"><?php
				_e( 'GET Endpoint Parameters Overview', 'jet-engine' );
			?></div>
			<div class="jet-engine-cb-list is-fullwidth" slot="content">
				<div class="jet-engine-cb-list__item-alt" v-for="field in fieldsForColumns" v-if="isAllowedForAdminCols( field )" :key="'param_' + field.name">
					<code>{{ field.name }}</code> - {{ field.title }};
				</div>
				<div class="jet-engine-cb-list__item-alt" v-for="( argData, argName ) in commonAPIArgs" :key="'param_' + argName">
					<code>{{ argName }}</code> - {{ argData.description }}, <?php _e( 'type:', 'jet-engine' ); ?> {{ argData.type }};
				</div>
				<br>
				<div><b><?php _e( 'Request URL with parameters example:', 'jet-engine' ); ?></b></div>
				<code style="display:block; width: 100%; font-size:12px;">{{ restBase }}{{ generalSettings.slug }}/?cct_author_id=1&_orderby=_ID&_order=desc&_ordertype=integer</code>
			</div>
		</cx-vui-popup>
		<jet-meta-fields v-model="metaFields" :hide-options="[ 'show_in_rest', 'revision_support' ]" slug-delimiter="_"></jet-meta-fields>
		<cx-vui-collapse
			:collapsed="false"
		>
			<h3 class="cx-vui-subtitle" slot="title"><?php _e( 'Admin Columns', 'jet-engine' ); ?></h3>
			<div slot="content">
				<div class="cx-vui-repeater-item cx-vui-panel" v-for="field in fieldsForColumns" v-if="isAllowedForAdminCols( field )">
					<div class="cx-vui-repeater-item__heading">
						<div class="cx-vui-repeater-item__heading-start">
							<div class="cx-vui-repeater-item__title">{{ field.title }}</div>
							<div class="cx-vui-repeater-item__subtitle">{{ field.name }}</div>
						</div>
					</div>
					<div class="cx-vui-repeater-item__content">
						<cx-vui-switcher
							label="<?php _e( 'Show in the admin columns', 'jet-engine' ); ?>"
							description="<?php _e( 'Check this if you want to show information from this field in the current Content Type page admin columns', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							v-model="generalSettings.admin_columns[ field.name ].enabled"
						></cx-vui-switcher>
						<div v-if="generalSettings.admin_columns[ field.name ].enabled">
							<cx-vui-input
								label="<?php _e( 'Prefix', 'jet-engine' ); ?>"
								description="<?php _e( 'Text to add before column cell value', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								:size="'fullwidth'"
								v-model="generalSettings.admin_columns[ field.name ].prefix"
							></cx-vui-input>
							<cx-vui-input
								label="<?php _e( 'Suffix', 'jet-engine' ); ?>"
								description="<?php _e( 'Text to add after column cell value', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								:size="'fullwidth'"
								v-model="generalSettings.admin_columns[ field.name ].suffix"
							></cx-vui-input>
							<cx-vui-switcher
								label="<?php _e( 'Is sortable', 'jet-engine' ); ?>"
								description="<?php _e( 'Check this to make current column sortable', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								v-model="generalSettings.admin_columns[ field.name ].is_sortable"
							></cx-vui-switcher>
							<cx-vui-switcher
								label="<?php _e( 'Is Numeric Field', 'jet-engine' ); ?>"
								description="<?php _e( 'Check this if field contain numbers. By default sorting will be alphabetical.', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								v-model="generalSettings.admin_columns[ field.name ].is_num"
								:conditions="[
									{
										input: generalSettings.admin_columns[ field.name ].is_sortable,
										compare: 'equal',
										value: true
									}
								]"
							></cx-vui-switcher>
						</div>
					</div>
				</div>
			</div>
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
			<?php do_action( 'jet-engine/custom-content-types/edit-type/custom-actions' ); ?>
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
	<jet-cct-delete-dialog
		v-if="showDeleteDialog"
		v-model="showDeleteDialog"
		:item-id="isEdit"
	></jet-cct-delete-dialog>
</div>
