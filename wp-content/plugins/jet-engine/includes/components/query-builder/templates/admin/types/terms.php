<?php
/**
 * Posts query component template
 */
?>
<div class="jet-engine-edit-page__fields">
	<div class="cx-vui-collapse__heading">
		<h3 class="cx-vui-subtitle"><?php _e( 'Terms Query', 'jet-engine' ); ?></h3>
	</div>
	<div class="cx-vui-panel">
		<cx-vui-tabs
			:in-panel="false"
			value="general"
			layout="vertical"
		>
			<cx-vui-tabs-panel
				name="general"
				:label="isInUseMark( [ 'taxonomy', 'object_ids', 'orderby', 'number_per_page', 'number', 'offset' ] ) + '<?php _e( 'General', 'jet-engine' ); ?>'"
				key="general"
			>
				<cx-vui-f-select
					label="<?php _e( 'Taxonomy', 'jet-engine' ); ?>"
					name="query_post_type"
					description="<?php _e( 'Taxonomy name, or list of taxonomies, to which results should be limited', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="taxonomies"
					size="fullwidth"
					:multiple="true"
					v-model="query.taxonomy"
				></cx-vui-f-select>
				<cx-vui-input
					label="<?php _e( 'Object/Post IDs', 'jet-engine' ); ?>"
					description="<?php _e( 'Object/Post ID, or comma-separated list of object IDs. Results will be limited to terms associated with these objects', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_object_ids"
					v-model="query.object_ids"
				><jet-query-dynamic-args v-model="dynamicQuery.object_ids"></jet-query-dynamic-args></cx-vui-input>
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
					description="<?php _e( 'Meta field name to order by', 'jet-engine' ); ?>"
					v-if="'meta_value' === query.orderby || 'meta_value_num' === query.orderby"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-model="query.meta_key"
				></cx-vui-input>
				<cx-vui-f-select
					v-if="'meta_clause' === query.orderby && metaClauses.length"
					label="<?php _e( 'Meta Clause', 'jet-engine' ); ?>"
					description="<?php _e( 'Select meta clause to order by.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="metaClauses"
					size="fullwidth"
					name="query_order_meta_clause"
					v-model="query.order_meta_clause"
				></cx-vui-f-select>
				<cx-vui-component-wrapper
					v-if="'meta_clause' === query.orderby && ! metaClauses.length"
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
					:conditions="[
						{
							input: query.orderby,
							compare: 'not_in',
							value: [ 'include', 'slug__in' ],
						}
					]"
					size="fullwidth"
					name="query_order"
					v-model="query.order"
				></cx-vui-select>
				<cx-vui-switcher
					label="<?php _e( 'Hide Empty', 'jet-engine' ); ?>"
					description="<?php _e( 'Whether to hide terms not assigned to any posts.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					name="query_hide_empty"
					v-model="query.hide_empty"
				></cx-vui-switcher>
				<cx-vui-input
					label="<?php _e( 'Show/Per Page Number', 'jet-engine' ); ?>"
					description="<?php _e( 'Accepts 0 or any positive number. Note that may not return accurate results when coupled with `Object IDs`. If using with JetSmartFilters pagination - its number of returned items per page. If without pagination - its number of visible items in the listing grid. To count all possible results count, but show only N items in the grid - set N into this option and leave empty <b>Total Query Number</b>', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_number_per_page"
					v-model="query.number_per_page"
				><jet-query-dynamic-args v-model="dynamicQuery.number_per_page"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Total Query Number', 'jet-engine' ); ?>"
					description="<?php _e( 'Maximum number of terms to return. Accepts 0 or any positive number. Note that `Number` may not return accurate results when coupled with `Object IDs`. If using with JetSmartFilters pagination - defines total number of found items. If without pagination - its a total number that will be shown in the Query Count dynamic tag, Listings Grid still will show number of items set in the <b>Show/Per Page Number</b> option. To count all possible results count, but show only N items in the grid - leave this option empty and set N into <b>Show/Per Page Number</b> option', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_number"
					v-model="query.number"
				><jet-query-dynamic-args v-model="dynamicQuery.number"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Offset', 'jet-engine' ); ?>"
					description="<?php _e( 'The number by which to offset the terms query', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_offset"
					v-model="query.offset"
				><jet-query-dynamic-args v-model="dynamicQuery.offset"></jet-query-dynamic-args></cx-vui-input>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="include_exclude"
				:label="isInUseMark( [ 'name', 'slug', 'include', 'exclude', 'exclude_tree', 'search', 'name__like', 'description__like' ] ) + '<?php _e( 'Include/Exclude', 'jet-engine' ); ?>'"
				key="include_exclude"
			>
				<cx-vui-input
					label="<?php _e( 'Name', 'jet-engine' ); ?>"
					description="<?php _e( 'Name or comma-separated list of names to return term(s) for', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_name"
					v-model="query.name"
				><jet-query-dynamic-args v-model="dynamicQuery.name"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Slug', 'jet-engine' ); ?>"
					description="<?php _e( 'Slug or list of slugs to return term(s) for', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_slug"
					v-model="query.slug"
				><jet-query-dynamic-args v-model="dynamicQuery.slug"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Include', 'jet-engine' ); ?>"
					description="<?php _e( 'Comma/space-separated string of term IDs to include', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_include"
					v-model="query.include"
				><jet-query-dynamic-args v-model="dynamicQuery.include"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Exclude', 'jet-engine' ); ?>"
					description="<?php _e( 'Comma/space-separated string of term IDs to exclude. If `Include` is non-empty, `Exclude` is ignored', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_exclude"
					v-model="query.exclude"
				><jet-query-dynamic-args v-model="dynamicQuery.exclude"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Exclude Tree', 'jet-engine' ); ?>"
					description="<?php _e( 'Comma/space-separated string of term IDs to exclude along with all of their descendant terms. If `Include` is non-empty, `Exclude` is ignored', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_exclude_tree"
					v-model="query.exclude_tree"
				><jet-query-dynamic-args v-model="dynamicQuery.exclude_tree"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Search', 'jet-engine' ); ?>"
					description="<?php _e( 'Search criteria to match terms', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_search"
					v-model="query.search"
				><jet-query-dynamic-args v-model="dynamicQuery.search"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Name Like', 'jet-engine' ); ?>"
					description="<?php _e( 'Retrieve terms with criteria by which a term is LIKE `Name Like`', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_name__like"
					v-model="query.name__like"
				><jet-query-dynamic-args v-model="dynamicQuery.name__like"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Description Like', 'jet-engine' ); ?>"
					description="<?php _e( 'Retrieve terms where the description is LIKE `Description Like`', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_description__like"
					v-model="query.description__like"
				><jet-query-dynamic-args v-model="dynamicQuery.description__like"></jet-query-dynamic-args></cx-vui-input>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="parent_child"
				:label="isInUseMark( [ 'hierarchical', 'child_of', 'parent', 'childless' ] ) + '<?php _e( 'Parent/Child', 'jet-engine' ); ?>'"
				key="parent_child"
			>
				<cx-vui-switcher
					label="<?php _e( 'Hierarchical', 'jet-engine' ); ?>"
					description="<?php _e( 'Whether to include terms that have non-empty descendants.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					name="query_hierarchical"
					v-model="query.hierarchical"
				></cx-vui-switcher>
				<cx-vui-input
					label="<?php _e( 'Child Of', 'jet-engine' ); ?>"
					description="<?php _e( 'Term ID to retrieve child terms of. If multiple taxonomies are passed, `Child Of` is ignored', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_child_of"
					v-model="query.child_of"
				><jet-query-dynamic-args v-model="dynamicQuery.child_of"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Parent', 'jet-engine' ); ?>"
					description="<?php _e( 'Parent term ID to retrieve direct-child terms of. You can set 0 to get only top-level terms', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_parent"
					v-model="query.parent"
				><jet-query-dynamic-args v-model="dynamicQuery.parent"></jet-query-dynamic-args></cx-vui-input>
				<cx-vui-switcher
					label="<?php _e( 'Childless', 'jet-engine' ); ?>"
					description="<?php _e( 'Enable to limit results to terms that have no children. This parameter has no effect on non-hierarchical taxonomies', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					name="query_childless"
					v-model="query.childless"
				></cx-vui-switcher>
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
			<?php do_action( 'jet-engine/query-builder/terms/controls' ); ?>
		</cx-vui-tabs>
	</div>
</div>
