<?php
/**
 * OpenCRM Lite - Entry Point
 * Bootstraps the entire application
 */

// Start session
session_start();

// Include core initialization
require_once __DIR__ . '/core/init.php';

// Include router
require_once __DIR__ . '/router.php';

// Initialize the application
init_app();

// Route the request
route_request();
