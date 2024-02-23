<cx-vui-popup
	class="jet-engine-condition-popup"
	v-model="isVisible"
	body-width="900px"
	@on-cancel="handleCancel"
	:footer="false"
>
	<div class="cx-vui-subtitle" slot="title">
		<?php _e( 'Conditional Logic for', 'jet-engine' ); ?>
		<span class="jet-engine-condition-field-name" v-html="field.title"></span>
		<?php _e( 'Field', 'jet-engine' ); ?>
	</div>
	<template slot="content">
		<cx-vui-switcher
			label="<?php _e( 'Enable Conditional Logic', 'jet-engine' ); ?>"
			description="<?php _e( 'Toggle this option to set display rules.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			v-model="isEnabled"
		></cx-vui-switcher>
		<cx-vui-component-wrapper
			:wrapper-css="[ 'fullwidth-control' ]"
			:conditions="[
				{
					'input':   isEnabled,
					'compare': 'equal',
					'value':   true,
				},
			]"
		>
			<div class="cx-vui-inner-panel">
				<cx-vui-repeater
					:button-label="'<?php _e( 'New Rule', 'jet-engine' ); ?>'"
					:button-style="'accent'"
					:button-size="'mini'"
					v-model="conditions"
					@add-new-item="addNewCondition()"
					append-to=".jet-engine-condition-popup .cx-vui-popup__body"
				>
					<cx-vui-repeater-item
						v-for="( condition, conditionIndex ) in conditions"
						:title="getConditionFieldTitle( conditionIndex )"
						:subtitle="getConditionFieldSubTitle( conditionIndex )"
						:collapsed="isCollapsed( condition )"
						:index="conditionIndex"
						@clone-item="cloneCondition( $event )"
						@delete-item="deleteCondition( $event )"
						:key="condition.id ? condition.id : condition.id = getRandomID()"
					>
						<cx-vui-select
							:label="'<?php _e( 'Field', 'jet-engine' ); ?>'"
							:wrapper-css="[ 'equalwidth' ]"
							:size="'fullwidth'"
							:options-list="getConditionFieldsList()"
							:value="conditions[ conditionIndex ].field"
							@input="setConditionProp( conditionIndex, 'field', $event )"
						></cx-vui-select>
						<cx-vui-select
							:label="'<?php _e( 'Operator', 'jet-engine' ); ?>'"
							:wrapper-css="[ 'equalwidth' ]"
							:size="'fullwidth'"
							:options-list="getOperatorsList( conditionIndex )"
							:value="conditions[ conditionIndex ].operator"
							@input="setConditionProp( conditionIndex, 'operator', $event )"
						></cx-vui-select>
						<cx-vui-input
							:label="'<?php _e( 'Value', 'jet-engine' ); ?>'"
							:wrapper-css="[ 'equalwidth' ]"
							:size="'fullwidth'"
							:value="conditions[ conditionIndex ].value"
							@input="setConditionProp( conditionIndex, 'value', $event )"
							:conditions="[
								{
									'input':    getConditionFieldType( conditionIndex ),
									'compare':  'not_in',
									'value':    [ 'checkbox', 'radio', 'select', 'switcher', 'repeater', 'media', 'gallery', 'posts', 'iconpicker', '' ],
								},
								{
									'input':   conditions[ conditionIndex ].operator,
									'compare': 'not_in',
									'value':   [ 'empty', '!empty', '' ],
								},
							]"
						>
							<div
								v-if="-1 !== ['in', 'not_in'].indexOf( conditions[ conditionIndex ].operator )"
								style="padding-top:4px;font-style:italic"
							>
								<?php _e( 'Separate multiple values with comma', 'jet-engine' ); ?>
							</div>
						</cx-vui-input>
						<cx-vui-select
							:label="'<?php _e( 'Value', 'jet-engine' ); ?>'"
							:wrapper-css="[ 'equalwidth' ]"
							:size="'fullwidth'"
							:options-list="[
								{
									value: 'true',
									label: '<?php _e( 'On', 'jet-engine' ); ?>',
								},
								{
									value: 'false',
									label: '<?php _e( 'Off', 'jet-engine' ); ?>',
								}
							]"
							:value="conditions[ conditionIndex ].value"
							@input="setConditionProp( conditionIndex, 'value', $event )"
							:conditions="[
								{
									'input':   getConditionFieldType( conditionIndex ),
									'compare': 'equal',
									'value':   'switcher',
								},
								{
									'input':   conditions[ conditionIndex ].operator,
									'compare': 'not_equal',
									'value':   '',
								},
							]"
						></cx-vui-select>
						<cx-vui-f-select
							:label="'<?php _e( 'Value', 'jet-engine' ); ?>'"
							:wrapper-css="[ 'equalwidth' ]"
							:size="'fullwidth'"
							:options-list="getConditionValuesList( conditionIndex )"
							:value="conditions[ conditionIndex ].value"
							@input="setConditionProp( conditionIndex, 'value', $event )"
							:remote="useRemoteCb( conditionIndex )"
							:remote-callback="getRemoteFields.bind( this, conditionIndex )"
							:remote-trigger="2"
							:conditions="[
								{
									'input':   getConditionFieldType( conditionIndex ),
									'compare': 'in',
									'value':   [ 'checkbox', 'radio', 'select' ],
								},
								{
									'input':   conditions[ conditionIndex ].operator,
									'compare': 'not_in',
									'value':   [ 'in', 'not_in', 'empty', '!empty', '' ],
								},
							]"
						></cx-vui-f-select>
						<cx-vui-f-select
							:label="'<?php _e( 'Value', 'jet-engine' ); ?>'"
							:wrapper-css="[ 'equalwidth' ]"
							:size="'fullwidth'"
							:multiple="true"
							:options-list="getConditionValuesList( conditionIndex )"
							:value="conditions[ conditionIndex ].values"
							@input="setConditionProp( conditionIndex, 'values', $event )"
							:remote="useRemoteCb( conditionIndex )"
							:remote-callback="getRemoteFields.bind( this, conditionIndex )"
							:remote-trigger="2"
							:conditions="[
								{
									'input':   getConditionFieldType( conditionIndex ),
									'compare': 'in',
									'value':   [ 'checkbox', 'radio', 'select' ],
								},
								{
									'input':   conditions[ conditionIndex ].operator,
									'compare': 'in',
									'value':   [ 'in', 'not_in' ],
								},
							]"
						></cx-vui-f-select>
					</cx-vui-repeater-item>
				</cx-vui-repeater>
			</div>
		</cx-vui-component-wrapper>
		<cx-vui-select
			label="<?php _e( 'Relation', 'jet-engine' ); ?>"
			description="<?php _e( 'The logical relationship between conditional rules', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="[
				{
					value: 'AND',
					label: '<?php _e( 'And', 'jet-engine' ); ?>',
				},
				{
					value: 'OR',
					label: '<?php _e( 'Or', 'jet-engine' ); ?>',
				},
			]"
			size="fullwidth"
			v-model="relation"
			:conditions="[
				{
					'input':   isEnabled,
					'compare': 'equal',
					'value':   true,
				},
			]"
		></cx-vui-select>
	</template>
</cx-vui-popup>