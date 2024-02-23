<?php
/**
 * WC product query component template
 */
?>
<div class="jet-engine-edit-page__fields">
	<div class="cx-vui-collapse__heading">
		<h3 class="cx-vui-subtitle"><?php _e( 'WC Product Query', 'jet-engine' ); ?></h3>
	</div>
	<div class="cx-vui-panel">
		<cx-vui-tabs
			:in-panel="false"
			value="general"
			layout="vertical"
		>
			<cx-vui-tabs-panel
				name="general"
				:label="isInUseMark( [ 'status', 'type', 'include', 'exclude', 'parent', 'parent_exclude', 'author', 'orderby', 'meta_key', 'order' ] ) + '<?php _e( 'General', 'jet-engine' ); ?>'"
				key="general"
			>
				<cx-vui-f-select
					label="<?php _e( 'Product Status', 'jet-engine' ); ?>"
					description="<?php _e( 'One or more of `draft`, `pending`, `private`, `publish`, or a custom status. By default include all WP default post statuses.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="postStatuses"
					size="fullwidth"
					:multiple="true"
					name="query_status"
					v-model="query.status"
				></cx-vui-f-select>
				<cx-vui-f-select
					label="<?php _e( 'Product Type', 'jet-engine' ); ?>"
					description="<?php _e( 'Set one or more of product type slug: `external`, `grouped`, `simple`, `variable`, or a custom type. Leave empty to retrieve products of any type.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="productTypes"
					size="fullwidth"
					:multiple="true"
					name="query_type"
					v-model="query.type"
				></cx-vui-f-select>
				<cx-vui-switcher
					label="<?php _e( 'Handle Search Query', 'jet-engine' ); ?>"
					description="<?php _e( 'Enable for compatibility with search query.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					name="query_search_query"
					v-model="query.search_query"
				></cx-vui-switcher>
				<cx-vui-input
					label="<?php _e( 'Include', 'jet-engine' ); ?>"
					description="<?php _e( 'Only includes products with IDs in the array. Comma-separated products IDs list - 1, 10, 25.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_include"
					v-model="query.include"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.include"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Exclude', 'jet-engine' ); ?>"
					description="<?php _e( 'Excludes products with IDs in the array Comma-separated Products IDs list - 1, 10, 25. ATTENTION: Ignored if you use `Include`.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_exclude"
					v-model="query.exclude"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.exclude"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Parent ID', 'jet-engine' ); ?>"
					description="<?php _e( 'Post ID of the product parent. Get product with a specific parent.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_parent"
					v-model="query.parent"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.parent"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Parent ID Exclude', 'jet-engine' ); ?>"
					description="<?php _e( 'Excludes products with parent ids in the array. Comma-separated post IDs list - 1, 10, 25. ATTENTION: Ignored if you use `Parent ID`.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_parent_exclude"
					v-model="query.parent_exclude"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.parent_exclude"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Author ID', 'jet-engine' ); ?>"
					type="number"
					description="<?php _e( 'Get products with a specific author.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_author"
					v-model="query.author"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.author"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-select
					label="<?php _e( 'Order By', 'jet-engine' ); ?>"
					description="<?php _e( 'Sort retrieved products by selected parameter.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="[
										{
											value: 'ID',
											label: 'Product id',
										},
										{
											value: 'name',
											label: 'Product name (slug)',
										},
										{
											value: 'type',
											label: 'Product type',
										},
										{
											value: 'rand',
											label: 'Random order',
										},
										{
											value: 'date',
											label: 'Date',
										},
										{
											value: 'modified',
											label: 'Last modified date',
										},
										{
											value: 'meta_value',
											label: 'Meta Value',
										},
										{
											value: 'meta_value_num',
											label: 'Numeric Meta Value',
										},
									]"
					size="fullwidth"
					v-model="query.orderby"
				></cx-vui-select>
				<cx-vui-input
					label="<?php _e( 'Meta Key', 'jet-engine' ); ?>"
					description="<?php _e( 'Set meta field key.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_meta_key"
					v-model="query.meta_key"
					v-if="'meta_value_num' === query.orderby || 'meta_value' === query.orderby"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.meta_key"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-select
					label="<?php _e( 'Order', 'jet-engine' ); ?>"
					description="<?php _e( 'Designates the ascending or descending order of the `Order By` parameter.', 'jet-engine' ); ?>"
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
					v-model="query.order"
				></cx-vui-select>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="product"
				:label="isInUseMark( [ 'sku', 'tag', 'category', 'total_sales', 'backorders', 'visibility', 'stock_quantity', 'stock_status', 'tax_status', 'shipping_class', 'download_limit', 'download_expiry', 'average_rating', 'review_count' ] ) + '<?php _e( 'Product', 'jet-engine' ); ?>'"
				key="product"
			>
				<cx-vui-input
					label="<?php _e( 'SKU', 'jet-engine' ); ?>"
					description="<?php _e( 'Product SKU to match on. Does partial matching on the SKU.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_sku"
					v-model="query.sku"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.sku"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-f-select
					label="<?php _e( 'Tags', 'jet-engine' ); ?>"
					description="<?php _e( 'Limit results to products assigned to specific tags by slug.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="productTags"
					size="fullwidth"
					:multiple="true"
					name="query_tag"
					v-model="query.tag"
				></cx-vui-f-select>
				<cx-vui-f-select
					label="<?php _e( 'Categories', 'jet-engine' ); ?>"
					description="<?php _e( 'Limit results to products assigned to specific categories by slug.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="productCategories"
					size="fullwidth"
					:multiple="true"
					name="query_category"
					v-model="query.category"
				></cx-vui-f-select>
				<cx-vui-input
					label="<?php _e( 'Total Sales', 'jet-engine' ); ?>"
					description="<?php _e( 'Gets products with that many sales.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_total_sales"
					v-model="query.total_sales"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.total_sales"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-select
					label="<?php _e( 'Backorders', 'jet-engine' ); ?>"
					description="<?php _e( 'Get products that match selected option.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="[
										{
											value: '',
											label: 'None',
										},
										{
											value: 'yes',
											label: 'Allow',
										},
										{
											value: 'no',
											label: 'Do not allow',
										},
										{
											value: 'notify',
											label: 'Allow, but notify customer',
										}
									]"
					size="fullwidth"
					v-model="query.backorders"
				></cx-vui-select>
				<cx-vui-select
					label="<?php _e( 'Visibility', 'jet-engine' ); ?>"
					description="<?php _e( 'Get products that match selected visibility option.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="[
										{
											'value': '',
											'label': 'Select type...'
										},
										{
											value: 'visible',
											label: 'Catalog & search',
										},
										{
											value: 'catalog',
											label: 'Catalog',
										},
										{
											value: 'search',
											label: 'Search',
										},
										{
											value: 'hidden',
											label: 'Hidden',
										}
									]"
					size="fullwidth"
					v-model="query.visibility"
				></cx-vui-select>
				<cx-vui-input
					label="<?php _e( 'Stock Quantity', 'jet-engine' ); ?>"
					description="<?php _e( 'The quantity of a product in stock.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_stock_quantity"
					v-model="query.stock_quantity"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.stock_quantity"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-select
					label="<?php _e( 'Stock Status', 'jet-engine' ); ?>"
					description="<?php _e( 'Get products that match selected option.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="[
										{
											value: '',
											label: 'All',
										},
										{
											value: 'instock',
											label: 'In stock',
										},
										{
											value: 'outofstock',
											label: 'Out of stock',
										}
									]"
					size="fullwidth"
					v-model="query.stock_status"
				></cx-vui-select>
				<cx-vui-select
					label="<?php _e( 'Tax Status', 'jet-engine' ); ?>"
					description="<?php _e( 'Get products that match selected option.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="[
										{
											value: '',
											label: 'All',
										},
										{
											value: 'none',
											label: 'None',
										},
										{
											value: 'taxable',
											label: 'Taxable',
										},
										{
											value: 'shipping',
											label: 'Shipping only',
										}
									]"
					size="fullwidth"
					v-model="query.tax_status"
				></cx-vui-select>
				<cx-vui-input
					label="<?php _e( 'Tax Class', 'jet-engine' ); ?>"
					description="<?php _e( 'A tax class slug. Get products that match specified class.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_tax_class"
					v-model="query.tax_class"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.tax_class"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Shipping Class', 'jet-engine' ); ?>"
					description="<?php _e( 'One or more shipping class slug. Get products that match specified class. Comma-separated class slugs list - class_1, class_2, class_3.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_shipping_class"
					v-model="query.shipping_class"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.shipping_class"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Download Limit', 'jet-engine' ); ?>"
					description="<?php _e( 'Gets products with that download limit, set `-1` for unlimited.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_download_limit"
					v-model="query.download_limit"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.download_limit"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Download Expiry', 'jet-engine' ); ?>"
					description="<?php _e( 'Gets products with that download expiry, set `-1` for unlimited.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_download_expiry"
					v-model="query.download_expiry"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.download_expiry"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Average Rating', 'jet-engine' ); ?>"
					description="<?php _e( 'Gets products with that average rating.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_average_rating"
					v-model="query.average_rating"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.average_rating"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Review Count', 'jet-engine' ); ?>"
					description="<?php _e( 'Gets products with that number of reviews.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_review_count"
					v-model="query.review_count"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.review_count"></jet-query-dynamic-args>
				</cx-vui-input>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="prices"
				:label="isInUseMark( [ 'price', 'regular_price', 'sale_price' ] ) + '<?php _e( 'Prices', 'jet-engine' ); ?>'"
				key="prices"
			>
				<cx-vui-input
					label="<?php _e( 'Price', 'jet-engine' ); ?>"
					description="<?php _e( 'Gets products with that price.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_price"
					v-model="query.price"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.price"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Regular Price', 'jet-engine' ); ?>"
					description="<?php _e( 'Gets products with that regular price.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_regular_price"
					v-model="query.regular_price"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.regular_price"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Sale Price', 'jet-engine' ); ?>"
					description="<?php _e( 'Gets products with that sale price.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_sale_price"
					v-model="query.sale_price"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.sale_price"></jet-query-dynamic-args>
				</cx-vui-input>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="measurements"
				:label="isInUseMark( [ 'weight', 'length', 'width', 'height' ] ) + '<?php _e( 'Measurements', 'jet-engine' ); ?>'"
				key="measurements"
			>
				<cx-vui-input
					label="<?php _e( 'Weight', 'jet-engine' ); ?>"
					description="<?php _e( 'Gets products with that weight.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_weight"
					v-model="query.weight"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.weight"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Length', 'jet-engine' ); ?>"
					description="<?php _e( 'Gets products with that length.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_length"
					v-model="query.length"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.length"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Width', 'jet-engine' ); ?>"
					description="<?php _e( 'Gets products with that width.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_width"
					v-model="query.width"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.width"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Height', 'jet-engine' ); ?>"
					description="<?php _e( 'Gets products with that height.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_height"
					v-model="query.height"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.height"></jet-query-dynamic-args>
				</cx-vui-input>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="specific"
				:label="isInUseMark( [ 'specific_query' ] ) + '<?php _e( 'Specific', 'jet-engine' ); ?>'"
				key="specific"
			>
				<cx-vui-component-wrapper
					:wrapper-css="[ 'query-fullwidth' ]"
				>
					<div class="cx-vui-inner-panel query-panel">
						<div class="cx-vui-component__label"><?php _e( 'Specific Settings', 'jet-engine' ); ?></div>
						<cx-vui-repeater
							button-label="<?php _e( 'Add new ', 'jet-engine' ); ?>"
							button-style="accent"
							button-size="mini"
							v-model="query.specific_query"
							@add-new-item="addNewField( $event, [], query.specific_query )"
						>
							<cx-vui-repeater-item
								v-for="( specificClause, index ) in query.specific_query"
								:title="query.specific_query[ index ].feature"
								:subtitle="query.specific_query[ index ].status"
								:collapsed="isCollapsed( specificClause )"
								:index="index"
								@clone-item="cloneField( $event, specificClause._id, query.specific_query )"
								@delete-item="deleteField( $event, specificClause._id, query.specific_query )"
								:key="specificClause._id"
							>
								<cx-vui-select
									label="<?php _e( 'Feature', 'jet-engine' ); ?>"
									description="<?php _e( 'Limit results to products with the specific settings or features.', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:options-list="[
										{
											value: 'virtual',
											label: 'Virtual',
										},
										{
											value: 'downloadable',
											label: 'Downloadable',
										},
										{
											value: 'featured',
											label: 'Featured',
										},
										{
											value: 'sold_individually',
											label: 'Sold individually',
										},
										{
											value: 'manage_stock',
											label: 'Manage stock',
										},
										{
											value: 'reviews_allowed',
											label: 'Reviews allowed',
										}
									]"
									size="fullwidth"
									:value="query.specific_query[ index ].feature"
									@input="setFieldProp( specificClause._id, 'feature', $event, query.specific_query )"
								></cx-vui-select>
								<cx-vui-select
									label="<?php _e( 'Status', 'jet-engine' ); ?>"
									description="<?php _e( 'Enable or disable selected `Feature`.', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:options-list="[
										{
											value: true,
											label: 'True',
										},
										{
											value: false,
											label: 'False',
										}
									]"
									size="fullwidth"
									:value="query.specific_query[ index ].status"
									@input="setFieldProp( specificClause._id, 'status', $event, query.specific_query )"
								></cx-vui-select>
							</cx-vui-repeater-item>
						</cx-vui-repeater>
					</div>
				</cx-vui-component-wrapper>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="pagination"
				:label="isInUseMark( [ 'limit', 'page', 'offset' ] ) + '<?php _e( 'Pagination', 'jet-engine' ); ?>'"
				key="pagination"
			>
				<cx-vui-switcher
					label="<?php _e( 'Enable Pagination', 'jet-engine' ); ?>"
					description="<?php _e( 'Enable to modifies the return result and get an object with fields: `products`, `total`, `max_num_pages`. ATTENTION: If you disable this option it may cause some query issues.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					name="query_paginate"
					:return-true="true"
					:return-false="false"
					v-model="query.paginate"
				></cx-vui-switcher>
				<cx-vui-input
					label="<?php _e( 'Limit', 'jet-engine' ); ?>"
					description="<?php _e( 'Maximum number of results to retrieve or `-1` for unlimited.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_limit"
					v-model="query.limit"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.limit"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Page', 'jet-engine' ); ?>"
					description="<?php _e( 'Page of results to retrieve. Does nothing if `offset` is used.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_page"
					v-model="query.page"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.page"></jet-query-dynamic-args>
				</cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Offset', 'jet-engine' ); ?>"
					description="<?php _e( 'Amount to offset product result. WARNING: Setting the offset parameter overrides/ignores the page parameter and breaks pagination.', 'jet-engine' ); ?>"
					type="number"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_offset"
					v-model="query.offset"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.offset"></jet-query-dynamic-args>
				</cx-vui-input>
			</cx-vui-tabs-panel>
			<cx-vui-tabs-panel
				name="date"
				:label="isInUseMark( [ 'date_query' ] ) + '<?php _e( 'Date', 'jet-engine' ); ?>'"
				key="date"
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
								>
									<jet-query-dynamic-args v-model="dynamicQuery.date_query[ dateClause._id ].year"></jet-query-dynamic-args>
								</cx-vui-input>
								<cx-vui-input
									label="<?php _e( 'Month', 'jet-engine' ); ?>"
									description="<?php _e( 'Month number (from 1 to 12)', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth', 'has-macros' ]"
									size="fullwidth"
									:value="query.date_query[ index ].month"
									@input="setFieldProp( dateClause._id, 'month', $event, query.date_query )"
								>
									<jet-query-dynamic-args v-model="dynamicQuery.date_query[ dateClause._id ].month"></jet-query-dynamic-args>
								</cx-vui-input>
								<cx-vui-input
									label="<?php _e( 'Day', 'jet-engine' ); ?>"
									description="<?php _e( 'Day of the month (from 1 to 31)', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth', 'has-macros' ]"
									size="fullwidth"
									:value="query.date_query[ index ].day"
									@input="setFieldProp( dateClause._id, 'day', $event, query.date_query )"
								>
									<jet-query-dynamic-args v-model="dynamicQuery.date_query[ dateClause._id ].day"></jet-query-dynamic-args>
								</cx-vui-input>
								<cx-vui-input
									label="<?php _e( 'After', 'jet-engine' ); ?>"
									description="<?php _e( 'Date to retrieve products after. Eg. January 1st 2020, Today, Tomorrow etc.', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth', 'has-macros' ]"
									size="fullwidth"
									:value="query.date_query[ index ].after"
									@input="setFieldProp( dateClause._id, 'after', $event, query.date_query )"
								>
									<jet-query-dynamic-args v-model="dynamicQuery.date_query[ dateClause._id ].after"></jet-query-dynamic-args>
								</cx-vui-input>
								<cx-vui-input
									label="<?php _e( 'Before', 'jet-engine' ); ?>"
									description="<?php _e( 'Date to retrieve products before. Eg. January 1st 2020, Today, Tomorrow etc.', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth', 'has-macros' ]"
									size="fullwidth"
									:value="query.date_query[ index ].before"
									@input="setFieldProp( dateClause._id, 'before', $event, query.date_query )"
								>
									<jet-query-dynamic-args v-model="dynamicQuery.date_query[ dateClause._id ].before"></jet-query-dynamic-args>
								</cx-vui-input>
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
									description="<?php _e( 'Products column to query against', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:options-list="[
										{
											value: 'date_created',
											label: 'Date created',
										},
										{
											value: 'date_modified',
											label: 'Date modified',
										},
										{
											value: 'date_on_sale_from',
											label: 'Date on sale from',
										},
										{
											value: 'date_on_sale_to',
											label: 'Date on sale to',
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
								<cx-vui-input
									label="<?php _e( 'Value', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth', 'has-macros' ]"
									size="fullwidth"
									:value="query.meta_query[ index ].value"
									@input="setFieldProp( clause._id, 'value', $event, query.meta_query )"
								><jet-query-dynamic-args v-model="dynamicQuery.meta_query[ clause._id ].value"></jet-query-dynamic-args></cx-vui-input>
								<cx-vui-select
									label="<?php _e( 'Compare', 'jet-engine' ); ?>"
									description="<?php _e( 'Operator to test', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:options-list="operators"
									size="fullwidth"
									:value="query.meta_query[ index ].compare"
									@input="setFieldProp( clause._id, 'compare', $event, query.meta_query )"
								></cx-vui-select>
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
							v-model="query.tax_query"
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
				<div class="cx-vui-component__desc" v-if="1 < query.tax_query.length">
					<?php _e( 'At the moment, WooCommerce partially supports the `tax_query` functionality and does not provide the ability to use the relation property. The relation option will be hidden until its functionality is implemented.', 'jet-engine' ); ?>
				</div>
				<!--<cx-vui-select
					v-if="1 < query.tax_query.length"
					label="<?php /*_e( 'Relation', 'jet-engine' ); */?>"
					description="<?php /*_e( 'The logical relationship between tax query clauses', 'jet-engine' ); */?>"
					:wrapper-css="[ 'equalwidth' ]"
					:options-list="[
						{
							value: 'and',
							label: '<?php /*_e( 'And', 'jet-engine' ); */?>',
						},
						{
							value: 'or',
							label: '<?php /*_e( 'Or', 'jet-engine' ); */?>',
						},
					]"
					size="fullwidth"
					v-model="query.tax_query_relation"
				></cx-vui-select>-->
			</cx-vui-tabs-panel>
		</cx-vui-tabs>
	</div>
</div>
