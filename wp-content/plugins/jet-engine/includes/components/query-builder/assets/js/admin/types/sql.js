(function( $ ) {

	'use strict';

	Vue.component( 'jet-sql-query', {
		template: '#jet-sql-query',
		mixins: [
			window.JetQueryWatcherMixin,
			window.JetQueryRepeaterMixin,
		],
		props: [ 'value', 'dynamic-value' ],
		data: function() {
			return {
				tablesList: window.jet_query_component_sql.tables,
				castObjectsList: window.jet_query_component_sql.cast_objects,
				operators: window.JetEngineQueryConfig.operators_list,
				dataTypes: window.JetEngineQueryConfig.data_types,
				query: {},
				dynamicQuery: {},
			};
		},
		created: function() {

			this.query = { ...this.value };
			this.dynamicQuery = { ...this.dynamicValue };

			this.presetJoin();
			this.presetWhere();
			this.presetOrder();
			this.presetCols()
		},
		computed: {
			columnSchema: function() {
				
				var result = [];

				if ( this.query.table ) {
					
					let columns = this.getColumns( this.query.table );
					
					result.push( {
						table: this.query.table,
						columns: [ ...columns ],
					} );

				}

				if ( this.query.use_join && this.query.join_tables.length ) {

					let processedTables = { [ this.query.table ]: 1 };

					for ( var i = 0; i < this.query.join_tables.length; i++ ) {

						let joinTable = this.query.join_tables[ i ].table;
						let preparedJoinTable = joinTable;

						if ( joinTable && processedTables[ joinTable ] ) {
							processedTables[ joinTable ]++;
							preparedJoinTable = joinTable + processedTables[ joinTable ];
						} else if ( joinTable ) {
							processedTables[ joinTable ] = 1;
						}

						if ( preparedJoinTable ) {

							let joinColumns = this.getColumns( joinTable );
							let preparedColumns = [];
							
							joinColumns = [ ...joinColumns ];

							for ( var j = 0; j < joinColumns.length; j++ ) {
								preparedColumns.push( {
									value: preparedJoinTable + '.' + joinColumns[ j ].value,
									label: preparedJoinTable + '.' + joinColumns[ j ].label,
								} )
							}

							result.push( {
								table: preparedJoinTable,
								columns: preparedColumns,
							} );
						}

					}

				}

				return result;

			},
			availableColumns: function() {

				var result = [];
				var schema = JSON.parse( JSON.stringify( this.columnSchema ) );

				for ( var i = 0; i < schema.length; i++ ) {

					let addPrefix = false;

					if ( 0 === i && 1 < schema.length ) {
						addPrefix = true;
					}

					for ( var j = 0; j < schema[ i ].columns.length; j++ ) {
						if ( addPrefix ) {
							schema[ i ].columns[ j ].value = schema[ i ].table + '.' + schema[ i ].columns[ j ].value;
							schema[ i ].columns[ j ].label = schema[ i ].table + '.' + schema[ i ].columns[ j ].label;
						}

						result.push( schema[ i ].columns[ j ] );
					}

				}

				return result;

			},
		},
		methods: {
			presetJoin: function() {
				if ( ! this.query.join_tables ) {
					this.$set( this.query, 'join_tables', [] );
				}

				if ( ! this.dynamicQuery.join_tables ) {
					this.$set( this.dynamicQuery, 'join_tables', {} );
				} else if ( 'object' !== typeof this.dynamicQuery.join_tables || undefined !== this.dynamicQuery.join_tables.length ) {
					this.$set( this.dynamicQuery, 'join_tables', {} );
				}
			},
			randID: function() {
				return Math.round( Math.random() * 1000000 )
			},
			newDynamicJoin: function( newClause, metaQuery, prevID ) {

				let newItem = {};

				if ( prevID && this.dynamicQuery.join_tables[ prevID ] ) {
					newItem = { ...this.dynamicQuery.join_tables[ prevID ] };
				}

				this.$set( this.dynamicQuery.join_tables, newClause._id, newItem );

			},
			deleteDynamicJoin: function( id ) {
				this.$delete( this.dynamicQuery.join_tables, id );
			},
			presetWhere: function() {
				if ( ! this.query.where ) {
					this.$set( this.query, 'where', [] );
				}

				if ( ! this.dynamicQuery.where ) {
					this.$set( this.dynamicQuery, 'where', {} );
				} else if ( 'object' !== typeof this.dynamicQuery.where || undefined !== this.dynamicQuery.where.length ) {
					this.$set( this.dynamicQuery, 'where', {} );
				}

				for ( var itemID in this.dynamicQuery.where ) {
					if ( 'object' !== typeof this.dynamicQuery.where[ itemID ] || undefined !== this.dynamicQuery.where[ itemID ].length ) {
						this.$set( this.dynamicQuery.where, itemID, {} );
					}
				}
			},
			presetCols: function() {
				if ( ! this.query.calc_cols ) {
					this.$set( this.query, 'calc_cols', [] );
				}
			},
			newDynamicWhere: function( newClause, metaQuery, prevID ) {

				let newItem = {};

				if ( prevID && this.dynamicQuery.where[ prevID ] ) {
					newItem = { ...this.dynamicQuery.where[ prevID ] };
				}

				this.$set( this.dynamicQuery.where, newClause._id, newItem );

			},
			deleteDynamicWhere: function( id ) {
				this.$delete( this.dynamicQuery.where, id );
			},
			getColumns: function( table ) {
				return window.jet_query_component_sql.columns[ table ] || [];
			},
			getJoinTitle( item, currentIndex ) {
				
				const allColumns = [ ...this.columnSchema ];

				currentIndex++;
				
				for ( var i = 0; i < allColumns.length; i++ ) {

					if ( i === currentIndex ) {
						return allColumns[ i ].table;
					}
					
				}

			},
			getJoinColumns( currentIndex ) {

				const allColumns = [ ...this.columnSchema ];
				const result = [];

				currentIndex++;

				for ( var i = 0; i < allColumns.length; i++ ) {

					if ( i !== currentIndex ) {
						result.push( {
							label: allColumns[ i ].table,
							options: allColumns[ i ].columns,
						} );
					}
					
				}

				return result;

			},
			presetOrder: function() {
				if ( ! this.query.orderby ) {
					this.$set( this.query, 'orderby', [] );
				}
			},
		}
	} );

})( jQuery );
