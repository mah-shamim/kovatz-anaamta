<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Listings;

use Jet_Engine\Modules\Custom_Content_Types\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Context {

	public $source = 'current_cct_item';
	public $found_users = array();
	public $change_context = false;

	public function __construct() {
		
		add_filter( 'jet-engine/listings/allowed-context-list', array( $this, 'register_context_source' ) );
		add_filter( 'jet-engine/listings/data/object-by-context/' . $this->source, array( $this, 'setup_object' ) );

		add_filter( 'jet-engine/elementor/dynamic-tags/user-context-list', array( $this, 'register_context_source' ) );
		add_filter( 'jet-engine/elementor/dynamic-tags/user-context-object/' . $this->source, array( $this, 'setup_object' ) );
		add_filter( 'jet-engine/profile-builder/page-url/change-user-context/' . $this->source, array( $this, 'maybe_change_context' ) );

	}

	public function register_context_source( $context_list ) {
		$context_list[ $this->source ] = __( 'Current CCT item author', 'jet-engine' );
		return $context_list;
	}

	public function maybe_change_context( $change_context ) {
		if ( $this->change_context ) {
			return true;
		} else {
			return $change_context;
		}
	}

	public function setup_object( $res = null ) {
		
		$current_object = jet_engine()->listings->data->get_current_object();
		$this->change_context = false;

		if ( isset( $current_object->cct_author_id ) ) {
			
			$user_id = $current_object->cct_author_id;

			if ( empty( $this->found_users[ $user_id ] ) ) {
				$this->found_users[ $user_id ] = get_user_by( 'id', $user_id );
			}

			$this->change_context = true;
			return $this->found_users[ $user_id ];

		}

		return $res;

	}

}
