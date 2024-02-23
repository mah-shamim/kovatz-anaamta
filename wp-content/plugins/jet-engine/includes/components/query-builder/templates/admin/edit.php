<div
	class="jet-engine-edit-page jet-engine-edit-page--loading"
	:class="{
		'jet-engine-edit-page--loaded': true,
	}"
>
	<div class="jet-engine-edit-page__fields">
		<div class="jet-engine-query-builder-heading">
			<h3 class="cx-vui-subtitle"><?php _e( 'General Settings', 'jet-engine' ); ?></h3>
			<cx-vui-switcher
				label="<?php _e( 'Preview results', 'jet-engine' ); ?>"
				:wrapper-css="[ 'jet-engine-query-builder-preview' ]"
				name="query_preview"
				:value="generalSettings.show_preview"
				@input="switchPreview"
			></cx-vui-switcher>
		</div>
		<div class="cx-vui-panel">
			<cx-vui-input
				:label="'<?php _e( 'Name', 'jet-engine' ); ?>'"
				:description="'<?php _e( 'Name of Query will be shown in the admin menu', 'jet-engine' ); ?>'"
				:wrapper-css="[ 'equalwidth' ]"
				:size="'fullwidth'"
				:error="errors.name"
				v-model="generalSettings.name"
				@on-focus="handleFocus( 'name' )"
			></cx-vui-input>
			<cx-vui-textarea
				:label="'<?php _e( 'Description', 'jet-engine' ); ?>'"
				:description="'<?php _e( 'Description of Query', 'jet-engine' ); ?>'"
				:wrapper-css="[ 'equalwidth' ]"
				:size="'fullwidth'"
				v-model="generalSettings.description"
			></cx-vui-textarea>
			<cx-vui-select
				:label="'<?php _e( 'Query Type', 'jet-engine' ); ?>'"
				:description="'<?php _e( 'Select type of queried data', 'jet-engine' ); ?>'"
				:wrapper-css="[ 'equalwidth' ]"
				:options-list="queryTypes"
				:size="'fullwidth'"
				v-model="generalSettings.query_type"
				@on-change="ensureQueryType"
			></cx-vui-select>
			<cx-vui-input
				:label="'<?php _e( 'Query ID', 'jet-engine' ); ?>'"
				:description="'<?php _e( 'Optional. Set custom Query ID to connect this query with JetSmartFilters. To work correctly this Query ID should be the same as Query ID set in the filter settings and ID of appropriate widget where this query is used.', 'jet-engine' ); ?>'"
				:wrapper-css="[ 'equalwidth' ]"
				:size="'fullwidth'"
				v-model="generalSettings.query_id"
			></cx-vui-input>
			<cx-vui-component-wrapper
				v-if="generalSettings.query_id"
				label="<?php _e( 'Warning!', 'jet-engine' ); ?>"
				description="<?php _e( 'Please make sure you set up the same ID for any filters used with this query.', 'jet-engine' ); ?>"
			></cx-vui-component-wrapper>
			<cx-vui-switcher
				:label="'<?php _e( 'Cache Query', 'jet-engine' ); ?>'"
				:description="'<?php _e( 'Whether to cache query results', 'jet-engine' ); ?>'"
				:wrapper-css="[ 'equalwidth' ]"
				:size="'fullwidth'"
				v-model="generalSettings.cache_query"
			></cx-vui-switcher>
		</div>
		<component
			v-if="generalSettings.query_type && typesComponents[ generalSettings.query_type ]"
			:is="typesComponents[ generalSettings.query_type ]"
			v-model="generalSettings[ generalSettings.query_type ]"
			@input="updatePreview"
			:dynamic-value="generalSettings[ '__dynamic_' + generalSettings.query_type ]"
			@dynamic-input="setDynamicQuery( '__dynamic_' + generalSettings.query_type, $event )"
		></component>
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
			<div class="jet-engine-query-preview" v-if="generalSettings.show_preview">
				<div class="jet-engine-query-preview__settings">
					<cx-vui-input
						label="<?php _e( 'Preview from page', 'jet-engine' ); ?>"
						description="<?php _e( 'Select post/page to preview current query from', 'jet-engine' ); ?>"
						:wrapper-css="[ 'preview-control' ]"
						size="fullwidth"
						name="query_preview_page_title"
						:value="generalSettings.preview_page_title"
						@input="searchPreviewPage"
					><div class="jet-engine-query-suggestions" v-if="suggestions.length">
						<div class="jet-engine-query-suggestions__list">
							<div class="jet-engine-query-suggestions__item" v-for="suggestion in suggestions" @click="applySuggestion( suggestion )">
								{{ suggestion.text }}
							</div>
							<div class="jet-engine-query-suggestions__close" @click="suggestions = []">&times;</div>
						</div>
					</div></cx-vui-input>
					<cx-vui-input
						label="<?php _e( 'Preview query string', 'jet-engine' ); ?>"
						description="<?php _e( 'Enter GET parameters string to preview current query with. Example: <i>arg_1=10&arg_2=text-value</i>', 'jet-engine' ); ?>"
						:wrapper-css="[ 'preview-control' ]"
						size="fullwidth"
						name="query_preview_query_string"
						v-model="generalSettings.preview_query_string"
						@input="updatePreview"
					></cx-vui-input>
				</div>
				<div :class="{ 'jet-engine-query-preview__body': true, 'jet-engine-query-preview__body-updating': updatingPreview }">
					<div class="jet-engine-query-preview__count"><b><?php _e( 'Results Count:', 'get-engine' ); ?></b> {{ previewCount }}</div>
					<div class="jet-engine-query-preview__items" v-if="previewBody"><pre>{{ previewBody }}</pre></div>
				</div>
			</div>
		</div>
	</div>
	<jet-query-delete-dialog
		v-if="showDeleteDialog"
		v-model="showDeleteDialog"
		:item-id="isEdit"
	></jet-query-delete-dialog>
</div>
