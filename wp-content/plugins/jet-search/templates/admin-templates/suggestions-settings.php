<div
	class="jet-search-settings-page jet-search-settings-page__suggestions"
>
	<div class="jet-search-suggestions-wrap">
		<header class="jet-search-suggestions-header">
			<h1 class="jet-search-suggestions-title"><?php esc_html_e( 'Suggestions', 'jet-search' ); ?></h1>
			<jet-search-add-new-suggestion @callPopup="callPopup"
				:class="{ 'jet-search-suggestions': isLoading, 'jet-search-suggestions-button-add-new': true, 'transition': true }"
			></jet-search-add-new-suggestion>
			<jet-search-suggestions-config :generalSettings="generalSettings" @saveSettings="saveSettings"></jet-search-suggestions-config>
		</header>
		<div class="jet-search-suggestions-listing">
			<jet-search-suggestions-filter @clearFilter="clearFilter" @updateFilters="updateFilters"></jet-search-suggestions-filter>
			<jet-search-suggestions-pagination :offset="offset" :totalItems="totalItems" :perPage="perPage" :pageNumber="pageNumber" :onPage="onPage" @changePage="changePage" @changePerPage="changePerPage"></jet-search-suggestions-pagination>

			
			<cx-vui-list-table
				:is-empty="! itemsList.length"
				empty-message="<?php _e( 'No suggestions found.', 'jet-search' ); ?>"
			>
				<cx-vui-list-table-heading
					:slots="columnsIDs"
					slot="heading"
				>
					<span
						:key="column"
						:slot="column"
						:class="classColumn( column )"
						v-for="column in columnsIDs"
						@click="sortColumn( column )"
					>{{ getItemLabel( column ) }}<svg v-if="! notSortable.includes( column )" class="jet-search-suggestions-active-column-icon" width="12" height="6" viewBox="0 0 10 5" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.833374 0.333328L5.00004 4.5L9.16671 0.333328H0.833374Z" fill="#7B7E81"/></svg>
					</span>
				</cx-vui-list-table-heading>

				<template slot="items">
					<template
						v-for="( item, index ) in itemsList">
						<div class="list-table-item">
							<div
								v-for="column in columnsIDs"
								:class="[ 'list-table-item__cell', 'cell--' + column ]"
							>
								{{ getItemColumnValue( item, column ) }}
								<span class="jet-search-suggestions__type-icon" v-if="column === 'type' && null != item['child']">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 3H3V11H11V3Z" fill="#7B7E81"/><path d="M11 13H3V21H11V13Z" fill="#7B7E81"/><path d="M13 13H21V21H13V13Z" fill="#7B7E81"/><path d="M21 3H13V11H21V3Z" fill="#7B7E81"/></svg>
									<div class="jet-search-suggestions__type-icon-tip"><?php
										_e( 'Parent Suggestion', 'jet-search' );
									?></div>
								</span>
								<span class="jet-search-suggestions__type-icon" v-if="column === 'type' && '0' != item['parent'] && null === item['child']">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M3 3V21H21V3H3ZM19 5H5V19H19V5Z" fill="#7B7E81"/></svg>
									<div class="jet-search-suggestions__type-icon-tip"><?php
										_e( 'Child Suggestion', 'jet-search' );
									?></div>
								</span>
								<span class="jet-search-suggestions__type-icon" v-if="column === 'type' && '0' === item['parent'] && null === item['child']">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 3H3V5H5V3Z" fill="#7B7E81"/><path d="M5 19H3V21H5V19Z" fill="#7B7E81"/><path d="M19 19H21V21H19V19Z" fill="#7B7E81"/><path d="M21 3H19V5H21V3Z" fill="#7B7E81"/><path d="M15 3H17V5H15V3Z" fill="#7B7E81"/><path d="M17 19H15V21H17V19Z" fill="#7B7E81"/><path d="M3 9V7H5V9H3Z" fill="#7B7E81"/><path d="M19 7V9H21V7H19Z" fill="#7B7E81"/><path d="M7 3H9V5H7V3Z" fill="#7B7E81"/><path d="M9 19H7V21H9V19Z" fill="#7B7E81"/><path d="M3 17V15H5V17H3Z" fill="#7B7E81"/><path d="M19 15V17H21V15H19Z" fill="#7B7E81"/><path d="M11 3H13V5H11V3Z" fill="#7B7E81"/><path d="M19 11V13H21V11H19Z" fill="#7B7E81"/><path d="M11 19H13V21H11V19Z" fill="#7B7E81"/><path d="M3 11V13H5V11H3Z" fill="#7B7E81"/></svg>
									<div class="jet-search-suggestions__type-icon-tip"><?php
										_e( 'Unassigned Suggestion', 'jet-search' );
									?></div>
								</span>
								<span
									v-if="column === 'actions'"
									class="jet-search-suggestions-actions"
								>
									<cx-vui-button
										button-style="link-accent"
										size="link"
										@click="callPopup( 'update', item )"
									>
										<span slot="label"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.5 12.375V15.5H3.625L12.8417 6.28333L9.71667 3.15833L0.5 12.375ZM2.93333 13.8333H2.16667V13.0667L9.71667 5.51667L10.4833 6.28333L2.93333 13.8333ZM15.2583 2.69167L13.3083 0.741667C13.1417 0.575 12.9333 0.5 12.7167 0.5C12.5 0.5 12.2917 0.583333 12.1333 0.741667L10.6083 2.26667L13.7333 5.39167L15.2583 3.86667C15.5833 3.54167 15.5833 3.01667 15.2583 2.69167Z" fill="#007CBA"/></svg></span>
									</cx-vui-button>
									<cx-vui-button
										button-style="link-error"
										size="link"
										@click="callPopup( 'delete', item )"
									>
										<span slot="label"><svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.999998 13.8333C0.999998 14.75 1.75 15.5 2.66666 15.5H9.33333C10.25 15.5 11 14.75 11 13.8333V3.83333H0.999998V13.8333ZM2.66666 5.5H9.33333V13.8333H2.66666V5.5ZM8.91667 1.33333L8.08333 0.5H3.91666L3.08333 1.33333H0.166664V3H11.8333V1.33333H8.91667Z" fill="#D6336C"/></svg></span>
									</cx-vui-button>
								</span>
							</div>
						</div>
					</template>
				</template>
				<div
					v-if="isLoading"
					class="jet-search-suggestions-main-notice"
					slot="footer"
				>
					<div class="jet-search-suggestions_preloader">
						<svg viewBox="25 25 50 50">
							<circle cx="50"
									cy="50"
									r="20"
									fill="none"
									stroke-width="4"
									stroke-miterlimit="10" />
						</svg>
					</div>
				</div>
			</cx-vui-list-table>
			<jet-search-suggestions-pagination :offset="offset" :totalItems="totalItems" :perPage="perPage" :pageNumber="pageNumber" :onPage="onPage" @changePage="changePage" @changePerPage="changePerPage"></jet-search-suggestions-pagination>
		</div>

		<jet-search-suggestions-popup :state="popUpState" :popUpContent="popUpContent" :popUpShow="popUpShow" @cancelPopup="cancelPopup" @popUpActions="popUpActions" :parentsList="parentsList" :nameError="nameError"></jet-search-suggestions-popup>
	</div>
</div>

