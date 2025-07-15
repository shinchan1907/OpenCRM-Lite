<?php
/**
 * Users Model
 */

class UsersModel {
    private $db;
    
    public function __construct() {
        $this->db = get_db();
    }
    
    public function getAll() {
        return $this->db->fetchAll("SELECT id, username, email, role, created_at FROM users ORDER BY username ASC");
    }
    
    public function get($id) {
        return $this->db->fetchOne("SELECT id, username, email, role, created_at FROM users WHERE id = ?", [$id]);
    }
    
    public function getByUsername($username) {
        return $this->db->fetchOne("SELECT * FROM users WHERE username = ?", [$username]);
    }
    
    public function getByEmail($email) {
        return $this->db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO users (username, email, password, role, created_at, updated_at) 
                VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        
        $this->db->query($sql, [
            $data['username'],
            $data['email'],
            $data['password'],
            $data['role'] ?? 'staff'
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }
        
        $fields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;
        
        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
    }
    
    public function getApiTokens($user_id) {
        return $this->db->fetchAll(
            "SELECT id, token, name, expires_at, created_at FROM api_tokens WHERE user_id = ? ORDER BY created_at DESC",
            [$user_id]
        );
    }
    
    public function deleteApiToken($token_id, $user_id) {
        return $this->db->query("DELETE FROM api_tokens WHERE id = ? AND user_id = ?", [$token_id, $user_id]);
    }
}
