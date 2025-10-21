/**
 * A-Tables & Charts - Table Header Filters
 * Client-side filtering with column headers
 *
 * @package ATablesCharts
 * @since 1.0.0
 */

(function($) {
	'use strict';

	let allTableData = []; // Store all data for client-side filtering
	let filteredData = []; // Currently filtered data
	let currentFilters = {}; // Active filters by column

	/**
	 * Initialize header filters
	 */
	function initHeaderFilters() {
		// Only run on view table page
		if (!$('.atables-view-page').length) {
			return;
		}

		// Load all table data first
		loadAllTableData();
	}

	/**
	 * Load ALL table data (no pagination)
	 */
	function loadAllTableData() {
		const tableId = getTableIdFromUrl();
		if (!tableId) return;

		// Show initial loading
		showGlobalLoading();

		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_load_all_table_data',
				nonce: aTablesAdmin.nonce,
				table_id: tableId
			},
			success: function(response) {
				if (response.success && response.data) {
					allTableData = response.data.rows || [];
					filteredData = [...allTableData];
					
					// Add filter inputs to headers
					addFilterInputs(response.data.headers);
					
					// Initial render
					renderFilteredData();
					
					// Initialize filter listeners
					initFilterListeners();
				}
			},
			error: function() {
				showError('Failed to load table data.');
			},
			complete: function() {
				hideGlobalLoading();
			}
		});
	}

	/**
	* Add filter inputs to table headers
	*/
	function addFilterInputs(headers) {
	const $thead = $('.atables-modern-table thead');
	
	// Check if filter row already exists
	if ($('.atables-filter-row').length) {
	 return;
	}
	
	// Build filter row HTML
	let filterRow = '<tr class="atables-filter-row">';
	headers.forEach(function(header, index) {
	filterRow += `
	<th>
	<input 
	 type="text" 
	  class="atables-column-filter" 
	   data-column="${index}"
	    placeholder="Filter ${header}..."
	    autocomplete="off"
	   />
	  </th>
	  `;
		});
		filterRow += '</tr>';
		
		// Append to thead (creates second row under headers)
		$thead.append(filterRow);
	}

	/**
	 * Initialize filter listeners
	 */
	function initFilterListeners() {
		// Real-time filtering as user types
		$(document).on('input', '.atables-column-filter', function() {
			const $input = $(this);
			const column = $input.data('column');
			const value = $input.val().trim().toLowerCase();
			
			// Update filters
			if (value) {
				currentFilters[column] = value;
			} else {
				delete currentFilters[column];
			}
			
			// Apply filters
			applyFilters();
		});

		// Clear button for each filter
		$(document).on('dblclick', '.atables-column-filter', function() {
			$(this).val('');
			const column = $(this).data('column');
			delete currentFilters[column];
			applyFilters();
		});
	}

	/**
	 * Apply all active filters
	 */
	function applyFilters() {
		// Start with all data
		filteredData = [...allTableData];
		
		// Apply each filter
		Object.keys(currentFilters).forEach(function(columnIndex) {
			const filterValue = currentFilters[columnIndex];
			
			filteredData = filteredData.filter(function(row) {
				const cellValue = (row[columnIndex] || '').toString().toLowerCase();
				return cellValue.includes(filterValue);
			});
		});
		
		// Update display
		renderFilteredData();
		
		// Update stats
		updateFilterStats();
	}

	/**
	 * Render filtered data with client-side pagination
	 */
	function renderFilteredData() {
		const $tbody = $('.atables-modern-table tbody');
		const perPage = 25; // Show 25 rows at a time
		const currentPage = 1; // For now, just show first page
		
		const startIndex = (currentPage - 1) * perPage;
		const endIndex = startIndex + perPage;
		const pageData = filteredData.slice(startIndex, endIndex);
		
		if (pageData.length === 0) {
			const colCount = $('.atables-modern-table thead th').length;
			$tbody.html(`
				<tr>
					<td colspan="${colCount}" class="atables-no-data">
						<span class="dashicons dashicons-info"></span>
						No rows match your filters. Try different values.
					</td>
				</tr>
			`);
			return;
		}
		
		let html = '';
		pageData.forEach(function(row) {
			html += '<tr>';
			row.forEach(function(cell) {
				html += '<td>' + escapeHtml(cell) + '</td>';
			});
			html += '</tr>';
		});
		
		$tbody.html(html);
	}

	/**
	 * Update filter statistics
	 */
	function updateFilterStats() {
		const total = allTableData.length;
		const filtered = filteredData.length;
		const activeFilters = Object.keys(currentFilters).length;
		
		// Remove old badge
		$('.atables-filter-stats').remove();
		
		if (activeFilters > 0) {
			const badge = `
				<div class="atables-filter-stats">
					<span class="dashicons dashicons-filter"></span>
					Showing ${filtered} of ${total} rows (${activeFilters} filter${activeFilters > 1 ? 's' : ''} active)
					<button class="button button-small atables-clear-all-filters">Clear All</button>
				</div>
			`;
			$('.atables-data-title').append(badge);
		}
	}

	/**
	 * Clear all filters
	 */
	$(document).on('click', '.atables-clear-all-filters', function() {
		currentFilters = {};
		$('.atables-column-filter').val('');
		applyFilters();
	});

	/**
	 * Show global loading
	 */
	function showGlobalLoading() {
		const $table = $('.atables-modern-table');
		$table.css('opacity', '0.5');
		
		if (!$('.atables-loading-overlay').length) {
			const overlay = `
				<div class="atables-loading-overlay">
					<span class="dashicons dashicons-update dashicons-spin"></span>
					<span>Loading table data...</span>
				</div>
			`;
			$('.atables-data-card').css('position', 'relative').prepend(overlay);
		}
	}

	/**
	 * Hide global loading
	 */
	function hideGlobalLoading() {
		$('.atables-modern-table').css('opacity', '1');
		$('.atables-loading-overlay').remove();
	}

	/**
	 * Show error
	 */
	function showError(message) {
		const $notice = $('<div class="notice notice-error is-dismissible"><p>' + message + '</p></div>');
		$('.atables-page-header').after($notice);
		setTimeout(function() {
			$notice.fadeOut(function() { $(this).remove(); });
		}, 5000);
	}

	/**
	 * Get table ID from URL
	 */
	function getTableIdFromUrl() {
		const urlParams = new URLSearchParams(window.location.search);
		return urlParams.get('table_id');
	}

	/**
	 * Escape HTML
	 */
	function escapeHtml(text) {
		if (!text) return '';
		const div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}

	// Initialize when document is ready
	$(document).ready(function() {
		initHeaderFilters();
	});

})(jQuery);
