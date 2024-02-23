<?php
/**
 * Class for the building ui-repeater elements.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'CX_Control_Repeater' ) ) {

	/**
	 * Class for the building ui-repeater elements.
	 */
	class CX_Control_Repeater extends CX_Controls_Base {

		/**
		 * Default settings
		 *
		 * @var array
		 */
		public $defaults_settings = array(
			'type'        => 'repeater',
			'id'          => 'cx-ui-repeater-id',
			'name'        => 'cx-ui-repeater-name',
			'value'       => array(),
			'fields'      => array(),
			'label'       => '',
			'add_label'   => 'Add Item',
			'class'       => '',
			'ui_kit'      => true,
			'required'    => false,
			'title_field' => '',
			'collapsed'   => false,
		);

		/**
		 * Stored data to process it while renderinr row
		 *
		 * @var array
		 */
		public $data = array();

		/**
		 * Repeater instances counter
		 *
		 * @var integer
		 */
		public static $instance_id = 0;

		/**
		 * Current onstance TMPL name
		 *
		 * @var string
		 */
		public $tmpl_name = '';

		/**
		 * Holder for templates to print it in bottom of customizer page
		 *
		 * @var string
		 */
		public static $customizer_tmpl_to_print = null;

		/**
		 * Is tmpl scripts already printed in customizer
		 *
		 * @var boolean
		 */
		public static $customizer_tmpl_printed = false;

		/**
		 * Child repeater instances
		 *
		 * @var array
		 */
		private $_childs = array();

		/**
		 * Check if we render template for JS
		 *
		 * @var boolean
		 */
		private $_is_js_row = false;

		/**
		 * Init.
		 *
		 * @since 1.0.0
		 */
		public function init() {

			$this->set_tmpl_data();
			add_action( 'admin_footer', array( $this, 'print_js_template' ), 0 );
			add_action( 'customize_controls_print_footer_scripts', array( $this, 'fix_customizer_tmpl' ), 9999 );

		}

		/**
		 * Retrun scripts dependencies list for current control.
		 *
		 * @return array
		 */
		public function get_script_depends() {
			return array( 'jquery-ui-sortable', 'wp-util' );
		}

		/**
		 * Get required attribute.
		 *
		 * @return string required attribute
		 */
		public function get_required() {
			if ( $this->settings['required'] ) {
				return 'required="required"';
			}
			return '';
		}

		/**
		 * Render html UI_Repeater.
		 *
		 * @since 1.0.1
		 */
		public function render() {

			$html        = '';
			$class       = $this->settings['class'];
			$ui_kit      = ! empty( $this->settings['ui_kit'] ) ? 'cx-ui-kit' : '';
			$value       = ( ! empty( $this->settings['value'] ) && is_array( $this->settings['value'] ) ) ? count( $this->settings['value'] ) : 0 ;
			$title_field = ! empty( $this->settings['title_field'] ) ? 'data-title-field="' . $this->settings['title_field'] . '"' : '' ;

			add_filter( 'cx_control/is_repeater', '__return_true' );

			$html .= sprintf( '<div class="cx-ui-repeater-container cx-ui-container %1$s %2$s">',
					$ui_kit,
					esc_attr( $class )
				);
				if ( '' !== $this->settings['label'] ) {
					$html .= '<label class="cx-label" for="' . esc_attr( $this->settings['id'] ) . '">' . wp_kses_post( $this->settings['label'] ) . '</label> ';
				}

				$html .= sprintf(
					'<div class="cx-ui-repeater-list" data-name="%1$s" data-index="%2$s" data-widget-id="__i__" %3$s id="%4$s">',
					$this->get_tmpl_name(),
					$value,
					$title_field,
					esc_attr( $this->settings['id'] )
				);

				if ( is_array( $this->settings['value'] ) ) {
					$index = 0;
					foreach ( $this->settings['value'] as $data ) {
						$this->_is_js_row = false;
						$html .= $this->render_row( $index, false, $data );
						$index++;
					}
				}
				$html .= '</div>';
				$html .= sprintf(
					'<a href="#" class="cx-ui-repeater-add">%1$s</a>',
					esc_html( $this->settings['add_label'] )
				);
			$html .= '</div>';

			/**
			 * Maybe add js repeater template to response
			 *
			 * @var bool
			 */
			$add_js_to_response = apply_filters( 'cx_control/add_data_to_element', false );

			if ( $add_js_to_response ) {
				$html .= $this->get_js_template();
			}

			$html .= $this->get_additional_data();

			remove_all_filters( 'cx_control/is_repeater' );

			return $html;
		}

		/**
		 * Get additional data to return
		 * @return [type] [description]
		 */
		public function get_additional_data() {

			$data = apply_filters( 'cx_control/add_repeater_data', array() );

			if ( ! empty( $data ) ) {
				return implode( ' ', $data );
			}

		}

		/**
		 * Render single row for repeater
		 *
		 * @param string $index        Current row index.
		 * @param number $widget_index It contains widget index.
		 * @param array  $data         Values to paste.
		 * @since 1.0.1
		 */
		public function render_row( $index, $widget_index, $data ) {
			$this->data = $data;

			$item_classes = array( 'cx-ui-repeater-item' );

			if ( $this->settings['collapsed'] && ! $this->_is_js_row ) {
				$item_classes[] = 'cx-ui-repeater-min';
			}

			$html = '<div class="' . implode( ' ', $item_classes ) . '" data-item-index="' . $index .'">';
			$html .= '<div class="cx-ui-repeater-actions-box">';

			$html .= '<a href="#" class="cx-ui-repeater-copy"><svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><rect width="16" height="16" transform="matrix(1 0 0 -1 0 16)" fill="white"></rect><path d="M13.401 14.5362V4.35745H5.47721V14.5362H13.401ZM13.401 2.92766C13.7848 2.92766 14.1234 3.07518 14.4169 3.37021C14.7104 3.64255 14.8571 3.97163 14.8571 4.35745V14.5362C14.8571 14.922 14.7104 15.2624 14.4169 15.5574C14.1234 15.8525 13.7848 16 13.401 16H5.47721C5.09344 16 4.75482 15.8525 4.46134 15.5574C4.16787 15.2624 4.02113 14.922 4.02113 14.5362V4.35745C4.02113 3.97163 4.16787 3.64255 4.46134 3.37021C4.75482 3.07518 5.09344 2.92766 5.47721 2.92766H13.401ZM11.2338 0V1.46383H2.56504V11.6426H1.14282V1.46383C1.14282 1.07801 1.27827 0.737589 1.54917 0.442553C1.84265 0.147518 2.18127 0 2.56504 0H11.2338Z"></path></svg></a>';
			$html .= '<a href="#" class="cx-ui-repeater-remove">';
				$html .= '<svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><rect width="16" height="16" transform="matrix(1 0 0 -1 0 16)" fill="white"></rect><path d="M2.28564 14.192V3.42847H13.7142V14.192C13.7142 14.6685 13.5208 15.0889 13.1339 15.4533C12.747 15.8177 12.3005 15.9999 11.7946 15.9999H4.20529C3.69934 15.9999 3.25291 15.8177 2.866 15.4533C2.4791 15.0889 2.28564 14.6685 2.28564 14.192Z"></path><path d="M14.8571 1.14286V2.28571H1.14282V1.14286H4.57139L5.56085 0H10.4391L11.4285 1.14286H14.8571Z"></path></svg>';
				$html .= '<div class="cx-tooltip">Are you sure?<br><span class="cx-ui-repeater-remove__confirm">Yes</span> / <span class="cx-ui-repeater-remove__cancel">No</span></div>';
			$html .='</a>';
			$html .= '<span class="cx-ui-repeater-title">' . $this->get_row_title() . '</span>';
			$html .= '<a href="#" class="cx-ui-repeater-toggle"></a>';

			$html .= '</div>';
			$html .= '<div class="cheryr-ui-repeater-content-box">';
			foreach ( $this->settings['fields'] as $field ) {

				$field_classes = array(
					$field['id'] . '-wrap',
					'cx-ui-repeater-item-control'
				);

				if ( ! empty( $field['class'] ) ) {
					$field_classes[] = $field['class'];
				}

				$field_classes = implode( ' ', $field_classes );

				$html .= '<div class="' . $field_classes . '" data-repeater-control-name="' . $field['id'] . '">';
				$html .= $this->render_field( $index, $widget_index, $field );
				$html .= '</div>';
			}
			$html .= '</div>';
			$html .= '</div>';

			$this->data = array();

			return $html;
		}

		/**
		 * Get repeater item title
		 *
		 * @return string
		 * @since 1.0.1
		 */
		public function get_row_title() {

			if ( empty( $this->settings['title_field'] ) ) {
				return '';
			}

			if ( ! empty( $this->data[ $this->settings['title_field'] ] ) ) {
				return  $this->data[ $this->settings['title_field'] ];
			}

			return '';
		}

		/**
		 * Return default IB view content
		 *
		 * @param  [type] $view [description]
		 * @param  array  $args [description]
		 * @return [type]       [description]
		 */
		public function get_view( $view, $args = array() ) {

			if ( ! $this->base_path ) {
				return null;
			}

			$file = $this->base_path . 'views/' . $view . '.php';

			if ( ! file_exists( $file ) ) {
				return null;
			}

			ob_start();
			include $file;
			return ob_get_clean();

		}

		/**
		 * Render single repeater field
		 *
		 * @param  string $index        Current row index.
		 * @param  number $widget_index It contains widget index.
		 * @param  array  $field        Values to paste.
		 * @return string
		 */
		public function render_field( $index, $widget_index, $field ) {

			if ( empty( $field['type'] ) || empty( $field['name'] ) ) {
				return '"type" and "name" are required fields for UI_Repeater items';
			}

			$field = wp_parse_args( $field, array(
				'value' => '',
			) );

			$parent_name    = str_replace( '__i__', $widget_index, $this->settings['name'] );
			$parent_name    = str_replace( '{{{data.index}}}', '{{{data.parentIndex}}}', $parent_name );
			$field['id']    = sprintf( '%s-%s', $field['id'], $index );
			$field['value'] = isset( $this->data[ $field['name'] ] ) ? $this->data[ $field['name'] ] : $field['value'];
			$field['name']  = sprintf( '%1$s[item-%2$s][%3$s]', $parent_name, $index, $field['name'] );

			switch ( $field['type'] ) {

				case 'html':
					return sprintf( '<div class="cx-ui-container">%s</div>', $field['html'] );

				default:

					$ui_class_name  = 'CX_Control_' . ucwords( $field['type'] );

					if ( ! class_exists( $ui_class_name ) ) {
						return '<p>Class <b>' . $ui_class_name . '</b> not exist!</p>';
					}

					$ui_item = new $ui_class_name( $field );

					if ( 'repeater' === $ui_item->settings['type'] && true === $this->_is_js_row ) {
						$this->_childs[] = $ui_item;
					}

					return $ui_item->render();

			}

		}

		/**
		 * Get TMPL name for current repeater instance.
		 *
		 * @return string
		 */
		public function get_tmpl_name() {
			return $this->tmpl_name;
		}

		/**
		 * Set current repeater instance ID
		 *
		 * @return void
		 */
		public function set_tmpl_data() {
			self::$instance_id++;
			$this->tmpl_name = sprintf( 'repeater-template-%s', self::$instance_id );

			global $wp_customize;
			if ( isset( $wp_customize ) ) {
				self::$customizer_tmpl_to_print .= $this->get_js_template();
			}

		}

		/**
		 * Print JS template for current repeater instance
		 *
		 * @return void
		 */
		public function print_js_template() {

			echo $this->get_js_template();

			if ( ! empty( $this->_childs ) ) {
				foreach ( $this->_childs as $child ) {
					echo $child->get_js_template();
				}
			}

		}

		/**
		 * Get JS template to print
		 *
		 * @return string
		 */
		public function get_js_template() {

			$this->_is_js_row = true;

			return sprintf(
				'<script type="text/html" id="tmpl-%1$s">%2$s</script>',
				$this->get_tmpl_name(),
				$this->render_row( '{{{data.index}}}', '{{{data.widgetId}}}', array() )
			);

		}

		/**
		 * Outputs JS templates on customizer page
		 *
		 * @return void
		 */
		public function fix_customizer_tmpl() {
			if ( true === self::$customizer_tmpl_printed ) {
				return;
			}
			self::$customizer_tmpl_printed = true;
			echo self::$customizer_tmpl_to_print;
		}
	}
}
