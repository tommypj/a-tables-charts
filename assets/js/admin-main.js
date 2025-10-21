/**
 * A-Tables & Charts - Admin JavaScript
 *
 * Handles file upload, data import, and table creation wizard.
 *
 * @package ATablesCharts
 * @since 1.0.0
 */

(function($) {
	'use strict';

	// Wizard state
	const wizard = {
		currentStep: 1,
		selectedSource: null,
		uploadedFile: null,
		importedData: null
	};

	/**
	 * Initialize the plugin
	 */
	function init() {
		// Step 1: Data source selection
		initDataSourceSelection();
		
		// Step 2: File upload
		initFileUpload();
		
		// Step 3: Preview and save
		initPreview();
		
		// Navigation
		initNavigation();
	}

	/**
	 * Initialize data source selection
	 */
	function initDataSourceSelection() {
		$('.atables-source-card').on('click', function() {
			const $card = $(this);
			
			// Check if disabled
			if ($card.hasClass('disabled')) {
				showNotice('error', 'This data source is not yet available.');
				return;
			}
			
			// Select card
			$('.atables-source-card').removeClass('selected');
			$card.addClass('selected');
			
			// Store selection
			wizard.selectedSource = $card.data('source');
			
			// Enable continue button
			$('.atables-step-1 .atables-btn-continue').prop('disabled', false);
		});
		
		// Continue to next step
		$('.atables-step-1 .atables-btn-continue').on('click', function() {
			if (wizard.selectedSource) {
				goToStep(2);
				updateStepDescription();
			}
		});
	}

	/**
	 * Initialize file upload
	 */
	function initFileUpload() {
		const $dropZone = $('#atables-drop-zone');
		const $fileInput = $('#atables-file-input');
		const $browseBtn = $('#atables-browse-btn');
		
		// Browse button
		$browseBtn.on('click', function() {
			$fileInput.click();
		});
		
		// Click drop zone
		$dropZone.on('click', function(e) {
			if (e.target === this || $(e.target).hasClass('atables-upload-icon') || $(e.target).is('h3, p')) {
				$fileInput.click();
			}
		});
		
		// File input change
		$fileInput.on('change', function() {
			const file = this.files[0];
			if (file) {
				handleFileSelect(file);
			}
		});
		
		// Drag and drop
		$dropZone.on('dragover', function(e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).addClass('drag-over');
		});
		
		$dropZone.on('dragleave', function(e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).removeClass('drag-over');
		});
		
		$dropZone.on('drop', function(e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).removeClass('drag-over');
			
			const file = e.originalEvent.dataTransfer.files[0];
			if (file) {
				handleFileSelect(file);
			}
		});
		
		// Remove file
		$('.atables-file-remove').on('click', function() {
			clearFileSelection();
		});
		
		// Import button
		$('.atables-step-2 .atables-btn-import').on('click', function() {
			importFile();
		});
	}

	/**
	 * Handle file selection
	 */
	function handleFileSelect(file) {
		// Validate file type
		const validTypes = getValidFileTypes();
		const fileExt = file.name.split('.').pop().toLowerCase();
		
		if (!validTypes.includes(fileExt)) {
			showNotice('error', 'Invalid file type. Please upload a ' + validTypes.join(', ') + ' file.');
			return;
		}
		
		// Validate file size (10MB)
		const maxSize = 10 * 1024 * 1024;
		if (file.size > maxSize) {
			showNotice('error', 'File is too large. Maximum size is 10MB.');
			return;
		}
		
		// Store file
		wizard.uploadedFile = file;
		
		// Show file info
		$('.atables-file-name').text(file.name);
		$('.atables-file-size').text(formatFileSize(file.size));
		$('.atables-upload-area').hide();
		$('.atables-file-info').fadeIn();
		$('.atables-import-options').fadeIn();
		
		// Show appropriate options
		showImportOptions(fileExt);
		
		// Enable import button
		$('.atables-step-2 .atables-btn-import').prop('disabled', false);
	}

	/**
	 * Clear file selection
	 */
	function clearFileSelection() {
		wizard.uploadedFile = null;
		$('#atables-file-input').val('');
		$('.atables-file-info').hide();
		$('.atables-import-options').hide();
		$('.atables-upload-area').fadeIn();
		$('.atables-step-2 .atables-btn-import').prop('disabled', true);
	}

	/**
	 * Show import options based on file type
	 */
	function showImportOptions(fileType) {
		$('.atables-csv-options, .atables-json-options, .atables-excel-options, .atables-xml-options').hide();
		
		if (fileType === 'csv' || fileType === 'txt') {
			$('.atables-csv-options').show();
		} else if (fileType === 'json') {
			$('.atables-json-options').show();
		} else if (fileType === 'xlsx' || fileType === 'xls') {
			$('.atables-excel-options').show();
		} else if (fileType === 'xml') {
			$('.atables-xml-options').show();
		}
	}

	/**
	 * Import file via AJAX
	 */
	function importFile() {
		if (!wizard.uploadedFile) {
			return;
		}
		
		// Debug: Log file info
		console.log('File to upload:', {
			name: wizard.uploadedFile.name,
			size: wizard.uploadedFile.size,
			type: wizard.uploadedFile.type
		});
		
		// Show progress
		$('.atables-progress').show();
		$('.atables-progress-text').text('Importing...');
		$('.atables-progress-fill').css('width', '30%');
		
		// Disable button
		$('.atables-step-2 .atables-btn-import').prop('disabled', true);
		
		// Determine file type and action
		const fileExt = wizard.uploadedFile.name.split('.').pop().toLowerCase();
		const isExcel = (fileExt === 'xlsx' || fileExt === 'xls');
		const isXml = (fileExt === 'xml');
		
		let action, nonce;
		if (isExcel) {
			action = 'atables_preview_excel';
			nonce = aTablesAdmin.importNonce;
		} else if (isXml) {
			action = 'atables_preview_xml';
			nonce = aTablesAdmin.importNonce;
		} else {
			action = 'atables_preview_import';
			nonce = aTablesAdmin.nonce;
		}
		
		// Prepare form data
		const formData = new FormData();
		formData.append('action', action);
		formData.append('nonce', nonce);
		formData.append('file', wizard.uploadedFile);
		
		// Add options
		const options = getImportOptions();
		for (const key in options) {
			formData.append(key, options[key]);
		}
		
		// Debug: Log FormData entries
		console.log('FormData contents:');
		for (let pair of formData.entries()) {
			if (pair[1] instanceof File) {
				console.log(pair[0] + ':', 'File(' + pair[1].name + ', ' + pair[1].size + ' bytes)');
			} else {
				console.log(pair[0] + ':', pair[1]);
			}
		}
		
		// Upload via AJAX
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			xhr: function() {
				const xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener('progress', function(e) {
					if (e.lengthComputable) {
						const percentComplete = (e.loaded / e.total) * 100;
						$('.atables-progress-fill').css('width', percentComplete + '%');
					}
				}, false);
				return xhr;
			},
			success: function(response) {
				$('.atables-progress-fill').css('width', '100%');
				
				// Debug logging
				console.log('AJAX Response:', response);
				
				if (response.success) {
					// Handle Excel and XML imports differently
					if (isExcel || isXml) {
						// Store temp file info
						wizard.tempFile = response.data.temp_file;
						// Transform response to match expected format
						wizard.importedData = {
							headers: response.data.headers,
							data: response.data.data,
							row_count: response.data.row_count,
							column_count: response.data.column_count
						};
					} else {
						wizard.importedData = response.data;
					}
					
					showNotice('success', response.message || 'File imported successfully!');
					
					// Move to preview step
					setTimeout(function() {
						goToStep(3);
						renderPreview();
					}, 500);
				} else {
					// Handle error - check both message and data
					const errorMsg = response.message || response.data || 'Import failed. Please try again.';
					console.error('Import failed:', errorMsg, response);
					showNotice('error', errorMsg);
					$('.atables-step-2 .atables-btn-import').prop('disabled', false);
				}
			},
			error: function(xhr, status, error) {
				// Enhanced error logging
				console.error('AJAX Error:', {
					status: xhr.status,
					statusText: xhr.statusText,
					responseText: xhr.responseText,
					error: error
				});
				
				// Try to parse error response
				let errorMsg = 'Upload failed: ' + error;
				try {
					const response = JSON.parse(xhr.responseText);
					if (response.message) {
						errorMsg = response.message;
					}
				} catch (e) {
					// Response is not JSON
					if (xhr.responseText) {
						errorMsg = xhr.responseText.substring(0, 200);
					}
				}
				
				showNotice('error', errorMsg);
				$('.atables-step-2 .atables-btn-import').prop('disabled', false);
			},
			complete: function() {
				setTimeout(function() {
					$('.atables-progress').fadeOut();
					$('.atables-progress-fill').css('width', '0');
				}, 1000);
			}
		});
	}

	/**
	 * Get import options from form
	 */
	function getImportOptions() {
		const options = {};
		const fileExt = wizard.uploadedFile.name.split('.').pop().toLowerCase();
		
		if (fileExt === 'csv' || fileExt === 'txt') {
			options.has_header = $('#atables-has-header').is(':checked');
			
			const delimiter = $('#atables-delimiter').val();
			if (delimiter !== 'auto') {
				options.delimiter = delimiter;
			}
			
			options.encoding = $('#atables-encoding').val();
		} else if (fileExt === 'json') {
			options.flatten_nested = $('#atables-flatten-nested').is(':checked');
			
			const arrayKey = $('#atables-array-key').val().trim();
			if (arrayKey) {
				options.array_key = arrayKey;
			}
		} else if (fileExt === 'xlsx' || fileExt === 'xls') {
			options.has_headers = $('#atables-excel-has-header').is(':checked');
			options.sheet_index = parseInt($('#atables-sheet-select').val()) || 0;
		} else if (fileExt === 'xml') {
			options.flatten_nested = $('#atables-xml-flatten').is(':checked');
		}
		
		return options;
	}

	/**
	 * Initialize preview section
	 */
	function initPreview() {
		$('.atables-step-3 .atables-btn-save').on('click', function() {
			saveTable();
		});
	}

	/**
	 * Render data preview
	 */
	function renderPreview() {
		if (!wizard.importedData) {
			return;
		}
		
		const data = wizard.importedData;
		
		// Update stats
		$('#atables-row-count').text(data.row_count);
		$('#atables-column-count').text(data.column_count);
		
		// Set default table name
		const fileName = wizard.uploadedFile.name.replace(/\.[^/.]+$/, '');
		$('#atables-table-title').val(fileName);
		
		// Render preview table
		let html = '<table>';
		
		// Headers
		html += '<thead><tr>';
		data.headers.forEach(function(header) {
			html += '<th>' + escapeHtml(header) + '</th>';
		});
		html += '</tr></thead>';
		
		// Data rows (max 10)
		html += '<tbody>';
		const maxRows = Math.min(data.data.length, 10);
		for (let i = 0; i < maxRows; i++) {
			html += '<tr>';
			data.data[i].forEach(function(cell) {
				html += '<td>' + escapeHtml(cell) + '</td>';
			});
			html += '</tr>';
		}
		html += '</tbody>';
		
		html += '</table>';
		
		$('#atables-data-preview').html(html);
	}

	/**
	 * Save table to database
	 */
	function saveTable() {
		const tableName = $('#atables-table-title').val().trim();
		
		if (!tableName) {
			showNotice('error', 'Please enter a table name.');
			return;
		}
		
		if (!wizard.importedData) {
			showNotice('error', 'No data to save.');
			return;
		}
		
		// Disable button
		$('.atables-step-3 .atables-btn-save').prop('disabled', true).text('Saving...');
		
		// Check if Excel or XML import
		const fileExt = wizard.uploadedFile.name.split('.').pop().toLowerCase();
		const isExcel = (fileExt === 'xlsx' || fileExt === 'xls');
		const isXml = (fileExt === 'xml');
		
		if ((isExcel || isXml) && wizard.tempFile) {
			// Excel or XML import - use different endpoint
			const action = isExcel ? 'atables_import_excel' : 'atables_import_xml';
			
			const postData = {
				action: action,
				nonce: aTablesAdmin.importNonce,
				title: tableName,
				temp_file: wizard.tempFile
			};
			
			// Add format-specific options
			if (isExcel) {
				postData.has_headers = $('#atables-excel-has-header').is(':checked');
				postData.sheet_index = parseInt($('#atables-sheet-select').val()) || 0;
			}
			
			$.ajax({
				url: aTablesAdmin.ajaxUrl,
				type: 'POST',
				data: postData,
				success: function(response) {
					if (response.success) {
						showNotice('success', response.data.message || 'Table saved successfully!');
						
						// Redirect after short delay
						setTimeout(async function() {
						const viewAllTables = await ATablesModal.confirm({
						title: 'Table Created Successfully!',
						 message: 'Your table has been created. Would you like to view all tables now?',
						type: 'success',
						icon: '✅',
						 confirmText: 'View All Tables',
						  cancelText: 'Create Another Table'
						});
						
						if (viewAllTables) {
							window.location.href = 'admin.php?page=a-tables-charts';
						} else {
							// Reset wizard for new table
							resetWizard();
						}
					}, 1000);
					} else {
						const errorMsg = response.data.message || response.data || 'Failed to save table.';
						showNotice('error', errorMsg);
						$('.atables-step-3 .atables-btn-save').prop('disabled', false).text('Save Table');
					}
				},
				error: function(xhr, status, error) {
					console.error('Save error:', xhr, status, error);
					let errorMsg = 'Failed to save table: ' + error;
					try {
						const response = JSON.parse(xhr.responseText);
						if (response.message || response.data) {
							errorMsg = response.message || response.data;
						}
					} catch (e) {}
					showNotice('error', errorMsg);
					$('.atables-step-3 .atables-btn-save').prop('disabled', false).text('Save Table');
				}
			});
		} else {
			// CSV/JSON import - existing code
			const postData = {
				action: 'atables_save_table',
				nonce: aTablesAdmin.nonce,
				title: tableName,
				source_type: wizard.selectedSource,
				import_data: JSON.stringify(wizard.importedData)
			};
			
			// Debug logging
			console.log('Saving table with data:', {
				title: tableName,
				source_type: wizard.selectedSource,
				import_data_size: postData.import_data.length,
				import_data_structure: wizard.importedData
			});
			
			// Save via AJAX
			$.ajax({
				url: aTablesAdmin.ajaxUrl,
				type: 'POST',
				data: postData,
				success: function(response) {
					if (response.success) {
						showNotice('success', response.message || 'Table saved successfully!');
						
						// Redirect after short delay
						setTimeout(async function() {
							const viewAllTables = await ATablesModal.confirm({
								title: 'Table Created Successfully!',
								message: 'Your table has been created. Would you like to view all tables now?',
								type: 'success',
								icon: '✅',
								confirmText: 'View All Tables',
								cancelText: 'Create Another Table'
							});
							
							if (viewAllTables) {
								window.location.href = 'admin.php?page=a-tables-charts';
							} else {
								// Reset wizard for new table
								resetWizard();
							}
						}, 1000);
					} else {
						const errorMsg = response.message || 'Failed to save table. Please try again.';
						showNotice('error', errorMsg);
						$('.atables-step-3 .atables-btn-save').prop('disabled', false).text('Save Table');
					}
				},
				error: function(xhr, status, error) {
					console.error('Save error:', xhr, status, error);
					console.error('Response text:', xhr.responseText);
					let errorMsg = 'Failed to save table: ' + error;
					
					// Try to parse error response
					try {
						const response = JSON.parse(xhr.responseText);
						console.error('Parsed error response:', response);
						if (response.message) {
							errorMsg = response.message;
						}
					} catch (e) {
						// Response is not JSON
						console.error('Could not parse error response');
					}
					
					showNotice('error', errorMsg);
					$('.atables-step-3 .atables-btn-save').prop('disabled', false).text('Save Table');
				}
			});
		}
	}

	/**
	 * Navigation functions
	 */
	function initNavigation() {
		$('.atables-btn-back').on('click', function() {
			if (wizard.currentStep > 1) {
				goToStep(wizard.currentStep - 1);
			}
		});
	}

	function goToStep(step) {
		$('.atables-step').removeClass('active');
		$('.atables-step-' + step).addClass('active');
		wizard.currentStep = step;
		
		// Scroll to top
		$('html, body').animate({ scrollTop: 0 }, 300);
	}

	/**
	 * Update step description
	 */
	function updateStepDescription() {
		const descriptions = {
			csv: 'Upload your CSV file',
			json: 'Upload your JSON file',
			excel: 'Upload your Excel file',
			xml: 'Upload your XML file',
			manual: 'Enter your table data'
		};
		
		$('.atables-step-2 .atables-step-description').text(descriptions[wizard.selectedSource] || '');
	}

	/**
	 * Helper functions
	 */
	function getValidFileTypes() {
		const types = {
			csv: ['csv', 'txt'],
			json: ['json'],
			excel: ['xlsx', 'xls'],
			xml: ['xml']
		};
		
		return types[wizard.selectedSource] || [];
	}

	function formatFileSize(bytes) {
		if (bytes === 0) return '0 Bytes';
		const k = 1024;
		const sizes = ['Bytes', 'KB', 'MB', 'GB'];
		const i = Math.floor(Math.log(bytes) / Math.log(k));
		return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
	}

	function escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}

	function showNotice(type, message) {
		if (typeof ATablesToast !== 'undefined') {
			if (type === 'success') {
				ATablesToast.success(message);
			} else if (type === 'error') {
				ATablesToast.error(message);
			} else if (type === 'warning') {
				ATablesToast.warning(message);
			} else {
				ATablesToast.info(message);
			}
		} else {
			// Fallback to console
			console.log(type.toUpperCase() + ': ' + message);
		}
	}

	function resetWizard() {
		wizard.currentStep = 1;
		wizard.selectedSource = null;
		wizard.uploadedFile = null;
		wizard.importedData = null;
		
		$('.atables-source-card').removeClass('selected');
		clearFileSelection();
		goToStep(1);
	}

	// Initialize when document is ready
	$(document).ready(function() {
		if ($('.atables-create-table').length) {
			init();
		}
		
		// Initialize export functionality
		if ($('.atables-view-page').length) {
			initExport();
		}
		
		// Initialize dashboard functionality
		if ($('.atables-dashboard').length) {
			initDashboard();
		}
	});
	
	/**
	 * Initialize dashboard functionality
	 */
	function initDashboard() {
		// Copy shortcode button
		$('.atables-copy-shortcode').on('click', function() {
			const tableId = $(this).data('table-id');
			const shortcode = '[atable id="' + tableId + '"]';
			
			// Copy to clipboard
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(shortcode).then(function() {
					showNotice('success', 'Shortcode copied: ' + shortcode);
				}).catch(function() {
					fallbackCopyTextToClipboard(shortcode);
				});
			} else {
				fallbackCopyTextToClipboard(shortcode);
			}
		});
		
		// Duplicate table button
		$('.atables-duplicate-btn').on('click', async function() {
			const $btn = $(this);
			const tableId = $btn.data('table-id');
			const tableTitle = $btn.data('table-title');
			
			// Show modal to get new title
			const result = await ATablesModal.prompt({
				title: 'Duplicate Table',
				message: 'Enter a name for the duplicated table:',
				placeholder: tableTitle + ' (Copy)',
				defaultValue: tableTitle + ' (Copy)',
				icon: '📋',
				confirmText: 'Duplicate',
				cancelText: 'Cancel'
			});
			
			if (!result) {
				return; // User cancelled
			}
			
			const newTitle = result.trim();
			if (!newTitle) {
				await ATablesModal.alert({
					title: 'Invalid Name',
					message: 'Please enter a valid table name.',
					type: 'warning',
					icon: '⚠️'
				});
				return;
			}
			
			// Disable button
			const originalText = $btn.html();
			$btn.prop('disabled', true).html('Duplicating...');
			
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
						// Reload page after short delay
						setTimeout(function() {
							location.reload();
						}, 1000);
					} else {
						await ATablesModal.error(response.data || 'Failed to duplicate table.');
						$btn.prop('disabled', false).html(originalText);
					}
				},
				error: async function(xhr, status, error) {
					console.error('Duplicate error:', error);
					await ATablesModal.error('Failed to duplicate table: ' + error);
					$btn.prop('disabled', false).html(originalText);
				}
			});
		});
		
		// Select all checkboxes
		$('#atables-select-all').on('change', function() {
			$('.atables-table-checkbox').prop('checked', $(this).prop('checked'));
		});
		
		// Apply bulk actions
		$('#atables-apply-bulk').on('click', function() {
			const action = $('#atables-bulk-action').val();
			const selectedIds = $('.atables-table-checkbox:checked').map(function() {
				return $(this).val();
			}).get();
			
			if (!action) {
				showNotice('error', 'Please select an action.');
				return;
			}
			
			if (selectedIds.length === 0) {
				showNotice('error', 'Please select at least one table.');
				return;
			}
			
			if (action === 'delete') {
				const confirmMsg = 'Are you sure you want to delete ' + selectedIds.length + ' table(s)? This action cannot be undone.';
				if (!confirm(confirmMsg)) {
					return;
				}
				
				// Disable button
				const $btn = $('#atables-apply-bulk');
				const originalText = $btn.html();
				$btn.prop('disabled', true).html('Deleting...');
				
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
							
							if (completed === selectedIds.length) {
								if (errors === 0) {
									showNotice('success', selectedIds.length + ' table(s) deleted successfully!');
								} else {
									showNotice('error', errors + ' table(s) failed to delete.');
								}
								setTimeout(function() {
									location.reload();
								}, 1000);
							}
						},
						error: function() {
							completed++;
							errors++;
							
							if (completed === selectedIds.length) {
								showNotice('error', errors + ' table(s) failed to delete.');
								$btn.prop('disabled', false).html(originalText);
							}
						}
					});
				});
			}
		});
	}
	
	/**
	 * Fallback copy to clipboard for older browsers
	 */
	function fallbackCopyTextToClipboard(text) {
		const textArea = document.createElement('textarea');
		textArea.value = text;
		textArea.style.position = 'fixed';
		textArea.style.top = '0';
		textArea.style.left = '0';
		textArea.style.opacity = '0';
		document.body.appendChild(textArea);
		textArea.focus();
		textArea.select();
		
		try {
			const successful = document.execCommand('copy');
			if (successful) {
				showNotice('success', 'Shortcode copied: ' + text);
			} else {
				prompt('Copy this shortcode:', text);
			}
		} catch (err) {
			prompt('Copy this shortcode:', text);
		}
		
		document.body.removeChild(textArea);
	}

	/**
	 * Initialize export functionality
	 */
	function initExport() {
		$('.atables-export-btn').on('click', function(e) {
			e.preventDefault();
			
			const $btn = $(this);
			const format = $btn.data('format') || 'csv'; // default to csv
			const tableId = getTableIdFromUrl();
			
			if (!tableId) {
				showNotice('error', 'Table ID not found.');
				return;
			}
			
			// Get current filters from URL
			const urlParams = new URLSearchParams(window.location.search);
			const search = urlParams.get('s') || '';
			const sortColumn = urlParams.get('sort') || '';
			const sortOrder = urlParams.get('order') || 'asc';
			
			// Show feedback
			const originalText = $btn.html();
			$btn.html('<span class="dashicons dashicons-update dashicons-spin"></span> Exporting...').prop('disabled', true);
			
			// Create hidden form and submit
			const $form = $('<form>', {
				method: 'POST',
				action: aTablesAdmin.ajaxUrl,
				target: '_blank'
			});
			
			// Add form fields
			$form.append($('<input>', { type: 'hidden', name: 'action', value: 'atables_export_table' }));
			$form.append($('<input>', { type: 'hidden', name: 'table_id', value: tableId }));
			$form.append($('<input>', { type: 'hidden', name: 'format', value: format }));
			$form.append($('<input>', { type: 'hidden', name: 'nonce', value: aTablesAdmin.exportNonce }));
			
			if (search) {
				$form.append($('<input>', { type: 'hidden', name: 'search', value: search }));
			}
			
			if (sortColumn) {
				$form.append($('<input>', { type: 'hidden', name: 'sort_column', value: sortColumn }));
				$form.append($('<input>', { type: 'hidden', name: 'sort_order', value: sortOrder }));
			}
			
			// Append to body and submit
			$('body').append($form);
			$form.submit();
			
			// Clean up and restore button
			setTimeout(function() {
				$form.remove();
				$btn.html(originalText).prop('disabled', false);
				const formatName = format === 'excel' ? 'Excel' : 'CSV';
				showNotice('success', formatName + ' export started! Your download should begin shortly.');
			}, 1000);
		});
	}
	
	/**
	 * Get table ID from URL
	 */
	function getTableIdFromUrl() {
		const urlParams = new URLSearchParams(window.location.search);
		return urlParams.get('table_id');
	}
	
	/**
	 * Build export URL with parameters
	 */
	function buildExportUrl(tableId, filters) {
		const params = new URLSearchParams({
			action: 'atables_export_table',
			table_id: tableId,
			nonce: aTablesAdmin.nonce
		});
		
		if (filters.search) {
			params.append('search', filters.search);
		}
		
		if (filters.sort_column) {
			params.append('sort_column', filters.sort_column);
			params.append('sort_order', filters.sort_order || 'asc');
		}
		
		return aTablesAdmin.ajaxUrl + '?' + params.toString();
	}

})(jQuery);
