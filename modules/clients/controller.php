<?php
/**
 * Clients Controller
 */

require_once __DIR__ . '/model.php';

class ClientsController {
    private $model;
    
    public function __construct() {
        $this->model = new ClientsModel();
    }
    
    public function index() {
        $search = $_GET['search'] ?? '';
        $clients = $this->model->getAll($search);
        
        render_view('layout', [
            'title' => 'Clients',
            'content' => load_view('clients/index', ['clients' => $clients, 'search' => $search])
        ]);
    }
    
    public function form($id = null) {
        $client = null;
        if ($id) {
            $client = $this->model->get($id);
            if (!$client) {
                redirect('/clients');
                return;
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name']),
                'email' => sanitize($_POST['email']),
                'phone' => sanitize($_POST['phone']),
                'company' => sanitize($_POST['company']),
                'address' => sanitize($_POST['address'])
            ];
            
            // Validation
            $errors = [];
            if (empty($data['name'])) {
                $errors[] = 'Name is required';
            }
            if (!empty($data['email']) && !validate_email($data['email'])) {
                $errors[] = 'Invalid email address';
            }
            
            if (empty($errors)) {
                if ($id) {
                    $this->model->update($id, $data);
                    do_action('client_updated', $id, $data);
                } else {
                    $client_id = $this->model->create($data);
                    do_action('client_created', $client_id, $data);
                }
                redirect('/clients');
                return;
            }
        }
        
        render_view('layout', [
            'title' => $id ? 'Edit Client' : 'Add Client',
            'content' => load_view('clients/form', [
                'client' => $client,
                'errors' => $errors ?? []
            ])
        ]);
    }
    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->delete($id);
            do_action('client_deleted', $id);
        }
        redirect('/clients');
    }
    
    public function view($id) {
        $client = $this->model->get($id);
        if (!$client) {
            redirect('/clients');
            return;
        }
        
        // Get related tasks and invoices
        require_once __DIR__ . '/../tasks/model.php';
        require_once __DIR__ . '/../invoices/model.php';
        
        $tasks_model = new TasksModel();
        $invoices_model = new InvoicesModel();
        
        $tasks = $tasks_model->getByClient($id);
        $invoices = $invoices_model->getByClient($id);
        
        render_view('layout', [
            'title' => 'Client: ' . $client['name'],
            'content' => load_view('clients/view', [
                'client' => $client,
                'tasks' => $tasks,
                'invoices' => $invoices
            ])
        ]);
    }
}
