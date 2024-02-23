<div
	class="plugin-item plugin-item--installed"
	:class="{ 'update-avaliable': updateAvaliable, 'activate-avaliable': activateAvaliable }"
>
	<div
		class="plugin-item__inner"
		:class="{ 'proccesing-state': proccesingState }"
	>
		<div class="plugin-tumbnail">
			<img
				v-if="!activateAvaliable"
				:src="pluginData.thumb"
			>
			<svg v-if="activateAvaliable" width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M77 0H3C1.34315 0 0 1.34315 0 3V77C0 78.6569 1.34315 80 3 80H77C78.6569 80 80 78.6569 80 77V3C80 1.34315 78.6569 0 77 0Z" fill="#DFDFDF"/>
				<path fill-rule="evenodd" clip-rule="evenodd" d="M69.5021 22.9808C70.4547 22.922 70.9981 24.1607 70.3652 24.9481L64.7315 31.9568C64.0718 32.7776 62.8423 32.2529 62.8552 31.1561L62.8887 28.3124C62.8931 27.9398 62.7382 27.5867 62.4706 27.3593L60.4282 25.6235C59.6404 24.954 60.0304 23.565 61.0233 23.5037L69.5021 22.9808ZM24.6754 41.3861C24.6754 48.1001 19.1872 53.5408 12.4241 53.5408C10.7295 53.5408 9.36133 52.1748 9.36133 50.5002C9.36133 48.8255 10.7295 47.4673 12.4241 47.4673C15.8057 47.4673 18.5498 44.7431 18.5498 41.3861V32.2721C18.5498 30.5897 19.9179 29.2315 21.6126 29.2315C23.3072 29.2315 24.6754 30.5897 24.6754 32.2721V41.3861ZM57.6978 41.3861C57.6978 44.7431 60.4419 47.4673 63.8235 47.4673C65.5181 47.4673 66.8863 48.8178 66.8863 50.5002C66.8863 52.1825 65.5181 53.5408 63.8235 53.5408C57.0604 53.5408 51.5722 48.1001 51.5722 41.3861V32.2721C51.5722 30.5897 52.9404 29.2315 54.635 29.2315C56.3297 29.2315 57.6978 30.5974 57.6978 32.2721V34.9962H60.131C61.8256 34.9962 63.2016 36.3622 63.2016 38.0446C63.2016 39.7269 61.8256 41.0929 60.131 41.0929H57.6978V41.3861ZM49.8154 37.1263C49.8232 37.1185 49.831 37.1185 49.831 37.1185C48.8671 34.6027 47.0636 32.3956 44.5449 30.9447C38.6991 27.58 31.2364 29.5711 27.8704 35.3899C24.4967 41.201 26.5023 48.6326 32.3403 51.9896C36.6391 54.4592 41.8164 54.027 45.5944 51.3105L45.571 51.2797C46.4417 50.7472 47.0169 49.7902 47.0169 48.7021C47.0169 47.0275 45.6488 45.6692 43.9619 45.6692C43.1457 45.6692 42.3994 45.9856 41.8552 46.5104C40.0207 47.7374 37.5797 47.9304 35.5197 46.8037L47.8565 41.1161C48.5717 40.9 49.2091 40.4215 49.6056 39.727C50.0875 38.9012 50.1342 37.952 49.8154 37.1263ZM41.4899 36.2002C41.8552 36.4085 42.1817 36.6478 42.4849 36.9102L32.3325 41.5791C32.3092 40.5064 32.5735 39.4183 33.1488 38.4227C34.8356 35.5211 38.567 34.5255 41.4899 36.2002Z" fill="white"/>
			</svg>
		</div>
		<div class="plugin-info">
			<div class="plugin-name">
				<span class="plugin-label">{{ pluginData.name }}</span>
				<span class="plugin-version">{{ pluginData.currentVersion }}</span>
				<span
					class="plugin-rollback"
					v-if="versionRollbackAvaliable"
				>
					<cx-vui-button
						button-style="link-accent"
						size="link"
						@click="showRollbackPopup"
					>
						<span slot="label">
							<span>Change Version</span>
						</span>
					</cx-vui-button>
				</span>
			</div>
			<div
				class="plugin-update-label"
			>
				<div v-if="!updateAvaliable">Your plugin is up to date</div>
				<div v-if="updateAvaliable">
					Version <span class="latest-version">{{pluginData.version}}</span> available
					<cx-vui-button
						button-style="link-accent"
						size="link"
						:loading="updatePluginProcessed"
						@click="updatePlugin"
					>
						<span slot="label">
							<span>Update Now</span>
						</span>
					</cx-vui-button>
				</div>
			</div>
			<div class="plugin-actions">
				<cx-vui-button
					class="cx-vui-button--style-accent"
					button-style="default"
					size="mini"
					@click="showPopupActivation"
					v-if="activateLicenseVisible"
				>
					<span slot="label">
						<span>Activate License</span>
					</span>
				</cx-vui-button>
				<cx-vui-button
					class="cx-vui-button--style-danger"
					button-style="default"
					size="mini"
					:loading="licenseActionProcessed"
					@click="deactivateLicense"
					v-if="deactivateLicenseVisible"
				>
					<span slot="label">
						<span>Deactivate License</span>
					</span>
				</cx-vui-button>
				<cx-vui-button
					class="cx-vui-button--style-default"
					button-style="default"
					size="mini"
					:loading="actionPluginProcessed"
					v-if="activateAvaliable"
					@click="activatePlugin"
				>
					<span slot="label">
						<span>Activate Plugin</span>
					</span>
				</cx-vui-button>
				<cx-vui-button
					class="cx-vui-button--style-default"
					button-style="default"
					size="mini"
					:loading="actionPluginProcessed"
					v-if="deactivateAvaliable"
					@click="deactivatePlugin"
				>
					<span slot="label">
						<span>Deactivate Plugin</span>
					</span>
				</cx-vui-button>
			</div>
		</div>
	</div>
	<transition name="popup">
		<cx-vui-popup
			class="rollback-popup"
			v-model="rollbackPopupVisible"
			:footer="false"
			body-width="450px"
		>
			<div class="cx-vui-popup__header-inner" slot="title">
				<div class="cx-vui-popup__header-label">{{ pluginData.name }} version rollback</div>
			</div>
			<div slot="content">
				<p><i>Warning: Please backup your database before making the rollback.</i></p>
				<cx-vui-select
					name="rollback-version"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					:prevent-wrap="true"
					:options-list="rollbackOptions"
					v-model="rollbackVersion"
				>
				</cx-vui-select>
				<cx-vui-button
					button-style="accent"
					size="mini"
					v-if="rollbackButtonVisible"
					:loading="rollbackPluginProcessed"
					@click="rollbackPluginVersion"
				>
					<span slot="label">
						<span>Reinstall version {{ rollbackVersion }}</span>
					</span>
				</cx-vui-button>
			</div>
		</cx-vui-popup>
	</transition>
</div>

