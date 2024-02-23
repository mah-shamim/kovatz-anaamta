<?php
namespace Jet_Engine\Modules\Profile_Builder\Dynamic_Visibility;

use Jet_Engine\Modules\Profile_Builder\Module;

class Is_Profile_Page extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'is-profile-page';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Is Profile Page', 'jet-engine' );
	}

	/**
	 * Returns group for current operator
	 *
	 * @return [type] [description]
	 */
	public function get_group() {
		return 'jet-engine';
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$type = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$value = ! empty( $args['value'] ) ? $args['value'] : false;

		if ( empty( $value ) ) {
			if ( 'hide' === $type ) {
				return false;
			} else {
				return true;
			}
		}

		foreach ( $value as $page ) {

			$data = explode( '::', $page );
			$is_page = Module::instance()->query->is_subpage_now( $data[1], $data[0] );

			if ( $is_page ) {
				if ( 'hide' === $type ) {
					return false;
				} else {
					return true;
				}
			}

		}

		if ( 'hide' === $type ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Check if is condition available for meta fields control
	 *
	 * @return boolean [description]
	 */
	public function is_for_fields() {
		return false;
	}

	/**
	 * Check if is condition available for meta value control
	 *
	 * @return boolean [description]
	 */
	public function need_value_detect() {
		return false;
	}

	/**
	 * Returns condition specific repeater controls
	 */
	public function get_custom_controls() {

		$pages = Module::instance()->elementor->get_pages_for_options();
		$options = array();

		foreach ( $pages as $group ) {
			foreach ( $group['options'] as $value => $label ) {
				$options[ $value ] = $group['label'] . ': ' . $label;
			}
		}

		return array(
			'value_' . $this->get_id() => array(
				'label'       => __( 'Profile Page', 'jet-engine' ),
				'label_block' => true,
				'multiple'    => true,
				'type'        => 'select2',
				'default'     => '',
				'options'     => $options,
			),
		);
	}

}
