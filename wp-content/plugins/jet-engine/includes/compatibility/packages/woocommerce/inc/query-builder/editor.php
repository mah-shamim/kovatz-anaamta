<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Query_Builder;

use Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Package;

class WC_Product_Query_Editor extends \Jet_Engine\Query_Builder\Query_Editor\Base_Query {

	/**
	 * Query type ID
	 */
	public function get_id() {
		return Manager::instance()->slug;
	}

	/**
	 * Query type name
	 */
	public function get_name() {
		return __( 'WC Product Query', 'jet-engine' );
	}

	/**
	 * Returns Vue component name for the Query editor for the current type.
	 *
	 * @return string
	 */
	public function editor_component_name() {
		return 'jet-wc-product-query';
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 *
	 * @return mixed|void
	 */
	public function editor_component_data() {
		return apply_filters( 'jet-engine/query-builder/types/wc-product-query/data', [
			'posts_statuses' => \Jet_Engine_Tools::get_post_statuses_for_js(),
			'product_types'  => $this->get_product_types_for_js(),
			'product_cat'    => $this->get_product_categories(),
			'product_tag'    => $this->get_product_tags(),
		] );
	}

	/**
	 * Returns all product types list to use in JS components
	 *
	 * @param bool $placeholder
	 *
	 * @return array
	 */
	public function get_product_types_for_js( $placeholder = false ) {

		$product_types = wc_get_product_types();
		$types_list    = \Jet_Engine_Tools::prepare_list_for_js( array_keys( $product_types ) );

		if ( $placeholder && is_array( $placeholder ) ) {
			$types_list = array_merge( [ $placeholder ], $types_list );
		}

		return $types_list;

	}

	/**
	 * Get categories list.
	 *
	 * @return array
	 */
	public function get_product_categories() {

		$categories = get_terms( 'product_cat' );

		if ( empty( $categories ) || ! is_array( $categories ) ) {
			return [];
		}

		return \Jet_Engine_Tools::prepare_list_for_js( $categories, 'slug', 'name' );

	}

	/**
	 * Get categories list.
	 *
	 * @return array
	 */
	public function get_product_tags() {

		$tags = get_terms( 'product_tag' );

		if ( empty( $tags ) || ! is_array( $tags ) ) {
			return [];
		}

		return \Jet_Engine_Tools::prepare_list_for_js( $tags, 'slug', 'name' );

	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 *
	 * @return false|string
	 */
	public function editor_component_template() {
		ob_start();
		include Package::instance()->package_path( 'templates/admin/query-editor.php' );
		return ob_get_clean();
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 *
	 * @return string
	 */
	public function editor_component_file() {
		return Package::instance()->package_url( 'assets/js/admin/query-editor.js' );
	}

}
