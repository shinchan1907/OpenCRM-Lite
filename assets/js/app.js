/**
 * OpenCRM Lite - Main Application JavaScript
 */

// Initialize application when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

/**
 * Main application initialization
 */
function initializeApp() {
    initializeFeatherIcons();
    initializeFormHandlers();
    initializeModalHandlers();
    initializeDropdownHandlers();
    initializeTooltips();
    initializeConfirmationDialogs();
    initializeAjaxForms();
    initializeDataTables();
    initializeNotifications();
    initializeAutoSave();
}

/**
 * Initialize Feather icons
 */
function initializeFeatherIcons() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

/**
 * Form handling and validation
 */
function initializeFormHandlers() {
    // Add loading state to form submissions
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
                
                // Re-enable after timeout to prevent permanent lock
                setTimeout(() => {
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                }, 10000);
            }
        });
    });
    
    // Auto-resize textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', autoResizeTextarea);
        autoResizeTextarea.call(textarea);
    });
    
    // Real-time validation
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', clearFieldError);
    });
}

/**
 * Auto-resize textarea to fit content
 */
function autoResizeTextarea() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
}

/**
 * Validate individual form field
 */
function validateField(e) {
    const field = e.target;
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    // Required field validation
    if (field.required && !value) {
        isValid = false;
        errorMessage = 'This field is required.';
    }
    
    // Email validation
    if (field.type === 'email' && value && !isValidEmail(value)) {
        isValid = false;
        errorMessage = 'Please enter a valid email address.';
    }
    
    // Phone validation
    if (field.type === 'tel' && value && !isValidPhone(value)) {
        isValid = false;
        errorMessage = 'Please enter a valid phone number.';
    }
    
    // Number validation
    if (field.type === 'number' && value) {
        const min = parseFloat(field.min);
        const max = parseFloat(field.max);
        const numValue = parseFloat(value);
        
        if (!isNaN(min) && numValue < min) {
            isValid = false;
            errorMessage = `Value must be at least ${min}.`;
        }
        
        if (!isNaN(max) && numValue > max) {
            isValid = false;
            errorMessage = `Value must be no more than ${max}.`;
        }
    }
    
    showFieldError(field, isValid ? null : errorMessage);
}

/**
 * Clear field error state
 */
function clearFieldError(e) {
    const field = e.target;
    if (field.classList.contains('is-invalid')) {
        showFieldError(field, null);
    }
}

/**
 * Show or hide field error
 */
function showFieldError(field, message) {
    const existingError = field.parentNode.querySelector('.invalid-feedback');
    
    if (message) {
        field.classList.add('is-invalid');
        
        if (!existingError) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        } else {
            existingError.textContent = message;
        }
    } else {
        field.classList.remove('is-invalid');
        if (existingError) {
            existingError.remove();
        }
    }
}

/**
 * Email validation
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Phone validation
 */
function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}

/**
 * Modal handlers
 */
function initializeModalHandlers() {
    // Modal triggers
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-toggle="modal"]')) {
            e.preventDefault();
            const targetModal = document.querySelector(e.target.getAttribute('data-target'));
            if (targetModal) {
                showModal(targetModal);
            }
        }
        
        // Close modal triggers
        if (e.target.matches('[data-dismiss="modal"]') || e.target.closest('.modal-backdrop')) {
            const modal = e.target.closest('.modal');
            if (modal) {
                hideModal(modal);
            }
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                hideModal(openModal);
            }
        }
    });
}

/**
 * Show modal
 */
function showModal(modal) {
    modal.style.display = 'flex';
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    // Focus first input
    const firstInput = modal.querySelector('input, select, textarea');
    if (firstInput) {
        setTimeout(() => firstInput.focus(), 100);
    }
}

/**
 * Hide modal
 */
