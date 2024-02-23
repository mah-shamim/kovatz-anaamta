<?php
/**
 * Register all actions and filters for the plugin
 *
 * @package Temporary Login Without Password
 */

/**
 * Class Wp_Temporary_Login_Without_Password_Loader
 *
 * @package Temporary Login Without Password
 */
class Wp_Temporary_Login_Without_Password_Loader {

	/**
	 * All Actions to be added
	 *
	 * @var array $actions add actions.
	 *
	 * @since 1.0.0
	 */
	protected $actions;

	/**
	 * All filteres to be added
	 *
	 * @var array $filters Define filteres.
	 *
	 * @since 1.0.0
	 */
	protected $filters;

	/**
	 * Wp_Temporary_Login_Without_Password_Loader constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add action into $this->actions array
	 *
	 * @param string $hook Hook to be added.
	 * @param string $component component.
	 * @param string $callback class name.
	 * @param int    $priority priority.
	 * @param int    $accepted_args No of arguments.
	 *
	 * @since 1.0
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add filters into $this->filteres array
	 *
	 * @param sting  $hook Hook to be added.
	 * @param string $component component.
	 * @param string $callback class name.
	 * @param int    $priority priority.
	 * @param int    $accepted_args No of arguments.
	 *
	 * @since 1.0
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Collect Hooks
	 *
	 * @param array  $hooks Hooks Array.
	 * @param string $hook hook.
	 * @param string $component component.
	 * @param string $callback class name.
	 * @param int    $priority Priority.
	 * @param int    $accepted_args No of arguments.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * Add Filters.
	 *
	 * @since 1.0
	 */
	public function run() {

		foreach ( $this->filters as $hook ) {
			add_filter(
				$hook['hook'], array(
					$hook['component'],
					$hook['callback'],
				), $hook['priority'], $hook['accepted_args']
			);
		}

		foreach ( $this->actions as $hook ) {
			add_action(
				$hook['hook'], array(
					$hook['component'],
					$hook['callback'],
				), $hook['priority'], $hook['accepted_args']
			);
		}
	}

}
