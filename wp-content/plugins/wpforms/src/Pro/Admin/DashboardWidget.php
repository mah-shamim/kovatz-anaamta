<?php

namespace WPForms\Pro\Admin;

/**
 * Dashboard Widget shows a chart and the form entries stats in WP Dashboard.
 *
 * @package    WPForms\Admin
 * @author     WPForms
 * @since      1.5.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2018, WPForms LLC
 */
class DashboardWidget {

	/**
	 * Widget settings.
	 *
	 * @var array
	 */
	public $settings;

	/**
	 * Constructor.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {

		// This widget should be displayed for certain high-level users only.
		if ( ! wpforms_current_user_can() ) {
			return;
		}

		if ( ! apply_filters( 'wpforms_admin_dashboardwidget', '__return_true' ) ) {
			return;
		}

		$this->settings();
		$this->hooks();
	}

	/**
	 * Filterable widget settings.
	 *
	 * @since 1.5.0
	 */
	public function settings() {

		$this->settings = array(

			// Number of forms to display in the forms list before "Show More" button appears.
			'forms_list_number_to_display'     => \apply_filters( 'wpforms_dash_widget_forms_list_number_to_display', 5 ),

			// Allow results caching to reduce DB load.
			'allow_data_caching'               => \apply_filters( 'wpforms_dash_widget_allow_data_caching', true ),

			// PHP DateTime supported string (http://php.net/manual/en/datetime.formats.php).
			'date_end_str'                     => \apply_filters( 'wpforms_dash_widget_date_end_str', 'yesterday' ),

			// Transient lifetime in seconds. Defaults to the end of a current day.
			'transient_lifetime'               => \apply_filters( 'wpforms_dash_widget_transient_lifetime', \strtotime( 'tomorrow' ) - \time() ),

			// Determines if the days with no entries should appear on a chart. Once switched, the effect applies after cache expiration.
			'display_chart_empty_entries'      => \apply_filters( 'wpforms_dash_widget_display_chart_empty_entries', true ),

			// Determines if the forms with no entries should appear in a forms list. Once switched, the effect applies after cache expiration.
			'display_forms_list_empty_entries' => \apply_filters( 'wpforms_dash_widget_display_forms_list_empty_entries', true ),
		);
	}

	/**
	 * Widget hooks.
	 *
	 * @since 1.5.0
	 */
	public function hooks() {

		\add_action( 'admin_enqueue_scripts', array( $this, 'widget_scripts' ) );

		\add_action( 'wp_dashboard_setup', array( $this, 'widget_register' ) );

		\add_action( 'wp_ajax_wpforms_dash_widget_get_chart_data', array( $this, 'get_chart_data_ajax' ) );
		\add_action( 'wp_ajax_wpforms_dash_widget_get_forms_list', array( $this, 'get_forms_list_ajax' ) );
		\add_action( 'wp_ajax_wpforms_dash_widget_save_widget_meta', array( $this, 'save_widget_meta_ajax' ) );

		\add_action( 'wpforms_create_form', __CLASS__ . '::clear_widget_cache' );
		\add_action( 'wpforms_save_form', __CLASS__ . '::clear_widget_cache' );
		\add_action( 'wpforms_delete_form', __CLASS__ . '::clear_widget_cache' );
	}

