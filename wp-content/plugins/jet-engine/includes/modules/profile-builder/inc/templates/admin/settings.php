<?php
/**
 * Main dashboard template
 */
?>
<div>
	<div class="wrap">
		<h1 class="cs-vui-title" style="display:inline-block;"><?php 
			_e( 'User Profile Builder', 'jet-engine' ); 
		?></h1>
		<?php
			jet_engine()->get_video_help_popup( array(
				'popup_title' => __( 'How to work with Profile Builder', 'jet-engine' ),
				'embed' => 'https://www.youtube.com/embed/DloUYSFmAvs',
			) )->popup_trigger( true );
		?>
		<div class="cx-vui-panel">
			<cx-vui-tabs
				:in-panel="false"
				value="pages"
				layout="vertical"
				ref="settingsTabs"
			>
				<cx-vui-tabs-panel
					name="pages"
					label="<?php _e( 'Pages', 'jet-engine' ); ?>"
					key="pages"
				>
					<cx-vui-select
						name="account_page"
						label="<?php _e( 'Account Page', 'jet-engine' ); ?>"
						description="<?php _e( 'Select a page to use as the current user account page', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						placeholder="<?php _e( 'Select page...', 'jet-engine' ); ?>"
						:options-list="pagesList"
						v-model="settings.account_page"
					></cx-vui-select>
					<cx-vui-switcher
						label="<?php _e( 'Users page', 'jet-engine' ); ?>"
						description="<?php _e( 'Add a public page for the All Users list', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						v-model="settings.enable_users_page"
					></cx-vui-switcher>
					<cx-vui-select
						name="users_page"
						label="<?php _e( 'Users Page', 'jet-engine' ); ?>"
						description="<?php _e( 'Select a page to use as the public users page', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						placeholder="<?php _e( 'Select page...', 'jet-engine' ); ?>"
						:options-list="pagesList"
						v-if="settings.enable_users_page"
						v-model="settings.users_page"
					></cx-vui-select>
					<cx-vui-switcher
						label="<?php _e( 'Single user page', 'jet-engine' ); ?>"
						description="<?php _e( 'Add a public page for a single user', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						v-model="settings.enable_single_user_page"
					></cx-vui-switcher>
					<cx-vui-select
						name="user_page"
						label="<?php _e( 'Single User Page', 'jet-engine' ); ?>"
						description="<?php _e( 'Select a page to use as the public single user page', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						placeholder="<?php _e( 'Select page...', 'jet-engine' ); ?>"
						:options-list="pagesList"
						v-if="settings.enable_single_user_page"
						v-model="settings.single_user_page"
					></cx-vui-select>
					<cx-vui-select
						name="user_page_rewrite"
						label="<?php _e( 'User Page Rewrite', 'jet-engine' ); ?>"
						description="<?php _e( 'Select the rewrite base for the single user page', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="rewriteOptions"
						v-if="settings.enable_single_user_page"
						v-model="settings.user_page_rewrite"
					></cx-vui-select>
					<cx-vui-select
						label="<?php _e( 'Template mode', 'jet-engine' ); ?>"
						description="<?php _e( 'Set how the subpage templates will be processed. If <b>Rewrite</b> is selected, the account page content will be totally rewritten; if <b>Content</b> – the subpages content will be rendered by a separate widget inside the page content.', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="[
							{
								value: 'rewrite',
								label: '<?php _e( 'Rewrite', 'jet-engine' ); ?>'
							},
							{
								value: 'content',
								label: '<?php _e( 'Content', 'jet-engine' ); ?>'
							},
						]"
						v-model="settings.template_mode"
					></cx-vui-select>
					<div
						class="cx-vui-component"
						v-if="'content' === settings.template_mode"
					>
						<div class="cx-vui-component__meta">
							<label class="cx-vui-component__label"><?php
								_e( 'Note:', 'jet-engine' );
							?></label>
							<div class="cx-vui-component__desc"><?php
								_e( 'Content mode is selected. That means you need to add the <b>Profile Content</b> widget to your Account or User Page content!', 'jet-engine' );
							?></div>
						</div>
					</div>
					<cx-vui-switcher
						label="<?php _e( 'Use page content', 'jet-engine' ); ?>"
						description="<?php _e( 'If the subpage template is not set, use the default page content', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						:conditions="[
							{
								input: this.settings.template_mode,
								compare: 'equal',
								value: 'rewrite',
							}
						]"
						v-model="settings.force_template_rewrite"
					></cx-vui-switcher>
					<cx-vui-switcher
						label="<?php _e( 'Hide admin bar', 'jet-engine' ); ?>"
						description="<?php _e( 'Disable the admin bar for non-admins', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						v-model="settings.disable_admin_bar"
					></cx-vui-switcher>
					<cx-vui-switcher
						label="<?php _e( 'Restrict admin area access', 'jet-engine' ); ?>"
						description="<?php _e( 'Make the default WordPress admin area accessible only to selected user roles', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						v-model="settings.restrict_admin_access"
					></cx-vui-switcher>
					<cx-vui-f-select
						label="<?php _e( 'Select Roles', 'jet-engine' ); ?>"
						description="<?php _e( 'Select user roles with admin area access. Note - admin area always accessible for the Administrator role. To restrict access to all roles except Administrtor, enable Restrict admin area access option and leave empty current option', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						:options-list="userRoles|nonAdmins"
						size="fullwidth"
						:multiple="true"
						v-model="settings.admin_access_roles"
						:conditions="[
							{
								input: this.settings.restrict_admin_access,
								compare: 'equal',
								value: true,
							}
						]"
					></cx-vui-f-select>
					<cx-vui-component-wrapper
						:wrapper-css="[ 'vertical-fullwidth' ]"
					>
						<cx-vui-button
							button-style="accent"
							:loading="saving"
							@click="saveSettings"
						>
							<span
								slot="label"
								v-html="'<?php _e( 'Save', 'jet-engine' ); ?>'"
							></span>
						</cx-vui-button>
					</cx-vui-component-wrapper>
				</cx-vui-tabs-panel>
				<cx-vui-tabs-panel
					name="account_page"
					label="<?php _e( 'Account Page', 'jet-engine' ); ?>"
					key="account_page"
				>
					<cx-vui-select
						label="<?php _e( 'For not authorized users', 'jet-engine' ); ?>"
						description="<?php _e( 'What to do when non-authorized user try to access account page', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="notLoggedActions"
						v-model="settings.not_logged_in_action"
					></cx-vui-select>
					<cx-vui-f-select
						label="<?php _e( 'Template', 'jet-engine' ); ?>"
						description="<?php _e( 'Select Elementor/Listing Item template to show as page content for non-authorized user', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						:remote="true"
						:remote-callback="getPosts"
						size="fullwidth"
						:multiple="false"
						v-if="'template' === settings.not_logged_in_action"
						v-model="settings.not_logged_in_template"
					></cx-vui-f-select>
					<cx-vui-input
						label="<?php _e( 'Redirect URL', 'jet-engine' ); ?>"
						description="<?php _e( 'Set page URL to redirect', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						v-if="'page_redirect' === settings.not_logged_in_action"
						v-model="settings.not_logged_in_redirect"
					></cx-vui-input>
					<cx-vui-select
						label="<?php _e( 'For users with restricted access', 'jet-engine' ); ?>"
						description="<?php _e( 'What to do when user without access to specific account page tries to open it', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="notAccessibleActions"
						v-model="settings.not_accessible_action"
					></cx-vui-select>
					<cx-vui-f-select
						label="<?php _e( 'Template', 'jet-engine' ); ?>"
						description="<?php _e( 'Select Elementor/Listing Item template to show as page content for non-authorized user', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						:remote="true"
						:remote-callback="getPosts"
						size="fullwidth"
						:multiple="false"
						v-if="'template' === settings.not_accessible_action"
						v-model="settings.not_accessible_template"
					></cx-vui-f-select>
					<cx-vui-input
						label="<?php _e( 'Redirect URL', 'jet-engine' ); ?>"
						description="<?php _e( 'Set page URL to redirect', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						v-if="'page_redirect' === settings.not_accessible_action"
						v-model="settings.not_accessible_redirect"
					></cx-vui-input>
					<div class="cx-vui-inner-panel">
						<cx-vui-repeater
							button-label="<?php _e( '+ Add New Subpage', 'jet-engine' ); ?>"
							button-style="link-accent"
							button-size="default"
							v-model="settings.account_page_structure"
							@add-new-item="addNewPage( 'account_page_structure' )"
						>
							<cx-vui-repeater-item
								v-for="( page, index ) in settings.account_page_structure"
								:title="settings.account_page_structure[ index ].title"
								:subtitle="settings.account_page_structure[ index ].slug + ' (' + stringifyRoles( settings.account_page_structure[ index ].roles, true ) + ')'"
								:collapsed="isCollapsed( page )"
								:index="index"
								@clone-item="clonePage( $event, 'account_page_structure' )"
								@delete-item="deletePage( $event, 'account_page_structure' )"
								:key="page.id"
							>
								<cx-vui-input
									label="<?php _e( 'Title', 'jet-engine' ); ?>"
									description="<?php _e( 'Page title. Will be added into account menu and page meta title', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									size="fullwidth"
									:value="settings.account_page_structure[ index ].title"
									@input="setPageProp( index, 'title', $event, 'account_page_structure' )"
									@on-input-change="preSetSlug( index, 'account_page_structure' )"
								></cx-vui-input>
								<cx-vui-input
									label="<?php _e( 'Slug', 'jet-engine' ); ?>"
									description="<?php _e( 'Page slug. Will be added to base page URL', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									size="fullwidth"
									:value="settings.account_page_structure[ index ].slug"
									@input="setPageProp( index, 'slug', $event, 'account_page_structure' )"
								></cx-vui-input>
								<cx-vui-f-select
									label="<?php _e( 'Template', 'jet-engine' ); ?>"
									description="<?php _e( 'Page template. Select Elementor/Listing Item template to show on current page', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:remote="true"
									:remote-callback="getPosts"
									size="fullwidth"
									:multiple="false"
									:value="settings.account_page_structure[ index ].template"
									@input="setPageProp( index, 'template', $event, 'account_page_structure' )"
								></cx-vui-f-select>
								<cx-vui-switcher
									label="<?php _e( 'Hide from menu', 'jet-engine' ); ?>"
									description="<?php _e( 'Page will be hidden from profile menu, but accessible by URL', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:value="settings.account_page_structure[ index ].hide"
									@input="setPageProp( index, 'hide', $event, 'account_page_structure' )"
								></cx-vui-switcher>
								<cx-vui-f-select
									label="<?php _e( 'Available for the user role', 'jet-engine' ); ?>"
									description="<?php _e( 'Select user role/roles who can access to this page', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:options-list="userRolesForPages"
									size="fullwidth"
									:multiple="true"
									:value="settings.account_page_structure[ index ].roles"
									@input="setPageProp( index, 'roles', $event, 'account_page_structure' )"
								></cx-vui-f-select>
							</cx-vui-repeater-item>
						</cx-vui-repeater>
					</div>
					<cx-vui-component-wrapper
						:wrapper-css="[ 'vertical-fullwidth' ]"
					>
						<cx-vui-button
							button-style="accent"
							:loading="saving"
							@click="saveSettings"
						>
							<span
								slot="label"
								v-html="'<?php _e( 'Save', 'jet-engine' ); ?>'"
							></span>
						</cx-vui-button>
					</cx-vui-component-wrapper>
				</cx-vui-tabs-panel>
				<cx-vui-tabs-panel
					name="user_page"
					label="<?php _e( 'User Page', 'jet-engine' ); ?>"
					key="user_page"
					v-if="settings.enable_single_user_page"
				>
					<cx-vui-textarea
						label="<?php _e( 'User Page Title', 'jet-engine' ); ?>"
						description="<?php _e( 'Set seo title for user page', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth', 'has-macros' ]"
						size="fullwidth"
						v-model="settings.user_page_seo_title"
					>
						<jet-profile-macros @add-macro="addMacroToField( $event, 'user_page_seo_title' )"></jet-profile-macros>
					</cx-vui-textarea>
					<cx-vui-textarea
						label="<?php _e( 'User Page Description', 'jet-engine' ); ?>"
						description="<?php _e( 'Set seo description for user page', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth', 'has-macros' ]"
						size="fullwidth"
						v-model="settings.user_page_seo_desc"
					>
						<jet-profile-macros @add-macro="addMacroToField( $event, 'user_page_seo_desc' )"></jet-profile-macros>
					</cx-vui-textarea>
					<cx-vui-select
						label="<?php _e( 'User Page Image Field', 'jet-engine' ); ?>"
						description="<?php _e( 'Set seo image for user page from user meta field', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="userPageImageFields"
						v-model="settings.user_page_seo_image"
					></cx-vui-select>
					<div class="cx-vui-inner-panel">
						<cx-vui-repeater
							button-label="<?php _e( '+ Add New Subpage', 'jet-engine' ); ?>"
							button-style="link-accent"
							button-size="default"
							v-model="settings.user_page_structure"
							@add-new-item="addNewPage( 'user_page_structure' )"
						>
							<cx-vui-repeater-item
								v-for="( page, index ) in settings.user_page_structure"
								:title="settings.user_page_structure[ index ].title"
								:subtitle="settings.user_page_structure[ index ].slug + ' (' + stringifyRoles( settings.user_page_structure[ index ].roles, true ) + ')'"
								:collapsed="isCollapsed( page )"
								:index="index"
								@clone-item="clonePage( $event, 'user_page_structure' )"
								@delete-item="deletePage( $event, 'user_page_structure' )"
								:key="page.id"
							>
								<cx-vui-input
									label="<?php _e( 'Title', 'jet-engine' ); ?>"
									description="<?php _e( 'Page title. Will be added into account menu and page meta title', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									size="fullwidth"
									:value="settings.user_page_structure[ index ].title"
									@input="setPageProp( index, 'title', $event, 'user_page_structure' )"
									@on-input-change="preSetSlug( index, 'user_page_structure' )"
								></cx-vui-input>
								<cx-vui-input
									label="<?php _e( 'Slug', 'jet-engine' ); ?>"
									description="<?php _e( 'Page slug. Will be added to base page URL', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									size="fullwidth"
									:value="settings.user_page_structure[ index ].slug"
									@input="setPageProp( index, 'slug', $event, 'user_page_structure' )"
								></cx-vui-input>
								<cx-vui-f-select
									label="<?php _e( 'Template', 'jet-engine' ); ?>"
									description="<?php _e( 'Page template. Select Elementor/Listing Item template to show on current page', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:remote="true"
									:remote-callback="getPosts"
									size="fullwidth"
									:multiple="false"
									:value="settings.user_page_structure[ index ].template"
									@input="setPageProp( index, 'template', $event, 'user_page_structure' )"
								></cx-vui-f-select>
								<cx-vui-switcher
									label="<?php _e( 'Hide from menu', 'jet-engine' ); ?>"
									description="<?php _e( 'Page will be hidden from profile menu, but accessible by URL', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:value="settings.user_page_structure[ index ].hide"
									@input="setPageProp( index, 'hide', $event, 'user_page_structure' )"
								></cx-vui-switcher>
								<cx-vui-select
									label="<?php _e( 'Page visibility', 'jet-engine' ); ?>"
									description="<?php _e( 'Who can access this page', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									size="fullwidth"
									:options-list="visibilityOptions"
									:value="settings.user_page_structure[ index ].access"
									@input="setPageProp( index, 'access', $event, 'user_page_structure' )"
								></cx-vui-select>
								<cx-vui-f-select
									label="<?php _e( 'Show this page for the user role', 'jet-engine' ); ?>"
									description="<?php _e( 'Show this page in the public profile only if queried user role is', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:options-list="userRolesForPages"
									size="fullwidth"
									:multiple="true"
									:value="settings.user_page_structure[ index ].roles"
									@input="setPageProp( index, 'roles', $event, 'user_page_structure' )"
								></cx-vui-f-select>
							</cx-vui-repeater-item>
						</cx-vui-repeater>
					</div>
					<cx-vui-component-wrapper
						:wrapper-css="[ 'vertical-fullwidth' ]"
					>
						<cx-vui-button
							button-style="accent"
							:loading="saving"
							@click="saveSettings"
						>
							<span
								slot="label"
								v-html="'<?php _e( 'Save', 'jet-engine' ); ?>'"
							></span>
						</cx-vui-button>
					</cx-vui-component-wrapper>
				</cx-vui-tabs-panel>
				<cx-vui-tabs-panel
					name="advanced"
					label="<?php _e( 'Advanced', 'jet-engine' ); ?>"
					key="advanced"
				>
					<div class="cx-vui-component">
						<div class="cx-vui-component__meta">
							<label class="cx-vui-component__label"><?php
								_e( 'Posts restriction rules:', 'jet-engine' );
							?></label>
							<div class="cx-vui-component__desc"><?php
								_e( 'Set maximum allowed posts count to insert by user roles', 'jet-engine' );
							?></div>
						</div>
					</div>
					<div class="cx-vui-inner-panel">
						<cx-vui-repeater
							button-label="<?php _e( '+ New Rule', 'jet-engine' ); ?>"
							button-style="link-accent"
							button-size="default"
							v-model="settings.posts_restrictions"
							@add-new-item="addNewRepeaterItem( 'posts_restrictions', { 'role': '', 'limit': 0, 'collapsed': false, 'id': getRandomID() } )"
						>
							<cx-vui-repeater-item
								v-for="( rule, index ) in settings.posts_restrictions"
								:title="stringifyRoles( settings.posts_restrictions[ index ].role )"
								:subtitle="stringifyLimit( settings.posts_restrictions[ index ].limit )"
								:collapsed="isCollapsed( rule )"
								:index="index"
								@clone-item="cloneItem( $event, 'posts_restrictions', [ 'role', 'post_type', 'limit', 'error_message' ] )"
								@delete-item="deleteItem( $event, 'posts_restrictions' )"
								:key="rule.id"
							>
								<cx-vui-f-select
									label="<?php _e( 'Role', 'jet-engine' ); ?>"
									description="<?php _e( 'Select user role', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:options-list="userRoles"
									size="fullwidth"
									:multiple="true"
									:value="settings.posts_restrictions[ index ].role"
									@input="setPageProp( index, 'role', $event, 'posts_restrictions' )"
								></cx-vui-f-select>
								<cx-vui-f-select
									:label="'<?php _e( 'Post Types', 'jet-engine' ); ?>'"
									:description="'<?php _e( 'Select post types affected by this rule', 'jet-engine' ); ?>'"
									:wrapper-css="[ 'equalwidth' ]"
									:options-list="postTypes"
									:size="'fullwidth'"
									:multiple="true"
									:value="settings.posts_restrictions[ index ].post_type"
									@input="setPageProp( index, 'post_type', $event, 'posts_restrictions' )"
								></cx-vui-f-select>
								<cx-vui-input
									label="<?php _e( 'Limit', 'jet-engine' ); ?>"
									description="<?php _e( 'Set maximum allowed posts number to publish. Set 0 for unlimited posts', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									size="fullwidth"
									:value="settings.posts_restrictions[ index ].limit"
									@input="setPageProp( index, 'limit', $event, 'posts_restrictions' )"
								></cx-vui-input>
								<cx-vui-input
									label="<?php _e( 'Error message', 'jet-engine' ); ?>"
									description="<?php _e( 'Set \'posts limit reached\' message', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									size="fullwidth"
									:value="settings.posts_restrictions[ index ].error_message"
									@input="setPageProp( index, 'error_message', $event, 'posts_restrictions' )"
								></cx-vui-input>
							</cx-vui-repeater-item>
						</cx-vui-repeater>
					</div>
					<cx-vui-component-wrapper
						:wrapper-css="[ 'vertical-fullwidth' ]"
					>
						<cx-vui-button
							button-style="accent"
							:loading="saving"
							@click="saveSettings"
						>
							<span
								slot="label"
								v-html="'<?php _e( 'Save', 'jet-engine' ); ?>'"
							></span>
						</cx-vui-button>
					</cx-vui-component-wrapper>
				</cx-vui-tabs-panel>
				<?php do_action( 'jet-engine/profile-builder/settings/tabs' ); ?>
			</cx-vui-tabs>
		</div>
	</div>
</div>
