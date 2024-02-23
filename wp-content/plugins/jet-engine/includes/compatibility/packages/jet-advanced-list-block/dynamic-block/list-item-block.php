<?php
namespace Jet_Engine\Compatibility\Packages;

class Advanced_List_Item_Block extends \Jet_Engine\Blocks_Views\Dynamic_Content\Blocks\Base {

	/**
	 * Returns block name to register dynamic attributes for
	 *
	 * @return [type] [description]
	 */
	public function block_name() {
		return 'jet-blocks/advanced-list-item';
	}

	/**
	 * Returns attributes array
	 *
	 * @return [type] [description]
	 */
	public function get_attrs() {
		return array(
			array(
				'attr'    => 'item_content',
				'label'   => 'Item Content',
				'rewrite' => true,
			),
		);
	}

}
