<div class="max-w-3xl mx-auto">
    <div class="space-y-6">
        <!-- Profile Header -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="flex items-center space-x-5">
                <div class="flex-shrink-0">
                    <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                        <span class="text-xl font-medium text-gray-700">
                            <?= strtoupper(substr($user['username'], 0, 2)) ?>
                        </span>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($user['username']) ?></h1>
                    <p class="text-sm font-medium text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                    <p class="text-sm text-gray-500">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            <?= $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' ?>">
                            <?= ucfirst($user['role']) ?>
                        </span>
                        • Member since <?= format_date($user['created_at']) ?>
                    </p>
                </div>
            </div>
        </div>

        <?php if (isset($success)): ?>
            <div class="bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-feather="check-circle" class="h-5 w-5 text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800"><?= htmlspecialchars($success) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Profile Form -->
        <form method="POST" class="space-y-6">
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Profile Information</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Update your account information and password.
                        </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                <input type="text" name="username" id="username" required
                                       value="<?= htmlspecialchars($user['username']) ?>"
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" required
                                       value="<?= htmlspecialchars($user['email']) ?>"
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6">
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    New Password (leave blank to keep current)
                                </label>
                                <input type="password" name="password" id="password"
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-feather="alert-circle" class="h-5 w-5 text-red-400"></i>
                        </div>
                        <div class="ml-3">
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

            <div class="flex justify-end space-x-3">
                <a href="/dashboard" 
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Profile
                </button>
            </div>
        </form>

        <!-- Quick Links -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Quick Links</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="/users/api_tokens" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <i data-feather="key" class="h-5 w-5 text-gray-400 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">API Tokens</p>
                        <p class="text-sm text-gray-500">Manage your API access tokens</p>
                    </div>
                </a>
                
                <a href="/invoices/settings" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <i data-feather="settings" class="h-5 w-5 text-gray-400 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Invoice Settings</p>
                        <p class="text-sm text-gray-500">Configure invoice preferences</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
