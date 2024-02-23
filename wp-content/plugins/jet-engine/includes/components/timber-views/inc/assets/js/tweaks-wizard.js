(function( $ ) {

	'use strict';

	Vue.component( 'jet-timber-views-wizard', {
		template: `<div class="jet-timber-views-wizard">
			<div class="jet-timber-views-wizard__desc">
				<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M10.4413 7.39906C10.9421 6.89828 11.1925 6.29734 11.1925 5.59624C11.1925 4.71987 10.8795 3.9687 10.2535 3.34272C9.62754 2.71674 8.87637 2.40376 8 2.40376C7.12363 2.40376 6.37246 2.71674 5.74648 3.34272C5.1205 3.9687 4.80751 4.71987 4.80751 5.59624H6.38498C6.38498 5.17058 6.54773 4.79499 6.87324 4.46948C7.19875 4.14398 7.57434 3.98122 8 3.98122C8.42566 3.98122 8.80125 4.14398 9.12676 4.46948C9.45227 4.79499 9.61502 5.17058 9.61502 5.59624C9.61502 6.02191 9.45227 6.3975 9.12676 6.723L8.15024 7.73709C7.52426 8.41315 7.21127 9.16432 7.21127 9.99061V10.4038H8.78873C8.78873 9.57747 9.10172 8.82629 9.7277 8.15024L10.4413 7.39906ZM8.78873 13.5962V12.0188H7.21127V13.5962H8.78873ZM2.32864 2.3662C3.9061 0.788732 5.79656 0 8 0C10.2034 0 12.0814 0.788732 13.6338 2.3662C15.2113 3.91862 16 5.79656 16 8C16 10.2034 15.2113 12.0939 13.6338 13.6714C12.0814 15.2238 10.2034 16 8 16C5.79656 16 3.9061 15.2238 2.32864 13.6714C0.776213 12.0939 0 10.2034 0 8C0 5.79656 0.776213 3.91862 2.32864 2.3662Z" fill="#007CBA"></path>
				</svg>
				<span>
					{{ description }}
				</span>
			</div>
			<div class="jet-timber-views-wizard__version">
				<b>
					Timber version installed:
					<span :style="versionCSS()">{{ version }}</span>
				</b>
				<div class="jet-timber-views-wizard__sources-title" style="font-weight: normal; font-size: 13px;" v-if="! hasTimber">
					Please select the preferred Timber plugin version:
				</div>
			</div>
			<div class="jet-timber-views-wizard__sources" v-if="! hasTimber">
				<div 
					class="jet-timber-views-wizard__source"
					:style="sourceCSS()"
					v-for="(source, index) in sources"
					@click="installSource( index )"
				>
					<div class="jet-timber-views-wizard__source-name">Install {{ source.name }}</div>
					<div class="jet-timber-views-wizard__source-desc">{{ source.description }}</div>
				</div>
			</div>
			<div class="jet-timber-views-wizard__error" v-if="error">{{ error }}</div>
		</div>`,
		data() {
			return {
				sources: window.JetEngineTimberViewsWizard.sources,
				hasTimber: window.JetEngineTimberViewsWizard.has_timber,
				version: window.JetEngineTimberViewsWizard.version,
				description: window.JetEngineTimberViewsWizard.description,
				nonce: window.JetEngineTimberViewsWizard.nonce,
				installing: false,
				error: null,
			};
		},
		methods: {
			versionCSS() {
				
				let styles = {};

				if ( 'not found' === this.version ) {
					styles.color = '#C92C2C';
				} else {
					styles.color = '#46B450';
				}

				return styles;

			},
			sourceCSS() {

				let styles = {};

				if ( this.installing ) {
					styles.pointerEvents = 'none';
					styles.opacity = '0.7';
				}

				return styles;

			},
			installSource( sourceIndex ) {

				this.error = null;
				this.installing = true;

				jQuery.ajax( {
					url: window.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_engine_timber_views_install_source',
						source: sourceIndex,
						nonce: window.JetEngineTimberViewsWizard.nonce,
					},
				} ).done( ( response ) => {
					
					if ( ! response.success ) {
						this.error = response.data;
					} else {
						this.version = response.data.version;
						this.hasTimber = true;
					}

					this.installing = false;

				} ).fail( ( jqXHR, textStatus, errorThrown ) => {
					this.error = errorThrown;
					this.installing = false;
				} );

			}
		}

	} );

})( jQuery );
