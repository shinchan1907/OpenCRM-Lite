<?php
/**
 * Simple Router - Maps URLs to controllers
 */

function route_request() {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = trim($uri, '/');
    
    // Check if installation is complete
    if (!defined('INSTALLATION_COMPLETE') && $uri !== 'install') {
        header('Location: /install');
        exit;
    }
    
    // Handle empty URI (home page)
    if (empty($uri)) {
        $uri = 'dashboard';
    }
    
    // API routes
    if (strpos($uri, 'api/') === 0) {
        require_once __DIR__ . '/core/api.php';
        handle_api_request();
        return;
    }
    
    // Webhook routes
    if (strpos($uri, 'webhook/') === 0) {
        require_once __DIR__ . '/core/webhook.php';
        handle_webhook_request();
        return;
    }
    
    // Parse route
    $parts = explode('/', $uri);
    $module = $parts[0] ?? 'dashboard';
    $action = $parts[1] ?? 'index';
    $id = $parts[2] ?? null;
    
    // Special routes
    if ($module === 'login') {
        if (!is_logged_in() || $action === 'logout') {
            require_once __DIR__ . '/modules/users/controller.php';
            $controller = new UsersController();
            if ($action === 'logout') {
                $controller->logout();
            } else {
                $controller->login();
            }
        } else {
            redirect('/dashboard');
        }
        return;
    }
    
    if ($module === 'install') {
        require_once __DIR__ . '/install.php';
        return;
    }
    
    // Check authentication for protected routes
    if (!is_logged_in()) {
        redirect('/login');
        return;
    }
    
    // Route to module controller
    $controller_file = __DIR__ . "/modules/{$module}/controller.php";
    
    if (file_exists($controller_file)) {
        require_once $controller_file;
        
        $controller_class = ucfirst($module) . 'Controller';
        if (class_exists($controller_class)) {
            $controller = new $controller_class();
            
            if (method_exists($controller, $action)) {
                $controller->$action($id);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    } else {
        show_404();
    }
}

function show_404() {
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
    exit;
}
