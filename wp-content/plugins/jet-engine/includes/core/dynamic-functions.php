<?php
/**
 * Dynamic functions class
 */

// If this file is called directly, abort.x
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Dynamic_Functions' ) ) {

	class Jet_Engine_Dynamic_Functions {

		private $functions_list = array();
		private $cache_group = 'jet_engine_dynamic_functions';

		public function __construct() {

			$number_format_settings = array(
				'decimal_point' => array(
					'label'   => __( 'Decimals Point', 'jet-engine' ),
					'type'    => 'text',
					'default' => '.',
				),
				'thousands_separator' => array(
					'label'   => __( 'Thousands separator', 'jet-engine' ),
					'type'    => 'text',
					'default' => ',',
				),
				'decimal_count' => array(
					'label'   => __( 'Decimals Count', 'jet-engine' ),
					'type'    => 'number',
					'default' => 0,
					'min'     => 0,
					'max'     => 5,
				),
			);

			$this->functions_list = apply_filters( 'jet-engine/dynamic-functions/functions-list', array(
				'sum' => array(
					'label'           => __( 'Summed value', 'jet-engine' ),
					'type'            => 'sql', // sql or raw
					'query'           => 'SUM( CAST( meta_value AS DECIMAL( 10, %decimal_count% ) ) )',
					'cb'              => array( $this, 'number_format' ),
					'custom_settings' => $number_format_settings,
				),
				'avg' => array(
					'label'           => __( 'Average value', 'jet-engine' ),
					'type'            => 'sql',
					'query'           => 'ROUND( AVG( CAST( meta_value AS DECIMAL( 10, %decimal_count% ) ) ), %decimal_count% )',
					'cb'              => array( $this, 'number_format' ),
					'custom_settings' => $number_format_settings,
				),
				'count' => array(
					'label' => __( 'Count', 'jet-engine' ),
					'type'  => 'sql',
					'query' => 'COUNT(meta_value)',
					'cb'    => false,
				),
				'max' => array(
					'label'           => __( 'Maximum value', 'jet-engine' ),
					'type'            => 'sql',
					'query'           => 'MAX( CAST( meta_value AS DECIMAL( 10, %decimal_count% ) ) )',
					'cb'              => array( $this, 'number_format' ),
					'custom_settings' => $number_format_settings,
				),
				'min' => array(
					'label'           => __( 'Minimum value', 'jet-engine' ),
					'type'            => 'sql',
					'query'           => 'MIN( CAST( meta_value AS DECIMAL( 10, %decimal_count% ) ) )',
					'cb'              => array( $this, 'number_format' ),
					'custom_settings' => $number_format_settings,
				),
				'query_var' => array(
					'label'              => __( 'SQL query results', 'jet-engine' ),
					'type'               => 'query_var',
					'query'              => false,
					'cb'                 => array( $this, 'number_format' ),
					'custom_settings'    => $number_format_settings,
					'custom_settings_cb' => array( $this, 'get_query_settings' ),
				),
			) );

			add_filter( 'jet-engine/blocks-views/dynamic-content/data-sources', array( $this, 'register_blocks_dynamic_source' ) );
			add_filter( 'jet-engine/blocks-views/editor-data', array( $this, 'register_blocks_controls' ) );
			add_filter( 'jet-engine/blocks-views/dynamic-content/get-dynamic-value/dynamic_function', array( $this, 'get_block_dynamic_value' ), 10, 2 );

		}

		/**
		 * Register Dynamic function source for blocks dynamic data.
		 *
		 * @param  [type] $sources [description]
		 * @return [type]          [description]
		 */
		public function register_blocks_dynamic_source( $sources ) {

			$sources[] = array(
				'value' => 'dynamic_function',
				'label' => __( 'Dynamic function', 'jet-engine' ),
			);

			return $sources;

		}

		/**
		 * Get block dyanmic value
		 *
		 * @param  [type] $result [description]
		 * @param  [type] $data   [description]
		 * @return [type]         [description]
		 */
		public function get_block_dynamic_value( $result, $data ) {

			$prepared_data = array();
			$prefix = 'dynamic_functions_';

			foreach ( $data as $key => $value ) {
				if ( false !== strpos( $key, $prefix ) ) {
					$prepared_data[ str_replace( $prefix, '', $key ) ] = $value;
				}
			}

			$function_name = \Jet_Engine_Tools::safe_get( 'function_name', $prepared_data );
			$source        = \Jet_Engine_Tools::safe_get( 'data_source', $prepared_data );
			$field_name    = \Jet_Engine_Tools::safe_get( 'field_name', $prepared_data );

			if ( ! $source ) {
				$source = 'post_meta';
			}

			$data_source = array( 'source' => $source );

			if ( empty( $function_name ) ) {
				return;
			}

			if ( 'post_meta' === $source ) {

				$data_source['context']          = \Jet_Engine_Tools::safe_get( 'data_context', $prepared_data , 'all_posts' );
				$data_source['context_tax']      = \Jet_Engine_Tools::safe_get( 'data_context_tax', $prepared_data );
				$data_source['context_tax_term'] = \Jet_Engine_Tools::safe_get( 'data_context_tax_term', $prepared_data );
				$data_source['context_user_id']  = \Jet_Engine_Tools::safe_get( 'data_context_user_id', $prepared_data );
				$data_source['context_relation'] = \Jet_Engine_Tools::safe_get( 'data_context_relation', $prepared_data );
				$data_source['post_status']      = \Jet_Engine_Tools::safe_get( 'data_post_status', $prepared_data );
				$data_source['post_types']       = \Jet_Engine_Tools::safe_get( 'data_post_types', $prepared_data );

			}

			$custom_settings = $this->get_custom_settings( $function_name, null, $prepared_data );

			$result = $this->call_function( $function_name, $data_source, $field_name, $custom_settings );

			return $result;
		}

		/**
		 * Register denamic functions related controls for the blocks editor
		 *
		 * @param  [type] $editor_config [description]
		 * @return [type]                [description]
		 */
		public function register_blocks_controls( $editor_config ) {
			$editor_config['dynamicFunctionsControls'] = $this->get_editor_controls( 'blocks' );
			return $editor_config;
		}

		/**
		 * Returns functions list for options
		 */
		public function functions_list() {

			$result = array();

			foreach ( $this->functions_list as $func_key => $func_data ) {
				$result[ $func_key ] = ! empty( $func_data['label'] ) ? $func_data['label'] : $func_key;
			}

			return $result;

		}

		public function get_query_settings() {

			$queries = \Jet_Engine\Query_Builder\Manager::instance()->get_queries();

			$options = array();
			$sql_queries = array();

			if ( ! empty( $queries ) ) {
				foreach( $queries as $query ) {

					if ( ! $query || ! is_object( $query ) ) {
						continue;
					}

					if ( 'sql' === $query->query_type ) {
						$sql_queries[] = $query;
					}
				}
			}

			if ( empty( $sql_queries ) ) {
				$options['no_query_notice'] = array(
					'type' => 'raw_html',
					'raw'  => sprintf(
						esc_html__( 'With this function you can output any data from custom SQL query. To start, please %s', 'jet-elements' ),
						'<a target="_blank" href="' . admin_url( 'admin.php?page=jet-engine-query&query_action=add' ) . '">' . esc_html__( 'create your query', 'jet-elements' ) . '</a>'
					)
				);
			}

			$columns = array();

			foreach ( $sql_queries as $query ) {

				$fields          = $query->get_instance_fields();
				$prepared_fields = array();

				foreach ( $fields as $field ) {
					$prepared_fields[ $query->get_instance_id() . '::' . $field ] = $field;
				}

				$columns[] = array(
					'label'   => $query->get_instance_name(),
					'options' => $prepared_fields,
				);

			}

			$options['_query_field'] = array(
				'label'       => __( 'Query Column', 'jet-engine' ),
				'description' => __( 'Select column from the query to show', 'jet-engine' ),
				'type'        => 'select',
				'groups'      => $columns,
			);

			$options['_additional_function'] = array(
				'label'       => __( 'Additional Function', 'jet-engine' ),
				'description' => __( 'Apply additional function to SQL query', 'jet-engine' ),
				'type'        => 'select',
				'options'     => array(
					''      => __( 'Select...', 'jet-engine' ),
					'SUM'   => __( 'Sum', 'jet-engine' ),
					'AVG'   => __( 'Average', 'jet-engine' ),
					'COUNT' => __( 'Count', 'jet-engine' ),
					'MIN'   => __( 'Minimum', 'jet-engine' ),
					'MAX'   => __( 'Maximum', 'jet-engine' ),
				),
			);

			return $options;
		}

		public function get_relations_for_options() {

			$result = array(
				'' => __( 'Select...', 'jet-engine' ),
			);

			$relations = jet_engine()->relations->get_relations_for_js();

			if ( ! empty( $relations ) ) {
				foreach ( $relations as $relations ) {
					$result[ $relations['value'] ] = $relations['label'];
				}
			}

			return $result;
		}

		/**
		 * Returns all taxonomies list for options
		 *
		 * @return [type] [description]
		 */
		public function get_taxonomies_for_options() {

			$result     = array();
			$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

			foreach ( $taxonomies as $taxonomy ) {

				if ( empty( $taxonomy->object_type ) || ! is_array( $taxonomy->object_type ) ) {
					continue;
				}

				foreach ( $taxonomy->object_type as $object ) {
					if ( empty( $result[ $object ] ) ) {
						$post_type = get_post_type_object( $object );

						if ( ! $post_type ) {
							continue;
						}

						$result[ $object ] = array(
							'label'   => $post_type->labels->name,
							'options' => array(),
						);
					}

					$result[ $object ]['options'][ $taxonomy->name ] = $taxonomy->labels->name;

				};
			}

			return $result;

		}

		/**
		 * Returns editor controls for Elementor or Blocks editor
		 *
		 * @param  string $for [description]
		 * @return [type]      [description]
		 */
		public function get_editor_controls( $for = 'elementor' ) {

			$controls       = array();
			$functions_list = array_reverse( $this->functions_list );

			$controls['function_name'] = array(
				'label'           => __( 'Function', 'jet-engine' ),
				'type'            => 'select',
				'add_placeholder' => __( 'Select...', 'jet-engine' ),
				'options'         => $this->functions_list(),
			);

			$controls['data_source'] = array(
				'label'           => __( 'Data Source', 'jet-engine' ),
				'type'            => 'select',
				'add_placeholder' => __( 'Select...', 'jet-engine' ),
				'options'         => array(
					'post_meta' => __( 'Post Meta', 'jet-engine' ),
					'term_meta' => __( 'Term Meta', 'jet-engine' ),
					'user_meta' => __( 'User Meta', 'jet-engine' ),
				),
				'condition' => array(
					'function_name!' => 'query_var',
				),
			);

			$controls['field_name'] = array(
				'label'       => __( 'Field Name', 'jet-engine' ),
				'type'        => 'text',
				'label_block' => true,
				'condition'   => array(
					'function_name!' => 'query_var',
				),
			);

			$controls['data_context'] = array(
				'label'           => __( 'Data Context', 'jet-engine' ),
				'type'            => 'select',
				'default'         => 'all_posts',
				'add_placeholder' => __( 'Select...', 'jet-engine' ),
				'options'         => array(
					'all_posts'     => __( 'All posts', 'jet-engine' ),
					'current_term'  => __( 'Posts from current term', 'jet-engine' ),
					'current_user'  => __( 'Posts by current user', 'jet-engine' ),
					'queried_user'  => __( 'Posts by queried user', 'jet-engine' ),
					'related_posts' => __( 'Related posts for current post', 'jet-engine' ),
				),
				'condition' => array(
					'data_source'    => 'post_meta',
					'function_name!' => 'query_var',
				),
			);

			$controls['data_context_relation'] = array(
				'label'     => __( 'Relation', 'jet-engine' ),
				'type'      => 'select',
				'options'   => $this->get_relations_for_options(),
				'condition' => array(
					'data_source'    => 'post_meta',
					'data_context'   => 'related_posts',
					'function_name!' => 'query_var',
				),
			);

			$controls['data_context_relation_object'] = array(
				'label'     => __( 'From Object (what to show)', 'jet-engine' ),
				'type'      => 'select',
				'options' => array(
					'parent_object' => __( 'Parent Object', 'jet-engine' ),
					'child_object'  => __( 'Child Object', 'jet-engine' ),
				),
				'default' => 'child_object',
				'condition' => array(
					'data_source'    => 'post_meta',
					'data_context'   => 'related_posts',
					'function_name!' => 'query_var',
				),
			);

			$controls['data_context_tax'] = array(
				'label'           => __( 'Taxonomy', 'jet-engine' ),
				'type'            => 'select',
				'groups'          => $this->get_taxonomies_for_options(),
				'add_placeholder' => __( 'Select...', 'jet-engine' ),
				'condition'       => array(
					'data_source'    => 'post_meta',
					'data_context'   => 'current_term',
					'function_name!' => 'query_var',
				),
			);

			$controls['data_context_tax_term'] = array(
				'label'  => __( 'Set term ID/slug', 'jet-engine' ),
				'type'   => 'text',
				'label_block' => true,
				'description' => __( 'Leave empty to get term dynamically. Use prefix <b>slug::</b> to set term by slug instead of ID, for example - slug::term-slug', 'jet-engine' ),
				'has_html'    => true,
				'condition' => array(
					'data_source'    => 'post_meta',
					'data_context'   => 'current_term',
					'function_name!' => 'query_var',
				),
			);

			$controls['data_context_user_id'] = array(
				'label'  => __( 'Set user ID/login/email', 'jet-engine' ),
				'type'   => 'text',
				'label_block' => true,
				'description' => __( 'Leave empty to get user ID dynamically. Use prefixes <b>login::</b> or <b>email::</b> to set user by login or email instead of ID, for example - email::admin@demolink.org', 'jet-engine' ),
				'has_html'    => true,
				'condition' => array(
					'data_source'    => 'post_meta',
					'data_context'   => 'queried_user',
					'function_name!' => 'query_var',
				),
			);

			$controls['data_post_status'] = array(
				'label'   => __( 'Query by posts with status', 'jet-engine' ),
				'description' => __( 'Leave empy to search anywhere', 'jet-engine' ),
				'type'    => 'select2',
				'default' => '',
				'label_block' => true,
				'multiple'    => true,
				'add_placeholder' => __( 'Select...', 'jet-engine' ),
				'options' => array(
					'publish'    => __( 'Publish', 'jet-engine' ),
					'pending'    => __( 'Pending', 'jet-engine' ),
					'draft'      => __( 'Draft', 'jet-engine' ),
					'auto-draft' => __( 'Auto draft', 'jet-engine' ),
					'future'     => __( 'Future', 'jet-engine' ),
					'private'    => __( 'Private', 'jet-engine' ),
					'trash'      => __( 'Trash', 'jet-engine' ),
					'any'        => __( 'Any', 'jet-engine' ),
				),
				'condition'   => array(
					'data_source'    => 'post_meta',
					'function_name!' => 'query_var',
				),
			);

			$controls['data_post_types'] = array(
				'label'       => esc_html__( 'Query by posts with types', 'jet-engine' ),
				'description' => __( 'Leave empy to search anywhere', 'jet-engine' ),
				'type'        => 'select2',
				'label_block' => true,
				'add_placeholder' => __( 'Select...', 'jet-engine' ),
				'multiple'    => true,
				'options'     => jet_engine()->listings->get_post_types_for_options(),
				'condition'   => array(
					'data_source'    => 'post_meta',
					'function_name!' => 'query_var',
				),
			);

			foreach ( $functions_list as $func_name => $data ) {

				$custom_settings = array();

				if ( ! empty( $data['custom_settings_cb'] ) && is_callable( $data['custom_settings_cb'] ) ) {
					$custom_settings = array_merge( $custom_settings, call_user_func( $data['custom_settings_cb'] ) );
				}

				if ( ! empty( $data['custom_settings'] ) ) {
					$custom_settings = array_merge( $custom_settings, $data['custom_settings'] );
				}

				if ( ! empty( $custom_settings ) ) {

					foreach ( $custom_settings as $setting_key => $setting_data ) {

						if ( empty( $controls[ $setting_key ] ) ) {

							$setting_data['condition'] = array(
								'function_name' => array( $func_name ),
							);

							$controls[ $setting_key ] = $setting_data;
						} else {
							$controls[ $setting_key ]['condition']['function_name'][] = $func_name;
						}

					}

				}

			}

			if ( 'blocks' === $for ) {
				$controls = \Jet_Engine_Tools::prepare_controls_for_js( $controls );
			}

			return $controls;

		}

		/**
		 * Allow to register custom settings to each function
		 */
		public function register_custom_settings( $tag ) {

			$controls = $this->get_editor_controls();

			if ( ! empty( $controls ) ) {
				foreach ( $controls as $control_key => $control_data ) {
					$tag->add_control( $control_key, $control_data );
				}
			}

		}

		/**
		 * Allow to get custom settings to each function
		 */
		public function get_custom_settings( $function, $tag, $all_settings = array() ) {

			$settings        = array();
			$custom_settings = array();
			$data            = ! empty( $this->functions_list[ $function ] ) ? $this->functions_list[ $function ] : array();

			if ( ! empty( $data['custom_settings_cb'] ) && is_callable( $data['custom_settings_cb'] ) ) {
				$custom_settings = array_merge( $custom_settings, call_user_func( $data['custom_settings_cb'] ) );
			}

			if ( ! empty( $data['custom_settings'] ) ) {
				$custom_settings = array_merge( $custom_settings, $data['custom_settings'] );
			}

			if ( empty( $this->functions_list[ $function ] ) || empty( $custom_settings ) ) {
				return $settings;
			}

			$required_sql_settings = array( 'decimal_count' );

			foreach ( $custom_settings as $setting_key => $setting_data ) {
				if ( $tag ) {
					$settings[ $setting_key ] = $tag->get_settings( $setting_key );
				} else {
					$settings[ $setting_key ] = isset( $all_settings[ $setting_key ] ) ? $all_settings[ $setting_key ] : false;
				}

				// Added to prevent SQL error if required sql setting is empty.
				if ( in_array( $setting_key, $required_sql_settings ) && Jet_Engine_Tools::is_empty( $settings[ $setting_key ] ) && isset( $setting_data['default'] ) ) {
					$settings[ $setting_key ] = $setting_data['default'];
				}
			}

			return $settings;

		}

		/**
		 * Call function by function name and arguments
		 */
		public function call_function( $function_name = null, $data_source = array(), $field_name = null, $custom_settings = array() ) {

			if ( empty( $this->functions_list[ $function_name ] ) ) {
				return null;
			}

			$func_data = $this->functions_list[ $function_name ];

			$func_data['function']        = $function_name;
			$func_data['data_source']     = $data_source;
			$func_data['field_name']      = $field_name;
			$func_data['custom_settings'] = $custom_settings;

			if ( ! empty( $func_data['type'] ) ) {
				switch ( $func_data['type'] ) {
					case 'sql':
						return $this->call_sql_function( $func_data );

					case 'query_var':
						return $this->get_query_var( $func_data );

					default:
						return $this->call_raw_function( $func_data );
				}
			}

		}

		public function get_query_var( $func_data ) {

			if ( empty( $func_data['custom_settings'] ) || empty( $func_data['custom_settings']['_query_field'] ) ) {
				return;
			}

			$query_data = explode( '::', $func_data['custom_settings']['_query_field'] );
			$query_id   = str_replace( '_query_', '', $query_data[0] );
			$column     = $query_data[1];
			$query      = \Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $query_id );

			if ( ! $query || 'sql' !== $query->query_type ) {
				return;
			}

			$func          = ! empty( $func_data['custom_settings']['_additional_function'] ) ? $func_data['custom_settings']['_additional_function'] : false;
			$decimal_count = isset( $func_data['custom_settings']['decimal_count'] ) ? $func_data['custom_settings']['decimal_count'] : 0;

			$callback = ! empty( $func_data['cb'] ) ? $func_data['cb'] : false;
			$result   = $query->get_var( $column, $func, $decimal_count );

			if ( $callback && is_callable( $callback ) ) {
				return call_user_func( 
					$callback, 
					array( 'result' => $result, 'custom_settings' => $func_data['custom_settings'] ) 
				);
			} else {
				return $result;
			}

		}

		/**
		 * Call plain PHP function
		 */
		public function call_raw_function( $func_data ) {

			$function = ! empty( $func_data['cb'] ) ? $func_data['cb'] : false;

			if ( ! $function || ! is_callable( $function ) ) {
				return null;
			}

			unset( $func_data['cb'] );

			return call_user_func( $function, $func_data );

		}

		/**
		 * Call sql-query function
		 */
		public function call_sql_function( $data ) {

			$query = ! empty( $data['query'] ) ? $data['query'] : false;
			$where = ! empty( $data['where'] ) ? $data['where'] : false;
			$data_source = ! empty( $data['data_source'] ) ? $data['data_source'] : false;
			$field_name = ! empty( $data['field_name'] ) ? $data['field_name'] : false;

			if ( ! $field_name || ! $query ) {
				return null;
			}

			$table = false;

			global $wpdb;

			$source = ! empty( $data_source['source'] ) ? $data_source['source'] : 'post_meta';

			switch ( $source ) {

				case 'post_meta':

					$table = $wpdb->postmeta;
					break;

				case 'term_meta':
					$table = $wpdb->termmeta;
					break;

				case 'user_meta':
					$table = $wpdb->usermeta;
					break;

				default:
					$table = apply_filters( 'jet-engine/dynamic-functions/custom-sql-table', $table, $data );
					break;
			}

			if ( ! $table ) {
				return null;
			}

			if ( ! $where ) {
				$where = $wpdb->prepare( "WHERE $table.meta_key ='%s'", $field_name );
			}

			if ( ! empty( $data['custom_settings'] ) ) {
				foreach ( $data['custom_settings'] as $key => $value ) {
					$query = str_replace( '%' . $key . '%', $value, $query );
					$where = str_replace( '%' . $key . '%', $value, $where );
				}
			}

			$posts_table = $wpdb->posts;
			$posts_query_join = " INNER JOIN $posts_table ON $table.post_id = $posts_table.ID ";

			$posts_join = "";
			$posts_where = "";

			if ( ! empty( $data_source['post_status'] ) ) {

				$posts_join = $posts_query_join;
				$posts_where .= " AND $posts_table.post_status";
				$post_status = is_array( $data_source['post_status'] ) ? $data_source['post_status'] : array( $data_source['post_status'] );

				if ( 1 === count( $post_status ) ) {
					$status = $post_status[0];
					$posts_where .= " = '$status'";
				} else {

					$statuses = array();

					foreach ( $post_status as $status ) {
						$statuses[] = sprintf( "'%s'", $status );
					}

					$statuses = implode( ', ', $statuses );
					$posts_where .= " IN ($statuses)";
				}

			}

			if ( ! empty( $data_source['post_types'] ) ) {

				if ( ! $posts_join ) {
					$posts_join = $posts_query_join;
				}

				$posts_where .= " AND $posts_table.post_type";
				$post_types = is_array( $data_source['post_types'] ) ? $data_source['post_types'] : array( $data_source['post_types'] );

				if ( 1 === count( $post_types ) ) {
					$type = $post_types[0];
					$posts_where .= " = '$type'";
				} else {

					$types = array();

					foreach ( $post_types as $type ) {
						$types[] = sprintf( "'%s'", $type );
					}

					$types = implode( ', ', $types );
					$posts_where .= " IN ($types)";
				}

			}

			$final_query = "SELECT $query FROM $table $posts_join $where $posts_where;";

			if ( 'post_meta' === $source ) {

				switch ( $data_source['context'] ) {

					case 'current_term':

						$term_id = $this->get_current_term_id( $data_source );

						if ( $term_id ) {
							$term_relationships = $wpdb->term_relationships;
							$final_query = "SELECT $query FROM $table $posts_join INNER JOIN $term_relationships ON $table.post_id = $term_relationships.object_id $where AND $term_relationships.term_taxonomy_id = $term_id $posts_where";
						}

						break;

					case 'related_posts':

						$relation = ! empty( $data_source['context_relation'] ) ? $data_source['context_relation'] : false;
						$post     = get_post();
						$posts    = false;

						if ( $relation && $post ) {

							$post_type     = $post->post_type;
							$relation_data = jet_engine()->relations->legacy->get_relation_info( $relation );

							if ( $relation_data ) {

								$from  = ( $post_type === $relation_data['post_type_1'] ) ? $relation_data['post_type_2'] : $relation_data['post_type_1'];
								$posts = jet_engine()->relations->legacy->get_related_posts( array(
									'post_type_1' => $relation_data['post_type_1'],
									'post_type_2' => $relation_data['post_type_2'],
									'hash'        => $relation,
									'from'        => $from,
								) );

							} else {
								
								$from = ! empty( $data_source['data_context_relation_object'] ) ? $data_source['data_context_relation_object'] : 'child_object';
								$object_id = jet_engine()->listings->data->get_current_object_id();

								$relation_instance = jet_engine()->relations->get_active_relations( $relation );

								if ( ! $relation_instance ) {
									return null;
								}

								switch ( $from ) {
									case 'parent_object':
										$posts = $relation_instance->get_parents( $object_id, 'ids' );
										break;

									default:
										$posts = $relation_instance->get_children( $object_id, 'ids' );
										break;

								}

							}

							if ( ! empty( $posts ) ) {
								$posts = implode( ', ', $posts );
							} else {
								// If related posts not found - return null to avoid incorrect calculations
								return null;
							}

							$final_query = str_replace( ";", " AND $table.post_id IN ( $posts );", $final_query );

						}

						break;

					case 'current_user':

						$user_id = get_current_user_id();

						if ( $user_id ) {
							$posts = $wpdb->posts;
							$final_query = "SELECT $query FROM $table INNER JOIN $posts ON $table.post_id = $posts.ID $where AND $posts.post_author = $user_id $posts_where";
						}

						break;

					case 'queried_user':

						$user_id = false;

						if ( ! empty( $data_source['context_user_id'] ) ) {

							$user = $data_source['context_user_id'];

							if ( false === strpos( $user, '::' ) ) {
								$user_id = absint( $user );
							} else {

								$user_data = explode( '::', $user );

								if ( ! empty( $user_data[0] ) && ! empty( $user_data[1] ) ) {
									$user_obj = get_user_by( $user_data[0], $user_data[1] );
								}

								if ( $user_obj ) {
									$user_id = $user_obj->ID;
								}

							}

						} else {
							if ( is_author() ) {
								$user_id = get_queried_object_id();
							} elseif ( jet_engine()->modules->is_module_active( 'profile-builder' ) ) {
								$user_id = \Jet_Engine\Modules\Profile_Builder\Module::instance()->query->get_queried_user_id();
							}
						}

						if ( ! $user_id ) {
							$user_id = get_current_user_id();
						}

						if ( $user_id ) {
							$posts = $wpdb->posts;
							$final_query = "SELECT $query FROM $table INNER JOIN $posts ON $table.post_id = $posts.ID $where AND $posts.post_author = $user_id $posts_where";
						}

						break;
				}

			}

			$final_query = apply_filters( 'jet-engine/dynamic-functions/final-query', $final_query, $data, $this );
			$hash = md5( $final_query );

			$result = wp_cache_get( $hash, $this->cache_group );

			if ( ! $result ) {
				$result = $wpdb->get_var( $final_query );

				if ( ! empty( $data['cb'] ) ) {
					$data['result'] = $result;

					$cb_result = $this->call_raw_function( $data );
					$result    = ( null !== $cb_result ) ? $cb_result : $result;
				}

				wp_cache_set( $hash, $result, $this->cache_group );
			}

			return $result;

		}

		/**
		 * Get term ID for current context and settings
		 */
		public function get_current_term_id( $data_source ) {

			if ( ! empty( $data_source['context_tax_term'] ) ) {

				$term = $data_source['context_tax_term'];
				$term_id = false;

				if ( false === strpos( $term, 'slug::' ) ) {
					$term_id = absint( $term );
				} elseif ( ! empty( $data_source['context_tax'] ) ) {
					$term = str_replace( 'slug::', '', $term );
					$term_obj = get_term_by( 'slug', $term, $data_source['context_tax'] );

					if ( $term_obj ) {
						$term_id = $term_obj->term_id;
					}

				}

				if ( $term_id ) {
					return $term_id;
				}

			}

			$listing_object = jet_engine()->listings->data->get_current_object();
			$term_id = false;

			if ( $listing_object && 'WP_Term' === get_class( $listing_object ) ) {
				$term_id = $listing_object->term_id;
			} else {
				$queried_object = get_queried_object();

				if ( $queried_object && isset( $queried_object->term_id ) ) {
					$term_id = $queried_object->term_id;
				} elseif ( $listing_object && 'WP_Post' === get_class( $listing_object ) && ! empty( $data_source['context_tax'] ) ) {
					$post_id = $listing_object->ID;
					$terms = wp_get_post_terms( $post_id, $data_source['context_tax'], array( 'fields' => 'ids' ) );

					if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
						$term_id = $terms[0];
					}

				}

			}

			return $term_id;

		}

		/**
		 * Number format.
		 *
		 * @param  array $data
		 * @return string|null
		 */
		public function number_format( $data = array() ) {

			if ( ! isset( $data['result'] ) ) {
				return null;
			}

			$decimal_point = isset( $data['custom_settings']['decimal_point'] ) ? $data['custom_settings']['decimal_point'] : '.';
			$thousands_sep = isset( $data['custom_settings']['thousands_separator'] ) ? $data['custom_settings']['thousands_separator'] : ',';
			$decimal_count = isset( $data['custom_settings']['decimal_count'] ) ? $data['custom_settings']['decimal_count'] : 0;

			return number_format( $data['result'], $decimal_count, $decimal_point, $thousands_sep );
		}

	}

}
