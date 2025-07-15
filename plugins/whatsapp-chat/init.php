<?php
/**
 * WhatsApp Chat Widget Plugin
 * Adds a floating WhatsApp chat button for client communication
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class WhatsAppChatWidget {
    private $plugin_url;
    private $settings;
    
    public function __construct() {
        $this->plugin_url = '/plugins/whatsapp-chat';
        $this->settings = $this->get_settings();
        
        // Register hooks
        add_action('init', [$this, 'init']);
        add_action('wp_footer', [$this, 'render_widget']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }
    
    public function init() {
        // Plugin initialization
        $this->load_textdomain();
    }
    
    public function enqueue_assets() {
        if (!$this->should_show_widget()) {
            return;
        }
        
        // Enqueue CSS
        echo '<link rel="stylesheet" href="' . $this->plugin_url . '/assets/whatsapp-widget.css">';
        
        // Enqueue JavaScript
        echo '<script src="' . $this->plugin_url . '/assets/whatsapp-widget.js"></script>';
    }
    
    public function render_widget() {
        if (!$this->should_show_widget()) {
            return;
        }
        
        $phone = $this->get_setting('phone_number');
        $message = $this->get_setting('welcome_message', 'Hello! I\'m interested in your services.');
        $position = $this->get_setting('position', 'bottom-right');
        
        if (empty($phone)) {
            return;
        }
        
        // Clean phone number
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Generate WhatsApp URL
        $whatsapp_url = 'https://wa.me/' . ltrim($phone, '+') . '?text=' . urlencode($message);
        
        ?>
        <div id="whatsapp-chat-widget" class="whatsapp-widget position-<?= $position ?>" data-position="<?= $position ?>">
            <a href="<?= $whatsapp_url ?>" target="_blank" rel="noopener" class="whatsapp-button" title="Chat with us on WhatsApp">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.516" fill="currentColor"/>
                </svg>
                <span class="whatsapp-tooltip">Chat with us</span>
            </a>
        </div>
        
        <style>
        .whatsapp-widget {
            position: fixed;
            z-index: 9999;
            transition: all 0.3s ease;
        }
        
        .whatsapp-widget.position-bottom-right {
            bottom: 20px;
            right: 20px;
        }
        
        .whatsapp-widget.position-bottom-left {
            bottom: 20px;
            left: 20px;
        }
        
        .whatsapp-widget.position-top-right {
            top: 80px;
            right: 20px;
        }
        
        .whatsapp-widget.position-top-left {
            top: 80px;
            left: 20px;
        }
        
        .whatsapp-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: #25D366;
            border-radius: 50%;
            color: white;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .whatsapp-button:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .whatsapp-tooltip {
            position: absolute;
            right: 70px;
            top: 50%;
            transform: translateY(-50%);
            background: #333;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        
        .whatsapp-tooltip::after {
            content: '';
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 6px solid transparent;
            border-left-color: #333;
        }
        
        .whatsapp-button:hover .whatsapp-tooltip {
            opacity: 1;
        }
        
        @media (max-width: 768px) {
            .whatsapp-widget {
                bottom: 80px;
            }
            
            .whatsapp-button {
                width: 50px;
                height: 50px;
            }
            
            .whatsapp-tooltip {
                display: none;
            }
        }
        </style>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const widget = document.getElementById('whatsapp-chat-widget');
            if (widget) {
                // Add entrance animation
                setTimeout(() => {
                    widget.style.animation = 'bounceIn 0.6s ease-out';
                }, 1000);
                
                // Add pulse animation every 10 seconds
                setInterval(() => {
                    const button = widget.querySelector('.whatsapp-button');
                    button.style.animation = 'pulse 1s ease-in-out';
                    setTimeout(() => {
                        button.style.animation = '';
                    }, 1000);
                }, 10000);
            }
        });
        
        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes bounceIn {
                0% { transform: scale(0); opacity: 0; }
                50% { transform: scale(1.2); opacity: 0.8; }
                100% { transform: scale(1); opacity: 1; }
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(style);
        </script>
        <?php
    }
    
    private function should_show_widget() {
        if (!$this->get_setting('enabled', true)) {
            return false;
        }
        
        $show_on_pages = $this->get_setting('show_on_pages', ['all']);
        
        if (in_array('all', $show_on_pages)) {
            return true;
        }
        
        $current_page = $this->get_current_page();
        return in_array($current_page, $show_on_pages);
    }
    
    private function get_current_page() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = trim($uri, '/');
        
        if (empty($uri) || $uri === 'dashboard') {
            return 'dashboard';
        }
        
        $parts = explode('/', $uri);
        return $parts[0];
    }
    
    private function get_settings() {
        // Get plugin settings from database
        $settings = get_setting('whatsapp_chat_settings');
        return $settings ? json_decode($settings, true) : [];
    }
    
    private function get_setting($key, $default = null) {
        return isset($this->settings[$key]) ? $this->settings[$key] : $default;
    }
    
    public function update_settings($new_settings) {
        $this->settings = array_merge($this->settings, $new_settings);
        set_setting('whatsapp_chat_settings', json_encode($this->settings));
    }
    
    private function load_textdomain() {
        // Load plugin translations if needed
    }
}

// Initialize the plugin
new WhatsAppChatWidget();

// Add plugin settings page hook
add_action('admin_init', function() {
    if (isset($_POST['save_whatsapp_settings'])) {
        $widget = new WhatsAppChatWidget();
        $settings = [
            'phone_number' => sanitize($_POST['phone_number']),
            'welcome_message' => sanitize($_POST['welcome_message']),
            'position' => sanitize($_POST['position']),
            'show_on_pages' => $_POST['show_on_pages'] ?? [],
            'enabled' => isset($_POST['enabled'])
        ];
        $widget->update_settings($settings);
        $success = 'WhatsApp settings saved successfully!';
    }
});
