<div :class="[ 'jet-search-suggestions-popup', 'jet-search-suggestions-popup-' + popUpState ]">
	<cx-vui-popup
		v-model="isShow"
		:body-width="popupWidth()"
		:footer="false"
		@on-cancel="cancelPopup"
		class="jet-search-suggestions-popup-wrapper"
	>
		<div class="cx-vui-subtitle" slot="title">
			<template v-if="popUpState === 'delete'">
				<?php esc_html_e('Are you sure? Deleted suggestion can\'t be restored.', 'jet-search'); ?>
			</template>
			<template v-else-if="popUpState === 'update'">
				<?php esc_html_e('Edit Suggestion:', 'jet-search'); ?>
			</template>
			<template v-else-if="popUpState === 'new'">
				<?php esc_html_e('Add New Suggestion:', 'jet-search'); ?>
			</template>

			<div class="cx-vui-subtitle-id" v-if="popUpState === 'update'">
				{{ getItemLabel( 'id' ) }} {{ content.id }}
			</div>
		</div>

		<div :class="contentClass()" slot="content">

			<div class="jet-search-suggestions-details-fields">
				<template v-if="popUpState === 'new' || popUpState === 'update'">

					<template v-for="item in columns">
						<div v-if="beVisible( item )"
							:key="item"
							:class="[ 'jet-search-suggestions-details__item', 'jet-search-suggestions-details__item-' + item ]"
						>
							<div class="jet-search-suggestions-details__label">{{ getItemLabel( item ) }}:</div>
							<div class="jet-search-suggestions-details__content">
								<cx-vui-input
									v-if="fieldType( item, 'input' )"
									:value="content[ item ]"
									:placeholder="placeholder[ item ]"
									:maxlength="120"
									:error="inputNameError"
									@input="changeValue( $event, item )"
								></cx-vui-input>
								<cx-vui-input
									v-if="fieldType( item, 'number' )"
									type="number"
									:value="content[ item ]"
									@input-validation="validationHandler"
									@on-blur-validation="blurValidationHandler"
									:min="1"
									:max="999999"
									:step="1"
								></cx-vui-input>
								<cx-vui-f-select
									v-else-if="fieldType( item, 'f-select' )"
									name="parents_list"
									:placeholder="placeholder[ item ]"
									:wrapper-css="[ 'equalwidth' ]"
									size="fullwidth"
									:multiple="false"
									:selected-label-inside="true"
									:remote="true"
									:remote-callback="getOptionList"
									:remote-trigger="2"
									:value="contentParents"
									@selected-options="selectedOptionsHandler"
									@input="changeValue( $event, item )"
									@query-change="queryChange"
									@on-blur-query="blurInputQueryHandler"
									:notFoundMeassge="notFoundMessage"
								></cx-vui-f-select>
							</div>
						</div>
					</template>
				</template>
			</div>
			<div class="jet-search-suggestions-popup-actions">
				<template v-if="popUpState === 'new'">
					<cx-vui-button
						class="jet-search-suggestions-popup-button-cancel"
						@click="cancelPopup()"
						button-style="accent-border"
						size="mini"
					>
						<template slot="label"><?php esc_html_e('Cancel', 'jet-search'); ?></template>
					</cx-vui-button>

					<cx-vui-button
						class="jet-search-suggestions-popup-button-add-new"
						@click="addNewItem()"
						button-style="accent"
						size="mini"
						:disabled="addButtonDisabled"
					>
						<template slot="label"><?php esc_html_e('Add New', 'jet-search'); ?></template>
					</cx-vui-button>
				</template>
				<template v-else-if="popUpState === 'update'">
					<cx-vui-button
						class="jet-search-suggestions-popup-button-cancel"
						@click="cancelPopup()"
						button-style="accent-border"
						size="mini"
					>
						<template slot="label"><?php esc_html_e('Cancel', 'jet-search'); ?></template>
					</cx-vui-button>

					<cx-vui-button
						class="jet-search-suggestions-popup-button-save"
						@click="updateItem()"
						button-style="accent"
						size="mini"
						:disabled="updateButtonDisabled"
					>
						<template slot="label"><?php esc_html_e('Save', 'jet-search'); ?></template>
					</cx-vui-button>
				</template>
				<template v-else-if="popUpState === 'delete'">
					<cx-vui-button
						class="jet-search-suggestions-popup-button-cancel"
						@click="cancelPopup()"
						button-style="accent-border"
						size="mini"
					>
						<template slot="label"><?php esc_html_e('Cancel', 'jet-search'); ?></template>
					</cx-vui-button>

					<cx-vui-button
						class="jet-search-suggestions-popup-button-delete"
						@click="deleteItem()"
						button-style="accent"
						size="mini"
					>
						<template slot="label">
							<svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.999959 13.8333C0.999959 14.75 1.74996 15.5 2.66663 15.5H9.33329C10.25 15.5 11 14.75 11 13.8333V3.83333H0.999959V13.8333ZM2.66663 5.5H9.33329V13.8333H2.66663V5.5ZM8.91663 1.33333L8.08329 0.5H3.91663L3.08329 1.33333H0.166626V3H11.8333V1.33333H8.91663Z" fill="#007CBA"/></svg>
							<?php esc_html_e('Delete', 'jet-search'); ?>
						</template>
					</cx-vui-button>
				</template>
			</div>
		</div>
	</cx-vui-popup>
</div>
