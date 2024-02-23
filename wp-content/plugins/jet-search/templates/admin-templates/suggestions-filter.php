<div class="jet-search-suggestions-filters">
	<div class="cx-vui-panel">
		<div class="jet-search-suggestions-filters-row">
			<template v-for="( filter, name ) in filters">
				<cx-vui-input
					v-if="'input' === filter.type"
					:key="name"
					:label="filter.label"
					:placeholder="filter.placeholder"
					:wrapper-css="[ 'jet-search-suggestions-filter' ]"
					:class="['jet-search-suggestions-filter-' + filter.name ]"
					:value="curentFilters[ name ]"
					@on-blur="updateFilters( $event, name, filter.type )"
					@on-keyup.enter="updateFilters( $event, name, filter.type )"
				>
				</cx-vui-input>
				<cx-vui-select
					v-else-if="'select' === filter.type"
					:key="name"
					:label="filter.label"
					:wrapper-css="[ 'jet-search-suggestions-filter' ]"
					:class="['jet-search-suggestions-filter-' + filter.name ]"
					:options-list="filter.options"
					:value="curentFilters[ name ]"
					@input="updateFilters( $event, name, filter.type )"
				>
				</cx-vui-select>
			</template>
			<cx-vui-button
				v-if="curentFilters"
				class="jet-search-suggestions-clear-filters"
				@click="clearFilter()"
				button-style="accent-border"
				size="mini"
				:disabled="filterButtonDisabled"
			>
				<template slot="label"><?php esc_html_e( 'Clear', 'jet-search' ); ?></template>
			</cx-vui-button>
		</div>
	</div>
</div>