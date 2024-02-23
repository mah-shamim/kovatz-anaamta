<div>
	<cx-vui-input
		label="<?php _e( 'Label', 'jet-engine' ); ?>"
		description="<?php _e( 'Meta field label. It will be displayed on Post edit page', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.title"
		@input="setFieldProp( 'title', $event )"
		@on-input-change="preSetFieldName()"
	></cx-vui-input>
	<cx-vui-input
		label="<?php _e( 'Name/ID', 'jet-engine' ); ?>"
		description="<?php _e( 'Meta field name/key/ID. Under this name field will be stored in the database. Should contain only Latin letters, numbers, `-` or `_` chars', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.name"
		@input="setFieldProp( 'name', $event )"
		@on-input-change="sanitizeFieldName()"
	></cx-vui-input>
	<cx-vui-select
		label="<?php _e( 'Object type', 'jet-engine' ); ?>"
		description="<?php _e( 'Current meta box object type: field or layout element. To close the action of previously selected Tab or Accordion group, use the `Endpoint` option', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="getFilteredObjectTypes( [
			{
				value: 'field',
				label: '<?php _e( 'Field', 'jet-engine' ); ?>'
			},
			{
				value: 'tab',
				label: '<?php _e( 'Tab', 'jet-engine' ); ?>'
			},
			{
				value: 'accordion',
				label: '<?php _e( 'Accordion', 'jet-engine' ); ?>'
			},
			{
				value: 'endpoint',
				label: '<?php _e( 'Endpoint', 'jet-engine' ); ?>'
			},
		] )"
		:value="field.object_type"
		@input="setFieldProp( 'object_type', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   'object_type',
				'compare': 'not_in',
				'value':   hideOptions,
			},
			{
				'input':    'object_type',
				'compare': 'not_in',
				'value':    disabledFields,
			},
		], 'object_type' )"
	></cx-vui-select>
	<cx-vui-select
		label="<?php _e( 'Field type', 'jet-engine' ); ?>"
		description="<?php _e( 'Meta field type. Defines the way field to be displayed on Post edit page', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="getFilteredFieldTypes( fieldTypes )"
		:value="field.type"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'type' )"
		@input="setFieldProp( 'type', $event )"
	></cx-vui-select>
	<cx-vui-select
		label="<?php _e( 'Layout', 'jet-engine' ); ?>"
		description="<?php _e( 'Select tab layout. Note, layout selected on first tab in set will be automatically applied to all other tabs', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: 'horizontal',
				label: '<?php _e( 'Horizontal', 'jet-engine' ); ?>'
			},
			{
				value: 'vertical',
				label: '<?php _e( 'Vertical', 'jet-engine' ); ?>'
			},
		]"
		:value="field.tab_layout"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'tab',
			}
		], 'tab_layout' )"
		@input="setFieldProp( 'tab_layout', $event )"
	></cx-vui-select>
	<cx-vui-switcher
		label="<?php _e( 'Allow Custom', 'jet-engine' ); ?>"
		description="<?php _e( 'Allow \'custom\' values to be added', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.allow_custom"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio' ],
			},
			{
				'input':    'allow_custom',
				'compare': 'not_in',
				'value':    disabledFields,
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   'allow_custom',
				'compare': 'not_in',
				'value':   hideOptions,
			}
		], 'allow_custom' )"
		@input="setFieldProp( 'allow_custom', $event )"
	></cx-vui-switcher>
	<cx-vui-switcher
		label="<?php _e( 'Save Custom', 'jet-engine' ); ?>"
		description="<?php _e( 'Save \'custom\' values to the field\'s options', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.save_custom"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio' ],
			},
			{
				'input':    'save_custom',
				'compare': 'not_in',
				'value':    disabledFields,
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.allow_custom,
				'compare': 'equal',
				'value':   true,
			},
			{
				'input':   'save_custom',
				'compare': 'not_in',
				'value':   hideOptions,
			}
		], 'save_custom' )"
		@input="setFieldProp( 'save_custom', $event )"
	></cx-vui-switcher>
	<cx-vui-select
		label="<?php _e( 'Source', 'jet-engine' ); ?>"
		description="<?php _e( 'Select source to get field options from', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="allowedSources"
		:value="field.options_source"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
		], 'options_source' )"
		@input="setFieldProp( 'options_source', $event )"
	></cx-vui-select>
	<cx-vui-select
		label="<?php _e( 'Glossary', 'jet-engine' ); ?>"
		description="<?php _e( 'Select exact glossary to get options from', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="glossariesList"
		:value="field.glossary_id"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'glossary',
			},
		], 'glossary_id' )"
		@input="setFieldProp( 'glossary_id', $event )"
	></cx-vui-select>
	<cx-vui-select
		label="<?php _e( 'Query', 'jet-engine' ); ?>"
		description="<?php _e( 'Select exact query to get options from', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="queriesList"
		:value="field.query_id"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'query',
			},
		], 'query_id' )"
		@input="setFieldProp( 'query_id', $event )"
	></cx-vui-select>
	<cx-vui-input
		label="<?php _e( 'Value Field', 'jet-engine' ); ?>"
		description="<?php _e( 'Query result field to use as option value. If empty, we`ll try to use most obvious value. For example ID for posts.', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.query_value_field"
		@input="setFieldProp( 'query_value_field', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'query',
			},
		], 'query_value_field' )"
	></cx-vui-input>
	<cx-vui-input
		label="<?php _e( 'Label Field', 'jet-engine' ); ?>"
		description="<?php _e( 'Query result field to use as option label. If empty, we`ll try to use most obvious value. For example post_title for posts.', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.query_label_field"
		@input="setFieldProp( 'query_label_field', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'query',
			},
		], 'query_label_field' )"
	></cx-vui-input>
	<cx-vui-textarea
		label="<?php _e( 'Bulk Options', 'jet-engine' ); ?>"
		description="<?php _e( 'One option per line. Allowed formats:<br>
			<i>value</i> - value and label will be the same<br>
			<i>value::label</i> - separate value and label<br>
			<i>value::label::checked</i> - separate value and label, checked by default', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:rows="Number(8)"
		:value="field.bulk_options"
		@input="setFieldProp( 'bulk_options', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'manual_bulk',
			},
		], 'bulk_options' )"
	></cx-vui-textarea>
	<cx-vui-component-wrapper
		:wrapper-css="[ 'fullwidth-control' ]"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'in',
				'value':    [ 'checkbox', 'select', 'radio' ],
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'manual',
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'options_wrapper' )"
	>
		<div class="cx-vui-inner-panel">
			<jet-meta-field-options
				:value="field.options"
				@input="setFieldProp( 'options', $event )"
				:field="field"
			/>
		</div>
	</cx-vui-component-wrapper>
	<cx-vui-select
		label="<?php _e( 'Layout', 'jet-engine' ); ?>"
		description="<?php _e( 'Select layout orientation of inputs', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: 'vertical',
				label: '<?php _e( 'Vertical', 'jet-engine' ); ?>'
			},
			{
				value: 'horizontal',
				label: '<?php _e( 'Horizontal', 'jet-engine' ); ?>'
			},
		]"
		:value="field.check_radio_layout"
		@input="setFieldProp( 'check_radio_layout', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
		], 'check_radio_layout' )"
	></cx-vui-select>
	<cx-vui-switcher
		label="<?php _e( 'Save as timestamp', 'jet-engine' ); ?>"
		description="<?php _e( 'If this option is enabled date will be saved in database Unix timestamp. Toggle it if you need to sort or query posts by date', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.is_timestamp"
		@input="setFieldProp( 'is_timestamp', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'in',
				'value':    [ 'date', 'datetime-local' ],
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'is_timestamp' )"
	></cx-vui-switcher>
	<cx-vui-input
		label="<?php _e( 'Placeholder', 'jet-engine' ); ?>"
		description="<?php _e( 'Placeholder text', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.placeholder"
		@input="setFieldProp( 'placeholder', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'equal',
				'value':    'select',
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'placeholder' )"
	></cx-vui-input>
	<cx-vui-switcher
		label="<?php _e( 'Save as array', 'jet-engine' ); ?>"
		description="<?php _e( 'If this option is enabled checked values will be stored as plain PHP array. Use this option if this meta value will be edited from front-end form', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.is_array"
		@input="setFieldProp( 'is_array', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    'is_array',
				'compare': 'not_in',
				'value':    disabledFields,
			},
			{
				'input':    field.type,
				'compare': 'equal',
				'value':    'checkbox',
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'is_array' )"
	></cx-vui-switcher>
	<cx-vui-f-select
		label="<?php _e( 'Search in post types', 'jet-engine' ); ?>"
		description="<?php _e( 'Select post types available to search in', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:options-list="postTypes"
		size="fullwidth"
		:multiple="true"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'posts',
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'search_post_type' )"
		:value="field.search_post_type"
		@input="setFieldProp( 'search_post_type', $event )"
	></cx-vui-f-select>
	<cx-vui-switcher
		label="<?php _e( 'Multiple', 'jet-engine' ); ?>"
		description="<?php _e( 'Allow to select multiple values', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.is_multiple"
		@input="setFieldProp( 'is_multiple', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'in',
				'value':    [ 'select', 'posts' ],
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'is_multiple' )"
	></cx-vui-switcher>
	<cx-vui-input
		label="<?php _e( 'Min value', 'jet-engine' ); ?>"
		description="<?php _e( 'Set a minimum value for a number field', 'jet-engine' ); ?>"
		type="number"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.min_value"
		@input="setFieldProp( 'min_value', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'number',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'min_value' )"
	></cx-vui-input>
	<cx-vui-input
		label="<?php _e( 'Max value', 'jet-engine' ); ?>"
		description="<?php _e( 'Set a maximum value for a number field', 'jet-engine' ); ?>"
		type="number"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.max_value"
		@input="setFieldProp( 'max_value', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'number',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'max_value' )"
	></cx-vui-input>
	<cx-vui-input
		label="<?php _e( 'Step value', 'jet-engine' ); ?>"
		description="<?php _e( 'Set a stepping interval for a number field', 'jet-engine' ); ?>"
		type="number"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.step_value"
		@input="setFieldProp( 'step_value', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'number',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'step_value' )"
	></cx-vui-input>
	<cx-vui-select
		label="<?php _e( 'Value format', 'jet-engine' ); ?>"
		description="<?php _e( 'Set the format of the value will be stored in the database', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: 'id',
				label: '<?php _e( 'Media ID', 'jet-engine' ); ?>'
			},
			{
				value: 'url',
				label: '<?php _e( 'Media URL', 'jet-engine' ); ?>'
			},
			{
				value: 'both',
				label: '<?php _e( 'Array with media ID and URL', 'jet-engine' ); ?>'
			},
		]"
		:value="field.value_format"
		@input="setFieldProp( 'value_format', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'media', 'gallery' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'value_format' )"
	></cx-vui-select>
	<cx-vui-component-wrapper
		:wrapper-css="[ 'fullwidth-control' ]"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'equal',
				'value':    'repeater',
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'repeater_wrapper' )"
	>
		<div class="cx-vui-inner-panel">
			<cx-vui-repeater
				button-label="<?php _e( 'New Repeater Field', 'jet-engine' ); ?>"
				button-style="accent"
				button-size="mini"
				:value="field['repeater-fields']"
				@input="setFieldProp( 'repeater-fields', $event )"
				@add-new-item="addNewRepeaterField( $event )"
			>
				<cx-vui-repeater-item
					v-for="( rField, rFieldIndex ) in field['repeater-fields']"
					:title="field['repeater-fields'][ rFieldIndex ].title"
					:subtitle="field['repeater-fields'][ rFieldIndex ].name + ' (' + field['repeater-fields'][ rFieldIndex ].type + ')'"
					:collapsed="isCollapsed( rField )"
					:index="rFieldIndex"
					@clone-item="cloneRepeaterField( $event )"
					@delete-item="deleteRepeaterField( $event )"
					:key="rField.id ? rField.id : rField.id = getRandomID()"
				>
					<div
						slot="before-actions"
						v-if="showCondition( rField )"
						@click="showRepeaterConditionPopup( rFieldIndex )"
						:class="{
							'jet-engine-conditional-field': true,
							'cx-vui-repeater-item__copy': true,
							'jet-engine-conditional-field--active': hasConditions( rField ),
						}"
					>
						<svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="1.414">
							<path d="M11.375 20.844c-1.125 0-1.875.75-1.875 1.875s.75 1.875 1.875 1.875c3.75 0 7.5 1.5 10.125 4.125.75.75 1.875.75 2.625 0s.75-1.875 0-2.625c-3.375-3.375-8.063-5.25-12.75-5.25z" fill-rule="nonzero"/>
							<path d="M53.938 21.219l-5.25-5.25c-.376-.375-.938-.563-1.313-.563-.563 0-.938.188-1.312.563-.75.75-.75 1.875 0 2.625l2.062 2.062h-4.313c-4.875 0-9.375 1.875-12.75 5.25l-9.375 9.375c-2.625 2.625-6.375 4.125-10.125 4.125-1.125 0-1.875.75-1.875 1.875s.75 1.875 1.875 1.875c4.688 0 9.375-1.875 12.75-5.25l9.375-9.375c2.813-2.625 6.375-4.125 10.125-4.125h4.313l-2.062 2.063c-.75.75-.75 1.875 0 2.625s1.875.75 2.625 0l5.25-5.25c.75-.563.75-1.875 0-2.625z" fill-rule="nonzero"/>
							<path d="M53.938 40.156l-5.25-5.25c-.376-.375-.938-.562-1.313-.562-.563 0-.938.187-1.312.562-.75.75-.75 1.875 0 2.625l2.062 2.063h-4.313c-3.75 0-7.5-1.5-10.125-4.125-.374-.375-.937-.563-1.312-.563-.563 0-.938.188-1.312.563-.75.75-.75 1.875 0 2.625 3.374 3.375 7.874 5.25 12.75 5.25h4.312l-2.063 2.062c-.75.75-.75 1.875 0 2.625s1.876.75 2.625 0l5.25-5.25c.75-.562.75-1.875 0-2.625z" fill-rule="nonzero"/>
						</svg>

						<div class="cx-vui-tooltip"><?php _e( 'Conditional Logic', 'jet-engine' ); ?></div>
					</div>

					<cx-vui-input
						label="<?php _e( 'Label', 'jet-engine' ); ?>"
						description="<?php _e( 'Repeater field label. Will be displayed on Post edit page', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:value="field['repeater-fields'][ rFieldIndex ].title"
						@input="setRepeaterFieldProp( rFieldIndex, 'title', $event )"
						@on-input-change="preSetRepeaterFieldName( rFieldIndex )"
					></cx-vui-input>
					<cx-vui-input
						label="<?php _e( 'Name', 'jet-engine' ); ?>"
						description="<?php _e( 'Repeater field name/ID. Should contain only latin letters, numbers, `-` or `_` chars', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:value="field['repeater-fields'][ rFieldIndex ].name"
						@input="setRepeaterFieldProp( rFieldIndex, 'name', $event )"
						@on-input-change="sanitizeRepeaterFieldName( rFieldIndex )"
					></cx-vui-input>
					<cx-vui-select
						label="<?php _e( 'Type', 'jet-engine' ); ?>"
						description="<?php _e( 'Repeater field type. Defines the way field be displayed on Post edit page', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="repeaterFieldTypes"
						:value="field['repeater-fields'][ rFieldIndex ].type"
						@input="setRepeaterFieldProp( rFieldIndex, 'type', $event )"
					></cx-vui-select>
					<cx-vui-select
						label="<?php _e( 'Source', 'jet-engine' ); ?>"
						description="<?php _e( 'Select source to get field options from', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="allowedSources"
						:value="field['repeater-fields'][ rFieldIndex ].options_source"
						@input="setRepeaterFieldProp( rFieldIndex, 'options_source', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':    [ 'checkbox', 'select', 'radio' ],
							}
						]"
					></cx-vui-select>
					<cx-vui-select
						label="<?php _e( 'Glossary', 'jet-engine' ); ?>"
						description="<?php _e( 'Select exact glossary to get options from', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="glossariesList"
						v-model="field['repeater-fields'][ rFieldIndex ].glossary_id"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':    [ 'checkbox', 'select', 'radio' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'glossary',
							}
						]"
					></cx-vui-select>
					<cx-vui-select
						label="<?php _e( 'Query', 'jet-engine' ); ?>"
						description="<?php _e( 'Select exact query to get options from', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="queriesList"
						v-model="field['repeater-fields'][ rFieldIndex ].query_id"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'checkbox', 'radio', 'select' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'query',
							},
						]"
					></cx-vui-select>
					<cx-vui-input
						label="<?php _e( 'Value Field', 'jet-engine' ); ?>"
						description="<?php _e( 'Query result field to use as option value. If empty, we`ll try to use most obvious value. For example ID for posts.', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						v-model="field['repeater-fields'][ rFieldIndex ].query_value_field"
						:conditions="getFilteredFieldConditions( [
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'checkbox', 'radio', 'select' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'query',
							},
						], 'query_value_field' )"
					></cx-vui-input>
					<cx-vui-input
						label="<?php _e( 'Label Field', 'jet-engine' ); ?>"
						description="<?php _e( 'Query result field to use as option label. If empty, we`ll try to use most obvious value. For example post_title for posts.', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						v-model="field['repeater-fields'][ rFieldIndex ].query_label_field"
						:conditions="getFilteredFieldConditions( [
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'checkbox', 'radio', 'select' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'query',
							},
						], 'query_label_field' )"
					></cx-vui-input>
					<cx-vui-textarea
						label="<?php _e( 'Bulk Options', 'jet-engine' ); ?>"
						description="<?php _e( 'One option per line. Allowed formats:<br>
							<i>value</i> - value and label will be the same<br>
							<i>value::label</i> - separate value and label<br>
							<i>value::label::checked</i> - separate value and label, checked by default', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:rows="Number(8)"
						v-model="field['repeater-fields'][ rFieldIndex ].bulk_options"
						:conditions="getFilteredFieldConditions( [
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'checkbox', 'radio', 'select' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'manual_bulk',
							},
						], 'bulk_options' )"
					></cx-vui-textarea>
					<cx-vui-component-wrapper
						:wrapper-css="[ 'fullwidth-control' ]"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':    [ 'checkbox', 'select', 'radio' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'manual',
							},
						]"
					>
						<div class="cx-vui-inner-panel">
							<jet-meta-field-options
								v-model="field['repeater-fields'][ rFieldIndex ].options"
								:field="field['repeater-fields'][ rFieldIndex ]"
							/>
						</div>
					</cx-vui-component-wrapper>
					<cx-vui-select
						label="<?php _e( 'Layout', 'jet-engine' ); ?>"
						description="<?php _e( 'Select layout orientation of inputs', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="[
							{
								value: 'vertical',
								label: '<?php _e( 'Vertical', 'jet-engine' ); ?>'
							},
							{
								value: 'horizontal',
								label: '<?php _e( 'Horizontal', 'jet-engine' ); ?>'
							},
						]"
						:value="field['repeater-fields'][ rFieldIndex ].check_radio_layout"
						@input="setRepeaterFieldProp( rFieldIndex, 'check_radio_layout', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':    [ 'checkbox', 'radio' ],
							},
						]"
					></cx-vui-select>
					<cx-vui-switcher
						label="<?php _e( 'Save as timestamp', 'jet-engine' ); ?>"
						description="<?php _e( 'If this option is enabled date will be saved in database Unix timestamp. Toggle it if you need to sort or query posts by date', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						:value="field['repeater-fields'][ rFieldIndex ].is_timestamp"
						@input="setRepeaterFieldProp( rFieldIndex, 'is_timestamp', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'date', 'datetime-local' ],
							}
						]"
					></cx-vui-switcher>
					<cx-vui-input
						label="<?php _e( 'Min value', 'jet-engine' ); ?>"
						description="<?php _e( 'Set a minimum value for a number field', 'jet-engine' ); ?>"
						type="number"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:value="field['repeater-fields'][ rFieldIndex ].min_value"
						@input="setRepeaterFieldProp( rFieldIndex, 'min_value', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'equal',
								'value':   'number',
							}
						]"
					></cx-vui-input>
					<cx-vui-input
						label="<?php _e( 'Max value', 'jet-engine' ); ?>"
						description="<?php _e( 'Set a maximum value for a number field', 'jet-engine' ); ?>"
						type="number"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:value="field['repeater-fields'][ rFieldIndex ].max_value"
						@input="setRepeaterFieldProp( rFieldIndex, 'max_value', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'equal',
								'value':   'number',
							}
						]"
					></cx-vui-input>
					<cx-vui-input
						label="<?php _e( 'Step value', 'jet-engine' ); ?>"
						description="<?php _e( 'Set a stepping interval for a number field', 'jet-engine' ); ?>"
						type="number"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:value="field['repeater-fields'][ rFieldIndex ].step_value"
						@input="setRepeaterFieldProp( rFieldIndex, 'step_value', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'equal',
								'value':   'number',
							}
						]"
					></cx-vui-input>
					<cx-vui-f-select
						label="<?php _e( 'Search in post types', 'jet-engine' ); ?>"
						description="<?php _e( 'Select post types available to search in', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						:options-list="postTypes"
						size="fullwidth"
						:multiple="true"
						:value="field['repeater-fields'][ rFieldIndex ].search_post_type"
						@input="setRepeaterFieldProp( rFieldIndex, 'search_post_type', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'equal',
								'value':   'posts',
							},
						]"
						v-model="field['repeater-fields'][ rFieldIndex ].search_post_type"
					></cx-vui-f-select>
					<cx-vui-input
						label="<?php _e( 'Placeholder', 'jet-engine' ); ?>"
						description="<?php _e( 'Placeholder text', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						v-model="field['repeater-fields'][ rFieldIndex ].placeholder"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'equal',
								'value':   'select',
							}
						]"
					></cx-vui-input>
					<cx-vui-switcher
						label="<?php _e( 'Multiple', 'jet-engine' ); ?>"
						description="<?php _e( 'Allow to select multiple values', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						:value="field['repeater-fields'][ rFieldIndex ].is_multiple"
						@input="setRepeaterFieldProp( rFieldIndex, 'is_multiple', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':    [ 'select', 'posts' ],
							},
						]"
					></cx-vui-switcher>
					<cx-vui-switcher
						label="<?php _e( 'Save as array', 'jet-engine' ); ?>"
						description="<?php _e( 'If this option is enabled checked values will be stored as plain PHP array. Use this option if this meta value will be edited from front-end form', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						:value="field['repeater-fields'][ rFieldIndex ].is_array"
						@input="setRepeaterFieldProp( rFieldIndex, 'is_array', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'equal',
								'value':   'checkbox',
							},
						]"
					></cx-vui-switcher>
					<cx-vui-select
						label="<?php _e( 'Value format', 'jet-engine' ); ?>"
						description="<?php _e( 'Set the format of the value will be stored in the database', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="[
							{
								value: 'id',
								label: '<?php _e( 'Media ID', 'jet-engine' ); ?>'
							},
							{
								value: 'url',
								label: '<?php _e( 'Media URL', 'jet-engine' ); ?>'
							},
							{
								value: 'both',
								label: '<?php _e( 'Array with media ID and URL', 'jet-engine' ); ?>'
							},
						]"
						:value="field['repeater-fields'][ rFieldIndex ].value_format"
						@input="setRepeaterFieldProp( rFieldIndex, 'value_format', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'media', 'gallery' ],
							}
						]"
					></cx-vui-select>
					<cx-vui-switcher
						label="<?php _e( 'Alpha mode', 'jet-engine' ); ?>"
						description="<?php _e( 'Toggle this option to enabled Alpha channel in Colorpicker', 'jet-engine' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						:value="field['repeater-fields'][ rFieldIndex ].alpha_mode"
						@input="setRepeaterFieldProp( rFieldIndex, 'alpha_mode', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'equal',
								'value':   'colorpicker',
							}
						]"
					></cx-vui-switcher>

					<?php do_action( 'jet-engine/meta-boxes/templates/fields/repeater/controls' ); ?>

				</cx-vui-repeater-item>
			</cx-vui-repeater>
		</div>
	</cx-vui-component-wrapper>

	<cx-vui-switcher
		label="<?php _e( 'Collapsed', 'jet-engine' ); ?>"
		description="<?php _e( 'Toggle this option to collapse repeater items on page load', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.repeater_collapsed"
		@input="setFieldProp( 'repeater_collapsed', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'repeater',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'repeater_collapsed' )"
	></cx-vui-switcher>
	<cx-vui-select
		label="<?php _e( 'Title Field', 'jet-engine' ); ?>"
		description="<?php _e( 'Select a repeater field to show as a repeater item title', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="getRepeaterTitleFields( index )"
		:value="field.repeater_title_field"
		@input="setFieldProp( 'repeater_title_field', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'repeater',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'repeater_title_field' )"
	></cx-vui-select>
	<cx-vui-switcher
		label="<?php _e( 'Alpha mode', 'jet-engine' ); ?>"
		description="<?php _e( 'Toggle this option to enabled Alpha channel in Colorpicker', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.alpha_mode"
		@input="setFieldProp( 'alpha_mode', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'colorpicker',
			}
		], 'alpha_mode' )"
	></cx-vui-switcher>
	<cx-vui-textarea
		label="<?php _e( 'Description', 'jet-engine' ); ?>"
		description="<?php _e( 'Meta field description to be shown on Post edit page', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.description"
		@input="setFieldProp( 'description', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':    field.type,
				'compare': 'not_equal',
				'value':    'html',
			},
		], 'description' )"
	></cx-vui-textarea>
	<cx-vui-textarea
		label="<?php _e( 'HTML Code', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.html"
		@input="setFieldProp( 'html', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':    field.type,
				'compare': 'equal',
				'value':    'html',
			},
		], 'html' )"
	></cx-vui-textarea>
	<cx-vui-input
		label="<?php _e( 'CSS Classes', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.html_css_class"
		@input="setFieldProp( 'html_css_class', $event )"
		@on-input-change="preSetFieldName()"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':    field.type,
				'compare': 'equal',
				'value':    'html',
			},
		], 'html_css_class' )"
	></cx-vui-input>
	<cx-vui-select
		label="<?php _e( 'Field width', 'jet-engine' ); ?>"
		description="<?php _e( 'Select meta field width from the dropdown list for Post edit page', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: '100%',
				label: '100%',
			},
			{
				value: '75%',
				label: '75%',
			},
			{
				value: '66.66666%',
				label: '66.6%',
			},
			{
				value: '50%',
				label: '50%',
			},
			{
				value: '33.33333%',
				label: '33.3%',
			},
			{
				value: '25%',
				label: '25%',
			},
		]"
		:value="field.width"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    'width',
				'compare': 'not_in',
				'value':    disabledFields,
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'width' )"
		@input="setFieldProp( 'width', $event )"
	></cx-vui-select>
	<cx-vui-input
		label="<?php _e( 'Character limit', 'jet-engine' ); ?>"
		description="<?php _e( 'Max field value length. Leave empty for no limit', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.max_length"
		@input="setFieldProp( 'max_length', $event )"
		@on-input-change="preSetFieldName( index )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'in',
				'value':    [ 'text', 'textarea' ],
			},
			{
				'input':    'max_length',
				'compare': 'not_in',
				'value':    disabledFields,
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'max_length' )"
	></cx-vui-input>
	<cx-vui-input
		label="<?php _e( 'Default value', 'jet-engine' ); ?>"
		description="<?php _e( 'Set default value for current field. <b>Note</b> For date field you can set any value could be processed by strtotime() function.', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.default_val"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'in',
				'value':    [ 'text', 'date', 'textarea', 'iconpicker', 'wysiwyg', 'number', 'colorpicker' ],
			},
			{
				'input':    'default_val',
				'compare': 'not_in',
				'value':    disabledFields,
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'default_val' )"
		@input="setFieldProp( 'default_val', $event )"
	></cx-vui-input>
	<cx-vui-switcher
		label="<?php _e( 'Is required', 'jet-engine' ); ?>"
		description="<?php _e( 'Toggle this option to make this field as required one', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.is_required"
		@input="setFieldProp( 'is_required', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'not_in',
				'value':    [ 'switcher', 'html' ],
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'is_required' )"
	></cx-vui-switcher>
	<template
		v-if="( 'checkbox' !== field.type ) || ( 'checkbox' === field.type && field.is_array )"
	>
		<cx-vui-switcher
			label="<?php _e( 'Quick edit support', 'jet-engine' ); ?>"
			description="<?php _e( 'Toggle this option to make this field available in the Quick Edit section', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:value="field.quick_editable"
			@input="setFieldProp( 'quick_editable', $event )"
			:conditions="getFilteredFieldConditions( [
				{
					'input':    field.type,
					'compare': 'in',
					'value':    quickEditSupports,
				},
				{
					'input':    'quick_editable',
					'compare': 'not_in',
					'value':    disabledFields,
				},
				{
					'input':    field.object_type,
					'compare': 'equal',
					'value':   'field',
				},
				{
					'input':   'quick_editable',
					'compare': 'not_in',
					'value':   hideOptions,
				}
			], 'quick_editable' )"
		></cx-vui-switcher>
	</template>
	<cx-vui-switcher
		label="<?php _e( 'Revision support', 'jet-engine' ); ?>"
		description="<?php _e( 'Toggle this option to make this field available in the Revisions', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.revision_support"
		@input="setFieldProp( 'revision_support', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   'revision_support',
				'compare': 'not_in',
				'value':   disabledFields,
			},
			{
				'input':   'revision_support',
				'compare': 'not_in',
				'value':   hideOptions,
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.type,
				'compare': 'not_equal',
				'value':   'html',
			},
		], 'revision_support' )"
	></cx-vui-switcher>

	<?php do_action( 'jet-engine/meta-boxes/templates/fields/controls' ); ?>

	<cx-vui-switcher
		label="<?php _e( 'Show in Rest API', 'jet-engine' ); ?>"
		description="<?php _e( 'Allow to get/update this field with WordPress Rest API', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.show_in_rest"
		@input="setFieldProp( 'show_in_rest', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    'show_in_rest',
				'compare': 'not_in',
				'value':    disabledFields,
			},
			{
				'input':   'show_in_rest',
				'compare': 'not_in',
				'value':   hideOptions,
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.type,
				'compare': 'not_equal',
				'value':   'html',
			},
		], 'show_in_rest' )"
	></cx-vui-switcher>
	<cx-vui-component-wrapper
		label="<?php _e( 'Conditional Logic', 'jet-engine' ); ?>"
		description="<?php _e( 'Click on button to set meta field display rules.', 'jet-engine' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		v-if="showCondition( field )"
	>
		<cx-vui-button
			size="mini"
			button-style="accent-border"
			@click="showConditionPopup()"
		>
			<span
				slot="label"
				v-html="hasConditions( field ) ? '<?php _e( 'Edit conditional rules', 'jet-engine' ); ?>' : '<?php _e( 'Set up conditional rules', 'jet-engine' ); ?>'"
			></span>
		</cx-vui-button>
	</cx-vui-component-wrapper>

</div>