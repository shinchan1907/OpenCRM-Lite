<div class="max-w-4xl mx-auto">
    <form method="POST" class="space-y-6">
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Invoice Information</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Create or update invoice details and billing information.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6">
                            <label for="client_id" class="block text-sm font-medium text-gray-700">Client *</label>
                            <select name="client_id" id="client_id" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select a client</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>" 
                                            <?= ($invoice['client_id'] ?? $_GET['client_id'] ?? '') == $client['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($client['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="invoice_type" class="block text-sm font-medium text-gray-700">Invoice Type</label>
                            <select name="invoice_type" id="invoice_type"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="builtin" <?= ($invoice['invoice_type'] ?? 'builtin') === 'builtin' ? 'selected' : '' ?>>Built-in</option>
                                <option value="zoho" <?= ($invoice['invoice_type'] ?? '') === 'zoho' ? 'selected' : '' ?>>Zoho Invoice</option>
                                <option value="carter" <?= ($invoice['invoice_type'] ?? '') === 'carter' ? 'selected' : '' ?>>Carter Finance</option>
                            </select>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                            <input type="date" name="due_date" id="due_date"
                                   value="<?= $invoice['due_date'] ?? '' ?>"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6">
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount *</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" name="amount" id="amount" step="0.01" min="0" required
                                       value="<?= $invoice['amount'] ?? '' ?>"
                                       class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                                       placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-span-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                      placeholder="Additional notes or payment terms..."><?= htmlspecialchars($invoice['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Line Items Section -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Line Items</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Add individual items or services to this invoice.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div id="line-items">
                        <?php 
                        $items = isset($invoice['items']) ? json_decode($invoice['items'], true) : [];
                        if (empty($items)) {
                            $items = [['description' => '', 'quantity' => 1, 'rate' => '']];
                        }
                        ?>
                        <?php foreach ($items as $index => $item): ?>
                            <div class="line-item grid grid-cols-12 gap-4 mb-4">
                                <div class="col-span-6">
                                    <input type="text" name="items[<?= $index ?>][description]" 
                                           value="<?= htmlspecialchars($item['description']) ?>"
                                           placeholder="Description"
                                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div class="col-span-2">
                                    <input type="number" name="items[<?= $index ?>][quantity]" 
                                           value="<?= $item['quantity'] ?>" min="1"
                                           placeholder="Qty"
                                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div class="col-span-3">
                                    <input type="number" name="items[<?= $index ?>][rate]" 
                                           value="<?= $item['rate'] ?>" step="0.01" min="0"
                                           placeholder="Rate"
                                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div class="col-span-1">
                                    <button type="button" onclick="removeLineItem(this)" 
                                            class="w-full h-10 text-red-600 hover:text-red-900">
                                        <i data-feather="trash-2" class="h-4 w-4"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button type="button" onclick="addLineItem()" 
                            class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i data-feather="plus" class="h-4 w-4 mr-2"></i>
                        Add Line Item
                    </button>
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
            <a href="/invoices" 
               class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <?= $invoice ? 'Update Invoice' : 'Create Invoice' ?>
            </button>
        </div>
    </form>
</div>

<script>
let itemIndex = <?= count($items) ?>;

function addLineItem() {
    const container = document.getElementById('line-items');
    const div = document.createElement('div');
    div.className = 'line-item grid grid-cols-12 gap-4 mb-4';
    div.innerHTML = `
        <div class="col-span-6">
            <input type="text" name="items[${itemIndex}][description]" 
                   placeholder="Description"
                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>
        <div class="col-span-2">
            <input type="number" name="items[${itemIndex}][quantity]" 
                   value="1" min="1" placeholder="Qty"
                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>
        <div class="col-span-3">
            <input type="number" name="items[${itemIndex}][rate]" 
                   step="0.01" min="0" placeholder="Rate"
                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>
        <div class="col-span-1">
            <button type="button" onclick="removeLineItem(this)" 
                    class="w-full h-10 text-red-600 hover:text-red-900">
                <i data-feather="trash-2" class="h-4 w-4"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
    feather.replace();
    itemIndex++;
}

function removeLineItem(button) {
    button.closest('.line-item').remove();
}
</script>
