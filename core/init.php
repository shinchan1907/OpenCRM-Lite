<?php
/**
 * Core Initialization
 * Loads config, database, auth, plugins, themes
 */

// Load configuration
require_once __DIR__ . '/../config.php';

// Load core files
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/hooks.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

function init_app() {
    // Initialize database
    init_database();
    
    // Load plugins
    load_plugins();
    
    // Load theme
    load_theme();
    
    // Fire init hook
    do_action('init');
}

function load_plugins() {
    $plugins_dir = __DIR__ . '/../plugins';
    
    if (!is_dir($plugins_dir)) {
        return;
    }
    
    foreach (scandir($plugins_dir) as $plugin) {
        if ($plugin === '.' || $plugin === '..') {
            continue;
        }
        
        $plugin_path = $plugins_dir . '/' . $plugin;
        $plugin_file = $plugin_path . '/init.php';
        $plugin_json = $plugin_path . '/plugin.json';
        
        if (is_dir($plugin_path) && file_exists($plugin_file) && file_exists($plugin_json)) {
            $plugin_data = json_decode(file_get_contents($plugin_json), true);
            
            if ($plugin_data && ($plugin_data['active'] ?? true)) {
                require_once $plugin_file;
                do_action('plugin_loaded', $plugin, $plugin_data);
            }
        }
    }
}

function load_theme() {
    $theme = DEFAULT_THEME;
    $theme_path = __DIR__ . '/../themes/' . $theme;
    
    if (is_dir($theme_path)) {
        $theme_json = $theme_path . '/theme.json';
        if (file_exists($theme_json)) {
            $theme_data = json_decode(file_get_contents($theme_json), true);
            define('CURRENT_THEME_PATH', $theme_path);
            define('CURRENT_THEME_DATA', $theme_data);
            do_action('theme_loaded', $theme, $theme_data);
        }
    }
}
