<?php
/**
 * OpenCRM Lite Configuration
 */

// Database Configuration
define('DB_TYPE', 'sqlite'); // 'sqlite' or 'mysql'
define('DB_PATH', __DIR__ . '/storage/database.db'); // For SQLite
define('DB_HOST', 'localhost'); // For MySQL
define('DB_NAME', 'opencrm'); // For MySQL
define('DB_USER', 'root'); // For MySQL
define('DB_PASS', ''); // For MySQL

// Site Configuration
define('SITE_NAME', 'OpenCRM Lite');
define('SITE_URL', 'http://localhost:5000');
define('ADMIN_EMAIL', 'admin@opencrm.local');

// Security
define('SECRET_KEY', 'your-secret-key-change-this-in-production');
define('API_VERSION', 'v1');

// Storage Paths
define('STORAGE_PATH', __DIR__ . '/storage');
define('UPLOADS_PATH', STORAGE_PATH . '/uploads');
define('LOGS_PATH', STORAGE_PATH . '/logs');
define('CACHE_PATH', STORAGE_PATH . '/cache');

// Third-party API Keys (from environment)
define('ZOHO_CLIENT_ID', getenv('ZOHO_CLIENT_ID') ?: '');
define('ZOHO_CLIENT_SECRET', getenv('ZOHO_CLIENT_SECRET') ?: '');
define('CARTER_API_KEY', getenv('CARTER_API_KEY') ?: '');

// Default Theme
define('DEFAULT_THEME', 'modern-light');

// Create storage directories if they don't exist
$dirs = [STORAGE_PATH, UPLOADS_PATH, LOGS_PATH, CACHE_PATH];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}
