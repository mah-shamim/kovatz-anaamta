<cx-vui-collapse
	:collapsed="false"
>
	<h3 class="cx-vui-subtitle" slot="title" v-html="blockTitle + ' (' + fieldsList.length + ')'"></h3>
	<cx-vui-repeater
		slot="content"
		:button-label="buttonLabel"
		button-style="accent"
		button-size="default"
		v-model="fieldsList"
		@input="onInput"
		@add-new-item="addNewField"
	>
		<cx-vui-repeater-item
			v-for="( field, index ) in fieldsList"
			:title="fieldsList[ index ].title"
			:subtitle="getFieldSubtitle( fieldsList[ index ] )"
			:collapsed="isCollapsed( field )"
			:index="index"
			:customCss="isNestedField( field ) ? 'jet-engine-nested-item' : ''"
			@clone-item="cloneField( $event )"
			@delete-item="deleteField( $event )"
			:key="field.id ? field.id : field.id = getRandomID()"
			:ref="'field' + field.id"
		>
			<div
				slot="before-actions"
				v-if="showCondition( field )"
				@click="showConditionPopup( index )"
				:class="{
					'jet-engine-conditional-field': true,
					'cx-vui-repeater-item__copy': true,
					'jet-engine-conditional-field--active': hasConditions( field ),
				}"
			>
				<svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="1.414">
					<path d="M11.375 20.844c-1.125 0-1.875.75-1.875 1.875s.75 1.875 1.875 1.875c3.75 0 7.5 1.5 10.125 4.125.75.75 1.875.75 2.625 0s.75-1.875 0-2.625c-3.375-3.375-8.063-5.25-12.75-5.25z" fill-rule="nonzero"/>
					<path d="M53.938 21.219l-5.25-5.25c-.376-.375-.938-.563-1.313-.563-.563 0-.938.188-1.312.563-.75.75-.75 1.875 0 2.625l2.062 2.062h-4.313c-4.875 0-9.375 1.875-12.75 5.25l-9.375 9.375c-2.625 2.625-6.375 4.125-10.125 4.125-1.125 0-1.875.75-1.875 1.875s.75 1.875 1.875 1.875c4.688 0 9.375-1.875 12.75-5.25l9.375-9.375c2.813-2.625 6.375-4.125 10.125-4.125h4.313l-2.062 2.063c-.75.75-.75 1.875 0 2.625s1.875.75 2.625 0l5.25-5.25c.75-.563.75-1.875 0-2.625z" fill-rule="nonzero"/>
					<path d="M53.938 40.156l-5.25-5.25c-.376-.375-.938-.562-1.313-.562-.563 0-.938.187-1.312.562-.75.75-.75 1.875 0 2.625l2.062 2.063h-4.313c-3.75 0-7.5-1.5-10.125-4.125-.374-.375-.937-.563-1.312-.563-.563 0-.938.188-1.312.563-.75.75-.75 1.875 0 2.625 3.374 3.375 7.874 5.25 12.75 5.25h4.312l-2.063 2.062c-.75.75-.75 1.875 0 2.625s1.876.75 2.625 0l5.25-5.25c.75-.562.75-1.875 0-2.625z" fill-rule="nonzero"/>
				</svg>
				
				<div class="cx-vui-tooltip"><?php _e( 'Conditional Logic', 'jet-engine' ); ?></div>
			</div>
			
			<jet-meta-field 
				v-model="fieldsList[ index ]"
				v-if="! isFieldCollapsed( field.id )"
				:index="index"
				:field-types="fieldTypes"
				:hide-options="hideOptions"
				:disabled-fields="disabledFields"
				:fields-names="fieldsNames"
				:slug-delimiter="slugDelimiter"
				@show-condition-popup="showConditionPopup( index )"
				@show-repeater-condition-popup="showConditionPopup( index, $event )"
			/>

		</cx-vui-repeater-item>

		<jet-meta-field-conditions-dialog
			v-if="isVisibleConditionPopup"
			:value="{
				'isEnabled': currentConditionField.conditional_logic ? currentConditionField.conditional_logic : false,
				'conditions': currentConditionField.conditions ? currentConditionField.conditions : [],
				'relation': currentConditionField.conditional_relation ? currentConditionField.conditional_relation : 'AND',
			}"
			:field="currentConditionField"
			:fieldsList="null !== currentConditionRepIndex ? fieldsList[ currentConditionIndex ]['repeater-fields'] : fieldsList"
			@input="setConditionsFieldProps( currentConditionIndex, currentConditionRepIndex, $event )"
			@on-close="hideConditionPopup"
		></jet-meta-field-conditions-dialog>

	</cx-vui-repeater>
</cx-vui-collapse>
