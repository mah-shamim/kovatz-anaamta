<div
	class="plugin-item plugin-item--avaliable"
>
	<div class="plugin-item__inner">
		<div class="plugin-tumbnail">
			<img :src="pluginData.thumb">
		</div>
		<div class="plugin-info">
			<div class="plugin-name">
				<span class="plugin-label">{{ pluginData.name }}</span>
			</div>

			<div class="plugin-actions">
				<cx-vui-button
					button-style="link-accent"
					size="link"
					:loading="pluginActionProcessed"
					v-if="installAvaliable"
					@click="installPlugin"
				>
					<span slot="label">
						<svg class="button-icon" width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M11.8337 5.5H8.50033V0.5H3.50033V5.5H0.166992L6.00033 11.3333L11.8337 5.5ZM0.166992 13V14.6667H11.8337V13H0.166992Z" fill="#007CBA"/>
						</svg>
						<span>Install</span>
					</span>
				</cx-vui-button>
			</div>
		</div>
	</div>
</div>

