<?php
namespace Jet_Engine\Glossaries;

class Settings {

	public $items = false;
	public $nonce_key = 'jet-engine-glossaries';
	public $order_option_name = 'jet_engine_glossaries_orders';

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		add_action( 'jet-engine/dashboard/tabs', array( $this, 'register_settings_tab' ), 99 );
		add_action( 'jet-engine/dashboard/assets', array( $this, 'register_settings_js' ) );
		add_action( 'wp_ajax_jet_engine_glossary_save', array( $this, 'save_item' ) );
		add_action( 'wp_ajax_jet_engine_glossary_delete', array( $this, 'delete_item' ) );
		add_action( 'wp_ajax_jet_engine_glossary_save_orders', array( $this, 'save_orders' ) );
		add_action( 'wp_ajax_jet_engine_glossary_get_fields_from_file', array( $this, 'get_fields_from_file' ) );

	}

	public function delete_item() {

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

		jet_engine()->glossaries->data->set_request( array( 'id' => $item_id ) );

		if ( jet_engine()->glossaries->data->delete_item( false ) ) {
			return wp_send_json_success( array( 'message' => __( 'Item settings updated', 'jet-engine' ) ) );
		} else {
			return wp_send_json_error( Module::instance()->get_notices() );
		}

	}

	/**
	 * Ajax callback to save settings
	 *
	 * @return [type] [description]
	 */
	public function save_item() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-engine' ) ) );
		}

		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->nonce_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Nonce validation failed', 'jet-engine' ) ) );
		}

		$item    = ! empty( $_REQUEST['item'] ) ? $_REQUEST['item'] : array();
		$item_id = ! empty( $_REQUEST['item_id'] ) ? absint( $_REQUEST['item_id'] ) : false;

		if ( $item_id ) {
			$item['id'] = $item_id;
		}

		jet_engine()->glossaries->data->set_request( $item );

		if ( ! $item_id ) {
			$done = jet_engine()->glossaries->data->create_item( false );
		} else {
			$done = jet_engine()->glossaries->data->edit_item( false );
		}

		if ( ! empty( $done ) ) {

			$message = __( 'Item settings updated', 'jet-engine' );

			wp_send_json_success( array(
				'item_id' => $done,
				'message' => $message,
			) );
		} else {

			$raw_notices = array();
			$notices     = jet_engine()->glossaries->get_notices();

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

	public function save_orders() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-engine' ) ) );
		}

		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->nonce_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Nonce validation failed', 'jet-engine' ) ) );
		}

		$orders = ! empty( $_REQUEST['orders'] ) ? $_REQUEST['orders'] : array();
		$done   = update_option( $this->order_option_name, $orders );

		if ( ! empty( $done ) ) {
			wp_send_json_success( array(
				'message' => __( 'Order of items updated', 'jet-engine' ),
			) );
		} else {
			wp_send_json_error( array(
				'message' => __( 'Items orders not updated', 'jet-engine' ),
			) );
		}

	}

	public function get_fields_from_file() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-engine' ) ) );
		}

		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->nonce_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Nonce validation failed', 'jet-engine' ) ) );
		}

		$item   = ! empty( $_REQUEST['item'] ) ? $_REQUEST['item'] : array();
		$fields = jet_engine()->glossaries->data->get_fields_from_file( $item );

		if ( empty( $fields ) ) {
			wp_send_json_error( array( 'message' => __( 'File is empty', 'jet-engine' ) ) );
		}

		wp_send_json_success( array(
			'fields'  => $fields,
			'message' => __( 'Item source converted', 'jet-engine' ),
		) );
	}

	/**
	 * Register settings JS file
	 *
	 * @return [type] [description]
	 */
	public function register_settings_js() {

		wp_enqueue_style(
			'jet-engine-media',
			jet_engine()->glossaries->component_url( 'assets/css/admin/media.css' ),
			array(),
			jet_engine()->get_version()
		);

		wp_enqueue_script(
			'vue-slicksort',
			jet_engine()->plugin_url( 'assets/lib/vue-slicksort/vue-slicksort.min.js' ),
			array(),
			jet_engine()->get_version(),
			true
		);

		wp_enqueue_script(
			'jet-engine-glossaries',
			jet_engine()->glossaries->component_url( 'assets/js/admin/settings.js' ),
			array( 'cx-vue-ui', 'vue-slicksort' ),
			jet_engine()->get_version(),
			true
		);

		$items = $this->get();

		wp_enqueue_media();

		wp_localize_script(
			'jet-engine-glossaries',
			'JetEngineGlossariesConfig',
			array(
				'items'       => $items,
				'_nonce'      => wp_create_nonce( $this->nonce_key ),
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
		<script type="text/x-template" id="jet_engine_glossaries">
			<div>
				<div class="cx-vui-component">
					<div class="cx-vui-component__meta">
						<a href="https://crocoblock.com/knowledge-base/articles/jetengine-glossaries-functionality-overview/?utm_source=jetengine&utm_medium=glossaries&utm_campaign=need-help" target="_blank" class="jet-engine-dash-help-link">
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.4413 7.39906C10.9421 6.89828 11.1925 6.29734 11.1925 5.59624C11.1925 4.71987 10.8795 3.9687 10.2535 3.34272C9.62754 2.71674 8.87637 2.40376 8 2.40376C7.12363 2.40376 6.37246 2.71674 5.74648 3.34272C5.1205 3.9687 4.80751 4.71987 4.80751 5.59624H6.38498C6.38498 5.17058 6.54773 4.79499 6.87324 4.46948C7.19875 4.14398 7.57434 3.98122 8 3.98122C8.42566 3.98122 8.80125 4.14398 9.12676 4.46948C9.45227 4.79499 9.61502 5.17058 9.61502 5.59624C9.61502 6.02191 9.45227 6.3975 9.12676 6.723L8.15024 7.73709C7.52426 8.41315 7.21127 9.16432 7.21127 9.99061V10.4038H8.78873C8.78873 9.57747 9.10172 8.82629 9.7277 8.15024L10.4413 7.39906ZM8.78873 13.5962V12.0188H7.21127V13.5962H8.78873ZM2.32864 2.3662C3.9061 0.788732 5.79656 0 8 0C10.2034 0 12.0814 0.788732 13.6338 2.3662C15.2113 3.91862 16 5.79656 16 8C16 10.2034 15.2113 12.0939 13.6338 13.6714C12.0814 15.2238 10.2034 16 8 16C5.79656 16 3.9061 15.2238 2.32864 13.6714C0.776213 12.0939 0 10.2034 0 8C0 5.79656 0.776213 3.91862 2.32864 2.3662Z" fill="#007CBA"></path></svg>
							What is this and how it works?
						</a>
					</div>
				</div>
				<div class="cx-vui-inner-panel">
					<div tabindex="0" class="cx-vui-repeater">
						<slick-list lockAxis="y" :use-drag-handle="true" v-model="items" class="cx-vui-repeater__items">
							<slick-item
								:class="{
									'cx-vui-repeater-item': true,
									'cx-vui-panel': true,
									'cx-vui-repeater-item--is-collpased': editID !== item.id
								}"
								v-for="( item, index ) in items"
								:index="index"
								:key="item.id"
							>
								<div :class="{ 'cx-vui-repeater-item__heading': true, 'cx-vui-repeater-item__heading--is-collpased': editID !== item.id }">
									<div class="cx-vui-repeater-item__heading-start" @click="setEdit( item.id )">
										<div v-handle class="cx-vui-repeater-item__handle">
											<svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg"><line x1="2" y1="3" x2="14" y2="3" stroke-width="2"/><line x1="2" y1="11" x2="14" y2="11" stroke-width="2"/><line x1="2" y1="7" x2="14" y2="7" stroke-width="2"/></svg>
										</div>
										<svg v-if="editID !== item.id" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" class="cx-vui-repeater-item__collapse cx-vui-repeater-item__collapse--is-collpased"><rect width="14" height="14" transform="matrix(1 0 0 -1 0 14)" fill="white"></rect><path d="M13 5.32911L7 11L1 5.32911L2.40625 4L7 8.34177L11.5938 4L13 5.32911Z"></path></svg>
										<svg v-else width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" class="cx-vui-repeater-item__collapse"><rect width="14" height="14" transform="matrix(1 0 0 -1 0 14)" fill="white"></rect><path d="M13 5.32911L7 11L1 5.32911L2.40625 4L7 8.34177L11.5938 4L13 5.32911Z"></path></svg>
										<div class="cx-vui-repeater-item__title">{{ item.name }}</div>
										<div class="cx-vui-repeater-item__subtitle">ID: {{ item.id }}</div>
									</div>
									<div class="cx-vui-repeater-item__heading-end">
										<div class="cx-vui-repeater-item__clean" @click="deleteID = item.id">
											<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="16" height="16" transform="matrix(1 0 0 -1 0 16)" fill="white"></rect><path d="M2.28564 14.192V3.42847H13.7142V14.192C13.7142 14.6685 13.5208 15.0889 13.1339 15.4533C12.747 15.8177 12.3005 15.9999 11.7946 15.9999H4.20529C3.69934 15.9999 3.25291 15.8177 2.866 15.4533C2.4791 15.0889 2.28564 14.6685 2.28564 14.192Z"></path><path d="M14.8571 1.14286V2.28571H1.14282V1.14286H4.57139L5.56085 0H10.4391L11.4285 1.14286H14.8571Z"></path></svg>
											<div class="cx-vui-tooltip" v-if="deleteID === item.id">
												<?php _e( 'Are you sure?', 'jet-engine' ); ?>
												<br><span class="cx-vui-repeater-item__confrim-del" @click.stop="deleteItem( item.id, index )"><?php _e( 'Yes', 'jet-engine' ); ?></span>&nbsp;/&nbsp;<span class="cx-vui-repeater-item__cancel-del" @click.stop="deleteID = false"><?php _e( 'No', 'jet-engine' ); ?></span>
											</div>
										</div>
									</div>
								</div>
								<div :class="{ 'cx-vui-repeater-item__content': true, 'cx-vui-repeater-item__content--is-collpased': editID !== item.id }">
									<jet-engine-glossary :value="item"/>
								</div>
							</slick-item>
						</slick-list>

						<div class="cx-vui-repeater__actions">
							<cx-vui-button
								button-style="accent-border"
								size="mini"
								:disabled="isBusy"
								@click="newItem"
							>
								<span
									slot="label"
									v-html="'<?php _e( '+ New Glossary', 'jet-engine' ); ?>'"
								></span>
							</cx-vui-button>
						</div>
					</div>
				</div>
			</div>
		</script>
		<script type="text/x-template" id="jet_engine_media">
			<div class="jet-engine-media">
				<div class="jet-engine-media__name" v-if="fileData.name">{{ fileData.name }}</div>
				<cx-vui-button
					button-style="accent"
					size="mini"
					@click="selectFile"
				>
					<span
						slot="label"
					><?php _e( 'Select file', 'jet-engine' ); ?></span>
				</cx-vui-button>
			</div>
		</script>
		<script type="text/x-template" id="jet_engine_glossary">
			<div>
				<cx-vui-input
					label="<?php _e( 'Name', 'jet-engine' ); ?>"
					description="<?php _e( 'Human-readable name for the glossary', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-model="settings.name"
				></cx-vui-input>
				<cx-vui-select
					label="<?php _e( 'Data Source', 'jet-engine' ); ?>"
					description="<?php _e( 'The way to get the data for glossary', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-model="settings.source"
					:options-list="[
						{
							value: 'manual',
							label: '<?php _e( 'Set items manually', 'jet-engine' ); ?>'
						},
						{
							value: 'file',
							label: '<?php _e( 'Get items from uploaded file', 'jet-engine' ); ?>'
						},
					]"
				>
				</cx-vui-select>
				<cx-vui-component-wrapper
					label="<?php _e( 'File to Get Data From', 'jet-engine' ); ?>"
					description="<?php _e( 'Select file from the media library to get data from. At the moment supports only JSON or CSV files', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-if="'file' === settings.source"
				>
					<jet-engine-media
						v-model="settings.source_file"
					></jet-engine-media>
				</cx-vui-component-wrapper>
				<cx-vui-input
					label="<?php _e( 'Value Column', 'jet-engine' ); ?>"
					description="<?php _e( 'Get value from the column (or key for JSON objects). Leave empty to detect automatically. <b>Please note:</b> columns names are case-sensitive.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-if="'file' === settings.source"
					v-model="settings.value_col"
				></cx-vui-input>
				<cx-vui-input
					label="<?php _e( 'Label Column', 'jet-engine' ); ?>"
					description="<?php _e( 'Get label from the column (or key for JSON objects). Leave empty to detect automatically. <b>Please note:</b> columns names are case-sensitive.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					v-if="'file' === settings.source"
					v-model="settings.label_col"
				></cx-vui-input>
				<cx-vui-component-wrapper
					label="<?php _e( 'Convert to Manual Source', 'jet-engine' ); ?>"
					description="<?php _e( 'Convert to manual source to be able to edit the glossary fields.<br> <b>Please note:</b> save settings after converting.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					v-if="'file' === settings.source && settings.source_file && settings.source_file.id"
				>
					<cx-vui-button
						size="mini"
						button-style="accent-border"
						:loading="isConverting"
						:disabled="isConverting"
						@click="convertSource"
					>
						<span
							slot="label"
							v-html="'<?php _e( 'Convert', 'jet-engine' ); ?>'"
						></span>
					</cx-vui-button>
				</cx-vui-component-wrapper>
				<cx-vui-component-wrapper
					:wrapper-css="[ 'fullwidth-control' ]"
					v-if="'file' !== settings.source"
				>
					<div class="cx-vui-inner-panel">
						<cx-vui-repeater
							:button-label="'<?php _e( 'New field', 'jet-engine' ); ?>'"
							:button-style="'accent'"
							:button-size="'mini'"
							v-model="settings.fields"
							@add-new-item="addNewField()"
						>
							<cx-vui-repeater-item
								v-for="( field, fieldIndex ) in settings.fields"
								:title="settings.fields[ fieldIndex ].label"
								:subtitle="settings.fields[ fieldIndex ].value"
								:collapsed="isCollapsed( field )"
								:index="fieldIndex"
								@clone-item="cloneField( $event, fieldIndex )"
								@delete-item="deleteField( $event, fieldIndex )"
								:key="'field' + fieldIndex"
							>
								<cx-vui-input
									:label="'<?php _e( 'Field Value', 'jet-engine' ); ?>'"
									:description="'<?php _e( 'This value will be saved into Database', 'jet-engine' ); ?>'"
									:wrapper-css="[ 'equalwidth' ]"
									:size="'fullwidth'"
									:value="settings.fields[ fieldIndex ].value"
									@input="setFieldProp( fieldIndex, 'value', $event )"
								></cx-vui-input>
								<cx-vui-input
									:label="'<?php _e( 'Field Label', 'jet-engine' ); ?>'"
									:description="'<?php _e( 'This will be shown for the user', 'jet-engine' ); ?>'"
									:wrapper-css="[ 'equalwidth' ]"
									:size="'fullwidth'"
									:value="settings.fields[ fieldIndex ].label"
									@input="setFieldProp( fieldIndex, 'label', $event )"
								></cx-vui-input>
								<cx-vui-switcher
									label="<?php _e( 'Is checked (selected)', 'jet-engine' ); ?>"
									description="<?php _e( 'Check this to make this field checked or selected by default.', 'jet-engine' ); ?>"
									:wrapper-css="[ 'equalwidth' ]"
									:value="settings.fields[ fieldIndex ].is_checked"
									@input="setFieldProp( fieldIndex, 'is_checked', $event )"
								></cx-vui-switcher>
							</cx-vui-repeater-item>
						</cx-vui-repeater>
					</div>
				</cx-vui-component-wrapper>
				<cx-vui-component-wrapper
					:wrapper-css="[ 'equalwidth' ]"
				>
					<cx-vui-button
						button-style="accent"
						:loading="saving"
						:disabled="isDisabled()"
						@click="saveItem"
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
	public function get( $item_id = false ) {

		if ( false === $this->items ) {

			$this->items = jet_engine()->glossaries->data->get_item_for_register();

			if ( empty( $this->items ) ) {
				$this->items = array();
			}

			foreach ( $this->items as $index => $item ) {
				if ( empty( $item['source'] ) ) {
					$item['source'] = 'manual';
					$this->items[ $index ] = $item;
				}
			}

			$orders = get_option( $this->order_option_name, array() );

			if ( empty( $orders ) ) {

				usort( $this->items, function( $a, $b ) {

					if ( $a['id'] === $b['id'] ) {
						return 0;
					}

					return ( $a['id'] < $b['id'] ) ? -1 : 1;
				} );

			} else {

				$items_ids = wp_list_pluck( $this->items, 'id'  );
				$diff_ids  = array_diff( $items_ids, $orders );
				$orders    = array_flip( $orders );

				if ( empty( $diff_ids ) ) {

					usort( $this->items, function ( $a, $b ) use ( $orders ) {
						return $orders[ $a['id'] ] - $orders[ $b['id'] ];
					} );

				} else {

					$items           = array_combine( $items_ids, $this->items );
					$intersect_items = array_intersect_key( $items, $orders );
					$diff_items      = array_diff_key( $items, $orders );

					usort( $intersect_items, function ( $a, $b ) use ( $orders ) {
						return $orders[ $a['id'] ] - $orders[ $b['id'] ];
					} );

					usort( $diff_items, function( $a, $b ) {

						if ( $a['id'] === $b['id'] ) {
							return 0;
						}

						return ( $a['id'] < $b['id'] ) ? -1 : 1;
					} );

					$this->items = array_merge( $intersect_items, $diff_items );
				}
			}

		}

		if ( false === $item_id ) {

			$result = array();

			foreach ( $this->items as $item ) {
				$result[] = $this->unslah_fields( $item );
			}

			return $result;

		} else {

			foreach ( $this->items as $item ) {
				if ( $item['id'] === $item_id ) {
					return $this->unslah_fields( $item );
				}
			}

			return false;
		}


	}

	/**
	 * Unslash fields of glossary
	 *
	 * @param  [type] $item [description]
	 * @return [type]       [description]
	 */
	public function unslah_fields( $item ) {

		if ( empty( $item['fields'] ) ) {
			return $item;
		}

		$item['fields'] = array_map( function ( $field ) {
			$field['value'] = wp_unslash( $field['value'] );
			$field['label'] = wp_unslash( $field['label'] );
			return $field;
		}, $item['fields'] );

		return $item;

	}

	/**
	 * Register settings tab
	 *
	 * @return [type] [description]
	 */
	public function register_settings_tab() {
		?>
		<cx-vui-tabs-panel
			name="glossaries"
			label="<?php _e( 'Glossaries', 'jet-engine' ); ?>"
			key="glossaries"
		>
			<keep-alive>
				<jet-engine-glossaries></jet-engine-glossaries>
			</keep-alive>
		</cx-vui-tabs-panel>
		<?php
	}

}
