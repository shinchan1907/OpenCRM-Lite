<?php
/**
 * Shared Helper Functions
 */

function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function redirect($url) {
    if (strpos($url, 'http') !== 0) {
        $url = rtrim(SITE_URL, '/') . '/' . ltrim($url, '/');
    }
    header("Location: $url");
    exit;
}

function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function load_view($view, $data = []) {
    // Check if theme has override
    $theme_view = CURRENT_THEME_PATH . '/' . $view . '.php';
    $default_view = __DIR__ . '/../views/' . $view . '.php';
    
    if (defined('CURRENT_THEME_PATH') && file_exists($theme_view)) {
        $view_file = $theme_view;
    } elseif (file_exists($default_view)) {
        $view_file = $default_view;
    } else {
        throw new Exception("View not found: $view");
    }
    
    extract($data);
    ob_start();
    include $view_file;
    $content = ob_get_clean();
    
    return $content;
}

function render_view($view, $data = []) {
    echo load_view($view, $data);
}

function get_setting($key, $default = null) {
    static $settings = null;
    
    if ($settings === null) {
        $db = get_db();
        $results = $db->fetchAll("SELECT key, value FROM settings");
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['key']] = $row['value'];
        }
    }
    
    return $settings[$key] ?? $default;
}

function set_setting($key, $value) {
    $db = get_db();
    $db->query(
        "INSERT OR REPLACE INTO settings (key, value, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP)",
        [$key, $value]
    );
}

function format_currency($amount) {
    return '$' . number_format($amount, 2);
}

function format_date($date) {
    return date('M j, Y', strtotime($date));
}

function generate_invoice_number() {
    $db = get_db();
    $last_invoice = $db->fetchOne("SELECT invoice_number FROM invoices ORDER BY id DESC LIMIT 1");
    
    if ($last_invoice) {
        $last_number = intval(substr($last_invoice['invoice_number'], 4));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1001;
    }
    
    return 'INV-' . str_pad($new_number, 4, '0', STR_PAD_LEFT);
}

function upload_file($file, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']) {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['error' => 'No file uploaded'];
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowed_types)) {
        return ['error' => 'File type not allowed'];
    }
    
    $filename = uniqid() . '.' . $extension;
    $upload_path = UPLOADS_PATH . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return ['success' => true, 'filename' => $filename, 'path' => $upload_path];
    }
    
    return ['error' => 'Upload failed'];
}

function log_error($message, $context = []) {
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'message' => $message,
        'context' => $context
    ];
    
    file_put_contents(
        LOGS_PATH . '/error-' . date('Y-m-d') . '.log',
        json_encode($log_entry) . "\n",
        FILE_APPEND | LOCK_EX
    );
}
