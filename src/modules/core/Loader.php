<?php
/**
 * Hook Loader Class
 *
 * Registers all actions and filters for the plugin.
 * Maintains arrays of all hooks that are registered throughout
 * the plugin, and register them with WordPress.
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

namespace ATablesCharts\Core;

/**
 * Loader Class
 *
 * Responsibilities:
 * - Register actions with WordPress
 * - Register filters with WordPress
 * - Maintain hooks registry
 * - Execute all registered hooks
 */
class Loader {

	/**
	 * Array of actions registered with WordPress
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $actions;

	/**
	 * Array of filters registered with WordPress
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $filters;

	/**
	 * Initialize the collections for hooks
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add a new action to the collection
	 *
	 * @since 1.0.0
	 * @param string $hook          The name of the WordPress action.
	 * @param object $component     Reference to the class instance.
	 * @param string $callback      The name of the function to call.
	 * @param int    $priority      Priority at which the function should be called (default: 10).
	 * @param int    $accepted_args Number of arguments the function accepts (default: 1).
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection
	 *
	 * @since 1.0.0
	 * @param string $hook          The name of the WordPress filter.
	 * @param object $component     Reference to the class instance.
	 * @param string $callback      The name of the function to call.
	 * @param int    $priority      Priority at which the function should be called (default: 10).
	 * @param int    $accepted_args Number of arguments the function accepts (default: 1).
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Utility function that adds hooks to the collection
	 *
	 * @since 1.0.0
	 * @param array  $hooks         The collection of hooks.
	 * @param string $hook          The name of the WordPress hook.
	 * @param object $component     Reference to the class instance.
	 * @param string $callback      The name of the function to call.
	 * @param int    $priority      Priority at which the function should be called.
	 * @param int    $accepted_args Number of arguments the function accepts.
	 * @return array The collection of hooks with the new hook added.
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
	 * Register all hooks with WordPress
	 *
	 * Loops through all registered actions and filters and registers them
	 * with WordPress using the add_action() and add_filter() functions.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		// Register all actions.
		foreach ( $this->actions as $hook ) {
			add_action(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}

		// Register all filters.
		foreach ( $this->filters as $hook ) {
			add_filter(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}
	}

	/**
	 * Get all registered actions
	 *
	 * Useful for debugging and testing.
	 *
	 * @since 1.0.0
	 * @return array All registered actions
	 */
	public function get_actions() {
		return $this->actions;
	}

	/**
	 * Get all registered filters
	 *
	 * Useful for debugging and testing.
	 *
	 * @since 1.0.0
	 * @return array All registered filters
	 */
	public function get_filters() {
		return $this->filters;
	}

	/**
	 * Remove an action from the collection
	 *
	 * @since 1.0.0
	 * @param string $hook      The name of the WordPress action.
	 * @param object $component Reference to the class instance.
	 * @param string $callback  The name of the function to call.
	 * @return bool True if action was removed, false otherwise
	 */
	public function remove_action( $hook, $component, $callback ) {
		return $this->remove( $this->actions, $hook, $component, $callback );
	}

	/**
	 * Remove a filter from the collection
	 *
	 * @since 1.0.0
	 * @param string $hook      The name of the WordPress filter.
	 * @param object $component Reference to the class instance.
	 * @param string $callback  The name of the function to call.
	 * @return bool True if filter was removed, false otherwise
	 */
	public function remove_filter( $hook, $component, $callback ) {
		return $this->remove( $this->filters, $hook, $component, $callback );
	}

	/**
	 * Utility function to remove hooks from the collection
	 *
	 * @since 1.0.0
	 * @param array  $hooks     The collection of hooks.
	 * @param string $hook      The name of the WordPress hook.
	 * @param object $component Reference to the class instance.
	 * @param string $callback  The name of the function to call.
	 * @return bool True if hook was removed, false otherwise
	 */
	private function remove( &$hooks, $hook, $component, $callback ) {
		foreach ( $hooks as $key => $registered_hook ) {
			if ( $registered_hook['hook'] === $hook &&
				 $registered_hook['component'] === $component &&
				 $registered_hook['callback'] === $callback ) {
				unset( $hooks[ $key ] );
				return true;
			}
		}
		return false;
	}
}
