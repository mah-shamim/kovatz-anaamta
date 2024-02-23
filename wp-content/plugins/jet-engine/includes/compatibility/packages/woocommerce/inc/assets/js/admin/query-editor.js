(function( $ ) {

	'use strict';

	Vue.component( 'jet-wc-product-query', {
		template: '#jet-wc-product-query',
		mixins: [
			window.JetQueryWatcherMixin,
			window.JetQueryDateParamsMixin,
			window.JetQueryRepeaterMixin,
			window.JetQueryTaxParamsMixin,
			window.JetQueryMetaParamsMixin,
			window.JetQueryTabInUseMixin
		],
		props: [ 'value', 'dynamic-value' ],
		data: function() {
			return {
				postStatuses: window.jet_query_component_wc_product_query.posts_statuses,
				productTypes: window.jet_query_component_wc_product_query.product_types,
				productTags: window.jet_query_component_wc_product_query.product_tag,
				productCategories: window.jet_query_component_wc_product_query.product_cat,
				operators: window.JetEngineQueryConfig.operators_list,
				dataTypes: window.JetEngineQueryConfig.data_types,
				taxonomies: window.JetEngineQueryConfig.taxonomies,
				query: {},
				dynamicQuery: {}
			};
		},
		computed: {
			dateOperators: function() {
				return this.operators.filter( function( item ) {
					const disallowed = [ '!=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'NOT BETWEEN', 'EXISTS', 'NOT EXISTS' ];
					return ! disallowed.includes( item.value );
				} );
			}
		},
		created: function() {

			this.query        = { ...this.value };
			this.dynamicQuery = { ...this.dynamicValue };

			if ( ! this.query.specific_query ) {
				this.$set( this.query, 'specific_query', [] );
			}

			if ( ! this.query.date_query ) {
				this.$set( this.query, 'date_query', [] );
			}

			if ( ! this.query.paginate ) {
				this.$set( this.query, 'paginate', true );
			}

			this.presetDate();
			this.presetMeta();
			this.presetTax();

		}
	} );

})( jQuery );
