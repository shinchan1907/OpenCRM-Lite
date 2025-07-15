<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'OpenCRM Lite' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-800">OpenCRM Lite</h1>
            </div>
            
            <nav class="mt-6">
                <div class="px-6 py-2">
                    <a href="/dashboard" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg <?= ($_SERVER['REQUEST_URI'] === '/dashboard' || $_SERVER['REQUEST_URI'] === '/') ? 'bg-blue-50 text-blue-700' : '' ?>">
                        <i data-feather="home" class="w-5 h-5 mr-3"></i>
                        Dashboard
                    </a>
                </div>
                
                <div class="px-6 py-2">
                    <a href="/clients" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg <?= strpos($_SERVER['REQUEST_URI'], '/clients') === 0 ? 'bg-blue-50 text-blue-700' : '' ?>">
                        <i data-feather="users" class="w-5 h-5 mr-3"></i>
                        Clients
                    </a>
                </div>
                
                <div class="px-6 py-2">
                    <a href="/tasks" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg <?= strpos($_SERVER['REQUEST_URI'], '/tasks') === 0 ? 'bg-blue-50 text-blue-700' : '' ?>">
                        <i data-feather="check-square" class="w-5 h-5 mr-3"></i>
                        Tasks
                    </a>
                </div>
                
                <div class="px-6 py-2">
                    <a href="/invoices" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg <?= strpos($_SERVER['REQUEST_URI'], '/invoices') === 0 ? 'bg-blue-50 text-blue-700' : '' ?>">
                        <i data-feather="file-text" class="w-5 h-5 mr-3"></i>
                        Invoices
                    </a>
                </div>
                
                <?php if (is_admin()): ?>
                <div class="px-6 py-2">
                    <a href="/users" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg <?= strpos($_SERVER['REQUEST_URI'], '/users') === 0 ? 'bg-blue-50 text-blue-700' : '' ?>">
                        <i data-feather="user" class="w-5 h-5 mr-3"></i>
                        Users
                    </a>
                </div>
                <?php endif; ?>
                
                <div class="px-6 py-2 mt-4 border-t border-gray-200">
                    <a href="/invoices/settings" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i data-feather="settings" class="w-5 h-5 mr-3"></i>
                        Settings
                    </a>
                </div>
                
                <div class="px-6 py-2">
                    <a href="/login/logout" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i data-feather="log-out" class="w-5 h-5 mr-3"></i>
                        Logout
                    </a>
                </div>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-semibold text-gray-900"><?= $title ?? 'Dashboard' ?></h1>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">Welcome, <?= htmlspecialchars(get_logged_in_user()['username']) ?></span>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <?= $content ?>
                </div>
            </main>
        </div>
    </div>
    
    <script>
        feather.replace();
    </script>
    <script src="/assets/js/app.js"></script>
</body>
</html>
