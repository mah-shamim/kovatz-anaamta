<div
	class="jet-engine-edit-page jet-engine-edit-page--relations jet-engine-edit-page--loading"
	:class="{
		'jet-engine-edit-page--loaded': true,
	}"
>
	<div class="jet-engine-edit-page__fields" v-if="isLegacy">
		<div class="cx-vui-panel jet-engine-relations-legacy-alert">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="jet-engine-relations-legacy-alert--icon"><rect x="0" fill="none" width="20" height="20"/><g><path d="M10 2c4.42 0 8 3.58 8 8s-3.58 8-8 8-8-3.58-8-8 3.58-8 8-8zm1.13 9.38l.35-6.46H8.52l.35 6.46h2.26zm-.09 3.36c.24-.23.37-.55.37-.96 0-.42-.12-.74-.36-.97s-.59-.35-1.06-.35-.82.12-1.07.35-.37.55-.37.97c0 .41.13.73.38.96.26.23.61.34 1.06.34s.8-.11 1.05-.34z"/></g></svg>
			<div>
				<p><?php _e( '<b>Is legacy relation.</b> You can keep use it as is. But if you want to use new Relations UI and all new features of the Relations, you need to convert this relation into new format.', 'jet-engine' ); ?></p>
				<p><?php _e( 'Conversion process will move all existing data into new DB table for relations automatically, but you\'ll still need to replace macros for this relation manually in all places where it was used.', 'jet-engine' ); ?></p>
				<cx-vui-button
					button-style="accent"
					size="mini"
					@click="showConvertDialog = true"
				>
					<span slot="label"><?php _e( 'Convert into the new format', 'jet-engine' ); ?></span>
				</cx-vui-button>
				<cx-vui-popup
					v-model="showConvertDialog"
					:ok-label="'<?php _e( 'Start', 'jet-engine' ) ?>'"
					:cancel-label="'<?php _e( 'Cancel', 'jet-engine' ) ?>'"
					body-width="600px"
					@on-cancel="showConvertDialog = false"
					@on-ok="convertCurrentRel"
				>
					<div class="cx-vui-subtitle" slot="title"><?php
						_e( 'Please confirm relation convertion', 'jet-engine' );
					?></div>
					<div slot="content">
						<p><?php _e( 'Are you sure you want to convert an old legacy realtion into new Relations UI?', 'jet-engine' ); ?></p>
						<p><?php _e( '<b>Please note:</b> after start this process can\'t be cancelled, all existing data of current relation will be moved into new DB table, all macros where current relation was used will stop working and you\'ll need to replace them with a new macros %rel_get_items|&lt;rel_id&gt;|&lt;parent_object or child_object&gt;|&lt;current_object&gt;%', 'jet-engine' ); ?></p>
					</div>
				</cx-vui-popup>
			</div>
		</div>
		<h3 class="cx-vui-subtitle" style="padding: 20px 0 20px;"><?php _e( 'General Settings', 'jet-engine' ); ?></h3>
		<div class="cx-vui-panel">
			<cx-vui-input
				name="name"
				label="<?php _e( 'Name', 'jet-engine' ); ?>"
				description="<?php _e( 'Set unique name for your current relation', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				v-model="args.name"
			></cx-vui-input>
			<cx-vui-select
				label="<?php _e( 'Parent post type', 'jet-engine' ); ?>"
				description="<?php _e( 'Select main post type for current relation', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:options-list="postTypes"
				size="fullwidth"
				:multiple="false"
				v-model="args.parent_object"
			></cx-vui-select>
			<cx-vui-select
				label="<?php _e( 'Child post type', 'jet-engine' ); ?>"
				description="<?php _e( 'Select child post type for current relation', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:options-list="postTypes"
				size="fullwidth"
				:multiple="false"
				v-model="args.child_object"
			></cx-vui-select>
			<cx-vui-select
				label="<?php _e( 'Relation Type', 'jet-engine' ); ?>"
				description="<?php _e( 'Select type of current relation. Read more about available relation types <a href=\'https://crocoblock.com/knowledge-base/articles/how-to-choose-the-needed-post-relations-and-set-them-with-jetengine-plugin/\'>here</a>', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:options-list="relationsTypes"
				size="fullwidth"
				:multiple="false"
					v-model="args.type"
			></cx-vui-select>
			<cx-vui-select
				label="<?php _e( 'Parent Relation', 'jet-engine' ); ?>"
				description="<?php _e( 'Select parent relation for current one. The relation could be selected as parent only if its \'Child post type\' is equal to \'Parent post type\' of current relation', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:options-list="availableParentRelations"
				size="fullwidth"
				:multiple="false"
				v-model="args.parent_rel"
			></cx-vui-select>
			<cx-vui-switcher
				name="post_type_1_control"
				label="<?php _e( 'Register controls for parent post type', 'jet-engine' ); ?>"
				description="<?php _e( 'Adds meta box `Related children` to parent post type edit page', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				v-model="args.parent_control"
			></cx-vui-switcher>
			<cx-vui-switcher
				name="post_type_2_control"
				label="<?php _e( 'Register controls for child post type', 'jet-engine' ); ?>"
				description="<?php _e( 'Adds meta box `Related parent` to child post type edit page', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				v-model="args.child_control"
			></cx-vui-switcher>
		</div>
	</div>
	<div class="jet-engine-edit-page__fields" v-if="!isLegacy">
		<jet-engine-relation v-if="loaded" v-model="args"/>
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
						button-style="accent"
						custom-css="fullwidth"
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
						button-style="link-error"
						size="link"
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
		:item-id="isEdit"
		@on-error="handleDeletionError"
	></jet-cpt-delete-dialog>
</div>
