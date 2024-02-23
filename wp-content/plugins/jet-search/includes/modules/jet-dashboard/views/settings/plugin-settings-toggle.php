<div
	class="plugin-settings-toggle"
>
	<div
		class="plugin-settings-toggle__header"
		@click="toggleHandler( config.slug )"
	>
		<span class="plugin-settings-toggle__header-label" v-html="config.name"></span>
		<span
			class="plugin-settings-toggle__header-marker"
			v-if="islinksEmpty"
		>
			<svg v-if="!linksVisible" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M1.41 0.589996L6 5.17L10.59 0.589996L12 2L6 8L0 2L1.41 0.589996Z" fill="#7B7E81"/>
			</svg>
			<svg v-if="linksVisible" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M1.41 7.41L6 2.83L10.59 7.41L12 6L6 3.8147e-06L0 6L1.41 7.41Z" fill="#7B7E81"/>
			</svg>
		</span>
	</div>
	<div
		class="plugin-settings-toggle__links"
		v-if="linksVisible"
	>
		<div
			class="plugin-settings-toggle__link"
			:class="{ active: subPageModule === module.page }"
			v-for="(module, index) in config.moduleList"
			:key="index"
		>
			<span
				v-html="module.name"
				@click="navigateHandler( module.link, module.page, config.slug )"
			></span>
		</div>
	</div>

</div>
