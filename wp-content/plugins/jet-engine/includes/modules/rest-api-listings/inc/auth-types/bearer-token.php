<?php
namespace Jet_Engine\Modules\Rest_API_Listings\Auth_Types;

class Bearer_Token extends Base {

	/**
	 * Return auth type ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'bearer-token';
	}

	/**
	 * Return auth type name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Bearer Token', 'jet-engine' );
	}

	/**
	 * Initialize authorization
	 *
	 * @return [type] [description]
	 */
	public function init() {
		add_filter( 'jet-engine/rest-api-listings/request/args', array( $this, 'set_token' ), 10, 2 );
	}

	public function set_token( $args, $request ) {

		$endpoint = $request->get_endpoint();

		if ( ! $this->is_current_type_endpoint( $endpoint ) ) {
			return $args;
		}

		if ( empty( $endpoint['bearer_token'] ) ) {
			return $args;
		}

		if ( ! isset( $args['headers'] ) ) {
			$args['headers'] = array();
		}

		$args['headers']['Authorization'] = 'Bearer ' . $endpoint['bearer_token'];

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
			label="<?php _e( 'Bearer token', 'jet-engine' ); ?>"
			description="<?php _e( 'Set token for Bearer Authorization type', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			v-model="settings.bearer_token"
			:conditions="[
				{
					input: settings.authorization,
					compare: 'equal',
					value: true,
				},
				{
					input: settings.auth_type,
					compare: 'equal',
					value: 'bearer-token',
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
		<div class="jet-form-editor__row" v-if="result.authorization && 'bearer-token' === result.auth_type">
			<div class="jet-form-editor__row-label">
				<?php _e( 'Bearer token:', 'jet-engine' ); ?>
			</div>
			<div class="jet-form-editor__row-control">
				<input type="text" @input="setField( $event, 'bearer_token' )" :value="result.bearer_token">
			</div>
			&nbsp;&nbsp;&nbsp;&nbsp;<div class="jet-form-editor__row-notice"><?php _e( 'Set token for Bearer Authorization type', 'jet-engine' ); ?></div>
		</div>
		<?php
	}

	/**
	 * Initialize authorization
	 *
	 * @return [type] [description]
	 */
	public function register_args( $args = array() ) {

		$args['bearer_token'] = array(
			'type'    => 'regular',
			'default' => '',
		);

		return $args;

	}

}
