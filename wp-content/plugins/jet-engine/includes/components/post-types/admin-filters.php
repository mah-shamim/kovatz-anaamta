<?php
/**
 * Custom post types manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CPT_Admin_Filters' ) ) {

	class Jet_Engine_CPT_Admin_Filters {

		/**
		 * Registered admin filters
		 * @var array
		 */
		public $admin_filters = array();

		/**
		 * Post type slug to register filters for
		 * @var array
		 */
		public $post_type = null;

		public $query_key = 'jet_engine_filters';

		public $inline_styles = 'margin-bottom: 5px;';

		/**
		 * Costructor
		 * @param [type] $post_type [description]
		 * @param [type] $columns   [description]
		 */
		public function __construct( $post_type, $filters ) {

			$this->admin_filters = $filters;
			$this->post_type     = $post_type;

			add_action( 'restrict_manage_posts', array( $this, 'render_filters' ) );
			add_filter( 'parse_query', array( $this, 'apply_filters' ) );
		}

		/**
		 * Apply all JetEngine amin filters to the query
		 *
		 * @param  [type] $query [description]
		 * @return [type]        [description]
		 */
		public function apply_filters( $query ) {

			if ( ! is_admin() || ! $query->is_main_query() ) {
				return $query;
			}

			if ( $this->post_type !== $query->query['post_type'] || empty( $_REQUEST[ $this->query_key ] ) ) {
				return $query;
			}

			foreach ( $_REQUEST[ $this->query_key ] as $index => $value ) {
				$query = $this->apply_filter( $query, $index, $value );
			}

		}

		/**
		 * Apply filter query depending on the filter type
		 *
		 * @param  [type] $query [description]
		 * @param  [type] $index [description]
		 * @param  [type] $value [description]
		 * @return [type]        [description]
		 */
		public function apply_filter( $query, $index, $value ) {

			$filter = ! empty( $this->admin_filters[ $index ] ) ? $this->admin_filters[ $index ] : false;

			if ( ! $filter ) {
				return $query;
			}

			switch ( $filter['type'] ) {
				case 'taxonomy':

					if ( ! empty( $value ) ) {
						$query = $this->apply_tax_filter( $query, $filter['tax'], $value );
					}

					break;

				case 'meta':

					$field = ! empty( $filter['custom_meta_key'] ) ? $filter['custom_meta_key'] : ( ! empty( $filter['meta_key'] ) ? $filter['meta_key'] : '' );

					$settings = array();

					if ( $field && empty( $filter['custom_meta_key'] ) ) {
						$all_fields = jet_engine()->meta_boxes->get_fields_for_context( 'post_type', $this->post_type );
						foreach ( $all_fields as $field_data ) {
							if ( $field_data['name'] === $field && 'checkbox' === $field_data['type'] ) {
								$settings['is_checkbox'] = true;
								$settings['is_array']    = ! empty( $field_data['is_array'] );
							}
						}
					}

					if ( ! empty( $value ) ) {
						$query = $this->apply_meta_filter( $query, $field, $value, $settings );
					}

					break;

				default:
					$query = apply_filters( 'jet-engine/admin-filters/apply-filter/' . $filter['type'], $query, $filter, $value, $this );
					break;
			}

			return $query;
		}

		/**
		 * Apply tax filter query
		 *
		 * @param  [type] $query    [description]
		 * @param  [type] $taxonomy [description]
		 * @param  [type] $value    [description]
		 * @return [type]           [description]
		 */
		public function apply_tax_filter( $query, $taxonomy, $value ) {

			if ( empty( $query->query_vars['tax_query'] ) ) {
				$query->query_vars['tax_query'] = array();
			}

			$query->query_vars['tax_query'][ $taxonomy ] = array(
				'taxonomy' => $taxonomy,
				'terms'    => $value,
			);

			return $query;

		}

		/**
		 * Apply meta filter query
		 *
		 * @param  [type] $query    [description]
		 * @param  [type] $meta_key [description]
		 * @param  [type] $value    [description]
		 * @param  array  $settings [description]
		 * @return [type]           [description]
		 */
		public function apply_meta_filter( $query, $meta_key, $value, $settings = array() ) {

			if ( empty( $query->query_vars['meta_query'] ) ) {
				$query->query_vars['meta_query'] = array();
			}

			$meta_row = array(
				'key'   => $meta_key,
				'value' => $value,
			);

			if ( ! empty( $settings['is_checkbox'] ) ) {
				if ( $settings['is_array'] ) {
					$meta_row['value'] = '"' . $value . '";';
					$meta_row['compare'] = 'LIKE';
				} else {
					$meta_row['value'] = ':["]?' . $value . '["]?;s:4:"true";';
					$meta_row['compare'] = 'RLIKE';
				}

			}

			$query->query_vars['meta_query'][ $meta_key ] = $meta_row;

			return $query;

		}

		/**
		 * Returns active filter value
		 *
		 * @param  [type] $index [description]
		 * @return [type]        [description]
		 */
		public function get_active_filter_value( $index ) {
			$request = ! empty( $_REQUEST[ $this->query_key ] ) ? $_REQUEST[ $this->query_key ] : array();
			return isset( $request[ $index ] ) ? $request[ $index ] : false;
		}

		/**
		 * Register filter
		 *
		 * @param  [type] $post_type [description]
		 * @return [type]            [description]
		 */
		public function render_filters( $post_type ) {

			if ( $this->post_type !== $post_type ) {
				return;
			}

			foreach ( $this->admin_filters as $index => $filter ) {

				switch ( $filter['type'] ) {

					case 'taxonomy':

						$option_all = '';

						if ( ! empty( $filter['title_as_placeholder'] ) ) {
							$option_all = ! empty( $filter['title'] ) ? $filter['title'] : '';
						}

						$args = array(
							'show_option_all' => $option_all,
							'name'            => $this->get_filter_name( $index ),
							'taxonomy'        => $filter['tax'],
							'selected'        => $this->get_active_filter_value( $index ),
							'hierarchical'    => ! empty( $filter['show_hierarchy'] ) ? true : false,
							'show_count'      => ! empty( $filter['show_count'] ) ? true : false,
						);

						if ( ! empty( $filter['tax_order_by'] ) ) {
							$args['orderby'] = $filter['tax_order_by'];
						}

						if ( ! empty( $filter['tax_order'] ) ) {
							$args['order'] = $filter['tax_order'];
						}

						wp_dropdown_categories( $args );

						break;

					case 'meta':
						$this->render_meta_filter( $filter, $index );
						break;

					default:
						do_action( 'jet-engine/post-types/admin-filters/custom-filter/' . $filter['type'], $filter, $index, $this );
						break;
				}

			}

			if ( $this->inline_styles ) {
				printf( '<style>.tablenav .actions select {%s}</style>', $this->inline_styles );
				$this->inline_styles = null;
			}

		}

		/**
		 * Returns filter name attribute
		 *
		 * @param  [type] $index [description]
		 * @return [type]        [description]
		 */
		public function get_filter_name( $index ) {
			return $this->query_key . '[' . $index . ']';
		}

		/**
		 * Render meta filter
		 *
		 * @param  [type] $filter [description]
		 * @return [type]         [description]
		 */
		public function render_meta_filter( $filter, $index ) {

			$options_list = $this->get_meta_filter_options( $filter );
			$selected     = $this->get_active_filter_value( $index );

			if ( empty( $options_list ) ) {
				return;
			}

			$options = $this->add_placeholder( $filter );

			foreach( $options_list as $option ) {
				$options .= sprintf(
					'<option value="%1$s"%3$s>%2$s</option>',
					$option['value'],
					$option['label'],
					selected( $selected, $option['value'], false )
				);
			}

			printf( '<select name="%1$s">%2$s</select>', $this->get_filter_name( $index ), $options );

		}

		/**
		 * Returns placeholder option HTML if allowed
		 *
		 * @param [type] $filter [description]
		 */
		public function add_placeholder( $filter ) {

			$result = '';

			if ( ! empty( $filter['title_as_placeholder'] ) ) {
				$title  = ! empty( $filter['title'] ) ? $filter['title'] : '';
				$result = '<option value="">' . $title . '</option>';
			}

			return $result;
		}

		/**
		 * Returns allowed options for the meta values filter
		 *
		 * @param  [type] $filter [description]
		 * @return [type]         [description]
		 */
		public function get_meta_filter_options( $filter ) {

			$result = array();

			switch ( $filter['options_source'] ) {

				case 'field_options':

					$fields = jet_engine()->meta_boxes->get_fields_for_context( 'post_type', $this->post_type );
					$key    = ! empty( $filter['meta_key'] ) ? $filter['meta_key'] : false;

					if ( $key ) {

						foreach ( $fields as $field ) {

							if ( $field['name'] === $key ) {

								$options = array();

								if ( ! class_exists( 'Jet_Engine_CPT_Meta' ) ) {
									require jet_engine()->meta_boxes->component_path( 'post.php' );
								}

								$post_meta = new Jet_Engine_CPT_Meta();

								if ( empty( $field['options'] ) ) {
									$field['options'] = array();
								}

								if ( ! empty( $filter['title_as_placeholder'] ) ) {
									$field['placeholder'] = '';
								}

								if ( in_array( $field['type'], array( 'checkbox', 'select' ) ) ) {
									$options = $post_meta->prepare_select_options( $field );
								} elseif ( 'radio' === $field['type'] ) {
									$options = $post_meta->prepare_radio_options( $field['options'], $field );
								}

								$result = ! empty( $options['options'] ) ? $options['options'] : array();

							}
						}
					}

					break;

				case 'glossary':

					if ( ! empty( $filter['glossary_id'] ) ) {
						$result = jet_engine()->glossaries->meta_fields->get_glossary_for_field( $filter['glossary_id'] );
					}

					break;

				case 'db':

					$field = ! empty( $filter['custom_meta_key'] ) ? $filter['custom_meta_key'] : ( ! empty( $filter['meta_key'] ) ? $filter['meta_key'] : '' );

					if ( $field ) {

						global $wpdb;

						$postmeta = $wpdb->postmeta;
						$posts    = $wpdb->posts;

						$sql = "SELECT DISTINCT pm.meta_value FROM $postmeta AS pm INNER JOIN $posts AS p ON p.ID = pm.post_id WHERE pm.meta_key = '%s' AND p.post_type = '%s'";

						if ( ! empty( $filter['meta_order'] ) ) {
							$sql .= " ORDER BY pm.meta_value " . $filter['meta_order'];
						}

						$result   = $wpdb->get_results(
							$wpdb->prepare(
								$sql,
								$field,
								$this->post_type
							),
							ARRAY_A
						);

					}

					break;
			}

			$formatted_result = array();

			foreach ( $result as $key => $value ) {

				if ( is_array( $value ) ) {
					if ( isset( $value['value'] ) && isset( $value['label'] ) ) {
						$formatted_result[] = array(
							'value' => apply_filters( 'jet-engine/admin-filters/filter-value', $value['value'], $filter, $this ),
							'label' => apply_filters( 'jet-engine/admin-filters/filter-label', $value['label'], $filter, $this ),
						);
					} else {
						$value = array_values( $value );
						if ( '' !== $value[0] ) {
							$formatted_result[] = array(
								'value' => apply_filters( 'jet-engine/admin-filters/filter-value', $value[0], $filter, $this ),
								'label' => apply_filters( 'jet-engine/admin-filters/filter-label', isset( $value[1] ) ? $value[1] : $value[0], $filter, $this ),
							);
						}
					}
				} else {
					$formatted_result[] = array(
						'value' => apply_filters( 'jet-engine/admin-filters/filter-value', $key, $filter, $this ),
						'label' => apply_filters( 'jet-engine/admin-filters/filter-label', $value, $filter, $this ),
					);
				}

			}

			return $formatted_result;

		}

	}

}
