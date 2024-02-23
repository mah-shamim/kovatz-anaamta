<?php
namespace Jet_Engine\Dashboard;

abstract class Base_Tab {

	abstract public function slug();

	abstract public function label();

	abstract public function load_config();

	public function condition() {
		return true;
	}

	/**
	 * Render tab on this hook
	 * @return [type] [description]
	 */
	public function hook() {
		return 'jet-engine/dashboard/tabs';
	}

	/**
	 * Returns assets needto be enqueued with this tab
	 * @return string|array
	 */
	public function assets() {
		return 'jet-engine-tab-manager';
	}

	/**
	 * Render tab callback
	 * @return [type] [description]
	 */
	public function render_tab() {
		?>
		<cx-vui-tabs-panel
				name="<?= $this->slug() ?>"
				label="<?= $this->label() ?>"
				key="<?= $this->slug() ?>"
		>
			<keep-alive>
				<jet-engine-tab-<?= $this->slug() ?> />
			</keep-alive>
		</cx-vui-tabs-panel>
		<?php
	}

	public function render_assets() {
	}

}
