/**
 * A-Tables & Charts Gutenberg Blocks
 *
 * Provides Table and Chart blocks for the Gutenberg editor.
 */

(function(wp) {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, SelectControl, Placeholder, Disabled } = wp.components;
	const { __ } = wp.i18n;
	const { createElement: el } = wp.element;
	const { ServerSideRender } = wp.serverSideRender || wp.editor;

	/**
	 * Table Block
	 */
	registerBlockType('atables/table', {
		title: __('A-Tables Table', 'a-tables-charts'),
		icon: 'grid-view',
		category: 'widgets',
		description: __('Display a table created with A-Tables & Charts plugin.', 'a-tables-charts'),
		keywords: [__('table', 'a-tables-charts'), __('data', 'a-tables-charts'), __('spreadsheet', 'a-tables-charts')],
		attributes: {
			tableId: {
				type: 'number',
				default: 0
			}
		},
		supports: {
			align: ['wide', 'full']
		},

		edit: function(props) {
			const { attributes, setAttributes } = props;
			const { tableId } = attributes;

			// Get tables from localized data
			const tables = window.atablesBlockData ? window.atablesBlockData.tables : [];

			// Create options for select control
			const tableOptions = [
				{ value: 0, label: __('Select a table...', 'a-tables-charts') }
			].concat(
				tables.map(function(table) {
					return { value: table.value, label: table.label };
				})
			);

			// Handler for table selection change
			function onChangeTable(newTableId) {
				setAttributes({ tableId: parseInt(newTableId) });
			}

			// If no table selected, show placeholder
			if (!tableId || tableId === 0) {
				return el(
					Placeholder,
					{
						icon: 'grid-view',
						label: __('A-Tables Table', 'a-tables-charts'),
						instructions: __('Select a table to display.', 'a-tables-charts')
					},
					el(SelectControl, {
						label: __('Select Table', 'a-tables-charts'),
						value: tableId,
						options: tableOptions,
						onChange: onChangeTable
					})
				);
			}

			// If table selected, show preview and inspector controls
			return el(
				'div',
				{ className: 'atables-block-wrapper' },
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __('Table Settings', 'a-tables-charts'), initialOpen: true },
						el(SelectControl, {
							label: __('Select Table', 'a-tables-charts'),
							value: tableId,
							options: tableOptions,
							onChange: onChangeTable,
							help: __('Choose which table to display in this block.', 'a-tables-charts')
						})
					)
				),
				el(
					Disabled,
					{},
					el(ServerSideRender, {
						block: 'atables/table',
						attributes: attributes
					})
				)
			);
		},

		save: function() {
			// Server-side rendering, return null
			return null;
		}
	});

	/**
	 * Chart Block
	 */
	registerBlockType('atables/chart', {
		title: __('A-Tables Chart', 'a-tables-charts'),
		icon: 'chart-bar',
		category: 'widgets',
		description: __('Display a chart created with A-Tables & Charts plugin.', 'a-tables-charts'),
		keywords: [__('chart', 'a-tables-charts'), __('graph', 'a-tables-charts'), __('visualization', 'a-tables-charts')],
		attributes: {
			chartId: {
				type: 'number',
				default: 0
			}
		},
		supports: {
			align: ['wide', 'full']
		},

		edit: function(props) {
			const { attributes, setAttributes } = props;
			const { chartId } = attributes;

			// Get charts from localized data
			const charts = window.atablesBlockData ? window.atablesBlockData.charts : [];

			// Create options for select control
			const chartOptions = [
				{ value: 0, label: __('Select a chart...', 'a-tables-charts') }
			].concat(
				charts.map(function(chart) {
					return { value: chart.value, label: chart.label };
				})
			);

			// Handler for chart selection change
			function onChangeChart(newChartId) {
				setAttributes({ chartId: parseInt(newChartId) });
			}

			// If no chart selected, show placeholder
			if (!chartId || chartId === 0) {
				return el(
					Placeholder,
					{
						icon: 'chart-bar',
						label: __('A-Tables Chart', 'a-tables-charts'),
						instructions: __('Select a chart to display.', 'a-tables-charts')
					},
					el(SelectControl, {
						label: __('Select Chart', 'a-tables-charts'),
						value: chartId,
						options: chartOptions,
						onChange: onChangeChart
					})
				);
			}

			// If chart selected, show preview and inspector controls
			return el(
				'div',
				{ className: 'atables-block-wrapper' },
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __('Chart Settings', 'a-tables-charts'), initialOpen: true },
						el(SelectControl, {
							label: __('Select Chart', 'a-tables-charts'),
							value: chartId,
							options: chartOptions,
							onChange: onChangeChart,
							help: __('Choose which chart to display in this block.', 'a-tables-charts')
						})
					)
				),
				el(
					Disabled,
					{},
					el(ServerSideRender, {
						block: 'atables/chart',
						attributes: attributes
					})
				)
			);
		},

		save: function() {
			// Server-side rendering, return null
			return null;
		}
	});

})(window.wp);
