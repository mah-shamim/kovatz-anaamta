<cx-vui-popup
	v-model="isVisible"
	:ok-label="'<?php _e( 'Delete', 'jet-engine' ) ?>'"
	:cancel-label="'<?php _e( 'Cancel', 'jet-engine' ) ?>'"
	@on-cancel="handleCancel"
	@on-ok="handleOk"
>
	<div class="cx-vui-subtitle" slot="title"><?php
		_e( 'Please confirm taxonomy deletion', 'jet-engine' );
	?>
		<template v-if="taxName">- <span class="jet-engine-accent-text">{{ taxName }}</span></template>
	</div>
	<p slot="content">
		<?php _e( 'Are you sure you want to delete this taxonomy?', 'jet-engine' ); ?><br>
		<?php _e( 'If yes - please select what to do with created terms of this taxonomy:', 'jet-engine' ); ?>
	</p>
	<cx-vui-select
		slot="content"
		v-model="termsAction"
		:size="'fullwidth'"
		:prevent-wrap="true"
		:options-list="[
			{
				value: 'none',
				label: '<?php _e( 'Leave terms without changes', 'jet-engine' ) ?>'
			},
			{
				value: 'delete',
				label: '<?php _e( 'Delete all terms', 'jet-engine' ) ?>'
			},
		]"
	></cx-vui-select>
	<cx-vui-select
		slot="content"
		v-model="attachTo"
		:size="'fullwidth'"
		:prevent-wrap="true"
		:wrapper-css="[ 'indent-top' ]"
		:options-list="availableTaxonomies"
		:conditions="[
			{
				input: this.termsAction,
				compare: 'equal',
				value: 'reattach',
			}
		]"
	></cx-vui-select>
</cx-vui-popup>