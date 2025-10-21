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
	
	// Wait a bit for everything to load
	setTimeout(function() {
		initializeTables();
	}, 100);
	
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
	 * Add column visibility toggle
	 */
	function addColumnToggle(dataTable, $table) {
		const $wrapper = $table.closest('.atables-frontend-wrapper');
		
		// Create column toggle button
		const $toggleBtn = $('<button>', {
			class: 'atables-column-toggle-btn',
			html: '<span class="dashicons dashicons-visibility"></span> Columns',
			type: 'button'
		});
		
		// Create dropdown
		const $dropdown = $('<div>', {
			class: 'atables-column-dropdown',
			css: { display: 'none' }
		});
		
		// Add checkboxes for each column
		dataTable.columns().every(function(index) {
			const column = this;
			const $header = $(column.header());
			const columnName = $header.text().trim();
			
			if (columnName) {
				const $checkbox = $('<label>', {
					class: 'atables-column-checkbox'
				}).append(
					$('<input>', {
						type: 'checkbox',
						checked: column.visible(),
						'data-column': index
					}),
					$('<span>').text(columnName)
				);
				
				$dropdown.append($checkbox);
			}
		});
		
		// Toggle dropdown on button click
		$toggleBtn.on('click', function(e) {
			e.stopPropagation();
			$dropdown.toggle();
		});
		
		// Hide dropdown when clicking outside
		$(document).on('click', function(e) {
			if (!$(e.target).closest('.atables-column-toggle').length) {
				$dropdown.hide();
			}
		});
		
		// Handle checkbox changes
		$dropdown.on('change', 'input[type="checkbox"]', function() {
			const columnIndex = $(this).data('column');
			const column = dataTable.column(columnIndex);
			column.visible($(this).is(':checked'));
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
	 */
	window.copyTableToClipboard = function(tableId) {
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
	};
	
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
	 */
	window.printTable = function(tableId) {
		const $table = $('#' + tableId);
		const dt = $table.DataTable();
		
		// Get table HTML
		const tableHtml = $table.closest('.atables-frontend-wrapper').html();
		
		// Create print window
		const printWindow = window.open('', '_blank');
		printWindow.document.write(`
			<!DOCTYPE html>
			<html>
			<head>
				<title>Print Table</title>
				<style>
					body { font-family: Arial, sans-serif; padding: 20px; }
					table { width: 100%; border-collapse: collapse; }
					th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
					th { background: #f5f5f5; font-weight: bold; }
					.atables-controls-wrapper,
					.atables-controls-bottom,
					.atables-column-toggle,
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
	};
	
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
