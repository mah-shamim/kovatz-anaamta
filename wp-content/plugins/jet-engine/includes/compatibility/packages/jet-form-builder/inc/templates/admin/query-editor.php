<?php
/**
 * WC product query component template
 */
?>
<div class="jet-engine-edit-page__fields">
	<div class="cx-vui-collapse__heading">
		<h3 class="cx-vui-subtitle"><?php _e( 'JetFormBuilder Records Query', 'jet-engine' ); ?></h3>
	</div>
	<div class="cx-vui-panel">
		<cx-vui-f-select
			label="<?php _e( 'Form', 'jet-engine' ); ?>"
			description="<?php _e( 'Query records only for selected form(s).', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="formsList"
			size="fullwidth"
			:multiple="true"
			name="form_id"
			v-model="query.form_id"
		></cx-vui-f-select>
		<cx-vui-input
			label="<?php _e( 'User', 'jet-engine' ); ?>"
			description="<?php _e( 'Query records only for given user. Please set user ID, you can find it in the address bar on user profile page.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'has-macros' ]"
			size="fullwidth"
			name="user_id"
			v-model="query.user_id"
		>
			<jet-query-dynamic-args v-model="dynamicQuery.user_id"></jet-query-dynamic-args>
		</cx-vui-input>
		<cx-vui-select
			label="<?php _e( 'Status', 'jet-engine' ); ?>"
			description="<?php _e( 'Select records with selected status of form submission', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="[
				{
					value: '',
					label: 'Any',
				},
				{
					value: 'success',
					label: 'Success',
				},
				{
					value: 'failed',
					label: 'Failed',
				},
			]"
			size="fullwidth"
			v-model="query.status"
		></cx-vui-select>
		<cx-vui-select
			label="<?php _e( 'Date Query', 'jet-engine' ); ?>"
			description="<?php _e( 'Select records limited by form submission dates', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="[
				{
					value: '',
					label: 'Select...',
				},
				{
					value: 'before',
					label: 'Before Date',
				},
				{
					value: 'after',
					label: 'After Date',
				},
				{
					value: 'between',
					label: 'Between Dates',
				},
			]"
			size="fullwidth"
			v-model="query.date_query"
		></cx-vui-select>
		
		<cx-vui-input
			label="<?php _e( 'From Date', 'jet-engine' ); ?>"
			description="<?php _e( 'Set date to get records after. You can use exact date or human-readable string like today, today - 7 days etc.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'has-macros' ]"
			:conditions="[
				{
					input: query.date_query,
					compare: 'in',
					value: [ 'after', 'between' ],
				}
			]"
			size="fullwidth"
			name="query_date_from"
			v-model="query.date_from"
		>
			<jet-query-dynamic-args v-model="dynamicQuery.date_from"></jet-query-dynamic-args>
		</cx-vui-input>
		<cx-vui-input
			label="<?php _e( 'Date To', 'jet-engine' ); ?>"
			description="<?php _e( 'Set date to get records before. You can use exact date or human-readable string like today, today - 7 days etc.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'has-macros' ]"
			size="fullwidth"
			:conditions="[
				{
					input: query.date_query,
					compare: 'in',
					value: [ 'before', 'between' ],
				}
			]"
			name="query_date_to"
			v-model="query.date_to"
		>
			<jet-query-dynamic-args v-model="dynamicQuery.date_from"></jet-query-dynamic-args>
		</cx-vui-input>
		
		<cx-vui-component-wrapper
			:wrapper-css="[ 'fullwidth-control' ]"
		>
			<div class="cx-vui-inner-panel query-panel">
				<div class="cx-vui-component__label"><?php _e( 'Query by Fields Values', 'jet-engine' ); ?></div>
				<cx-vui-repeater
					button-label="<?php _e( 'Add new', 'jet-engine' ); ?>"
					button-style="accent"
					button-size="mini"
					v-model="query.meta_query"
					@add-new-item="addNewField( $event, [], query.meta_query, newDynamicMeta )"
				>
					<cx-vui-repeater-item
						v-for="( clause, index ) in query.meta_query"
						:collapsed="isCollapsed( clause )"
						:index="index"
						@clone-item="cloneField( $event, clause._id, query.meta_query, newDynamicMeta )"
						@delete-item="deleteField( $event, clause._id, query.meta_query, deleteDynamicMeta )"
						:key="clause._id"
					>
						<cx-vui-input
							label="<?php _e( 'Field name', 'jet-engine' ); ?>"
							description="<?php _e( 'Form field name to compare with', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							size="fullwidth"
							:value="query.meta_query[ index ].key"
							@input="setFieldProp( clause._id, 'key', $event, query.meta_query )"
						></cx-vui-input>
						<cx-vui-select
							label="<?php _e( 'Compare', 'jet-engine' ); ?>"
							description="<?php _e( 'Operator to test', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:options-list="operators"
							size="fullwidth"
							:value="query.meta_query[ index ].compare"
							@input="setFieldProp( clause._id, 'compare', $event, query.meta_query )"
						></cx-vui-select>
						<cx-vui-input
							label="<?php _e( 'Value', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth', 'has-macros' ]"
							size="fullwidth"
							:value="query.meta_query[ index ].value"
							@input="setFieldProp( clause._id, 'value', $event, query.meta_query )"
						><jet-query-dynamic-args v-model="dynamicQuery.meta_query[ clause._id ].value"></jet-query-dynamic-args></cx-vui-input>
						<cx-vui-select
							label="<?php _e( 'Type', 'jet-engine' ); ?>"
							description="<?php _e( 'Data type stored in the given field', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:options-list="dataTypes"
							size="fullwidth"
							:value="query.meta_query[ index ].type"
							@input="setFieldProp( clause._id, 'type', $event, query.meta_query )"
						></cx-vui-select>
					</cx-vui-repeater-item>
				</cx-vui-repeater>
			</div>
		</cx-vui-component-wrapper>
		<cx-vui-select
			v-if="1 < query.meta_query.length"
			label="<?php _e( 'Fields Query Relation', 'jet-engine' ); ?>"
			description="<?php _e( 'The logical relationship between fields query clauses', 'jet-engine' ); ?>"
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
			v-model="query.meta_query_relation"
		></cx-vui-select>
		<cx-vui-input
			label="<?php _e( 'Show/Per Page Limit', 'jet-engine' ); ?>"
			description="<?php _e( 'If using with JetSmartFilters pagination - its number of returned items per page. If without pagination - its number of visible items in the listing grid. Query counter will ignore this option and count all items matched to query.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'has-macros' ]"
			size="fullwidth"
			v-model="query.limit_per_page"
		><jet-query-dynamic-args v-model="dynamicQuery.limit_per_page"></jet-query-dynamic-args></cx-vui-input>
		<cx-vui-input
			label="<?php _e( 'Offset', 'jet-engine' ); ?>"
			description="<?php _e( 'Number of items to skip from start. <b>Note:</b> This option doesn`t work without <b>Limit</b>. If you need unlimited query results with offset, please set some extra large number into Limit option.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'has-macros' ]"
			size="fullwidth"
			v-model="query.offset"
		><jet-query-dynamic-args v-model="dynamicQuery.offset"></jet-query-dynamic-args></cx-vui-input>
	</div>
</div>
