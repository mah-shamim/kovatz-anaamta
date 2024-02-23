<?php
/**
 * Custom post types manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CPT_Admin_Columns' ) ) {

	class Jet_Engine_CPT_Admin_Columns {

		/**
		 * Post type slug
		 * @var string
		 */
		public $post_type = null;

		/**
		 * Registered admin columns
		 * @var array
		 */
		public $admin_columns = array();

		/**
		 * registered admin columns
		 * @var array
		 */
		public $render_columns = array();

		/**
		 * Sortbale columns
		 *
		 * @var array
		 */
		public $sortable_columns = array();

		/**
		 * Costructor
		 * @param [type] $post_type [description]
		 * @param [type] $columns   [description]
		 */
		public function __construct( $post_type, $columns ) {

			$this->post_type     = $post_type;
			$this->admin_columns = $columns;

			add_filter( 'manage_' . $post_type . '_posts_columns', array( $this, 'edit_columns' ) );
			add_action( 'manage_' . $post_type . '_posts_custom_column', array( $this, 'manage_columns' ), 10, 2 );

			if ( ! empty( $this->admin_columns ) ) {
				foreach ( $this->admin_columns as $index => $column ) {
					if ( ! empty( $column['is_sortable'] ) && 'post_terms' !== $column['type'] ) {

						$column['index'] = $index;

						$query_var = false;

						if ( 'meta_value' === $column['type'] ) {
							$query_var = isset( $column['meta_field'] ) ? $column['meta_field'] : false;
						} elseif ( 'post_id' === $column['type'] ) {
							$query_var = 'ID';
						} elseif ( 'custom_callback' === $column['type'] && ! empty( $column['sort_by_field'] ) ) {
							$query_var = $column['sort_by_field'];
						} elseif ( 'custom_callback' === $column['type'] && ! empty( $column['callback'] ) && 'jet_engine_custom_cb_menu_order' === $column['callback'] ) {
							$query_var = 'menu_order';
						}

						if ( $query_var ) {
							$column['query_var']                  = $query_var;
							$this->sortable_columns[ $query_var ] = $column;
						}
					}
				}
			}

			if ( ! empty( $this->sortable_columns ) ) {
				add_filter( 'manage_edit-' . $post_type . '_sortable_columns', array( $this, 'sortable_columns' ) );
				add_action( 'pre_get_posts', array( $this, 'sort_columns' ) );
			}

		}

		/**
		 * Edit columns
		 *
		 * @return [type] [description]
		 */
		public function edit_columns( $columns ) {

			$new_columns = ! empty( $this->admin_columns ) ? $this->admin_columns : array();

			foreach ( $new_columns as $index => $column_data ) {

				if ( empty( $column_data['title'] ) ) {
					continue;
				}

				$column_key = sanitize_title( $column_data['title'] );

				if ( isset( $columns[ $column_key ] ) ) {
					$column_key .= '-' . $index;
				}

				$this->render_columns[ $column_key ] = $column_data;

				if ( ! empty( $column_data['position'] ) && 0 !== (int) $column_data['position'] ) {

					$length = count( $columns );

					if ( (int) $column_data['position'] > $length ) {
						$columns[ $column_key ] = $column_data['title'];
					}

					$columns_before = array_slice( $columns, 0, (int) $column_data['position'] );
					$columns_after  = array_slice( $columns, (int) $column_data['position'], $length - (int) $column_data['position'] );

					$columns = array_merge(
						$columns_before,
						array(
							$column_key => $column_data['title'],
						),
						$columns_after
					);
				} else {
					$columns[ $column_key ] = $column_data['title'];
				}
			}

			return $columns;

		}

		/**
		 * Sort columns
		 *
		 * @param  [type] $query [description]
		 * @return [type]        [description]
		 */
		public function sort_columns( $query ) {

			$post_type = $query->get( 'post_type' );

			if ( ! is_string( $post_type ) || $post_type !== $this->post_type ) {
				return;
			}

			$orderby = $query->get( 'orderby' );

			if ( is_string( $orderby ) && ! empty( $this->sortable_columns[ $orderby ] ) ) {

				$column = $this->sortable_columns[ $orderby ];

				if ( 'post_id' === $column['type'] ) {
					$query->set( 'orderby', 'ID' );
				} elseif ( ! empty( $column['callback'] ) && 'jet_engine_custom_cb_menu_order' === $column['callback'] ) {
					$query->set( 'orderby', 'menu_order' );
				} else {

					$query->set( 'meta_key', $orderby );

					if ( ! empty( $column['is_num'] ) ) {
						$query->set( 'orderby', 'meta_value_num' );
					} else {
						$query->set( 'orderby', 'meta_value' );
					}
				}

			}
		}

		/**
		 * Define sortable columns
		 *
		 * @return [type] [description]
		 */
		public function sortable_columns( $columns ) {

			foreach ( $this->sortable_columns as $column ) {

				if ( empty( $column['title'] ) ) {
					continue;
				}

				$column_key = sanitize_title( $column['title'] );

				if ( isset( $columns[ $column_key ] ) ) {
					$column_key .= '-' . $column['index'];
				}

				$columns[ $column_key ] = $column['query_var'];

			}

			return $columns;
		}

		/**
		 * Render columns content
		 *
		 * @param  string $column  current post list categories.
		 * @param  int    $post_id current post ID.
		 * @return void
		 */
		public function manage_columns( $column, $post_id ) {

			if ( empty( $this->render_columns[ $column ] ) ) {
				return;
			}

			$column_data = $this->render_columns[ $column ];
			$result      = '';

			switch ( $column_data['type'] ) {

				case 'post_id':

					$result = $post_id;

					break;

				case 'meta_value':

					if ( $column_data['meta_field'] ) {
						$result = get_post_meta( $post_id, $column_data['meta_field'], true );
					}

					break;

				case 'post_terms':

					if ( $column_data['taxonomy'] ) {

						$terms     = wp_get_post_terms( $post_id, $column_data['taxonomy'], array( 'orderby' => 'parent' ) );
						$terms_str = array();

						if ( $terms && ! is_wp_error( $terms ) ) {
							foreach ( $terms as $term ) {
								$terms_str[] = $term->name;
							}
						}

						$result = implode( ', ', $terms_str );
					}

					break;

				case 'custom_callback':

					if ( ! empty( $column_data['callback'] ) ) {

						if ( ! is_array( $column_data['callback'] ) ) {

							$callback_data = explode( '::', $column_data['callback'] );
							$callback      = $callback_data[0];

							unset( $callback_data[0] );

							if ( false !== strpos( $callback, 'jet_engine_custom_cb' ) ) {

								$args = ! empty( $callback_data ) ? array_values( $callback_data ) : array();
								$args = array_merge( array( $post_id ), $args );

								if ( is_callable( $callback ) ) {
									$result = call_user_func_array( $callback, $args );
								}

							} elseif ( is_callable( $callback ) ) {
								$result = call_user_func( $callback, $column, $post_id );
							}

						} else {
							if ( is_callable( $column_data['callback'] ) ) {
								$result = call_user_func( $column_data['callback'], $post_id, $column );
							}
						}

					}

					break;

			}

			$prefix = isset( $column_data['prefix'] ) ? do_shortcode( $column_data['prefix'] ) : '';
			$suffix = isset( $column_data['suffix'] ) ? do_shortcode( $column_data['suffix'] ) : '';

			if ( $result ) {
				echo $prefix . $result . $suffix;
			}

		}

	}

}
