<?php
namespace Jet_Engine\Modules\Dynamic_Visibility;

class Elementor_Integration extends Condition_Checker {

	/**
	 * Holder for hidden elements ids.
	 *
	 * @var array
	 */
	private $hidden_elements_ids = array();

	/**
	 * @var boolean
	 */
	private $need_unregistered_inline_css_widget = false;

	private $resize_columns_ids = array();

	public function __construct() {

		if ( ! jet_engine()->has_elementor() ) {
			return;
		}
		
		require jet_engine()->modules->modules_path( 'dynamic-visibility/inc/elementor-settings.php' );
		new Settings();

		$el_types = array(
			'section',
			'column',
			'widget',
			'container',
		);

		foreach ( $el_types as $el ) {
			//add_filter( 'elementor/frontend/' . $el . '/should_render', array( $this, 'check_cond' ), 10, 2 );

			add_action( 'elementor/frontend/' . $el . '/before_render', array( $this, 'before_element_render' ) );
			add_action( 'elementor/frontend/' . $el . '/after_render',  array( $this, 'after_element_render' ) );

		}

		add_action( 'elementor/element/after_add_attributes', array( $this, 'maybe_add_resize_columns_class' ) );
		add_action( 'elementor/frontend/column/after_render', array( $this, 'add_resize_columns_prop' ) );

	}


	/**
	 * Maybe add conditions hooks for hidden elements.
	 *
	 * @param object $element
	 */
	public function before_element_render( $element ) {

		$settings = $element->get_settings();

		$is_enabled = ! empty( $settings['jedv_enabled'] ) ? $settings['jedv_enabled'] : false;
		$is_enabled = filter_var( $is_enabled, FILTER_VALIDATE_BOOLEAN );

		if ( ! $is_enabled ) {
			return;
		}

		$is_visible = $this->check_cond( $element->get_settings(), $element->get_settings_for_display() );

		if ( ! $is_visible ) {
			add_filter( 'elementor/element/get_child_type', '__return_false' ); // for prevent getting content of inner elements.
			add_filter( 'elementor/frontend/' . $element->get_type() . '/should_render', '__return_false' );

			if ( 'widget' === $element->get_type() ) {

				$is_inline_css_mode = \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_css_loading' );

				if ( $is_inline_css_mode && ! in_array( $element->get_name(), $element::$registered_inline_css_widgets ) ) {
					$this->need_unregistered_inline_css_widget = true;
				}
			}

			$this->hidden_elements_ids[] = $element->get_id();
		}
	}

	/**
	 * Maybe remove conditions hooks for hidden elements.
	 *
	 * @param object $element
	 */
	public function after_element_render( $element ) {

		if ( ! in_array( $element->get_id(), $this->hidden_elements_ids ) ) {
			return;
		}

		remove_filter( 'elementor/element/get_child_type', '__return_false' );
		remove_filter( 'elementor/frontend/' . $element->get_type() . '/should_render', '__return_false' );

		if ( 'widget' === $element->get_type() && $this->need_unregistered_inline_css_widget ) {

			if ( in_array( $element->get_name(), $element::$registered_inline_css_widgets ) ) {

				$registered_inline_css_widgets = $element::$registered_inline_css_widgets;
				$index = array_search( $element->get_name(), $registered_inline_css_widgets );

				unset( $registered_inline_css_widgets[ $index ] );

				$element::$registered_inline_css_widgets = $registered_inline_css_widgets;
			}

			$this->need_unregistered_inline_css_widget = false;
		}
	}

	/**
	 * Add `jedv_resize_columns` property for column.
	 *
	 * @param $column
	 */
	public function add_resize_columns_prop( $column ) {

		if ( empty( $this->hidden_elements_ids ) ) {
			return;
		}

		if ( ! in_array( $column->get_id(), $this->hidden_elements_ids ) ) {
			return;
		}

		$settings = $column->get_settings();

		if ( ! isset( $settings['jedv_resize_columns'] ) ) {
			return;
		}

		if ( ! filter_var( $settings['jedv_resize_columns'], FILTER_VALIDATE_BOOLEAN ) ) {
			return;
		}

		$this->resize_columns_ids[] = $column->get_id();
	}

	/**
	 * Maybe add `jedv-resize-columns` css class for section.
	 *
	 * @param $section
	 */
	public function maybe_add_resize_columns_class( $section ) {

		if ( 'section' !== $section->get_type() ) {
			return;
		}

		$has_resize_columns = false;

		foreach ( $section->get_children() as $column ) {
			if ( in_array( $column->get_id(), $this->resize_columns_ids ) ) {
				$has_resize_columns = true;
				break;
			}
		}

		if ( $has_resize_columns ) {
			$section->add_render_attribute( '_wrapper', array(
				'class' => 'jedv-resize-columns',
			) );
		}
	}

}
