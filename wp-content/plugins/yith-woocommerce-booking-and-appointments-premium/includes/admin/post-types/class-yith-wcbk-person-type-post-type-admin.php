<?php
/**
 * Class YITH_WCBK_Booking_Person_Type_Post_Type_Admin
 *
 * Handles the Booking post type on admin side.
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Booking_Person_Type_Post_Type_Admin' ) ) {
	/**
	 * Class YITH_WCBK_Booking_Person_Type_Post_Type_Admin
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Booking_Person_Type_Post_Type_Admin extends YITH_Post_Type_Admin {
		/**
		 * The post type.
		 *
		 * @var string
		 */
		protected $post_type = YITH_WCBK_Post_Types::PERSON_TYPE;

		/**
		 * Initialize the WP List handlers.
		 */
		public function init_wp_list_handlers() {
			parent::init_wp_list_handlers();
			if ( $this->should_wp_list_handlers_be_loaded() ) {
				add_action( 'admin_footer', array( $this, 'render_add_post_form' ) );
				$this->maybe_redirect_to_main_list();
			}
		}

		/**
		 * Redirect to main list if the current view is 'trash' and there are no post.
		 */
		private function maybe_redirect_to_main_list() {
			$post_status = wc_clean( wp_unslash( $_REQUEST['post_status'] ?? 'any' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( 'trash' === $post_status ) {
				$counts = (array) wp_count_posts( $this->post_type );
				unset( $counts['auto-draft'] );
				$count = array_sum( $counts );

				if ( 0 < $count ) {
					return;
				}

				$args = array(
					'post_type' => $this->post_type,
					'deleted'   => isset( $_GET['deleted'] ) ? wc_clean( wp_unslash( $_GET['deleted'] ) ) : null, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				);

				$list_url = add_query_arg( $args, admin_url( 'edit.php' ) );

				wp_safe_redirect( $list_url );
				exit();
			}
		}

		/**
		 * Render add post form.
		 */
		public function render_add_post_form() {
			global $post_type, $post_type_object;

			$fields = array(
				'name'        => array(
					'label'             => __( 'Name', 'yith-booking-for-woocommerce' ),
					'name'              => 'name',
					'type'              => 'text',
					'desc'              => __( 'Enter a name to identify the person type.', 'yith-booking-for-woocommerce' ),
					'custom_attributes' => array(
						'required' => 'required',
					),
				),
				'description' => array(
					'label' => __( 'Description', 'yith-booking-for-woocommerce' ),
					'name'  => 'description',
					'type'  => 'textarea',
					'desc'  => __( 'Enter a description.', 'yith-booking-for-woocommerce' ),
				),
			);

			$post_status = wc_clean( wp_unslash( $_REQUEST['post_status'] ?? 'any' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( $post_type && $post_type_object && current_user_can( $post_type_object->cap->create_posts ) && in_array( $post_status, array( 'any', 'publish' ), true ) ) {
				yith_wcbk_get_view( 'post-types/new-post-form.php', compact( 'fields' ) );
			}
		}

		/**
		 * Retrieve an array of parameters for blank state.
		 *
		 * @return array{
		 * @type string $icon_url The icon URL.
		 * @type string $message  The message to be shown.
		 * @type string $cta      The call-to-action button title.
		 * @type string $cta_icon The call-to-action button icon.
		 * @type string $cta_url  The call-to-action button URL.
		 *              }
		 */
		protected function get_blank_state_params() {
			return array(
				'icon'    => 'people',
				'message' => __( 'You have no person yet!', 'yith-booking-for-woocommerce' ),
			);
		}

		/**
		 * Return true if you want to use the object. False otherwise.
		 *
		 * @return bool
		 */
		protected function use_object() {
			return false;
		}

		/**
		 * Define which columns to show on this screen.
		 *
		 * @param array $columns Existing columns.
		 *
		 * @return array
		 */
		public function define_columns( $columns ) {
			if ( isset( $columns['date'] ) ) {
				unset( $columns['date'] );
			}

			$columns['actions'] = __( 'Actions', 'yith-booking-for-woocommerce' );

			return $columns;
		}

		/**
		 * Define bulk actions.
		 *
		 * @param array $actions Existing actions.
		 *
		 * @return array
		 */
		public function define_bulk_actions( $actions ) {
			if ( isset( $actions['edit'] ) ) {
				unset( $actions['edit'] );
			}

			if ( isset( $actions['trash'] ) ) {
				unset( $actions['trash'] );
			}
			$post_type_object = get_post_type_object( $this->post_type );

			if ( current_user_can( $post_type_object->cap->delete_posts ) ) {
				$actions['delete'] = __( 'Delete', 'yith-booking-for-woocommerce' );
			}

			return $actions;
		}

		/**
		 * Render Actions column
		 */
		protected function render_actions_column() {
			$actions = yith_plugin_fw_get_default_post_actions( $this->post_id );

			if ( isset( $actions['trash'] ) ) {
				unset( $actions['trash'] );
			}
			$post  = get_post( $this->post_id );
			$title = _draft_or_post_title( $post );

			// translators: %s is the title of the post object.
			$delete_message = sprintf( __( 'Are you sure you want to delete "%s"?', 'yith-plugin-fw' ), '<strong>' . $title . '</strong>' ) . '<br /><br />' . __( 'This action cannot be undone and you will not be able to recover this data.', 'yith-plugin-fw' );

			if ( current_user_can( 'delete_post', $this->post_id ) ) {
				$actions['delete']                 = array(
					'type'   => 'action-button',
					'title'  => _x( 'Delete Permanently', 'Post action', 'yith-plugin-fw' ),
					'action' => 'delete',
					'icon'   => 'trash',
					'url'    => get_delete_post_link( $this->post_id, '', true ),
				);
				$actions['delete']['confirm_data'] = array(
					'title'               => __( 'Confirm delete', 'yith-plugin-fw' ),
					'message'             => $delete_message,
					'cancel-button'       => __( 'No', 'yith-plugin-fw' ),
					'confirm-button'      => _x( 'Yes, delete', 'Delete confirmation action', 'yith-plugin-fw' ),
					'confirm-button-type' => 'delete',
				);
			}

			yith_plugin_fw_get_action_buttons( $actions, true );
		}

		/**
		 * Show blank slate.
		 *
		 * @param string $which String which table-nav is being shown.
		 */
		public function maybe_render_blank_state( $which ) {
			global $post_type;

			if ( $this->get_blank_state_params() && $post_type === $this->post_type && 'bottom' === $which ) {
				$counts = (array) wp_count_posts( $post_type );
				unset( $counts['auto-draft'] );
				unset( $counts['trash'] );
				$count = array_sum( $counts );

				if ( 0 < $count ) {
					return;
				}

				$this->render_blank_state();

				echo '<style type="text/css">#posts-filter .wp-list-table, #posts-filter .tablenav.top, .tablenav.bottom > *, .wrap .subsubsub  { display: none; } #posts-filter .tablenav.bottom { height: auto; display: block } </style>';
			}
		}

	}
}

return YITH_WCBK_Booking_Person_Type_Post_Type_Admin::instance();
