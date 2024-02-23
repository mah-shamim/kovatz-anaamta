<?php
/**
 * Jet Smart Filters Post Type Class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Post_Type' ) ) {

	/**
	 * Define Jet_Smart_Filters_Post_Type class
	 */
	class Jet_Smart_Filters_Post_Type {

		/**
		 * Post type slug.
		 *
		 * @var string
		 */
		public $post_type = 'jet-smart-filters';

		/**
		 * Constructor for the class
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'register_post_type' ) );
			add_filter( 'post_row_actions', array( $this, 'remove_view_action' ), 10, 2 );
		}

		/**
		 * Register templates post type
		 */
		public function register_post_type() {

			$args = array(
				'labels' => array(
					'name'               => esc_html__( 'Smart Filters', 'jet-smart-filters' ),
					'singular_name'      => esc_html__( 'Filter', 'jet-smart-filters' ),
				),
				'public'            => false,
				'query_var'         => false,
				'rewrite'           => false,
				'supports'          => array( 'title' ),
				'show_in_admin_bar' => true,
				'can_export'        => true,
			);

			$post_type = register_post_type(
				$this->slug(),
				apply_filters( 'jet-smart-filters/post-type/args', $args )
			);
		}

		/**
		 * Actions posts
		 */
		public function remove_view_action( $actions, $post ) {

			if ( $this->slug() === $post->post_type ) {
				unset( $actions['view'] );
			}

			return $actions;
		}

		/**
		 * Templates post type slug
		 */
		public function slug() {

			return $this->post_type;
		}
	}
}
