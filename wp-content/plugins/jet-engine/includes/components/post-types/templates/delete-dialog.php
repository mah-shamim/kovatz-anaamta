<cx-vui-popup
	v-model="isVisible"
	:ok-label="'<?php _e( 'Delete post type', 'jet-engine' ) ?>'"
	:cancel-label="'<?php _e( 'Cancel', 'jet-engine' ) ?>'"
	@on-cancel="handleCancel"
	@on-ok="handleOk"
>
	<div class="cx-vui-subtitle" slot="title"><?php
		_e( 'Please confirm post type deletion', 'jet-engine' );
	?>
		<template v-if="postTypeName">- <span class="jet-engine-accent-text">{{ postTypeName }}</span></template>
	</div>
	<p slot="content">
		<?php _e( 'Are you sure you want to delete this post type?', 'jet-engine' ); ?><br>
		<?php _e( 'If yes - please select what to do with created posts of this post type:', 'jet-engine' ); ?>
	</p>
	<cx-vui-select
		slot="content"
		v-model="postsAction"
		:size="'fullwidth'"
		:prevent-wrap="true"
		:options-list="[
			{
				value: 'none',
				label: '<?php _e( 'Leave posts without changes', 'jet-engine' ) ?>'
			},
			{
				value: 'reattach',
				label: '<?php _e( 'Attach to another post type', 'jet-engine' ) ?>'
			},
			{
				value: 'delete',
				label: '<?php _e( 'Delete all posts', 'jet-engine' ) ?>'
			},
		]"
	></cx-vui-select>
	<cx-vui-select
		slot="content"
		v-model="attachTo"
		:size="'fullwidth'"
		:prevent-wrap="true"
		:wrapper-css="[ 'indent-top' ]"
		:options-list="availablePostTypes"
		:conditions="[
			{
				input: this.postsAction,
				compare: 'equal',
				value: 'reattach',
			}
		]"
	></cx-vui-select>
</cx-vui-popup>