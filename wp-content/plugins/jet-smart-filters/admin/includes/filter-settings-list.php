<?php
/**
 * List of options for a single filter
 */

return array(
	'labels' => array(
		'_filter_label' => array(
			'type'  => 'text',
			'title' => __( 'Filter Label', 'jet-smart-filters' ),
		),
		'_active_label' => array(
			'type'  => 'text',
			'title' => __( 'Active Filter Label', 'jet-smart-filters' ),
		),
	),

	'settings' => array(
		// Filter Type
		'_filter_type' => array(
			'type'     => 'type_selector',
			'title'    => __( 'Filter Type', 'jet-smart-filters' ),
			'options'  => $this->types(),
			'deselect' => true
		),

		// Is Hierarchical
		'_is_hierarchical' => array(
			'type'       => 'switcher',
			'title'      => __( 'Is hierarchical', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type' => 'select',
			),
		),

		'_ih_source_map' => array(
			'type'        => 'repeater',
			'title'       => __( 'Filter hierarchy', 'jet-smart-filters' ),
			'add_label'   => __( 'New Level', 'jet-smart-filters' ),
			'fields'      => array(
				'label' => array(
					'type'  => 'text',
					'title' => __( 'Label', 'jet-smart-filters' ),
				),
				'placeholder' => array(
					'type'        => 'text',
					'placeholder' => __( 'Select...', 'jet-smart-filters' ),
					'title'       => __( 'Placeholder', 'jet-smart-filters' ),
				),
				'tax' => array(
					'type'        => 'select',
					'title'       => __( 'Taxonomy', 'jet-smart-filters' ),
					'placeholder' => __( 'Taxonomy...', 'jet-smart-filters' ),
					'options'     => $this->taxonomy_options,
				),
			),
			'conditions' => array(
				'_filter_type'     => 'select',
				'_is_hierarchical' => true
			),
		),

		// Data Source
		'_data_source' => array(
			'type'        => 'select',
			'title'       => __( 'Data Source', 'jet-smart-filters' ),
			'placeholder' => __( 'Select data source...', 'jet-smart-filters' ),
			'options'     => $this->sources(),
			'conditions'  => array(
				'_filter_type'      => array( 'checkboxes', 'select', 'radio', 'color-image' ),
				'_is_hierarchical!' => true
			),
		),

		// Manual input
		'_source_manual_input' => array(
			'type'        => 'repeater',
			'title'       => __( 'Options List', 'jet-smart-filters' ),
			'add_label'   => __( 'New Option', 'jet-smart-filters' ),
			'fields'      => array(
				'label' => array(
					'type'  => 'text',
					'title' => __( 'Label', 'jet-smart-filters' ),
				),
				'value' => array(
					'type'  => 'text',
					'title' => __( 'Value', 'jet-smart-filters' ),
				),
			),
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio' ),
				'_data_source' => 'manual_input',
			),
		),

		// Taxonomies
		'_source_taxonomy' => array(
			'type'       => 'select',
			'title'      => __( 'Taxonomy', 'jet-smart-filters' ),
			'value'      => array_key_first( $this->taxonomy_options ),
			'options'    => $this->taxonomy_options,
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
				'_data_source' => 'taxonomies',
			),
		),
		'_terms_relational_operator' => array(
			'type'    => 'select',
			'title'   => __( 'Relational Operator', 'jet-smart-filters' ),
			'value'   => 'OR',
			'options' => array(
				'OR'  => __( 'Union', 'jet-smart-filters' ),
				'AND' => __( 'Intersection', 'jet-smart-filters' ),
			),
			'conditions' => array(
				'_filter_type' => 'checkboxes',
				'_data_source' => 'taxonomies',
			),
		),

		// Select filter
		'_placeholder' => array(
			'type'        => 'text',
			'title'       => __( 'Placeholder', 'jet-smart-filters' ),
			'placeholder' => __( 'Select...', 'jet-smart-filters' ),
			'conditions'  => array(
				'_filter_type'      => 'select',
				'_data_source!'     => '',
				'_is_hierarchical!' => true
			),
		),

		// Check Range
		'_source_manual_input_range' => array(
			'type'      => 'repeater',
			'title'     => __( 'Options List', 'jet-smart-filters' ),
			'add_label' => __( 'New Option', 'jet-smart-filters' ),
			'fields'    => array(
				'min' => array(
					'type'        => 'text',
					'title'       => __( 'Min Value', 'jet-smart-filters' ),
					'placeholder' => '0'
				),
				'max' => array(
					'type'        => 'text',
					'title'       => __( 'Max Value', 'jet-smart-filters' ),
					'placeholder' => '100'
				),
			),
			'conditions' => array(
				'_filter_type' => 'check-range',
			),
		),

		// Range
		'_source_callback' => array(
			'type'    => 'select',
			'title'   => __( 'Get min/max dynamically', 'jet-smart-filters' ),
			'value'   => 'none',
			'options' => apply_filters( 'jet-smart-filters/range/source-callbacks', array(
				'none'                               => __( 'None', 'jet-smart-filters' ),
				'jet_smart_filters_woo_prices'       => __( 'WooCommerce min/max prices', 'jet-smart-filters' ),
				'jet_smart_filters_meta_values'      => __( 'Get from Post Meta by query meta key', 'jet-smart-filters' ),
				'jet_smart_filters_term_meta_values' => __( 'Get from Term Meta by query meta key', 'jet-smart-filters' ),
				'jet_smart_filters_user_meta_values' => __( 'Get from User Meta by query meta key', 'jet-smart-filters' ),
			) ),
			'conditions' => array(
				'_filter_type' => 'range',
			),
		),
		'_source_min' => array(
			'type'        => 'number',
			'title'       => __( 'Min Value', 'jet-smart-filters' ),
			'placeholder' => '0',
			'conditions'  => array(
				'_filter_type'     => 'range',
				'_source_callback' => 'none'
			),
		),
		'_source_max' => array(
			'type'        => 'number',
			'title'       => __( 'Max Value', 'jet-smart-filters' ),
			'placeholder' => '100',
			'conditions'  => array(
				'_filter_type'     => 'range',
				'_source_callback' => 'none'
			),
		),
		'_source_step' => array(
			'type'        => 'number',
			'title'       => __( 'Step', 'jet-smart-filters' ),
			'description' => __( '1, 10, 100, 0.1 etc', 'jet-smart-filters' ),
			'placeholder' => '1',
			'conditions'  => array(
				'_filter_type' => 'range',
			),
		),
		'_values_prefix' => array(
			'type'       => 'text',
			'title'      => __( 'Values prefix', 'jet-smart-filters' ),
			'value'      => '',
			'conditions' => array(
				'_filter_type' => array( 'range', 'check-range' ),
			),
		),
		'_values_suffix' => array(
			'type'       => 'text',
			'title'      => __( 'Values suffix', 'jet-smart-filters' ),
			'value'      => '',
			'conditions' => array(
				'_filter_type' => array( 'range', 'check-range' ),
			),
		),
		'_range_inputs_enabled' => array(
			'type'       => 'switcher',
			'title'      => __( 'Inputs enabled', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type' => 'range',
			),
		),
		'_range_inputs_separators_enabled' => array(
			'type'        => 'switcher',
			'title'       => __( 'Inputs separators enabled', 'jet-smart-filters' ),
			'description' => __( 'Apply thousands and decimal separators to inputs', 'jet-smart-filters' ),
			'conditions'  => array(
				'_filter_type'          => 'range',
				'_range_inputs_enabled' => true
			),
		),
		'_values_decimal_num' => array(
			'type'        => 'number',
			'title'       => __( 'Number of decimals', 'jet-smart-filters' ),
			'min'         => 0,
			'max'         => 10,
			'placeholder' => '0',
			'conditions'  => array(
				'_filter_type' => array( 'range', 'check-range' ),
			),
		),
		'_values_decimal_sep' => array(
			'type'        => 'text',
			'title'       => __( 'Decimal separator', 'jet-smart-filters' ),
			'placeholder' => '.',
			'conditions'  => array(
				'_filter_type' => array( 'range', 'check-range' ),
			),
		),
		'_values_thousand_sep' => array(
			'type'       => 'text',
			'title'      => __( 'Thousands separator', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type' => array( 'range', 'check-range' ),
			),
		),

		// Date
		'_date_source' => array(
			'type'        => 'select',
			'title'       => __( 'Filter by', 'jet-smart-filters' ),
			'placeholder' => __( 'Select date source...', 'jet-smart-filters' ),
			'options'     => array(
				'meta_query' => __( 'Meta Date', 'jet-smart-filters' ),
				'date_query' => __( 'Post Date', 'jet-smart-filters' ),
			),
			'conditions'  => array(
				'_filter_type' => array( 'date-range', 'date-period' ),
			),
		),
		'_date_format' => array(
			'type'        => 'text',
			'title'       => __( 'Date Format', 'jet-smart-filters' ),
			//'description' => '<a href="https://api.jqueryui.com/datepicker/#utility-formatDate" target="_blank">' . __( 'Datepicker date formats', 'jet-smart-filters' ) . '</a>',
			'value'       => 'mm/dd/yy',
			'placeholder' => 'mm/dd/yy',
			'conditions'  => array(
				'_filter_type'  => 'date-range',
				'_date_source!' => '',
			),
		),
		'_date_from_placeholder' => array(
			'type'       => 'text',
			'title'      => __( 'From Placeholder', 'jet-smart-filters' ),
			'value'      => '',
			'conditions' => array(
				'_filter_type'  => 'date-range',
				'_date_source!' => '',
			),
		),
		'_date_to_placeholder' => array(
			'type'       => 'text',
			'title'      => __( 'To Placeholder', 'jet-smart-filters' ),
			'value'      => '',
			'conditions' => array(
				'_filter_type'  => 'date-range',
				'_date_source!' => '',
			),
		),

		// Date period
		'_date_period_type' => array(
			'type'        => 'select',
			'title'       => __( 'Period Type', 'jet-smart-filters' ),
			'placeholder' => __( 'Select period type...', 'jet-smart-filters' ),
			'options'     => array(
				'range' => __( 'Custom range', 'jet-smart-filters' ),
				'day'   => __( 'Day', 'jet-smart-filters' ),
				'week'  => __( 'Week', 'jet-smart-filters' ),
				'month' => __( 'Month', 'jet-smart-filters' ),
				'year'  => __( 'Year', 'jet-smart-filters' ),
			),
			'conditions'  => array(
				'_filter_type'  => 'date-period',
				'_date_source!' => '',
			),
		),
		'_date_period_datepicker_button_text' => array(
			'type'       => 'text',
			'title'      => __( 'Datepicker Button Text', 'jet-smart-filters' ),
			'value'      => __( 'Select Date', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type'       => 'date-period',
				'_date_source!'      => '',
				'_date_period_type!' => ''
			),
		),
		'_min_max_date_period_enabled' => array(
			'type'       => 'switcher',
			'title'      => __( 'Min/Max Dates Enabled', 'jet-smart-filters' ),
			'value'      => false,
			'conditions' => array(
				'_filter_type'       => 'date-period',
				'_date_source!'      => '',
				'_date_period_type!' => ''
			),
		),
		'_min_date_period' => array(
			'type'       => 'text',
			'title'      => __( 'Minimum possible date to select', 'jet-smart-filters' ),
			'conditions' => array(
				'_min_max_date_period_enabled' => true
			),
		),
		'_max_date_period' => array(
			'type'       => 'text',
			'title'      => __( 'Maximum possible date to select', 'jet-smart-filters' ),
			'conditions' => array(
				'_min_max_date_period_enabled' => true
			),
		),
		'_min_max_date_period_info' => array(
			'type'       => 'html',
			'fullwidth'  => true,
			'html'       => $this->min_max_date_period_info,
			'conditions' => array(
				'_min_max_date_period_enabled' => true
			),
		),
		'_date_period_start_end_enabled' => array(
			'type'       => 'switcher',
			'title'      => __( 'Start/End Date Period Enabled', 'jet-smart-filters' ),
			'value'      => true,
			'conditions' => array(
				'_filter_type'       => 'date-period',
				'_date_source!'      => '',
				'_date_period_type!' => ''
			),
		),
		'_date_period_format' => array(
			'type'        => 'text',
			'title'       => __( 'Date Period Format', 'jet-smart-filters' ),
			'placeholder' => 'mm/dd/yy',
			'conditions'  => array(
				'_filter_type'                   => 'date-period',
				'_date_source!'                  => '',
				'_date_period_type!'             => '',
				'_date_period_start_end_enabled' => false
			),
		),
		'_date_period_start_format' => array(
			'type'        => 'text',
			'title'       => __( 'Start Format', 'jet-smart-filters' ),
			'description' => __( 'If Period Type is Day, only this value will be taken', 'jet-smart-filters' ),
			'placeholder' => 'mm/dd/yy',
			'conditions'  => array(
				'_filter_type'                   => 'date-period',
				'_date_source!'                  => '',
				'_date_period_type!'             => '',
				'_date_period_start_end_enabled' => true
			),
		),
		'_date_period_separator' => array(
			'type'        => 'text',
			'title'       => __( 'Separator', 'jet-smart-filters' ),
			'placeholder' => '-',
			'conditions'  => array(
				'_filter_type'                   => 'date-period',
				'_date_source!'                  => '',
				'_date_period_type!'             => '',
				'_date_period_start_end_enabled' => true
			),
		),
		'_date_period_end_format' => array(
			'type'        => 'text',
			'title'       => __( 'End Format', 'jet-smart-filters' ),
			'placeholder' => 'mm/dd/yy',
			'conditions'  => array(
				'_filter_type'                   => 'date-period',
				'_date_source!'                  => '',
				'_date_period_type!'             => '',
				'_date_period_start_end_enabled' => true
			),
		),
		'_date_period_date_formats_info' => array(
			'type'       => 'html',
			'title'      => __( 'Date Formats', 'jet-smart-filters' ),
			'fullwidth'  => true,
			'html'       => $this->date_formats_info,
			'conditions' => array(
				'_filter_type'       => array( 'date-range', 'date-period' ),
				'_date_source!'      => '',
				'_date_period_type!' => ''
			),
		),

		// Posts
		'_source_post_type' => array(
			'type'        => 'select',
			'title'       => __( 'Post Type', 'jet-smart-filters' ),
			'value'       => array_key_first( $this->post_types_options ),
			'placeholder' => __( 'Select post type...', 'jet-smart-filters' ),
			'options'     => $this->post_types_options,
			'conditions'  => array(
				'_filter_type'     => array( 'checkboxes', 'select', 'radio', 'color-image' ),
				'_data_source'     => 'posts',
			),
		),

		// Radio
		'_add_all_option' => array(
			'type'       => 'switcher',
			'title'      => __( 'Add all option', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type'  => 'radio',
				'_data_source!' => '',
			),
		),
		'_all_option_label' => array(
			'type'       => 'text',
			'title'      => __( 'All option label', 'jet-smart-filters' ),
			'value'      => __( 'All', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type'    => 'radio',
				'_add_all_option' => true
			),
		),
		'_ability_deselect_radio' => array(
			'type'       => 'switcher',
			'title'      => __( 'Ability to deselect radio buttons', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type'  => 'radio',
				'_data_source!' => '',
			),
		),

		// Rating
		'_rating_options' => array(
			'type'       => 'number',
			'title'      => __( 'Stars count', 'jet-smart-filters' ),
			'value'      => 5,
			'min'        => 1,
			'max'        => 10,
			'conditions' => array(
				'_filter_type' => 'rating'
			),
		),
		'_rating_compare_operand' => array(
			'type'        => 'select',
			'title'       => __( 'Inequality operator', 'jet-smart-filters' ),
			'placeholder' => __( 'Select operator...', 'jet-smart-filters' ),
			'description' => __( 'Set relation between values', 'jet-smart-filters' ),
			'options'     => array(
				'greater' => __( 'Greater than or equals (>=)', 'jet-smart-filters' ),
				'less'    => __( 'Less than or equals (<=)', 'jet-smart-filters' ),
				'equal'   => __( 'Equals (=)', 'jet-smart-filters' ),
			),
			'conditions'  => array(
				'_filter_type' => 'rating'
			),
		),

		// Alphabet
		'_alphabet_behavior' => array(
			'type'       => 'select',
			'title'      => __( 'Behavior', 'jet-smart-filters' ),
			'value'      => 'radio',
			'options'    => array(
				'radio'    => __( 'Radio', 'jet-smart-filters' ),
				'checkbox' => __( 'Checkbox', 'jet-smart-filters' ),
			),
			'conditions' => array(
				'_filter_type' => 'alphabet'
			),
		),
		'_alphabet_radio_deselect' => array(
			'type'       => 'switcher',
			'title'      => __( 'Ability to deselect radio buttons', 'jet-smart-filters' ),
			'value'      => true,
			'conditions' => array(
				'_filter_type'       => 'alphabet',
				'_alphabet_behavior' => 'radio'
			),
		),
		'_alphabet_options' => array(
			'type'        => 'textarea',
			'title'       => __( 'Options', 'jet-smart-filters' ),
			'description' => __( 'Use comma to separate options', 'jet-smart-filters' ),
			'value'       => 'A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q, R, S, T, U, V, W, X, Y, Z',
			'rows'        => 4,
			'conditions'  => array(
				'_filter_type' => 'alphabet'
			),
		),

		// Search
		'_s_by' => array(
			'type'        => 'select',
			'title'       => __( 'Search by', 'jet-smart-filters' ),
			'placeholder' => __( 'Select search source...', 'jet-smart-filters' ),
			'options'     => array(
				'default' => __( 'Default WordPress search', 'jet-smart-filters' ),
				'meta'    => __( 'By Custom Field (from Query Variable)', 'jet-smart-filters' ),
			),
			'conditions'  => array(
				'_filter_type' => 'search'
			),
		),
		'_s_placeholder' => array(
			'type'       => 'text',
			'title'      => __( 'Placeholder', 'jet-smart-filters' ),
			'value'      => __( 'Search...', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type' => 'search',
				'_s_by!'       => ''
			),
		),

		// Visual
		'_color_image_type' => array(
			'title'       => __( 'Type', 'jet-smart-filters' ),
			'type'        => 'select',
			'placeholder' => __( 'Select type...', 'jet-smart-filters' ),
			'options'     => array(
				'color' => __( 'Color', 'jet-smart-filters' ),
				'image' => __( 'Image', 'jet-smart-filters' ),
			),
			'conditions'  => array(
				'_filter_type' => 'color-image',
				'_data_source!'=> '',
			),
		),
		'_color_image_behavior' => array(
			'title'       => __( 'Behavior', 'jet-smart-filters' ),
			'type'        => 'select',
			'placeholder' => __( 'Select behavior...', 'jet-smart-filters' ),
			'options'     => array(
				'checkbox' => __( 'Checkbox', 'jet-smart-filters' ),
				'radio'    => __( 'Radio', 'jet-smart-filters' ),
			),
			'conditions'  => array(
				'_filter_type'  => 'color-image',
				'_data_source!' => '',
			),
		),
		'_source_color_image_input' => array(
			'type'        => 'repeater',
			'title'       => __( 'Options List', 'jet-smart-filters' ),
			'axis'        => 'xy',
			'add_label'   => __( 'New Option', 'jet-smart-filters' ),
			'fields'      => array(
				'label' => array(
					'type'  => 'text',
					'title' => __( 'Label', 'jet-smart-filters' ),
				),
				'value' => array(
					'type'  => 'text',
					'title' => __( 'Value', 'jet-smart-filters' ),
				),
				'selected_value' => array(
					'type'        => 'select',
					'title'       => __( 'Value', 'jet-smart-filters' ),
					'placeholder' => __( 'Select item...', 'jet-smart-filters' ),
				),
				'source_color' => array(
					'type'  => 'colorpicker',
					'title' => __( 'Color', 'jet-smart-filters' ),
				),
				'source_image' => array(
					'type'     => 'media',
					'title'    => __( 'Image', 'jet-smart-filters' ),
					'multiple' => false,
				),
			),
			'conditions' => array(
				'_filter_type'           => 'color-image',
				'_data_source!'          => '',
				'_color_image_behavior!' => ''
			),
		),

		'_color_image_add_all_option' => array(
			'type'       => 'switcher',
			'title'      => __( 'Add all option', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type'          => 'color-image',
				'_color_image_behavior' => 'radio',
			),
		),

		'_color_image_add_all_option_lael' => array(
			'type'       => 'text',
			'title'      => __( 'All option label', 'jet-smart-filters' ),
			'value'      => __( 'All', 'jet-smart-filters' ),
			'conditions' => array(
				'_color_image_add_all_option' => true,
			),
		),

		'_color_image_add_all_option_image' => array(
			'type'       => 'media',
			'conditions' => array(
				'_color_image_add_all_option' => true,
			),
		),

		'_color_image_ability_deselect_radio' => array(
			'type'       => 'switcher',
			'title'      => __( 'Ability to deselect radio buttons', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type'          => 'color-image',
				'_color_image_behavior' => 'radio',
			),
		),

		// Taxonomies Custom Fields
		'_show_empty_terms' => array(
			'type'       => 'switcher',
			'title'      => __( 'Show empty terms', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
				'_data_source' => 'taxonomies',
			),
		),
		'_only_child' => array(
			'type'       => 'switcher',
			'title'      => __( 'Show only childs of current term', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
				'_data_source' => 'taxonomies',
			),
		),
		'_group_by_parent' => array(
			'type'       => 'switcher',
			'title'      => __( 'Group terms by parents', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'radio' ),
				'_data_source' => 'taxonomies',
			),
		),

		// Custom Fields
		'_source_custom_field' => array(
			'type'       => 'text',
			'title'      => __( 'Custom Field Key', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
				'_data_source' => 'custom_fields',
			),
		),
		'_source_get_from_field_data' => array(
			'type'       => 'switcher',
			'title'      => __( 'Get Choices From Field Data', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio' ),
				'_data_source' => 'custom_fields',
			),
		),
		'_custom_field_source_plugin' => array(
			'type'    => 'select',
			'title'   => __( 'Field Source Plugin', 'jet-smart-filters' ),
			'options' => array(
				'jet_engine' => __( 'JetEngine', 'jet-smart-filters' ),
				'acf'        => __( 'ACF', 'jet-smart-filters' ),
			),
			'conditions' => array(
				'_source_get_from_field_data' => true,
			),
		),

		'_is_custom_checkbox' => array(
			'type'        => 'switcher',
			'title'       => __( 'Is Checkbox Meta Field', 'jet-smart-filters' ),
			'description' => __( 'This option should be enabled if the meta field data is a serialized object, as this is done in the Checkbox meta field type created using the JetEngine or ACF plugins.', 'jet-smart-filters' ),
			'conditions'  => array(
				'_filter_type'  => array( 'checkboxes', 'select', 'radio', 'color-image' ),
				'_data_source'  => 'is_visible',
				'_data_source!' => array( '', 'cct' ),
			),
		),
		'_use_exclude_include' => array(
			'type'    => 'select',
			'title'   => __( 'Exclude/Include', 'jet-smart-filters' ),
			'options' => array(
				''        => __( 'None', 'jet-smart-filters' ),
				'exclude' => __( 'Exclude', 'jet-smart-filters' ),
				'include' => __( 'Include', 'jet-smart-filters' ),
			),
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio' ),
				'_data_source' => array( 'taxonomies', 'posts' ),
			),
		),
		'_data_exclude_include' => array(
			'type'        => 'select',
			'title'       => __( 'Exclude Or Include Items', 'jet-smart-filters' ),
			'placeholder' => __( 'Select item...', 'jet-smart-filters' ),
			'multiple'    => true,
			'conditions'  => array(
				'_use_exclude_include'  => 'is_visible',
				'_use_exclude_include!' => '',
			),
		),

		// Query Variable
		'_query_var' => array(
			'type'        => 'advanced_input',
			'title'       => __( 'Query Variable *', 'jet-smart-filters' ),
			'description' => __( 'Set queried field key. For multiple field keys separate them with commas', 'jet-smart-filters' ),
			'required'    => true,
			'conditions'  => array(
				'_filter_type!'      => array( '', 'alphabet' ),
				'_data_source!'      => array( '', 'taxonomies' ),
				'_date_source!'      => array( '', 'date_query' ),
				'_date_period_type!' => '',
				'_s_by!'             => array( '', 'default' ),
				'_is_hierarchical!'  => true,
			),
		),
		'_is_custom_query_var' => array(
			'type'    => 'switcher',
			'title'   => __( 'Use Custom Query Variable', 'jet-smart-filters' ),
			'conditions' => array(
				'_data_source' => 'taxonomies'
			),
		),
		'_custom_query_var' => array(
			'type'       => 'advanced_input',
			'title'      => __( 'Custom Query Variable', 'jet-smart-filters' ),
			'conditions' => array(
				'_is_custom_query_var' => true
			)
		),
		'_query_compare' => array(
			'type'        => 'select',
			'title'       => __( 'Comparison operator', 'jet-smart-filters' ),
			'description' => __( 'How to compare the above value', 'jet-smart-filters' ),
			'value'       => 'equal',
			'options'     => array(
				'equal'   => __( 'Equals (=)', 'jet-smart-filters' ),
				'less'    => __( 'Less than or equals (<=)', 'jet-smart-filters' ),
				'greater' => __( 'Greater than or equals (>=)', 'jet-smart-filters' ),
				'like'    => __( 'LIKE', 'jet-smart-filters' ),
				//'in'      => __( 'IN', 'jet-smart-filters' ),
				//'between' => __( 'BETWEEN', 'jet-smart-filters' ),
				'exists'  => __( 'EXISTS', 'jet-smart-filters' ),
				'regexp'  => __( 'REGEXP', 'jet-smart-filters' )
			),
			'conditions' => array(
				'_query_var'   => 'is_visible',
				'_filter_type' => array( 'select', 'radio' ),
			),
		),
	)
);