<?php
namespace Jet_Engine\Modules\Rest_API_Listings;

class Settings {

	public $items = false;
	public $nonce_key = 'jet-engine-rest-api-listings';

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		add_action( 'jet-engine/dashboard/tabs', array( $this, 'register_settings_tab' ), 99 );
		add_action( 'jet-engine/dashboard/assets', array( $this, 'register_settings_js' ) );
		add_action( 'jet-engine/dashboard/assets-after', array( $this, 'register_settings_css' ) );
		add_action( 'wp_ajax_jet_engine_api_endpoint_save', array( $this, 'save_endpoint' ) );
		add_action( 'wp_ajax_jet_engine_api_endpoint_delete', array( $this, 'delete_endpoint' ) );

	}

	public function delete_endpoint() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-engine' ) ) );
		}

		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->nonce_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Nonce validation failed', 'jet-engine' ) ) );
		}

		$item_id = ! empty( $_REQUEST['item_id'] ) ? absint( $_REQUEST['item_id'] ) : false;

		if ( ! $item_id ) {
			wp_send_json_error( array( 'message' => __( 'Item ID not found in the request', 'jet-engine' ) ) );
		}

		Module::instance()->data->set_request( array( 'id' => $item_id ) );

		if ( Module::instance()->data->delete_item( false ) ) {
			return wp_send_json_success( array( 'message' => __( 'Endpoint settings updated', 'jet-engine' ) ) );
		} else {
			return wp_send_json_error( Module::instance()->get_notices() );
		}

	}

	/**
	 * Ajax callback to save settings
	 *
	 * @return [type] [description]
	 */
	public function save_endpoint() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-engine' ) ) );
		}

		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->nonce_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Nonce validation failed', 'jet-engine' ) ) );
		}

		$item    = ! empty( $_REQUEST['item'] ) ? $_REQUEST['item'] : array();
		$item_id = ! empty( $_REQUEST['item_id'] ) ? absint( $_REQUEST['item_id'] ) : false;
		$with_sample_request = isset( $_REQUEST['with_sample_request'] ) ? $_REQUEST['with_sample_request'] : false;
		$with_sample_request = filter_var( $with_sample_request, FILTER_VALIDATE_BOOLEAN );

		if ( $item_id ) {
			$item['id'] = $item_id;
		}

		if ( $with_sample_request ) {

			$items = Module::instance()->request->set_endpoint( $item, true )->get_items( array(), true );

			if ( false === $items ) {
				wp_send_json_error( 
					array( 
						'message' => Module::instance()->request->get_error(), 
						'error_details' => Module::instance()->request->get_error_details() 
					) 
				);
			}

			if ( ! is_array( $items ) ) {
				wp_send_json_error( array( 'message' => __( 'The API endpoint is connected, but the items list has an incorrect format. Please check the Items path option to make sure you set the correct path to the items list in the response', 'jet-engine' ) ) );
			}

			$sample_item = $items[0];

			$item['connected'] = true;
			$item['sample_item'] = $sample_item;
			$item['fetched_fields'] = array_keys( get_object_vars( $sample_item ) );

		} elseif ( $item_id ) {

			$prev_item = Module::instance()->data->get_item_for_edit( $item_id );
			$args      = isset( $prev_item['args'] ) ? maybe_unserialize( $prev_item['args'] ) : false;

			$item['connected']      = isset( $args['connected'] ) ? $args['connected'] : false;
			$item['sample_item']    = isset( $args['sample_item'] ) ? $args['sample_item'] : false;
			$item['fetched_fields'] = isset( $args['fetched_fields'] ) ? $args['fetched_fields'] : array();

		}

		Module::instance()->data->set_request( $item );

		if ( ! $item_id ) {
			$done = Module::instance()->data->create_item( false );
		} else {
			$done = Module::instance()->data->edit_item( false );
		}

		if ( ! empty( $done ) ) {

			$message = __( 'Endpoint settings updated', 'jet-engine' );

			if ( $with_sample_request ) {
				$message = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"></rect><g><path d="M10 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm-.615 12.66h-1.34l-3.24-4.54 1.34-1.25 2.57 2.4 5.14-5.93 1.34.94-5.81 8.38z"></path></g></svg>' . __( 'Request successfully sent. The sample item is fetched. Endpoint settings updated', 'jet-engine' );
			}

			wp_send_json_success( array(
				'item_id' => $done,
				'message' => $message,
			) );
		} else {

			$raw_notices = array();
			$notices     = Module::instance()->get_notices();

			if ( ! empty( $notices ) ) {
				foreach ( $notices as $notice ) {
					$raw_notices[] = $notice['message'];
				}
			}

			wp_send_json_error( array(
				'message' => implode( ', ', $raw_notices ),
			) );
		}

	}

	public function register_settings_css() {

		wp_add_inline_style( 'jet-engine-dashboard', '
			.jet-rest-api-connected,
			.jet-rest-api-not-connected {
				display: flex;
				align-items: center;
				align-content: center;
			}
			.jet-rest-api-connected {
				color: #46B450;
				font-weight: bold;
			}
			.jet-rest-api-connected svg,
			.jet-rest-api-not-connected svg {
				width: 20px;
				height: 20px;
				margin: 0 4px 0 0;
			}
			.rtl .jet-rest-api-connected svg,
			.rtl .jet-rest-api-not-connected svg {
				margin: 0 0 0 4px;
			}
			.jet-rest-api-connected svg path {
				fill: #46B450;
			}
			.jet-rest-api-not-connected svg path {
				fill: #DCDCDD;
			}
			.cx-vui-button + .jet-rest-api-connected {
				padding: 10px 0 0 0;
				font-weight: normal;
			}
			.jet-rest-api-error {
				color: #C92C2C;
				padding: 10px 0 0 0;
				display: flex;
				align-items: center;
				align-content: center;
			}
			.jet-rest-api-error:before {
				content: "\f534";
				margin: 0 5px 0 0;
				font-family: dashicons;
				font-size: 20px;
			}
			.rtl .jet-rest-api-error:before {
				margin: 0 0 0 5px;
			}
		' );

	}

	/**
	 * Register settings JS file
	 *
	 * @return [type] [description]
	 */
	public function register_settings_js() {

		wp_enqueue_script(
			'jet-engine-rest-listings',
			Module::instance()->module_url( 'assets/js/admin/settings.js' ),
			array( 'cx-vue-ui' ),
			jet_engine()->get_version(),
			true
		);

		$items = $this->get();

		wp_localize_script(
			'jet-engine-rest-listings',
			'JetEngineRestListingsConfig',
			array(
				'items'       => $items,
				'_nonce'      => wp_create_nonce( $this->nonce_key ),
				'auth_types'  => array_merge(
					array(
						array(
							'value' => '',
							'label' => __( 'Select type...', 'jet-engine' ),
						),
					),
					Module::instance()->auth_types->get_types_for_js()
				),
				'sample_item' => array(
					'name'          => 'Crocoblock Widgets',
					'url'           => 'https://crocoblock.com/wp-json/croco-site-api/v1/get-widgets',
					'items_path'    => '/items',
					'authorization' => false,
				),
				'save_label' => __( 'Save', 'jet-engine' ),
				'saving_label' => __( 'Saving...', 'jet-engine' ),
			)
		);

		add_action( 'admin_footer', array( $this, 'print_templates' ) );

	}

	/**
	 * Print VU template for maps settings
	 *
	 * @return [type] [description]
	 */
	public function print_templates() {
		?>
		<script type="text/x-template" id="jet_engine_rest_api_listings">
			<div>
				<div class="cx-vui-component">
					<div class="cx-vui-component__meta">
						<a href="https://crocoblock.com/blog/jetengine-rest-api-new-features-and-use-cases/?utm_source=jetengine&utm_medium=rest-api-listings&utm_campaign=need-help" target="_blank" class="jet-engine-dash-help-link">
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.4413 7.39906C10.9421 6.89828 11.1925 6.29734 11.1925 5.59624C11.1925 4.71987 10.8795 3.9687 10.2535 3.34272C9.62754 2.71674 8.87637 2.40376 8 2.40376C7.12363 2.40376 6.37246 2.71674 5.74648 3.34272C5.1205 3.9687 4.80751 4.71987 4.80751 5.59624H6.38498C6.38498 5.17058 6.54773 4.79499 6.87324 4.46948C7.19875 4.14398 7.57434 3.98122 8 3.98122C8.42566 3.98122 8.80125 4.14398 9.12676 4.46948C9.45227 4.79499 9.61502 5.17058 9.61502 5.59624C9.61502 6.02191 9.45227 6.3975 9.12676 6.723L8.15024 7.73709C7.52426 8.41315 7.21127 9.16432 7.21127 9.99061V10.4038H8.78873C8.78873 9.57747 9.10172 8.82629 9.7277 8.15024L10.4413 7.39906ZM8.78873 13.5962V12.0188H7.21127V13.5962H8.78873ZM2.32864 2.3662C3.9061 0.788732 5.79656 0 8 0C10.2034 0 12.0814 0.788732 13.6338 2.3662C15.2113 3.91862 16 5.79656 16 8C16 10.2034 15.2113 12.0939 13.6338 13.6714C12.0814 15.2238 10.2034 16 8 16C5.79656 16 3.9061 15.2238 2.32864 13.6714C0.776213 12.0939 0 10.2034 0 8C0 5.79656 0.776213 3.91862 2.32864 2.3662Z" fill="#007CBA"></path></svg>
							What is this and how it works?
						</a>
					</div>
				</div>
				<div class="cx-vui-inner-panel">
					<div tabindex="0" class="cx-vui-repeater">
						<div class="cx-vui-repeater__items">
							<div :class="{ 'cx-vui-repeater-item': true, 'cx-vui-panel': true, 'cx-vui-repeater-item--is-collpased': editID !== item.id }" v-for="( item, index ) in items">
								<div :class="{ 'cx-vui-repeater-item__heading': true, 'cx-vui-repeater-item__heading--is-collpased': editID !== item.id }">
									<div class="cx-vui-repeater-item__heading-start" @click="setEdit( item.id )">
										<svg v-if="editID !== item.id" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" class="cx-vui-repeater-item__collapse cx-vui-repeater-item__collapse--is-collpased"><rect width="14" height="14" transform="matrix(1 0 0 -1 0 14)" fill="white"></rect><path d="M13 5.32911L7 11L1 5.32911L2.40625 4L7 8.34177L11.5938 4L13 5.32911Z"></path></svg>
										<svg v-else width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" class="cx-vui-repeater-item__collapse"><rect width="14" height="14" transform="matrix(1 0 0 -1 0 14)" fill="white"></rect><path d="M13 5.32911L7 11L1 5.32911L2.40625 4L7 8.34177L11.5938 4L13 5.32911Z"></path></svg>
										<div class="cx-vui-repeater-item__title">{{ item.name }}</div>
										<div class="cx-vui-repeater-item__subtitle">{{ item.url }}</div>
									</div>
									<div class="cx-vui-repeater-item__heading-end">
										<div class="cx-vui-repeater-item__clean" @click="deleteID = item.id">
											<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="16" height="16" transform="matrix(1 0 0 -1 0 16)" fill="white"></rect><path d="M2.28564 14.192V3.42847H13.7142V14.192C13.7142 14.6685 13.5208 15.0889 13.1339 15.4533C12.747 15.8177 12.3005 15.9999 11.7946 15.9999H4.20529C3.69934 15.9999 3.25291 15.8177 2.866 15.4533C2.4791 15.0889 2.28564 14.6685 2.28564 14.192Z"></path><path d="M14.8571 1.14286V2.28571H1.14282V1.14286H4.57139L5.56085 0H10.4391L11.4285 1.14286H14.8571Z"></path></svg>
											<div class="cx-vui-tooltip" v-if="deleteID === item.id">
												<?php _e( 'Are you sure?', 'jet-engine' ); ?>
												<br><span class="cx-vui-repeater-item__confrim-del" @click.stop="deleteEndpoint( item.id, index )"><?php _e( 'Yes', 'jet-engine' ); ?></span>&nbsp;/&nbsp;<span class="cx-vui-repeater-item__cancel-del" @click.stop="deleteID = false"><?php _e( 'No', 'jet-engine' ); ?></span>
											</div>
										</div>
									</div>
								</div>
								<div :class="{ 'cx-vui-repeater-item__content': true, 'cx-vui-repeater-item__content--is-collpased': editID !== item.id }">
									<jet-engine-rest-api-listing-item :value="item"/>
								</div>
							</div>
						</div>
						<div class="cx-vui-repeater__actions">
							<cx-vui-button
								button-style="accent-border"
								size="mini"
								:disabled="isBusy"
								@click="newEndpoint"
							>
								<span
									slot="label"
									v-html="'<?php _e( '+ New Endpoint', 'jet-engine' ); ?>'"
								></span>
							</cx-vui-button>
							<cx-vui-button
								button-style="link-accent"
								size="mini"
								:disabled="isBusy"
								@click="newEndpoint( $event, true )"
							>
								<span
									slot="label"
									v-html="'<?php _e( 'Add Sample Endpoint', 'jet-engine' ); ?>'"
								></span>
							</cx-vui-button>
						</div>
					</div>
				</div>
			</div>
		</script>
		<script type="text/x-template" id="jet_engine_rest_api_listing_item">
			<div>
				<cx-vui-input
					label="<?php _e( 'Name', 'jet-engine' ); ?>"
					description="<?php _e( 'Endpoint name', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-model="settings.name"
				></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'API Endpoint URL', 'jet-engine' ); ?>"
					description="<?php _e( 'URL for the API endpoints to get items from', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-model="settings.url"
				></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Items path', 'jet-engine' ); ?>"
					description="<?php _e( 'Path to the items inside APIs response. If the response contains only items, leave `/`, if items are nested, please set the path to the items separated with `/` for example: `/data/items`. <a href=\'https://crocoblock.com/troubleshooting/articles/getting-right-item-path-for-rest-api-with-jetengine-wordpress-plugin/\' targer=\'blank\'>More instructions here</a>', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-model="settings.items_path"
				></cx-vui-input>
				<cx-vui-switcher
					label="<?php _e( 'Authorization', 'jet-engine' ); ?>"
					description="<?php _e( 'API endpoints require authorization', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="settings.authorization"
				></cx-vui-switcher>
				<cx-vui-select
					label="<?php _e( 'Authorization type', 'jet-engine' ); ?>"
					description="<?php _e( 'Select authorization type', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					:options-list="authTypes"
					v-model="settings.auth_type"
					:conditions="[
						{
							input: settings.authorization,
							compare: 'equal',
							value: true,
						}
					]"
				></cx-vui-select>
				<?php do_action( 'jet-engine/rest-api-listings/settings/auth-controls' ); ?>
				<cx-vui-component-wrapper
					label="<?php _e( 'Status', 'jet-engine' ); ?>"
					description="<?php _e( 'Is the endpoint connected or not. To connect the endpoint, you need to send a sample request to test authorization and fetch sample data. Without this, it may not work properly in the listing grid.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
				>
					<div v-if="settings.connected" class="jet-rest-api-connected">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M10 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm-.615 12.66h-1.34l-3.24-4.54 1.34-1.25 2.57 2.4 5.14-5.93 1.34.94-5.81 8.38z"/></g></svg>
						<?php _e( 'Connected', 'jet-engine' ); ?>
					</div>
					<div v-else class="jet-rest-api-not-connected">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M10 2c4.42 0 8 3.58 8 8s-3.58 8-8 8-8-3.58-8-8 3.58-8 8-8zm1 4c0-.55-.45-1-1-1s-1 .45-1 1 .45 1 1 1 1-.45 1-1zm0 9V9H9v6h2z"/></g></svg>
						<?php _e( 'Not connected. Set up authorization settings if required and press&nbsp;&nbsp;<b>Send Request</b>', 'jet-engine' ); ?>
					</div>
				</cx-vui-component-wrapper>
				<cx-vui-component-wrapper
					label="<?php _e( 'Sample request', 'jet-engine' ); ?>"
					description="<?php _e( 'Send a sample request to check API endpoint connection and fetch available fields', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
				>
					<cx-vui-button
						button-style="accent-border"
						size="mini"
						:loading="makingSampleRequest"
						@click="makeSampleRequest"
					>
						<span
							slot="label"
						><?php _e( 'Send Request', 'jet-engine' ); ?></span>
					</cx-vui-button>
					<div v-if="sampleRequestError" v-html="sampleRequestError" class="jet-rest-api-error"></div>
					<div v-if="sampleRequestSuccess" v-html="sampleRequestSuccess" class="jet-rest-api-connected"></div>
				</cx-vui-component-wrapper>
				<cx-vui-switcher
					label="<?php _e( 'Cache', 'jet-engine' ); ?>"
					description="<?php _e( 'Cache API responses. Responses will be cached by used request arguments. So if 2 widgets use the same query arguments, the data will be returned from the same cached storage.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-model="settings.cache"
				></cx-vui-switcher>
				<cx-vui-select
					label="<?php _e( 'Cache duration period', 'jet-engine' ); ?>"
					description="<?php _e( 'Select duration period - minutes, hours or days', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					:options-list="[
						{
							value: 'minutes',
							label: '<?php _e( 'Minutes', 'jet-engine' ) ?>',
						},
						{
							value: 'hours',
							label: '<?php _e( 'Hours', 'jet-engine' ) ?>',
						},
						{
							value: 'days',
							label: '<?php _e( 'Days', 'jet-engine' ) ?>',
						},
					]"
					v-model="settings.cache_period"
					:conditions="[
						{
							input: settings.cache,
							compare: 'equal',
							value: true,
						}
					]"
				></cx-vui-select>
				<cx-vui-input
					label="<?php _e( 'Cache duration value', 'jet-engine' ); ?>"
					description="<?php _e( 'Set numerice value, eg. 5, 10, 15, 30', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-model="settings.cache_value"
					:conditions="[
						{
							input: settings.cache,
							compare: 'equal',
							value: true,
						}
					]"
				></cx-vui-input>
				<cx-vui-component-wrapper
					:wrapper-css="[ 'equalwidth' ]"
				>
					<cx-vui-button
						button-style="accent"
						:loading="saving"
						:disabled="isDisabled()"
						@click="saveEndpoint"
					>
						<span
							slot="label"
						>{{ buttonLabel() }}</span>
					</cx-vui-button>
				</cx-vui-component-wrapper>
			</div>
		</script>
		<?php
	}

	/**
	 * Returns all settings
	 *
	 * @return [type] [description]
	 */
	public function get( $endpoint_id = false ) {

		if ( false === $this->items ) {
			$this->items = Module::instance()->data->get_item_for_register();

			if ( empty( $this->items ) ) {
				$this->items = array();
			}

			usort( $this->items, function( $a, $b ) {

				if ( $a['id'] === $b['id'] ) {
					return 0;
				}

				return ( $a['id'] < $b['id'] ) ? -1 : 1;
			} );

		}

		if ( false === $endpoint_id ) {
			return $this->items;
		} else {

			foreach ( $this->items as $item ) {
				if ( $item['id'] == $endpoint_id ) {
					return $item;
				}
			}

			return false;
		}


	}

	/**
	 * Register settings tab
	 *
	 * @return [type] [description]
	 */
	public function register_settings_tab() {
		?>
		<cx-vui-tabs-panel
			name="rest_api_endpoints"
			label="<?php _e( 'REST API Endpoints', 'jet-engine' ); ?>"
			key="rest_api_endpoints"
		>
			<keep-alive>
				<jet-engine-rest-api-listings></jet-engine-rest-api-listings>
			</keep-alive>
		</cx-vui-tabs-panel>
		<?php
	}

}
