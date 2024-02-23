<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Dynamic_Tags;

use Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings\Manager as Listings_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Gallery_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'jet-wc-product-gallery';
	}

	public function get_title() {
		return __( 'WooCommerce Product Gallery', 'jet-engine' );
	}

	public function get_group() {
		return \Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			\Jet_Engine_Dynamic_Tags_Module::GALLERY_CATEGORY,
		);
	}

	protected function register_controls() {

		$this->add_control(
			'fallback',
			array(
				'label' => __( 'Fallback', 'jet-engine' ),
				'type'  => \Elementor\Controls_Manager::GALLERY,
			)
		);

		$this->add_control(
			'gallery_field_note',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( '<b>Please note:</b> Works only with WC Query (from JetEngine Query Builder) or on single product page', 'jet-engine' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

	}

	public function get_value( array $options = [] ) {

		$product = Listings_Manager::instance()->get_current_product();

		if ( ! $product ) {
			return [];
		}

		$value          = [];
		$attachment_ids = $product->get_gallery_image_ids();

		foreach ( $attachment_ids as $attachment_id ) {
			$value[] = [
				'id' => $attachment_id,
			];
		}

		if ( empty( $value ) ) {
			return $this->get_settings( 'fallback' );
		}

		return $value;

	}

}