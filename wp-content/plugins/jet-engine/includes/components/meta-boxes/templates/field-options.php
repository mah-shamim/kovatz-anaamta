<cx-vui-repeater
	button-label="<?php _e( 'New Field Option', 'jet-engine' ); ?>"
	button-style="accent"
	button-size="mini"
	v-model="options"
	@add-new-item="addNewFieldOption( $event )"
>
	<cx-vui-repeater-item
		v-for="( option, optionIndex ) in options"
		:title="options[ optionIndex ].value"
		:subtitle="getOptionSubtitle( options[ optionIndex ] )"
		:collapsed="isCollapsed( option )"
		:index="optionIndex"
		@clone-item="cloneOption( $event )"
		@delete-item="deleteOption( $event )"
		:key="option.id ? option.id : option.id = getRandomID()"
	>
		<cx-vui-input
			label="<?php _e( 'Option Value', 'jet-engine' ); ?>"
			description="<?php _e( 'This value will be saved into Database', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			:value="options[ optionIndex ].key"
			@input="setOptionProp( optionIndex, 'key', $event )"
		></cx-vui-input>
		<cx-vui-input
			label="<?php _e( 'Option label', 'jet-engine' ); ?>"
			description="<?php _e( 'This will be shown for user on Post edit page', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			:value="options[ optionIndex ].value"
			@input="setOptionProp( optionIndex, 'value', $event )"
		></cx-vui-input>
		<cx-vui-switcher
			label="<?php _e( 'Is checked (selected)', 'jet-engine' ); ?>"
			description="<?php _e( 'Check this to make this field checked or selected by default.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:value="options[ optionIndex ].is_checked"
			@input="setOptionProp( optionIndex, 'is_checked', $event )"
		></cx-vui-switcher>
	</cx-vui-repeater-item>
</cx-vui-repeater>