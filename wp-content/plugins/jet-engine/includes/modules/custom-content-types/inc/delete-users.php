<?php
namespace Jet_Engine\Modules\Custom_Content_Types;

class Delete_Users {

	public function __construct() {

		add_filter( 'users_have_additional_content', array( $this, 'users_have_cct_items' ), 10, 2 );
		add_action( 'delete_user', array( $this, 'delete_or_reassign_cct_items' ), 10, 2 );

	}

	public function users_have_cct_items( $result, $users_ids ) {

		if ( empty( $users_ids ) ) {
			return $result;
		}

		foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {

			$count = $instance->get_db()->count( array(
				array(
					'field'    => 'cct_author_id',
					'operator' => 'IN',
					'value'    => $users_ids,
				),
			) );

			if ( ! empty( $count ) ) {
				add_action( 'delete_user_form', array( $this, 'add_notice' ) );
				return true;
			}

		}

		return $result;
	}

	public function add_notice() {
		?>
		<h4><?php esc_html_e( 'JetEngine - Custom Content Types', 'jet-engine' ); ?></h4>
		<p><?php esc_html_e( 'Please note that this user has Custom Content Types items.', 'jet-engine' ); ?></p>
		<?php
	}

	public function delete_or_reassign_cct_items( $id, $reassign ) {

		foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {

			$count = $instance->get_db()->count( array( 'cct_author_id' => $id ) );

			if ( empty( $count ) ) {
				continue;
			}

			if ( ! empty( $reassign ) ) {
				$instance->get_db()->update(
					array( 'cct_author_id' => absint( $reassign ) ),
					array( 'cct_author_id' => $id )
				);
			} else {
				$instance->get_db()->delete( array( 'cct_author_id' => $id ) );
			}

		}
	}

}