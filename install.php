<?php
/**
 * OpenCRM Lite Installation Script
 * Sets up the database, creates admin user, and initializes the system
 */

// Prevent direct access if already installed
if (file_exists(__DIR__ . '/config.php')) {
    $config_content = file_get_contents(__DIR__ . '/config.php');
    if (strpos($config_content, 'INSTALLATION_COMPLETE') !== false) {
        header('Location: /dashboard');
        exit;
    }
}

$step = $_GET['step'] ?? 1;
$errors = [];
$success = [];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 1:
            $errors = validateRequirements();
            if (empty($errors)) {
                $step = 2;
            }
            break;
            
        case 2:
            $errors = validateDatabaseConfig($_POST);
            if (empty($errors)) {
                $step = 3;
            }
            break;
            
        case 3:
            $errors = createAdminUser($_POST);
            if (empty($errors)) {
                $step = 4;
            }
            break;
            
        case 4:
            $errors = finalizeInstallation($_POST);
            if (empty($errors)) {
                $step = 5;
            }
            break;
    }
}

function validateRequirements() {
    $errors = [];
    
    // Check PHP version
    if (version_compare(PHP_VERSION, '7.4.0', '<')) {
        $errors[] = 'PHP 7.4 or higher is required. Current version: ' . PHP_VERSION;
    }
    
    // Check required extensions
    $required_extensions = ['pdo', 'pdo_sqlite', 'json', 'curl', 'openssl'];
    foreach ($required_extensions as $ext) {
        if (!extension_loaded($ext)) {
            $errors[] = "Required PHP extension '$ext' is not installed.";
        }
    }
    
    // Check file permissions
    $writable_dirs = [
        __DIR__ . '/storage',
        __DIR__ . '/storage/uploads',
        __DIR__ . '/storage/logs',
        __DIR__ . '/storage/cache'
    ];
    
    foreach ($writable_dirs as $dir) {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                $errors[] = "Cannot create directory: $dir";
            }
        }
        
        if (!is_writable($dir)) {
            $errors[] = "Directory is not writable: $dir";
        }
    }
    
    // Check if config.php is writable
    if (file_exists(__DIR__ . '/config.php') && !is_writable(__DIR__ . '/config.php')) {
        $errors[] = 'config.php file is not writable.';
    }
    
    return $errors;
}

function validateDatabaseConfig($data) {
    $errors = [];
    
    $db_type = $data['db_type'] ?? 'sqlite';
    $site_name = trim($data['site_name'] ?? '');
    $site_url = trim($data['site_url'] ?? '');
    
    if (empty($site_name)) {
        $errors[] = 'Site name is required.';
    }
    
    if (empty($site_url)) {
        $errors[] = 'Site URL is required.';
    } elseif (!filter_var($site_url, FILTER_VALIDATE_URL)) {
        $errors[] = 'Site URL must be a valid URL.';
    }
    
    if ($db_type === 'mysql') {
        $required_fields = ['db_host', 'db_name', 'db_user'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required for MySQL.';
            }
        }
        
        // Test MySQL connection
        if (empty($errors)) {
            try {
                $dsn = "mysql:host={$data['db_host']};dbname={$data['db_name']};charset=utf8mb4";
                $pdo = new PDO($dsn, $data['db_user'], $data['db_pass'] ?? '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                $errors[] = 'Database connection failed: ' . $e->getMessage();
            }
        }
    } else {
        // SQLite - check if we can create the database file
        $db_path = __DIR__ . '/storage/database.db';
        try {
            $pdo = new PDO('sqlite:' . $db_path);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $errors[] = 'Cannot create SQLite database: ' . $e->getMessage();
        }
    }
    
    // Save configuration if valid
    if (empty($errors)) {
        $config_content = generateConfigFile($data);
        if (!file_put_contents(__DIR__ . '/config.php', $config_content)) {
            $errors[] = 'Cannot write configuration file.';
        } else {
            // Initialize database
            require_once __DIR__ . '/config.php';
            require_once __DIR__ . '/core/db.php';
            
            try {
                init_database();
            } catch (Exception $e) {
                $errors[] = 'Database initialization failed: ' . $e->getMessage();
            }
        }
    }
    
    return $errors;
}

