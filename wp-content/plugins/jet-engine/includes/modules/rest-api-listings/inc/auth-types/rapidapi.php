<?php
namespace Jet_Engine\Modules\Rest_API_Listings\Auth_Types;

class RapidAPI extends Base {

	/**
	 * Return auth type ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'rapidapi';
	}

	/**
	 * Return auth type name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'RapidAPI', 'jet-engine' );
	}

	/**
	 * Initialize authorization
	 *
	 * @return [type] [description]
	 */
	public function init() {
		add_filter( 'jet-engine/rest-api-listings/request/args', array( $this, 'set_headers' ), 10, 2 );
		add_action( 'jet-engine/rest-api-listings/request/query-args', array( $this, 'set_query' ), 10, 2 );
	}

	public function set_query( $args = array(), $request = null ) {

		if ( $request->is_sample_request ) {
			$endpoint = $request->get_endpoint();
			if ( $this->is_current_type_endpoint( $endpoint ) && ! empty( $endpoint['rapidapi_query_string'] ) ) {
				if ( empty( $args ) ) {
					$args = array();
				}
				$query_args = explode( '&', $endpoint['rapidapi_query_string'] );

				foreach ( $query_args as $arg ) {
					$arg = explode( '=', $arg, 2 );

					$args = array_merge( $args, array(
						$arg[0] => isset( $arg[1] ) ? $arg[1] : $arg[0],
					) );

				}

			}
		}

		return $args;
	}

	public function set_headers( $args, $request ) {

		$endpoint = $request->get_endpoint();

		if ( ! $this->is_current_type_endpoint( $endpoint ) ) {
			return $args;
		}

		if ( empty( $endpoint['rapidapi_key'] ) || empty( $endpoint['rapidapi_host'] ) ) {
			return $args;
		}

		if ( ! isset( $args['headers'] ) ) {
			$args['headers'] = array();
		}

		$args['headers']['x-rapidapi-key']  = $endpoint['rapidapi_key'];
		$args['headers']['x-rapidapi-host'] = $endpoint['rapidapi_host'];
		$args['headers']['useQueryString']  = true;

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
			label="<?php _e( 'RapidAPI Key', 'jet-engine' ); ?>"
			description="<?php _e( 'X-RapidAPI-Key from endpoint settings at the rapidapi.com', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			v-model="settings.rapidapi_key"
			:conditions="[
				{
					input: settings.authorization,
					compare: 'equal',
					value: true,
				},
				{
					input: settings.auth_type,
					compare: 'equal',
					value: 'rapidapi',
				}
			]"
		></cx-vui-input>
		<cx-vui-input
			label="<?php _e( 'RapidAPI Host', 'jet-engine' ); ?>"
			description="<?php _e( 'X-RapidAPI-Host from endpoint settings at the rapidapi.com', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			v-model="settings.rapidapi_host"
			:conditions="[
				{
					input: settings.authorization,
					compare: 'equal',
					value: true,
				},
				{
					input: settings.auth_type,
					compare: 'equal',
					value: 'rapidapi',
				}
			]"
		></cx-vui-input>
		<cx-vui-input
			label="<?php _e( 'Query String', 'jet-engine' ); ?>"
			description="<?php _e( '<b>Optional.</b> Is required only to connect API and fetch fields. Some APIs do not return any data if required query parameters are not passed. Use this option to pass required query parameters and connect such APIs. Format: query_key=value or query_key_1=value-1&query_key_2=value-2', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			v-model="settings.rapidapi_query_string"
			:conditions="[
				{
					input: settings.authorization,
					compare: 'equal',
					value: true,
				},
				{
					input: settings.auth_type,
					compare: 'equal',
					value: 'rapidapi',
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
		<div class="jet-form-editor__row" v-if="result.authorization && 'rapidapi' === result.auth_type">
			<div class="jet-form-editor__row-label">
				<?php _e( 'RapidAPI Key:', 'jet-engine' ); ?>
			</div>
			<div class="jet-form-editor__row-control">
				<input type="text" @input="setField( $event, 'rapidapi_key' )" :value="result.rapidapi_key">
			</div>
			&nbsp;&nbsp;&nbsp;&nbsp;<div class="jet-form-editor__row-notice"><?php _e( 'X-RapidAPI-Key from endpoint settings at the rapidapi.com', 'jet-engine' ); ?></div>
		</div>
		<div class="jet-form-editor__row" v-if="result.authorization && 'rapidapi' === result.auth_type">
			<div class="jet-form-editor__row-label">
				<?php _e( 'RapidAPI Host:', 'jet-engine' ); ?>
			</div>
			<div class="jet-form-editor__row-control">
				<input type="text" @input="setField( $event, 'rapidapi_host' )" :value="result.rapidapi_host">
			</div>
			&nbsp;&nbsp;&nbsp;&nbsp;<div class="jet-form-editor__row-notice"><?php _e( 'X-RapidAPI-Host from endpoint settings at the rapidapi.com', 'jet-engine' ); ?></div>
		</div>
		<?php
	}

	/**
	 * Initialize authorization
	 *
	 * @return [type] [description]
	 */
	public function register_args( $args = array() ) {

		$args['rapidapi_key'] = array(
			'type'    => 'regular',
			'default' => '',
		);

		$args['rapidapi_host'] = array(
			'type'    => 'regular',
			'default' => '',
		);

		return $args;

	}

}
