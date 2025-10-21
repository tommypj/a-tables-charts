/**
 * A-Tables & Charts - Bulk Edit Rows
 * 
 * Handles bulk operations on table rows (delete, duplicate, edit)
 * 
 * @package ATablesCharts
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Bulk Edit Manager
     */
    const BulkEditManager = {
        
        /**
         * Selected rows
         */
        selectedRows: [],
        
        /**
         * Initialize bulk edit
         */
        init: function() {
            this.bindEvents();
            this.updateBulkActionsBar();
        },
        
        /**
         * Bind events
         */
        bindEvents: function() {
            const self = this;
            
            // Select all checkbox
            $(document).on('change', '#atables-select-all', function() {
                const isChecked = $(this).prop('checked');
                $('.atables-row-checkbox').prop('checked', isChecked);
                self.updateSelectedRows();
            });
            
            // Individual row checkboxes
            $(document).on('change', '.atables-row-checkbox', function() {
                self.updateSelectedRows();
                self.updateSelectAllCheckbox();
            });
            
            // Bulk delete button
            $(document).on('click', '#atables-bulk-delete', function(e) {
                e.preventDefault();
                self.bulkDelete();
            });
            
            // Bulk duplicate button
            $(document).on('click', '#atables-bulk-duplicate', function(e) {
                e.preventDefault();
                self.bulkDuplicate();
            });
            
            // Bulk edit button
            $(document).on('click', '#atables-bulk-edit', function(e) {
                e.preventDefault();
                self.openBulkEditModal();
            });
            
            // Clear selection button
            $(document).on('click', '#atables-clear-selection', function(e) {
                e.preventDefault();
                self.clearSelection();
            });
        },
        
        /**
         * Update selected rows array
         */
        updateSelectedRows: function() {
            this.selectedRows = [];
            
            $('.atables-row-checkbox:checked').each(function() {
                const rowIndex = $(this).data('row-index');
                this.selectedRows.push(rowIndex);
            }.bind(this));
            
            this.updateBulkActionsBar();
        },
        
        /**
         * Update select all checkbox state
         */
        updateSelectAllCheckbox: function() {
            const totalCheckboxes = $('.atables-row-checkbox').length;
            const checkedCheckboxes = $('.atables-row-checkbox:checked').length;
            
            $('#atables-select-all').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
        },
        
        /**
         * Update bulk actions bar
         */
        updateBulkActionsBar: function() {
            const count = this.selectedRows.length;
            const $bar = $('#atables-bulk-actions-bar');
            const $count = $('#atables-selected-count');
            
            if (count > 0) {
                $bar.slideDown(200);
                $count.text(count);
            } else {
                $bar.slideUp(200);
            }
        },
        
        /**
         * Clear selection
         */
        clearSelection: function() {
            $('.atables-row-checkbox').prop('checked', false);
            $('#atables-select-all').prop('checked', false);
            this.updateSelectedRows();
        },
        
        /**
         * Bulk delete rows
         */
        bulkDelete: function() {
            if (this.selectedRows.length === 0) {
                alert('Please select rows to delete.');
                return;
            }
            
            const count = this.selectedRows.length;
            const message = count === 1 
                ? 'Are you sure you want to delete this row?' 
                : `Are you sure you want to delete ${count} rows?`;
            
            if (!confirm(message)) {
                return;
            }
            
            this.performBulkAction('delete', {
                rows: this.selectedRows
            });
        },
        
        /**
         * Bulk duplicate rows
         */
        bulkDuplicate: function() {
            if (this.selectedRows.length === 0) {
                alert('Please select rows to duplicate.');
                return;
            }
            
            const count = this.selectedRows.length;
            const message = count === 1 
                ? 'Duplicate this row?' 
                : `Duplicate ${count} rows?`;
            
            if (!confirm(message)) {
                return;
            }
            
            this.performBulkAction('duplicate', {
                rows: this.selectedRows
            });
        },
        
        /**
         * Open bulk edit modal
         */
        openBulkEditModal: function() {
            if (this.selectedRows.length === 0) {
                alert('Please select rows to edit.');
                return;
            }
            
            // Get column headers
            const headers = [];
            $('#atables-data-table thead th').each(function(index) {
                if (index > 0) { // Skip checkbox column
                    headers.push($(this).text().trim());
                }
            });
            
            // Build modal HTML
            let modalHTML = `
                <div class="atables-modal" id="atables-bulk-edit-modal">
                    <div class="atables-modal-content" style="max-width: 600px;">
                        <div class="atables-modal-header">
                            <h3>Bulk Edit ${this.selectedRows.length} Row(s)</h3>
                            <button class="atables-modal-close">&times;</button>
                        </div>
                        <div class="atables-modal-body">
                            <p class="description">Select a column and enter a value to apply to all selected rows.</p>
                            <form id="atables-bulk-edit-form">
                                <div class="atables-form-group">
                                    <label>Column to Edit:</label>
                                    <select name="column" id="atables-bulk-edit-column" class="atables-input" required>
                                        <option value="">-- Select Column --</option>
            `;
            
            headers.forEach(function(header) {
                modalHTML += `<option value="${header}">${header}</option>`;
            });
            
            modalHTML += `
                                    </select>
                                </div>
                                <div class="atables-form-group">
                                    <label>New Value:</label>
                                    <input type="text" name="value" id="atables-bulk-edit-value" class="atables-input" required placeholder="Enter new value">
                                </div>
                                <div class="atables-form-actions">
                                    <button type="submit" class="atables-btn atables-btn-primary">
                                        Apply to ${this.selectedRows.length} Row(s)
                                    </button>
                                    <button type="button" class="atables-btn atables-btn-secondary atables-modal-close">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            
            // Append modal to body
            $('body').append(modalHTML);
            
            // Show modal
            $('#atables-bulk-edit-modal').fadeIn(200);
            
            // Bind modal events
            this.bindModalEvents();
        },
        
        /**
         * Bind modal events
         */
        bindModalEvents: function() {
            const self = this;
            
            // Close modal
            $(document).on('click', '.atables-modal-close', function() {
                $('#atables-bulk-edit-modal').fadeOut(200, function() {
                    $(this).remove();
                });
            });
            
            // Submit bulk edit form
            $(document).on('submit', '#atables-bulk-edit-form', function(e) {
                e.preventDefault();
                
                const column = $('#atables-bulk-edit-column').val();
                const value = $('#atables-bulk-edit-value').val();
                
                if (!column || !value) {
                    alert('Please select a column and enter a value.');
                    return;
                }
                
                self.performBulkAction('edit', {
                    rows: self.selectedRows,
                    column: column,
                    value: value
                });
                
                // Close modal
                $('#atables-bulk-edit-modal').fadeOut(200, function() {
                    $(this).remove();
                });
            });
        },
        
        /**
         * Perform bulk action via AJAX
         */
        performBulkAction: function(action, data) {
            const self = this;
            const tableId = $('#atables-table-id').val();
            
            // Show loading
            this.showLoading();
            
            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atables_bulk_action',
                    nonce: aTablesAdmin.nonce,
                    table_id: tableId,
                    bulk_action: action,
                    data: data
                },
                success: function(response) {
                    self.hideLoading();
                    
                    if (response.success) {
                        // Show success message
                        if (window.ATablesNotifications) {
                            window.ATablesNotifications.show(
                                response.data.message || 'Action completed successfully',
                                'success'
                            );
                        }
                        
                        // Reload page to show updated data
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        alert(response.data.message || 'Action failed. Please try again.');
                    }
                },
                error: function() {
                    self.hideLoading();
                    alert('An error occurred. Please try again.');
                }
            });
        },
        
        /**
         * Show loading indicator
         */
        showLoading: function() {
            $('body').append('<div class="atables-loading-overlay"><div class="atables-spinner"></div></div>');
        },
        
        /**
         * Hide loading indicator
         */
        hideLoading: function() {
            $('.atables-loading-overlay').remove();
        }
    };
    
    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        // Only initialize on table edit/view pages
        if ($('#atables-data-table').length > 0) {
            BulkEditManager.init();
        }
    });
    
})(jQuery);
