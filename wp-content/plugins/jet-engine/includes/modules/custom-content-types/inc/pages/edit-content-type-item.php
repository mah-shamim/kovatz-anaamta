<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Pages;

use Jet_Engine\Modules\Custom_Content_Types\Module;
use Jet_Engine\Modules\Custom_Content_Types\DB;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Options_Page_Factory class
 */
class Edit_Item_Page extends \Jet_Engine_Options_Page_Factory {

	/**
	 * Current page data
	 *
	 * @var null
	 */
	public $page = null;

	/**
	 * Current page slug
	 *
	 * @var null
	 */
	public $slug = null;

	/**
	 * Prepared fields array
	 *
	 * @var null
	 */
	public $prepared_fields = null;

	/**
	 * Holder for is page or not is page now prop
	 *
	 * @var null
	 */
	public $is_page_now = null;

	/**
	 * Inerface builder instance
	 *
	 * @var null
	 */
	public $builder = null;

	/**
	 * Saved options holder
	 *
	 * @var null
	 */
	public $options = null;

	/**
	 * Save trigger
	 *
	 * @var string
	 */
	public $save_action = 'jet-cct-save-item';

	/**
	 * Delete trigger
	 *
	 * @var string
	 */
	public $delete_action = 'jet-cct-delete-item';

	/**
	 * Clone trigger
	 *
	 * @var string
	 */
	public $clone_action = 'jet-cct-clone-item';

	public $layout_now        = false;
	public $current_component = false;
	public $current_panel     = false;

	private $pages_manager = null;

	/**
	 * Constructor for the class
	 */
	public function __construct( $page, $pages_manager ) {

		$this->page             = $page;
		$this->slug             = $page['slug'];
		$this->action           = $page['action'];
		$this->meta_box         = $page['fields'];
		$this->pages_manager    = $pages_manager;
		$this->hide_field_names = $page['hide_field_names'];

		if ( $this->is_page_now() ) {

			$this->setup_page_fields();

			add_action( 'admin_enqueue_scripts', array( $this, 'init_builder' ), 0 );
			add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_inline_js' ), 20 );

			add_action( 'admin_init', array( $this, 'save' ), 40 );
			add_action( 'admin_notices', array( $this, 'saved_notice' ) );
		}

	}

	public function setup_page_fields() {

		foreach ( $this->page['fields'] as $index => $field ) {
			if ( ! empty( $field['object_type'] ) && 'service_field' === $field['object_type'] ) {
				unset( $this->meta_box[ $index ] );
				unset( $this->page['fields'][ $index ] );
			}
		}

		$this->meta_box       = $this->prepare_meta_fields( $this->meta_box );
		$this->page['fields'] = $this->meta_box;
	}

	/**
	 * Check if current options page is processed now
	 *
	 * @return boolean [description]
	 */
	public function is_page_now() {

		if ( null !== $this->is_page_now ) {
			return $this->is_page_now;
		}

		if ( isset( $_GET['page'] ) && $this->slug === $_GET['page'] && ! empty( $_GET[ $this->pages_manager->action_key ] ) ) {
			$this->is_page_now = true;
		} else {
			$this->is_page_now = false;
		}

		return $this->is_page_now;

	}

	/**
	 * Get saved options
	 *
	 * @param  [type]  $option [description]
	 * @param  boolean $default [description]
	 * @return [type]           [description]
	 */
	public function get( $option = null, $default = false, $field = array() ) {

		$item  = ! empty( $this->page['item'] ) ? $this->page['item'] : array();
		$value = isset( $item[ $option ] ) ? wp_unslash( $this->pages_manager->factory->maybe_from_timestamp( $item[ $option ], $field ) ) : $default;

		return $value;
	}

	/**
	 * Show saved notice
	 *
	 * @return bool
	 */
	public function saved_notice() {

		if ( ! isset( $_GET['dialog-saved'] ) ) {
			return false;
		}

		$message = __( 'Saved', 'jet-engine' );

		printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', $message );

		return true;

	}

