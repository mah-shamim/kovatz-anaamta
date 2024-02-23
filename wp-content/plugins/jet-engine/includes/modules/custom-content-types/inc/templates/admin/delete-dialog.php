<cx-vui-popup
	v-model="isVisible"
	:ok-label="'<?php _e( 'Delete content type', 'jet-engine' ) ?>'"
	:cancel-label="'<?php _e( 'Cancel', 'jet-engine' ) ?>'"
	@on-cancel="handleCancel"
	@on-ok="handleOk"
>
	<div class="cx-vui-subtitle" slot="title"><?php
		_e( 'Please confirm content type deletion', 'jet-engine' );
	?>
		<template v-if="itemName">- <span class="jet-engine-accent-text">{{ itemName }}</span></template>
	</div>
	<p slot="content">
		<?php _e( 'Are you sure you want to delete this content type? Related database table also will be deleted and all data will be lost.', 'jet-engine' ); ?><br>
	</p>
</cx-vui-popup>