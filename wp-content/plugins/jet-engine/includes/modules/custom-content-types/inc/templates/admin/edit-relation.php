<cx-vui-collapse
	:collapsed="false"
	v-if="showCCTSettings() && ensureArgs()"
>
	<h3 class="cx-vui-subtitle" slot="title"><?php _e( 'Content Type Related Settings', 'jet-engine' ); ?></h3>
	<div class="cx-vui-panel" slot="content">
		<div v-for="contentType in getActiveTypes()">
			<h4 class="cx-vui-subtitle" slot="title">{{ getTypeLabel( contentType ) }}:</h4>
			<cx-vui-select
				label="<?php _e( 'Title field', 'jet-engine' ); ?>"
				description="<?php _e( 'Select field will be used as title of related items for current Content Type', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:options-list="getTypeFields( contentType )"
				size="fullwidth"
				:multiple="false"
				:value="args[ contentType ].title_field"
				@input="( newValue ) => { setArg( newValue, contentType, 'title_field' ) }"
			></cx-vui-select>
			<cx-vui-f-select
				label="<?php _e( 'Create fields', 'jet-engine' ); ?>"
				description="<?php _e( 'Select fields will be used to create new related item of current Content Type', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:options-list="getTypeFields( contentType )"
				size="fullwidth"
				:multiple="true"
				:value="args[ contentType ].create_fields"
				@input="( newValue ) => { setArg( newValue, contentType, 'create_fields' ) }"
			></cx-vui-f-select>
		</div>
	</div>
</cx-vui-collapse>
