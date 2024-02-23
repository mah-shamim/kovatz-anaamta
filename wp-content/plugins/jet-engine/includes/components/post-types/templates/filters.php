<cx-vui-repeater
	:button-label="'<?php _e( 'Add New', 'jet-engine' ); ?>'"
	:button-style="'accent'"
	:button-size="'default'"
	v-model="adminFilters"
	@input="onInput"
	@add-new-item="addNewField( $event, [], adminFilters )"
>
	<cx-vui-repeater-item
		v-for="( filter, index ) in adminFilters"
		:title="adminFilters[ index ].title"
		:subtitle="adminFilters[ index ].type"
		:collapsed="isCollapsed( filter )"
		:index="index"
		@clone-item="cloneField( $event, filter._id, adminFilters )"
		@delete-item="deleteField( $event, filter._id, adminFilters )"
		:key="filter._id"
		>
		<cx-vui-input
			label="<?php _e( 'Name/Placeholder', 'jet-engine' ); ?>"
			description="<?php _e( 'Current filter name. Can be used as placeholder for filter dropdown control (see option below)', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			:value="adminFilters[ index ].title"
			@input="setFieldProp( filter._id, 'title', $event, adminFilters )"
		></cx-vui-input>
		<cx-vui-switcher
			label="<?php _e( 'Use Name as Placeholder', 'jet-engine' ); ?>"
			description="<?php _e( 'Use title as placeholder/reset option for the filter dropdown.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:value="adminFilters[ index ].title_as_placeholder"
			@input="setFieldProp( filter._id, 'title_as_placeholder', $event, adminFilters )"
		></cx-vui-switcher>
		<cx-vui-select
			label="<?php _e( 'Type', 'jet-engine' ); ?>"
			description="<?php _e( 'Select type of data to filter by - taxonomies or meta data', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			:options-list="adminFiltersTypes"
			:value="adminFilters[ index ].type"
			@input="setFieldProp( filter._id, 'type', $event, adminFilters )"
		></cx-vui-select>
		<template
			v-if="'taxonomy' === filter.type && hasTaxonomies()"
		>
			<cx-vui-select
				label="<?php _e( 'Taxonomy', 'jet-engine' ); ?>"
				description="<?php _e( 'Select taxonomy to filter data by', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				:options-list="taxonomiesList()"
				:value="adminFilters[ index ].tax"
				@input="setFieldProp( filter._id, 'tax', $event, adminFilters )"
			></cx-vui-select>
			<cx-vui-switcher
				v-if="isHierarchical( filter.tax )"
				label="<?php _e( 'Show Hierarchy', 'jet-engine' ); ?>"
				description="<?php _e( 'Show terms hierarchy with spaces.', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:value="adminFilters[ index ].show_hierarchy"
				@input="setFieldProp( filter._id, 'show_hierarchy', $event, adminFilters )"
			></cx-vui-switcher>
			<cx-vui-switcher
				label="<?php _e( 'Show Counts', 'jet-engine' ); ?>"
				description="<?php _e( 'Show posts counts for taxonomy terms.', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:value="adminFilters[ index ].show_count"
				@input="setFieldProp( filter._id, 'show_count', $event, adminFilters )"
			></cx-vui-switcher>
			<cx-vui-select
				label="<?php _e( 'Order By', 'jet-engine' ); ?>"
				description="<?php _e( 'Field to order terms by', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:options-list="[
					{
						value: 'name',
						label: 'Name',
					},
					{
						value: 'slug',
						label: 'Slug',
					},
					{
						value: 'term_group',
						label: 'Term group',
					},
					{
						value: 'term_id',
						label: 'Term ID',
					},
					{
						value: 'description',
						label: 'Description',
					},
					{
						value: 'parent',
						label: 'Parent',
					},
					{
						value: 'term_order',
						label: 'Term Order',
					},
					{
						value: 'count',
						label: 'By the number of objects associated with the term',
					},
				]"
				size="fullwidth"
				:value="adminFilters[ index ].tax_order_by"
				@input="setFieldProp( filter._id, 'tax_order_by', $event, adminFilters )"
			></cx-vui-select>
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
				:value="adminFilters[ index ].tax_order"
				@input="setFieldProp( filter._id, 'tax_order', $event, adminFilters )"
			></cx-vui-select>
		</template>
		<cx-vui-component-wrapper
			v-else-if="'taxonomy' === filter.type"
			label="<?php _e( 'Note!', 'jet-engine' ); ?>"
			description="<?php
				printf(
					__( 'You haven`t any taxonomies registered for this post type. %s', 'jet-engine' ),
					'<a href=\'' . admin_url( 'admin.php?page=jet-engine-cpt-tax&cpt_tax_action=add' ) . '\' target=\'_blank\'>' . __( 'Please create some here and proceed', 'jet-engine' ) . '</a>'
				);
			?>"
		></cx-vui-component-wrapper>
		<template
			v-if="'meta' === filter.type"
		>
			<cx-vui-select
				label="<?php _e( 'Meta Field', 'jet-engine' ); ?>"
				description="<?php _e( 'Select JetEngine meta field to filter posts by', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				v-if="currentTypeFields.length"
				:options-list="currentTypeFields"
				:value="adminFilters[ index ].meta_key"
				@input="setFieldProp( filter._id, 'meta_key', $event, adminFilters )"
			></cx-vui-select>
			<cx-vui-input
				label="<?php _e( 'Custom Meta Field', 'jet-engine' ); ?>"
				description="<?php _e( 'Or enter custom meta field name manually. Note! This meta field overrides previous option if set.', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				:value="adminFilters[ index ].custom_meta_key"
				@input="setFieldProp( filter._id, 'custom_meta_key', $event, adminFilters )"
			></cx-vui-input>
			<cx-vui-select
				label="<?php _e( 'Options Source', 'jet-engine' ); ?>"
				description="<?php _e( 'Get options for the filter from selected source', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				:options-list="optionsSources( filter )"
				:value="adminFilters[ index ].options_source"
				@input="setFieldProp( filter._id, 'options_source', $event, adminFilters )"
			></cx-vui-select>
			<cx-vui-select
				label="<?php _e( 'Glossary', 'jet-engine' ); ?>"
				description="<?php _e( 'Select exact glossary to get options from', 'jet-engine' ); ?>"
				v-if="'glossary' === filter.options_source"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				:options-list="glossariesList"
				:value="adminFilters[ index ].glossary_id"
				@input="setFieldProp( filter._id, 'glossary_id', $event, adminFilters )"
			></cx-vui-select>
			<cx-vui-select
				label="<?php _e( 'Order', 'jet-engine' ); ?>"
				description="<?php _e( 'Designates the ascending or descending order', 'jet-engine' ); ?>"
				v-if="'db' === filter.options_source"
				:wrapper-css="[ 'equalwidth' ]"
				:options-list="[
					{
						value: '',
						label: '<?php _e( 'Default', 'jet-engine' ); ?>',
					},
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
				:value="adminFilters[ index ].meta_order"
				@input="setFieldProp( filter._id, 'meta_order', $event, adminFilters )"
			></cx-vui-select>
		</template>
		<?php do_action( 'jet-engine/post-types/admin-filters/custom-controls' ); ?>
	</cx-vui-repeater-item>
</cx-vui-repeater>
