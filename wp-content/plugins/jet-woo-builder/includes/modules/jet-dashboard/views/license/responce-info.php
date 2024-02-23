<div
	class="responce-data-popup__inner"
	:class="[ 'responce-type-' + type ]"
>
	<div class="responce-data-popup__header">
		<div class="responce-data-popup__title">
			{{ title }}
		</div>
	</div>
	<div class="responce-data-popup__content">
		<div v-if="code === 'limit_exceeded'">
			<p>You have reached the limit of sites available for license activation. Your activation limit is <span class="activation-limit">{{ activationLimit }}</span></p>
			<div class="responce-data-popup__activated-sites">
				<div class="site-list">
					<p>You have already activated current license on these sites:</p>
					<div
						class="site-item"
						v-for="( site, index ) in activatedSites"
					>
						<a :href="'https://' + site">{{ site }}</a>
					</div>
				</div>
			</div>
			<p>If you want to activate a license on this site, you should deactivate the license on the sites where the license has been already activated.</p>
			<p>Also, we provide an upgrade option that will allow you to get all Jet Plugins for unlimited websites. <a href="https://crocoblock.com/pricing/?utm_source=jet-dashboard&utm_medium=activation-limit-exceeded&utm_campaign=update-license-link" target="_blank">Update Now</a></p>
		</div>
	</div>
</div>
