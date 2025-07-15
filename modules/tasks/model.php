<?php
/**
 * Tasks Model
 */

class TasksModel {
    private $db;
    
    public function __construct() {
        $this->db = get_db();
    }
    
    public function getAll($status = '', $client_id = '') {
        $sql = "SELECT t.*, c.name as client_name, u.username 
                FROM tasks t 
                LEFT JOIN clients c ON t.client_id = c.id 
                LEFT JOIN users u ON t.user_id = u.id";
        
        $params = [];
        $conditions = [];
        
        if ($status) {
            $conditions[] = "t.status = ?";
            $params[] = $status;
        }
        
        if ($client_id) {
            $conditions[] = "t.client_id = ?";
            $params[] = $client_id;
        }
        
        if ($conditions) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY t.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function get($id) {
        $sql = "SELECT t.*, c.name as client_name, u.username 
                FROM tasks t 
                LEFT JOIN clients c ON t.client_id = c.id 
                LEFT JOIN users u ON t.user_id = u.id 
                WHERE t.id = ?";
        
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO tasks (title, description, client_id, user_id, status, priority, due_date, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        
        $this->db->query($sql, [
            $data['title'],
            $data['description'],
            $data['client_id'],
            $data['user_id'],
            $data['status'],
            $data['priority'],
            $data['due_date']
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        
        $fields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;
        
        $sql = "UPDATE tasks SET " . implode(", ", $fields) . " WHERE id = ?";
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM tasks WHERE id = ?", [$id]);
    }
    
    public function getByClient($client_id) {
        $sql = "SELECT t.*, u.username 
                FROM tasks t 
                LEFT JOIN users u ON t.user_id = u.id 
                WHERE t.client_id = ? 
                ORDER BY t.created_at DESC";
        
        return $this->db->fetchAll($sql, [$client_id]);
    }
    
    public function getByStatus($status) {
        return $this->db->fetchAll(
            "SELECT * FROM tasks WHERE status = ? ORDER BY created_at DESC",
            [$status]
        );
    }
    
    public function getOverdue() {
        return $this->db->fetchAll(
            "SELECT t.*, c.name as client_name 
             FROM tasks t 
             LEFT JOIN clients c ON t.client_id = c.id 
             WHERE t.due_date < DATE('now') AND t.status != 'completed' 
             ORDER BY t.due_date ASC"
        );
    }
    
    public function getStats() {
        return [
            'total' => $this->db->fetchOne("SELECT COUNT(*) as count FROM tasks")['count'],
            'pending' => $this->db->fetchOne("SELECT COUNT(*) as count FROM tasks WHERE status = 'pending'")['count'],
            'in_progress' => $this->db->fetchOne("SELECT COUNT(*) as count FROM tasks WHERE status = 'in_progress'")['count'],
            'completed' => $this->db->fetchOne("SELECT COUNT(*) as count FROM tasks WHERE status = 'completed'")['count'],
            'overdue' => $this->db->fetchOne("SELECT COUNT(*) as count FROM tasks WHERE due_date < DATE('now') AND status != 'completed'")['count']
        ];
    }
}
