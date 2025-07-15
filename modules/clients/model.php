<?php
/**
 * Clients Model
 */

class ClientsModel {
    private $db;
    
    public function __construct() {
        $this->db = get_db();
    }
    
    public function getAll($search = '') {
        $sql = "SELECT * FROM clients";
        $params = [];
        
        if ($search) {
            $sql .= " WHERE name LIKE ? OR email LIKE ? OR company LIKE ?";
            $search_term = "%$search%";
            $params = [$search_term, $search_term, $search_term];
        }
        
        $sql .= " ORDER BY name ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function get($id) {
        return $this->db->fetchOne("SELECT * FROM clients WHERE id = ?", [$id]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO clients (name, email, phone, company, address, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        
        $this->db->query($sql, [
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['company'],
            $data['address']
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE clients SET name = ?, email = ?, phone = ?, company = ?, address = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        
        return $this->db->query($sql, [
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['company'],
            $data['address'],
            $id
        ]);
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM clients WHERE id = ?", [$id]);
    }
    
    public function search($term) {
        $sql = "SELECT * FROM clients WHERE name LIKE ? OR email LIKE ? OR company LIKE ? ORDER BY name ASC";
        $search_term = "%$term%";
        return $this->db->fetchAll($sql, [$search_term, $search_term, $search_term]);
    }
    
    public function getCount() {
        $result = $this->db->fetchOne("SELECT COUNT(*) as count FROM clients");
        return $result['count'];
    }
    
    public function getRecentlyAdded($limit = 5) {
        return $this->db->fetchAll(
            "SELECT * FROM clients ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }
}
