<?php
namespace Jet_Engine\Blocks_Views\Dynamic_Content\Blocks;

class Container extends Base {

	/**
	 * Returns block name to register dynamic attributes for
	 *
	 * @return [type] [description]
	 */
	public function block_name() {
		return 'jet-engine/container';
	}

	/**
	 * Returns attributes array
	 *
	 * @return [type] [description]
	 */
	public function get_attrs() {
		return array(
			array(
				'attr'             => 'background_image_url',
				'label'            => 'Background image',
				'type'             => 'image',
				'custom_size'      => 'background_settings/image_size',
				'replace_callback' => array( $this, 'replace_bg_image' ),
			),
			array(
				'attr'    => 'section_url',
				'label'   => 'Section URL',
				'rewrite' => true,
			)
		);
	}

	/**
	 * Replace image URL with dyanmic URL in the given content
	 *
	 * @param  string $value       Dynamic value to insert
	 * @param  string $content     Block content
	 * @param  array  $block_attrs Parsed block attributes
	 * @return string
	 */
	public function replace_bg_image( $value, $content, $attrs = array(), $parsed_attrs = array() ) {

		if ( false !== strpos( $content, 'background-image:url(' ) ) {

			if ( is_int( $value ) ) {

				if ( is_attachment( $value ) ) {
					$img_id = $value;
				} else {
					$img_id = get_post_thumbnail_id( $value );
				}

				if ( $img_id ) {
					$size = ! empty( $parsed_attrs['background_settings']['image_size'] ) ? $parsed_attrs['background_settings']['image_size'] : 'full';
					$url = wp_get_attachment_image_url( $img_id, $size );
				}

			} else {
				$url = $value;
			}

			$content = preg_replace( '/background-image:url\(.*?\)/', 'background-image:url(' . $url . ')', $content );
		}

		return $content;

	}

}
