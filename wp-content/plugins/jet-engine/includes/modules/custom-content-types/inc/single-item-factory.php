<?php
namespace Jet_Engine\Modules\Custom_Content_Types;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Hadle actions related to single post item of current CCT item
 */
class Single_Item_Factory {

	private $type_slug      = null;
	private $type_name      = null;
	private $post_type      = null;
	private $post_type_name = null;

	public function __construct( $factory ) {
		
		if ( ! $factory->get_arg( 'has_single' ) ) {
			return;
		}

		$this->type_slug = $factory->get_arg( 'slug' );
		$this->type_name = $factory->get_arg( 'name' );
		$this->post_type = $factory->get_arg( 'related_post_type' );

		$post_type_obj = get_post_type_object( $this->post_type );

		if ( ! $post_type_obj ) {
			$this->post_type_name = $this->post_type;
		} else {
			$this->post_type_name = $post_type_obj->labels->singular_name;
		}
		

		add_filter( 'jet-engine/listings/allowed-context-list', [ $this, 'register_single_item_context' ] );
		add_filter( 'jet-engine/relations/sources-list', [ $this, 'register_single_item_source' ] );

		add_filter( 
			'jet-engine/relations/object-id-by-source/' . $this->source_name(), 
			[ $this, 'apply_source' ]
		);

		add_filter(
			'jet-engine/listings/data/object-by-context/' . $this->source_name(),
			[ $this, 'apply_conntext' ]
		);

	}

	/**
	 * Returns name of context and related items source
	 * 
	 * @return [type] [description]
	 */
	public function source_name() {
		return 'related_cct_item_' . $this->type_slug;
	}

	/**
	 * Apply current source
	 * 
	 * @param  [type] $result [description]
	 * @return [type]         [description]
	 */
	public function apply_conntext( $result ) {

		$item = $this->get_item();

		if ( ! $item ) {
			return $result;
		}

		return $item;

	}

	/**
	 * Apply current source
	 * 
	 * @param  [type] $result [description]
	 * @return [type]         [description]
	 */
	public function apply_source( $result ) {

		$item = $this->get_item();

		if ( ! $item ) {
			return $result;
		}

		return $item->_ID;

	}

	/**
	 * Get CCT fro the current post
	 * 
	 * @return [type] [description]
	 */
	public function get_item() {
		
		global $post;

		if ( ! $post ) {
			return false;
		}

		$item = Module::instance()->manager->get_item_for_post(
			$post->ID,
			Module::instance()->manager->get_content_types( $this->type_slug ),
			$this->post_type
		);

		if ( empty( $item ) ) {
			return false;
		}

		return (object) $item;

	}

	/**
	 * Register separate context to get related CCT item object for the current single post,
	 * If CCT item, has single
	 * 
	 * @param  [type] $context_list [description]
	 * @return [type]               [description]
	 */
	public function register_single_item_context( $context_list ) {

		$context_list[ $this->source_name() ] = sprintf( 
			__( 'Related CCT item %1$s for current %2$s' ),
			$this->type_name,
			$this->post_type_name
		);

		return $context_list;
	}

	/**
	 * Regiser separate relations source to get related CCT item object for the current single post
	 * 
	 * @param  [type] $sources [description]
	 * @return [type]          [description]
	 */
	public function register_single_item_source( $sources ) {
		return $this->register_single_item_context( $sources );
	}

}
