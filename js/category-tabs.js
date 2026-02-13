/**
 * Category Tabs functionality for New Arrivals page
 */
(function() {
    'use strict';

    function initCategoryTabs() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const productsGrids = document.querySelectorAll('.products-grid');

        if (!tabButtons.length || !productsGrids.length) {
            return;
        }

        // Handle tab click
        tabButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const category = this.getAttribute('data-category');

                // Remove active class from all tabs and grids
                tabButtons.forEach(function(btn) {
                    btn.classList.remove('active');
                });
                productsGrids.forEach(function(grid) {
                    grid.classList.remove('active');
                });

                // Add active class to clicked tab
                this.classList.add('active');

                // Show corresponding products grid
                const targetGrid = document.getElementById(category + '-products');
                if (targetGrid) {
                    targetGrid.classList.add('active');

                    // Scroll to products container smoothly
                    const productsContainer = document.querySelector('.products-container');
                    if (productsContainer) {
                        setTimeout(function() {
                            productsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 100);
                    }
                }

                // Update URL - remove paged parameter and add hash
                const url = new URL(window.location.href);
                url.searchParams.delete('paged'); // Remove page parameter when switching tabs
                url.hash = category;

                if (history.replaceState) {
                    history.replaceState(null, null, url.toString());
                }
            });
        });

        // Check URL hash on page load
        function checkUrlHash() {
            const hash = window.location.hash.substring(1); // Remove #

            if (hash && hash !== 'all') {
                const targetButton = document.querySelector('[data-category="' + hash + '"]');
                if (targetButton) {
                    // If there's a paged parameter, remove it since we're going to a category
                    const url = new URL(window.location.href);
                    if (url.searchParams.has('paged')) {
                        url.searchParams.delete('paged');
                        if (history.replaceState) {
                            history.replaceState(null, null, url.toString());
                        }
                    }

                    targetButton.click();
                }
            }
        }

        // Listen for hash changes
        window.addEventListener('hashchange', checkUrlHash);

        // Check hash on page load
        checkUrlHash();

        // Initialize AJAX pagination for category tabs
        initCategoryPagination();
    }

    function initCategoryPagination() {
        // Handle pagination clicks in category grids
        document.addEventListener('click', function(e) {
            const link = e.target.closest('.category-pagination a.page-numbers');
            if (!link) return;

            e.preventDefault();

            const pagination = link.closest('.category-pagination');
            const category = pagination.getAttribute('data-category');
            const grid = document.getElementById(category + '-products');

            if (!grid) return;

            // Get page number from link
            let page = 1;

            if (link.classList.contains('next')) {
                const currentPage = parseInt(pagination.querySelector('.page-numbers.current')?.textContent) || 1;
                page = currentPage + 1;
            } else if (link.classList.contains('prev')) {
                const currentPage = parseInt(pagination.querySelector('.page-numbers.current')?.textContent) || 1;
                page = Math.max(1, currentPage - 1);
            } else {
                page = parseInt(link.textContent) || 1;
            }

            loadCategoryProducts(category, page, grid);
        });
    }

    function loadCategoryProducts(category, page, grid) {
        const productsInner = grid.querySelector('.category-products-inner');

        // Show loading state
        grid.classList.add('loading');

        // Build AJAX URL
        const ajaxUrl = typeof kacCategoryAjax !== 'undefined' ? kacCategoryAjax.ajaxurl : '/wp-admin/admin-ajax.php';

        // Use query-slug if available (for multilingual support), otherwise use category
        const querySlug = grid.getAttribute('data-query-slug') || category;

        const formData = new FormData();
        formData.append('action', 'load_category_products');
        formData.append('category', querySlug);
        formData.append('page', page);
        formData.append('nonce', typeof kacCategoryAjax !== 'undefined' ? kacCategoryAjax.nonce : '');

        // Add price filters if present
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('min_price')) {
            formData.append('min_price', urlParams.get('min_price'));
        }
        if (urlParams.has('max_price')) {
            formData.append('max_price', urlParams.get('max_price'));
        }

        fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update products
                if (productsInner) {
                    productsInner.innerHTML = data.data.products;
                } else {
                    // Remove old product cards and pagination, then add new content
                    const oldCards = grid.querySelectorAll('.product-card');
                    const oldPagination = grid.querySelector('.category-pagination');
                    oldCards.forEach(card => card.remove());
                    if (oldPagination) oldPagination.remove();

                    // Insert new products
                    grid.insertAdjacentHTML('afterbegin', data.data.products);
                }

                // Handle pagination - update or remove
                const existingPagination = grid.querySelector('.category-pagination');

                if (data.data.pagination && data.data.max_pages > 1) {
                    // There are multiple pages - show pagination
                    if (existingPagination) {
                        existingPagination.innerHTML = data.data.pagination;
                        existingPagination.style.display = '';
                    } else {
                        // Create new pagination
                        const paginationNav = document.createElement('nav');
                        paginationNav.className = 'woocommerce-pagination category-pagination';
                        paginationNav.setAttribute('data-category', category);
                        paginationNav.setAttribute('data-max-pages', data.data.max_pages);
                        paginationNav.innerHTML = data.data.pagination;
                        grid.appendChild(paginationNav);
                    }
                } else {
                    // No pagination needed - hide or remove existing
                    if (existingPagination) {
                        existingPagination.style.display = 'none';
                    }
                }

                // Scroll to top of grid
                grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
        })
        .finally(() => {
            grid.classList.remove('loading');
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCategoryTabs);
    } else {
        initCategoryTabs();
    }

})();
