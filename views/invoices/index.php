<div class="space-y-6">
    <!-- Header with Filters and Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div class="flex space-x-4">
            <form method="GET" class="flex space-x-4">
                <select name="status" onchange="this.form.submit()" 
                        class="block w-40 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">All Status</option>
                    <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="sent" <?= $status === 'sent' ? 'selected' : '' ?>>Sent</option>
                    <option value="paid" <?= $status === 'paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="overdue" <?= $status === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                </select>
                
                <select name="client_id" onchange="this.form.submit()" 
                        class="block w-48 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">All Clients</option>
                    <?php foreach ($clients as $client_option): ?>
                        <option value="<?= $client_option['id'] ?>" <?= $client_id == $client_option['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($client_option['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        
        <div class="flex space-x-3">
            <a href="/invoices/sync" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i data-feather="refresh-cw" class="h-4 w-4 mr-2"></i>
                Sync External
            </a>
            <a href="/invoices/form" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i data-feather="plus" class="h-4 w-4 mr-2"></i>
                Create Invoice
            </a>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <?php if (empty($invoices)): ?>
            <div class="text-center py-12">
                <i data-feather="file-text" class="mx-auto h-12 w-12 text-gray-400"></i>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No invoices found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    <?= $status || $client_id ? 'Try adjusting your filters.' : 'Get started by creating your first invoice.' ?>
                </p>
                <?php if (!$status && !$client_id): ?>
                    <div class="mt-6">
                        <a href="/invoices/form" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i data-feather="plus" class="h-4 w-4 mr-2"></i>
                            Create Invoice
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($invoices as $invoice): ?>
                    <li>
                        <div class="px-4 py-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i data-feather="file-text" class="h-5 w-5 text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2">
                                            <p class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($invoice['invoice_number']) ?>
                                            </p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                <?= $invoice['invoice_type'] === 'builtin' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' ?>">
                                                <?= ucfirst($invoice['invoice_type']) ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span><?= htmlspecialchars($invoice['client_name']) ?></span>
                                            <span>•</span>
                                            <span><?= format_currency($invoice['amount']) ?></span>
                                            <span>•</span>
                                            <span><?= format_date($invoice['created_at']) ?></span>
                                            <?php if ($invoice['due_date']): ?>
                                                <span>•</span>
                                                <span>Due: <?= format_date($invoice['due_date']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        <?= $invoice['status'] === 'paid' ? 'bg-green-100 text-green-800' : 
                                           ($invoice['status'] === 'sent' ? 'bg-blue-100 text-blue-800' : 
                                           ($invoice['status'] === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) ?>">
                                        <?= ucfirst($invoice['status']) ?>
                                    </span>
                                    
                                    <div class="flex space-x-2">
                                        <?php if ($invoice['invoice_type'] === 'builtin'): ?>
                                            <a href="/invoices/pdf/<?= $invoice['id'] ?>" 
                                               class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                                PDF
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($invoice['external_url']): ?>
                                            <a href="<?= htmlspecialchars($invoice['external_url']) ?>" target="_blank"
                                               class="text-purple-600 hover:text-purple-900 text-sm font-medium">
                                                View External
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="/invoices/send/<?= $invoice['id'] ?>" 
                                           class="text-green-600 hover:text-green-900 text-sm font-medium">
                                            Send
                                        </a>
                                        <a href="/invoices/form/<?= $invoice['id'] ?>" 
                                           class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            Edit
                                        </a>
                                        <form method="POST" action="/invoices/delete/<?= $invoice['id'] ?>" 
                                              onsubmit="return confirm('Are you sure you want to delete this invoice?')" 
                                              class="inline">
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
