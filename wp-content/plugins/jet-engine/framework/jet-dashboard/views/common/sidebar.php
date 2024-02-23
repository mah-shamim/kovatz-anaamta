<div
	class="jet-dashboard-page__sidebar"
	v-if="visible"
>
	<div class="jet-dashboard-page__guide">
		<div class="jet-dashboard-page__guide-videos">
			<div
				class="jet-dashboard-page__guide-video"
				v-for="(videoData, index) in guideVideos"
				:key="index"
				:style="{ backgroundImage: `url(${ videoData.preview })` }"
				@click="openVideoPopupHandler( videoData.embed )"
			>
				<svg width="27" height="36" viewBox="0 0 27 36" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M0 33.1265C0 34.7329 1.79827 35.6838 3.12595 34.7794L25.5038 19.536C26.6692 18.7422 26.6692 17.024 25.5038 16.2301L3.12595 0.98676C1.79827 0.0823681 0 1.03326 0 2.6397V33.1265Z" fill="white" fill-opacity="0.6"/>
				</svg>
			</div>
		</div>

		<div class="jet-dashboard-page__guide-content">
			<div class="jet-dashboard-page__panel-header">
				<div class="panel-header-icon">
					<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M17.7193 0.701754C17.6491 0.491228 17.4737 0.315789 17.2632 0.245614C16.1404 0 15.2632 0 14.386 0C10.7719 0 8.59649 1.96491 6.98246 4.49123H3.29825C2.73684 4.52632 2.07018 4.91228 1.82456 5.4386L0.0701754 8.91228C0.0350877 9.01754 0 9.1579 0 9.26316C0 9.75439 0.350877 10.1053 0.842105 10.1053H4.45614L3.68421 10.9123C3.50877 11.0877 3.36842 11.4386 3.36842 11.7193C3.36842 11.9649 3.50877 12.3158 3.68421 12.4912L5.47368 14.2807C5.64912 14.4561 6 14.6316 6.24561 14.6316C6.52632 14.6316 6.87719 14.4561 7.05263 14.2807L7.85965 13.5088V17.1228V17.1579C7.85965 17.614 8.21053 18 8.70175 18C8.80702 18 8.94737 17.9298 9.05263 17.8947L12.5263 16.1754C13.0526 15.8947 13.4386 15.2281 13.4386 14.6667V10.9825C16 9.36842 17.9298 7.19298 17.9298 3.57895C17.9649 2.70175 17.9649 1.82456 17.7193 0.701754ZM13.4737 5.89474C12.6667 5.89474 12.0702 5.29825 12.0702 4.49123C12.0702 3.7193 12.6667 3.08772 13.4737 3.08772C14.2456 3.08772 14.8772 3.7193 14.8772 4.49123C14.8772 5.29825 14.2456 5.89474 13.4737 5.89474Z" fill="#7B7E81"/>
					</svg>
				</div>
				<div class="panel-header-content">
					<span class="panel-header-desc"><?php _e( 'New to Crocoblock?', 'jet-dashboard' ); ?></span>
					<div class="panel-header-title"><?php _e( 'Start Here', 'jet-dashboard' ); ?></div>
				</div>
			</div>
			<div class="jet-dashboard-page__panel-content jet-dashboard-page__guide-links">
				<div
					class="jet-dashboard-page__guide-link"
					v-for="( linkData, index ) in guideLinks"
					:key="index"
				>
					<a :href="linkData.link" target="_blank">
						<span>{{ linkData.label }}</span>
						<svg v-if="'youtube' === linkData.type" width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M19.5052 2.22396C19.2865 1.34896 18.5937 0.65625 17.7552 0.4375C16.1875 0 9.98958 0 9.98958 0C9.98958 0 3.75521 0 2.1875 0.4375C1.34896 0.65625 0.65625 1.34896 0.4375 2.22396C0 3.75521 0 7.03646 0 7.03646C0 7.03646 0 10.2812 0.4375 11.849C0.65625 12.724 1.34896 13.3802 2.1875 13.599C3.75521 14 9.98958 14 9.98958 14C9.98958 14 16.1875 14 17.7552 13.599C18.5937 13.3802 19.2865 12.724 19.5052 11.849C19.9427 10.2812 19.9427 7.03646 19.9427 7.03646C19.9427 7.03646 19.9427 3.75521 19.5052 2.22396ZM7.94792 9.98958V4.08333L13.125 7.03646L7.94792 9.98958Z" fill="url(#youtybe_icon_linear)"/>
							<defs>
								<linearGradient id="youtybe_icon_linear" x1="-0.510416" y1="-7.2279e-08" x2="19.4896" y2="14" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FF0000"/>
									<stop offset="1" stop-color="#D90000"/>
								</linearGradient>
							</defs>
						</svg>
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="jet-dashboard-page__help-center jet-dashboard-page__panel">
		<div class="jet-dashboard-page__panel-header">
			<div class="panel-header-icon">
				<svg width="14" height="21" viewBox="0 0 14 21" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5.25 21H8.75V17.5H5.25V21ZM7 0C3.1325 0 0 3.1325 0 7H3.5C3.5 5.075 5.075 3.5 7 3.5C8.925 3.5 10.5 5.075 10.5 7C10.5 10.5 5.25 10.0625 5.25 15.75H8.75C8.75 11.8125 14 11.375 14 7C14 3.1325 10.8675 0 7 0Z" fill="#7B7E81"/>
				</svg>
			</div>
			<div class="panel-header-content">
				<span class="panel-header-desc"><?php _e( 'Support & Info', 'jet-dashboard' ); ?></span>
				<div class="panel-header-title"><?php _e( 'Help Center', 'jet-dashboard' ); ?></div>
			</div>
		</div>
		<div class="jet-dashboard-page__panel-content jet-dashboard-page__help-center-links">
			<div
				class="jet-dashboard-page__help-center-link"
				v-for="( linkData, index ) in helpCenterLinks"
				:key="index"
			>
				<a :href="linkData.link" target="_blank">
					<div class="help-center-link-icon" v-html="linkData.icon"></div>
					<div class="help-center-link-label">{{ linkData.label }}</div>
				</a>
			</div>
		</div>
	</div>

	<transition name="popup">
		<cx-vui-popup
			class="video-guide-popup"
			v-model="videoPopupEnable"
			:header="false"
			:footer="false"
			body-width="800px"
		>
			<div slot="content" v-html="videoEmbed"></div>
		</cx-vui-popup>
	</transition>

</div>
