<?php
namespace Jet_Engine\Modules\Profile_Builder;

class Base_Integration {

	public $pages = [];

	/**
	 * Get all profile pages list to use as options
	 *
	 * @return [type] [description]
	 */
	public function get_pages_for_options( $for = 'elementor' ) {

		if ( empty( $this->pages[ $for ] ) ) {

			$pages    = array();
			$settings = Module::instance()->settings->get();

			if ( ! empty( $settings['account_page_structure'] ) ) {

				$options = array();

				foreach ( $settings['account_page_structure'] as $page ) {
					if ( 'elementor' === $for ) {
						$options['account_page::' . $page['slug'] ] = $page['title'];
					} else {
						$options[] = array(
							'value' => 'account_page::' . $page['slug'],
							'label' => $page['title'],
						);
					}
				}

				$pages[] = array(
					'label'   => __( 'Account Page', 'jet-engine' ),
					'options' => $options,
				);

			}

			if ( ! empty( $settings['enable_single_user_page'] ) && ! empty( $settings['user_page_structure'] ) ) {

				$options = array();

				foreach ( $settings['user_page_structure'] as $page ) {
					if ( 'elementor' === $for ) {
						$options['single_user_page::' . $page['slug'] ] = $page['title'];
					} else {
						$options[] = array(
							'value' => 'single_user_page::' . $page['slug'],
							'label' => $page['title'],
						);
					}
				}

				$pages[] = array(
					'label'   => __( 'Single User Page', 'jet-engine' ),
					'options' => $options,
				);

			}

			$this->pages[ $for ] = $pages;

		}

		return $this->pages[ $for ];

	}

}
