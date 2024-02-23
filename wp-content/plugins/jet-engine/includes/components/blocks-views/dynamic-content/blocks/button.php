<?php
namespace Jet_Engine\Blocks_Views\Dynamic_Content\Blocks;

class Button extends Base {

	/**
	 * Returns block name to register dynamic attributes for
	 *
	 * @return [type] [description]
	 */
	public function block_name() {
		return 'core/button';
	}

	/**
	 * Returns attributes array
	 *
	 * @return [type] [description]
	 */
	public function get_attrs() {
		return array(
			array(
				'attr'    => 'text',
				'label'   => 'Text',
				'rewrite' => true,
			),
			array(
				'attr'    => 'url',
				'label'   => 'URL',
				'replace' =>  array(
					'source'    => 'attribute',
					'selector'  => 'a',
					'attribute' => 'href',
				),
			),
		);
	}

}
