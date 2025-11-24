/**
 * Frontend JavaScript
 *
 * @package ATables
 * @since 2.0.0
 */

(function($) {
    'use strict';

    class ATablesTable {
        constructor(wrapper) {
            this.wrapper = $(wrapper);
            this.table = this.wrapper.find('table');
            this.tbody = this.table.find('tbody');
            this.searchInput = this.wrapper.find('.atables-search-input');

            this.allRows = [];
            this.filteredRows = [];
            this.currentPage = 1;
            this.perPage = parseInt(this.wrapper.data('per-page')) || 10;
            this.enablePagination = this.wrapper.data('paginate') === 'true' || this.wrapper.data('paginate') === true;

            this.init();
        }

        init() {
            // Store all rows
            this.allRows = this.tbody.find('tr').toArray();
            this.filteredRows = this.allRows.slice();

            // Initialize search
            if (this.searchInput.length) {
                this.searchInput.on('input', () => this.handleSearch());
            }

            // Initialize sorting
            this.table.find('th.sortable').on('click', (e) => this.handleSort(e));

            // Initialize pagination
            if (this.enablePagination) {
                this.setupPagination();
                this.render();
            }
        }

        handleSearch() {
            const searchTerm = this.searchInput.val().toLowerCase();

            this.filteredRows = this.allRows.filter(row => {
                const text = $(row).text().toLowerCase();
                return text.includes(searchTerm);
            });

            this.currentPage = 1;
            this.render();
        }

        handleSort(e) {
            const th = $(e.currentTarget);
            const column = th.data('column');
            const columnIndex = th.index();
            const isAsc = th.hasClass('sort-asc');

            // Remove sort classes from all headers
            this.table.find('th').removeClass('sort-asc sort-desc');

            // Add sort class to current header
            if (isAsc) {
                th.addClass('sort-desc');
            } else {
                th.addClass('sort-asc');
            }

            // Sort rows
            this.filteredRows.sort((a, b) => {
                const aValue = $(a).find('td').eq(columnIndex).text().trim();
                const bValue = $(b).find('td').eq(columnIndex).text().trim();

                // Try numeric comparison
                const aNum = parseFloat(aValue);
                const bNum = parseFloat(bValue);

                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return isAsc ? bNum - aNum : aNum - bNum;
                }

                // String comparison
                return isAsc
                    ? bValue.localeCompare(aValue)
                    : aValue.localeCompare(bValue);
            });

            this.render();
        }

        setupPagination() {
            const prevBtn = this.wrapper.find('.atables-prev-page');
            const nextBtn = this.wrapper.find('.atables-next-page');

            prevBtn.on('click', () => {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.render();
                }
            });

            nextBtn.on('click', () => {
                const totalPages = Math.ceil(this.filteredRows.length / this.perPage);
                if (this.currentPage < totalPages) {
                    this.currentPage++;
                    this.render();
                }
            });
        }

        render() {
            if (!this.enablePagination) {
                return;
            }

            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            const visibleRows = this.filteredRows.slice(start, end);

            // Clear tbody
            this.tbody.empty();

            // Add visible rows
            visibleRows.forEach(row => {
                this.tbody.append(row);
            });

            // Update pagination
            this.updatePagination();
        }

        updatePagination() {
            const total = this.filteredRows.length;
            const totalPages = Math.ceil(total / this.perPage);
            const start = (this.currentPage - 1) * this.perPage + 1;
            const end = Math.min(start + this.perPage - 1, total);

            // Update info
            this.wrapper.find('.showing-info').text(
                `Showing ${start} to ${end} of ${total} entries`
            );

            // Update buttons
            this.wrapper.find('.atables-prev-page').prop('disabled', this.currentPage === 1);
            this.wrapper.find('.atables-next-page').prop('disabled', this.currentPage === totalPages);

            // Update page numbers
            const pageNumbers = this.wrapper.find('.atables-page-numbers');
            pageNumbers.empty();

            // Show max 5 page numbers
            let startPage = Math.max(1, this.currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);

            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }

            for (let i = startPage; i <= endPage; i++) {
                const btn = $('<button>')
                    .text(i)
                    .toggleClass('active', i === this.currentPage)
                    .on('click', () => {
                        this.currentPage = i;
                        this.render();
                    });
                pageNumbers.append(btn);
            }
        }
    }

    // Initialize all tables
    $(document).ready(function() {
        $('.atables-wrapper').each(function() {
            new ATablesTable(this);
        });
    });

})(jQuery);