function createAdminUser($data) {
    $errors = [];
    
    $username = trim($data['admin_username'] ?? '');
    $email = trim($data['admin_email'] ?? '');
    $password = $data['admin_password'] ?? '';
    $password_confirm = $data['admin_password_confirm'] ?? '';
    
    // Validation
    if (empty($username)) {
        $errors[] = 'Admin username is required.';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Admin username must be at least 3 characters.';
    }
    
    if (empty($email)) {
        $errors[] = 'Admin email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Admin email must be a valid email address.';
    }
    
    if (empty($password)) {
        $errors[] = 'Admin password is required.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Admin password must be at least 6 characters.';
    }
    
    if ($password !== $password_confirm) {
        $errors[] = 'Password confirmation does not match.';
    }
    
    // Create admin user if valid
    if (empty($errors)) {
        require_once __DIR__ . '/config.php';
        require_once __DIR__ . '/core/db.php';
        
        try {
            $db = get_db();
            
            // Remove default admin user
            $db->query("DELETE FROM users WHERE username = 'admin'");
            
            // Create new admin user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $db->query(
                "INSERT INTO users (username, email, password, role, created_at, updated_at) VALUES (?, ?, ?, 'admin', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                [$username, $email, $hashed_password]
            );
            
        } catch (Exception $e) {
            $errors[] = 'Failed to create admin user: ' . $e->getMessage();
        }
    }
    
    return $errors;
}

function finalizeInstallation($data) {
    $errors = [];
    
    try {
        // Create sample data if requested
        if (isset($data['create_sample_data'])) {
            createSampleData();
        }
        
        // Mark installation as complete
        $config_path = __DIR__ . '/config.php';
        $config_content = file_get_contents($config_path);
        $config_content .= "\n\n// Installation completed\ndefine('INSTALLATION_COMPLETE', true);\n";
        
        if (!file_put_contents($config_path, $config_content)) {
            $errors[] = 'Cannot finalize installation.';
        }
        
        // Set final permissions
        chmod(__DIR__ . '/config.php', 0644);
        
    } catch (Exception $e) {
        $errors[] = 'Installation finalization failed: ' . $e->getMessage();
    }
    
    return $errors;
}

