<?php
namespace Jet_Engine\Modules\Custom_Content_Types;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Type Pages class
 */
class Type_Pages {

	public $factory;
	public $edit_page;
	public $action_key = 'cct_action';

	public function __construct( Factory $factory ) {

		$this->factory = $factory;
		add_action( 'admin_menu', array( $this, 'register_page' ) );

	}

	/**
	 * Initialize page
	 *
	 * @return [type] [description]
	 */
	public function init() {

		if ( $this->is_type_page() && ! empty( $_REQUEST[ $this->action_key ] ) ) {

			$item_id = $this->get_item_id();

			if ( $item_id ) {
				$item = $this->factory->db->get_item( $item_id );

				if ( empty( $item ) ) {
					wp_die( 'You attempted to edit an item that does not exist. Perhaps it was deleted?' );
				}

			} else {
				$item = array();
			}

			$this->edit_page = $this->get_edit_page_instance( $item, $_REQUEST[ $this->action_key ] );

			$this->factory->get_item_handler( $this->action_key, array(
				'save'       => $this->edit_page->save_action,
				'delete'     => $this->edit_page->delete_action,
				'clone'      => $this->edit_page->clone_action,
				'quick_edit' => $this->get_page_slug(),
			) );

			add_action( 'admin_enqueue_scripts', array( $this, 'edit_page_assets' ) );

		} else if ( $this->is_type_page() ) {

			// Add screen options.
			add_action( 'current_screen', array( $this, 'add_screen_options' ) );

			add_filter(
				'set_screen_option_' . $this->get_per_page_option_name(),
				array( $this, 'set_items_per_page_option' ), 10, 3
			);
		}

	}

	/**
	 * Returns a new instance of the edit item page
	 *
	 * @param  [type] $item [description]
	 * @return [type]       [description]
	 */
	public function get_edit_page_instance( $item, $action ) {

		if ( ! class_exists( '\Jet_Engine\Modules\Custom_Content_Types\Pages\Edit_Item_Page' ) ) {
			require_once Module::instance()->module_path( 'pages/edit-content-type-item.php' );
		}

		return new Pages\Edit_Item_Page( array(
			'slug'             => $this->get_page_slug(),
			'action'           => esc_attr( $action ),
			'fields'           => $this->factory->fields,
			'nonce'            => $this->nonce(),
			'capability'       => $this->factory->get_arg( 'capability', 'manage_options' ),
			'item'             => $item,
			'hide_field_names' => ! empty( $this->factory->args['hide_field_names'] ) ? $this->factory->args['hide_field_names'] : false,
		), $this );

	}

	/**
	 * Enqueue add/edit page specific assets
	 *
	 * @return [type] [description]
	 */
	public function edit_page_assets() {

		wp_enqueue_style(
			'jet-cct-edit-page',
			Module::instance()->module_url( 'assets/css/admin.css' ),
			array(),
			jet_engine()->get_version()
		);

	}

	/**
	 * Nonce value
	 *
	 * @return [type] [description]
	 */
	public function nonce() {
		return wp_create_nonce( 'jet-cct-nonce' );
	}

	/**
	 * Register menu page for custom content type
	 *
	 * @return [type] [description]
	 */
	public function register_page() {

		$position = $this->factory->get_arg( 'position' );

		if ( ! $position && 0 !== $position ) {
			$position = null;
		}

		add_menu_page(
			$this->factory->get_arg( 'name' ),
			$this->factory->get_arg( 'name' ),
			$this->factory->user_cap(),
			$this->get_page_slug(),
			array( $this, 'render_page' ),
			$this->factory->get_arg( 'icon' ),
			$position
		);
	}

	/**
	 * Returns page slug
	 *
	 * @return [type] [description]
	 */
	public function get_page_slug() {
		return 'jet-cct-' . $this->factory->get_arg( 'slug' );
	}

	/**
	 * Returns requested item ID
	 * @return [type] [description]
	 */
	public function get_item_id() {
		return ! empty( $_GET['item_id'] ) ? absint( $_GET['item_id'] ) : ( ! empty( $_POST['item_id'] ) ? $_POST['item_id'] : false );
	}

