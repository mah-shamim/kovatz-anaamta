<?php
namespace Jet_Engine\Modules\Data_Stores\Stores;

use Jet_Engine\Modules\Data_Stores\Module;

class Factory {

	private $type;
	private $slug;
	private $name;
	private $size;
	private $count_posts;
	private $count_posts_key = 'jet_engine_store_count_';
	private $args = array();

	public function __construct( $args = array(), $type = false ) {

		if ( ! $type ) {
			return;
		}

		$this->type        = $type;
		$this->slug        = ! empty( $args['slug'] ) ? $args['slug'] : false;
		$this->name        = ! empty( $args['name'] ) ? $args['name'] : false;
		$this->size        = ! empty( $args['size'] ) ? absint( $args['size'] ) : 0;
		$this->count_posts = ! empty( $args['count_posts'] ) ? $args['count_posts'] : false;
		$this->count_posts = filter_var( $this->count_posts, FILTER_VALIDATE_BOOLEAN );
		$this->args        = $args;

		if ( ! $this->slug ) {
			return;
		}

		$this->get_type()->on_init();

		if ( $this->get_type()->is_front_store() ) {
			add_action( 'jet-engine/listings/frontend-scripts', array( $this, 'register_store_instatnces_js_object' ) );
		}

		add_action( 'wp_ajax_jet_engine_add_to_store_' . $this->slug, array( $this, 'ajax_add_to_store' ) );
		add_action( 'wp_ajax_nopriv_jet_engine_add_to_store_' . $this->slug, array( $this, 'ajax_add_to_store' ) );

		add_action( 'wp_ajax_jet_engine_remove_from_store_' . $this->slug, array( $this, 'ajax_remove_from_store' ) );
		add_action( 'wp_ajax_nopriv_jet_engine_remove_from_store_' . $this->slug, array( $this, 'ajax_remove_from_store' ) );


	}

	public function get_slug() {
		return $this->slug;
	}

	public function get_name() {
		return $this->name;
	}

	public function get_size() {
		return $this->size;
	}

	public function get_type() {
		return $this->type;
	}

	public function is_user_store() {
		$is_user = $this->get_arg( 'is_user' );
		return filter_var( $is_user, FILTER_VALIDATE_BOOLEAN );
	}

	public function is_on_view_store() {
		$is_view_store = $this->get_arg( 'store_on_view' );
		return filter_var( $is_view_store, FILTER_VALIDATE_BOOLEAN );
	}

	public function get_arg( $name = null ) {
		if ( ! $name ) {
			return false;
		} else {
			return isset( $this->args[ $name ] ) ? $this->args[ $name ] : false;
		}
	}

	public function can_count_posts() {
		return $this->count_posts;
	}

	public function get_count() {
		$store = $this->get_type()->get( $this->get_slug() );
		return count( $store );
	}

	public function get_post_count( $post_id = null ) {

		if ( ! $this->can_count_posts() ) {
			return 0;
		}

		$custm_count = apply_filters( 'jet-engine/data-stores/pre-get-post-count', false, $post_id, $this );

		if ( false !== $custm_count ) {
			return $custm_count;
		}

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id ) {
			return 0;
		}

