<?php
/**
 * Invoice Third-Party Integrations
 * Handles Zoho Invoice, Carter Finance, and other external invoice systems
 */

class InvoiceIntegrations {
    
    // Zoho Invoice Integration
    public function createZohoInvoice($invoice, $client) {
        $access_token = $this->getZohoAccessToken();
        
        $invoice_data = [
            'customer_name' => $client['name'],
            'customer_email' => $client['email'],
            'invoice_number' => $invoice['invoice_number'],
            'date' => date('Y-m-d'),
            'due_date' => $invoice['due_date'],
            'line_items' => json_decode($invoice['items'], true) ?: [
                [
                    'name' => 'Service',
                    'rate' => $invoice['amount'],
                    'quantity' => 1
                ]
            ],
            'notes' => $invoice['notes']
        ];
        
        $response = $this->makeZohoRequest('POST', 'invoices', $invoice_data, $access_token);
        
        if ($response && isset($response['invoice'])) {
            return [
                'id' => $response['invoice']['invoice_id'],
                'url' => $response['invoice']['invoice_url']
            ];
        }
        
        throw new Exception('Failed to create Zoho invoice');
    }
    
    public function getZohoInvoiceStatus($external_id) {
        $access_token = $this->getZohoAccessToken();
        $response = $this->makeZohoRequest('GET', "invoices/$external_id", null, $access_token);
        
        if ($response && isset($response['invoice'])) {
            return strtolower($response['invoice']['status']);
        }
        
        return null;
    }
    
    public function sendZohoInvoice($external_id) {
        $access_token = $this->getZohoAccessToken();
        return $this->makeZohoRequest('POST', "invoices/$external_id/status/sent", [], $access_token);
    }
    
    private function getZohoAccessToken() {
        // Implement OAuth flow for Zoho
        // This is a simplified version - you'd need proper OAuth implementation
        $refresh_token = get_setting('zoho_refresh_token');
        
        if (!$refresh_token) {
            throw new Exception('Zoho not properly configured');
        }
        
        $data = [
            'refresh_token' => $refresh_token,
            'client_id' => ZOHO_CLIENT_ID,
            'client_secret' => ZOHO_CLIENT_SECRET,
            'grant_type' => 'refresh_token'
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://accounts.zoho.com/oauth/v2/token',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200) {
            $token_data = json_decode($response, true);
            return $token_data['access_token'];
        }
        
        throw new Exception('Failed to refresh Zoho access token');
    }
    
    private function makeZohoRequest($method, $endpoint, $data, $access_token) {
        $url = "https://invoice.zoho.com/api/v3/$endpoint";
        
        $headers = [
            'Authorization: Zoho-oauthtoken ' . $access_token,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code >= 200 && $http_code < 300) {
            return json_decode($response, true);
        }
        
        log_error("Zoho API Error", [
            'method' => $method,
            'endpoint' => $endpoint,
            'http_code' => $http_code,
            'response' => $response
        ]);
        
        return null;
    }
    
    // Carter Finance Integration
    public function createCarterInvoice($invoice, $client) {
        $api_key = CARTER_API_KEY;
        
        if (!$api_key) {
            throw new Exception('Carter Finance not properly configured');
        }
        
        $invoice_data = [
            'client' => [
                'name' => $client['name'],
                'email' => $client['email'],
                'company' => $client['company']
            ],
            'invoice_number' => $invoice['invoice_number'],
            'amount' => $invoice['amount'],
            'currency' => 'USD',
            'due_date' => $invoice['due_date'],
            'description' => $invoice['notes'],
            'line_items' => json_decode($invoice['items'], true) ?: [
                [
                    'description' => 'Service',
                    'amount' => $invoice['amount'],
                    'quantity' => 1
                ]
            ]
        ];
        
        $response = $this->makeCarterRequest('POST', 'invoices', $invoice_data, $api_key);
        
        if ($response && isset($response['id'])) {
            return [
                'id' => $response['id'],
                'url' => $response['invoice_url'] ?? null
            ];
        }
        
        throw new Exception('Failed to create Carter invoice');
    }
    
    public function getCarterInvoiceStatus($external_id) {
        $api_key = CARTER_API_KEY;
        $response = $this->makeCarterRequest('GET', "invoices/$external_id", null, $api_key);
        
        if ($response && isset($response['status'])) {
            return strtolower($response['status']);
        }
        
        return null;
    }
    
    public function sendCarterInvoice($external_id) {
        $api_key = CARTER_API_KEY;
        return $this->makeCarterRequest('POST', "invoices/$external_id/send", [], $api_key);
    }
    
    private function makeCarterRequest($method, $endpoint, $data, $api_key) {
        $url = "https://api.carterfinance.com/v1/$endpoint";
        
        $headers = [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code >= 200 && $http_code < 300) {
            return json_decode($response, true);
        }
        
        log_error("Carter API Error", [
            'method' => $method,
            'endpoint' => $endpoint,
            'http_code' => $http_code,
            'response' => $response
        ]);
        
        return null;
    }
    
    // Generic invoice integration framework
    public function createInvoice($type, $invoice, $client) {
        switch ($type) {
            case 'zoho':
                return $this->createZohoInvoice($invoice, $client);
            case 'carter':
                return $this->createCarterInvoice($invoice, $client);
            default:
                throw new Exception("Unknown invoice type: $type");
        }
    }
    
    public function getInvoiceStatus($type, $external_id) {
        switch ($type) {
            case 'zoho':
                return $this->getZohoInvoiceStatus($external_id);
            case 'carter':
                return $this->getCarterInvoiceStatus($external_id);
            default:
                return null;
        }
    }
    
    public function sendInvoice($type, $external_id) {
        switch ($type) {
            case 'zoho':
                return $this->sendZohoInvoice($external_id);
            case 'carter':
                return $this->sendCarterInvoice($external_id);
            default:
                throw new Exception("Unknown invoice type: $type");
        }
    }
}
