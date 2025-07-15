<?php
/**
 * Invoices Controller
 */

require_once __DIR__ . '/model.php';
require_once __DIR__ . '/integrations.php';
require_once __DIR__ . '/../clients/model.php';

class InvoicesController {
    private $model;
    private $clients_model;
    private $integrations;
    
    public function __construct() {
        $this->model = new InvoicesModel();
        $this->clients_model = new ClientsModel();
        $this->integrations = new InvoiceIntegrations();
    }
    
    public function index() {
        $status = $_GET['status'] ?? '';
        $client_id = $_GET['client_id'] ?? '';
        
        $invoices = $this->model->getAll($status, $client_id);
        $clients = $this->clients_model->getAll();
        
        render_view('layout', [
            'title' => 'Invoices',
            'content' => load_view('invoices/index', [
                'invoices' => $invoices,
                'clients' => $clients,
                'status' => $status,
                'client_id' => $client_id
            ])
        ]);
    }
    
    public function form($id = null) {
        $invoice = null;
        if ($id) {
            $invoice = $this->model->get($id);
            if (!$invoice) {
                redirect('/invoices');
                return;
            }
        }
        
        $clients = $this->clients_model->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'client_id' => $_POST['client_id'],
                'amount' => floatval($_POST['amount']),
                'invoice_type' => $_POST['invoice_type'] ?? 'builtin',
                'items' => json_encode($_POST['items'] ?? []),
                'notes' => sanitize($_POST['notes']),
                'due_date' => $_POST['due_date'] ?: null,
                'user_id' => get_logged_in_user()['id']
            ];
            
            if (!$id) {
                $data['invoice_number'] = generate_invoice_number();
            }
            
            // Validation
            $errors = [];
            if (empty($data['client_id'])) {
                $errors[] = 'Client is required';
            }
            if ($data['amount'] <= 0) {
                $errors[] = 'Amount must be greater than 0';
            }
            
            if (empty($errors)) {
                if ($id) {
                    $this->model->update($id, $data);
                    do_action('invoice_updated', $id, $data);
                } else {
                    $invoice_id = $this->model->create($data);
                    
                    // Handle external invoice creation
                    if ($data['invoice_type'] !== 'builtin') {
                        $this->create_external_invoice($invoice_id, $data);
                    }
                    
                    do_action('invoice_created', $invoice_id, $data);
                }
                redirect('/invoices');
                return;
            }
        }
        
        render_view('layout', [
            'title' => $id ? 'Edit Invoice' : 'Create Invoice',
            'content' => load_view('invoices/form', [
                'invoice' => $invoice,
                'clients' => $clients,
                'errors' => $errors ?? []
            ])
        ]);
    }
    
    public function settings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Save integration settings
            $settings = [
                'default_invoice_type' => $_POST['default_invoice_type'],
                'zoho_enabled' => isset($_POST['zoho_enabled']),
                'carter_enabled' => isset($_POST['carter_enabled']),
                'zoho_client_id' => $_POST['zoho_client_id'] ?? '',
                'zoho_client_secret' => $_POST['zoho_client_secret'] ?? '',
                'carter_api_key' => $_POST['carter_api_key'] ?? ''
            ];
            
            foreach ($settings as $key => $value) {
                set_setting($key, $value);
            }
            
            $success = 'Settings saved successfully';
        }
        
        render_view('layout', [
            'title' => 'Invoice Settings',
            'content' => load_view('invoices/settings', [
                'success' => $success ?? null
            ])
        ]);
    }
    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->delete($id);
            do_action('invoice_deleted', $id);
        }
        redirect('/invoices');
    }
    
    public function pdf($id) {
        $invoice = $this->model->get($id);
        if (!$invoice) {
            redirect('/invoices');
            return;
        }
        
        $this->generate_pdf($invoice);
    }
    
    public function send($id) {
        $invoice = $this->model->get($id);
        if (!$invoice) {
            redirect('/invoices');
            return;
        }
        
        if ($invoice['invoice_type'] === 'builtin') {
            // Send built-in invoice via email
            $this->send_builtin_invoice($invoice);
        } else {
            // Send external invoice
            $this->send_external_invoice($invoice);
        }
        
        redirect('/invoices');
    }
    
    public function sync() {
        // Sync all external invoices
        $external_invoices = $this->model->getExternal();
        
        foreach ($external_invoices as $invoice) {
            $this->sync_external_invoice($invoice);
        }
        
        redirect('/invoices');
    }
    
    private function create_external_invoice($invoice_id, $data) {
        $invoice = $this->model->get($invoice_id);
        $client = $this->clients_model->get($data['client_id']);
        
        try {
            switch ($data['invoice_type']) {
                case 'zoho':
                    $external_data = $this->integrations->createZohoInvoice($invoice, $client);
                    break;
                case 'carter':
                    $external_data = $this->integrations->createCarterInvoice($invoice, $client);
                    break;
                default:
                    return;
            }
            
            // Update invoice with external data
            $this->model->update($invoice_id, [
                'external_id' => $external_data['id'],
                'external_url' => $external_data['url'] ?? null
            ]);
            
        } catch (Exception $e) {
            log_error("Failed to create external invoice", [
                'invoice_id' => $invoice_id,
                'type' => $data['invoice_type'],
                'error' => $e->getMessage()
            ]);
        }
    }
    
    private function sync_external_invoice($invoice) {
        try {
            switch ($invoice['invoice_type']) {
                case 'zoho':
                    $status = $this->integrations->getZohoInvoiceStatus($invoice['external_id']);
                    break;
                case 'carter':
                    $status = $this->integrations->getCarterInvoiceStatus($invoice['external_id']);
                    break;
                default:
                    return;
            }
            
            if ($status && $status !== $invoice['status']) {
                $this->model->update($invoice['id'], ['status' => $status]);
                do_action('invoice_synced', $invoice, $status);
            }
            
        } catch (Exception $e) {
            log_error("Failed to sync external invoice", [
                'invoice_id' => $invoice['id'],
                'external_id' => $invoice['external_id'],
                'error' => $e->getMessage()
            ]);
        }
    }
    
    private function generate_pdf($invoice) {
        // Basic PDF generation - you might want to use a proper PDF library
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="invoice-' . $invoice['invoice_number'] . '.pdf"');
        
        // Simple text-based PDF content
        echo "%PDF-1.4\n";
        echo "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        echo "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        echo "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R >>\nendobj\n";
        echo "4 0 obj\n<< /Length 50 >>\nstream\nBT\n/F1 12 Tf\n100 700 Td\n(Invoice " . $invoice['invoice_number'] . ") Tj\nET\nendstream\nendobj\n";
        echo "xref\n0 5\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000214 00000 n \n";
        echo "trailer\n<< /Size 5 /Root 1 0 R >>\nstartxref\n314\n%%EOF";
    }
    
    private function send_builtin_invoice($invoice) {
        // Email invoice implementation
        // This would integrate with your email system
        do_action('invoice_sent', $invoice);
    }
    
    private function send_external_invoice($invoice) {
        // Send external invoice
        try {
            switch ($invoice['invoice_type']) {
                case 'zoho':
                    $this->integrations->sendZohoInvoice($invoice['external_id']);
                    break;
                case 'carter':
                    $this->integrations->sendCarterInvoice($invoice['external_id']);
                    break;
            }
            do_action('external_invoice_sent', $invoice);
        } catch (Exception $e) {
            log_error("Failed to send external invoice", [
                'invoice_id' => $invoice['id'],
                'error' => $e->getMessage()
            ]);
        }
    }
}
