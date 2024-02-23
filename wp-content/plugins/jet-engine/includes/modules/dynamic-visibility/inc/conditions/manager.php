<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Manager {

	private $_conditions = array();

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_conditions' ), 20 );
	}

	/**
	 * Register conditions
	 *
	 * @return [type] [description]
	 */
	public function register_conditions() {

		$path = jet_engine()->modules->modules_path( 'dynamic-visibility/inc/conditions/' );

		require_once $path . 'base.php';
		require_once $path . 'user.php';
		require_once $path . 'user-not-logged.php';
		require_once $path . 'user-role.php';
		require_once $path . 'user-role-not.php';
		require_once $path . 'user-id.php';
		require_once $path . 'user-id-not.php';
		require_once $path . 'equal.php';
		require_once $path . 'not-equal.php';
		require_once $path . 'greater-than.php';
		require_once $path . 'greater-or-equal.php';
		require_once $path . 'less-than.php';
		require_once $path . 'less-or-equal.php';
		require_once $path . 'in-list.php';
		require_once $path . 'not-in-list.php';
		require_once $path . 'exists.php';
		require_once $path . 'not-exists.php';
		require_once $path . 'contains.php';
		require_once $path . 'not-contains.php';
		require_once $path . 'between.php';
		require_once $path . 'not-between.php';
		require_once $path . 'regexp.php';
		require_once $path . 'not-regexp.php';
		require_once $path . 'is-mobile.php';
		require_once $path . 'post-id.php';
		require_once $path . 'post-id-not.php';
		require_once $path . 'single-post-type.php';
		require_once $path . 'single-post-type-not.php';
		require_once $path . 'archive-post-type.php';
		require_once $path . 'archive-post-type-not.php';
		require_once $path . 'archive-tax.php';
		require_once $path . 'archive-tax-not.php';
		require_once $path . 'archive-search.php';
		require_once $path . 'archive-search-not.php';
		require_once $path . 'post-author.php';
		require_once $path . 'post-author-not.php';
		require_once $path . 'switcher-enabled.php';
		require_once $path . 'switcher-disabled.php';
		require_once $path . 'value-checked.php';
		require_once $path . 'value-not-checked.php';
		require_once $path . 'post-has-terms.php';
		require_once $path . 'post-has-not-terms.php';
		require_once $path . 'is-parent.php';
		require_once $path . 'is-not-parent.php';
		require_once $path . 'is-child-of.php';
		require_once $path . 'is-not-child-of.php';
		require_once $path . 'time-period.php';
		require_once $path . 'week-days.php';

		require_once $path . 'listing-even.php';
		require_once $path . 'listing-odd.php';
		require_once $path . 'listing-is-num.php';

		do_action( 'jet-engine/modules/dynamic-visibility/conditions/register', $this );

	}

	/**
	 * Condition instance
	 *
	 * @param  [type] $instance [description]
	 * @return [type]           [description]
	 */
	public function register_condition( $instance ) {
		$this->_conditions[ $instance->get_id() ] = $instance;
	}

	/**
	 * Returns registered conditions in id => name format
	 *
	 * @return [type] [description]
	 */
	public function get_conditions_for_options() {

		$result = array();

		foreach ( $this->_conditions as $id => $instance ) {
			$result[ $id ] = $instance->get_name();
		}

		return $result;

	}

	/**
	 * Returns registered conditions in id => name format
	 *
	 * @return [type] [description]
	 */
	public function get_grouped_conditions_for_options() {

		$result = apply_filters( 'jet-engine/modules/dynamic-visibility/conditions/groups', array(
			'general'    => array(
				'label'   => __( 'General', 'jet-engine' ),
				'options' => array(),
			),
			'jet-engine' => array(
				'label'   => __( 'JetEngine specific', 'jet-engine' ),
				'options' => array(),
			),
			'user'       => array(
				'label'   => __( 'User', 'jet-engine' ),
				'options' => array(),
			),
			'posts'      => array(
				'label'   => __( 'Posts', 'jet-engine' ),
				'options' => array(),
			),
			'date_time'  => array(
				'label'   => __( 'Date & Time', 'jet-engine' ),
				'options' => array(),
			),
			'listing'  => array(
				'label'   => __( 'Listing', 'jet-engine' ),
				'options' => array(),
			),
		) );

		foreach ( $this->_conditions as $id => $instance ) {

			$group = $instance->get_group();

			if ( ! $group ) {
				$group = 'general';
			}

			if ( empty( $result[ $group ] ) ) {
				$result[ $group ] = array(
					'label'   => $group,
					'options' => array(),
				);
			}

			$result[ $group ]['options'][ $id ] = $instance->get_name();

		}

		return array_values( $result );

	}

	/**
	 * Get conditions allowed for meta fields
	 *
	 * @return [type] [description]
	 */
	public function get_conditions_for_fields() {

		$result = array();

		foreach ( $this->_conditions as $id => $instance ) {
			if ( $instance->is_for_fields() ) {
				$result[] = $id;
			}
		}

		return $result;

	}

	/**
	 * Returns conditions list that is requires value detection
	 *
	 * @return [type] [description]
	 */
	public function get_conditions_with_value_detect() {

		$result = array();

		foreach ( $this->_conditions as $id => $instance ) {
			if ( $instance->need_value_detect() ) {
				$result[] = $id;
			}
		}

		return $result;

	}

	public function add_condition_specific_controls() {

		$result = array();

		foreach ( $this->_conditions as $id => $instance ) {

			$custom_controls = $instance->get_custom_controls();

			if ( empty( $custom_controls ) ) {
				continue;
			}

			foreach ( $custom_controls as $key => $control ) {

				if ( isset( $result[ $key ] ) ) {
					$result[ $key ]['condition']['jedv_condition'][] = $id;
					continue;
				}

				$control['condition'] = array(
					'jedv_condition' => array( $id ),
				);

				$result[ $key ] = $control;

			}

		}

		return $result;
	}

	/**
	 * Returns conditions list that is requires type detection
	 *
	 * @return [type] [description]
	 */
	public function get_conditions_with_type_detect() {

		$result = array();

		foreach ( $this->_conditions as $id => $instance ) {
			if ( $instance->need_type_detect() ) {
				$result[] = $id;
			}
		}

		return $result;

	}

	/**
	 * Get condition instance by ID
	 *
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function get_condition( $id ) {
		return isset( $this->_conditions[ $id ] ) ? $this->_conditions[ $id ] : false;
	}

}
