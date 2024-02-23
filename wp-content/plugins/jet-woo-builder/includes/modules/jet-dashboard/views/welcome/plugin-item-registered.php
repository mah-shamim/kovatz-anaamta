<div
	:class="itemClass"
	@mouseover="mouseoverHandle"
	@mouseleave="mouseleaveHandle"
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
					tag-name="a"
					:url="pluginData.docs"
					target="_blank"
					v-if="usefulLinksEmpty"
				>
					<span slot="label">
						<svg class="button-icon" width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M13.458 11.2552L13.458 1.4115C13.458 1.03064 13.1357 0.708374 12.7549 0.708374L3.14551 0.708374C1.59277 0.708374 0.333008 1.96814 0.333008 3.52087L0.333008 12.8959C0.333008 14.4486 1.59277 15.7084 3.14551 15.7084L12.7549 15.7084C13.1357 15.7084 13.458 15.4154 13.458 15.0052L13.458 14.5365C13.458 14.3314 13.3408 14.1263 13.1943 14.0092C13.0479 13.5404 13.0479 12.2513 13.1943 11.8119C13.3408 11.6947 13.458 11.4896 13.458 11.2552ZM4.08301 4.63416C4.08301 4.54626 4.1416 4.45837 4.25879 4.45837L10.4697 4.45837C10.5576 4.45837 10.6455 4.54626 10.6455 4.63416L10.6455 5.22009C10.6455 5.33728 10.5576 5.39587 10.4697 5.39587L4.25879 5.39587C4.1416 5.39587 4.08301 5.33728 4.08301 5.22009L4.08301 4.63416ZM4.08301 6.50916C4.08301 6.42127 4.1416 6.33337 4.25879 6.33337L10.4697 6.33337C10.5576 6.33337 10.6455 6.42127 10.6455 6.50916L10.6455 7.09509C10.6455 7.21228 10.5576 7.27087 10.4697 7.27087L4.25879 7.27087C4.1416 7.27087 4.08301 7.21228 4.08301 7.09509L4.08301 6.50916ZM11.4951 13.8334L3.14551 13.8334C2.61816 13.8334 2.20801 13.4232 2.20801 12.8959C2.20801 12.3978 2.61816 11.9584 3.14551 11.9584L11.4951 11.9584C11.4365 12.4857 11.4365 13.3353 11.4951 13.8334Z" fill="#007CBA"></path>
						</svg>

						<span><?php _e( 'Documentation', 'jet-dashboard' ); ?></span>
					</span>
				</cx-vui-button>

				<cx-vui-button
					button-style="link-accent"
					size="link"
					tag-name="a"
					:url="mainLinkItem.url"
					:target="mainLinkItem.target"
					v-if="!usefulLinksEmpty && !dropdownAvaliable"
				>
					<span slot="label">
						<svg class="button-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M13.9503 8.78C13.9803 8.53 14.0003 8.27 14.0003 8C14.0003 7.73 13.9803 7.47 13.9403 7.22L15.6303 5.9C15.7803 5.78 15.8203 5.56 15.7303 5.39L14.1303 2.62C14.0303 2.44 13.8203 2.38 13.6403 2.44L11.6503 3.24C11.2303 2.92 10.7903 2.66 10.3003 2.46L10.0003 0.34C9.97025 0.14 9.80025 0 9.60025 0H6.40025C6.20025 0 6.04025 0.14 6.01025 0.34L5.71025 2.46C5.22025 2.66 4.77025 2.93 4.36025 3.24L2.37025 2.44C2.19025 2.37 1.98025 2.44 1.88025 2.62L0.280252 5.39C0.180252 5.57 0.220252 5.78 0.380252 5.9L2.07025 7.22C2.03025 7.47 2.00025 7.74 2.00025 8C2.00025 8.26 2.02025 8.53 2.06025 8.78L0.370252 10.1C0.220252 10.22 0.180252 10.44 0.270252 10.61L1.87025 13.38C1.97025 13.56 2.18025 13.62 2.36025 13.56L4.35025 12.76C4.77025 13.08 5.21025 13.34 5.70025 13.54L6.00025 15.66C6.04025 15.86 6.20025 16 6.40025 16H9.60025C9.80025 16 9.97025 15.86 9.99025 15.66L10.2903 13.54C10.7803 13.34 11.2303 13.07 11.6403 12.76L13.6303 13.56C13.8103 13.63 14.0203 13.56 14.1203 13.38L15.7203 10.61C15.8203 10.43 15.7803 10.22 15.6203 10.1L13.9503 8.78ZM8.00025 11C6.35025 11 5.00025 9.65 5.00025 8C5.00025 6.35 6.35025 5 8.00025 5C9.65025 5 11.0003 6.35 11.0003 8C11.0003 9.65 9.65025 11 8.00025 11Z" fill="#007CBA"/>
						</svg>
						<span v-html="mainLinkItem.label"></span>
					</span>
				</cx-vui-button>

				<cx-vui-button
					button-style="link-accent"
					size="link"
					v-if="dropdownAvaliable"
				>
					<span slot="label">
						<span><?php _e( 'Plugin pages', 'jet-dashboard' ); ?></span>
						<span class="button-dropdown-icon">
							<svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M10.59 0.590088L6 5.17009L1.41 0.590087L-1.23266e-07 2.00009L6 8.00009L12 2.00009L10.59 0.590088Z" fill="#007CBA"/>
							</svg>
						</span>
					</span>
				</cx-vui-button>

			</div>
			<transition name="dropdown-menu">
				<div
					class="useful-links"
					v-if="dropdownVisible"
				>
					<a
						class="useful-link"
						v-for="( linkData, index ) in dropdownLinkItems"
						:key="index"
						:href="linkData.url"
						:target="linkData.target"
					>
						<span>{{ linkData.label }}</span>
					</a>
				</div>
			</transition>
		</div>
	</div>
</div>

