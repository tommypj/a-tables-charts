<?php
/**
 * Hooks Loader
 *
 * @package ATables\Core
 * @since 3.0.0
 */

namespace ATables\Core;

/**
 * Loader Class
 *
 * Registers all actions and filters
 */
class Loader {

    /**
     * The array of actions registered
     *
     * @var array
     */
    protected $actions;

    /**
     * The array of filters registered
     *
     * @var array
     */
    protected $filters;

    /**
     * Initialize collections
     */
    public function __construct() {
        $this->actions = array();
        $this->filters = array();
    }

    /**
     * Add a new action
     */
    public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
    }

    /**
     * Add a new filter
     */
    public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
    }

    /**
     * Add hook to collection
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
     * Register all hooks
     */
    public function run() {
        foreach ( $this->filters as $hook ) {
            add_filter(
                $hook['hook'],
                array( $hook['component'], $hook['callback'] ),
                $hook['priority'],
                $hook['accepted_args']
            );
        }

        foreach ( $this->actions as $hook ) {
            add_action(
                $hook['hook'],
                array( $hook['component'], $hook['callback'] ),
                $hook['priority'],
                $hook['accepted_args']
            );
        }
    }
}
