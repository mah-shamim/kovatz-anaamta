<cx-vui-popup
	v-model="isVisible"
	:ok-label="'<?php _e( 'Delete query', 'jet-engine' ) ?>'"
	:cancel-label="'<?php _e( 'Cancel', 'jet-engine' ) ?>'"
	@on-cancel="handleCancel"
	@on-ok="handleOk"
>
	<div class="cx-vui-subtitle" slot="title"><?php
		_e( 'Please confirm query deletion', 'jet-engine' );
	?>
		<template v-if="itemName">- <span class="jet-engine-accent-text">{{ itemName }}</span></template>
	</div>
	<p slot="content">
		<?php _e( 'Are you sure you want to delete this query? Please ensure you removed it from all widgets and blocks where it was used.', 'jet-engine' ); ?><br>
	</p>
</cx-vui-popup>
