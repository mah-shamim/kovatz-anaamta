<?php
/**
 * Conditions manager class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Meta_Boxes_Conditions class
 */
class Jet_Engine_Meta_Boxes_Conditions {

	private $_conditions = array();
	private $_ajax_stack = array();

	public function __construct() {

		if ( ! is_admin() ) {
			return;
		}

		$this->register_conditions();

		add_action( 'wp_ajax_jet-engine/meta-box/update-conditions', array( $this, 'ajax_check_conditions' ) );

	}

	/**
	 * Register allowed visibility conditions for Meta boxes
	 *
	 * @return [type] [description]
	 */
	public function register_conditions() {

		require jet_engine()->meta_boxes->component_path( 'conditions/base.php' );
		require jet_engine()->meta_boxes->component_path( 'conditions/include-posts.php' );
		require jet_engine()->meta_boxes->component_path( 'conditions/exclude-posts.php' );
		require jet_engine()->meta_boxes->component_path( 'conditions/include-user-roles.php' );
		require jet_engine()->meta_boxes->component_path( 'conditions/exclude-user-roles.php' );
		require jet_engine()->meta_boxes->component_path( 'conditions/post-has-terms.php' );

		$this->register_condition_type( new \Jet_Engine\Components\Meta_Boxes\Conditions\Include_Posts() );
		$this->register_condition_type( new \Jet_Engine\Components\Meta_Boxes\Conditions\Exclude_Posts() );
		$this->register_condition_type( new \Jet_Engine\Components\Meta_Boxes\Conditions\Include_User_Roles() );
		$this->register_condition_type( new \Jet_Engine\Components\Meta_Boxes\Conditions\Exclude_User_Roles() );
		$this->register_condition_type( new \Jet_Engine\Components\Meta_Boxes\Conditions\Post_Has_Terms() );

		do_action( 'jet-engine/meta-boxes/conditions/register', $this );

	}

	/**
	 * Resgister new condition instance
	 *
	 * @param  [type] $condition_instance [description]
	 * @return [type]                     [description]
	 */
	public function register_condition_type( $condition_instance ) {
		$this->_conditions[ $condition_instance->get_key() ] = $condition_instance;
	}

	/**
	 * Get all conditions list
	 *
	 * @return [type] [description]
	 */
	public function get_conditions( $key = null ) {

		if ( ! $key ) {
			return $this->_conditions;
		} else {
			return isset( $this->_conditions[ $key ] ) ? $this->_conditions[ $key ] : false;
		}
	}

	/**
	 * Get conditions data for using in JS of Meta Box edit page
	 * @return [type] [description]
	 */
	public function get_conditions_data_for_edit() {

		$result = array();

		foreach ( $this->get_conditions() as $condition ) {
			$result[] = array(
				'key'     => $condition->get_key(),
				'name'    => $condition->get_name(),
				'sources' => $condition->allowed_sources(),
			);
		}

		return $result;

	}

	/**
	 * Register conditions to check with AJAX from post/term/user editor
	 * @param [type] $screen [description]
	 * @param [type] $data   [description]
	 */
	public function add_to_ajax_stack( $screen, $data ) {

		if ( is_array( $screen ) ) {

			foreach ( $screen as $_screen ) {
				$this->add_to_ajax_stack( $_screen, $data );
			}

			return;
		}

		if ( ! $screen ) {
			return;
		}

		if ( empty( $this->_ajax_stack[ $screen ] ) ) {
			$this->_ajax_stack[ $screen ] = array();
		}

		if ( ! in_array( $data, $this->_ajax_stack[ $screen ] ) ) {
			$this->_ajax_stack[ $screen ][] = $data;
		}

	}

	/**
	 * [get_screen_name description]
	 * @return [type] [description]
	 */
	public function get_screen_name( $args = array() ) {

		$source = ! empty( $args['object_type'] ) ? $args['object_type'] : 'post';
		$screen = null;

		switch ( $source ) {

			case 'post':
				$screen = ! empty( $args['allowed_post_type'] ) ? $args['allowed_post_type'] : array();
				break;

			case 'taxonomy':

				$taxes  = ! empty( $args['allowed_tax'] ) ? $args['allowed_tax'] : array();
				$screen = array();

				foreach ( $taxes as $tax ) {
					$screen[] = 'edit-' . $tax;
				}

				break;

			case 'user':
				$screen = array( 'user-edit', 'profile' );
				break;

			default:
				$screen = apply_filters( 'jet-engine/meta-boxes/conditions/get-ajax-screen/' . $source, $screen, $args, $this );
				break;

		}

		if ( $screen ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_ajax_handler' ) );
		}

		return $screen;

	}

