<?php
namespace Jet_Engine\Modules\Custom_Content_Types;

if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class List_Table extends \WP_List_Table {

	private $factory;
	private $per_page;

	function __construct( $args = array() ) {

		global $status, $page;

		$args = wp_parse_args( $args, array(
			'singular' => 'item',
			'plural'   => 'items',
			'ajax'     => false
		) );

		//Set parent defaults
		parent::__construct( $args );

		$this->per_page = 30;

	}

	/**
	 * Setup factory object for the current instance
	 *
	 * @param [type] $page [description]
	 */
	public function set_factory( $factory ) {
		$this->factory = $factory;
	}


	/**
	 * Output column data
	 *
	 * @access      private
	 * @since       1.0
	 * @return      void
	 */
	public function column_default( $item, $column_name ) {

		$admin_columns = $this->factory->get_admin_columns();

		if ( empty( $admin_columns[ $column_name ] ) ) {
			return '--';
		}

		$data = $admin_columns[ $column_name ];

		if ( empty( $data['_cb'] ) ) {
			$value = isset( $item[ $column_name ] ) ? $item[ $column_name ] : '--';
			return $this->prepare_column_value( $value, $data );
		}

		$value = isset( $item[ $column_name ] ) ? $item[ $column_name ] : false;

		switch ( $data['_cb'] ) {

			case 'date_i18n':
				$format = ! empty( $data['date_format'] ) ? $data['date_format'] : get_option( 'date_format' );
				$value  = date_i18n( $format, $value );

				break;

			case 'wp_get_attachment_image':

				$img  = \Jet_Engine_Tools::get_attachment_image_data_array( $value, 'id' );
				$size = ! empty( $data['image_size'] ) ? $data['image_size'] : array( 50, 50 );

				if ( $img && is_array( $img ) && ! empty( $img['id'] ) ) {
					$value = wp_get_attachment_image( $img['id'], $size );
				}

				break;

			case 'jet_engine_render_simple_gallery':

				$size  = ! empty( $data['image_size'] ) ? $data['image_size'] : 50;
				$value = jet_engine_render_simple_gallery( $value, $size );

				break;

			default:
				$value = call_user_func( $data['_cb'], $value, $item, $data, $column_name );

				break;

		}

		return $this->prepare_column_value( $value, $data );

	}

	public function prepare_column_value( $value = null, $data = array() ) {

		if ( is_array( $value ) ) {
			$value = $this->convert_array( $value );
		}

		if ( ! empty( $data['prefix'] ) ) {
			$value = $data['prefix'] . $value;
		}

		if ( ! empty( $data['suffix'] ) ) {
			$value = $value . $data['suffix'];
		}

		return $value;
	}

	public function convert_array( $value = null, $glue = false ) {

		$children_glue = false;

		$res = '';

		if ( ! is_array( $value ) ) {
			return $value;
		} else {
			foreach ( $value as $child ) {

				if ( ! $glue ) {
					if ( is_array( $child ) ) {
						$glue = "\n";
						$children_glue = '; ';
					} else {
						$glue = '; ';
					}
				} elseif ( is_array( $child ) ) {
					if ( '; ' === $glue ) {
						$children_glue = ', ';
					}
				}

				$res .= $this->convert_array( $child, $children_glue ) . $glue;

			}

			$res = rtrim( $res, $glue );
		}

		return $res;
	}

	/**
	 * Output the checkbox column
	 */
	public function column_cb( $item ) {

		return sprintf(
			'<input type="checkbox" name="%1$s_id[]" value="%2$s" />',
			esc_attr( $this->_args['singular'] ),
			esc_attr( $item['_ID'] )
		);

	}

	/**
	 * Output the item ID column
	 */
	public function column_cct_item_id( $item ) {
		$result = '#'. $item['_ID'];

		if ( 'publish' !== $item['cct_status'] ) {
			$available_statuses = $this->factory->get_statuses();
			$result .= ' — <b class="post-state">' . $available_statuses[ $item['cct_status'] ] . '</b>';
		}

		return $result;
	}

	/**
	 * Output the actions column
	 */
	public function column_cct_item_actions( $item ) {

		$edit_url        = $this->factory->admin_pages->page_url( 'edit', $item['_ID'] );
		$clone_url       = $this->factory->admin_pages->page_url( 'jet-cct-clone-item', $item['_ID'], false, true );
		$delete_url      = $this->factory->admin_pages->page_url( 'jet-cct-delete-item', $item['_ID'], false, true );
		$quick_edit_link = '';

		if ( $this->has_quick_edit_columns() ) {

			$quick_edit_data = array(
				'data-quick-edit-values="' . htmlspecialchars( $this->get_saved_quick_edit_data( $item, true ) ) . '"',
				'data-quick-edit-id="' . $item['_ID'] . '"',
				'data-quick-edit-nonce="' . $this->factory->admin_pages->nonce() . '"',
				'data-quick-edit-action="' . $this->factory->admin_pages->get_page_slug() . '"',
			);

			$quick_edit_link   = '<span class="edit"><a href="#" class="jet-engine-cct-quick-edit" ' . implode( ' ', $quick_edit_data ) . '>' . __( 'Quick Edit', 'jet-engine' ) . '</a>&nbsp;|&nbsp;</span>';
		}

		return sprintf(
			'<div class="jet-cct-actions"><span class="edit"><a href="%2$s" aria-label="%6$s #%1$d">%4$s</a>&nbsp;|&nbsp;</span>%11$s<span class="edit"><a href="%8$s" aria-label="%9$s #%1$d">%10$s</a>&nbsp;|&nbsp;</span><a href="%3$s" class="submitdelete" aria-label="%7$s #%1$d">%5$s</a></span></div>',
			$item['_ID'],
			$edit_url,
			$delete_url,
			__( 'Edit', 'jet-engine' ),
			__( 'Delete', 'jet-engine' ),
			__( 'Edit Item', 'jet-engine' ),
			__( 'Delete Item', 'jet-engine' ),
			$clone_url,
			__( 'Duplicate Item', 'jet-engine' ),
			__( 'Clone', 'jet-engine' ),
			$quick_edit_link
		);
	}

	/**
	 * Check if CCT has quic editable columns
	 * @return boolean [description]
	 */
	public function has_quick_edit_columns() {
		$columns = $this->factory->get_quick_edit_columns();
		return ! empty( $columns );
	}

	/**
	 * Returns data for saved columns
	 * @param  array   $item   [description]
	 * @param  boolean $encode [description]
	 * @return [type]          [description]
	 */
	public function get_saved_quick_edit_data( $item = array(), $encode = false ) {

		if ( $this->has_quick_edit_columns() ) {
			$columns = $this->factory->get_quick_edit_columns();
		} else {
			$columns = array();
		}

		$result = array();

		foreach ( $columns as $col => $data ) {
			if ( isset( $item[ $col ] ) ) {

				$value = ! empty( $item[ $col ] ) ? $item[ $col ] : '';

				if ( ! empty( $data['is_timestamp'] ) && \Jet_Engine_Tools::is_valid_timestamp( $value ) ) {
					switch ( $data['type'] ) {
						case 'date':
							$value = date( 'Y-m-d', $value );
							break;

						case 'datetime-local':
							$value = date( 'Y-m-d\TH:i', $value );
							break;
					}
				}

				$result[ $col ] = array(
					'value' => $value,
					'type'  => $data['type'],
				);
			}
		}

		if ( $encode ) {
			$result = json_encode( $result );
		}

		return $result;

	}

	public function quick_edit_template() {

		if ( ! $this->has_quick_edit_columns() ) {
			return;
		}

		$cols = $this->get_columns();
		ob_start();

		if ( ! class_exists( 'Quick_Edit' ) ) {
			require Module::instance()->module_path( 'quick-edit.php' );
		}

		$quick_edit = new Quick_Edit( array(
			'fields' => $this->factory->get_quick_edit_columns(),
		), false );

		$quick_edit->render_fields();

		echo '<div class="submit inline-edit-save">
			<button type="button" class="button jet-engine-cct-quick-edit--cancel alignleft">Cancel</button>
			<button type="button" class="button button-primary jet-engine-cct-quick-edit--save alignright">Update</button><span class="spinner"></span>
			<div class="clear"></div>
			<div class="notice notice-error notice-alt inline hidden">
				<p class="error"></p>
			</div>
		</div>';

		$template = ob_get_clean();

		printf( '<template id="jet_engine_cct_quick_edit_template"><td colspan="%2$d"><form method="post">%1$s</form></td></template>', $template, count( $cols ) );
	}

	/**
	 * Setup columns
	 */
	public function get_columns() {

		$columns = array(
			'cb' => '<input type="checkbox"/>',
		);

		$admin_columns = $this->factory->get_admin_columns();

		foreach ( $admin_columns as $column => $data ) {
			$columns[ $column ] = $data['title'];
		}

		$columns['cct_item_actions'] = __( 'Actions', 'jet-engine' );

		return $columns;

	}

	/**
	 * Retrieve the table's sortable columns
	 */
	public function get_sortable_columns() {

		$columns  = $this->factory->get_admin_columns();
		$sortable = array();

		foreach ( $columns as $name => $column ) {
			if ( ! empty( $column['is_sortable'] ) ) {
				$sortable[ $name ] = array( $name, false );
			}
		}

		return $sortable;
	}

	/**
	 * Setup available views
	 */

	public function get_views() {

		$views = array();

		return $views;

	}


	/**
	 * Retrieve the current page number
	 */
	public function get_paged() {
		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}


	/**
	 * Retrieve the total number of items
	 */
	public function get_total_items() {
		return $this->factory->db->count();
	}

	/**
	 * Setup available bulk actions
	 */

	public function get_bulk_actions() {

		$actions = array(
			'delete'            => __( 'Delete', 'jet-engine' ),
			'switch_to_draft'   => __( 'Switch status to draft', 'jet-engine' ),
			'switch_to_publish' => __( 'Switch status to publish', 'jet-engine' ),
		);

		return $actions;

	}


	/**
	 * Process bulk actions
	 */
	public function process_bulk_action() {

		if( empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-items' ) ) {
			return;
		}

		$ids = isset( $_GET['item_id'] ) ? $_GET['item_id'] : false;

		if( ! $ids ) {
			return;
		}

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		foreach ( $ids as $id ) {

			switch ( $this->current_action() ) {
				case 'delete':
					$this->factory->db->delete( array( '_ID' => $id ) );
					break;

				case 'switch_to_draft':
					$this->factory->db->update( array( 'cct_status' => 'draft' ), array( '_ID' => $id ) );
					break;

				case 'switch_to_publish':
					$this->factory->db->update( array( 'cct_status' => 'publish' ), array( '_ID' => $id ) );
					break;
			}

		}

	}

	/** ************************************************************************
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 **************************************************************************/
	public function prepare_items( $args = array() ) {

		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = $this->get_items_per_page( $this->factory->admin_pages->get_per_page_option_name(), $this->per_page );

		add_thickbox();

		$admin_columns = $this->factory->get_admin_columns();
		$columns       = $this->get_columns();
		$hidden        = array(); // no hidden columns

		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

		$order = array();

		if ( ! empty( $_GET['orderby'] ) && ! empty( $_GET['order'] ) ) {

			$sort_column = isset( $admin_columns[ $_GET['orderby'] ] ) ? $admin_columns[ $_GET['orderby'] ] : false;

			if ( $sort_column ) {
				$order[] = array(
					'orderby' => esc_attr( $_GET['orderby'] ),
					'order'   => esc_attr( $_GET['order'] ),
					'type'    => ! empty( $sort_column['is_num'] ) ? 'integer' : false,
				);
			}
		}

		if ( ! empty( $args ) ) {
			$args = array_merge( $args, $this->build_search_args() );
		} else {
			$args = $this->build_search_args();
		}

		$current_page = $this->get_pagenum();
		$total_items  = $this->get_total_items();
		$this->items  = $this->factory->db->query( $args, $per_page, $per_page * ( $current_page - 1 ), $order );

		$pagination_args = array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		);

		$this->set_pagination_args( $pagination_args );

	}


	/**
	 * Build the args array for search and count comment_form_default_fields*
	 *
	 * @since 3.5
	 * @param array $args The existing args
	 * @return array $args The updated args
	 */
	public function build_search_args( $args = array() ) {

		// check to see if we are searching
		if( ! empty( $_GET['s'] ) ) {

			$skip_types           = array( 'html' );
			$allowed_object_types = array( 'field', 'service_field' );
			$all_fields           = $this->factory->fields;
			$search_in            = array();

			foreach ( $all_fields as $field ) {

				if ( ! empty( $field['object_type'] ) && ! in_array( $field['object_type'], $allowed_object_types ) ) {
					continue;
				}

				if ( in_array( $field['type'], $skip_types ) ) {
					continue;
				}

				$search_in[] = $field['name'];
			}

			$args['_cct_search'] = array(
				'keyword' => sanitize_text_field( trim( $_GET['s'] ) ),
				'fields'  => $search_in,
			);
		}

		return $args;

	}

	public function quick_edit_assets() {

		wp_enqueue_script(
			'jet-engine-cct-quick-edit',
			Module::instance()->module_url( 'assets/js/admin/quick-edit.js' ),
			array( 'jquery' ),
			jet_engine()->get_version(),
			true
		);

		wp_enqueue_style( 'jet-engine-cct-quick-edit', Module::instance()->module_url( 'assets/css/quick-edit.css' ) );

	}

	public function display_tablenav( $which ) {

		parent::display_tablenav( $which );

		if ( 'bottom' === $which ) {
			$this->quick_edit_template();
			$this->quick_edit_assets();
		}

	}

}
