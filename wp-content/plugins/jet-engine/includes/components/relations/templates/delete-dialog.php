<cx-vui-popup
	v-model="isVisible"
	:ok-label="'<?php _e( 'Delete', 'jet-engine' ) ?>'"
	:cancel-label="'<?php _e( 'Cancel', 'jet-engine' ) ?>'"
	@on-cancel="handleCancel"
	@on-ok="handleOk"
>
	<div class="cx-vui-subtitle" slot="title"><?php
		_e( 'Please confirm relation deletion', 'jet-engine' );
	?>
		<template v-if="itemName">- <span class="jet-engine-accent-text">{{ itemName }}</span></template>
	</div>
	<div slot="content">
		<?php _e( 'Are you sure you want to delete this posts relation?', 'jet-engine' ); ?><br>
	</div>
</cx-vui-popup>