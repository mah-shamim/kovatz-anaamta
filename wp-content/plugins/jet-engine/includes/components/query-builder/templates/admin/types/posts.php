<?php
/**
 * Posts query component template
 */
?>
<div class="jet-engine-edit-page__fields">
	<div class="cx-vui-collapse__heading">
		<h3 class="cx-vui-subtitle"><?php _e( 'Posts Query', 'jet-engine' ); ?></h3>
	</div>
	<div class="cx-vui-panel">
		<cx-vui-tabs
			:in-panel="false"
			value="general"
			layout="vertical"
		>
			<cx-vui-tabs-panel
				name="general"
				:label="isInUseMark( [ 'post_type', 'post_status', 's', 'orderby', 'has_password', 'post_password' ] ) + '<?php _e( 'General', 'jet-engine' ); ?>'"
				key="general"
			>
				<cx-vui-f-select
					label="<?php _e( 'Post Type', 'jet-engine' ); ?>"
					name="query_post_type"
					description="<?php _e( 'Set post type slug. Leave empty to retrieve posts of any type', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="postTypes"
					size="fullwidth"
					:multiple="true"
					v-model="query.post_type"
				></cx-vui-f-select>
				<cx-vui-f-select
					label="<?php _e( 'Post Status', 'jet-engine' ); ?>"
					description="<?php _e( 'Use post status', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="postStatuses"
					size="fullwidth"
					:multiple="true"
					name="query_post_status"
					v-model="query.post_status"
				></cx-vui-f-select>
				<cx-vui-input
					label="<?php _e( 'Search Keyword', 'jet-engine' ); ?>"
					description="<?php _e( 'Prepending a term with a hyphen will exclude posts matching that term. Eg, `pillow -sofa` will return posts containing `pillow` but not `sofa`', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_s"
					v-model="query.s"
				><jet-query-dynamic-args v-model="dynamicQuery.s"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-switcher
					label="<?php _e( 'Sentence Search', 'jet-engine' ); ?>"
					description="<?php _e( 'Whether to search by phrase.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					name="query_sentence"
					v-model="query.sentence"
					:conditions="[
						{
							operator: 'OR',
						},
						{
							input: query.s,
							compare: 'not_in',
							value: [ '', undefined ],
						},
						{
							input: dynamicQuery.s,
							compare: 'not_in',
							value: [ '', undefined ],
						}
					]"
				></cx-vui-switcher>
				<cx-vui-component-wrapper
					:wrapper-css="[ 'query-fullwidth' ]"
				>
					<div class="cx-vui-inner-panel query-panel">
						<div class="cx-vui-component__label"><?php _e( 'Order & Order By', 'jet-engine' ); ?></div>
						<cx-vui-repeater
							button-label="<?php _e( 'Add new sorting parameter', 'jet-engine' ); ?>"
							button-style="accent"
							button-size="mini"
							v-model="query.orderby"
							@add-new-item="addNewField( $event, [], query.orderby )"
						>
							<cx-vui-repeater-item
								v-for="( order, index ) in query.orderby"
								:title="query.orderby[ index ].orderby"
								:subtitle="query.orderby[ index ].order"
								:collapsed="isCollapsed( order )"
								:index="index"
								@clone-item="cloneField( $event, order._id, query.orderby )"
								@delete-item="deleteField( $event, order._id, query.orderby )"
								:key="order._id"
							>
								<cx-vui-select
									label="<?php _e( 'Order By', 'jet-engine' ); ?>"
									description="<?php _e( 'Sort retrieved posts by selected parameter', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:options-list="orderbyOptions"
									size="fullwidth"
									:value="query.orderby[ index ].orderby"
									@input="setFieldProp( order._id, 'orderby', $event, query.orderby )"
								></cx-vui-select>
								<cx-vui-input
									label="<?php _e( 'Meta key', 'jet-engine' ); ?>"
									description="<?php _e( 'Meta field name to order by', 'jet-engine' ); ?>"
									v-if="'meta_value' === query.orderby[ index ].orderby || 'meta_value_num' === query.orderby[ index ].orderby"
									:wrapper-css="[ 'equalwidth' ]"
									size="fullwidth"
									:value="query.orderby[ index ].meta_key"
									@input="setFieldProp( order._id, 'meta_key', $event, query.orderby )"
								></cx-vui-input>
								<cx-vui-select
									v-if="'meta_clause' === query.orderby[ index ].orderby && metaClauses.length"
									label="<?php _e( 'Meta Clause', 'jet-engine' ); ?>"
									description="<?php _e( 'Select meta clause to order by.', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:options-list="metaClauses"
									size="fullwidth"
									:value="query.orderby[ index ].order_meta_clause"
									@input="setFieldProp( order._id, 'order_meta_clause', $event, query.orderby )"
								></cx-vui-select>
								<cx-vui-component-wrapper
									v-if="'meta_clause' === query.orderby[ index ].orderby && ! metaClauses.length"
									label="<?php _e( 'Warning', 'jet-engine' ); ?>"
									description="<?php _e( 'You have not created any meta clauses yet. You can do this at the Meta Query tab. Note that Clause name option is required for the meta query if you want to use it for ordering', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
								></cx-vui-component-wrapper>
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
									size="fullwidth"
									:value="query.orderby[ index ].order"
									@input="setFieldProp( order._id, 'order', $event, query.orderby )"
								></cx-vui-select>
							</cx-vui-repeater-item>
						</cx-vui-repeater>
					</div>
				</cx-vui-component-wrapper>
				<cx-vui-switcher
					label="<?php _e( 'Has Password', 'jet-engine' ); ?>"
					description="<?php _e( 'Enable to get only posts with passwords. Disable to get posts without passwords.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					name="query_has_password"
					v-model="query.has_password"
				></cx-vui-switcher>
				<cx-vui-input
					label="<?php _e( 'Post Password', 'jet-engine' ); ?>"
					description="<?php _e( 'Show posts with a particular password', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_post_password"
					v-model="query.post_password"
				><jet-query-dynamic-args v-model="dynamicQuery.post_password"></jet-query-dynamic-args></cx-vui-input>
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
								<cx-vui-input
									label="<?php _e( 'Clause name', 'jet-engine' ); ?>"
									description="<?php _e( 'Set current clause name to used as Order By parameter', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth', 'has-macros' ]"
									size="fullwidth"
									:value="query.meta_query[ index ].clause_name"
									@input="setFieldProp( clause._id, 'clause_name', $event, query.meta_query )"
								></cx-vui-input>
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
				name="tax_query"
				:label="isInUseMark( [ 'tax_query' ] ) + '<?php _e( 'Tax Query', 'jet-engine' ); ?>'"
				key="tax_query"
			>
				<cx-vui-component-wrapper
					:wrapper-css="[ 'query-fullwidth' ]"
				>
					<div class="cx-vui-inner-panel query-panel">
						<div class="cx-vui-component__label"><?php _e( 'Tax Query Clauses', 'jet-engine' ); ?></div>
						<cx-vui-repeater
							button-label="<?php _e( 'Add new', 'jet-engine' ); ?>"
							button-style="accent"
							button-size="mini"
							v-model="query.meta_query"
							@add-new-item="addNewField( $event, [], query.tax_query, newDynamicTax )"
						>
							<cx-vui-repeater-item
								v-for="( taxClause, index ) in query.tax_query"
								:collapsed="isCollapsed( taxClause )"
								:index="index"
								@clone-item="cloneField( $event, taxClause._id, query.tax_query, newDynamicTax )"
								@delete-item="deleteField( $event, taxClause._id, query.tax_query, deleteDynamicTax )"
								:key="taxClause._id"
							>
								<cx-vui-select
									label="<?php _e( 'Taxonomy', 'jet-engine' ); ?>"
									description="<?php _e( 'Select taxonomy to get posts from', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:options-list="taxonomies"
									size="fullwidth"
									:value="query.tax_query[ index ].taxonomy"
									@input="setFieldProp( taxClause._id, 'taxonomy', $event, query.tax_query )"
								></cx-vui-select>
								<cx-vui-select
									label="<?php _e( 'Field', 'jet-engine' ); ?>"
									description="<?php _e( 'Select taxonomy term by. Default value is `Term ID`.', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:options-list="[
										{
											value: 'term_id',
											label: '<?php _e( 'Term ID', 'jet-engine' ); ?>',
										},
										{
											value: 'name',
											label: '<?php _e( 'Name', 'jet-engine' ); ?>',
										},
										{
											value: 'slug',
											label: '<?php _e( 'Slug', 'jet-engine' ); ?>',
										},
										{
											value: 'term_taxonomy_id',
											label: '<?php _e( 'Term taxonomy ID', 'jet-engine' ); ?>',
										},
									]"
									size="fullwidth"
									:value="query.tax_query[ index ].field"
									@input="setFieldProp( taxClause._id, 'field', $event, query.tax_query )"
								></cx-vui-select>
								<cx-vui-input
									label="<?php _e( 'Terms', 'jet-engine' ); ?>"
									description="<?php _e( 'Taxonomy term(s) to get posts by.', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth', 'has-macros' ]"
									size="fullwidth"
									:value="query.tax_query[ index ].terms"
									@input="setFieldProp( taxClause._id, 'terms', $event, query.tax_query )"
								><jet-query-dynamic-args v-model="dynamicQuery.tax_query[ taxClause._id ].terms"></jet-query-dynamic-args></cx-vui-input>
								<cx-vui-switcher
									label="<?php _e( 'Exclude children', 'jet-engine' ); ?>"
									description="<?php _e( 'By default children for hierarchical taxonomies will be included into query results. Enable this option to exclude children terms.', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:value="query.tax_query[ index ].exclude_children"
									@input="setFieldProp( taxClause._id, 'exclude_children', $event, query.tax_query )"
								></cx-vui-switcher>
								<cx-vui-select
									label="<?php _e( 'Compare operator', 'jet-engine' ); ?>"
									description="<?php _e( 'Operator to test terms against.', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:options-list="[
										{
											value: 'IN',
											label: 'IN',
										},
										{
											value: 'NOT IN',
											label: 'NOT IN',
										},
										{
											value: 'AND',
											label: 'AND',
										},
										{
											value: 'EXISTS',
											label: 'EXISTS',
										},
										{
											value: 'NOT EXISTS',
											label: 'NOT EXISTS',
										}
									]"
									size="fullwidth"
									:value="query.tax_query[ index ].operator"
									@input="setFieldProp( taxClause._id, 'operator', $event, query.tax_query )"
								></cx-vui-select>
							</cx-vui-repeater-item>
						</cx-vui-repeater>
					</div>
				</cx-vui-component-wrapper>
				<cx-vui-select
					v-if="1 < query.tax_query.length"
					label="<?php _e( 'Relation', 'jet-engine' ); ?>"
					description="<?php _e( 'The logical relationship between tax query clauses', 'jet-engine' ); ?>"
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
					v-model="query.tax_query_relation"
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
											value: 'post_date',
											label: 'Post date',
										},
										{
											value: 'post_date_gmt',
											label: 'Post date GMT',
										},
										{
											value: 'post_modified',
											label: 'Post modified',
										},
										{
											value: 'post_modified_gmt',
											label: 'Post modified GMT',
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
			<cx-vui-tabs-panel
				name="post_page"
				:label="isInUseMark( [ 'post__in', 'post__not_in', 'post_name__in', 'post_parent', 'post_parent__in', 'post_parent__not_in', 'p', 'name', 'page_id', 'pagename' ] ) + '<?php _e( 'Post & Page', 'jet-engine' ); ?>'"
				key="category_tag"
			>
				<cx-vui-input
					label="<?php _e( 'Post In', 'jet-engine' ); ?>"
					description="<?php _e( 'Use post ids. Specify posts to retrieve. ATTENTION If you use sticky posts, they will be included (prepended!) in the posts you retrieve whether you want it or not. To suppress this behaviour enbale `Ignore Sticky Posts` option. Comma-separated post IDs list - 1, 10, 25', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_post__in"
					v-model="query.post__in"
				><jet-query-dynamic-args v-model="dynamicQuery.post__in"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Post Not In', 'jet-engine' ); ?>"
					description="<?php _e( 'Use post ids. Specify post NOT to retrieve. Comma-separated post IDs list - 1, 10, 25', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_post__not_in"
					v-model="query.post__not_in"
				><jet-query-dynamic-args v-model="dynamicQuery.post__not_in"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Post Name In', 'jet-engine' ); ?>"
					description="<?php _e( 'Use post slugs. Specify posts list to retrieve. Comma-separated post slugs list', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_post_name__in"
					v-model="query.post_name__in"
				><jet-query-dynamic-args v-model="dynamicQuery.post_name__in"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Post Parent ID', 'jet-engine' ); ?>"
					description="<?php _e( 'Use page/post ID to return only child pages/posts. Set to 0 to return only top-level entries', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_post_parent"
					v-model="query.post_parent"
				><jet-query-dynamic-args v-model="dynamicQuery.post_parent"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Post Parent In', 'jet-engine' ); ?>"
					description="<?php _e( 'Use post ids. Specify posts whose parent is in the list. Comma-separated post IDs list - 1, 10, 25', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_post_parent__in"
					v-model="query.post_parent__in"
				><jet-query-dynamic-args v-model="dynamicQuery.post_parent__in"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Post Parent Not In', 'jet-engine' ); ?>"
					description="<?php _e( 'Use post ids. Specify posts whose parent is not in the list. Comma-separated post IDs list - 1, 10, 25', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_post_parent__not_in"
					v-model="query.post_parent__not_in"
				><jet-query-dynamic-args v-model="dynamicQuery.post_parent__not_in"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Post ID', 'jet-engine' ); ?>"
					description="<?php _e( 'Get post by ID', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_p"
					v-model="query.p"
				><jet-query-dynamic-args v-model="dynamicQuery.p"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Post Slug', 'jet-engine' ); ?>"
					description="<?php _e( 'Get post by slug', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_name"
					v-model="query.name"
				><jet-query-dynamic-args v-model="dynamicQuery.name"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Page ID', 'jet-engine' ); ?>"
					description="<?php _e( 'Get page by ID', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_page_id"
					v-model="query.page_id"
				><jet-query-dynamic-args v-model="dynamicQuery.page_id"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Page Slug', 'jet-engine' ); ?>"
					description="<?php _e( 'Get page by slug', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_pagename"
					v-model="query.pagename"
				><jet-query-dynamic-args v-model="dynamicQuery.pagename"></jet-query-dynamic-args></cx-vui-input>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="comments"
				:label="isInUseMark( [ 'comment_count_value', 'comment_count_compare' ] ) + '<?php _e( 'Comments', 'jet-engine' ); ?>'"
				key="comments"
			>
				<cx-vui-input
					label="<?php _e( 'Comments number', 'jet-engine' ); ?>"
					description="<?php _e( 'The amount of comments your post has to have when comparing', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_comment_count_value"
					v-model="query.comment_count_value"
				><jet-query-dynamic-args v-model="dynamicQuery.comment_count_value"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-select
					label="<?php _e( 'Compare', 'jet-engine' ); ?>"
					description="<?php _e( 'The search operator', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="commentOperators"
					size="fullwidth"
					v-model="query.comment_count_compare"
				></cx-vui-select>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="pagination"
				:label="isInUseMark( [ 'posts_per_page', 'offset', 'paged', 'page', 'ignore_sticky_posts' ] ) + '<?php _e( 'Pagination', 'jet-engine' ); ?>'"
				key="pagination"
			>
				<cx-vui-input
					label="<?php _e( 'Posts Per Page', 'jet-engine' ); ?>"
					description="<?php _e( 'Number of post to show per page. Use `-1` to show all posts (the `Offset` parameter is ignored with a -1 value)', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_posts_per_page"
					v-model="query.posts_per_page"
				><jet-query-dynamic-args v-model="dynamicQuery.posts_per_page"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Offset', 'jet-engine' ); ?>"
					description="<?php _e( 'Number of post to displace or pass over. Warning: Setting the offset parameter overrides/ignores the paged parameter and breaks pagination', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_offset"
					v-model="query.offset"
				><jet-query-dynamic-args v-model="dynamicQuery.offset"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Paged', 'jet-engine' ); ?>"
					description="<?php _e( 'Number of page. Show the posts that would normally show up just on page X', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_paged"
					v-model="query.paged"
				><jet-query-dynamic-args v-model="dynamicQuery.paged"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Page', 'jet-engine' ); ?>"
					description="<?php _e( 'Number of page for a static front page. Show the posts that would normally show up just on page X of a Static Front Page', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_page"
					v-model="query.page"
				><jet-query-dynamic-args v-model="dynamicQuery.page"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-switcher
					label="<?php _e( 'Ignore Sticky Posts', 'jet-engine' ); ?>"
					description="<?php _e( 'Ignore post stickiness. When disabled - move sticky posts to the start of the set. When enabled - do not move sticky posts to the start of the set', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					name="query_ignore_sticky_posts"
					v-model="query.ignore_sticky_posts"
				></cx-vui-switcher>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="author"
				:label="isInUseMark( [ 'author', 'author_name', 'author__in', 'author__not_in' ] ) + '<?php _e( 'Author', 'jet-engine' ); ?>'"
				key="author"
			>
				<cx-vui-input
					label="<?php _e( 'Author ID', 'jet-engine' ); ?>"
					description="<?php _e( 'Use specific author ID. Only single ID is allowed', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_author"
					v-model="query.author"
				><jet-query-dynamic-args v-model="dynamicQuery.author"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Author Name', 'jet-engine' ); ?>"
					description="<?php _e( 'Use `user_nicename` – NOT name', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_author_name"
					v-model="query.author_name"
				><jet-query-dynamic-args v-model="dynamicQuery.author_name"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Author In', 'jet-engine' ); ?>"
					description="<?php _e( 'Comma-separated author IDs list - 1, 10, 25', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_author__in"
					v-model="query.author__in"
				><jet-query-dynamic-args v-model="dynamicQuery.author__in"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Author Not In', 'jet-engine' ); ?>"
					description="<?php _e( 'Comma-separated author IDs list - 1, 10, 25', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_author__not_in"
					v-model="query.author__not_in"
				><jet-query-dynamic-args v-model="dynamicQuery.author__not_in"></jet-query-dynamic-args></cx-vui-input>
			</cx-vui-tabs-panel>
			<?php do_action( 'jet-engine/query-builder/posts/controls' ); ?>
		</cx-vui-tabs>
	</div>
</div>
