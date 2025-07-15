<div class="space-y-6">
    <!-- Header with Search and Add Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div class="flex-1 max-w-lg">
            <form method="GET" class="relative">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Search clients..." 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-feather="search" class="h-5 w-5 text-gray-400"></i>
                </div>
            </form>
        </div>
        <a href="/clients/form" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i data-feather="plus" class="h-4 w-4 mr-2"></i>
            Add Client
        </a>
    </div>

    <!-- Clients Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <?php if (empty($clients)): ?>
            <div class="text-center py-12">
                <i data-feather="users" class="mx-auto h-12 w-12 text-gray-400"></i>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No clients found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    <?= $search ? 'Try adjusting your search criteria.' : 'Get started by adding your first client.' ?>
                </p>
                <?php if (!$search): ?>
                    <div class="mt-6">
                        <a href="/clients/form" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i data-feather="plus" class="h-4 w-4 mr-2"></i>
                            Add Client
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($clients as $client): ?>
                    <li>
                        <div class="px-4 py-4 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            <?= strtoupper(substr($client['name'], 0, 2)) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            <?= htmlspecialchars($client['name']) ?>
                                        </p>
                                        <?php if ($client['company']): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                <?= htmlspecialchars($client['company']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <?php if ($client['email']): ?>
                                            <div class="flex items-center">
                                                <i data-feather="mail" class="h-4 w-4 mr-1"></i>
                                                <?= htmlspecialchars($client['email']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($client['phone']): ?>
                                            <div class="flex items-center">
                                                <i data-feather="phone" class="h-4 w-4 mr-1"></i>
                                                <?= htmlspecialchars($client['phone']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <a href="/clients/view/<?= $client['id'] ?>" 
                                   class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    View
                                </a>
                                <a href="/clients/form/<?= $client['id'] ?>" 
                                   class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    Edit
                                </a>
                                <form method="POST" action="/clients/delete/<?= $client['id'] ?>" 
                                      onsubmit="return confirm('Are you sure you want to delete this client?')" 
                                      class="inline">
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
