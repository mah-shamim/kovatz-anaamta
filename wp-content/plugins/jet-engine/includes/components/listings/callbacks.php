<?php
/**
 * Listings callbacks manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Listings_Callbacks class
 */
class Jet_Engine_Listings_Callbacks {

	/**
	 * Holds all user-registered callbacks
	 * But only callbacks registered with new register_callback() method
	 * @var array
	 */
	private $_callbacks = array();

	public function __construct() {

		// Register core callbacks in a new way
		$this->register_callback( 'jet_engine_get_user_data_by_id', __( 'Get user data by ID', 'jet-engine' ), [
			'user_data_to_get' => [
				'label'     => __( 'User Data to get', 'jet-engine' ),
				'type'      => 'select',
				'default'   => 'display_name',
				'options'   => jet_engine()->listings->data->get_user_object_fields(),
			]
		] );

		do_action( 'jet-engine/callbacks/register', $this );

		add_filter( 'jet-engine/listings/allowed-callbacks', [ $this, 'register_callbacks' ] );
		add_filter( 'jet-engine/listings/allowed-callbacks-args', [ $this, 'register_callbacks_args' ] );
		add_filter( 'jet-engine/listing/dynamic-field/callback-args', [ $this, 'apply_callbacks_args' ], 10, 4 );

	}

	/**
	 * Register custom callback
	 * 
	 * @param  [type] $callback [description]
	 * @param  [type] $label    [description]
	 * @param  array  $args     [description]
	 * @return [type]           [description]
	 */
	public function register_callback( $callback, $label, $args = [] ) {

		$this->_callbacks[] = array(
			'callback' => $callback,
			'label'    => $label,
			'args'     => $args,
		);

	}

	/**
	 * Register callbacks for options
	 * 
	 * @return [type] [description]
	 */
	public function register_callbacks( $callbacks ) {

		foreach ( $this->_callbacks as $callback_data ) {
			$callbacks[ $callback_data['callback'] ] = $callback_data['label'];
		}

		return $callbacks;

	}

	/**
	 * Register callbacks args for options
	 * 
	 * @return [type] [description]
	 */
	public function register_callbacks_args( $args ) {

		foreach ( $this->_callbacks as $callback_data ) {
			
			$callback_args = $callback_data['args'];

			if ( ! empty( $callback_args ) ) {

				foreach ( $callback_args as $key => $data ) {

					$condition = array(
						'dynamic_field_filter' => 'yes',
						'filter_callback'      => array( $callback_data['callback'] ),
					);

					if ( ! empty( $data['condition'] ) ) {
						$data['condition'] = array_merge( $condition, $data['condition'] );
					} else {
						$data['condition'] = $condition;
					}

					$args[ $key ] = $data;
				}

			}
		}

		return $args;

	}

	/**
	 * Apply callbacks args for given callback
	 * 
	 * @return [type] [description]
	 */
	public function apply_callbacks_args( $args, $callback, $settings = array(), $widget = null ) {

		foreach ( $this->_callbacks as $callback_data ) {
			
			if ( $callback_data['callback'] === $callback ) {

				$callback_args = $callback_data['args'];

				if ( ! empty( $callback_args ) ) {
					foreach ( $callback_args as $key => $data ) {
						$default = ! empty( $data['default'] ) ? $data['default'] : '';
						$args[]  = ! empty( $settings[ $key ] ) ? $settings[ $key ] : $default;
					}
				}

			}

		}

		return $args;

	}

