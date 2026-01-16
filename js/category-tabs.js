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
                }

                // Update URL hash without scrolling
                if (history.pushState) {
                    history.pushState(null, null, '#' + category);
                } else {
                    window.location.hash = '#' + category;
                }
            });
        });

        // Check URL hash on page load
        function checkUrlHash() {
            const hash = window.location.hash.substring(1); // Remove #

            if (hash) {
                const targetButton = document.querySelector('[data-category="' + hash + '"]');
                if (targetButton) {
                    targetButton.click();
                }
            }
        }

        // Listen for hash changes
        window.addEventListener('hashchange', checkUrlHash);

        // Check hash on page load
        checkUrlHash();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCategoryTabs);
    } else {
        initCategoryTabs();
    }

})();
