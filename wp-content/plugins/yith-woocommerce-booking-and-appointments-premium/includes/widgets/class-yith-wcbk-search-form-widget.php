<?php
/**
 * Class YITH_WCBK_Search_Form_Widget
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit();

if ( ! class_exists( 'YITH_WCBK_Search_Form_Widget' ) ) {
	/**
	 * YITH_WCBK_Search_Form_Widget
	 *
	 * @since  1.0.0
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Search_Form_Widget extends WC_Widget {
		/**
		 * Constructor
		 */
		public function __construct() {
			$this->widget_cssclass    = 'yith_wcbk_booking_search_form_widget';
			$this->widget_description = __( 'Display booking search form', 'yith-booking-for-woocommerce' );
			$this->widget_id          = 'yith_wcbk_search_form';
			$this->widget_name        = _x( 'Booking Search Form', 'Widget Name', 'yith-booking-for-woocommerce' );

			$forms = yith_wcbk()->search_form_helper->get_forms_in_array();
			$forms = array( '' => __( 'Select a search form...', 'yith-booking-for-woocommerce' ) ) + $forms;

			$this->settings = array(
				'title'                  => array(
					'type'  => 'text',
					'std'   => _x( 'Booking Search Form', 'Default title for booking search form widget', 'yith-booking-for-woocommerce' ),
					'label' => __( 'Title', 'yith-booking-for-woocommerce' ),
				),
				'form'                   => array(
					'type'    => 'select',
					'label'   => __( 'Search Form', 'yith-booking-for-woocommerce' ),
					'std'     => '',
					'options' => $forms,
				),
				'hide-in-single-product' => array(
					'type'  => 'checkbox',
					'label' => __( 'Hide in single product', 'yith-booking-for-woocommerce' ),
					'std'   => 0,
				),
			);

			parent::__construct();
		}

		/**
		 * Print the widget
		 *
		 * @param array $args     Arguments.
		 * @param array $instance Widget data.
		 */
		public function widget( $args, $instance ) {
			$form_id                = ! empty( $instance['form'] ) ? absint( $instance['form'] ) : 0;
			$hide_in_single_product = ! empty( $instance['hide-in-single-product'] ) ? $instance['hide-in-single-product'] : 0;

			if ( ! ! $hide_in_single_product && function_exists( 'is_product' ) && is_product() ) {
				return;
			}

			if ( ! $form_id ) {
				return;
			}

			if ( $this->get_cached_widget( $args ) ) {
				return;
			}

			$form = new YITH_WCBK_Search_Form( $form_id );
			if ( ! $form->is_valid() ) {
				return;
			}

			ob_start();

			$style = 'yith_wcbk_booking_search_form_widget-' . $form_id;

			$args['before_widget'] = str_replace( 'yith_wcbk_booking_search_form_widget', "yith_wcbk_booking_search_form_widget $style", $args['before_widget'] );

			$this->widget_start( $args, $instance );

			echo do_shortcode( '[shop_messages]' );
			$form->output();

			$this->widget_end( $args );

			wp_reset_postdata();
			echo $this->cache_widget( $args, ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Outputs the settings update form.
		 *
		 * @param array $instance Widget data.
		 */
		public function form( $instance ) {
			parent::form( $instance );

			echo '<p style="text-align:right">';
			echo sprintf(
				'<a href="%s">%s</a>',
				esc_url( add_query_arg( array( 'post_type' => YITH_WCBK_Post_Types::SEARCH_FORM ), admin_url( 'post-new.php' ) ) ),
				esc_html__( 'Create search booking form', 'yith-booking-for-woocommerce' )
			);
			echo '</p>';
		}
	}
}
