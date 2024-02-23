<?php
namespace Jet_Engine\Modules\Data_Stores\Stores;

use Jet_Engine\Modules\Data_Stores\Module;

class On_View {

	private $manager = null;
	private $stores  = false;

	public function __construct( Manager $manager ) {

		$this->manager = $manager;

		if ( ! $this->has_stores_on_view() ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'init_view_js' ) );

	}

	/**
	 * Check if we need to initialize view JS file
	 *
	 * @return [type] [description]
	 */
	public function init_view_js() {

		if ( ! is_singular() ) {
			return;
		}

		$watch_for_stores = array();

		foreach ( $this->get_stores_on_view() as $store ) {
			if ( $store->get_arg( 'on_post_type' ) && is_singular( $store->get_arg( 'on_post_type' ) ) ) {
				$watch_for_stores[] = $store;
			}
		}

		if ( empty( $watch_for_stores ) ) {
			return;
		}

		jet_engine()->frontend->frontend_scripts();

		$views_data = array();

		foreach ( $watch_for_stores as $store ) {
			$views_data[] = array(
				'slug'     => $store->get_slug(),
				'type'     => $store->get_type()->type_id(),
				'is_front' => $store->get_type()->is_front_store(),
				'post_id'  => get_the_ID(),
				'size'     => $store->get_size(),
			);
		}

		ob_start();
		?>
		JetEngineViewsData.forEach( function( store ) {
			if ( store.is_front ) {
				var storeInstance = window.JetEngineStores[ store.type ];

				if ( storeInstance && ! storeInstance.inStore( store.slug, '' + store.post_id ) ) {
					storeInstance.addToStore( store.slug, store.post_id, store.size, true );
				}

			} else {

				jQuery.ajax({
					url: JetEngineSettings.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_engine_add_to_store_' + store.slug,
						store: store.slug,
						post_id: store.post_id,
					},
				});

			}
		} );
		<?php
		$inline_js = ob_get_clean();

		wp_localize_script( 'jet-engine-frontend', 'JetEngineViewsData', $views_data );
		wp_add_inline_script( 'jet-engine-frontend', $inline_js );

	}

	public function has_stores_on_view() {
		$store = $this->get_stores_on_view();
		return ! empty( $store );
	}

	public function get_stores_on_view() {

		if ( false === $this->stores ) {
			foreach ( $this->manager->get_stores() as $store ) {
				if ( $store->get_arg( 'store_on_view' ) ) {

					if ( ! is_array( $this->stores ) ) {
						$this->stores = array();
					}

					$this->stores[] = $store;
				}
			}
		}

		return $this->stores;

	}

}
