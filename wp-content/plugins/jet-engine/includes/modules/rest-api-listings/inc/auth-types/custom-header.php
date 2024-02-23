<?php
namespace Jet_Engine\Modules\Rest_API_Listings\Auth_Types;

class Custom_Header extends Base {

	/**
	 * Return auth type ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'custom-header';
	}

	/**
	 * Return auth type name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Custom Header', 'jet-engine' );
	}

	/**
	 * Initialize authorization
	 *
	 * @return [type] [description]
	 */
	public function init() {
		add_filter( 'jet-engine/rest-api-listings/request/args', array( $this, 'set_header' ), 10, 2 );
	}

	public function set_header( $args, $request ) {

		$endpoint = $request->get_endpoint();

		if ( ! $this->is_current_type_endpoint( $endpoint ) ) {
			return $args;
		}

		if ( empty( $endpoint['custom_header_name'] ) || empty( $endpoint['custom_header_value'] ) ) {
			return $args;
		}

		if ( ! isset( $args['headers'] ) ) {
			$args['headers'] = array();
		}

		$header = $endpoint['custom_header_name'];
		$value  = $endpoint['custom_header_value'];

		$args['headers'][ $header ] = $value;

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
			label="<?php _e( 'Header name', 'jet-engine' ); ?>"
			description="<?php _e( 'Set authorization header name. Could be found in your API docs', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			v-model="settings.custom_header_name"
			:conditions="[
				{
					input: settings.authorization,
					compare: 'equal',
					value: true,
				},
				{
					input: settings.auth_type,
					compare: 'equal',
					value: 'custom-header',
				}
			]"
		></cx-vui-input>
		<cx-vui-input
			label="<?php _e( 'Header value', 'jet-engine' ); ?>"
			description="<?php _e( 'Set authorization header value. Could be found in your API docs or you user profile related to this API', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			v-model="settings.custom_header_value"
			:conditions="[
				{
					input: settings.authorization,
					compare: 'equal',
					value: true,
				},
				{
					input: settings.auth_type,
					compare: 'equal',
					value: 'custom-header',
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
		<div class="jet-form-editor__row" v-if="result.authorization && 'custom-header' === result.auth_type">
			<div class="jet-form-editor__row-label"><?php
				_e( 'Header name:', 'jet-engine' );
			?></div>
			<div class="jet-form-editor__row-control">
				<input type="text" @input="setField( $event, 'custom_header_name' )" :value="result.custom_header_name">
			</div>
			&nbsp;&nbsp;&nbsp;&nbsp;<div class="jet-form-editor__row-notice"><?php _e( 'Set authorization header name. Could be found in your API docs', 'jet-engine' ); ?></div>
		</div>
		<div class="jet-form-editor__row" v-if="result.authorization && 'custom-header' === result.auth_type">
			<div class="jet-form-editor__row-label"><?php
				_e( 'Header name:', 'jet-engine' );
			?></div>
			<div class="jet-form-editor__row-control">
				<input type="text" @input="setField( $event, 'custom_header_value' )" :value="result.custom_header_value">
			</div>
			&nbsp;&nbsp;&nbsp;&nbsp;<div class="jet-form-editor__row-notice"><?php _e( 'Set authorization header value. Could be found in your API docs or you user profile related to this API', 'jet-engine' ); ?></div>
		</div>
		<?php
	}

	/**
	 * Initialize authorization
	 *
	 * @return [type] [description]
	 */
	public function register_args( $args = array() ) {

		$args['custom_header_name'] = array(
			'type'    => 'regular',
			'default' => '',
		);

		$args['custom_header_value'] = array(
			'type'    => 'regular',
			'default' => '',
		);

		return $args;

	}

}
