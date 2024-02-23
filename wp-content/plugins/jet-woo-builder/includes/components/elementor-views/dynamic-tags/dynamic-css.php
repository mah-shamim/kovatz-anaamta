<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Woo_Builder_Elementor_Dynamic_CSS extends Elementor\Core\DynamicTags\Dynamic_CSS {

	private $_post_id_for_data;

	public function __construct( $post_id, $post_id_for_data ) {

		$this->_post_id_for_data = $post_id_for_data;

		$post_css_file = Elementor\Core\Files\CSS\Post::create( $post_id_for_data );

		parent::__construct( $post_id, $post_css_file );

	}

	public function get_post_id_for_data() {
		return $this->_post_id_for_data;
	}

}
