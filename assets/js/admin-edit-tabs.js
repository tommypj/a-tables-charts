/**
 * A-Tables & Charts - Enhanced Edit Page Tabs
 * 
 * Tabbed interface for managing table features
 * 
 * @package ATablesCharts
 * @since 1.0.0
 */

(function($) {
    'use strict';

    const ATablesEditTabs = {
        
        currentTab: 'basic',
        tableId: null,
        
        /**
         * Initialize tabs
         */
        init: function() {
            this.tableId = $('#atables-table-id').val();
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
            
            // Save all settings
            $(document).on('click', '#atables-save-all-settings', function() {
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
                    this.loadConditionalFormatting();
                    break;
                case 'formulas':
                    this.loadFormulas();
                    break;
                case 'validation':
                    this.loadValidation();
                    break;
                case 'merging':
                    this.loadCellMerging();
                    break;
                case 'advanced':
                    this.loadAdvancedSettings();
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
                }
            });
        },
        
        /**
         * Populate all settings
         */
        populateSettings: function(settings) {
            // Display settings already handled by PHP
            
            // Conditional formatting
            if (settings.conditional_formatting) {
                this.renderConditionalRules(settings.conditional_formatting);
            }
            
            // Formulas
            if (settings.formulas) {
                this.renderFormulas(settings.formulas);
            }
            
            // Validation rules
            if (settings.validation_rules) {
                this.renderValidationRules(settings.validation_rules);
            }
            
            // Cell merges
            if (settings.cell_merges) {
                this.renderCellMerges(settings.cell_merges);
            }
        },
        
        /**
         * Load conditional formatting
         */
        loadConditionalFormatting: function() {
            // Already loaded from populateSettings
        },
        
        /**
         * Render conditional formatting rules
         */
        renderConditionalRules: function(rules) {
            const $container = $('#conditional-rules-list');
            $container.empty();
            
            if (!rules || rules.length === 0) {
                $container.html('<p class="atables-empty-state">No conditional formatting rules yet. Click "Add Rule" to get started.</p>');
                return;
            }
            
            rules.forEach((rule, index) => {
                const ruleHtml = this.buildConditionalRuleHtml(rule, index);
                $container.append(ruleHtml);
            });
        },
        
        /**
         * Build conditional rule HTML
         */
        buildConditionalRuleHtml: function(rule, index) {
            return `
                <div class="atables-rule-item" data-index="${index}">
                    <div class="atables-rule-summary">
                        <span class="atables-rule-column">${rule.column || 'Unknown Column'}</span>
                        <span class="atables-rule-operator">${rule.operator || 'equals'}</span>
                        <span class="atables-rule-value">${rule.value || ''}</span>
                        <span class="atables-rule-style-preview" style="background-color: ${rule.background_color || 'transparent'}; color: ${rule.text_color || 'inherit'};">
                            Preview
                        </span>
                    </div>
                    <div class="atables-rule-actions">
                        <button type="button" class="button button-small atables-edit-rule" data-index="${index}">
                            <span class="dashicons dashicons-edit"></span> Edit
                        </button>
                        <button type="button" class="button button-small atables-delete-rule" data-index="${index}">
                            <span class="dashicons dashicons-trash"></span> Delete
                        </button>
                    </div>
                </div>
            `;
        },
        
        /**
         * Load formulas
         */
        loadFormulas: function() {
            // Already loaded from populateSettings
        },
        
        /**
         * Render formulas
         */
        renderFormulas: function(formulas) {
            const $container = $('#formulas-list');
            $container.empty();
            
            if (!formulas || formulas.length === 0) {
                $container.html('<p class="atables-empty-state">No formulas yet. Click "Add Formula" to get started.</p>');
                return;
            }
            
            formulas.forEach((formula, index) => {
                const formulaHtml = this.buildFormulaHtml(formula, index);
                $container.append(formulaHtml);
            });
        },
        
        /**
         * Build formula HTML
         */
        buildFormulaHtml: function(formula, index) {
            return `
                <div class="atables-formula-item" data-index="${index}">
                    <div class="atables-formula-content">
                        <span class="atables-formula-cell">
                            Row ${formula.target_row}, Col: ${formula.target_col}
                        </span>
                        <code class="atables-formula-code">${formula.formula}</code>
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
        
        /**
         * Load validation
         */
        loadValidation: function() {
            // Already loaded from populateSettings
        },
        
        /**
         * Render validation rules
         */
        renderValidationRules: function(rules) {
            const $container = $('#validation-rules-list');
            $container.empty();
            
            if (!rules || Object.keys(rules).length === 0) {
                $container.html('<p class="atables-empty-state">No validation rules yet. Click "Add Rule" to get started.</p>');
                return;
            }
            
            Object.keys(rules).forEach(column => {
                const columnRules = rules[column];
                const ruleHtml = this.buildValidationRuleHtml(column, columnRules);
                $container.append(ruleHtml);
            });
        },
        
        /**
         * Build validation rule HTML
         */
        buildValidationRuleHtml: function(column, rules) {
            const rulesDisplay = Object.keys(rules).map(rule => {
                let ruleText = rule;
                if (typeof rules[rule] !== 'boolean') {
                    ruleText += `: ${rules[rule]}`;
                }
                return ruleText;
            }).join(', ');
            
            return `
                <div class="atables-validation-item" data-column="${column}">
                    <div class="atables-validation-summary">
                        <strong class="atables-validation-column">${column}</strong>
                        <span class="atables-validation-rules">${rulesDisplay}</span>
                    </div>
                    <div class="atables-validation-actions">
                        <button type="button" class="button button-small atables-edit-validation" data-column="${column}">
                            <span class="dashicons dashicons-edit"></span> Edit
                        </button>
                        <button type="button" class="button button-small atables-delete-validation" data-column="${column}">
                            <span class="dashicons dashicons-trash"></span> Delete
                        </button>
                    </div>
                </div>
            `;
        },
        
        /**
         * Load cell merging
         */
        loadCellMerging: function() {
            // Already loaded from populateSettings
        },
        
        /**
         * Render cell merges
         */
        renderCellMerges: function(merges) {
            const $container = $('#cell-merges-list');
            $container.empty();
            
            if (!merges || merges.length === 0) {
                $container.html('<p class="atables-empty-state">No cell merges yet. Click "Add Merge" to get started.</p>');
                return;
            }
            
            merges.forEach((merge, index) => {
                const mergeHtml = this.buildCellMergeHtml(merge, index);
                $container.append(mergeHtml);
            });
        },
        
        /**
         * Build cell merge HTML
         */
        buildCellMergeHtml: function(merge, index) {
            return `
                <div class="atables-merge-item" data-index="${index}">
                    <div class="atables-merge-info">
                        <span class="atables-merge-range">
                            Row ${merge.start_row}, Col ${merge.start_col}
                        </span>
                        <span class="atables-merge-span">
                            (${merge.row_span} × ${merge.col_span})
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
        
        /**
         * Load advanced settings
         */
        loadAdvancedSettings: function() {
            // Already populated from PHP
        },
        
        /**
         * Save all settings
         */
        saveAllSettings: function() {
            const self = this;
            const $btn = $('#atables-save-all-settings');
            const originalText = $btn.html();
            
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> Saving...');
            
            // Collect all settings
            const settings = {
                conditional_formatting: this.getConditionalRules(),
                formulas: this.getFormulas(),
                validation_rules: this.getValidationRules(),
                cell_merges: this.getCellMerges()
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
                        if (window.ATablesNotifications) {
                            window.ATablesNotifications.show('Settings saved successfully!', 'success');
                        }
                    } else {
                        alert(response.data.message || 'Failed to save settings.');
                    }
                },
                error: function() {
                    alert('Failed to save settings. Please try again.');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        },
        
        /**
         * Get conditional rules from UI
         */
        getConditionalRules: function() {
            // Implement based on your UI structure
            return [];
        },
        
        /**
         * Get formulas from UI
         */
        getFormulas: function() {
            // Implement based on your UI structure
            return [];
        },
        
        /**
         * Get validation rules from UI
         */
        getValidationRules: function() {
            // Implement based on your UI structure
            return {};
        },
        
        /**
         * Get cell merges from UI
         */
        getCellMerges: function() {
            // Implement based on your UI structure
            return [];
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        if ($('.atables-edit-page-tabs').length > 0) {
            ATablesEditTabs.init();
        }
    });
    
    // Expose globally
    window.ATablesEditTabs = ATablesEditTabs;
    
})(jQuery);
