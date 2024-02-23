<?php
namespace Jet_Engine\Compatibility\Packages;

class Advanced_List_Block extends \Jet_Engine\Blocks_Views\Dynamic_Content\Blocks\Base {

	/**
	 * Returns block name to register dynamic attributes for
	 *
	 * @return [type] [description]
	 */
	public function block_name() {
		return 'jet-blocks/advanced-list';
	}

	/**
	 * Returns attributes array
	 *
	 * @return [type] [description]
	 */
	public function get_attrs() {
		return array(
			array(
				'attr'             => 'items_list',
				'label'            => 'Items List',
				'replace_callback' => array( $this, 'replace_items_list' ),
			),
		);
	}

	/**
	 * Replace dynamic rating value
	 *
	 * @param  string $value       Dynamic value to insert
	 * @param  string $content     Block content
	 * @param  array  $block_attrs Parsed block attributes
	 * @return string
	 */
	public function replace_items_list( $value, $content, $attrs, $parsed_attrs ) {

		$items_list = $this->get_items_list( $value, $attrs, $parsed_attrs );

		preg_match( '/(\<div class="wp-block-jet-blocks-advanced-list jet-advanced-list-block.*?\>)(.*)\<\/div\>/', $content, $matches );

		if ( empty( $matches ) ) {
			return $content;
		} else {
			return $matches[1] . $items_list . '</div>';
		}

	}

	/**
	 * Returns rendered items list
	 *
	 * @param  [type] $value        [description]
	 * @param  [type] $attrs        [description]
	 * @param  [type] $parsed_attrs [description]
	 * @return [type]               [description]
	 */
	public function get_items_list( $value, $attrs, $parsed_attrs ) {

		if ( empty( $value ) || ! is_array( $value ) ) {
			return '';
		}

		$result = '';
		$block  = new \stdClass;

		$block->context = array(
			'jet-blocks/advanced-list/layout'          => ! empty( $parsed_attrs['layout'] ) ? $parsed_attrs['layout'] : 'horizontal-start',
			'jet-blocks/advanced-list/icon_src'        => ! empty( $parsed_attrs['icon_src'] ) ? $parsed_attrs['icon_src'] : '',
			'jet-blocks/advanced-list/icon_size'       => ! empty( $parsed_attrs['icon_size'] ) ? $parsed_attrs['icon_size'] : 24,
			'jet-blocks/advanced-list/icon_color'      => ! empty( $parsed_attrs['icon_color'] ) ? $parsed_attrs['icon_color'] : '',
			'jet-blocks/advanced-list/icon_gap_before' => ! empty( $parsed_attrs['icon_gap_before'] ) ? $parsed_attrs['icon_gap_before'] : '',
			'jet-blocks/advanced-list/icon_gap_after'  => ! empty( $parsed_attrs['icon_gap_after'] ) ? $parsed_attrs['icon_gap_after'] : '',
		);

		foreach ( $value as $item ) {
			$result .= jet_advanced_list_item_render( array(
				'item_label'   => '',
				'item_content' => $item,
			), '', $block );
		}

		return $result;

	}

}
