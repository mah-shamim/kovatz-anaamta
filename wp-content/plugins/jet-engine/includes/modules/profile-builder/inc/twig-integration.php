<?php
namespace Jet_Engine\Modules\Profile_Builder;

class Twig_Integration extends Base_Integration {

	public function __construct() {
		add_filter( 
			'jet-engine/twig-views/functions/dynamic-url/controls', 
			[ $this, 'register_url_controls' ], 10 
		);

		add_filter(
			'jet-engine/twig-views/functions/dynamic-url/controls-map',
			[ $this, 'add_control_to_map' ], 10 
		);
	}

	public function add_control_to_map( $map ) {
		$map['profile_page'] = 'dynamic_link_profile_page';
		return $map;
	}

	public function register_url_controls( $controls ) {

		$new_controls = [];

		foreach ( $controls as $key => $control ) {

			$new_controls[ $key ] = $control;

			if ( 'source' === $key ) {
				$new_controls['profile_page'] = [
					'label'     => __( 'Profile Page', 'jet-engine' ),
					'type'      => 'select',
					'groups'    => $this->get_pages_for_options( 'blocks' ),
					'condition' => [
						'source' => 'profile_page',
					],
				];
			}

		}

		return $new_controls;
	}

}
