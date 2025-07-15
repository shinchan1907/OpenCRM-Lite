# OpenCRM Lite - System Documentation

## Overview

OpenCRM Lite is a fast, modular, and self-hosted CRM designed for freelancers, solopreneurs, and small agencies. Built with vanilla PHP (no heavy frameworks), it emphasizes simplicity, modularity, and ease of deployment on any shared or local PHP server. The system follows a plugin-based architecture similar to WordPress, allowing for easy extensibility through themes and plugins.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Core Architecture
- **Language**: Vanilla PHP (no frameworks)
- **Architecture Pattern**: Modular MVC with plugin system
- **Database**: PDO-based abstraction supporting SQLite and MySQL
- **Routing**: Simple custom router mapping URLs to controllers
- **Authentication**: Session-based with token support
- **API**: RESTful API endpoints
- **Plugin System**: WordPress-style hooks and actions

### Frontend Architecture
- **CSS Framework**: Custom CSS with utility classes
- **JavaScript**: Vanilla JavaScript with modular initialization
- **Icons**: Feather Icons
- **Themes**: Overridable template system
- **Responsive Design**: Mobile-first approach

## Key Components

### Core System (`core/`)
- **init.php**: Application bootstrap loading config, plugins, modules, and themes
- **db.php**: Database connection and PDO abstraction layer
- **auth.php**: Authentication, session management, and token handling
- **api.php**: REST API entry point and routing
- **webhook.php**: Webhook registration and dispatch system
- **functions.php**: Shared utility functions (sanitization, redirects, etc.)
- **hooks.php**: Plugin system implementation (add_action, do_action)

### Modules (`modules/`)
Core business logic modules including:
- **clients/**: Client management
- **tasks/**: Task tracking and management
- **invoices/**: Invoice generation and management
- **dashboard/**: Main dashboard functionality
- **users/**: User management

### View Layer (`views/`)
- Default template files that can be overridden by themes
- Includes layout.php, login.php, dashboard.php, and module-specific views

### Theme System (`themes/`)
- **modern-light/**: Default clean, modern theme
- Supports CSS and JavaScript overrides
- Theme configuration via theme.json
- Color scheme and styling customization

### Plugin System (`plugins/`)
- **whatsapp-chat/**: Example plugin for WhatsApp integration
- Plugin configuration via plugin.json
- Settings management and activation controls

## Data Flow

1. **Request Handling**: 
   - All requests go through index.php
   - .htaccess provides clean URLs and security rules
   - router.php maps URLs to appropriate controllers

2. **Initialization**:
   - Core system loads configuration
   - Database connection established
   - Plugins and modules initialized
   - Theme loaded and templates prepared

3. **Authentication**:
   - Session-based authentication with token support
   - User permissions and access control

4. **Module Processing**:
   - Business logic handled by appropriate modules
   - Database operations through PDO abstraction
   - API endpoints for AJAX operations

5. **Response Generation**:
   - Views rendered with theme overrides applied
   - JSON responses for API calls
   - Webhook dispatching for external integrations

## External Dependencies

### Frontend Dependencies
- **Feather Icons**: Lightweight icon library
- **Custom CSS Framework**: Utility-based styling system
- **Vanilla JavaScript**: No external JS frameworks required

### Backend Dependencies
- **PHP 7.4+**: Core language requirement
- **PDO**: Database abstraction (built into PHP)
- **SQLite/MySQL**: Database options

### Optional Integrations
- **WhatsApp API**: Through plugin system
- **Webhook Support**: For external service integrations

## Deployment Strategy

### Hosting Requirements
- **Shared Hosting Compatible**: No special server requirements
- **PHP 7.4+**: Standard PHP installation
- **Database**: SQLite (file-based) or MySQL
- **Web Server**: Apache with .htaccess support or Nginx

### Deployment Process
1. Upload files to web server
2. Configure database connection in config.php
3. Set appropriate file permissions
4. Access index.php to complete installation

### Configuration
- **config.php**: Main configuration file for database, site settings, and API configuration
- **Database**: Automatic setup with PDO abstraction
- **Plugins/Themes**: Drop-in installation via file upload

### Security Features
- **.htaccess**: Security rules and clean URL configuration
- **Input Sanitization**: Built-in security functions
- **Session Management**: Secure authentication handling
- **Token-based API**: Secure API access

The system is designed for easy maintenance and extensibility while maintaining simplicity and performance. The plugin and theme architecture allows for customization without modifying core files.