	/**
	 * Retruns registered callbacks list to use in options pages
	 * 
	 * @return array
	 */
	public function get_cllbacks_for_options() {
		return apply_filters( 'jet-engine/listings/allowed-callbacks', array(
			'jet_engine_date'                       => __( 'Format date', 'jet-engine' ),
			'date'                                  => __( 'Format date. Legacy', 'jet-engine' ),
			'date_i18n'                             => __( 'Format date, localized. Legacy', 'jet-engine' ),
			'number_format'                         => __( 'Format number', 'jet-engine' ),
			'jet_engine_url_scheme'                 => __( 'Add URL scheme', 'jet-engine' ),
			'get_the_title'                         => __( 'Get post/page title', 'jet-engine' ),
			'get_permalink'                         => __( 'Get post/page URL', 'jet-engine' ),
			'jet_get_pretty_post_link'              => __( 'Get post/page link', 'jet-engine' ),
			'jet_engine_post_thumbnail'             => __( 'Get post/page thumbnail', 'jet-engine' ),
			'jet_get_term_name'                     => __( 'Get term name', 'jet-engine' ),
			'get_term_link'                         => __( 'Get term URL', 'jet-engine' ),
			'jet_get_pretty_term_link'              => __( 'Get term link', 'jet-engine' ),
			'wp_oembed_get'                         => __( 'Embed URL', 'jet-engine' ),
			'make_clickable'                        => __( 'Make clickable', 'jet-engine' ),
			'jet_engine_icon_html'                  => __( 'Embed icon from Iconpicker', 'jet-engine' ),
			'jet_engine_render_multiselect'         => __( 'Multiple select field values', 'jet-engine' ),
			'jet_engine_render_checkbox_values'     => __( 'Checkbox field values', 'jet-engine' ),
			'jet_engine_render_checklist'           => __( 'Checked values list', 'jet-engine' ),
			'jet_engine_render_switcher'            => __( 'Switcher field values', 'jet-engine' ),
			'jet_engine_render_acf_checkbox_values' => __( 'ACF Checkbox field values', 'jet-engine' ),
			'jet_engine_render_post_titles'         => __( 'Get post titles from IDs', 'jet-engine' ),
			'jet_related_posts_list'                => __( 'Related posts list', 'jet-engine' ),
			'jet_related_items_list'                => __( 'Related items list', 'jet-engine' ),
			'jet_engine_render_field_values_count'  => __( 'Field values count (for arrays returns array items count)', 'jet-engine' ),
			'wp_get_attachment_image'               => __( 'Get image by ID', 'jet-engine' ),
			'do_shortcode'                          => __( 'Do shortcodes', 'jet-engine' ),
			'human_time_diff'                       => __( 'Human readable time difference', 'jet-engine' ),
			'wpautop'                               => __( 'Add paragraph tags (wpautop)', 'jet-engine' ),
			'zeroise'                               => __( 'Zeroise (add leading zeros)', 'jet-engine' ),
			'jet_engine_get_child'                  => __( 'Get child element from object/array', 'jet-engine' ),
			'jet_engine_label_by_glossary'          => __( 'Get labels by glossary data', 'jet-engine' ),
			'jet_engine_proportional'               => __( 'Proportional', 'jet-engine' ),
		) );
	}

