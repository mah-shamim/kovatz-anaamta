<cx-vui-popup
	v-model="isVisible"
	:ok-label="'<?php _e( 'Delete page', 'jet-engine' ) ?>'"
	:cancel-label="'<?php _e( 'Cancel', 'jet-engine' ) ?>'"
	@on-cancel="handleCancel"
	@on-ok="handleOk"
>
	<div class="cx-vui-subtitle" slot="title"><?php
		_e( 'Please confirm page deletion', 'jet-engine' );
	?>
		<template v-if="itemName">- <span class="jet-engine-accent-text">{{ itemName }}</span></template>
	</div>
	<p slot="content">
		<?php _e( 'Are you sure you want to delete this page?', 'jet-engine' ); ?><br>
		<?php _e( 'If yes - please select what to do with saved options related to this page:', 'jet-engine' ); ?>
	</p>
	<cx-vui-select
		slot="content"
		v-model="pageAction"
		:size="'fullwidth'"
		:prevent-wrap="true"
		:options-list="[
			{
				value: 'none',
				label: '<?php _e( 'Left options without changes', 'jet-engine' ) ?>'
			},
			{
				value: 'delete',
				label: '<?php _e( 'Delete saved options', 'jet-engine' ) ?>'
			},
		]"
	></cx-vui-select>
</cx-vui-popup>