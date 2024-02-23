<cx-vui-popup
	v-model="isVisible"
	:ok-label="'<?php _e( 'Copy Content Type', 'jet-engine' ) ?>'"
	:cancel-label="'<?php _e( 'Cancel', 'jet-engine' ) ?>'"
	@on-cancel="handleCancel"
	@on-ok="handleOk"
>
	<div class="cx-vui-subtitle" slot="title"><?php
		_e( 'Please set a new name and slug of Custom Content Type', 'jet-engine' );
	?></div>
	<cx-vui-input
		slot="content"
		:label="'<?php _e( 'Name', 'jet-engine' ); ?>'"
		:description="'<?php _e( 'Name of Custom Content Type will be shown in the admin menu', 'jet-engine' ); ?>'"
		:wrapper-css="[ 'equalwidth' ]"
		:size="'fullwidth'"
		v-model="copiedItemName"
	></cx-vui-input>
	<cx-vui-input
		slot="content"
		:label="'<?php _e( 'Slug', 'jet-engine' ); ?>'"
		:description="'<?php _e( 'Slug will be used to create appropriate data base table and access Custom Content Type data. Only latin letters, `-` or `_` are allowed.', 'jet-engine' ); ?>'"
		:wrapper-css="[ 'equalwidth' ]"
		:size="'fullwidth'"
		:error="slugError"
		v-model="copiedItemSlug"
		@on-focus="handleSlugFocus"
		@on-blur="handleSlugBlur"
	>
		<div
			class="cx-vui-inline-notice cx-vui-inline-notice--error"
			v-if="slugError"
			v-html="slugErrorNotice"
		></div>
	</cx-vui-input>
</cx-vui-popup>