<?php
namespace Jet_Engine\Modules\Performance;

use Jet_Engine\Dashboard\Base_Tab;

/**
 * Dashboard tab class
 */
class Dashboard_Tab extends Base_Tab {
	
	public function slug() {
		return 'performance';
	}

	public function label() {
		return __( 'Performance', 'jet-engine' );
	}

	public function load_config() {
		return array(
			'saved' => Module::instance()->get_tweaks_config(),
		);
	}

	/**
	 * Render tab on this hook
	 * @return [type] [description]
	 */
	public function hook() {
		return 'jet-engine/dashboard/tabs/after-modules';
	}

	/**
	 * Returns assets needto be enqueued with this tab
	 * @return string|array
	 */
	public function assets() {
		return 'jet-engine-dashboard-performance-tab';
	}

	/**
	 * Tabs assets
	 * 
	 * @return [type] [description]
	 */
	public function render_assets() {
		
		wp_register_script(
			'jet-engine-dashboard-performance-tab',
			Module::instance()->module_url( 'assets/js/dashboard-tab.js' ),
			array( 'cx-vue-ui' ),
			jet_engine()->get_version(),
			true
		);

		printf( 
			'<script type="text/x-template" id="jet-engine-tab-performance">%s</script>',
			$this->get_tab_template()
		);

	}

	/**
	 * Returns tab template
	 * 
	 * @return [type] [description]
	 */
	public function get_tab_template() {
		ob_start();
		?>
		<div style="padding: 0 0 20px;">
			<div
				class="cx-vui-subtitle"
			><?php _e( 'Performance tweaks', 'jet-engine' ); ?></div>
			<cx-vui-switcher
				label="<?php _e( 'Optimized DOM', 'jet-engine' ); ?>"
				description="<?php _e( 'Remove some additional HTML wrappers from JetEngine elements. The number of removed wrappers depends on each specific element', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth', 'collpase-sides' ]"
				v-model="tweaks.optimized_dom"
			></cx-vui-switcher>
			<cx-vui-component-wrapper
				v-if="tweaks.optimized_dom"
				label="<?php _e( 'Warning:', 'jet-engine' ); ?>"
				:wrapper-css="[ 'collpase-sides' ]"
				description="<?php _e( 'This feature changes the HTML output and styling of JetEngine-related widgets. So after enabling it, you need to re-style all used JetEngine widgets', 'jet-engine' ); ?>"
			></cx-vui-component-wrapper>
			<cx-vui-switcher
				label="<?php _e( 'Elementor Views', 'jet-engine' ); ?>"
				description="<?php _e( 'Enable/disable all Elementor-related functionality', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth', 'collpase-sides' ]"
				v-model="tweaks.enable_elementor_views"
			></cx-vui-switcher>
			<cx-vui-switcher
				label="<?php _e( 'Blocks Views', 'jet-engine' ); ?>"
				description="<?php _e( 'Enable/disable all blocks-related functionality', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth', 'collpase-sides' ]"
				v-model="tweaks.enable_blocks_views"
			></cx-vui-switcher>
			<cx-vui-switcher
				label="<?php _e( 'Bricks Views', 'jet-engine' ); ?>"
				description="<?php _e( 'Enable/disable all Bricks-related functionality', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth', 'collpase-sides' ]"
				v-model="tweaks.enable_bricks_views"
			></cx-vui-switcher>
			<?php do_action( 'jet-engine/modules/performance/tweaks-tab' ); ?>
			<cx-vui-button
				button-style="accent"
				size="mini"
				:disabled="saving"
				@click="saveTweaks"
			>
				<span slot="label" v-if="!saving"><?php _e( 'Save tweaks config', 'jet-engine' ); ?></span>
				<span slot="label" v-else><?php _e( 'Saving...', 'jet-engine' ); ?></span>
			</cx-vui-button>
		</div>
		<?php
		return ob_get_clean();
	}

}
