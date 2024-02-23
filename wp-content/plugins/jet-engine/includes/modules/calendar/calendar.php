<?php
/**
 * Calendar widget module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Module_Calendar' ) ) {

	/**
	 * Define Jet_Engine_Module_Calendar class
	 */
	class Jet_Engine_Module_Calendar extends Jet_Engine_Module_Base {

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'calendar';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Calendar', 'jet-engine' );
		}

		/**
		 * Returns detailed information about current module for the dashboard page
		 * @return [type] [description]
		 */
		public function get_module_details() {
			return '<p>After activation, the <b>Calendar widget</b> will appear in the Elementor widget menu and the <b>Listing Calendar block</b> will appear in the Gutenberg editor.</p>
					<p>With help of this widget, you can showcase posts from any CPT in the events calendar format.</p>
					<p>You will have to link with a Custom Post Type to show the events.</p>';
		}

		public function get_video_embed() {
			return 'https://www.youtube.com/embed/sm3qbH82kMM';
		}

		/**
		 * Returns array links to the module-related resources
		 * @return array
		 */
		public function get_module_links() {
			return array(
				array(
					'label' => 'JetEngine: How to Create a Dynamic Calendar',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-calendar-listing-functionality-how-to-add-a-dynamic-calendar/',
				),
				array(
					'label' => 'JetEngine: How to Hide the Calendar Widget if the Query Is Empty',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/listing-calendar-widgets-visibility-how-to-hide-the-widget-if-the-query-is-empty/',
				),
				array(
					'label' => 'JetSmartFilters: How to Filter Events in Calendar Widget',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/jetsmartfilters-how-to-filter-the-events-from-the-dynamic-calendar-widget-jetengine/',
				),
				array(
					'label' => 'JetSmartFilters: How to Use Filters with Listing Grid and Calendar widgets',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/jetsmartfilters-how-to-use-the-jetsmartfilters-widgets-with-the-listing-grid-and-listing-calendar-widgets/',
				),
				array(
					'label'    => 'How to create Elementor event calendar',
					'url'      => 'https://www.youtube.com/watch?v=sm3qbH82kMM',
					'is_video' => true,
				),

			);
		}

		/**
		 * Get allowed group keys
		 *
		 * @return array
		 */
		public function get_calendar_group_keys( $blocks = false ) {
			
			$keys = apply_filters( 'jet-engine/listing/calendar/group-keys', array(
				'post_date' => __( 'Post publication date', 'jet-engine' ),
				'post_mod'  => __( 'Post modification date', 'jet-engine' ),
				'meta_date' => __( 'Date from custom field', 'jet-engine' ),
				'item_date' => __( 'Query Item creation date (depends on used query)', 'jet-engine' ),
			) );

			if ( $blocks ) {
				
				foreach ( $keys as $key => $value ) {
					$keys[ $key ] = array(
						'value' => $key,
						'label' => $value,
					);
				}

				$keys = array_values( $keys );
			}

			return $keys;
		}

		/**
		 * Module init
		 *
		 * @return void
		 */
		public function module_init() {

			add_action( 'jet-engine/elementor-views/widgets/register', array( $this, 'register_calendar_widget' ), 20, 2 );
			
			add_action( 'wp_ajax_jet_engine_calendar_get_month', array( $this, 'calendar_get_month' ) );
			add_action( 'wp_ajax_nopriv_jet_engine_calendar_get_month', array( $this, 'calendar_get_month' ) );

			// Register render class.
			add_action( 'jet-engine/listings/renderers/registered', array( $this, 'register_render_class' ) );

			// Blocks Integration
			add_action( 'jet-engine/blocks-views/register-block-types', array( $this, 'register_block_types' ) );
			add_filter( 'jet-engine/blocks-views/editor/config',        array( $this, 'add_editor_config' ) );

			add_action( 'jet-engine/register-macros', array( $this, 'register_macros' ) );

			add_filter( 'jet-smart-filters/query/allowed-ajax-actions', array( $this, 'allow_month_action' ) );

			// Bricks Integration
			require jet_engine()->plugin_path( 'includes/modules/calendar/bricks-views/manager.php' );
			new Jet_Engine\Modules\Calendar\Bricks_Views\Manager();

			require jet_engine()->plugin_path( 'includes/modules/calendar/advanced-date-field/manager.php' );
			Jet_Engine_Advanced_Date_Field::instance();

		}

		/**
		 * Allow month action
		 *
		 * @return array
		 */
		public function allow_month_action( $allowed_actions = array() ) {

			$allowed_actions[] = 'jet_engine_calendar_get_month';
			return $allowed_actions;

		}

		/**
		 * Check if get month request is processed
		 *
		 * @return boolean [description]
		 */
		public function is_month_request() {

			if ( isset( $_REQUEST['action'] ) && 'jet_engine_calendar_get_month' === $_REQUEST['action'] ) {
				return true;
			}

			if ( isset( $_REQUEST['jet_engine_action'] ) && 'jet_engine_calendar_get_month' === $_REQUEST['jet_engine_action'] ) {
				return true;
			}

			return false;

		}

		public function register_macros() {
			require jet_engine()->modules->modules_path( 'calendar/queried-month-macros.php' );
			new Jet_Engine_Queried_Month_Macros();
		}

		/**
		 * Ajax handler for months navigation
		 *
		 * @return [type] [description]
		 */
		public function calendar_get_month() {

			ob_start();

			$current_post = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : false;
			$settings     = isset( $_REQUEST['settings'] ) ? $_REQUEST['settings'] : array();

			if ( $current_post ) {
				global $post;
				$post = get_post( $current_post );
				jet_engine()->listings->data->set_current_object( $post );
			}

			if ( jet_engine()->has_elementor() ) {
				Elementor\Plugin::instance()->frontend->start_excerpt_flag( null );
			}

			$instance = jet_engine()->listings->get_render_instance( 'listing-calendar', $settings );
			$instance->render_content();

			wp_send_json_success( array(
				'content' => ob_get_clean(),
			) );

		}

		/**
		 * Register calendar widget
		 *
		 * @param $widgets_manager
		 * @param $elementor_views
		 *
		 * @return void
		 */
		public function register_calendar_widget( $widgets_manager, $elementor_views ) {

			$elementor_views->register_widget(
				jet_engine()->modules->modules_path( 'calendar/widget.php' ),
				$widgets_manager,
				'Elementor\Jet_Listing_Calendar_Widget'
			);

		}

		/**
		 * Register render class.
		 *
		 * @param object $listings
		 */
		public function register_render_class( $listings ) {

			require jet_engine()->modules->modules_path( 'calendar/query.php' );
			new Jet_Engine_Calendar_Query();

			$listings->register_render_class(
				'listing-calendar',
				array(
					'class_name' => 'Jet_Listing_Render_Calendar',
					'path'       => jet_engine()->modules->modules_path( 'calendar/render.php' ),
					'deps'       => array( 'listing-grid' ),
				)
			);
		}

		/**
		 * Register block types
		 *
		 * @param  object $blocks_types
		 * @return void
		 */
		public function register_block_types( $blocks_types ) {
			require jet_engine()->modules->modules_path( 'calendar/block.php' );

			$calendar_type = new Jet_Listing_Calendar_Block_Type();

			$blocks_types->register_block_type( $calendar_type );
		}

		/**
		 * Add editor config.
		 *
		 * @param  array $config
		 * @return array
		 */
		public function add_editor_config( $config = array() ) {

			$config['atts']['listingCalendar'] = jet_engine()->blocks_views->block_types->get_block_atts( 'listing-calendar' );

			return $config;
		}

		/**
		 * Returns path to module template file.
		 *
		 * @param $name
		 *
		 * @return string|bool
		 */
		public function get_template( $name ) {

			$template = jet_engine()->get_template( 'calendar/' . $name ); // for back-compatibility

			if ( $template ) {
				return $template;
			}

			return jet_engine()->modules->modules_path( 'calendar/templates/' . $name );
		}

	}

}
