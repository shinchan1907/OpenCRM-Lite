<div class="max-w-2xl mx-auto">
    <form method="POST" class="space-y-6">
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Task Details</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Create or update task information and assignment.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6">
                            <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                            <input type="text" name="title" id="title" required
                                   value="<?= htmlspecialchars($task['title'] ?? '') ?>"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="client_id" class="block text-sm font-medium text-gray-700">Client</label>
                            <select name="client_id" id="client_id"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select a client</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>" 
                                            <?= ($task['client_id'] ?? $_GET['client_id'] ?? '') == $client['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($client['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                            <input type="date" name="due_date" id="due_date"
                                   value="<?= $task['due_date'] ?? '' ?>"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="pending" <?= ($task['status'] ?? 'pending') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="in_progress" <?= ($task['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="completed" <?= ($task['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                            <select name="priority" id="priority"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="low" <?= ($task['priority'] ?? 'medium') === 'low' ? 'selected' : '' ?>>Low</option>
                                <option value="medium" <?= ($task['priority'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Medium</option>
                                <option value="high" <?= ($task['priority'] ?? 'medium') === 'high' ? 'selected' : '' ?>>High</option>
                            </select>
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
            <a href="/tasks" 
               class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <?= $task ? 'Update Task' : 'Create Task' ?>
            </button>
        </div>
    </form>
</div>
