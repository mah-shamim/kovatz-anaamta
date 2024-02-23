<?php
/**
 * Quick edit manager
 *
 * Fields supported at the moment:
 * - text
 * - number
 * - date
 * - time
 * - datetime-local
 * - textarea
 * - checkbox (only with Save as array enabled)
 * - radio
 * - select
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CPT_Quick_Edit' ) ) {

	/**
	 * Define Jet_Engine_CPT_Quick_Edit class
	 */
	class Jet_Engine_CPT_Quick_Edit {

		public static $hook_after_save_all = array();

		public $post_type   = null;
		public $field       = null;
		public $trigger_col = 'jet_engine_quick_edit';

		public static $add_inline_css = false;
		public static $is_first = true;

		public function __construct( $post_type, $field ) {

			$this->post_type = $post_type;
			$this->field     = $field;

			add_filter( 'manage_' . $this->get_post_type() . '_posts_columns', array( $this, 'register_quick_edit_trigger' ), 10, 2 );
			add_action( 'manage_' . $this->get_post_type() . '_posts_custom_column', array( $this, 'set_column_value' ), 10, 2 );
			add_filter( 'hidden_columns', array( $this, 'hide_quick_edit_trigger' ), 10, 2 );

			add_action( 'quick_edit_custom_box', array( $this, 'render_control' ), 10, 2 );
			add_action( 'save_post_' . $this->post_type, array( $this, 'save_field' ), 5 );

			if ( ! in_array( $this->post_type, self::$hook_after_save_all ) ) {
				add_action( 'save_post_' . $this->post_type, array( $this, 'after_save_all' ), 6 );
				self::$hook_after_save_all[] = $this->post_type;
			}

		}

		/**
		 * Run cx_post_meta/after_save hook after save all quick edit boxes to better compatibility with Cherry_X_Post_Meta
		 *
		 * @return void
		 */
		public function after_save_all() {
			$post_id = ! empty( $_REQUEST['post_ID'] ) ? $_REQUEST['post_ID'] : false;
			do_action( 'cx_post_meta/after_save', $post_id, get_post( $post_id ) );
		}

		/**
		 * Post type slug current field is related to
		 *
		 * @return string
		 */
		public function get_post_type() {
			return $this->post_type;
		}

		/**
		 * Save field on quick edit update call
		 *
		 * @return null
		 */
		public function save_field() {

			if ( empty( $_REQUEST['post_type'] ) || $this->get_post_type() !== $_REQUEST['post_type'] ) {
				return;
			}

			$quick_edit = ! empty( $_REQUEST['jet_engine_quick_edit'] ) ? $_REQUEST['jet_engine_quick_edit'] : array();

			if ( empty( $quick_edit ) || ! is_array( $quick_edit ) || ! in_array( $this->get_trigger_col(), $quick_edit ) ) {
				return;
			}

			$post_id = ! empty( $_REQUEST['post_ID'] ) ? $_REQUEST['post_ID'] : false;

			if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			$post_meta = new Cherry_X_Post_Meta();
			$post_meta->args = array(
				'fields' => array(
					$this->get_field( 'name' ) => $this->get_field(),
				)
			);

			if ( 'checkbox' === $this->get_field( 'type' ) ) {

				$value = ! empty( $_POST[ $this->get_field( 'name' ) ] ) ? $_POST[ $this->get_field( 'name' ) ] : array();

				if ( ! is_array( $value ) ) {
					$value = array();
				}

				update_post_meta( $post_id, $this->get_field( 'name' ), $value );

				// save custom support
				if ( ! empty( $value ) ) {
					$_POST[ $this->get_field( 'name' ) ] = array();
					foreach ( $value as $val ) {
						$_POST[ $this->get_field( 'name' ) ][ $val ] = true;
					}
				}

			} else {
				$post_meta->save_meta_option( $post_id );
			}

		}

		/**
		 * Get current field data or spicified argument
		 *
		 * @param  [type] $key [description]
		 * @return [type]      [description]
		 */
		public function get_field( $key = null ) {

			$field = $this->field;

			if ( ! $key ) {
				return $field;
			} else {
				return isset( $field[ $key ] ) ? $field[ $key ] : null;
			}

		}

		/**
		 * Set field arg
		 *
		 * @param [type] $key   [description]
		 * @param [type] $value [description]
		 */
		public function set_field( $key, $value ) {
			$this->field[ $key ] = $value;
		}

		/**
		 * Get column name related to current field
		 *
		 * @return [type] [description]
		 */
		public function get_trigger_col() {
			return $this->trigger_col . '_' . $this->get_post_type() . '_' . $this->get_field( 'name' );
		}

		/**
		 * Hide column related to current field
		 *
		 * @param  [type] $hidden [description]
		 * @param  [type] $screen [description]
		 * @return [type]         [description]
		 */
		public function hide_quick_edit_trigger( $hidden, $screen ) {

			if ( 'edit' === $screen->base && $this->get_post_type() !== $screen->post_type ) {
				return $hidden;
			}

			if ( ! in_array( $this->get_trigger_col(), $hidden ) ) {
				$hidden[] = $this->get_trigger_col();
			}

			return $hidden;

		}

		/**
		 * Print current field value into prepared column
		 *
		 * @param [type] $column  [description]
		 * @param [type] $post_id [description]
		 */
		public function set_column_value( $column, $post_id ) {

			if ( $column !== $this->get_trigger_col() ) {
				return;
			}

			$post      = get_post( $post_id );
			$post_meta = new Cherry_X_Post_Meta();
			$value     = $post_meta->get_meta( $post, $this->get_field( 'name' ), false, $this->get_field() );

			if ( 'select' === $this->get_field( 'type' ) && $this->get_field( 'multiple' ) && ! is_array( $value ) ) {
				$value = array( $value );
			} elseif ( 'select' === $this->get_field( 'type' ) && ! $this->get_field( 'multiple' ) && is_array( $value ) ) {
				$value = $value[0];
			}

			if ( 'radio' === $this->get_field( 'type' ) ) {
				$this->set_field( 'value', '' );
			}

			if ( 'checkbox' === $this->get_field( 'type' ) ) {
				$this->set_field( 'type', 'checkbox-raw' );
				$this->set_field( 'value', array() );
			}

			if ( 'text' === $this->get_field( 'type' ) && ! empty( $value )
				 && $post_meta->to_timestamp( $this->get_field() ) && is_numeric( $value )
			) {

				switch ( $this->get_field( 'input_type' ) ) {
					case 'date':
						$value = date( 'Y-m-d', $value );
						break;

					case 'datetime-local':
						$value = date( 'Y-m-d\TH:i', $value );
						break;
				}
			}

			printf(
				'<div data-jet-engine-quick-edit-val="%1$s" data-jet-engine-quick-edit-type="%3$s">%2$s</div>',
				$this->get_field( 'name' ),
				htmlentities( json_encode( $value ) ),
				$this->get_field( 'type' )
			);
		}

		/**
		 * Register related columne for current field
		 *
		 * @param  [type] $columns [description]
		 * @return [type]          [description]
		 */
		public function register_quick_edit_trigger( $columns ) {

			$columns = array_merge(
				array( $this->get_trigger_col() => '' ),
				$columns
			);

			return $columns;

		}

		/**
		 * Render field related control into quick edit section
		 *
		 * @param  [type] $column    [description]
		 * @param  [type] $post_type [description]
		 * @return [type]            [description]
		 */
		public function render_control( $column, $post_type ) {

			if ( $post_type !== $this->get_post_type() || $column !== $this->get_trigger_col() ) {
				return;
			}

			$field = $this->get_field();

			$builder_data = jet_engine()->framework->get_included_module_data( 'cherry-x-interface-builder.php' );
			$builder      = new CX_Interface_Builder(
				array(
					'path' => $builder_data['path'],
					'url'  => $builder_data['url'],
				)
			);

			$field['id'] = $field['name'];

			if ( self::$is_first ) {
				self::$is_first = false;
				echo '<div class="cx-ui-clear"></div>';
			}

			$builder->register_control( $field );
			$builder->render();

			printf( '<input type="hidden" name="jet_engine_quick_edit[]" value="%s">', $this->get_trigger_col() );

			if ( false === self::$add_inline_css ) {

				self::$add_inline_css = true;
				echo '<style>
					.inline-edit-row .cx-ui-clear {
						clear: both;
						padding: 15px 0 0 0;
					}
					.inline-edit-row .cx-control {
						display: inline-flex;
						padding: 10px 10px 10px 0;
						width: 24.5%;
						min-width: 250px;
						box-sizing: border-box;
						justify-content: flex-between;
					}
					.inline-edit-row .cx-control select[multiple] {
						height: 120px;
					}
					.inline-edit-row .cx-control__info {
						flex: 0 0 110px;
					}
					.inline-edit-row .cx-ui-kit__description {
						font-size: 12px;
						font-style: italic;
						margin: -5px 0 0 0;
						opacity: .7;
					}
					.inline-edit-row .cx-ui-kit__content {
						flex: 0 0 calc( 100% - 115px );
					}
				</style>';

				ob_start();
				include jet_engine()->meta_boxes->component_path( 'assets/js/inline-quick-edit.js' );
				wp_add_inline_script( 'inline-edit-post', ob_get_clean() );

			}

		}

	}

}