function generateConfigFile($data) {
    $secret_key = bin2hex(random_bytes(32));
    
    $config = '<?php
/**
 * OpenCRM Lite Configuration
 */

// Database Configuration
define(\'DB_TYPE\', \'' . ($data['db_type'] ?? 'sqlite') . '\');
';

    if (($data['db_type'] ?? 'sqlite') === 'mysql') {
        $config .= "define('DB_HOST', '" . addslashes($data['db_host']) . "');
define('DB_NAME', '" . addslashes($data['db_name']) . "');
define('DB_USER', '" . addslashes($data['db_user']) . "');
define('DB_PASS', '" . addslashes($data['db_pass'] ?? '') . "');
define('DB_PATH', ''); // Not used for MySQL
";
    } else {
        $config .= "define('DB_PATH', __DIR__ . '/storage/database.db');
define('DB_HOST', 'localhost');
define('DB_NAME', 'opencrm');
define('DB_USER', 'root');
define('DB_PASS', '');
";
    }

    $config .= "
// Site Configuration
define('SITE_NAME', '" . addslashes($data['site_name']) . "');
define('SITE_URL', '" . rtrim($data['site_url'], '/') . "');
define('ADMIN_EMAIL', '" . addslashes($data['admin_email'] ?? 'admin@example.com') . "');

// Security
define('SECRET_KEY', '$secret_key');
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
\$dirs = [STORAGE_PATH, UPLOADS_PATH, LOGS_PATH, CACHE_PATH];
foreach (\$dirs as \$dir) {
    if (!is_dir(\$dir)) {
        mkdir(\$dir, 0755, true);
    }
}
";

    return $config;
}

function createSampleData() {
    require_once __DIR__ . '/config.php';
    require_once __DIR__ . '/core/db.php';
    
    $db = get_db();
    
    // Sample clients
    $clients = [
        ['Acme Corporation', 'john@acme.com', '+1-555-0101', 'Acme Corporation', '123 Business St, Business City, BC 12345'],
        ['Tech Solutions Inc', 'contact@techsolutions.com', '+1-555-0102', 'Tech Solutions Inc', '456 Tech Ave, Innovation City, IC 67890'],
        ['Marketing Pro LLC', 'info@marketingpro.com', '+1-555-0103', 'Marketing Pro LLC', '789 Marketing Blvd, Creative City, CC 54321']
    ];
    
    foreach ($clients as $client) {
        $db->query(
            "INSERT INTO clients (name, email, phone, company, address, created_at, updated_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
            $client
        );
    }
    
    // Sample tasks
    $tasks = [
        ['Website Redesign', 'Complete the new website design and development', 1, 1, 'in_progress', 'high', date('Y-m-d', strtotime('+1 week'))],
        ['SEO Optimization', 'Optimize website for search engines', 2, 1, 'pending', 'medium', date('Y-m-d', strtotime('+2 weeks'))],
        ['Social Media Campaign', 'Plan and execute social media marketing campaign', 3, 1, 'pending', 'medium', date('Y-m-d', strtotime('+3 days'))]
    ];
    
    foreach ($tasks as $task) {
        $db->query(
            "INSERT INTO tasks (title, description, client_id, user_id, status, priority, due_date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
            $task
        );
    }
    
    // Sample invoices
    $invoices = [
        ['INV-1001', 1, 1, 2500.00, 'sent', 'builtin', null, null, '[{"description":"Website Design","quantity":1,"rate":2500}]', 'Net 30 terms', date('Y-m-d', strtotime('+30 days'))],
        ['INV-1002', 2, 1, 1800.00, 'draft', 'builtin', null, null, '[{"description":"SEO Services","quantity":1,"rate":1800}]', 'Net 15 terms', date('Y-m-d', strtotime('+15 days'))],
        ['INV-1003', 3, 1, 3200.00, 'paid', 'builtin', null, null, '[{"description":"Marketing Campaign","quantity":1,"rate":3200}]', 'Paid in full', date('Y-m-d', strtotime('-5 days'))]
    ];
    
    foreach ($invoices as $invoice) {
        $db->query(
            "INSERT INTO invoices (invoice_number, client_id, user_id, amount, status, invoice_type, external_id, external_url, items, notes, due_date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
            $invoice
        );
    }
    
    // Sample settings
    $settings = [
        ['default_invoice_type', 'builtin'],
        ['zoho_enabled', '0'],
        ['carter_enabled', '0'],
        ['whatsapp_chat_settings', '{"enabled":false,"phone_number":"","welcome_message":"Hello! I\'m interested in your services.","position":"bottom-right","show_on_pages":["all"]}']
    ];
    
    foreach ($settings as $setting) {
        $db->query(
            "INSERT OR REPLACE INTO settings (key, value, created_at, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
            $setting
        );
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenCRM Lite Installation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto py-12 px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg mx-auto mb-4"></div>
            <h1 class="text-3xl font-bold text-gray-900">OpenCRM Lite</h1>
            <p class="text-gray-600 mt-2">Installation Wizard</p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between text-sm font-medium text-gray-500 mb-2">
                <span class="<?= $step >= 1 ? 'text-blue-600' : '' ?>">Requirements</span>
                <span class="<?= $step >= 2 ? 'text-blue-600' : '' ?>">Database</span>
                <span class="<?= $step >= 3 ? 'text-blue-600' : '' ?>">Admin User</span>
                <span class="<?= $step >= 4 ? 'text-blue-600' : '' ?>">Settings</span>
                <span class="<?= $step >= 5 ? 'text-blue-600' : '' ?>">Complete</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: <?= ($step - 1) * 25 ?>%"></div>
            </div>
        </div>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <i data-feather="alert-circle" class="h-5 w-5 text-red-400 mr-3 mt-0.5"></i>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Installation Steps -->
        <div class="bg-white shadow rounded-lg p-6">
            <?php if ($step == 1): ?>
                <!-- Step 1: Requirements Check -->
                <h2 class="text-xl font-semibold text-gray-900 mb-4">System Requirements</h2>
                <p class="text-gray-600 mb-6">Let's check if your server meets the requirements for OpenCRM Lite.</p>
                
                <div class="space-y-4 mb-6">
                    <div class="flex items-center">
                        <i data-feather="<?= version_compare(PHP_VERSION, '7.4.0', '>=') ? 'check-circle' : 'x-circle' ?>" 
                           class="h-5 w-5 <?= version_compare(PHP_VERSION, '7.4.0', '>=') ? 'text-green-500' : 'text-red-500' ?> mr-3"></i>
                        <span>PHP 7.4+ (Current: <?= PHP_VERSION ?>)</span>
                    </div>
                    
                    <?php 
                    $required_extensions = ['pdo', 'pdo_sqlite', 'json', 'curl', 'openssl'];
                    foreach ($required_extensions as $ext): 
                        $loaded = extension_loaded($ext);
                    ?>
                        <div class="flex items-center">
                            <i data-feather="<?= $loaded ? 'check-circle' : 'x-circle' ?>" 
                               class="h-5 w-5 <?= $loaded ? 'text-green-500' : 'text-red-500' ?> mr-3"></i>
                            <span><?= ucfirst($ext) ?> Extension</span>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php 
                    $storage_writable = is_writable(__DIR__ . '/storage') || mkdir(__DIR__ . '/storage', 0755, true);
                    ?>
                    <div class="flex items-center">
                        <i data-feather="<?= $storage_writable ? 'check-circle' : 'x-circle' ?>" 
                           class="h-5 w-5 <?= $storage_writable ? 'text-green-500' : 'text-red-500' ?> mr-3"></i>
                        <span>Storage Directory Writable</span>
                    </div>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="step" value="1">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                        Continue
                    </button>
                </form>

            <?php elseif ($step == 2): ?>
                <!-- Step 2: Database Configuration -->
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Database & Site Configuration</h2>
                <p class="text-gray-600 mb-6">Configure your database connection and site settings.</p>
                
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="step" value="2">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                        <input type="text" name="site_name" value="<?= htmlspecialchars($_POST['site_name'] ?? 'OpenCRM Lite') ?>" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site URL</label>
                        <input type="url" name="site_url" value="<?= htmlspecialchars($_POST['site_url'] ?? 'http://localhost:5000') ?>" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Database Type</label>
                        <select name="db_type" onchange="toggleDatabaseFields(this.value)" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="sqlite" <?= ($_POST['db_type'] ?? 'sqlite') === 'sqlite' ? 'selected' : '' ?>>SQLite (Recommended)</option>
                            <option value="mysql" <?= ($_POST['db_type'] ?? '') === 'mysql' ? 'selected' : '' ?>>MySQL</option>
                        </select>
                    </div>
                    
                    <div id="mysql-fields" class="space-y-4" style="display: <?= ($_POST['db_type'] ?? 'sqlite') === 'mysql' ? 'block' : 'none' ?>">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Database Host</label>
                            <input type="text" name="db_host" value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Database Name</label>
                            <input type="text" name="db_name" value="<?= htmlspecialchars($_POST['db_name'] ?? 'opencrm') ?>" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Database Username</label>
                            <input type="text" name="db_user" value="<?= htmlspecialchars($_POST['db_user'] ?? '') ?>" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Database Password</label>
                            <input type="password" name="db_pass" value="<?= htmlspecialchars($_POST['db_pass'] ?? '') ?>" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                        Continue
                    </button>
                </form>

            <?php elseif ($step == 3): ?>
                <!-- Step 3: Admin User Creation -->
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Create Admin User</h2>
                <p class="text-gray-600 mb-6">Create your administrator account to manage OpenCRM Lite.</p>
                
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="step" value="3">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="admin_username" value="<?= htmlspecialchars($_POST['admin_username'] ?? '') ?>" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="admin_email" value="<?= htmlspecialchars($_POST['admin_email'] ?? '') ?>" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="admin_password" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <p class="text-sm text-gray-500 mt-1">Minimum 6 characters</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" name="admin_password_confirm" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                        Continue
                    </button>
                </form>

            <?php elseif ($step == 4): ?>
                <!-- Step 4: Final Settings -->
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Final Settings</h2>
                <p class="text-gray-600 mb-6">Configure final settings and complete the installation.</p>
                
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="step" value="4">
                    
                    <div class="bg-gray-50 p-4 rounded-md">
                        <label class="flex items-center">
                            <input type="checkbox" name="create_sample_data" value="1" checked 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Create sample data (recommended for testing)</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-2">This will create sample clients, tasks, and invoices to help you get started.</p>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-md">
                        <h3 class="text-sm font-medium text-blue-900 mb-2">What's Next?</h3>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Set up your invoice integrations (Zoho, Carter Finance)</li>
                            <li>• Configure API tokens for external integrations</li>
                            <li>• Customize your theme and branding</li>
                            <li>• Add your first clients and start managing projects</li>
                        </ul>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                        Complete Installation
                    </button>
                </form>

            <?php elseif ($step == 5): ?>
                <!-- Step 5: Installation Complete -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-feather="check" class="h-8 w-8 text-green-600"></i>
                    </div>
                    
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Installation Complete!</h2>
                    <p class="text-gray-600 mb-8">OpenCRM Lite has been successfully installed and configured.</p>
                    
                    <div class="bg-gray-50 p-6 rounded-md mb-6 text-left">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Important Security Notes:</h3>
                        <ul class="text-sm text-gray-700 space-y-2">
                            <li>• Delete or rename this <code class="bg-gray-200 px-1 rounded">install.php</code> file for security</li>
                            <li>• Set up SSL/HTTPS for production use</li>
                            <li>• Configure regular database backups</li>
                            <li>• Review and update your PHP and server security settings</li>
                        </ul>
                    </div>
                    
                    <div class="space-y-3">
                        <a href="/dashboard" class="block w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 transition duration-200 text-center">
                            Go to Dashboard
                        </a>
                        <a href="/invoices/settings" class="block w-full bg-gray-600 text-white py-3 px-4 rounded-md hover:bg-gray-700 transition duration-200 text-center">
                            Configure Integrations
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        feather.replace();
        
        function toggleDatabaseFields(type) {
            const mysqlFields = document.getElementById('mysql-fields');
            mysqlFields.style.display = type === 'mysql' ? 'block' : 'none';
        }
    </script>
</body>
</html>
