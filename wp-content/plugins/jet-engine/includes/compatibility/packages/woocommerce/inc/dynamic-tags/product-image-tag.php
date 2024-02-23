<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Dynamic_Tags;

use Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings\Manager as Listings_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Image_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'jet-wc-product-image';
	}

	public function get_title() {
		return __( 'WooCommerce Product Image', 'jet-engine' );
	}

	public function get_group() {
		return \Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			\Jet_Engine_Dynamic_Tags_Module::IMAGE_CATEGORY,
		);
	}

	protected function register_controls() {

		$this->add_control(
			'fallback',
			array(
				'label' => __( 'Fallback', 'jet-engine' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'img_field_note',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( '<b>Please note:</b> Works only with WC Query (from JetEngine Query Builder) or on single product page', 'jet-engine' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

	}

	public function get_value( array $options = array() ) {

		$product = Listings_Manager::instance()->get_current_product();

		if ( ! $product ) {
			return;
		}

		$img_id = false;

		if ( $product->get_image_id() ) {
			$img_id = $product->get_image_id();
		} elseif ( $product->get_parent_id() ) {
			$parent_product = wc_get_product( $product->get_parent_id() );
			if ( $parent_product ) {
				$img_id = $product->get_image_id();
			}
		}

		if ( $img_id ) {

			$img_data = \Jet_Engine_Tools::get_attachment_image_data_array( $img_id );

			if ( $img_data ) {
				return $img_data;
			} else {
				return $this->get_settings( 'fallback' );
			}
			
		} else {
			return $this->get_settings( 'fallback' );
		}

	}

}
