/**
 * A-Tables & Charts - Toast Notification System
 * Beautiful toast notifications that appear at the bottom-right
 *
 * @package ATablesCharts
 * @since 1.0.6
 */

(function() {
    'use strict';

    // Create toast container on page load
    function createToastContainer() {
        if (document.getElementById('atables-toast-container')) {
            return; // Already exists
        }

        const container = document.createElement('div');
        container.id = 'atables-toast-container';
        container.className = 'atables-toast-container';
        document.body.appendChild(container);
    }

    // Toast Manager
    const Toast = {
        // Configuration
        config: {
            duration: {
                success: 5000,  // 5 seconds (was 4)
                error: 0,       // Manual dismiss only
                warning: 7000,  // 7 seconds (was 6)
                info: 6000      // 6 seconds (was 5)
            },
            icons: {
                success: '✓',
                error: '✖',
                warning: '⚠',
                info: 'ℹ'
            },
            maxToasts: 5
        },

        // Active toasts
        toasts: [],

        /**
         * Show a toast notification
         */
        show: function(message, type, duration) {
            type = type || 'info';
            
            // Get duration (custom or default)
            if (duration === undefined) {
                duration = this.config.duration[type];
            }

            // Ensure container exists
            createToastContainer();

            // Remove oldest toast if we have too many
            if (this.toasts.length >= this.config.maxToasts) {
                this.remove(this.toasts[0]);
            }

            // Create toast element
            const toast = this.create(message, type);
            
            // Add to container
            const container = document.getElementById('atables-toast-container');
            container.appendChild(toast);

            // Track toast
            this.toasts.push(toast);

            // Trigger show animation
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);

            // Auto-dismiss if duration is set
            if (duration > 0) {
                setTimeout(() => {
                    this.remove(toast);
                }, duration);
            }

            return toast;
        },

        /**
         * Create toast element
         */
        create: function(message, type) {
            const toast = document.createElement('div');
            toast.className = `atables-toast atables-toast-${type}`;
            
            const icon = this.config.icons[type] || this.config.icons.info;
            
            toast.innerHTML = `
                <div class="atables-toast-icon">${icon}</div>
                <div class="atables-toast-message">${this.escapeHtml(message)}</div>
                <button class="atables-toast-close" title="Dismiss">×</button>
            `;

            // Add close button handler
            const closeBtn = toast.querySelector('.atables-toast-close');
            closeBtn.addEventListener('click', () => {
                this.remove(toast);
            });

            return toast;
        },

        /**
         * Remove a toast
         */
        remove: function(toast) {
            if (!toast || !toast.parentNode) return;

            // Remove from tracking
            const index = this.toasts.indexOf(toast);
            if (index > -1) {
                this.toasts.splice(index, 1);
            }

            // Trigger hide animation
            toast.classList.remove('show');
            toast.classList.add('hide');

            // Remove from DOM after animation
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        },

        /**
         * Clear all toasts
         */
        clear: function() {
            const toastsCopy = [...this.toasts];
            toastsCopy.forEach(toast => this.remove(toast));
        },

        /**
         * Success toast
         */
        success: function(message, duration) {
            return this.show(message, 'success', duration);
        },

        /**
         * Error toast
         */
        error: function(message, duration) {
            return this.show(message, 'error', duration);
        },

        /**
         * Warning toast
         */
        warning: function(message, duration) {
            return this.show(message, 'warning', duration);
        },

        /**
         * Info toast
         */
        info: function(message, duration) {
            return this.show(message, 'info', duration);
        },

        /**
         * Escape HTML to prevent XSS
         */
        escapeHtml: function(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    // Expose globally
    window.ATablesToast = Toast;
    
    // Alias for convenience
    window.ATablesNotifications = {
        show: function(message, type, duration) {
            return Toast.show(message, type, duration);
        },
        success: function(message, duration) {
            return Toast.success(message, duration);
        },
        error: function(message, duration) {
            return Toast.error(message, duration);
        },
        warning: function(message, duration) {
            return Toast.warning(message, duration);
        },
        info: function(message, duration) {
            return Toast.info(message, duration);
        },
        clear: function() {
            return Toast.clear();
        }
    };

    // Legacy support
    window.showNotification = function(message, type) {
        return Toast.show(message, type);
    };

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createToastContainer);
    } else {
        createToastContainer();
    }

    // Debug - log that it loaded
    console.log('✓ A-Tables Toast System Loaded');
    
    // Auto-test on load (remove this in production)
    setTimeout(() => {
        if (window.location.search.includes('page=a-tables-charts')) {
            // Toast.info('Toast system ready!', 2000);
        }
    }, 500);

})();
