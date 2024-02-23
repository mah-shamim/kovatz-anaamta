(function( $ ) {

	'use strict';

	Vue.component( 'jet-form-builder-query', {
		template: '#jet-form-builder-query',
		mixins: [
			window.JetQueryWatcherMixin,
			window.JetQueryRepeaterMixin,
			window.JetQueryMetaParamsMixin
		],
		props: [ 'value', 'dynamic-value' ],
		data: function() {
			console.log( window.jet_query_component_jet_form_builder_query.forms );
			return {
				formsList: window.jet_query_component_jet_form_builder_query.forms,
				operators: window.JetEngineQueryConfig.operators_list,
				dataTypes: window.JetEngineQueryConfig.data_types,
				query: {},
				dynamicQuery: {}
			};
		},
		created: function() {

			this.query        = { ...this.value };
			this.dynamicQuery = { ...this.dynamicValue };

			if ( ! this.query.date_query ) {
				this.$set( this.query, 'date_query', '' );
			}

			this.presetMeta();

		}
	} );

})( jQuery );
