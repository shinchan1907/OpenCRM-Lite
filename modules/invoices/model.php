<?php
/**
 * Invoices Model
 */

class InvoicesModel {
    private $db;
    
    public function __construct() {
        $this->db = get_db();
    }
    
    public function getAll($status = '', $client_id = '') {
        $sql = "SELECT i.*, c.name as client_name, u.username 
                FROM invoices i 
                LEFT JOIN clients c ON i.client_id = c.id 
                LEFT JOIN users u ON i.user_id = u.id";
        
        $params = [];
        $conditions = [];
        
        if ($status) {
            $conditions[] = "i.status = ?";
            $params[] = $status;
        }
        
        if ($client_id) {
            $conditions[] = "i.client_id = ?";
            $params[] = $client_id;
        }
        
        if ($conditions) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY i.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function get($id) {
        $sql = "SELECT i.*, c.name as client_name, c.email as client_email, u.username 
                FROM invoices i 
                LEFT JOIN clients c ON i.client_id = c.id 
                LEFT JOIN users u ON i.user_id = u.id 
                WHERE i.id = ?";
        
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO invoices (invoice_number, client_id, user_id, amount, status, invoice_type, items, notes, due_date, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        
        $this->db->query($sql, [
            $data['invoice_number'],
            $data['client_id'],
            $data['user_id'],
            $data['amount'],
            $data['status'] ?? 'draft',
            $data['invoice_type'],
            $data['items'],
            $data['notes'],
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
        
        $sql = "UPDATE invoices SET " . implode(", ", $fields) . " WHERE id = ?";
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM invoices WHERE id = ?", [$id]);
    }
    
    public function getByClient($client_id) {
        $sql = "SELECT i.*, u.username 
                FROM invoices i 
                LEFT JOIN users u ON i.user_id = u.id 
                WHERE i.client_id = ? 
                ORDER BY i.created_at DESC";
        
        return $this->db->fetchAll($sql, [$client_id]);
    }
    
    public function getByExternalId($external_id) {
        return $this->db->fetchOne(
            "SELECT * FROM invoices WHERE external_id = ?",
            [$external_id]
        );
    }
    
    public function getExternal() {
        return $this->db->fetchAll(
            "SELECT * FROM invoices WHERE invoice_type != 'builtin' AND external_id IS NOT NULL"
        );
    }
    
    public function getStats() {
        return [
            'total' => $this->db->fetchOne("SELECT COUNT(*) as count FROM invoices")['count'],
            'draft' => $this->db->fetchOne("SELECT COUNT(*) as count FROM invoices WHERE status = 'draft'")['count'],
            'sent' => $this->db->fetchOne("SELECT COUNT(*) as count FROM invoices WHERE status = 'sent'")['count'],
            'paid' => $this->db->fetchOne("SELECT COUNT(*) as count FROM invoices WHERE status = 'paid'")['count'],
            'overdue' => $this->db->fetchOne("SELECT COUNT(*) as count FROM invoices WHERE status = 'overdue'")['count'],
            'total_amount' => $this->db->fetchOne("SELECT SUM(amount) as total FROM invoices WHERE status = 'paid'")['total'] ?? 0,
            'outstanding' => $this->db->fetchOne("SELECT SUM(amount) as total FROM invoices WHERE status IN ('sent', 'overdue')")['total'] ?? 0
        ];
    }
    
    public function getRecent($limit = 5) {
        return $this->db->fetchAll(
            "SELECT i.*, c.name as client_name 
             FROM invoices i 
             LEFT JOIN clients c ON i.client_id = c.id 
             ORDER BY i.created_at DESC 
             LIMIT ?",
            [$limit]
        );
    }
}
