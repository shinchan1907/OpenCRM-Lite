<?php
/**
 * Dashboard Model
 */

class DashboardModel {
    private $db;
    
    public function __construct() {
        $this->db = get_db();
    }
    
    public function getStats() {
        return [
            'clients' => [
                'total' => $this->db->fetchOne("SELECT COUNT(*) as count FROM clients")['count'],
                'new_this_month' => $this->db->fetchOne("SELECT COUNT(*) as count FROM clients WHERE created_at >= DATE('now', 'start of month')")['count']
            ],
            'tasks' => [
                'total' => $this->db->fetchOne("SELECT COUNT(*) as count FROM tasks")['count'],
                'pending' => $this->db->fetchOne("SELECT COUNT(*) as count FROM tasks WHERE status = 'pending'")['count'],
                'in_progress' => $this->db->fetchOne("SELECT COUNT(*) as count FROM tasks WHERE status = 'in_progress'")['count'],
                'completed' => $this->db->fetchOne("SELECT COUNT(*) as count FROM tasks WHERE status = 'completed'")['count'],
                'overdue' => $this->db->fetchOne("SELECT COUNT(*) as count FROM tasks WHERE due_date < DATE('now') AND status != 'completed'")['count']
            ],
            'invoices' => [
                'total' => $this->db->fetchOne("SELECT COUNT(*) as count FROM invoices")['count'],
                'draft' => $this->db->fetchOne("SELECT COUNT(*) as count FROM invoices WHERE status = 'draft'")['count'],
                'sent' => $this->db->fetchOne("SELECT COUNT(*) as count FROM invoices WHERE status = 'sent'")['count'],
                'paid' => $this->db->fetchOne("SELECT COUNT(*) as count FROM invoices WHERE status = 'paid'")['count'],
                'overdue' => $this->db->fetchOne("SELECT COUNT(*) as count FROM invoices WHERE status = 'overdue'")['count'],
                'total_amount' => floatval($this->db->fetchOne("SELECT SUM(amount) as total FROM invoices WHERE status = 'paid'")['total'] ?? 0),
                'outstanding' => floatval($this->db->fetchOne("SELECT SUM(amount) as total FROM invoices WHERE status IN ('sent', 'overdue')")['total'] ?? 0)
            ]
        ];
    }
    
    public function getRecentActivities($limit = 10) {
        $activities = [];
        
        // Recent clients
        $recent_clients = $this->db->fetchAll(
            "SELECT 'client' as type, 'created' as action, name as title, created_at FROM clients ORDER BY created_at DESC LIMIT 3"
        );
        
        // Recent tasks
        $recent_tasks = $this->db->fetchAll(
            "SELECT 'task' as type, 'created' as action, title, created_at FROM tasks ORDER BY created_at DESC LIMIT 3"
        );
        
        // Recent invoices
        $recent_invoices = $this->db->fetchAll(
            "SELECT 'invoice' as type, 'created' as action, CONCAT('Invoice ', invoice_number) as title, created_at FROM invoices ORDER BY created_at DESC LIMIT 4"
        );
        
        $activities = array_merge($recent_clients, $recent_tasks, $recent_invoices);
        
        // Sort by created_at
        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($activities, 0, $limit);
    }
    
    public function getUpcomingTasks($limit = 5) {
        return $this->db->fetchAll(
            "SELECT t.*, c.name as client_name 
             FROM tasks t 
             LEFT JOIN clients c ON t.client_id = c.id 
             WHERE t.status != 'completed' AND t.due_date >= DATE('now') 
             ORDER BY t.due_date ASC 
             LIMIT ?",
            [$limit]
        );
    }
    
    public function getOverdueInvoices($limit = 5) {
        return $this->db->fetchAll(
            "SELECT i.*, c.name as client_name 
             FROM invoices i 
             LEFT JOIN clients c ON i.client_id = c.id 
             WHERE i.status IN ('sent', 'overdue') AND i.due_date < DATE('now') 
             ORDER BY i.due_date ASC 
             LIMIT ?",
            [$limit]
        );
    }
    
    public function getRevenueData($period = '12months') {
        switch ($period) {
            case '30days':
                return $this->db->fetchAll(
                    "SELECT DATE(created_at) as date, SUM(amount) as revenue 
                     FROM invoices 
                     WHERE status = 'paid' AND created_at >= DATE('now', '-30 days') 
                     GROUP BY DATE(created_at) 
                     ORDER BY date ASC"
                );
            case '12months':
            default:
                return $this->db->fetchAll(
                    "SELECT strftime('%Y-%m', created_at) as month, SUM(amount) as revenue 
                     FROM invoices 
                     WHERE status = 'paid' AND created_at >= DATE('now', '-12 months') 
                     GROUP BY strftime('%Y-%m', created_at) 
                     ORDER BY month ASC"
                );
        }
    }
    
    public function getTasksData() {
        return $this->db->fetchAll(
            "SELECT status, COUNT(*) as count 
             FROM tasks 
             GROUP BY status"
        );
    }
    
    public function getInvoicesByStatus() {
        return $this->db->fetchAll(
            "SELECT status, COUNT(*) as count, SUM(amount) as total_amount 
             FROM invoices 
             GROUP BY status"
        );
    }
}
