<?php
/**
 * Trait to use bounded parent -> child data class notices system
 */

trait Jet_Engine_Get_Data_Sources_Trait {
	
	/**
	 * Get meta fields for post type
	 *
	 * @return array
	 */
	public function get_dynamic_sources( $for = 'media', $is_common = false ) {

		if ( 'media' === $for ) {

			$default = array(
				'label'  => __( 'General', 'jet-engine' ),
				'values' => array(
					array(
						'value' => 'post_thumbnail',
						'label' => __( 'Post thumbnail', 'jet-engine' ),
					),
					array(
						'value' => 'user_avatar',
						'label' => __( 'User avatar (works only for user listing and pages)', 'jet-engine' ),
					),
				),
			);

		} else {

			$default = array(
				'label'  => __( 'General', 'jet-engine' ),
				'values' => array(
					array(
						'value' => '_permalink',
						'label' => __( 'Permalink', 'jet-engine' ),
					),
					array(
						'value' => 'delete_post_link',
						'label' => __( 'Delete current post link', 'jet-engine' ),
					),
				),
			);

			if ( jet_engine()->modules->is_module_active( 'profile-builder' ) ) {
				$default['values'][] = array(
					'value' => 'profile_page',
					'label' => __( 'Profile Page', 'jet-engine' ),
				);
			}

		}

		$result      = array();
		$meta_fields = array();

		if ( jet_engine()->meta_boxes ) {
			$meta_fields = jet_engine()->meta_boxes->get_fields_for_select( $for, 'blocks' );
		}

		if ( jet_engine()->options_pages ) {
			$default['values'][] = array(
				'value' => 'options_page',
				'label' => __( 'Options', 'jet-engine' ),
			);
		}

		$result = apply_filters(
			'jet-engine/blocks-views/editor/dynamic-sources/fields',
			array_merge( array( $default ), $meta_fields ),
			$for
		);

		$common_prefix = $is_common ? '/common' : '';

		if ( 'media' === $for ) {
			$hook_name = 'jet-engine/listings/dynamic-image/fields' . $common_prefix;
		} else {
			$hook_name = 'jet-engine/listings/dynamic-link/fields' . $common_prefix;
		}

		$extra_fields = apply_filters( $hook_name, array(), $for, $is_common );

		if ( ! empty( $extra_fields ) ) {

			foreach ( $extra_fields as $key => $data ) {

				if ( ! is_array( $data ) ) {

					$result[] = array(
						'label'  => $data,
						'values' => array(
							array(
								'value' => $key,
								'label' => $data,
							),
						),
					);

					continue;
				}

				$values = array();

				if ( ! empty( $data['options'] ) ) {
					foreach ( $data['options'] as $val => $label ) {
						$values[] = array(
							'value' => $val,
							'label' => $label,
						);
					}
				}

				$result[] = array(
					'label'  => $data['label'],
					'values' => $values,
				);
			}

		}

		return $result;

	}

}
