<div class="plugin-item plugin-item--more">
	<div class="plugin-item__inner">
		<div class="plugin-tumbnail">
			<img :src="pluginData.thumb_alt">
		</div>
		<div class="plugin-info">

			<p class="plugin-desc">{{ pluginData.desc }}</p>

			<div class="plugin-actions">
				<cx-vui-button
					class="cx-vui-button--style-accent"
					button-style="default"
					size="mini"
					:url="demoLink"
					tag-name="a"
					target="_blank"
				>
					<span slot="label">
						<span>Get Plugin</span>
					</span>
				</cx-vui-button>
			</div>
		</div>
	</div>
</div>

