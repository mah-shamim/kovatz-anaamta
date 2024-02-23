Vue.component( 'jet-query-ai-popup', {
	name: 'jet-query-ai-popup',
	template: '#jet-query-ai-popup',
	//directives: { clickOutside: window.JetVueUIClickOutside },
	props: [ 'value' ],
	data: function() {
		return {
			isAllowed: window.JetEngineQueryAIPopup.is_allowed,
			hasLicense: window.JetEngineQueryAIPopup.has_license,
			limit: window.JetEngineQueryAIPopup.limit,
			isActive: false,
			prompt: '',
			loading: false,
			snippets: window.JetEngineQueryAIPopup.snippets,
			mode: 'request',
			error: null,
			completion: '',
			usage: null,
			rows: 4,
		};
	},
	created: function() {
	},
	methods: {
		switchIsActive: function() {

			if ( this.isActive ) {
				this.loading = false;
				this.error   = null;
				this.switchToRequest();
			}

			this.isActive = ! this.isActive;

		},
		generateQuery: function() {

			this.loading = true;
			this.error   = null;

			jQuery.ajax({
				url: window.ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: window.JetEngineQueryAIPopup.action,
					nonce: window.JetEngineQueryAIPopup.nonce,
					source: 'sql',
					prompt: this.prompt,
				},
			}).done( ( response ) => {

				this.loading = false;

				if ( response.success ) {
					this.mode = 'completion';
					this.completion = response.data.completion;
					this.usage = response.data.requests_used + '/' + response.data.requests_limit;
					this.rows = response.data.rows + 1;
				} else {
					this.error = response.data;
				}

			} ).fail( ( xhr, textStatus, error ) => {
				this.loading = false;
				this.error = error;
			} );

		},
		useQuery: function() {
			this.$emit( 'input', this.completion );
			this.switchIsActive();
		},
		switchToRequest: function() {
			this.completion = '';
			this.mode = 'request';
			this.usage = null;
			this.rows = 4;
		},
		onClickOutside: function() {
			this.switchIsActive();
		},
	},
} );
