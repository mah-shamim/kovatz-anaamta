<cx-vui-popup
	v-model="isVisible"
	:ok-label="'<?php _e( 'Delete meta box', 'jet-engine' ) ?>'"
	:cancel-label="'<?php _e( 'Cancel', 'jet-engine' ) ?>'"
	@on-cancel="handleCancel"
	@on-ok="handleOk"
>
	<div class="cx-vui-subtitle" slot="title"><?php
		_e( 'Please confirm meta box deletion', 'jet-engine' );
	?>
		<template v-if="itemName">- <span class="jet-engine-accent-text">{{ itemName }}</span></template>
	</div>
	<p slot="content">
		<?php _e( 'Are you sure you want to delete this meta box?', 'jet-engine' ); ?><br>
	</p>
</cx-vui-popup>