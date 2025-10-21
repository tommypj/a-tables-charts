/**
 * A-Tables & Charts - Table View AJAX
 * Handles AJAX search, sort, and pagination for view table page
 *
 * @package ATablesCharts
 * @since 1.0.0
 */

(function($) {
	'use strict';

	/**
	 * Initialize table view AJAX functionality
	 */
	function initTableViewAjax() {
		// Only run on view table page
		if (!$('.atables-view-page').length) {
			return;
		}

		const tableId = getTableIdFromUrl();
		if (!tableId) {
			return;
		}

		// Initialize all AJAX handlers
		initSearchAjax();
		initSortAjax();
		initPaginationAjax();
		initPerPageAjax();
		
		// Initialize clear button on page load
		const url = new URL(window.location.href);
		const searchTerm = url.searchParams.get('s') || '';
		updateClearButton(searchTerm);
	}

	/**
	 * Initialize AJAX search
	 */
	function initSearchAjax() {
		$('.atables-search-form').on('submit', function(e) {
			e.preventDefault();
			
			const searchTerm = $(this).find('input[name="s"]').val().trim();
			
			// Update URL without reload
			const url = new URL(window.location.href);
			if (searchTerm) {
				url.searchParams.set('s', searchTerm);
			} else {
				url.searchParams.delete('s');
			}
			url.searchParams.set('paged', '1'); // Reset to first page
			
			window.history.pushState({}, '', url);
			
			// Load data via AJAX
			loadTableData();
		});

		// Clear search button - Handle dynamically created button
		$(document).on('click', '.atables-clear-search', function(e) {
			e.preventDefault();
			
			const url = new URL(window.location.href);
			url.searchParams.delete('s');
			url.searchParams.set('paged', '1');
			
			window.history.pushState({}, '', url);
			
			// Clear search input
			$('.atables-search-form input[name="s"]').val('');
			
			// Load data via AJAX
			loadTableData();
		});
	}

	/**
	 * Initialize AJAX sorting
	 */
	function initSortAjax() {
		$(document).on('click', '.atables-sort-link', function(e) {
			e.preventDefault();
			
			const href = $(this).attr('href');
			const url = new URL(href, window.location.origin);
			
			// Update URL without reload
			window.history.pushState({}, '', url);
			
			// Load data via AJAX
			loadTableData();
		});
	}

	/**
	 * Initialize AJAX pagination
	 */
	function initPaginationAjax() {
		// Page number links
		$(document).on('click', '.atables-page-num, .atables-page-btn', function(e) {
			e.preventDefault();
			
			if ($(this).hasClass('disabled') || $(this).hasClass('active')) {
				return;
			}
			
			const href = $(this).attr('href');
			if (!href) return;
			
			const url = new URL(href, window.location.origin);
			
			// Update URL without reload
			window.history.pushState({}, '', url);
			
			// Scroll to table smoothly
			$('html, body').animate({
				scrollTop: $('.atables-data-card').offset().top - 100
			}, 300);
			
			// Load data via AJAX
			loadTableData();
		});
	}

	/**
	 * Initialize per-page selector AJAX
	 */
	function initPerPageAjax() {
		$('#per-page-select').off('change').on('change', function() {
			const perPage = $(this).val();
			
			const url = new URL(window.location.href);
			url.searchParams.set('per_page', perPage);
			url.searchParams.set('paged', '1'); // Reset to first page
			
			window.history.pushState({}, '', url);
			
			// Load data via AJAX
			loadTableData();
		});
	}

	/**
	 * Load table data via AJAX
	 */
	function loadTableData() {
		const tableId = getTableIdFromUrl();
		const url = new URL(window.location.href);
		
		const params = {
			action: 'atables_load_table_data',
			nonce: aTablesAdmin.nonce,
			table_id: tableId,
			per_page: url.searchParams.get('per_page') || 10,
			paged: url.searchParams.get('paged') || 1,
			search: url.searchParams.get('s') || '',
			sort_column: url.searchParams.get('sort') || '',
			sort_order: url.searchParams.get('order') || 'asc'
		};

		// Show loading state
		showLoadingState();

		// Send AJAX request
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: params,
			timeout: 30000, // 30 second timeout
			success: function(response) {
				console.log('AJAX Response:', response); // Debug log
				
				if (response.success && response.data) {
					// Update table content
					updateTableContent(response.data);
					
					// Update pagination
					updatePagination(response.data.pagination);
					
					// Update stats
					updateStats(response.data.pagination);
					
					// Update filter badge
					updateFilterBadge(params.search);
				} else {
					console.error('Invalid response:', response);
					showError('Failed to load table data. Please refresh the page.');
				}
			},
			error: function(xhr, status, error) {
				console.error('AJAX error:', {xhr: xhr, status: status, error: error});
				
				if (status === 'timeout') {
					showError('Request timed out. Please try again.');
				} else {
					showError('An error occurred while loading data. Please refresh the page.');
				}
			},
			complete: function() {
				// Always hide loading state
				hideLoadingState();
			}
		});
	}

	/**
	 * Update table content
	 */
	function updateTableContent(data) {
		const $tbody = $('.atables-modern-table tbody');
		
		if (!data.rows || data.rows.length === 0) {
			const colspan = data.headers ? data.headers.length : 1;
			const message = data.is_search ? 'No results found for your search.' : 'No data available in this table.';
			
			$tbody.html(`
				<tr>
					<td colspan="${colspan}" class="atables-no-data">
						<span class="dashicons dashicons-info"></span>
						${message}
					</td>
				</tr>
			`);
			return;
		}

		let html = '';
		data.rows.forEach(function(row) {
			html += '<tr>';
			row.forEach(function(cell) {
				html += '<td>' + escapeHtml(cell) + '</td>';
			});
			html += '</tr>';
		});

		$tbody.html(html);
	}

	/**
	 * Update pagination controls
	 */
	function updatePagination(pagination) {
		if (!pagination) return;

		const $footer = $('.atables-data-footer');
		
		if (pagination.total_pages <= 1) {
			$('.atables-pagination-controls').remove();
			return;
		}

		// Build pagination HTML
		let html = '<div class="atables-pagination-controls">';
		
		// First/Previous buttons
		if (pagination.current_page > 1) {
			html += `<a href="${getPaginationUrl(1)}" class="atables-page-btn atables-page-first" title="First Page"><span class="dashicons dashicons-controls-skipback"></span></a>`;
			html += `<a href="${getPaginationUrl(pagination.current_page - 1)}" class="atables-page-btn atables-page-prev" title="Previous Page"><span class="dashicons dashicons-arrow-left-alt2"></span></a>`;
		} else {
			html += '<span class="atables-page-btn atables-page-first disabled"><span class="dashicons dashicons-controls-skipback"></span></span>';
			html += '<span class="atables-page-btn atables-page-prev disabled"><span class="dashicons dashicons-arrow-left-alt2"></span></span>';
		}

		// Page numbers
		html += '<div class="atables-page-numbers">';
		
		const range = 2;
		const start = Math.max(1, pagination.current_page - range);
		const end = Math.min(pagination.total_pages, pagination.current_page + range);

		// First page + ellipsis
		if (start > 1) {
			html += `<a href="${getPaginationUrl(1)}" class="atables-page-num">1</a>`;
			if (start > 2) {
				html += '<span class="atables-page-ellipsis">...</span>';
			}
		}

		// Page range
		for (let i = start; i <= end; i++) {
			if (i === pagination.current_page) {
				html += `<span class="atables-page-num active">${i}</span>`;
			} else {
				html += `<a href="${getPaginationUrl(i)}" class="atables-page-num">${i}</a>`;
			}
		}

		// Last page + ellipsis
		if (end < pagination.total_pages) {
			if (end < pagination.total_pages - 1) {
				html += '<span class="atables-page-ellipsis">...</span>';
			}
			html += `<a href="${getPaginationUrl(pagination.total_pages)}" class="atables-page-num">${pagination.total_pages}</a>`;
		}

		html += '</div>';

		// Next/Last buttons
		if (pagination.current_page < pagination.total_pages) {
			html += `<a href="${getPaginationUrl(pagination.current_page + 1)}" class="atables-page-btn atables-page-next" title="Next Page"><span class="dashicons dashicons-arrow-right-alt2"></span></a>`;
			html += `<a href="${getPaginationUrl(pagination.total_pages)}" class="atables-page-btn atables-page-last" title="Last Page"><span class="dashicons dashicons-controls-skipforward"></span></a>`;
		} else {
			html += '<span class="atables-page-btn atables-page-next disabled"><span class="dashicons dashicons-arrow-right-alt2"></span></span>';
			html += '<span class="atables-page-btn atables-page-last disabled"><span class="dashicons dashicons-controls-skipforward"></span></span>';
		}

		html += '</div>';

		// Remove old pagination and add new
		$footer.find('.atables-pagination-controls').remove();
		$footer.append(html);
	}

	/**
	 * Update stats display
	 */
	function updateStats(pagination) {
		if (!pagination) return;

		const $info = $('.atables-pagination-info p');
		
		if (pagination.filtered_total > 0) {
			let text = `Showing <strong>${pagination.start_row}</strong> to <strong>${pagination.end_row}</strong> of <strong>${pagination.filtered_total}</strong> rows`;
			
			if (pagination.filtered_total < pagination.total_rows) {
				text += ` (filtered from <strong>${pagination.total_rows}</strong> total)`;
			}
			
			$info.html(text);
		} else {
			$info.html('No rows to display');
		}
	}

	/**
	 * Update filter badge
	 */
	function updateFilterBadge(searchTerm) {
		$('.atables-filter-badge').remove();
		
		if (searchTerm) {
			const badge = `<span class="atables-filter-badge"><span class="dashicons dashicons-filter"></span>Filtered by: "${escapeHtml(searchTerm)}"</span>`;
			$('.atables-data-title h2').after(badge);
		}
		
		// Update clear button visibility
		updateClearButton(searchTerm);
	}
	
	/**
	 * Update clear button visibility
	 */
	function updateClearButton(searchTerm) {
		// Remove existing clear button
		$('.atables-clear-search').remove();
		
		// Add clear button if there's a search term
		if (searchTerm && searchTerm.trim()) {
			const clearBtn = `<button type="button" class="button atables-clear-search">Clear</button>`;
			$('.atables-search-form button[type="submit"]').after(clearBtn);
		}
	}

	/**
	 * Show loading state
	 */
	function showLoadingState() {
		const $table = $('.atables-modern-table');
		const $tbody = $table.find('tbody');
		
		// Add loading class
		$table.addClass('atables-loading');
		
		// Disable interactions
		$tbody.css({
			'opacity': '0.5',
			'pointer-events': 'none'
		});
		
		// Show loading spinner if not already present
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
	function hideLoadingState() {
		const $table = $('.atables-modern-table');
		const $tbody = $table.find('tbody');
		
		// Remove loading class
		$table.removeClass('atables-loading');
		
		// Re-enable interactions
		$tbody.css({
			'opacity': '1',
			'pointer-events': 'auto'
		});
		
		// Remove loading spinner
		$('.atables-loading-spinner').remove();
	}

	/**
	 * Show error message
	 */
	function showError(message) {
		const $notice = $('<div class="notice notice-error is-dismissible"><p>' + message + '</p></div>');
		$('.atables-page-header').after($notice);
		
		setTimeout(function() {
			$notice.fadeOut(function() {
				$(this).remove();
			});
		}, 5000);
	}

	/**
	 * Get pagination URL
	 */
	function getPaginationUrl(page) {
		const url = new URL(window.location.href);
		url.searchParams.set('paged', page);
		return url.toString();
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

	/**
	 * Handle browser back/forward buttons
	 */
	window.addEventListener('popstate', function() {
		loadTableData();
	});

	// Initialize when document is ready
	$(document).ready(function() {
		initTableViewAjax();
	});

})(jQuery);
