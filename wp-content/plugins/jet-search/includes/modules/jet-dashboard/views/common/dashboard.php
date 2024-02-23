<?php
/**
 * Main dashboard template
 */
?><div
	id="jet-dashboard-page"
	class="jet-dashboard-page"
	ref="jetDashboardPage"
>

	<div class="jet-dashboard-page__body">

		<jet-dashboard-alert-list
			:alert-list="alertNoticeList"
		></jet-dashboard-alert-list>

		<jet-dashboard-before-content
			:config="beforeContentConfig"
		></jet-dashboard-before-content>

		<div class="jet-dashboard-page__content">

			<jet-dashboard-header
				:config="headerConfig"
			></jet-dashboard-header>

			<div class="jet-dashboard-page__component">

				<jet-dashboard-before-component
					:config="beforeComponentConfig"
				></jet-dashboard-before-component>

				<component
					:is="pageModule"
					:subpage="subPageModule"
				></component>

				<jet-dashboard-after-component
					:config="afterComponentConfig"
				></jet-dashboard-after-component>

			</div>

		</div>

		<div
			class="jet-dashboard-page__sidebar-container"
			v-if="sidebarVisible"
		>

			<jet-dashboard-before-sidebar
				:config="beforeSidebarConfig"
			></jet-dashboard-before-sidebar>

			<jet-dashboard-sidebar
				:config="sidebarConfig"
				:guide="guideConfig"
				:help-center="helpCenterConfig"
			></jet-dashboard-sidebar>

			<jet-dashboard-after-sidebar
				:config="afterSidebarConfig"
			></jet-dashboard-after-sidebar>

		</div>

	</div>

	<transition name="popup">
		<cx-vui-popup
			class="service-actions-popup"
			v-model="serviceActionsVisible"
			:footer="false"
			body-width="400px"
		>
			<div slot="title">
				<div class="cx-vui-popup__header-label">Service Actions</div>
			</div>
			<div class="service-actions-popup__form" slot="content">
				<cx-vui-select
					size="fullwidth"
					placeholder="Choose Action"
					:prevent-wrap="true"
					:options-list="serviceActionOptions"
					v-model="serviceAction"
				></cx-vui-select>
				<cx-vui-button
					button-style="accent"
					size="mini"
					:loading="serviceActionProcessed"
					@click="executeServiceAction"
				>
					<span slot="label">Go</span>
				</cx-vui-button>
			</div>
		</cx-vui-popup>
	</transition>

</div>
