<?php
/**
 * Class: Jet_Smart_Filters_Provider_EPro_Loop_Grid
 * Name: Elementor Pro Loop Grid
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
	
/**
 * Define Jet_Smart_Filters_Provider_EPro_Loop_Grid class
 */
class Jet_Smart_Filters_Provider_EPro_Loop_Grid extends Jet_Smart_Filters_Provider_Base {
	
	protected $rendered_block = null;

	/**
	 * Add hooks specific for exact provider
	 */
	public function __construct() {

		add_action(
			'elementor/element/' . $this->widget_name() . '/section_layout/before_section_end',
			array( $this, 'register_provider_controls' )
		);

		add_action(
			'elementor/query/query_results',
			array( $this, 'add_no_result_text' ),
			10, 2
		);

		if ( ! jet_smart_filters()->query->is_ajax_filter() ) {
			/**
			 * First of all you need to store default provider query and required attributes to allow
			 * JetSmartFilters attach this data to AJAX request.
			 */
			add_filter( 'elementor/widget/before_render_content', array( $this, 'store_default_settings' ), 0, 3 );

			/**
			 * Store default query args
			 */
			add_action( 'elementor/query/query_results', array( $this, 'store_default_query' ), 10, 2 );
		}
	}

	/**
	 * Register provider-specific controls for the widget
	 */
	public function register_provider_controls( $widget ) {

		$widget->add_control(
			'_jsf_filterable',
			array(
				'label'       => esc_html__( 'Is Filterable', 'elementor-pro' ),
				'description' => esc_html__( 'Enable this if you want to filter current loop with JetSmartFilters', 'jet-samrt-filters' ),
				'type'        => 'switcher',
				'separator'   => 'before',
			)
		);

		$widget->add_control(
			'_jsf_no_result_text',
			array(
				'label'       => esc_html__( 'No Result Text', 'elementor-pro' ),
				'label_block' => true,
				'description' => esc_html__( 'Text that will be displayed if there are no results after filtering', 'jet-samrt-filters' ),
				'type'        => 'text',
				'default'     => __( 'Sorry, nothing to see here...', 'elementor-pro' ),
				'condition'   => array(
					'_jsf_filterable' => 'yes'
				),
			)
		);
	}

	public function add_no_result_text( $query, $widget ) {

		if ( ! $this->is_filterable_widget( $widget ) ) {
			return;
		}

		$total = $query->found_posts;
		if ( $total == 0 ) {
			$no_result_text = $widget->get_settings_for_display()['_jsf_no_result_text'];

			if ( $no_result_text ) {
				remove_action(
					'elementor/query/query_results',
					array( $this, 'add_no_result_text' ),
					10, 2
				);

				$classes = apply_filters( 'jet-smart-filters/providers/' . $this->widget_name() . '/no-result-classes', array(
					'container' => 'elementor-loop-container',
					'text'      => 'jet-smart-filters-elementor-loop-no-result'
				));

				echo '<div class="' . $classes['container'] . '"><div class="' . $classes['text'] . '">' . $no_result_text . '</div></div>';
			}
		}
	}

	public function is_filterable_widget( $widget ) {

		if ( $this->widget_name() !== $widget->get_name() ) {
			return false;
		}

		$settings      = $widget->get_settings_for_display();
		$is_filterable = isset( $settings['_jsf_filterable'] ) ? $settings['_jsf_filterable'] : false;
		$is_filterable = filter_var( $is_filterable, FILTER_VALIDATE_BOOLEAN );

		if ( ! $is_filterable ) {
			return false;
		}

		return true;
	}

	public function get_query_id( $widget ) {

		$settings = $widget->get_settings_for_display();

		return ! empty( $settings['_element_id'] ) ? $settings['_element_id'] : 'default';
	}

	/**
	 * Store default block attributes to add them to filters AJAX request
	 */
	public function store_default_settings( $widget ) {

		if ( ! $this->is_filterable_widget( $widget ) ) {
			return;
		}

		$settings         = $widget->get_settings_for_display();
		$query_id         = $this->get_query_id( $widget );
		$current_document = \Elementor\Plugin::$instance->documents->get_current();

		if ( ! $current_document ) {
			$post_id = get_the_ID();
		} else {
			$post_id = $current_document->get_main_id();
		}

		/**
		 * We'll parse required block settings from page content.
		 * In this case such approach used because we need inner content anyway.
		 * If your block content defined only with attributes - here you can set array of these attributes
		 * and store it with jet_smart_filters()->providers->add_provider_settings(), than filter add these attributes 
		 * to request and you'll can create new instane of required block without content parsing
		 */
		$attrs = array(
			'widget_id'        => $widget->get_id(),
			'filtered_post_id' => $post_id,
		);

		jet_smart_filters()->providers->add_provider_settings( $this->get_id(), $attrs, $query_id );

		/**
		 * Store default query args from widget query settings
		 */
		// regular query args filter
		add_filter( 'elementor/query/query_args', array( $this, 'store_default_widget_query_settings' ), 20, 2 );
		// current query args filter
		add_filter( 'elementor/query/get_query_args/current_query', function( $current_query_args ) use ( $widget ) {
			return $this->store_default_widget_query_settings(
				// adding 'post_status' to current query arguments to hide draft posts
				array_merge(
					array(
						'post_status' => array( 'publish', 'private' )
					),
					$current_query_args
				),
				$widget
			);
		} );
	}

