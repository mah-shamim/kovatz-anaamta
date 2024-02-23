<?php
namespace Jet_Engine\Relations\Forms\Jet_Engine_Forms;

use Jet_Engine\Relations\Forms\Manager as Forms;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Notification class
 */
class Notification {

	public $slug = null;

	public function __construct() {

		$this->slug = Forms::instance()->slug();

		add_action(
			'jet-engine/forms/editor/before-assets',
			array( $this, 'assets' )
		);

		add_filter(
			'jet-engine/forms/booking/notification-types',
			array( $this, 'register_notification' )
		);

		add_action(
			'jet-engine/forms/booking/notifications/fields-after',
			array( $this, 'notification_fields' )
		);

		add_filter(
			'jet-engine/forms/booking/notification/' . $this->slug,
			array( $this, 'handle_notification' ),
			1, 2
		);

	}

	/**
	 * Register notification assets
	 * @return [type] [description]
	 */
	public function assets() {

		wp_enqueue_script(
			'jet-engine-connect-rel-notification',
			jet_engine()->plugin_url( 'includes/components/relations/forms/jet-engine/assets/js/form-notification.js' ),
			array(),
			jet_engine()->get_version(),
			true
		);

		add_action( 'admin_footer', array( $this, 'notification_component_template' ) );

	}

	/**
	 * Print notification component template
	 *
	 * @return [type] [description]
	 */
	public function notification_component_template() {

		ob_start();
		include jet_engine()->relations->component_path( 'forms/jet-engine/templates/notification-component.php' );
		$content = ob_get_clean();

		printf(
			'<script type="text/x-template" id="jet-connect-rel-notification">%s</script>',
			$content
		);

	}

	/**
	 * Register new notification type
	 *
	 * @return [type] [description]
	 */
	public function register_notification( $notifications ) {
		$notifications[ $this->slug ] = Forms::instance()->action_title();
		return $notifications;
	}

	/**
	 * Render additional notification fields
	 *
	 * @return [type] [description]
	 */
	public function notification_fields() {

		$relations = htmlspecialchars( json_encode( jet_engine()->relations->get_relations_for_js() ) );

		?>
		<div v-if="'<?php echo $this->slug; ?>' === currentItem.type">
			<jet-connect-rel-notification v-model="currentItem.connect_rel" :fields="availableFields" :relations="<?php echo $relations; ?>"></jet-connect-rel-notification>
		</div>
		<?php
	}

	/**
	 * Handle form notification
	 *
	 * @return [type] [description]
	 */
	public function handle_notification( $args, $notifications ) {

		$config           = ! empty( $args['connect_rel'] ) ? $args['connect_rel'] : array();
		$relation         = ! empty( $config['relation'] ) ? $config['relation'] : false;
		$parent_field     = ! empty( $config['parent_id'] ) ? $config['parent_id'] : false;
		$parent_id        = ! empty( $notifications->data[ $parent_field ] ) ? $notifications->data[ $parent_field ] : false;
		$child_field      = ! empty( $config['child_id'] ) ? $config['child_id'] : false;
		$context          = ! empty( $config['context'] ) ? $config['context'] : 'child';
		$store_items_type = ! empty( $config['store_items_type'] ) ? $config['store_items_type'] : 'replace';
		$child_id         = ! empty( $notifications->data[ $child_field ] ) ? $notifications->data[ $child_field ] : false;

		$res = Forms::instance()->update_related_items( array(
			'relation'         => $relation,
			'parent_id'        => $parent_id,
			'child_id'         => $child_id,
			'context'          => $context,
			'store_items_type' => $store_items_type,
		) );

		if ( is_wp_error( $res ) ) {
			$notifications->log[] = $notifications->set_specific_status( $res->get_error_message() );
			return false;
		} else {
			$notifications->log[] = true;
		}

	}

}
