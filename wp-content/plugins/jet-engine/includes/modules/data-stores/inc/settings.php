<?php
namespace Jet_Engine\Modules\Data_Stores;

class Settings {

	public $stores = false;

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		add_action( 'jet-engine/dashboard/tabs', array( $this, 'register_stores_tab' ), 99 );
		add_action( 'jet-engine/dashboard/assets', array( $this, 'register_stores_js' ) );

		add_action( 'wp_ajax_jet_engine_data_stores_save', array( $this, 'save_stores' ) );

	}

	/**
	 * Ajax callback to save settings
	 *
	 * @return [type] [description]
	 */
	public function save_stores() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-engine' ) ) );
		}

		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'jet-engine-data-stores' ) ) {
			wp_send_json_error( array( 'message' => __( 'Nonce validation failed', 'jet-engine' ) ) );
		}

		$items = ! empty( $_REQUEST['items'] ) ? $_REQUEST['items'] : array();
		$table = jet_engine()->db->tables( Module::instance()->data->table, 'name' );

		jet_engine()->db->delete( Module::instance()->data->table, array( 'status' => 'data-store' ), array( '%s' ) );

		$prepared_items = array();

		foreach ( array_reverse( $items ) as $item ) {
			$slug          = ! empty( $item['slug'] ) ? Module::instance()->data->sanitize_slug( $item['slug'] ) : '';
			$name          = ! empty( $item['name'] ) ? esc_html( wp_unslash( $item['name'] ) ) : '';
			$name          = str_replace( '\\', '', $name ); // remove the remaining slashes
			$type          = ! empty( $item['type'] ) ? $item['type'] : 'cookies';
			$size          = ! empty( $item['size'] ) ? absint( $item['size'] ) : 0;
			$count         = ! empty( $item['count_posts'] ) ? $item['count_posts'] : false;
			$count         = filter_var( $count, FILTER_VALIDATE_BOOLEAN );
			$is_user       = ! empty( $item['is_user'] ) ? $item['is_user'] : false;
			$is_user       = filter_var( $is_user, FILTER_VALIDATE_BOOLEAN );
			$store_on_view = ! empty( $item['store_on_view'] ) ? $item['store_on_view'] : false;
			$store_on_view = filter_var( $store_on_view, FILTER_VALIDATE_BOOLEAN );
			$on_post_type  = ! empty( $item['on_post_type'] ) ? $item['on_post_type'] : '';

			$format = '\'%s\'';

			$args = apply_filters( 'jet-engine/data-stores/settings/args-to-save', array(
				'type'          => $type,
				'size'          => $size,
				'count_posts'   => $count,
				'is_user'       => $is_user,
				'store_on_view' => $store_on_view,
				'on_post_type'  => $on_post_type,
			), $item );

			$prepared_items[] = '(' . implode( ', ', array(
				'slug' => sprintf( $format, $slug ),
				'status' => sprintf( $format, 'data-store' ),
				'labels' => sprintf( $format, maybe_serialize( array( 'name' => $name ) ) ),
				'args' => sprintf( $format, maybe_serialize( $args ) ),
			) ) . ')';
		}

		$prepared_items = implode( ', ', $prepared_items );

		global $wpdb;

		$wpdb->query( "INSERT INTO $table ( `slug`, `status`, `labels`, `args` ) VALUES $prepared_items;" );

		wp_send_json_success( array( 'message' => __( 'Settings saved', 'jet-engine' ) ) );

	}

	/**
	 * Register settings JS file
	 *
	 * @return [type] [description]
	 */
	public function register_stores_js() {

		wp_enqueue_script(
			'jet-engine-admin-tools',
			jet_engine()->plugin_url( 'assets/js/admin/tools.js' ),
			array(),
			jet_engine()->get_version(),
			true
		);

		wp_enqueue_script(
			'jet-engine-data-stores',
			Module::instance()->module_url( 'assets/js/admin/dashboard/data-stores.js' ),
			array( 'cx-vue-ui' ),
			jet_engine()->get_version(),
			true
		);

		$stores = Module::instance()->stores->get_store_types();

		$counts_allowed = array();

		foreach ( $stores as $store ) {
			if ( ! $store->is_front_store() ) {
				$counts_allowed[] = $store->type_id();
			}
		}

		wp_localize_script(
			'jet-engine-data-stores',
			'JetEngineDataStores',
			array(
				'items'            => $this->get(),
				'types'            => Module::instance()->stores->get_types_for_js(),
				'can_posts_counts' => $counts_allowed,
				'post_types'       => \Jet_Engine_Tools::get_post_types_for_js(),
				'_nonce'           => wp_create_nonce( 'jet-engine-data-stores' ),
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
		<script type="text/x-template" id="jet_engine_data_stores">
			<div>
				<div class="cx-vui-inner-panel">
					<cx-vui-repeater
						button-label="<?php _e( '+ New Store', 'jet-engine' ); ?>"
						button-style="link-accent"
						button-size="default"
						v-model="dataStores"
						@add-new-item="addNewRepeaterItem( { 'name': '', 'slug': '', 'type': 'cookies', 'size': 0, 'collapsed': false } )"
					>
						<cx-vui-repeater-item
							v-for="( store, index ) in dataStores"
							:title="store.name"
							:subtitle="store.slug"
							:collapsed="isCollapsed( store )"
							:index="index"
							@clone-item="cloneItem( $event, [ 'name', 'slug', 'type', 'size' ] )"
							@delete-item="deleteItem( $event )"
							:key="store._id ? store._id : store._id = getRandomID()"
						>
							<cx-vui-input
								label="<?php _e( 'Name', 'jet-engine' ); ?>"
								description="<?php _e( 'Store public name', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								size="fullwidth"
								:value="dataStores[ index ].name"
								@input="setProp( index, 'name', $event )"
								@on-input-change="preSetSlug( index )"
							></cx-vui-input>
							<cx-vui-input
								label="<?php _e( 'Slug', 'jet-engine' ); ?>"
								description="<?php _e( 'Store slug. Only letters, - and _ chars allowed', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								size="fullwidth"
								:value="dataStores[ index ].slug"
								@input="setProp( index, 'slug', $event )"
							></cx-vui-input>
							<cx-vui-select
								label="<?php _e( 'Store type', 'jet-engine' ); ?>"
								description="<?php _e( 'Select store type for current store', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								:size="'fullwidth'"
								:options-list="storeTypes"
								:value="dataStores[ index ].type"
								@input="setProp( index, 'type', $event )"
							></cx-vui-select>
							<cx-vui-input
								label="<?php _e( 'Max size', 'jet-engine' ); ?>"
								description="<?php _e( 'Maximum items allowed to store. Set 0 for unlimited size', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								size="fullwidth"
								:value="dataStores[ index ].size"
								@input="setProp( index, 'size', $event )"
							></cx-vui-input>
							<cx-vui-switcher
								label="<?php _e( 'Count items', 'jet-engine' ); ?>"
								description="<?php _e( 'Check this if you want to count how many times each item was added into store', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								:conditions="[
									{
										'input':   dataStores[ index ].type,
										'compare': 'in',
										'value':   canCount,
									}
								]"
								:value="dataStores[ index ].count_posts"
								@input="setProp( index, 'count_posts', $event )"
							></cx-vui-switcher>
							<cx-vui-component-wrapper
								:conditions="[
									{
										'input':   dataStores[ index ].type,
										'compare': 'not_in',
										'value':   canCount,
									}
								]"
							>
								<div class="cx-vui-component__meta">
									<label class="cx-vui-component__label"><?php
										_e( 'Note:', 'jet-engine' );
									?></label>
									<div class="cx-vui-component__desc"><?php
										_e( 'Items count is not allowed for current store type', 'jet-engine' );
									?></div>
								</div>
							</cx-vui-component-wrapper>
							<cx-vui-switcher
								label="<?php _e( 'Is users store', 'jet-engine' ); ?>"
								description="<?php _e( 'This store will contain user IDs. Allowed only for server-side stores', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								:value="dataStores[ index ].is_user"
								@input="setProp( index, 'is_user', $event )"
								:conditions="[
									{
										'input':   dataStores[ index ].type,
										'compare': 'in',
										'value':   canCount,
									}
								]"
							></cx-vui-switcher>
							<cx-vui-switcher
								label="<?php _e( 'Store item on view', 'jet-engine' ); ?>"
								description="<?php _e( 'Works only with post-related stores. Add post into store on visit to the single post page. Can be used to create `Recently Viewed` stores.', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								:conditions="[
									{
										'input':   dataStores[ index ].is_user,
										'compare': 'not_equal',
										'value':   true,
									}
								]"
								:value="dataStores[ index ].store_on_view"
								@input="setProp( index, 'store_on_view', $event )"
							></cx-vui-switcher>
							<cx-vui-f-select
								:label="'<?php _e( 'Watch for post types', 'jet-engine' ); ?>'"
								:description="'<?php _e( 'Select post types where you want to watch for single page views', 'jet-engine' ); ?>'"
								:wrapper-css="[ 'equalwidth' ]"
								:options-list="postTypes"
								:size="'fullwidth'"
								:multiple="true"
								:conditions="[
									{
										'input':   dataStores[ index ].is_user,
										'compare': 'not_equal',
										'value':   true,
									},
									{
										input: dataStores[ index ].store_on_view,
										compare: 'equal',
										value: true,
									}
								]"
								:value="dataStores[ index ].on_post_type"
								@input="setProp( index, 'on_post_type', $event )"
							></cx-vui-f-select>
							<?php do_action( 'jet-engine/data-stores/settings/custom-controls', $this ); ?>
						</cx-vui-repeater-item>
					</cx-vui-repeater>
				</div>
				<cx-vui-component-wrapper
					:wrapper-css="[ 'vertical-fullwidth' ]"
				>
					<cx-vui-button
						button-style="accent"
						:loading="saving"
						@click="saveStores"
					>
						<span
							slot="label"
							v-html="'<?php _e( 'Save', 'jet-engine' ); ?>'"
						></span>
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
	public function get() {

		if ( false === $this->stores ) {
			$this->stores = Module::instance()->data->get_item_for_register();

			if ( empty( $this->stores ) ) {
				$this->stores = array();
			}

		}

		return $this->stores;

	}

	/**
	 * Register settings tab
	 *
	 * @return [type] [description]
	 */
	public function register_stores_tab() {
		?>
		<cx-vui-tabs-panel
			name="data_stores"
			label="<?php _e( 'Data Stores', 'jet-engine' ); ?>"
			key="data_stores"
		>
			<keep-alive>
				<jet-engine-data-stores></jet-engine-data-stores>
			</keep-alive>
		</cx-vui-tabs-panel>
		<?php
	}

}
