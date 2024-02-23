<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Listings;

use Jet_Engine\Modules\Custom_Content_Types\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Popups {

	private $_js_inited = false;

	public function __construct() {

		add_filter( 'jet-engine/compatibility/popup-package/request-data', array( $this, 'add_content_type_data_to_request' ) );
		add_filter( 'jet-engine/compatibility/popup-package/custom-content', array( $this, 'set_custom_content' ), 10, 2 );

		add_action( 'wp_footer', array( $this, 'add_inline_js_for_blocks' ) );

	}

	public function set_custom_content( $content, $popup_data ) {

		if ( empty( $popup_data['cctSlug'] ) || empty( $popup_data['postId'] ) ) {
			return $content;
		}

		$type_slug = $popup_data['cctSlug'];
		$item_id = $popup_data['postId'];
		$popup_id = $popup_data['popup_id'];
		$type = Module::instance()->manager->get_content_types( $type_slug );
		$flag = \OBJECT;

		$type->db->set_format_flag( $flag );

		$item = $type->db->get_item( $item_id );

		jet_engine()->listings->data->set_current_object( $item, true );

		$type->db->set_queried_item_id( $item_id );
		$type->db->set_queried_item( $item );

		$content_type = ! empty( $popup_data['content_type'] ) ? $popup_data['content_type'] : 'elementor';

		if ( 'elementor' === $content_type && jet_engine()->has_elementor() ) {
			$content = \Elementor\Plugin::instance()->frontend->get_builder_content( $popup_id );
		} else {
			$popup_post = get_post( $popup_id );

			if ( $popup_post ) {
				$content = do_blocks( $popup_post->post_content );
				$content = do_shortcode( $content );
			}
		}

		$content = apply_filters( 'jet-engine/compatibility/popup-package/the_content', $content, $popup_data );

		return $content;

	}

	public function add_content_type_data_to_request( $data ) {
		
		$object = jet_engine()->listings->data->get_current_object();

		if ( ! $object || ! isset( $object->cct_slug ) ) {
			return $data;
		}

		$data['cct_slug'] = $object->cct_slug;

		if ( ! $this->_js_inited ) {
			if ( wp_doing_ajax() ) {
				add_filter( 'jet-engine/ajax/get_listing/response', array( $this, 'init_js' ) );
			} else {
				add_action( 'wp_footer', array( $this, 'init_js' ) );
			}
			
			$this->_js_inited = true;
		}

		return $data;

	}

	public function init_js( $response ) {

		$data = '';

		if ( ! wp_doing_ajax() ) {
			$data .= "jQuery( window ).on( 'elementor/frontend/init', function() {\r\n";
		}
		
		$data .= "window.elementorFrontend.hooks.addFilter( 'jet-popup/widget-extensions/popup-data', function( popupData, widgetData, \$scope ) {\r\n";
		$data .= "if ( widgetData['cct_slug'] ) {\r\n";
		$data .= "popupData['cctSlug'] = widgetData['cct_slug'];\r\n";
		$data .= "}\r\n";
		$data .= "return popupData;\r\n";
		$data .= "} );\r\n";

		if ( ! wp_doing_ajax() ) {
		 $data .= "} );\r\n";
		}

		if ( wp_doing_ajax() ) {
			$response['html'] = $response['html'] . sprintf( '<script>%s</script>', $data );
			return $response;
		} else {
			wp_add_inline_script( 'jet-engine-frontend', $data, 'before' );
		}
		
	}

	public function add_inline_js_for_blocks() {

		$data = "
			jQuery( window ).on( 'jet-engine/frontend/loaded', function() {
				window.JetPlugins.hooks.addFilter(
					'jet-popup.show-popup.data',
					'JetEngine.popupData',
					function( popupData, popup, triggeredBy ) {

						if ( ! triggeredBy ) {
							return popupData;
						}

						if ( ! triggeredBy.data( 'popupIsJetEngine' ) ) {
							return popupData;
						}

						var wrapper = triggeredBy.closest( '.jet-listing-grid__items' );

						if ( wrapper.length && wrapper.data( 'cctSlug' ) ) {
							popupData['cctSlug'] = wrapper.data( 'cctSlug' );
						}

						return popupData;
					}
				);
			} );
		";

		wp_add_inline_script( 'jet-engine-frontend', $data, 'before' );
	}
}
