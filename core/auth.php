<?php
/**
 * Authentication and Session Management
 */

function login_user($username, $password) {
    $db = get_db();
    
    $user = $db->fetchOne(
        "SELECT * FROM users WHERE username = ? OR email = ?",
        [$username, $username]
    );
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        
        do_action('user_login', $user);
        return true;
    }
    
    return false;
}

function logout_user() {
    do_action('user_logout', get_logged_in_user());
    session_destroy();
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function get_logged_in_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    $db = get_db();
    return $db->fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

function is_admin() {
    return is_logged_in() && $_SESSION['user_role'] === 'admin';
}

function require_auth() {
    if (!is_logged_in()) {
        redirect('/login');
        exit;
    }
}

function require_admin() {
    require_auth();
    if (!is_admin()) {
        http_response_code(403);
        echo "Access denied. Admin privileges required.";
        exit;
    }
}

function generate_api_token($user_id, $name = null, $expires_at = null) {
    $db = get_db();
    $token = bin2hex(random_bytes(32));
    
    $db->query(
        "INSERT INTO api_tokens (user_id, token, name, expires_at) VALUES (?, ?, ?, ?)",
        [$user_id, $token, $name, $expires_at]
    );
    
    return $token;
}

function validate_api_token($token) {
    $db = get_db();
    
    $token_data = $db->fetchOne(
        "SELECT t.*, u.* FROM api_tokens t 
         JOIN users u ON t.user_id = u.id 
         WHERE t.token = ? AND (t.expires_at IS NULL OR t.expires_at > NOW())",
        [$token]
    );
    
    return $token_data;
}
