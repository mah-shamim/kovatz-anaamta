<?php
/**
 * Posts query component template
 */
?>
<div class="jet-engine-edit-page__fields">
	<div class="cx-vui-collapse__heading">
		<h3 class="cx-vui-subtitle"><?php _e( 'Users Query', 'jet-engine' ); ?></h3>
	</div>
	<div class="cx-vui-panel">
		<cx-vui-tabs
			:in-panel="false"
			value="role"
			layout="vertical"
		>
			<cx-vui-tabs-panel
				name="role"
				:label="isInUseMark( [ 'role', 'role__in', 'role__not_in' ] ) + '<?php _e( 'User Role', 'jet-engine' ); ?>'"
				key="role"
			>
				<cx-vui-f-select
					label="<?php _e( 'Role', 'jet-engine' ); ?>"
					description="<?php _e( 'A list of role names that users must match to be included in results. Note that this is an inclusive list: users must match *each* role.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="rolesList"
					:multiple="true"
					size="fullwidth"
					name="query_role"
					v-model="query.role"
				></cx-vui-f-select>
				<cx-vui-f-select
					label="<?php _e( 'Role In', 'jet-engine' ); ?>"
					description="<?php _e( 'A list of role names. Matched users must have at least one of these roles.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="rolesList"
					:multiple="true"
					size="fullwidth"
					name="query_role__in"
					v-model="query.role__in"
				></cx-vui-f-select>
				<cx-vui-f-select
					label="<?php _e( 'Role Not In', 'jet-engine' ); ?>"
					description="<?php _e( 'A list of role names to exclude. Users matching one or more of these roles will not be included in results.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="rolesList"
					:multiple="true"
					size="fullwidth"
					name="query_role__not_in"
					v-model="query.role__not_in"
				></cx-vui-f-select>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="include_exclude"
				:label="isInUseMark( [ 'include', 'exclude' ] ) + '<?php _e( 'Include/Exclude', 'jet-engine' ); ?>'"
				key="include_exclude"
			>
				<cx-vui-input
					label="<?php _e( 'Include', 'jet-engine' ); ?>"
					description="<?php _e( 'User ID or comma-seaparated user IDs list', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_include"
					v-model="query.include"
				><jet-query-dynamic-args v-model="dynamicQuery.include"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Exclude', 'jet-engine' ); ?>"
					description="<?php _e( 'User ID or comma-seaparated user IDs list', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_exclude"
					v-model="query.exclude"
				><jet-query-dynamic-args v-model="dynamicQuery.exclude"></jet-query-dynamic-args></cx-vui-input>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="misc"
				:label="isInUseMark( [ 'search', 'search_columns', 'number', 'offset', 'paged', 'orderby' ] ) + '<?php _e( 'Misc', 'jet-engine' ); ?>'"
				key="misc"
			>
				<cx-vui-input
					label="<?php _e( 'Search', 'jet-engine' ); ?>"
					description="<?php _e( 'Searches for possible string matches on columns. Use of the * wildcard before and/or after the string will match on columns starting with*, *ending with, or *containing* the string you enter', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_search"
					v-model="query.search"
				><jet-query-dynamic-args v-model="dynamicQuery.search"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-f-select
					label="<?php _e( 'Search Columns', 'jet-engine' ); ?>"
					description="<?php _e( 'List of database table columns to matches the search string across multiple columns', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="[
						{
							value: 'ID',
							label: 'User ID',
						},
						{
							value: 'user_login',
							label: 'Login',
						},
						{
							value: 'user_nicename',
							label: 'Nicename',
						},
						{
							value: 'user_email',
							label: 'Email',
						},
						{
							value: 'user_url',
							label: 'User URL',
						},
					]"
					size="fullwidth"
					:multiple="true"
					name="query_search_columns"
					v-model="query.search_columns"
				></cx-vui-f-select>
				<cx-vui-input
					label="<?php _e( 'Number', 'jet-engine' ); ?>"
					description="<?php _e( 'The maximum returned number of results', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_number"
					v-model="query.number"
				><jet-query-dynamic-args v-model="dynamicQuery.number"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Offset', 'jet-engine' ); ?>"
					description="<?php _e( 'Offset the returned results. Warning: Setting the offset parameter overrides/ignores the paged parameter and breaks pagination', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_offset"
					v-model="query.offset"
				><jet-query-dynamic-args v-model="dynamicQuery.search"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Paged', 'jet-engine' ); ?>"
					description="<?php _e( 'When used with number, defines the page of results to return', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_paged"
					v-model="query.paged"
				><jet-query-dynamic-args v-model="dynamicQuery.paged"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-select
					label="<?php _e( 'Order By', 'jet-engine' ); ?>"
					description="<?php _e( 'Field to order terms by', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="orderbyOptions"
					size="fullwidth"
					name="query_orderby"
					v-model="query.orderby"
				></cx-vui-select>
				<cx-vui-input
					label="<?php _e( 'Meta key', 'jet-engine' ); ?>"
					description="<?php _e( 'Required. Meta field key/name to order by', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					:conditions="[
						{
							input: query.orderby,
							compare: 'in',
							value: [ 'meta_value', 'meta_value_num' ]
						}
					]"
					name="query_meta_key"
					v-model="query.meta_key"
				><jet-query-dynamic-args v-model="dynamicQuery.meta_key"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-select
					label="<?php _e( 'Order', 'jet-engine' ); ?>"
					description="<?php _e( 'Designates the ascending or descending order of the `Order By` parameter', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="[
						{
							value: 'ASC',
							label: 'From lowest to highest values (1, 2, 3; a, b, c)',
						},
						{
							value: 'DESC',
							label: 'From highest to lowest values (3, 2, 1; c, b, a)',
						},
					]"
					:conditions="[
						{
							input: query.orderby,
							compare: 'not_in',
							value: [ 'include' ],
						}
					]"
					size="fullwidth"
					name="query_order"
					v-model="query.order"
				></cx-vui-select>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="meta_query"
				:label="isInUseMark( [ 'meta_query' ] ) + '<?php _e( 'Meta Query', 'jet-engine' ); ?>'"
				key="meta_query"
			>
				<cx-vui-component-wrapper
					:wrapper-css="[ 'query-fullwidth' ]"
				>
					<div class="cx-vui-inner-panel query-panel">
						<div class="cx-vui-component__label"><?php _e( 'Meta Query Clauses', 'jet-engine' ); ?></div>
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
									label="<?php _e( 'Field key/name', 'jet-engine' ); ?>"
									description="<?php _e( 'You can use `JetEngine meta field` macros to get name of the field created by JetEngine', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth', 'has-macros' ]"
									size="fullwidth"
									:value="query.meta_query[ index ].key"
									@input="setFieldProp( clause._id, 'key', $event, query.meta_query )"
								><jet-query-dynamic-args v-model="dynamicQuery.meta_query[ clause._id ].key"></jet-query-dynamic-args></cx-vui-input>
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
					label="<?php _e( 'Relation', 'jet-engine' ); ?>"
					description="<?php _e( 'The logical relationship between meta query clauses', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="[
						{
							value: 'and',
							label: '<?php _e( 'And', 'jet-engine' ); ?>',
						},
						{
							value: 'or',
							label: '<?php _e( 'Or', 'jet-engine' ); ?>',
						},
					]"
					size="fullwidth"
					v-model="query.meta_query_relation"
				></cx-vui-select>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="date_query"
				:label="isInUseMark( [ 'date_query' ] ) + '<?php _e( 'Date Query', 'jet-engine' ); ?>'"
				key="date_query"
			>
				<cx-vui-component-wrapper
					:wrapper-css="[ 'query-fullwidth' ]"
				>
					<div class="cx-vui-inner-panel query-panel">
						<div class="cx-vui-component__label"><?php _e( 'Date Query Clauses', 'jet-engine' ); ?></div>
						<cx-vui-repeater
							button-label="<?php _e( 'Add new', 'jet-engine' ); ?>"
							button-style="accent"
							button-size="mini"
							v-model="query.date_query"
							@add-new-item="addNewField( $event, [], query.date_query, newDynamicDate )"
						>
							<cx-vui-repeater-item
								v-for="( dateClause, index ) in query.date_query"
								:collapsed="isCollapsed( dateClause )"
								:index="index"
								@clone-item="cloneField( $event, dateClause._id, query.date_query, newDynamicDate )"
								@delete-item="deleteField( $event, dateClause._id, query.date_query, deleteDynamicDate )"
								:key="dateClause._id"
							>
								<cx-vui-input
									label="<?php _e( 'Year', 'jet-engine' ); ?>"
									description="<?php _e( '4 digit year (e.g. 2011)', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth', 'has-macros' ]"
									size="fullwidth"
									:value="query.date_query[ index ].year"
									@input="setFieldProp( dateClause._id, 'year', $event, query.date_query )"
								><jet-query-dynamic-args v-model="dynamicQuery.date_query[ dateClause._id ].year"></jet-query-dynamic-args></cx-vui-input>
								<cx-vui-input
									label="<?php _e( 'Month', 'jet-engine' ); ?>"
									description="<?php _e( 'Month number (from 1 to 12)', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth', 'has-macros' ]"
									size="fullwidth"
									:value="query.date_query[ index ].month"
									@input="setFieldProp( dateClause._id, 'month', $event, query.date_query )"
								><jet-query-dynamic-args v-model="dynamicQuery.date_query[ dateClause._id ].month"></jet-query-dynamic-args></cx-vui-input>
								<cx-vui-input
									label="<?php _e( 'Day', 'jet-engine' ); ?>"
									description="<?php _e( 'Day of the month (from 1 to 31)', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth', 'has-macros' ]"
									size="fullwidth"
									:value="query.date_query[ index ].day"
									@input="setFieldProp( dateClause._id, 'day', $event, query.date_query )"
								><jet-query-dynamic-args v-model="dynamicQuery.date_query[ dateClause._id ].day"></jet-query-dynamic-args></cx-vui-input>
								<cx-vui-input
									label="<?php _e( 'After', 'jet-engine' ); ?>"
									description="<?php _e( 'Date to retrieve posts after. Eg. January 1st 2020, Today, Tomorrow etc.', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth', 'has-macros' ]"
									size="fullwidth"
									:value="query.date_query[ index ].after"
									@input="setFieldProp( dateClause._id, 'after', $event, query.date_query )"
								><jet-query-dynamic-args v-model="dynamicQuery.date_query[ dateClause._id ].after"></jet-query-dynamic-args></cx-vui-input>
								<cx-vui-input
									label="<?php _e( 'Before', 'jet-engine' ); ?>"
									description="<?php _e( 'Date to retrieve posts before. Eg. January 1st 2020, Today, Tomorrow etc.', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth', 'has-macros' ]"
									size="fullwidth"
									:value="query.date_query[ index ].before"
									@input="setFieldProp( dateClause._id, 'before', $event, query.date_query )"
								><jet-query-dynamic-args v-model="dynamicQuery.date_query[ dateClause._id ].before"></jet-query-dynamic-args></cx-vui-input>
								<cx-vui-switcher
									label="<?php _e( 'Inclusive', 'jet-engine' ); ?>"
									description="<?php _e( 'For after/before, whether exact value should be matched or not.', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:value="query.date_query[ index ].inclusive"
									@input="setFieldProp( dateClause._id, 'inclusive', $event, query.date_query )"
								></cx-vui-switcher>
								<cx-vui-select
									label="<?php _e( 'Compare', 'jet-engine' ); ?>"
									description="<?php _e( 'The search operator', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:options-list="dateOperators"
									size="fullwidth"
									:value="query.date_query[ index ].compare"
									@input="setFieldProp( dateClause._id, 'compare', $event, query.date_query )"
								></cx-vui-select>
								<cx-vui-select
									label="<?php _e( 'Column', 'jet-engine' ); ?>"
									description="<?php _e( 'Posts column to query against', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:options-list="[
										{
											value: 'user_registered',
											label: 'User Registered',
										},
										{
											value: 'registered',
											label: 'Registered',
										},
									]"
									size="fullwidth"
									:value="query.date_query[ index ].column"
									@input="setFieldProp( dateClause._id, 'column', $event, query.date_query )"
								></cx-vui-select>
							</cx-vui-repeater-item>
						</cx-vui-repeater>
					</div>
				</cx-vui-component-wrapper>
				<cx-vui-select
					v-if="1 < query.date_query.length"
					label="<?php _e( 'Relation', 'jet-engine' ); ?>"
					description="<?php _e( 'The logical relationship between date query clauses', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="[
						{
							value: 'and',
							label: '<?php _e( 'And', 'jet-engine' ); ?>',
						},
						{
							value: 'or',
							label: '<?php _e( 'Or', 'jet-engine' ); ?>',
						},
					]"
					size="fullwidth"
					v-model="query.date_query_relation"
				></cx-vui-select>
			</cx-vui-tabs-panel>
			<?php do_action( 'jet-engine/query-builder/users/controls' ); ?>
		</cx-vui-tabs>
	</div>
</div>