	/**
	 * Load widget-specific scripts.
	 *
	 * @since 1.5.0
	 */
	public function widget_scripts() {

		$screen = \get_current_screen();
		if ( ! isset( $screen->id ) || 'dashboard' !== $screen->id ) {
			return;
		}

		$min = \wpforms_get_min_suffix();

		\wp_enqueue_style(
			'wpforms-dashboard-widget',
			\WPFORMS_PLUGIN_URL . "assets/css/dashboard-widget{$min}.css",
			array(),
			\WPFORMS_VERSION
		);

		\wp_enqueue_script(
			'wpforms-moment',
			\WPFORMS_PLUGIN_URL . 'assets/js/moment.min.js',
			array(),
			'2.22.2',
			true
		);

		\wp_enqueue_script(
			'wpforms-chart',
			\WPFORMS_PLUGIN_URL . 'assets/js/chart.min.js',
			array( 'wpforms-moment' ),
			'2.7.2',
			true
		);

		\wp_enqueue_script(
			'wpforms-dashboard-widget',
			\WPFORMS_PLUGIN_URL . "pro/assets/js/admin/dashboard-widget{$min}.js",
			array( 'jquery', 'wpforms-chart' ),
			\WPFORMS_VERSION,
			true
		);

		\wp_localize_script(
			'wpforms-dashboard-widget',
			'wpforms_dashboard_widget',
			array(
				'nonce'            => \wp_create_nonce( 'wpforms_dash_widget_nonce' ),
				'empty_chart_html' => $this->get_empty_chart_html(),
				'chart_data'       => $this->get_entries_count_by(
					'date',
					$this->widget_meta( 'get', 'timespan' ),
					$this->widget_meta( 'get', 'active_form_id' )
				),
				'show_more_html'   => \esc_html__( 'Show More', 'wpforms' ) . '<span class="dashicons dashicons-arrow-down"></span>',
				'show_less_html'   => \esc_html__( 'Show Less', 'wpforms' ) . '<span class="dashicons dashicons-arrow-up"></span>',
				'i18n'             => array(
					'total_entries' => \esc_html__( 'Total Entries', 'wpforms' ),
					'entries'       => \esc_html__( 'Entries', 'wpforms' ),
				),
			)
		);
	}

	/**
	 * Register the widget.
	 *
	 * @since 1.5.0
	 */
	public function widget_register() {

		global $wp_meta_boxes;

		$widget_key = 'wpforms_reports_widget_pro';

		\wp_add_dashboard_widget(
			$widget_key,
			\esc_html__( 'WPForms', 'wpforms' ),
			array( $this, 'widget_content' )
		);

		// Attempt to place the widget at the top.
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		$widget_instance  = array( $widget_key => $normal_dashboard[ $widget_key ] );
		unset( $normal_dashboard[ $widget_key ] );
		$sorted_dashboard = \array_merge( $widget_instance, $normal_dashboard );

		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}

	/**
	 * Load widget content.
	 *
	 * @since 1.5.0
	 */
	public function widget_content() {

		$forms = \wpforms()->form->get( '', array( 'fields' => 'ids' ) );

		echo '<div class="wpforms-dash-widget wpforms-pro">';

		if ( empty( $forms ) ) {
			$this->widget_content_no_forms_html();
		} else {
			$this->widget_content_html();
		}

		$plugins          = \get_plugins();
		$hide_recommended = $this->widget_meta( 'get', 'hide_recommended_block' );

		if (
			! \array_key_exists( 'google-analytics-for-wordpress/googleanalytics.php', $plugins ) &&
			! \array_key_exists( 'google-analytics-premium/googleanalytics-premium.php', $plugins ) &&
			! empty( $forms ) &&
			! $hide_recommended
		) {
			$this->recommended_plugin_block_html();
		}

		echo '</div><!-- .wpforms-dash-widget -->';
	}

