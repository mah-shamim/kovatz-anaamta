<?php
/**
 * YITH_WCBK_Search_Form Class
 *
 * @package YITH\Booking\Classes
 * @author  YITH
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Search_Form' ) ) {
	/**
	 * Class YITH_WCBK_Search_Form
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Search_Form {
		/**
		 * The ID.
		 *
		 * @var int
		 */
		private $id;

		/**
		 * Instance number.
		 *
		 * @var int
		 */
		public static $instance_number = 0;

		/**
		 * Current instance number;
		 *
		 * @var int
		 */
		private $current_instance_number;

		/**
		 * Data.
		 *
		 * @var array
		 */
		private $data = array(
			'fields'                      => array(
				'search'     => array(
					'enabled' => 'no',
					'label'   => '',
				),
				'location'   => array(
					'enabled'       => 'yes',
					'default_range' => 30,
					'show_range'    => 'yes',
				),
				'categories' => array(
					'enabled' => 'no',
				),
				'tags'       => array(
					'enabled' => 'no',
				),
				'date'       => array(
					'enabled' => 'yes',
					'type'    => '',
				),
				'persons'    => array(
					'enabled' => 'yes',
					'type'    => 'persons',
				),
				'services'   => array(
					'enabled' => 'yes',
					'type'    => '',
				),
			),
			'layout'                      => 'vertical',
			'colors'                      => array(
				'background' => 'transparent',
				'text'       => '#333333',
			),
			'search_button_colors'        => array(
				'background'       => '#3b4b56',
				'text'             => '#ffffff',
				'background-hover' => '#2e627c',
				'text-hover'       => '#ffffff',
			),
			'search_button_border_radius' => array(
				'dimensions' => array(
					'top-left'     => 5,
					'top-right'    => 5,
					'bottom-right' => 5,
					'bottom-left'  => 5,
				),
				'unit'       => 'px',
				'linked'     => 'yes',
			),
			'show_results'                => 'popup',
		);

		/**
		 * Meta to prop map.
		 *
		 * @var array
		 * @since 3.0.0
		 */
		private $meta_to_prop = array(
			'_yith_wcbk_admin_search_form_fields' => 'fields',
			'_layout'                             => 'layout',
			'_colors'                             => 'colors',
			'_search-button-colors'               => 'search_button_colors',
			'_show-results'                       => 'show_results',
			'_search_button_border_radius'        => 'search_button_border_radius',
		);

		/**
		 * Default data.
		 *
		 * @var array
		 * @since 3.0.0
		 */
		private $default_data = array();

		/**
		 * YITH_WCBK_Search_Form constructor.
		 *
		 * @param int $id Search form ID.
		 */
		public function __construct( $id ) {
			$this->id           = absint( $id );
			$this->default_data = $this->data;

			self::$instance_number ++;
			$this->current_instance_number = self::$instance_number;

			// todo: maybe it should be better using data stores.
			$this->read();
		}

		/**
		 * Magic Getter for backward compatibility.
		 *
		 * @param string $key The key.
		 *
		 * @return mixed
		 */
		public function __get( $key ) {
			yith_wcbk_doing_it_wrong( $key, 'Search form properties should not be accessed directly.', '3.0.0' );

			if ( 'id' === $key ) {
				return $this->get_id();
			}

			return null;
		}

		/*
		|--------------------------------------------------------------------------
		| Getters
		|--------------------------------------------------------------------------
		|
		| Methods for getting data from the product object.
		*/

		/**
		 * Get fields
		 *
		 * @return array
		 */
		public function get_fields() {
			return $this->get_prop( 'fields' );
		}

		/**
		 * Get colors
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function get_colors() {
			return $this->get_prop( 'colors' );
		}

		/**
		 * Get search_button_colors
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function get_search_button_colors() {
			return $this->get_prop( 'search_button_colors' );
		}

		/**
		 * Get search_button_border_radius
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function get_search_button_border_radius() {
			return $this->get_prop( 'search_button_border_radius' );
		}

		/**
		 * Get show_results
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_show_results() {
			return $this->get_prop( 'show_results' );
		}

		/**
		 * Get layout
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_layout() {
			return $this->get_prop( 'layout' );
		}

		/*
		|--------------------------------------------------------------------------
		| Setters
		|--------------------------------------------------------------------------
		|
		| Functions for setting product data.
		*/

		/**
		 * Set fields
		 *
		 * @param array $value The fields.
		 *
		 * @since 3.0.0
		 */
		public function set_fields( $value ) {
			$value = is_array( $value ) ? $value : array();

			// Fill the array with default fields, if missing. Don't use wp_parse_args/array_merge since fields are custom-sorted.
			foreach ( $this->default_data['fields'] as $key => $field ) {
				if ( ! isset( $value[ $key ] ) ) {
					$field['enabled'] = 'no'; // If not exist, it'll be disabled.
					$value[ $key ]    = $field;
				}
			}

			$this->set_prop( 'fields', $value );
		}

		/**
		 * Set colors
		 *
		 * @param array $value The colors.
		 */
		public function set_colors( $value ) {
			$value = is_array( $value ) ? $value : array();
			$value = wp_parse_args( $value, $this->default_data['colors'] );

			$this->set_prop( 'colors', $value );
		}

		/**
		 * Set search_button_colors
		 *
		 * @param array $value The search button colors.
		 */
		public function set_search_button_colors( $value ) {
			$value = is_array( $value ) ? $value : array();
			$value = wp_parse_args( $value, $this->default_data['search_button_colors'] );

			$this->set_prop( 'search_button_colors', $value );
		}

		/**
		 * Set search_button_border_radius
		 *
		 * @param array $value The search button border radius.
		 */
		public function set_search_button_border_radius( $value ) {
			$value = is_array( $value ) ? $value : array();
			$value = wp_parse_args( $value, $this->default_data['search_button_border_radius'] );

			$this->set_prop( 'search_button_border_radius', $value );
		}

		/**
		 * Set show_results
		 *
		 * @param array $value The show-results value.
		 */
		public function set_show_results( $value ) {
			$allowed = array( 'popup', 'shop' );
			$value   = in_array( $value, $allowed, true ) ? $value : $this->default_data['show_results'];
			$this->set_prop( 'show_results', $value );
		}

		/**
		 * Set layout
		 *
		 * @param string $value The layout.
		 */
		public function set_layout( $value ) {
			$allowed = array( 'vertical', 'horizontal' );
			$value   = in_array( $value, $allowed, true ) ? $value : $this->default_data['layout'];
			$this->set_prop( 'layout', $value );
		}

		/*
		|--------------------------------------------------------------------------
		| CRUD Methods
		|--------------------------------------------------------------------------
		|
		*/

		/**
		 * Read from database.
		 *
		 * @since 3.0.0
		 */
		protected function read() {
			$post_meta_values = get_post_meta( $this->get_id() );
			$props_to_set     = array();

			foreach ( $this->meta_to_prop as $meta_key => $prop ) {
				$meta_value            = $post_meta_values[ $meta_key ][0] ?? null;
				$props_to_set[ $prop ] = maybe_unserialize( $meta_value ); // get_post_meta only un-serializes single values.
			}

			foreach ( $props_to_set as $prop => $value ) {
				if ( is_null( $value ) ) {
					continue;
				}
				$setter = "set_{$prop}";
				if ( is_callable( array( $this, $setter ) ) ) {
					$this->{$setter}( $value );
				} else {
					$this->set_prop( $prop, $value );
				}
			}
		}

		/**
		 * Set object properties
		 *
		 * @param string $prop  The property.
		 * @param mixed  $value The value.
		 *
		 * @since 3.0.0
		 */
		public function set_prop( $prop, $value ) {
			if ( array_key_exists( $prop, $this->data ) ) {
				$this->data[ $prop ] = $value;
			}
		}

		/**
		 * Get object properties
		 *
		 * @param string $prop The prop.
		 *
		 * @return mixed
		 * @since 3.0.0
		 */
		protected function get_prop( $prop ) {
			$value = null;

			if ( array_key_exists( $prop, $this->data ) ) {
				$value = $this->data[ $prop ];
			}

			return $value;
		}

		/*
		|--------------------------------------------------------------------------
		| Non CRUD Methods
		|--------------------------------------------------------------------------
		|
		*/

		/**
		 * Return the current instance number.
		 *
		 * @return int
		 */
		public function get_current_instance_number() {
			return $this->current_instance_number;
		}

		/**
		 * Get the ID.
		 *
		 * @return int
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Return an unique identifier
		 * id-current_instance_number
		 *
		 * @return string
		 */
		public function get_unique_id() {
			return $this->get_id() . '-' . $this->get_current_instance_number();
		}

		/**
		 * Get style settings.
		 *
		 * @return array
		 */
		public function get_styles() {
			return array(
				'style'                => 'default', // Deprecated 3.0.0. Kept for backward compatibility.
				'colors'               => $this->get_colors(),
				'search-button-colors' => $this->get_search_button_colors(),
			);
		}


		/**
		 * Is this valid?
		 *
		 * @return bool
		 */
		public function is_valid() {
			return ! empty( $this->get_id() ) && get_post_type( $this->get_id() ) === YITH_WCBK_Post_Types::SEARCH_FORM;
		}

		/**
		 * Retrieve the CSS custom style.
		 *
		 * @return string
		 */
		public function get_css_style() {
			$form_id   = $this->get_id();
			$selectors = array(
				'form'                => '.yith-wcbk-booking-search-form-' . esc_attr( $form_id ),
				'widget'              => '.yith_wcbk_booking_search_form_widget-' . esc_attr( $form_id ),
				'search_button'       => '.yith-wcbk-booking-search-form-submit',
				'search_button:hover' => '.yith-wcbk-booking-search-form-submit:hover',
			);

			$colors               = $this->get_colors();
			$search_colors        = $this->get_search_button_colors();
			$search_border_radius = $this->get_search_button_border_radius();
			$search_border_radius = yith_plugin_fw_parse_dimensions( $search_border_radius );
			$search_border_radius = implode( ' ', $search_border_radius );

			$styles = array(
				array(
					'parents'   => array( $selectors['form'], $selectors['widget'] ),
					'selector'  => null,
					'styles'    => array(
						'background' => $colors['background'],
						'color'      => $colors['text'],
					),
					'important' => true,
				),
				array(
					'parents'   => array( $selectors['form'] ),
					'selector'  => $selectors['search_button'],
					'styles'    => array(
						'background'    => $search_colors['background'],
						'color'         => $search_colors['text'],
						'border-radius' => $search_border_radius,
					),
					'important' => true,
				),
				array(
					'parents'   => array( $selectors['form'] ),
					'selector'  => $selectors['search_button:hover'],
					'styles'    => array(
						'background' => $search_colors['background-hover'],
						'color'      => $search_colors['text-hover'],
					),
					'important' => true,
				),
			);

			return yith_wcbk_css( $styles );
		}

		/**
		 * Print the search form
		 *
		 * @param array $args Arguments.
		 */
		public function output( $args = array() ) {
			static $printed_css = array();
			if ( ! $this->is_valid() ) {
				return;
			}

			if ( ! in_array( $this->get_id(), $printed_css, true ) ) {
				echo '<style>' . $this->get_css_style() . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$printed_css[] = $this->get_id();
			}

			$defaults            = array(
				'cat' => '',
			);
			$args                = wp_parse_args( $args, $defaults );
			$args['search_form'] = $this;

			wc_get_template( 'booking/search-form/booking-search-form.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
		}

		/*
		|--------------------------------------------------------------------------
		| Deprecated Methods
		|--------------------------------------------------------------------------
		|
		*/

		/**
		 * Retrieve the post object.
		 *
		 * @return null|WP_Post
		 * @deprecated 3.0.0
		 */
		public function get_post_data() {
			return get_post( $this->get_id() );
		}

		/**
		 * Get options.
		 *
		 * @return array
		 * @deprecated 3.0.0
		 */
		public function get_options() {
			return array(
				'show-results' => $this->get_show_results(),
			);
		}
	}
}
