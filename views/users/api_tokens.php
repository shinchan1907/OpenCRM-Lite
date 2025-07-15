<div class="max-w-4xl mx-auto">
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900">API Tokens</h1>
            <p class="mt-1 text-sm text-gray-500">
                Manage API tokens for external integrations and automation tools.
            </p>
        </div>

        <?php if (isset($success)): ?>
            <div class="bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-feather="check-circle" class="h-5 w-5 text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Token Created Successfully</h3>
                        <p class="mt-2 text-sm text-green-700"><?= htmlspecialchars($success) ?></p>
                        <p class="mt-2 text-xs text-green-600">
                            <strong>Important:</strong> Copy this token now. You won't be able to see it again for security reasons.
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Create New Token -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Create New Token</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Generate a new API token for external applications.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form method="POST" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Token Name</label>
                            <input type="text" name="name" id="name" required
                                   placeholder="e.g., Zapier Integration, Mobile App"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <p class="mt-2 text-sm text-gray-500">Give your token a descriptive name to help you remember what it's for.</p>
                        </div>

                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700">Expiration Date (Optional)</label>
                            <input type="date" name="expires_at" id="expires_at"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <p class="mt-2 text-sm text-gray-500">Leave blank for tokens that never expire.</p>
                        </div>

                        <div>
                            <button type="submit" 
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Generate Token
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Existing Tokens -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Your API Tokens</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Manage your existing API tokens and their permissions.
                </p>
            </div>
            
            <?php if (empty($tokens)): ?>
                <div class="text-center py-12">
                    <i data-feather="key" class="mx-auto h-12 w-12 text-gray-400"></i>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No API tokens</h3>
                    <p class="mt-1 text-sm text-gray-500">You haven't created any API tokens yet.</p>
                </div>
            <?php else: ?>
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($tokens as $token): ?>
                        <li>
                            <div class="px-4 py-4 flex items-center justify-between hover:bg-gray-50">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i data-feather="key" class="h-5 w-5 text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            <?= htmlspecialchars($token['name'] ?: 'Unnamed Token') ?>
                                        </p>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span>Created <?= format_date($token['created_at']) ?></span>
                                            <?php if ($token['expires_at']): ?>
                                                <span>•</span>
                                                <span>Expires <?= format_date($token['expires_at']) ?></span>
                                                <?php if (strtotime($token['expires_at']) < time()): ?>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        Expired
                                                    </span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span>•</span>
                                                <span>Never expires</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mt-1">
                                            <code class="text-xs font-mono text-gray-600 bg-gray-100 px-2 py-1 rounded">
                                                <?= substr($token['token'], 0, 8) ?>...<?= substr($token['token'], -8) ?>
                                            </code>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <button onclick="copyToClipboard('<?= htmlspecialchars($token['token']) ?>')"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        Copy
                                    </button>
                                    <form method="POST" action="/users/api_tokens/delete/<?= $token['id'] ?>" 
                                          onsubmit="return confirm('Are you sure you want to delete this token?')" 
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

        <!-- API Documentation -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">API Documentation</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Learn how to use the OpenCRM API with your tokens.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Base URL</h4>
                            <code class="block mt-1 text-sm text-gray-600 bg-gray-100 px-3 py-2 rounded">
                                <?= SITE_URL ?>/api/v1/
                            </code>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Authentication</h4>
                            <p class="mt-1 text-sm text-gray-600">Include your token in the Authorization header:</p>
                            <code class="block mt-1 text-sm text-gray-600 bg-gray-100 px-3 py-2 rounded">
                                Authorization: Bearer YOUR_TOKEN_HERE
                            </code>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Available Endpoints</h4>
                            <ul class="mt-1 text-sm text-gray-600 space-y-1">
                                <li><code>GET /clients</code> - List all clients</li>
                                <li><code>POST /clients</code> - Create a new client</li>
                                <li><code>GET /tasks</code> - List all tasks</li>
                                <li><code>POST /tasks</code> - Create a new task</li>
                                <li><code>GET /invoices</code> - List all invoices</li>
                                <li><code>POST /invoices</code> - Create a new invoice</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // You could add a toast notification here
        console.log('Token copied to clipboard');
    });
}
</script>
