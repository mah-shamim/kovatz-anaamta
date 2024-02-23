(function( $ ) {

	'use strict';

	Vue.component( 'jet-users-query', {
		template: '#jet-users-query',
		mixins: [
			window.JetQueryWatcherMixin,
			window.JetQueryRepeaterMixin,
			window.JetQueryMetaParamsMixin,
			window.JetQueryDateParamsMixin,
			window.JetQueryTabInUseMixin,
		],
		props: [ 'value', 'dynamic-value' ],
		data: function() {
			return {
				operators: window.JetEngineQueryConfig.operators_list,
				dataTypes: window.JetEngineQueryConfig.data_types,
				rolesList: window.jet_query_component_users.roles,
				orderbyOptions: window.JetEngineQueryConfig.orderby_options.users,
				query: {},
				dynamicQuery: {},
			};
		},
		computed: {
			dateOperators: function() {
				return this.operators.filter( function( item ) {
					const disallowed = [ 'EXISTS', 'NOT EXISTS', 'LIKE', 'NOT LIKE' ];
					return ! disallowed.includes( item.value );
				} );
			},
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

			this.query        = { ...this.value };
			this.dynamicQuery = { ...this.dynamicValue };

			this.presetMeta();
			this.presetDate();

		}
	} );

})( jQuery );
