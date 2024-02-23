<div
	:class="itemClass"
>
	<div
		class="offers-item__logo"
		v-html="logo"
		v-if="logo"
	></div>
	<div class="offers-item__details">
		<div
			class="offers-item__name"
			v-html="name"
			v-if="name"
		></div>
		<p
			class="offers-item__desc"
			v-html="desc"
			v-if="desc"
		></p>
		<cx-vui-button
			v-if="actionConfig"
			button-style="link-accent"
			size="link"
			tag-name="a"
			:url="actionConfig.url"
			target="_blank"
		>
			<span slot="label">
				<span v-html="actionConfig.label"></span>
				<span class="button-dropdown-icon">
					<svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M0.589844 1.41L5.16984 6L0.589844 10.59L1.99984 12L7.99984 6L1.99984 -1.68141e-08L0.589844 1.41Z" fill="#007CBA"/>
					</svg>
				</span>
			</span>
		</cx-vui-button>
	</div>
</div>
