<?php
namespace Jet_Engine\Query_Builder\Helpers;

/**
 * Allows to set cutom posts per page for existsing Current WP Query instances
 */
class Posts_Per_Page_Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	protected $items = [];

	protected $hooked = false;

	protected $glue = '::';

	public function add_items( $items = [] ) {

		$this->maybe_add_hooks();

		foreach ( $items as $item ) {
			$this->add_item( $item );
		}

	}

	public function add_item( $item ) {

		if ( empty( $item['page'] ) || empty( $item['posts_number'] ) ) {
			return;
		}

		$page_data = explode( $this->glue, $item['page'] );

		if ( ! isset( $this->items[ $page_data[0] ] ) ) {
			$this->items[ $page_data[0] ] = [];
		}

		$this->items[ $page_data[0] ][ $page_data[1] ] = absint( $item['posts_number'] );

	}

	public function maybe_add_hooks() {

		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}

		if ( $this->hooked ) {
			return;
		}

		$this->hooked = true;

		add_action( 'pre_get_posts', [ $this, 'check_items' ] );

	}

	public function check_items( $query ) {

		if ( ! $query->is_main_query() ) {
			return;
		} else {
			remove_action( 'pre_get_posts', [ $this, 'check_items' ] );
		}

		foreach ( $this->items as $items_group => $items_list ) {
			switch ( $items_group ) {
				case 'post_types':
					$this->check_post_types_items( $items_list, $query );
					break;

				case 'taxonomies':
					$this->check_tax_items( $items_list, $query );
					break;

				case 'misc':
					$this->check_misc_items( $items_list, $query );
					break;
				
				default:
					
					do_action_ref_array( 
						'jet-engine/query-builder/posts-per-page-manager/check-group/' . $items_group, 
						array( $items_list, &$query )
					);

					break;
			}
		}

	}

	public function check_misc_items( $items, &$query ) {

		foreach ( $items as $condition => $per_page ) {

			switch ( $condition ) {
				case 'author':
					if ( $query->is_author() ) {
						$query->set( 'posts_per_page', $per_page );
						return;
					}
					break;
				
				case 'search':
					if ( $query->is_search() ) {
						$query->set( 'posts_per_page', $per_page );
						return;
					}
					break;

				default:
					do_action_ref_array( 
						'jet-engine/query-builder/posts-per-page-manager/check-misc/' . $condition, 
						array( $per_page, &$query )
					);
					break;
			}

		}

	}

	public function check_post_types_items( $items, &$query ) {

		foreach ( $items as $post_type => $per_page ) {

			// additional check for home page and default post taxonomies
			if ( 'post' === $post_type ) {
				if ( $query->is_home() || $query->is_category() || $query->is_tag() ) {
					$query->set( 'posts_per_page', $per_page );
					return;
				}
			}

			$queried_post_type = $query->get( 'post_type' );

			if ( $queried_post_type && $post_type === $queried_post_type ) {
				$query->set( 'posts_per_page', $per_page );
				return;
			}

			// apply the same for all CPT taxes by default
			if ( $query->is_tax() ) {
				global $wp_taxonomies;
				$queried_object = $query->get_queried_object();

				if ( isset( $queried_object->taxonomy ) 
					&& ! empty( $wp_taxonomies[ $queried_object->taxonomy ]->object_type ) 
					&& in_array( $post_type, $wp_taxonomies[ $queried_object->taxonomy ]->object_type )
				) {
					$query->set( 'posts_per_page', $per_page );
					return;
				}

			}

		}

	}

	public function check_tax_items( $items, &$query ) {

		foreach ( $items as $tax => $per_page ) {

			if ( $query->is_tax( $tax ) ) {
				$query->set( 'posts_per_page', $per_page );
				return;
			}

		}

	}

	public function get_options() {

		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		if ( ! empty( $post_types ) ) {
			$post_types = array_values( array_map( function( $object ) {
				return array(
					'value' => $object->name,
					'label' => $object->label,
				);
			}, $post_types ) );
		}

		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

		if ( ! empty( $taxonomies ) ) {
			$taxonomies = array_values( array_map( function( $object ) {
				return array(
					'value' => $object->name,
					'label' => $object->label,
				);
			}, $taxonomies ) );
		}

		$options = apply_filters( 'jet-engine/query-builder/posts-per-page-manager/page-types-options', array(
			array(
				'id' => 'post_types', // used to add custom options to this group from 3rd party plugins
				'label' => __( 'Post type archives', 'jet-engine' ),
				'options' => $post_types,
			),
			array(
				'id' => 'taxonomies',
				'label' => __( 'Taxonomy archives', 'jet-engine' ),
				'options' => $taxonomies,
			),
			array(
				'id' => 'misc',
				'label' => __( 'Misc', 'jet-engine' ),
				'options' => array(
					array(
						'value' => 'author',
						'label' => __( 'Author archives', 'jet-engine' ),
					),
					array(
						'value' => 'search',
						'label' => __( 'Search results', 'jet-engine' ),
					),
				),
			),
		) );

		foreach ( $options as $group_index => $group ) {
			foreach ( $group['options'] as $option_index => $option ) {
				
				$new_value = sprintf(
					'%1$s::%2$s',
					$options[ $group_index ]['id'],
					$options[ $group_index ]['options'][ $option_index ]['value'] 
				);

				$options[ $group_index ]['options'][ $option_index ]['value'] = $new_value;
			}
		}

		return $options;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}
