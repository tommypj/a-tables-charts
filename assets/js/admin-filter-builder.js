/**
 * Filter Builder JavaScript
 * 
 * Handles the visual filter builder UI and interactions
 * 
 * @package ATablesCharts
 * @since 1.0.5
 */

(function($) {
    'use strict';

    // Debug logging
    console.log('Filter Builder JS loaded');

    /**
     * Filter Builder Class
     */
    class FilterBuilder {
        constructor(tableId, columns) {
            this.tableId = tableId;
            this.columns = columns;
            this.filters = [];
            this.presets = [];
            this.currentPreset = null;
            
            console.log('FilterBuilder initialized:', { tableId, columns });
            
            this.init();
        }

        /**
         * Initialize the filter builder
         */
        init() {
            this.loadPresets();
            this.bindEvents();
            this.renderFilters();
            this.setupPerPageSelector();
        }

        /**
         * Setup per-page selector to work with both filtered and non-filtered states
         */
        setupPerPageSelector() {
            const self = this;
            
            // Don't override the per-page selector on init
            // Only override it when filters are applied
            // The admin-table-view.js will handle per-page changes normally
        }

        /**
         * Bind event handlers
         */
        bindEvents() {
            const self = this;

            console.log('Binding filter events...');

            // Add filter rule
            $(document).on('click', '.atables-filter-add-rule', function(e) {
                e.preventDefault();
                console.log('Add filter clicked');
                self.addFilter();
            });

            // Remove filter rule
            $(document).on('click', '.atables-filter-remove-rule', function(e) {
                e.preventDefault();
                const index = $(this).data('index');
                self.removeFilter(index);
            });

            // Column change - analyze column
            $(document).on('change', '.atables-filter-column', function() {
                const index = $(this).closest('.atables-filter-rule').data('index');
                const column = $(this).val();
                self.onColumnChange(index, column);
            });

            // Operator change
            $(document).on('change', '.atables-filter-operator', function() {
                const index = $(this).closest('.atables-filter-rule').data('index');
                const operator = $(this).val();
                self.onOperatorChange(index, operator);
            });

            // Apply filters
            $(document).on('click', '.atables-filter-apply', function(e) {
                e.preventDefault();
                self.applyFilters();
            });

            // Clear filters
            $(document).on('click', '.atables-filter-clear, .atables-active-filters-clear', function(e) {
                e.preventDefault();
                self.clearFilters();
            });

            // Save preset
            $(document).on('click', '.atables-filter-save', function(e) {
                e.preventDefault();
                self.showSavePresetModal();
            });

            // Load preset
            $(document).on('click', '.atables-preset-load', function(e) {
                e.preventDefault();
                const presetId = $('.atables-preset-select').val();
                if (presetId) {
                    self.loadPreset(parseInt(presetId));
                }
            });

            // Delete preset
            $(document).on('click', '.atables-preset-delete', function(e) {
                e.preventDefault();
                const presetId = $('.atables-preset-select').val();
                if (presetId && confirm('Are you sure you want to delete this preset?')) {
                    self.deletePreset(parseInt(presetId));
                }
            });

            // Duplicate preset
            $(document).on('click', '.atables-preset-duplicate', function(e) {
                e.preventDefault();
                const presetId = $('.atables-preset-select').val();
                if (presetId) {
                    self.duplicatePreset(parseInt(presetId));
                }
            });

            // Save preset modal
            $(document).on('click', '.atables-preset-modal-save', function(e) {
                e.preventDefault();
                self.savePreset();
            });

            $(document).on('click', '.atables-preset-modal-cancel', function(e) {
                e.preventDefault();
                self.hideSavePresetModal();
            });

            // Close modal on outside click
            $(document).on('click', '.atables-preset-modal', function(e) {
                if ($(e.target).hasClass('atables-preset-modal')) {
                    self.hideSavePresetModal();
                }
            });

            // Add value for IN operator
            $(document).on('click', '.atables-filter-add-value', function(e) {
                e.preventDefault();
                const index = $(this).closest('.atables-filter-rule').data('index');
                self.addValueField(index);
            });

            // Remove value for IN operator
            $(document).on('click', '.atables-filter-value-remove', function(e) {
                e.preventDefault();
                $(this).closest('.atables-filter-value-item').remove();
            });

            console.log('Events bound successfully');
        }

        /**
         * Add a new filter rule
         */
        addFilter() {
            console.log('Adding new filter');
            
            if (!this.columns || this.columns.length === 0) {
                this.showNotice('error', 'No columns available for filtering.');
                return;
            }

            const filter = {
                column: this.columns[0] || '',
                operator: 'equals',
                value: '',
                data_type: 'text'
            };

            this.filters.push(filter);
            this.renderFilters();

            // Analyze the first column
            if (filter.column) {
                this.analyzeColumn(this.filters.length - 1, filter.column);
            }
        }

        /**
         * Remove a filter rule
         */
        removeFilter(index) {
            this.filters.splice(index, 1);
            this.renderFilters();
        }

        /**
         * Clear all filters
         */
        clearFilters() {
            this.filters = [];
            this.currentPreset = null;
            this.currentFilteredData = null; // Clear filtered data
            this.renderFilters();
            this.renderPresetSelector();
            
            // Remove active filters indicator
            $('.atables-active-filters').remove();
            
            // Reload page to show original data
            window.location.reload();
        }

        /**
         * Render all filter rules
         */
        renderFilters() {
            const $container = $('.atables-filter-rules');
            
            if (this.filters.length === 0) {
                $container.html(this.getEmptyState());
                $('.atables-filter-apply, .atables-filter-clear, .atables-filter-save').prop('disabled', true);
                return;
            }

            let html = '';
            this.filters.forEach((filter, index) => {
                html += this.renderFilterRule(filter, index);
            });

            $container.html(html);
            $('.atables-filter-apply, .atables-filter-clear, .atables-filter-save').prop('disabled', false);
        }

        /**
         * Render a single filter rule
         */
        renderFilterRule(filter, index) {
            const dataType = filter.data_type || 'text';
            const operators = this.getOperatorsForType(dataType);
            
            let html = `
                <div class="atables-filter-rule" data-index="${index}">
                    <div class="atables-filter-rule-fields">
                        <div class="atables-filter-field atables-filter-field-column">
                            <label>Column</label>
                            <select class="atables-filter-column" data-index="${index}">
                                ${this.columns.map(col => `
                                    <option value="${col}" ${filter.column === col ? 'selected' : ''}>${col}</option>
                                `).join('')}
                            </select>
                        </div>
                        
                        <div class="atables-filter-field atables-filter-field-operator">
                            <label>Operator</label>
                            <select class="atables-filter-operator" data-index="${index}">
                                ${operators.map(op => `
                                    <option value="${op.value}" ${filter.operator === op.value ? 'selected' : ''}>
                                        ${op.label}
                                    </option>
                                `).join('')}
                            </select>
                        </div>
                        
                        <div class="atables-filter-field atables-filter-field-value">
                            <label>Value</label>
                            ${this.renderValueField(filter, index)}
                        </div>
                    </div>
                    
                    <div class="atables-filter-rule-actions">
                        <button class="atables-filter-remove-rule" data-index="${index}" title="Remove this filter">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                        <div class="atables-filter-rule-info">${this.formatDataType(dataType)}</div>
                    </div>
                </div>
            `;

            return html;
        }

        /**
         * Render value input field based on operator
         */
        renderValueField(filter, index) {
            const operator = filter.operator;
            const dataType = filter.data_type;

            // IN and NOT_IN operators need multiple values
            if (operator === 'in' || operator === 'not_in') {
                const values = Array.isArray(filter.value) ? filter.value : (filter.value ? [filter.value] : ['']);
                
                let html = '<div class="atables-filter-values-list">';
                values.forEach((val, i) => {
                    html += `
                        <div class="atables-filter-value-item">
                            <input type="text" value="${val}" data-index="${index}" data-value-index="${i}">
                            ${i > 0 ? '<button class="atables-filter-value-remove">×</button>' : ''}
                        </div>
                    `;
                });
                html += '</div>';
                html += '<button class="atables-filter-add-value">+ Add Value</button>';
                return html;
            }

            // BETWEEN operator needs two values
            if (operator === 'between' || operator === 'date_between') {
                const values = Array.isArray(filter.value) ? filter.value : ['', ''];
                const inputType = dataType === 'date' ? 'date' : (dataType === 'number' ? 'number' : 'text');
                
                return `
                    <div class="atables-filter-between-values">
                        <input type="${inputType}" value="${values[0] || ''}" data-index="${index}" data-value-index="0">
                        <span class="atables-filter-between-separator">and</span>
                        <input type="${inputType}" value="${values[1] || ''}" data-index="${index}" data-value-index="1">
                    </div>
                `;
            }

            // Regular single value input
            const inputType = dataType === 'date' ? 'date' : (dataType === 'number' ? 'number' : 'text');
            return `<input type="${inputType}" value="${filter.value}" data-index="${index}">`;
        }

        /**
         * Format data type for display
         */
        formatDataType(dataType) {
            const labels = {
                'text': 'Text',
                'number': 'Number',
                'date': 'Date',
                'select': 'Select'
            };
            return labels[dataType] || 'Text';
        }

        /**
         * Get operators for data type
         */
        getOperatorsForType(dataType) {
            const operators = {
                text: [
                    { value: 'equals', label: 'Equals' },
                    { value: 'not_equals', label: 'Not Equals' },
                    { value: 'contains', label: 'Contains' },
                    { value: 'not_contains', label: 'Not Contains' },
                    { value: 'starts_with', label: 'Starts With' },
                    { value: 'ends_with', label: 'Ends With' }
                ],
                number: [
                    { value: 'equals', label: 'Equals' },
                    { value: 'not_equals', label: 'Not Equals' },
                    { value: 'greater_than', label: 'Greater Than' },
                    { value: 'less_than', label: 'Less Than' },
                    { value: 'greater_or_equal', label: 'Greater or Equal' },
                    { value: 'less_or_equal', label: 'Less or Equal' },
                    { value: 'between', label: 'Between' }
                ],
                date: [
                    { value: 'date_equals', label: 'Equals' },
                    { value: 'date_after', label: 'After' },
                    { value: 'date_before', label: 'Before' },
                    { value: 'date_between', label: 'Between' }
                ],
                select: [
                    { value: 'equals', label: 'Equals' },
                    { value: 'not_equals', label: 'Not Equals' },
                    { value: 'in', label: 'In' },
                    { value: 'not_in', label: 'Not In' }
                ]
            };

            return operators[dataType] || operators.text;
        }

        /**
         * Get empty state HTML
         */
        getEmptyState() {
            return `
                <div class="atables-filter-empty">
                    <div class="atables-filter-empty-icon">
                        <span class="dashicons dashicons-filter"></span>
                    </div>
                    <div class="atables-filter-empty-text">No filters added yet</div>
                    <div class="atables-filter-empty-hint">Click "Add Filter" to create your first filter rule</div>
                </div>
            `;
        }

        /**
         * Handle column change - analyze column
         */
        onColumnChange(index, column) {
            this.filters[index].column = column;
            this.analyzeColumn(index, column);
        }

        /**
         * Analyze column to detect data type
         */
        analyzeColumn(index, column) {
            const self = this;

            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'GET',
                data: {
                    action: 'atables_analyze_column',
                    table_id: this.tableId,
                    column: column,
                    nonce: aTablesAdmin.nonce
                },
                success: function(response) {
                    if (response.success && response.data) {
                        // WordPress wraps the data in response.data.data
                        const columnData = response.data.data || response.data;
                        
                        if (columnData && columnData.data_type) {
                            self.filters[index].data_type = columnData.data_type;
                            
                            // Set default operator for detected type
                            const operators = self.getOperatorsForType(columnData.data_type);
                            if (operators.length > 0) {
                                self.filters[index].operator = operators[0].value;
                            }
                            
                            self.renderFilters();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to analyze column:', error);
                }
            });
        }

        /**
         * Handle operator change
         */
        onOperatorChange(index, operator) {
            this.filters[index].operator = operator;
            
            // Reset value for operators that need different input
            if (operator === 'between' || operator === 'date_between') {
                this.filters[index].value = ['', ''];
            } else if (operator === 'in' || operator === 'not_in') {
                this.filters[index].value = [''];
            } else {
                this.filters[index].value = '';
            }
            
            this.renderFilters();
        }

        /**
         * Add value field for IN operator
         */
        addValueField(index) {
            if (!Array.isArray(this.filters[index].value)) {
                this.filters[index].value = [this.filters[index].value || ''];
            }
            this.filters[index].value.push('');
            this.renderFilters();
        }

        /**
         * Collect filter values from inputs
         */
        collectFilterValues() {
            const self = this;
            
            $('.atables-filter-rule').each(function() {
                const index = $(this).data('index');
                const filter = self.filters[index];
                const operator = filter.operator;

                if (operator === 'in' || operator === 'not_in') {
                    // Multiple values
                    const values = [];
                    $(this).find('input[type="text"]').each(function() {
                        const val = $(this).val().trim();
                        if (val) values.push(val);
                    });
                    filter.value = values;
                } else if (operator === 'between' || operator === 'date_between') {
                    // Two values
                    const values = [];
                    $(this).find('input').each(function() {
                        values.push($(this).val());
                    });
                    filter.value = values;
                } else {
                    // Single value
                    filter.value = $(this).find('input').val();
                }
            });
        }

        /**
         * Apply filters to table
         */
        applyFilters() {
            this.collectFilterValues();

            // Validate filters
            const validFilters = this.filters.filter(f => f.column && f.operator && f.value);
            
            if (validFilters.length === 0) {
                this.showNotice('warning', 'Please add at least one complete filter rule.');
                return;
            }

            const self = this;
            $('.atables-filter-apply').prop('disabled', true).text('Applying...');

            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atables_apply_filters',
                    table_id: this.tableId,
                    filters: JSON.stringify(validFilters),
                    nonce: aTablesAdmin.nonce
                },
                success: function(response) {
                    if (response.success && response.data) {
                        // WordPress wraps the data in response.data.data
                        const filterData = response.data.data || response.data;
                        
                        const filteredCount = filterData.filtered_count || 0;
                        const originalCount = filterData.original_count || 0;
                        const filteredData = filterData.filtered_data || [];
                        
                        self.showActiveFilters(validFilters.length, {
                            filtered_count: filteredCount,
                            original_count: originalCount
                        });
                        
                        // Update the DataTable with filtered data
                        self.updateTableWithFilteredData(filteredData);
                        
                        self.showNotice('success', 'Filters applied successfully! Showing ' + filteredCount + ' of ' + originalCount + ' rows.');
                    } else {
                        const errorMsg = response.data && response.data.message ? response.data.message : 'Unknown error';
                        self.showNotice('error', 'Error applying filters: ' + errorMsg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to apply filters:', error);
                    self.showNotice('error', 'Failed to apply filters. Please try again.');
                },
                complete: function() {
                    $('.atables-filter-apply').prop('disabled', false).text('Apply Filters');
                }
            });
        }

        /**
         * Show active filters indicator
         */
        showActiveFilters(count, stats) {
            // Remove existing indicator
            $('.atables-active-filters').remove();
            
            const html = `
                <div class="atables-active-filters">
                    <span class="atables-active-filters-text">Active Filters:</span>
                    <span class="atables-active-filters-count">${count}</span>
                    <span class="atables-active-filters-text">
                        Showing ${stats.filtered_count} of ${stats.original_count} rows
                    </span>
                    <button class="atables-active-filters-clear">Clear All Filters</button>
                </div>
            `;

            $('.atables-filter-panel').before(html);
        }

        /**
         * Update DataTable with filtered data
         */
        updateTableWithFilteredData(filteredData) {
            // Try multiple selectors to find the table
            let $table = $('#atables-table-' + this.tableId);
            
            if (!$table.length) {
                $table = $('.atables-modern-table');
            }
            
            if (!$table.length) {
                $table = $('.atables-table');
            }
            
            if (!$table.length) {
                console.error('Table not found. Tried multiple selectors.');
                return;
            }

            console.log('Found table:', $table);
            console.log('Filtered data:', filteredData);

            // Convert object to array if necessary
            let dataArray = filteredData;
            if (!Array.isArray(filteredData)) {
                // Convert object with numeric keys to array
                dataArray = Object.values(filteredData);
                console.log('Converted to array:', dataArray);
            }

            // Store filtered data for pagination
            this.currentFilteredData = dataArray;
            this.currentPage = 1; // Reset to first page
            
            // Get per-page value from selector
            const perPage = parseInt($('#per-page-select').val() || 10);
            
            // Render first page
            this.renderPage(perPage, 1);
            
            // Update pagination controls
            this.updatePaginationForFiltered(dataArray.length, perPage, 1);
        }

        /**
         * Render a specific page of data
         */
        renderPage(perPage, page) {
            const $tbody = $('.atables-modern-table tbody');
            
            if (!$tbody.length) {
                console.error('Table tbody not found');
                return;
            }
            
            const dataArray = this.currentFilteredData || [];
            
            if (dataArray.length === 0) {
                const colspan = $(' .atables-modern-table thead th').length || 1;
                $tbody.html(`
                    <tr>
                        <td colspan="${colspan}" class="atables-no-data">
                            <span class="dashicons dashicons-info"></span>
                            No rows match the current filters.
                        </td>
                    </tr>
                `);
                return;
            }
            
            // Calculate start and end indices
            const start = (page - 1) * perPage;
            const end = Math.min(start + perPage, dataArray.length);
            const pageData = dataArray.slice(start, end);
            
            // Clear and render page
            $tbody.empty();
            
            pageData.forEach(row => {
                let rowHtml = '<tr>';
                Object.values(row).forEach(cell => {
                    const cellValue = cell !== null && cell !== undefined ? cell : '';
                    rowHtml += `<td>${this.escapeHtml(String(cellValue))}</td>`;
                });
                rowHtml += '</tr>';
                $tbody.append(rowHtml);
            });
            
            console.log(`Rendered page ${page} with ${pageData.length} rows`);
        }

        /**
         * Update pagination controls for filtered data
         */
        updatePaginationForFiltered(total, perPage, currentPage) {
            const totalPages = Math.ceil(total / perPage);
            const start = total > 0 ? ((currentPage - 1) * perPage) + 1 : 0;
            const end = Math.min(currentPage * perPage, total);
            
            // Update stats
            $('.atables-pagination-info p').html(
                `Showing <strong>${start}</strong> to <strong>${end}</strong> of <strong>${total}</strong> filtered rows`
            );
            
            // Remove old pagination
            $('.atables-pagination-controls').remove();
            
            if (totalPages <= 1) {
                return; // No pagination needed
            }
            
            // Build pagination HTML
            let html = '<div class="atables-pagination-controls">';
            
            // First/Previous
            if (currentPage > 1) {
                html += `<a href="#" class="atables-page-btn atables-page-first" data-page="1"><span class="dashicons dashicons-controls-skipback"></span></a>`;
                html += `<a href="#" class="atables-page-btn atables-page-prev" data-page="${currentPage - 1}"><span class="dashicons dashicons-arrow-left-alt2"></span></a>`;
            } else {
                html += '<span class="atables-page-btn atables-page-first disabled"><span class="dashicons dashicons-controls-skipback"></span></span>';
                html += '<span class="atables-page-btn atables-page-prev disabled"><span class="dashicons dashicons-arrow-left-alt2"></span></span>';
            }
            
            // Page numbers
            html += '<div class="atables-page-numbers">';
            
            const range = 2;
            const startPage = Math.max(1, currentPage - range);
            const endPage = Math.min(totalPages, currentPage + range);
            
            // First page + ellipsis
            if (startPage > 1) {
                html += `<a href="#" class="atables-page-num" data-page="1">1</a>`;
                if (startPage > 2) {
                    html += '<span class="atables-page-ellipsis">...</span>';
                }
            }
            
            // Page range
            for (let i = startPage; i <= endPage; i++) {
                if (i === currentPage) {
                    html += `<span class="atables-page-num active">${i}</span>`;
                } else {
                    html += `<a href="#" class="atables-page-num" data-page="${i}">${i}</a>`;
                }
            }
            
            // Last page + ellipsis
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += '<span class="atables-page-ellipsis">...</span>';
                }
                html += `<a href="#" class="atables-page-num" data-page="${totalPages}">${totalPages}</a>`;
            }
            
            html += '</div>';
            
            // Next/Last
            if (currentPage < totalPages) {
                html += `<a href="#" class="atables-page-btn atables-page-next" data-page="${currentPage + 1}"><span class="dashicons dashicons-arrow-right-alt2"></span></a>`;
                html += `<a href="#" class="atables-page-btn atables-page-last" data-page="${totalPages}"><span class="dashicons dashicons-controls-skipforward"></span></a>`;
            } else {
                html += '<span class="atables-page-btn atables-page-next disabled"><span class="dashicons dashicons-arrow-right-alt2"></span></span>';
                html += '<span class="atables-page-btn atables-page-last disabled"><span class="dashicons dashicons-controls-skipforward"></span></span>';
            }
            
            html += '</div>';
            
            // Add to footer
            $('.atables-data-footer').append(html);
            
            // Bind pagination click events
            this.bindPaginationEvents();
        }

        /**
         * Bind pagination click events for filtered data
         */
        bindPaginationEvents() {
            const self = this;
            
            // Unbind previous events to avoid conflicts
            $(document).off('click', '.atables-page-num, .atables-page-btn');
            $(document).on('click', '.atables-page-num, .atables-page-btn', function(e) {
                e.preventDefault();
                
                if ($(this).hasClass('disabled') || $(this).hasClass('active')) {
                    return;
                }
                
                const page = parseInt($(this).data('page'));
                if (page && !isNaN(page)) {
                    const perPage = parseInt($('#per-page-select').val() || 10);
                    self.currentPage = page;
                    self.renderPage(perPage, page);
                    self.updatePaginationForFiltered(self.currentFilteredData.length, perPage, page);
                    
                    // Scroll to table
                    $('html, body').animate({
                        scrollTop: $('.atables-data-card').offset().top - 100
                    }, 300);
                }
            });
            
            // Override per-page selector ONLY when filters are active
            $('#per-page-select').off('change').on('change', function(e) {
                if (self.currentFilteredData && self.currentFilteredData.length > 0) {
                    // Prevent default AJAX behavior from admin-table-view.js
                    e.stopImmediatePropagation();
                    
                    const perPage = parseInt($(this).val());
                    self.currentPage = 1; // Reset to first page
                    self.renderPage(perPage, 1);
                    self.updatePaginationForFiltered(self.currentFilteredData.length, perPage, 1);
                }
                // If no filters, let admin-table-view.js handle it
            });
            
            // Mark that we have active filters
            this.hasActiveFilters = true;
        }

        /**
         * Show notification using the global notification system
         */
        showNotice(type, message) {
            if (typeof window.ATablesNotifications !== 'undefined') {
                window.ATablesNotifications.show(message, type);
            } else {
                // Fallback to alert if notification system not loaded
                alert(message);
            }
        }

        /**
         * Escape HTML to prevent XSS
         */
        escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        /**
         * Load presets from server
         */
        loadPresets() {
            const self = this;

            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'GET',
                data: {
                    action: 'atables_get_table_presets',
                    table_id: this.tableId,
                    nonce: aTablesAdmin.nonce
                },
                success: function(response) {
                    console.log('Load presets response:', response);
                    if (response.success && response.data) {
                        // Ensure data is an array
                        self.presets = Array.isArray(response.data) ? response.data : [];
                        self.renderPresetSelector();
                    } else {
                        self.presets = [];
                        self.renderPresetSelector();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load presets:', error);
                    self.presets = [];
                    self.renderPresetSelector();
                }
            });
        }

        /**
         * Render preset selector dropdown
         */
        renderPresetSelector() {
            if (this.presets.length === 0) {
                $('.atables-preset-selector').hide();
                return;
            }

            let html = '<option value="">-- Select a preset --</option>';
            this.presets.forEach(preset => {
                const selected = this.currentPreset && this.currentPreset.id === preset.id ? 'selected' : '';
                const defaultBadge = preset.is_default ? ' (Default)' : '';
                html += `<option value="${preset.id}" ${selected}>${preset.name}${defaultBadge}</option>`;
            });

            $('.atables-preset-select').html(html);
            $('.atables-preset-selector').show();
        }

        /**
         * Load a preset
         */
        loadPreset(presetId) {
            const preset = this.presets.find(p => p.id === presetId);
            if (!preset) return;

            this.currentPreset = preset;
            this.filters = preset.filters.map(f => ({ ...f }));
            this.renderFilters();
        }

        /**
         * Delete a preset
         */
        deletePreset(presetId) {
            const self = this;

            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atables_delete_filter_preset',
                    preset_id: presetId,
                    nonce: aTablesAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.loadPresets();
                        if (self.currentPreset && self.currentPreset.id === presetId) {
                            self.currentPreset = null;
                            self.clearFilters();
                        }
                        self.showNotice('success', 'Preset deleted successfully!');
                    } else {
                        self.showNotice('error', 'Error deleting preset: ' + (response.data ? response.data.message : 'Unknown error'));
                    }
                }
            });
        }

        /**
         * Duplicate a preset
         */
        duplicatePreset(presetId) {
            const self = this;

            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atables_duplicate_preset',
                    preset_id: presetId,
                    nonce: aTablesAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.loadPresets();
                        self.showNotice('success', 'Preset duplicated successfully!');
                    } else {
                        self.showNotice('error', 'Error duplicating preset: ' + (response.data ? response.data.message : 'Unknown error'));
                    }
                }
            });
        }

        /**
         * Show save preset modal
         */
        showSavePresetModal() {
            this.collectFilterValues();

            if (this.filters.length === 0) {
                this.showNotice('warning', 'Please add at least one filter before saving.');
                return;
            }

            $('.atables-preset-modal').addClass('active');
            $('#atables-preset-name').val('');
            $('#atables-preset-description').val('');
            $('#atables-preset-default').prop('checked', false);
        }

        /**
         * Hide save preset modal
         */
        hideSavePresetModal() {
            $('.atables-preset-modal').removeClass('active');
        }

        /**
         * Save preset
         */
        savePreset() {
            const name = $('#atables-preset-name').val().trim();
            const description = $('#atables-preset-description').val().trim();
            const isDefault = $('#atables-preset-default').is(':checked');

            if (!name) {
                this.showNotice('warning', 'Please enter a preset name.');
                return;
            }

            const self = this;
            $('.atables-preset-modal-save').prop('disabled', true).text('Saving...');

            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atables_create_filter_preset',
                    table_id: this.tableId,
                    name: name,
                    description: description,
                    filters: JSON.stringify(this.filters),
                    is_default: isDefault,
                    nonce: aTablesAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.hideSavePresetModal();
                        self.loadPresets();
                        self.showNotice('success', 'Preset saved successfully!');
                    } else {
                        self.showNotice('error', 'Error saving preset: ' + (response.data ? response.data.message : 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Save preset error:', error);
                    self.showNotice('error', 'Failed to save preset. Please try again.');
                },
                complete: function() {
                    $('.atables-preset-modal-save').prop('disabled', false).text('Save Preset');
                }
            });
        }
    }

    // Initialize filter builder when DOM is ready
    $(document).ready(function() {
        console.log('DOM ready, looking for filter panel...');
        
        // Check if we're on a table view page with filter panel
        if ($('.atables-filter-panel').length > 0) {
            console.log('Filter panel found!');
            
            const tableId = $('.atables-filter-panel').data('table-id');
            const columnsData = $('.atables-filter-panel').data('columns');
            
            console.log('Table ID:', tableId);
            console.log('Columns data:', columnsData);
            
            if (tableId && columnsData) {
                const columns = Array.isArray(columnsData) ? columnsData : [];
                console.log('Parsed columns:', columns);
                
                window.aTablesFilterBuilder = new FilterBuilder(tableId, columns);
                console.log('FilterBuilder instance created');
            } else {
                console.error('Missing table ID or columns data');
            }
        } else {
            console.log('No filter panel found on this page');
        }
    });

})(jQuery);
