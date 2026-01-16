/**
 * Shop Filters Toggle functionality
 */
(function() {
    'use strict';

    function initShopFilters() {
        const filtersToggle = document.querySelector('.filters-toggle');
        const filtersContent = document.querySelector('.filters-content');

        if (!filtersToggle || !filtersContent) {
            return;
        }

        // Toggle filters on mobile
        filtersToggle.addEventListener('click', function() {
            filtersContent.classList.toggle('active');

            // Update button text
            if (filtersContent.classList.contains('active')) {
                this.innerHTML = this.innerHTML.replace('Filters', 'Hide Filters');
            } else {
                this.innerHTML = this.innerHTML.replace('Hide Filters', 'Filters');
            }
        });

        // Close filters when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                const isClickInside = event.target.closest('.shop-sidebar');

                if (!isClickInside && filtersContent.classList.contains('active')) {
                    filtersContent.classList.remove('active');
                    filtersToggle.innerHTML = filtersToggle.innerHTML.replace('Hide Filters', 'Filters');
                }
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initShopFilters);
    } else {
        initShopFilters();
    }

})();