function hideModal(modal) {
    modal.classList.remove('show');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

/**
 * Dropdown handlers
 */
function initializeDropdownHandlers() {
    document.addEventListener('click', function(e) {
        const dropdown = e.target.closest('.dropdown');
        const toggle = e.target.closest('.dropdown-toggle');
        
        if (toggle) {
            e.preventDefault();
            e.stopPropagation();
            
            const menu = dropdown.querySelector('.dropdown-menu');
            const isOpen = menu.classList.contains('show');
            
            // Close all other dropdowns
            closeAllDropdowns();
            
            // Toggle current dropdown
            if (!isOpen) {
                menu.classList.add('show');
            }
        } else if (!dropdown) {
            // Close all dropdowns when clicking outside
            closeAllDropdowns();
        }
    });
}

/**
 * Close all open dropdowns
 */
function closeAllDropdowns() {
    const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
    openDropdowns.forEach(menu => menu.classList.remove('show'));
}

/**
 * Initialize tooltips
 */
function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[title], [data-tooltip]');
    tooltipElements.forEach(element => {
        const tooltipText = element.getAttribute('title') || element.getAttribute('data-tooltip');
        if (tooltipText) {
            element.setAttribute('title', ''); // Remove default title
            
            let tooltip = null;
            
            element.addEventListener('mouseenter', function() {
                tooltip = createTooltip(tooltipText);
                document.body.appendChild(tooltip);
                positionTooltip(element, tooltip);
            });
            
            element.addEventListener('mouseleave', function() {
                if (tooltip) {
                    tooltip.remove();
                    tooltip = null;
                }
            });
        }
    });
}

/**
 * Create tooltip element
 */
function createTooltip(text) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = text;
    tooltip.style.cssText = `
        position: absolute;
        z-index: 9999;
        background: #1f2937;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 14px;
        white-space: nowrap;
        opacity: 0;
        transition: opacity 0.2s ease;
        pointer-events: none;
    `;
    
    setTimeout(() => tooltip.style.opacity = '1', 10);
    return tooltip;
}

/**
 * Position tooltip relative to element
 */
function positionTooltip(element, tooltip) {
    const elementRect = element.getBoundingClientRect();
    const tooltipRect = tooltip.getBoundingClientRect();
    
    let left = elementRect.left + (elementRect.width / 2) - (tooltipRect.width / 2);
    let top = elementRect.top - tooltipRect.height - 10;
    
    // Adjust if tooltip goes off screen
    if (left < 10) left = 10;
    if (left + tooltipRect.width > window.innerWidth - 10) {
        left = window.innerWidth - tooltipRect.width - 10;
    }
    
    if (top < 10) {
        top = elementRect.bottom + 10;
    }
    
    tooltip.style.left = left + 'px';
    tooltip.style.top = top + 'px';
}

/**
 * Confirmation dialogs
 */
function initializeConfirmationDialogs() {
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-confirm]')) {
            const message = e.target.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
                e.stopPropagation();
            }
        }
    });
}

/**
 * AJAX form handlers
 */
function initializeAjaxForms() {
    const ajaxForms = document.querySelectorAll('[data-ajax="true"]');
    ajaxForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitAjaxForm(form);
        });
    });
}

/**
 * Submit form via AJAX
 */
function submitAjaxForm(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);
    
    if (submitBtn) {
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
    }
    
    fetch(form.action || window.location.href, {
        method: form.method || 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success!', data.message || 'Operation completed successfully.', 'success');
            if (data.redirect) {
                setTimeout(() => window.location.href = data.redirect, 1500);
            }
        } else {
            showNotification('Error', data.message || 'An error occurred.', 'error');
        }
    })
    .catch(error => {
        console.error('Ajax form error:', error);
        showNotification('Error', 'A network error occurred.', 'error');
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
        }
    });
}

/**
 * Initialize data tables with sorting and filtering
 */
function initializeDataTables() {
    const tables = document.querySelectorAll('[data-table="true"]');
    tables.forEach(table => {
        makeTableSortable(table);
        addTableSearch(table);
    });
}

/**
 * Make table sortable
 */
function makeTableSortable(table) {
    const headers = table.querySelectorAll('th[data-sortable="true"]');
    headers.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            sortTable(table, header);
        });
    });
}

/**
 * Sort table by column
 */
function sortTable(table, header) {
    const columnIndex = Array.from(header.parentNode.children).indexOf(header);
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    const currentOrder = header.getAttribute('data-order') || 'asc';
    const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
    
    rows.sort((a, b) => {
        const aValue = a.children[columnIndex].textContent.trim();
        const bValue = b.children[columnIndex].textContent.trim();
        
        let comparison = 0;
        if (isNumeric(aValue) && isNumeric(bValue)) {
            comparison = parseFloat(aValue) - parseFloat(bValue);
        } else {
            comparison = aValue.localeCompare(bValue);
        }
        
        return newOrder === 'asc' ? comparison : -comparison;
    });
    
    // Clear previous sort indicators
    table.querySelectorAll('th').forEach(th => th.removeAttribute('data-order'));
    
    // Set new sort indicator
    header.setAttribute('data-order', newOrder);
    
    // Reorder rows
    rows.forEach(row => tbody.appendChild(row));
}

