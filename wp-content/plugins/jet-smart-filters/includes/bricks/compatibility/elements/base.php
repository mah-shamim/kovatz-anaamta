<?php
namespace Jet_Engine\Bricks_Views\Elements;

use Jet_Engine\Bricks_Views\Helpers\Preview;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Base extends \Bricks\Element {
	
	// Element properties
	public $category     = 'general'; // Use predefined element category 'general'
	public $name         = ''; // Make sure to prefix your elements
	public $icon         = 'ti-bolt-alt'; // Themify icon font class
	public $css_selector = ''; // Default CSS selector
	public $scripts      = []; // Script(s) run when element is rendered on frontend or updated in builder

	public $current_jet_group = null;
	public $current_jet_tab   = null;

	public $jet_element_render_instance;
	public $jet_element_render = '';

	// Return localised element label
	public function get_label() {
		return '';
	}

	/**
	 * Register new control group
	 * You can't add elements into new groups without registering these groups before
	 * 
	 * @param  [type] $name [description]
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function register_jet_control_group( $name, $data = [] ) {
		$this->control_groups[ $name ] = $data;
	}

	/**
	 * Start controls group (aka Sections in Elementor)
	 * @param  [type] $group [description]
	 * @return [type]        [description]
	 */
	public function start_jet_control_group( $group ) {
		
		$data = isset( $this->control_groups[ $group ] ) ? $this->control_groups[ $group ] : [];
		$this->current_jet_tab = isset( $data['tab'] ) ? $data['tab'] : 'content';

		$this->current_jet_group = $group;

	}

	/**
	 * End controls grous
	 * @return [type] [description]
	 */
	public function end_jet_control_group() {
		$this->current_jet_tab   = null;
		$this->current_jet_group = null;
	}

	/**
	 * Wrapper to register control
	 * 
	 * @param  [type] $name [description]
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function register_jet_control( $name, $data = [] ) {

		if ( ! $this->current_jet_tab ) {
			$this->current_jet_tab = 'content';
		}

		$data['tab']   = $this->current_jet_tab;
		$data['group'] = $this->current_jet_group;

		$this->controls[ $name ] = $data;

	}

	public function get_jet_settings( $setting = null, $default = false ) {

		if ( ! $setting ) {
			return $this->settings;
		}
		
		$value = null;

		if ( isset( $this->settings[ $setting ] ) ) {
			$value = $this->settings[ $setting ];
		} else {
			$value = isset( $this->controls[ $setting ] ) && isset( $this->controls[ $setting ]['default'] ) ? $this->controls[ $setting ]['default'] : $default;
		}

		return $value;
	}

	public function parse_jet_render_attributes( $attrs = [] ) {
		return apply_filters( 'jet-engine/bricks-views/element/parsed-attrs', $attrs, $this );
	}

	public function jet_get_request_data() {
		$data = false;

		if ( bricks_is_rest_call() ) {
			$data = file_get_contents( 'php://input' );
		} elseif ( wp_doing_ajax() && 'bricks_render_element' === $_REQUEST['action'] ) {
			$data = $_REQUEST;
		}

		if ( ! $data ) {
			return false;
		}

		if ( is_string( $data ) ) {
			$data = json_decode( $data, true );
		}

		return $data;
	}

	public function get_post_id() {

		if ( ! bricks_is_rest_call() && ! wp_doing_ajax() ) {
			return false;
		}

		$data    = $this->jet_get_request_data();
		$post_id = ! empty( $data['postId'] ) ? absint( $data['postId'] ) : false;

		if ( $post_id ) {
			return $post_id;
		} else {
			return false;
		}
		
	}

	public function is_requested_element() {
		$data = $this->jet_get_request_data();
		return ( $data && $data['element']['id'] === $this->id );
	}

	public function get_jet_el_id() {

		$result = '';

		if ( ! empty( $this->settings['_cssId'] ) ) {
			$result = $this->settings['_cssId'];
		}

		if ( ! empty( $this->settings['_attributes'] ) ) {
			foreach ( $this->settings['_attributes'] as $attribute ) {
				if ( 'id' === $attribute['name'] ) {
					$result = $attribute['value'];
				}
			}
		}

		return $result;
	}

	public function get_jet_render_instance() {

		if ( ! $this->jet_element_render_instance ) {

			$settings = $this->get_jet_settings();
			$settings['_element_id'] = $this->get_jet_el_id();

			if ( ! function_exists( 'jet_engine' ) ) {
				return false;
			}
			
			$this->jet_element_render_instance = jet_engine()->listings->get_render_instance( 
				$this->jet_element_render, 
				$this->parse_jet_render_attributes( $settings )
			);

			$post_id = $this->get_post_id();

			if ( $post_id && $this->is_requested_element() ) {
				$preview = new Preview( $post_id );
				$preview->setup_preview_for_render( $this->jet_element_render_instance );
			}

		}

		return $this->jet_element_render_instance;

	}

	// Set builder control groups
	public function set_control_groups() {
	}

	// Set builder controls
	public function set_controls() {
	}

	// Enqueue element styles and scripts
	public function enqueue_scripts() {
	}

	// Render element HTML
	public function render() {
		$this->set_attribute( '_root', 'data-is-block', 'jet-engine/' . $this->jet_element_render );
	}
}