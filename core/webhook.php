<?php
/**
 * Webhook Handler
 * Receives and dispatches webhook events
 */

function handle_webhook_request() {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = str_replace('/webhook/', '', $uri);
    $path = trim($path, '/');
    
    $parts = explode('/', $path);
    $webhook_type = $parts[0] ?? '';
    
    switch ($webhook_type) {
        case 'invoice-status':
            handle_invoice_status_webhook();
            break;
        case 'zoho':
            handle_zoho_webhook();
            break;
        case 'carter':
            handle_carter_webhook();
            break;
        default:
            http_response_code(404);
            echo "Webhook endpoint not found";
    }
}

function handle_invoice_status_webhook() {
    $payload = json_decode(file_get_contents('php://input'), true);
    
    if (!$payload) {
        http_response_code(400);
        echo "Invalid payload";
        return;
    }
    
    // Verify webhook signature if provided
    $signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? '';
    if ($signature && !verify_webhook_signature($payload, $signature)) {
        http_response_code(401);
        echo "Invalid signature";
        return;
    }
    
    // Update invoice status
    if (isset($payload['invoice_id'], $payload['status'])) {
        require_once __DIR__ . '/../modules/invoices/model.php';
        $model = new InvoicesModel();
        
        $invoice = $model->getByExternalId($payload['invoice_id']);
        if ($invoice) {
            $model->update($invoice['id'], ['status' => $payload['status']]);
            do_action('invoice_status_updated', $invoice, $payload['status']);
        }
    }
    
    echo "OK";
}

function handle_zoho_webhook() {
    $payload = json_decode(file_get_contents('php://input'), true);
    
    if (isset($payload['invoice'])) {
        require_once __DIR__ . '/../modules/invoices/model.php';
        $model = new InvoicesModel();
        
        $invoice_data = $payload['invoice'];
        $external_id = $invoice_data['invoice_id'];
        
        $invoice = $model->getByExternalId($external_id);
        if ($invoice) {
            $update_data = [
                'status' => strtolower($invoice_data['status']),
                'external_url' => $invoice_data['invoice_url'] ?? null
            ];
            $model->update($invoice['id'], $update_data);
            do_action('zoho_invoice_updated', $invoice, $invoice_data);
        }
    }
    
    echo "OK";
}

function handle_carter_webhook() {
    $payload = json_decode(file_get_contents('php://input'), true);
    
    if (isset($payload['event'], $payload['data'])) {
        $event = $payload['event'];
        $data = $payload['data'];
        
        if ($event === 'invoice.updated' || $event === 'invoice.paid') {
            require_once __DIR__ . '/../modules/invoices/model.php';
            $model = new InvoicesModel();
            
            $external_id = $data['id'];
            $invoice = $model->getByExternalId($external_id);
            
            if ($invoice) {
                $update_data = ['status' => $data['status']];
                $model->update($invoice['id'], $update_data);
                do_action('carter_invoice_updated', $invoice, $data);
            }
        }
    }
    
    echo "OK";
}

function verify_webhook_signature($payload, $signature) {
    $secret = get_setting('webhook_secret');
    if (!$secret) {
        return true; // No secret configured
    }
    
    $computed_signature = hash_hmac('sha256', json_encode($payload), $secret);
    return hash_equals($signature, $computed_signature);
}

function register_webhook($name, $url, $events, $secret = null) {
    $db = get_db();
    
    $db->query(
        "INSERT INTO webhooks (name, url, events, secret) VALUES (?, ?, ?, ?)",
        [$name, $url, is_array($events) ? implode(',', $events) : $events, $secret]
    );
    
    return $db->lastInsertId();
}

function get_webhooks($active_only = true) {
    $db = get_db();
    
    $sql = "SELECT * FROM webhooks";
    if ($active_only) {
        $sql .= " WHERE active = 1";
    }
    
    return $db->fetchAll($sql);
}

function trigger_webhook($event, $data) {
    $webhooks = get_webhooks();
    
    foreach ($webhooks as $webhook) {
        $events = explode(',', $webhook['events']);
        if (in_array($event, $events) || in_array('*', $events)) {
            send_webhook($webhook, $event, $data);
        }
    }
}

function send_webhook($webhook, $event, $data) {
    $payload = [
        'event' => $event,
        'data' => $data,
        'timestamp' => time()
    ];
    
    $json_payload = json_encode($payload);
    
    $headers = [
        'Content-Type: application/json',
        'User-Agent: OpenCRM-Lite/1.0'
    ];
    
    if ($webhook['secret']) {
        $signature = hash_hmac('sha256', $json_payload, $webhook['secret']);
        $headers[] = 'X-Webhook-Signature: ' . $signature;
    }
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $webhook['url'],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $json_payload,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Log webhook delivery
    log_error("Webhook delivered: {$webhook['name']} -> {$event}", [
        'webhook_id' => $webhook['id'],
        'event' => $event,
        'response_code' => $http_code,
        'response' => $response
    ]);
}