	/**
	 * Render page
	 *
	 * @return [type] [description]
	 */
	public function render_page() {

		$action  = ! empty( $_GET[ $this->action_key ] ) ? esc_attr( $_GET[ $this->action_key ] ) : false;

		switch ( $action ) {
			case 'add':
			case 'edit':
				$this->render_edit_page( $this->get_item_id() );
				break;

			default:
				$this->render_list_page();
				break;
		}

	}

	/**
	 * Is type admin page
	 *
	 * @return boolean [description]
	 */
	public function is_type_page() {
		return ( ! empty( $_GET['page'] ) && $this->get_page_slug() === $_GET['page'] )
				|| ( wp_doing_ajax() && ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] === $this->get_page_slug() );
	}

	/**
	 * Returns URL to one of the current type admin pages
	 *
	 * @param  string  $action  [description]
	 * @param  boolean $item_id [description]
	 * @param  boolean $status  [description]
	 * @param  boolean $nonce   [description]
	 * @return [type]           [description]
	 */
	public function page_url( $action = 'add', $item_id = false, $status = false, $nonce = false ) {

		$args = array(
			'page' => $this->get_page_slug(),
		);

		if ( $action ) {
			$args[ $this->action_key ] = $action;
		}

		if ( ! empty( $item_id ) ) {
			$args['item_id'] = $item_id;
		}

		if ( ! empty( $status ) ) {
			$args['status'] = $status;
		}

		if ( $nonce ) {
			$args['_nonce'] = $this->nonce();
		}

		return add_query_arg( $args, esc_url( admin_url( 'admin.php' ) ) );

	}

	/**
	 * Render add/edit item page
	 *
	 * @return [type] [description]
	 */
	public function render_edit_page( $item_id = false ) {

		?>
		<div class="wrap">
		<?php
			$this->edit_page->render_page();
		?>
		</div>
		<?php

	}

	/**
	 * Render list page
	 *
	 * @return [type] [description]
	 */
	public function render_list_page() {

		?>
		<div class="wrap">
			<style type="text/css">
				td.column-cct_item_id,
				th.column-cct_item_id {
					width: 90px;
				}
				.jet-cct-actions .submitdelete {
					color: #a00;
				}
				.jet-cct-actions .submitdelete:hover {
					color: #dc3232;
				}
				.cct-heading {
					display:flex;
					align-items: center;
					padding: 15px 0 0 0;
					flex-wrap: wrap;
					overflow: hidden;
				}
				.cct-heading h2 {
					margin: 0 10px 7px 0;
				}
				.cct-heading > a {
					margin: 0 15px;
				}
				.cct-heading .notice {
					flex: 0 0 100%;
					order: 99;
					box-sizing: border-box;
				}
				.jet-cct-actions {
					display: flex;
					flex-wrap: wrap;
				}
			</style>
			<div class="cct-heading" style="">
				<h2><?php echo $this->factory->get_arg( 'name' ); ?></h2>
				<a href="<?php echo $this->page_url(); ?>" class="page-title-action"><?php
					_e( 'Add New', 'jet-engine' );
				?></a>
				<?php Module::instance()->export->ui( $this->factory->get_arg( 'slug' ) ); ?>
			</div>
			<script>
				jQuery( document ).on( 'click', '.jet-cct-actions .submitdelete', function( event ) {
					event.preventDefault();
					if ( confirm( '<?php esc_html_e( 'Are you sure you want to delete this item?', 'jet-engine' ); ?>' ) ) {
						window.location = jQuery( this ).attr( 'href' );
					}
				});
			</script>
			<form id="items-filter" method="get">
				<input type="hidden" name="page" value="<?php echo $this->get_page_slug(); ?>" />
				<?php

					require Module::instance()->module_path( 'list-table.php' );

					$items_table = new List_Table();

					$items_table->set_factory( $this->factory );

					$items_table->prepare_items();
					$items_table->search_box( 'search', 'items_search' );
					$items_table->views();
					$items_table->display();

				?>
			</form>
		</div>
		<?php

	}

	public function add_screen_options() {
		add_screen_option(
			'per_page',
			array(
				'default' => 30,
				'option'  => $this->get_per_page_option_name(),
			)
		);
	}

	public function get_per_page_option_name() {
		return str_replace( '-', '_', $this->get_page_slug() ) . '_per_page';
	}

	public function set_items_per_page_option( $status, $option, $value ) {
		return $value;
	}

}
