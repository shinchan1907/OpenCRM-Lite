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
    
    <!-- Theme-specific styles -->
    <link rel="stylesheet" href="/themes/modern-light/style.css">
    
    <!-- Custom Tailwind Config for Theme -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen">
        <!-- Enhanced Sidebar -->
        <div class="w-64 bg-white shadow-xl border-r border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg mr-3"></div>
                    <h1 class="text-xl font-bold text-gray-800">OpenCRM</h1>
                </div>
                <p class="text-xs text-gray-500 mt-1">Modern Light Theme</p>
            </div>
            
            <nav class="mt-6">
                <!-- Navigation items with enhanced styling -->
                <div class="px-3">
                    <a href="/dashboard" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 <?= ($_SERVER['REQUEST_URI'] === '/dashboard' || $_SERVER['REQUEST_URI'] === '/') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                        <i data-feather="home" class="w-5 h-5 mr-3 transition-colors"></i>
                        Dashboard
                    </a>
                </div>
                
                <div class="px-3 mt-1">
                    <a href="/clients" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 <?= strpos($_SERVER['REQUEST_URI'], '/clients') === 0 ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                        <i data-feather="users" class="w-5 h-5 mr-3 transition-colors"></i>
                        Clients
                    </a>
                </div>
                
                <div class="px-3 mt-1">
                    <a href="/tasks" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 <?= strpos($_SERVER['REQUEST_URI'], '/tasks') === 0 ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                        <i data-feather="check-square" class="w-5 h-5 mr-3 transition-colors"></i>
                        Tasks
                    </a>
                </div>
                
                <div class="px-3 mt-1">
                    <a href="/invoices" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 <?= strpos($_SERVER['REQUEST_URI'], '/invoices') === 0 ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                        <i data-feather="file-text" class="w-5 h-5 mr-3 transition-colors"></i>
                        Invoices
                    </a>
                </div>
                
                <?php if (is_admin()): ?>
                <div class="px-3 mt-6 pt-6 border-t border-gray-200">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administration</p>
                    <a href="/users" class="nav-item group flex items-center px-3 py-2 mt-2 text-sm font-medium rounded-lg transition-all duration-200 <?= strpos($_SERVER['REQUEST_URI'], '/users') === 0 ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                        <i data-feather="user" class="w-5 h-5 mr-3 transition-colors"></i>
                        Users
                    </a>
                </div>
                <?php endif; ?>
                
                <div class="px-3 mt-6 pt-6 border-t border-gray-200">
                    <a href="/invoices/settings" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                        <i data-feather="settings" class="w-5 h-5 mr-3 transition-colors"></i>
                        Settings
                    </a>
                </div>
                
                <div class="px-3 mt-1">
                    <a href="/login/logout" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 text-gray-600 hover:bg-red-50 hover:text-red-700">
                        <i data-feather="log-out" class="w-5 h-5 mr-3 transition-colors"></i>
                        Logout
                    </a>
                </div>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">
            <!-- Enhanced Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900"><?= $title ?? 'Dashboard' ?></h1>
                            <div class="flex items-center mt-1 text-sm text-gray-500">
                                <i data-feather="calendar" class="w-4 h-4 mr-1"></i>
                                <?= date('l, F j, Y') ?>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="hidden md:block text-right">
                                <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars(get_logged_in_user()['username']) ?></p>
                                <p class="text-xs text-gray-500"><?= ucfirst(get_logged_in_user()['role']) ?></p>
                            </div>
                            <div class="h-8 w-8 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center">
                                <span class="text-xs font-medium text-white">
                                    <?= strtoupper(substr(get_logged_in_user()['username'], 0, 2)) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
