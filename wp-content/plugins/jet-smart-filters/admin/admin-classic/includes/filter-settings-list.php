<?php
/**
 * List of options for a single filter
 */

return array(
	'labels' => array(
		'_filter_label' => array(
			'title'   => __( 'Filter Label', 'jet-smart-filters' ),
			'type'    => 'text',
			'value'   => '',
			'element' => 'control',
		),
		'_active_label' => array(
			'title'   => __( 'Active Filter Label', 'jet-smart-filters' ),
			'type'    => 'text',
			'value'   => '',
			'element' => 'control',
		),
	),

	'settings' => array(
		'_filter_type' => array(
			'title'   => __( 'Filter Type', 'jet-smart-filters' ),
			'type'    => 'select',
			'element' => 'control',
			'options' => $this->types(),
		),
		'_date_source' => array(
			'title'   => __( 'Filter by', 'jet-smart-filters' ),
			'type'    => 'select',
			'element' => 'control',
			'options' => array(
				'meta_query' => __( 'Meta Date', 'jet-smart-filters' ),
				'date_query' => __( 'Post Date', 'jet-smart-filters' ),
			),
			'conditions' => array(
				'_filter_type' => array( 'date-range', 'date-period' ),
			),
		),
		'_is_hierarchical' => array(
			'title'      => __( 'Is hierarchical', 'jet-smart-filters' ),
			'type'       => 'switcher',
			'element'    => 'control',
			'value'      => false,
			'conditions' => array(
				'_filter_type' => array( 'select' ),
			),
		),
		'_ih_source_map' => array(
			'title'       => __( 'Filter hierarchy', 'jet-smart-filters' ),
			'element'     => 'control',
			'type'        => 'repeater',
			'add_label'   => __( 'New Level', 'jet-smart-filters' ),
			'title_field' => 'label',
			'fields'      => array(
				'label' => array(
					'type'  => 'text',
					'id'    => 'label',
					'name'  => 'label',
					'label' => __( 'Label', 'jet-smart-filters' ),
					'class' => 'source-map-control label-control',
				),
				'placeholder' => array(
					'type'        => 'text',
					'id'          => 'placeholder',
					'name'        => 'placeholder',
					'placeholder' => __( 'Select...', 'jet-smart-filters' ),
					'label'       => __( 'Placeholder', 'jet-smart-filters' ),
					'class'       => 'source-map-control placeholder-control',
				),
				'tax' => array(
					'type'    => 'select',
					'id'      => 'tax',
					'name'    => 'tax',
					'label'   => __( 'Taxonomy', 'jet-smart-filters' ),
					'options' => $this->taxonomy_options,
					'class'   => 'source-map-control tax-control',
				),
			),
			'conditions' => array(
				'_is_hierarchical' => array( true ),
				'_filter_type'     => array( 'select' ),
			),
		),
		'_data_source' => array(
			'title'   => __( 'Data Source', 'jet-smart-filters' ),
			'type'    => 'select',
			'element' => 'control',
			'options' => $this->sources(),
			'conditions' => array(
				'_filter_type'     => array( 'checkboxes', 'select', 'radio', 'color-image' ),
				'_is_hierarchical' => array( false, '' ),
			),
		),
		'_rating_options' => array(
			'title'      => __( 'Stars count', 'jet-smart-filters' ),
			'type'       => 'stepper',
			'element'    => 'control',
			'value'       => 5,
			'max_value'   => 10,
			'min_value'   => 1,
			'step_value'  => 1,
			'conditions' => array(
				'_filter_type'   => array( 'rating' ),
			),
		),
		'_rating_compare_operand' => array(
			'title'       => __( 'Inequality operator', 'jet-smart-filters' ),
			'description' => __( 'Set relation between values', 'jet-smart-filters' ),
			'type'        => 'select',
			'options'     => array(
				'greater' => __( 'Greater than or equals (>=)', 'jet-smart-filters' ),
				'less'    => __( 'Less than or equals (<=)', 'jet-smart-filters' ),
				'equal'   => __( 'Equals (=)', 'jet-smart-filters' ),
			),
			'element'     => 'control',
			'conditions'  => array(
				'_filter_type' => array( 'rating' ),
			),
		),
		'_source_taxonomy' => array(
			'title'      => __( 'Taxonomy', 'jet-smart-filters' ),
			'type'       => 'select',
			'element'    => 'control',
			'options'    => $this->taxonomy_options,
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
				'_data_source' => 'taxonomies',
				'_is_hierarchical' => array( false, '' ),
			),
		),
		'_terms_relational_operator' => array(
			'title'            => __( 'Relational Operator', 'jet-smart-filters' ),
			'type'             => 'select',
			'element'          => 'control',
			'options' => array(
				'OR'  => __( 'Union', 'jet-smart-filters' ),
				'AND' => __( 'Intersection', 'jet-smart-filters' ),
			),
			'conditions'       => array(
				'_filter_type' => array( 'checkboxes' ),
				'_data_source' => 'taxonomies',
				'_is_hierarchical' => array( false, '' ),
			),
		),
		'_source_post_type' => array(
			'title'      => __( 'Post Type', 'jet-smart-filters' ),
			'type'       => 'select',
			'element'    => 'control',
			'options'    => $this->post_types_options,
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
				'_data_source' => 'posts',
			),
		),
		'_add_all_option' => array(
			'title'   => __( 'Add all option', 'jet-smart-filters' ),
			'type'    => 'switcher',
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => 'radio'
			),
		),
		'_all_option_label' => array(
			'title'   => __( 'All option label', 'jet-smart-filters' ),
			'type'    => 'text',
			'value'   => __( 'All', 'jet-smart-filters' ),
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => 'radio',
				'_add_all_option' => true
			),
		),
		'_ability_deselect_radio' => array(
			'title'   => __( 'Ability to deselect radio buttons', 'jet-smart-filters' ),
			'type'    => 'switcher',
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => 'radio'
			),
		),
		'_show_empty_terms' => array(
			'title'   => __( 'Show empty terms', 'jet-smart-filters' ),
			'type'    => 'switcher',
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
				'_data_source' => 'taxonomies',
				'_is_hierarchical' => array( false, '' ),
			),
		),
		'_only_child' => array(
			'title'   => __( 'Show only children of current term', 'jet-smart-filters' ),
			'type'    => 'switcher',
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
				'_data_source' => 'taxonomies',
				'_is_hierarchical' => array( false, '' ),
			),
		),
		'_group_by_parent' => array(
			'title'   => __( 'Group terms by parents', 'jet-smart-filters' ),
			'type'    => 'switcher',
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'radio' ),
				'_data_source' => 'taxonomies',
			),
		),
		'_source_custom_field' => array(
			'title'   => __( 'Custom Field Key', 'jet-smart-filters' ),
			'type'    => 'text',
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
				'_data_source' => 'custom_fields',
			),
		),
		'_source_get_from_field_data' => array(
			'title'   => __( 'Get Choices From Field Data', 'jet-smart-filters' ),
			'type'    => 'switcher',
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio' ),
				'_data_source' => 'custom_fields',
			),
		),
		'_custom_field_source_plugin' => array(
			'title'   => __( 'Field Source Plugin', 'jet-smart-filters' ),
			'type'    => 'select',
			'element' => 'control',
			'options' => array(
				'jet_engine' => __( 'JetEngine', 'jet-smart-filters' ),
				'acf'        => __( 'ACF', 'jet-smart-filters' ),
			),
			'conditions' => array(
				'_filter_type'                => array( 'checkboxes', 'select', 'radio' ),
				'_data_source'                => 'custom_fields',
				'_source_get_from_field_data' => array( true ),
			),
		),
		'_source_manual_input' => array(
			'title'       => __( 'Options List', 'jet-smart-filters' ),
			'element'     => 'control',
			'type'        => 'repeater',
			'add_label'   => __( 'New Option', 'jet-smart-filters' ),
			'title_field' => 'label',
			'fields'      => array(
				'value' => array(
					'type'  => 'text',
					'id'    => 'value',
					'name'  => 'value',
					'label' => __( 'Value', 'jet-smart-filters' ),
				),
				'label' => array(
					'type'  => 'text',
					'id'    => 'label',
					'name'  => 'label',
					'label' => __( 'Label', 'jet-smart-filters' ),
				),
			),
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio' ),
				'_data_source' => 'manual_input',
			),
		),
		'_color_image_type' => array(
			'title'      => __( 'Type', 'jet-smart-filters' ),
			'type'       => 'select',
			'options'    => array(
				0       => __( 'Choose Type', 'jet-smart-filters' ),
				'color' => __( 'Color', 'jet-smart-filters' ),
				'image' => __( 'Image', 'jet-smart-filters' ),
			),
			'element'    => 'control',
			'conditions' => array(
				'_filter_type' => array( 'color-image' ),
				'_data_source' => array( 'taxonomies', 'posts', 'custom_fields', 'manual_input' ),
			),
		),
		'_color_image_behavior' => array(
			'title'      => __( 'Behavior', 'jet-smart-filters' ),
			'type'       => 'select',
			'options'    => array(
				'checkbox' => __( 'Checkbox', 'jet-smart-filters' ),
				'radio'    => __( 'Radio', 'jet-smart-filters' ),
			),
			'element'    => 'control',
			'conditions' => array(
				'_filter_type' => array( 'color-image' ),
				'_data_source' => array( 'taxonomies', 'posts', 'custom_fields', 'manual_input' ),
			),
		),
		'_source_color_image_input' => array(
			'title'       => __( 'Options List', 'jet-smart-filters' ),
			'element'     => 'control',
			'type'        => 'repeater',
			'add_label'   => __( 'New Option', 'jet-smart-filters' ),
			'title_field' => 'label',
			'class'       => 'jet-smart-filters-color-image',
			'fields'      => array(
				'label' => array(
					'type'  => 'text',
					'id'    => 'label',
					'name'  => 'label',
					'label' => __( 'Label', 'jet-smart-filters' ),
					'class' => 'color-image-type-control label-control',
				),
				'value' => array(
					'type'  => 'text',
					'id'    => 'value',
					'name'  => 'value',
					'label' => __( 'Value', 'jet-smart-filters' ),
					'class' => 'color-image-type-control value-control',
				),
				'selected_value' => array(
					'type'    => 'select',
					'id'      => 'selected_value',
					'name'    => 'selected_value',
					'options' => array(),
					'label'   => __( 'Value', 'jet-smart-filters' ),
					'class'   => 'color-image-type-control selected-value-control',
				),
				'source_color' => array(
					'type'  => 'colorpicker',
					'id'    => 'source_color',
					'name'  => 'source_color',
					'label' => __( 'Color', 'jet-smart-filters' ),
					'class' => 'color-image-type-control color-control',
				),
				'source_image' => array(
					'type'         => 'media',
					'id'           => 'source_image',
					'name'         => 'source_image',
					'multi_upload' => false,
					'library_type' => 'image',
					'label'        => __( 'Image', 'jet-smart-filters' ),
					'class'        => 'color-image-type-control image-control',
				),
			),
			'conditions' => array(
				'_filter_type'      => array( 'color-image' ),
				'_data_source'      => array( 'taxonomies', 'posts', 'custom_fields', 'manual_input' ),
				'_color_image_type' => array( 'color', 'image' ),
			),
		),
		'_color_image_add_all_option' => array(
			'type'       => 'switcher',
			'title'      => __( 'Add all option', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type'          => array( 'color-image' ),
				'_color_image_behavior' => 'radio',
			),
		),
		'_color_image_add_all_option_lael' => array(
			'type'       => 'text',
			'title'      => __( 'All option label', 'jet-smart-filters' ),
			'value'      => __( 'All', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type'                => array( 'color-image' ),
				'_color_image_behavior'       => 'radio',
				'_color_image_add_all_option' => true,
			),
		),
		'_color_image_add_all_option_image' => array(
			'type'       => 'media',
			'conditions' => array(
				'_filter_type'                => array( 'color-image' ),
				'_color_image_behavior'       => 'radio',
				'_color_image_add_all_option' => true,
			),
		),
		'_color_image_ability_deselect_radio' => array(
			'type'       => 'switcher',
			'title'      => __( 'Ability to deselect radio buttons', 'jet-smart-filters' ),
			'conditions' => array(
				'_filter_type'          => array( 'color-image' ),
				'_color_image_behavior' => 'radio',
			),
		),
		'_source_manual_input_range' => array(
			'title'       => __( 'Options List', 'jet-smart-filters' ),
			'element'     => 'control',
			'type'        => 'repeater',
			'add_label'   => __( 'New Option', 'jet-smart-filters' ),
			'title_field' => 'label',
			'fields'      => array(
				'min' => array(
					'type'  => 'text',
					'id'    => 'min',
					'name'  => 'min',
					'label' => __( 'Min Value', 'jet-smart-filters' ),
					'placeholder' => '0'
				),
				'max' => array(
					'type'  => 'text',
					'id'    => 'max',
					'name'  => 'max',
					'label' => __( 'Max Value', 'jet-smart-filters' ),
					'placeholder' => '100'
				),
			),
			'conditions' => array(
				'_filter_type' => 'check-range',
			),
		),
		'_placeholder' => array(
			'title'       => __( 'Placeholder', 'jet-smart-filters' ),
			'type'        => 'text',
			'placeholder' => __( 'Select...', 'jet-smart-filters' ),
			'value'       => __( 'Select...', 'jet-smart-filters' ),
			'element'     => 'control',
			'conditions'  => array(
				'_filter_type' => 'select',
				'_is_hierarchical' => array( false, '' ),
			),
		),
		'_s_placeholder' => array(
			'title'   => __( 'Placeholder', 'jet-smart-filters' ),
			'type'    => 'text',
			'value'   => __( 'Search...', 'jet-smart-filters' ),
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => 'search',
			),
		),
		'_is_custom_checkbox' => array(
			'title'   => __( 'Is Checkbox Meta Field', 'jet-smart-filters' ),
			'description' => __( 'This option should be enabled if the meta field data is a serialized object, as this is done in the Checkbox meta field type created using the JetEngine or ACF plugins.', 'jet-smart-filters' ),
			'type'    => 'switcher',
			'element' => 'control',
			'conditions' => array(
				'_filter_type'     => array( 'checkboxes', 'select', 'radio', 'color-image' ),
				'_is_hierarchical' => array( false, '' ),
				'_data_source!'    => array( 'cct' )
			),
		),
		'_s_by' => array(
			'title'   => __( 'Search by', 'jet-smart-filters' ),
			'type'    => 'select',
			'element' => 'control',
			'options' => array(
				'default' => __( 'Default WordPress search', 'jet-smart-filters' ),
				'meta'    => __( 'By Custom Field (from Query Variable)', 'jet-smart-filters' ),
			),
			'conditions' => array(
				'_filter_type' => 'search',
			),
		),
		'_date_format' => array(
			'title'       => __( 'Date Format', 'jet-smart-filters' ),
			'description' => '<a href="https://api.jqueryui.com/datepicker/#utility-formatDate" target="_blank">' . __( 'Datepicker date formats', 'jet-smart-filters' ) . '</a>',
			'type'        => 'text',
			'placeholder' => 'mm/dd/yy',
			'value'       => 'mm/dd/yy',
			'element'     => 'control',
			'conditions'  => array(
				'_filter_type' => 'date-range',
			),
		),
		'_date_from_placeholder' => array(
			'title'   => __( 'From Placeholder', 'jet-smart-filters' ),
			'type'    => 'text',
			'value'   => '',
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => 'date-range',
			),
		),
		'_date_to_placeholder' => array(
			'title'   => __( 'To Placeholder', 'jet-smart-filters' ),
			'type'    => 'text',
			'value'   => '',
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => 'date-range',
			),
		),
		'_date_period_type' => array(
			'title'   => __( 'Period Type', 'jet-smart-filters' ),
			'type'    => 'select',
			'options' => array(
				'range' => __( 'Custom range', 'jet-smart-filters' ),
				'day'   => __( 'Day', 'jet-smart-filters' ),
				'week'  => __( 'Week', 'jet-smart-filters' ),
				'month' => __( 'Month', 'jet-smart-filters' ),
				'year'  => __( 'Year', 'jet-smart-filters' ),
			),
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => 'date-period',
			),
		),
		'_date_period_datepicker_button_text' => array(
			'title'      => __( 'Datepicker Button Text', 'jet-smart-filters' ),
			'type'       => 'text',
			'value'      => __( 'Select Date', 'jet-smart-filters' ),
			'element'    => 'control',
			'conditions' => array(
				'_filter_type' => 'date-period',
			),
		),
		'_min_max_date_period_enabled' => array(
			'title'      => __( 'Min/Max Dates Enabled', 'jet-smart-filters' ),
			'type'       => 'switcher',
			'element'    => 'control',
			'value'      => false,
			'conditions' => array(
				'_filter_type' => 'date-period',
			),
		),
		'_min_date_period' => array(
			'title'       => __( 'Minimum possible date to select', 'jet-smart-filters' ),
			'type'        => 'text',
			'element'     => 'control',
			'description' => __( 'To set the limit by the current date, fill in', 'jet-smart-filters' ). ' - "today"',
			'conditions'  => array(
				'_filter_type'                 => 'date-period',
				'_min_max_date_period_enabled' => true
			),
		),
		'_max_date_period' => array(
			'type'        => 'text',
			'title'       => __( 'Maximum possible date to select', 'jet-smart-filters' ),
			'description' => __( 'To set the limit by the current date, fill in', 'jet-smart-filters' ). ' - "today"',
			'element'     => 'control',
			'conditions'  => array(
				'_filter_type'                 => 'date-period',
				'_min_max_date_period_enabled' => true
			),
		),
		'_date_period_start_end_enabled' => array(
			'title'   => __( 'Start/End Date Period Enabled', 'jet-smart-filters' ),
			'type'    => 'switcher',
			'element' => 'control',
			'value'   => true,
			'conditions' => array(
				'_filter_type' => 'date-period',
			),
		),
		'_date_period_format' => array(
			'title'       => __( 'Date Period Format', 'jet-smart-filters' ),
			'type'        => 'text',
			'placeholder' => 'mm/dd/yy',
			'element'     => 'control',
			'conditions'  => array(
				'_filter_type'                   => 'date-period',
				'_date_period_start_end_enabled' => false
			),
		),

		'_date_period_start_format' => array(
			'title'       => __( 'Start Format', 'jet-smart-filters' ),
			'type'        => 'text',
			'description' => __( 'If Period Type is Day, only this value will be taken', 'jet-smart-filters' ),
			'placeholder' => 'mm/dd/yy',
			'element'     => 'control',
			'conditions'  => array(
				'_filter_type'                   => 'date-period',
				'_date_period_start_end_enabled' => true
			),
		),
		'_date_period_separator' => array(
			'title'       => __( 'Separator', 'jet-smart-filters' ),
			'type'        => 'text',
			'placeholder' => '-',
			'element'     => 'control',
			'conditions'  => array(
				'_filter_type'                   => 'date-period',
				'_date_period_start_end_enabled' => true
			),
		),
		'_date_period_end_format' => array(
			'title'       => __( 'End Format', 'jet-smart-filters' ),
			'type'        => 'text',
			'placeholder' => 'mm/dd/yy',
			'element'     => 'control',
			'conditions'  => array(
				'_filter_type'                   => 'date-period',
				'_date_period_start_end_enabled' => true
			),
		),
		/* '_date_period_duration' => array(
			'title'       => __( 'Period Duration', 'jet-smart-filters' ),
			'type'        => 'text',
			'placeholder' => '1',
			'element'     => 'control',
			'conditions'  => array(
				'_filter_type' => 'date-period',
			),
		), */
		'_range_inputs_enabled' => array(
			'title'      => __( 'Inputs enabled', 'jet-smart-filters' ),
			'type'       => 'switcher',
			'element'    => 'control',
			'value'      => false,
			'conditions' => array(
				'_filter_type' => 'range',
			),
		),
		'_range_inputs_separators_enabled' => array(
			'title'       => __( 'Inputs separators enabled', 'jet-smart-filters' ),
			'type'        => 'switcher',
			'description' => __( 'Apply thousands and decimal separators to inputs', 'jet-smart-filters' ),
			'element'     => 'control',
			'conditions'  => array(
				'_filter_type'          => 'range',
				'_range_inputs_enabled' => true
			),
		),
		'_values_prefix' => array(
			'title'   => __( 'Values prefix', 'jet-smart-filters' ),
			'type'    => 'text',
			'value'   => '',
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => array( 'range', 'check-range' ),
			),
		),
		'_values_suffix' => array(
			'title'   => __( 'Values suffix', 'jet-smart-filters' ),
			'type'    => 'text',
			'value'   => '',
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => array( 'range', 'check-range' ),
			),
		),
		'_values_thousand_sep' => array(
			'title'      => __( 'Thousands separator', 'jet-smart-filters' ),
			'type'       => 'text',
			'value'      => '',
			'element'    => 'control',
			'conditions' => array(
				'_filter_type' => array( 'range', 'check-range' ),
			),
		),
		'_values_decimal_sep' => array(
			'title'   => __( 'Decimal separator', 'jet-smart-filters' ),
			'type'    => 'text',
			'value'   => '.',
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => array( 'range', 'check-range' ),
			),
		),
		'_values_decimal_num' => array(
			'title'      => __( 'Number of decimals', 'jet-smart-filters' ),
			'type'       => 'text',
			'value'      => 0,
			'element'    => 'control',
			'conditions' => array(
				'_filter_type' => array( 'range', 'check-range' ),
			),
		),
		'_source_min' => array(
			'title'       => __( 'Min Value', 'jet-smart-filters' ),
			'placeholder' => '0',
			'type'        => 'text',
			'element'     => 'control',
			'conditions'  => array(
				'_filter_type' => 'range',
			),
		),
		'_source_max' => array(
			'title'       => __( 'Max Value', 'jet-smart-filters' ),
			'placeholder' => '100',
			'type'        => 'text',
			'element'     => 'control',
			'conditions'  => array(
				'_filter_type' => 'range',
			),
		),
		'_source_step' => array(
			'title'             => __( 'Step', 'jet-smart-filters' ),
			'placeholder'       => '1',
			'type'              => 'text',
			'element'           => 'control',
			'default'           => 1,
			'sanitize_callback' => array( $this, 'sanitize_range_step' ),
			'description'       => __( '1, 10, 100, 0.1 etc', 'jet-smart-filters' ),
			'conditions'        => array(
				'_filter_type' => 'range',
			),
		),
		'_source_callback' => array(
			'title'   => __( 'Get min/max dynamically', 'jet-smart-filters' ),
			'type'    => 'select',
			'options' => apply_filters( 'jet-smart-filters/range/source-callbacks', array(
				0                               => __( 'Select...', 'jet-smart-filters' ),
				'jet_smart_filters_woo_prices'  => __( 'WooCommerce min/max prices', 'jet-smart-filters' ),
				'jet_smart_filters_meta_values' => __( 'Get from query meta key', 'jet-smart-filters' ),
			) ),
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => 'range',
			),
		),
		'_use_exclude_include' => array(
			'title'   => __( 'Exclude/Include', 'jet-smart-filters' ),
			'type'    => 'select',
			'options' => array(
				0         => __( 'None', 'jet-smart-filters' ),
				'exclude' => __( 'Exclude', 'jet-smart-filters' ),
				'include' => __( 'Include', 'jet-smart-filters' ),
			),
			'element' => 'control',
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio' ),
				'_data_source' => array( 'taxonomies', 'posts' ),
				'_is_hierarchical' => array( false, '' ),
			),
		),
		'_data_exclude_include' => array(
			'title'   => __( 'Exclude Or Include Items', 'jet-smart-filters' ),
			'type'    => 'select',
			'element' => 'control',
			'multiple' => true,
			'options' => array(
				'' => '',
			),
			'conditions' => array(
				'_filter_type' => array( 'checkboxes', 'select', 'radio' ),
				'_data_source' => array( 'taxonomies', 'posts' ),
				'_use_exclude_include' => array( 'exclude', 'include' ),
				'_is_hierarchical' => array( false, '' ),
			),
		),
		'_alphabet_behavior' => array(
			'title'      => __( 'Behavior', 'jet-smart-filters' ),
			'type'       => 'select',
			'options'    => array(
				'checkbox' => __( 'Checkbox', 'jet-smart-filters' ),
				'radio'    => __( 'Radio', 'jet-smart-filters' ),
			),
			'element'    => 'control',
			'conditions' => array(
				'_filter_type' => 'alphabet'
			),
		),
		'_alphabet_radio_deselect' => array(
			'title'      => __( 'Ability to deselect radio buttons', 'jet-smart-filters' ),
			'type'       => 'switcher',
			'value'      => true,
			'element'    => 'control',
			'conditions' => array(
				'_filter_type'       => 'alphabet',
				'_alphabet_behavior' => 'radio'
			),
		),
		'_alphabet_options' => array(
			'title'       => __( 'Options', 'jet-smart-filters' ),
			'type'        => 'textarea',
			'value'       => 'A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q, R, S, T, U, V, W, X, Y, Z',
			'element'     => 'control',
			'description' => __( 'Use comma to separate options', 'jet-smart-filters' ),
			'conditions'  => array(
				'_filter_type' => 'alphabet'
			),
		),
	),

	'query' => array(
		'_query_var' => array(
			'title'       => __( 'Query Variable *', 'jet-smart-filters' ),
			'type'        => 'text',
			'description' => __( 'Set queried field key. For multiple field keys separate them with commas', 'jet-smart-filters' ),
			'element'     => 'control',
			'required'    => true,
		),
		'_is_custom_query_var' => array(
			'title'   => __( 'Use Custom Query Variable', 'jet-smart-filters' ),
			'type'    => 'switcher',
			'element' => 'control',
			'conditions' => array(
				'_data_source' => 'taxonomies'
			),
		),
		'_custom_query_var' => array(
			'title'   => __( 'Custom Query Variable', 'jet-smart-filters' ),
			'type'    => 'text',
			'element' => 'control',
			'conditions' => array(
				'_data_source'         => 'taxonomies',
				'_is_custom_query_var' => true
			)
		),
		'_query_compare' => array(
			'title'       => __( 'Comparison operator', 'jet-smart-filters' ),
			'description' => __( 'How to compare the above value', 'jet-smart-filters' ),
			'type'        => 'select',
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
			'element'     => 'control',
		),
	)
);