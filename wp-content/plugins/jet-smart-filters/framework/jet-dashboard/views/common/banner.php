<div
	:id="config.id"
	:class="classes"
	v-if="visible"
>
	<div class="banner-frame">
		<div
			class="banner-inner"
			v-if="'custom' !== preset"
		>
			<div
				class="banner-label"
				v-if="label"
				v-html="label"
			></div>
			<div class="banner-title"
				v-if="title"
			>
				<span v-html="title"></span>
			</div>
			<div
				class="banner-content"
				v-if="content"
				v-html="content"
			></div>
			<div
				class="banner-buttons"
				v-if="buttons"
			>
				<cx-vui-button
					v-for="(button, index) in buttons"
					:key="`button-${index}`"
					class="cx-vui-button--style-accent"
					button-style="default"
					size="mini"
					:url="generateUrmLik( button )"
					tag-name="a"
					target="_blank"
				>
					<span slot="label">
						<span>{{ button.label }}</span>
					</span>
				</cx-vui-button>
			</div>
		</div>
		<div
			class="banner-inner"
			v-if="'custom' === preset"
			v-html="customHtml"
		></div>
	</div>
</div>
