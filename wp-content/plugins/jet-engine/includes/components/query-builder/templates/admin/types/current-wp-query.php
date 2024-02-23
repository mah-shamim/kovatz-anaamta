<?php
/**
 * Posts query component template
 */
?>
<div class="jet-engine-edit-page__fields">
	<div class="cx-vui-collapse__heading">
		<h3 class="cx-vui-subtitle"><?php _e( 'Current WP Query', 'jet-engine' ); ?></h3>
	</div>
	<div class="cx-vui-panel">
		<div style="padding: 20px;"><?php _e( 'This query always inherits current WP Query object so there is no settings for this query type, except <b>Posts per page</b>.', 'jet-engine' ); ?></div>
		<cx-vui-component-wrapper
			:wrapper-css="[ 'query-fullwidth' ]"
			style="padding: 0; border-top: 1px solid #ECECEC;"
		>
			<div class="cx-vui-inner-panel query-panel">
				<div class="cx-vui-component__label"><?php _e( 'Posts per page', 'jet-engine' ); ?></div>
				<div class="cx-vui-component__description" style="padding-bottom: 15px;"><?php _e( 'Here you can set post per page for different page types where Current Query may be used. <b>Please note:</b> these settings applied to the <b>global WP Query object</b> of selected page type.', 'jet-engine' ); ?></div>
				<cx-vui-repeater
					button-label="<?php _e( 'Add new page type', 'jet-engine' ); ?>"
					button-style="accent"
					button-size="mini"
					v-model="query.posts_per_page"
					@add-new-item="addNewField( $event, [], query.posts_per_page )"
				>
					<cx-vui-repeater-item
						v-for="( posts, index ) in query.posts_per_page"
						:title="query.posts_per_page[ index ].page"
						:subtitle="query.posts_per_page[ index ].posts_number"
						:collapsed="isCollapsed( posts )"
						:index="index"
						@clone-item="cloneField( $event, posts._id, query.posts_per_page )"
						@delete-item="deleteField( $event, posts._id, query.posts_per_page )"
						:key="posts._id"
					>
						<cx-vui-select
							label="<?php _e( 'Page Type', 'jet-engine' ); ?>"
							description="<?php _e( 'Select page type to apply custom posts number for', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:groups-list="pageTypesOptions"
							size="fullwidth"
							:value="query.posts_per_page[ index ].page"
							@input="setFieldProp( posts._id, 'page', $event, query.posts_per_page )"
						></cx-vui-select>
						<cx-vui-input
							label="<?php _e( 'Posts number', 'jet-engine' ); ?>"
							description="<?php _e( 'Exact number of posts per page to show on selected page type in the any listing on the page used current WP Query object.', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							size="fullwidth"
							:value="query.posts_per_page[ index ].posts_number"
							@input="setFieldProp( posts._id, 'posts_number', $event, query.posts_per_page )"
						></cx-vui-input>
					</cx-vui-repeater-item>
				</cx-vui-repeater>
			</div>
		</cx-vui-component-wrapper>
		<cx-vui-component-wrapper
			:wrapper-css="[ 'fullwidth' ]"
			label="<?php  _e( 'Please note:', 'jet-engine' ); ?>"
			description="<?php printf( __( 'If you not find required page type in the list, you can change posts number on the page you need from %s', 'jet-engine' ), '<a href=\'' . admin_url( 'options-reading.php#posts_per_page' ) . '\' target=\'_blank\'>Reading settings of your website</a>' ); ?>"
		></cx-vui-component-wrapper>
	</div>
</div>