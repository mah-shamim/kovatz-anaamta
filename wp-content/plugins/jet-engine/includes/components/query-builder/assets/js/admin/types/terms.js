(function( $ ) {

	'use strict';

	Vue.component( 'jet-terms-query', {
		template: '#jet-terms-query',
		mixins: [
			window.JetQueryWatcherMixin,
			window.JetQueryRepeaterMixin,
			window.JetQueryMetaParamsMixin,
			window.JetQueryTabInUseMixin,
		],
		props: [ 'value', 'dynamic-value' ],
		data: function() {
			return {
				taxonomies: window.JetEngineQueryConfig.taxonomies,
				operators: window.JetEngineQueryConfig.operators_list,
				dataTypes: window.JetEngineQueryConfig.data_types,
				orderbyOptions: window.JetEngineQueryConfig.orderby_options.terms,
				query: {},
				dynamicQuery: {},
			};
		},
		computed: {
			metaClauses: function() {

				let result = [];

				for ( var i = 0; i < this.query.meta_query.length; i++ ) {
					if ( this.query.meta_query[ i ].clause_name ) {
						result.push( {
							value: this.query.meta_query[ i ].clause_name,
							label: this.query.meta_query[ i ].clause_name,
						} )
					}
				}

				return result;
			},
		},
		created: function() {

			this.query = { ...this.value };
			this.dynamicQuery = { ...this.dynamicValue };

			this.presetMeta();

			// if ( undefined === this.query.hide_empty ) {
			// 	this.$set( this.query, 'hide_empty', true );
			// }

		},
	} );

})( jQuery );
