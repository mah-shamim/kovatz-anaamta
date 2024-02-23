<div
	:class="itemClass"
>
	<div
		class="extras-item__logo"
		v-html="logo"
		v-if="logo"
	></div>
	<div class="extras-item__details">
		<div
			class="extras-item__name"
			v-html="name"
			v-if="name"
		></div>
		<p
			class="extras-item__desc"
			v-html="desc"
			v-if="desc"
		></p>
		<cx-vui-button
			v-if="actionConfig"
			button-style="link-accent"
			size="link"
			tag-name="a"
			:url="actionConfig.url"
			:target="actionConfig.target"
		>
			<span slot="label">
				<span v-html="actionConfig.label"></span>
			</span>
		</cx-vui-button>
	</div>
</div>
