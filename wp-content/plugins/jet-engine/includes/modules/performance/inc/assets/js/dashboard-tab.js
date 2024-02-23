Vue.component( 'jet-engine-tab-performance', {
	name: 'jet-engine-tab-performance',
	template: '#jet-engine-tab-performance',
	data: function() {
		return {
			tweaks: { ...window.JetEngineDashboardConfig._config__performance.saved },
			saving: false,
		};
	},
	methods: {
		saveTweaks() {

			this.saving = true;

			jQuery.ajax({
				url: window.ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'jet_engine_dashboard_save_tweaks',
					nonce: window.JetEngineDashboardConfig._nonce,
					tweaks: this.tweaks,
				},
			} ).done( ( response ) => {

				this.saving = false;

				if ( response.success ) {
					this.$CXNotice.add( {
						message: response.data.message,
						type: 'success',
					} );
				} else {
					this.$CXNotice.add( {
						message: response.data.message,
						type: 'error',
					} );
				}

			} ).fail( ( jqXHR, textStatus, errorThrown ) => {

				this.saving = false;

				this.$CXNotice.add( {
					message: jqXHR.statusText,
					type: 'error',
				} );

			} );
		}
	},
} );