		return absint( get_post_meta( $post_id, $this->count_posts_key . $this->get_slug(), true ) );

	}

	public function get_store() {
		return $this->get_type()->get( $this->get_slug() );
	}

	public function in_store( $post_id ) {
		$store = $this->get_store();
		return in_array( $post_id, $store );
	}

	public function ajax_add_to_store() {

		$this->verify_request();

		$post_id   = sanitize_text_field( $_REQUEST['post_id'] );
		$store     = esc_attr( $_REQUEST['store'] );
		$size      = $this->get_size();
		$old_count = $this->get_count();

		/**
		 * Hook fires before adding any data into the store
		 */
		do_action( 'jet-engine/data-stores/before-add-to-store', $post_id, $store, $this );

		if ( 0 < $size && $old_count >= $size ) {

			if ( $this->is_on_view_store() && ! $this->in_store( $post_id ) ) {
				$store_val = $this->get_type()->get( $store );
				$this->get_type()->remove( $store, $store_val[0] );
			} else {
				wp_send_json_error( array( 'message' => __( 'You can`t add more posts', 'jet-engine' ) ) );
			}
		}

		$count     = $this->get_type()->add_to_store( $store, $post_id );
		$fragments = array();

		if ( $this->can_count_posts() ) {

			if ( $count > $old_count ) {
				$new_post_count = $this->increase_post_count( $post_id );
			} else {
				$new_post_count = $this->get_post_count( $post_id );
			}

			$selector = '.jet-engine-data-post-count[data-store="' . $this->get_slug() . '"][data-post="' . $post_id . '"]';

			$fragments[ $selector ] = $new_post_count;

		}

		/**
		 * Hook fires after adding any data from the store
		 */
		do_action( 'jet-engine/data-stores/after-add-to-store', $post_id, $store, $this );

		$fragments = apply_filters( 'jet-engine/data-stores/ajax-store-fragments', $fragments, $this, $post_id );

		wp_send_json_success(
			array(
				'count'     => $count,
				'fragments' => $fragments,
			)
		);

	}

	public function ajax_remove_from_store() {

		$this->verify_request();

		$post_id = sanitize_text_field( $_REQUEST['post_id'] );
		$store = esc_attr( $_REQUEST['store'] );

		$old_count = $this->get_count();
		$count = $this->get_type()->remove( $store, $post_id );
		$fragments = array();

		/**
		 * Hook fires before removing any data into the store
		 */
		do_action( 'jet-engine/data-stores/before-remove-from-store', $post_id, $store, $this );

		if ( $this->can_count_posts() ) {

			if ( $count < $old_count ) {
				$new_post_count = $this->decrease_post_count( $post_id );
			} else {
				$new_post_count = $this->get_post_count( $post_id );
			}

			$selector = '.jet-engine-data-post-count[data-store="' . $this->get_slug() . '"][data-post="' . $post_id . '"]';

			$fragments[ $selector ] = $new_post_count;

		}

		/**
		 * Hook fires after removing any data from the store
		 */
		do_action( 'jet-engine/data-stores/after-remove-from-store', $post_id, $store, $this );

		$fragments = apply_filters( 'jet-engine/data-stores/ajax-store-fragments', $fragments, $this, $post_id );

		wp_send_json_success(
			array(
				'count'     => $count,
				'fragments' => $fragments,
			)
		);

	}

	public function increase_post_count( $post_id ) {

		$count = $this->get_post_count( $post_id );
		$count++;

		update_post_meta( $post_id, $this->count_posts_key . $this->get_slug(), $count );

		/**
		 * Allow to custom data stores to increase items count
		 */
		do_action( 'jet-engine/data-stores/post-count-increased', $post_id, $count, $this );

		return $count;
	}

	public function decrease_post_count( $post_id ) {

		$count = $this->get_post_count( $post_id );
		$count--;

		if ( $count < 0 ) {
			$count = 0;
		}

		update_post_meta( $post_id, $this->count_posts_key . $this->get_slug(), $count );

		/**
		 * Allow to custom data stores to decrease items count
		 */
		do_action( 'jet-engine/data-stores/post-count-decreased', $post_id, $count, $this );

		return $count;
	}

	public function verify_request() {

		$post_id = ! empty( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : false;
		$store = ! empty( $_REQUEST['store'] ) ? $_REQUEST['store'] : false;

		if ( ! $post_id ) {
			wp_send_json_error( array( 'message' => __( 'Post ID not found in the request', 'jet-engine' ) ) );
		}

		if ( ! $store ) {
			wp_send_json_error( array( 'message' => __( 'Store slug not found in the request', 'jet-engine' ) ) );
		}

		if ( $store !== $this->get_slug() ) {
			wp_send_json_error( array( 'message' => __( 'Invalid store requested', 'jet-engine' ) ) );
		}

	}

	public function register_store_instatnces_js_object() {

		$data = sprintf(
			'window.JetEngineRegisteredStores = window.JetEngineRegisteredStores || {};
			window.JetEngineRegisteredStores[\'%1$s\'] = \'%2$s\';',
			$this->get_slug(),
			$this->get_type()->type_id()
		);

		wp_add_inline_script( 'jet-engine-frontend', $data, 'before' );
	}

}