	/**
	 * Widget content HTML if a user has no forms.
	 *
	 * @since 1.5.0
	 */
	public function widget_content_no_forms_html() {

		$create_form_url = \add_query_arg( 'page', 'wpforms-builder', \admin_url( 'admin.php' ) );
		$learn_more_url  = 'https://wpforms.com/docs/creating-first-form/?utm_source=WordPress&utm_medium=link&utm_campaign=plugin&utm_content=dashboardwidget';

		?>
		<div class="wpforms-dash-widget-block wpforms-dash-widget-block-no-forms">
			<img class="wpforms-dash-widget-block-sullie-logo" src="<?php echo \esc_url( WPFORMS_PLUGIN_URL . 'assets/images/sullie.png' ); ?>" alt="<?php \esc_attr_e( 'Sullie the WPForms mascot', 'wpforms' ); ?>">
			<h2><?php \esc_html_e( 'Create Your First Form to Start Collecting Leads', 'wpforms' ); ?></h2>
			<p><?php \esc_html_e( 'You can use WPForms to build contact forms, surveys, payment forms, and more with just a few clicks.', 'wpforms' ); ?></p>
			<a href="<?php echo \esc_url( $create_form_url ); ?>" class="button button-primary">
				<?php \esc_html_e( 'Create Your Form', 'wpforms' ); ?>
			</a>
			<a href="<?php echo \esc_url( $learn_more_url ); ?>" class="button" target="_blank" rel="noopener noreferrer">
				<?php \esc_html_e( 'Learn More', 'wpforms' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Widget content HTML.
	 *
	 * @since 1.5.0
	 */
	public function widget_content_html() {

		$timespan = $this->widget_meta( 'get', 'timespan' );
		$active_form_id      = $this->widget_meta( 'get', 'active_form_id' );

		$title = empty( $active_form_id ) ? \esc_html__( 'Total Entries', 'wpforms' ) : \get_the_title( $active_form_id );

		?>
		<div class="wpforms-dash-widget-chart-block-container">

			<div class="wpforms-dash-widget-block">
				<h3 id="wpforms-dash-widget-chart-title">
					<?php echo \esc_html( $title ); ?>
				</h3>
				<button type="button" id="wpforms-dash-widget-reset-chart" class="wpforms-dash-widget-reset-chart" title="<?php \esc_html_e( 'Reset chart to display all forms', 'wpforms' ); ?>"
					<?php echo empty( $active_form_id ) ? 'style="display: none;"' : ''; ?>>
					<span class="dashicons dashicons-dismiss"></span>
				</button>
			</div>

			<div class="wpforms-dash-widget-block wpforms-dash-widget-chart-block">
				<canvas id="wpforms-dash-widget-chart" width="400" height="300"></canvas>
				<div class="wpforms-dash-widget-overlay"></div>
			</div>

		</div>

		<div class="wpforms-dash-widget-block">
			<h3><?php \esc_html_e( 'Total Entries by Form', 'wpforms' ); ?></h3>
			<select id="wpforms-dash-widget-timespan" class="wpforms-dash-widget-select-timespan" title="<?php \esc_html_e( 'Select timespan', 'wpforms' ); ?>"
				<?php echo ! empty( $active_form_id ) ? 'data-active-form-id="' . \absint( $active_form_id ) . '"' : ''; ?>>
				<?php $this->timespan_options_html( $this->get_timespan_options() ); ?>
			</select>
		</div>

		<div id="wpforms-dash-widget-forms-list-block" class="wpforms-dash-widget-block wpforms-dash-widget-forms-list-block">
			<?php $this->forms_list_block( $timespan ); ?>
		</div>
		<?php
	}

	/**
	 * Timespan select options HTML.
	 *
	 * @since 1.5.0
	 *
	 * @param array  $options Timespan options (in days).
	 * @param string $meta    Widget meta name to get user saved timespan from.
	 */
	public function timespan_options_html( $options ) {

		$timespan = $this->widget_meta( 'get', 'timespan' );

		foreach ( $options as $option ) :
			?>
			<option value="<?php echo \absint( $option ); ?>" <?php \selected( $timespan, \absint( $option ) ); ?>>
				<?php /* translators: %d - Number of days. */ ?>
				<?php echo \esc_html( \sprintf( \_n( 'Last %d day', 'Last %d days', \absint( $option ), 'wpforms' ), \absint( $option ) ) ); ?>
			</option>
			<?php
		endforeach;
	}

	/**
	 * Forms list block.
	 *
	 * @since 1.5.0
	 *
	 * @param int $days Timespan (in days) to fetch the data for.
	 */
	public function forms_list_block( $days ) {

		$forms = $this->get_entries_count_by( 'form', $days );

		if ( empty( $forms ) ) {
			$this->forms_list_block_empty_html();
		} else {
			$this->forms_list_block_html( $forms );
		}
	}

	/**
	 * Empty forms list block HTML.
	 *
	 * @since 1.5.0
	 */
	public function forms_list_block_empty_html() {

		?>
		<p class="wpforms-error wpforms-error-no-data-forms-list">
			<?php \esc_html_e( 'No entries for selected period', 'wpforms' ); ?>
		</p>
		<?php
	}

	/**
	 * Forms list block HTML.
	 *
	 * @since 1.5.0
	 *
	 * @param array $forms Forms to display in the list.
	 */
	public function forms_list_block_html( $forms ) {

		// Number of forms to display in the forms list before "Show More" button appears.
		$show_forms = $this->settings['forms_list_number_to_display'];

		?>
		<table id="wpforms-dash-widget-forms-list-table" cellspacing="0">
			<?php
			foreach ( \array_values( $forms ) as $key => $form ) :
				if ( ! \is_array( $form ) ) {
					continue;
				}
				if ( ! isset( $form['form_id'], $form['title'], $form['count'], $form['edit_url'] ) ) {
					continue;
				}
				?>
				<tr <?php echo $key >= $show_forms ? 'class="wpforms-dash-widget-forms-list-hidden-el"' : ''; ?> data-form-id="<?php echo \absint( $form['form_id'] ); ?>">
					<td><span class="wpforms-dash-widget-form-title"><?php echo \esc_html( $form['title'] ); ?></span></td>
					<td><a href="<?php echo \esc_url( $form['edit_url'] ); ?>"><?php echo \absint( $form['count'] ); ?></a></td>
					<td class="graph">
						<?php if ( \absint( $form['count'] ) > 0 ) : ?>
						<button type="button" class="wpforms-dash-widget-single-chart-btn" title="<?php \esc_html_e( 'Display only this form data on a chart', 'wpforms' ); ?>"></button>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>

		<?php if ( \count( $forms ) > $show_forms ) : ?>
			<button type="button" id="wpforms-dash-widget-forms-more" class="wpforms-dash-widget-forms-more" title="<?php \esc_html_e( 'Show all forms', 'wpforms' ); ?>">
				<?php \esc_html_e( 'Show More', 'wpforms' ); ?> <span class="dashicons dashicons-arrow-down"></span>
			</button>
		<?php endif; ?>

		<?php
	}

	/**
	 * Recommended plugin block HTML.
	 *
	 * @since 1.5.0
	 */
	public function recommended_plugin_block_html() {

		$install_mi_url = \wp_nonce_url(
			\self_admin_url( 'update.php?action=install-plugin&plugin=google-analytics-for-wordpress' ),
			'install-plugin_google-analytics-for-wordpress'
		);

		?>
		<div class="wpforms-dash-widget-recommended-plugin-block">
			<span class="wpforms-dash-widget-recommended-plugin">
				<span class="recommended"><?php \esc_html_e( 'Recommended Plugin:', 'wpforms' ); ?></span>
				<span>
					<b><?php \esc_html_e( 'MonsterInsights', 'wpforms' ); ?></b> <span class="sep">-</span>
					<a href="<?php echo \esc_url( $install_mi_url ); ?>"><?php \esc_html_e( 'Install', 'wpforms' ); ?></a> <span class="sep sep-vertical">&vert;</span>
					<a href="https://www.monsterinsights.com/?utm_source=wpformsplugin&utm_medium=link&utm_campaign=wpformsdashboardwidget"><?php \esc_html_e( 'Learn More', 'wpforms' ); ?></a>
				</span>
			</span>
			<button type="button" id="wpforms-dash-widget-dismiss-recommended-plugin-block" class="wpforms-dash-widget-dismiss-recommended-plugin-block" title="<?php \esc_html_e( 'Dismiss recommended plugin block', 'wpforms' ); ?>">
				<span class="dashicons dashicons-no-alt"></span>
			</button>
		</div>
		<?php
	}

	/**
	 * Get empty chart HTML.
	 *
	 * @since 1.5.0
	 */
	public function get_empty_chart_html() {

		\ob_start();
		?>
		<div class="wpforms-error wpforms-error-no-data-chart">
			<div class="wpforms-dash-widget-modal">
				<h2><?php \esc_html_e( 'No entries for selected period', 'wpforms' ); ?></h2>
				<p><?php \esc_html_e( 'Please select a different period or check back later.', 'wpforms' ); ?></p>
			</div>
		</div>
		<?php

		return \ob_get_clean();
	}

	/**
	 * Get timespan options for $element (in days).
	 *
	 * @since 1.5.0
	 * @deprecated 1.5.2
	 *
	 * @param string $element 'chart' or 'forms_list'.
	 * @return array
	 */
	public function get_timespan_options_for( $element ) {
		_deprecated_function( __METHOD__, '1.5.2 of WPForms plugin', 'get_timespan_options()' );
		return $this->get_timespan_options();
	}

	/**
	 * Get timespan options (in days).
	 *
	 * @since 1.5.2
	 *
	 * @return array
	 */
	public function get_timespan_options() {

		$default = array( 7, 30 );

		$options = $default;

		// Apply deprecated filters.
		if ( function_exists( 'apply_filters_deprecated' ) ) {
			$options = \apply_filters_deprecated( 'wpforms_dash_widget_chart_timespan_options', array( $options ), '5.0', 'wpforms_dash_widget_timespan_options' );
			$options = \apply_filters_deprecated( 'wpforms_dash_widget_forms_list_timespan_options', array( $options ), '5.0', 'wpforms_dash_widget_timespan_options' );
		} else {
			$options = \apply_filters( 'wpforms_dash_widget_chart_timespan_options', $options );
			$options = \apply_filters( 'wpforms_dash_widget_forms_list_timespan_options', $options );
		}

		if ( ! \is_array( $options ) ) {
			$options = $default;
		}

		$options = \apply_filters( 'wpforms_dash_widget_timespan_options', $options );
		if ( ! \is_array( $options ) ) {
			return array();
		}

		$options = \array_filter( $options, 'is_numeric' );

		return empty( $options ) ? $default : $options;
	}


	/**
	 * Get default timespan option for $element.
	 *
	 * @since 1.5.0
	 * @deprecated 1.5.2
	 *
	 * @param string $element 'chart' or 'forms_list'.
	 * @return int|null
	 */
	public function get_timespan_default_for( $element ) {
		_deprecated_function( __METHOD__, '1.5.2 of WPForms plugin', 'get_timespan_default()' );
		return $this->get_timespan_default();
	}

	/**
	 * Get default timespan option.
	 *
	 * @since 1.5.3
	 *
	 * @return int|null
	 */
	public function get_timespan_default() {

		$options = $this->get_timespan_options();
		$default = \reset( $options );
		if ( ! \is_numeric( $default ) ) {
			return null;
		}
		return $default;
	}

	/**
	 * Get/set a widget meta.
	 *
	 * @since 1.5.0
	 *
	 * @param string $action  'get' or 'set'.
	 * @param string $meta    Meta name.
	 * @param int    $value   Value to set.
	 *
	 * @return bool|int|mixed
	 */
	public function widget_meta( $action, $meta, $value = 0 ) {

		$allowed_actions = array( 'get', 'set' );

		if ( ! \in_array( $action, $allowed_actions, true ) ) {
			return false;
		}

		$defaults = array(
			'timespan'               => $this->get_timespan_default(),
			'active_form_id'         => 0,
			'hide_recommended_block' => 0,
		);

		if ( ! \array_key_exists( $meta, $defaults ) ) {
			return false;
		}

		$meta_key = 'wpforms_dash_widget_' . $meta;

		if ( 'get' === $action ) {
			$meta_value = \absint( \get_user_meta( \get_current_user_id(), $meta_key, true ) );
			// Returns default value from $defaults if $meta_value is empty.
			return empty( $meta_value ) ? $defaults[ $meta ] : $meta_value;
		}

		$value = \absint( $value );
		if ( 'set' === $action && ! empty( $value ) ) {
			return \update_user_meta( \get_current_user_id(), $meta_key, $value );
		}

		if ( 'set' === $action && empty( $value ) ) {
			return \delete_user_meta( \get_current_user_id(), $meta_key );
		}

		return false;
	}

	/**
	 * Get entries count grouped by $param.
	 * Main point of entry to fetch form entry count data from DB.
	 * Caches the result.
	 *
	 * @since 1.5.0
	 *
	 * @param string $param   'date' or 'form'.
	 * @param int    $days    Timespan (in days) to fetch the data for.
	 * @param int    $form_id Form ID to fetch the data for.
	 *
	 * @return array
	 */
	public function get_entries_count_by( $param, $days = 0, $form_id = 0 ) {

		$allowed_params = array( 'date', 'form' );

		if ( ! \in_array( $param, $allowed_params, true ) ) {
			return array();
		}

		// Allow results caching to reduce DB load.
		$allow_caching = $this->settings['allow_data_caching'];

		if ( $allow_caching ) {
			$transient_name  = 'wpforms_dash_widget_pro_entries_by_' . $param . '_' . $days;
			$transient_name .= ! empty( $form_id ) ? '_' . $form_id : '';
			$cache           = \get_transient( $transient_name );
			// Filter the cache to clear or alter its data.
			$cache = \apply_filters( 'wpforms_dash_widget_cached_data', $cache, $param, $days, $form_id );
		}

		// is_array() detects cached empty searches.
		if ( $allow_caching && \is_array( $cache ) ) {
			return $cache;
		}

		// PHP DateTime supported string (http://php.net/manual/en/datetime.formats.php).
		$date_end_str = $this->settings['date_end_str'];

		try {
			$date_end = new \DateTime( $date_end_str );
		} catch ( \Exception $e ) {
			return array();
		}

		try {
			$date_start = new \DateTime( $date_end_str );
		} catch ( \Exception $e ) {
			return array();
		}

		$date_end = $date_end->setTime( 23, 59, 59 );

		$date_start = $date_start->modify( '-' . \absint( $days ) . 'days' );
		$date_start = $date_start->setTime( 0, 0, 0 );

		switch ( $param ) {
			case 'date':
				$result = $this->get_entries_count_by_date_sql( $form_id, $date_start, $date_end );
				break;
			case 'form':
				$result = $this->get_entries_count_by_form_sql( $form_id, $date_start, $date_end );
				break;
			default:
				$result = array();
		}

		if ( $allow_caching ) {
			// Transient lifetime in seconds. Defaults to the end of a current day.
			$transient_lifetime = $this->settings['transient_lifetime'];
			\set_transient( $transient_name, $result, $transient_lifetime );
		}

		return $result;
	}

	/**
	 * Get entries count grouped by date.
	 * In most cases it's better to use `get_entries_count_by( 'date' )` instead.
	 * Doesn't cache the result.
	 *
	 * @since 1.5.0
	 *
	 * @param int       $form_id    Form ID to fetch the data for.
	 * @param \DateTime $date_start Start date for the search.
	 * @param \DateTime $date_end   End date for the search.
	 *
	 * @return array
	 */
	public function get_entries_count_by_date_sql( $form_id = 0, $date_start = null, $date_end = null ) {

		global $wpdb;

		$table_name   = \wpforms()->entry->table_name;
		$format       = 'Y-m-d H:i:s';
		$placeholders = array();

		$sql = "SELECT CAST(date AS DATE) as day, COUNT(entry_id) as count
				FROM {$table_name}
				WHERE 1=1";

		if ( ! empty( $form_id ) ) {
			$sql .= ' AND form_id = %d';
			$placeholders[] = $form_id;
		}

		if ( ! empty( $date_start ) ) {
			$sql .= ' AND date >= %s';
			$placeholders[] = $date_start->format( $format );
		}

		if ( ! empty( $date_end ) ) {
			$sql .= ' AND date <= %s';
			$placeholders[] = $date_end->format( $format );
		}

		$sql .= ' GROUP BY day;';

		if ( ! empty( $placeholders ) ) {
			$sql = $wpdb->prepare( $sql, $placeholders );
		}

		$results = $wpdb->get_results( $sql, \OBJECT_K );

		if ( empty( $results ) ) {
			return array();
		}

		// Determines if the days with no entries should appear on a chart. Once switched, the effect applies after cache expiration.
		if ( $this->settings['display_chart_empty_entries'] ) {
			$results = $this->fill_chart_empty_entries( $results, $date_start, $date_end );
		}

		return (array) $results;
	}

	/**
	 * Get entries count grouped by form.
	 * In most cases it's better to use `get_entries_count_by( 'form' )` instead.
	 * Doesn't cache the result.
	 *
	 * @since 1.5.0
	 *
	 * @param int       $form_id    Form ID to fetch the data for.
	 * @param \DateTime $date_start Start date for the search.
	 * @param \DateTime $date_end   End date for the search.
	 *
	 * @return array
	 */
	public function get_entries_count_by_form_sql( $form_id = 0, $date_start = null, $date_end = null ) {

		global $wpdb;

		$table_name = \wpforms()->entry->table_name;
		$format     = 'Y-m-d H:i:s';

		$sql = "SELECT form_id, COUNT(entry_id) as count
				FROM {$table_name}
				WHERE 1=1";

		if ( ! empty( $form_id ) ) {
			$sql .= ' AND form_id = %d';
			$placeholders[] = $form_id;
		}

		if ( ! empty( $date_start ) ) {
			$sql .= ' AND date >= %s';
			$placeholders[] = $date_start->format( $format );
		}

		if ( ! empty( $date_end ) ) {
			$sql .= ' AND date <= %s';
			$placeholders[] = $date_end->format( $format );
		}

		$sql .= 'GROUP BY form_id ORDER BY count DESC;';

		if ( ! empty( $placeholders ) ) {
			$sql = $wpdb->prepare( $sql, $placeholders );
		}

		$results = $wpdb->get_results( $sql, \OBJECT_K );

		// Determines if the forms with no entries should appear in a forms list. Once switched, the effect applies after cache expiration.
		if ( $this->settings['display_forms_list_empty_entries'] ) {
			return $this->fill_forms_list_empty_entries_form_data( $results );
		}

		return (array) $this->fill_forms_list_form_data( $results );
	}

	/**
	 * Fill DB results with empty entries where there's no data.
	 * Needed to correctly distribute labels and data on a chart.
	 *
	 * @since 1.5.0
	 *
	 * @param array     $results    DB results from `$wpdb->prepare()`.
	 * @param \DateTime $date_start Start date for the search.
	 * @param \DateTime $date_end   End date for the search.
	 *
	 * @return array
	 */
	public function fill_chart_empty_entries( $results, $date_start, $date_end ) {

		if ( ! \is_array( $results ) ) {
			return array();
		}

		$period = new \DatePeriod(
			$date_start,
			new \DateInterval( 'P1D' ),
			$date_end,
			\DatePeriod::EXCLUDE_START_DATE
		);

		foreach ( $period as $key => $value ) {
			/* @var \DateTime $value */
			$date = $value->format( 'Y-m-d' );
			if ( ! \array_key_exists( $date, $results ) ) {
				$results[ $date ] = array(
					'day'   => $date,
					'count' => 0,
				);
				continue;
			}

			// Mold an object to array to stay uniform.
			$results[ $date ] = (array) $results[ $date ];
		}

		\ksort( $results );

		return $results;
	}

	/**
	 * Fill a forms list with the data needed for a frontend display.
	 *
	 * @since 1.5.0
	 *
	 * @param array $results DB results from `$wpdb->prepare()`.
	 *
	 * @return array
	 */
	public function fill_forms_list_form_data( $results ) {

		if ( ! \is_array( $results ) ) {
			return array();
		}

		$processed = array();

		foreach ( $results as $form_id => $result ) {

			$form = \wpforms()->form->get( $form_id );

			if ( empty( $form ) ) {
				continue;
			}

			$data = $this->get_formatted_forms_list_form_data( $form, $results );

			if ( $data ) {
				$processed[ $form->ID ] = $data;
			}
		}

		return $processed;
	}

	/**
	 * Fill a forms list with the data needed for a frontend display.
	 * Includes forms with zero entries.
	 *
	 * @since 1.5.0
	 *
	 * @param array $results DB results from `$wpdb->prepare()`.
	 *
	 * @return array
	 */
	public function fill_forms_list_empty_entries_form_data( $results ) {

		if ( ! \is_array( $results ) ) {
			return array();
		}

		$forms = \wpforms()->form->get();

		if ( empty( $forms ) ) {
			return array();
		}

		$processed = array();

		foreach ( $forms as $form ) {

			$data = $this->get_formatted_forms_list_form_data( $form, $results );

			if ( $data ) {
				$processed[ $form->ID ] = $data;
			}
		}

		return \wp_list_sort( $processed, 'count', 'DESC' );
	}

	/**
	 * Get formatted form data for a forms list frontend display.
	 *
	 * @since 1.5.4
	 *
	 * @param \WP_Post $form    Form object.
	 * @param array    $results DB results from `$wpdb->prepare()`.
	 *
	 * @return array
	 */
	public function get_formatted_forms_list_form_data( $form, $results ) {

		if ( ! ( $form instanceof \WP_Post ) ) {
			return array();
		}

		$edit_url = \add_query_arg(
			array(
				'page'    => 'wpforms-entries',
				'view'    => 'list',
				'form_id' => \absint( $form->ID ),
			),
			\admin_url( 'admin.php' )
		);

		return array(
			'form_id'  => $form->ID,
			'count'    => isset( $results[ $form->ID ]->count ) ? \absint( $results[ $form->ID ]->count ) : 0,
			'title'    => $form->post_title,
			'edit_url' => $edit_url,
		);
	}


	/**
	 * Get the data for a chart using AJAX.
	 *
	 * @since 1.5.0
	 */
	public function get_chart_data_ajax() {

		\check_admin_referer( 'wpforms_dash_widget_nonce' );

		$days    = ! empty( $_POST['days'] ) ? \absint( $_POST['days'] ) : 0;
		$form_id = ! empty( $_POST['form_id'] ) ? \absint( $_POST['form_id'] ) : 0;

		$data = $this->get_entries_count_by( 'date', $days, $form_id );

		\wp_send_json( $data );
	}

	/**
	 * Get the data for a forms list using AJAX.
	 *
	 * @since 1.5.0
	 */
	public function get_forms_list_ajax() {

		\check_admin_referer( 'wpforms_dash_widget_nonce' );

		$days = ! empty( $_POST['days'] ) ? \absint( $_POST['days'] ) : 0;

		\ob_start();
		$this->forms_list_block( $days );
		\wp_send_json( \ob_get_clean() );
	}

	/**
	 * Save a widget meta for a current user using AJAX.
	 *
	 * @since 1.5.0
	 */
	public function save_widget_meta_ajax() {

		\check_admin_referer( 'wpforms_dash_widget_nonce' );

		$meta  = ! empty( $_POST['meta'] ) ? \sanitize_key( $_POST['meta'] ) : '';
		$value = ! empty( $_POST['value'] ) ? \absint( $_POST['value'] ) : 0;

		$this->widget_meta( 'set', $meta, $value );

		exit();
	}

	/**
	 * Clear dashboard widget cached data.
	 *
	 * @since 1.5.2
	 */
	public static function clear_widget_cache() {

		global $wpdb;

		$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%wpforms_dash_widget_pro_entries_by_%'" ); //phpcs:ignore
	}
}
