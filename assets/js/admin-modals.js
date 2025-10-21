/**
 * A-Tables & Charts - Modal System
 * Beautiful modal dialogs for confirmations and alerts
 *
 * @package ATablesCharts
 * @since 1.0.0
 */

(function($) {
	'use strict';

	// Modal System
	window.ATablesModal = {
		/**
		* Show a confirmation dialog
		*
		* @param {Object} options - Modal options
		* @return {Promise} Resolves with true if confirmed, false if cancelled
		*/
		confirm: function(options) {
		const defaults = {
		title: 'Confirm Action',
		message: 'Are you sure you want to proceed?',
		type: 'warning', // success, warning, danger, info
		icon: '⚠️',
		confirmText: 'OK',
		cancelText: 'Cancel',
		confirmClass: 'danger',
		 requireConfirmation: false, // Require typing to confirm
			confirmationText: '', // Text user must type to confirm
		 confirmationPlaceholder: 'Type to confirm...'
		};

		const settings = $.extend({}, defaults, options);

		 return new Promise((resolve) => {
			this._createModal(settings, resolve);
		});
	},

		/**
		 * Show an alert dialog
		 *
		 * @param {Object} options - Modal options
		 * @return {Promise} Resolves when OK is clicked
		 */
		alert: function(options) {
			const defaults = {
				title: 'Notice',
				message: '',
				type: 'info',
				icon: 'ℹ️',
				confirmText: 'OK',
				confirmClass: 'primary'
			};

			const settings = $.extend({}, defaults, options);

			return new Promise((resolve) => {
				this._createModal(settings, resolve, true);
			});
		},

		/**
		 * Create and show prompt modal
		 *
		 * @private
		 */
		_createPromptModal: function(settings, resolve) {
			// Create modal HTML with input
			const modalHTML = `
				<div class="atables-modal-overlay">
					<div class="atables-modal">
						<div class="atables-modal-header ${settings.type}">
							<h3>
								<span class="atables-modal-icon">${settings.icon}</span>
								${settings.title}
							</h3>
							<button class="atables-modal-close" data-action="close">×</button>
						</div>
						<div class="atables-modal-body">
							<p>${settings.message}</p>
							<input 
								type="text" 
								class="atables-prompt-input" 
								placeholder="${settings.placeholder}"
								value="${settings.defaultValue}"
								autocomplete="off"
							/>
						</div>
						<div class="atables-modal-footer">
							<button class="atables-modal-btn atables-modal-btn-secondary" data-action="cancel">${settings.cancelText}</button>
							<button class="atables-modal-btn atables-modal-btn-${settings.confirmClass}" data-action="confirm">${settings.confirmText}</button>
						</div>
					</div>
				</div>
			`;

			// Append to body
			const $modal = $(modalHTML).appendTo('body');
			const $input = $modal.find('.atables-prompt-input');
			
			// Focus and select input
			setTimeout(() => {
				$input.focus().select();
			}, 100);

			// Show modal with animation
			setTimeout(() => {
				$modal.addClass('active');
			}, 10);

			// Handle confirm
			$modal.find('[data-action="confirm"]').on('click', () => {
				const value = $input.val();
				this._closeModal($modal, () => {
					resolve(value);
				});
			});

			// Handle cancel
			$modal.find('[data-action="cancel"], [data-action="close"]').on('click', () => {
				this._closeModal($modal, () => {
					resolve(null);
				});
			});

			// Handle Enter key
			$input.on('keypress', (e) => {
				if (e.key === 'Enter') {
					$modal.find('[data-action="confirm"]').click();
				}
			});

			// Handle overlay click
			$modal.on('click', (e) => {
				if ($(e.target).hasClass('atables-modal-overlay')) {
					this._closeModal($modal, () => {
						resolve(null);
					});
				}
			});

			// Handle ESC key
			$(document).on('keydown.atables-modal', (e) => {
				if (e.key === 'Escape') {
					this._closeModal($modal, () => {
						resolve(null);
					});
				}
			});
		},

		/**
		 * Show a success message
		 *
		 * @param {string|Object} messageOrOptions - Success message or options
		 * @return {Promise}
		 */
		success: function(messageOrOptions) {
			const options = typeof messageOrOptions === 'string' 
				? { message: messageOrOptions }
				: messageOrOptions;

			return this.alert({
				title: 'Success!',
				type: 'success',
				icon: '✓',
				confirmClass: 'success',
				...options
			});
		},

		/**
		 * Show an error message
		 *
		 * @param {string|Object} messageOrOptions - Error message or options
		 * @return {Promise}
		 */
		error: function(messageOrOptions) {
			const options = typeof messageOrOptions === 'string' 
				? { message: messageOrOptions }
				: messageOrOptions;

			return this.alert({
				title: 'Error',
				type: 'danger',
				icon: '✕',
				confirmClass: 'danger',
				...options
			});
		},

		/**
		 * Show a prompt dialog
		 *
		 * @param {Object} options - Prompt options
		 * @return {Promise} Resolves with input value if confirmed, null if cancelled
		 */
		prompt: function(options) {
			const defaults = {
				title: 'Input Required',
				message: 'Please enter a value:',
				placeholder: '',
				defaultValue: '',
				type: 'info',
				icon: '✏️',
				confirmText: 'OK',
				cancelText: 'Cancel',
				confirmClass: 'primary'
			};

			const settings = $.extend({}, defaults, options);

			return new Promise((resolve) => {
				this._createPromptModal(settings, resolve);
			});
		},

		/**
		* Create and show modal
		*
		* @private
		*/
		_createModal: function(settings, resolve, alertMode = false) {
		// Create confirmation input HTML if required
		const confirmationInput = settings.requireConfirmation ? `
		<div class="atables-modal-confirmation">
		<p class="atables-confirmation-label">
		Please type <strong>${settings.confirmationText}</strong> to confirm:
		</p>
		<input 
		type="text" 
		class="atables-confirmation-input" 
		placeholder="${settings.confirmationPlaceholder}"
		autocomplete="off"
		spellcheck="false"
		/>
		</div>
		` : '';
		
		// Create modal HTML
		const modalHTML = `
		<div class="atables-modal-overlay">
		 <div class="atables-modal">
		   <div class="atables-modal-header ${settings.type}">
						<h3>
		     <span class="atables-modal-icon">${settings.icon}</span>
		     ${settings.title}
						</h3>
		    <button class="atables-modal-close" data-action="close">×</button>
		   </div>
		  <div class="atables-modal-body">
		    <p>${settings.message}</p>
						${confirmationInput}
		   </div>
		   <div class="atables-modal-footer">
		   ${!alertMode ? `<button class="atables-modal-btn atables-modal-btn-secondary" data-action="cancel">${settings.cancelText}</button>` : ''}
		  <button class="atables-modal-btn atables-modal-btn-${settings.confirmClass}" data-action="confirm" ${settings.requireConfirmation ? 'disabled' : ''}>${settings.confirmText}</button>
		  </div>
		  </div>
			</div>
		`;

		// Append to body
		const $modal = $(modalHTML).appendTo('body');
		
		// Handle confirmation input validation
		if (settings.requireConfirmation) {
		 const $input = $modal.find('.atables-confirmation-input');
		 const $confirmBtn = $modal.find('[data-action="confirm"]');
		
		// Focus input
		setTimeout(() => {
		$input.focus();
		}, 100);
		 
			// Check input on keyup
		 $input.on('input', function() {
		  const inputValue = $(this).val().trim();
		 const isValid = inputValue === settings.confirmationText;
		
		if (isValid) {
		 $confirmBtn.prop('disabled', false).addClass('enabled');
		  $(this).addClass('valid');
		  } else {
		    $confirmBtn.prop('disabled', true).removeClass('enabled');
					$(this).removeClass('valid');
				}
			});
			
			// Submit on Enter if valid
			$input.on('keypress', function(e) {
				if (e.key === 'Enter' && !$confirmBtn.prop('disabled')) {
					$confirmBtn.click();
				}
			});
		}

		// Show modal with animation
		setTimeout(() => {
			$modal.addClass('active');
		}, 10);

		// Handle confirm
		$modal.find('[data-action="confirm"]').on('click', () => {
			// Check if confirmation is required and valid
			if (settings.requireConfirmation) {
				const inputValue = $modal.find('.atables-confirmation-input').val().trim();
				if (inputValue !== settings.confirmationText) {
					return; // Don't close if invalid
				}
			}
			
			this._closeModal($modal, () => {
				resolve(true);
			});
		});

		// Handle cancel
		$modal.find('[data-action="cancel"], [data-action="close"]').on('click', () => {
			this._closeModal($modal, () => {
				resolve(false);
			});
		});

		// Handle overlay click
		$modal.on('click', (e) => {
			if ($(e.target).hasClass('atables-modal-overlay')) {
				this._closeModal($modal, () => {
					resolve(false);
				});
			}
		});

		// Handle ESC key
		$(document).on('keydown.atables-modal', (e) => {
			if (e.key === 'Escape') {
				this._closeModal($modal, () => {
					resolve(false);
				});
			}
		});
	},

		/**
		 * Close modal with animation
		 *
		 * @private
		 */
		_closeModal: function($modal, callback) {
			$modal.removeClass('active');
			
			setTimeout(() => {
				$modal.remove();
				$(document).off('keydown.atables-modal');
				if (callback) callback();
			}, 300);
		}
	};

})(jQuery);
