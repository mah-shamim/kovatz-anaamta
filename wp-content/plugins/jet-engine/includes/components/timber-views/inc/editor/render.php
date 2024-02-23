<?php
/**
 * Timber editor render class
 */
namespace Jet_Engine\Timber_Views\Editor;

use Jet_Engine\Timber_Views\Package;
use Timber\Loader as Timber_Loader;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Render {

	private $editor_trigger = 'jet_engine_timber_editor';
	private $preview;
	private $save;

	public $twig;

	public function __construct() {

		require_once Package::instance()->package_path( 'editor/preview.php' );
		require_once Package::instance()->package_path( 'editor/save.php' );

		$this->preview = new Preview();
		$this->save    = new Save();

		if ( $this->is_editor() ) {
			add_filter( 'replace_editor', '__return_true' );
			add_action( 'post_action_' . $this->editor_trigger, [ $this, 'render_editor' ] );
		}
	}

	public function get_editor_trigger() {
		return $this->editor_trigger;
	}

	public function render_editor( $post_id ) {

		global $post, $self, $parent_file, $submenu_file;

		$parent_file  = 'jet-engine';
		$submenu_file = 'edit.php?post_type=jet-engine';
		
		$post = get_post( $post_id );
		$self = 'post.php';

		$dummy_loader = new Timber_Loader();
		$this->twig = $dummy_loader->get_twig();

		require_once ABSPATH . 'wp-admin/admin-header.php';
		$this->editor_assets();
		?>
		<div class="wrap">
			<h1><?php _e( 'Edit listing item template', 'jet-engine' ); ?></h1>
			<div id="<?php echo $this->editor_trigger; ?>"></div>
		</div>
		<?php
		require_once ABSPATH . 'wp-admin/admin-footer.php';
		exit();

	}

	public function editor_assets() {

		$module_data = jet_engine()->framework->get_included_module_data( 'cherry-x-vue-ui.php' );
		$ui          = new \CX_Vue_UI( $module_data );

		$ui->enqueue_assets();

		$html_settings = wp_enqueue_code_editor( [ 'type' => 'text/html' ] );
		$css_settings  = wp_enqueue_code_editor( [ 'type' => 'text/css' ] );

		jet_engine()->register_jet_plugins_js();

		wp_enqueue_script(
			'jquery-slick',
			jet_engine()->plugin_url( 'assets/lib/slick/slick.min.js' ),
			array( 'jquery' ),
			'1.8.1',
			true
		);
		
		jet_engine()->frontend->ensure_lib( 'imagesloaded' );

		wp_enqueue_script(
			'jet-engine-frontend',
			jet_engine()->plugin_url( 'assets/js/frontend.js' ),
			array( 'jquery', 'jet-plugins' ),
			jet_engine()->get_version(),
			true
		);

		wp_enqueue_script(
			'jet-engine-timber-editor', 
			Package::instance()->package_url( 'assets/js/editor.js' ),
			[ 'jquery', 'cx-vue-ui' ],
			jet_engine()->get_version(),
			true
		);

		wp_enqueue_style(
			'jet-engine-timber-editor', 
			Package::instance()->package_url( 'assets/css/editor.css' ),
			[],
			jet_engine()->get_version(),
		);

		wp_enqueue_style( 'jet-engine-frontend' );

		global $post;

		$listing = jet_engine()->listings->get_new_doc( [], $post->ID );

		require_once Package::instance()->package_path( 'editor/presets.php' );
		$presets = new Presets( $this->twig );

		wp_localize_script( 'jet-engine-timber-editor', 'JetEngineTimberEditor', [
			'post_title'       => $post->post_title,
			'ID'               => $post->ID,
			'settings'         => $listing->get_settings(),
			'listing_css'      => $listing->get_listing_css(),
			'listing_html'     => $listing->get_listing_html(),
			'html_settings'    => $html_settings,
			'css_settings'     => $css_settings,
			'preview_settings' => $listing->get_meta( '_twig_preview_settings' ),
			'nonce'            => $this->preview->nonce(),
			'functions'        => Package::instance()->registry->functions->get_functions_for_js(),
			'filters'          => Package::instance()->registry->filters->get_filters_for_js(),
			'presets'          => $presets->get_presets_with_preview( $listing->get_settings(), $post->ID ),
		] );

		printf(
			'<script type="text/x-template" id="%1$s_template">%2$s</script>',
			$this->editor_trigger,
			$this->editor_main_template()
		);

		printf(
			'<script type="text/x-template" id="%1$s_settings_template">%2$s</script>',
			$this->editor_trigger,
			$this->editor_settings_template()
		);

		printf(
			'<script type="text/x-template" id="%1$s_dynamic_data_template">%2$s</script>',
			$this->editor_trigger,
			$this->editor_dynamic_data_template()
		);

	}

	public function editor_dynamic_data_template() {
		ob_start();
		?>
		<div
			class="jet-engine-timber-dynamic-data"
			v-click-outside.capture="closePopup"
			v-click-outside:mousedown.capture="closePopup"
			v-click-outside:touchstart.capture="closePopup"
			@keydown.esc="closePopup"
		>
			<cx-vui-button
				button-style="accent-border"
				@click="switchPopup"
				size="mini"
			>
				<slot slot="label"></slot>
			</cx-vui-button>
			<div
				class="jet-engine-timber-dynamic-data__popup jet-engine-timber-editor-popup"
				v-if="showPopup"
				tabindex="-1"
			>
				<div v-if="'functions' == currentMode">
					<div class="jet-engine-timber-dynamic-data__single-item" v-if="currentFunction">
						<div class="jet-engine-timber-dynamic-data__single-item-title">
							<span 
								class="jet-engine-timber-dynamic-data__single-item-back" 
								@click="resetEdit()"><?php 
									_e( 'All Functions', 'jet-engine' );
								?></span> > {{ currentFunction.label }}:
						</div>
						<div class="jet-engine-timber-dynamic-data__single-item-controls">
							<div
								v-if="! currentFunction.chained && currentFunction.args"
								class="jet-engine-timber-dynamic-data__single-item-control"
								v-for="control in getPreparedControls( currentFunction.args )"
							>
								<component
									:is="control.type"
									:options-list="control.optionsList"
									:groups-list="control.groupsList"
									:label="control.label"
									:wrapper-css="[ 'mini-label' ]"
									:multiple="control.multiple"
									size="fullwidth"
									v-if="checkCondition( control.condition, result )"
									v-model="result[ control.name ]"
								><small v-if="control.description" v-html="control.description"></small></component>
							</div>
							<div
								v-if="currentFunction.chained"
								class="jet-engine-timber-dynamic-data__single-item-control is-chained"
							>
								<jet-engine-timber-chained-control
									depth="0"
									:children="currentFunction.children"
									v-model="chainedResult"
								></jet-engine-timber-chained-control>
							</div>
						</div>
						<div class="jet-engine-timber-dynamic-data__single-actions">
							<cx-vui-button
								button-style="accent-border"
								size="mini"
								@click="goToFilter()"
							><span slot="label"><?php _e( 'Add filter to result', 'jet-engine' ); ?></span></cx-vui-button>
							or
							<cx-vui-button
								button-style="accent"
								size="mini"
								@click="insertFunction()"
							><span slot="label"><?php _e( 'Insert', 'jet-engine' ); ?></span></cx-vui-button>
						</div>
					</div>
					<div v-else>
						<h4><?php _e( 'JetEngine', 'jet-engine' ); ?></h4>
						<div class="jet-engine-timber-dynamic-data__list">
							<div 
								class="jet-engine-timber-dynamic-data__item" 
								v-for="( functionData, functionName ) in functions" 
								v-if="'jet-engine' === functionData.source"
								@click="selectFunction( functionData )"
							>
								<span class="jet-engine-timber-dynamic-data__item-mark">≫</span>
								{{ functionData.label }}
							</div>
						</div>
						<h4><?php _e( 'Default Data', 'jet-engine' ); ?></h4>
						<div 
							class="jet-engine-timber-dynamic-data__item" 
							v-for="( functionData, functionName ) in functions" 
							v-if="'default' === functionData.source"
							@click="selectFunction( functionData )"
						>
							<span class="jet-engine-timber-dynamic-data__item-mark">≫</span>
							{{ functionData.label }}
						</div>
					</div>
				</div>
				<div v-else-if="'filters' == currentMode">
					<div class="jet-engine-timber-dynamic-data__single-item" v-if="currentFilter">
						<div class="jet-engine-timber-dynamic-data__single-item-title">
							<span 
								class="jet-engine-timber-dynamic-data__single-item-back" 
								@click="resetEdit()"><?php 
									_e( 'All Filters', 'jet-engine' );
								?></span> > {{ currentFilter.label }}:
						</div>
						<div class="jet-engine-timber-dynamic-data__notice" style="padding-top: 10px;" v-if="currentFilter.note">
							<span>*</span>
							<span v-html="currentFilter.note"></span>
						</div>
						<div class="jet-engine-timber-dynamic-data__single-item-controls">
							<div
								class="jet-engine-timber-dynamic-data__single-item-control"
								v-for="control in getPreparedControls( currentFilter.args )"
							>
								<component
									:is="control.type"
									:options-list="control.optionsList"
									:groups-list="control.groupsList"
									:label="control.label"
									:wrapper-css="[ 'mini-label' ]"
									:multiple="control.multiple"
									size="fullwidth"
									v-if="checkCondition( control.condition, filterResult )"
									v-model="filterResult[ control.name ]"
								><small v-if="control.description" v-html="control.description"></small></component>
							</div>
						</div>
						<div class="jet-engine-timber-dynamic-data__single-actions">
							<cx-vui-button
								button-style="accent"
								size="mini"
								@click="insertFilter()"
							><span slot="label"><?php _e( 'Insert', 'jet-engine' ); ?></span></cx-vui-button>
						</div>
					</div>
					<div v-else>
						<div class="jet-engine-timber-dynamic-data__single-item-title with-indent" v-if="'functions' == mode">
							<span 
								class="jet-engine-timber-dynamic-data__single-item-back" 
								@click="resetEdit( 'functions' )"><?php 
									_e( '< Back to Functions', 'jet-engine' );
								?></span>
						</div>
						<div class="jet-engine-timber-dynamic-data__list">
							<div 
								class="jet-engine-timber-dynamic-data__item" 
								v-for="( filterData, filterName ) in filters" 
								@click="selectFilter( filterData )"
							>
								<span class="jet-engine-timber-dynamic-data__item-mark">≫</span>
								{{ filterData.label }}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function editor_settings_template() {

		ob_start();

		$data    = [];
		$sources = jet_engine()->listings->post_type->get_listing_item_sources();
		$views   = jet_engine()->listings->post_type->get_listing_views();

		include jet_engine()->get_template( 'admin/listing-settings-form.php' );
		$form = ob_get_clean();

		$form = preg_replace( '/name="(.*?)"/', '$0 v-model="settings.$1"', $form );

		ob_start();
		?>
		<div
			class="jet-engine-timber-settings"
			v-click-outside.capture="closePopup"
			v-click-outside:mousedown.capture="closePopup"
			v-click-outside:touchstart.capture="closePopup"
			@keydown.esc="closePopup"
		>
			<button type="button" class="jet-engine-timber-settings__trigger" @click="switchPopup">
				<svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.9498 8.78C13.9798 8.53 13.9998 8.27 13.9998 8C13.9998 7.73 13.9798 7.47 13.9398 7.22L15.6298 5.9C15.7798 5.78 15.8198 5.56 15.7298 5.39L14.1298 2.62C14.0298 2.44 13.8198 2.38 13.6398 2.44L11.6498 3.24C11.2298 2.92 10.7898 2.66 10.2998 2.46L9.99976 0.34C9.96976 0.14 9.79976 0 9.59976 0H6.39976C6.19976 0 6.03976 0.14 6.00976 0.34L5.70976 2.46C5.21976 2.66 4.76976 2.93 4.35976 3.24L2.36976 2.44C2.18976 2.37 1.97976 2.44 1.87976 2.62L0.279763 5.39C0.179763 5.57 0.219763 5.78 0.379763 5.9L2.06976 7.22C2.02976 7.47 1.99976 7.74 1.99976 8C1.99976 8.26 2.01976 8.53 2.05976 8.78L0.369763 10.1C0.219763 10.22 0.179763 10.44 0.269763 10.61L1.86976 13.38C1.96976 13.56 2.17976 13.62 2.35976 13.56L4.34976 12.76C4.76976 13.08 5.20976 13.34 5.69976 13.54L5.99976 15.66C6.03976 15.86 6.19976 16 6.39976 16H9.59976C9.79976 16 9.96976 15.86 9.98976 15.66L10.2898 13.54C10.7798 13.34 11.2298 13.07 11.6398 12.76L13.6298 13.56C13.8098 13.63 14.0198 13.56 14.1198 13.38L15.7198 10.61C15.8198 10.43 15.7798 10.22 15.6198 10.1L13.9498 8.78ZM7.99976 11C6.34976 11 4.99976 9.65 4.99976 8C4.99976 6.35 6.34976 5 7.99976 5C9.64976 5 10.9998 6.35 10.9998 8C10.9998 9.65 9.64976 11 7.99976 11Z"></path></svg>
				<?php _e( 'Settings', 'jet-engine' ); ?>
			</button>
			<div
				v-if="showPopup"
				class="jet-engine-timber-settings__popup jet-engine-timber-editor-popup"
				ref="popup"
				tabindex="-1"
			><?php
				echo $form;
			?></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function editor_main_template() {
		ob_start();
		?>
		<div class="jet-engine-timber-editor">
			<div class="jet-engine-timber-editor__header">
				<div id="titlediv" class="jet-engine-timber-editor__header-input">
					<input type="text" v-model="postTitle" id="title" class="jet-engine-timber-editor__title">
				</div>
				<div class="jet-engine-timber-editor__header-actions">
					<jet-engine-timber-settings
						:listing-id="postID"
						v-model="settings"
					/>
					<cx-vui-button
						button-style="accent"
						size="header-action"
						@click="save"
						:disabled="saving"
					><span slot="label"><?php _e( 'Save', 'jet-engine' ); ?></span></cx-vui-button>
				</div>
			</div>
			<div class="jet-engine-timber-editor-top-bar">
				<jet-engine-timber-presets
					@insert="applyPreset"
				></jet-engine-timber-presets>
				<div class="jet-engine-timber-editor-preview-settings">
					<label for="jet_engine_timber_editor_preview_width"><?php
						_e( 'Preview width:', 'jet-engine' );
					?></label>
					<input
						id="jet_engine_timber_editor_preview_width"
						class="jet-engine-timber-editor-preview-width-control"
						type="number"
						min="10"
						step="1"
						v-model="previewSettings.width"
					>
					<select class="jet-engine-timber-editor-preview-units-control" v-model="previewSettings.units">
						<option value="%">%</option>
						<option value="px">px</option>
						<option value="vw">vw</option>
					</select>
				</div>
			</div>
			<div class="jet-engine-timber-editor__body">
				<div 
					class="jet-engine-timber-editor__data"
					:style="{
						width: 'calc( 100% - ' + getPreviewWidth() + ' )',
						flex: '0 0 calc( 100% - ' + getPreviewWidth() + ' )',
					}"
				>
					<div class="jet-engine-timber-editor__data-control">
						<div class="jet-engine-timber-editor__group-title">
							<div class="jet-engine-timber-editor__group-title-text">HTML</div>
							<div class="jet-engine-timber-editor__group-title-actions">
								<jet-engine-timber-dynamic-data 
									@insert="insertDynamicData"
									mode="functions"
								>
									{ } &nbsp;<?php _e( 'Dynamic data', 'jet-engine' ); ?>
								</jet-engine-timber-dynamic-data>
								<jet-engine-timber-dynamic-data 
									@insert="insertDynamicData"
									mode="filters"
								>
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20"><g><path d="M13.11 4.36L9.87 7.6 8 5.73l3.24-3.24c.35-.34 1.05-.2 1.56.32.52.51.66 1.21.31 1.55zm-8 1.77l.91-1.12 9.01 9.01-1.19.84c-.71.71-2.63 1.16-3.82 1.16H6.14L4.9 17.26c-.59.59-1.54.59-2.12 0-.59-.58-.59-1.53 0-2.12l1.24-1.24v-3.88c0-1.13.4-3.19 1.09-3.89zm7.26 3.97l3.24-3.24c.34-.35 1.04-.21 1.55.31.52.51.66 1.21.31 1.55l-3.24 3.25z" fill="currentColor"/></g></svg>
									<?php _e( 'Filter data', 'jet-engine' ); ?>
								</jet-engine-timber-dynamic-data>
								<?php do_action( 'jet-engine/twig-views/editor/custom-actions', $this ); ?>
							</div>
						</div>
						<textarea ref="html" :value="html" class="jet-engine-timber-editor__data-control-input"></textarea>
					</div>
					<div class="jet-engine-timber-editor__data-control">
						<div class="jet-engine-timber-editor__group-title">
							<div class="jet-engine-timber-editor__group-title-text">CSS</div>
							<div class="jet-engine-timber-editor__group-title-actions">
								<div class="jet-engine-timber-editor__group-title-notice"><?php
									printf( __( '* Use %s statement before each CSS selector to make it unique for current listing', 'jet-engine' ), '<code>selector</code>' );
								?></div>
							</div>
						</div>
						<textarea ref="css" :value="css" class="jet-engine-timber-editor__data-control-input"></textarea>
					</div>
				</div>
				<div 
					class="jet-engine-timber-editor__preview"
					:style="{
						width: getPreviewWidth(),
						flex: '0 0 ' + getPreviewWidth(),
					}"
				>
					<div class="jet-engine-timber-editor__group-title">
						<div class="jet-engine-timber-editor__group-title-text">Preview</div>
						<div class="jet-engine-timber-editor__group-title-actions">
							<cx-vui-button
								button-style="accent-border"
								@click="reloadPreview"
								size="mini"
							>
								<svg slot="label" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M20.944 12.979c-.489 4.509-4.306 8.021-8.944 8.021-2.698 0-5.112-1.194-6.763-3.075l1.245-1.633c1.283 1.645 3.276 2.708 5.518 2.708 3.526 0 6.444-2.624 6.923-6.021h-2.923l4-5.25 4 5.25h-3.056zm-15.864-1.979c.487-3.387 3.4-6 6.92-6 2.237 0 4.228 1.059 5.51 2.698l1.244-1.632c-1.65-1.876-4.061-3.066-6.754-3.066-4.632 0-8.443 3.501-8.941 8h-3.059l4 5.25 4-5.25h-2.92z" fill="currentColor"/></svg>
								<span slot="label">&nbsp;<?php _e( 'Reload preview', 'jet-engine' ); ?></span>
							</cx-vui-button>
						</div>
					</div>
					<jet-style :listing-id="postID">{{ css }}</jet-style>
					<div 
						:class="[ 'jet-engine-timber-editor__preview-body', 'jet-listing-' + postID ]"
						:style="reloadingStyles()"
						ref="previewBody"
						v-html="previewHTML"
					></div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function is_editor() {
		return ( ! empty( $_GET['action'] ) && $this->editor_trigger === $_GET['action'] ) ? true : false;
	}

}
