<?php
namespace Jet_Engine\Modules\Maps_Listings\Compatibility;

use Jet_Engine\Modules\Maps_Listings\Module;
use BorlabsCookie\Cookie\Frontend\JavaScript;

class Borlabs_Cookie {

	private static $js_added = false;

	private $deps_scripts = array(
		'jet-maps-listings',
	);

	private $block_scripts = false;

	private $blocker_id = 'jet-engine-maps-listings';

	public function __construct() {
		add_filter( 'jet-engine/maps-listings/content', array( $this, 'add_handle_content_blocking' ), 10, 2 );
		add_filter( 'script_loader_tag',                array( $this, 'block_scripts' ), 999, 2 );
	}

	public function get_deps_scripts() {
		$provider = Module::instance()->providers->get_active_map_provider();
		return array_merge( $this->deps_scripts, $provider->get_script_handles() );
	}

	public function add_handle_content_blocking( $html, $render_instance ) {

		if ( is_admin() ) {
			return $html;
		}

		if ( ! function_exists( 'BorlabsCookieHelper' ) ) {
			return $html;
		}

		$content_blocker_data = BorlabsCookieHelper()->getContentBlockerData( 'googlemaps' );

		// Only modify when Google Maps Content Blocker is active.
		if ( ! empty( $content_blocker_data ) ) {

			$this->block_scripts = true;

			if ( ! self::$js_added ) {

				// Overwrite setting and always execute global code before unblocking the content
				$content_blocker_data['settings']['executeGlobalCodeBeforeUnblocking'] = '1';

				$global_js = '
					window.BorlabsCookie.allocateScriptBlockerToContentBlocker( contentBlockerData.id, "' . $this->blocker_id . '", "scriptBlockerId" );
					window.BorlabsCookie.unblockScriptBlockerId( "' . $this->blocker_id . '" );
					jQuery( window ).on( "jet-engine/frontend-maps/loaded", function() {
						window.JetEngineMaps.init();
					} );
				';

				$map_init_js = '
					if ( undefined === window.JetEngineMaps ) {
						jQuery( window ).on( "jet-engine/frontend-maps/loaded", function() {
							window.JetEngineMaps.customInitMapBySelector( jQuery(el) );
						} );
					} else {
						window.JetEngineMaps.customInitMapBySelector( jQuery(el) );
					}
				';

				// Add updated settings, global js, and init js of the Content Blocker
				JavaScript::getInstance()->addContentBlocker(
					$content_blocker_data['content_blocker_id'],
					$content_blocker_data['globalJS'] . $global_js,
					$content_blocker_data['initJS'] . $map_init_js,
					$content_blocker_data['settings']
				);

				self::$js_added = true;
			}

			$html = BorlabsCookieHelper()->blockContent( $html, 'googlemaps' );
		}

		return $html;
	}

	public function block_scripts( $tag, $handle ) {

		if ( ! $this->block_scripts ) {
			return $tag;
		}

		if ( ! in_array( $handle, $this->get_deps_scripts() ) ) {
			return $tag;
		}

		$tag = str_replace(
			array(
				'text/javascript',
				'<script',
				'src=',
			),
			array(
				'text/template',
				'<script data-borlabs-script-blocker-js-handle="' . $handle . '" data-borlabs-script-blocker-id="' . $this->blocker_id . '"',
				'data-borlabs-script-blocker-src=',
			),
			$tag
		);

		return $tag;
	}

}
