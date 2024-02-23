<div class="jet-engine-shortcode-generator">
	<div class="jet-engine-shortcode-generator__fields">
		<template v-for="control in controls" v-if="">
			<cx-vui-component-wrapper
				:wrapper-css="[ 'fullwidth-control' ]"
				v-if="'repeater' === control.type && isVisible( control )"
			>
				<div class="cx-vui-inner-panel query-panel">
					<div class="cx-vui-component__label">{{ control.label }}</div>
					<br>
					<cx-vui-repeater
						button-label="<?php _e( 'Add new item', 'jet-engine' ); ?>"
						button-style="accent"
						button-size="mini"
						v-model="attrs[ control.name ]"
						@add-new-item="addNewItem( $event, [], attrs[ control.name ], control )"
					>
						<cx-vui-repeater-item
							v-for="( item, index ) in attrs[ control.name ]"
							:title="attrs[ control.name ][ index ][ control.title ]"
							:collapsed="isCollapsed( item )"
							:index="index"
							@clone-item="cloneItem( $event, item._id, attrs[ control.name ] )"
							@delete-item="deleteItem( $event, item._id, attrs[ control.name ] )"
							:key="item._id"
						>
							<component
								v-for="field in getPreparedControls( control.fields )"
								:is="field.type"
								:type="field.inputType"
								:options-list="field.optionsList"
								:groups-list="field.groupsList"
								:label="field.label"
								:description="field.description"
								:wrapper-css="[ 'equalwidth' ]"
								:key="control.name + field.name + index"
								size="fullwidth"
								v-if="isRepeaterFieldVisible( field, item )"
								:value="attrs[ control.name ][ index ][ field.name ]"
								@input="setItemProp( item._id, field.name, $event, attrs[ control.name ] )"
							/>
						</cx-vui-repeater-item>
					</cx-vui-repeater>
				</div>
			</cx-vui-component-wrapper>
			<component
				:is="control.type"
				:key="control.name"
				:options-list="control.optionsList"
				:groups-list="control.groupsList"
				:label="control.label"
				:description="control.description"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				v-model="attrs[ control.name ]"
				v-else-if="isVisible( control )"
			/>
		</template>
	</div>
	<div class="jet-shortocde-generator__result">
		{{ generatedShortcode }}		
		<div
			class="jet-shortocde-generator__result-copy"
			role="button"
			v-if="showCopyShortcode"
			@click="copyShortcodeToClipboard"
		>
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24px" height="24px"><path d="M0 0h24v24H0z" fill="none"/><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>
			<div
				class="cx-vui-tooltip"
				v-if="copied"
			>
				<?php _e( 'Copied!', 'jet-engine' ); ?>
			</div>
		</div>
	</div>
</div>