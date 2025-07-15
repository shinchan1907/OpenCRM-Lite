<div class="space-y-6">
    <!-- Client Header -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900"><?= htmlspecialchars($client['name']) ?></h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Client information and activity</p>
            </div>
            <div class="flex space-x-3">
                <a href="/clients/form/<?= $client['id'] ?>" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i data-feather="edit" class="h-4 w-4 mr-2"></i>
                    Edit
                </a>
                <a href="/tasks/form?client_id=<?= $client['id'] ?>" 
                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i data-feather="plus" class="h-4 w-4 mr-2"></i>
                    Add Task
                </a>
                <a href="/invoices/form?client_id=<?= $client['id'] ?>" 
                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i data-feather="file-text" class="h-4 w-4 mr-2"></i>
                    Create Invoice
                </a>
            </div>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= $client['email'] ? htmlspecialchars($client['email']) : 'Not provided' ?>
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= $client['phone'] ? htmlspecialchars($client['phone']) : 'Not provided' ?>
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Company</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= $client['company'] ? htmlspecialchars($client['company']) : 'Not provided' ?>
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= $client['address'] ? nl2br(htmlspecialchars($client['address'])) : 'Not provided' ?>
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Client since</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= format_date($client['created_at']) ?>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tasks -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Tasks</h3>
                <?php if (empty($tasks)): ?>
                    <p class="text-gray-500 text-center py-4">No tasks found for this client</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($tasks as $task): ?>
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($task['title']) ?></p>
                                    <p class="text-xs text-gray-500">
                                        Due: <?= $task['due_date'] ? format_date($task['due_date']) : 'No due date' ?>
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    <?= $task['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($task['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') ?>">
                                    <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Invoices -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Invoices</h3>
                <?php if (empty($invoices)): ?>
                    <p class="text-gray-500 text-center py-4">No invoices found for this client</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($invoices as $invoice): ?>
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($invoice['invoice_number']) ?></p>
                                    <p class="text-xs text-gray-500">
                                        <?= format_currency($invoice['amount']) ?> • <?= format_date($invoice['created_at']) ?>
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    <?= $invoice['status'] === 'paid' ? 'bg-green-100 text-green-800' : 
                                       ($invoice['status'] === 'sent' ? 'bg-blue-100 text-blue-800' : 
                                       ($invoice['status'] === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) ?>">
                                    <?= ucfirst($invoice['status']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
