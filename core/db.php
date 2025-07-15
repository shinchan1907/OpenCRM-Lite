<?php
/**
 * Database Connection and Setup
 * Supports SQLite and MySQL
 */

class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        try {
            if (DB_TYPE === 'sqlite') {
                $this->pdo = new PDO('sqlite:' . DB_PATH);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->exec('PRAGMA foreign_keys = ON');
            } else {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $this->pdo = new PDO($dsn, DB_USER, DB_PASS);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}

function get_db() {
    return Database::getInstance();
}

function init_database() {
    $db = get_db();
    
    // Create tables if they don't exist
    $tables = [
        'users' => "
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(20) DEFAULT 'staff',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
        'clients' => "
            CREATE TABLE IF NOT EXISTS clients (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100),
                phone VARCHAR(20),
                company VARCHAR(100),
                address TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
        'tasks' => "
            CREATE TABLE IF NOT EXISTS tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title VARCHAR(200) NOT NULL,
                description TEXT,
                client_id INTEGER,
                user_id INTEGER,
                status VARCHAR(20) DEFAULT 'pending',
                priority VARCHAR(10) DEFAULT 'medium',
                due_date DATE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (client_id) REFERENCES clients(id),
                FOREIGN KEY (user_id) REFERENCES users(id)
            )",
        'invoices' => "
            CREATE TABLE IF NOT EXISTS invoices (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                invoice_number VARCHAR(50) UNIQUE NOT NULL,
                client_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                status VARCHAR(20) DEFAULT 'draft',
                invoice_type VARCHAR(20) DEFAULT 'builtin',
                external_id VARCHAR(100),
                external_url VARCHAR(255),
                items TEXT,
                notes TEXT,
                due_date DATE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (client_id) REFERENCES clients(id),
                FOREIGN KEY (user_id) REFERENCES users(id)
            )",
        'api_tokens' => "
            CREATE TABLE IF NOT EXISTS api_tokens (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                token VARCHAR(255) UNIQUE NOT NULL,
                name VARCHAR(100),
                expires_at DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )",
        'settings' => "
            CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                key VARCHAR(100) UNIQUE NOT NULL,
                value TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
        'webhooks' => "
            CREATE TABLE IF NOT EXISTS webhooks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL,
                url VARCHAR(255) NOT NULL,
                events VARCHAR(255) NOT NULL,
                secret VARCHAR(255),
                active BOOLEAN DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )"
    ];
    
    foreach ($tables as $table => $sql) {
        $db->query($sql);
    }
    
    // Create default admin user if none exists
    $user_count = $db->fetchOne("SELECT COUNT(*) as count FROM users");
    if ($user_count['count'] == 0) {
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $db->query(
            "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)",
            ['admin', 'admin@opencrm.local', $password, 'admin']
        );
    }
}
