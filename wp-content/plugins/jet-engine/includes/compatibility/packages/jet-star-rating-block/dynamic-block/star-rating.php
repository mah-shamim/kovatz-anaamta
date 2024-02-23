<?php
namespace Jet_Engine\Compatibility\Packages;

class Star_Rating extends \Jet_Engine\Blocks_Views\Dynamic_Content\Blocks\Base {

	/**
	 * Returns block name to register dynamic attributes for
	 *
	 * @return [type] [description]
	 */
	public function block_name() {
		return 'jet-blocks/star-rating';
	}

	/**
	 * Returns attributes array
	 *
	 * @return [type] [description]
	 */
	public function get_attrs() {
		return array(
			array(
				'attr'             => 'rating',
				'label'            => 'Rating',
				'replace_callback' => array( $this, 'replace_rating' ),
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
	public function replace_rating( $value, $content, $attrs, $parsed_attrs ) {

		$scale = ! empty( $parsed_attrs['scale'] ) ? absint( $parsed_attrs['scale'] ) : 5;
		$value = round( 100 * ( $value / $scale ), 2 );

		$content = preg_replace(
			array( '/clip-path:inset\(0 0 0 \d+\%\)/', '/clip-path:inset\(0 calc\(100\% - \d+\%\) 0 0\)/' ),
			array( 'clip-path:inset(0 0 0 ' . $value . '%)', 'clip-path:inset(0 calc(100% - ' . $value . '%) 0 0)' ),
			$content
		);

		return $content;
	}

}
