<div
	class="jet-dashboard-page__after-component"
	v-if="visible"
>
	<div class="jet-dashboard-page__banners">
		<jet-dashboard-banner
			v-for="( banner, index ) in bannersList"
			:key="index"
			:config="banner"
		></jet-dashboard-banner>
	</div>
</div>
