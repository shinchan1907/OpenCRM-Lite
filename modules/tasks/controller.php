<?php
/**
 * Tasks Controller
 */

require_once __DIR__ . '/model.php';
require_once __DIR__ . '/../clients/model.php';

class TasksController {
    private $model;
    private $clients_model;
    
    public function __construct() {
        $this->model = new TasksModel();
        $this->clients_model = new ClientsModel();
    }
    
    public function index() {
        $status = $_GET['status'] ?? '';
        $client_id = $_GET['client_id'] ?? '';
        
        $tasks = $this->model->getAll($status, $client_id);
        $clients = $this->clients_model->getAll();
        
        render_view('layout', [
            'title' => 'Tasks',
            'content' => load_view('tasks/index', [
                'tasks' => $tasks,
                'clients' => $clients,
                'status' => $status,
                'client_id' => $client_id
            ])
        ]);
    }
    
    public function form($id = null) {
        $task = null;
        if ($id) {
            $task = $this->model->get($id);
            if (!$task) {
                redirect('/tasks');
                return;
            }
        }
        
        $clients = $this->clients_model->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => sanitize($_POST['title']),
                'description' => sanitize($_POST['description']),
                'client_id' => $_POST['client_id'] ?: null,
                'status' => sanitize($_POST['status']),
                'priority' => sanitize($_POST['priority']),
                'due_date' => $_POST['due_date'] ?: null,
                'user_id' => get_logged_in_user()['id']
            ];
            
            // Validation
            $errors = [];
            if (empty($data['title'])) {
                $errors[] = 'Title is required';
            }
            
            if (empty($errors)) {
                if ($id) {
                    $this->model->update($id, $data);
                    do_action('task_updated', $id, $data);
                } else {
                    $task_id = $this->model->create($data);
                    do_action('task_created', $task_id, $data);
                }
                redirect('/tasks');
                return;
            }
        }
        
        render_view('layout', [
            'title' => $id ? 'Edit Task' : 'Add Task',
            'content' => load_view('tasks/form', [
                'task' => $task,
                'clients' => $clients,
                'errors' => $errors ?? []
            ])
        ]);
    }
    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->delete($id);
            do_action('task_deleted', $id);
        }
        redirect('/tasks');
    }
    
    public function update_status() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task_id = $_POST['task_id'];
            $status = $_POST['status'];
            
            $this->model->update($task_id, ['status' => $status]);
            do_action('task_status_updated', $task_id, $status);
            
            json_response(['success' => true]);
        }
    }
}
