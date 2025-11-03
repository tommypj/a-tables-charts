/**
 * A-Tables & Charts - Frontend Tables Interactivity
 *
 * Initializes DataTables for interactive frontend tables.
 *
 * @package ATablesCharts
 * @since 1.0.0
 */

jQuery(document).ready(function($) {
	'use strict';

	// Initialize button event listeners
	initializeButtonHandlers();

	// Wait a bit for everything to load
	setTimeout(function() {
		initializeTables();
	}, 100);

	/**
	 * Initialize keyboard-accessible button handlers
	 * WCAG 2.2 Level AA - Keyboard Accessible (2.1.1)
	 */
	function initializeButtonHandlers() {
		// Copy button handler
		$(document).on('click keydown', '.atables-copy-btn', function(e) {
			// Handle both click and Enter/Space key
			if (e.type === 'click' || e.which === 13 || e.which === 32) {
				e.preventDefault();
				const tableId = $(this).data('table-id');
				const fullTableId = 'atables-table-' + tableId;
				copyTableToClipboard(fullTableId);

				// Announce success to screen readers
				announceToScreenReader(fullTableId, 'Table copied to clipboard');
			}
		});

		// Print button handler
		$(document).on('click keydown', '.atables-print-btn', function(e) {
			// Handle both click and Enter/Space key
			if (e.type === 'click' || e.which === 13 || e.which === 32) {
				e.preventDefault();
				const tableId = $(this).data('table-id');
				const fullTableId = 'atables-table-' + tableId;
				printTable(fullTableId);
			}
		});
	}

	function initializeTables() {
		$('.atables-interactive').each(function() {
			const $table = $(this);
			const tableId = $table.attr('id');

			console.log('Initializing table:', tableId);

			// Destroy if already initialized
			if ($.fn.DataTable.isDataTable($table)) {
				console.log('Table already initialized, skipping');
				return; // Skip this table
			}
			
			// Read configuration from data attributes
			const searchAttr = $table.data('search');
			const sortingAttr = $table.data('sorting');
			const paginationAttr = $table.data('pagination');
			const infoAttr = $table.data('info');
			const pageLengthAttr = $table.data('page-length');
			
			// Convert data attributes to proper booleans
			const searching = searchAttr === 'true' || searchAttr === true || searchAttr === 1;
			const ordering = sortingAttr === 'true' || sortingAttr === true || sortingAttr === 1;
			const paging = paginationAttr === 'true' || paginationAttr === true || paginationAttr === 1;
			const info = infoAttr === 'true' || infoAttr === true || infoAttr === 1;
			
			console.log('Table configuration:', {
				searching: searching,
				ordering: ordering,
				paging: paging,
				info: info,
				pageLength: pageLengthAttr
			});
			
			const config = {
				searching: searching,
				ordering: ordering,
				paging: paging,
				info: info,
				pageLength: parseInt(pageLengthAttr) || 10,
				lengthMenu: [[10, 25, 50, 100], ["10", "25", "50", "100"]],
				autoWidth: false,
				order: [], // Disable initial sorting
				columnDefs: [
					{
						targets: '_all',
						orderable: ordering,
						type: 'string' // Force string type to avoid auto-detection
					}
				],
				language: {
					search: "Search:",
					lengthMenu: "Show _MENU_ entries",
					info: "Showing _START_ to _END_ of _TOTAL_ entries",
					paginate: {
						first: "First",
						last: "Last",
						next: "Next",
						previous: "Previous"
					},
					processing: '<i class="atables-spinner"></i> Loading...'
				},
				processing: true, // Show loading indicator
				dom: '<"atables-controls-wrapper"<"atables-controls-left"l><"atables-controls-right"f>>rt<"atables-controls-bottom"<"atables-info-wrapper"i><"atables-pagination-wrapper"p>>'
			};
			
			// Initialize with configuration from data attributes
			try {
				const dt = $table.DataTable(config);

				// ACCESSIBILITY: Add ARIA labels to DataTables controls
				addDataTablesAccessibility($table, tableId);

				// ACCESSIBILITY: Add sort state announcements
				addSortStateAnnouncements(dt, tableId);

				// CRITICAL: Force Bootstrap theme styles after DataTables loads
				if ($table.hasClass('atables-theme-classic')) {
					// Bootstrap Primary (Blue)
					$table.find('thead th').attr('style', 'background: #0d6efd !important; color: #fff !important; border-color: #0d6efd !important; font-weight: 600 !important; padding: 14px 16px !important;');
				} else if ($table.hasClass('atables-theme-modern')) {
					// Bootstrap Success (Green)
					$table.find('thead th').attr('style', 'background: #198754 !important; color: #fff !important; border-color: #198754 !important; font-weight: 600 !important; padding: 14px 16px !important;');
				} else if ($table.hasClass('atables-theme-dark')) {
					// Bootstrap Dark
					$table.find('thead th').attr('style', 'background: #343a40 !important; color: #fff !important; border-color: #495057 !important; font-weight: 600 !important; padding: 14px 16px !important;');
					$table.css('background', '#212529');
					$table.find('tbody td').attr('style', 'background: #212529 !important; color: #dee2e6 !important; border-color: #495057 !important;');
				} else if ($table.hasClass('atables-theme-striped')) {
					// Bootstrap Striped
					$table.find('thead th').attr('style', 'background: #f8f9fa !important; color: #212529 !important; border-bottom: 2px solid #dee2e6 !important; font-weight: 600 !important; padding: 14px 16px !important;');
					$table.find('tbody tr').each(function(index) {
						if (index % 2 === 0) {
							$(this).find('td').attr('style', 'background: rgba(0, 0, 0, 0.05) !important; color: #212529 !important; border-bottom: 1px solid #dee2e6 !important;');
						} else {
							$(this).find('td').attr('style', 'background: #fff !important; color: #212529 !important; border-bottom: 1px solid #dee2e6 !important;');
						}
					});
				} else if ($table.hasClass('atables-theme-minimal')) {
					// Bootstrap Bordered
					$table.find('thead th').attr('style', 'background: #fff !important; color: #212529 !important; border: 1px solid #dee2e6 !important; font-weight: 700 !important; padding: 14px 16px !important; text-transform: uppercase !important; font-size: 13px !important;');
					$table.find('tbody td').attr('style', 'border: 1px solid #dee2e6 !important; color: #212529 !important; background: #fff !important;');
					$table.css('border', '2px solid #dee2e6');
				} else if ($table.hasClass('atables-theme-material')) {
					// Bootstrap Hover (Minimalist)
					$table.find('thead th').attr('style', 'background: transparent !important; color: #495057 !important; border-bottom: 2px solid #dee2e6 !important; font-weight: 700 !important; padding: 14px 16px !important;');
					$table.find('tbody td').attr('style', 'border: none !important; border-bottom: 1px solid #dee2e6 !important; background: transparent !important; color: #212529 !important;');
					$table.css('border', 'none');
				}
				
				// Re-apply theme on draw (pagination, sort, search)
				$table.on('draw.dt', function() {
					if ($table.hasClass('atables-theme-classic')) {
						$table.find('thead th').attr('style', 'background: #0d6efd !important; color: #fff !important; border-color: #0d6efd !important; font-weight: 600 !important; padding: 14px 16px !important;');
					} else if ($table.hasClass('atables-theme-modern')) {
						$table.find('thead th').attr('style', 'background: #198754 !important; color: #fff !important; border-color: #198754 !important; font-weight: 600 !important; padding: 14px 16px !important;');
					} else if ($table.hasClass('atables-theme-dark')) {
						$table.find('thead th').attr('style', 'background: #343a40 !important; color: #fff !important; border-color: #495057 !important; font-weight: 600 !important; padding: 14px 16px !important;');
						$table.css('background', '#212529');
						$table.find('tbody td').attr('style', 'background: #212529 !important; color: #dee2e6 !important; border-color: #495057 !important;');
					} else if ($table.hasClass('atables-theme-striped')) {
						$table.find('thead th').attr('style', 'background: #f8f9fa !important; color: #212529 !important; border-bottom: 2px solid #dee2e6 !important; font-weight: 600 !important; padding: 14px 16px !important;');
						$table.find('tbody tr').each(function(index) {
							if (index % 2 === 0) {
								$(this).find('td').attr('style', 'background: rgba(0, 0, 0, 0.05) !important; color: #212529 !important; border-bottom: 1px solid #dee2e6 !important;');
							} else {
								$(this).find('td').attr('style', 'background: #fff !important; color: #212529 !important; border-bottom: 1px solid #dee2e6 !important;');
							}
						});
					} else if ($table.hasClass('atables-theme-minimal')) {
						$table.find('thead th').attr('style', 'background: #fff !important; color: #212529 !important; border: 1px solid #dee2e6 !important; font-weight: 700 !important; padding: 14px 16px !important; text-transform: uppercase !important; font-size: 13px !important;');
						$table.find('tbody td').attr('style', 'border: 1px solid #dee2e6 !important; color: #212529 !important; background: #fff !important;');
						$table.css('border', '2px solid #dee2e6');
					} else if ($table.hasClass('atables-theme-material')) {
						$table.find('thead th').attr('style', 'background: transparent !important; color: #495057 !important; border-bottom: 2px solid #dee2e6 !important; font-weight: 700 !important; padding: 14px 16px !important;');
						$table.find('tbody td').attr('style', 'border: none !important; border-bottom: 1px solid #dee2e6 !important; background: transparent !important; color: #212529 !important;');
						$table.css('border', 'none');
					}
				});
				
				// Add column visibility toggle
				addColumnToggle(dt, $table);
				
				console.log('✓ Table initialized successfully');
			} catch (error) {
				console.error('✗ Initialization error:', error);
			}
		});
	}
	
	/**
	 * Add ARIA labels and accessibility attributes to DataTables controls
	 * WCAG 2.2 Level AA - Labels or Instructions (3.3.2), Name, Role, Value (4.1.2)
	 */
	function addDataTablesAccessibility($table, tableId) {
		const $wrapper = $table.closest('.atables-frontend-wrapper');

		// Add label to search input
		const $searchInput = $wrapper.find('.dataTables_filter input[type="search"]');
		if ($searchInput.length) {
			$searchInput.attr({
				'aria-label': 'Search table',
				'placeholder': 'Search...',
				'id': 'search-' + tableId
			});

			// Add proper label element
			const $searchLabel = $wrapper.find('.dataTables_filter label');
			if ($searchLabel.length) {
				$searchLabel.attr('for', 'search-' + tableId);
			}
		}

		// Add label to length selector
		const $lengthSelect = $wrapper.find('.dataTables_length select');
		if ($lengthSelect.length) {
			$lengthSelect.attr({
				'aria-label': 'Number of rows to display',
				'id': 'length-' + tableId
			});

			// Add proper label element
			const $lengthLabel = $wrapper.find('.dataTables_length label');
			if ($lengthLabel.length) {
				$lengthLabel.attr('for', 'length-' + tableId);
			}
		}

		// Add ARIA labels to pagination buttons
		$wrapper.find('.dataTables_paginate .paginate_button').each(function() {
			const $btn = $(this);
			const text = $btn.text().trim();

			if ($btn.hasClass('first')) {
				$btn.attr('aria-label', 'First page');
			} else if ($btn.hasClass('previous')) {
				$btn.attr('aria-label', 'Previous page');
			} else if ($btn.hasClass('next')) {
				$btn.attr('aria-label', 'Next page');
			} else if ($btn.hasClass('last')) {
				$btn.attr('aria-label', 'Last page');
			} else if ($btn.hasClass('current')) {
				$btn.attr({
					'aria-label': 'Current page, page ' + text,
					'aria-current': 'page'
				});
			} else {
				$btn.attr('aria-label', 'Go to page ' + text);
			}
		});

		// Make pagination keyboard accessible
		$wrapper.find('.dataTables_paginate .paginate_button').on('keydown', function(e) {
			if (e.which === 13 || e.which === 32) { // Enter or Space
				e.preventDefault();
				$(this).click();
			}
		});

		// Add ARIA labels to sortable column headers
		$table.find('thead th').each(function() {
			const $th = $(this);
			const columnName = $th.text().trim();

			if (columnName) {
				$th.attr({
					'role': 'columnheader',
					'aria-label': columnName + ' (sortable)',
					'tabindex': '0'
				});

				// Keyboard support for sorting
				$th.on('keydown', function(e) {
					if (e.which === 13 || e.which === 32) { // Enter or Space
						e.preventDefault();
						$(this).click();
					}
				});
			}
		});

		// Update pagination ARIA on page changes
		$table.on('draw.dt', function() {
			$wrapper.find('.dataTables_paginate .paginate_button').each(function() {
				const $btn = $(this);
				const text = $btn.text().trim();

				if ($btn.hasClass('current')) {
					$btn.attr({
						'aria-label': 'Current page, page ' + text,
						'aria-current': 'page'
					});
				} else if (!$btn.hasClass('first') && !$btn.hasClass('previous') && !$btn.hasClass('next') && !$btn.hasClass('last')) {
					$btn.attr('aria-label', 'Go to page ' + text);
					$btn.removeAttr('aria-current');
				}
			});
		});
	}

	/**
	 * Add sort state announcements for screen readers
	 * WCAG 2.2 Level AA - Status Messages (4.1.3)
	 */
	function addSortStateAnnouncements(dataTable, tableId) {
		dataTable.on('order.dt', function() {
			const order = dataTable.order();
			if (order.length > 0) {
				const columnIndex = order[0][0];
				const direction = order[0][1]; // 'asc' or 'desc'
				const columnName = $(dataTable.column(columnIndex).header()).text().trim();

				const message = 'Table sorted by ' + columnName + ', ' +
				                (direction === 'asc' ? 'ascending' : 'descending') + ' order';

				// Announce to screen readers
				announceToScreenReader(tableId, message);

				// Update column header ARIA labels
				dataTable.columns().every(function(index) {
					const $header = $(this.header());
					const name = $header.text().trim();

					if (index === columnIndex) {
						$header.attr('aria-sort', direction === 'asc' ? 'ascending' : 'descending');
						$header.attr('aria-label', name + ' (sorted ' + (direction === 'asc' ? 'ascending' : 'descending') + ')');
					} else {
						$header.removeAttr('aria-sort');
						$header.attr('aria-label', name + ' (sortable)');
					}
				});
			}
		});

		// Announce pagination changes
		dataTable.on('page.dt', function() {
			const info = dataTable.page.info();
			const message = 'Showing page ' + (info.page + 1) + ' of ' + info.pages;
			announceToScreenReader(tableId, message);
		});

		// Announce search results
		dataTable.on('search.dt', function() {
			// Use setTimeout to announce after table redraws
			setTimeout(function() {
				const info = dataTable.page.info();
				const message = info.recordsDisplay + ' results found';
				announceToScreenReader(tableId, message);
			}, 100);
		});
	}

	/**
	 * Announce messages to screen readers via ARIA live region
	 * WCAG 2.2 Level AA - Status Messages (4.1.3)
	 */
	function announceToScreenReader(tableId, message) {
		const $liveRegion = $('#table-status-' + tableId.replace('atables-table-', ''));
		if ($liveRegion.length) {
			// Clear previous message
			$liveRegion.text('');

			// Set new message with slight delay to ensure it's announced
			setTimeout(function() {
				$liveRegion.text(message);
			}, 100);

			// Clear message after announcement
			setTimeout(function() {
				$liveRegion.text('');
			}, 3000);
		}
	}

	/**
	 * Add column visibility toggle
	 * Enhanced with keyboard accessibility
	 */
	function addColumnToggle(dataTable, $table) {
		const $wrapper = $table.closest('.atables-frontend-wrapper');

		// Create column toggle button
		const $toggleBtn = $('<button>', {
			class: 'atables-column-toggle-btn',
			html: '<span class="dashicons dashicons-visibility" aria-hidden="true"></span> Columns',
			type: 'button',
			'aria-label': 'Toggle column visibility',
			'aria-expanded': 'false',
			'aria-haspopup': 'true'
		});

		// Create dropdown
		const $dropdown = $('<div>', {
			class: 'atables-column-dropdown',
			role: 'menu',
			'aria-label': 'Column visibility options',
			css: { display: 'none' }
		});

		// Add checkboxes for each column
		dataTable.columns().every(function(index) {
			const column = this;
			const $header = $(column.header());
			const columnName = $header.text().trim();

			if (columnName) {
				const checkboxId = 'col-toggle-' + $table.attr('id') + '-' + index;
				const $checkbox = $('<label>', {
					class: 'atables-column-checkbox',
					role: 'menuitemcheckbox',
					'aria-checked': column.visible() ? 'true' : 'false',
					tabindex: '0'
				}).append(
					$('<input>', {
						type: 'checkbox',
						checked: column.visible(),
						'data-column': index,
						id: checkboxId,
						'aria-hidden': 'true' // Hide from screen readers, use label's aria-checked
					}),
					$('<span>').text(columnName)
				);

				// Keyboard support for checkboxes
				$checkbox.on('keydown', function(e) {
					if (e.which === 13 || e.which === 32) { // Enter or Space
						e.preventDefault();
						const $input = $(this).find('input');
						$input.prop('checked', !$input.prop('checked')).trigger('change');
					} else if (e.which === 27) { // Escape
						$dropdown.hide();
						$toggleBtn.attr('aria-expanded', 'false');
						$toggleBtn.focus();
					}
				});

				$dropdown.append($checkbox);
			}
		});

		// Toggle dropdown on button click
		$toggleBtn.on('click', function(e) {
			e.stopPropagation();
			const isExpanded = $dropdown.is(':visible');
			$dropdown.toggle();
			$(this).attr('aria-expanded', !isExpanded);

			// Focus first checkbox when opening
			if (!isExpanded) {
				$dropdown.find('.atables-column-checkbox').first().focus();
			}
		});

		// Keyboard support for toggle button
		$toggleBtn.on('keydown', function(e) {
			if (e.which === 13 || e.which === 32) { // Enter or Space
				e.preventDefault();
				$(this).click();
			}
		});

		// Hide dropdown when clicking outside
		$(document).on('click', function(e) {
			if (!$(e.target).closest('.atables-column-toggle').length) {
				$dropdown.hide();
				$toggleBtn.attr('aria-expanded', 'false');
			}
		});

		// Handle checkbox changes
		$dropdown.on('change', 'input[type="checkbox"]', function() {
			const columnIndex = $(this).data('column');
			const column = dataTable.column(columnIndex);
			const isVisible = $(this).is(':checked');

			column.visible(isVisible);

			// Update ARIA
			$(this).closest('.atables-column-checkbox').attr('aria-checked', isVisible ? 'true' : 'false');
		});

		// Add toggle container
		const $toggleContainer = $('<div>', {
			class: 'atables-column-toggle'
		}).append($toggleBtn, $dropdown);

		// Insert before the table
		$wrapper.find('.atables-controls-right').append($toggleContainer);
	}
	
	/**
	 * Add copy to clipboard functionality
	 * Enhanced with proper announcements
	 */
	function copyTableToClipboard(tableId) {
		const $table = $('#' + tableId);
		const dt = $table.DataTable();

		// Get all data (including hidden by pagination)
		const data = dt.rows({ search: 'applied' }).data().toArray();
		const columns = dt.columns().header().toArray().map(th => $(th).text());

		// Build CSV-like string
		let text = columns.join('\t') + '\n';
		data.forEach(function(row) {
			text += row.join('\t') + '\n';
		});

		// Copy to clipboard
		if (navigator.clipboard) {
			navigator.clipboard.writeText(text).then(function() {
				showNotification('Table copied to clipboard!', 'success');
			}).catch(function(err) {
				console.error('Copy failed:', err);
				fallbackCopy(text);
			});
		} else {
			fallbackCopy(text);
		}
	}
	
	/**
	 * Fallback copy method
	 */
	function fallbackCopy(text) {
		const $temp = $('<textarea>').val(text).appendTo('body').select();
		try {
			document.execCommand('copy');
			showNotification('Table copied to clipboard!', 'success');
		} catch (err) {
			showNotification('Failed to copy table', 'error');
		}
		$temp.remove();
	}
	
	/**
	 * Print table
	 * Opens print dialog with clean table view
	 */
	function printTable(tableId) {
		const $table = $('#' + tableId);
		const dt = $table.DataTable();

		// Get table HTML
		const tableHtml = $table.closest('.atables-frontend-wrapper').html();

		// Create print window
		const printWindow = window.open('', '_blank');
		printWindow.document.write(`
			<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title>Print Table</title>
				<style>
					body { font-family: Arial, sans-serif; padding: 20px; }
					table { width: 100%; border-collapse: collapse; }
					th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
					th { background: #f5f5f5; font-weight: bold; }
					.atables-controls-wrapper,
					.atables-controls-bottom,
					.atables-column-toggle,
					.atables-skip-link,
					.atables-toolbar,
					.atables-sr-only,
					button { display: none !important; }
				</style>
			</head>
			<body>
				${tableHtml}
			</body>
			</html>
		`);
		printWindow.document.close();

		// Wait for content to load then print
		printWindow.onload = function() {
			printWindow.print();
		};
	}
	
	/**
	 * Show notification
	 */
	function showNotification(message, type) {
		const $notification = $('<div>', {
			class: 'atables-notification atables-notification-' + type,
			text: message
		}).appendTo('body');
		
		setTimeout(function() {
			$notification.addClass('show');
		}, 10);
		
		setTimeout(function() {
			$notification.removeClass('show');
			setTimeout(function() {
				$notification.remove();
			}, 300);
		}, 3000);
	}
});
