<div class="space-y-6">
    <!-- Filters and Add Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div class="flex space-x-4">
            <form method="GET" class="flex space-x-4">
                <select name="status" onchange="this.form.submit()" 
                        class="block w-40 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">All Status</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
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
        
        <a href="/tasks/form" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i data-feather="plus" class="h-4 w-4 mr-2"></i>
            Add Task
        </a>
    </div>

    <!-- Tasks Grid -->
    <?php if (empty($tasks)): ?>
        <div class="text-center py-12">
            <i data-feather="check-square" class="mx-auto h-12 w-12 text-gray-400"></i>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks found</h3>
            <p class="mt-1 text-sm text-gray-500">
                <?= $status || $client_id ? 'Try adjusting your filters.' : 'Get started by creating your first task.' ?>
            </p>
            <?php if (!$status && !$client_id): ?>
                <div class="mt-6">
                    <a href="/tasks/form" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i data-feather="plus" class="h-4 w-4 mr-2"></i>
                        Add Task
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($tasks as $task): ?>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    <?= $task['priority'] === 'high' ? 'bg-red-100 text-red-800' : 
                                       ($task['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') ?>">
                                    <?= ucfirst($task['priority']) ?>
                                </span>
                            </div>
                            <div class="flex space-x-1">
                                <a href="/tasks/form/<?= $task['id'] ?>" 
                                   class="text-indigo-600 hover:text-indigo-900">
                                    <i data-feather="edit" class="h-4 w-4"></i>
                                </a>
                                <form method="POST" action="/tasks/delete/<?= $task['id'] ?>" 
                                      onsubmit="return confirm('Are you sure?')" class="inline">
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i data-feather="trash-2" class="h-4 w-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900"><?= htmlspecialchars($task['title']) ?></h3>
                            <?php if ($task['description']): ?>
                                <p class="mt-1 text-sm text-gray-500 line-clamp-2"><?= htmlspecialchars($task['description']) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-4 space-y-2">
                            <?php if ($task['client_name']): ?>
                                <div class="flex items-center text-sm text-gray-500">
                                    <i data-feather="user" class="h-4 w-4 mr-2"></i>
                                    <?= htmlspecialchars($task['client_name']) ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($task['due_date']): ?>
                                <div class="flex items-center text-sm text-gray-500">
                                    <i data-feather="calendar" class="h-4 w-4 mr-2"></i>
                                    Due: <?= format_date($task['due_date']) ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex items-center text-sm text-gray-500">
                                <i data-feather="user-check" class="h-4 w-4 mr-2"></i>
                                Assigned to: <?= htmlspecialchars($task['username']) ?>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <select onchange="updateTaskStatus(<?= $task['id'] ?>, this.value)" 
                                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="pending" <?= $task['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="in_progress" <?= $task['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="completed" <?= $task['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function updateTaskStatus(taskId, status) {
    fetch('/tasks/update_status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `task_id=${taskId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