	/**
	 * Save default query
	 */
	public function store_default_query( $wp_query, $widget ) {

		if ( ! $this->is_filterable_widget( $widget ) ) {
			return;
		}

		$settings = $widget->get_settings_for_display();
		$query_id = $this->get_query_id( $widget );

		$wp_query->set( 'jet_smart_filters', $this->get_id() . '/' . $query_id );

		jet_smart_filters()->query->set_props(
			$this->get_id(),
			array(
				'found_posts'   => $wp_query->found_posts,
				'max_num_pages' => $wp_query->max_num_pages,
				'page'          => $wp_query->get( 'paged' ),
			),
			$query_id
		);
	}

	/**
	 * Save default query from widget query settings
	 */
	public function store_default_widget_query_settings( $query_args, $widget ) {

		if ( ! $this->is_filterable_widget( $widget ) ) {
			return $query_args;
		}

		$settings     = $widget->get_settings_for_display();
		$query_id     = $this->get_query_id( $widget );
		$default_args = $query_args;

		/*
		 * these parameters break:
			- the alternate template "Static item position" option
			- Query source - Sales/Featured/Cross-Sells
		*/
		foreach ( array( 'paged', 'posts_per_page', 'ignore_sticky_posts', ) as $propKey ) {
			unset( $default_args[$propKey] );
		}

		jet_smart_filters()->query->store_provider_default_query( $this->get_id(), $default_args, $query_id );

		return $query_args;
	}

	/**
	 * Returns Elementor Pro apropriate widget name
	 */
	public function widget_name() {

		return 'loop-grid';
	}

	/**
	 * Get provider name
	 */
	public function get_name() {

		return __( 'Elementor Pro Loop Grid', 'jet-samrt-filters' );
	}

	/**
	 * Get provider ID
	 */
	public function get_id() {

		return 'epro-loop-builder';
	}

	/**
	 * Get provider wrapper selector
	 * Its CSS selector of HTML element with provider content.
	 */
	public function get_wrapper_selector() {

		return '.elementor-loop-container';
	}

	/**
	 * Set prefix for unique ID selector. Mostly is default '#' sign, but sometimes class '.' sign needed.
	 * For example for Query Loop block we don't have HTML/CSS ID attribute, so we need to use class as unique identifier.
	 */
	public function id_prefix() {

		return '#';
	}

	/**
	 * Action for wrapper selector - 'insert' into it or 'replace'
	 */
	public function get_wrapper_action() {

		return 'replace';
	}

	/**
	 * If added unique ID this paramter will determine - search selector inside this ID, or is the same element
	 */
	public function in_depth() {

		return true;
	}

	/**
	 * Get filtered provider content.
	 */
	public function ajax_get_content() {

		$settings  = ! empty( $_REQUEST['settings'] ) ? $_REQUEST['settings'] : [];
		$post_id   = ! empty( $settings['filtered_post_id'] ) ? absint( $settings['filtered_post_id'] ) : false;
		$widget_id = ! empty( $settings['widget_id'] ) ? $settings['widget_id'] : false;

		if ( ! $post_id || ! $widget_id ) {
			_e( 'Error. Incomplete request', 'jet-smart-filters' );
			return;
		}

		$widget = $this->get_filtered_widget( $post_id, $widget_id );

		if ( $widget ) {

			// regular query args filter
			add_filter( 'elementor/query/query_args', array( $this, 'add_query_args' ), 30, 2 );
			// current query args filter
			add_filter( 'elementor/query/get_query_args/current_query', function( $current_query_vars ) use ( $widget ) {
				return $this->add_query_args( $current_query_vars, $widget );
			} );

			// render content
			ob_start();

			$skin = $widget->get_current_skin();
			
			if ( $skin ) {
				$skin->set_parent( $widget );
				$skin->render_by_mode();
			} else {
				$widget->render_by_mode();
			}

			$content = ob_get_clean();

			if ( $content ) {
				echo $content;
			} else {
				echo '<div class="elementor-loop-container"></div>';
			}
		} else {
			echo 'Widget not found';
		}
	}

