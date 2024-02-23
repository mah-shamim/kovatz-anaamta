<?php
namespace Jet_Engine\Modules\Maps_Listings;

trait Preview_Trait {

	/**
	 * Preview scripts
	 *
	 * @return void
	 */
	public function preview_scripts() {
		
		$provider = Module::instance()->providers->get_active_map_provider();

		$provider->register_public_assets();
		$provider->public_assets( null, array( 'marker_clustering' => true ), null );

		wp_enqueue_script( 'jet-maps-listings' );
		
	}

}
