<div
	class="jet-dashboard-page__alerts"
	v-if="visible"
>
	<jet-dashboard-alert-item
		v-for="( alertConfig, index ) in alertList"
		:key="index"
		:config="alertConfig"
	></jet-dashboard-alert-item>
</div>
