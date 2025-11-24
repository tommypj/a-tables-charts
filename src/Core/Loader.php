<?php
/**
 * Hooks Loader
 *
 * @package ATables\Core
 * @since 2.0.0
 */

namespace ATables\Core;

/**
 * Loader Class
 *
 * Registers all hooks for the plugin.
 */
class Loader {

    /**
     * Array of actions
     *
     * @var array
     */
    protected $actions = array();

    /**
     * Array of filters
     *
     * @var array
     */
    protected $filters = array();

    /**
     * Add action hook
     *
     * @param string $hook          Hook name.
     * @param object|string $component Component object or class name.
     * @param string $callback      Callback method.
     * @param int    $priority      Priority.
     * @param int    $accepted_args Number of arguments.
     */
    public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->actions[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args,
        );
    }

    /**
     * Add filter hook
     *
     * @param string $hook          Hook name.
     * @param object|string $component Component object or class name.
     * @param string $callback      Callback method.
     * @param int    $priority      Priority.
     * @param int    $accepted_args Number of arguments.
     */
    public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->filters[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args,
        );
    }

    /**
     * Register hooks with WordPress
     */
    public function run() {
        foreach ( $this->actions as $hook ) {
            add_action(
                $hook['hook'],
                $this->get_callback( $hook['component'], $hook['callback'] ),
                $hook['priority'],
                $hook['accepted_args']
            );
        }

        foreach ( $this->filters as $hook ) {
            add_filter(
                $hook['hook'],
                $this->get_callback( $hook['component'], $hook['callback'] ),
                $hook['priority'],
                $hook['accepted_args']
            );
        }
    }

    /**
     * Get callback for hook
     *
     * @param object|string $component Component.
     * @param string        $callback  Callback method.
     * @return array|callable
     */
    private function get_callback( $component, $callback ) {
        // If component is a string (class name), instantiate it
        if ( is_string( $component ) && class_exists( $component ) ) {
            $component = new $component();
        }

        return array( $component, $callback );
    }
}