	/**
	 * Apply filters on page reload
	 * Filter arguments in this case pased with $_GET request
	 */
	public function apply_filters_in_request() {

		$args = jet_smart_filters()->query->get_query_args();

		if ( ! $args ) {
			return;
		}

		// regular query args filter
		add_filter( 'elementor/query/query_args', array( $this, 'add_query_args' ), 30, 2 );

		// current query args filter
		add_filter( 'elementor/widget/before_render_content', function( $widget ) {
			if ( $this->is_filterable_widget( $widget ) ) {
				add_filter( 'elementor/query/get_query_args/current_query', function( $current_query_vars ) use ( $widget ) {
					return $this->add_query_args( $current_query_vars, $widget );
				} );
			}
		} );
	}

	/**
	 * Find filtered widget inside given page content
	 */
	public function get_filtered_widget( $post_id, $widget_id ) {

		$elementor = \Elementor\Plugin::instance();
		$document = $elementor->documents->get( $post_id );

		if ( $document ) {
			$widget = $this->find_widget_recursive( $document->get_elements_data(), $widget_id );

			if ( $widget ) {
				$widget_instance = $elementor->elements_manager->create_element_instance( $widget );
			}
		}

		return $widget_instance;
	}

	/**
	 * Find required widget in given widgets stack
	 */
	public function find_widget_recursive( $widgets, $widget_id ) {

		foreach ( $widgets as $widget ) {

			if ( $widget_id === $widget['id'] ) {
				return $widget;
			}

			if ( ! empty( $widget['elements'] ) ) {

				$widget = $this->find_widget_recursive( $widget['elements'], $widget_id );

				if ( $widget ) {
					return $widget;
				}
			}
		}

		return false;
	}

	/**
	 * Check if is currently filtered widget
	 */
	public function is_currently_filtered_widget( $widget, $query_id = 'default' ) {
		
		$settings      = $widget->get_settings_for_display();
		$is_filterable = isset( $settings['_jsf_filterable'] ) ? $settings['_jsf_filterable'] : false;
		$is_filterable = filter_var( $is_filterable, FILTER_VALIDATE_BOOLEAN );

		if ( ! $is_filterable ) {
			return false;
		}

		$widget_query_id = ! empty( $settings['_element_id'] ) ? $settings['_element_id'] : 'default';

		return $query_id === $widget_query_id;
	}

	/**
	 * Add custom query arguments
	 * This methos used by both - AJAX and page reload filters to add filter request data to query.
	 * You need to check - should it be applied or not before hooking on 'pre_get_posts'
	 */
	public function add_query_args( $query_args, $widget ) {

		/**
		 * With this method we can get prepared query arguments from filters request.
		 * This method returns only filtered query argumnets, not whole query.
		 * Arguments returned in the format prepared for WP_Query usage. If you need to use it in some other way -
		 * you need to manually parse this arguments into required format.
		 *
		 * All custom query variables will be gathered under 'meta_query'
		 */
		$args = jet_smart_filters()->query->get_query_args();

		if ( empty( $args ) ) {
			return $query_args;
		}

		$provider = jet_smart_filters()->query->get_current_provider();

		if ( empty( $provider ) || $this->get_id() !== $provider['provider'] ) {
			return $query_args;
		}

		if ( ! $this->is_currently_filtered_widget( $widget, $provider['query_id'] ) ) {
			return $query_args;
		}

		foreach ( $args as $query_var => $value ) {
			if ( in_array( $query_var, array( 'tax_query', 'meta_query' ) ) ) {

				$current = isset( $query_args[ $query_var ] ) ? $query_args[ $query_var ] : [];

				if ( ! empty( $current ) ) {
					$value = array_merge( $current, $value );
				}

				$query_args[ $query_var ] = $value;
			} else {
				$query_args[ $query_var ] = $value;
			}

		}

		// query args type conversion from string to boolean
		$boolean_args = apply_filters(
			'jet-smart-filters/widgets/loop-grid/boolean-query-args',
			array( 'nopaging', 'no_found_rows', 'ignore_sticky_posts')
		);
		foreach ( $boolean_args as $arg ) {
			if ( isset( $query_args[$arg] ) ) {
				$query_args[$arg] = filter_var( $query_args[$arg], FILTER_VALIDATE_BOOLEAN );
			}
		}

		// Remove offset parameter when pagination is used, because it brakes the pagination
		if ( ! empty( $query_args['paged'] ) && isset( $query_args['offset'] ) ) {
			unset( $query_args['offset'] );
		}
		
		return $query_args;
	}
}
