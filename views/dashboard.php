<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Clients Stats -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i data-feather="users" class="h-6 w-6 text-gray-400"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Clients</dt>
                            <dd class="text-lg font-medium text-gray-900"><?= $stats['clients']['total'] ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-green-600 font-medium">+<?= $stats['clients']['new_this_month'] ?></span>
                    <span class="text-gray-500">this month</span>
                </div>
            </div>
        </div>

        <!-- Tasks Stats -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i data-feather="check-square" class="h-6 w-6 text-gray-400"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Tasks</dt>
                            <dd class="text-lg font-medium text-gray-900"><?= $stats['tasks']['pending'] + $stats['tasks']['in_progress'] ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <?php if ($stats['tasks']['overdue'] > 0): ?>
                        <span class="text-red-600 font-medium"><?= $stats['tasks']['overdue'] ?> overdue</span>
                    <?php else: ?>
                        <span class="text-green-600 font-medium">No overdue tasks</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Revenue Stats -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i data-feather="dollar-sign" class="h-6 w-6 text-gray-400"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                            <dd class="text-lg font-medium text-gray-900"><?= format_currency($stats['invoices']['total_amount']) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-orange-600 font-medium"><?= format_currency($stats['invoices']['outstanding']) ?></span>
                    <span class="text-gray-500">outstanding</span>
                </div>
            </div>
        </div>

        <!-- Invoices Stats -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i data-feather="file-text" class="h-6 w-6 text-gray-400"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Invoices</dt>
                            <dd class="text-lg font-medium text-gray-900"><?= $stats['invoices']['total'] ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-green-600 font-medium"><?= $stats['invoices']['paid'] ?> paid</span>
                    <span class="text-gray-500">•</span>
                    <span class="text-blue-600 font-medium"><?= $stats['invoices']['sent'] ?> sent</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activities -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Activities</h3>
            </div>
            <div class="px-6 py-4">
                <?php if (empty($recent_activities)): ?>
                    <p class="text-gray-500 text-center py-4">No recent activities</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <?php
                                    $icon = 'circle';
                                    $color = 'text-gray-400';
                                    switch ($activity['type']) {
                                        case 'client':
                                            $icon = 'user';
                                            $color = 'text-blue-500';
                                            break;
                                        case 'task':
                                            $icon = 'check-square';
                                            $color = 'text-green-500';
                                            break;
                                        case 'invoice':
                                            $icon = 'file-text';
                                            $color = 'text-purple-500';
                                            break;
                                    }
                                    ?>
                                    <i data-feather="<?= $icon ?>" class="h-4 w-4 <?= $color ?>"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900"><?= htmlspecialchars($activity['title']) ?></p>
                                    <p class="text-xs text-gray-500"><?= format_date($activity['created_at']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Upcoming Tasks -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Upcoming Tasks</h3>
            </div>
            <div class="px-6 py-4">
                <?php if (empty($upcoming_tasks)): ?>
                    <p class="text-gray-500 text-center py-4">No upcoming tasks</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($upcoming_tasks as $task): ?>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($task['title']) ?></p>
                                    <p class="text-xs text-gray-500">
                                        <?= $task['client_name'] ? htmlspecialchars($task['client_name']) : 'No client' ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500"><?= format_date($task['due_date']) ?></p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        <?= $task['priority'] === 'high' ? 'bg-red-100 text-red-800' : 
                                           ($task['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') ?>">
                                        <?= ucfirst($task['priority']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Task Status Chart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Task Status Distribution</h3>
            </div>
            <div class="px-6 py-4">
                <canvas id="tasksChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Invoice Status Chart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Invoice Status Distribution</h3>
            </div>
            <div class="px-6 py-4">
                <canvas id="invoicesChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
// Task Status Chart
const tasksCtx = document.getElementById('tasksChart').getContext('2d');
new Chart(tasksCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'In Progress', 'Completed', 'Overdue'],
        datasets: [{
            data: [
                <?= $stats['tasks']['pending'] ?>,
                <?= $stats['tasks']['in_progress'] ?>,
                <?= $stats['tasks']['completed'] ?>,
                <?= $stats['tasks']['overdue'] ?>
            ],
            backgroundColor: ['#FED7AA', '#BFDBFE', '#BBF7D0', '#FECACA']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Invoice Status Chart
const invoicesCtx = document.getElementById('invoicesChart').getContext('2d');
new Chart(invoicesCtx, {
    type: 'doughnut',
    data: {
        labels: ['Draft', 'Sent', 'Paid', 'Overdue'],
        datasets: [{
            data: [
                <?= $stats['invoices']['draft'] ?>,
                <?= $stats['invoices']['sent'] ?>,
                <?= $stats['invoices']['paid'] ?>,
                <?= $stats['invoices']['overdue'] ?>
            ],
            backgroundColor: ['#E5E7EB', '#BFDBFE', '#BBF7D0', '#FECACA']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
