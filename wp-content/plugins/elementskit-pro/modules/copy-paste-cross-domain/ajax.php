<?php

defined('ABSPATH') || exit;

use Elementor\Controls_Stack;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Core\Common\Modules\Ajax\Module;

/**
 * Class Name : Ajax - Includes code section executable for plugin ajax call
 *
 * @since 1.0.0
 * @access Public
 */
class Elementskit_Copy_Paste_Ajax {

    private $elementor;

    public function __construct() {

        $this->elementor = Plugin::$instance;
        add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );
    }

    /**
     * Register action methods from elementor ajax request hooks
     * 
	 * @since 1.0.0
	 * @access public
     * 
     * @param Elementor\Core\Common\Modules\Ajax\Module $ajax
     * @return void
     */
    public function register_ajax_actions( Module $ajax ) {
		$ajax->register_ajax_action( 'ekit_copy_paste', function( array $data ) {

			if ( ! current_user_can( 'edit_posts' ) ) {
                throw new \Exception( __( 'Access denied [CP]', 'elementskit' ) );
			} elseif(!isset($data['type']) || $data['type'] !== 'single') {
                throw new \Exception( __( 'Invalid type [CP]', 'elementskit' ) );
            } elseif(!isset($data['template']) || empty($data['template'])) {
                throw new \Exception( __( 'Element code not found [CP]', 'elementskit' ) );
            }

			return $this->prepare_copy_paste_data($data['template']);
        } );
    }

    /**
     * Process element object data by importing necessary media content in current server
     * 
	 * @since 1.0.0
	 * @access public
     *
     * @return void
     */
    public function prepare_copy_paste_data($data) {   
        if(is_string($data)) {
            $data = json_decode( $data, true );
        }

        // Enable additional file-mime support
		add_filter( 'upload_mimes', [ $this, 'additional_file_support' ] );

        $content = $this->replace_elements_ids( $data );
		$content = $this->process_import_content( $content, 'on_import' );

		// Disable additional file-mime support
		remove_filter( 'upload_mimes', [ $this, 'additional_file_support' ] );

        return $content;
    }

    /**
     * Add additional file support with existing mime-support array
     *
	 * @since 1.0.0
	 * @access public
     * 
     * @param [array] $mimes
     * @return array
     */
    public function additional_file_support( $mimes = [] ) {
        $mimes['svg']  = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';
        $mimes['pdf']  = 'application/pdf'; 
		return $mimes;
	}

    /**
	 * Replace elements IDs.
	 *
	 * For any given Elementor content/data, replace the IDs with new randomly
	 * generated IDs.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array $content Any type of Elementor data.
	 *
	 * @return mixed Iterated data.
	 */
	protected function replace_elements_ids( $content ) {
		return $this->elementor->db->iterate_data( $content, function( $element ) {
			$element['id'] = Utils::generate_random_string();

			return $element;
		} );
	}

	/**
	 * Process content for import.
	 *
	 * Process the content and all the inner elements, and prepare all the
	 * elements data for import.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array  $content A set of elements.
	 * @param string $method  Accepts `on_import` to import data.
	 *
	 * @return mixed Processed content data.
	 */
	protected function process_import_content( $content, $method ) {
		return $this->elementor->db->iterate_data(
			$content, function( $element_data ) use ( $method ) {
				$element = $this->elementor->elements_manager->create_element_instance( $element_data );

				// If the widget/element isn't exist, like a plugin that creates a widget but deactivated
				if ( ! $element ) {
					return null;
				}

				return $this->process_element_import_content( $element, $method );
			}
		);
	}

	/**
	 * Process single element content for import.
	 *
	 * Process any given element and prepare the element data for import.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param Controls_Stack $element
	 * @param string         $method
	 *
	 * @return array Processed element data.
	 */
	protected function process_element_import_content( Controls_Stack $element, $method ) {
		$element_data = $element->get_data();

		if ( method_exists( $element, $method ) ) {
			// TODO: Use the internal element data without parameters.
			$element_data = $element->{$method}( $element_data );
		}

		foreach ( $element->get_controls() as $control ) {
			$control_class = $this->elementor->controls_manager->get_control( $control['type'] );

			// If the control isn't exist, like a plugin that creates the control but deactivated.
			if ( ! $control_class ) {
				return $element_data;
			}

			if ( method_exists( $control_class, $method ) ) {
				$element_data['settings'][ $control['name'] ] = $control_class->{$method}( $element->get_settings( $control['name'] ), $control );
			}
		}

		return $element_data;
	}
}