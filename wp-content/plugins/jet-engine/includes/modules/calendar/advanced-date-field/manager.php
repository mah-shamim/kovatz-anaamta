<?php
/**
 * Register Advanced date meta field type
 */

class Jet_Engine_Advanced_Date_Field {

	public $field_type = 'advanced-date';

	public $view;
	public $data;
	public $rest;

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		require_once jet_engine()->plugin_path( 'includes/modules/calendar/advanced-date-field/view.php' );
		require_once jet_engine()->plugin_path( 'includes/modules/calendar/advanced-date-field/data.php' );
		require_once jet_engine()->plugin_path( 'includes/modules/calendar/advanced-date-field/rest-api.php' );

		$this->view = new Jet_Engine_Advanced_Date_Field_View( $this->field_type );
		$this->data = new Jet_Engine_Advanced_Date_Field_Data( $this->field_type );
		$this->rest = new Jet_Engine_Advanced_Date_Field_Rest_API( $this->field_type );

		add_action(
			'jet-engine/callbacks/register',
			array( $this, 'register_advanced_date_callbacks' )
		);

		add_filter(
			'jet-engine/listing/dynamic-field/callback-args', 
			array( $this, 'add_field_name_to_callback_args' ), 
			10, 4 
		);

		add_filter(
			'jet-engine/listings/allowed-callbacks-args', 
			array( $this, 'modify_date_format_conditions' ), 
			10, 4
		);

		add_action(
			'jet-engine/meta-fields/enqueue-assets', 
			array( $this, 'add_editor_js' )
		);

	}

	/**
	 * Add Meta boxes editor JS file
	 */
	public function add_editor_js() {
		wp_enqueue_script(
			'jet-engine-advanced-date-meta-boxes',
			jet_engine()->plugin_url( 'includes/modules/calendar/assets/js/meta-boxes.js' ),
			array( 'jet-plugins' ),
			jet_engine()->get_version(),
			true
		);
	}

	/**
	 * Regsiter advanced date realted JetEngine callback(s)
	 * 
	 * @param  [type] $callbacks [description]
	 * @return [type]            [description]
	 */
	public function register_advanced_date_callbacks( $callbacks ) {
		$callbacks->register_callback( 
			'jet_engine_advanced_date_next', 
			__( 'Get next date', 'jet-engine' )
		);
	}

	/**
	 * Add jet_engine_advanced_date_next callback to date_format callback control conditions
	 * 
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function modify_date_format_conditions( $args = [] ) {
		$args['date_format']['condition']['filter_callback'][] = 'jet_engine_advanced_date_next';
		return $args;
	}

	/**
	 * Adjust jet_engine_advanced_date_next callback arguments before apply the callback
	 * 
	 * @param [type] $args     [description]
	 * @param [type] $callback [description]
	 * @param array  $settings [description]
	 * @param [type] $widget   [description]
	 */
	public function add_field_name_to_callback_args( $args, $callback, $settings = array(), $widget = null ) {

		if ( 'jet_engine_advanced_date_next' !== $callback ) {
			return $args;
		}

		$format = ! empty( $settings['date_format'] ) ? $settings['date_format'] : 'd F Y';
		$field  = ! empty( $settings['dynamic_field_post_meta_custom'] ) ? $settings['dynamic_field_post_meta_custom'] : false;

		if ( ! $field && isset( $settings['dynamic_field_post_meta'] ) ) {
			$field = ! empty( $settings['dynamic_field_post_meta'] ) ? $settings['dynamic_field_post_meta'] : false;
		}

		$args[] = $format;
		$args[] = $field;

		return $args;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return Jet_Engine
	 */
	public static function instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

}

/**
 * At the JetEngine callbacks support only functions 
 * so we need to register appropriate function for the callback
 */
function jet_engine_advanced_date_next( $result, $format = 'd F Y', $field = '' ) {

	$time      = time();
	$post_id   = jet_engine()->listings->data->get_current_object_id();
	$dates     = Jet_Engine_Advanced_Date_Field::instance()->data->get_dates( $post_id, $field );
	$next_date = false;

	if ( empty( $dates ) ) {
		return $result;
	}

	$found_index = false;

	foreach ( $dates as $index => $date ) {
		if ( $date > $time ) {
			$next_date = $date;
			$found_index = $index;
			break;
		}
	}

	$result_date = wp_date( $format, $next_date );
	$end_dates   = Jet_Engine_Advanced_Date_Field::instance()->data->get_end_dates( $post_id, $field );

	if ( ! empty( $end_dates ) && ! empty( $end_dates[ $index ] ) ) {
		
		$end_date  = wp_date( $format, $end_dates[ $index ] );
		$md_format = apply_filters( 
			'jet-engine/calendar/advanced-date/multiday-format', 
			'%1$s - %2$s', $result_date, $end_date
		);

		$result_date = sprintf( $md_format, $result_date, $end_date );

	}

	return $result_date;

}
