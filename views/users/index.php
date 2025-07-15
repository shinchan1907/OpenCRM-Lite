<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Users</h1>
            <p class="mt-1 text-sm text-gray-500">Manage system users and their access permissions.</p>
        </div>
        <a href="/users/form" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i data-feather="user-plus" class="h-4 w-4 mr-2"></i>
            Add User
        </a>
    </div>

    <!-- Users Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <?php if (empty($users)): ?>
            <div class="text-center py-12">
                <i data-feather="users" class="mx-auto h-12 w-12 text-gray-400"></i>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding your first user.</p>
                <div class="mt-6">
                    <a href="/users/form" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i data-feather="user-plus" class="h-4 w-4 mr-2"></i>
                        Add User
                    </a>
                </div>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($users as $user): ?>
                    <li>
                        <div class="px-4 py-4 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            <?= strtoupper(substr($user['username'], 0, 2)) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            <?= htmlspecialchars($user['username']) ?>
                                        </p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            <?= $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <i data-feather="mail" class="h-4 w-4 mr-1"></i>
                                            <?= htmlspecialchars($user['email']) ?>
                                        </div>
                                        <div class="flex items-center">
                                            <i data-feather="calendar" class="h-4 w-4 mr-1"></i>
                                            Joined <?= format_date($user['created_at']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <a href="/users/form/<?= $user['id'] ?>" 
                                   class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    Edit
                                </a>
                                <?php if ($user['id'] != get_logged_in_user()['id']): ?>
                                    <form method="POST" action="/users/delete/<?= $user['id'] ?>" 
                                          onsubmit="return confirm('Are you sure you want to delete this user?')" 
                                          class="inline">
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900 text-sm font-medium">
                                            Delete
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- User Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i data-feather="users" class="h-6 w-6 text-gray-400"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                            <dd class="text-lg font-medium text-gray-900"><?= count($users) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i data-feather="shield" class="h-6 w-6 text-gray-400"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Administrators</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                <?= count(array_filter($users, function($u) { return $u['role'] === 'admin'; })) ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i data-feather="user" class="h-6 w-6 text-gray-400"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Staff Members</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                <?= count(array_filter($users, function($u) { return $u['role'] === 'staff'; })) ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