	/**
	 * [get_ajax_stack description]
	 * @return [type] [description]
	 */
	public function get_ajax_stack() {
		return $this->_ajax_stack;
	}

	/**
	 * Enqueue JS with ajax conditions
	 * @return [type] [description]
	 */
	public function enqueue_ajax_handler() {

		global $current_screen;

		$stack = $this->get_ajax_stack();

		if ( empty( $stack ) || ! isset( $stack[ $current_screen->id ] ) ) {
			return;
		}

		wp_enqueue_script(
			'jet-engine-mb-ajax-conditions',
			jet_engine()->plugin_url( 'includes/components/meta-boxes/assets/js/ajax-conditions.js' ),
			array( 'jquery' ),
			jet_engine()->get_version(),
			true
		);

		wp_localize_script( 'jet-engine-mb-ajax-conditions', 'JetEnginMBAjaxConditionsData', $stack[ $current_screen->id ] );
		wp_localize_script( 'jet-engine-mb-ajax-conditions', 'JetEnginMBAjaxConditionsSettings', array(
			'nonce' => wp_create_nonce( 'jet-engine/meta-boxes/conditions' ),
		) );
		wp_localize_script( 'jet-engine-mb-ajax-conditions', 'JetEnginMBAjaxConditionsHandlers', array( 'default' => null ) );

		foreach ( $stack[ $current_screen->id ] as $stack_item ) {

			$condition_type = $this->get_conditions( $stack_item['condition_type'] );

			if ( $condition_type ) {
				wp_add_inline_script( 'jet-engine-mb-ajax-conditions', $condition_type->get_js_handler(), 'before' );
			}
		}

	}

	/**
	 * Ajax callback to check given meta box conditions
	 */
	public function ajax_check_conditions() {

		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		// There is no senesetive data returned by this callback so there is no need to additonal verfifcation except nonce
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'jet-engine/meta-boxes/conditions' ) ) {
			wp_send_json_error( 'Invalid request' );
		}

		$conditions = ! empty( $_REQUEST['conditions'] ) ? $_REQUEST['conditions'] : array();
		$request = $_REQUEST;
		$result = array();

		unset( $request['conditions'] );
		unset( $request['action'] );
		unset( $request['nonce'] );

		foreach ( $conditions as $condition ) {

			$condition_key = ! empty( $condition['condition_type'] ) ? $condition['condition_type'] : false;

			if ( ! $condition_key ) {
				continue;
			}

			$condition_type = $this->get_conditions( $condition_key );

			if ( ! $condition_type ) {
				continue;
			}

			if ( $condition_type->check_condition( array( 'settings' => $condition, 'request' => $request ) ) ) {
				$result[] = array(
					'id' => $condition['meta_box'],
					'display' => 'block',
				);
			} else {
				$result[] = array(
					'id' => $condition['meta_box'],
					'display' => 'none',
				);
			}
		}
		
		wp_send_json_success( $result );

	}

	/**
	 * Check conditions
	 *
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	public function check_conditions( $id, $args ) {

		// Meta box conditions affects only renererd meta box itself so is is nothing to do on front-end on rest API requests
		if ( ! is_admin() ) {
			return false;
		}

		$active_conditions = ! empty( $args['active_conditions'] ) ? $args['active_conditions'] : array();

		foreach ( $active_conditions as $condition_key ) {

			$condition_type = $this->get_conditions( $condition_key );

			if ( ! $condition_type ) {
				continue;
			}

			if ( $condition_type->is_ajax() ) {
				
				$this->add_to_ajax_stack(
					$this->get_screen_name( $args ),
					array_merge( array(
						'condition_type' => $condition_key,
						'meta_box'       => $id,
					), $condition_type->get_ajax_data_from_args( $args ) )
				);

			} elseif ( ! $condition_type->check_condition( array( 'settings' => $args ) ) ) {
				return false;
			}

		}

		return true;

	}

}