	/**
	 * Returns allowed callback arguments list
	 *
	 * @return [type] [description]
	 */
	public function get_callbacks_args( $for = 'elementor' ) {

		$glossaries = array(
			'' => __( 'Select glossary...', 'jet-engine' ),
		);

		foreach ( jet_engine()->glossaries->settings->get() as $glossary ) {
			$glossaries[ $glossary['id']] = $glossary['name'];
		}

		$args = apply_filters( 'jet-engine/listings/allowed-callbacks-args', array(
			'labels_by_glossary' => array(
				'label'     => __( 'Get labels by glossary', 'jet-engine' ),
				'type'      => 'select',
				'default'   => '',
				'options'   => $glossaries,
				'condition' => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_label_by_glossary', 'jet_engine_render_checklist' ),
				),
			),
			'date_format' => array(
				'label'       => esc_html__( 'Format', 'jet-engine' ),
				'type'        => 'text',
				'default'     => 'F j, Y',
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'date', 'date_i18n', 'jet_engine_date' ),
				),
				'has_html'    => true,
				'description' => sprintf( '<a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank">%s</a>', __( 'Documentation on date and time formatting', 'jet-engine' ) ),
			),
			'num_dec_point' => array(
				'label'       => esc_html__( 'Decimal point', 'jet-engine' ),
				'type'        => 'text',
				'default'     => '.',
				'description' => __( 'Sets the separator for the decimal point', 'jet-engine' ),
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'number_format' ),
				),
			),
			'num_thousands_sep' => array(
				'label'       => esc_html__( 'Thousands separator', 'jet-engine' ),
				'type'        => 'text',
				'default'     => ',',
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'number_format' ),
				),
			),
			'human_time_diff_from_key' => array(
				'label'       => esc_html__( 'Additional meta key', 'jet-engine' ),
				'description' => esc_html__( 'Pass additional date meta key for calculating time diff. If not set, difference will be calculated between current time and input time. If set - between time from this meta field and input time.', 'jet-engine' ),
				'type'        => 'text',
				'default'     => '',
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'human_time_diff' ),
				),
			),
			'num_decimals' => array(
				'label'       => esc_html__( 'Decimal points', 'jet-engine' ),
				'type'        => 'number',
				'min'         => 0,
				'max'         => 10,
				'step'        => 1,
				'default'     => 2,
				'description' => __( 'Sets the number of visible decimal points', 'jet-engine' ),
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'number_format' ),
				),
			),
			'zeroise_threshold' => array(
				'label'       => esc_html__( 'Threshold', 'jet-engine' ),
				'type'        => 'number',
				'min'         => 0,
				'max'         => 10,
				'step'        => 1,
				'default'     => 3,
				'description' => __( 'Digit place numbers need not to have zeros added', 'jet-engine' ),
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'zeroise' ),
				),
			),
			'proportion_divisor' => array(
				'label'       => esc_html__( 'Divisor', 'jet-engine' ),
				'type'        => 'number',
				'min'         => 1,
				'step'        => 1,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => 10,
				'description' => __( 'Divisor in (value/divisor)*multiplier statement', 'jet-engine' ),
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_proportional' ),
				),
			),
			'proportion_multiplier' => array(
				'label'       => esc_html__( 'Multiplier', 'jet-engine' ),
				'type'        => 'number',
				'min'         => 1,
				'step'        => 1,
				'default'     => 5,
				'dynamic'     => array(
					'active' => true,
				),
				'description' => __( 'Multiplier in (value/divisor)*multiplier statement', 'jet-engine' ),
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_proportional' ),
				),
			),
			'proportion_precision' => array(
				'label'       => esc_html__( 'Result precision', 'jet-engine' ),
				'type'        => 'number',
				'min'         => 0,
				'step'        => 5,
				'default'     => 0,
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_proportional' ),
				),
			),
			'child_path' => array(
				'label'       => __( 'Child item name', 'jet-engine' ),
				'type'        => 'text',
				'label_block' => true,
				'default'     => '',
				'description' => __( 'Name of the child item to get. Or path to the nested child item. Separate nesting levels with "/". For example - level-1-name/level-2-name/child-item-name', 'jet-engine' ),
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_get_child' ),
				),
			),
			'attachment_image_size' => array(
				'label'   => __( 'Image size', 'jet-engine' ),
				'type'    => 'select',
				'default' => 'full',
				'options' => Jet_Engine_Tools::get_image_sizes(),
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'wp_get_attachment_image', 'jet_engine_post_thumbnail' ),
				),
			),
			'thumbnail_add_permalink' => array(
				'label'        => esc_html__( 'Add permalink', 'jet-engine' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_post_thumbnail' ),
				),
			),
			'related_list_is_single' => array(
				'label'        => esc_html__( 'Single value', 'jet-engine' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_related_posts_list', 'jet_related_items_list' ),
				),
			),
			'related_list_is_linked' => array(
				'label'        => esc_html__( 'Add links to related posts', 'jet-engine' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_related_posts_list', 'jet_related_items_list' ),
				),
			),
			'related_list_tag' => array(
				'label'   => __( 'Related list HTML tag', 'jet-engine' ),
				'type'    => 'select',
				'default' => 'ul',
				'options' => array(
					'ul'   => 'UL',
					'ol'   => 'OL',
					'div'  => 'DIV',
				),
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_related_posts_list', 'jet_related_items_list' ),
				),
			),
			'multiselect_delimiter' => array(
				'label'       => esc_html__( 'Delimiter', 'jet-engine' ),
				'type'        => 'text',
				'default'     => ', ',
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_render_multiselect', 'jet_related_posts_list', 'jet_related_items_list', 'jet_engine_render_post_titles', 'jet_engine_render_checkbox_values', 'jet_engine_label_by_glossary' ),
				),
			),
			'switcher_true' => array(
				'label'       => esc_html__( 'Text if enabled', 'jet-engine' ),
				'type'        => 'textarea',
				'default'     => '',
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_render_switcher' ),
				),
			),
			'switcher_false' => array(
				'label'       => esc_html__( 'Text if disabled', 'jet-engine' ),
				'type'        => 'textarea',
				'default'     => '',
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_render_switcher' ),
				),
			),
			'url_scheme' => array(
				'label'   => __( 'Select URL scheme', 'jet-engine' ),
				'type'    => 'select',
				'default' => '',
				'options' => \Jet_Engine_URL_Shemes_Manager::instance()->get_allowed_url_schemes(),
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_url_scheme' ),
				),
			),
			'checklist_cols_num' => array(
				'label'       => __( 'Columns number', 'jet-engine' ),
				'type'        => 'number',
				'default'     => 1,
				'min'         => 1,
				'max'         => 6,
				'step'        => 1,
				'condition'   => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_render_checklist' ),
				),
			),
			'checklist_divider' => array(
				'label'        => esc_html__( 'Add divider between items', 'jet-engine' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_render_checklist' ),
				),
			),
			'checklist_divider_color' => array(
				'label' => __( 'Divider color', 'jet-engine' ),
				'type' => 'color',
				'condition'    => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_render_checklist' ),
					'checklist_divider'    => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-listing-dynamic-field .jet-check-list__item' => 'border-color: {{VALUE}}',
				),
			),
		) );

		if ( 'blocks' === $for ) {
			foreach ( $args as $key => $data ) {
				if ( ! empty( $data['options'] ) ) {
					$data['options'] = \Jet_Engine_Tools::prepare_list_for_js( $data['options'], ARRAY_A );
					$args[ $key ]    = $data;
				}
			}
		}

		return $args;
	}

	/**
	 * Check if current callback is registered and allowed to execute
	 * 
	 * @param  [type]  $callback [description]
	 * @return boolean           [description]
	 */
	public function is_allowed_callback( $callback ) {

		$callbacks = $this->get_cllbacks_for_options();
		$callbacks = array_keys( $callbacks );

		return in_array( $callback, $callbacks );
	}

	/**
	 * Apply selected callback for given data
	 * 
	 * @param  [type] $input    [description]
	 * @param  [type] $callback [description]
	 * @param  array  $settings [description]
	 * @param  [type] $widget   [description]
	 * @return [type]           [description]
	 */
	public function apply_callback( $input = null, $callback = null, $settings = array(), $widget = null ) {

		if ( ! $callback ) {
			return;
		}

		if ( ! is_callable( $callback ) || ! $this->is_allowed_callback( $callback) ) {
			return;
		}

		$args   = array();
		$result = $input;

		switch ( $callback ) {

			case 'date':
			case 'date_i18n':
			case 'jet_engine_date':

				// Added to prevent print `January 1, 1970` if date field is empty.
				if ( empty( $result ) ) {
					return '';
				}

				if ( ! Jet_Engine_Tools::is_valid_timestamp( $result ) ) {
					$result = strtotime( $result );
				}

				$format = ! empty( $settings['date_format'] ) ? $settings['date_format'] : 'F j, Y';
				$args   = array( $format, $result );

				break;

			case 'number_format':

				$result        = floatval( $result );
				$dec_point     = isset( $settings['num_dec_point'] ) ? $settings['num_dec_point'] : '.';
				$thousands_sep = isset( $settings['num_thousands_sep'] ) ? $settings['num_thousands_sep'] : ',';
				$decimals      = isset( $settings['num_decimals'] ) ? absint( $settings['num_decimals'] ) : 2;
				$args          = array( $result, $decimals, $dec_point, $thousands_sep );

				break;

			case 'wp_get_attachment_image':

				$size = isset( $settings['attachment_image_size'] ) ? $settings['attachment_image_size'] : 'full';
				$args = array( $result, $size );

				break;

			case 'jet_engine_label_by_glossary':
				$glossary  = isset( $settings['labels_by_glossary'] ) ? $settings['labels_by_glossary'] : false;
				$delimiter = isset( $settings['multiselect_delimiter'] ) ? $settings['multiselect_delimiter'] : ', ';
				$args      = array( $result, $glossary, $delimiter );
				break;

			case 'jet_engine_render_multiselect':
			case 'jet_engine_render_post_titles':
			case 'jet_engine_render_checkbox_values':

				$delimiter = isset( $settings['multiselect_delimiter'] ) ? $settings['multiselect_delimiter'] : ', ';
				$args      = array( $result, $delimiter );

				break;

			case 'jet_related_posts_list':
			case 'jet_related_items_list':

				$tag       = isset( $settings['related_list_tag'] ) ? $settings['related_list_tag'] : 'ul';
				$tag       = Jet_Engine_Tools::sanitize_html_tag( $tag );
				$is_linked = isset( $settings['related_list_is_linked'] ) ? $settings['related_list_is_linked'] : '';
				$is_single = isset( $settings['related_list_is_single'] ) ? $settings['related_list_is_single'] : '';
				$delimiter = isset( $settings['multiselect_delimiter'] ) ? wp_kses_post( $settings['multiselect_delimiter'] ) : ', ';
				$is_linked = filter_var( $is_linked, FILTER_VALIDATE_BOOLEAN );
				$is_single = filter_var( $is_single, FILTER_VALIDATE_BOOLEAN );
				$args      = array( $result, $tag, $is_single, $is_linked, $delimiter );

				if ( 'jet_related_items_list' === $callback ) {

					if ( ! empty( $settings['dynamic_field_post_object'] ) ) {
						$args[] = $settings['dynamic_field_post_object'];
					} elseif ( ! empty( $settings['object_field'] ) ) {
						$args[] = $settings['object_field'];
					} elseif ( ! empty( $settings['related_items_prop'] ) ) {
						$args[] = $settings['related_items_prop'];
					}

				}

				break;

			case 'jet_engine_render_switcher':

				$true_text  = isset( $settings['switcher_true'] ) ? $settings['switcher_true'] : '';
				$false_text = isset( $settings['switcher_false'] ) ? $settings['switcher_false'] : '';
				$args       = array( $result, $true_text, $false_text );

				break;

			case 'jet_engine_post_thumbnail':

				$image_size    = isset( $settings['attachment_image_size'] ) ? $settings['attachment_image_size'] : 'full';
				$add_permalink = isset( $settings['thumbnail_add_permalink'] ) ? $settings['thumbnail_add_permalink'] : false;
				$add_permalink = filter_var( $add_permalink, FILTER_VALIDATE_BOOLEAN );
				$args          = array( $result, $image_size, $add_permalink );

				break;

			case 'jet_engine_render_checklist':

				$cols = isset( $settings['checklist_cols_num'] ) ? $settings['checklist_cols_num'] : 1;

				$field_icon = ! empty( $settings['field_icon'] ) ? esc_attr( $settings['field_icon'] ) : false;
				$new_icon   = ! empty( $settings['selected_field_icon'] ) ? $settings['selected_field_icon'] : false;

				if ( is_callable( array( $widget, 'get_name' ) ) ) {
					$base_class = $widget->get_name();
				} else {
					$base_class = 'jet-dynamic-field';
				}

				$new_icon_html = Jet_Engine_Tools::render_icon( $new_icon, $base_class . '__icon' );
				$icon          = false;

				if ( $new_icon_html ) {
					$icon = $new_icon_html;
				} elseif ( $field_icon ) {
					$icon = sprintf( '<i class="%1$s %2$s__icon"></i>', $field_icon, $base_class );
				}

				if ( $icon && $widget ) {
					$widget->prevent_icon = true;
				}

				$divider     = ! empty( $settings['checklist_divider'] ) ? filter_var( $settings['checklist_divider'], FILTER_VALIDATE_BOOLEAN ) : false;
				$glossary_id = ! empty( $settings['labels_by_glossary'] ) ? $settings['labels_by_glossary'] : false;

				$args = array( $result, $icon, $cols, $divider, $glossary_id );

				break;

			case 'human_time_diff':

				$from = ! empty( $settings['human_time_diff_from_key'] ) ? jet_engine()->listings->data->get_meta( $settings['human_time_diff_from_key'] ) : 0;
				$from = absint( $from );

				if ( ! Jet_Engine_Tools::is_valid_timestamp( $result ) ) {
					$result = strtotime( $result );
				}

				if ( ! $from ) {
					$args = array( $result );
					$from = current_time( 'U' );
				}

				if ( $from < $result ) {
					$args = array( $from, $result );
				} else {
					$args = array( $result, $from );
				}

				break;

			case 'zeroise':
				$threshold = isset( $settings['zeroise_threshold'] ) ? $settings['zeroise_threshold'] : 3;
				$args      = array( $result, $threshold );
				break;

			case 'jet_engine_get_child':
				$path = isset( $settings['child_path'] ) ? $settings['child_path'] : 3;
				$args = array( $result, $path );
				break;

			case 'jet_engine_url_scheme':
				$url_scheme = isset( $settings['url_scheme'] ) ? $settings['url_scheme'] : null;
				$args       = array( $result, $url_scheme );
				break;

			case 'jet_engine_proportional':
				$divisor    = isset( $settings['proportion_divisor'] ) ? $settings['proportion_divisor'] : 1;
				$multiplier = isset( $settings['proportion_multiplier'] ) ? $settings['proportion_multiplier'] : 1;
				$precision  = isset( $settings['proportion_precision'] ) ? $settings['proportion_precision'] : 0;
				$args       = array( $result, $divisor, $multiplier, $precision );
				break;

			default:

				$args = apply_filters(
					'jet-engine/listing/dynamic-field/callback-args',
					array( $result ),
					$callback,
					$settings,
					$widget
				);

				break;
		}

		return call_user_func_array( $callback, $args );
	}

}