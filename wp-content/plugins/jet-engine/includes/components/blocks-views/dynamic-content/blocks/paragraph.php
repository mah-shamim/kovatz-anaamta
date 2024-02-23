<?php
namespace Jet_Engine\Blocks_Views\Dynamic_Content\Blocks;

class Paragraph extends Base {

	/**
	 * Returns block name to register dynamic attributes for
	 *
	 * @return [type] [description]
	 */
	public function block_name() {
		return 'core/paragraph';
	}

	/**
	 * Returns attributes array
	 *
	 * @return [type] [description]
	 */
	public function get_attrs() {
		return array(
			array(
				'attr'    => 'content',
				'label'   => 'Content',
				'rewrite' => true,
			),
		);
	}

}
