/**
 * A-Tables & Charts - Delete Functionality
 * Handles all delete operations with beautiful modals
 *
 * @package ATablesCharts
 * @since 1.0.0
 */

(function($) {
	'use strict';

	/**
	 * Initialize delete functionality
	 */
	function initDelete() {
		// Dashboard - Single table delete
		initDashboardDelete();
		
		// Dashboard - Bulk delete
		initBulkDelete();
		
		// Edit page - Delete button
		initEditPageDelete();
		
		// Copy shortcode (update to use modals)
		initCopyShortcode();
		
		// Duplicate table (update to use modals)
		initDuplicateTable();
	}

	/**
	 * Dashboard single table delete
	 */
	function initDashboardDelete() {
		$(document).on('click', '.atables-delete-btn', async function() {
			const $btn = $(this);
			const tableId = $btn.data('table-id');
			
			// Try to get title from data attribute first (view/edit pages)
			let tableTitle = $btn.data('table-title');
			
			// If not found, try to get from table row (dashboard page)
			if (!tableTitle) {
				const $row = $btn.closest('tr');
				tableTitle = $row.find('strong').first().text().trim();
			}
			
			// Fallback
			if (!tableTitle) {
				tableTitle = 'this table';
			}
			
			const confirmed = await ATablesModal.confirm({
				title: 'Delete Table?',
				message: `You are about to permanently delete the table <strong>"${tableTitle}"</strong>. All data will be lost and this action cannot be undone.`,
				type: 'danger',
				icon: '🗑️',
				confirmText: 'Delete Table',
				cancelText: 'Cancel',
				confirmClass: 'danger',
				requireConfirmation: true,
				confirmationText: tableTitle,
				confirmationPlaceholder: 'Type table name to confirm deletion...'
			});
			
			if (!confirmed) return;
			
			// Disable button
			const originalHtml = $btn.html();
			$btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span>');
			
			// Send delete request
			$.ajax({
				url: aTablesAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'atables_delete_table',
					nonce: aTablesAdmin.nonce,
					table_id: tableId
				},
				success: async function(response) {
					if (response.success) {
						// Check if we're on dashboard (in a table row)
						const $row = $btn.closest('tr');
						if ($row.length) {
							// Remove row with animation
							$row.fadeOut(400, function() {
								$(this).remove();
								
								// Check if table is empty
								if ($('.atables-table-list tbody tr').length === 0) {
									location.reload();
								}
							});
						} else {
							// On view page, redirect to dashboard
							await ATablesModal.success('Table deleted successfully!');
							window.location.href = 'admin.php?page=a-tables-charts';
							return;
						}
						
						await ATablesModal.success('Table deleted successfully!');
					} else {
						await ATablesModal.error(response.data || 'Failed to delete table. Please try again.');
						$btn.prop('disabled', false).html(originalHtml);
					}
				},
				error: async function(xhr, status, error) {
					console.error('Delete error:', error);
					await ATablesModal.error('An error occurred while deleting the table.');
					$btn.prop('disabled', false).html(originalHtml);
				}
			});
		});
	}

	/**
	 * Bulk delete functionality
	 */
	function initBulkDelete() {
		$(document).on('click', '#atables-apply-bulk', async function() {
			const action = $('#atables-bulk-action').val();
			const selectedIds = $('.atables-table-checkbox:checked').map(function() {
				return $(this).val();
			}).get();
			
			if (!action) {
				await ATablesModal.alert({
					title: 'No Action Selected',
					message: 'Please select an action from the dropdown.',
					type: 'warning',
					icon: '⚠️'
				});
				return;
			}
			
			if (selectedIds.length === 0) {
				await ATablesModal.alert({
					title: 'No Tables Selected',
					message: 'Please select at least one table to perform bulk actions.',
					type: 'warning',
					icon: '⚠️'
				});
				return;
			}
			
			if (action === 'delete') {
				const confirmed = await ATablesModal.confirm({
					title: 'Delete Multiple Tables?',
					message: `You are about to permanently delete <strong>${selectedIds.length} table(s)</strong>. All data will be lost and this action cannot be undone.`,
					type: 'danger',
					icon: '🗑️',
					confirmText: `Delete ${selectedIds.length} Tables`,
					cancelText: 'Cancel',
					confirmClass: 'danger',
					requireConfirmation: true,
					confirmationText: 'DELETE',
					confirmationPlaceholder: 'Type "DELETE" to confirm...'
				});
				
				if (!confirmed) return;
				
				// Disable button
				const $btn = $('#atables-apply-bulk');
				const originalText = $btn.text();
				$btn.prop('disabled', true).text('Deleting...');
				
				// Delete each table
				let completed = 0;
				let errors = 0;
				
				selectedIds.forEach(function(tableId) {
					$.ajax({
						url: aTablesAdmin.ajaxUrl,
						type: 'POST',
						data: {
							action: 'atables_delete_table',
							nonce: aTablesAdmin.nonce,
							table_id: tableId
						},
						success: function(response) {
							completed++;
							if (!response.success) {
								errors++;
							}
							
							// Remove row
							$(`.atables-table-checkbox[value="${tableId}"]`).closest('tr').fadeOut(400);
							
							if (completed === selectedIds.length) {
								if (errors === 0) {
									ATablesModal.success(`${selectedIds.length} table(s) deleted successfully!`).then(() => {
										setTimeout(() => location.reload(), 500);
									});
								} else {
									ATablesModal.error(`${errors} table(s) failed to delete.`);
									$btn.prop('disabled', false).text(originalText);
								}
							}
						},
						error: function() {
							completed++;
							errors++;
							
							if (completed === selectedIds.length) {
								ATablesModal.error(`${errors} table(s) failed to delete.`);
								$btn.prop('disabled', false).text(originalText);
							}
						}
					});
				});
			}
		});
	}

	/**
	 * Edit page delete button
	 */
	function initEditPageDelete() {
		$(document).on('click', '.atables-delete-table-btn', async function() {
			const $btn = $(this);
			const tableId = $btn.data('table-id');
			const tableTitle = $btn.data('table-title') || 'this table';
			
			const confirmed = await ATablesModal.confirm({
				title: 'Delete Table?',
				message: `You are about to permanently delete <strong>"${tableTitle}"</strong>. All data will be lost and this action cannot be undone.`,
				type: 'danger',
				icon: '🗑️',
				confirmText: 'Delete Table',
				cancelText: 'Cancel',
				confirmClass: 'danger',
				requireConfirmation: true,
				confirmationText: tableTitle,
				confirmationPlaceholder: 'Type table name to confirm deletion...'
			});
			
			if (!confirmed) return;
			
			// Disable button
			const originalHtml = $btn.html();
			$btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> Deleting...');
			
			// Send delete request
			$.ajax({
				url: aTablesAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'atables_delete_table',
					nonce: aTablesAdmin.nonce,
					table_id: tableId
				},
				success: async function(response) {
					if (response.success) {
						await ATablesModal.success('Table deleted successfully!');
						// Redirect to dashboard
						window.location.href = 'admin.php?page=a-tables-charts';
					} else {
						await ATablesModal.error(response.data || 'Failed to delete table. Please try again.');
						$btn.prop('disabled', false).html(originalHtml);
					}
				},
				error: async function(xhr, status, error) {
					console.error('Delete error:', error);
					await ATablesModal.error('An error occurred while deleting the table.');
					$btn.prop('disabled', false).html(originalHtml);
				}
			});
		});
	}

	/**
	 * Copy shortcode with modal feedback
	 */
	function initCopyShortcode() {
		$(document).on('click', '.atables-copy-shortcode', async function() {
			const $btn = $(this);
			
			// Check if button has pre-built shortcode (view-table.php)
			let shortcode = $btn.data('shortcode');
			
			// If no shortcode, build from table-id (dashboard.php)
			if (!shortcode) {
				const tableId = $btn.data('table-id');
				if (!tableId) {
					console.error('No table ID or shortcode found:', $btn);
					await ATablesModal.error('Unable to copy shortcode. Please try again.');
					return;
				}
				shortcode = '[atable id="' + tableId + '"]';
			}
			
			try {
				await navigator.clipboard.writeText(shortcode);
				await ATablesModal.success({
					title: 'Shortcode Copied!',
					message: `Table shortcode copied to clipboard:<br><code style="background:#f6f7f7;padding:4px 8px;border-radius:4px;font-family:monospace;">${shortcode}</code>`
				});
			} catch (err) {
				await ATablesModal.alert({
					title: 'Copy Shortcode',
					message: `Please copy this shortcode manually:<br><code style="background:#f6f7f7;padding:8px 12px;border-radius:4px;font-family:monospace;display:block;margin-top:8px;">${shortcode}</code>`,
					type: 'info',
					icon: '📋'
				});
			}
		});
	}

	/**
	 * Duplicate table with modal
	 */
	function initDuplicateTable() {
		$(document).on('click', '.atables-duplicate-btn', async function() {
			const $btn = $(this);
			const tableId = $btn.data('table-id');
			const tableTitle = $btn.data('table-title');
			
			const newTitle = prompt('Enter a name for the duplicated table:', tableTitle + ' (Copy)');
			
			if (!newTitle) {
				return; // User cancelled
			}
			
			// Disable button
			const originalHtml = $btn.html();
			$btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span>');
			
			// Send AJAX request
			$.ajax({
				url: aTablesAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'atables_duplicate_table',
					nonce: aTablesAdmin.nonce,
					table_id: tableId,
					new_title: newTitle
				},
				success: async function(response) {
					if (response.success) {
						await ATablesModal.success(response.data.message || 'Table duplicated successfully!');
						setTimeout(() => location.reload(), 1000);
					} else {
						await ATablesModal.error(response.data || 'Failed to duplicate table.');
						$btn.prop('disabled', false).html(originalHtml);
					}
				},
				error: async function(xhr, status, error) {
					console.error('Duplicate error:', error);
					await ATablesModal.error('Failed to duplicate table: ' + error);
					$btn.prop('disabled', false).html(originalHtml);
				}
			});
		});
	}

	// Initialize when document is ready
	$(document).ready(function() {
		initDelete();
	});

})(jQuery);
