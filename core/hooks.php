<?php
/**
 * WordPress-style Plugin System
 * Implements add_action, do_action for extensibility
 */

class HookSystem {
    private static $actions = [];
    private static $filters = [];
    
    public static function add_action($hook, $function, $priority = 10) {
        self::$actions[$hook][$priority][] = $function;
        if (isset(self::$actions[$hook])) {
            ksort(self::$actions[$hook]);
        }
    }
    
    public static function do_action($hook, ...$args) {
        if (isset(self::$actions[$hook])) {
            foreach (self::$actions[$hook] as $priority => $functions) {
                foreach ($functions as $function) {
                    if (is_callable($function)) {
                        call_user_func_array($function, $args);
                    }
                }
            }
        }
    }
    
    public static function add_filter($hook, $function, $priority = 10) {
        self::$filters[$hook][$priority][] = $function;
        if (isset(self::$filters[$hook])) {
            ksort(self::$filters[$hook]);
        }
    }
    
    public static function apply_filters($hook, $value, ...$args) {
        if (isset(self::$filters[$hook])) {
            foreach (self::$filters[$hook] as $priority => $functions) {
                foreach ($functions as $function) {
                    if (is_callable($function)) {
                        $value = call_user_func_array($function, array_merge([$value], $args));
                    }
                }
            }
        }
        return $value;
    }
    
    public static function remove_action($hook, $function, $priority = 10) {
        if (isset(self::$actions[$hook][$priority])) {
            $key = array_search($function, self::$actions[$hook][$priority]);
            if ($key !== false) {
                unset(self::$actions[$hook][$priority][$key]);
            }
        }
    }
    
    public static function remove_filter($hook, $function, $priority = 10) {
        if (isset(self::$filters[$hook][$priority])) {
            $key = array_search($function, self::$filters[$hook][$priority]);
            if ($key !== false) {
                unset(self::$filters[$hook][$priority][$key]);
            }
        }
    }
}

// Global functions for easier access
function add_action($hook, $function, $priority = 10) {
    HookSystem::add_action($hook, $function, $priority);
}

function do_action($hook, ...$args) {
    HookSystem::do_action($hook, ...$args);
}

function add_filter($hook, $function, $priority = 10) {
    HookSystem::add_filter($hook, $function, $priority);
}

function apply_filters($hook, $value, ...$args) {
    return HookSystem::apply_filters($hook, $value, ...$args);
}

function remove_action($hook, $function, $priority = 10) {
    HookSystem::remove_action($hook, $function, $priority);
}

function remove_filter($hook, $function, $priority = 10) {
    HookSystem::remove_filter($hook, $function, $priority);
}
