/**
 * A-Tables & Charts - Edit Page Save Handler
 * 
 * Coordinates saving data from all tabs
 * 
 * @package ATablesCharts
 * @since 1.0.0
 */

(function($) {
    'use strict';

    const ATablesSaveHandler = {
        
        tableId: null,
        
        // Storage for all settings
        settings: {
            basic: {},
            display: {},
            data: {},
            conditional: [],
            formulas: [],
            validation: {},
            merges: []
        },
        
        /**
         * Initialize save handler
         */
        init: function() {
            this.tableId = $('#atables-table-id').val();
            this.bindEvents();
        },
        
        /**
         * Bind save events
         */
        bindEvents: function() {
            const self = this;
            
            // Main save button
            $(document).on('atables:saveAll', function() {
                self.saveAll();
            });
            
            // Collect data from each tab
            $(document).on('atables:cf:getRules', function(e, rules) {
                self.settings.conditional = rules;
            });
            
            $(document).on('atables:formulas:getFormulas', function(e, formulas) {
                self.settings.formulas = formulas;
            });
            
            $(document).on('atables:validation:getRules', function(e, rules) {
                self.settings.validation = rules;
            });
            
            $(document).on('atables:merging:getMerges', function(e, merges) {
                self.settings.merges = merges;
            });
        },
        
        /**
         * Collect all data from all tabs
         */
        collectAllData: function() {
            // Basic Info
            this.settings.basic = {
                title: $('#table-title').val(),
                description: $('#table-description').val()
            };
            
            // Display Settings
            this.settings.display = {
                theme: $('input[name="display_theme"]:checked').val() || 'default',
                responsive_mode: $('input[name="display_responsive"]:checked').val() || 'scroll',
                enable_search: $('input[name="display_enable_search"]').is(':checked'),
                enable_sorting: $('input[name="display_enable_sorting"]').is(':checked'),
                enable_pagination: $('input[name="display_enable_pagination"]').is(':checked'),
                rows_per_page: parseInt($('input[name="display_rows_per_page"]').val()) || 10
            };
            
            // Table Data
            this.settings.data = this.collectTableData();
            
            // Trigger events to collect from other tabs
            $(document).trigger('atables:cf:getRules');
            $(document).trigger('atables:formulas:getFormulas');
            $(document).trigger('atables:validation:getRules');
            $(document).trigger('atables:merging:getMerges');
            
            return this.settings;
        },
        
        /**
         * Collect table data (headers and rows)
         */
        collectTableData: function() {
            const headers = [];
            const data = [];
            
            // Collect headers
            $('#atables-editable-table thead .atables-header-input').each(function() {
                headers.push($(this).val().trim());
            });
            
            // Collect data rows
            $('#atables-editable-table tbody tr:not(.atables-no-data)').each(function() {
                const row = [];
                $(this).find('.atables-cell-input').each(function() {
                    row.push($(this).val());
                });
                if (row.length > 0) {
                    data.push(row);
                }
            });
            
            return {
                headers: headers,
                rows: data
            };
        },
        
        /**
         * Save all data
         */
        saveAll: function() {
            const self = this;
            const $btn = $('#atables-save-all-btn');
            const originalText = $btn.html();
            
            // Show loading
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> Saving...');
            
            // Collect all data
            const allData = this.collectAllData();
            
            // Prepare data for AJAX
            const postData = {
                action: 'atables_save_enhanced_table',
                nonce: aTablesAdmin.nonce,
                table_id: this.tableId,
                title: allData.basic.title,
                description: allData.basic.description,
                headers: allData.data.headers,
                data: allData.data.rows,
                display_settings: JSON.stringify({
                    theme: allData.display.theme,
                    responsive_mode: allData.display.responsive_mode,
                    enable_search: allData.display.enable_search,
                    enable_sorting: allData.display.enable_sorting,
                    enable_pagination: allData.display.enable_pagination,
                    rows_per_page: allData.display.rows_per_page,
                    conditional_formatting: allData.conditional,
                    formulas: allData.formulas,
                    validation_rules: allData.validation,
                    cell_merges: allData.merges
                })
            };
            
            // Send AJAX request
            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: postData,
                success: function(response) {
                    if (response.success) {
                        self.showNotification('All changes saved successfully!', 'success');
                        
                        // Update table title in header
                        $('#current-table-title').text(allData.basic.title);
                    } else {
                        self.showNotification(response.data.message || 'Failed to save changes.', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Save error:', xhr, status, error);
                    self.showNotification('Failed to save changes. Please try again.', 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        },
        
        /**
         * Show notification
         */
        showNotification: function(message, type) {
            if (window.ATablesNotifications) {
                window.ATablesNotifications.show(message, type);
            } else {
                // Fallback to simple message container
                const $container = $('#atables-message-container');
                const className = type === 'success' ? 'notice-success' : 'notice-error';
                const html = '<div class="notice ' + className + ' is-dismissible"><p>' + message + '</p></div>';
                
                $container.html(html);
                
                setTimeout(function() {
                    $container.empty();
                }, 5000);
            }
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        if ($('.atables-edit-page-tabs').length > 0) {
            ATablesSaveHandler.init();
        }
    });
    
    // Expose globally
    window.ATablesSaveHandler = ATablesSaveHandler;
    
})(jQuery);
