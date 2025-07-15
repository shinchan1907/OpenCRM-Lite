<?php
/**
 * REST API Handler
 * Provides token-based authentication for external integrations
 */

function handle_api_request() {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Remove /api/v1 prefix
    $path = str_replace('/api/v1', '', $uri);
    $path = trim($path, '/');
    
    // Get auth token
    $token = get_auth_token();
    if (!$token) {
        json_response(['error' => 'Authentication required'], 401);
    }
    
    $user = validate_api_token($token);
    if (!$user) {
        json_response(['error' => 'Invalid token'], 401);
    }
    
    // Set current user context
    $_SESSION['api_user'] = $user;
    
    // Route API request
    $parts = explode('/', $path);
    $resource = $parts[0] ?? '';
    $id = $parts[1] ?? null;
    
    try {
        switch ($resource) {
            case 'clients':
                handle_clients_api($method, $id);
                break;
            case 'tasks':
                handle_tasks_api($method, $id);
                break;
            case 'invoices':
                handle_invoices_api($method, $id);
                break;
            case 'users':
                handle_users_api($method, $id);
                break;
            default:
                json_response(['error' => 'Resource not found'], 404);
        }
    } catch (Exception $e) {
        json_response(['error' => $e->getMessage()], 500);
    }
}

function get_auth_token() {
    // Check Authorization header
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $auth = $headers['Authorization'];
        if (preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
            return $matches[1];
        }
    }
    
    // Check query parameter
    return $_GET['token'] ?? null;
}

function handle_clients_api($method, $id) {
    require_once __DIR__ . '/../modules/clients/model.php';
    $model = new ClientsModel();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                $client = $model->get($id);
                if (!$client) {
                    json_response(['error' => 'Client not found'], 404);
                }
                json_response($client);
            } else {
                $clients = $model->getAll();
                json_response($clients);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $client_id = $model->create($data);
            json_response(['id' => $client_id, 'message' => 'Client created'], 201);
            break;
            
        case 'PUT':
            if (!$id) {
                json_response(['error' => 'Client ID required'], 400);
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $model->update($id, $data);
            json_response(['message' => 'Client updated']);
            break;
            
        case 'DELETE':
            if (!$id) {
                json_response(['error' => 'Client ID required'], 400);
            }
            $model->delete($id);
            json_response(['message' => 'Client deleted']);
            break;
            
        default:
            json_response(['error' => 'Method not allowed'], 405);
    }
}

function handle_tasks_api($method, $id) {
    require_once __DIR__ . '/../modules/tasks/model.php';
    $model = new TasksModel();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                $task = $model->get($id);
                if (!$task) {
                    json_response(['error' => 'Task not found'], 404);
                }
                json_response($task);
            } else {
                $tasks = $model->getAll();
                json_response($tasks);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $task_id = $model->create($data);
            json_response(['id' => $task_id, 'message' => 'Task created'], 201);
            break;
            
        case 'PUT':
            if (!$id) {
                json_response(['error' => 'Task ID required'], 400);
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $model->update($id, $data);
            json_response(['message' => 'Task updated']);
            break;
            
        case 'DELETE':
            if (!$id) {
                json_response(['error' => 'Task ID required'], 400);
            }
            $model->delete($id);
            json_response(['message' => 'Task deleted']);
            break;
            
        default:
            json_response(['error' => 'Method not allowed'], 405);
    }
}

function handle_invoices_api($method, $id) {
    require_once __DIR__ . '/../modules/invoices/model.php';
    $model = new InvoicesModel();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                $invoice = $model->get($id);
                if (!$invoice) {
                    json_response(['error' => 'Invoice not found'], 404);
                }
                json_response($invoice);
            } else {
                $invoices = $model->getAll();
                json_response($invoices);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $invoice_id = $model->create($data);
            json_response(['id' => $invoice_id, 'message' => 'Invoice created'], 201);
            break;
            
        case 'PUT':
            if (!$id) {
                json_response(['error' => 'Invoice ID required'], 400);
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $model->update($id, $data);
            json_response(['message' => 'Invoice updated']);
            break;
            
        case 'DELETE':
            if (!$id) {
                json_response(['error' => 'Invoice ID required'], 400);
            }
            $model->delete($id);
            json_response(['message' => 'Invoice deleted']);
            break;
            
        default:
            json_response(['error' => 'Method not allowed'], 405);
    }
}

function handle_users_api($method, $id) {
    // Require admin access for user management
    if ($_SESSION['api_user']['role'] !== 'admin') {
        json_response(['error' => 'Admin access required'], 403);
    }
    
    require_once __DIR__ . '/../modules/users/model.php';
    $model = new UsersModel();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                $user = $model->get($id);
                if (!$user) {
                    json_response(['error' => 'User not found'], 404);
                }
                unset($user['password']); // Don't expose password
                json_response($user);
            } else {
                $users = $model->getAll();
                foreach ($users as &$user) {
                    unset($user['password']);
                }
                json_response($users);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $user_id = $model->create($data);
            json_response(['id' => $user_id, 'message' => 'User created'], 201);
            break;
            
        case 'PUT':
            if (!$id) {
                json_response(['error' => 'User ID required'], 400);
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $model->update($id, $data);
            json_response(['message' => 'User updated']);
            break;
            
        case 'DELETE':
            if (!$id) {
                json_response(['error' => 'User ID required'], 400);
            }
            $model->delete($id);
            json_response(['message' => 'User deleted']);
            break;
            
        default:
            json_response(['error' => 'Method not allowed'], 405);
    }
}
