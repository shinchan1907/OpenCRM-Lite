<div class="max-w-4xl mx-auto">
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Invoice Settings</h1>
            <p class="mt-1 text-sm text-gray-500">
                Configure invoice preferences and third-party integrations.
            </p>
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

        <form method="POST" class="space-y-6">
            <!-- General Settings -->
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">General Settings</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Default invoice preferences and behavior.
                        </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="space-y-6">
                            <div>
                                <label for="default_invoice_type" class="block text-sm font-medium text-gray-700">Default Invoice Type</label>
                                <select name="default_invoice_type" id="default_invoice_type"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="builtin" <?= get_setting('default_invoice_type', 'builtin') === 'builtin' ? 'selected' : '' ?>>Built-in System</option>
                                    <option value="zoho" <?= get_setting('default_invoice_type') === 'zoho' ? 'selected' : '' ?>>Zoho Invoice</option>
                                    <option value="carter" <?= get_setting('default_invoice_type') === 'carter' ? 'selected' : '' ?>>Carter Finance</option>
                                </select>
                                <p class="mt-2 text-sm text-gray-500">Choose which invoice system to use by default when creating new invoices.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Zoho Invoice Integration -->
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Zoho Invoice</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Configure Zoho Invoice API integration for external invoice management.
                        </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="space-y-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="zoho_enabled" id="zoho_enabled" 
                                       value="1" <?= get_setting('zoho_enabled') ? 'checked' : '' ?>
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="zoho_enabled" class="ml-2 block text-sm text-gray-900">
                                    Enable Zoho Invoice Integration
                                </label>
                            </div>
                            
                            <div>
                                <label for="zoho_client_id" class="block text-sm font-medium text-gray-700">Client ID</label>
                                <input type="text" name="zoho_client_id" id="zoho_client_id"
                                       value="<?= htmlspecialchars(get_setting('zoho_client_id', '')) ?>"
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       placeholder="Your Zoho OAuth Client ID">
                                <p class="mt-2 text-sm text-gray-500">Get this from your Zoho Developer Console.</p>
                            </div>
                            
                            <div>
                                <label for="zoho_client_secret" class="block text-sm font-medium text-gray-700">Client Secret</label>
                                <input type="password" name="zoho_client_secret" id="zoho_client_secret"
                                       value="<?= htmlspecialchars(get_setting('zoho_client_secret', '')) ?>"
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       placeholder="Your Zoho OAuth Client Secret">
                            </div>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i data-feather="info" class="h-5 w-5 text-blue-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-blue-800">Setup Instructions</h4>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <ol class="list-decimal list-inside space-y-1">
                                                <li>Create a Zoho Developer account and register your application</li>
                                                <li>Set the redirect URI to: <code class="bg-blue-100 px-1 rounded"><?= SITE_URL ?>/oauth/zoho</code></li>
                                                <li>Copy the Client ID and Secret from your Zoho app</li>
                                                <li>Complete the OAuth flow to get your refresh token</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carter Finance Integration -->
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Carter Finance</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Configure Carter Finance API integration for invoice processing.
                        </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="space-y-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="carter_enabled" id="carter_enabled" 
                                       value="1" <?= get_setting('carter_enabled') ? 'checked' : '' ?>
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="carter_enabled" class="ml-2 block text-sm text-gray-900">
                                    Enable Carter Finance Integration
                                </label>
                            </div>
                            
                            <div>
                                <label for="carter_api_key" class="block text-sm font-medium text-gray-700">API Key</label>
                                <input type="password" name="carter_api_key" id="carter_api_key"
                                       value="<?= htmlspecialchars(get_setting('carter_api_key', '')) ?>"
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       placeholder="Your Carter Finance API Key">
                                <p class="mt-2 text-sm text-gray-500">Get this from your Carter Finance dashboard.</p>
                            </div>
                            
                            <div class="bg-green-50 border border-green-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i data-feather="info" class="h-5 w-5 text-green-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-green-800">Setup Instructions</h4>
                                        <div class="mt-2 text-sm text-green-700">
                                            <ol class="list-decimal list-inside space-y-1">
                                                <li>Sign up for a Carter Finance account</li>
                                                <li>Navigate to API settings in your dashboard</li>
                                                <li>Generate an API key for OpenCRM integration</li>
                                                <li>Set up webhook URL: <code class="bg-green-100 px-1 rounded"><?= SITE_URL ?>/webhook/carter</code></li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Webhook Settings -->
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Webhook Endpoints</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            URLs for third-party services to send updates.
                        </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Invoice Status Updates</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text" readonly 
                                           value="<?= SITE_URL ?>/webhook/invoice-status"
                                           class="flex-1 block w-full rounded-none rounded-l-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                    <button type="button" onclick="copyToClipboard(this.previousElementSibling.value)"
                                            class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm">
                                        Copy
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Zoho Invoice Webhook</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text" readonly 
                                           value="<?= SITE_URL ?>/webhook/zoho"
                                           class="flex-1 block w-full rounded-none rounded-l-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                    <button type="button" onclick="copyToClipboard(this.previousElementSibling.value)"
                                            class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm">
                                        Copy
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Carter Finance Webhook</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text" readonly 
                                           value="<?= SITE_URL ?>/webhook/carter"
                                           class="flex-1 block w-full rounded-none rounded-l-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                    <button type="button" onclick="copyToClipboard(this.previousElementSibling.value)"
                                            class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm">
                                        Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // You could add a toast notification here
        console.log('Copied to clipboard');
    });
}
</script>
