<?php
/**
 * Users Controller
 */

require_once __DIR__ . '/model.php';

class UsersController {
    private $model;
    
    public function __construct() {
        $this->model = new UsersModel();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = sanitize($_POST['username']);
            $password = $_POST['password'];
            
            if (login_user($username, $password)) {
                redirect('/dashboard');
                return;
            } else {
                $error = 'Invalid username or password';
            }
        }
        
        // Don't use layout for login page
        render_view('login', ['error' => $error ?? null]);
    }
    
    public function logout() {
        logout_user();
        redirect('/login');
    }
    
    public function index() {
        require_admin();
        
        $users = $this->model->getAll();
        
        render_view('layout', [
            'title' => 'Users',
            'content' => load_view('users/index', ['users' => $users])
        ]);
    }
    
    public function form($id = null) {
        require_admin();
        
        $user = null;
        if ($id) {
            $user = $this->model->get($id);
            if (!$user) {
                redirect('/users');
                return;
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => sanitize($_POST['username']),
                'email' => sanitize($_POST['email']),
                'role' => sanitize($_POST['role'])
            ];
            
            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            // Validation
            $errors = [];
            if (empty($data['username'])) {
                $errors[] = 'Username is required';
            }
            if (empty($data['email']) || !validate_email($data['email'])) {
                $errors[] = 'Valid email is required';
            }
            if (!$id && empty($_POST['password'])) {
                $errors[] = 'Password is required for new users';
            }
            
            // Check for duplicate username/email
            $existing = $this->model->getByUsername($data['username']);
            if ($existing && (!$id || $existing['id'] != $id)) {
                $errors[] = 'Username already exists';
            }
            
            $existing = $this->model->getByEmail($data['email']);
            if ($existing && (!$id || $existing['id'] != $id)) {
                $errors[] = 'Email already exists';
            }
            
            if (empty($errors)) {
                if ($id) {
                    $this->model->update($id, $data);
                    do_action('user_updated', $id, $data);
                } else {
                    $user_id = $this->model->create($data);
                    do_action('user_created', $user_id, $data);
                }
                redirect('/users');
                return;
            }
        }
        
        render_view('layout', [
            'title' => $id ? 'Edit User' : 'Add User',
            'content' => load_view('users/form', [
                'user' => $user,
                'errors' => $errors ?? []
            ])
        ]);
    }
    
    public function delete($id) {
        require_admin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Don't allow deleting the current user
            if ($id == get_logged_in_user()['id']) {
                redirect('/users');
                return;
            }
            
            $this->model->delete($id);
            do_action('user_deleted', $id);
        }
        redirect('/users');
    }
    
    public function profile() {
        $user = get_logged_in_user();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => sanitize($_POST['username']),
                'email' => sanitize($_POST['email'])
            ];
            
            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            // Validation
            $errors = [];
            if (empty($data['username'])) {
                $errors[] = 'Username is required';
            }
            if (empty($data['email']) || !validate_email($data['email'])) {
                $errors[] = 'Valid email is required';
            }
            
            if (empty($errors)) {
                $this->model->update($user['id'], $data);
                do_action('profile_updated', $user['id'], $data);
                $success = 'Profile updated successfully';
                $user = $this->model->get($user['id']); // Refresh user data
            }
        }
        
        render_view('layout', [
            'title' => 'My Profile',
            'content' => load_view('users/profile', [
                'user' => $user,
                'errors' => $errors ?? [],
                'success' => $success ?? null
            ])
        ]);
    }
    
    public function api_tokens() {
        $user = get_logged_in_user();
        $tokens = $this->model->getApiTokens($user['id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = sanitize($_POST['name']);
            $expires_at = $_POST['expires_at'] ?: null;
            
            $token = generate_api_token($user['id'], $name, $expires_at);
            $success = "API token created: $token";
            $tokens = $this->model->getApiTokens($user['id']); // Refresh
        }
        
        render_view('layout', [
            'title' => 'API Tokens',
            'content' => load_view('users/api_tokens', [
                'tokens' => $tokens,
                'success' => $success ?? null
            ])
        ]);
    }
}
