<?php
namespace Jet_Engine\Modules\Rest_API_Listings\Auth_Types;

class Application_Password extends Base {

	/**
	 * Return auth type ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'application-password';
	}

	/**
	 * Return auth type name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Application Password', 'jet-engine' );
	}

	/**
	 * Initialize authorization
	 *
	 * @return [type] [description]
	 */
	public function init() {
		add_filter( 'jet-engine/rest-api-listings/request/args', array( $this, 'set_password' ), 10, 2 );
	}

	public function set_password( $args, $request ) {

		$endpoint = $request->get_endpoint();

		if ( ! $this->is_current_type_endpoint( $endpoint ) ) {
			return $args;
		}

		if ( empty( $endpoint['application_pass'] ) ) {
			return $args;
		}

		if ( ! isset( $args['headers'] ) ) {
			$args['headers'] = array();
		}

		$args['headers']['Authorization'] = 'Basic ' . base64_encode( $endpoint['application_pass'] );

		return $args;

	}

	/**
	 * Initialize authorization
	 *
	 * @return [type] [description]
	 */
	public function register_controls() {
		?>
		<cx-vui-input
			label="<?php _e( 'User:password string', 'jet-engine' ); ?>"
			description="<?php _e( 'Set application user and password separated with `:`', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			v-model="settings.application_pass"
			:conditions="[
				{
					input: settings.authorization,
					compare: 'equal',
					value: true,
				},
				{
					input: settings.auth_type,
					compare: 'equal',
					value: 'application-password',
				}
			]"
		></cx-vui-input>
		<?php
	}

	/**
	 * Register form-related controls
	 *
	 * @return [type] [description]
	 */
	public function register_form_controls() {
		?>
		<div class="jet-form-editor__row" v-if="result.authorization && 'application-password' === result.auth_type">
			<div class="jet-form-editor__row-label">
				<?php _e( 'User:password string:', 'jet-engine' ); ?>
			</div>
			<div class="jet-form-editor__row-control">
				<input type="text" @input="setField( $event, 'application_pass' )" :value="result.application_pass">
			</div>
			&nbsp;&nbsp;&nbsp;&nbsp;<div class="jet-form-editor__row-notice"><?php _e( 'Set application user and password separated with `:`', 'jet-engine' ); ?></div>
		</div>
		<?php
	}

	/**
	 * Initialize authorization
	 *
	 * @return [type] [description]
	 */
	public function register_args( $args = array() ) {

		$args['application_pass'] = array(
			'type'    => 'regular',
			'default' => '',
		);

		return $args;

	}

}
