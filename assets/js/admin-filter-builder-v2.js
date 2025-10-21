/**
 * A-Tables & Charts - Filter Builder (Server-Side)
 * Simplified approach using server-side filtering and pagination
 *
 * @package ATablesCharts
 * @since 1.0.0
 */

(function($) {
    'use strict';

    class ServerSideFilterBuilder {
        constructor(tableId, columns) {
            this.tableId = tableId;
            this.columns = columns;
            this.filters = [];
            this.currentPage = 1;
            this.perPage = 10;
            this.filterCount = 0;
            
            this.init();
        }

        /**
         * Initialize
         */
        init() {
            this.bindEvents();
            this.updatePerPageFromSelector();
            this.loadPresetList();
        }

        /**
         * Update per-page from selector
         */
        updatePerPageFromSelector() {
            this.perPage = parseInt($('#per-page-select').val() || 10);
        }

        /**
         * Bind events
         */
        bindEvents() {
            const self = this;

            // Add Filter button
            $(document).on('click', '.atables-filter-add-rule', function(e) {
                e.preventDefault();
                self.addFilterRule();
            });

            // Apply Filters button
            $(document).on('click', '.atables-filter-apply', function(e) {
                e.preventDefault();
                self.collectAndApplyFilters();
            });

            // Clear Filters button
            $(document).on('click', '.atables-filter-clear', function(e) {
                e.preventDefault();
                self.clearFilters();
            });

            // Save as Preset button
            $(document).on('click', '.atables-filter-save', function(e) {
                e.preventDefault();
                self.showSavePresetModal();
            });

            // Save preset modal - Cancel button
            $(document).on('click', '.atables-preset-modal-cancel', function(e) {
                e.preventDefault();
                self.hideSavePresetModal();
            });

            // Save preset modal - Save button
            $(document).on('click', '.atables-preset-modal-save', function(e) {
                e.preventDefault();
                self.savePreset();
            });

            // Load preset
            $(document).on('click', '.atables-preset-load', function(e) {
                e.preventDefault();
                self.loadPreset();
            });

            // Delete preset
            $(document).on('click', '.atables-preset-delete', function(e) {
                e.preventDefault();
                self.deletePreset();
            });

            // Preset selector change
            $(document).on('change', '#atables-preset-select', function() {
                const hasSelection = $(this).val() !== '';
                $('.atables-preset-load, .atables-preset-delete').prop('disabled', !hasSelection);
            });

            // Remove filter rule
            $(document).on('click', '.atables-filter-remove', function(e) {
                e.preventDefault();
                $(this).closest('.atables-filter-rule').remove();
                self.updateButtonStates();
                
                // Show empty state if no rules
                if ($('.atables-filter-rule').length === 0) {
                    $('.atables-filter-empty').show();
                }
            });

            // Per-page selector
            $('#per-page-select').on('change', function() {
                self.perPage = parseInt($(this).val());
                if (self.filters.length > 0) {
                    self.currentPage = 1;
                    self.applyFilters();
                }
            });

            // Listen for pagination events when filters are active
            $(document).on('click', '.atables-page-num, .atables-page-btn', function(e) {
                if (self.filters.length > 0) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    
                    if ($(this).hasClass('disabled') || $(this).hasClass('active')) {
                        return;
                    }
                    
                    const page = parseInt($(this).data('page'));
                    if (page && !isNaN(page)) {
                        self.currentPage = page;
                        self.applyFilters();
                        
                        // Scroll to table
                        $('html, body').animate({
                            scrollTop: $('.atables-data-card').offset().top - 100
                        }, 300);
                    }
                }
            });

            // Enable/disable filter value based on operator
            $(document).on('change', '.atables-filter-operator', function() {
                const $rule = $(this).closest('.atables-filter-rule');
                const operator = $(this).val();
                const $valueInput = $rule.find('.atables-filter-value');
                
                // Disable value input for "is empty" and "is not empty"
                if (operator === 'is_empty' || operator === 'is_not_empty') {
                    $valueInput.prop('disabled', true).val('');
                } else {
                    $valueInput.prop('disabled', false);
                }
            });
        }

        /**
         * Add a new filter rule
         */
        addFilterRule() {
            // Hide empty state
            $('.atables-filter-empty').hide();
            
            const ruleId = ++this.filterCount;
            const ruleHtml = `
                <div class="atables-filter-rule" data-rule-id="${ruleId}">
                    <div class="atables-filter-rule-content">
                        <div class="atables-filter-field">
                            <select class="atables-filter-column" required>
                                <option value="">${this.escapeHtml('Select column...')}</option>
                                ${this.columns.map(col => `<option value="${this.escapeHtml(col)}">${this.escapeHtml(col)}</option>`).join('')}
                            </select>
                        </div>
                        
                        <div class="atables-filter-field">
                            <select class="atables-filter-operator" required>
                                <option value="equals">Equals</option>
                                <option value="not_equals">Not Equals</option>
                                <option value="contains">Contains</option>
                                <option value="not_contains">Does Not Contain</option>
                                <option value="starts_with">Starts With</option>
                                <option value="ends_with">Ends With</option>
                                <option value="greater_than">Greater Than</option>
                                <option value="less_than">Less Than</option>
                                <option value="is_empty">Is Empty</option>
                                <option value="is_not_empty">Is Not Empty</option>
                            </select>
                        </div>
                        
                        <div class="atables-filter-field">
                            <input type="text" class="atables-filter-value" placeholder="Enter value..." />
                        </div>
                        
                        <button type="button" class="atables-filter-remove button">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                </div>
            `;
            
            $('.atables-filter-rules').append(ruleHtml);
            this.updateButtonStates();
        }

        /**
         * Update button states
         */
        updateButtonStates() {
            const hasRules = $('.atables-filter-rule').length > 0;
            $('.atables-filter-apply, .atables-filter-clear, .atables-filter-save').prop('disabled', !hasRules);
        }

        /**
         * Collect filters from UI and apply
         */
        collectAndApplyFilters() {
            console.log('collectAndApplyFilters called');
            this.filters = [];
            
            $('.atables-filter-rule').each((index, element) => {
                const $rule = $(element);
                const column = $rule.find('.atables-filter-column').val();
                const operator = $rule.find('.atables-filter-operator').val();
                const value = $rule.find('.atables-filter-value').val();
                
                console.log('Filter rule:', {column, operator, value});
                
                // For "is_empty" and "is_not_empty", value is not needed
                if (column && operator) {
                    if (operator === 'is_empty' || operator === 'is_not_empty' || value) {
                        this.filters.push({
                            column: column,
                            operator: operator,
                            value: value || ''
                        });
                    }
                }
            });

            console.log('Collected filters:', this.filters);

            if (this.filters.length === 0) {
                this.showNotice('Please configure at least one filter', 'warning');
                return;
            }

            this.currentPage = 1; // Reset to first page
            this.applyFilters();
        }

        /**
         * Apply filters via AJAX
         */
        applyFilters() {
            if (this.filters.length === 0) {
                return;
            }

            this.showLoading();

            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atables_filter_table_data',
                    nonce: aTablesAdmin.nonce,
                    table_id: this.tableId,
                    filters: JSON.stringify(this.filters),
                    per_page: this.perPage,
                    paged: this.currentPage
                },
                success: (response) => {
                    if (response.success && response.data) {
                        // Let admin-table-view.js handle the table update
                        // It already has the logic to handle the response format
                        if (typeof window.updateTableFromResponse === 'function') {
                            window.updateTableFromResponse(response.data);
                        } else {
                            // Fallback: Update table ourselves
                            this.updateTable(response.data);
                        }
                        this.showFilterBadge();
                    } else {
                        this.showNotice('Failed to apply filters', 'error');
                    }
                },
                error: () => {
                    this.showNotice('Error applying filters', 'error');
                },
                complete: () => {
                    this.hideLoading();
                }
            });
        }

        /**
         * Clear all filters
         */
        clearFilters() {
            this.filters = [];
            this.currentPage = 1;
            $('.atables-filter-rules').empty();
            $('.atables-filter-empty').show();
            this.updateButtonStates();
            $('.atables-filter-badge').remove();
            
            // Reload page to show original data
            window.location.reload();
        }

        /**
         * Update table with response data
         */
        updateTable(data) {
            // Update table body
            const $tbody = $('.atables-modern-table tbody');
            $tbody.empty();

            if (!data.rows || data.rows.length === 0) {
                const colspan = data.headers ? data.headers.length : 1;
                $tbody.html(`
                    <tr>
                        <td colspan="${colspan}" class="atables-no-data">
                            <span class="dashicons dashicons-info"></span>
                            No rows match the current filters.
                        </td>
                    </tr>
                `);
            } else {
                data.rows.forEach(row => {
                    let rowHtml = '<tr>';
                    
                    // Handle both array and object formats
                    if (Array.isArray(row)) {
                        // Array format: [val1, val2, val3]
                        row.forEach(cell => {
                            rowHtml += `<td>${this.escapeHtml(String(cell || ''))}</td>`;
                        });
                    } else {
                        // Object format: {col1: val1, col2: val2}
                        Object.values(row).forEach(cell => {
                            rowHtml += `<td>${this.escapeHtml(String(cell || ''))}</td>`;
                        });
                    }
                    
                    rowHtml += '</tr>';
                    $tbody.append(rowHtml);
                });
            }

            // Update pagination
            if (data.pagination) {
                this.updatePagination(data.pagination);
                this.updateStats(data.pagination);
            }
        }

        /**
         * Update pagination controls
         */
        updatePagination(pagination) {
            $('.atables-pagination-controls').remove();

            if (pagination.total_pages <= 1) {
                return;
            }

            let html = '<div class="atables-pagination-controls">';

            // First/Previous
            if (pagination.current_page > 1) {
                html += `<a href="#" class="atables-page-btn atables-page-first" data-page="1"><span class="dashicons dashicons-controls-skipback"></span></a>`;
                html += `<a href="#" class="atables-page-btn atables-page-prev" data-page="${pagination.current_page - 1}"><span class="dashicons dashicons-arrow-left-alt2"></span></a>`;
            } else {
                html += '<span class="atables-page-btn atables-page-first disabled"><span class="dashicons dashicons-controls-skipback"></span></span>';
                html += '<span class="atables-page-btn atables-page-prev disabled"><span class="dashicons dashicons-arrow-left-alt2"></span></span>';
            }

            // Page numbers
            html += '<div class="atables-page-numbers">';
            const range = 2;
            const start = Math.max(1, pagination.current_page - range);
            const end = Math.min(pagination.total_pages, pagination.current_page + range);

            if (start > 1) {
                html += `<a href="#" class="atables-page-num" data-page="1">1</a>`;
                if (start > 2) html += '<span class="atables-page-ellipsis">...</span>';
            }

            for (let i = start; i <= end; i++) {
                if (i === pagination.current_page) {
                    html += `<span class="atables-page-num active">${i}</span>`;
                } else {
                    html += `<a href="#" class="atables-page-num" data-page="${i}">${i}</a>`;
                }
            }

            if (end < pagination.total_pages) {
                if (end < pagination.total_pages - 1) html += '<span class="atables-page-ellipsis">...</span>';
                html += `<a href="#" class="atables-page-num" data-page="${pagination.total_pages}">${pagination.total_pages}</a>`;
            }

            html += '</div>';

            // Next/Last
            if (pagination.current_page < pagination.total_pages) {
                html += `<a href="#" class="atables-page-btn atables-page-next" data-page="${pagination.current_page + 1}"><span class="dashicons dashicons-arrow-right-alt2"></span></a>`;
                html += `<a href="#" class="atables-page-btn atables-page-last" data-page="${pagination.total_pages}"><span class="dashicons dashicons-controls-skipforward"></span></a>`;
            } else {
                html += '<span class="atables-page-btn atables-page-next disabled"><span class="dashicons dashicons-arrow-right-alt2"></span></span>';
                html += '<span class="atables-page-btn atables-page-last disabled"><span class="dashicons dashicons-controls-skipforward"></span></span>';
            }

            html += '</div>';
            $('.atables-data-footer').append(html);
        }

        /**
         * Update stats display
         */
        updateStats(pagination) {
            let text = `Showing <strong>${pagination.start_row}</strong> to <strong>${pagination.end_row}</strong> of <strong>${pagination.filtered_total}</strong>`;
            
            if (pagination.filtered_total < pagination.total_rows) {
                text += ` filtered rows (from <strong>${pagination.total_rows}</strong> total)`;
            } else {
                text += ' rows';
            }

            $('.atables-pagination-info p').html(text);
        }

        /**
         * Show filter badge
         */
        showFilterBadge() {
            $('.atables-filter-badge').remove();
            const badge = `<span class="atables-filter-badge"><span class="dashicons dashicons-filter"></span>${this.filters.length} filter(s) applied</span>`;
            $('.atables-data-title h2').after(badge);
        }

        /**
         * Show loading state
         */
        showLoading() {
            $('.atables-modern-table').addClass('atables-loading');
            $('.atables-modern-table tbody').css('opacity', '0.5');
            
            if (!$('.atables-loading-spinner').length) {
                const spinner = `
                    <div class="atables-loading-spinner">
                        <span class="dashicons dashicons-update dashicons-spin"></span>
                        <span>Loading...</span>
                    </div>
                `;
                $('.atables-data-card').css('position', 'relative').prepend(spinner);
            }
        }

        /**
         * Hide loading state
         */
        hideLoading() {
            $('.atables-modern-table').removeClass('atables-loading');
            $('.atables-modern-table tbody').css('opacity', '1');
            $('.atables-loading-spinner').remove();
        }

        /**
         * Show notice message
         */
        showNotice(message, type = 'info') {
            const noticeClass = type === 'error' ? 'notice-error' : (type === 'warning' ? 'notice-warning' : 'notice-info');
            const $notice = $(`<div class="notice ${noticeClass} is-dismissible"><p>${message}</p></div>`);
            $('.atables-page-header').after($notice);
            
            setTimeout(() => {
                $notice.fadeOut(() => $notice.remove());
            }, 5000);
        }

        /**
         * Escape HTML
         */
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        /**
         * Load preset list from server
         */
        loadPresetList() {
            console.log('Loading preset list for table:', this.tableId);
            
            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atables_get_filter_presets',
                    nonce: aTablesAdmin.nonce,
                    table_id: this.tableId
                },
                success: (response) => {
                    console.log('Preset list response:', response);
                    if (response.success && response.data) {
                        // Handle nested data structure from send_success
                        const presets = response.data.data || response.data;
                        this.updatePresetSelector(presets);
                    } else {
                        console.log('No presets found or error');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Error loading presets:', error);
                }
            });
        }

        /**
         * Update preset selector dropdown
         */
        updatePresetSelector(presets) {
            console.log('Updating preset selector with:', presets);
            
            const $select = $('#atables-preset-select');
            $select.empty().append('<option value="">-- Select a preset --</option>');

            if (presets && presets.length > 0) {
                presets.forEach(preset => {
                    console.log('Adding preset:', preset);
                    $select.append(`<option value="${preset.id}">${this.escapeHtml(preset.name)}</option>`);
                });
                $('.atables-preset-selector').show();
                console.log('Preset selector shown');
            } else {
                $('.atables-preset-selector').hide();
                console.log('No presets to show');
            }
        }

        /**
         * Show save preset modal
         */
        showSavePresetModal() {
            // Clear modal inputs
            $('#atables-preset-name').val('');
            $('#atables-preset-description').val('');
            $('#atables-preset-default').prop('checked', false);

            // Show modal
            $('.atables-preset-modal').addClass('active');
        }

        /**
         * Hide save preset modal
         */
        hideSavePresetModal() {
            $('.atables-preset-modal').removeClass('active');
        }

        /**
         * Save current filters as preset
         */
        savePreset() {
            const name = $('#atables-preset-name').val().trim();
            const description = $('#atables-preset-description').val().trim();
            const isDefault = $('#atables-preset-default').is(':checked');

            if (!name) {
                this.showNotice('Please enter a preset name', 'warning');
                return;
            }

            // Collect current filters
            this.collectAndApplyFilters();

            if (this.filters.length === 0) {
                this.showNotice('No filters to save', 'warning');
                return;
            }

            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atables_save_filter_preset',
                    nonce: aTablesAdmin.nonce,
                    table_id: this.tableId,
                    name: name,
                    description: description,
                    is_default: isDefault,
                    filters: JSON.stringify(this.filters)
                },
                success: (response) => {
                    if (response.success) {
                        this.showNotice('Filter preset saved successfully!', 'success');
                        this.hideSavePresetModal();
                        this.loadPresetList();
                    } else {
                        this.showNotice(response.data || 'Failed to save preset', 'error');
                    }
                },
                error: () => {
                    this.showNotice('Error saving preset', 'error');
                }
            });
        }

        /**
         * Load a saved preset
         */
        loadPreset() {
            const presetId = $('#atables-preset-select').val();

            if (!presetId) {
                this.showNotice('Please select a preset', 'warning');
                return;
            }

            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atables_load_filter_preset',
                    nonce: aTablesAdmin.nonce,
                    preset_id: presetId
                },
                success: (response) => {
                    if (response.success && response.data) {
                        // Handle nested data structure
                        const preset = response.data.data || response.data;
                        this.loadFiltersFromPreset(preset);
                        this.showNotice(`Loaded preset: ${preset.name}`, 'success');
                    } else {
                        this.showNotice('Failed to load preset', 'error');
                    }
                },
                error: () => {
                    this.showNotice('Error loading preset', 'error');
                }
            });
        }

        /**
         * Load filters from preset data
         */
        loadFiltersFromPreset(preset) {
            // Clear existing filters
            $('.atables-filter-rules').empty();
            $('.atables-filter-empty').show();

            // Load filters from preset
            if (preset.filters && preset.filters.length > 0) {
                preset.filters.forEach(filter => {
                    this.addFilterRule();
                    const $lastRule = $('.atables-filter-rule').last();
                    $lastRule.find('.atables-filter-column').val(filter.column);
                    $lastRule.find('.atables-filter-operator').val(filter.operator);
                    $lastRule.find('.atables-filter-value').val(filter.value);
                });

                // Auto-apply filters
                this.collectAndApplyFilters();
            }
        }

        /**
         * Delete a saved preset
         */
        deletePreset() {
            const presetId = $('#atables-preset-select').val();
            const presetName = $('#atables-preset-select option:selected').text();

            if (!presetId) {
                this.showNotice('Please select a preset', 'warning');
                return;
            }

            if (!confirm(`Are you sure you want to delete the preset "${presetName}"?`)) {
                return;
            }

            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atables_delete_filter_preset',
                    nonce: aTablesAdmin.nonce,
                    preset_id: presetId
                },
                success: (response) => {
                    if (response.success) {
                        this.showNotice('Preset deleted successfully', 'success');
                        this.loadPresetList();
                    } else {
                        this.showNotice(response.data || 'Failed to delete preset', 'error');
                    }
                },
                error: () => {
                    this.showNotice('Error deleting preset', 'error');
                }
            });
        }
    }

    // Initialize on page load
    $(document).ready(function() {
        if ($('.atables-view-page').length && $('.atables-filter-panel').length) {
            const tableId = new URLSearchParams(window.location.search).get('table_id');
            const $panel = $('.atables-filter-panel');
            const columns = $panel.data('columns');
            
            if (tableId && columns) {
                window.atablesFilterBuilder = new ServerSideFilterBuilder(tableId, columns);
            }
        }
    });

})(jQuery);
