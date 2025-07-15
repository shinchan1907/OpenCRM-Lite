<?php
/**
 * Dashboard Controller
 */

require_once __DIR__ . '/model.php';

class DashboardController {
    private $model;
    
    public function __construct() {
        $this->model = new DashboardModel();
    }
    
    public function index() {
        $stats = $this->model->getStats();
        $recent_activities = $this->model->getRecentActivities();
        $upcoming_tasks = $this->model->getUpcomingTasks();
        $overdue_invoices = $this->model->getOverdueInvoices();
        
        render_view('layout', [
            'title' => 'Dashboard',
            'content' => load_view('dashboard', [
                'stats' => $stats,
                'recent_activities' => $recent_activities,
                'upcoming_tasks' => $upcoming_tasks,
                'overdue_invoices' => $overdue_invoices
            ])
        ]);
    }
    
    public function api_stats() {
        $stats = $this->model->getStats();
        json_response($stats);
    }
}
