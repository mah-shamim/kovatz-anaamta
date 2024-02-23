<?php
/**
 * Posts query component template
 */
?>
<div class="jet-engine-edit-page__fields">
	<div class="cx-vui-collapse__heading">
		<h3 class="cx-vui-subtitle"><?php _e( 'Comments Query', 'jet-engine' ); ?></h3>
	</div>
	<div class="cx-vui-panel">
		<cx-vui-tabs
			:in-panel="false"
			value="include_exclude"
			layout="vertical"
		>
			<cx-vui-tabs-panel
				name="include_exclude"
				:label="isInUseMark( [ 'comment__in', 'comment__not_in', 'parent', 'parent__in', 'parent__not_in' ] ) + '<?php _e( 'Include/Exclude', 'jet-engine' ); ?>'"
				key="include_exclude"
			>
				<cx-vui-input
					label="<?php _e( 'Comment In', 'jet-engine' ); ?>"
					description="<?php _e( 'List of comment IDs to include', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_comment__in"
					v-model="query.comment__in"
				><jet-query-dynamic-args v-model="dynamicQuery.comment__in"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Comment Not In', 'jet-engine' ); ?>"
					description="<?php _e( 'List of comment IDs to exclude', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_comment__not_in"
					v-model="query.comment__not_in"
				><jet-query-dynamic-args v-model="dynamicQuery.comment__not_in"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Parent', 'jet-engine' ); ?>"
					description="<?php _e( 'Parent ID of comment to retrieve children of', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_parent"
					v-model="query.parent"
				><jet-query-dynamic-args v-model="dynamicQuery.parent"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Parent In', 'jet-engine' ); ?>"
					description="<?php _e( 'List of parent IDs of comments to retrieve children for', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_parent__in"
					v-model="query.parent__in"
				><jet-query-dynamic-args v-model="dynamicQuery.parent__in"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Parent Not In', 'jet-engine' ); ?>"
					description="<?php _e( 'List of parent IDs of comments <b>not</b> to retrieve children for', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_parent__not_in"
					v-model="query.parent__not_in"
				><jet-query-dynamic-args v-model="dynamicQuery.parent__not_in"></jet-query-dynamic-args></cx-vui-input>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="author"
				:label="isInUseMark( [ 'author_email', 'author_url', 'author__in', 'author__not_in', 'post_author__not_in' ] ) + '<?php _e( 'Author', 'jet-engine' ); ?>'"
				key="author"
			>
				<cx-vui-input
					label="<?php _e( 'Author Email', 'jet-engine' ); ?>"
					description="<?php _e( 'Comment author email address', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_author_email"
					v-model="query.author_email"
				><jet-query-dynamic-args v-model="dynamicQuery.author_email"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Author URL', 'jet-engine' ); ?>"
					description="<?php _e( 'Comment author URL', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_author_url"
					v-model="query.author_url"
				><jet-query-dynamic-args v-model="dynamicQuery.author_url"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Author In', 'jet-engine' ); ?>"
					description="<?php _e( 'List of author IDs to include comments for', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_author__in"
					v-model="query.author__in"
				><jet-query-dynamic-args v-model="dynamicQuery.author__in"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Author Not In', 'jet-engine' ); ?>"
					description="<?php _e( 'List of comment author IDs to exclude comments for', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_author__not_in"
					v-model="query.author__not_in"
				><jet-query-dynamic-args v-model="dynamicQuery.author__not_in"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Post Author In', 'jet-engine' ); ?>"
					description="<?php _e( 'List of post author IDs to retrieve comments for', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_post_author__in"
					v-model="query.post_author__in"
				><jet-query-dynamic-args v-model="dynamicQuery.post_author__in"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Post Author Not In', 'jet-engine' ); ?>"
					description="<?php _e( 'List of author IDs <b>not</b> to retrieve comments for', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_post_author__not_in"
					v-model="query.post_author__not_in"
				><jet-query-dynamic-args v-model="dynamicQuery.post_author__not_in"></jet-query-dynamic-args></cx-vui-input>
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
											value: 'comment_date',
											label: 'Comment Date',
										},
										{
											value: 'comment_date_gmt',
											label: 'Comment Date GMT',
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
				name="pagination"
				:label="isInUseMark( [ 'number', 'paged', 'offset', 'orderby' ] ) + '<?php _e( 'Pagination', 'jet-engine' ); ?>'"
				key="pagination"
			>
				<cx-vui-input
					label="<?php _e( 'Number', 'jet-engine' ); ?>"
					description="<?php _e( 'Maximum number of comments to retrieve', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_number"
					v-model="query.number"
				><jet-query-dynamic-args v-model="dynamicQuery.number"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Paged', 'jet-engine' ); ?>"
					description="<?php _e( 'Defines the page of results to return. When used with <b>Offset</b>, <b>Offset</b> takes precedence. Setting this option will break pagination', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_paged"
					v-model="query.paged"
				><jet-query-dynamic-args v-model="dynamicQuery.paged"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Offset', 'jet-engine' ); ?>"
					description="<?php _e( 'Number of comments to offset the query', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_offset"
					v-model="query.offset"
				><jet-query-dynamic-args v-model="dynamicQuery.offset"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-select
					label="<?php _e( 'Order By', 'jet-engine' ); ?>"
					description="<?php _e( 'Field to order terms by', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="[
						{
							value: 'comment_agent',
							label: 'Comment agent',
						},
						{
							value: 'comment_approved',
							label: 'Comment approved',
						},
						{
							value: 'comment_author',
							label: 'Comment author',
						},
						{
							value: 'comment_author_email',
							label: 'Comment author email',
						},
						{
							value: 'comment_author_IP',
							label: 'Comment author IP',
						},
						{
							value: 'comment_author_url',
							label: 'Comment author URL',
						},
						{
							value: 'comment_content',
							label: 'Comment content',
						},
						{
							value: 'comment_date',
							label: 'Comment date',
						},
						{
							value: 'comment_date_gmt',
							label: 'Comment date GMT',
						},
						{
							value: 'comment_ID',
							label: 'Comment ID',
						},
						{
							value: 'meta_value',
							label: 'Meta value',
						},
						{
							value: 'meta_value_num',
							label: 'Numeric meta value',
						},
					]"
					size="fullwidth"
					name="query_orderby"
					v-model="query.orderby"
				></cx-vui-select>
				<cx-vui-input
					label="<?php _e( 'Meta key', 'jet-engine' ); ?>"
					description="<?php _e( 'Required. Meta field key/name to order by', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
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
				></cx-vui-input>
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
					name="query_order"
					v-model="query.order"
				></cx-vui-select>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="post"
				:label="isInUseMark( [ 'post_status', 'post_type', 'post_name', 'post_parent' ] ) + '<?php _e( 'Post', 'jet-engine' ); ?>'"
				key="post"
			>
				<cx-vui-input
					label="<?php _e( 'Post ID', 'jet-engine' ); ?>"
					description="<?php _e( 'Limit results to those affiliated with a given post ID', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_post_id"
					v-model="query.post_id"
				><jet-query-dynamic-args v-model="dynamicQuery.post_id"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Post In', 'jet-engine' ); ?>"
					description="<?php _e( 'Array of post IDs to include affiliated comments for', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_post__in"
					v-model="query.post__in"
				><jet-query-dynamic-args v-model="dynamicQuery.post_name"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Post Not In', 'jet-engine' ); ?>"
					description="<?php _e( 'Array of post IDs to exclude affiliated comments for', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_post__not_in"
					v-model="query.post__not_in"
				><jet-query-dynamic-args v-model="dynamicQuery.post_name"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-f-select
					label="<?php _e( 'Post Status', 'jet-engine' ); ?>"
					description="<?php _e( 'Post status or list of post statuses to retrieve affiliated comments for.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="postStatuses"
					size="fullwidth"
					:multiple="true"
					name="query_post_status"
					v-model="query.post_status"
				></cx-vui-f-select>
				<cx-vui-f-select
					label="<?php _e( 'Post Type', 'jet-engine' ); ?>"
					name="query_post_type"
					description="<?php _e( 'Post type or list of post types to retrieve affiliated comments for.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="postTypes"
					size="fullwidth"
					:multiple="true"
					v-model="query.post_type"
				></cx-vui-f-select>
				<cx-vui-input
					label="<?php _e( 'Post Name', 'jet-engine' ); ?>"
					description="<?php _e( 'Post name to retrieve affiliated comments for', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_post_name"
					v-model="query.post_name"
				><jet-query-dynamic-args v-model="dynamicQuery.post_name"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Post Parent', 'jet-engine' ); ?>"
					description="<?php _e( 'Post parent ID to retrieve affiliated comments for', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_post_parent"
					v-model="query.post_parent"
				><jet-query-dynamic-args v-model="dynamicQuery.post_parent"></jet-query-dynamic-args></cx-vui-input>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="misc"
				:label="isInUseMark( [ 'search', 'status', 'type', 'type__in', 'type__not_in' ] ) + '<?php _e( 'Misc', 'jet-engine' ); ?>'"
				key="misc"
			>
				<cx-vui-input
					label="<?php _e( 'Search', 'jet-engine' ); ?>"
					description="<?php _e( 'Search term(s) to retrieve matching comments for', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_search"
					v-model="query.search"
				><jet-query-dynamic-args v-model="dynamicQuery.search"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Status', 'jet-engine' ); ?>"
					description="<?php _e( 'Comment statuses to limit results by. Accepts a space/comma-separated list of \'hold\' (comment_status=0), \'approve\' (comment_status=1), \'all\', or a custom comment status. Default \'all\'', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_status"
					v-model="query.status"
				><jet-query-dynamic-args v-model="dynamicQuery.status"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Type', 'jet-engine' ); ?>"
					description="<?php _e( 'Include comments of a given type, or array of types. Accepts \'comment\', \'pings\' (includes \'pingback\' and \'trackback\'), or any custom type string', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="type"
					v-model="query.type"
				><jet-query-dynamic-args v-model="dynamicQuery.type"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Type In', 'jet-engine' ); ?>"
					description="<?php _e( 'Comma separated comment types list to include', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_type__in"
					v-model="query.type__in"
				><jet-query-dynamic-args v-model="dynamicQuery.type__in"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Type Not In', 'jet-engine' ); ?>"
					description="<?php _e( 'Comma separated comment types list to exclude', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_type__not_in"
					v-model="query.type__not_in"
				><jet-query-dynamic-args v-model="dynamicQuery.type__not_in"></jet-query-dynamic-args></cx-vui-input>
			</cx-vui-tabs-panel>
		</cx-vui-tabs>
	</div>
</div>