	/**
	 * Initialize page builder
	 *
	 * @return [type] [description]
	 */
	public function init_builder() {

		$builder_data = jet_engine()->framework->get_included_module_data( 'cherry-x-interface-builder.php' );

		$this->builder = new \CX_Interface_Builder(
			array(
				'path' => $builder_data['path'],
				'url'  => $builder_data['url'],
			)
		);

		$slug    = $this->page['slug'];
		$item_id = $this->pages_manager->get_item_id();

		if ( 'edit' === $this->page['action'] ) {
			$page_name    = __( 'Edit Item', 'jet-engine' );
			$button_label = __( 'Save', 'jet-engine' );
		} else {
			$page_name    = __( 'Add Item', 'jet-engine' );
			$button_label = __( 'Add', 'jet-engine' );
		}

		$description = '<div>';

		if ( $item_id ) {

			$description .= sprintf(
				'<a href="%1$s" class="cx-button cx-button-default-style cct-confirm">%2$s</a>',
				$this->pages_manager->page_url(),
				__( 'New item', 'jet-engine' )
			);

			$description .= '&nbsp;&nbsp;&nbsp;&nbsp;';
		}

		$description .= sprintf(
			'<a href="%1$s" class="cx-button cx-button-default-style cct-confirm">%2$s</a>',
			$this->pages_manager->page_url( false ),
			__( 'Back to the items list', 'jet-engine' )
		);

		$description .= '</div>';

		$this->builder->register_section(
			array(
				$slug => array(
					'type'        => 'section',
					'scroll'      => false,
					'title'       => $page_name,
					'description' => $description,
				),
			)
		);

		$this->builder->register_form(
			array(
				$slug . '_form' => array(
					'type'   => 'form',
					'parent' => $slug,
					'action' => $this->pages_manager->page_url( $this->save_action, $item_id ),
				),
			)
		);

		$this->builder->register_settings(
			array(
				'settings_top' => array(
					'type'   => 'settings',
					'parent' => $slug . '_form',
				),
				'settings_bottom' => array(
					'type'   => 'settings',
					'parent' => $slug . '_form',
				),
			)
		);

		if ( ! empty( $this->page['fields'] ) ) {

			$this->builder->register_control(
				$this->get_prepared_fields()
			);

			$has_single = $this->pages_manager->factory->get_arg( 'has_single' );
			$post_id    = $this->get( 'cct_single_post_id' );
			$post_link  = false;

			if ( $post_id ) {
				$post_link = get_edit_post_link( absint( $post_id ), 'url' );
			}

			if ( $post_link ) {

				$single_link = sprintf(
					'<a href="%1$s" target="_blank">%2$s</a>',
					$post_link,
					__( 'Go to single post', 'jet-engine' )
				);

				$this->builder->register_html(
					array(
						'go_to_single' => array(
							'type'   => 'html',
							'parent' => 'settings_top',
							'class'  => 'cx-control',
							'html'   => '<div class="cx-control__info"><h4 class="cx-ui-kit__title cx-control__title" role="banner">' . __( 'Single post', 'jet-engine' ) . '</h4><div class="cx-ui-kit__description cx-control__description" role="note">' . __( 'Link to the single post related to current item', 'jet-engine' ) . '</div></div><div class="cx-ui-kit__content cx-control__content" role="group"><div class="cx-ui-container ">' . $single_link . '</div></div>',
						),
					)
				);
			}

		}

		$additional_data = '<input type="hidden" name="cct_nonce" value="' . $this->page['nonce'] . '">';

		if ( 'edit' === $this->page['action'] ) {

			$delete_url = $this->pages_manager->page_url( $this->delete_action, $item_id, false, true );

			$additional_data .= '<a href="' . $delete_url . '" class="jet-cct-delete-item">' . __( 'Delete item', 'jet-engine' ) . '</a>';

		}

		$this->builder->register_control(
			array(
				'cct_status' => array(
					'type'    => 'select',
					'parent'  => 'settings_bottom',
					'id'      => 'cct_status',
					'name'    => 'cct_status',
					'label'   => __( 'Item status', 'jet-engine' ),
					'value'   => $this->get( 'cct_status' ),
					'options' => $this->pages_manager->factory->get_statuses(),
				),
			)
		);

		$this->builder->register_html(
			array(
				'save_button' => array(
					'type'   => 'html',
					'parent' => 'settings_bottom',
					'class'  => 'cx-component dialog-save',
					'html'   => '<button type="submit" class="cx-button cx-button-primary-style">' . $button_label . '</button>' . $additional_data,
				),
			)
		);

		$this->print_custom_css();

	}

	/**
	 * Render options page
	 *
	 * @return [type] [description]
	 */
	public function render_page() {
		?>
		<style type="text/css">
			.jet-cct-edit-page-wrap .cx-section__info {
				display: flex;
				justify-content: space-between;
				align-items: center;
			}
			.jet-cct-edit-page-wrap .cx-section__description {
				margin: 0;
			}
		</style>
		<script>
			jQuery( document ).on( 'click', '.cct-confirm', function( event ) {
				event.preventDefault();
				if ( confirm( '<?php esc_html_e( 'Are you sure? All unsaved changes will be lost.', 'jet-engine' ); ?>' ) ) {
					window.location = jQuery( this ).attr( 'href' );
				}
			});
			jQuery( document ).on( 'click', '.jet-cct-delete-item', function( event ) {
				event.preventDefault();
				if ( confirm( '<?php esc_html_e( 'Are you sure you want to delete this item?', 'jet-engine' ); ?>' ) ) {
					window.location = jQuery( this ).attr( 'href' );
				}
			});
		</script>
		<div class="jet-cct-edit-page-wrap">
		<?php $this->builder->render(); ?>
		<?php do_action( 'jet-engine/custom-content-types/after-edit-page/' . $this->pages_manager->factory->get_arg( 'slug' ), $this ); ?>
		</div>
		<?php
	}

}