/**
 * Check if value is numeric
 */
function isNumeric(value) {
    return !isNaN(parseFloat(value)) && isFinite(value);
}

/**
 * Add search functionality to table
 */
function addTableSearch(table) {
    const searchInput = document.querySelector(`[data-table-search="${table.id}"]`);
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterTable(table, this.value);
        });
    }
}

/**
 * Filter table rows based on search term
 */
function filterTable(table, searchTerm) {
    const tbody = table.querySelector('tbody');
    const rows = tbody.querySelectorAll('tr');
    const term = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
}

/**
 * Notification system
 */
function initializeNotifications() {
    // Auto-hide existing notifications
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => {
        if (!alert.querySelector('.btn-close')) {
            setTimeout(() => {
                hideNotification(alert);
            }, 5000);
        }
    });
}

/**
 * Show notification
 */
function showNotification(title, message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} notification`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 500px;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    `;
    
    notification.innerHTML = `
        <div style="display: flex; align-items: flex-start;">
            <div style="flex: 1;">
                <strong>${title}</strong>
                <div>${message}</div>
            </div>
            <button type="button" class="btn-close" onclick="hideNotification(this.parentElement.parentElement)" style="margin-left: 10px;">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 10);
    
    // Auto-hide
    if (duration > 0) {
        setTimeout(() => {
            hideNotification(notification);
        }, duration);
    }
}

/**
 * Hide notification
 */
function hideNotification(notification) {
    notification.style.opacity = '0';
    notification.style.transform = 'translateX(100%)';
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

/**
 * Auto-save functionality
 */
function initializeAutoSave() {
    const autoSaveForms = document.querySelectorAll('[data-autosave="true"]');
    autoSaveForms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', debounce(() => {
                autoSaveForm(form);
            }, 2000));
        });
    });
}

/**
 * Auto-save form data
 */
function autoSaveForm(form) {
    const formData = new FormData(form);
    formData.append('autosave', '1');
    
    fetch(form.action || window.location.href, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAutoSaveIndicator();
        }
    })
    .catch(error => {
        console.error('Auto-save error:', error);
    });
}

/**
 * Show auto-save indicator
 */
function showAutoSaveIndicator() {
    let indicator = document.querySelector('.autosave-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.className = 'autosave-indicator';
        indicator.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 9999;
        `;
        indicator.textContent = 'Draft saved';
        document.body.appendChild(indicator);
    }
    
    indicator.style.opacity = '1';
    setTimeout(() => {
        indicator.style.opacity = '0';
    }, 2000);
}

/**
 * Debounce function to limit function calls
 */
function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction() {
        const context = this;
        const args = arguments;
        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

/**
 * Utility functions
 */

// Format currency
function formatCurrency(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

// Format date
function formatDate(date, options = {}) {
    const defaultOptions = {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    };
    return new Intl.DateTimeFormat('en-US', { ...defaultOptions, ...options }).format(new Date(date));
}

// Copy to clipboard
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Copied!', 'Text copied to clipboard.', 'success', 2000);
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Copied!', 'Text copied to clipboard.', 'success', 2000);
    }
}

// Export to CSV
function exportTableToCSV(table, filename = 'export.csv') {
    const rows = table.querySelectorAll('tr');
    const csvContent = Array.from(rows).map(row => {
        const cells = row.querySelectorAll('th, td');
        return Array.from(cells).map(cell => {
            return '"' + cell.textContent.replace(/"/g, '""') + '"';
        }).join(',');
    }).join('\n');
    
    downloadCSV(csvContent, filename);
}

// Download CSV file
function downloadCSV(csvContent, filename) {
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Global error handler
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
    // Only show notification for critical errors
    if (e.error instanceof TypeError || e.error instanceof ReferenceError) {
        showNotification('Error', 'A JavaScript error occurred. Please refresh the page.', 'error');
    }
});

// Make utility functions globally available
window.OpenCRM = {
    showNotification,
    hideNotification,
    showModal,
    hideModal,
    formatCurrency,
    formatDate,
    copyToClipboard,
    exportTableToCSV
};
