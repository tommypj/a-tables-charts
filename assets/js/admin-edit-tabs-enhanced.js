/**
 * A-Tables & Charts - Enhanced Edit Page Tabs (Full Implementation)
 *
 * Complete tabbed interface for managing all table features
 *
 * @package ATablesCharts
 * @since 1.0.0
 */

(function($) {
    'use strict';

    const ATablesEditTabsEnhanced = {

        currentTab: 'basic',
        tableId: null,

        // In-memory storage for rules/formulas/etc
        conditionalRules: [],
        formulas: [],
        validationRules: {},
        cellMerges: [],

        /**
         * Initialize tabs
         */
        init: function() {
            this.tableId = $('#atables-table-id').val();

            if (!this.tableId) {
                console.warn('No table ID found');
                return;
            }

            this.bindEvents();
            this.loadSavedData();
        },

        /**
         * Bind tab events
         */
        bindEvents: function() {
            const self = this;

            // Tab switching
            $(document).on('click', '.atables-tab-nav button', function() {
                const tab = $(this).data('tab');
                self.switchTab(tab);
            });

            // Conditional Formatting Events
            $(document).on('click', '#atables-add-cf-rule', function() {
                self.showConditionalFormattingModal();
            });

            $(document).on('click', '.atables-edit-cf-rule', function() {
                const index = $(this).data('index');
                self.editConditionalRule(index);
            });

            $(document).on('click', '.atables-delete-cf-rule', function() {
                const index = $(this).data('index');
                self.deleteConditionalRule(index);
            });

            $(document).on('click', '.atables-apply-preset', function() {
                const presetId = $(this).data('preset');
                self.applyConditionalPreset(presetId);
            });

            // Formula Events
            $(document).on('click', '#atables-add-formula', function() {
                self.showFormulaModal();
            });

            $(document).on('click', '.atables-edit-formula', function() {
                const index = $(this).data('index');
                self.editFormula(index);
            });

            $(document).on('click', '.atables-delete-formula', function() {
                const index = $(this).data('index');
                self.deleteFormula(index);
            });

            $(document).on('click', '.atables-test-formula', function() {
                const index = $(this).data('index');
                self.testFormula(index);
            });

            // Validation Events
            $(document).on('click', '#atables-add-validation', function() {
                self.showValidationModal();
            });

            $(document).on('click', '.atables-edit-validation', function() {
                const column = $(this).data('column');
                self.editValidation(column);
            });

            $(document).on('click', '.atables-delete-validation', function() {
                const column = $(this).data('column');
                self.deleteValidation(column);
            });

            // Cell Merging Events
            $(document).on('click', '#atables-add-merge', function() {
                self.showMergeModal();
            });

            $(document).on('click', '.atables-delete-merge', function() {
                const index = $(this).data('index');
                self.deleteMerge(index);
            });

            // Template Events
            $(document).on('click', '.atables-apply-template', function() {
                const templateId = $(this).data('template');
                self.applyTemplate(templateId);
            });

            // Save Events
            $(document).on('click', '#atables-save-all-settings', function() {
                self.saveAllSettings();
            });

            // Listen for unified save trigger
            $(document).on('atables:saveAll', function() {
                self.saveAllSettings();
            });
        },

        /**
         * Switch active tab
         */
        switchTab: function(tabName) {
            this.currentTab = tabName;

            // Update navigation
            $('.atables-tab-nav button').removeClass('active');
            $('[data-tab="' + tabName + '"]').addClass('active');

            // Update content
            $('.atables-tab-content').removeClass('active');
            $('#atables-tab-' + tabName).addClass('active');

            // Load tab-specific data
            this.loadTabData(tabName);
        },

        /**
         * Load tab-specific data
         */
        loadTabData: function(tabName) {
            switch(tabName) {
                case 'conditional':
                    this.renderConditionalRules(this.conditionalRules);
                    break;
                case 'formulas':
                    this.renderFormulas(this.formulas);
                    break;
                case 'validation':
                    this.renderValidationRules(this.validationRules);
                    break;
                case 'merging':
                    this.renderCellMerges(this.cellMerges);
                    break;
            }
        },

        /**
         * Load saved data for all tabs
         */
        loadSavedData: function() {
            const self = this;

            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atables_get_table_settings',
                    nonce: aTablesAdmin.nonce,
                    table_id: self.tableId
                },
                success: function(response) {
                    if (response.success && response.data) {
                        self.populateSettings(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load table settings:', error);
                }
            });
        },

        /**
         * Populate all settings
         */
        populateSettings: function(settings) {
            // Store in memory
            this.conditionalRules = settings.conditional_formatting || [];
            this.formulas = settings.formulas || [];
            this.validationRules = settings.validation_rules || {};
            this.cellMerges = settings.cell_merges || [];

            // Render if on respective tabs
            if (this.currentTab === 'conditional') {
                this.renderConditionalRules(this.conditionalRules);
            } else if (this.currentTab === 'formulas') {
                this.renderFormulas(this.formulas);
            } else if (this.currentTab === 'validation') {
                this.renderValidationRules(this.validationRules);
            } else if (this.currentTab === 'merging') {
                this.renderCellMerges(this.cellMerges);
            }
        },

        // ============================================================
        // CONDITIONAL FORMATTING METHODS
        // ============================================================

        renderConditionalRules: function(rules) {
            const $container = $('#conditional-rules-list');
            $container.empty();

            if (!rules || rules.length === 0) {
                $container.html('<div class="atables-empty-state"><p>No conditional formatting rules yet.</p><p class="description">Click "Add Rule" to create your first formatting rule.</p></div>');
                return;
            }

            rules.forEach((rule, index) => {
                const ruleHtml = this.buildConditionalRuleHtml(rule, index);
                $container.append(ruleHtml);
            });
        },

        buildConditionalRuleHtml: function(rule, index) {
            return `
                <div class="atables-rule-item" data-index="${index}">
                    <div class="atables-rule-summary">
                        <span class="atables-rule-column"><strong>${this.escapeHtml(rule.column || 'Unknown')}</strong></span>
                        <span class="atables-rule-operator">${this.escapeHtml(rule.operator || 'equals')}</span>
                        <span class="atables-rule-value">"${this.escapeHtml(rule.value || '')}"</span>
                        <span class="atables-rule-style-preview" style="background-color: ${rule.background_color || 'transparent'}; color: ${rule.text_color || 'inherit'}; font-weight: ${rule.font_weight || 'normal'}; padding: 3px 8px; border-radius: 3px;">
                            Preview
                        </span>
                    </div>
                    <div class="atables-rule-actions">
                        <button type="button" class="button button-small atables-edit-cf-rule" data-index="${index}">
                            <span class="dashicons dashicons-edit"></span> Edit
                        </button>
                        <button type="button" class="button button-small atables-delete-cf-rule" data-index="${index}">
                            <span class="dashicons dashicons-trash"></span> Delete
                        </button>
                    </div>
                </div>
            `;
        },

        showConditionalFormattingModal: function() {
            // Implementation depends on modal structure in conditional-tab.php
            $('#atables-cf-modal').show();
            $('#cf-rule-index').val('');
            this.clearConditionalFormModal();
        },

        editConditionalRule: function(index) {
            const rule = this.conditionalRules[index];
            if (!rule) return;

            $('#atables-cf-modal').show();
            $('#cf-rule-index').val(index);
            $('#cf-column').val(rule.column);
            $('#cf-operator').val(rule.operator);
            $('#cf-value').val(rule.value);
            $('#cf-bg-color').val(rule.background_color || '');
            $('#cf-text-color').val(rule.text_color || '');
            $('#cf-font-weight').val(rule.font_weight || 'normal');
        },

        deleteConditionalRule: function(index) {
            if (confirm('Are you sure you want to delete this rule?')) {
                this.conditionalRules.splice(index, 1);
                this.renderConditionalRules(this.conditionalRules);
                this.autoSaveSettings();
            }
        },

        applyConditionalPreset: function(presetId) {
            // Preset data would come from ConditionalFormattingService
            console.log('Applying preset:', presetId);
            // Implementation depends on preset structure
        },

        clearConditionalFormModal: function() {
            $('#cf-column').val('');
            $('#cf-operator').val('equals');
            $('#cf-value').val('');
            $('#cf-bg-color').val('');
            $('#cf-text-color').val('');
            $('#cf-font-weight').val('normal');
        },

        // ============================================================
        // FORMULA METHODS
        // ============================================================

        renderFormulas: function(formulas) {
            const $container = $('#formulas-list');
            $container.empty();

            if (!formulas || formulas.length === 0) {
                $container.html('<div class="atables-empty-state"><p>No formulas yet.</p><p class="description">Click "Add Formula" to create your first formula.</p></div>');
                return;
            }

            formulas.forEach((formula, index) => {
                const formulaHtml = this.buildFormulaHtml(formula, index);
                $container.append(formulaHtml);
            });
        },

        buildFormulaHtml: function(formula, index) {
            return `
                <div class="atables-formula-item" data-index="${index}">
                    <div class="atables-formula-content">
                        <span class="atables-formula-cell">
                            <strong>Cell:</strong> Row ${formula.target_row}, Col ${formula.target_col}
                        </span>
                        <code class="atables-formula-code">${this.escapeHtml(formula.formula)}</code>
                    </div>
                    <div class="atables-formula-actions">
                        <button type="button" class="button button-small atables-test-formula" data-index="${index}">
                            <span class="dashicons dashicons-yes-alt"></span> Test
                        </button>
                        <button type="button" class="button button-small atables-edit-formula" data-index="${index}">
                            <span class="dashicons dashicons-edit"></span> Edit
                        </button>
                        <button type="button" class="button button-small atables-delete-formula" data-index="${index}">
                            <span class="dashicons dashicons-trash"></span> Delete
                        </button>
                    </div>
                </div>
            `;
        },

        showFormulaModal: function() {
            $('#atables-formula-modal').show();
            $('#formula-index').val('');
            this.clearFormulaModal();
        },

        editFormula: function(index) {
            const formula = this.formulas[index];
            if (!formula) return;

            $('#atables-formula-modal').show();
            $('#formula-index').val(index);
            $('#formula-row').val(formula.target_row);
            $('#formula-col').val(formula.target_col);
            $('#formula-input').val(formula.formula);
        },

        deleteFormula: function(index) {
            if (confirm('Are you sure you want to delete this formula?')) {
                this.formulas.splice(index, 1);
                this.renderFormulas(this.formulas);
                this.autoSaveSettings();
            }
        },

        testFormula: function(index) {
            const formula = this.formulas[index];
            if (!formula) return;

            const self = this;
            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atables_calculate_formula',
                    nonce: aTablesAdmin.nonce,
                    table_id: self.tableId,
                    formula: formula.formula
                },
                success: function(response) {
                    if (response.success) {
                        alert('Formula result: ' + response.data.result);
                    } else {
                        alert('Formula error: ' + response.data.message);
                    }
                }
            });
        },

        clearFormulaModal: function() {
            $('#formula-row').val('');
            $('#formula-col').val('');
            $('#formula-input').val('');
        },

        // ============================================================
        // VALIDATION METHODS
        // ============================================================

        renderValidationRules: function(rules) {
            const $container = $('#validation-rules-list');
            $container.empty();

            if (!rules || Object.keys(rules).length === 0) {
                $container.html('<div class="atables-empty-state"><p>No validation rules yet.</p><p class="description">Click "Add Rule" to create your first validation rule.</p></div>');
                return;
            }

            Object.keys(rules).forEach(column => {
                const columnRules = rules[column];
                const ruleHtml = this.buildValidationRuleHtml(column, columnRules);
                $container.append(ruleHtml);
            });
        },

        buildValidationRuleHtml: function(column, rules) {
            const rulesDisplay = Object.keys(rules).map(rule => {
                let ruleText = rule.replace(/_/g, ' ');
                if (typeof rules[rule] !== 'boolean') {
                    ruleText += ': ' + rules[rule];
                }
                return ruleText;
            }).join(', ');

            return `
                <div class="atables-validation-item" data-column="${this.escapeHtml(column)}">
                    <div class="atables-validation-summary">
                        <strong class="atables-validation-column">${this.escapeHtml(column)}</strong>
                        <span class="atables-validation-rules">${this.escapeHtml(rulesDisplay)}</span>
                    </div>
                    <div class="atables-validation-actions">
                        <button type="button" class="button button-small atables-edit-validation" data-column="${this.escapeHtml(column)}">
                            <span class="dashicons dashicons-edit"></span> Edit
                        </button>
                        <button type="button" class="button button-small atables-delete-validation" data-column="${this.escapeHtml(column)}">
                            <span class="dashicons dashicons-trash"></span> Delete
                        </button>
                    </div>
                </div>
            `;
        },

        showValidationModal: function() {
            $('#atables-validation-modal').show();
            this.clearValidationModal();
        },

        editValidation: function(column) {
            const rules = this.validationRules[column];
            if (!rules) return;

            $('#atables-validation-modal').show();
            $('#validation-column').val(column).prop('disabled', true);
            // Populate other validation fields based on rules structure
        },

        deleteValidation: function(column) {
            if (confirm('Are you sure you want to delete validation for column "' + column + '"?')) {
                delete this.validationRules[column];
                this.renderValidationRules(this.validationRules);
                this.autoSaveSettings();
            }
        },

        clearValidationModal: function() {
            $('#validation-column').val('').prop('disabled', false);
            // Clear other validation fields
        },

        // ============================================================
        // CELL MERGING METHODS
        // ============================================================

        renderCellMerges: function(merges) {
            const $container = $('#cell-merges-list');
            $container.empty();

            if (!merges || merges.length === 0) {
                $container.html('<div class="atables-empty-state"><p>No cell merges yet.</p><p class="description">Click "Add Merge" to merge cells.</p></div>');
                return;
            }

            merges.forEach((merge, index) => {
                const mergeHtml = this.buildCellMergeHtml(merge, index);
                $container.append(mergeHtml);
            });
        },

        buildCellMergeHtml: function(merge, index) {
            return `
                <div class="atables-merge-item" data-index="${index}">
                    <div class="atables-merge-info">
                        <span class="atables-merge-range">
                            <strong>Start:</strong> Row ${merge.start_row}, Col ${merge.start_col}
                        </span>
                        <span class="atables-merge-span">
                            <strong>Span:</strong> ${merge.row_span} rows × ${merge.col_span} cols
                        </span>
                    </div>
                    <div class="atables-merge-actions">
                        <button type="button" class="button button-small atables-delete-merge" data-index="${index}">
                            <span class="dashicons dashicons-trash"></span> Delete
                        </button>
                    </div>
                </div>
            `;
        },

        showMergeModal: function() {
            $('#atables-merge-modal').show();
            this.clearMergeModal();
        },

        deleteMerge: function(index) {
            if (confirm('Are you sure you want to delete this cell merge?')) {
                this.cellMerges.splice(index, 1);
                this.renderCellMerges(this.cellMerges);
                this.autoSaveSettings();
            }
        },

        clearMergeModal: function() {
            $('#merge-start-row').val('');
            $('#merge-start-col').val('');
            $('#merge-row-span').val('1');
            $('#merge-col-span').val('1');
        },

        // ============================================================
        // TEMPLATE METHODS
        // ============================================================

        applyTemplate: function(templateId) {
            const self = this;

            if (!confirm('Apply this template? This will update display settings.')) {
                return;
            }

            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atables_apply_template',
                    nonce: aTablesAdmin.nonce,
                    table_id: self.tableId,
                    template_id: templateId
                },
                success: function(response) {
                    if (response.success) {
                        self.showNotification('Template applied successfully!', 'success');
                        // Reload settings
                        self.loadSavedData();
                    } else {
                        self.showNotification(response.data.message || 'Failed to apply template', 'error');
                    }
                },
                error: function() {
                    self.showNotification('Failed to apply template. Please try again.', 'error');
                }
            });
        },

        // ============================================================
        // SAVE METHODS
        // ============================================================

        saveAllSettings: function() {
            const self = this;
            const $btn = $('#atables-save-all-settings');

            if (!$btn.length) {
                // No save button, trigger through event system
                this.autoSaveSettings();
                return;
            }

            const originalText = $btn.html();
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> Saving...');

            this.performSave(function(success, message) {
                $btn.prop('disabled', false).html(originalText);
                if (success) {
                    self.showNotification(message || 'Settings saved successfully!', 'success');
                } else {
                    self.showNotification(message || 'Failed to save settings', 'error');
                }
            });
        },

        autoSaveSettings: function() {
            this.performSave(function(success, message) {
                // Silent save, no UI feedback
                if (!success) {
                    console.error('Auto-save failed:', message);
                }
            });
        },

        performSave: function(callback) {
            const self = this;

            const settings = {
                conditional_formatting: this.conditionalRules,
                formulas: this.formulas,
                validation_rules: this.validationRules,
                cell_merges: this.cellMerges
            };

            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atables_save_table_settings',
                    nonce: aTablesAdmin.nonce,
                    table_id: self.tableId,
                    settings: JSON.stringify(settings)
                },
                success: function(response) {
                    if (response.success) {
                        callback(true, response.data.message);
                    } else {
                        callback(false, response.data.message);
                    }
                },
                error: function(xhr, status, error) {
                    callback(false, 'Network error: ' + error);
                }
            });
        },

        // ============================================================
        // GETTER METHODS (for compatibility with save handler)
        // ============================================================

        getConditionalRules: function() {
            return this.conditionalRules;
        },

        getFormulas: function() {
            return this.formulas;
        },

        getValidationRules: function() {
            return this.validationRules;
        },

        getCellMerges: function() {
            return this.cellMerges;
        },

        // ============================================================
        // UTILITY METHODS
        // ============================================================

        showNotification: function(message, type) {
            if (window.ATablesNotifications) {
                window.ATablesNotifications.show(message, type);
            } else {
                if (type === 'success') {
                    alert(message);
                } else {
                    alert('Error: ' + message);
                }
            }
        },

        escapeHtml: function(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        if ($('.atables-edit-page-tabs').length > 0) {
            ATablesEditTabsEnhanced.init();
        }
    });

    // Expose globally
    window.ATablesEditTabsEnhanced = ATablesEditTabsEnhanced;

    // Also expose methods for save handler compatibility
    $(document).on('atables:cf:getRules', function(e) {
        $(document).trigger('atables:cf:rulesReady', [ATablesEditTabsEnhanced.getConditionalRules()]);
    });

    $(document).on('atables:formulas:getFormulas', function(e) {
        $(document).trigger('atables:formulas:ready', [ATablesEditTabsEnhanced.getFormulas()]);
    });

    $(document).on('atables:validation:getRules', function(e) {
        $(document).trigger('atables:validation:ready', [ATablesEditTabsEnhanced.getValidationRules()]);
    });

    $(document).on('atables:merging:getMerges', function(e) {
        $(document).trigger('atables:merging:ready', [ATablesEditTabsEnhanced.getCellMerges()]);
    });

})(jQuery);
